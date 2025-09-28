<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\User\NotificationController;
use Illuminate\Support\Facades\Auth;

echo "=== Testing Notification API Endpoints ===\n";

// Get first user and simulate authentication
$user = User::first();
Auth::login($user);

echo "Testing as user: {$user->email} (ID: {$user->id})\n";

// Test unreadCount method
$controller = new NotificationController();
$response = $controller->unreadCount();
$data = json_decode($response->getContent(), true);

echo "Unread count API response: " . json_encode($data) . "\n";

// Test index method (list notifications)
$request = new Request();
$response = $controller->index($request);
$responseData = json_decode($response->getContent(), true);

echo "Notifications list response: " . json_encode($responseData, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . "\n";

echo "\n=== API Test completed ===\n";
