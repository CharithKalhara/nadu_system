<?php

namespace App\Filament\Resources\Documents\Pages;

use App\Support\CompanyContext;
use App\Filament\Resources\Documents\DocumentResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListDocuments extends ListRecords
{
    protected static string $resource = DocumentResource::class;

    protected static string $layout = 'layouts.company-workspace';

    public function mount(): void
    {
        $companyId = request()->query('company', app(CompanyContext::class)->id());

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
            CreateAction::make()
                ->url(fn (): string => DocumentResource::getUrl('create', [
                    'company' => app(CompanyContext::class)->id(),
                ])),
        ];
    }
}
