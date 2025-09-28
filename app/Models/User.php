<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Utility\Enums\UserStatusEnum;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class User extends Authenticatable implements HasMedia
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, HasApiTokens, HasRoles, InteractsWithMedia;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'first_name',
        'last_name',
        'phone_number',
        'date_of_birth',
        'gender',
        'password',
        'type',
        'status',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected $casts = [
        'status' => UserStatusEnum::class,
    ];

    /**
     * Get the FCM tokens associated with the user.
     */
    public function fcmTokens()
    {
        return $this->hasMany(FcmToken::class);
    }

    /**
     * Get the notifications associated with the user.
     */
    public function notifications()
    {
        return $this->hasMany(Notification::class);
    }


    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('avatar')
            ->singleFile(); // Only one avatar at a time
    }

    ///// Scopes ////////

    public function scopeStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    public function scopeType($query, $type)
    {
        return $query->where('type', $type);
    }

    public function scopePhoneNumber($query, $phoneNumber)
    {
        return $query->where('phone_number', 'like', "%{$phoneNumber}%");
    }

    public function scopeName($query, $name)
    {
        return $query->whereRaw('LOWER(CONCAT(first_name, " ", last_name)) LIKE ?', ['%' . strtolower($name) . '%']);
    }

    public function scopeGender($query, $gender)
    {
        return $query->whereRaw('LOWER(gender) = ?', [strtolower($gender)]);
    }
    public function scopeDateOfBirth($query, $dateOfBirth)
    {
        return $query->whereDate('date_of_birth', $dateOfBirth);
    }

    /**
     * Get the reviews for the user.
     */
    public function reviews(): HasMany
    {
        return $this->hasMany(Review::class);
    }

    /**
     * Get the user's favorite products.
     */
    public function favorites()
    {
        return $this->belongsToMany(Product::class, 'favorites')->withTimestamps();
    }

    /**
     * Get notifications that this user has read
     */
    public function readNotifications()
    {
        return $this->belongsToMany(Notification::class, 'user_notification_reads')->withTimestamps();
    }
}
