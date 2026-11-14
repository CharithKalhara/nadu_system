<?php

namespace App\Support;

use App\Models\Company;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;

class MobileCompanyAccess
{
    public function resolve(Request $request, mixed $companyId): Company
    {
        abort_unless(
            is_scalar($companyId)
                && filter_var($companyId, FILTER_VALIDATE_INT) !== false
                && (int) $companyId > 0,
            404,
        );

        $company = Company::query()
            ->where('status', 'typing')
            ->findOrFail((int) $companyId);

        $tableName = (string) $company->table_name;

        abort_unless(
            preg_match('/^company_\d{4}_cases$/', $tableName) === 1
                && Schema::connection('companies')->hasTable($tableName),
            404,
        );

        // Existing Nadu scopes resolve their dynamic table from CompanyContext.
        // Set this only after validating the company against the typing queue.
        // FormRequest is a clone, while existing model hooks read the base request.
        $companyId = (string) $company->getKey();
        $request->query->set('company', $companyId);
        app('request')->query->set('company', $companyId);
        app(CompanyContext::class)->activate($company);

        return $company;
    }
}
