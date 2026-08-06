<?php

namespace App\Services;

use App\Models\Nadu;
use App\Support\DocumentValueFormatter;
use PhpOffice\PhpWord\TemplateProcessor;

class BulkMulKola2Service extends BulkThinduwaWrittenService
{
    protected function templatePath(): string
    {
        return storage_path('app/documents/mul_kola_2.docx');
    }

    protected function outputDirectoryName(): string
    {
        return 'mul-kola-2';
    }

    protected function filePrefix(): string
    {
        return 'mul_kola_2';
    }

    protected function documentType(): string
    {
        return 'Bulk Mul Kola 2';
    }

    protected function fillTemplate(TemplateProcessor $template, Nadu $case, array $templateValues = []): void
    {
        foreach ([
            'first_pages_block' => '',
            '/first_pages_block' => '',
            'නඩු_අංකය' => $case->nadu_ankaya,
            'ණයකරු_1' => $case->nayakaru1_nama,
            'ණයකරු_1__ලිපිනය_1' => $case->nayakaru1_lipinaya1,
            'ණයකරු_1__ලිපිනය_2' => $case->nayakaru1_lipinaya2,
            'ණයකරු_1__ලිපිනය_3' => $case->nayakaru1_lipinaya3,
            'ණයකරු_2' => $case->nayakaru2_nama,
            'ණයකරු_2__ලිපිනය_1' => $case->nayakaru2_lipinaya1,
            'ණයකරු_2__ලිපිනය_2' => $case->nayakaru2_lipinaya2,
            'ණයකරු_2__ලිපිනය_3' => $case->nayakaru2_lipinaya3,
            'ඇපකරු_1' => $case->aepakaru1_nama,
            'ඇපකරු_1__ලිපිනය_1' => $case->aepakaru1_lipinaya1,
            'ඇපකරු_1__ලිපිනය_2' => $case->aepakaru1_lipinaya2,
            'ඇපකරු_1__ලිපිනය_3' => $case->aepakaru1_lipinaya3,
            'ඇපකරු_2_' => $case->aepakaru2_nama,
            'ඇපකරු_2__ලිපිනය_1' => $case->aepakaru2_lipinaya1,
            'ඇපකරු_2__ලිපිනය_2' => $case->aepakaru2_lipinaya2,
            'ඇපකරු_2__ලිපිනය_3' => $case->aepakaru2_lipinaya3,
            'ආරවුල්_මුදල' => number_format((float) ($case->arawul_mudala ?? 0), 2),
            'පොලී_ප්රතිශතය' => DocumentValueFormatter::percentage($case->poli_prathishathaya),
        ] as $key => $value) {
            $template->setValue($key, $value ?? '');
        }
    }
}
