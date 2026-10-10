<?php

namespace App\Filament\Resources\Nadus\Pages;

use App\Filament\Resources\Nadus\NaduResource;
use App\Support\CompanyContext;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewNadu extends ViewRecord
{
    protected static string $resource = NaduResource::class;

    protected static string $layout = 'layouts.company-workspace';

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make()
                ->url(fn ($record): string => NaduResource::getUrl('edit', [
                    'record' => $record,
                    'company' => app(CompanyContext::class)->id(),
                ])),
        ];
    }
}
