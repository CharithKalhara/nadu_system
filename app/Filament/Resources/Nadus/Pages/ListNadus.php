<?php

namespace App\Filament\Resources\Nadus\Pages;

use App\Filament\Resources\Nadus\NaduResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListNadus extends ListRecords
{
    protected static string $resource = NaduResource::class;

    public function mount(): void
    {
        if (! session()->has('company_table')) {
            $this->redirectRoute('filament.admin.resources.companies.index');

            return;
        }

        parent::mount();
    }

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
