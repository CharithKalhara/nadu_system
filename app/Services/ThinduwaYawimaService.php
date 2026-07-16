<?php

namespace App\Services;

use App\Models\Document;
use App\Models\Nadu;
use App\Support\DocumentValueFormatter;
use Illuminate\Support\Facades\Auth;
use PhpOffice\PhpWord\TemplateProcessor;

class ThinduwaYawimaService
{
    public function generate(Nadu $case): Document
    {
        $template = new TemplateProcessor(storage_path('app/documents/thinduwa_yawima.docx'));

        $template->setValue('ණයකරු_1', $case->nayakaru1_nama ?? '');
        $template->setValue('ඇපකරු_1', $case->aepakaru1_nama ?? '');
        $template->setValue('ඇපකරු_2', $case->aepakaru2_nama ?? '');
        $template->setValue('නඩු_අංකය', $case->nadu_ankaya ?? '');
        // The source template already includes the literal ".00" after these
        // values, so pass the whole-rupee amount to avoid output such as
        // "1,000.00.00".
        $template->setValue('මුල්_මුදල', number_format((float) ($case->mul_mudala ?? 0), 0));
        $template->setValue('පොලී_ප්රතිශතය', DocumentValueFormatter::percentage($case->poli_prathishathaya));
        $template->setValue('පොලිය', number_format((float) ($case->poliya ?? 0), 0));
        $template->setValue('නඩු_ගාස්තු', number_format((float) ($case->nadu_gasthu ?? 0), 0));
        $template->setValue('මුළු_මුදල', number_format((float) ($case->total ?? 0), 2));

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
}
