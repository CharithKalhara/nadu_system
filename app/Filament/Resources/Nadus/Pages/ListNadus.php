<?php

namespace App\Filament\Resources\Nadus\Pages;

use App\Filament\Resources\Nadus\NaduResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListNadus extends ListRecords
{
    protected static string $resource = NaduResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
