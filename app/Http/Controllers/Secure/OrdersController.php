<?php

namespace App\Http\Controllers\Secure;

use App\Http\Controllers\Controller;
use App\Mail\AdminOrderCancelled;
use App\Mail\OrderCancelled;
use App\Mail\OrderDelivered;
use App\Mail\OrderReturn;
use App\Mail\OrderShipped;

use App\Models\Order;
use App\Models\Product;
use App\Models\Wishlist;
use App\Models\OrderHistory;
use App\Traits\AddressTrait;
use Illuminate\Http\Request;
use App\Models\OrderProductList;
use App\Models\ReturnReasonsList;
use App\Models\ReturnRequest;
use App\Models\ShipRocketOrderShippingDetails;
use App\Models\User;
use Yajra\Datatables\Datatables;
use App\Traits\OrderShippingTrait;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Response;
use App\Enums\OrderStatus;
use App\Jobs\SendOrderEmailJob;

class OrdersController extends Controller
{
    use AddressTrait;
    use OrderShippingTrait;

    public function userOrders()
    {
        $pageTitle = 'All Orders';

        $orders = Order::where([
            'user_id' => Auth::user()->user_id,
            'status' => 1
        ])->whereNotIn('order_status', [0])->orderBy('id', 'DESC')->paginate(10);

        $all_products = Product::where('status', 1)->orderBy('id', 'DESC')->limit(8)->get();

        $userWishlists = Wishlist::where([
            'user_id' => Auth::user()->user_id,
            'status' => 1
        ])->get()->toArray();

        $userWishlistIDs = array_column($userWishlists, 'product_id');

        return view('user.orders.orders', compact('orders', 'userWishlistIDs', 'all_products', 'pageTitle'));
    }

    public function adminAllOrders()
    {
        return view('admin.orders.all-orders')->with([
            'orderType' => 'ALL',
            'pageTitle' => 'All Orders'
        ]);;
    }

    public function adminNewOrders()
    {
        return view('admin.orders.all-orders')->with([
            'orderType' => 'NEW',
            'pageTitle' => 'New Orders'
        ]);
    }

    public function adminProcessingOrders()
    {
        return view('admin.orders.all-orders')->with([
            'orderType' => 'PROCESSING',
            'pageTitle' => 'Processing Orders'
        ]);
    }

    public function adminShippedOrders()
    {
        return view('admin.orders.all-orders')->with([
            'orderType' => 'SHIPPED',
            'pageTitle' => 'Shipped Orders'
        ]);
    }

    public function adminDeliveredOrders()
    {
        return view('admin.orders.all-orders')->with([
            'orderType' => 'DELIVERED',
            'pageTitle' => 'Delivered Orders'
        ]);
    }

    public function adminCancelledOrders()
    {
        return view('admin.orders.all-orders')->with([
            'orderType' => 'CANCELLED',
            'pageTitle' => 'Cancelled Orders'
        ]);
    }

