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
use Yajra\Datatables\Datatables;
use App\Traits\AddressTrait;
use App\Traits\OrderShippingTrait;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

use App\Services\ReturnReasonService;
use App\Services\CancelReasonService;

class UserOrderController extends Controller
{
    use AddressTrait, OrderShippingTrait;

    protected $orderService;
    protected $addressService;
    protected $productListService;
    protected $historyService;
    protected $returnReasonService;
    protected $cancelReasonService;

    public function __construct()
    {
        $this->orderService = new OrderService();
        $this->addressService = new OrderAddressService();
        $this->productListService = new OrderProductListService();
        $this->historyService = new OrderHistoryService();
        $this->returnReasonService = new ReturnReasonService();
        $this->cancelReasonService = new CancelReasonService();
    }

    public function userOrders()
    {
        $pageTitle = 'All Orders';
        $user_id = Auth::user()->id;
        $orders = $this->orderService->findForUser($user_id);
        return view('secure.orders.user_orders', compact('pageTitle', 'orders'));
    }


    public function viewUserOrderDetails($id)
    {
        $decrypted_id = Crypt::decryptString($id);
        $order = $this->orderService->findById($decrypted_id);

        if (!$order) {
            abort(404);
        }

        // Order Product List
        $orderProductsList = $order->orderProductList;
        // Order Address
        $orderAddress = $this->getOrderAddressUsingOrderNo($order->user_id, $order->order_number);
        // Order Tracking
        $orderTrackingStatus = $order->orderHistory;

        // Return Reasons
        $returnReasons = $this->returnReasonService->findAll();

        // Cancel Reasons
        $cancelReasons = $this->cancelReasonService->findAll();

        $pageTitle = 'Order Details: ' . $order->order_number;
        return view('secure.orders.view_user_order_details', compact('order', 'orderProductsList', 'pageTitle', 'orderAddress', 'orderTrackingStatus', 'returnReasons', 'cancelReasons'));
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
}
