<?php

namespace App\Services;

use App\Models\Document;
use App\Models\Nadu;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use PhpOffice\PhpWord\TemplateProcessor;

class StatementService
{
    public function generate(Nadu $case): Document
    {
        $template = new TemplateProcessor(storage_path('app/documents/statement.docx'));

        $loanDate = $case->dun_dinaya
            ? Carbon::parse($case->dun_dinaya)->format('Y-m-d')
            : '';
        $loanAmount = number_format((float) ($case->dun_naya_mudala ?? 0), 2);
        $total = (float) ($case->total ?? 0);
        $term = (int) ($case->kalaya ?? 0);
        $monthlyInstallment = $term > 0 ? number_format($total / $term, 2) : '';

        $values = [
            'නඩු_අංකය' => $case->nadu_ankaya ?? '',
            'ණයකරු_1' => $case->nayakaru1_nama ?? '',
            'ණයකරු1' => $case->nayakaru1_nama ?? '',
            'ණයකරු_1__ලිපිනය_1' => $case->nayakaru1_lipinaya1 ?? '',
            'ණයකරු_1__ලිපිනය_2' => $case->nayakaru1_lipinaya2 ?? '',
            'ණයකරු_1__ලිපිනය_3' => $case->nayakaru1_lipinaya3 ?? '',
            'ණයකරු1__සාමාජිකඅංකය' => $case->nayakaru1_samajika_ankaya ?? '',
            'ණයකරු_1__සාමාජික_අංකය' => $case->nayakaru1_samajika_ankaya ?? '',
            'ඇපකරු_1' => $case->aepakaru1_nama ?? '',
            'ඇපකරු_1__ලිපිනය_1' => $case->aepakaru1_lipinaya1 ?? '',
            'ඇපකරු_1__ලිපිනය_2' => $case->aepakaru1_lipinaya2 ?? '',
            'ඇපකරු_1__ලිපිනය_3' => $case->aepakaru1_lipinaya3 ?? '',
            'ඇපකරු1__සාමාජිකඅංකය' => $case->aepakaru1_samajika_ankaya ?? '',
            'ඇපකරු_1__සාමාජික_අංකය' => $case->aepakaru1_samajika_ankaya ?? '',
            'ඇපකරු_2' => $case->aepakaru2_nama ?? '',
            'ඇපකරු_2__ලිපිනය_1' => $case->aepakaru2_lipinaya1 ?? '',
            'ඇපකරු_2__ලිපිනය_2' => $case->aepakaru2_lipinaya2 ?? '',
            'ඇපකරු_2__ලිපිනය_3' => $case->aepakaru2_lipinaya3 ?? '',
            'ඇපකරු2__සාමාජිකඅංකය' => $case->aepakaru2_samajika_ankaya ?? '',
            'ඇපකරු_2__සාමාජික_අංකය' => $case->aepakaru2_samajika_ankaya ?? '',
            'දුන්දිනය' => $loanDate,
            'දුන්_දිනය' => $loanDate,
            'දුන්ණයමුදල' => $loanAmount,
            'දුන්_ණය_මුදල' => $loanAmount,
            'එකතුව' => number_format($total, 2),
            'කාලය' => $term ?: '',
            'මාසිකවාරිකය' => $monthlyInstallment,
            'මාසික_වාරිකය' => $monthlyInstallment,
        ];

        foreach ($values as $placeholder => $value) {
            $template->setValue($placeholder, $value);
        }

        $directory = storage_path('app/public/statements');

        if (! is_dir($directory)) {
            mkdir($directory, 0755, true);
        }

        $fileName = 'statement_'.$case->id.'_'.now()->format('YmdHis').'.docx';
        $template->saveAs($directory.'/'.$fileName);

        return Document::create([
            'company_id' => session('company_id'),
            'nadu_id' => $case->id,
            'document_type' => 'Statement',
            'file_name' => $fileName,
            'file_path' => 'public/statements/'.$fileName,
            'generated_by' => Auth::id(),
        ]);
    }
}
