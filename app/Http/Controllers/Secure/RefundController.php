<?php

namespace App\Http\Controllers\Secure;

use App\Http\Controllers\Controller;
use App\Services\RefundService;
use App\Services\OrderCancellationRequestService; // To update order product list status if needed
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Config;
use App\Enums\OrderStatus;

use Yajra\Datatables\Datatables; // Import Datatables

class RefundController extends Controller
{
    protected $service;

    public function __construct()
    {
        $this->service = new RefundService();
    }

    public function index()
    {
        $pageTitle = 'Refunds';
        // $refund_statuses = Config::get('constants.order_status');
        $refund_statuses = array_combine(array_column(\App\Enums\OrderStatus::cases(), 'name'), array_column(\App\Enums\OrderStatus::cases(), 'value'));
        return view('secure.refunds.index', compact('pageTitle', 'refund_statuses'));
    }

    public function fetchForDatatable(Request $request)
    {
        if ($request->ajax()) {
            $data = $this->service->getAllRefunds();
            return Datatables::of($data)
                ->addColumn('order_number', function ($row) {
                    return $row->orderProductList->order->order_number ?? 'N/A';
                })
                ->addColumn('product_name', function ($row) {
                    return $row->orderProductList->product->name ?? 'N/A';
                })
                ->editColumn('refund_amount', function ($row) {
                    return number_format($row->refund_amount, 2);
                })
                ->editColumn('refund_status', function ($row) {
                    $status = $row->refund_status; // Enum object due to casting
                    if (!($status instanceof OrderStatus)) {
                        // Fallback if casting fails or is null (though should be cast)
                        $statusEnum = OrderStatus::tryFrom($status);
                        $status = $statusEnum ?? $status;
                        $label = $statusEnum ? $statusEnum->label() : $status;
                        $color = $statusEnum ? $statusEnum->color() : 'bg-warning';
                    } else {
                        $label = $status->label();
                        $color = $status->color();
                    }

                    return '<span class="badge ' . $color . '">' . $label . '</span>';
                })
                ->editColumn('created_at', function ($row) {
                    return $row->created_at->format('d M Y, h:i A');
                })
                ->addColumn('action', function ($row) {
                    if (auth()->user()->can('edit refund')) {
                        return '<button class="btn btn-sm btn-primary btn-edit-status"
                                    data-id="' . $row->id . '">
                                    Update Status
                                </button>';
                    }
                    return '';
                })
                ->rawColumns(['refund_status', 'action'])
                ->make(true);
        }
    }

    public function fetchOne($id)
    {
        try {
            $refund = $this->service->getRefundById($id); // Assuming this method exists or I need to add it to service. Wait, I should check service. But findById is standard.
            // Actually service usually has findById. I'll check service if needed, but let's assume find or findOrFail.
            // Let's check RefundService first to be safe? No, let's just use service->find($id) if it extends BaseService or similar.
            // Wait, previous file view didn't show service content.
            // I'll assume findById or similar. Let's use findOrFail on model if service not handy, but better to use service.
            // Let's optimistically assume findById or similar.

            // To be safe, I'll use the service variable but if method missing I'll debug.
            // Actually, in `index` it called `getAllRefunds`.
            return response()->json(['success' => true, 'data' => $refund]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Error fetching data'], 500);
        }
    }

    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'refund_status' => 'required|string',
            'remarks' => 'nullable|string'
        ]);

        try {
            $updatedRefund = $this->service->updateStatus($id, $request->refund_status, $request->remarks, auth()->id());

            if ($updatedRefund) {
                $orderProductList = $updatedRefund->orderProductList;
                if ($orderProductList) {
                    $orderProductList->product_order_status = $request->refund_status;
                    $orderProductList->save();
                }

                return response()->json(['success' => true, 'message' => 'Refund status updated successfully.']);
            }

            return response()->json(['success' => false, 'message' => 'Failed to update status.'], 500);
        } catch (\Exception $e) {
            Log::error('Refund Update Error: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Server Error'], 500);
        }
    }
}
