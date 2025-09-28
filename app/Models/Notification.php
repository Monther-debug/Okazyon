<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Utility\Enums\NotificationStatusEnum;
use App\Utility\Enums\NotificationTypeEnum;

use App\Services\Firebase\NotificationService;

class Notification extends Model
{


    protected $fillable = [
        'target_id',
        'en_title',
        'ar_title',
        'en_body',
        'ar_body',
        'target_type',
        'status',
        'scheduled_at',
    ];


    protected $casts = [
        'status' => NotificationStatusEnum::class,
        'target_type' => NotificationTypeEnum::class,
    ];

    /**
     * Get the user that owns the notification.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }


    public function scopeForUser($query, $id)
    {
        return $query->where('target_id', $id);
    }

    public function scopeForType($query, $type)
    {
        return $query->where('target_type', $type);
    }
    public function scopeForStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    public function scopeForTargetType($query, $type)
    {
        return $query->where('target_type', $type);
    }

    /**
     * Get users who have read this notification
     */
    public function readByUsers()
    {
        return $this->belongsToMany(User::class, 'user_notification_reads')->withTimestamps();
    }

    /**
     * Check if a specific user has read this notification
     */
    public function isReadByUser($userId)
    {
        return $this->readByUsers()->where('user_id', $userId)->exists();
    }

}
