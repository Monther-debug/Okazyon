<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\General\TempUploadController;
use App\Http\Controllers\General\LocalizationController;

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/upload-image', [TempUploadController::class, 'uploadImage']);
});

// Localization test routes (for testing purposes)
Route::get('/locale', [LocalizationController::class, 'getLocale']);
Route::post('/locale', [LocalizationController::class, 'setLocale']);
