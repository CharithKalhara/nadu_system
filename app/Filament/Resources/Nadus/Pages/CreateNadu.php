<?php

namespace App\Filament\Resources\Nadus\Pages;

use App\Filament\Resources\Nadus\NaduResource;
use Filament\Resources\Pages\CreateRecord;

class CreateNadu extends CreateRecord
{
    protected static string $resource = NaduResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['company_id'] = session('company_id');

        return $data;
    }
}
