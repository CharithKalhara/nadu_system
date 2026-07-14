<?php

namespace App\Filament\Resources\Companies\Pages;

use App\Filament\Resources\Companies\CompanyResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Support\Facades\Schema;

class EditCompany extends EditRecord
{
    protected static string $resource = CompanyResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make(),
            DeleteAction::make()
                ->requiresConfirmation()
                ->modalHeading('Delete Company')
                ->modalDescription('This permanently deletes the company and all of its Nadu records. This action cannot be undone.')
                ->modalSubmitActionLabel('Permanently delete')
                ->before(function (): void {
                    if (Schema::connection('companies')->hasTable($this->record->table_name)) {
                        Schema::connection('companies')->drop($this->record->table_name);
                    }
                }),
        ];
    }
}
