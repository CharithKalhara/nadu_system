<?php

namespace App\Filament\Resources\Nadus\Pages;

use App\Support\CompanyContext;
use App\Filament\Resources\Nadus\NaduResource;
use Filament\Resources\Pages\CreateRecord;

class CreateNadu extends CreateRecord
{
    protected static string $resource = NaduResource::class;

    protected static string $layout = 'layouts.company-workspace';

    public function mount(): void
    {
        if (! request()->query('company', app(CompanyContext::class)->id())) {
            $this->redirectRoute('filament.admin.resources.companies.index');

            return;
        }

        app(CompanyContext::class)->resolve(request());

        parent::mount();
    }

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['company_id'] = app(CompanyContext::class)->id();

        return $data;
    }
}
