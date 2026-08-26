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
        $createdDate = now()->format('Y/m/d');
        $template->setValue('අද_දිනය'.$suffix, $createdDate);
        $template->setValue('ada_dinaya'.$suffix, $createdDate);
        $template->setValue('තීන්දුව_ලබාදුන්_දිනය'.$suffix, str_replace('-', '/', (string) ($company?->thinduwa_labadena_dinaya ?? '')));
        $template->setValue('ගෙවිය_යුතු_දිනය'.$suffix, str_replace('-', '/', (string) ($company?->gewia_yuthu_dinaya ?? '')));
        $template->setValue('අභියාචනය_ඉදිරිපත්_කළ_යුතු_දිනය'.$suffix, str_replace('-', '/', (string) ($company?->abiyachana_idiripath_kala_yuthu_dinaya ?? '')));
        $template->setValue('තැපැල් ගාස්තු'.$suffix, number_format((float) ($company?->thepal_gasthu ?? 0), 2));
        $template->setValue('ණයකරු_1'.$suffix, $case->nayakaru1_nama ?? '');
        $this->setSecondDebtorParagraph($template, $case, $suffix);
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
        $template->setValue('ඇපැල්_පැ'.$suffix, number_format((float) ($case->apal_pa ?? 0), 2));
        $template->setValue('ඇපැල්_වි'.$suffix, number_format((float) ($case->apal_vi ?? 0), 2));
    }

    /**
     * Keeps the numbered debtor-2 paragraph only when the case has a second
     * debtor. Removing the complete paragraph also removes its number.
     */
    private function setSecondDebtorParagraph(TemplateProcessor $template, Nadu $case, string $suffix): void
    {
        $secondDebtor = trim((string) ($case->nayakaru2_nama ?? ''));
        $placeholder = 'ණයකරු_2'.$suffix;

        if ($secondDebtor !== '') {
            $template->setValue($placeholder, $secondDebtor);

            return;
        }

        $reflection = new \ReflectionClass($template);
        $property = $reflection->getProperty('tempDocumentMainPart');
        $property->setAccessible(true);

        $xml = $property->getValue($template);
        $macro = preg_quote('${'.$placeholder.'}', '/');
        // Do not cross a paragraph boundary: only the debtor-2 list paragraph
        // may be removed, never the content before it.
        $paragraphContent = '(?:(?!<w:p\\b).)*?';
        $xml = preg_replace(
            '/<w:p\\b[^>]*>'.$paragraphContent.$macro.$paragraphContent.'<\\/w:p>/su',
            '',
            $xml,
        );
        $xml = $this->setManualPartyNumber($xml, 'ඇපකරු_1'.$suffix, '2');
        $xml = $this->setManualPartyNumber($xml, 'ඇපකරු_2'.$suffix, '3');

        $property->setValue($template, $xml);
    }

    /**
     * The template has typed list numbers rather than Word's automatic
     * numbering. Update only the number in the paragraph that owns a placeholder.
     */
    private function setManualPartyNumber(string $xml, string $placeholder, string $number): string
    {
        $document = new \DOMDocument;

        if (! $document->loadXML($xml)) {
            return $xml;
        }

        $xpath = new \DOMXPath($document);
        $xpath->registerNamespace('w', 'http://schemas.openxmlformats.org/wordprocessingml/2006/main');
        $macro = '${'.$placeholder.'}';

        foreach ($xpath->query('//w:t') as $textNode) {
            if (! str_contains($textNode->textContent, $macro)) {
                continue;
            }

            $paragraph = $textNode;

            while ($paragraph !== null && $paragraph->localName !== 'p') {
                $paragraph = $paragraph->parentNode;
            }

            if ($paragraph === null) {
                return $xml;
            }

            foreach ($xpath->query('.//w:t', $paragraph) as $paragraphTextNode) {
                if (preg_match('/^\d+$/', trim($paragraphTextNode->textContent))) {
                    $paragraphTextNode->nodeValue = $number;

                    return $document->saveXML();
                }
            }
        }

        return $xml;
    }
}
