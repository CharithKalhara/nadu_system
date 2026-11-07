<?php

namespace App\Services;

use App\Support\CompanyContext;
use App\Models\Document;
use App\Models\Nadu;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\LazyCollection;
use InvalidArgumentException;
use PhpOffice\PhpWord\TemplateProcessor;
use RuntimeException;

class BulkCoverPageService
{
    public function createDocumentForNaduIds(array $naduIds): Document
    {
        $naduIds = array_values(array_unique(array_filter($naduIds)));

        if ($naduIds === []) {
            throw new InvalidArgumentException('Select at least one Nadu record.');
        }

        $templatePath = storage_path('app/documents/cover_page.docx');

        if (! is_file($templatePath)) {
            throw new RuntimeException('The cover page template was not found.');
        }

        $cases = $this->selectedNadus($naduIds)->collect()->values();

        if ($cases->isEmpty()) {
            throw new RuntimeException('No Nadu records were found for the current company.');
        }

        $template = new TemplateProcessor($templatePath);
        $this->trimBlockMarkerWhitespace($template, 'cover_page_block');
        $company = app(CompanyContext::class)->company();

        $replacements = $cases->map(fn (Nadu $case): array => [
            'නඩු_අංකය_ format ' => $company?->nadu_ankaya_format ?? '',
            'සමිතිය' => $company?->company_name ?? '',
            'නඩු_අංකය' => $case->nadu_ankaya ?? '',
            'ණයකරු_1' => $case->nayakaru1_nama ?? '',
            'ණයකරු_2' => $case->nayakaru2_nama ?? '',
            'තීරක' => $company?->teeraka_name_with_initials ?? '',
            'ඇපකරු_1' => $case->aepakaru1_nama ?? '',
            'ඇපකරු_2' => $case->aepakaru2_nama ?? '',
        ])->all();

        if ($template->cloneBlock('cover_page_block', $cases->count(), true, false, $replacements) === null) {
            throw new RuntimeException('The cover page repeat block could not be found in the template.');
        }

        $directory = storage_path('app/public/cover-pages');

        if (! is_dir($directory) && ! mkdir($directory, 0755, true) && ! is_dir($directory)) {
            throw new RuntimeException('Unable to create the cover page output folder.');
        }

        $fileName = 'cover_pages_all_'.now()->format('YmdHis').'.docx';
        $template->saveAs($directory.DIRECTORY_SEPARATOR.$fileName);

        $document = Document::create([
            'company_id' => app(CompanyContext::class)->id(),
            'nadu_id' => $cases->first()->id,
            'document_type' => 'Bulk Cover Page',
            'file_name' => $fileName,
            'file_path' => 'public/cover-pages/'.$fileName,
            'generated_by' => Auth::id(),
        ]);

        $document->setAttribute('bulk_record_count', $cases->count());

        return $document;
    }

    /** @return LazyCollection<int, Nadu> */
    private function selectedNadus(array $naduIds): LazyCollection
    {
        return Nadu::query()
            ->where('company_id', app(CompanyContext::class)->id())
            ->whereKey($naduIds)
            ->orderBy('id')
            ->lazyById(500);
    }

    /**
     * Word commonly adds spaces after block markers. PHPWord's cloneBlock()
     * requires the marker to be the final text in its paragraph, so remove
     * only that harmless trailing whitespace before cloning.
     */
    private function trimBlockMarkerWhitespace(TemplateProcessor $template, string $blockName): void
    {
        $reflection = new \ReflectionClass($template);
        $property = $reflection->getProperty('tempDocumentMainPart');
        $property->setAccessible(true);

        $xml = $property->getValue($template);
        $marker = preg_quote($blockName, '/');
        $xml = preg_replace(
            '/(\$\{\/?'.$marker.'\})\s+(?=<\/w:t>)/u',
            '$1',
            $xml,
        );

        $property->setValue($template, $xml);
    }
}
