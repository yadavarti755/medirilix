<?php

namespace App\Console\Commands;

use App\Jobs\GeneratePaySlipPdf;
use App\Models\DivisionPortal\PaySlip;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class DispatchPaySlipGeneration extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:dispatch-pay-slip-generation 
                            {--month= : The month (1-12) for which to generate payslips}
                            {--year= : The year for which to generate payslips}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Dispatch PDF generation jobs for payslips for current or specified month/year';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        // Get month and year from options or use current month/year
        $month = $this->option('month') ?? now()->month;
        $year = $this->option('year') ?? now()->year;

        // Validate month
        if ($month < 1 || $month > 12) {
            $this->error('Month must be between 1 and 12');
            return Command::FAILURE;
        }

        // Validate year
        if ($year < 1900 || $year > now()->year + 10) {
            $this->error('Please provide a valid year');
            return Command::FAILURE;
        }

        $this->info("Dispatching payslip generation jobs for {$month}/{$year}...");
        Log::info("COMMAND: Dispatching payslip generation jobs for month: {$month}, year: {$year}");

        // Create start and end dates for the specified month/year
        $startDate = Carbon::create($year, $month, 1)->startOfMonth();
        $endDate = Carbon::create($year, $month, 1)->endOfMonth();

        $count = 0;

        // Fetch payslips for the specified month/year that are not deleted
        PaySlip::whereYear('ondate', $year)
            ->whereMonth('ondate', $month)
            ->chunk(100, function ($payslips) use (&$count) {
                foreach ($payslips as $payslip) {
                    GeneratePaySlipPdf::dispatch($payslip);
                    $count++;
                }
            });

        if ($count > 0) {
            $this->info("Successfully dispatched {$count} payslip generation jobs for {$month}/{$year}");
            Log::info("COMMAND: Successfully dispatched {$count} payslip generation jobs for month: {$month}, year: {$year}");
        } else {
            $this->warn("No payslips found for {$month}/{$year}");
            Log::info("COMMAND: No payslips found for month: {$month}, year: {$year}");
        }

        return Command::SUCCESS;
    }
}
