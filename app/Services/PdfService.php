<?php

namespace App\Services;

use Str;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\Log;

class PdfService
{
    public static function generateApplicationPDF($application, $course, $salutation)
    {
        // Initialize mPDF
        $mpdf = new \Mpdf\Mpdf([
            'format' => 'A4',
            'margin_left' => 15,
            'margin_right' => 15,
            'margin_top' => 16,
            'margin_bottom' => 16,
            'margin_header' => 9,
            'margin_footer' => 9
        ]);

        // Set document properties
        $mpdf->SetTitle('Application Form - ' . $application->application_number);
        $mpdf->SetAuthor('DHTI');
        $mpdf->SetSubject('Training Application Form');

        // Generate PDF content
        $html = view('pdfs.dhti_application', compact(
            'application',
            'course',
            'salutation'
        ))->render();

        $mpdf->WriteHTML($html);

        // Generate filename
        $filename = 'DHTI_Application_' . $application->application_number . '.pdf';

        // Return PDF in browser (inline display)
        return response($mpdf->Output($filename, 'S'), 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'inline; filename="' . $filename . '"',
            'Cache-Control' => 'no-cache, no-store, must-revalidate',
            'Pragma' => 'no-cache',
            'Expires' => '0'
        ]);
    }
}
