<?php

namespace App\Filament\Resources\Documents\Pages;

use App\Filament\Resources\Documents\DocumentResource;
use App\Models\Company;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListDocuments extends ListRecords
{
    protected static string $resource = DocumentResource::class;

    protected static string $layout = 'layouts.company-workspace';

    public function mount(): void
    {
        $companyId = request()->query('company', session('company_id'));

        if (! $companyId) {
            $this->redirectRoute('filament.admin.resources.companies.index');

            return;
        }

        $company = Company::findOrFail($companyId);

        session([
            'company_id' => $company->id,
            'company_table' => $company->table_name,
        ]);

        parent::mount();
    }

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
