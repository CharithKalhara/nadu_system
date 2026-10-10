<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Company;
use App\Models\Document;
use App\Models\Nadu;
use App\Support\CaseTableSchema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class DesktopCompanyController extends Controller
{
    private const COMPANY_FIELDS = [
        'nadu_ankaya_format', 'teeraka', 'teeraka_name_with_initials', 'karyalaya',
        'wibhaga_dinaya', 'thinduwa_labadena_dinaya', 'gewia_yuthu_dinaya',
        'abiyachana_idiripath_kala_yuthu_dinaya', 'thepal_gasthu', 'welawa',
        'samithiya_lipinaya', 'niyojithaya', 'niyojithaya_lipinaya_1',
        'niyojithaya_lipinaya_2', 'niyojithaya_lipinaya_3', 'first_sithasiya_date',
        'first_sithasiya_post_office', 'first_sithasiya_receipt_no', 'second_sithasiya_date',
        'second_sithasiya_post_office', 'second_sithasiya_receipt_no', 'wibaga_sthanaya',
    ];

    public function dashboard(Request $request): JsonResponse
    {
        $companies = $this->visibleCompanies($request);
        $records = 0;

        foreach ($companies as $company) {
            if ($this->validCaseTable($company)) {
                $records += DB::connection('companies')->table($company->table_name)->count();
            }
        }

        $documents = Document::withoutGlobalScope('company_context')->count();

        return response()->json([
            'companies' => $companies->count(),
            'records' => $records,
            'documents' => $request->user()->role === 'admin' ? $documents : null,
            'users' => $request->user()->role === 'admin' ? \App\Models\User::query()->count() : null,
        ]);
    }

    public function index(Request $request): JsonResponse
    {
        return response()->json([
            'data' => $this->visibleCompanies($request)->map(fn (Company $company): array => $this->companyData($company))->values(),
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        abort_unless(in_array($request->user()->role, ['admin', 'typing'], true), 403);
        $data = $request->validate([
            'company_name' => ['required', 'string', 'max:255'],
            'status' => [$request->user()->role === 'admin' ? 'required' : 'nullable', 'in:typing,completed'],
        ]);
        $data['status'] = $request->user()->role === 'admin' ? $data['status'] : 'typing';
        $usedNumbers = collect(DB::connection('companies')->select('SHOW TABLES'))
            ->map(fn (object $table) => array_values((array) $table)[0])
            ->map(fn (string $table) => preg_match('/^company_(\d{4})_cases$/', $table, $matches) ? (int) $matches[1] : null)
            ->filter()
            ->all();
        $number = 1;

        while (in_array($number, $usedNumbers, true)) {
            $number++;
        }

        $tableName = 'company_'.str_pad((string) $number, 4, '0', STR_PAD_LEFT).'_cases';
        Schema::connection('companies')->create($tableName, function (Blueprint $table): void {
            CaseTableSchema::define($table);
        });

        try {
            $company = Company::query()->create([
                ...$data,
                'table_name' => $tableName,
            ]);
        } catch (\Throwable $exception) {
            Schema::connection('companies')->dropIfExists($tableName);

            throw $exception;
        }

        return response()->json(['company' => $this->companyData($company)], 201);
    }

    public function show(Request $request, string $company): JsonResponse
    {
        $record = $this->findVisibleCompany($request, $company);
        $tableExists = $this->validCaseTable($record);

        return response()->json([
            'company' => $this->companyData($record),
            'records_count' => $tableExists ? DB::connection('companies')->table($record->table_name)->count() : 0,
            'documents_count' => $request->user()->role === 'admin'
                ? Document::withoutGlobalScope('company_context')->where('company_id', $record->id)->count()
                : null,
        ]);
    }

    public function update(Request $request, string $company): JsonResponse
    {
        abort_unless($request->user()->role === 'admin', 403);
        $record = Company::query()->findOrFail($company);
        $fields = array_merge(self::COMPANY_FIELDS, ['company_name', 'status']);
        $data = $request->validate([
            'company_name' => ['sometimes', 'required', 'string', 'max:255'],
            'status' => ['sometimes', 'required', 'in:typing,completed'],
        ] + collect(self::COMPANY_FIELDS)->mapWithKeys(fn (string $field): array => [$field => ['sometimes', 'nullable', 'string', 'max:255']])->all());

        $record->fill(array_intersect_key($data, array_flip($fields)))->save();

        return response()->json(['company' => $this->companyData($record->fresh())]);
    }

    public function destroy(Request $request, string $company): JsonResponse
    {
        abort_unless($request->user()->role === 'admin', 403);
        $record = Company::query()->findOrFail($company);
        abort_unless($this->validCaseTable($record), 409, 'The company case table is unavailable.');

        Schema::connection('companies')->drop($record->table_name);
        $record->delete();

        return response()->json(['message' => 'Company and its case table deleted.']);
    }

    private function visibleCompanies(Request $request)
    {
        $query = Company::query()->orderBy('company_name');

        if ($request->user()->role !== 'admin') {
            $query->where('status', 'typing');
        }

        return $query->get();
    }

    private function findVisibleCompany(Request $request, string $id): Company
    {
        $query = Company::query()->whereKey($id);

        if ($request->user()->role !== 'admin') {
            $query->where('status', 'typing');
        }

        return $query->firstOrFail();
    }

    private function companyData(Company $company): array
    {
        return array_merge([
            'id' => $company->id,
            'company_name' => $company->company_name,
            'status' => $company->status,
        ], $company->only(self::COMPANY_FIELDS));
    }

    private function validCaseTable(Company $company): bool
    {
        return preg_match('/^company_\d{4}_cases$/', (string) $company->table_name) === 1
            && Schema::connection('companies')->hasTable($company->table_name);
    }
}
