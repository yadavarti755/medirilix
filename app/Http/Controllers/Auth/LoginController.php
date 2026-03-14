<?php

namespace App\Http\Controllers\Auth;

use Carbon\Carbon;
use App\Http\Controllers\Controller;
use App\Mail\BackendLoginOtpMail;
use App\Models\Otp;
use App\Models\User;
use App\Services\EmailService;
use App\Services\RateLimitService;
use App\Services\SMSService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;

class LoginController extends Controller
{
    protected $rateLimitService;

    public function __construct(RateLimitService $rateLimitService)
    {
        $this->rateLimitService = $rateLimitService;
    }

    public function index()
    {
        setEncryptionKey();
        return view('auth.login');
    }

    // Login
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
            $userRoles = $user->roles->pluck('name')->toArray();

            if (in_array('USER', $userRoles)) {
                auth()->logout();
                return response()->json([
                    'status' => 'error',
                    'message' => 'You are not allowed to login from here.',
                    'key' => setEncryptionKey()
                ], 401);
            }

            if (!$user) {
                auth()->logout();
                return response()->json([
                    'status' => 'error',
                    'message' => 'No user found for the entered credentials.',
                    'key' => setEncryptionKey()
                ], 401);
            }

            $request->session()->regenerate();
            // Login the user
            Auth::login($user);

            $userRoles = $user->roles->pluck('name')->toArray();
            $userLandingPageUrls = $user->roles->pluck('landing_page_url')->toArray();
            $redirectUrl = '';

            if (in_array('SUPERADMIN', $userRoles)) {
                $redirectUrl = route('admin.dashboard');
            } else {
                $url = (!empty($userLandingPageUrls)) ? $userLandingPageUrls[0] : '/';
                $redirectUrl = url()->to($url);
            }

            // Update session information
            $user->updateSessionInfo();

            return response()->json([
                'status' => 'success',
                'message' => 'Login successful!',
                'redirect' => $redirectUrl
            ]);
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
     * Show reset password form
     */
    public function showResetPasswordForm($token)
    {
        setEncryptionKey();
        $pageTitle = 'Reset Password';

        // Get recent reset token records within the last hour
        $resetRecords = DB::table('password_reset_tokens')
            ->where('created_at', '>', Carbon::now()->subHours(48))
            ->get();

        $token = base64_decode($token);

        // Search for matching hashed token
        $matchingRecord = $resetRecords->first(function ($record) use ($token) {
            return Hash::check($token, $record->token);
        });

        if (!$matchingRecord) {
            return redirect()->route('login');
        }

        // Token is valid
        return view('auth.reset_password', compact('pageTitle',  'token'));
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

        return response()->json([
            'success' => true,
            'redirect_url' => route('login'),
            'message' => 'Your password has been successfully updated. Please login with your new password.'
        ]);
    }
}