    public function getAllAdminOrders(Request $request)
    {
        $where = [
            'status' => 1
        ];
        if ($request->order_type == 'NEW') {
            $where['order_status'] = OrderStatus::PLACED->value;
        }

        if ($request->order_type == 'PROCESSING') {
            $where['order_status'] = OrderStatus::PROCESSING->value;
        }

        if ($request->order_type == 'SHIPPED') {
            $where['order_status'] = OrderStatus::SHIPPED->value;
        }

        if ($request->order_type == 'DELIVERED') {
            $where['order_status'] = OrderStatus::DELIVERED->value;
        }

        if ($request->order_type == 'CANCELLED') {
            $where['order_status'] = OrderStatus::CANCELLED->value;
        }

        $query = Order::query();


        if (request('order_from_date') && request('order_to_date')) {
            $query->when(request('order_from_date'), function ($q) {
                $order_from_date = Carbon::parse(request()->order_from_date)->toDateTimeString();
                $order_to_date = Carbon::parse(request()->order_to_date)->toDateTimeString();

                return $q->whereBetween('order_date', [$order_from_date, $order_to_date]);
            });
        }

        if (request('order_from_date')) {
            $query->when(request('order_from_date'), function ($q) {
                $order_from_date = Carbon::parse(request()->order_from_date)->toDateTimeString();
                return $q->whereBetween('order_date', [$order_from_date, date('Y-m-d H:i:s')]);
            });
        }


        $query->when(request('order_date'), function ($q) {
            $order_date = date('Y-m-d', strtotime(request('order_date')));
            return $q->where('order_date', 'LIKE', '%' . $order_date . '%');
        });
        $query->when(request('order_number'), function ($q) {
            return $q->where('order_number', 'LIKE', '%' . request('order_number') . '%');
        });

        $data = $query->where($where)->orderBy('id', 'desc')->get();

        // $data = Order::where($where)->orderBy('id', 'desc')->get();

        return Datatables::of($data)
            ->addColumn('action', function ($data) {
                // <button class='btn btn-primary btn-xs btn_change_status' id='" . Crypt::encryptString($data->id) . "' data-order-number='" . Crypt::encryptString($data->order_number) . "' title='Confirm Order'><i class='fas fa-paper-plane'></i> Status</button>
                $button = "<a href='" . url()->to('/admin/orders/view/' . Crypt::encryptString($data->id)) . "' class='btn btn-warning btn-xs btn_view_order_details' id='" . Crypt::encryptString($data->id) . "' data-order-number='" . Crypt::encryptString($data->order_number) . "' title='View Order Details'><i class='fas fa-eye'></i> View</a>";
                return $button;
            })
            ->addColumn('txn_id', function ($order) {
                return (isset($order->transaction->txn_id)) ? $order->transaction->txn_id : '';
            })
            ->addColumn('order_status_desc', function ($data) {
                // $orderStatus = Config::get('constants.order_status')[$data->order_status];
                $orderStatus = $data->order_status instanceof OrderStatus ? $data->order_status->label() : $data->order_status;
                $color = $data->order_status instanceof OrderStatus ? $data->order_status->color() : 'bg-secondary';

                $orderStatusBadge = "<span class='badge " . $color . "'>" . $orderStatus . "</span>";
                if ($data->order_status == 'CONFIRMED') {
                    $orderStatusBadge = "<span class='badge badge-info'>" . $orderStatus . "</span>";
                }
                if ($data->order_status == 'PLACED') {
                    $orderStatusBadge = "<span class='badge badge-primary'>" . $orderStatus . "</span>";
                }
                if ($data->order_status == 'DISPATCHED') {
                    $orderStatusBadge = "<span class='badge badge-warning'>" . $orderStatus . "</span>";
                }
                if ($data->order_status == 'DELIVERED') {
                    $orderStatusBadge = "<span class='badge badge-success'>" . $orderStatus . "</span>";
                }
                if ($data->order_status == 'CANCEL') {
                    $orderStatusBadge = "<span class='badge badge-danger'>" . $orderStatus . "</span>";
                }
                return $orderStatusBadge;
            })
            ->rawColumns(['order_status_desc', 'action'])
            ->make(true);
    }

