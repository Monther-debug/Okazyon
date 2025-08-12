<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Notification;
use App\Services\Firebase\NotificationService;
use Illuminate\Http\Request;

class FCMController extends Controller
{

    protected $fcmService;
    public function __construct()
    {
        $this->fcmService = new NotificationService();
    }
    public function registerToken(Request $request)
    {
        $request->validate([
            'token' => 'required|string',
            'device_id' => 'required|string',
            'device_type' => 'required|in:android,ios',
        ]);
        auth()->user()->fcmTokens()->updateOrCreate(
            ['device_id' => $request->device_id],
            [
                'token' => $request->token,
                'device_type' => $request->device_type,
            ]
        );
        $this->fcmService->subscribeUsersToTopic([auth()->id()], 'general');
        return response()->json(['message' => __('user.fcm_token_registered_successfully')], 200);
    }
}
