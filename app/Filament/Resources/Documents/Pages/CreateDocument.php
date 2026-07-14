<?php

namespace App\Filament\Resources\Documents\Pages;

use App\Filament\Resources\Documents\DocumentResource;
use App\Models\Company;
use App\Models\Document;
use App\Models\Nadu;
use App\Services\EnvelopeService;
use App\Services\SithasiService;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;

class CreateDocument extends CreateRecord
{
    protected static string $resource = DocumentResource::class;

    protected static string $layout = 'layouts.company-workspace';

    protected function handleRecordCreation(array $data): Document
    {
        $company = Company::findOrFail(session('company_id'));

        session([
            'company_id' => $company->id,
            'company_table' => $company->table_name,
        ]);

        $case = Nadu::query()
            ->where('company_id', $company->id)
            ->findOrFail($data['nadu_id']);

        $document = match ($data['document_type']) {
            'envelope' => app(EnvelopeService::class)->generate($case),
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
        return DocumentResource::getUrl('index');
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
            'sithasi_and_envelope' => 'Sithasi and envelope generated successfully.',
            default => 'Sithasi generated successfully.',
        };
    }
}
