<?php

namespace App\Services;

use App\Models\Document;
use App\Models\Company;
use App\Models\Nadu;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use PhpOffice\PhpWord\TemplateProcessor;

class WibagaDinaya1Service
{
    public function generate(Nadu $case): Document
    {
        $template = new TemplateProcessor(storage_path('app/documents/1_wibaga_dinaya.docx'));
        $company = Company::find(session('company_id'));

        $template->setValue('1_wibaga_dinaya_block', '');
        $template->setValue('/1_wibaga_dinaya_block', '');
        $template->setValue('ණයකරු_1', $case->nayakaru1_nama ?? '');
        $template->setValue('ණයකරු_2', $case->nayakaru2_nama ?? '');
        $template->setValue('ඇපකරු_1', $case->aepakaru1_nama ?? '');
        $template->setValue('ඇපකරු_2', $case->aepakaru2_nama ?? '');
        $template->setValue('1_විභාග_දිනය', $this->formatDate($company?->first_sithasiya_date));
        $template->setValue('2_විභාග_දිනය', $this->formatDate($company?->second_sithasiya_date));
        $template->setValue('ස්ථානය', $company?->wibaga_sthanaya ?? '');
        $template->setValue('නියෝජිතයා', $company?->niyojithaya ?? '');

        $directory = storage_path('app/public/wibaga-dinaya-1');

        if (! is_dir($directory)) {
            mkdir($directory, 0755, true);
        }

        $fileName = 'wibaga_dinaya_1_'.$case->id.'_'.now()->format('YmdHis').'.docx';
        $template->saveAs($directory.'/'.$fileName);

        return Document::create([
            'company_id' => session('company_id'),
            'nadu_id' => $case->id,
            'document_type' => '1 Wibaga Dinaya',
            'file_name' => $fileName,
            'file_path' => 'public/wibaga-dinaya-1/'.$fileName,
            'generated_by' => Auth::id(),
        ]);
    }

    private function formatDate(?string $date): string
    {
        return $date ? Carbon::parse($date)->format('Y/m/d') : '';
    }
}
