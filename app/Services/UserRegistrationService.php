<?php

namespace App\Services;

use App\DTO\UserRegistrationDto;
use App\DTO\VerificationDto;
use App\Repositories\UserRepository;
use App\Repositories\VerificationCodeRepository;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use App\Mail\UserCreated;
use App\Mail\VerificationCodeMail;
use App\Mail\ResendVerificationCodeMail;

class UserRegistrationService
{
    protected $userRepository;
    protected $verificationCodeRepository;
    protected $verificationExpiryMinutes = 15;

    public function __construct(
        UserRepository $userRepository,
        VerificationCodeRepository $verificationCodeRepository
    ) {
        $this->userRepository = $userRepository;
        $this->verificationCodeRepository = $verificationCodeRepository;
    }

    /**
     * Send verification code only (without creating user)
     */
    public function sendVerificationCodeOnly($email, $name)
    {
        try {
            $code = generateVerificationCode();
            $expirationTime = Carbon::now()->addMinutes($this->verificationExpiryMinutes);

            $verificationData = [
                'email_id' => $email,
                'code' => $code,
                'expiration_time' => $expirationTime,
                'otp_used' => 0
            ];

            $verificationCode = $this->verificationCodeRepository->create($verificationData);

            if (!$verificationCode) {
                return false;
            }

            // Create a temporary user object for email
            $tempUser = (object) [
                'name' => $name,
                'email' => $email
            ];

            // Send email
            try {
                Mail::to($email)->send(new VerificationCodeMail($tempUser, $code, $this->verificationExpiryMinutes));
            } catch (\Exception $e) {
                Log::error('Failed to send verification email: ' . $e->getMessage());
                return false;
            }

            return true;
        } catch (\Exception $e) {
            Log::error('Failed to send verification code: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Verify OTP only (without creating user)
     */
    public function verifyOtpOnly(VerificationDto $dto)
    {
        DB::beginTransaction();
        try {
            $email = trim(strip_tags($dto->email_id));
            $code = trim(strip_tags($dto->verification_code));

            $verify = $this->verificationCodeRepository->findByEmailAndCode(
                $email,
                $code
            );

            if (!$verify) {
                DB::rollBack();
                return [
                    'success' => false,
                    'message' => 'Incorrect code is provided. Please enter correct code.'
                ];
            }

            if ($verify->otp_used == 1) {
                DB::rollBack();
                return [
                    'success' => false,
                    'message' => 'OTP is already used, please check your mail for recent code.'
                ];
            }

            if (Carbon::now()->gt($verify->expiration_time)) {
                DB::rollBack();
                return [
                    'success' => false,
                    'message' => 'OTP is expired, please try again.'
                ];
            }

            // Mark OTP as used
            $this->verificationCodeRepository->markAllAsUsed($email);

            DB::commit();
            return [
                'success' => true,
                'verify_type' => $dto->verify_type,
                'message' => 'OTP verified successfully'
            ];
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('OTP verification failed: ' . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Something went wrong. Please try again.'
            ];
        }
    }

    /**
     * Create verified user (after OTP verification)
     */
    public function createVerifiedUser(UserRegistrationDto $dto)
    {
        DB::beginTransaction();
        try {
            $userData = [
                'user_id' => $dto->user_id,
                'name' => $dto->name,
                'username' => $dto->username,
                'email' => $dto->email,
                'mobile_number' => $dto->phone_number,
                'password' => Hash::make($dto->password),
                'is_verified' => 1,
                'created_by' => $dto->created_by
            ];

            $user = $this->userRepository->create($userData);

            if (!$user) {
                DB::rollBack();
                return null;
            }

            // Send welcome email
            try {
                Mail::to($user->email)->send(new UserCreated($user));
            } catch (\Exception $e) {
                Log::error('Failed to send user created email: ' . $e->getMessage());
                // Don't rollback - user is created, just email failed
            }

            $user->assignRole($dto->roles);

            DB::commit();
            return $user;
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('User creation failed: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Resend verification code for pending registration
     */
    public function resendVerificationCodeForPending($email, $name)
    {
        try {
            $code = generateVerificationCode();
            $expirationTime = Carbon::now()->addMinutes($this->verificationExpiryMinutes);

            $verificationData = [
                'email_id' => $email,
                'code' => $code,
                'expiration_time' => $expirationTime,
                'otp_used' => 0
            ];

            $verificationCode = $this->verificationCodeRepository->create($verificationData);

            if (!$verificationCode) {
                return false;
            }

            // Create a temporary user object for email
            $tempUser = (object) [
                'name' => $name,
                'email' => $email
            ];

            // Send resend email
            try {
                Mail::to($email)->send(new ResendVerificationCodeMail($tempUser, $code, $this->verificationExpiryMinutes));
            } catch (\Exception $e) {
                Log::error('Failed to send resend verification email: ' . $e->getMessage());
                return false;
            }

            return true;
        } catch (\Exception $e) {
            Log::error('Failed to resend verification code: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * OLD METHOD - Register user and send verification (Creates user in DB immediately)
     * Keeping this for backwards compatibility if needed elsewhere
     */
    public function register(UserRegistrationDto $dto)
    {
        DB::beginTransaction();
        try {
            $userData = [
                'user_id' => $dto->user_id,
                'name' => $dto->name,
                'username' => $dto->username,
                'email' => $dto->email,
                'phone_number' => $dto->phone_number,
                'password' => Hash::make($dto->password),
                'is_verified' => $dto->is_verified,
                'created_by' => $dto->created_by
            ];

            $user = $this->userRepository->create($userData);

            if (!$user) {
                DB::rollBack();
                return null;
            }

            // Send verification code
            $verificationSent = $this->sendVerificationCode($user);

            if (!$verificationSent) {
                DB::rollBack();
                return null;
            }

            DB::commit();
            return $user;
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('User registration failed: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Send verification code to existing user
     */
    public function sendVerificationCode($user, $isResend = false)
    {
        try {
            $code = generateVerificationCode();
            $expirationTime = Carbon::now()->addMinutes($this->verificationExpiryMinutes);

            $verificationData = [
                'email_id' => $user->email,
                'code' => $code,
                'expiration_time' => $expirationTime,
                'otp_used' => 0
            ];

            $verificationCode = $this->verificationCodeRepository->create($verificationData);

            if (!$verificationCode) {
                return false;
            }

            // Send email based on type
            try {
                if ($isResend) {
                    Mail::to($user->email)->send(new ResendVerificationCodeMail($user, $code, $this->verificationExpiryMinutes));
                } else {
                    Mail::to($user->email)->send(new VerificationCodeMail($user, $code, $this->verificationExpiryMinutes));
                }
            } catch (\Exception $e) {
                Log::error('Failed to send verification email: ' . $e->getMessage());
                return false;
            }

            return true;
        } catch (\Exception $e) {
            Log::error('Failed to send verification code: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Resend verification code to existing user
     */
    public function resendVerificationCode($user)
    {
        return $this->sendVerificationCode($user, true);
    }

    /**
     * Verify user account (for existing users who were already in DB)
     */
    public function verifyUser(VerificationDto $dto)
    {
        DB::beginTransaction();
        try {
            $email = trim(strip_tags($dto->email_id));
            $code = trim(strip_tags($dto->verification_code));

            $verify = $this->verificationCodeRepository->findByEmailAndCode(
                $email,
                $code
            );

            if (!$verify) {
                return [
                    'success' => false,
                    'message' => 'Incorrect code is provided. Please enter correct code.'
                ];
            }

            if ($verify->otp_used == 1) {
                return [
                    'success' => false,
                    'message' => 'OTP is already used, please check your mail for recent code.'
                ];
            }

            if (Carbon::now()->gt($verify->expiration_time)) {
                return [
                    'success' => false,
                    'message' => 'OTP is expired, please try again.'
                ];
            }

            // Handle different verification types
            if ($dto->verify_type == 'FORGOT_PASSWORD') {
                $this->verificationCodeRepository->markAllAsUsed($email);
                DB::commit();
                return [
                    'success' => true,
                    'verify_type' => 'FORGOT_PASSWORD',
                    'message' => 'OTP verified successfully'
                ];
            }

            if ($dto->verify_type == 'USER_REGISTER') {
                $updated = $this->userRepository->updateVerificationStatus($email, 1);

                if (!$updated) {
                    DB::rollBack();
                    return [
                        'success' => false,
                        'message' => 'Server failed, please try again.'
                    ];
                }

                $this->verificationCodeRepository->markAllAsUsed($email);

                $user = $this->userRepository->findByEmail($email);

                if (!$user) {
                    DB::rollBack();
                    return [
                        'success' => false,
                        'message' => 'Error occurred while searching for user. Please try again or contact support team.'
                    ];
                }

                // Send user creation email
                try {
                    Mail::to($user->email)->send(new UserCreated($user));
                } catch (\Exception $e) {
                    Log::error('Failed to send user created email: ' . $e->getMessage());
                    // Don't rollback - user is created, just email failed
                }

                DB::commit();
                return [
                    'success' => true,
                    'verify_type' => 'USER_REGISTER',
                    'message' => 'Account verified successfully. Please login again.'
                ];
            }

            DB::rollBack();
            return [
                'success' => false,
                'message' => 'Invalid verification type.'
            ];
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('User verification failed: ' . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Something went wrong. Please try again.'
            ];
        }
    }
}
