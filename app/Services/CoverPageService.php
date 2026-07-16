<?php

namespace App\Services;

use App\Models\Document;
use App\Models\Nadu;
use Illuminate\Support\Facades\Auth;
use PhpOffice\PhpWord\TemplateProcessor;

class CoverPageService
{
    public function generate(Nadu $case): Document
    {
        $template = new TemplateProcessor(storage_path('app/documents/cover_page.docx'));

        $template->setValue('ණයකරු_1', $case->nayakaru1_nama ?? '');
        $template->setValue('ඇපකරු_1', $case->aepakaru1_nama ?? '');
        $template->setValue('ඇපකරු_2', $case->aepakaru2_nama ?? '');

        $directory = storage_path('app/public/cover-pages');

        if (! is_dir($directory)) {
            mkdir($directory, 0755, true);
        }

        $fileName = 'cover_page_'.$case->id.'_'.now()->format('YmdHis').'.docx';
        $template->saveAs($directory.'/'.$fileName);

        return Document::create([
            'company_id' => session('company_id'),
            'nadu_id' => $case->id,
            'document_type' => 'Cover Page',
            'file_name' => $fileName,
            'file_path' => 'public/cover-pages/'.$fileName,
            'generated_by' => Auth::id(),
        ]);
    }
}
