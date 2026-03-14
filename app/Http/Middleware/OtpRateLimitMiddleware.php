<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Services\RateLimitService;

class OtpRateLimitMiddleware
{
    protected $rateLimitService;

    public function __construct(RateLimitService $rateLimitService)
    {
        $this->rateLimitService = $rateLimitService;
    }

    public function handle(Request $request, Closure $next)
    {
        $rateLimitCheck = $this->rateLimitService->isRateLimited($request);

        if ($rateLimitCheck['limited']) {
            return response()->json([
                'status' => 'error',
                'message' => $rateLimitCheck['message']
            ], 429);
        }

        return $next($request);
    }
}
