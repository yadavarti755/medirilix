<?php

namespace App\Services;

use App\DTO\EmailLogDto;
use App\Mail\BackendLoginOtpMail;
use App\Mail\DHTIApplicationSubmitted;
use App\Mail\DHTICertificateIssuedMail;
use App\Mail\EmployeeForgotPasswordOtpMail;
use App\Mail\EmployeeLoginOtpMail;
use App\Mail\PasswordResetMail;
use Exception;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use App\Models\SiteSetting;

class EmailService
{
    public function __construct(
        private EmailLogService $emailLogService
    ) {}

    /**
     * Send password reset email
     */
    public static function sendPasswordResetEmail($email, $resetUrl, $name = '', $isNewUser = false)
    {
        $emailLogService = app(EmailLogService::class);

        try {
            Mail::to($email)->send(new PasswordResetMail($resetUrl, $email, $name, $isNewUser));
            $siteName = SiteSetting::first()?->site_name ?? config('app.name');
            $emailLogDto = new EmailLogDto(
                $email,
                'password_reset',
                'Reset Your ' . $siteName . ' Account Password',
                'sent',
                $name,
                '',
                [
                    'is_new_user' => $isNewUser,
                    'reset_url' => $resetUrl,
                ],
                date('Y-m-d H:i:s')
            );

            $emailLogResult = $emailLogService->create($emailLogDto);
            if (!$emailLogResult) {
                Log::error('Failed to log email sent ' . date('Y-m-d H:i:s'));
            }

            Log::info('Reset Password Mail Sent' . date('Y-m-d H:i:s') . ' to ' . $email . ' - ' . $resetUrl);
        } catch (\Exception $e) {
            Log::error('Failed to send password reset email: ' . $e->getMessage());

            $siteName = SiteSetting::first()?->site_name ?? config('app.name');
            $emailLogDto = new EmailLogDto(
                $email,
                'password_reset',
                'Reset Your ' . $siteName . ' Account Password',
                'failed',
                $name,
                $e->getMessage(),
                [
                    'is_new_user' => $isNewUser,
                    'reset_url' => $resetUrl,
                ],
                date('Y-m-d H:i:s')
            );

            $emailLogResult = $emailLogService->create($emailLogDto);
            if (!$emailLogResult) {
                Log::error('Failed to log email sent ' . date('Y-m-d H:i:s'));
            }

            Log::info('Reset Password Mail Sent' . date('Y-m-d H:i:s') . ' to ' . $email . ' - ' . $resetUrl);
        }
    }

    /**
     * Send OTP email for backend login
     */
    public static function sendBackendOtpEmail($email, $otp)
    {
        $emailLogService = app(EmailLogService::class);

        try {
            Mail::to($email)->send(new BackendLoginOtpMail($otp));

            $siteName = SiteSetting::first()?->site_name ?? config('app.name');
            $emailLogDto = new EmailLogDto(
                $email,
                'backend_otp',
                'Your ' . $siteName . ' Login OTP',
                'sent',
                '',
                '',
                [
                    'otp' => $otp
                ],
                date('Y-m-d H:i:s')
            );

            $emailLogResult = $emailLogService->create($emailLogDto);
            if (!$emailLogResult) {
                Log::error('Failed to log OTP email sent ' . date('Y-m-d H:i:s'));
            }

            Log::info('OTP Mail Sent' . date('Y-m-d H:i:s') . ' to ' . $email);
        } catch (\Exception $e) {
            $siteName = SiteSetting::first()?->site_name ?? config('app.name');
            $emailLogDto = new EmailLogDto(
                $email,
                'backend_otp',
                'Your ' . $siteName . ' Login OTP',
                'failed',
                '',
                $e->getMessage(),
                [
                    'otp' => $otp
                ],
                date('Y-m-d H:i:s')
            );

            $emailLogResult = $emailLogService->create($emailLogDto);
            if (!$emailLogResult) {
                Log::error('Failed to log OTP email sent ' . date('Y-m-d H:i:s'));
            }
            Log::error('Failed to send OTP email: ' . $e->getMessage());
        }
    }

