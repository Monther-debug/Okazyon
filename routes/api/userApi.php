<?php
use App\Http\Controllers\User\AuthController;
use App\Http\Controllers\User\OTPController;
use App\Http\Controllers\User\FCMController;
use App\Http\Controllers\User\UserController;
use App\Http\Controllers\User\NotificationController;
use App\Http\Controllers\User\SellerDashboardController;
use App\Http\Controllers\User\SellerOrderController;
use App\Http\Controllers\API\ProductController;
use App\Http\Controllers\API\OrderController;
use App\Http\Controllers\API\ReviewController;
use App\Http\Controllers\API\FavoriteController;
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
    
    // Product management routes (seller only)
    Route::post('/products', [ProductController::class, 'store']);
    Route::put('/products/{product}', [ProductController::class, 'update']);
    Route::delete('/products/{product}', [ProductController::class, 'destroy']);
    
    // Product review routes (authenticated users only)
    Route::post('/products/{product}/reviews', [ReviewController::class, 'store']);
    
    // Favorites routes (authenticated users only)
    Route::get('/favorites', [FavoriteController::class, 'index']);
    Route::post('/products/{product}/favorite', [FavoriteController::class, 'store']);
    Route::delete('/products/{product}/favorite', [FavoriteController::class, 'destroy']);
    
    // Order management routes (buyer)
    Route::post('/orders', [OrderController::class, 'store']);
    Route::get('/orders', [OrderController::class, 'index']);
    Route::get('/orders/{order}', [OrderController::class, 'show']);
    Route::delete('/orders/{order}', [OrderController::class, 'destroy']); // Cancel order
    
    // Seller dashboard and order management routes
    Route::prefix('seller')->group(function () {
        Route::get('/dashboard', [SellerDashboardController::class, 'index']);
        Route::get('/orders', [SellerOrderController::class, 'index']);
        Route::get('/orders/{order}', [SellerOrderController::class, 'show']);
        Route::put('/orders/{order}', [SellerOrderController::class, 'update']);
    });
});
