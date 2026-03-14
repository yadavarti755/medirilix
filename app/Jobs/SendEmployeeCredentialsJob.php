<?php

namespace App\Jobs;

use App\Models\User;
use App\Services\EmailService;
use App\Services\SMSService;
use App\Services\EmailLogService;
use App\DTOs\EmailLogDto;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Carbon\Carbon;

class SendEmployeeCredentialsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $userId;
    protected $sendEmail;
    protected $sendSms;

    /**
     * The number of times the job may be attempted.
     */
    public $tries = 3;

    /**
     * The maximum number of seconds the job can run.
     */
    public $timeout = 120;

    /**
     * Create a new job instance.
     */
    public function __construct(int $userId, bool $sendEmail = true, bool $sendSms = true)
    {
        $this->userId = $userId;
        $this->sendEmail = $sendEmail;
        $this->sendSms = $sendSms;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        try {
            $user = User::with('roles')->find($this->userId);

            if (!$user) {
                Log::error("User not found with ID: {$this->userId}");
                return;
            }

            // Generate and store password reset token
            $resetUrl = $this->generatePasswordResetToken($user);

            // Send email if requested
            if ($this->sendEmail && $user->email) {
                $this->sendPasswordResetEmail($user, $resetUrl);
            }

            // Send SMS if requested
            if ($this->sendSms && $user->mobile_number) {
                $this->sendPasswordResetSms($user, $resetUrl);
            }

            Log::info("Successfully processed credentials for user: {$user->email} (ID: {$this->userId})");
        } catch (\Exception $e) {
            Log::error("Failed to send credentials for user ID {$this->userId}: " . $e->getMessage());
            throw $e; // Re-throw to trigger job retry
        }
    }

    /**
     * Generate password reset token and return reset URL
     */
    private function generatePasswordResetToken(User $user): string
    {
        // Generate token
        $token = Str::random(60);
        $hashedToken = Hash::make($token);

        // Delete any existing reset tokens for this email
        DB::table('password_reset_tokens')->where('email', $user->email)->delete();

        // Create new reset token record
        DB::table('password_reset_tokens')->insert([
            'email' => $user->email,
            'token' => $hashedToken,
            'created_at' => Carbon::now()
        ]);

        // Encode the token for URL safety
        $encodedToken = base64_encode($token);

        // Determine reset URL based on user role
        $userRoles = $user->roles->pluck('name')->toArray();
        if (in_array('EMPLOYEE', $userRoles)) {
            return route('employee-reset-password.form', ['token' => $encodedToken]);
        } else {
            return route('backend-reset-password.form', ['token' => $encodedToken]);
        }
    }

    /**
     * Send password reset email
     */
    private function sendPasswordResetEmail(User $user, string $resetUrl): void
    {
        try {
            EmailService::sendInitialPasswordResetEmail($user->email, $user->name, $resetUrl, true);
            Log::info("Password reset email queued for: {$user->email}");
        } catch (\Exception $e) {
            Log::error("Failed to send password reset email to {$user->email}: " . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Send password reset SMS
     */
    private function sendPasswordResetSms(User $user, string $resetUrl): void
    {
        try {
            $smsService = new SMSService();
            $result = $smsService->sendPasswordSetup($user->mobile_number, $user->name, $resetUrl, true);

            // Log SMS result
            if (isset($result['status']) && $result['status'] === 'success') {
                Log::info("Password reset SMS sent successfully to: {$user->mobile_number}");
            } else {
                Log::warning("SMS sending may have failed for {$user->mobile_number}: " . json_encode($result));
            }
        } catch (\Exception $e) {
            Log::error("Failed to send password reset SMS to {$user->mobile_number}: " . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Handle job failure
     */
    public function failed(\Throwable $exception): void
    {
        Log::error("SendEmployeeCredentialsJob failed for user ID {$this->userId} after {$this->tries} attempts: " . $exception->getMessage());

        // Optionally, you could send an admin notification here
        // or mark the user record with a failed status
    }
}