    /**
     * Send OTP email for employee login
     */
    public static function sendOtpEmail($email, $otp)
    {
        $emailLogService = app(EmailLogService::class);
        try {
            Mail::to($email)->send(new EmployeeLoginOtpMail($otp));

            $siteName = SiteSetting::first()?->site_name ?? config('app.name');
            // Email Logs
            $emailLogDto = new EmailLogDto(
                $email,
                'employee_otp',
                'Your ' . $siteName . ' Login OTP',
                'sent',
                '',
                '',
                [
                    'otp' => $otp
                ],
                date('Y-m-d H:i:s')
            );

            $emailLogResult = $emailLogService->create($emailLogDto);
            if (!$emailLogResult) {
                Log::error('Failed to log OTP email sent ' . date('Y-m-d H:i:s'));
            }
            Log::info('OTP Mail Sent' . date('Y-m-d H:i:s') . ' to ' . $email);
        } catch (\Exception $e) {
            $siteName = SiteSetting::first()?->site_name ?? config('app.name');
            // Email Logs
            $emailLogDto = new EmailLogDto(
                $email,
                'employee_otp',
                'Your ' . $siteName . ' Login OTP',
                'failed',
                '',
                $e->getMessage(),
                [
                    'otp' => $otp
                ],
                date('Y-m-d H:i:s')
            );

            $emailLogResult = $emailLogService->create($emailLogDto);
            if (!$emailLogResult) {
                Log::error('Failed to log OTP email sent ' . date('Y-m-d H:i:s'));
            }
            Log::error('Failed to send OTP email: ' . $e->getMessage());
        }
    }

    /**
     * Send OTP email for employee login
     */
    public static function sendForgotPasswordOtpEmail($email, $otp)
    {
        $emailLogService = app(EmailLogService::class);
        try {
            Mail::to($email)->send(new EmployeeForgotPasswordOtpMail($otp));

            $siteName = SiteSetting::first()?->site_name ?? config('app.name');
            // Email Logs
            $emailLogDto = new EmailLogDto(
                $email,
                'forgot_password_otp',
                'Your ' . $siteName . ' Forgot Password OTP',
                'sent',
                '',
                '',
                [
                    'otp' => $otp
                ],
                date('Y-m-d H:i:s')
            );

            $emailLogResult = $emailLogService->create($emailLogDto);
            if (!$emailLogResult) {
                Log::error('Failed to log OTP email sent ' . date('Y-m-d H:i:s'));
            }
            Log::info('OTP Mail Sent' . date('Y-m-d H:i:s') . ' to ' . $email);
        } catch (\Exception $e) {
            $siteName = SiteSetting::first()?->site_name ?? config('app.name');
            // Email Logs
            $emailLogDto = new EmailLogDto(
                $email,
                'forgot_password_otp',
                'Your ' . $siteName . ' Forgot Password OTP',
                'failed',
                '',
                $e->getMessage(),
                [
                    'otp' => $otp
                ],
                date('Y-m-d H:i:s')
            );

            $emailLogResult = $emailLogService->create($emailLogDto);
            if (!$emailLogResult) {
                Log::error('Failed to log OTP email sent ' . date('Y-m-d H:i:s'));
            }
            Log::error('Failed to send OTP email: ' . $e->getMessage());
        }
    }

