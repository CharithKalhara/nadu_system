<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\UploadMobileAttachmentRequest;
use App\Models\Document;
use App\Models\Nadu;
use App\Support\MobileCompanyAccess;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Throwable;

class AttachmentController extends Controller
{
    public function store(
        UploadMobileAttachmentRequest $request,
        MobileCompanyAccess $companies,
    ): JsonResponse {
        $validated = $request->validated();
        $company = $companies->resolve($request, $validated['company_id']);
        $case = Nadu::query()
            ->where('company_id', $company->getKey())
            ->findOrFail($validated['case_id']);
        $file = $request->file('file');
        $path = $file->store("mobile-attachments/companies/{$company->getKey()}", 'local');

        if (! $path) {
            return response()->json(['message' => 'The attachment could not be stored.'], 500);
        }

        $originalName = Str::limit(
            basename(str_replace('\\', '/', $file->getClientOriginalName())),
            255,
            '',
        );

        try {
            $document = Document::query()->create([
                'company_id' => $company->getKey(),
                'nadu_id' => $case->getKey(),
                'document_type' => 'mobile_attachment',
                'file_name' => $originalName !== '' ? $originalName : 'attachment',
                'file_path' => $path,
                'generated_by' => $request->user()->getKey(),
            ]);
        } catch (Throwable $exception) {
            Storage::disk('local')->delete($path);
            throw $exception;
        }

        Log::info('mobile_api.attachment.uploaded', [
            'user_id' => $request->user()->getKey(),
            'company_id' => $company->getKey(),
            'case_id' => $case->getKey(),
            'document_id' => $document->getKey(),
        ]);

        return response()->json([
            'data' => [
                'id' => $document->getKey(),
                'case_id' => $case->getKey(),
                'file_name' => $document->file_name,
                'created_at' => $document->created_at,
            ],
        ], 201);
    }
}
