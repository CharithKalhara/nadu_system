<?php

namespace App\Services;

use App\Models\Nadu;
use PhpOffice\PhpWord\TemplateProcessor;

class BulkMudrithaPradanayaService extends BulkThinduwaWrittenService
{
    protected function templatePath(): string
    {
        return app(MudrithaPradanayaService::class)->templatePath();
    }

    protected function outputDirectoryName(): string
    {
        return 'mudritha-pradanaya';
    }

    protected function filePrefix(): string
    {
        return 'mudritha_pradanaya';
    }

    protected function documentType(): string
    {
        return 'Bulk මුද්‍රිත ප්‍රදානය';
    }

    protected function fillTemplate(TemplateProcessor $template, Nadu $case, array $templateValues = []): void
    {
        app(MudrithaPradanayaService::class)->fillTemplate($template, $case);
    }
}
