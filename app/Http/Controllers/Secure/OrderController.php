<?php

namespace App\Http\Controllers\Secure;

use App\DTO\OrderStatusUpdateDto;
use App\Http\Controllers\Controller;
use App\Models\ReturnReasonsList;
use Illuminate\Http\Request;
use App\Services\OrderService;
use App\Services\OrderAddressService;
use App\Services\OrderProductListService;
use App\Services\OrderHistoryService;
use App\Services\DhlTrackingService;
use Yajra\Datatables\Datatables;
use App\Traits\AddressTrait;
use App\Traits\OrderShippingTrait;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Enums\OrderStatus;

class OrderController extends Controller
{
    use AddressTrait, OrderShippingTrait;

    protected $orderService;
    protected $addressService;
    protected $productListService;
    protected $historyService;
    protected $dhlService;

    public function __construct()
    {
        $this->orderService = new OrderService();
        $this->addressService = new OrderAddressService();
        $this->productListService = new OrderProductListService();
        $this->historyService = new OrderHistoryService();
        $this->dhlService = new DhlTrackingService();
    }

    public function userOrders()
    {
        $pageTitle = 'All Orders';
        $user_id = Auth::user()->id;
        $orders = $this->orderService->findForUser($user_id);
        return view('secure.orders.user_orders', compact('pageTitle', 'orders'));
    }

    public function adminOrders()
    {
        return view('secure.orders.orders')->with([
            'pageTitle' => 'All Orders'
        ]);;
    }

    public function fetchAllAdminOrdersForDatatable(Request $request)
    {
        $filters = [];

        if ($request->has('order_from_date') && !empty($request->order_from_date)) {
            $filters['date_from'] = $request->order_from_date;
        }
        if ($request->has('order_to_date') && !empty($request->order_to_date)) {
            $filters['date_to'] = $request->order_to_date;
        }

        if ($request->has('order_date') && !empty($request->order_date)) {
            $filters['order_date'] = date('Y-m-d', strtotime($request->order_date));
        }

        if ($request->has('order_number') && !empty($request->order_number)) {
            $filters['order_number'] = $request->order_number;
        }

        if ($request->has('order_status') && !empty($request->order_status)) {
            $filters['order_status'] = $request->order_status;
        }

        if ($request->has('payment_status') && !empty($request->payment_status)) {
            $filters['payment_status'] = $request->payment_status;
        }

        $data = $this->orderService->findForAdmin($filters);

        return Datatables::of($data)
            ->addColumn('action', function ($data) {
                // <button class='btn btn-primary btn-xs btn_change_status' id='" . Crypt::encryptString($data->id) . "' data-order-number='" . Crypt::encryptString($data->order_number) . "' title='Confirm Order'><i class='fas fa-paper-plane'></i> Status</button>
                $button = "<a href='" . route('orders.view', Crypt::encryptString($data->id)) . "' class='btn btn-warning btn-xs btn_view_order_details' id='" . Crypt::encryptString($data->id) . "' data-order-number='" . Crypt::encryptString($data->order_number) . "' title='View Order Details'><i class='fas fa-eye'></i></a>";
                return $button;
            })
            ->addColumn('order_status_desc', function ($data) {
                // $orderStatus = Config::get('constants.order_status')[$data->order_status];
                $statusEnum = $data->order_status instanceof OrderStatus
                    ? $data->order_status
                    : OrderStatus::tryFrom($data->order_status);

                $orderStatus = $statusEnum ? $statusEnum->label() : $data->order_status;
                $color = $statusEnum ? $statusEnum->color() : 'bg-secondary';

                $orderStatusBadge = "<span class='badge " . $color . "'>" . $orderStatus . "</span>";

                return $orderStatusBadge;
            })
            ->rawColumns(['order_status_desc', 'action'])
            ->make(true);
    }

