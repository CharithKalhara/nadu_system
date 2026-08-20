<?php

namespace App\Filament\Resources\Documents\Pages;

use App\Filament\Resources\Documents\DocumentResource;
use App\Models\Company;
use App\Models\Document;
use App\Models\Nadu;
use App\Services\BulkCoverPageService;
use App\Services\BulkEnvelopeService;
use App\Services\BulkHethupataService;
use App\Services\BulkMudrithaPradanayaService;
use App\Services\BulkMudrithaPradanayaSecondPageService;
use App\Services\BulkMulKola2Service;
use App\Services\BulkSithasiService;
use App\Services\BulkThirakawaraJournalService;
use App\Services\BulkThinduwaWrittenService;
use App\Services\BulkThinduwaYawimaService;
use App\Services\BulkWibagaDinaya1Service;
use App\Services\BulkWibagaDinaya2Service;
use App\Services\CoverPageService;
use App\Services\EnvelopeService;
use App\Services\HethupataService;
use App\Services\MulKola2Service;
use App\Services\MudrithaPradanayaService;
use App\Services\MudrithaPradanayaSecondPageService;
use App\Services\SithasiService;
use App\Services\StatementService;
use App\Services\ThinduwaWrittenService;
use App\Services\ThinduwaYawimaService;
use App\Services\ThirakawaraJournalService;
use App\Services\WibagaDinaya1Service;
use App\Services\WibagaDinaya2Service;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Arr;
use Illuminate\Support\Js;

class CreateDocument extends CreateRecord
{
    protected static string $resource = DocumentResource::class;

    protected static string $layout = 'layouts.company-workspace';

    protected static bool $canCreateAnother = false;

    public function create(bool $another = false): void
    {
        // Keep this page available for the next document while afterCreate()
        // starts the download for the document that was just generated.
        parent::create(another: true);
    }

    protected function handleRecordCreation(array $data): Document
    {
        $company = Company::findOrFail(session('company_id'));

        session([
            'company_id' => $company->id,
            'company_table' => $company->table_name,
        ]);

        if ($data['document_type'] === 'sithasi') {
            $company->update(Arr::only($data, [
                'nadu_ankaya_format',
                'teeraka',
                'karyalaya',
                'wibhaga_dinaya',
                'welawa',
            ]));
        }

        if ($data['document_type'] === 'thinduwa_yawima') {
            $company->update(Arr::only($data, ['teeraka_name_with_initials']));
        }

        if ($data['document_type'] === 'thirakawara_journal') {
            $company->update(Arr::only($data, [
                'samithiya_lipinaya',
                'niyojithaya', 'niyojithaya_lipinaya_1', 'niyojithaya_lipinaya_2',
                'niyojithaya_lipinaya_3', 'first_sithasiya_date', 'first_sithasiya_post_office',
                'first_sithasiya_receipt_no', 'second_sithasiya_date',
                'second_sithasiya_post_office', 'second_sithasiya_receipt_no',
            ]));
        }

        if ($data['document_type'] === 'mul_kola_2') {
            $company->update(Arr::only($data, [
                'nadu_ankaya_format',
                'samithiya_lipinaya',
                'niyojithaya',
                'niyojithaya_lipinaya_1',
                'niyojithaya_lipinaya_2',
                'niyojithaya_lipinaya_3',
            ]));
        }

        if (in_array($data['document_type'], ['wibaga_dinaya_1', 'wibaga_dinaya_2'], true)) {
            $company->update(Arr::only($data, [
                'first_sithasiya_date',
                'second_sithasiya_date',
                'wibaga_sthanaya',
                'niyojithaya',
            ]));
        }

        if (in_array($data['document_type'], ['sithasi', 'cover_page', 'envelope', 'thinduwa_yawima', 'thinduwa_written', 'thirakawara_journal', 'mudritha_pradanaya', 'mudritha_pradanaya_second_page', 'wibaga_dinaya_1', 'wibaga_dinaya_2', 'mul_kola_2', 'hethupata'], true)) {
            $naduIds = $data['scope'] === 'all'
                ? Nadu::query()->where('company_id', $company->id)->pluck('id')->all()
                : $data['nadu_ids'];

            $document = match ($data['document_type']) {
                'cover_page' => app(BulkCoverPageService::class)->createDocumentForNaduIds($naduIds),
                'envelope' => app(BulkEnvelopeService::class)->createDocumentForNaduIds($naduIds),
                'hethupata' => app(BulkHethupataService::class)->createDocumentForNaduIds($naduIds),
                'mudritha_pradanaya' => app(BulkMudrithaPradanayaService::class)->createDocumentForNaduIds($naduIds),
                'mudritha_pradanaya_second_page' => app(BulkMudrithaPradanayaSecondPageService::class)->createDocumentForNaduIds($naduIds),
                'mul_kola_2' => app(BulkMulKola2Service::class)->createDocumentForNaduIds($naduIds, $data),
                'thirakawara_journal' => app(BulkThirakawaraJournalService::class)->createDocumentForNaduIds($naduIds, $data),
                'thinduwa_written' => app(BulkThinduwaWrittenService::class)->createDocumentForNaduIds($naduIds),
                'thinduwa_yawima' => app(BulkThinduwaYawimaService::class)->createDocumentForNaduIds($naduIds),
                'wibaga_dinaya_1' => app(BulkWibagaDinaya1Service::class)->createDocumentForNaduIds($naduIds),
                'wibaga_dinaya_2' => app(BulkWibagaDinaya2Service::class)->createDocumentForNaduIds($naduIds),
                default => app(BulkSithasiService::class)->createDocumentForNaduIds($naduIds),
            };

            $documentName = match ($data['document_type']) {
                'cover_page' => 'cover page',
                'envelope' => 'envelope',
                'hethupata' => 'Hethupata',
                'mudritha_pradanaya' => 'මුද්‍රිත ප්‍රදානය',
                'mudritha_pradanaya_second_page' => 'මුද්‍රිත ප්‍රදානය (දෙවන පිට)',
                'mul_kola_2' => 'Mul Kola 2',
                'thirakawara_journal' => 'තීරකවරයාගේ ජර්නලය',
                'thinduwa_written' => 'Thinduwa Written',
                'thinduwa_yawima' => 'Thinduwa Yawima',
                'wibaga_dinaya_1' => '1 Wibaga Dinaya',
                'wibaga_dinaya_2' => '2 Wibaga Dinaya',
                default => 'Sithasi',
            };

            Notification::make()
                ->title("One {$documentName} Word document was generated for {$document->bulk_record_count} record(s).")
                ->success()
                ->send();

            return $document;
        }

        $case = Nadu::query()
            ->where('company_id', $company->id)
            ->findOrFail($data['nadu_id']);

        $document = match ($data['document_type']) {
            'envelope' => app(EnvelopeService::class)->generate($case),
            'statement' => app(StatementService::class)->generate($case),
            'cover_page' => app(CoverPageService::class)->generate($case),
            'thinduwa_yawima' => app(ThinduwaYawimaService::class)->generate($case),
            'thinduwa_written' => app(ThinduwaWrittenService::class)->generate($case),
            'thirakawara_journal' => app(ThirakawaraJournalService::class)->generate($case, $data),
            'mudritha_pradanaya' => app(MudrithaPradanayaService::class)->generate($case),
            'mudritha_pradanaya_second_page' => app(MudrithaPradanayaSecondPageService::class)->generate($case),
            'wibaga_dinaya_1' => app(WibagaDinaya1Service::class)->generate($case),
            'wibaga_dinaya_2' => app(WibagaDinaya2Service::class)->generate($case),
            'mul_kola_2' => app(MulKola2Service::class)->generate($case, $data),
            'hethupata' => app(HethupataService::class)->generate($case),
            'sithasi_and_envelope' => $this->generateSithasiAndEnvelope($case),
            default => app(SithasiService::class)->generate($case),
        };

        Notification::make()
            ->title($this->getSuccessMessage($data['document_type']))
            ->success()
            ->send();

        return $document;
    }

