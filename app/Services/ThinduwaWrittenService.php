<?php

namespace App\Services;

use App\Models\Document;
use App\Models\Company;
use App\Models\Nadu;
use App\Support\DocumentValueFormatter;
use Illuminate\Support\Facades\Auth;
use PhpOffice\PhpWord\TemplateProcessor;

class ThinduwaWrittenService
{
    public function generate(Nadu $case): Document
    {
        $template = new TemplateProcessor(storage_path('app/documents/thinduwa_written.docx'));

        $this->fillTemplate($template, $case);

        $directory = storage_path('app/public/thinduwa-written');

        if (! is_dir($directory)) {
            mkdir($directory, 0755, true);
        }

        $fileName = 'thinduwa_written_'.$case->id.'_'.now()->format('YmdHis').'.docx';
        $template->saveAs($directory.'/'.$fileName);

        return Document::create([
            'company_id' => session('company_id'),
            'nadu_id' => $case->id,
            'document_type' => 'Thinduwa Written',
            'file_name' => $fileName,
            'file_path' => 'public/thinduwa-written/'.$fileName,
            'generated_by' => Auth::id(),
        ]);
    }

    public function fillTemplate(TemplateProcessor $template, Nadu $case): void
    {
        $company = Company::find(session('company_id'));
        $template->setValue('thinduwa_written_block', '');
        $template->setValue('/thinduwa_written_block', '');
        $template->setValue('නඩු_අංකය_ format ', $company?->nadu_ankaya_format ?? '');
        $template->setValue('නඩු_අංකය', $case->nadu_ankaya ?? '');
        $template->setValue('සමිතිය', $company?->company_name ?? '');
        $template->setValue('තීන්දුව_ලබාදුන්_දිනය', str_replace('-', '/', (string) ($company?->thinduwa_labadena_dinaya ?? '')));
        $template->setValue('ගෙවිය_යුතු_දිනය', str_replace('-', '/', (string) ($company?->gewia_yuthu_dinaya ?? '')));
        $template->setValue('අභියාචනය_ඉදිරිපත්_කළ_යුතු_දිනය', str_replace('-', '/', (string) ($company?->abiyachana_idiripath_kala_yuthu_dinaya ?? '')));
        $template->setValue('තැපැල් ගාස්තු', number_format((float) ($company?->thepal_gasthu ?? 0), 2));
        $template->setValue('ණයකරු_1', $case->nayakaru1_nama ?? '');
        $secondDebtor = trim((string) ($case->nayakaru2_nama ?? ''));
        $template->setValue('ණයකරු_2', $secondDebtor === '' ? '' : ', '.$secondDebtor);
        $template->setValue('ඇපකරු_1', $case->aepakaru1_nama ?? '');
        $template->setValue('ඇපකරු_2', $case->aepakaru2_nama ?? '');
        $template->setValue('මුල්_මුදල', number_format((float) ($case->mul_mudala ?? 0), 0));
        $template->setValue('පොලී_ප්රතිශතය', DocumentValueFormatter::percentage($case->poli_prathishathaya));
        $template->setValue('පොලිය', number_format((float) ($case->poliya ?? 0), 0));
        $template->setValue('නඩු_ගාස්තු', number_format((float) ($case->nadu_gasthu ?? 0), 0));
        $template->setValue('එකතුව', number_format((float) ($case->total ?? 0), 0));
        $template->setValue('ඇපැල්_පැ', number_format((float) ($case->apal_pa ?? 0), 2));
        $template->setValue('ඇපැල්_වි', number_format((float) ($case->apal_vi ?? 0), 2));
    }
}
