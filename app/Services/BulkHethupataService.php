<?php

namespace App\Services;

use App\Models\Company;
use App\Models\Nadu;
use App\Support\DocumentValueFormatter;
use PhpOffice\PhpWord\TemplateProcessor;

class BulkHethupataService extends BulkThinduwaWrittenService
{
    protected function templatePath(): string
    {
        return storage_path('app/documents/hethupata.docx');
    }

    protected function outputDirectoryName(): string
    {
        return 'hethupata';
    }

    protected function filePrefix(): string
    {
        return 'hethupata';
    }

    protected function documentType(): string
    {
        return 'Bulk Hethupata';
    }

    protected function fillTemplate(TemplateProcessor $template, Nadu $case, array $templateValues = []): void
    {
        $company = Company::find(session('company_id'));

        foreach ([
            'hethupata_block' => '',
            '/hethupata_block' => '',
            'නඩු_අංකය_ format ' => $company?->nadu_ankaya_format,
            'නඩු_අංකය' => $case->nadu_ankaya,
            'ණයකරු_1' => $case->nayakaru1_nama,
            'ණයකරු_2' => $case->nayakaru2_nama,
            'ඇපකරු_1' => $case->aepakaru1_nama,
            'ඇපකරු_2_' => $case->aepakaru2_nama,
            'දුන්_දිනය' => $case->dun_dinaya,
            'දුන්_ණය_මුදල' => number_format((float) ($case->dun_naya_mudala ?? 0), 0),
            'පොලී_ප්රතිශතය' => DocumentValueFormatter::percentage($case->poli_prathishathaya),
            'කාලය' => $case->kalaya,
            'මුල්_මුදල' => number_format((float) ($case->mul_mudala ?? 0), 0),
            'පොලිය' => number_format((float) ($case->poliya ?? 0), 0),
            'නඩු_ගාස්තු' => number_format((float) ($case->nadu_gasthu ?? 0), 0),
            'එකතුව' => number_format((float) ($case->total ?? 0), 0),
        ] as $key => $value) {
            $template->setValue($key, $value ?? '');
        }
    }
}
