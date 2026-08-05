<?php

namespace App\Services;

use App\Models\Nadu;
use PhpOffice\PhpWord\TemplateProcessor;

class BulkMudrithaPradanayaSecondPageService extends BulkThinduwaWrittenService
{
    protected function templatePath(): string
    {
        return app(MudrithaPradanayaSecondPageService::class)->templatePath();
    }

    protected function outputDirectoryName(): string
    {
        return 'mudritha-pradanaya-second-page';
    }

    protected function filePrefix(): string
    {
        return 'mudritha_pradanaya_second_page';
    }

    protected function documentType(): string
    {
        return 'Bulk මුද්‍රිත ප්‍රදානය (දෙවන පිට)';
    }

    protected function fillTemplate(TemplateProcessor $template, Nadu $case): void
    {
        app(MudrithaPradanayaSecondPageService::class)->fillTemplate($template, $case);
    }
}
