<?php

namespace App\Http\Controllers\Secure;

use App\Http\Controllers\Controller;
use App\Services\OrderCancellationRequestService;
use App\DTO\OrderCancellationRequestDto;
use App\DTO\OrderCancellationRequestMessageDto;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Yajra\Datatables\Datatables;
use Illuminate\Support\Facades\Crypt;

class OrderCancellationRequestController extends Controller
{
    protected $service;

    public function __construct()
    {
        $this->service = new OrderCancellationRequestService();
    }

    public function index()
    {
        $pageTitle = "Order Cancellation Requests";
        return view('secure.order_cancellation_requests.index', compact('pageTitle'));
    }

    public function fetchForDatatable(Request $request)
    {
        if ($request->ajax()) {
            $data = $this->service->findAll();
            return Datatables::of($data)
                ->addColumn('order_number', function ($row) {
                    return $row->orderProductList && $row->orderProductList->order ? $row->orderProductList->order->order_number : 'N/A';
                })
                ->addColumn('order_date', function ($row) {
                    return $row->orderProductList && $row->orderProductList->order ? \Carbon\Carbon::parse($row->orderProductList->order->order_date)->format('d M, Y') : 'N/A';
                })
                ->addColumn('product_name', function ($row) {
                    return $row->orderProductList && $row->orderProductList->product ? $row->orderProductList->product->name : 'N/A';
                })
                ->addColumn('product_order_status', function ($row) {
                    return $row->orderProductList ? $row->orderProductList->product_order_status : 'N/A';
                })
                ->addColumn('cancellation_reason', function ($row) {
                    return $row->cancelReason ? $row->cancelReason->title : 'N/A';
                })
                ->addColumn('status', function ($row) {
                    return $row->status;
                })
                ->addColumn('action', function ($row) {
                    if ($row->orderProductList && $row->orderProductList->order) {
                        $url = route('orders.view', Crypt::encryptString($row->orderProductList->order->id));
                        $btn = '<a href="' . $url . '" class="btn btn-sm btn-info" title="View Order"><i class="ti ti-eye"></i> View</a>';
                        return $btn;
                    }
                    return '';
                })
                ->rawColumns(['action'])
                ->make(true);
        }
    }

    public function store(Request $request)
    {
        $request->validate([
            'order_product_list_id' => 'required|exists:order_product_lists,id',
            'cancel_reason_id' => 'required|exists:cancel_reasons,id',
            'description' => 'required|string',
        ]);

        try {
            $dto = new OrderCancellationRequestDto(
                $request->order_product_list_id,
                auth()->user()->id,
                $request->cancel_reason_id,
                $request->description,
                'Pending',
                null,
                auth()->user()->id,
                auth()->user()->id
            );

            $result = $this->service->processCancellationRequest($dto);

            if ($result) {
                // If direct cancellation returns object with message
                $msg = isset($result->message) ? $result->message : 'Cancellation request submitted successfully.';
                return response()->json(['success' => true, 'message' => $msg]);
            }
            return response()->json(['success' => false, 'message' => 'Failed to submit request.'], 500);
        } catch (\Exception $e) {
            Log::error('Cancellation Request Error: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => $e->getMessage()], 400); // 400 Bad Request for validation/logic errors
        }
    }

    public function storeMessage(Request $request)
    {
        $request->validate([
            'order_cancellation_request_id' => 'required|exists:order_cancellation_requests,id',
            'message' => 'required|string',
        ]);

        try {
            $dto = new OrderCancellationRequestMessageDto(
                $request->order_cancellation_request_id,
                auth()->user()->id,
                $request->message
            );

            $result = $this->service->createMessage($dto);

            if ($result) {
                // Return the new message HTML to append dynamically
                $html = '<div class="d-flex justify-content-end mb-2">
                            <div class="bg-primary text-white p-2 rounded-3" style="max-width: 75%;">
                                <p class="mb-0 small">' . $result->message . '</p>
                                <small class="text-white-50" style="font-size: 0.7rem;">Just Not</small>
                            </div>
                        </div>';
                return response()->json(['success' => true, 'message' => 'Message sent.', 'html' => $html]);
            }
            return response()->json(['success' => false, 'message' => 'Failed to send message.'], 500);
        } catch (\Exception $e) {
            Log::error('Message Error: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Server Error'], 500);
        }
    }

    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|string' // Pending, Closed
        ]);

        try {
            $result = $this->service->updateStatus($id, $request->status, auth()->user()->id);
            if ($result) {
                return response()->json(['success' => true, 'message' => 'Status updated successfully.']);
            }
            return response()->json(['success' => false, 'message' => 'Failed to update status.'], 500);
        } catch (\Exception $e) {
            Log::error('Status Update Error: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Server Error'], 500);
        }
    }
}
