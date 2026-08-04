<?php

namespace App\Services;

use App\Models\Document;
use App\Models\Company;
use App\Models\Nadu;
use App\Support\DocumentValueFormatter;
use Illuminate\Support\Facades\Auth;
use PhpOffice\PhpWord\TemplateProcessor;

class ThinduwaYawimaService
{
    public function generate(Nadu $case): Document
    {
        $template = new TemplateProcessor(storage_path('app/documents/thinduwa_yawima.docx'));

        $this->fillTemplate($template, $case);

        $directory = storage_path('app/public/thinduwa-yawima');

        if (! is_dir($directory)) {
            mkdir($directory, 0755, true);
        }

        $fileName = 'thinduwa_yawima_'.$case->id.'_'.now()->format('YmdHis').'.docx';
        $template->saveAs($directory.'/'.$fileName);

        return Document::create([
            'company_id' => session('company_id'),
            'nadu_id' => $case->id,
            'document_type' => 'Thinduwa Yawima',
            'file_name' => $fileName,
            'file_path' => 'public/thinduwa-yawima/'.$fileName,
            'generated_by' => Auth::id(),
        ]);
    }

    public function fillTemplate(TemplateProcessor $template, Nadu $case, string $suffix = '', ?Company $company = null): void
    {
        $company ??= Company::find(session('company_id'));

        $template->setValue('thinduwa_yawima_block'.$suffix, '');
        $template->setValue('/thinduwa_yawima_block'.$suffix, '');
        $template->setValue('සමිතිය'.$suffix, $company?->company_name ?? '');
        $template->setValue('නඩු_අංකය_ format '.$suffix, $company?->nadu_ankaya_format ?? '');
        $template->setValue('තීරක'.$suffix, $company?->teeraka_name_with_initials ?? $company?->teeraka ?? '');
        $template->setValue('ණයකරු_1'.$suffix, $case->nayakaru1_nama ?? '');
        $template->setValue('ඇපකරු_1'.$suffix, $case->aepakaru1_nama ?? '');
        $template->setValue('ඇපකරු_2'.$suffix, $case->aepakaru2_nama ?? '');
        $template->setValue('නඩු_අංකය'.$suffix, $case->nadu_ankaya ?? '');
        // The source template already includes the literal ".00" after these
        // values, so pass the whole-rupee amount to avoid output such as
        // "1,000.00.00".
        $template->setValue('මුල්_මුදල'.$suffix, number_format((float) ($case->mul_mudala ?? 0), 0));
        $template->setValue('පොලී_ප්රතිශතය'.$suffix, DocumentValueFormatter::percentage($case->poli_prathishathaya));
        $template->setValue('පොලිය'.$suffix, number_format((float) ($case->poliya ?? 0), 0));
        $template->setValue('නඩු_ගාස්තු'.$suffix, number_format((float) ($case->nadu_gasthu ?? 0), 0));
        $template->setValue('මුළු_මුදල'.$suffix, number_format((float) ($case->total ?? 0), 2));
    }
}
