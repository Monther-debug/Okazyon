<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Otp extends Model
{
    protected $fillable = [
        'phone_number',
        'otp_code',
        'expires_at',
        'is_verified',
        'verified_at',
        'purpose',
    ];


    public function verify()
    {
        $this->is_verified = true;
        $this->verified_at = now();
        $this->save();
    }
    public function isExpired()
    {
        return $this->expires_at && now()->greaterThan($this->expires_at);
    }
    public function scopeForPhoneNumber($query, $phoneNumber)
    {
        return $query->where('phone_number', $phoneNumber);
    }
    public function scopeForPurpose($query, $purpose)
    {
        return $query->where('purpose', $purpose);
    }

    public function scopeForOtpCode($query, $otpCode)
    {
        return $query->where('otp_code', $otpCode);
    }

    public function scopeForUnverified($query)
    {
        return $query->where('is_verified', false);
    }

    public function scopeForVerified($query)
    {
        return $query->where('is_verified', true);
    }


    public function isVerifiedUnderIntime()
    {
        return $this->is_verified && $this->verified_at && now()->diffInMinutes($this->verified_at) <= 5;
    }

}
