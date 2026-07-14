<?php

namespace App\Filament\Pages;

use App\Models\Company;
use Filament\Pages\Page;

class CompanyDashboard extends Page
{
    public Company $company;

    public function mount(): void
    {
        if (! session()->has('company_id')) {
            redirect()->route('filament.admin.resources.companies.index')->send();

            return;
        }

        $this->company = Company::findOrFail(session('company_id'));
    }

    public function getTitle(): string
    {
        return $this->company->company_name;
    }
}