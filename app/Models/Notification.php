<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Services\Firebase\NotificationService;

class Notification extends Model
{


    protected $fillable = [
        'user_id',
        'en_title',
        'ar_title',
        'en_body',
        'ar_body',
        'type',
        'status',
    ];


    /**
     * Get the user that owns the notification.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

}
