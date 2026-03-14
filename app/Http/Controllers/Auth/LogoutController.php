<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class LogoutController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        if ($user) {
            $user->clearSessionInfo();
        }
        $userRoles = auth()->user()->roles->pluck('name')->toArray();
        auth()->logout();

        // ✅ Invalidate session and regenerate CSRF token
        request()->session()->invalidate();
        request()->session()->regenerateToken();

        if (!in_array('USER', $userRoles)) {
            return redirect()->route('login');
        } else {
            return redirect()->route('public.login');
        }
    }

    public function logoutUsingPost()
    {
        $user = auth()->user();
        if ($user) {
            $user->clearSessionInfo();
        }
        $userRoles = auth()->user()->roles->pluck('name')->toArray();
        auth()->logout();

        // ✅ Invalidate session and regenerate CSRF token
        request()->session()->invalidate();
        request()->session()->regenerateToken();

        if (!in_array('USER', $userRoles)) {
            return redirect()->route('login');
        } else {
            return redirect()->route('public.login');
        }
    }

    public function logOutOtherDevices()
    {
        $userId = request()->query('user_id');
        if (!$userId) {
            return redirect()->route('login')->withErrors(['error' => 'User ID is required']);
        }

        $id = customDecrypt($userId);
        $user = User::where('id', $id)->first();
        if ($user) {
            $user->clearSessionInfo();
        }

        $userRoles = $user->roles->pluck('name')->toArray();
        auth()->logout();

        // ✅ Invalidate session and regenerate CSRF token
        request()->session()->invalidate();
        request()->session()->regenerateToken();

        if (!in_array('USER', $userRoles)) {
            return redirect()->route('login');
        } else {
            return redirect()->route('public.login');
        }
    }

    public function checkSession()
    {
        $user = auth()->user();
        $userRoles = $user->roles->pluck('name')->toArray();

        if (auth()->user()->current_session_id != session()->getId()) {
            if ($user) {
                $user->clearSessionInfo();
            }

            auth()->logout();
            // ✅ Invalidate session and regenerate CSRF token
            request()->session()->invalidate();
            request()->session()->regenerateToken();

            if (!in_array('USER', $userRoles)) {
                return response()->json(['status' => 'INACTIVE', 'logout' => true, 'redirect_url' => route('login'), 'message' => 'Logout due to inactivity']);
            } else {
                return response()->json(['status' => 'INACTIVE', 'logout' => true, 'redirect_url' => route('public.login'), 'message' => 'Logout due to inactivity']);
            }
        } else {
            return response()->json(['status' => 'ACTIVE', 'logout' => false]);
        }
    }
}
