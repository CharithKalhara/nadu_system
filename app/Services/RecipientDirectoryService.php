<?php

namespace App\Services;

use App\Models\Nadu;
use Illuminate\Support\LazyCollection;
use InvalidArgumentException;
use PhpOffice\PhpWord\TemplateProcessor;
use RuntimeException;

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

        return [
            'path' => $path,
            'fileName' => $fileName,
            'recipientCount' => count($recipients),
        ];
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
