<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class SendOrderEmailJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $recipient;
    protected $mailable;

    /**
     * The number of times the job may be attempted.
     *
     * @var int
     */
    public $tries;

    /**
     * The number of seconds the job can run before timing out.
     *
     * @var int
     */
    public $timeout;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($recipient, $mailable)
    {
        $this->recipient = $recipient;
        $this->mailable = $mailable;
        $this->tries = env('MAIL_JOB_TRIES', 3);
        $this->timeout = env('MAIL_JOB_TIMEOUT', 30);
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        try {
            Mail::to($this->recipient)->send($this->mailable);
        } catch (\Exception $e) {
            Log::error('Failed to send order email via queue: ' . $e->getMessage());
            $this->fail($e);
        }
    }

    /**
     * Get the number of seconds the job depends on the queue connection.
     *
     * @return int
     */
    public function backoff()
    {
        return [30, 60, 120];
    }
}
