<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Company;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Schema;

class CompanyController extends Controller
{
    public function index(): JsonResponse
    {
        $companies = Company::query()
            ->where('status', 'typing')
            ->orderBy('company_name')
            ->get(['id', 'company_name', 'table_name'])
            ->filter(fn (Company $company): bool => preg_match('/^company_\d{4}_cases$/', (string) $company->table_name) === 1
                    && Schema::connection('companies')->hasTable($company->table_name)
            )
            ->map(fn (Company $company): array => [
                'id' => $company->getKey(),
                'company_name' => $company->company_name,
            ])
            ->values();

        return response()->json(['data' => $companies]);
    }
}
