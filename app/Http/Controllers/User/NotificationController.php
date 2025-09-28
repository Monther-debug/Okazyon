<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Http\Resources\NotificationResource;
use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Spatie\QueryBuilder\QueryBuilder;
use Spatie\QueryBuilder\AllowedFilter;

class NotificationController extends Controller
{
    /**
     * Get user notifications
     */
    public function index(Request $request)
    {
        $notifications = QueryBuilder::for(Notification::class)
            ->where(function ($query) {
                $query->where('target_type', 'all')
                    ->orWhere(function ($subQuery) {
                        $subQuery->where('target_type', 'specific_user')
                            ->where('target_id', Auth::id());
                    });
            })
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return NotificationResource::collection($notifications);
    }

    /**
     * Get specific notification
     */
    public function show(Notification $notification)
    {
        // Check if user can view this notification
        if ($notification->target_type === 'specific_user' && $notification->target_id !== Auth::id()) {
            return response()->json(['message' => __('auth.unauthorized')], 403);
        }

        return new NotificationResource($notification);
    }

    /**
     * Get unread notifications count for the current user
     */
    public function unreadCount()
    {
        $userId = Auth::id();
        
        // Get all notifications for this user (both 'all' and 'specific_user')
        $allNotificationIds = Notification::where(function ($query) use ($userId) {
            $query->where('target_type', 'all')
                ->orWhere(function ($subQuery) use ($userId) {
                    $subQuery->where('target_type', 'specific_user')
                        ->where('target_id', $userId);
                });
        })->pluck('id');

        // Get notification IDs that user has already read
        $readNotificationIds = \DB::table('user_notification_reads')
            ->where('user_id', $userId)
            ->whereIn('notification_id', $allNotificationIds)
            ->pluck('notification_id');

        // Calculate unread count
        $unreadCount = $allNotificationIds->count() - $readNotificationIds->count();

        return response()->json([
            'unread_count' => $unreadCount
        ]);
    }

    /**
     * Mark a notification as read for the current user
     */
    public function markAsRead(Notification $notification)
    {
        $userId = Auth::id();

        // Check if user can read this notification
        if ($notification->target_type === 'specific_user' && $notification->target_id !== $userId) {
            return response()->json(['message' => __('auth.unauthorized')], 403);
        }

        // Check if already read
        $alreadyRead = \DB::table('user_notification_reads')
            ->where('user_id', $userId)
            ->where('notification_id', $notification->id)
            ->exists();

        if (!$alreadyRead) {
            \DB::table('user_notification_reads')->insert([
                'user_id' => $userId,
                'notification_id' => $notification->id,
                'read_at' => now(),
                'created_at' => now(),
                'updated_at' => now()
            ]);
        }

        return response()->json([
            'message' => 'Notification marked as read',
            'is_read' => true
        ]);
    }

    /**
     * Mark all notifications as read for the current user
     */
    public function markAllAsRead()
    {
        $userId = Auth::id();
        
        // Get all notifications for this user
        $allNotificationIds = Notification::where(function ($query) use ($userId) {
            $query->where('target_type', 'all')
                ->orWhere(function ($subQuery) use ($userId) {
                    $subQuery->where('target_type', 'specific_user')
                        ->where('target_id', $userId);
                });
        })->pluck('id');

        // Get notification IDs that user has NOT read yet
        $readNotificationIds = \DB::table('user_notification_reads')
            ->where('user_id', $userId)
            ->whereIn('notification_id', $allNotificationIds)
            ->pluck('notification_id');

        $unreadNotificationIds = $allNotificationIds->diff($readNotificationIds);

        // Mark unread notifications as read
        if ($unreadNotificationIds->isNotEmpty()) {
            $insertData = $unreadNotificationIds->map(function ($notificationId) use ($userId) {
                return [
                    'user_id' => $userId,
                    'notification_id' => $notificationId,
                    'read_at' => now(),
                    'created_at' => now(),
                    'updated_at' => now()
                ];
            })->toArray();

            \DB::table('user_notification_reads')->insert($insertData);
        }

        return response()->json([
            'message' => 'All notifications marked as read',
            'marked_count' => $unreadNotificationIds->count()
        ]);
    }
}
