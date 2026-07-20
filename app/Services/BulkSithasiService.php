<?php

namespace App\Services;

use App\Models\Company;
use App\Models\Document;
use App\Models\Nadu;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\LazyCollection;
use InvalidArgumentException;
use PhpOffice\PhpWord\TemplateProcessor;
use RuntimeException;

class BulkSithasiService
{
    public function createDocumentForNaduIds(array $naduIds): Document
    {
        $generated = $this->generateForNaduIds($naduIds);

        $document = Document::create([
            'company_id' => session('company_id'),
            // A bulk document relates to several cases; retain the first case for
            // compatibility with the existing non-null nadu_id database column.
            'nadu_id' => $this->selectedNadus($naduIds)->value('id'),
            'document_type' => 'Bulk Sithasi',
            'file_name' => $generated['fileName'],
            'file_path' => 'public/summons/'.$generated['fileName'],
            'generated_by' => Auth::id(),
        ]);

        $document->setAttribute('bulk_record_count', $generated['count']);

        return $document;
    }

    /**
     * Generate one Word file containing a Sithasi for each selected Nadu record.
     *
     * @param  array<int, int|string>  $naduIds
     * @return array{path: string, fileName: string, count: int}
     */
    public function generateForNaduIds(array $naduIds): array
    {
        $naduIds = array_values(array_unique(array_filter($naduIds)));

        if ($naduIds === []) {
            throw new InvalidArgumentException('Select at least one Nadu record.');
        }

        $templatePath = storage_path('app/documents/sithasi.docx');

        if (! is_file($templatePath)) {
            throw new RuntimeException('The Sithasi template was not found.');
        }

        $cases = $this->selectedNadus($naduIds)->values();

        if ($cases->isEmpty()) {
            throw new RuntimeException('No Nadu records were found for the current company.');
        }

        $template = new TemplateProcessor($templatePath);
        $template->cloneBlock('sithasi_block', $cases->count(), true, true);
        $company = Company::findOrFail(session('company_id'));

        $cases->each(function (Nadu $case, int $index) use ($template, $company): void {
            app(SithasiService::class)->fillTemplate($template, $case, $company, '#'.($index + 1));
        });

        $directory = storage_path('app/public/summons');

        if (! is_dir($directory) && ! mkdir($directory, 0755, true) && ! is_dir($directory)) {
            throw new RuntimeException('Unable to create the Sithasi output folder.');
        }

        $fileName = 'sithasi_all_'.now()->format('YmdHis').'.docx';
        $path = $directory.DIRECTORY_SEPARATOR.$fileName;
        $template->saveAs($path);

        return [
            'path' => $path,
            'fileName' => $fileName,
            'count' => $cases->count(),
        ];
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
}
