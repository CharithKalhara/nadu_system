<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\StoreMobileCaseRequest;
use App\Models\Nadu;
use App\Support\MobileCaseFieldCatalog;
use App\Support\MobileCompanyAccess;
use Illuminate\Contracts\Cache\LockTimeoutException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class CaseController extends Controller
{
    public function index(Request $request, MobileCompanyAccess $companies): JsonResponse
    {
        $filters = $request->validate([
            'company_id' => ['required', 'integer', 'min:1'],
            'search' => ['nullable', 'string', 'max:255'],
            'per_page' => ['nullable', 'integer', 'min:1', 'max:50'],
            'page' => ['nullable', 'integer', 'min:1'],
        ]);
        $company = $companies->resolve($request, $filters['company_id']);
        $query = Nadu::query()->where('company_id', $company->getKey());
        $search = trim($filters['search'] ?? '');

        if ($search !== '') {
            $query->where(function ($query) use ($search): void {
                $pattern = '%'.$search.'%';

                foreach ([
                    'nadu_ankaya',
                    'nayakaru1_nama',
                    'nayakaru2_nama',
                    'aepakaru1_nama',
                    'aepakaru2_nama',
                ] as $column) {
                    $query->orWhere($column, 'like', $pattern);
                }
            });
        }

        $page = $query->latest('created_at')->paginate($filters['per_page'] ?? 15);

        return response()->json([
            'data' => array_map(
                fn (Nadu $case): array => MobileCaseFieldCatalog::serialize($case),
                $page->items(),
            ),
            'meta' => [
                'current_page' => $page->currentPage(),
                'per_page' => $page->perPage(),
                'total' => $page->total(),
            ],
        ]);
    }

    public function show(Request $request, string $id, MobileCompanyAccess $companies): JsonResponse
    {
        $filters = $request->validate(['company_id' => ['required', 'integer', 'min:1']]);
        $company = $companies->resolve($request, $filters['company_id']);
        $case = Nadu::query()
            ->where('company_id', $company->getKey())
            ->findOrFail($id);

        return response()->json(['data' => MobileCaseFieldCatalog::serialize($case)]);
    }

    public function update(
        StoreMobileCaseRequest $request,
        string $id,
        MobileCompanyAccess $companies,
    ): JsonResponse {
        $validated = $request->validated();
        $company = $companies->resolve($request, $validated['company_id']);
        $case = Nadu::query()
            ->where('company_id', $company->getKey())
            ->findOrFail($id);

        $case->fill(Arr::only($validated, MobileCaseFieldCatalog::names()));
        $case->save();

        Log::info('mobile_api.case.updated', [
            'user_id' => $request->user()->getKey(),
            'company_id' => $company->getKey(),
            'case_id' => $case->getKey(),
        ]);

        return response()->json(['data' => MobileCaseFieldCatalog::serialize($case)]);
    }

    public function store(StoreMobileCaseRequest $request, MobileCompanyAccess $companies): JsonResponse
    {
        $validated = $request->validated();
        Log::info('mobile_api.case.submission_attempt', [
            'user_id' => $request->user()->getKey(),
            'company_id' => $validated['company_id'],
        ]);
        $company = $companies->resolve($request, $validated['company_id']);
        Log::info('mobile_api.case.company_resolved', [
            'user_id' => $request->user()->getKey(),
            'company_id' => $company->getKey(),
            'table_name' => $company->table_name,
        ]);
        $caseData = Arr::only($validated, MobileCaseFieldCatalog::names());
        $key = $request->header('Idempotency-Key');

        validator(['key' => $key], [
            'key' => ['required', 'string', 'min:8', 'max:128', 'regex:/^[A-Za-z0-9._:-]+$/'],
        ])->validate();

        ksort($caseData);
        $fingerprint = hash_hmac(
            'sha256',
            json_encode([
                'company_id' => $company->getKey(),
                'case' => $caseData,
            ], JSON_THROW_ON_ERROR),
            (string) config('app.key'),
        );
        $userId = (int) $request->user()->getKey();
        $keyHash = hash('sha256', $key);
        $resultKey = "mobile-api:idempotency:{$userId}:{$keyHash}";
        $lockKey = "mobile-api:idempotency-lock:{$userId}:{$keyHash}";

        try {
            return Cache::lock($lockKey, 300)->block(10, function () use (
                $company,
                $caseData,
                $fingerprint,
                $userId,
                $resultKey,
            ): JsonResponse {
                $existing = Cache::get($resultKey);

                if (is_array($existing)) {
                    if (! hash_equals($existing['fingerprint'], $fingerprint)) {
                        return response()->json([
                            'message' => 'This idempotency key was already used with different case data.',
                        ], 409);
                    }

                    $case = Nadu::query()
                        ->where('company_id', $company->getKey())
                        ->findOrFail($existing['case_id']);

                    return response()->json(
                        ['data' => MobileCaseFieldCatalog::serialize($case)],
                        201,
                        ['Idempotent-Replay' => 'true'],
                    );
                }

                $case = Nadu::query()->create($caseData);

                Cache::put($resultKey, [
                    'fingerprint' => $fingerprint,
                    'case_id' => $case->getKey(),
                ], now()->addDay());

                Log::info('mobile_api.case.created', [
                    'user_id' => $userId,
                    'company_id' => $company->getKey(),
                    'case_id' => $case->getKey(),
                ]);

                return response()->json(
                    ['data' => MobileCaseFieldCatalog::serialize($case)],
                    201,
                );
            });
        } catch (LockTimeoutException) {
            return response()->json([
                'message' => 'A submission with this idempotency key is still being processed. Retry shortly.',
            ], 409);
        }
    }
}
