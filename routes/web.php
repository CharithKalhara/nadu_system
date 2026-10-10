<?php

use App\Models\Document;
use App\Models\Nadu;
use App\Services\SithasiService;
use App\Support\CompanyContext;
use Illuminate\Support\Facades\Route;

Route::middleware('auth')->get('/test-sithasi/{id}', function ($id) {
    app(CompanyContext::class)->resolve(request());

    $case = Nadu::findOrFail($id);

    $document = app(SithasiService::class)->generate($case);

    return response()->download(
        storage_path('app/'.$document->file_path),
        $document->file_name,
    );
});

Route::middleware('auth')->get('/documents/{documentId}/download', function (string $documentId) {
    app(CompanyContext::class)->resolve(request());

    $document = Document::query()->findOrFail($documentId);

    return response()->download(
        storage_path('app/'.$document->file_path),
        $document->file_name,
    );
})->name('documents.download');
