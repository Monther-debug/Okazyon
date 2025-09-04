<?php
use App\Http\Controllers\User\AuthController;
use App\Http\Controllers\User\OTPController;
use App\Http\Controllers\User\FCMController;
use App\Http\Controllers\User\UserController;
use App\Http\Controllers\User\NotificationController;
use Illuminate\Support\Facades\Route;

Route::middleware('throttle:otp')->group(function () {
    Route::post('/sendotp', [OTPController::class, 'generateOTP']);
    Route::post('/verifyotp', [OTPController::class, 'verifyOTP']);
});

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::post('/reset-password', [AuthController::class, 'reSetPassword']);

Route::middleware('auth:sanctum')->group(function () {
    // Auth routes
    Route::post('/logout', [AuthController::class, 'logout']);

    // FCM routes
    Route::post('/fcm-token', [FCMController::class, 'registerToken']);

    // User profile routes
    Route::get('/profile', [UserController::class, 'profile']);
    Route::put('/profile', [UserController::class, 'updateProfile']);
    Route::post('/change-password', [UserController::class, 'changePassword']);
    
    // Notification routes
    Route::get('/notifications', [NotificationController::class, 'index']);
    Route::get('/notifications/{notification}', [NotificationController::class, 'show']);
});
