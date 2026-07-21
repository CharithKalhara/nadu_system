<?php

namespace App\Services;

use App\Models\Nadu;
use DOMDocument;
use DOMElement;
use DOMXPath;
use Illuminate\Support\LazyCollection;
use InvalidArgumentException;
use PhpOffice\PhpWord\TemplateProcessor;
use RuntimeException;
use ZipArchive;

class RecipientDirectoryService
{
    /**
     * Generate a recipient directory for the selected Nadu record IDs.
     *
     * @param  array<int, int|string>  $naduIds
     * @return array{path: string, fileName: string, recipientCount: int}
     */
    public function generateForNaduIds(array $naduIds): array
    {
        $naduIds = array_values(array_unique(array_filter($naduIds)));

        if ($naduIds === []) {
            throw new InvalidArgumentException('Select at least one Nadu record.');
        }

        $templatePath = storage_path('app/documents/directory.docx');

        if (! is_file($templatePath)) {
            throw new RuntimeException('The recipient directory template was not found.');
        }

        $recipients = $this->getRecipients($naduIds);

        if ($recipients === []) {
            throw new RuntimeException('The selected Nadu records do not contain any recipients with a name.');
        }

        $template = new TemplateProcessor($templatePath);
        $template->cloneRow('nadu_no', count($recipients));

        foreach ($recipients as $index => $recipient) {
            $row = $index + 1;

            $template->setValue("nadu_no#{$row}", $recipient['nadu_no']);
            $template->setValue("name#{$row}", $recipient['name']);
            $template->setValue("address#{$row}", $recipient['address']);
        }

        $directory = storage_path('app/public/directories');

        if (! is_dir($directory) && ! mkdir($directory, 0755, true) && ! is_dir($directory)) {
            throw new RuntimeException('Unable to create the recipient directory output folder.');
        }

        $fileName = 'recipient_directory_'.now()->format('YmdHis').'.docx';
        $path = $directory.DIRECTORY_SEPARATOR.$fileName;
        $template->saveAs($path);
        $this->addRowIndexes($path, count($recipients));

        return [
            'path' => $path,
            'fileName' => $fileName,
            'recipientCount' => count($recipients),
        ];
    }

    /**
     * Fill the blank first column in the directory table with sequential row numbers.
     */
    private function addRowIndexes(string $path, int $recipientCount): void
    {
        $document = new ZipArchive;

        if ($document->open($path) !== true) {
            throw new RuntimeException('Unable to update the generated recipient directory.');
        }

        try {
            $xml = $document->getFromName('word/document.xml');

            if ($xml === false) {
                throw new RuntimeException('The generated recipient directory is missing its document XML.');
            }

            $dom = new DOMDocument;

            if (! $dom->loadXML($xml)) {
                throw new RuntimeException('Unable to read the generated recipient directory.');
            }

            $xpath = new DOMXPath($dom);
            $xpath->registerNamespace('w', 'http://schemas.openxmlformats.org/wordprocessingml/2006/main');
            $rows = $xpath->query('//w:tbl[1]/w:tr');

            if ($rows === false || $rows->length < $recipientCount) {
                throw new RuntimeException('The recipient directory table does not contain enough rows.');
            }

            $firstRecipientRow = $rows->length - $recipientCount;

            for ($index = 0; $index < $recipientCount; $index++) {
                $row = $rows->item($firstRecipientRow + $index);
                $firstCell = $row === null ? null : $xpath->query('./w:tc[1]', $row)->item(0);

                if (! $firstCell instanceof DOMElement) {
                    throw new RuntimeException('The recipient directory table is missing its index column.');
                }

                $paragraph = $xpath->query('./w:p[1]', $firstCell)->item(0);

                if (! $paragraph instanceof DOMElement) {
                    $paragraph = $dom->createElementNS($firstCell->namespaceURI, 'w:p');
                    $firstCell->appendChild($paragraph);
                }

                $run = $dom->createElementNS($firstCell->namespaceURI, 'w:r');
                $text = $dom->createElementNS($firstCell->namespaceURI, 'w:t', (string) ($index + 1));
                $run->appendChild($text);
                $paragraph->appendChild($run);
            }

            if (! $document->deleteName('word/document.xml')
                || ! $document->addFromString('word/document.xml', $dom->saveXML())) {
                throw new RuntimeException('Unable to save the recipient directory indexes.');
            }
        } finally {
            $document->close();
        }
    }

    /**
     * @param  array<int, int|string>  $naduIds
     * @return array<int, array{nadu_no: string, name: string, address: string}>
     */
    private function getRecipients(array $naduIds): array
    {
        $recipients = [];

        $this->selectedNadus($naduIds)->each(function (Nadu $nadu) use (&$recipients): void {
            foreach ([
                [$nadu->nayakaru1_nama, $nadu->nayakaru1_lipinaya1, $nadu->nayakaru1_lipinaya2, $nadu->nayakaru1_lipinaya3],
                [$nadu->nayakaru2_nama, $nadu->nayakaru2_lipinaya1, $nadu->nayakaru2_lipinaya2, $nadu->nayakaru2_lipinaya3],
                [$nadu->aepakaru1_nama, $nadu->aepakaru1_lipinaya1, $nadu->aepakaru1_lipinaya2, $nadu->aepakaru1_lipinaya3],
                [$nadu->aepakaru2_nama, $nadu->aepakaru2_lipinaya1, $nadu->aepakaru2_lipinaya2, $nadu->aepakaru2_lipinaya3],
            ] as [$name, $addressLine1, $addressLine2, $addressLine3]) {
                $name = trim((string) $name);

                if ($name === '') {
                    continue;
                }

                $recipients[] = [
                    'nadu_no' => (string) ($nadu->nadu_ankaya ?? ''),
                    'name' => $name,
                    'address' => implode(', ', array_filter([
                        trim((string) $addressLine1),
                        trim((string) $addressLine2),
                        trim((string) $addressLine3),
                    ])),
                ];
            }
        });

        return $recipients;
    }

    /**
     * @param  array<int, int|string>  $naduIds
     * @return LazyCollection<int, Nadu>
     */
    private function selectedNadus(array $naduIds): LazyCollection
    {
        return Nadu::query()
            ->where('company_id', session('company_id'))
            ->whereKey($naduIds)
            ->orderBy('id')
            ->lazyById(500);
    }
}
