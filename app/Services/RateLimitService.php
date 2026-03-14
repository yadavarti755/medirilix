<?php

namespace App\Services;

use App\Models\IpRateLimit;
use App\Models\User;
use Illuminate\Http\Request;

class RateLimitService
{
    const OTP_VERIFICATION_ACTION = 'otp_verification';

    // General OTP actions
    const OTP_GENERATION = 'otp_generation';
    const OTP_RESEND = 'otp_resend';

    // Backend-specific OTP actions
    const BACKEND_OTP_GENERATION = 'backend_otp_generation';
    const BACKEND_OTP_RESEND = 'backend_otp_resend';

    // DHTI-specific OTP actions
    const DHTI_OTP_GENERATION = 'dhti_otp_generation';
    const DHTI_EMAIL_OTP_RESEND = 'dhti_email_otp_resend';
    const DHTI_MOBILE_OTP_RESEND = 'dhti_mobile_otp_resend';

    const MAX_ATTEMPTS = 5;
    const RATE_LIMIT_WINDOW_MINUTES = 20;
    const BLOCK_DURATION_HOURS = 1;

    /**
     * Check if IP is rate limited for OTP verification
     */
    public function isRateLimited(Request $request): array
    {
        $ipAddress = $this->getClientIp($request);
        $record = IpRateLimit::getOrCreateForIp($ipAddress, self::OTP_VERIFICATION_ACTION);

        // Check if IP is currently blocked
        if ($record->isBlocked()) {
            return [
                'limited' => true,
                'message' => 'Too many failed attempts. Your IP is temporarily blocked. Please try again after ' .
                    $record->blocked_until->diffForHumans() . '.',
                'blocked_until' => $record->blocked_until
            ];
        }

        // Check if rate limit window has expired, reset if so
        if ($record->isWindowExpired()) {
            $record->reset();
        }

        return [
            'limited' => false,
            'attempts' => $record->attempts,
            'record' => $record
        ];
    }

    /**
     * Record a failed OTP attempt
     */
    public function recordFailedAttempt(Request $request): array
    {
        $ipAddress = $this->getClientIp($request);
        $record = IpRateLimit::getOrCreateForIp($ipAddress, self::OTP_VERIFICATION_ACTION);

        // If window expired, reset first
        if ($record->isWindowExpired()) {
            $record->reset();
        }

        $record->incrementAttempts();

        // Check if we should block the IP
        if ($record->attempts >= self::MAX_ATTEMPTS) {
            $record->blockIp();

            return [
                'blocked' => true,
                'message' => 'Too many failed attempts. Your IP has been temporarily blocked for 1 hour.',
                'blocked_until' => $record->blocked_until
            ];
        }

        $remainingAttempts = self::MAX_ATTEMPTS - $record->attempts;

        return [
            'blocked' => false,
            'attempts' => $record->attempts,
            'remaining_attempts' => $remainingAttempts,
            'message' => "Invalid OTP. You have {$remainingAttempts} attempt(s) remaining."
        ];
    }

    /**
     * Record a successful OTP verification (clears the rate limit)
     */
    public function recordSuccessfulAttempt(Request $request): void
    {
        $ipAddress = $this->getClientIp($request);

        // Clear the rate limit record for this IP
        IpRateLimit::where('ip_address', $ipAddress)
            ->where('action', self::OTP_VERIFICATION_ACTION)
            ->delete();
    }