    /**
     * `Summary of changeOrderStatus`
     * @param Request $request
     * @param mixed $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function changeOrderStatus(Request $request, $id)
    {
        $inputs = [
            'order_status' => 'required'
        ];

        $validator = Validator::make($request->all(), $inputs);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => implode(', ', $validator->errors()->all())
            ], 422);
        }

        try {
            $decryptedId = Crypt::decryptString($id);
            $order = $this->orderService->findById($decryptedId);

            if (!$order) {
                return response()->json([
                    'success' => false,
                    'message' => 'Order not found.'
                ], 404);
            }

            $dto = new OrderStatusUpdateDto(
                $order->order_number,
                $request->order_status,
                $request->remarks ?? null,
                Auth::user()->id
            );

            $this->orderService->changeOrderStatus($dto);

            return response()->json([
                'success' => true,
                'message' => 'Order status updated successfully.'
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Server failed: ' . $e->getMessage()
            ], 500);
        }
    }

    public function viewOrderDetails($id)
    {
        $decrypted_id = Crypt::decryptString($id);
        $order = $this->orderService->findById($decrypted_id);

        if (!$order) {
            abort(404);
        }

        $orderProductsList = $order->orderProductList;
        $orderAddress = $this->getOrderAddressUsingOrderNo($order->user_id, $order->order_number);
        $orderTrackingStatus = $order->orderHistory;

        // $shiprocketShipment = $this->orderService->getShipRocketDetails($order->order_number);

        $orderTracking = '';
        $trackingData = '';

        // DHL Tracking per product
        foreach ($orderProductsList as $item) {
            if ($item->shippingDetail && $item->shippingDetail->dhl_tracking_id) {
                $item->dhl_tracking_data = $this->dhlService->trackShipment($item->shippingDetail->dhl_tracking_id);
            }
        }

        $pageTitle = 'Order Details: ' . $order->order_number;
        return view('secure.orders.view_order_details', compact('order', 'orderProductsList', 'pageTitle', 'orderAddress', 'orderTrackingStatus', 'trackingData'));
    }

    public function viewUserOrderDetails()
    {
        $encrypted_id = request()->segment(4);
        $decrypted_id = Crypt::decryptString($encrypted_id);

        $order = $this->orderService->findById($decrypted_id);

        if (!$order || $order->user_id != Auth::user()->id) {
            abort(404);
        }

        // $orderProductsList = OrderProductList::where([
        //     'order_number' => $order->order_number,
        //     'user_id' => Auth::user()->id
        // ])->get();
        // Since we are using Order model with relations, we can use the relation. 
        // OrderProductList stores items for order.
        $orderProductsList = $order->orderProductList; // Assuming filtering by User ID in relation is not needed as Order Number is unique and belongs to user

        $orderAddress = $this->getOrderAddressUsingOrderNo(Auth::user()->id, $order->order_number);

        $orderTrackingStatus = $order->orderHistory;

        $return_reasons_lists = ReturnReasonsList::where('status', 1)->get(); // Should move to Service but small read.

        $pageTitle = 'Order Details: ' . $order->order_number;
        return view('user.orders.view_order_details', compact('order', 'orderProductsList', 'pageTitle', 'orderAddress', 'orderTrackingStatus', 'return_reasons_lists'));
    }

    public function monthwiseOrderAnalytics()
    {
        DB::statement("SET SQL_MODE=''");
        $data = DB::select("SELECT mt.month_name, mt.month_number, COUNT(c.id) AS cnt ,MONTH(STR_TO_DATE(c.order_date,'%Y-%m-%d')) AS registered_month 
        FROM month_table AS mt LEFT JOIN  orders AS c ON  YEAR(STR_TO_DATE(c.order_date,'%Y-%m-%d'))= '" . date('Y') . "' AND MONTH(STR_TO_DATE(c.order_date,'%Y-%m-%d')) = mt.month_number
        GROUP BY mt.month_number, mt.month_name ORDER BY mt.id ASC");
        // $query = DB::getQueryLog();
        // dd(end($query));
        return Response::json([
            'status' => true,
            'data' => $data
        ]);
    }

    public function cancelUserOrder(Request $request)
    {
        $inputs = [
            'order_id' => 'required',
            'cancel_reason' => 'required',
        ];

        $validator = Validator::make($request->all(), $inputs);

        if ($validator->fails()) {
            return Response::json([
                'status' => false,
                'message' => $validator->errors()->all()
            ]);
        }

        try {
            $decrypted_id = Crypt::decryptString($request->order_id);

            $this->orderService->cancelUserOrder(
                $decrypted_id,
                $request->cancel_reason,
                Auth::user()->id,
                Auth::user()->email
            );

            $message = 'Your order cancelled successfully.';
            if ($request->session()) {
                $request->session()->flash('message', $message);
            }

            return response()->json(['success' => true, 'message' => $message]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Server failed: ' . $e->getMessage()
            ]);
        }
    }

    public function returnUserOrder(Request $request)
    {
        $inputs = [
            'return_order_id' => 'required',
            'return_reason' => 'required',
        ];

        $validator = Validator::make($request->all(), $inputs);

        if ($validator->fails()) {
            return Response::json([
                'status' => false,
                'message' => $validator->errors()->all()
            ]);
        }

        if (count($request->product) < 1) {
            return Response::json([
                'status' => false,
                'message' => 'Please select products which you want to return.'
            ]);
        }

        try {
            $decrypted_id = Crypt::decryptString($request->return_order_id);

            $this->orderService->returnUserOrder(
                $decrypted_id,
                $request->product,
                $request->return_reason,
                $request->return_comment,
                Auth::user()->id,
                Auth::user()->email
            );

            $message = 'Your order return initiated successfully.';
            if ($request->session()) {
                $request->session()->flash('message', $message);
            }

            return response()->json(['success' => true, 'message' => $message]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Server failed: ' . $e->getMessage()
            ]);
        }
    }

    public function cancelReturnUserOrder(Request $request)
    {
        $inputs = [
            'order_number' => 'required',
            'product_id' => 'required',
        ];

        $validator = Validator::make($request->all(), $inputs);

        if ($validator->fails()) {
            return Response::json([
                'status' => false,
                'message' => $validator->errors()->all()
            ]);
        }

        try {
            $order_number = Crypt::decryptString($request->order_number);
            $product_id = Crypt::decryptString($request->product_id);

            $this->orderService->cancelReturnUserOrder(
                $order_number,
                $product_id,
                Auth::user()->id,
                Auth::user()->email
            );

            $message = 'Your order return cancelled successfully.';
            if ($request->session()) {
                $request->session()->flash('message', $message);
            }

            return response()->json(['success' => true, 'message' => $message]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Server failed: ' . $e->getMessage()
            ]);
        }
    }

    // public function adminReturnRequests()
    // {
    //     return view('secure.orders.return-requests')->with([
    //         'pageTitle' => 'Return Requests',
    //         'type' => 'RETURN_REQUESTS'
    //     ]);
    // }

    // public function adminReturnCancelled()
    // {
    //     return view('secure.orders.return-requests')->with([
    //         'pageTitle' => 'Return Cancelled',
    //         'type' => 'RETURN_CANCELLED'
    //     ]);
    // }

    // public function adminReturnApproved()
    // {
    //     return view('secure.orders.return-requests')->with([
    //         'pageTitle' => 'Return Approved',
    //         'type' => 'RETURN_APPROVED'
    //     ]);
    // }

    // public function getAllAdminReturnRequests(Request $request)
    // {
    //     $filters = [];

    //     if ($request->return_type == 'RETURN_REQUESTS') {
    //         $filters['return_status'] = 7;
    //     }
    //     if ($request->return_type == 'RETURN_CANCELLED') {
    //         $filters['return_status'] = 10;
    //     }
    //     if ($request->return_type == 'RETURN_APPROVED') {
    //         $filters['return_status'] = 8;
    //     }

    //     if ($request->has('return_from_date')) {
    //         $filters['date_from'] = $request->return_from_date;
    //     }
    //     if ($request->has('return_to_date')) {
    //         $filters['date_to'] = $request->return_to_date;
    //     }

    //     if ($request->has('return_date')) {
    //         $filters['return_date'] = date('Y-m-d', strtotime($request->return_date));
    //     }

    //     if ($request->has('order_number')) {
    //         $filters['order_number'] = $request->order_number;
    //     }

    //     $data = $this->orderService->getAllReturnRequests($filters);

    //     return Datatables::of($data)
    //         ->addColumn('action', function ($data) {
    //             // <button class='btn btn-primary btn-xs btn_change_status' id='" . Crypt::encryptString($data->id) . "' data-order-number='" . Crypt::encryptString($data->order_number) . "' title='Confirm Order'><i class='fas fa-paper-plane'></i> Status</button>
    //             if (request()->return_type == 'RETURN_REQUESTS') {
    //                 $view_url = url()->to('/admin/return-requests/view?type=' . customUrlEncode($data->return_status) . '&return_code=' . urlencode(Crypt::encryptString($data->return_code)) . '&order_number=' . urlencode(Crypt::encryptString($data->order_number)));
    //             }
    //             if (request()->return_type == 'RETURN_CANCELLED') {
    //                 $return_status = 10;
    //                 $view_url = url()->to('/admin/return-cancelled/view?type=' . customUrlEncode($data->return_status) . '&return_code=' . urlencode(Crypt::encryptString($data->return_code)) . '&order_number=' . urlencode(Crypt::encryptString($data->order_number)));
    //             }
    //             if (request()->return_type == 'RETURN_APPROVED') {
    //                 $return_status = 9;
    //                 $view_url = url()->to('/admin/return-approved/view?type=' . customUrlEncode($data->return_status) . '&return_code=' . urlencode(Crypt::encryptString($data->return_code)) . '&order_number=' . urlencode(Crypt::encryptString($data->order_number)));
    //             }

    //             $button = "<a href='" . $view_url . "' class='btn btn-warning btn-xs btn_view_order_details' id='" . Crypt::encryptString($data->return_code) . "' data-order-number='" . Crypt::encryptString($data->order_number) . "' title='View Return Request Details'><i class='fas fa-eye'></i> View</a>";
    //             return $button;
    //         })
    //         ->addColumn('customer_name', function ($data) {
    //             return ($data->user->name) ? $data->user->name : '';
    //         })
    //         ->addColumn('product_name', function ($data) {
    //             return ($data->order_product_list->product_name) ? $data->order_product_list->product_name : '';
    //         })
    //         ->addColumn('return_reason_desc', function ($data) {
    //             return ($data->return_request_list->text) ? $data->return_request_list->text : '';
    //         })
    //         ->addColumn('return_status_desc', function ($data) {
    //             return '<span class="badge badge-status ' . Config::get('constants.status_wise_bg_color')[$data->return_status] . '">' . Config::get('constants.order_status_text')[$data->return_status] . '</span>';
    //         })
    //         ->addColumn('product_featured_image', function ($data) {
    //             $product_featured_image = asset('storage/product_images/' . $data->order_product_list->product_featured_image);
    //             return $product_featured_image;
    //         })
    //         ->rawColumns(['return_status_desc', 'action', 'product_featured_image'])
    //         ->make(true);
    // }

    // public function viewReturnRequestDetails()
    // {
    //     $order_number = request()->get('order_number');
    //     $return_code = request()->get('return_code');

    //     $order_number = urldecode(Crypt::decryptString($order_number));
    //     $return_code = urldecode(Crypt::decryptString($return_code));

    //     $return_requests = \App\Models\ReturnRequest::where([
    //         'return_code' => $return_code,
    //     ])->get();
    //     // Should move checking to repository ideally but it's a simple query

    //     if ($return_requests->isNotEmpty()) {

    //         $order = $this->orderService->findByOrderNumber($order_number);

    //         $orderProductsList = $order->orderProductList()->where('user_id', $order->user_id)->get();

    //         $orderAddress = $this->getOrderAddressUsingOrderNo($order->user_id, $order->order_number);

    //         $pageTitle = 'Return details of order: ' . $order->order_number;
    //         return view('secure.orders.view-return-requests', compact('order', 'pageTitle', 'orderAddress', 'return_requests', 'orderProductsList'));
    //     } else {
    //         return redirect(route('secure.return-requests'));
    //     }
    // }

    // public function returnRequestOperation(Request $request)
    // {
    //     $inputs = [
    //         'hidden_order_number' => 'required',
    //         'hidden_return_code' => 'required',
    //         'rr_approval_status' => 'required',
    //         'length' => 'required',
    //         'breadth' => 'required',
    //         'height' => 'required',
    //         'weight' => 'required',
    //     ];

    //     if ($request->rr_approval_status && $request->rr_approval_status == 9) {
    //         $inputs['rr_remarks'] = 'required';
    //     }

    //     $validator = Validator::make($request->all(), $inputs);

    //     if ($validator->fails()) {
    //         return Response::json([
    //             'status' => false,
    //             'message' => $validator->errors()->all()
    //         ]);
    //     }

    //     if (!$request->return_id || count($request->return_id) < 1) {
    //         return Response::json([
    //             'status' => false,
    //             'message' => 'Please select products.'
    //         ]);
    //     }

    //     try {
    //         $orderNumber = Crypt::decryptString($request->hidden_order_number);
    //         $return_code = Crypt::decryptString($request->hidden_return_code);

    //         $additionalData = [];
    //         if ($request->rr_approval_status == 8) {
    //             $additionalData['box_data'] = [
    //                 'length' => $request->length,
    //                 'breadth' => $request->breadth,
    //                 'height' => $request->height,
    //                 'weight' => $request->weight,
    //             ];
    //         }

    //         $result = $this->orderService->approveReturnRequest(
    //             $orderNumber,
    //             $return_code,
    //             $request->return_id,
    //             $request->rr_approval_status,
    //             $request->rr_remarks,
    //             Auth::user()->username,
    //             $additionalData
    //         );

    //         return Response::json($result);
    //     } catch (\Exception $e) {
    //         return Response::json([
    //             'status' => false,
    //             'message' => 'Server failed: ' . $e->getMessage()
    //         ]);
    //     }
    // }
}
