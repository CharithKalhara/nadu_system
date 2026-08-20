<?php

namespace App\Services;

use App\Models\Document;
use App\Models\Company;
use App\Models\Nadu;
use App\Support\DocumentValueFormatter;
use Illuminate\Support\Facades\Auth;
use PhpOffice\PhpWord\TemplateProcessor;

class MulKola2Service
{
    public function generate(Nadu $case, array $templateValues = []): Document
    {
        $template = new TemplateProcessor(storage_path('app/documents/mul_kola_2.docx'));
        $company = Company::find(session('company_id'));

        $template->setValue('first_pages_block', '');
        $template->setValue('/first_pages_block', '');
        $template->setValue('නඩු_අංකය_ format ', $templateValues['nadu_ankaya_format'] ?? $company?->nadu_ankaya_format ?? '');
        $template->setValue('නඩු_අංකය', $case->nadu_ankaya ?? '');
        $template->setValue('සමිතිය', $company?->company_name ?? '');
        $template->setValue('සමිතිය_ලිපිනය', $templateValues['samithiya_lipinaya'] ?? $company?->samithiya_lipinaya ?? '');
        $template->setValue('නියෝජිතයා', $templateValues['niyojithaya'] ?? $company?->niyojithaya ?? '');
        $template->setValue('නියෝජිතයා_ලිපිනය_1', $templateValues['niyojithaya_lipinaya_1'] ?? $company?->niyojithaya_lipinaya_1 ?? '');
        // The supplied template has a leading space in these two placeholder names.
        $template->setValue(' නියෝජිතයා_ලිපිනය_2', $templateValues['niyojithaya_lipinaya_2'] ?? $company?->niyojithaya_lipinaya_2 ?? '');
        $template->setValue(' නියෝජිතයා_ලිපිනය_3', $templateValues['niyojithaya_lipinaya_3'] ?? $company?->niyojithaya_lipinaya_3 ?? '');
        $template->setValue('ණයකරු_1', $case->nayakaru1_nama ?? '');
        $template->setValue('ණයකරු_1__ලිපිනය_1', $case->nayakaru1_lipinaya1 ?? '');
        $template->setValue('ණයකරු_1__ලිපිනය_2', $case->nayakaru1_lipinaya2 ?? '');
        $template->setValue('ණයකරු_1__ලිපිනය_3', $case->nayakaru1_lipinaya3 ?? '');
        $template->setValue('ණයකරු_2', $case->nayakaru2_nama ?? '');
        $template->setValue('ණයකරු_2__ලිපිනය_1', $case->nayakaru2_lipinaya1 ?? '');
        $template->setValue('ණයකරු_2__ලිපිනය_2', $case->nayakaru2_lipinaya2 ?? '');
        $template->setValue('ණයකරු_2__ලිපිනය_3', $case->nayakaru2_lipinaya3 ?? '');
        $template->setValue('ඇපකරු_1', $case->aepakaru1_nama ?? '');
        $template->setValue('ඇපකරු_1__ලිපිනය_1', $case->aepakaru1_lipinaya1 ?? '');
        $template->setValue('ඇපකරු_1__ලිපිනය_2', $case->aepakaru1_lipinaya2 ?? '');
        $template->setValue('ඇපකරු_1__ලිපිනය_3', $case->aepakaru1_lipinaya3 ?? '');
        $template->setValue('ඇපකරු_2_', $case->aepakaru2_nama ?? '');
        $template->setValue('ඇපකරු_2__ලිපිනය_1', $case->aepakaru2_lipinaya1 ?? '');
        $template->setValue('ඇපකරු_2__ලිපිනය_2', $case->aepakaru2_lipinaya2 ?? '');
        $template->setValue('ඇපකරු_2__ලිපිනය_3', $case->aepakaru2_lipinaya3 ?? '');
        $template->setValue('ආරවුල්_මුදල', number_format((float) ($case->arawul_mudala ?? 0), 2));
        $template->setValue('පොලී_ප්රතිශතය', DocumentValueFormatter::percentage($case->poli_prathishathaya));

        $directory = storage_path('app/public/mul-kola-2');

        if (! is_dir($directory)) {
            mkdir($directory, 0755, true);
        }

        $fileName = 'mul_kola_2_'.$case->id.'_'.now()->format('YmdHis').'.docx';
        $template->saveAs($directory.'/'.$fileName);

        return Document::create([
            'company_id' => session('company_id'),
            'nadu_id' => $case->id,
            'document_type' => 'Mul Kola 2',
            'file_name' => $fileName,
            'file_path' => 'public/mul-kola-2/'.$fileName,
            'generated_by' => Auth::id(),
        ]);
    }
}