    public function changeOrderStatus(Request $request)
    {
        DB::beginTransaction();
        $inputs = [
            'hidden_order_number' => 'required',
            'order_status' => 'required'
        ];

        if ($request->order_status && $request->order_status == 3) {
            $inputs['length'] = 'required';
            $inputs['breadth'] = 'required';
            $inputs['height'] = 'required';
            $inputs['weight'] = 'required';
        }

        $validator = Validator::make($request->all(), $inputs);

        if ($validator->fails()) {
            return Response::json([
                'status' => false,
                'message' => $validator->errors()->all()
            ]);
        } else {
            $remarks = '';
            $orderNumber = Crypt::decryptString($request->hidden_order_number);

            $orderDetails = Order::where('order_number', $orderNumber)->first();

            $cancel_reason = '';
            if ($request->order_status == 5) {
                if (!$request->cancel_reason) {
                    return Response::json([
                        'status' => false,
                        'message' => 'Cancel reason is required if you want to cancel the order.'
                    ]);
                }
                $cancel_reason = $request->cancel_reason;
            }
            $result = Order::where('order_number', $orderNumber)->update([
                'order_status' => $request->order_status,
                'order_status_changed_date' => now(),
                'cancel_reason' => $cancel_reason,
                'updated_by' => Auth::user()->username,
                'updated_at' => now()
            ]);

            if (!$result) {
                DB::rollBack();
                return Response::json([
                    'status' => false,
                    'message' => 'Server is not responding. Please try again.'
                ]);
            }

            // Update product status
            $opl_result = OrderProductList::where(
                'order_number',
                $orderNumber
            )->update([
                'product_order_status' => $request->order_status,
                'status_changed_date' => now(),
                'updated_by' => Auth::user()->username,
                'updated_at' => now()
            ]);

            if (!$opl_result) {
                DB::rollBack();
                return Response::json([
                    'status' => false,
                    'message' => 'Error while saving product status. Please try again.'
                ]);
            }

            // Insert data into order history
            $result = OrderHistory::create([
                'user_id' => $orderDetails->user_id,
                'order_number' => $orderNumber,
                'order_status' => $request->order_status,
                'status_changed_date' => now(),
                'remarks' => ($request->order_status == 5) ? $request->cancel_reason : '',
                'created_by' => Auth::user()->username,
                'created_at' => now(),
                'updated_by' => Auth::user()->username,
                'updated_at' => now()
            ]);

            // Send the order details to ship rocket panel
            if ($request->order_status == 3) {
                $orderBoxData = [
                    'length' => $request->length,
                    'breadth' => $request->breadth,
                    'height' => $request->height,
                    'weight' => $request->weight,
                ];
                $spResult = $this->pushOrderToShipRocket($orderNumber, $orderBoxData);

                if ($spResult['status'] == true) {
                    DB::commit();

                    // If order shipped by admin, then send mail to user
                    // User details
                    $user = User::where(
                        'user_id',
                        $orderDetails->user_id
                    )->first();

                    $mailable = new OrderShipped($orderNumber, $orderDetails, $user);
                    SendOrderEmailJob::dispatch($user['email'], $mailable);

                    return Response::json([
                        'status' => true,
                        'message' => 'Order status changed successfully. And order pushed to shiprocket panel.'
                    ]);
                } else {
                    DB::rollBack();
                    return Response::json([
                        'status' => false,
                        'message' => $result['msg']
                    ]);
                }
            }

            if ($result == true) {
                DB::commit();

                // If order cancelled by admin then send mail to user
                if ($request->order_status == 5) {
                    // User details
                    $user = User::where('user_id', $orderDetails->user_id)->first();

                    $mailable = new AdminOrderCancelled($orderNumber, $orderDetails);
                    SendOrderEmailJob::dispatch($user->email, $mailable);
                }

                // If order delivered, then send mail to user
                if ($request->order_status == 4) {
                    // User details
                    $user = User::where('user_id', $orderDetails->user_id)->first();

                    $mailable = new OrderDelivered($orderNumber, $orderDetails, $user);
                    SendOrderEmailJob::dispatch($user['email'], $mailable);
                }

                return Response::json([
                    'status' => true,
                    'message' => 'Order status changed successfully.'
                ]);
            } else {
                DB::rollBack();
                return Response::json([
                    'status' => false,
                    'message' => 'Server failed to respond, please try again.'
                ]);
            }
        }
    }

    public function viewOrderDetails()
    {
        $encrypted_id = request()->segment(4);
        $decrypted_id = Crypt::decryptString($encrypted_id);
        $order = Order::find($decrypted_id);
        $orderProductsList = OrderProductList::where('order_number', $order->order_number)->get();
        $orderAddress = $this->getOrderAddressUsingOrderNo($order->user_id, $order->order_number);
        $orderTrackingStatus = OrderHistory::where([
            'order_number' => $order->order_number,
        ])->get();

        $shiprocketShipment =  ShipRocketOrderShippingDetails::where('ag_order_number', $order->order_number)->orderBy('id', 'DESC')->first();

        $orderTracking = '';
        $trackingData = '';

        if ($shiprocketShipment) {
            $orderTracking = $this->trackShipRocketOrder($shiprocketShipment->shipment_id);
            $orderTracking = json_decode(json_encode($orderTracking), true);

            if ($orderTracking['tracking_data']['track_status'] != 0) {
                $trackingData = $orderTracking['tracking_data'];
            }
        }

        $pageTitle = 'Order Details: ' . $order->order_number;
        return view('admin.orders.view_order_details', compact('order', 'orderProductsList', 'pageTitle', 'orderAddress', 'orderTrackingStatus', 'trackingData'));
    }

