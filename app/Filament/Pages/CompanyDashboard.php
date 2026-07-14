<?php

namespace App\Filament\Pages;

use App\Models\Company;
use Filament\Pages\Page;

class CompanyDashboard extends Page
{
    public Company $company;

    public function mount(): void
    {
        $companyId = request()->query('company', session('company_id'));

        if (! $companyId) {
            $this->redirectRoute('filament.admin.resources.companies.index');

            return;
        }

        $this->company = Company::findOrFail($companyId);

        session([
            'company_id' => $this->company->id,
            'company_table' => $this->company->table_name,
        ]);
    }

    public function getTitle(): string
    {
        return $this->company->company_name;
    }
}
