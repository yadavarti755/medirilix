<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;
use Carbon\Carbon;

class VerificationCode extends Model
{
    use SoftDeletes, LogsActivity;

    protected $fillable = [
        'email_id',
        'code',
        'expiration_time',
        'otp_used',
    ];

    protected $casts = [
        'expiration_time' => 'datetime',
        'otp_used' => 'integer',
        'code' => 'integer',
    ];

    protected $appends = ['is_expired', 'is_used_desc', 'time_remaining'];

    /**
     * Check if OTP is expired
     */
    public function getIsExpiredAttribute()
    {
        return Carbon::now()->gt($this->expiration_time);
    }

    /**
     * Get OTP used status description
     */
    public function getIsUsedDescAttribute()
    {
        return $this->otp_used ? 'Used' : 'Unused';
    }

    /**
     * Get time remaining for OTP expiration
     */
    public function getTimeRemainingAttribute()
    {
        if ($this->is_expired) {
            return 'Expired';
        }

        $now = Carbon::now();
        $expiration = Carbon::parse($this->expiration_time);
        $diff = $now->diffInMinutes($expiration);

        return $diff . ' minutes';
    }

    /**
     * Scope to get only valid (unused and not expired) codes
     */
    public function scopeValid($query)
    {
        return $query->where('otp_used', 0)
            ->where('expiration_time', '>', Carbon::now());
    }

    /**
     * Scope to get codes by email
     */
    public function scopeByEmail($query, $email)
    {
        return $query->where('email_id', $email);
    }

    /**
     * Scope to get expired codes
     */
    public function scopeExpired($query)
    {
        return $query->where('expiration_time', '<', Carbon::now());
    }

    /**
     * Scope to get used codes
     */
    public function scopeUsed($query)
    {
        return $query->where('otp_used', 1);
    }

    /**
     * Relationship with User (if needed)
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'email_id', 'email');
    }

    // 📝 Spatie activity log configuration
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logAll() // logs all fillable attributes
            ->useLogName('verification_code') // label for this model in logs
            ->logOnlyDirty() // logs only changed fields
            ->setDescriptionForEvent(fn(string $eventName) => "Verification code has been {$eventName}");
    }
}
