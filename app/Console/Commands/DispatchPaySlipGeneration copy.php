<?php

namespace App\Console\Commands;

use App\Jobs\GeneratePaySlipPdf;
use App\Models\DivisionPortal\PaySlip;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class DispatchPaySlipGeneration extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:dispatch-pay-slip-generation';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Dispatch PDF generation jobs for payslips';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        Log::info('COMMAND: Dispatching payslip generation jobs...');
        // Fetch all payslips that are not deleted
        PaySlip::chunk(100, function ($payslips) {
            foreach ($payslips as $payslip) {
                GeneratePaySlipPdf::dispatch($payslip);
            }
        });
        Log::info('COMMAND: All payslip generation jobs dispatched.');
    }
}
