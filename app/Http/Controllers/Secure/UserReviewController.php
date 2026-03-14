<?php

namespace App\Http\Controllers\Secure;

use App\DTO\CustomerReviewDto;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreCustomerReviewRequest;
use App\Services\CustomerReviewService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class UserReviewController extends Controller
{
    private $service;

    public function __construct()
    {
        $this->service = new CustomerReviewService();
    }

    public function store(StoreCustomerReviewRequest $request)
    {
        try {
            $dto = new CustomerReviewDto(
                user_id: Auth::id(),
                product_id: $request->product_id,
                message: $request->message,
                rating: $request->rating,
                images: $request->file('images'), // Pass the array of uploaded files
                created_by: Auth::id(),
                updated_by: Auth::id()
            );

            $this->service->create($dto);

            return redirect()->back()->with('success', 'Review submitted successfully!');
        } catch (\Exception $e) {
            Log::error("Review submission failed: " . $e->getMessage());
            return redirect()->back()->with('error', 'Failed to submit review: ' . $e->getMessage());
        }
    }
}