    protected function getRedirectUrl(): string
    {
        return DocumentResource::getUrl('create', [
            'company' => session('company_id'),
        ]);
    }

    protected function afterCreate(): void
    {
        $downloadUrl = route('documents.download', $this->record);

        $this->js(<<<JS
            const downloadFrame = document.createElement('iframe');
            downloadFrame.hidden = true;
            downloadFrame.src = {$this->toJavaScript($downloadUrl)};
            document.body.appendChild(downloadFrame);
            setTimeout(() => downloadFrame.remove(), 60000);
        JS);
    }

    private function generateSithasiAndEnvelope(Nadu $case): Document
    {
        $sithasi = app(SithasiService::class)->generate($case);

        app(EnvelopeService::class)->generate($case);

        return $sithasi;
    }

    private function getSuccessMessage(string $documentType): string
    {
        return match ($documentType) {
            'envelope' => 'Envelope generated successfully.',
            'statement' => 'Statement generated successfully.',
            'cover_page' => 'Cover page generated successfully.',
            'thinduwa_yawima' => 'Thinduwa Yawima generated successfully.',
            'thinduwa_written' => 'Thinduwa Written generated successfully.',
            'thirakawara_journal' => 'තීරකවරයාගේ ජර්නලය generated successfully.',
            'mudritha_pradanaya' => 'මුද්‍රිත ප්‍රදානය generated successfully.',
            'mudritha_pradanaya_second_page' => 'මුද්‍රිත ප්‍රදානය (දෙවන පිට) generated successfully.',
            'wibaga_dinaya_1' => '1 Wibaga Dinaya generated successfully.',
            'wibaga_dinaya_2' => '2 Wibaga Dinaya generated successfully.',
            'mul_kola_2' => 'Mul Kola 2 generated successfully.',
            'hethupata' => 'Hethupata generated successfully.',
            'sithasi_and_envelope' => 'Sithasi and envelope generated successfully.',
            default => 'Sithasi generated successfully.',
        };
    }

    private function toJavaScript(string $value): string
    {
        return Js::from($value)->toHtml();
    }
}