    /**
     * Get the client's IP address
     */
    private function getClientIp(Request $request): string
    {
        // Check for various headers that might contain the real IP
        $headers = [
            'HTTP_CF_CONNECTING_IP', // Cloudflare
            'HTTP_X_REAL_IP',        // Nginx
            'HTTP_X_FORWARDED_FOR',  // Load balancer/proxy
            'HTTP_CLIENT_IP',        // Proxy
            'REMOTE_ADDR'            // Default
        ];

        foreach ($headers as $header) {
            $ip = $request->server($header);
            if ($ip && $ip !== 'unknown') {
                // If X-Forwarded-For contains multiple IPs, get the first one
                if (strpos($ip, ',') !== false) {
                    $ip = trim(explode(',', $ip)[0]);
                }

                // Validate IP address
                if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE)) {
                    return $ip;
                }
            }
        }

        // Fallback to request IP
        return $request->ip();
    }

    /**
     * Check if user has exceeded OTP generation limit
     */
    public function checkOtpGenerationLimit(Request $request, User $user): array
    {
        $ipAddress = $request->ip();
        $action = self::OTP_GENERATION;

        $rateLimit = IpRateLimit::getOrCreateForIp($ipAddress, $action);

        // Check if IP is currently blocked
        if ($rateLimit->isBlocked()) {
            return [
                'limited' => true,
                'message' => 'Too many OTP generation requests. Please try again after ' . $rateLimit->blocked_until->diffForHumans() . '.'
            ];
        }

        // Check if rate limit window has expired
        if ($rateLimit->isWindowExpired()) {
            $rateLimit->reset();
            return ['limited' => false];
        }

        // Check if attempts exceed limit
        if ($rateLimit->attempts >= 5) {
            $rateLimit->blockIp();
            return [
                'limited' => true,
                'message' => 'You have exceeded the OTP generation limit. Please try again after 1 hour.'
            ];
        }

        return ['limited' => false];
    }

    /**
     * Check if user has exceeded OTP resend limit
     */
    public function checkOtpResendLimit(Request $request, User $user): array
    {
        $ipAddress = $request->ip();
        $action = self::OTP_RESEND;

        $rateLimit = IpRateLimit::getOrCreateForIp($ipAddress, $action);

        // Check if IP is currently blocked
        if ($rateLimit->isBlocked()) {
            return [
                'limited' => true,
                'message' => 'Too many OTP resend requests. Please try again after ' . $rateLimit->blocked_until->diffForHumans() . '.'
            ];
        }

        // Check if rate limit window has expired
        if ($rateLimit->isWindowExpired()) {
            $rateLimit->reset();
            return ['limited' => false];
        }

        // Check if attempts exceed limit
        if ($rateLimit->attempts >= 5) {
            $rateLimit->blockIp();
            return [
                'limited' => true,
                'message' => 'You have exceeded the OTP resend limit. Please try again after 1 hour.'
            ];
        }

        return ['limited' => false];
    }

    /**
     * Record OTP generation attempt
     */
    public function recordOtpGenerationAttempt(Request $request, User $user): void
    {
        $ipAddress = $request->ip();
        $action = self::OTP_GENERATION;

        $rateLimit = IpRateLimit::getOrCreateForIp($ipAddress, $action);
        $rateLimit->incrementAttempts();
    }

    /**
     * Record OTP resend attempt
     */
    public function recordOtpResendAttempt(Request $request, User $user): void
    {
        $ipAddress = $request->ip();
        $action = self::OTP_RESEND;

        $rateLimit = IpRateLimit::getOrCreateForIp($ipAddress, $action);
        $rateLimit->incrementAttempts();
    }

    /**
     * Record a successful OTP verification (clears the rate limit)
     */
    public function recordSuccessfullSendAttempt(Request $request): void
    {
        $ipAddress = $this->getClientIp($request);

        // Clear the rate limit record for this IP
        IpRateLimit::where('ip_address', $ipAddress)
            ->where('action', self::OTP_GENERATION)
            ->delete();
    }

    /**
     * Record a successful OTP verification (clears the rate limit)
     */
    public function recordSuccessfullReSendAttempt(Request $request): void
    {
        $ipAddress = $this->getClientIp($request);

        // Clear the rate limit record for this IP
        IpRateLimit::where('ip_address', $ipAddress)
            ->where('action', self::OTP_RESEND)
            ->delete();
    }

    /**
     * Check if user has exceeded OTP generation limit for backend
     */
    public function checkBackendOtpGenerationLimit(Request $request, User $user): array
    {
        $ipAddress = $request->ip();
        $action = self::BACKEND_OTP_GENERATION;

        $rateLimit = IpRateLimit::getOrCreateForIp($ipAddress, $action);

        // Check if IP is currently blocked
        if ($rateLimit->isBlocked()) {
            return [
                'limited' => true,
                'message' => 'Too many OTP generation requests. Please try again after ' . $rateLimit->blocked_until->diffForHumans() . '.'
            ];
        }

        // Check if rate limit window has expired
        if ($rateLimit->isWindowExpired()) {
            $rateLimit->reset();
            return ['limited' => false];
        }

        // Check if attempts exceed limit
        if ($rateLimit->attempts >= 5) {
            $rateLimit->blockIp();
            return [
                'limited' => true,
                'message' => 'You have exceeded the OTP generation limit. Please try again after 1 hour.'
            ];
        }

        return ['limited' => false];
    }

    /**
     * Check if user has exceeded OTP resend limit for backend
     */
    public function checkBackendOtpResendLimit(Request $request, User $user): array
    {
        $ipAddress = $request->ip();
        $action = self::BACKEND_OTP_RESEND;

        $rateLimit = IpRateLimit::getOrCreateForIp($ipAddress, $action);

        // Check if IP is currently blocked
        if ($rateLimit->isBlocked()) {
            return [
                'limited' => true,
                'message' => 'Too many OTP resend requests. Please try again after ' . $rateLimit->blocked_until->diffForHumans() . '.'
            ];
        }

        // Check if rate limit window has expired
        if ($rateLimit->isWindowExpired()) {
            $rateLimit->reset();
            return ['limited' => false];
        }

        // Check if attempts exceed limit
        if ($rateLimit->attempts >= 5) {
            $rateLimit->blockIp();
            return [
                'limited' => true,
                'message' => 'You have exceeded the OTP resend limit. Please try again after 1 hour.'
            ];
        }

        return ['limited' => false];
    }

    /**
     * Record OTP generation attempt for backend
     */
    public function recordBackendOtpGenerationAttempt(Request $request, User $user): void
    {
        $ipAddress = $request->ip();
        $action = self::BACKEND_OTP_GENERATION;

        $rateLimit = IpRateLimit::getOrCreateForIp($ipAddress, $action);
        $rateLimit->incrementAttempts();
    }

    /**
     * Record OTP resend attempt for backend
     */
    public function recordBackendOtpResendAttempt(Request $request, User $user): void
    {
        $ipAddress = $request->ip();
        $action = self::BACKEND_OTP_RESEND;

        $rateLimit = IpRateLimit::getOrCreateForIp($ipAddress, $action);
        $rateLimit->incrementAttempts();
    }

    /**
     * Record a successful OTP verification (clears the rate limit)
     */
    public function recordSuccessfullBackendSendAttempt(Request $request): void
    {
        $ipAddress = $this->getClientIp($request);

        // Clear the rate limit record for this IP
        IpRateLimit::where('ip_address', $ipAddress)
            ->where('action', self::BACKEND_OTP_GENERATION)
            ->delete();
    }

    /**
     * Record a successful OTP verification (clears the rate limit)
     */
    public function recordSuccessfullBackendReSendAttempt(Request $request): void
    {
        $ipAddress = $this->getClientIp($request);

        // Clear the rate limit record for this IP
        IpRateLimit::where('ip_address', $ipAddress)
            ->where('action', self::BACKEND_OTP_RESEND)
            ->delete();
    }

    /**
     * Check if user has exceeded OTP generation limit for DHTI
     */
    public function checkDhtiOtpGenerationLimit(Request $request): array
    {
        $ipAddress = $request->ip();
        $action = self::DHTI_OTP_GENERATION;

        $rateLimit = IpRateLimit::getOrCreateForIp($ipAddress, $action);

        // Check if IP is currently blocked
        if ($rateLimit->isBlocked()) {
            return [
                'limited' => true,
                'message' => 'Too many OTP generation requests. Please try again after ' . $rateLimit->blocked_until->diffForHumans() . '.'
            ];
        }

        // Check if rate limit window has expired
        if ($rateLimit->isWindowExpired()) {
            $rateLimit->reset();
            return ['limited' => false];
        }

        // Check if attempts exceed limit
        if ($rateLimit->attempts >= 5) {
            $rateLimit->blockIp();
            return [
                'limited' => true,
                'message' => 'You have exceeded the OTP generation limit. Please try again after 1 hour.'
            ];
        }

        return ['limited' => false];
    }

    /**
     * Check if user has exceeded email OTP resend limit for DHTI
     */
    public function checkDhtiEmailOtpResendLimit(Request $request): array
    {
        $ipAddress = $request->ip();
        $action = self::DHTI_EMAIL_OTP_RESEND;

        $rateLimit = IpRateLimit::getOrCreateForIp($ipAddress, $action);

        // Check if IP is currently blocked
        if ($rateLimit->isBlocked()) {
            return [
                'limited' => true,
                'message' => 'Too many email OTP resend requests. Please try again after ' . $rateLimit->blocked_until->diffForHumans() . '.'
            ];
        }

        // Check if rate limit window has expired
        if ($rateLimit->isWindowExpired()) {
            $rateLimit->reset();
            return ['limited' => false];
        }

        // Check if attempts exceed limit
        if ($rateLimit->attempts >= 5) {
            $rateLimit->blockIp();
            return [
                'limited' => true,
                'message' => 'You have exceeded the email OTP resend limit. Please try again after 1 hour.'
            ];
        }

        return ['limited' => false];
    }

    /**
     * Check if user has exceeded mobile OTP resend limit for DHTI
     */
    public function checkDhtiMobileOtpResendLimit(Request $request): array
    {
        $ipAddress = $request->ip();
        $action = self::DHTI_MOBILE_OTP_RESEND;

        $rateLimit = IpRateLimit::getOrCreateForIp($ipAddress, $action);

        // Check if IP is currently blocked
        if ($rateLimit->isBlocked()) {
            return [
                'limited' => true,
                'message' => 'Too many mobile OTP resend requests. Please try again after ' . $rateLimit->blocked_until->diffForHumans() . '.'
            ];
        }

        // Check if rate limit window has expired
        if ($rateLimit->isWindowExpired()) {
            $rateLimit->reset();
            return ['limited' => false];
        }

        // Check if attempts exceed limit
        if ($rateLimit->attempts >= 5) {
            $rateLimit->blockIp();
            return [
                'limited' => true,
                'message' => 'You have exceeded the mobile OTP resend limit. Please try again after 1 hour.'
            ];
        }

        return ['limited' => false];
    }

    /**
     * Record OTP generation attempt for DHTI
     */
    public function recordDhtiOtpGenerationAttempt(Request $request): void
    {
        $ipAddress = $request->ip();
        $action = self::DHTI_OTP_GENERATION;

        $rateLimit = IpRateLimit::getOrCreateForIp($ipAddress, $action);
        $rateLimit->incrementAttempts();
    }

    /**
     * Record email OTP resend attempt for DHTI
     */
    public function recordDhtiEmailOtpResendAttempt(Request $request): void
    {
        $ipAddress = $request->ip();
        $action = self::DHTI_EMAIL_OTP_RESEND;

        $rateLimit = IpRateLimit::getOrCreateForIp($ipAddress, $action);
        $rateLimit->incrementAttempts();
    }

    /**
     * Record mobile OTP resend attempt for DHTI
     */
    public function recordDhtiMobileOtpResendAttempt(Request $request): void
    {
        $ipAddress = $request->ip();
        $action = self::DHTI_MOBILE_OTP_RESEND;

        $rateLimit = IpRateLimit::getOrCreateForIp($ipAddress, $action);
        $rateLimit->incrementAttempts();
    }

    /**
     * Record a successful OTP verification (clears the rate limit)
     */
    public function recordSuccessfullDHTISendAttempt(Request $request): void
    {
        $ipAddress = $this->getClientIp($request);

        // Clear the rate limit record for this IP
        IpRateLimit::where('ip_address', $ipAddress)
            ->where('action', self::DHTI_OTP_GENERATION)
            ->delete();
    }

    /**
     * Record a successful OTP verification (clears the rate limit)
     */
    public function recordSuccessfullDHTIEmailReSendAttempt(Request $request): void
    {
        $ipAddress = $this->getClientIp($request);

        // Clear the rate limit record for this IP
        IpRateLimit::where('ip_address', $ipAddress)
            ->where('action', self::DHTI_EMAIL_OTP_RESEND)
            ->delete();
    }

    /**
     * Record a successful OTP verification (clears the rate limit)
     */
    public function recordSuccessfullDHTIMobileReSendAttempt(Request $request): void
    {
        $ipAddress = $this->getClientIp($request);

        // Clear the rate limit record for this IP
        IpRateLimit::where('ip_address', $ipAddress)
            ->where('action', self::DHTI_MOBILE_OTP_RESEND)
            ->delete();
    }
}
