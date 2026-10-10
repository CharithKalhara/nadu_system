<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Nadu;
use App\Support\CompanyContext;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;

class DesktopCaseController extends Controller
{
    public function index(Request $request, string $company): JsonResponse
    {
        $this->resolveCompany($request);
        $query = Nadu::query()->orderByDesc('id');
        $search = trim((string) $request->query('search', ''));

        if ($search !== '') {
            $query->where(function ($query) use ($search): void {
                $query->where('nadu_ankaya', 'like', "%{$search}%")
                    ->orWhere('nayakaru1_nama', 'like', "%{$search}%")
                    ->orWhere('nayakaru1_samajika_ankaya', 'like', "%{$search}%");
            });
        }

        return response()->json($query->paginate(min(max((int) $request->query('per_page', 50), 1), 100)));
    }

    public function show(Request $request, string $company, string $record): JsonResponse
    {
        $this->resolveCompany($request);

        return response()->json(['case' => Nadu::query()->findOrFail($record)]);
    }

    public function store(Request $request, string $company): JsonResponse
    {
        $record = $this->resolveCompany($request);
        $data = $this->validatedData($request, $record->table_name, true);
        $case = new Nadu($data);
        $case->company_id = $record->id;
        $case->save();

        return response()->json(['case' => $case->fresh()], 201);
    }

    public function update(Request $request, string $company, string $record): JsonResponse
    {
        $recordCompany = $this->resolveCompany($request);
        $case = Nadu::query()->findOrFail($record);
        $case->fill($this->validatedData($request, $recordCompany->table_name, false));
        $case->save();

        return response()->json(['case' => $case->fresh()]);
    }

    public function destroy(Request $request, string $company, string $record): JsonResponse
    {
        $this->resolveCompany($request);
        Nadu::query()->findOrFail($record)->delete();

        return response()->json(['message' => 'Case deleted.']);
    }

    private function resolveCompany(Request $request)
    {
        $company = app(CompanyContext::class)->resolve($request);

        abort_if($request->user()->role !== 'admin' && $company->status !== 'typing', 403);

        return $company;
    }

    private function validatedData(Request $request, string $table, bool $creating): array
    {
        $columns = array_diff(
            Schema::connection('companies')->getColumnListing($table),
            ['id', 'company_id', 'created_at', 'updated_at'],
        );
        $rules = [];
        $numeric = [
            'dun_naya_mudala', 'arawul_mudala', 'poli_prathishathaya', 'apal_pa', 'apal_vi',
            'mul_mudala', 'poliya', 'nadu_gasthu', 'total',
        ];
        $dates = ['dun_dinaya', 'awasan_mudal_bendima'];

        foreach ($columns as $column) {
            $rules[$column] = match (true) {
                in_array($column, $numeric, true) => ['sometimes', 'nullable', 'numeric'],
                in_array($column, $dates, true) => ['sometimes', 'nullable', 'date'],
                $column === 'dina_ganuna' => ['sometimes', 'nullable', 'integer'],
                default => ['sometimes', 'nullable', 'string', 'max:255'],
            };
        }

        if ($creating) {
            $rules['nadu_ankaya'] = ['required', 'string', 'max:255'];
        } else {
            $rules['nadu_ankaya'] = ['sometimes', 'required', 'string', 'max:255'];
        }

        return $request->validate($rules);
    }
}
