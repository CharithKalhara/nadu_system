<?php

namespace App\Services;

use App\Models\Document;
use App\Models\Nadu;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\LazyCollection;
use InvalidArgumentException;
use PhpOffice\PhpWord\TemplateProcessor;
use RuntimeException;
use ZipArchive;

class BulkEnvelopeService
{
    public function createDocumentForNaduIds(array $naduIds): Document
    {
        $generated = $this->generateForNaduIds($naduIds);

        $document = Document::create([
            'company_id' => session('company_id'),
            // A bulk document relates to several cases; retain the first case for
            // compatibility with the existing non-null nadu_id database column.
            'nadu_id' => $generated['firstNaduId'],
            'document_type' => 'Bulk Envelope',
            'file_name' => $generated['fileName'],
            'file_path' => 'public/envelopes/'.$generated['fileName'],
            'generated_by' => Auth::id(),
        ]);

        $document->setAttribute('bulk_record_count', $generated['count']);

        return $document;
    }

    /**
     * Generate one Word file containing the envelope pages for each selected Nadu record.
     *
     * @param  array<int, int|string>  $naduIds
     * @return array{path: string, fileName: string, count: int, firstNaduId: int}
     */
    public function generateForNaduIds(array $naduIds): array
    {
        $naduIds = array_values(array_unique(array_filter($naduIds)));

        if ($naduIds === []) {
            throw new InvalidArgumentException('Select at least one Nadu record.');
        }

        $templatePath = storage_path('app/documents/envelop_format.docx');

        if (! is_file($templatePath)) {
            throw new RuntimeException('The envelope template was not found.');
        }

        $cases = $this->selectedNadus($naduIds)->collect()->values();

        if ($cases->isEmpty()) {
            throw new RuntimeException('No Nadu records were found for the current company.');
        }

        $temporaryFiles = [];

        try {
            $documentXml = $cases->map(function (Nadu $case) use ($templatePath, &$temporaryFiles): string {
                $temporaryPath = tempnam(sys_get_temp_dir(), 'bulk-envelope-');

                if ($temporaryPath === false) {
                    throw new RuntimeException('Unable to create a temporary envelope file.');
                }

                $temporaryFiles[] = $temporaryPath;
                $template = new TemplateProcessor($templatePath);
                $this->fillTemplate($template, $case);
                $template->saveAs($temporaryPath);

                return $this->readDocumentXml($temporaryPath);
            })->all();

            $directory = storage_path('app/public/envelopes');

            if (! is_dir($directory) && ! mkdir($directory, 0755, true) && ! is_dir($directory)) {
                throw new RuntimeException('Unable to create the envelope output folder.');
            }

            $fileName = 'envelopes_all_'.now()->format('YmdHis').'.docx';
            $path = $directory.DIRECTORY_SEPARATOR.$fileName;

            copy($temporaryFiles[0], $path);
            $this->writeDocumentXml($path, $this->combineDocumentXml($documentXml));
        } finally {
            foreach ($temporaryFiles as $temporaryFile) {
                @unlink($temporaryFile);
            }
        }

        return [
            'path' => $path,
            'fileName' => $fileName,
            'count' => $cases->count(),
            'firstNaduId' => $cases->first()->id,
        ];
    }

