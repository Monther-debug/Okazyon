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
}
