<?php

namespace App\Support;

use App\Models\Company;
use Illuminate\Http\Request;

class TypingCompanyContext
{
    public function resolve(Request $request): Company
    {
        $companyId = $request->query('company', session('typing_company_id'));

        abort_unless(filter_var($companyId, FILTER_VALIDATE_INT) !== false && (int) $companyId > 0, 404);

        $company = Company::query()->findOrFail((int) $companyId);

        abort_unless(preg_match('/^company_\d{4}_cases$/', $company->table_name), 404);

        session([
            'typing_company_id' => $company->getKey(),
            'typing_company_table' => $company->table_name,
        ]);

        return $company;
    }
}
