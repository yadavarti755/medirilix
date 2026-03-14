<?php

namespace App\Console\Commands;

use App\Services\BulkCredentialsService;
use App\Models\User;
use Illuminate\Console\Command;

class SendCredentialsCommand extends Command
{
    protected $signature = 'employees:send-credentials
                            {--role= : Send to users with specific role}
                            {--recent= : Send to users created in last X hours}
                            {--emails= : Comma-separated list of emails}
                            {--user-ids= : Comma-separated list of user IDs}
                            {--no-email : Skip sending emails}
                            {--no-sms : Skip sending SMS}
                            {--delay=5 : Delay between jobs in seconds}
                            {--dry-run : Show what would be processed without actually sending}';

    protected $description = 'Send credentials (email/SMS) to employees in bulk';

    protected BulkCredentialsService $credentialsService;

    public function __construct(BulkCredentialsService $credentialsService)
    {
        parent::__construct();
        $this->credentialsService = $credentialsService;
    }

    public function handle(): int
    {
        $sendEmail = !$this->option('no-email');
        $sendSms = !$this->option('no-sms');
        $delay = (int) $this->option('delay');
        $isDryRun = $this->option('dry-run');

        if (!$sendEmail && !$sendSms) {
            $this->error('Cannot skip both email and SMS. At least one must be enabled.');
            return 1;
        }

        $this->info('🚀 Starting bulk credentials sending process...');
        $this->line('');

        // Determine which users to process
        $users = $this->getUsersToProcess();

        if ($users->isEmpty()) {
            $this->warn('No users found matching the specified criteria.');
            return 0;
        }

        $this->displayJobSummary($users, $sendEmail, $sendSms, $delay, $isDryRun);

        if ($isDryRun) {
            $this->info('🔍 Dry run completed. No credentials were sent.');
            return 0;
        }

        if (!$this->confirm('Do you want to proceed with sending credentials?')) {
            $this->info('Operation cancelled.');
            return 0;
        }

        // Execute the bulk sending
        $result = $this->credentialsService->sendCredentialsToUsers(
            $users->pluck('id'),
            $sendEmail,
            $sendSms,
            $delay
        );

        $this->displayResults($result);

        return 0;
    }

    private function getUsersToProcess()
    {
        if ($role = $this->option('role')) {
            return User::with('roles')->whereHas('roles', function ($query) use ($role) {
                $query->where('name', $role);
            })->get();
        }

        if ($recent = $this->option('recent')) {
            return User::where('created_at', '>=', now()->subHours($recent))->get();
        }

        if ($emails = $this->option('emails')) {
            $emailList = array_map('trim', explode(',', $emails));
            return User::whereIn('email', $emailList)->get();
        }

        if ($userIds = $this->option('user-ids')) {
            $idList = array_map('trim', explode(',', $userIds));
            return User::whereIn('id', $idList)->get();
        }

        $this->error('Please specify one of: --role, --recent, --emails, or --user-ids');
        exit(1);
    }

    private function displayJobSummary($users, bool $sendEmail, bool $sendSms, int $delay, bool $isDryRun): void
    {
        $this->line('📋 <info>Job Summary:</info>');
        $this->line("   Users to process: <comment>{$users->count()}</comment>");
        $this->line("   Send Email: <comment>" . ($sendEmail ? 'Yes' : 'No') . "</comment>");
        $this->line("   Send SMS: <comment>" . ($sendSms ? 'Yes' : 'No') . "</comment>");
        $this->line("   Delay between jobs: <comment>{$delay} seconds</comment>");
        $this->line("   Dry run: <comment>" . ($isDryRun ? 'Yes' : 'No') . "</comment>");
        $this->line('');

        if ($users->count() <= 10) {
            $this->line('👥 <info>Users:</info>');
            foreach ($users as $user) {
                $roles = $user->roles->pluck('name')->implode(', ');
                $this->line("   • {$user->name} ({$user->email}) - Roles: {$roles}");
            }
            $this->line('');
        }

        $estimatedTime = ($users->count() * $delay) / 60;
        $this->line("⏱️  <info>Estimated completion time:</info> ~{$estimatedTime} minutes");
        $this->line('');
    }

    private function displayResults(array $result): void
    {
        $this->line('');
        $this->line('✅ <info>Bulk credentials sending completed!</info>');
        $this->line('');
        $this->line('📊 <info>Results:</info>');
        $this->line("   Total users: <comment>{$result['total']}</comment>");
        $this->line("   Successfully dispatched: <comment>{$result['dispatched']}</comment>");
        $this->line("   Failed to dispatch: <comment>{$result['failed']}</comment>");

        if ($result['failed'] > 0) {
            $this->line('');
            $this->warn('⚠️  Some jobs failed to dispatch. Check the logs for details.');
        }

        $this->line('');
        $this->info('🎯 Jobs have been queued. Monitor the queue worker for processing status.');
        $this->line('   You can check job status with: php artisan queue:work');
    }
}
