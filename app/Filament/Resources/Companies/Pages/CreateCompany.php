<?php

namespace App\Filament\Resources\Companies\Pages;

use App\Filament\Resources\Companies\CompanyResource;
use App\Support\CaseTableSchema;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CreateCompany extends CreateRecord
{
    protected static string $resource = CompanyResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        // Get all tables in companies database
        $tables = DB::connection('companies')->select('SHOW TABLES');

        $usedNumbers = [];

        foreach ($tables as $table) {
            $tableName = array_values((array) $table)[0];

            if (preg_match('/company_(\d{4})_cases/', $tableName, $matches)) {
                $usedNumbers[] = (int) $matches[1];
            }
        }

        sort($usedNumbers);

        // Find first available number
        $number = 1;

        while (in_array($number, $usedNumbers)) {
            $number++;
        }

        $data['table_name'] = 'company_' . str_pad($number, 4, '0', STR_PAD_LEFT) . '_cases';

        return $data;
    }

    protected function afterCreate(): void
    {
        Schema::connection('companies')->create(
            $this->record->table_name,
            function (Blueprint $table) {
                CaseTableSchema::define($table);
            }
        );
    }
}