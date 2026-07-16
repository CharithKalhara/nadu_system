<?php

namespace App\Services;

use App\Models\Document;
use App\Models\Nadu;
use App\Support\DocumentValueFormatter;
use Illuminate\Support\Facades\Auth;
use PhpOffice\PhpWord\TemplateProcessor;

class HethupataService
{
    public function generate(Nadu $case): Document
    {
        $template = new TemplateProcessor(storage_path('app/documents/hethupata.docx'));

        $template->setValue('ණයකරු_1', $case->nayakaru1_nama ?? '');
        $template->setValue('ණයකරු_2', $case->nayakaru2_nama ?? '');
        $template->setValue('ඇපකරු_1', $case->aepakaru1_nama ?? '');
        $template->setValue('ඇපකරු_2_', $case->aepakaru2_nama ?? '');
        $template->setValue('දුන්_දිනය', $case->dun_dinaya ?? '');
        $template->setValue('දුන්_ණය_මුදල', number_format((float) ($case->dun_naya_mudala ?? 0), 0));
        $template->setValue('පොලී_ප්රතිශතය', DocumentValueFormatter::percentage($case->poli_prathishathaya));
        $template->setValue('කාලය', $case->kalaya ?? '');
        $template->setValue('මුල්_මුදල', number_format((float) ($case->mul_mudala ?? 0), 0));
        $template->setValue('පොලිය', number_format((float) ($case->poliya ?? 0), 0));
        $template->setValue('නඩු_ගාස්තු', number_format((float) ($case->nadu_gasthu ?? 0), 0));
        $template->setValue('එකතුව', number_format((float) ($case->total ?? 0), 0));

        $directory = storage_path('app/public/hethupata');

        if (! is_dir($directory)) {
            mkdir($directory, 0755, true);
        }

        $fileName = 'hethupata_'.$case->id.'_'.now()->format('YmdHis').'.docx';
        $template->saveAs($directory.'/'.$fileName);

        return Document::create([
            'company_id' => session('company_id'),
            'nadu_id' => $case->id,
            'document_type' => 'Hethupata',
            'file_name' => $fileName,
            'file_path' => 'public/hethupata/'.$fileName,
            'generated_by' => Auth::id(),
        ]);
    }
}