    /**
     * Send initial password reset email for new users
     */
    public static function sendInitialPasswordResetEmail($email, $name, $resetUrl,  $isNewUser = true)
    {
        $emailLogService = app(EmailLogService::class);
        try {
            Mail::to($email)->send(new PasswordResetMail($resetUrl, $email, $name, $isNewUser));
            $siteName = SiteSetting::first()?->site_name ?? config('app.name');
            // Email Logs
            $emailLogDto = new EmailLogDto(
                $email,
                'password_reset',
                'Set Your ' . $siteName . ' Account Password',
                'sent',
                $name,
                '',
                [
                    'reset_url' => $resetUrl,
                    'is_new_user' => $isNewUser
                ],
                date('Y-m-d H:i:s')
            );

            $emailLogResult = $emailLogService->create($emailLogDto);
            if (!$emailLogResult) {
                Log::error('Failed to log password reset email sent ' . date('Y-m-d H:i:s'));
            }
            Log::info('Initial password reset email sent successfully to: ' . $email);
        } catch (Exception $e) {
            $siteName = SiteSetting::first()?->site_name ?? config('app.name');
            // Email Logs
            $emailLogDto = new EmailLogDto(
                $email,
                'password_reset',
                'Set Your ' . $siteName . ' Account Password',
                'failed',
                $name,
                $e->getMessage(),
                [
                    'reset_url' => $resetUrl,
                    'is_new_user' => $isNewUser
                ],
                date('Y-m-d H:i:s')
            );

            $emailLogResult = $emailLogService->create($emailLogDto);
            if (!$emailLogResult) {
                Log::error('Failed to log password reset email sent ' . date('Y-m-d H:i:s'));
            }
            Log::error('Failed to send initial password reset email: ' . $e->getMessage());
        }
    }

    public static function sendApplicationConfirmationEmail($application, $downloadUrl)
    {
        $emailLogService = app(EmailLogService::class);

        try {
            $mailable = new DHTIApplicationSubmitted(
                name: $application->name,
                applicationNumber: $application->application_number,
                downloadUrl: $downloadUrl,
                courseTitle: $application->course_title ?? 'N/A'
            );

            Mail::to($application->email_id, $application->name)->send($mailable);

            $siteName = SiteSetting::first()?->site_name ?? config('app.name');
            // Email Logs
            $emailLogDto = new EmailLogDto(
                $application->email_id,
                'dhti_confirmation',
                'Your ' . $siteName . ' Application Has Been Submitted Successfully',
                'sent',
                $application->name,
                '',
                [
                    'application_number' => $application->application_number,
                    'download_url' => $downloadUrl,
                    'name' => $application->name,
                    'course_title' => $application->course_title ?? 'N/A'
                ],
                date('Y-m-d H:i:s')
            );

            $emailLogResult = $emailLogService->create($emailLogDto);
            if (!$emailLogResult) {
                Log::error('Failed to log DHTI application email sent ' . date('Y-m-d H:i:s'));
            }

            Log::info('Application ' . $application->application_number . ' submitted and confirmation email sent to: ' . $application->email_id);
            return true;
        } catch (\Exception $e) {
            // Email Logs
            $emailLogDto = new EmailLogDto(
                $application->email_id,
                'dhti_confirmation',
                'Your DHTI Application Has Been Submitted Successfully',
                'failed',
                $application->name,
                $e->getMessage(),
                [
                    'application_number' => $application->application_number,
                    'download_url' => $downloadUrl,
                    'name' => $application->name,
                    'course_title' => $application->course_title ?? 'N/A'
                ],
                date('Y-m-d H:i:s')
            );

            $emailLogResult = $emailLogService->create($emailLogDto);
            if (!$emailLogResult) {
                Log::error('Failed to log DHTI application email sent ' . date('Y-m-d H:i:s'));
            }
            Log::error('Failed to send DHTI application ' . $application->application_number . ' confirmation email: ' . $e->getMessage());
            // Don't throw exception here as application is already submitted
            return false;
        }
    }

    public static function sendCertificateIssuedEmail($applicantData, $courseCode, $encodedUrl)
    {
        try {
            Mail::send(new DHTICertificateIssuedMail($applicantData, $courseCode, $encodedUrl));

            Log::info('Certificate email sent successfully', [
                'application_number' => $applicantData['application_number'],
                'email' => $applicantData['email_id']
            ]);
        } catch (Exception $e) {
            Log::error('Failed to send certificate email', [
                'application_number' => $applicantData['application_number'],
                'email' => $applicantData['email_id'],
                'error' => $e->getMessage()
            ]);
        }
    }
}
