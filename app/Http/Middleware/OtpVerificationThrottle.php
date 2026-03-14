<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\RateLimiter;

class OtpVerificationThrottle
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
    public function handle(Request $request, Closure $next, $maxAttempts = 3, $decayMinutes = 30)
    {
        $maxAttempts = (int) $maxAttempts;
        $decayMinutes = (int) $decayMinutes;

        $ip = $request->ip();

        // Unique identifier using IP and contact info
        $identifier = $ip;
        $key = 'otp_verification_' . $identifier;

        // If user is blocked
        if (Cache::has($key . '_blocked')) {
            return response()->json([
                'success' => false,
                'message' => 'Too many failed verification attempts. Please try again after 30 minutes.',
                'blocked_until' => Cache::get($key . '_blocked_until')
            ], 429);
        }

        // Proceed with the request
        $response = $next($request);

        // Decode the response to check OTP verification status
        $responseData = json_decode($response->getContent(), true);

        if (isset($responseData['success']) && $responseData['success'] === true) {
            // On success, reset all attempts and block flags
            Cache::forget($key);
            Cache::forget($key . '_blocked');
            Cache::forget($key . '_blocked_until');
        } elseif (isset($responseData['success']) && $responseData['success'] === false) {
            // On failure, increment attempt count
            $attempts = Cache::get($key, 0) + 1;

            if ($attempts >= $maxAttempts) {
                // Block user for 30 minutes
                $blockedUntil = now()->addMinutes($decayMinutes);
                Cache::put($key . '_blocked', true, $blockedUntil);
                Cache::put($key . '_blocked_until', $blockedUntil->toISOString(), $blockedUntil);

                return response()->json([
                    'success' => false,
                    'message' => 'Too many failed verification attempts. You are now blocked for 30 minutes.',
                    'blocked_until' => $blockedUntil->toISOString()
                ], 429);
            }

            // Save incremented attempts
            Cache::put($key, $attempts, now()->addMinutes($decayMinutes));
        }

        return $response;
    }
}
