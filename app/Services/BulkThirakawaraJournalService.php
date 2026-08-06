<?php

namespace App\Services;

use App\Models\Nadu;
use PhpOffice\PhpWord\TemplateProcessor;

class BulkThirakawaraJournalService extends BulkThinduwaWrittenService
{
    protected function templatePath(): string
    {
        return app(ThirakawaraJournalService::class)->templatePath();
    }

    protected function outputDirectoryName(): string
    {
        return 'thirakawara-journal';
    }

    protected function filePrefix(): string
    {
        return 'thirakawara_journal';
    }

    protected function documentType(): string
    {
        return 'Bulk තීරකවරයාගේ ජර්නලය';
    }

    protected function fillTemplate(TemplateProcessor $template, Nadu $case, array $templateValues = []): void
    {
        app(ThirakawaraJournalService::class)->fillTemplate($template, $case, $templateValues);
    }
}
