<?php

namespace App\Http\Controllers\Website;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Validator;
use App\Services\ProductService;
use App\Services\CouponService;
use App\Services\CouponUsageService;
use App\Models\Coupon;
use App\Models\Product;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class CartController extends Controller
{
    protected ProductService $productService;
    protected CouponService $couponService;
    protected CouponUsageService $couponUsageService;

    public function __construct()
    {
        $this->productService = new ProductService();
        $this->couponService = new CouponService();
        $this->couponUsageService = new CouponUsageService();
    }

    public function cart()
    {

        return view('website.cart')->with(['pageTitle' => 'Cart']);
    }

    /* =======================
     | CART SESSION HANDLERS
     ======================= */

    private function getCart(): array
    {
        return session()->get('cart', []);
    }

    private function saveCart(array $cart): void
    {
        session()->put('cart', $cart);
    }

    /* =======================
     | ADD TO CART
     ======================= */

    public function addToCart(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'product_id' => 'required|numeric',
            'quantity'   => 'required|numeric|min:1',
            'size'       => 'nullable|string',
            'color'      => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return Response::json([
                'status'  => false,
                'message' => $validator->errors()->first()
            ]);
        }

        $product = $this->productService->findById($request->product_id);

        if (!$product) {
            return Response::json(['status' => false, 'message' => 'Product not found.']);
        }

        if ($product->stock_availability != 1) {
            return Response::json(['status' => false, 'message' => 'Product is out of stock.']);
        }

        // Check if product is published/active if the field exists
        if (isset($product->is_published) && $product->is_published != 1) {
            return Response::json(['status' => false, 'message' => 'Product is not available.']);
        }

        if ($product->available_quantity < $request->quantity) {
            return Response::json([
                'status'  => false,
                'message' => "Only {$product->available_quantity} quantity available."
            ]);
        }

        $cart = $this->getCart();

        // Unique cart key per variant
        $uniqueId = $product->id . '-' . ($request->size ?? 'na') . '-' . ($request->color ?? 'na');

        if (isset($cart[$uniqueId])) {
            $newQty = $cart[$uniqueId]['qty'] + $request->quantity;

            if ($product->available_quantity < $newQty) {
                return Response::json([
                    'status'  => false,
                    'message' => "Only {$product->available_quantity} quantity available."
                ]);
            }

            $cart[$uniqueId]['qty'] = $newQty;
        } else {
            $cart[$uniqueId] = [
                'product_id' => $product->id,
                'name'       => $product->name,
                'price'      => $product->selling_price,
                'qty'        => $request->quantity,
                'image'      => $product->featured_image_full_path,
                'attributes' => [
                    'sku'           => $product->product_sku,
                    'size'          => $request->size,
                    'color'         => $request->color,
                    'category_name' => $product->category->name ?? '',
                    'product_slug'  => $product->slug,
                ],
            ];
        }

        $this->saveCart($cart);

        return Response::json([
            'status'     => true,
            'message'    => 'Product added to cart.',
            'cart_count' => count($cart),
            'totals'     => $this->calculateTotals(),
        ]);
    }

    /* =======================
     | UPDATE QUANTITY
     ======================= */

    public function updateQuantity(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'unique_id' => 'required|string',
            'operation' => 'required|in:PLUS,MINUS',
        ]);

        if ($validator->fails()) {
            return Response::json([
                'status'  => false,
                'message' => $validator->errors()->first()
            ]);
        }

        $cart = $this->getCart();

        if (!isset($cart[$request->unique_id])) {
            return Response::json(['status' => false, 'message' => 'Cart item not found.']);
        }

        $item = $cart[$request->unique_id];
        $product = $this->productService->findById($item['product_id']);

        if (!$product) {
            return Response::json(['status' => false, 'message' => 'Product not found.']);
        }

        $qty = $item['qty'];

        if ($request->operation === 'MINUS') {
            if ($qty > 1) {
                $cart[$request->unique_id]['qty'] = $qty - 1;
            }
        } else {
            if ($product->available_quantity < ($qty + 1)) {
                return Response::json([
                    'status'  => false,
                    'message' => "Only {$product->available_quantity} quantity available."
                ]);
            }

            $cart[$request->unique_id]['qty'] = $qty + 1;
        }

        $this->saveCart($cart);

        return Response::json([
            'status'  => true,
            'message' => 'Cart updated.',
            'totals'  => $this->calculateTotals(),
        ]);
    }

    /* =======================
     | REMOVE CART ITEM
     ======================= */

    public function removeCartItem(Request $request)
    {
        $cart = $this->getCart();

        if (!isset($cart[$request->unique_id])) {
            return Response::json(['status' => false, 'message' => 'Cart item not found.']);
        }

        unset($cart[$request->unique_id]);
        $this->saveCart($cart);

        return Response::json([
            'status'  => true,
            'message' => 'Item removed from cart.',
            'totals'  => $this->calculateTotals(),
        ]);
    }

    /* =======================
     | CLEAR CART
     ======================= */

    public function destroyCart()
    {
        session()->forget('cart');
        session()->forget('coupon_code'); // Also clear coupon

        return Response::json([
            'status'  => true,
            'message' => 'Cart cleared.',
        ]);
    }

    /* =======================
     | COUPON HANDLERS
     ======================= */

    public function applyCoupon(Request $request)
    {
        if (!Auth::check()) {
            return Response::json(['status' => false, 'message' => 'Please login to apply coupon.']);
        }

        $validator = Validator::make($request->all(), [
            'code' => 'required|string',
        ]);

        if ($validator->fails()) {
            return Response::json(['status' => false, 'message' => $validator->errors()->first()]);
        }

        $code = strtoupper($request->code);
        $coupon = $this->couponService->findByCode($code);

        if (!$coupon) {
            return Response::json(['status' => false, 'message' => 'Invalid coupon code.']);
        }

        // Basic Validation
        if (!$coupon->is_active) {
            return Response::json(['status' => false, 'message' => 'Coupon is inactive.']);
        }

        $now = now();
        if ($coupon->start_date && $now->lt($coupon->start_date)) {
            return Response::json(['status' => false, 'message' => 'Coupon is not yet valid.']);
        }
        if ($coupon->end_date && $now->gt($coupon->end_date)) {
            return Response::json(['status' => false, 'message' => 'Coupon has expired.']);
        }

        // Global Usage Limit
        if (!$this->couponUsageService->checkGlobalLimit($coupon->id, $coupon->usage_limit_per_coupon)) {
            return Response::json(['status' => false, 'message' => 'Coupon usage limit exceeded.']);
        }

        // Per User Usage Limit
        if ($coupon->usage_limit_per_user > 0) {
            $userUsage = DB::table('coupon_usages')
                ->where('coupon_id', $coupon->id)
                ->where('user_id', Auth::id())
                ->count();

            if ($userUsage >= $coupon->usage_limit_per_user) {
                return Response::json(['status' => false, 'message' => 'You have already used this coupon the maximum number of times.']);
            }
        }

        // Min Spend Check
        $totals = $this->calculateTotals(false); // Get subtotal first without discount
        $subtotal = $totals['subtotal'];

        if ($coupon->min_spend && $subtotal < $coupon->min_spend) {
            return Response::json(['status' => false, 'message' => "Minimum spend of {$coupon->min_spend} required."]);
        }

        session()->put('coupon_code', $code);

        return Response::json([
            'status'  => true,
            'message' => 'Coupon applied successfully.',
            'totals'  => $this->calculateTotals(),
        ]);
    }

    public function removeCoupon()
    {
        session()->forget('coupon_code');
        return Response::json([
            'status'  => true,
            'message' => 'Coupon removed.',
            'totals'  => $this->calculateTotals(),
        ]);
    }

    /* =======================
     | TOTAL CALCULATIONS
     ======================= */

    public function calculateTotals($applyCoupon = true): array
    {
        $cart = $this->getCart();
        $subtotal = 0;

        // Initialize discount amount for all items
        foreach ($cart as $key => $item) {
            $cart[$key]['discount_amount'] = 0;
            $subtotal += $item['price'] * $item['qty'];
        }

        $discount = 0;
        $couponCode = session()->get('coupon_code');
        $couponData = null;

        if ($applyCoupon && $couponCode) {
            $coupon = Coupon::with(['products', 'categories'])->where('code', $couponCode)->first();

            if ($coupon && $coupon->is_active) {
                $isValid = true;
                $now = now();
                if (($coupon->start_date && $now->lt($coupon->start_date)) || ($coupon->end_date && $now->gt($coupon->end_date))) {
                    $isValid = false;
                }

                $eligibleSubtotal = 0;
                $restrictedProductIds = $coupon->products->pluck('id')->toArray();
                $restrictedCategoryIds = $coupon->categories->pluck('id')->toArray();
                $hasRestrictions = !empty($restrictedProductIds) || !empty($restrictedCategoryIds);
                $matchingItemsFound = false;

                // First pass: Calculate Eligible Subtotal
                foreach ($cart as $key => $item) {
                    $isItemEligible = false;
                    if (!$hasRestrictions) {
                        $isItemEligible = true;
                    } else {
                        if (in_array($item['product_id'], $restrictedProductIds)) {
                            $isItemEligible = true;
                        }
                        if (!$isItemEligible && !empty($restrictedCategoryIds)) {
                            // Optimization: Ideally store category_id in session, but for now fetching product is safe enough
                            // Note: In high traffic, this N+1 is bad.
                            // Assuming product won't change category frequently, but price might.
                            $prod = Product::find($item['product_id']);
                            if ($prod && in_array($prod->category_id, $restrictedCategoryIds)) {
                                $isItemEligible = true;
                            }
                        }
                    }

                    if ($isItemEligible) {
                        $cart[$key]['is_eligible'] = true;
                        $eligibleSubtotal += $item['price'] * $item['qty'];
                        $matchingItemsFound = true;
                    } else {
                        $cart[$key]['is_eligible'] = false;
                    }
                }

                if ($hasRestrictions && !$matchingItemsFound) {
                    $isValid = false;
                }

                if ($coupon->min_spend && $subtotal < $coupon->min_spend) {
                    $isValid = false;
                }

                if ($isValid && $eligibleSubtotal > 0) {
                    if ($coupon->discount_type == 'fixed') {
                        $discount = $coupon->value;
                        if ($hasRestrictions && $discount > $eligibleSubtotal) {
                            $discount = $eligibleSubtotal;
                        }
                    } elseif ($coupon->discount_type == 'percentage') {
                        $discount = ($eligibleSubtotal * $coupon->value) / 100;
                        if ($coupon->max_discount && $discount > $coupon->max_discount) {
                            $discount = $coupon->max_discount;
                        }
                    }

                    // Distribution Logic (Pro-rata)
                    $remainingDiscount = $discount;
                    $eligibleItemsCount = 0;
                    foreach ($cart as $key => $item) {
                        if (!empty($item['is_eligible'])) $eligibleItemsCount++;
                    }

                    $processedCount = 0;
                    foreach ($cart as $key => $item) {
                        if (!empty($item['is_eligible'])) {
                            $processedCount++;
                            $itemTotal = $item['price'] * $item['qty'];

                            if ($processedCount == $eligibleItemsCount) {
                                // Last item gets the remainder to fix rounding issues
                                $itemDiscount = $remainingDiscount;
                            } else {
                                $ratio = $itemTotal / $eligibleSubtotal;
                                $itemDiscount = round($discount * $ratio, 2);
                            }

                            // Safety check: Discount shouldn't exceed item total
                            if ($itemDiscount > $itemTotal) {
                                $itemDiscount = $itemTotal;
                            }

                            $cart[$key]['discount_amount'] = $itemDiscount;
                            $remainingDiscount -= $itemDiscount;
                        }
                    }

                    $couponData = [
                        'code' => $coupon->code,
                        'discount_amount' => $discount
                    ];
                }
            }
        }

        if ($discount > $subtotal) {
            $discount = $subtotal;
        }

        $taxRate  = config('constants.tax_percentage');
        $taxable = max(0, $subtotal - $discount);
        $tax      = $taxable * ($taxRate / 100);

        $shipping = 0;
        if ($subtotal < config('constants.shipping_charges_limit')) {
            $shipping = config('constants.shipping_charges');
        }

        $total    = $subtotal - $discount + $tax + $shipping;

        return [
            'subtotal' => round($subtotal, 2),
            'discount' => round($discount, 2),
            'coupon'   => $couponData,
            'tax'      => round($tax, 2),
            'shipping' => round($shipping, 2),
            'total'    => round($total, 2),
            'cart_items' => $cart, // Return enriched cart items with discount info
        ];
    }

    public function totalPrice()
    {
        return Response::json($this->calculateTotals());
    }
}
