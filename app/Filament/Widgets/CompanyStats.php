<?php

namespace App\Filament\Widgets;

use App\Models\Company;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CompanyStats extends StatsOverviewWidget
{
    protected function getStats(): array
    {
        $companyId = request()->query('company');

        if (filter_var($companyId, FILTER_VALIDATE_INT) === false || (int) $companyId < 1) {
            return [];
        }

        $company = Company::find($companyId);

        if (! $company) {
            return [];
        }

        $table = $company->table_name;

        if (! preg_match('/^company_\d{4}_cases$/', $table)
            || ! Schema::connection('companies')->hasTable($table)
            || ! Schema::connection('companies')->hasColumn($table, 'company_id')) {
            return [];
        }

        // Total cases
        $caseCount = DB::connection('companies')
            ->table($table)
            ->where('company_id', $company->id)
            ->count();

        // Open cases
        $openCases = Schema::connection('companies')->hasColumn($table, 'status')
            ? DB::connection('companies')
                ->table($table)
                ->where('company_id', $company->id)
                ->where('status', 'Open')
                ->count()
            : $caseCount;

        // Total debt
        $totalDebt = 0;

        if (Schema::connection('companies')->hasColumn($table, 'total')) {
            $totalDebt = DB::connection('companies')
                ->table($table)
                ->where('company_id', $company->id)
                ->sum('total');
        }

        // Documents
        $documentCount = DB::table('documents')
            ->where('company_id', $company->id)
            ->count();

        return [
            Stat::make('Total Cases', number_format($caseCount))
                ->description('Cases in this company')
                ->color('primary')
                ->icon('heroicon-o-folder'),

            Stat::make('Documents', number_format($documentCount))
                ->description('Generated documents')
                ->color('success')
                ->icon('heroicon-o-document-text'),

            Stat::make('Total Debt', number_format($totalDebt, 2))
                ->description('Total debt amount')
                ->color('warning')
                ->icon('heroicon-o-banknotes'),

            Stat::make('Open Cases', number_format($openCases))
                ->description('Currently active')
                ->color('danger')
                ->icon('heroicon-o-scale'),
        ];
    }
}
