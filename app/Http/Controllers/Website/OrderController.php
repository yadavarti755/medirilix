<?php

namespace App\Http\Controllers\Website;

use App\DTO\OrderDto;
use Illuminate\Support\Facades\Auth;

use App\Http\Controllers\Controller;

use App\Services\AddressService;
use App\Services\OrderService;
use App\Services\PaymentGatewayService;
use App\Http\Requests\Website\InitiatePaymentRequest;
use Illuminate\Http\Request;
use App\Enums\OrderStatus;
use App\Enums\PaymentStatus;
use App\Services\PaymentService;

class OrderController extends Controller
{
    protected $addressService;
    protected $orderService;
    protected $paymentService;
    protected $paymentGatewayService;

    // Construct
    public function __construct(
        AddressService $addressService,
        OrderService $orderService,
        PaymentService $paymentService,
        PaymentGatewayService $paymentGatewayService
    ) {
        $this->addressService = $addressService;
        $this->orderService = $orderService;
        $this->paymentService = $paymentService;
        $this->paymentGatewayService = $paymentGatewayService;
    }

    public function orderSummary()
    {
        // dd(session()->all());
        $cart = session()->get('cart', []);
        if (count($cart) < 1) {
            return redirect('/cart');
        }

        if (!session()->has('shipping_address')) {
            return redirect('/checkout');
        }

        $address = $this->addressService->getSelectedAddress(Auth::id(), session()->get('shipping_address')['address_id']);
        $gateways = $this->paymentGatewayService->findActive();

        return view('website.order-summary', compact('address', 'gateways'))->with(['pageTitle' => 'Order Summary']);
    }

    public function initiatePayment(InitiatePaymentRequest $request)
    {
        // Validation handled by InitiatePaymentRequest

        $gateway = $this->paymentGatewayService->findById($request->payment_gateway);
        // dd($gateway);

        // 1. Calculate Total Amount
        $totals = getCartTotals();
        $cart = $totals['cart_items'] ?? session()->get('cart', []); // Fallback just in case, though totals should have it

        if (count($cart) < 1) {
            return redirect('/cart');
        }

        $subtotal = $totals['subtotal'];
        $discount = $totals['discount'];
        $tax = $totals['tax'];
        $shipping = $totals['shipping'];
        $total = $totals['total'];

        $couponCode = null;
        $couponData = null;
        if (!empty($totals['coupon'])) {
            $couponCode = $totals['coupon']['code'];
            $couponData = $totals['coupon']; // Store basic discount info or look up full coupon if needed.
            // We can store the array as json. OrderService handles json_encoding.
        }


        // 2. Prepare Order DTO
        $user = Auth::user();
        $orderNumber = 'ORD-' . strtoupper(uniqid());

        $orderDto = new OrderDto(
            $orderNumber,
            $user->id,
            OrderStatus::PENDING->value, // Order Status
            now(), // Order Date
            $total, // Total Price
            $tax, // Tax
            $shipping, // Shipping
            $total, // Grand Total
            $gateway->gateway_name, // Payment Method
            PaymentStatus::PENDING->value, // Payment Status
            null, // Cancel Reason
            now(), // Status Changed Date
            $gateway->gateway_name, // Payment Type
            $subtotal, // Subtotal
            $user->id, // Created By
            $user->id, // Updated By
            $discount, // Discount Amount
            $couponCode, // Coupon Code
            $couponData, // Coupon Data
            $tax, // Tax Amount
            $shipping // Shipping Charges
        );

        $shippingAddress = null;
        if (session()->has('shipping_address')) {
            $shippingAddressSession = session()->get('shipping_address');
            $shippingAddress = $this->addressService->getSelectedAddress($user->id, $shippingAddressSession['address_id']);
        }

        // 3. Create Order via Service
        $order = $this->orderService->createOrderWithDetails($orderDto, $cart, $shippingAddress);

        if (!$order) {
            return redirect()->back()->with('error', 'Failed to create order.');
        }

        // Store Order Number in Session for Success/Failure Pages
        session()->put('order_number', $order->order_number);

        // 4. Initiate Payment via PaymentService
        $paymentResult = $this->paymentService->processPayment($order, $gateway);
        if ($paymentResult['status']) {
            if ($paymentResult['action'] == 'redirect') {
                return redirect()->away($paymentResult['url']);
            } elseif ($paymentResult['action'] == 'view') {
                return view($paymentResult['view'], $paymentResult['data']);
            }
        }
        // dd($paymentResult);

        return redirect()->back()->with('error', $paymentResult['message'] ?? 'Payment initiation failed.');
    }

