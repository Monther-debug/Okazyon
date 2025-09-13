<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\CategoryController;
use App\Http\Controllers\API\ProductController;
use App\Http\Controllers\General\TempUploadController;
use App\Http\Controllers\General\LocalizationController;

// Public routes 
Route::get('/categories', [CategoryController::class, 'index']);
Route::get('/products', [ProductController::class, 'index']);
Route::get('/products/{product}', [ProductController::class, 'show']);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/upload-image', [TempUploadController::class, 'uploadImage']);
});

// Localization test routes (for testing purposes)
Route::get('/locale', [LocalizationController::class, 'getLocale']);
Route::post('/locale', [LocalizationController::class, 'setLocale']);
