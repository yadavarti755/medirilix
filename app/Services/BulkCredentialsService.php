<?php

namespace App\Services;

use App\Jobs\SendEmployeeCredentialsJob;
use App\Models\User;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;

class BulkCredentialsService
{
    /**
     * Send credentials to multiple users
     */
    public function sendCredentialsToUsers(
        Collection|array $userIds,
        bool $sendEmail = true,
        bool $sendSms = true,
        int $delayBetweenJobs = 5
    ): array {
        $dispatched = 0;
        $failed = 0;
        $userIds = is_array($userIds) ? collect($userIds) : $userIds;

        foreach ($userIds as $index => $userId) {
            try {
                // Add delay between jobs to prevent overwhelming the queue/services
                $delay = $index * $delayBetweenJobs;

                SendEmployeeCredentialsJob::dispatch($userId, $sendEmail, $sendSms)
                    ->delay(now()->addSeconds($delay))
                    ->onQueue('credentials'); // Use a dedicated queue for better control

                $dispatched++;
            } catch (\Exception $e) {
                Log::error("Failed to dispatch credentials job for user ID {$userId}: " . $e->getMessage());
                $failed++;
            }
        }

        Log::info("Bulk credentials dispatch completed. Dispatched: {$dispatched}, Failed: {$failed}");

        return [
            'dispatched' => $dispatched,
            'failed' => $failed,
            'total' => $userIds->count()
        ];
    }

    /**
     * Send credentials to users by email addresses
     */
    public function sendCredentialsByEmails(
        array $emails,
        bool $sendEmail = true,
        bool $sendSms = true,
        int $delayBetweenJobs = 5
    ): array {
        $users = User::whereIn('email', $emails)->pluck('id');

        if ($users->isEmpty()) {
            Log::warning("No users found for provided emails");
            return ['dispatched' => 0, 'failed' => 0, 'total' => 0];
        }

        return $this->sendCredentialsToUsers($users, $sendEmail, $sendSms, $delayBetweenJobs);
    }

    /**
     * Send credentials to users with specific role
     */
    public function sendCredentialsByRole(
        string $roleName,
        bool $sendEmail = true,
        bool $sendSms = true,
        int $delayBetweenJobs = 5
    ): array {
        $users = User::whereHas('roles', function ($query) use ($roleName) {
            $query->where('name', $roleName);
        })->pluck('id');

        if ($users->isEmpty()) {
            Log::warning("No users found with role: {$roleName}");
            return ['dispatched' => 0, 'failed' => 0, 'total' => 0];
        }

        return $this->sendCredentialsToUsers($users, $sendEmail, $sendSms, $delayBetweenJobs);
    }

    /**
     * Send credentials to recently created users
     */
    public function sendCredentialsToRecentUsers(
        int $hoursBack = 24,
        bool $sendEmail = true,
        bool $sendSms = true,
        int $delayBetweenJobs = 5
    ): array {
        $users = User::where('created_at', '>=', now()->subHours($hoursBack))
            ->pluck('id');

        if ($users->isEmpty()) {
            Log::warning("No users found created in the last {$hoursBack} hours");
            return ['dispatched' => 0, 'failed' => 0, 'total' => 0];
        }

        return $this->sendCredentialsToUsers($users, $sendEmail, $sendSms, $delayBetweenJobs);
    }

    /**
     * Get job status summary
     */
    public function getJobStatusSummary(): array
    {
        // This would require you to implement job status tracking
        // You might want to store job statuses in a separate table
        return [
            'pending' => 0,
            'processing' => 0,
            'completed' => 0,
            'failed' => 0
        ];
    }
}