    public function viewUserOrderDetails()
    {
        $encrypted_id = request()->segment(4);
        $decrypted_id = Crypt::decryptString($encrypted_id);
        $order = Order::where([
            'id' => $decrypted_id,
            'user_id' => Auth::user()->user_id
        ])->first();

        $orderProductsList = OrderProductList::where([
            'order_number' => $order->order_number,
            'user_id' => Auth::user()->user_id
        ])->get();

        $orderAddress = $this->getOrderAddressUsingOrderNo(Auth::user()->user_id, $order->order_number);
        $orderTrackingStatus = OrderHistory::where([
            'order_number' => $order->order_number,
        ])->get();

        $shiprocketShipment =  ShipRocketOrderShippingDetails::where('ag_order_number', $order->order_number)->orderBy('id', 'DESC')->first();

        $orderTracking = '';
        $trackingData = '';

        if ($shiprocketShipment) {
            $orderTracking = $this->trackShipRocketOrder($shiprocketShipment->shipment_id);
            $orderTracking = json_decode(json_encode($orderTracking), true);

            if ($orderTracking['tracking_data']['track_status'] != 0) {
                $trackingData = $orderTracking['tracking_data'];
            }
        }

        $return_reasons_lists = ReturnReasonsList::where(['status' => 1])->get();

        $pageTitle = 'Order Details: ' . $order->order_number;
        return view('user.orders.view_order_details', compact('order', 'orderProductsList', 'pageTitle', 'orderAddress', 'orderTrackingStatus', 'trackingData', 'return_reasons_lists'));
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
        DB::beginTransaction();
        $inputs = [
            'order_id' => 'required',
            'cancel_reason' => 'required',
        ];

        $validator = Validator::make($request->all(), $inputs);

        if ($validator->fails()) {
            DB::rollBack();
            return Response::json([
                'status' => false,
                'message' => $validator->errors()->all()
            ]);
        }

        $decrypted_id = Crypt::decryptString($request->order_id);

        $order = Order::where([
            'id' => $decrypted_id,
            'user_id' => Auth::user()->user_id
        ])->first();

        if (!$order) {
            DB::rollBack();
            return Response::json([
                'status' => false,
                'message' => 'Invalid order id provided.'
            ]);
        }

        if ($order['order_status'] != 1) {
            DB::rollBack();
            return Response::json([
                'status' => false,
                'message' => 'You can not cancel your order at this time. Please contact sales team for this, if you still wants to cancel the order'
            ]);
        }

        $result = Order::where([
            'id' => $decrypted_id,
            'user_id' => Auth::user()->user_id,
        ])->update([
            'order_status' => OrderStatus::CANCELLED->value,
            'order_status_changed_date' => Carbon::now(),
            'cancel_reason' => $request->cancel_reason
        ]);

        if ($result) {

            $oplResult = OrderProductList::where([
                'order_number' => $order->order_number,
                'user_id' => Auth::user()->user_id,
            ])->update([
                'product_order_status' => OrderStatus::CANCELLED->value,
                'status_changed_date' => Carbon::now(),
                'updated_at' => now()
            ]);

            $orderHistoryResult = OrderHistory::create([
                'user_id' => Auth::user()->user_id,
                'order_number' => $order['order_number'],
                'order_status' => $order['order_status'],
                'status_changed_date' => $order['order_status_changed_date'],
                'remarks' => $request->cancel_reason,
                'created_by' => Auth::user()->email,
                'updated_by' => Auth::user()->email
            ]);

            if (!$oplResult || !$orderHistoryResult) {
                DB::rollBack();
                return Response::json([
                    'status' => false,
                    'message' => 'Server failed. Please try again.'
                ]);
            }

            // Send order cancellation mail
            SendOrderEmailJob::dispatch(Auth::user()->email, new OrderCancelled($order['order_number'], $order));

            $request->session()->flash('message', 'Your order with order number ' . $order['order_number'] . ' cancelled successfully.');

            DB::commit();
            return Response::json([
                'status' => true,
                'message' => 'Your order cancelled successfully.'
            ]);
        }

        DB::rollBack();
        return Response::json([
            'status' => false,
            'message' => 'Server failed. Please try again.'
        ]);
    }

