<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ValidateReferer
{
    /**
     * Allowed referers (you can customize this)
     */
    protected $allowedReferers = [
        'http://127.0.0.1:8000',
        'http://medirilix.test',
        'https://medirilix.test',
        'https://nexageo.com',
        'https://www.nexageo.com',
        'http://www.nexageo.com',
        'https://nexageo.in',
        'https://www.nexageo.in',
        'http://www.nexageo.in',
    ];

    protected $except = [
        'payment/razorpay/callback',
        'payment/response/*',
    ];

    public function handle(Request $request, Closure $next): Response
    {
        // Skip check for excluded routes
        foreach ($this->except as $except) {
            if ($request->is($except)) {
                return $next($request);
            }
        }
        // Only check referer for unsafe HTTP methods (non-GET)
        if (in_array($request->method(), ['POST', 'PUT', 'PATCH', 'DELETE'])) {
            $referer = $request->headers->get('referer');

            if ($referer && !$this->isValidReferer($referer)) {
                abort(403, 'Invalid Referer');
            }
        }

        return $next($request);
    }

    protected function isValidReferer(string $referer): bool
    {
        foreach ($this->allowedReferers as $allowed) {
            if (str_starts_with($referer, $allowed)) {
                return true;
            }
        }

        return false;
    }
}
