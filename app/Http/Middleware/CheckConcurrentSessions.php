<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CheckConcurrentSessions
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        if (Auth::check()) {
            $user = Auth::user();
            $currentSessionId = session()->getId();
            $userRoles = $user->roles->pluck('name')->toArray();
            if (in_array('EMPLOYEE', $userRoles)) {
                $redirectUrl = redirect()->route('public.login');
            } else {
                $redirectUrl = redirect()->route('login');
            }

            // Check if user's session has been invalidated by another login
            if (($user->current_session_id && $user->current_session_id !== $currentSessionId) || empty($user->current_session_id)) {
                // User has been logged in from another device
                Auth::logout();
                session()->invalidate();
                session()->regenerateToken();

                if ($request->ajax()) {
                    return response()->json([
                        'status' => 'session_expired',
                        'message' => 'Your session has been expired due to login from another device.',
                        'redirect_url' => $redirectUrl
                    ], 401);
                }

                return $redirectUrl;
            }
        }

        return $next($request);
    }
}
