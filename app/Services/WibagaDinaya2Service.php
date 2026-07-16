<?php

namespace App\Services;

use App\Models\Document;
use App\Models\Nadu;
use Illuminate\Support\Facades\Auth;
use PhpOffice\PhpWord\TemplateProcessor;

class WibagaDinaya2Service
{
    public function generate(Nadu $case): Document
    {
        $template = new TemplateProcessor(storage_path('app/documents/2_wibaga_dinaya.docx'));

        $template->setValue('ණයකරු_1', $case->nayakaru1_nama ?? '');
        $template->setValue('ණයකරු_2', $case->nayakaru2_nama ?? '');
        $template->setValue('ඇපකරු_1', $case->aepakaru1_nama ?? '');
        $template->setValue('ඇපකරු_2', $case->aepakaru2_nama ?? '');

        $directory = storage_path('app/public/wibaga-dinaya-2');

        if (! is_dir($directory)) {
            mkdir($directory, 0755, true);
        }

        $fileName = 'wibaga_dinaya_2_'.$case->id.'_'.now()->format('YmdHis').'.docx';
        $template->saveAs($directory.'/'.$fileName);

        return Document::create([
            'company_id' => session('company_id'),
            'nadu_id' => $case->id,
            'document_type' => '2 Wibaga Dinaya',
            'file_name' => $fileName,
            'file_path' => 'public/wibaga-dinaya-2/'.$fileName,
            'generated_by' => Auth::id(),
        ]);
    }
}
