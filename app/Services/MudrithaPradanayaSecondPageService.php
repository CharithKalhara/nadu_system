<?php

namespace App\Services;

use App\Models\Company;
use App\Models\Document;
use App\Models\Nadu;
use Illuminate\Support\Facades\Auth;
use PhpOffice\PhpWord\TemplateProcessor;

class MudrithaPradanayaSecondPageService
{
    public function generate(Nadu $case): Document
    {
        $template = new TemplateProcessor($this->templatePath());
        $this->fillTemplate($template, $case);

        $directory = storage_path('app/public/mudritha-pradanaya-second-page');
        if (! is_dir($directory)) {
            mkdir($directory, 0755, true);
        }

        $fileName = 'mudritha_pradanaya_second_page_'.$case->id.'_'.now()->format('YmdHis').'.docx';
        $template->saveAs($directory.'/'.$fileName);

        return Document::create([
            'company_id' => session('company_id'),
            'nadu_id' => $case->id,
            'document_type' => 'මුද්‍රිත ප්‍රදානය (දෙවන පිට)',
            'file_name' => $fileName,
            'file_path' => 'public/mudritha-pradanaya-second-page/'.$fileName,
            'generated_by' => Auth::id(),
        ]);
    }

    public function templatePath(): string
    {
        return storage_path('app/documents/ප්‍රදානය දෙවන පිට.docx');
    }

    public function fillTemplate(TemplateProcessor $template, Nadu $case): void
    {
        $company = Company::find(session('company_id'));
        $values = [
            'pradhanaya_block' => '',
            '/pradhanaya_block' => '',
            'නඩු_අංකය_ format ' => $company?->nadu_ankaya_format ?? '',
            'නඩු_අංකය' => $case->nadu_ankaya ?? '',
            'ණයකරු_1' => $case->nayakaru1_nama ?? '',
            'ණයකරු_2' => $case->nayakaru2_nama ?? '',
            'ඇපකරු_1' => $case->aepakaru1_nama ?? '',
            // The supplied template includes the trailing underscore.
            'ඇපකරු_2_' => $case->aepakaru2_nama ?? '',
        ];

        foreach ($values as $placeholder => $value) {
            $template->setValue($placeholder, $value);
        }
    }
}
