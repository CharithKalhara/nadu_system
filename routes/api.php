<?php

use App\Http\Controllers\Api\DesktopAuthController;
use App\Http\Controllers\Api\DesktopCaseController;
use App\Http\Controllers\Api\DesktopCompanyController;
use App\Http\Controllers\Api\DesktopDocumentController;
use App\Http\Controllers\Api\DesktopUserController;
use App\Http\Middleware\AuthenticateDesktopToken;
use Illuminate\Support\Facades\Route;

Route::prefix('desktop')->group(function (): void {
    Route::post('/login', [DesktopAuthController::class, 'login'])->middleware('throttle:8,1');

    Route::middleware(AuthenticateDesktopToken::class)->group(function (): void {
        Route::get('/me', [DesktopAuthController::class, 'me']);
        Route::post('/logout', [DesktopAuthController::class, 'logout']);
        Route::get('/dashboard', [DesktopCompanyController::class, 'dashboard']);
        Route::get('/companies', [DesktopCompanyController::class, 'index']);
        Route::post('/companies', [DesktopCompanyController::class, 'store']);
        Route::get('/companies/{company}', [DesktopCompanyController::class, 'show']);
        Route::patch('/companies/{company}', [DesktopCompanyController::class, 'update']);
        Route::delete('/companies/{company}', [DesktopCompanyController::class, 'destroy']);

        Route::get('/companies/{company}/cases', [DesktopCaseController::class, 'index']);
        Route::post('/companies/{company}/cases', [DesktopCaseController::class, 'store']);
        Route::get('/companies/{company}/cases/{record}', [DesktopCaseController::class, 'show']);
        Route::put('/companies/{company}/cases/{record}', [DesktopCaseController::class, 'update']);
        Route::delete('/companies/{company}/cases/{record}', [DesktopCaseController::class, 'destroy']);

        Route::get('/companies/{company}/documents/options', [DesktopDocumentController::class, 'options']);
        Route::get('/companies/{company}/documents', [DesktopDocumentController::class, 'index']);
        Route::post('/companies/{company}/documents', [DesktopDocumentController::class, 'generate']);
        Route::get('/companies/{company}/documents/{document}/download', [DesktopDocumentController::class, 'download']);
        Route::delete('/companies/{company}/documents/{document}', [DesktopDocumentController::class, 'destroy']);

        Route::get('/users', [DesktopUserController::class, 'index']);
        Route::post('/users', [DesktopUserController::class, 'store']);
        Route::put('/users/{user}', [DesktopUserController::class, 'update']);
        Route::delete('/users/{user}', [DesktopUserController::class, 'destroy']);
    });
});