    private function fillTemplate(TemplateProcessor $template, Nadu $case): void
    {
        $template->setValue('envelop', '');
        $template->setValue('/envelop', '');
        $template->setValue('නඩු_අංකය', $case->nadu_ankaya ?? '');
        $template->setValue('ණයකරු_1', $case->nayakaru1_nama ?? '');
        $template->setValue('ණයකරු_1__ලිපිනය_1', $case->nayakaru1_lipinaya1 ?? '');
        $template->setValue('ණයකරු_1__ලිපිනය_2', $case->nayakaru1_lipinaya2 ?? '');
        $template->setValue('ණයකරු_1__ලිපිනය_3', $case->nayakaru1_lipinaya3 ?? '');
        $template->setValue('ඇපකරු_1', $case->aepakaru1_nama ?? '');
        $template->setValue('ඇපකරු_1__ලිපිනය_1', $case->aepakaru1_lipinaya1 ?? '');
        $template->setValue('ඇපකරු_1__ලිපිනය_2', $case->aepakaru1_lipinaya2 ?? '');
        $template->setValue('ඇපකරු_1__ලිපිනය_3', $case->aepakaru1_lipinaya3 ?? '');
        $template->setValue('ඇපකරු_2', $case->aepakaru2_nama ?? '');
        $template->setValue('ඇපකරු_2__ලිපිනය_1', $case->aepakaru2_lipinaya1 ?? '');
        $template->setValue('ඇපකරු_2__ලිපිනය_2', $case->aepakaru2_lipinaya2 ?? '');
        $template->setValue('ඇපකරු_2__ලිපිනය_3', $case->aepakaru2_lipinaya3 ?? '');
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

    private function readDocumentXml(string $path): string
    {
        $archive = new ZipArchive;

        if ($archive->open($path) !== true) {
            throw new RuntimeException('Unable to open a generated envelope document.');
        }

        $xml = $archive->getFromName('word/document.xml');
        $archive->close();

        if ($xml === false) {
            throw new RuntimeException('The generated envelope document is invalid.');
        }

        return $xml;
    }

    /** @param array<int, string> $documents */
    private function combineDocumentXml(array $documents): string
    {
        $combined = new \DOMDocument;
        $combined->preserveWhiteSpace = true;

        if (! $combined->loadXML(array_shift($documents))) {
            throw new RuntimeException('Unable to read the generated envelope document.');
        }

        $xpath = new \DOMXPath($combined);
        $xpath->registerNamespace('w', 'http://schemas.openxmlformats.org/wordprocessingml/2006/main');
        $body = $xpath->query('/w:document/w:body')->item(0);
        $sectionProperties = $xpath->query('./w:sectPr', $body)->item(0);

        if ($body === null || $sectionProperties === null) {
            throw new RuntimeException('The envelope template has an invalid document structure.');
        }

        foreach ($documents as $document) {
            $source = new \DOMDocument;
            $source->preserveWhiteSpace = true;

            if (! $source->loadXML($document)) {
                throw new RuntimeException('Unable to read a generated envelope document.');
            }

            $sourceXpath = new \DOMXPath($source);
            $sourceXpath->registerNamespace('w', 'http://schemas.openxmlformats.org/wordprocessingml/2006/main');
            $sourceBody = $sourceXpath->query('/w:document/w:body')->item(0);

            if ($sourceBody === null) {
                throw new RuntimeException('The generated envelope document has an invalid document structure.');
            }

            $body->insertBefore($this->pageBreak($combined), $sectionProperties);

            foreach ($sourceBody->childNodes as $node) {
                if ($node->localName === 'sectPr') {
                    continue;
                }

                $body->insertBefore($combined->importNode($node, true), $sectionProperties);
            }
        }

        return $combined->saveXML();
    }

    private function pageBreak(\DOMDocument $document): \DOMElement
    {
        $namespace = 'http://schemas.openxmlformats.org/wordprocessingml/2006/main';
        $paragraph = $document->createElementNS($namespace, 'w:p');
        $run = $document->createElementNS($namespace, 'w:r');
        $break = $document->createElementNS($namespace, 'w:br');
        $break->setAttributeNS($namespace, 'w:type', 'page');
        $run->appendChild($break);
        $paragraph->appendChild($run);

        return $paragraph;
    }

    private function writeDocumentXml(string $path, string $xml): void
    {
        $archive = new ZipArchive;

        if ($archive->open($path) !== true || ! $archive->addFromString('word/document.xml', $xml)) {
            throw new RuntimeException('Unable to write the bulk envelope document.');
        }

        $archive->close();
    }
}