    public function returnUserOrder(Request $request)
    {
        DB::beginTransaction();
        $inputs = [
            'return_order_id' => 'required',
            'return_reason' => 'required',
        ];

        $validator = Validator::make($request->all(), $inputs);

        if ($validator->fails()) {
            DB::rollBack();
            return Response::json([
                'status' => false,
                'message' => $validator->errors()->all()
            ]);
        }

        if (count($request->product) < 1) {
            DB::rollBack();
            return Response::json([
                'status' => false,
                'message' => 'Please select products which you want to return.'
            ]);
        }

        // Decrypt the order id
        $decrypted_id = Crypt::decryptString($request->return_order_id);

        $order = Order::where([
            'id' => $decrypted_id,
            'user_id' => Auth::user()->user_id
        ])->first();

        if (check_return_date_validity($order->order_status_changed_date) == true) {
            DB::rollBack();
            return Response::json([
                'status' => false,
                'message' => 'Return date expired. You can not return order at this stage.'
            ]);
        }

        if (!$order) {
            DB::rollBack();
            return Response::json([
                'status' => false,
                'message' => 'Invalid order id provided.'
            ]);
        }

        if ($order['order_status'] != 4) {
            DB::rollBack();
            return Response::json([
                'status' => false,
                'message' => 'You can not return order at this time. Please contact sales team for this, if you still wants to do this.'
            ]);
        }

        // dd($request->all());

        // Set result to false first.
        $result = false;
        $return_code = generateReturnCode();

        foreach ($request->product as $product) {

            // Check if the return request is generated for any item or not
            $rr_result = ReturnRequest::where([
                'user_id' => Auth::user()->user_id,
                'order_number' => $order->order_number,
                'return_status' => OrderStatus::RETURN_REQUESTED->value
            ])->first();

            if ($rr_result) {
                $return_code = $rr_result->return_code;
            } else {
                $return_code = $return_code;
            }

            $result = ReturnRequest::create([
                'user_id' => Auth::user()->user_id,
                'order_number' => $order->order_number,
                'return_code' => $return_code,
                'product_id' => $product,
                'return_status' => OrderStatus::RETURN_REQUESTED->value,
                'return_reason' => $request->return_reason,
                'return_date' => Carbon::now(),
                'comment' => $request->return_comment,
                'created_by' => Auth::user()->email,
                'updated_by' => Auth::user()->email
            ]);

            $result2 = OrderProductList::where([
                'product_id' => $product,
                'user_id' => Auth::user()->user_id,
                'order_number' => $order->order_number,
            ])->update([
                'product_order_status' => OrderStatus::RETURN_REQUESTED->value,
                'status_changed_date' => now(),
            ]);
        }


        if ($result && $result2) {

            $orderHistoryResult = OrderHistory::create([
                'user_id' => Auth::user()->user_id,
                'order_number' => $order['order_number'],
                'order_status' => OrderStatus::RETURN_REQUESTED->value,
                'status_changed_date' => Carbon::now(),
                'remarks' => $request->return_comment,
                'created_by' => Auth::user()->email,
                'updated_by' => Auth::user()->email
            ]);

            if (!$orderHistoryResult) {
                DB::rollBack();
                return Response::json([
                    'status' => false,
                    'message' => 'Server failed. Please try again.'
                ]);
            }

            // Send order cancellation mail
            SendOrderEmailJob::dispatch(Auth::user()->email, new OrderReturn($order['order_number'], $order));

            $request->session()->flash('message', 'Return with order number ' . $order['order_number'] . ' has initiated successfully.');

            DB::commit();
            return Response::json([
                'status' => true,
                'message' => 'Your order return initiated successfully.'
            ]);
        }

        DB::rollBack();
        return Response::json([
            'status' => false,
            'message' => 'Server failed. Please try again.'
        ]);
    }

