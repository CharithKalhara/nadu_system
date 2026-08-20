<?php

namespace App\Services;

use App\Models\Company;
use App\Models\Document;
use App\Models\Nadu;
use App\Support\DocumentValueFormatter;
use Illuminate\Support\Facades\Auth;
use PhpOffice\PhpWord\TemplateProcessor;

class MudrithaPradanayaService
{
    public function generate(Nadu $case): Document
    {
        $template = new TemplateProcessor($this->templatePath());
        $this->fillTemplate($template, $case);

        $directory = storage_path('app/public/mudritha-pradanaya');
        if (! is_dir($directory)) {
            mkdir($directory, 0755, true);
        }

        $fileName = 'mudritha_pradanaya_'.$case->id.'_'.now()->format('YmdHis').'.docx';
        $template->saveAs($directory.'/'.$fileName);

        return Document::create([
            'company_id' => session('company_id'),
            'nadu_id' => $case->id,
            'document_type' => 'මුද්‍රිත ප්‍රදානය',
            'file_name' => $fileName,
            'file_path' => 'public/mudritha-pradanaya/'.$fileName,
            'generated_by' => Auth::id(),
        ]);
    }

    public function templatePath(): string
    {
        return storage_path('app/documents/මුද්‍රිත ප්‍රදානය.docx');
    }

    public function fillTemplate(TemplateProcessor $template, Nadu $case): void
    {
        $company = Company::find(session('company_id'));
        $values = [
            'pradhanaya_block' => '',
            '/pradhanaya_block' => '',
            'නඩු_අංකය_ format' => $company?->nadu_ankaya_format ?? '',
            'නඩු_අංකය' => $case->nadu_ankaya ?? '',
            'සමිතිය' => $company?->company_name ?? '',
            'තීරක' => $company?->teeraka_name_with_initials ?? $company?->teeraka ?? '',
            'නියෝජිතයා' => $company?->niyojithaya ?? '',
            'ණයකරු_1' => $case->nayakaru1_nama ?? '',
            'ණයකරු_1__ලිපිනය_1' => $case->nayakaru1_lipinaya1 ?? '',
            'ණයකරු_1__ලිපිනය_2' => $case->nayakaru1_lipinaya2 ?? '',
            'ණයකරු_1__ලිපිනය_3' => $case->nayakaru1_lipinaya3 ?? '',
            'ණයකරු_2' => $case->nayakaru2_nama ?? '',
            'ණයකරු_2__ලිපිනය_1' => $case->nayakaru2_lipinaya1 ?? '',
            'ණයකරු_2__ලිපිනය_2' => $case->nayakaru2_lipinaya2 ?? '',
            'ණයකරු_2__ලිපිනය_3' => $case->nayakaru2_lipinaya3 ?? '',
            'ඇපකරු_1' => $case->aepakaru1_nama ?? '',
            'ඇපකරු_1__ලිපිනය_1' => $case->aepakaru1_lipinaya1 ?? '',
            'ඇපකරු_1__ලිපිනය_2' => $case->aepakaru1_lipinaya2 ?? '',
            'ඇපකරු_1__ලිපිනය_3' => $case->aepakaru1_lipinaya3 ?? '',
            'ඇපකරු_2' => $case->aepakaru2_nama ?? '',
            'ඇපකරු_2__ලිපිනය_1' => $case->aepakaru2_lipinaya1 ?? '',
            'ඇපකරු_2__ලිපිනය_2' => $case->aepakaru2_lipinaya2 ?? '',
            'ඇපකරු_2__ලිපිනය_3' => $case->aepakaru2_lipinaya3 ?? '',
            'ආරවුල්_මුදල' => number_format((float) ($case->arawul_mudala ?? 0), 2),
            'පොලී_ප්රතිශතය' => DocumentValueFormatter::percentage($case->poli_prathishathaya),
        ];

        // Preserve the document layout. A missing second debtor should only
        // remove the comma that separates its otherwise blank fields.
        $this->removeSecondDebtorSeparators($template, $case);
        $this->removeSecondDebtorListLine($template, $case);

        foreach ($values as $placeholder => $value) {
            $template->setValue($placeholder, $value);
        }
    }

