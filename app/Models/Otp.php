<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class Otp extends Model
{
    protected $fillable = [
        'email',
        'mobile',
        'otp',
        'type',
        'is_verified',
        'expires_at'
    ];

    protected $casts = [
        'expires_at' => 'datetime',
        'is_verified' => 'boolean',
    ];

    protected $appends = ['is_expired'];

    // Rate limiting constants
    const MAX_OTP_ATTEMPTS = 5;
    const RATE_LIMIT_WINDOW_MINUTES = 60;

    public function getIsExpiredAttribute()
    {
        return $this->expires_at->isPast();
    }

    /**
     * Check if user has exceeded OTP rate limit
     */
    public static function isRateLimited($email = null, $mobile = null)
    {
        $rateLimitStart = Carbon::now()->subMinutes(self::RATE_LIMIT_WINDOW_MINUTES);

        $results = [];

        if ($email) {
            // Get all OTPs in the current window
            $emailOtps = self::where('email', $email)
                ->where('type', 'email')
                ->where('created_at', '>=', $rateLimitStart)
                ->orderBy('created_at', 'desc')
                ->get();

            $emailCount = $emailOtps->count();

            // If user has reached the limit, check if they're still in blocking period
            $isLimited = false;
            if ($emailCount >= self::MAX_OTP_ATTEMPTS) {
                // Get the 3rd most recent OTP (the one that triggered the block)
                $thirdOtp = $emailOtps->skip(self::MAX_OTP_ATTEMPTS - 1)->first();
                if ($thirdOtp) {
                    $blockUntil = $thirdOtp->created_at->addMinutes(self::RATE_LIMIT_WINDOW_MINUTES);
                    $isLimited = Carbon::now()->isBefore($blockUntil);
                }
            }

            $results['email'] = [
                'is_limited' => $isLimited,
                'attempts' => $emailCount,
                'remaining' => $isLimited ? 0 : max(0, self::MAX_OTP_ATTEMPTS - $emailCount)
            ];
        }

        if ($mobile) {
            // Get all OTPs in the current window
            $mobileOtps = self::where('mobile', $mobile)
                ->where('type', 'mobile')
                ->where('created_at', '>=', $rateLimitStart)
                ->orderBy('created_at', 'desc')
                ->get();

            $mobileCount = $mobileOtps->count();

            // If user has reached the limit, check if they're still in blocking period
            $isLimited = false;
            if ($mobileCount >= self::MAX_OTP_ATTEMPTS) {
                // Get the 3rd most recent OTP (the one that triggered the block)
                $thirdOtp = $mobileOtps->skip(self::MAX_OTP_ATTEMPTS - 1)->first();
                if ($thirdOtp) {
                    $blockUntil = $thirdOtp->created_at->addMinutes(self::RATE_LIMIT_WINDOW_MINUTES);
                    $isLimited = Carbon::now()->isBefore($blockUntil);
                }
            }

            $results['mobile'] = [
                'is_limited' => $isLimited,
                'attempts' => $mobileCount,
                'remaining' => $isLimited ? 0 : max(0, self::MAX_OTP_ATTEMPTS - $mobileCount)
            ];
        }

        return $results;
    }

    /**
     * Get time remaining for rate limit reset
     */
    public static function getRateLimitResetTime($email = null, $mobile = null)
    {
        $rateLimitStart = Carbon::now()->subMinutes(self::RATE_LIMIT_WINDOW_MINUTES);

        $results = [];

        if ($email) {
            // Get the most recent OTPs to find the blocking OTP
            $emailOtps = self::where('email', $email)
                ->where('type', 'email')
                ->where('created_at', '>=', $rateLimitStart)
                ->orderBy('created_at', 'desc')
                ->limit(self::MAX_OTP_ATTEMPTS)
                ->get();

            if ($emailOtps->count() >= self::MAX_OTP_ATTEMPTS) {
                // The 3rd OTP (last in the ordered collection) determines the block time
                $blockingOtp = $emailOtps->last();
                $resetTime = $blockingOtp->created_at->addMinutes(self::RATE_LIMIT_WINDOW_MINUTES);

                $results['email'] = [
                    'reset_at' => $resetTime,
                    'minutes_remaining' => max(0, Carbon::now()->diffInMinutes($resetTime, false))
                ];
            }
        }

        if ($mobile) {
            // Get the most recent OTPs to find the blocking OTP
            $mobileOtps = self::where('mobile', $mobile)
                ->where('type', 'mobile')
                ->where('created_at', '>=', $rateLimitStart)
                ->orderBy('created_at', 'desc')
                ->limit(self::MAX_OTP_ATTEMPTS)
                ->get();

            if ($mobileOtps->count() >= self::MAX_OTP_ATTEMPTS) {
                // The 3rd OTP (last in the ordered collection) determines the block time
                $blockingOtp = $mobileOtps->last();
                $resetTime = $blockingOtp->created_at->addMinutes(self::RATE_LIMIT_WINDOW_MINUTES);

                $results['mobile'] = [
                    'reset_at' => $resetTime,
                    'minutes_remaining' => max(0, Carbon::now()->diffInMinutes($resetTime, false))
                ];
            }
        }

        return $results;
    }

    /**
     * Generate OTP for user with rate limiting
     */
    public static function generateOtp($email = null, $mobile = null)
    {
        // Check rate limits
        $rateLimitStatus = self::isRateLimited($email, $mobile);

        // Check if any of the requested methods are rate limited
        $blockedMethods = [];

        if ($email && isset($rateLimitStatus['email']) && $rateLimitStatus['email']['is_limited']) {
            $blockedMethods[] = 'email';
        }

        if ($mobile && isset($rateLimitStatus['mobile']) && $rateLimitStatus['mobile']['is_limited']) {
            $blockedMethods[] = 'mobile';
        }

        if (!empty($blockedMethods)) {
            $resetTimes = self::getRateLimitResetTime($email, $mobile);

            return [
                'success' => false,
                'error' => 'rate_limit_exceeded',
                'message' => 'Too many OTP requests. Please try again later.',
                'blocked_methods' => $blockedMethods,
                'rate_limit_status' => $rateLimitStatus,
                'reset_times' => $resetTimes
            ];
        }

        // Generate OTP
        // $otp = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);
        $otp = rand(99999, 999999);
        $expiresAt = Carbon::now()->addMinutes(10); // OTP expires in 10 minutes

        // Clear any existing expired OTPs for this user (keep recent ones for rate limiting)
        $expiredTime = Carbon::now();
        if ($email) {
            self::where('email', $email)
                ->where('expires_at', '<', $expiredTime)
                ->delete();
        }
        if ($mobile) {
            self::where('mobile', $mobile)
                ->where('expires_at', '<', $expiredTime)
                ->delete();
        }

        $otpRecords = [];

        // Create OTP record for email if provided
        if ($email) {
            $otpRecords[] = self::create([
                'email' => $email,
                'mobile' => null,
                'otp' => $otp,
                'type' => 'email',
                'is_verified' => false,
                'expires_at' => $expiresAt
            ]);
        }

        // Create OTP record for mobile if provided
        if ($mobile) {
            $otpRecords[] = self::create([
                'email' => null,
                'mobile' => $mobile,
                'otp' => $otp,
                'type' => 'mobile',
                'is_verified' => false,
                'expires_at' => $expiresAt
            ]);
        }

        return [
            'success' => true,
            'otp' => $otp,
            'records' => $otpRecords,
            'expires_at' => $expiresAt,
            'rate_limit_status' => $rateLimitStatus
        ];
    }

    /**
     * Verify OTP
     */
    public static function verifyOtp($otp, $email = null, $mobile = null)
    {
        $query = self::where('otp', $otp)
            ->where('is_verified', false)
            ->where('expires_at', '>', Carbon::now());

        if ($email || $mobile) {
            $query->where(function ($q) use ($email, $mobile) {
                if ($email) {
                    $q->where('email', $email);
                }

                if ($mobile) {
                    $q->orWhere('mobile', $mobile);
                }
            });
        }

        $otpRecords = $query->get();

        if ($otpRecords->count() > 0) {
            foreach ($otpRecords as $record) {
                $record->update(['is_verified' => true]);
            }
            return true;
        }

        return false;
    }

    /**
     * Check if OTP is valid and not expired
     */
    public function isValid()
    {
        return !$this->is_verified && $this->expires_at > Carbon::now();
    }

    /**
     * Check if OTP is expired
     */
    public function isExpired()
    {
        return $this->expires_at < Carbon::now();
    }

    /**
     * Clean up old OTP records (can be used in a scheduled job)
     */
    public static function cleanupOldRecords()
    {
        $cleanupTime = Carbon::now()->subHours(2); // Keep records for 2 hours for rate limiting

        self::where('created_at', '<', $cleanupTime)->delete();
    }
}
