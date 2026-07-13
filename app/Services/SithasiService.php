<?php

namespace App\Services;

use App\Models\Document;
use App\Models\Nadu;
use Illuminate\Support\Facades\Auth;
use PhpOffice\PhpWord\TemplateProcessor;

class SithasiService
{
    public function generate(Nadu $case): string
    {
        $template = new TemplateProcessor(
            storage_path('app/documents/sithasi.docx')
        );

        $template->setValue('නඩු_අංකය', $case->nadu_ankaya ?? '');
        $template->setValue('ණයකරු_1', $case->nayakaru1_nama ?? '');
        $template->setValue('ඇපකරු_1', $case->aepakaru1_nama ?? '');
        $template->setValue('ඇපකරු_2', $case->aepakaru2_nama ?? '');

        $template->setValue(
            'ආරවුල්_මුදල',
            number_format($case->arawul_mudala ?? 0, 2)
        );

        $template->setValue(
            'පොලී_ප්රතිශතය',
            $case->poli_prathishathaya ?? ''
        );

        // Create output directory if it doesn't exist
        $directory = storage_path('app/public/summons');

        if (! is_dir($directory)) {
            mkdir($directory, 0755, true);
        }

        // Generate unique filename
        $fileName = 'sithasi_' . $case->id . '_' . now()->format('YmdHis') . '.docx';
        $filePath = $directory . '/' . $fileName;

        // Save Word document
        $template->saveAs($filePath);

        // Save document record to database
        Document::create([
            'nadu_id'       => $case->id,
            'document_type' => 'Sithasi',
            'file_name'     => $fileName,
            'file_path'     => 'public/summons/' . $fileName,
            'generated_by'  => Auth::id(),
        ]);

        return $filePath;
    }
}