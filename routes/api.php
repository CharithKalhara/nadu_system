<?php

use App\Http\Controllers\Api\V1\AttachmentController;
use App\Http\Controllers\Api\V1\AuthController;
use App\Http\Controllers\Api\V1\CaseController;
use App\Http\Controllers\Api\V1\CompanyController;
use App\Http\Controllers\Api\V1\ReferenceDataController;
use App\Http\Middleware\EnsureMobileTypingStaff;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function (): void {
    Route::post('auth/login', [AuthController::class, 'login'])
        ->middleware('throttle:6,1');

    Route::middleware([
        'auth:sanctum',
        EnsureMobileTypingStaff::class,
        'throttle:120,1',
    ])->group(function (): void {
        Route::post('auth/logout', [AuthController::class, 'logout']);
        Route::get('me', [AuthController::class, 'me']);
        Route::get('companies', [CompanyController::class, 'index']);
        Route::get('reference-data', [ReferenceDataController::class, 'index']);
        Route::get('cases', [CaseController::class, 'index']);
        Route::post('cases', [CaseController::class, 'store']);
        Route::get('cases/{id}', [CaseController::class, 'show'])->whereNumber('id');
        Route::post('attachments', [AttachmentController::class, 'store']);
    });
});