    private function removeSecondDebtorSeparators(TemplateProcessor $template, Nadu $case): void
    {
        if (trim((string) ($case->nayakaru2_nama ?? '')) !== '') {
            return;
        }

        $reflection = new \ReflectionClass($template);
        $property = $reflection->getProperty('tempDocumentMainPart');

        $xml = $property->getValue($template);

        if (! is_string($xml)) {
            return;
        }

        $document = new \DOMDocument();

        if (! $document->loadXML($xml)) {
            return;
        }

        $xpath = new \DOMXPath($document);
        $xpath->registerNamespace('w', 'http://schemas.openxmlformats.org/wordprocessingml/2006/main');
        $textNodes = iterator_to_array($xpath->query('//w:t'));

        foreach ($textNodes as $index => $textNode) {
            if (! str_contains($textNode->textContent, '${ණයකරු_2')) {
                continue;
            }

            $paragraph = $textNode;

            while ($paragraph !== null && $paragraph->localName !== 'p') {
                $paragraph = $paragraph->parentNode;
            }

            if ($paragraph === null) {
                continue;
            }

            $isSecondDebtorName = $textNode->textContent === '${ණයකරු_2}';

            // Word keeps the comma, party label, and following macro in
            // separate text nodes. Remove only the Nayakaru 2 separator and
            // its "පදිංචි ණයකාර" label, while preserving the paragraph.
            for ($previousIndex = $index - 1; $previousIndex >= 0; $previousIndex--) {
                $previousTextNode = $textNodes[$previousIndex];
                $previousParagraph = $previousTextNode;

                while ($previousParagraph !== null && $previousParagraph->localName !== 'p') {
                    $previousParagraph = $previousParagraph->parentNode;
                }

                if ($previousParagraph === null || ! $previousParagraph->isSameNode($paragraph)) {
                    break;
                }

                $updatedText = $previousTextNode->textContent;

                if ($isSecondDebtorName) {
                    $updatedText = preg_replace('/\s*පදිංචි\s+ණයකාර\s*$/u', '', $updatedText);
                }

                $updatedText = preg_replace('/,\s*$/u', '', $updatedText);

                if ($updatedText !== $previousTextNode->textContent) {
                    $previousTextNode->nodeValue = $updatedText;
                }

                break;
            }
        }

        $property->setValue($template, $document->saveXML());
    }

    /**
     * On page 2, Nayakaru 2 has its own numbered line. Unlike the narrative
     * paragraph on page 1, remove that empty line and close the number gap.
     */
    private function removeSecondDebtorListLine(TemplateProcessor $template, Nadu $case): void
    {
        if (trim((string) ($case->nayakaru2_nama ?? '')) !== '') {
            return;
        }

        $reflection = new \ReflectionClass($template);
        $property = $reflection->getProperty('tempDocumentMainPart');
        $xml = $property->getValue($template);

        if (! is_string($xml)) {
            return;
        }

        $document = new \DOMDocument();

        if (! $document->loadXML($xml)) {
            return;
        }

        $xpath = new \DOMXPath($document);
        $xpath->registerNamespace('w', 'http://schemas.openxmlformats.org/wordprocessingml/2006/main');

        foreach ($xpath->query('//w:p') as $paragraph) {
            $text = '';

            foreach ($xpath->query('.//w:t', $paragraph) as $textNode) {
                $text .= $textNode->textContent;
            }

            if (str_contains($text, '${ණයකරු_2}') && str_contains($text, 'නම:')) {
                $paragraph->parentNode?->removeChild($paragraph);

                break;
            }
        }

        $this->setListNumber($xpath, 'ඇපකරු_1', '2');
        $this->setListNumber($xpath, 'ඇපකරු_2', '3');

        $property->setValue($template, $document->saveXML());
    }

    private function setListNumber(\DOMXPath $xpath, string $placeholder, string $number): void
    {
        $macro = '${'.$placeholder.'}';

        foreach ($xpath->query('//w:p') as $paragraph) {
            $paragraphText = '';

            foreach ($xpath->query('.//w:t', $paragraph) as $textNode) {
                $paragraphText .= $textNode->textContent;
            }

            if (! str_contains($paragraphText, $macro)) {
                continue;
            }

            foreach ($xpath->query('.//w:t', $paragraph) as $paragraphTextNode) {
                if (preg_match('/^\d+$/', trim($paragraphTextNode->textContent))) {
                    $paragraphTextNode->nodeValue = $number;

                    return;
                }
            }
        }
    }
}
