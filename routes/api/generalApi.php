<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\General\TempUploadController;

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/upload-image', [TempUploadController::class, 'uploadImage']);
});
