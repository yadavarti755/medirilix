<?php

use App\Http\Controllers\Auth\GoogleSocialController;
use App\Http\Controllers\Auth\PublicLoginController;
use App\Http\Controllers\Auth\PublicRegisterController;
use App\Http\Controllers\Secure\FeedbackController;
use App\Http\Controllers\SitemapController;
use App\Http\Controllers\Website\CartController;
use App\Http\Controllers\Website\CheckoutController;
use App\Http\Controllers\Website\ContactUsController;
use App\Http\Controllers\Website\HomeController;
use App\Http\Controllers\Website\OrderController;
use App\Http\Controllers\Website\PageController;
use App\Http\Controllers\Website\ProductController;
use Illuminate\Support\Facades\Route;

Route::group(['middleware' => ['setLocale', 'trackVisitor', 'referer.check']], function () {

    // Cart controller
    Route::post('/destroy-cart', [CartController::class, 'destroyCart'])->name('destroy-cart');
    Route::post('/remove-cart-item', [CartController::class, 'removeCartItem'])->name('remove-cart-item');
    Route::post('/update-cart-quantity', [CartController::class, 'updateQuantity'])->name('update-card-quantity');
    Route::post('/update-cart', [CartController::class, 'updateCart'])->name('update-cart');
    Route::post('/add-to-cart', [CartController::class, 'addToCart'])->name('add-to-cart');
    Route::get('/cart', [CartController::class, 'cart'])->name('cart');
    Route::post('/apply-coupon', [CartController::class, 'applyCoupon'])->name('apply-coupon');
    Route::post('/remove-coupon', [CartController::class, 'removeCoupon'])->name('remove-coupon');

    Route::get('/offer', [HomeController::class, 'offer'])->name('offer');

    Route::get('/order-summary', [OrderController::class, 'orderSummary'])->name('order-summary');

    // Payment Routes
    Route::post('/payment/initiate', [OrderController::class, 'initiatePayment'])->name('payment.initiate');
    Route::get('/payment/paypal/success', [OrderController::class, 'verifyPayPal'])->name('payment.paypal.success');
    Route::get('/payment/paypal/cancel', [OrderController::class, 'cancelPayPal'])->name('payment.paypal.cancel');

    Route::post('/payment/razorpay/callback', [OrderController::class, 'verifyRazorpay'])->name('payment.razorpay.callback');
    Route::get('/payment/razorpay/cancel', [OrderController::class, 'cancelRazorpay'])->name('payment.razorpay.cancel');

    Route::post('/proceed-checkout', [CheckoutController::class, 'proceedCheckout']);
    Route::get('/checkout', [CheckoutController::class, 'checkout'])->name('checkout');
    Route::get('/my-account', [HomeController::class, 'myAccount'])->name('my-account');

    // Google Redirects
    Route::get('/auth/google/callback', [GoogleSocialController::class, 'handleGoogleCallback']);
    Route::get('/auth/google', [GoogleSocialController::class, 'redirectToGoogle'])->name('auth.google');

    Route::get('/show/{slug}', [ProductController::class, 'showProduct'])->name('product-details');
    Route::get('/search', [ProductController::class, 'shop'])->name('search');
    Route::get('/shop/{slug?}', [ProductController::class, 'shop'])->name('shop');

    Route::post('/contact-us/submit', [ContactUsController::class, 'submitContactUs'])->name('contact-us.submit');
    Route::get('/contact-us', [ContactUsController::class, 'contactUs'])->name('contact-us');
    Route::get('/reload-captcha', [ContactUsController::class, 'reloadCaptcha'])->name('reload-captcha');

    Route::get('/login', [PublicLoginController::class, 'login'])->name('public.login');
    Route::post('/public/login/check', [PublicLoginController::class, 'checkLogin'])->name('public.login.check');

    // Register
    Route::get('/register', [PublicRegisterController::class, 'register'])->name('public.register');
    Route::post('/register/store', [PublicRegisterController::class, 'storeRegisteration'])->name('public.register.store');
    Route::get('/register/user/verification', [PublicRegisterController::class, 'showUserVerifyForm'])->name('public.register.user-verification');
    Route::post('/register/user/verify', [PublicRegisterController::class, 'userVerify'])->name('public.register.verify-user');
    Route::post('/register/user/resend-otp', [PublicRegisterController::class, 'resendOtp'])->name('public.register.resend-otp');

    // Forget Password Routes
    Route::get('/forget-password', [PublicLoginController::class, 'showForgetPasswordForm'])
        ->name('public.login.forget-password.index');
    // Reset Password Routes
    Route::get('/reset-password/{token}', [PublicLoginController::class, 'showResetPasswordForm'])
        ->name('public.login.reset-password.form');
    Route::post('/reset-password/update', [PublicLoginController::class, 'updatePassword'])
        ->name('public.login.reset-password.update');

    Route::post('/forget-password/verify-otp', [PublicLoginController::class, 'verifyForgetPasswordOtp'])
        ->name('public.login.forget-password.verify-otp');
    Route::post('/forget-password/send-otp', [PublicLoginController::class, 'sendForgetPasswordOtp'])
        ->name('public.login.forget-password.send-otp');
    Route::post('/forget-password/resend-otp', [PublicLoginController::class, 'resendForgetPasswordOtp'])
        ->name('public.login.forget-password.resend-otp');

    // Feedback
    // Route::get('/feedback', [FeedbackController::class, 'showFeedbackForm'])->name('feedback');
    // Route::post('/feedback', [FeedbackController::class, 'saveFeedback'])->name('feedback.submit');

    Route::get('/sitemap', [SitemapController::class, 'showSitemap'])->name('sitemap');

    Route::get('/', [HomeController::class, 'index'])->name('homepage');
    Route::post('/subscribe-newsletter', [App\Http\Controllers\Website\NewsletterController::class, 'subscribe'])->name('subscribe-newsletter');

    // Handle wildcard pages
    Route::get('/{any}', [PageController::class, 'page'])->where('any', '.*');
});
