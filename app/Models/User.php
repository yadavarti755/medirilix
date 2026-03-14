<?php

namespace App\Models;

use App\Models\DivisionPortal\Employee;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Config;
use Spatie\Permission\Traits\HasRoles;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;
use Rappasoft\LaravelAuthenticationLog\Traits\AuthenticationLoggable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, HasRoles, SoftDeletes, LogsActivity, AuthenticationLoggable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'profile_image',
        'email',
        'google_id',
        'avatar',
        'password',
        'mobile_number',
        'created_by',
        'updated_by',
        'current_session_id',
        'last_login_at',
        'login_ip',
        'user_agent',
        'lockout_until',
        'failed_logins',
    ];

    protected $appends = ['profile_image_full_path'];

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
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'last_login_at' => 'datetime',
            'lockout_until' => 'datetime',
        ];
    }

    public function getProfileImageFullPathAttribute()
    {
        return asset('storage' . Config::get('file_paths')['USER_PROFILE_IMAGE_PATH'] . '/' . $this->profile_image);
    }

    // 📝 Spatie activity log configuration
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logAll() // logs all fillable attributes
            ->useLogName('user') // label used in the activity_log table
            ->logOnlyDirty() // only log changes (not unchanged updates)
            ->setDescriptionForEvent(fn(string $eventName) => "User model has been {$eventName}");
    }

    public function employee()
    {
        return $this->hasOne(Employee::class);
    }

    /**
     * Check if user is logged in from another session
     */
    public function isLoggedInElsewhere($currentSessionId = null)
    {
        $currentSessionId = $currentSessionId ?: session()->getId();
        return !empty($this->current_session_id) && $this->current_session_id !== $currentSessionId;
    }

    /**
     * Update user session information
     */
    public function updateSessionInfo($sessionId = null, $ip = null, $userAgent = null)
    {
        $this->update([
            'current_session_id' => $sessionId ?: session()->getId(),
            'last_login_at' => now(),
            'login_ip' => $ip ?: request()->ip(),
            'user_agent' => $userAgent ?: request()->userAgent()
        ]);
    }

    /**
     * Clear user session information
     */
    public function clearSessionInfo()
    {
        $this->update([
            'current_session_id' => null,
            'login_ip' => null,
            'user_agent' => null
        ]);
    }

    /**
     * Check if the user is currently locked out.
     */
    public function isLockedOut(): bool
    {
        return $this->lockout_until && now()->lessThan($this->lockout_until);
    }

    /**
     * Register a failed login attempt.
     * If 4 failures, lock the user for 24 hours.
     */
    public function registerFailedLogin(): void
    {
        $this->increment('failed_logins');

        if ($this->failed_logins >= 5) {
            $this->lockout_until = now()->addHour(); // ⏱️ 1 hours lock
            $this->save();
        }
    }

    /**
     * Reset failed login attempts & lockout.
     * Should be called on successful login.
     */
    public function resetLoginAttempts(): void
    {
        $this->update([
            'failed_logins' => 0,
            'lockout_until' => null,
        ]);
    }
}
