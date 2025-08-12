<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\UserResource;
use Carbon\Carbon;

class NotificationResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'target_id' => $this->target_id,
            'en_title' => $this->en_title,
            'ar_title' => $this->ar_title,
            'en_body' => $this->en_body,
            'ar_body' => $this->ar_body,
            'target_type' => $this->target_type,
            'status' => $this->status,
            'scheduled_at' => $this->when($this->scheduled_at, function () {
                return Carbon::parse($this->scheduled_at)->setTimezone('Africa/Tripoli')->toDateTimeString();
            }),
            'user' => new UserResource($this->whenLoaded('user')),
        ];
    }
}
