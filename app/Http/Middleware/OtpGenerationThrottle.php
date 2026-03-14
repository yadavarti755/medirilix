<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\RateLimiter;

class OtpGenerationThrottle
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  int  $maxAttempts
     * @param  int  $decayMinutes
     * @return mixed
     */
    public function handle(Request $request, Closure $next, $maxAttempts = 5, $decayMinutes = 30)
    {
        $maxAttempts = (int) $maxAttempts;
        $decayMinutes = (int) $decayMinutes;

        $ip = $request->ip();
        $key = 'otp_generation_' . $ip;

        // Check if IP is currently blocked
        if (Cache::has($key . '_blocked')) {
            return response()->json([
                'success' => false,
                'message' => 'Too many OTP generation attempts. Please try again after 30 minutes.',
                'blocked_until' => Cache::get($key . '_blocked_until')
            ], 429);
        }

        // Check current attempts in the time window
        $attempts = Cache::get($key, 0);

        if ($attempts >= $maxAttempts) {
            // Block the IP for 30 minutes
            $blockedUntil = now()->addMinutes($decayMinutes);
            Cache::put($key . '_blocked', true, $blockedUntil);
            Cache::put($key . '_blocked_until', $blockedUntil->toISOString(), $blockedUntil);

            return response()->json([
                'success' => false,
                'message' => 'Too many OTP generation attempts. You are now blocked for 30 minutes.',
                'blocked_until' => $blockedUntil->toISOString()
            ], 429);
        }

        // Increment attempts and reset decay window
        Cache::put($key, $attempts + 1, now()->addMinutes($decayMinutes));

        return $next($request);
    }
}
