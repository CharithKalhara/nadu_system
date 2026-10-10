<?php

namespace App\Support;

use App\Models\Company;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;

class CompanyContext
{
    public function resolve(Request $request): Company
    {
        $companyId = $request->query('company', session('company_id'));

        abort_unless(filter_var($companyId, FILTER_VALIDATE_INT) !== false && (int) $companyId > 0, 404);

        $company = Company::query()->findOrFail((int) $companyId);

        $this->activate($company);

        return $company;
    }

    public function activate(Company $company): void
    {
        $tableName = (string) $company->table_name;

        abort_unless(
            preg_match('/^company_\d{4}_cases$/', $tableName) === 1
                && Schema::connection('companies')->hasTable($tableName),
            404,
        );

        session([
            'company_id' => $company->getKey(),
            'company_table' => $tableName,
        ]);
    }

    public function id(): ?int
    {
        $companyId = session('company_id');

        return filter_var($companyId, FILTER_VALIDATE_INT) !== false && (int) $companyId > 0
            ? (int) $companyId
            : null;
    }

    public function tableName(): string
    {
        $companyId = $this->id();
        $tableName = session('company_table');

        if ($companyId === null) {
            return 'cases';
        }

        abort_unless(
            is_string($tableName)
                && preg_match('/^company_\d{4}_cases$/', $tableName) === 1
                && Schema::connection('companies')->hasTable($tableName),
            404,
        );

        $company = Company::query()->findOrFail($companyId);
        abort_unless($company->table_name === $tableName, 404);

        return $tableName;
    }
}
