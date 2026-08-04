<?php

namespace App\Services;

use App\Models\Document;
use App\Models\Company;
use App\Models\Nadu;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\LazyCollection;
use InvalidArgumentException;
use PhpOffice\PhpWord\TemplateProcessor;
use RuntimeException;
use ZipArchive;

class BulkThinduwaYawimaService
{
    public function createDocumentForNaduIds(array $naduIds): Document
    {
        $naduIds = array_values(array_unique(array_filter($naduIds)));

        if ($naduIds === []) {
            throw new InvalidArgumentException('Select at least one Nadu record.');
        }

        $templatePath = storage_path('app/documents/thinduwa_yawima.docx');

        if (! is_file($templatePath)) {
            throw new RuntimeException('The Thinduwa Yawima template was not found.');
        }

        $cases = $this->selectedNadus($naduIds)->values();

        if ($cases->isEmpty()) {
            throw new RuntimeException('No Nadu records were found for the current company.');
        }

        $temporaryFiles = [];

        try {
            $company = Company::findOrFail(session('company_id'));

            $documentXml = $cases->map(function (Nadu $case) use ($templatePath, $company, &$temporaryFiles): string {
                $temporaryPath = tempnam(sys_get_temp_dir(), 'bulk-thinduwa-yawima-');

                if ($temporaryPath === false) {
                    throw new RuntimeException('Unable to create a temporary Thinduwa Yawima file.');
                }

                $temporaryFiles[] = $temporaryPath;
                $template = new TemplateProcessor($templatePath);
                app(ThinduwaYawimaService::class)->fillTemplate($template, $case, company: $company);
                $template->saveAs($temporaryPath);

                return $this->readDocumentXml($temporaryPath);
            })->all();

            $directory = storage_path('app/public/thinduwa-yawima');

            if (! is_dir($directory) && ! mkdir($directory, 0755, true) && ! is_dir($directory)) {
                throw new RuntimeException('Unable to create the Thinduwa Yawima output folder.');
            }

            $fileName = 'thinduwa_yawima_all_'.now()->format('YmdHis').'.docx';
            $path = $directory.DIRECTORY_SEPARATOR.$fileName;

            copy($temporaryFiles[0], $path);
            $this->writeDocumentXml($path, $this->combineDocumentXml($documentXml));
        } finally {
            foreach ($temporaryFiles as $temporaryFile) {
                @unlink($temporaryFile);
            }
        }

        $document = Document::create([
            'company_id' => session('company_id'),
            'nadu_id' => $cases->first()->id,
            'document_type' => 'Bulk Thinduwa Yawima',
            'file_name' => $fileName,
            'file_path' => 'public/thinduwa-yawima/'.$fileName,
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

    private function readDocumentXml(string $path): string
    {
        $archive = new ZipArchive;

        if ($archive->open($path) !== true) {
            throw new RuntimeException('Unable to open a generated Thinduwa Yawima document.');
        }

        $xml = $archive->getFromName('word/document.xml');
        $archive->close();

        if ($xml === false) {
            throw new RuntimeException('The generated Thinduwa Yawima document is invalid.');
        }

        return $xml;
    }

    /** @param array<int, string> $documents */
    private function combineDocumentXml(array $documents): string
    {
        $combined = new \DOMDocument;
        $combined->preserveWhiteSpace = true;

        if (! $combined->loadXML(array_shift($documents))) {
            throw new RuntimeException('Unable to read the generated Thinduwa Yawima document.');
        }

        $xpath = new \DOMXPath($combined);
        $xpath->registerNamespace('w', 'http://schemas.openxmlformats.org/wordprocessingml/2006/main');
        $body = $xpath->query('/w:document/w:body')->item(0);
        $sectionProperties = $xpath->query('./w:sectPr', $body)->item(0);

        if ($body === null || $sectionProperties === null) {
            throw new RuntimeException('The Thinduwa Yawima template has an invalid document structure.');
        }

        foreach ($documents as $document) {
            $source = new \DOMDocument;
            $source->preserveWhiteSpace = true;

            if (! $source->loadXML($document)) {
                throw new RuntimeException('Unable to read a generated Thinduwa Yawima document.');
            }

            $sourceXpath = new \DOMXPath($source);
            $sourceXpath->registerNamespace('w', 'http://schemas.openxmlformats.org/wordprocessingml/2006/main');
            $sourceBody = $sourceXpath->query('/w:document/w:body')->item(0);

            if ($sourceBody === null) {
                throw new RuntimeException('The generated Thinduwa Yawima document has an invalid document structure.');
            }

            $body->insertBefore($this->pageBreak($combined), $sectionProperties);

            foreach ($sourceBody->childNodes as $node) {
                if ($node->localName !== 'sectPr') {
                    $body->insertBefore($combined->importNode($node, true), $sectionProperties);
                }
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
            throw new RuntimeException('Unable to write the bulk Thinduwa Yawima document.');
        }

        $archive->close();
    }
}
