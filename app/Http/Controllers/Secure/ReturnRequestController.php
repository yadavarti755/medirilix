<?php

namespace App\Http\Controllers\Secure;

use App\DTO\ReturnRequestDto;
use Illuminate\Http\Request;
use Yajra\Datatables\Datatables;
use App\Http\Controllers\Controller;
use App\Services\ReturnRequestService;
use App\Http\Requests\StoreReturnRequestRequest;
use App\Http\Requests\UpdateReturnRequestRequest;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Config;

class ReturnRequestController extends Controller
{
    protected $service;

    public function __construct()
    {
        $this->service = new ReturnRequestService();
    }

    public function index()
    {
        $pageTitle = "Return Requests";
        return view('secure.return_requests.index', compact('pageTitle'));
    }

    public function fetchForDatatable(Request $request)
    {
        if ($request->ajax()) {
            $data = $this->service->findAll();
            return Datatables::of($data)
                ->addColumn('user_name', function ($row) {
                    return $row->user ? $row->user->name : 'N/A';
                })
                ->addColumn('product_name', function ($row) {
                    return $row->orderProductList && $row->orderProductList->product ? $row->orderProductList->product->name : 'N/A';
                })
                ->addColumn('return_reason', function ($row) {
                    return $row->returnReason ? $row->returnReason->title : 'N/A';
                })
                ->addColumn('action', function ($row) {
                    $btn = "";
                    if ($row->return_status != Config::get('constants.order_status_codes.REFUND_COMPLETED')) {
                        $btn .= '<button type="button" data-id="' . $row->id . '" class="btn btn-sm btn-info btn-update-status" title="Update Status">Update Status</button>';
                    }
                    return $btn;
                })
                ->rawColumns(['action'])
                ->make(true);
        }
    }

    public function store(StoreReturnRequestRequest $request)
    {
        try {
            $dto = new ReturnRequestDto(
                auth()->id(),
                $request->input('order_product_list_id'),
                $request->input('return_list_id'),
                $request->input('return_description'),
                Config::get('constants.order_status_codes.RETURN_REQUESTED'),
                null,
                auth()->id(),
                null
            );

            $result = $this->service->create($dto);

            if (!$result) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error while submitting return request.',
                ], 500);
            }

            return response()->json([
                'success' => true,
                'message' => 'Return request submitted successfully!'
            ], 201);
        } catch (\Exception $e) {
            Log::error('Return Request Store failed: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Server failed while processing request: ' . $e->getMessage()
            ], 500);
        }
    }

    public function updateStatus(UpdateReturnRequestRequest $request, $id)
    {
        try {
            $status = $request->input('return_status');
            $pickupDetails = $request->input('return_pickup_details');
            $refundData = [
                'amount' => $request->input('refund_amount'),
                'remarks' => $request->input('remarks'),
            ];
            $result = $this->service->updateStatus($id, $status, $pickupDetails, auth()->id(), $refundData);

            if (!$result) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error while updating status.',
                ], 500);
            }

            return response()->json([
                'success' => true,
                'message' => 'Status updated successfully!'
            ], 200);
        } catch (\Exception $e) {
            Log::error('Status update failed: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Something went wrong!',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function fetchOne(Request $request, $id)
    {
        try {
            $data = $this->service->findById($id);
            return response()->json([
                'success' => true,
                'data' => $data
            ], 200);
        } catch (\Exception $e) {
            Log::error('Fetch failed: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Server failed while processing request'
            ], 500);
        }
    }
}
