<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Company;
use App\Models\Document;
use App\Models\Nadu;
use App\Services\BulkCoverPageService;
use App\Services\BulkEnvelopeService;
use App\Services\BulkHethupataService;
use App\Services\BulkMudrithaPradanayaSecondPageService;
use App\Services\BulkMudrithaPradanayaService;
use App\Services\BulkMulKola2Service;
use App\Services\BulkSithasiService;
use App\Services\BulkStatementService;
use App\Services\BulkThinduwaWrittenService;
use App\Services\BulkThinduwaYawimaService;
use App\Services\BulkThirakawaraJournalService;
use App\Services\BulkWibagaDinaya1Service;
use App\Services\BulkWibagaDinaya2Service;
use App\Services\CoverPageService;
use App\Services\EnvelopeService;
use App\Services\HethupataService;
use App\Services\MulKola2Service;
use App\Services\MudrithaPradanayaSecondPageService;
use App\Services\MudrithaPradanayaService;
use App\Services\SithasiService;
use App\Services\StatementService;
use App\Services\ThinduwaWrittenService;
use App\Services\ThinduwaYawimaService;
use App\Services\ThirakawaraJournalService;
use App\Services\WibagaDinaya1Service;
use App\Services\WibagaDinaya2Service;
use App\Support\CompanyContext;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class DesktopDocumentController extends Controller
{
    private const TYPES = [
        'sithasi', 'envelope', 'statement', 'cover_page', 'thinduwa_yawima', 'thinduwa_written',
        'thirakawara_journal', 'mudritha_pradanaya', 'mudritha_pradanaya_second_page',
        'wibaga_dinaya_1', 'wibaga_dinaya_2', 'mul_kola_2', 'hethupata', 'sithasi_and_envelope',
    ];

    private const BULK_SERVICES = [
        'sithasi' => BulkSithasiService::class,
        'envelope' => BulkEnvelopeService::class,
        'statement' => BulkStatementService::class,
        'cover_page' => BulkCoverPageService::class,
        'thinduwa_yawima' => BulkThinduwaYawimaService::class,
        'thinduwa_written' => BulkThinduwaWrittenService::class,
        'thirakawara_journal' => BulkThirakawaraJournalService::class,
        'mudritha_pradanaya' => BulkMudrithaPradanayaService::class,
        'mudritha_pradanaya_second_page' => BulkMudrithaPradanayaSecondPageService::class,
        'wibaga_dinaya_1' => BulkWibagaDinaya1Service::class,
        'wibaga_dinaya_2' => BulkWibagaDinaya2Service::class,
        'mul_kola_2' => BulkMulKola2Service::class,
        'hethupata' => BulkHethupataService::class,
    ];

    private const COMPANY_SETTING_FIELDS = [
        'nadu_ankaya_format', 'teeraka', 'teeraka_name_with_initials', 'karyalaya',
        'wibhaga_dinaya', 'thinduwa_labadena_dinaya', 'gewia_yuthu_dinaya',
        'abiyachana_idiripath_kala_yuthu_dinaya', 'thepal_gasthu', 'welawa',
        'samithiya_lipinaya', 'niyojithaya', 'niyojithaya_lipinaya_1', 'niyojithaya_lipinaya_2',
        'niyojithaya_lipinaya_3', 'first_sithasiya_date', 'first_sithasiya_post_office',
        'first_sithasiya_receipt_no', 'second_sithasiya_date', 'second_sithasiya_post_office',
        'second_sithasiya_receipt_no', 'wibaga_sthanaya',
    ];

    public function index(Request $request, string $company): JsonResponse
    {
        $this->resolveCompany($request);

        $documents = Document::query()
            ->with(['nadu:id,nadu_ankaya', 'user:id,name'])
            ->latest('created_at')
            ->paginate(min(max((int) $request->query('per_page', 50), 1), 100));

        return response()->json($documents);
    }

    public function options(Request $request, string $company): JsonResponse
    {
        $record = $this->resolveCompany($request);

        return response()->json([
            'types' => self::TYPES,
            'settings' => $record->only(self::COMPANY_SETTING_FIELDS),
            'cases' => Nadu::query()->orderBy('nadu_ankaya')->get(['id', 'nadu_ankaya', 'nayakaru1_nama']),
        ]);
    }

    public function generate(Request $request, string $company): JsonResponse
    {
        $record = $this->resolveCompany($request);
        $data = $request->validate([
            'document_type' => ['required', Rule::in(self::TYPES)],
            'scope' => ['nullable', Rule::in(['all', 'selected'])],
            'nadu_ids' => ['nullable', 'array', 'min:1'],
            'nadu_ids.*' => ['integer'],
            'nadu_id' => ['nullable', 'integer'],
            'nadu_ankaya_format' => ['nullable', 'string', 'max:255'],
            'teeraka' => ['nullable', 'string', 'max:255'],
            'teeraka_name_with_initials' => ['nullable', 'string', 'max:255'],
            'karyalaya' => ['nullable', 'string', 'max:255'],
            'wibhaga_dinaya' => ['nullable', 'date'],
            'thinduwa_labadena_dinaya' => ['nullable', 'date'],
            'gewia_yuthu_dinaya' => ['nullable', 'date'],
            'abiyachana_idiripath_kala_yuthu_dinaya' => ['nullable', 'date'],
            'thepal_gasthu' => ['nullable', 'numeric', 'min:0'],
            'welawa' => ['nullable', 'date_format:H:i'],
            'samithiya_lipinaya' => ['nullable', 'string', 'max:255'],
            'niyojithaya' => ['nullable', 'string', 'max:255'],
            'niyojithaya_lipinaya_1' => ['nullable', 'string', 'max:255'],
            'niyojithaya_lipinaya_2' => ['nullable', 'string', 'max:255'],
            'niyojithaya_lipinaya_3' => ['nullable', 'string', 'max:255'],
            'first_sithasiya_date' => ['nullable', 'date'],
            'first_sithasiya_post_office' => ['nullable', 'string', 'max:255'],
            'first_sithasiya_receipt_no' => ['nullable', 'string', 'max:255'],
            'second_sithasiya_date' => ['nullable', 'date'],
            'second_sithasiya_post_office' => ['nullable', 'string', 'max:255'],
            'second_sithasiya_receipt_no' => ['nullable', 'string', 'max:255'],
            'wibaga_sthanaya' => ['nullable', 'string', 'max:255'],
        ]);

        if ($data['document_type'] !== 'sithasi_and_envelope') {
            validator($data, [
                'scope' => ['required', Rule::in(['all', 'selected'])],
                'nadu_ids' => [($data['scope'] ?? null) === 'selected' ? 'required' : 'nullable', 'array', 'min:1'],
            ])->validate();
        }

        $this->validateRequiredSettings($data);
        $this->saveCompanySettings($record, $data);

        if ($data['document_type'] === 'sithasi_and_envelope') {
            validator($data, ['nadu_id' => ['required', 'integer']])->validate();
            $case = Nadu::query()->findOrFail($data['nadu_id']);
            $documents = [
                app(SithasiService::class)->generate($case),
                app(EnvelopeService::class)->generate($case),
            ];
        } else {
            $ids = $data['scope'] === 'all'
                ? Nadu::query()->pluck('id')->all()
                : array_values(array_unique($data['nadu_ids']));

            abort_if($ids === [], 422, 'No cases are available for document creation.');
            abort_unless(Nadu::query()->whereKey($ids)->count() === count($ids), 422, 'One or more selected cases do not belong to this company.');

            $service = app(self::BULK_SERVICES[$data['document_type']]);
            $document = in_array($data['document_type'], ['mul_kola_2', 'thirakawara_journal', 'thinduwa_written'], true)
                ? $service->createDocumentForNaduIds($ids, $data)
                : $service->createDocumentForNaduIds($ids);
            $documents = [$document];
        }

        return response()->json([
            'message' => 'Document created successfully.',
            'documents' => array_map(fn (Document $document): array => $this->documentData($document), $documents),
        ], 201);
    }

    public function download(Request $request, string $company, string $document): BinaryFileResponse
    {
        $this->resolveCompany($request);
        $record = Document::query()->findOrFail($document);
        $path = storage_path('app/'.$record->file_path);

        abort_unless(is_file($path), 404, 'The generated document file is unavailable.');

        return response()->download($path, $record->file_name);
    }

    public function destroy(Request $request, string $company, string $document): JsonResponse
    {
        $this->resolveCompany($request);
        Document::query()->findOrFail($document)->delete();

        return response()->json(['message' => 'Document record deleted.']);
    }

    private function resolveCompany(Request $request): Company
    {
        abort_unless($request->user()->role === 'admin', 403);
        $record = app(CompanyContext::class)->resolve($request);

        return $record;
    }

    private function saveCompanySettings(Company $company, array $data): void
    {
        $fields = match ($data['document_type']) {
            'sithasi' => ['nadu_ankaya_format', 'teeraka', 'karyalaya', 'wibhaga_dinaya', 'welawa'],
            'thinduwa_yawima' => ['teeraka_name_with_initials', 'thinduwa_labadena_dinaya', 'gewia_yuthu_dinaya', 'abiyachana_idiripath_kala_yuthu_dinaya', 'thepal_gasthu'],
            'thirakawara_journal' => ['samithiya_lipinaya', 'niyojithaya', 'niyojithaya_lipinaya_1', 'niyojithaya_lipinaya_2', 'niyojithaya_lipinaya_3', 'first_sithasiya_date', 'first_sithasiya_post_office', 'first_sithasiya_receipt_no', 'second_sithasiya_date', 'second_sithasiya_post_office', 'second_sithasiya_receipt_no'],
            'mul_kola_2' => ['nadu_ankaya_format', 'samithiya_lipinaya', 'niyojithaya', 'niyojithaya_lipinaya_1', 'niyojithaya_lipinaya_2', 'niyojithaya_lipinaya_3'],
            'thinduwa_written' => ['thinduwa_labadena_dinaya', 'gewia_yuthu_dinaya', 'abiyachana_idiripath_kala_yuthu_dinaya', 'thepal_gasthu'],
            'wibaga_dinaya_1', 'wibaga_dinaya_2' => ['first_sithasiya_date', 'second_sithasiya_date', 'wibaga_sthanaya', 'niyojithaya'],
            default => [],
        };

        $company->fill(Arr::only($data, array_intersect($fields, self::COMPANY_SETTING_FIELDS)))->save();
    }

    private function validateRequiredSettings(array $data): void
    {
        $required = match ($data['document_type']) {
            'sithasi' => ['nadu_ankaya_format', 'teeraka', 'karyalaya', 'wibhaga_dinaya', 'welawa'],
            'thinduwa_yawima' => ['teeraka_name_with_initials', 'thepal_gasthu'],
            'thirakawara_journal' => ['niyojithaya', 'niyojithaya_lipinaya_1', 'first_sithasiya_date', 'first_sithasiya_post_office', 'first_sithasiya_receipt_no'],
            'thinduwa_written' => ['thinduwa_labadena_dinaya', 'gewia_yuthu_dinaya', 'abiyachana_idiripath_kala_yuthu_dinaya', 'thepal_gasthu'],
            'wibaga_dinaya_1', 'wibaga_dinaya_2' => ['first_sithasiya_date', 'second_sithasiya_date', 'wibaga_sthanaya', 'niyojithaya'],
            'mul_kola_2' => ['nadu_ankaya_format', 'samithiya_lipinaya', 'niyojithaya', 'niyojithaya_lipinaya_1'],
            default => [],
        };

        validator($data, collect($required)->mapWithKeys(fn (string $field): array => [$field => ['required']])->all())->validate();
    }

    private function documentData(Document $document): array
    {
        return [
            'id' => $document->id,
            'document_type' => $document->document_type,
            'file_name' => $document->file_name,
            'created_at' => $document->created_at,
        ];
    }
}
