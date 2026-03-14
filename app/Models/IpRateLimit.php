<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class IpRateLimit extends Model
{
    protected $fillable = [
        'ip_address',
        'action',
        'attempts',
        'first_attempt_at',
        'last_attempt_at',
        'blocked_until'
    ];

    protected $casts = [
        'first_attempt_at' => 'datetime',
        'last_attempt_at' => 'datetime',
        'blocked_until' => 'datetime',
    ];

    /**
     * Check if IP is currently blocked
     */
    public function isBlocked(): bool
    {
        return $this->blocked_until && $this->blocked_until->isFuture();
    }

    /**
     * Check if rate limit window has expired
     */
    public function isWindowExpired(): bool
    {
        return $this->first_attempt_at->addMinutes(20)->isPast();
    }

    /**
     * Reset the rate limit record
     */
    public function reset(): void
    {
        $this->update([
            'attempts' => 0,
            'first_attempt_at' => now(),
            'last_attempt_at' => now(),
            'blocked_until' => null
        ]);
    }

    /**
     * Increment attempt count
     */
    public function incrementAttempts(): void
    {
        $this->increment('attempts');
        $this->update(['last_attempt_at' => now()]);
    }

    /**
     * Block the IP address
     */
    public function blockIp(): void
    {
        $this->update([
            'blocked_until' => now()->addHour() // Block for 1 hour
        ]);
    }

    /**
     * Get or create rate limit record for IP and action
     */
    public static function getOrCreateForIp(string $ipAddress, string $action): self
    {
        $record = self::where('ip_address', $ipAddress)
            ->where('action', $action)
            ->first();

        if (!$record) {
            $record = self::create([
                'ip_address' => $ipAddress,
                'action' => $action,
                'attempts' => 0,
                'first_attempt_at' => now(),
                'last_attempt_at' => now()
            ]);
        }

        return $record;
    }

    /**
     * Clean up expired records
     */
    public static function cleanupExpired(): void
    {
        // Remove records where both rate limit window and block period have expired
        self::where(function ($query) {
            $query->whereNull('blocked_until')
                ->where('first_attempt_at', '<', now()->subMinutes(20));
        })
            ->orWhere(function ($query) {
                $query->whereNotNull('blocked_until')
                    ->where('blocked_until', '<', now())
                    ->where('first_attempt_at', '<', now()->subMinutes(20));
            })
            ->delete();
    }
}
