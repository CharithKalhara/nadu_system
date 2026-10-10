<?php

namespace App\Support;

use App\Models\Company;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;

class CompanyContext
{
    public function resolve(Request $request): Company
    {
        $companyId = $this->requestedCompanyId($request);

        abort_unless(
            is_scalar($companyId)
                && filter_var($companyId, FILTER_VALIDATE_INT) !== false
                && (int) $companyId > 0,
            404,
        );

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
    }

    public function id(): ?int
    {
        $companyId = $this->requestedCompanyId(request());

        return is_scalar($companyId)
            && filter_var($companyId, FILTER_VALIDATE_INT) !== false
            && (int) $companyId > 0
            ? (int) $companyId
            : null;
    }

    public function company(): ?Company
    {
        $companyId = $this->id();

        return $companyId ? Company::query()->find($companyId) : null;
    }

    public function companyOrFail(): Company
    {
        $companyId = $this->id();

        abort_unless($companyId !== null, 404);

        return Company::query()->findOrFail($companyId);
    }

    public function tableName(): string
    {
        $companyId = $this->id();

        abort_unless($companyId !== null, 404);

        $company = Company::query()->findOrFail($companyId);
        $tableName = (string) $company->table_name;

        abort_unless(
            preg_match('/^company_\d{4}_cases$/', $tableName) === 1
                && Schema::connection('companies')->hasTable($tableName),
            404,
        );

        return $tableName;
    }

    private function requestedCompanyId(Request $request): mixed
    {
        $companyId = $request->query('company');

        if ($companyId || ! $request->hasHeader('X-Livewire')) {
            return $companyId;
        }

        $referer = $request->headers->get('referer');
        $query = $referer ? parse_url($referer, PHP_URL_QUERY) : null;
        $parameters = [];

        if (is_string($query)) {
            parse_str($query, $parameters);
        }

        return $parameters['company'] ?? null;
    }
}
