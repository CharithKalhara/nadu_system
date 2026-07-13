<?php

namespace App\Filament\Resources\Companies\Pages;

use App\Filament\Resources\Companies\CompanyResource;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\DB;

class CreateCompany extends CreateRecord
{
    protected static string $resource = CompanyResource::class;

    protected function afterCreate(): void
    {
        $company = $this->record;

        // Generate table name
        $tableName = 'company_' . str_pad($company->id, 4, '0', STR_PAD_LEFT) . '_cases';

        // Save generated table name
        $company->update([
            'table_name' => $tableName,
        ]);

        // Create company table from template
        DB::connection('companies')->statement("
            CREATE TABLE `{$tableName}` LIKE `company_template_cases`
        ");
    }
}