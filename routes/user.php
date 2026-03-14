<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Secure\DashboardController;
use App\Http\Controllers\Secure\UserOrderController;
use App\Http\Controllers\Secure\OrderCancellationRequestController;
use App\Http\Controllers\Secure\ProfileController;
use App\Http\Controllers\Secure\WishlistController;
use App\Http\Controllers\Secure\AddressController;
use App\Http\Controllers\Secure\PaymentController;
use App\Http\Controllers\Secure\ReturnRequestController;

Route::middleware(['auth', 'role:USER', 'sessionTimeout', 'checkConcurrentSessions', 'preventBackHistory', 'throttle:40,1'])->group(function () {
    Route::get('/user/dashboard', [DashboardController::class, 'userDashboard'])->name('user.dashboard');
    // Orders controller
    Route::get('/user/orders', [UserOrderController::class, 'userOrders'])->name('user.orders');
    Route::get('/user/orders/view/{id}', [UserOrderController::class, 'viewUserOrderDetails'])->name('user.orders.view');
    Route::post('/ajax/user/orders/cancel', [UserOrderController::class, 'cancelUserOrder']);
    Route::post('/ajax/user/orders/return', [UserOrderController::class, 'returnUserOrder']);
    Route::post('/ajax/user/orders/cancel-return', [UserOrderController::class, 'cancelReturnUserOrder']);

    // Order Cancellation Requests
    Route::post('/order/cancel-request/store', [OrderCancellationRequestController::class, 'store'])->name('order.cancel.store');
    Route::post('/order/cancel-request/message/store', [OrderCancellationRequestController::class, 'storeMessage'])->name('order.cancel.message.store');
    Route::post('/order/cancel-request/status/update/{id}', [OrderCancellationRequestController::class, 'updateStatus'])->name('order.cancel.status.update');

    // Return Requests
    Route::post('/return-requests/store', [ReturnRequestController::class, 'store'])->name('user.return-requests.store');


    // Profile Controller - WE NEED TO FIX THIS
    Route::get('/user/profile', [ProfileController::class, 'userProfile'])->name('user.profile');
    Route::post('/user/profile/update', [ProfileController::class, 'userProfileUpdate'])->name('user.profile.update');
    Route::post('/user/profile/change-password', [ProfileController::class, 'userChangePassword'])->name('user.profile.change-password');

    // Orders controller
    Route::post('/ajax/get/users/all-orders', [UserOrderController::class, 'getAllUsersOrders']);

    // Wishlist controller
    Route::get('/user/wishlist', [WishlistController::class, 'index'])->name('user.wishlist');
    Route::post('/user/ajax/wishlist/delete', [WishlistController::class, 'destroy'])->name('user.wishlist.destroy');
    Route::post('/add-to-wishlist', [WishlistController::class, 'store'])->name('user.wishlist.add');

    // Address Controller
    // Route::post('/pincode', [AddressController::class, 'pincode']);
    Route::post('/ajax/address/delete', [AddressController::class, 'destroy']);
    Route::post('/ajax/address/get-single-address', [AddressController::class, 'fetchOne']);

    Route::post('/address/save', [AddressController::class, 'store']);
    Route::post('/address/update/{address}', [AddressController::class, 'update']);

    // Payment Controller
    // Route::post('/payment/initiate', [PaymentController::class, 'initiatePayment'])->name('payment.initiate');
    Route::any('/payment/response/{gateway?}', [PaymentController::class, 'paymentResponse'])->name('payment.response'); // specific gateway callback
    Route::get('/order-placed', [PaymentController::class, 'orderPlaced'])->name('order.placed');
    Route::get('/order-failed', [PaymentController::class, 'orderFailed'])->name('order.failed');

    // Customer Reviews
    Route::post('/user/review/store', [App\Http\Controllers\Secure\UserReviewController::class, 'store'])->name('user.review.store');
});
