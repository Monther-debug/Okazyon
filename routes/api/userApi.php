<?php
use App\Http\Controllers\User\AuthController;
use App\Http\Controllers\User\OTPController;
use App\Http\Controllers\User\FCMController;

Route::middleware('throttle:otp')->group(function () {
    Route::post('/sendotp', [OTPController::class, 'generateOTP']);
    Route::post('/verifyotp', [OTPController::class, 'verifyOTP']);
});

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::post('/reset-password', [AuthController::class, 'resetPassword']);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::post('/fcm-token', [FCMController::class, 'registerToken']);
});
