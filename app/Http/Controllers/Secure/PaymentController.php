<?php

namespace App\Http\Controllers\Secure;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\PaymentService;
use App\Jobs\SendOrderEmailJob;

class PaymentController extends Controller
{
    protected $paymentService;

    public function __construct(PaymentService $paymentService)
    {
        $this->paymentService = $paymentService;
    }

    public function initiatePayment(Request $request)
    {
        // Validation: Request must have payment_method
        $request->validate([
            'payment_method' => 'required|string|exists:payment_gateways,code'
        ]);

        $cart = session()->get('cart', []);
        if (count($cart) < 1) {
            return redirect()->to('error-page')->with('error', 'Cart is empty');
        }
    }

    public function paymentResponse(Request $request, $gateway = null) {}

    public function orderPlaced()
    {
        $orderNumber = session()->get('order_number');
        if (!$orderNumber) {
            return redirect()->route('homepage'); // Redirect if no order in session
        }

        $pageTitle = 'Order Placed';
        $order = $this->paymentService->getOrderByNumber($orderNumber);
        session()->forget('cart');
        return view('website.order-placed', compact('order'))->with([
            'pageTitle' => $pageTitle,
            'orderNumber' => $orderNumber,
        ]);
    }

    public function orderFailed()
    {
        $orderNumber = session()->get('order_number');
        if (!$orderNumber) {
            return redirect()->route('homepage');
        }

        $pageTitle = 'Order Failed';
        $order = $this->paymentService->getOrderByNumber($orderNumber);

        if ($order) {
            try {
                $adminEmail = config('app.admin_email');
                SendOrderEmailJob::dispatch($order->user->email ?? $order->billing_email, new \App\Mail\OrderFailed($order))
                    ->onQueue('default');
                if ($adminEmail) {
                    SendOrderEmailJob::dispatch($adminEmail, new \App\Mail\OrderFailed($order))->onQueue('default');
                }
            } catch (\Exception $e) {
                \Illuminate\Support\Facades\Log::error("Failed to send OrderFailed email: " . $e->getMessage());
            }
        }

        // Cart::destroy(); // Usually kept in failed state?
        return view('website.order-failed', compact('order'))->with([
            'pageTitle' => $pageTitle,
            'orderNumber' => $orderNumber,
        ]);
    }
}
