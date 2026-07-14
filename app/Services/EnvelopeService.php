<?php

namespace App\Services;

use App\Models\Document;
use App\Models\Nadu;
use Illuminate\Support\Facades\Auth;
use PhpOffice\PhpWord\TemplateProcessor;

class EnvelopeService
{
    public function generate(Nadu $case): Document
    {
        $template = new TemplateProcessor(storage_path('app/documents/envelop_format.docx'));

        $template->setValue('නඩු_අංකය', $case->nadu_ankaya ?? '');
        $template->setValue('ණයකරු_1', $case->nayakaru1_nama ?? '');
        $template->setValue('ණයකරු_1__ලිපිනය_1', $case->nayakaru1_lipinaya1 ?? '');
        $template->setValue('ණයකරු_1__ලිපිනය_2', $case->nayakaru1_lipinaya2 ?? '');
        $template->setValue('ණයකරු_1__ලිපිනය_3', $case->nayakaru1_lipinaya3 ?? '');
        $template->setValue('ඇපකරු_1', $case->aepakaru1_nama ?? '');
        $template->setValue('ඇපකරු_1__ලිපිනය_1', $case->aepakaru1_lipinaya1 ?? '');
        $template->setValue('ඇපකරු_1__ලිපිනය_2', $case->aepakaru1_lipinaya2 ?? '');
        $template->setValue('ඇපකරු_1__ලිපිනය_3', $case->aepakaru1_lipinaya3 ?? '');
        $template->setValue('ඇපකරු_2', $case->aepakaru2_nama ?? '');
        $template->setValue('ඇපකරු_2__ලිපිනය_1', $case->aepakaru2_lipinaya1 ?? '');
        $template->setValue('ඇපකරු_2__ලිපිනය_2', $case->aepakaru2_lipinaya2 ?? '');
        $template->setValue('ඇපකරු_2__ලිපිනය_3', $case->aepakaru2_lipinaya3 ?? '');

        $directory = storage_path('app/public/envelopes');

        if (! is_dir($directory)) {
            mkdir($directory, 0755, true);
        }

        $fileName = 'envelope_'.$case->id.'_'.now()->format('YmdHis').'.docx';
        $template->saveAs($directory.'/'.$fileName);

        return Document::create([
            'company_id' => session('company_id'),
            'nadu_id' => $case->id,
            'document_type' => 'Envelope',
            'file_name' => $fileName,
            'file_path' => 'public/envelopes/'.$fileName,
            'generated_by' => Auth::id(),
        ]);
    }
}
