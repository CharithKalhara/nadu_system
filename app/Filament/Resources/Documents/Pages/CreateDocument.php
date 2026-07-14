<?php

namespace App\Filament\Resources\Documents\Pages;

use App\Filament\Resources\Documents\DocumentResource;
use App\Models\Document;
use App\Models\Nadu;
use App\Services\SithasiService;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;

class CreateDocument extends CreateRecord
{
    protected static string $resource = DocumentResource::class;

    protected static string $layout = 'layouts.company-workspace';

    protected function handleRecordCreation(array $data): Document
    {
        // Find the selected case
        $case = Nadu::findOrFail($data['nadu_id']);

        // Generate the document.
        // SithasiService is responsible for saving the Document record.
        app(SithasiService::class)->generate($case);

        Notification::make()
            ->title('Sithasi generated successfully.')
            ->success()
            ->send();

        // Return the newly created document record
        return Document::latest()->first();
    }

    protected function getRedirectUrl(): string
    {
        return DocumentResource::getUrl('index');
    }
}
