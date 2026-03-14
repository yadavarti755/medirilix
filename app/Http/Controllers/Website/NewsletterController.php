<?php

namespace App\Http\Controllers\Website;

use Log;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreSubscribeNewsletterRequest;
use App\Services\SubscribeNewsletterService;
use App\DTO\SubscribeNewsletterDto;
use Illuminate\Http\Request;

class NewsletterController extends Controller
{
    protected $service;

    public function __construct()
    {
        $this->service = new SubscribeNewsletterService();
    }

    public function subscribe(StoreSubscribeNewsletterRequest $request)
    {
        try {
            $dto = new SubscribeNewsletterDto($request->newsletter_email_id);
            $result = $this->service->create($dto);

            if ($result) {
                return response()->json([
                    'status' => true,
                    'message' => 'Thank you for subscribing to our newsletter!'
                ]);
            }

            return response()->json([
                'status' => false,
                'message' => 'Something went wrong. Please try again later.'
            ]);
        } catch (\Exception $e) {
            Log::error('Newsletter Subscribe Error: ' . $e->getMessage());
            return response()->json([
                'status' => false,
                'message' => 'Something went wrong. Please try again later.'
            ]);
        }
    }
}
