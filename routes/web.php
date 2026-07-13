<?php

use Illuminate\Support\Facades\Route;
use App\Models\Nadu;
use App\Services\SithasiService;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/test-sithasi/{id}', function ($id) {
    $case = Nadu::findOrFail($id);

    $path = app(SithasiService::class)->generate($case);

    return response()->download($path);
});