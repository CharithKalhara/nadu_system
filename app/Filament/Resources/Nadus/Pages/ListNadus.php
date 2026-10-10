<?php

namespace App\Filament\Resources\Nadus\Pages;

use App\Filament\Resources\Nadus\NaduResource;
use App\Support\CompanyContext;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListNadus extends ListRecords
{
    protected static string $resource = NaduResource::class;

    protected static string $layout = 'layouts.company-workspace';

    public function mount(): void
    {
        $companyId = request()->query('company', session('company_id'));

        if (! $companyId) {
            $this->redirectRoute('filament.admin.resources.companies.index');

            return;
        }

        app(CompanyContext::class)->resolve(request());

        parent::mount();
    }

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
