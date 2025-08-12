<?php

namespace App\Services\Firebase;
use App\Models\Notification;
use App\Models\User;
use App\Models\FcmToken;
use App\Utility\Enums\NotificationTypeEnum;
use Kreait\Firebase\Messaging\CloudMessage;
use Kreait\Firebase\Messaging;
use Illuminate\Support\Facades\Log;

class NotificationService
{
    protected Messaging $messaging;
    public function __construct()
    {
        $this->initializeFirebase();
    }

    protected function initializeFirebase()
    {
        $credentialsPath = env('FIREBASE_CREDENTIALS');
        if (file_exists($credentialsPath)) {
            $this->messaging = (new \Kreait\Firebase\Factory())
                ->withServiceAccount($credentialsPath)
                ->createMessaging();
        } else {
            throw new \Exception("Firebase credentials file not found at: {$credentialsPath}");
        }
    }

    public function sendToUsers(array $userIds, Notification $notification)
    {
        $users = User::whereIn('id', $userIds)->with('fcmTokens')->get();

        $allTokens = $users->flatMap(function ($user) {
            return $user->fcmTokens->pluck('token');
        })->unique()->toArray();

        if (!empty($allTokens)) {
            $responses = [];

            foreach ($allTokens as $token) {
                if (empty($token)) {
                    Log::warning("Skipping notification due to empty token");
                    continue;
                }
                try {
                    $message = CloudMessage::new()
                        ->withNotification([
                            'title' => $notification->jsonSerialize()['title'],
                            'body' => $notification->jsonSerialize()['body'],
                        ])
                        ->withDefaultSounds()
                        ->toToken($token->token);

                    Log::info("Attempting to send notification via FCM to token: {$token} (Device Type: {$token->device_type})");
                    $response = $this->messaging->send($message);
                    $responses[$token->device_type][] = $response;
                    Log::info("Successfully sent notification via FCM to token: {$token}");

                } catch (\Kreait\Firebase\Exception\Messaging\NotFound $e) {
                    Log::error("FCM token {$token} not found or unregistered (device type: {$token->device_type}): " . $e->getMessage());
                } catch (\Kreait\Firebase\Exception\Messaging\InvalidMessage $e) {
                    Log::error("Invalid FCM message for token {$token} (device type: {$token->device_type}): " . $e->getMessage() . " Details: " . json_encode($e->errors()));
                } catch (\Kreait\Firebase\Exception\MessagingException $e) { // Catch other Firebase messaging exceptions
                    Log::error("Firebase Messaging Exception for token {$token} (device type: {$token->device_type}): " . $e->getMessage());
                } catch (\Exception $e) {
                    Log::error("Generic error sending notification to token {$token} (device type: {$token->device_type}): " . $e->getMessage());
                }
            }

        }
        return $responses;
    }


    /**
     * Sends a notification to everyone subscribed to a specific topic.
     *
     * @param string $topic
     * @param string $title
     * @param string $body
     */
    public function sendToTopic(string $topic, Notification $notification)
    {
        $message = CloudMessage::new()
            ->withNotification([
                'title' => $notification->jsonSerialize()['title'],
                'body' => $notification->jsonSerialize()['body'],
            ])->toTopic($topic);

        $this->messaging->send($message);
    }


    public function subscribeUsersToTopic(array $userIds, string $topic)
    {
        $users = User::whereIn('id', $userIds)->with('fcmTokens')->get();

        $allTokens = $users->flatMap(function ($user) {
            return $user->fcmTokens->pluck('token');
        })->unique()->toArray();

        if (!empty($allTokens)) {
            $this->messaging->subscribeToTopic($topic, $allTokens);
        }
    }

    public function send(Notification $notification)
    {
        if ($notification->target_type === NotificationTypeEnum::ALL) {
            $this->sendToTopic('all', $notification);
        } elseif ($notification->target_type === NotificationTypeEnum::SPECIFIC_USER) {
            $this->sendToUsers([$notification->target_id], $notification);
        }
    }

}