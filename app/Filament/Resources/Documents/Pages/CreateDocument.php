<?php

namespace App\Filament\Resources\Documents\Pages;

use App\Filament\Resources\Documents\DocumentResource;
use App\Models\Company;
use App\Models\Document;
use App\Models\Nadu;
use App\Services\BulkCoverPageService;
use App\Services\BulkEnvelopeService;
use App\Services\BulkSithasiService;
use App\Services\CoverPageService;
use App\Services\EnvelopeService;
use App\Services\HethupataService;
use App\Services\MulKola2Service;
use App\Services\SithasiService;
use App\Services\StatementService;
use App\Services\ThinduwaWrittenService;
use App\Services\ThinduwaYawimaService;
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

        if (in_array($data['document_type'], ['sithasi', 'cover_page', 'envelope'], true)) {
            $naduIds = $data['scope'] === 'all'
                ? Nadu::query()->where('company_id', $company->id)->pluck('id')->all()
                : $data['nadu_ids'];

            $document = match ($data['document_type']) {
                'cover_page' => app(BulkCoverPageService::class)->createDocumentForNaduIds($naduIds),
                'envelope' => app(BulkEnvelopeService::class)->createDocumentForNaduIds($naduIds),
                default => app(BulkSithasiService::class)->createDocumentForNaduIds($naduIds),
            };

            $documentName = match ($data['document_type']) {
                'cover_page' => 'cover page',
                'envelope' => 'envelope',
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
            'wibaga_dinaya_1' => app(WibagaDinaya1Service::class)->generate($case),
            'wibaga_dinaya_2' => app(WibagaDinaya2Service::class)->generate($case),
            'mul_kola_2' => app(MulKola2Service::class)->generate($case),
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
