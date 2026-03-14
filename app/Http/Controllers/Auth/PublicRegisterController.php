<?php

namespace App\Http\Controllers\Auth;

use App\DTO\UserRegistrationDto;
use App\DTO\VerificationDto;
use App\Http\Controllers\Controller;
use App\Http\Requests\UserRegistrationRequest;
use App\Http\Requests\VerifyUserRequest;
use App\Services\UserRegistrationService;
use App\Repositories\UserRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\URL;

class PublicRegisterController extends Controller
{
    protected $userRegistrationService;
    protected $userRepository;

    public function __construct(
        UserRegistrationService $userRegistrationService,
        UserRepository $userRepository
    ) {
        $this->userRegistrationService = $userRegistrationService;
        $this->userRepository = $userRepository;
    }

    /**
     * Show registration form
     */
    public function register()
    {
        return view('website.register')->with(['pageTitle' => 'Customer Registration']);
    }

    /**
     * Handle user registration - Store in session and send OTP
     */
    public function storeRegisteration(UserRegistrationRequest $request)
    {
        try {
            // Store user registration data in session (not in DB yet)
            $registrationData = [
                'name' => strip_tags($request->input('name')),
                'email' => strip_tags($request->input('email_id')),
                'phone_number' => strip_tags($request->input('phone_number')),
                'password' => $request->input('password'), // Will be hashed when saving to DB
            ];

            $request->session()->put('pending_registration', $registrationData);

            // Send verification code
            $verificationSent = $this->userRegistrationService->sendVerificationCodeOnly(
                $registrationData['email'],
                $registrationData['name']
            );

            if (!$verificationSent) {
                $request->session()->forget('pending_registration');
                return Response::json([
                    'status' => false,
                    'message' => 'Failed to send verification code. Please try again.'
                ], 500);
            }

            // Store verification session data
            $request->session()->put('user', [
                'name' => $registrationData['name'],
                'email' => $registrationData['email'],
                'phone_number' => $registrationData['phone_number'],
            ]);

            // Handle redirect after verification
            if ($request->has('type') && customUrlDecode($request->type) == 'checkout') {
                $request->session()->put('redirect_route_after_verification', route('checkout'));
            }

            $request->session()->put('verify_account', true);
            $request->session()->put('verify_type', 'USER_REGISTER');

            return Response::json([
                'status' => true,
                'redirect_to' => route('public.register.user-verification'),
                'message' => 'Registration done successfully. Please verify your account. You will receive a verification code on email id -> ' . $registrationData['email']
            ], 201);
        } catch (\Exception $e) {
            Log::error('User registration failed: ' . $e->getMessage());
            $request->session()->forget('pending_registration');
            return Response::json([
                'status' => false,
                'message' => 'Server failed to respond. Please try again.'
            ], 500);
        }
    }

    /**
     * Show user verification form
     */
    public function showUserVerifyForm(Request $request)
    {
        if ($request->session()->get('verify_account') && $request->session()->has('pending_registration')) {
            return view('website.verify-user')->with(['pageTitle' => 'Verify User']);
        }

        return redirect('page-not-found');
    }

    /**
     * Verify user account and save to database
     */
    public function userVerify(VerifyUserRequest $request)
    {
        try {
            // Check if pending registration exists in session
            if (!session()->has('pending_registration')) {
                return Response::json([
                    'status' => false,
                    'message' => 'Registration session expired. Please register again.'
                ], 400);
            }

            $verificationDto = new VerificationDto(
                strip_tags($request->input('email_id')),
                strip_tags($request->input('verification_code')),
                session()->get('verify_type')
            );

            // Verify the OTP first
            $verificationResult = $this->userRegistrationService->verifyOtpOnly($verificationDto);

            if (!$verificationResult['success']) {
                return Response::json([
                    'status' => false,
                    'message' => $verificationResult['message']
                ], 400);
            }

            // Handle different verification types
            if ($verificationResult['verify_type'] == 'FORGOT_PASSWORD') {
                return Response::json([
                    'status' => true,
                    'redirect_to' => URL::to('reset-password/' . Crypt::encryptString($request->email_id)),
                    'message' => $verificationResult['message']
                ], 200);
            }

            if ($verificationResult['verify_type'] == 'USER_REGISTER') {
                // Get pending registration data from session
                $pendingData = session()->get('pending_registration');

                // Create UserRegistrationDto
                $userDto = new UserRegistrationDto(
                    $pendingData['name'],
                    $pendingData['email'],
                    $pendingData['phone_number'],
                    $pendingData['password']
                );

                // Now save user to database after OTP verification
                $user = $this->userRegistrationService->createVerifiedUser($userDto);

                if (!$user) {
                    return Response::json([
                        'status' => false,
                        'message' => 'Failed to create user account. Please try again.'
                    ], 500);
                }

                // Clear session data
                $request->session()->forget(['pending_registration', 'verify_account', 'verify_type', 'user']);

                $redirectUrl = session()->has('redirect_route_after_verification')
                    ? URL::to(session()->get('redirect_route_after_verification'))
                    : URL::to('/login');

                // Clear redirect route
                $request->session()->forget('redirect_route_after_verification');

                return Response::json([
                    'status' => true,
                    'redirect_to' => $redirectUrl,
                    'message' => 'Account verified and created successfully. Please login.'
                ], 200);
            }

            return Response::json([
                'status' => false,
                'message' => 'Invalid verification type.'
            ], 400);
        } catch (\Exception $e) {
            Log::error('User verification failed: ' . $e->getMessage());
            return Response::json([
                'status' => false,
                'message' => 'Something went wrong. Please try again.'
            ], 500);
        }
    }

    /**
     * Resend OTP
     */
    public function resendOtp(Request $request)
    {
        try {
            // Check if pending registration exists
            if (!session()->has('pending_registration')) {
                return Response::json([
                    'status' => false,
                    'message' => 'Registration session expired. Please register again.'
                ], 400);
            }

            $pendingData = session()->get('pending_registration');

            $result = $this->userRegistrationService->resendVerificationCodeForPending(
                $pendingData['email'],
                $pendingData['name']
            );

            if (!$result) {
                return Response::json([
                    'status' => false,
                    'message' => 'Server failed while resending OTP. Please try again.'
                ], 500);
            }

            return Response::json([
                'status' => true,
                'message' => 'A new verification code is sent on email id -> ' . $pendingData['email']
            ], 200);
        } catch (\Exception $e) {
            Log::error('Resend OTP failed: ' . $e->getMessage());
            return Response::json([
                'status' => false,
                'message' => 'Server failed while resending OTP. Please try again.'
            ], 500);
        }
    }
}
