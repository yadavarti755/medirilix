<?php

namespace App\Jobs;

# Start the queue worker
// php artisan queue:work --queue=emails,default --tries=3

use App\Services\EmailService;
use Exception;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class SendCertificateEmailJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $tries = 3; // Maximum retry attempts
    public $backoff = [60, 300, 900];

    protected $applicantData;
    protected $courseCode;
    protected $encodedUrl;

    /**
     * Create a new job instance.
     */
    public function __construct($applicantData, $courseCode, $encodedUrl)
    {
        $this->applicantData = $applicantData;
        $this->courseCode = $courseCode;
        $this->encodedUrl = $encodedUrl;
        $this->onQueue('emails'); // Use specific queue for emails
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        try {
            EmailService::sendCertificateIssuedEmail($this->applicantData, $this->courseCode, $this->encodedUrl);

            Log::info('Certificate email sent successfully', [
                'application_number' => $this->applicantData['application_number'],
                'email' => $this->applicantData['email_id']
            ]);
        } catch (Exception $e) {
            Log::error('Failed to send certificate email', [
                'application_number' => $this->applicantData['application_number'],
                'email' => $this->applicantData['email_id'],
                'error' => $e->getMessage(),
                'attempt' => $this->attempts()
            ]);

            throw $e; // Re-throw to trigger retry mechanism
        }
    }

    public function failed(Exception $exception)
    {
        // Log the final failure after all retries are exhausted
        // EmailFailureLog::create([
        //     'application_number' => $this->applicantData['application_number'],
        //     'email' => $this->applicantData['email_id'],
        //     'course_code' => $this->courseCode,
        //     'error_message' => $exception->getMessage(),
        //     'attempts' => $this->attempts(),
        //     'failed_at' => now()
        // ]);

        Log::error('Certificate email permanently failed', [
            'application_number' => $this->applicantData['application_number'],
            'email' => $this->applicantData['email_id'],
            'error' => $exception->getMessage(),
            'total_attempts' => $this->attempts()
        ]);
    }
}
