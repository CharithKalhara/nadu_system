<?php

namespace App\Filament\Resources\Nadus\Pages;

use App\Filament\Resources\Nadus\NaduResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewNadu extends ViewRecord
{
    protected static string $resource = NaduResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
