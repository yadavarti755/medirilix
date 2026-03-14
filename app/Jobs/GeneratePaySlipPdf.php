<?php

namespace App\Jobs;

use App\Models\DivisionPortal\Employee;
use App\Models\DivisionPortal\PaySlip;
use Carbon\Carbon;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class GeneratePaySlipPdf implements ShouldQueue
{
    use Queueable;

    public $payslip;

    /**
     * Create a new job instance.
     */
    public function __construct(PaySlip $payslip)
    {
        $this->payslip = $payslip;
    }


    /**
     * Execute the job.
     */
    public function handle(): void
    {
        Log::info('JOB: Generating payslip PDF for employee: ' . $this->payslip->employee_no);
        $employee = Employee::with('user')->where('employee_no', $this->payslip->employee_no)->first();
        if (!$employee) return;

        $year = Carbon::parse($this->payslip->ondate)->format('Y');
        $month = Carbon::parse($this->payslip->ondate)->format('m');

        $pdf = Pdf::loadView('pdfs.payslip', [
            'payslip' => $this->payslip,
            'employee' => $employee
        ]);

        $filePath = Config::get('file_paths')['GENERATED_PAY_SLIP_FILE_PATH'] . "/{$year}/{$month}/{$employee->employee_no}.pdf";
        Storage::disk('public')->put($filePath, $pdf->output());

        // Delete the payslip record
        // $this->payslip->delete();
        Log::info('JOB: Payslip PDF generated and saved for employee: ' . $this->payslip->employee_no);
    }
}
