<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Laravel\Socialite\Facades\Socialite;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Spatie\Permission\Models\Role;
use Exception;

class GoogleSocialController extends Controller
{
    public function redirectToGoogle(Request $request)
    {
        if ($request->has('origin')) {
            session(['login_origin' => $request->get('origin')]);
        }
        return Socialite::driver('google')->redirect();
    }

    public function handleGoogleCallback()
    {
        try {
            $googleUser = Socialite::driver('google')->user();

            $finduser = User::where('google_id', $googleUser->id)->first();

            $redirectTarget = route('user.dashboard');

            // Check if login_origin is set in session
            if (session()->has('login_origin')) {
                $origin = session('login_origin');
                // Basic validation to ensure it's a relative path or same domain
                // If it contains "login" (like /login page), avoid redirecting there loops
                if (!str_contains($origin, '/login') && !str_contains($origin, 'login_field')) {
                    $redirectTarget = $origin;
                }
                session()->forget('login_origin');
            }

            if ($finduser) {

                Auth::login($finduser);
                $finduser->updateSessionInfo();
                return redirect()->intended($redirectTarget);
            } else {
                // Check if user exists with this email
                $existingUser = User::where('email', $googleUser->email)->first();

                if ($existingUser) {
                    // Update existing user with google_id
                    $existingUser->update([
                        'google_id' => $googleUser->id,
                        'avatar' => $googleUser->avatar
                    ]);

                    Auth::login($existingUser);
                    $existingUser->updateSessionInfo();
                    return redirect()->intended($redirectTarget);
                } else {
                    // Create new user
                    $newUser = User::create([
                        'name' => $googleUser->name,
                        'email' => $googleUser->email,
                        'google_id' => $googleUser->id,
                        'avatar' => $googleUser->avatar,
                        'password' => encrypt('password') // Dummy password
                    ]);

                    // Assign USER role
                    $newUser->assignRole('USER');

                    Auth::login($newUser);
                    $newUser->updateSessionInfo();

                    return redirect()->intended($redirectTarget);
                }
            }
        } catch (Exception $e) {
            \Illuminate\Support\Facades\Log::error('Google Login Error: ' . $e->getMessage());
            return redirect()->route('public.login')->with('error', 'Something went wrong with Google Login. Please try again or contact support.');
        }
    }
}