    public function verifyPayPal(Request $request)
    {
        if (!$request->has('token')) {
            return redirect()->route('order.failed')->with('error', 'Invalid response from PayPal.');
        }

        $paypalOrderId = session()->get('paypal_order_id');
        $localOrderId = session()->get('current_order_id');

        $result = $this->paymentService->verifyPayPal($paypalOrderId);

        if ($result['status']) {
            $this->orderService->updatePaymentStatus($localOrderId, OrderStatus::PLACED->value, PaymentStatus::COMPLETED->value, $result['transaction_id']);
            $this->clearSession();
            return redirect()->route('order.placed'); // Assuming order.success or order.placed exists, let me verify order.placed first
        } else {
            $this->orderService->updatePaymentStatus($localOrderId, OrderStatus::FAILED->value, PaymentStatus::FAILED->value);
            return redirect()->route('order.failed')->with('error', $result['message']);
        }
    }

    public function verifyRazorpay(Request $request)
    {
        $localOrderId = session()->get('current_order_id');
        $expectedRazorpayOrderId = session()->get('razorpay_order_id');

        \Illuminate\Support\Facades\Log::info("Razorpay Callback - Initial Session: localOrderId={$localOrderId}, razorpayOrderId={$expectedRazorpayOrderId}");

        // Recovery if session is lost (Modern browser Cookie/Session issues on cross-site POST)
        if (!$localOrderId || !$expectedRazorpayOrderId) {
            \Illuminate\Support\Facades\Log::warning("Razorpay Callback - Session Lost. Attempting Recovery...");
            try {
                $razorpayOrderId = $request->razorpay_order_id;
                $razorpayOrder = $this->paymentService->fetchRazorpayOrder($razorpayOrderId);
                $orderNumber = $razorpayOrder['receipt']; // We store order_number in 'receipt'

                \Illuminate\Support\Facades\Log::info("Razorpay Callback - Fetched Razorpay Order: ID={$razorpayOrderId}, Receipt={$orderNumber}");

                $order = $this->orderService->findByOrderNumber($orderNumber);

                if ($order) {
                    $localOrderId = $order->id;
                    $expectedRazorpayOrderId = $razorpayOrderId;
                    session()->put('current_order_id', $localOrderId);
                    session()->put('razorpay_order_id', $expectedRazorpayOrderId);
                    \Illuminate\Support\Facades\Log::info("Razorpay Callback - Order Recovered: LocalID={$localOrderId}");
                } else {
                    \Illuminate\Support\Facades\Log::error("Razorpay Callback - Order not found for Receipt: {$orderNumber}");
                }
            } catch (\Exception $e) {
                \Illuminate\Support\Facades\Log::error("Razorpay Callback - Order recovery failed: " . $e->getMessage());
            }
        }

        $attributes = [
            'razorpay_order_id' => $request->razorpay_order_id,
            'razorpay_payment_id' => $request->razorpay_payment_id,
            'razorpay_signature' => $request->razorpay_signature
        ];

        $result = $this->paymentService->verifyRazorpay($attributes, $expectedRazorpayOrderId);

        if ($result['status']) {
            if ($localOrderId) {
                $this->orderService->updatePaymentStatus($localOrderId, OrderStatus::PLACED->value, PaymentStatus::COMPLETED->value, $result['transaction_id']);
                $this->clearSession();
                return redirect()->route('order.placed');
            } else {
                \Illuminate\Support\Facades\Log::error("Razorpay Callback - Verification Success but Local Order ID still NULL.");
                return redirect()->route('order.failed')->with('error', 'Order context lost. Please contact support.');
            }
        } else {
            if ($localOrderId) {
                $this->orderService->updatePaymentStatus($localOrderId, OrderStatus::FAILED->value, PaymentStatus::FAILED->value);
            }
            return redirect()->route('order.failed')->with('error', $result['message']);
        }
    }

    public function cancelRazorpay()
    {
        $this->cancelOrder('Payment cancelled by user.');
        return redirect()->route('order.failed')->with('error', 'Payment cancelled by user.');
    }

    public function cancelPayPal()
    {
        $this->cancelOrder('Payment cancelled by user.');
        return redirect()->route('order.failed')->with('error', 'Payment cancelled by user.');
    }

    private function cancelOrder($reason)
    {
        $localOrderId = session()->get('current_order_id');
        if ($localOrderId) {
            $this->orderService->updatePaymentStatus($localOrderId, OrderStatus::CANCELLED->value, PaymentStatus::FAILED->value);
        }
    }

    private function clearSession()
    {
        session()->forget('cart');
        session()->forget('shipping_address');
        session()->forget('current_order_id');
        session()->forget('paypal_order_id');
        session()->forget('razorpay_order_id');
    }
}
