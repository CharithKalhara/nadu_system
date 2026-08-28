<?php

namespace App\Services;

use App\Models\Document;
use App\Models\Company;
use App\Models\Nadu;
use App\Support\DocumentValueFormatter;
use Illuminate\Support\Facades\Auth;
use PhpOffice\PhpWord\TemplateProcessor;

class ThirakawaraJournalService
{
    public function generate(Nadu $case, array $templateValues = []): Document
    {
        $template = new TemplateProcessor($this->templatePath());
        $this->fillTemplate($template, $case, $templateValues);

        $directory = storage_path('app/public/thirakawara-journal');
        if (! is_dir($directory)) {
            mkdir($directory, 0755, true);
        }

        $fileName = 'thirakawara_journal_'.$case->id.'_'.now()->format('YmdHis').'.docx';
        $template->saveAs($directory.'/'.$fileName);

        return Document::create([
            'company_id' => session('company_id'),
            'nadu_id' => $case->id,
            'document_type' => 'තීරකවරයාගේ ජර්නලය',
            'file_name' => $fileName,
            'file_path' => 'public/thirakawara-journal/'.$fileName,
            'generated_by' => Auth::id(),
        ]);
    }

    public function templatePath(): string
    {
        return storage_path('app/documents/තීරකවරයාගේ ජර්නලය.docx');
    }

    public function fillTemplate(TemplateProcessor $template, Nadu $case, array $templateValues = []): void
    {
        $company = Company::find(session('company_id'));
        $values = [
            'thirakawara_journal_block' => '',
            '/thirakawara_journal_block' => '',
            'නඩු_අංකය_ format' => $company?->nadu_ankaya_format ?? '',
            'නඩු_අංකය' => $case->nadu_ankaya ?? '',
            'තීරක' => $company?->teeraka_name_with_initials ?? $company?->teeraka ?? '',
            'සමිතිය' => $company?->company_name ?? '',
            'සමිතිය_ලිපිනය' => $templateValues['samithiya_lipinaya'] ?? $company?->samithiya_lipinaya ?? '',
            'නියෝජිතයා' => $templateValues['niyojithaya'] ?? $company?->niyojithaya ?? '',
            'නියෝජිතයා_ලිපිනය_1' => $templateValues['niyojithaya_lipinaya_1'] ?? $company?->niyojithaya_lipinaya_1 ?? '',
            ' නියෝජිතයා_ලිපිනය_1' => $templateValues['niyojithaya_lipinaya_1'] ?? $company?->niyojithaya_lipinaya_1 ?? '',
            ' නියෝජිතයා_ලිපිනය_2' => $templateValues['niyojithaya_lipinaya_2'] ?? $company?->niyojithaya_lipinaya_2 ?? '',
            'නියෝජිතයා_ලිපිනය_3' => $templateValues['niyojithaya_lipinaya_3'] ?? $company?->niyojithaya_lipinaya_3 ?? '',
            ' නියෝජිතයා_ලිපිනය_3' => $templateValues['niyojithaya_lipinaya_3'] ?? $company?->niyojithaya_lipinaya_3 ?? '',
            'ණයකරු_1' => $case->nayakaru1_nama ?? '',
            'ණයකරු_1__ලිපිනය_1' => $case->nayakaru1_lipinaya1 ?? '',
            'ණයකරු_1__ලිපිනය_2' => $case->nayakaru1_lipinaya2 ?? '',
            'ණයකරු_1__ලිපිනය_3' => $case->nayakaru1_lipinaya3 ?? '',
            // These match the single-address placeholders used by the journal template.
            'ණයකරු_1_ලිපිනය' => $this->combineAddress([
                $case->nayakaru1_lipinaya1,
                $case->nayakaru1_lipinaya2,
                $case->nayakaru1_lipinaya3,
            ]),
            'ණයකරු_2' => $case->nayakaru2_nama ?? '',
            'ණයකරු_2_ලිපිනය' => $this->combineAddress([
                $case->nayakaru2_lipinaya1,
                $case->nayakaru2_lipinaya2,
                $case->nayakaru2_lipinaya3,
            ]),
            'ඇපකරු_1' => $case->aepakaru1_nama ?? '',
            'ඇපකරු_1__ලිපිනය_1' => $case->aepakaru1_lipinaya1 ?? '',
            'ඇපකරු_1__ලිපිනය_2' => $case->aepakaru1_lipinaya2 ?? '',
            'ඇපකරු_1__ලිපිනය_3' => $case->aepakaru1_lipinaya3 ?? '',
            'ඇපකරු_1_ලිපිනය' => $this->combineAddress([
                $case->aepakaru1_lipinaya1,
                $case->aepakaru1_lipinaya2,
                $case->aepakaru1_lipinaya3,
            ]),
            // The supplied Word template uses this exact placeholder name
            // (including the trailing underscore).
            'ඇපකරු_2_' => $case->aepakaru2_nama ?? '',
            'ඇපකරු_2__ලිපිනය_1' => $case->aepakaru2_lipinaya1 ?? '',
            'ඇපකරු_2__ලිපිනය_2' => $case->aepakaru2_lipinaya2 ?? '',
            'ඇපකරු_2__ලිපිනය_3' => $case->aepakaru2_lipinaya3 ?? '',
            'ඇපකරු_2_ලිපිනය' => $this->combineAddress([
                $case->aepakaru2_lipinaya1,
                $case->aepakaru2_lipinaya2,
                $case->aepakaru2_lipinaya3,
            ]),
            'මුල්_මුදල' => number_format((float) ($case->mul_mudala ?? 0), 2),
            'පොලී_ප්රතිශතය' => DocumentValueFormatter::percentage($case->poli_prathishathaya),
            ' පළමු_සිතාසිය_දිනය' => $templateValues['first_sithasiya_date'] ?? $company?->first_sithasiya_date ?? '',
            'පළමු_සිතාසිය_post_office' => $templateValues['first_sithasiya_post_office'] ?? $company?->first_sithasiya_post_office ?? '',
            ' පළමු_සිතාසිය_කුවි_අං' => $templateValues['first_sithasiya_receipt_no'] ?? $company?->first_sithasiya_receipt_no ?? '',
            'දෙවන_සිතාසිය_දිනය' => $templateValues['second_sithasiya_date'] ?? $company?->second_sithasiya_date ?? '',
            ' දෙවන_සිතාසිය_post_office' => $templateValues['second_sithasiya_post_office'] ?? $company?->second_sithasiya_post_office ?? '',
            'දෙවන_සිතාසිය_කුවි_අං' => $templateValues['second_sithasiya_receipt_no'] ?? $company?->second_sithasiya_receipt_no ?? '',
            'පොලිය' => number_format((float) ($case->poliya ?? 0), 2),
            'නඩු_ගාස්තු' => number_format((float) ($case->nadu_gasthu ?? 0), 2),
            'ආරවුල්_මුදල' => number_format((float) ($case->arawul_mudala ?? 0), 2),
            'එකතුව' => number_format((float) ($case->total ?? 0), 2),
        ];

        foreach ($values as $placeholder => $value) {
            $template->setValue($placeholder, $value);
        }
    }

    /** @param array<int, string|null> $addressLines */
    private function combineAddress(array $addressLines): string
    {
        return collect($addressLines)
            ->filter(fn (?string $addressLine): bool => filled($addressLine))
            ->implode(', ');
    }
}