    public function cancelReturnUserOrder(Request $request)
    {
        DB::beginTransaction();
        $inputs = [
            'order_number' => 'required',
            'product_id' => 'required',
        ];

        $validator = Validator::make($request->all(), $inputs);

        if ($validator->fails()) {
            DB::rollBack();
            return Response::json([
                'status' => false,
                'message' => $validator->errors()->all()
            ]);
        }

        // Decrypt the order id
        $order_number = Crypt::decryptString($request->order_number);
        $product_id = Crypt::decryptString($request->product_id);

        $result = ReturnRequest::where([
            'order_number' => $order_number,
            'user_id' => Auth::user()->user_id,
            'return_status' => Config::get('constants.order_status_codes')['RETURN_REQUESTED'],
            'product_id' => $product_id
        ])->update([
            'return_status' => OrderStatus::RETURN_CANCELLED->value,
            'created_by' => Auth::user()->email,
            'updated_by' => Auth::user()->email
        ]);

        $result2 = OrderProductList::where([
            'product_id' => $product_id,
            'user_id' => Auth::user()->user_id,
            'order_number' => $order_number,
        ])->update([
            'product_order_status' => OrderStatus::RETURN_CANCELLED->value,
            'status_changed_date' => now(),
        ]);


        if ($result && $result2) {

            $orderHistoryResult = OrderHistory::create([
                'user_id' => Auth::user()->user_id,
                'order_number' => $order_number,
                'order_status' => OrderStatus::RETURN_CANCELLED->value,
                'status_changed_date' => Carbon::now(),
                'remarks' => $request->return_comment,
                'created_by' => Auth::user()->email,
                'updated_by' => Auth::user()->email
            ]);

            if (!$orderHistoryResult) {
                DB::rollBack();
                return Response::json([
                    'status' => false,
                    'message' => 'Server failed. Please try again.'
                ]);
            }

            $request->session()->flash('message', 'Return with order number ' . $order_number . ' has been cancelled successfully.');

            DB::commit();
            return Response::json([
                'status' => true,
                'message' => 'Your order return cancelled successfully.'
            ]);
        }

        DB::rollBack();
        return Response::json([
            'status' => false,
            'message' => 'Server failed. Please try again.'
        ]);
    }

    public function adminReturnRequests()
    {
        return view('admin.orders.return-requests')->with([
            'pageTitle' => 'Return Requests',
            'type' => 'RETURN_REQUESTS'
        ]);
    }

    public function adminReturnCancelled()
    {
        return view('admin.orders.return-requests')->with([
            'pageTitle' => 'Return Cancelled',
            'type' => 'RETURN_CANCELLED'
        ]);
    }

    public function adminReturnApproved()
    {
        return view('admin.orders.return-requests')->with([
            'pageTitle' => 'Return Approved',
            'type' => 'RETURN_APPROVED'
        ]);
    }

