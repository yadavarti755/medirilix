<?php

namespace App\Http\Controllers\Auth;

use Illuminate\Support\Str;
use App\Http\Controllers\Controller;
use App\Models\Otp;
use App\Models\User;
use App\Services\EmailService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use App\Services\RateLimitService;

class PublicLoginController extends Controller
{
    protected $rateLimitService;

    public function __construct(RateLimitService $rateLimitService)
    {
        $this->rateLimitService = $rateLimitService;
    }

    public function login()
    {
        $pageTitle = "Login";
        setEncryptionKey();
        return view('website.login', compact('pageTitle'));
    }

    public function checkLogin(Request $request)
    {
        // Validate request - allow either email or mobile
        $validator = Validator::make($request->all(), [
            'login_field' => 'required|string',
            'password' => 'required',
            'captcha' => 'required|captcha'
        ], [
            'captcha.captcha' => 'Invalid captcha code.',
            'login_field.required' => 'Email or mobile number is required.'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => $validator->errors()->first(),
                'key' => setEncryptionKey()
            ], 422);
        }

        $loginField = $request->login_field;
        $password = $request->password;

        // Determine if login field is email or mobile
        $fieldType = filter_var($loginField, FILTER_VALIDATE_EMAIL) ? 'email' : 'mobile_number';

