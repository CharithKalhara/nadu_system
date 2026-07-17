<?php

namespace App\Filament\Typing\Resources\Companies\Pages;

use App\Filament\Typing\Resources\Companies\CompanyResource;
use App\Filament\Typing\Resources\Nadus\NaduResource;
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
        $usedNumbers = collect(DB::connection('companies')->select('SHOW TABLES'))
            ->map(fn (object $table) => array_values((array) $table)[0])
            ->map(fn (string $table) => preg_match('/^company_(\d{4})_cases$/', $table, $matches) ? (int) $matches[1] : null)
            ->filter()
            ->all();

        $number = 1;

        while (in_array($number, $usedNumbers, true)) {
            $number++;
        }

        $data['table_name'] = 'company_'.str_pad((string) $number, 4, '0', STR_PAD_LEFT).'_cases';
        $data['status'] = 'typing';

        return $data;
    }

    protected function afterCreate(): void
    {
        Schema::connection('companies')->create($this->record->table_name, function (Blueprint $table): void {
            CaseTableSchema::define($table);
        });
    }

    protected function getRedirectUrl(): string
    {
        return NaduResource::getUrl('index', [
            'company' => $this->record->getKey(),
        ]);
    }
}
