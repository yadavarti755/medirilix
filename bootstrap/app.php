<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Spatie\Csp\AddCspHeaders;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->validateCsrfTokens(except: [
            '/payment/razorpay/callback',
            '/payment/response/*',
        ]);
        $middleware->append(AddCspHeaders::class);
        $middleware->alias([
            'sessionTimeout' => \App\Http\Middleware\SessionTimeout::class,
            'role' => \App\Http\Middleware\RoleMiddleware::class,
            'setLocale' => \App\Http\Middleware\SetLocale::class,
            'trackVisitor' => \App\Http\Middleware\TrackVisitor::class,
            'checkConcurrentSessions' => \App\Http\Middleware\CheckConcurrentSessions::class,
            'scanUploadedFiles' => \App\Http\Middleware\ScanUploadedFiles::class,
            'preventBackHistory' => \App\Http\Middleware\PreventBackHistory::class,
            'otp.generation.throttle' => \App\Http\Middleware\OtpGenerationThrottle::class,
            'otp.verification.throttle' => \App\Http\Middleware\OtpVerificationThrottle::class,
            'otp.rate.limit' => \App\Http\Middleware\OtpRateLimitMiddleware::class,
            'referer.check' => \App\Http\Middleware\ValidateReferer::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })
    ->create();
