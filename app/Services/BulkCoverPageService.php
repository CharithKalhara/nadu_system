<?php

namespace App\Services;

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

        $cases = $this->selectedNadus($naduIds)->values();

        if ($cases->isEmpty()) {
            throw new RuntimeException('No Nadu records were found for the current company.');
        }

        $template = new TemplateProcessor($templatePath);
        $this->trimBlockMarkerWhitespace($template, 'cover_page_block');

        if ($template->cloneBlock('cover_page_block', $cases->count(), true, true) === null) {
            throw new RuntimeException('The cover page repeat block could not be found in the template.');
        }

        $cases->each(function (Nadu $case, int $index) use ($template): void {
            $number = $index + 1;

            $template->setValue("නඩු_අංකය#{$number}", $case->nadu_ankaya ?? '');
            $template->setValue("ණයකරු_1#{$number}", $case->nayakaru1_nama ?? '');
            $template->setValue("ඇපකරු_1#{$number}", $case->aepakaru1_nama ?? '');
            $template->setValue("ඇපකරු_2#{$number}", $case->aepakaru2_nama ?? '');
        });

        $directory = storage_path('app/public/cover-pages');

        if (! is_dir($directory) && ! mkdir($directory, 0755, true) && ! is_dir($directory)) {
            throw new RuntimeException('Unable to create the cover page output folder.');
        }

        $fileName = 'cover_pages_all_'.now()->format('YmdHis').'.docx';
        $template->saveAs($directory.DIRECTORY_SEPARATOR.$fileName);

        $document = Document::create([
            'company_id' => session('company_id'),
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
            ->where('company_id', session('company_id'))
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