    public function getAllAdminReturnRequests(Request $request)
    {
        if ($request->return_type == 'RETURN_REQUESTS') {
            $return_status = 7;
        }
        if ($request->return_type == 'RETURN_CANCELLED') {
            $return_status = 10;
        }
        if ($request->return_type == 'RETURN_APPROVED') {
            $return_status = 8;
        }
        $where = [
            'return_status' => $return_status,
            'status' => 1
        ];

        $query = ReturnRequest::query();


        if (request('return_from_date') && request('return_to_date')) {
            $query->when(request('return_from_date'), function ($q) {
                $return_from_date = Carbon::parse(request()->return_from_date)->toDateTimeString();
                $return_to_date = Carbon::parse(request()->return_to_date)->toDateTimeString();

                return $q->whereBetween('return_date', [$return_from_date, $return_to_date]);
            });
        }

        if (request('return_from_date')) {
            $query->when(request('return_from_date'), function ($q) {
                $return_from_date = Carbon::parse(request()->return_from_date)->toDateTimeString();
                return $q->whereBetween('return_date', [$return_from_date, date('Y-m-d H:i:s')]);
            });
        }

        $query->when(request('return_date'), function ($q) {
            $return_date = date('Y-m-d', strtotime(request('return_date')));
            return $q->where('return_date', 'LIKE', '%' . $return_date . '%');
        });
        $query->when(request('order_number'), function ($q) {
            return $q->where('order_number', 'LIKE', '%' . request('order_number') . '%');
        });

        $data = $query->where($where)->orderBy('id', 'desc')->get();

        // $data = Order::where($where)->orderBy('id', 'desc')->get();

        return Datatables::of($data)
            ->addColumn('action', function ($data) {
                // <button class='btn btn-primary btn-xs btn_change_status' id='" . Crypt::encryptString($data->id) . "' data-order-number='" . Crypt::encryptString($data->order_number) . "' title='Confirm Order'><i class='fas fa-paper-plane'></i> Status</button>
                if (request()->return_type == 'RETURN_REQUESTS') {
                    $view_url = url()->to('/admin/return-requests/view?type=' . customUrlEncode($data->return_status) . '&return_code=' . urlencode(Crypt::encryptString($data->return_code)) . '&order_number=' . urlencode(Crypt::encryptString($data->order_number)));
                }
                if (request()->return_type == 'RETURN_CANCELLED') {
                    $return_status = 10;
                    $view_url = url()->to('/admin/return-cancelled/view?type=' . customUrlEncode($data->return_status) . '&return_code=' . urlencode(Crypt::encryptString($data->return_code)) . '&order_number=' . urlencode(Crypt::encryptString($data->order_number)));
                }
                if (request()->return_type == 'RETURN_APPROVED') {
                    $return_status = 9;
                    $view_url = url()->to('/admin/return-approved/view?type=' . customUrlEncode($data->return_status) . '&return_code=' . urlencode(Crypt::encryptString($data->return_code)) . '&order_number=' . urlencode(Crypt::encryptString($data->order_number)));
                }

                $button = "<a href='" . $view_url . "' class='btn btn-warning btn-xs btn_view_order_details' id='" . Crypt::encryptString($data->return_code) . "' data-order-number='" . Crypt::encryptString($data->order_number) . "' title='View Return Request Details'><i class='fas fa-eye'></i> View</a>";
                return $button;
            })
            ->addColumn('customer_name', function ($data) {
                return ($data->user->name) ? $data->user->name : '';
            })
            ->addColumn('product_name', function ($data) {
                return ($data->order_product_list->product_name) ? $data->order_product_list->product_name : '';
            })
            ->addColumn('return_reason_desc', function ($data) {
                return ($data->return_request_list->text) ? $data->return_request_list->text : '';
            })
            ->addColumn('return_status_desc', function ($data) {
                return '<span class="badge badge-status ' . Config::get('constants.status_wise_bg_color')[$data->return_status] . '">' . Config::get('constants.order_status_text')[$data->return_status] . '</span>';
            })
            ->addColumn('product_featured_image', function ($data) {
                $product_featured_image = asset('storage/product_images/' . $data->order_product_list->product_featured_image);
                return $product_featured_image;
            })
            ->rawColumns(['return_status_desc', 'action', 'product_featured_image'])
            ->make(true);
    }

    public function viewReturnRequestDetails()
    {
        $order_number = request()->get('order_number');
        $return_code = request()->get('return_code');

        $order_number = urldecode(Crypt::decryptString($order_number));
        $return_code = urldecode(Crypt::decryptString($return_code));

        $return_requests = ReturnRequest::where([
            'return_code' => $return_code,
        ])->get();


        if ($return_requests) {

            $order = Order::where([
                'order_number' => $order_number
            ])->first();

            $orderProductsList = OrderProductList::where([
                'order_number' => $order->order_number,
                'user_id' => $order->user_id
            ])->get();

            $orderAddress = $this->getOrderAddressUsingOrderNo($order->user_id, $order->order_number);

            $pageTitle = 'Return details of order: ' . $order->order_number;
            return view('admin.orders.view-return-requests', compact('order', 'pageTitle', 'orderAddress', 'return_requests', 'orderProductsList'));
        } else {
            return redirect(route('admin.return-requests'));
        }
    }

