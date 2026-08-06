<?php

namespace App\Services;

use App\Models\Company;
use App\Models\Document;
use App\Models\Nadu;
use App\Support\DocumentValueFormatter;
use Illuminate\Support\Facades\Auth;
use PhpOffice\PhpWord\TemplateProcessor;

class MudrithaPradanayaService
{
    public function generate(Nadu $case): Document
    {
        $template = new TemplateProcessor($this->templatePath());
        $this->fillTemplate($template, $case);

        $directory = storage_path('app/public/mudritha-pradanaya');
        if (! is_dir($directory)) {
            mkdir($directory, 0755, true);
        }

        $fileName = 'mudritha_pradanaya_'.$case->id.'_'.now()->format('YmdHis').'.docx';
        $template->saveAs($directory.'/'.$fileName);

        return Document::create([
            'company_id' => session('company_id'),
            'nadu_id' => $case->id,
            'document_type' => 'මුද්‍රිත ප්‍රදානය',
            'file_name' => $fileName,
            'file_path' => 'public/mudritha-pradanaya/'.$fileName,
            'generated_by' => Auth::id(),
        ]);
    }

    public function templatePath(): string
    {
        return storage_path('app/documents/මුද්‍රිත ප්‍රදානය.docx');
    }

    public function fillTemplate(TemplateProcessor $template, Nadu $case): void
    {
        $company = Company::find(session('company_id'));
        $values = [
            'pradhanaya_block' => '',
            '/pradhanaya_block' => '',
            'නඩු_අංකය_ format' => $company?->nadu_ankaya_format ?? '',
            'නඩු_අංකය' => $case->nadu_ankaya ?? '',
            'සමිතිය' => $company?->company_name ?? '',
            'තීරක' => $company?->teeraka_name_with_initials ?? $company?->teeraka ?? '',
            'නියෝජිතයා' => $company?->niyojithaya ?? '',
            'ණයකරු_1' => $case->nayakaru1_nama ?? '',
            'ණයකරු_1__ලිපිනය_1' => $case->nayakaru1_lipinaya1 ?? '',
            'ණයකරු_1__ලිපිනය_2' => $case->nayakaru1_lipinaya2 ?? '',
            'ණයකරු_1__ලිපිනය_3' => $case->nayakaru1_lipinaya3 ?? '',
            'ඇපකරු_1' => $case->aepakaru1_nama ?? '',
            'ඇපකරු_1__ලිපිනය_1' => $case->aepakaru1_lipinaya1 ?? '',
            'ඇපකරු_1__ලිපිනය_2' => $case->aepakaru1_lipinaya2 ?? '',
            'ඇපකරු_1__ලිපිනය_3' => $case->aepakaru1_lipinaya3 ?? '',
            'ඇපකරු_2' => $case->aepakaru2_nama ?? '',
            'ඇපකරු_2__ලිපිනය_1' => $case->aepakaru2_lipinaya1 ?? '',
            'ඇපකරු_2__ලිපිනය_2' => $case->aepakaru2_lipinaya2 ?? '',
            'ඇපකරු_2__ලිපිනය_3' => $case->aepakaru2_lipinaya3 ?? '',
            'ආරවුල්_මුදල' => number_format((float) ($case->arawul_mudala ?? 0), 2),
            'පොලී_ප්රතිශතය' => DocumentValueFormatter::percentage($case->poli_prathishathaya),
        ];

        foreach ($values as $placeholder => $value) {
            $template->setValue($placeholder, $value);
        }
    }
}
