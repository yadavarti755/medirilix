<?php

namespace App\Http\Controllers;

use App\Models\Otp;
use App\Mail\SendOtpEmail;
use App\Mail\SendOtpEmailForDHTIApplication;
use App\Services\RateLimitService;
use App\Services\SMSService;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;

class OtpController extends Controller
{
    protected $rateLimitService;

    public function __construct(RateLimitService $rateLimitService)
    {
        $this->rateLimitService = $rateLimitService;
    }

    public function sendOtpForDHTIApplicationSubmission(Request $request)
    {
        // Define validation rules
        $rules = [
            'email' => 'required|email|max:255',
            'mobile' => 'required|digits:10',
        ];

        // Create a validator instance and validate the request data
        $validator = Validator::make($request->all(), $rules);

        // Check if validation fails
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation errors occurred.',
                'errors' => $validator->errors(),
            ], 422);
        }

        // Check OTP generation rate limiting for DHTI
        $otpGenerationLimit = $this->rateLimitService->checkDhtiOtpGenerationLimit($request);
        if ($otpGenerationLimit['limited']) {
            return response()->json([
                'success' => false,
                'message' => $otpGenerationLimit['message']
            ], 429);
        }

        DB::beginTransaction();

        try {
            // Generate and send OTP
            $emailOtpData = Otp::generateOtp($request->email, null);
            if (!$emailOtpData['success']) {
                return response()->json([
                    'success' => false,
                    'message' => $emailOtpData['message']
                ], 429);
            }

            $mobileOtpData = Otp::generateOtp(null, $request->mobile);
            if (!$mobileOtpData['success']) {
                return response()->json([
                    'success' => false,
                    'message' => $mobileOtpData['message']
                ], 429);
            }

            // Record OTP generation attempt for DHTI
            $this->rateLimitService->recordDhtiOtpGenerationAttempt($request);

            Mail::to($request->email)->send(new SendOtpEmailForDHTIApplication($emailOtpData['otp']));

            // SMS dispatch
            // Sms::send($request->mobile, "Your OTP is: $mobileOtpData['otp']");
            $smsService = new SMSService();
            $smsService->sendDhtiApplicationOtp($request->mobile, $mobileOtpData['otp']);

            session()->put('email_otp_verified', false);
            session()->put('mobile_otp_verified', false);

            DB::commit();
            return response()->json([
                'success' => true,
                'message' => 'OTPs sent successfully.'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('OTP send error', [
                'email'    => $request->email,
                'mobile'   => $request->mobile,
                'error'    => $e->getMessage(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to send OTP. Please try again later.',
                'error'   => $e instanceof \Illuminate\Validation\ValidationException
                    ? $e->errors()
                    : $e->getMessage(),
            ], 500);
        }
    }

    public function resendEmailOtpForDHTIApplicationSubmission(Request $request)
    {
        $data = $request->validate([
            'email'  => 'required|email',
        ]);

        // Check email OTP resend rate limiting for DHTI
        $emailOtpResendLimit = $this->rateLimitService->checkDhtiEmailOtpResendLimit($request);
        if ($emailOtpResendLimit['limited']) {
            return response()->json([
                'success' => false,
                'message' => $emailOtpResendLimit['message']
            ], 429);
        }

        // Check if the otp are sent more than desired times
        $emailOtpData = Otp::generateOtp($data['email'], null);
        if (!$emailOtpData['success']) {
            return response()->json([
                'success' => false,
                'message' => $emailOtpData['message']
            ], 429);
        }

        // Record email OTP resend attempt for DHTI
        $this->rateLimitService->recordDhtiEmailOtpResendAttempt($request);

        Mail::to($data['email'])->send(new SendOtpEmailForDHTIApplication($emailOtpData['otp']));
        session()->put('email_otp_verified', false);

        return response()->json([
            'success' => true,
            'message' => 'Email OTP sent successfully.',
        ]);
    }

    public function resendMobileOtpForDHTIApplicationSubmission(Request $request)
    {
        $data = $request->validate([
            'mobile' => 'required|string|max:10',
        ]);

        // Check mobile OTP resend rate limiting for DHTI
        $mobileOtpResendLimit = $this->rateLimitService->checkDhtiMobileOtpResendLimit($request);
        if ($mobileOtpResendLimit['limited']) {
            return response()->json([
                'success' => false,
                'message' => $mobileOtpResendLimit['message']
            ], 429);
        }

        // Check if the otp are sent more than desired times
        $mobileOtpData = Otp::generateOtp($data['mobile'], null);
        if (!$mobileOtpData['success']) {
            return response()->json([
                'success' => false,
                'message' => $mobileOtpData['message']
            ], 429);
        }

        // Record mobile OTP resend attempt for DHTI
        $this->rateLimitService->recordDhtiMobileOtpResendAttempt($request);

        // Send SMS
        $smsService = new SMSService();
        $smsService->sendDhtiApplicationOtp($data['mobile'], $mobileOtpData['otp']);

        session()->put('mobile_otp_verified', false);

        return response()->json([
            'success' => true,
            'message' => 'Mobile OTP sent successfully.',
        ]);
    }

    /**
     * Verify email otp
     */
    public function verifyEmailOtp(Request $request)
    {
        // Check rate limiting first
        $rateLimitCheck = $this->rateLimitService->isRateLimited($request);

        if ($rateLimitCheck['limited']) {
            return response()->json([
                'success' => false,
                'message' => $rateLimitCheck['message']
            ], 429); // Too Many Requests
        }

        // Define validation rules
        $rules = [
            'email' => 'required|email|max:255',
            'otp' => 'required|max:20',
        ];

        // Create a validator instance and validate the request data
        $validator = Validator::make($request->all(), $rules);

        // Check if validation fails
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation errors occurred.',
                'errors' => $validator->errors(),
            ], 422);
        }

        if (Otp::verifyOtp($request->otp, $request->email, null)) {
            // Record successful attempt (clears rate limiting)
            $this->rateLimitService->recordSuccessfulAttempt($request);

            session()->put('email_otp_verified', true);
            return response()->json(['success' => true, 'message' => 'Email OTP verified.'], 200);
        } else {
            // Record failed attempt
            $failedAttemptResult = $this->rateLimitService->recordFailedAttempt($request);

            return response()->json([
                'success' => false,
                'message' => $failedAttemptResult['message'],
            ], $failedAttemptResult['blocked'] ? 429 : 400);
        }
    }

    /**
     * Verify mobile otp
     */
    public function verifyMobileOtp(Request $request)
    {
        // Check rate limiting first
        $rateLimitCheck = $this->rateLimitService->isRateLimited($request);

        if ($rateLimitCheck['limited']) {
            return response()->json([
                'success' => false,
                'message' => $rateLimitCheck['message']
            ], 429); // Too Many Requests
        }

        // Define validation rules
        $rules = [
            'mobile' => 'required|max:10',
            'otp' => 'required|max:20',
        ];

        // Create a validator instance and validate the request data
        $validator = Validator::make($request->all(), $rules);

        // Check if validation fails
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation errors occurred.',
                'errors' => $validator->errors(),
            ], 422);
        }

        if (Otp::verifyOtp($request->otp, null, $request->mobile)) {
            // Record successful attempt (clears rate limiting)
            $this->rateLimitService->recordSuccessfulAttempt($request);

            session()->put('mobile_otp_verified', true);
            return response()->json(['success' => true, 'message' => 'Mobile OTP verified.'], 200);
        } else {
            // Record failed attempt
            $failedAttemptResult = $this->rateLimitService->recordFailedAttempt($request);

            return response()->json([
                'success' => false,
                'message' => $failedAttemptResult['message'],
            ], $failedAttemptResult['blocked'] ? 429 : 400);
        }
    }
}