    public function returnRequestOperation(Request $request)
    {
        DB::beginTransaction();
        $inputs = [
            'hidden_order_number' => 'required',
            'hidden_return_code' => 'required',
            'rr_approval_status' => 'required',
            'length' => 'required',
            'breadth' => 'required',
            'height' => 'required',
            'weight' => 'required',
        ];

        if ($request->rr_approval_status && $request->rr_approval_status == 9) {
            $inputs['rr_remarks'] = 'required';
        }

        $validator = Validator::make($request->all(), $inputs);

        if ($validator->fails()) {
            return Response::json([
                'status' => false,
                'message' => $validator->errors()->all()
            ]);
        } else {

            if (!$request->return_id || count($request->return_id) < 1) {
                return Response::json([
                    'status' => false,
                    'message' => 'Please select products.'
                ]);
            }


            $orderNumber = Crypt::decryptString($request->hidden_order_number);
            $return_code = Crypt::decryptString($request->hidden_return_code);

            $orderDetails = Order::where('order_number', $orderNumber)->first();
            $return_requests = ReturnRequest::where('return_code', $return_code)->get();

            // Update the each request id status
            foreach ($request->return_id as $rid) {

                // Get the return requests
                $return_request = ReturnRequest::where([
                    'id' => $rid,
                ])->first();

                // Update the return request status
                $rr_result = ReturnRequest::where([
                    'id' => $rid,
                ])->update([
                    'return_status' => $request->rr_approval_status,
                    'ag_remarks' => $request->rr_remarks
                ]);

                // Update the order product status
                $opl_result = OrderProductList::where([
                    'order_number' => $orderNumber,
                    'product_id' => $return_request->product_id
                ])->update([
                    'product_order_status' => $request->rr_approval_status,
                ]);

                if (!$rr_result || !$opl_result) {
                    DB::rollBack();
                    return Response::json([
                        'status' => false,
                        'message' => 'Something went wrong while updating status. Please try again.'
                    ]);
                }
            }


            // Insert data into order history
            OrderHistory::create([
                'user_id' => $orderDetails->user_id,
                'order_number' => $orderNumber,
                'order_status' => $request->rr_approval_status,
                'status_changed_date' => now(),
                'remarks' => $request->rr_remarks,
                'created_by' => Auth::user()->username,
                'created_at' => now(),
                'updated_by' => Auth::user()->username,
                'updated_at' => now()
            ]);

            // Send the return order details to ship rocket panel
            if ($request->rr_approval_status == 8) {
                $orderBoxData = [
                    'length' => $request->length,
                    'breadth' => $request->breadth,
                    'height' => $request->height,
                    'weight' => $request->weight,
                ];

                $spResult = $this->pushReturnRequestOrderToShipRocket($return_requests, $orderNumber, $orderBoxData);

                if ($spResult['status'] == true) {
                    DB::commit();

                    // User details
                    $user = User::where(
                        'user_id',
                        $orderDetails->user_id
                    )->first();

                    // Mail::to($user['email'])->send(new OrderShipped($orderNumber, $orderDetails, $user));

                    return Response::json([
                        'status' => true,
                        'message' => 'Return status changed successfully. And return order pushed to shiprocket panel.'
                    ]);
                } else {
                    DB::rollBack();
                    return Response::json([
                        'status' => false,
                        'message' => $spResult['message']
                    ]);
                }
            }

            $result = true;

            if ($result == true) {
                DB::commit();

                return Response::json([
                    'status' => true,
                    'message' => 'Return order status changed successfully.'
                ]);
            } else {
                DB::rollBack();
                return Response::json([
                    'status' => false,
                    'message' => 'Server failed to respond, please try again.'
                ]);
            }
        }
    }
}
