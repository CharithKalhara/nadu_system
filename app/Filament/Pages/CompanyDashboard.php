<?php

namespace App\Filament\Pages;

use App\Models\Company;
use App\Models\Document;
use App\Models\Nadu;
use Filament\Pages\Page;

class CompanyDashboard extends Page
{
    protected string $view = 'filament.pages.company-dashboard';

    public Company $company;

    public int $naduCount = 0;

    public int $documentCount = 0;

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

        $this->naduCount = Nadu::query()
            ->where('company_id', $this->company->id)
            ->count();

        $this->documentCount = Document::query()
            ->where('company_id', $this->company->id)
            ->count();
    }

    public function getTitle(): string
    {
        return $this->company->company_name;
    }

    public function getNaduRecordsUrl(): string
    {
        return route('filament.admin.resources.nadus.index', [
            'company' => $this->company->id,
        ]);
    }

    public function getDocumentsUrl(): string
    {
        return route('filament.admin.resources.documents.index', [
            'company' => $this->company->id,
        ]);
    }
}
