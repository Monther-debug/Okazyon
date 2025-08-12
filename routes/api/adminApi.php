<?php

use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\NotificationController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth:sanctum', 'admin'])->group(function () {
    // User management routes
    Route::apiResource('/users', UserController::class);
    Route::post('/users/{user}/alter-ban', [UserController::class, 'alterBan']);

    // Notification management routes
    Route::apiResource('/notifications', NotificationController::class);
    Route::post('/notifications/{notification}/send', [NotificationController::class, 'send']);
});