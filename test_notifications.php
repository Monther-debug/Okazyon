<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\User;
use App\Models\Notification;
use Illuminate\Support\Facades\DB;

echo "=== Testing Notification System ===\n";

// Get first user
$user = User::first();
if (!$user) {
    echo "No users found. Please create a user first.\n";
    exit;
}

echo "Testing with user: {$user->email} (ID: {$user->id})\n";

// Count total notifications
$totalNotifications = Notification::count();
echo "Total notifications in system: {$totalNotifications}\n";

// Get notifications available to this user
$userNotifications = Notification::where(function ($query) use ($user) {
    $query->where('target_type', 'all')
        ->orWhere(function ($subQuery) use ($user) {
            $subQuery->where('target_type', 'specific_user')
                ->where('target_id', $user->id);
        });
})->get();

echo "Notifications available to user: {$userNotifications->count()}\n";

// Check read status
$readNotificationIds = DB::table('user_notification_reads')
    ->where('user_id', $user->id)
    ->pluck('notification_id');

echo "Notifications already read: {$readNotificationIds->count()}\n";

$unreadCount = $userNotifications->count() - $readNotificationIds->count();
echo "Unread notifications: {$unreadCount}\n";

// Test marking a notification as read
if ($userNotifications->isNotEmpty()) {
    $firstNotification = $userNotifications->first();
    echo "\nTesting mark as read for notification ID: {$firstNotification->id}\n";
    
    $alreadyRead = DB::table('user_notification_reads')
        ->where('user_id', $user->id)
        ->where('notification_id', $firstNotification->id)
        ->exists();
    
    if (!$alreadyRead) {
        DB::table('user_notification_reads')->insert([
            'user_id' => $user->id,
            'notification_id' => $firstNotification->id,
            'read_at' => now(),
            'created_at' => now(),
            'updated_at' => now()
        ]);
        echo "✓ Marked notification as read\n";
    } else {
        echo "- Notification already marked as read\n";
    }
    
    // Check updated counts
    $newReadCount = DB::table('user_notification_reads')->where('user_id', $user->id)->count();
    $newUnreadCount = $userNotifications->count() - $newReadCount;
    echo "Updated - Read: {$newReadCount}, Unread: {$newUnreadCount}\n";
}

echo "\n=== Test completed ===\n";
