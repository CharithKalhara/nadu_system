<?php

use App\Models\Document;
use App\Models\Nadu;
use App\Services\SithasiService;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/test-sithasi/{id}', function ($id) {
    $case = Nadu::findOrFail($id);

    $path = app(SithasiService::class)->generate($case);

    return response()->download($path);
});

Route::middleware('auth')->get('/documents/{document}/download', function (Document $document) {
    abort_unless((int) $document->company_id === (int) session('company_id'), 403);

    return response()->download(
        storage_path('app/'.$document->file_path),
        $document->file_name,
    );
})->name('documents.download');
