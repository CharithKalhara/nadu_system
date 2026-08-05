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

class BulkThinduwaWrittenService
{
    public function createDocumentForNaduIds(array $naduIds): Document
    {
        $naduIds = array_values(array_unique(array_filter($naduIds)));

        if ($naduIds === []) {
            throw new InvalidArgumentException('Select at least one Nadu record.');
        }

        $templatePath = $this->templatePath();

        if (! is_file($templatePath)) {
            throw new RuntimeException('The Thinduwa Written template was not found.');
        }

        $cases = $this->selectedNadus($naduIds)->values();

        if ($cases->isEmpty()) {
            throw new RuntimeException('No Nadu records were found for the current company.');
        }

        $temporaryFiles = [];

        try {
            $documentXml = $cases->map(function (Nadu $case) use ($templatePath, &$temporaryFiles): string {
                $temporaryPath = tempnam(sys_get_temp_dir(), 'bulk-thinduwa-written-');

                if ($temporaryPath === false) {
                    throw new RuntimeException('Unable to create a temporary Thinduwa Written file.');
                }

                $temporaryFiles[] = $temporaryPath;
                $template = new TemplateProcessor($templatePath);
                $this->fillTemplate($template, $case);
                $template->saveAs($temporaryPath);

                return $this->readDocumentXml($temporaryPath);
            })->all();

            $directory = $this->outputDirectory();

            if (! is_dir($directory) && ! mkdir($directory, 0755, true) && ! is_dir($directory)) {
                throw new RuntimeException('Unable to create the Thinduwa Written output folder.');
            }

            $fileName = $this->filePrefix().'_all_'.now()->format('YmdHis').'.docx';
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
            'document_type' => $this->documentType(),
            'file_name' => $fileName,
            'file_path' => 'public/'.$this->outputDirectoryName().'/'.$fileName,
            'generated_by' => Auth::id(),
        ]);
        $document->setAttribute('bulk_record_count', $cases->count());

        return $document;
    }

    /** @return LazyCollection<int, Nadu> */
    protected function templatePath(): string
    {
        return storage_path('app/documents/thinduwa_written.docx');
    }

    protected function outputDirectoryName(): string
    {
        return 'thinduwa-written';
    }

    protected function outputDirectory(): string
    {
        return storage_path('app/public/'.$this->outputDirectoryName());
    }

    protected function filePrefix(): string
    {
        return 'thinduwa_written';
    }

    protected function documentType(): string
    {
        return 'Bulk Thinduwa Written';
    }

    protected function fillTemplate(TemplateProcessor $template, Nadu $case): void
    {
        app(ThinduwaWrittenService::class)->fillTemplate($template, $case);
    }

    private function selectedNadus(array $naduIds): LazyCollection
    {
        return Nadu::query()->where('company_id', session('company_id'))->whereKey($naduIds)->orderBy('id')->lazyById(500);
    }

    private function readDocumentXml(string $path): string
    {
        $archive = new ZipArchive;
        if ($archive->open($path) !== true) {
            throw new RuntimeException('Unable to open a generated Thinduwa Written document.');
        }
        $xml = $archive->getFromName('word/document.xml');
        $archive->close();
        if ($xml === false) {
            throw new RuntimeException('The generated Thinduwa Written document is invalid.');
        }
        return $xml;
    }

    /** @param array<int, string> $documents */
    private function combineDocumentXml(array $documents): string
    {
        $combined = new \DOMDocument;
        $combined->preserveWhiteSpace = true;
        if (! $combined->loadXML(array_shift($documents))) {
            throw new RuntimeException('Unable to read the generated Thinduwa Written document.');
        }
        $xpath = new \DOMXPath($combined);
        $xpath->registerNamespace('w', 'http://schemas.openxmlformats.org/wordprocessingml/2006/main');
        $body = $xpath->query('/w:document/w:body')->item(0);
        $sectionProperties = $xpath->query('./w:sectPr', $body)->item(0);
        if ($body === null || $sectionProperties === null) {
            throw new RuntimeException('The Thinduwa Written template has an invalid document structure.');
        }

        foreach ($documents as $document) {
            $source = new \DOMDocument;
            $source->preserveWhiteSpace = true;
            if (! $source->loadXML($document)) {
                throw new RuntimeException('Unable to read a generated Thinduwa Written document.');
            }
            $sourceXpath = new \DOMXPath($source);
            $sourceXpath->registerNamespace('w', 'http://schemas.openxmlformats.org/wordprocessingml/2006/main');
            $sourceBody = $sourceXpath->query('/w:document/w:body')->item(0);
            if ($sourceBody === null) {
                throw new RuntimeException('The generated Thinduwa Written document has an invalid document structure.');
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
            throw new RuntimeException('Unable to write the bulk Thinduwa Written document.');
        }
        $archive->close();
    }
}
