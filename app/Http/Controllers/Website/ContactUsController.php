<?php

namespace App\Http\Controllers\Website;

use App\DTO\ContactUsDto;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreContactUsRequest;
use App\Services\ContactDetailService;
use App\Services\ContactUsService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ContactUsController extends Controller
{
    protected $contactUsService;
    protected $contactDetailService;

    public function __construct()
    {
        $this->contactUsService = new ContactUsService();
        $this->contactDetailService = new ContactDetailService();
    }

    public function contactUs()
    {
        $pageTitle = 'Contact Us';
        $contactDetails = $this->contactDetailService->findAll();
        // Assuming we might want to filter or process these details for the view, 
        // but for now passing all of them. The view can decide which one is primary etc.
        // Actually, let's pass them as is.

        return view('website.contact-us', compact('pageTitle', 'contactDetails'));
    }

    public function submitContactUs(StoreContactUsRequest $request)
    {
        try {
            $contactUsDto = new ContactUsDto(
                strip_tags($request->input('name')),
                strip_tags($request->input('email_id')),
                strip_tags($request->input('phone_number')),
                strip_tags($request->input('message')),
                0,
                0,
                0
            );

            $result = $this->contactUsService->create($contactUsDto);

            if ($result) {
                try {
                    $adminEmail = config('app.admin_email');
                    if ($adminEmail) {
                        \Illuminate\Support\Facades\Mail::to($adminEmail)->send(new \App\Mail\ContactQueryAdminNotification($contactUsDto));
                    }
                } catch (\Exception $e) {
                    Log::error("Failed to send ContactQueryAdminNotification: " . $e->getMessage());
                }

                return response()->json([
                    'success' => true,
                    'status' => true,
                    'message' => 'Query sent successfully. We will respond you as soon as possible.'
                ]);
            }

            return response()->json([
                'success' => false,
                'status' => false,
                'message' => 'Server failed while processing request'
            ], 500);
        } catch (\Exception $e) {
            Log::error('Contact Us submission failed: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'status' => false,
                'message' => 'Server is not responding. Please try again.'
            ], 500);
        }
    }

    public function reloadCaptcha()
    {
        return response()->json(['captcha' => captcha_img()]);
    }
}
