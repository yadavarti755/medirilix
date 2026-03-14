<?php

namespace App\Http\Controllers\Secure;

use App\Http\Controllers\Controller;
use App\Services\CustomerReviewService;
use Illuminate\Http\Request;
use Yajra\Datatables\Datatables;
use Illuminate\Support\Facades\Log;

class CustomerReviewController extends Controller
{
    protected $service;

    public function __construct()
    {
        $this->service = new CustomerReviewService();
    }

    public function index()
    {
        $pageTitle = "Customer Reviews";
        return view('secure.customer_reviews.index', compact('pageTitle'));
    }

    public function fetchForDatatable(Request $request)
    {
        if ($request->ajax()) {
            $reviews = $this->service->findAll();
            return Datatables::of($reviews)
                ->addColumn('product_name', function ($review) {
                    return $review->product ? $review->product->name : 'N/A';
                })
                ->addColumn('user_name', function ($review) {
                    return $review->user ? $review->user->name : 'N/A';
                })
                ->addColumn('rating', function ($review) {
                    return $review->rating . ' Stars';
                })
                ->addColumn('status', function ($review) {
                    return $review->is_active ? '<span class="badge bg-success">Active</span>' : '<span class="badge bg-danger">Inactive</span>';
                })
                ->addColumn('action', function ($review) {
                    $button = '';
                    // Add permission checks if needed
                    $button .= '<button class="btn btn-sm btn-danger btn-delete" data-id="' . $review->id . '" title="Delete"><i class="fa fa-trash"></i></button>';

                    // Toggle Status Button
                    $statusBtnClass = $review->is_active ? 'btn-secondary' : 'btn-success';
                    $statusBtnText = $review->is_active ? 'Deactivate' : 'Activate';
                    $button .= ' <button class="btn btn-sm ' . $statusBtnClass . ' btn-toggle-status" data-id="' . $review->id . '" data-status="' . !$review->is_active . '" title="Toggle Status">' . $statusBtnText . '</button>';

                    return $button;
                })
                ->rawColumns(['status', 'action'])
                ->make(true);
        }
    }

    public function destroy($id)
    {
        try {
            $result = $this->service->delete($id);
            if (!$result) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error while deleting record.',
                ], 500);
            }
            return response()->json(['success' => true, 'message' => 'Record moved to trash successfully!']);
        } catch (\Exception $e) {
            Log::error('CustomerReview: Deletion failed: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Something went wrong!'], 500);
        }
    }

    public function updateStatus(Request $request, $id)
    {
        try {
            $result = $this->service->update(['is_active' => $request->status], $id);
            if (!$result) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error while updating status.',
                ], 500);
            }
            return response()->json(['success' => true, 'message' => 'Status updated successfully!']);
        } catch (\Exception $e) {
            Log::error('CustomerReview: Status update failed: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Something went wrong!'], 500);
        }
    }
}