        // Additional validation for mobile number format
        if ($fieldType === 'mobile_number') {
            if (!preg_match('/^[0-9]{10}$/', $loginField)) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Please enter a valid mobile number.',
                    'key' => setEncryptionKey()
                ], 422);
            }
        }

        $user = User::where($fieldType, $loginField)->first();

        if ($user && $user->isLockedOut()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Due to suspicious activity, your account is locked. You can login again after ' . $user->lockout_until->diffForHumans(),
                'key' => setEncryptionKey()
            ], 401);
        }

        $password = decryptPassword($request->password);

        // Attempt login with the determined field
        $credentials = [
            $fieldType => $loginField,
            'password' => $password
        ];

        if (Auth::attempt($credentials)) {
            if (!$user) {
                Auth::logout();
                return response()->json([
                    'status' => 'error',
                    'message' => 'No user found for the entered credentials.',
                    'key' => setEncryptionKey()
                ], 401);
            }

            $user->resetLoginAttempts(); // ✅ reset on success

            $userRoles = $user->roles->pluck('name')->toArray();

            if (in_array('USER', $userRoles)) {
                $request->session()->regenerate();

                // Login the user
                Auth::login($user);

                // Update session information
                $user->updateSessionInfo();

                $redirectRoute = route('user.dashboard');
                if ($request->login_source == 'checkout') {
                    $redirectRoute = route('checkout');
                }

                return response()->json([
                    'status' => 'success',
                    'message' => 'Login successful!',
                    'redirect' => $redirectRoute
                ]);
            } else {
                Auth::logout();
                return response()->json([
                    'status' => 'error',
                    'message' => 'You do not have user access.',
                    'key' => setEncryptionKey()
                ], 401);
            }
        } else {
            // If login fails and user exists, register a failed attempt
            if ($user) {
                $user->registerFailedLogin();
            }

            return response()->json([
                'status' => 'error',
                'message' => 'Invalid credentials provided.',
                'key' => setEncryptionKey()
            ], 401);
        }
    }

    /**
     * Show forget password form
     */
    public function showForgetPasswordForm()
    {
        $pageTitle = "Forget Password";
        return view('website.forget_password', compact('pageTitle'));
    }

    /**
     * Send OTP for password reset
     */
    public function sendForgetPasswordOtp(Request $request)
    {
        // Check rate limiting first
        $rateLimitCheck = $this->rateLimitService->isRateLimited($request);

        if ($rateLimitCheck['limited']) {
            return response()->json([
                'status' => 'error',
                'message' => $rateLimitCheck['message']
            ], 429); // Too Many Requests
        }

        // Validate request
        $validator = Validator::make($request->all(), [
            'login_field' => 'required|string',
            'captcha' => 'required|captcha'
        ], [
            'captcha.captcha' => 'Invalid captcha code.',
            'login_field.required' => 'Email or mobile number is required.'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => $validator->errors()->first()
            ], 422);
        }

        $loginField = $request->login_field;

        // Determine if login field is email or mobile
        $fieldType = filter_var($loginField, FILTER_VALIDATE_EMAIL) ? 'email' : 'mobile_number';

        // Additional validation for mobile number format
        if ($fieldType === 'mobile_number') {
            if (!preg_match('/^[0-9]{10}$/', $loginField)) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Please enter a valid mobile number.'
                ], 422);
            }
        }

        // Find user by email or mobile
        $user = User::where($fieldType, $loginField)
            ->whereHas('roles', function ($query) {
                $query->where('name', 'USER');
            })
            ->first();

        if ($user) {
            // Generate and send OTP
            $otpData = Otp::generateOtp($user->email, $user->mobile_number);
            if (!$otpData['success']) {
                return response()->json([
                    'status' => 'error',
                    'message' => $otpData['message']
                ], 429);
            }

            // Send OTP via email ONLY
            EmailService::sendForgotPasswordOtpEmail($user->email, $otpData['otp']);

            // Store user ID in session temporarily for password reset
            session()->put('reset_password_user_id', $user->id);

            // Record successful attempt (clears rate limiting)
            $this->rateLimitService->recordSuccessfulAttempt($request);
        } else {
            // Record failed attempt to prevent enumeration/brute force
            // Note: We intentionally do not return 404 here to prevent user enumeration
            $this->rateLimitService->recordFailedAttempt($request);
        }

        return response()->json([
            'status' => 'success',
            'message' => 'If an account exists, OTP sent on your email id.',
        ]);
    }

    public function verifyForgetPasswordOtp(Request $request)
    {
        // Check rate limiting first
        $rateLimitCheck = $this->rateLimitService->isRateLimited($request);

        if ($rateLimitCheck['limited']) {
            return response()->json([
                'status' => 'error',
                'message' => $rateLimitCheck['message']
            ], 429); // Too Many Requests
        }

        $validator = Validator::make($request->all(), [
            'otp' => 'required|string|size:6'
        ], [
            'otp.required' => 'OTP is required.',
            'otp.size' => 'OTP must be 6 digits.'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => $validator->errors()->first()
            ], 422);
        }

        $tempUserId = session()->get('reset_password_user_id');
        if (!$tempUserId) {
            return response()->json([
                'status' => 'error',
                'message' => 'Session expired. Please start the process again.'
            ], 401);
        }

        $user = User::find($tempUserId);
        if (!$user) {
            return response()->json([
                'status' => 'error',
                'message' => 'User not found. Please start the process again.'
            ], 401);
        }

        // Verify OTP
        if (Otp::verifyOtp($request->otp, $user->email, $user->mobile_number)) {
            // Record successful attempt (clears rate limiting)
            $this->rateLimitService->recordSuccessfulAttempt($request);

            // Generate password reset token
            $token = Str::random(60);

            // Store password reset token in database
            DB::table('password_reset_tokens')->updateOrInsert(
                ['email' => $user->email],
                [
                    'email' => $user->email,
                    'token' => Hash::make($token),
                    'created_at' => Carbon::now()
                ]
            );

            $resetUrl = route('public.login.reset-password.form', ['token' => base64_encode($token)]);
            // Send password reset email
            EmailService::sendPasswordResetEmail($user->email, $resetUrl, $user->name);

            // Clear temporary session
            session()->forget('reset_password_user_id');

            return response()->json([
                'status' => 'success',
                'message' => 'If an account exists, reset password link sent on your email id.'
            ]);
        } else {
            // Record failed attempt
            $failedAttemptResult = $this->rateLimitService->recordFailedAttempt($request);
            Log::info('Error while sending reset password link mail');

            return response()->json([
                'status' => 'error',
                'message' => $failedAttemptResult['message']
            ], $failedAttemptResult['blocked'] ? 429 : 401);
        }
    }

    /**
     * Resend OTP for password reset
     */
    public function resendForgetPasswordOtp(Request $request)
    {
        $tempUserId = session('reset_password_user_id');
        if (!$tempUserId) {
            return response()->json([
                'status' => 'error',
                'message' => 'Session expired. Please start the process again.'
            ], 401);
        }

        $user = User::find($tempUserId);
        if (!$user) {
            return response()->json([
                'status' => 'error',
                'message' => 'User not found. Please start the process again.'
            ], 401);
        }

        // Generate and send new OTP
        $otpData = Otp::generateOtp($user->email, $user->mobile_number);
        if (!$otpData['success']) {
            return response()->json([
                'status' => 'error',
                'message' => $otpData['message']
            ], 429);
        }

        // Send OTP via email ONLY
        EmailService::sendOtpEmail($user->email, $otpData['otp']);

        return response()->json([
            'status' => 'success',
            'message' => 'New OTP has been sent to your email.'
        ]);
    }

    /**
     * Show reset password form
     */
    public function showResetPasswordForm($token)
    {
        setEncryptionKey();
        $pageTitle = 'Reset Password';

        $token = base64_decode($token);

        // Get recent reset token records within the last hour
        $resetRecords = DB::table('password_reset_tokens')
            ->where('created_at', '>', Carbon::now()->subHours(48))
            ->get();

        // Search for matching hashed token
        $matchingRecord = $resetRecords->first(function ($record) use ($token) {
            return Hash::check($token, $record->token);
        });

        if (!$matchingRecord) {
            return redirect()->route('public.login');
        }

        // Token is valid
        return view('website.reset_password', compact('pageTitle', 'token'));
    }

    /**
     * Update password
     */
    public function updatePassword(Request $request)
    {
        try {
            // Decrypt the password fields
            $decryptedPassword = decryptPassword($request->password);
            $decryptedPasswordConfirmation = decryptPassword($request->password_confirmation);

            // Replace decrypted values into request object for validation
            $request->merge([
                'password' => $decryptedPassword,
                'password_confirmation' => $decryptedPasswordConfirmation,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'key' => setEncryptionKey(),
                'message' => 'Invalid encrypted password data.'
            ], 400);
        }

        $validator = Validator::make($request->all(), [
            'token' => 'required',
            'password' => [
                'required',
                'string',
                'min:8',
                'confirmed',
                'regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]/'
            ],
            'captcha' => 'required|captcha'
        ], [
            'password.required' => 'Password is required.',
            'password.min' => 'Password must be at least 8 characters long.',
            'password.confirmed' => 'Password confirmation does not match.',
            'password.regex' => 'Password must contain uppercase, lowercase, numbers, and special characters.',
            'captcha.captcha' => 'Invalid captcha code.',
            'token.required' => 'Reset token is required.'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'key' => setEncryptionKey(),
                'message' => 'Validation failed.',
                'errors' => $validator->errors()
            ], 422);
        }

        $resetRecords = DB::table('password_reset_tokens')
            ->where('created_at', '>', Carbon::now()->subHour())
            ->get();

        $validRecord = null;
        foreach ($resetRecords as $record) {
            if (Hash::check($request->token, $record->token)) {
                $validRecord = $record;
                break;
            }
        }

        if (!$validRecord) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid or expired OTP. Please try again.'
            ], 401);
        }

        $user = User::where('email', $validRecord->email)->first();
        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'User not found.'
            ], 404);
        }

        $user->password = Hash::make($request->password);
        $user->save();

        DB::table('password_reset_tokens')->where('email', $validRecord->email)->delete();

        if ($user) {
            $user->clearSessionInfo();
        }

        return response()->json([
            'success' => true,
            'redirect_url' => route('public.login'),
            'message' => 'Your password has been successfully updated. Please login with your new password.'
        ]);
    }
}
