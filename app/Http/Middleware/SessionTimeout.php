<?php

namespace App\Http\Middleware;

use Auth;
use Illuminate\Support\Facades\Session;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SessionTimeout
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (Auth::check()) {
            $lastActivity = $request->session()->get('last_activity');
            $timeout = (int) config('session.inactivity_timeout', env('SESSION_INACTIVITY_TIMEOUT', 2)) * 60;

            // If expired, logout
            if ($lastActivity && (time() - $lastActivity) >= $timeout) {
                $user = auth()->user();
                $roles = $user ? $user->roles->pluck('name')->toArray() : [];
                if ($user) {
                    $user->clearSessionInfo();
                }

                // optional: $user->clearSessionInfo();
                auth()->logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();

                $redirectUrl = in_array('EMPLOYEE', $roles, true)
                    ? route('employee-corner.login')
                    : route('login');

                if ($request->expectsJson()) {
                    return response()->json([
                        'logout' => true,
                        'message' => 'Session expired due to inactivity',
                        'redirect_url' => $redirectUrl,
                        'status' => 'INACTIVITY',
                    ], 200);
                }

                return redirect()->to($redirectUrl)
                    ->with('message', 'You have been logged out due to inactivity.');
            }

            // 🔑 Only refresh activity on *non*-heartbeat routes
            if (!$request->routeIs('session.ping')) {
                $request->session()->put('last_activity', time());
            }
        }

        return $next($request);
    }
}
