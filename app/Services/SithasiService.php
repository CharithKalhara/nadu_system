<?php

namespace App\Services;

use App\Models\Company;
use App\Models\Document;
use App\Models\Nadu;
use App\Support\DocumentValueFormatter;
use App\Support\SithasiValueFormatter;
use Illuminate\Support\Facades\Auth;
use PhpOffice\PhpWord\TemplateProcessor;

class SithasiService
{
    public function generate(Nadu $case): Document
    {
        $template = new TemplateProcessor(
            storage_path('app/documents/sithasi.docx')
        );

        $template->setValue('sithasi_block', '');
        $template->setValue('/sithasi_block', '');
        $this->fillTemplate($template, $case, Company::findOrFail(session('company_id')));

        // Create output directory if it doesn't exist
        $directory = storage_path('app/public/summons');

        if (! is_dir($directory)) {
            mkdir($directory, 0755, true);
        }

        // Generate unique filename
        $fileName = 'sithasi_'.$case->id.'_'.now()->format('YmdHis').'.docx';
        $filePath = $directory.'/'.$fileName;

        // Save Word document
        $template->saveAs($filePath);

        // Save document record to database
        return Document::create([
            'company_id' => session('company_id'),
            'nadu_id' => $case->id,
            'document_type' => 'Sithasi',
            'file_name' => $fileName,
            'file_path' => 'public/summons/'.$fileName,
            'generated_by' => Auth::id(),
        ]);

    }

    public function fillTemplate(TemplateProcessor $template, Nadu $case, Company $company, string $suffix = ''): void
    {
        $date = SithasiValueFormatter::dateParts($company->wibhaga_dinaya);

        $template->setValue('නඩු_අංකය'.$suffix, $case->nadu_ankaya ?? '');
        $template->setValue('නඩු_අංකය_ format '.$suffix, $company->nadu_ankaya_format ?? '');
        $template->setValue('සමිතිය'.$suffix, $company->company_name ?? '');
        $template->setValue('ණයකරු_1'.$suffix, $case->nayakaru1_nama ?? '');
        $template->setValue('ඇපකරු_1'.$suffix, $case->aepakaru1_nama ?? '');
        $template->setValue('ඇපකරු_2'.$suffix, $case->aepakaru2_nama ?? '');
        $template->setValue('තීරක'.$suffix, $company->teeraka ?? '');
        $template->setValue('කාර්යාලය'.$suffix, $company->karyalaya ?? '');
        $template->setValue('වර්ෂය'.$suffix, $date['warshaya']);
        $template->setValue('මාසය'.$suffix, $date['masaya']);
        $template->setValue('දිනය'.$suffix, $date['dinaya']);
        $template->setValue('වරුව'.$suffix, SithasiValueFormatter::waruwa($company->welawa));
        $template->setValue('වෙලාව'.$suffix, SithasiValueFormatter::time($company->welawa));
        $template->setValue('ආරවුල්_මුදල'.$suffix, number_format((float) ($case->arawul_mudala ?? 0), 2));
        $template->setValue('පොලී_ප්රතිශතය'.$suffix, DocumentValueFormatter::percentage($case->poli_prathishathaya));
    }
}
