<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Notification\CreateNotificationRequest;
use App\Http\Requests\Admin\Notification\UpdateNotificationRequest;
use App\Services\Firebase\NotificationService;
use App\Utility\Enums\NotificationStatusEnum;
use Illuminate\Http\Request;
use Spatie\QueryBuilder\QueryBuilder;
use App\Models\Notification;
use App\Http\Resources\NotificationResource;
use Spatie\QueryBuilder\AllowedFilter;
use Carbon\Carbon;


class NotificationController extends Controller
{
    protected $notificationService;

    public function __construct()
    {
        $this->notificationService = new NotificationService();
    }

    public function index(Request $request)
    {
        $notifications = QueryBuilder::for(Notification::class)
            ->allowedFilters([
                AllowedFilter::exact('id'),
                AllowedFilter::scope('status', 'status'),
            ])
            ->paginate(10);

        return NotificationResource::collection($notifications);
    }

    public function store(CreateNotificationRequest $request)
    {
        $validated = $request->validated();

        if (isset($validated['scheduled_at'])) {
            $userDateTime = $validated['scheduled_at'];
            $scheduleTime = Carbon::parse($userDateTime, 'Africa/Tripoli');
            $validated['scheduled_at'] = $scheduleTime->setTimezone('UTC');
        }

        $notification = Notification::create([
            'target_id' => $validated['target_id'],
            'en_title' => $validated['en_title'],
            'ar_title' => $validated['ar_title'],
            'en_body' => $validated['en_body'],
            'ar_body' => $validated['ar_body'],
            'target_type' => $validated['target_type'],
            'status' => NotificationStatusEnum::PENDING,
            'scheduled_at' => $validated['scheduled_at'] ?? null,
        ]);

        return new NotificationResource($notification);
    }


    public function show(Notification $notification)
    {
        return new NotificationResource($notification);
    }


    public function update(UpdateNotificationRequest $request, Notification $notification)
    {
        $validated = $request->validated();
        if (isset($validated['scheduled_at'])) {
            $userDateTime = $validated['scheduled_at'];
            $scheduleTime = Carbon::parse($userDateTime, 'Africa/Tripoli');
            $validated['scheduled_at'] = $scheduleTime->setTimezone('UTC');
        }
        $notification->update($validated);

        return new NotificationResource($notification);
    }

    public function destroy(Notification $notification)
    {
        if ($notification->status === NotificationStatusEnum::SENT) {
            return response()->json(['message' => "Notification with ID: {$notification->id} is already sent and cannot be deleted."], 400);
        }
        $notification->delete();
        return response()->noContent();
    }


    public function send(Notification $notification)
    {
        if ($notification->status === NotificationStatusEnum::SENT) {
            return response()->json(['message' => "Notification with ID: {$notification->id} is already sent."], 400);
        }
        $this->notificationService->send($notification);

        $notification->update(['status' => NotificationStatusEnum::SENT]);

        return new NotificationResource($notification);
    }

}
