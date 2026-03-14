<?php

use App\Http\Controllers\Secure\SizeController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\LogoutController;
use App\Http\Controllers\CaptchaController;
use App\Http\Controllers\LanguageController;
use App\Http\Controllers\Secure\AnnouncementController;
use App\Http\Controllers\Secure\AuditLogController;
use App\Http\Controllers\Secure\AuthenticationLogController;
use App\Http\Controllers\Secure\ContactDetailController;
use App\Http\Controllers\Secure\DashboardController;
use App\Http\Controllers\FileViewController;
use App\Http\Controllers\Secure\AddressController;
use App\Http\Controllers\Secure\BrandController;
use App\Http\Controllers\Secure\OfferController;
use App\Http\Controllers\Secure\CouponController;
use App\Http\Controllers\Secure\CategoryController;
use App\Http\Controllers\Secure\CountryController;
use App\Http\Controllers\Secure\EmailLogController;
use App\Http\Controllers\Secure\FeedbackController;
use App\Http\Controllers\Secure\IntendedUseController;
use App\Http\Controllers\Secure\MaterialController;
use App\Http\Controllers\Secure\OurPartnerController;
use App\Http\Controllers\Secure\MediaController;
use App\Http\Controllers\Secure\MenuController;
use App\Http\Controllers\Secure\OrderController;
use App\Http\Controllers\Secure\PageController;
use App\Http\Controllers\Secure\PaymentGatewayController;
use App\Http\Controllers\Secure\ProductController;
use App\Http\Controllers\Secure\ProductTypeController;
use App\Http\Controllers\Secure\ProfileController;
use App\Http\Controllers\Secure\RoleController;
use App\Http\Controllers\Secure\SiteSettingController;
use App\Http\Controllers\Secure\SliderController;
use App\Http\Controllers\Secure\SmsLogController;
use App\Http\Controllers\Secure\SocialMediaController;
use App\Http\Controllers\Secure\StateController;
use App\Http\Controllers\Secure\UnitTypeController;
use App\Http\Controllers\Secure\UserController;
use App\Http\Controllers\Secure\OrderProductShippingDetailController;
use App\Http\Controllers\Secure\RefundController;
use App\Http\Controllers\Secure\ReturnPolicyController;
use App\Http\Controllers\Secure\ReturnReasonController;
use App\Http\Controllers\Secure\CancelReasonController;
use App\Http\Controllers\Secure\CustomerReviewController;
use App\Http\Controllers\Secure\ReturnRequestController;
use App\Http\Controllers\Secure\OrderCancellationRequestController;
use App\Http\Controllers\Secure\PaymentMethodController;
use App\Http\Controllers\Secure\QueryController;
use App\Http\Controllers\Secure\SubscribeNewsletterController;
use Illuminate\Support\Facades\Route;

Route::middleware('referer.check')->group(function () {
    Route::prefix('secure')->middleware(['auth', 'sessionTimeout', 'checkConcurrentSessions', 'scanUploadedFiles', 'preventBackHistory', 'throttle:40,1'])->group(function () {
        Route::get('/logout', [LogoutController::class, 'index'])->name('logout');
        Route::post('/logout', [LogoutController::class, 'index'])->name('logout.post');

        Route::get('/dashboard', [DashboardController::class, 'adminDashboard'])->middleware('can:view admin dashboard')->name('admin.dashboard');
        Route::get('/dashboard/revenue-chart', [DashboardController::class, 'getRevenueChartData'])->name('admin.dashboard.revenue-chart');
        Route::get('/dashboard/orders-chart', [DashboardController::class, 'getOrdersChartData'])->name('admin.dashboard.orders-chart');
        Route::get('/dashboard/yearly-orders-chart', [DashboardController::class, 'getYearlyOrdersChartData'])->name('admin.dashboard.yearly-orders-chart');

        Route::get('/audit-logs', [AuditLogController::class, 'index'])->middleware('can:view audit log')->name('audit-logs.index');
        // Return Reasons Setup
        Route::group(['prefix' => 'return-reasons', 'as' => 'return-reasons.'], function () {
            Route::get('/', [ReturnReasonController::class, 'index'])->middleware('can:view return reason')->name('index');
            Route::get('fetch-data', [ReturnReasonController::class, 'fetchForDatatable'])->middleware('can:view return reason')->name('fetch.data');
            Route::post('store', [ReturnReasonController::class, 'store'])->middleware('can:add return reason')->name('store');
            Route::get('fetch-one/{id}', [ReturnReasonController::class, 'fetchOne'])->middleware('can:view return reason')->name('fetch.one');
            Route::put('update/{id}', [ReturnReasonController::class, 'update'])->middleware('can:edit return reason')->name('update');
            Route::delete('delete/{id}', [ReturnReasonController::class, 'destroy'])->middleware('can:delete return reason')->name('delete');
        });

        // Cancel Reasons Setup
        Route::group(['prefix' => 'cancel-reasons', 'as' => 'cancel-reasons.'], function () {
            Route::get('/', [CancelReasonController::class, 'index'])->middleware('can:view cancel reason')->name('index');
            Route::get('fetch-data', [CancelReasonController::class, 'fetchForDatatable'])->middleware('can:view cancel reason')->name('fetch.data');
            Route::post('store', [CancelReasonController::class, 'store'])->middleware('can:add cancel reason')->name('store');
            Route::get('fetch-one/{id}', [CancelReasonController::class, 'fetchOne'])->middleware('can:view cancel reason')->name('fetch.one');
            Route::put('update/{id}', [CancelReasonController::class, 'update'])->middleware('can:edit cancel reason')->name('update');
            Route::delete('delete/{id}', [CancelReasonController::class, 'destroy'])->middleware('can:delete cancel reason')->name('delete');
        });

        // Return Requests
        Route::group(['prefix' => 'return-requests', 'as' => 'return-requests.'], function () {
            Route::get('/', [ReturnRequestController::class, 'index'])->middleware('can:view return request')->name('index');
            Route::get('fetch-data', [ReturnRequestController::class, 'fetchForDatatable'])->middleware('can:view return request')->name('fetch.data');
            Route::post('store', [ReturnRequestController::class, 'store'])->middleware('can:edit return request')->name('store');
            Route::put('update-status/{id}', [ReturnRequestController::class, 'updateStatus'])->middleware('can:edit return request')->name('update-status');
            Route::get('fetch-one/{id}', [ReturnRequestController::class, 'fetchOne'])->middleware('can:view return request')->name('fetch.one');
        });

        Route::post('/audit-logs/fetch-for-datatable', [AuditLogController::class, 'fetchForDatatable'])->middleware('can:view audit log')->name('audit-logs.fetch-for-datatable');
        // ------------------------------------
        // Users
        // ------------------------------------
        Route::get('/users', [UserController::class, 'index'])->middleware('can:view user')->name('users.index');
        Route::post('/users/fetch-for-datatable', [UserController::class, 'fetchForDatatable'])->middleware('can:view user')->name('users.fetch-for-datatable');
        Route::get('/users/create', [UserController::class, 'create'])->middleware('can:add user')->name('users.create');
        Route::post('/users', [UserController::class, 'store'])->middleware('can:add user')->name('users.store');
        Route::get('/users/{user}', [UserController::class, 'show'])->middleware('can:view user')->name('users.show');
        Route::post('/users/delete/{user}', [UserController::class, 'destroy'])->middleware('can:delete user')->name('users.destroy');
        Route::get('/users/{user}/edit', [UserController::class, 'edit'])->middleware('can:edit user')->name('users.edit');
        Route::post('/users/reset-password/{user}', [UserController::class, 'resetPassword'])->middleware('can:reset password')->name('users.reset-password');
        Route::post('/users/{user}', [UserController::class, 'update'])->middleware('can:edit user')->name('users.update');

        // ------------------------------------
        // Roles
        // ------------------------------------
        Route::get('/roles', [RoleController::class, 'index'])->middleware('can:view role')->name('roles.index');
        Route::post('/roles/fetch-for-datatable', [RoleController::class, 'fetchRolesForDatatable'])->middleware('can:view role')->name('roles.fetch-for-datatable');
        Route::get('/roles/create', [RoleController::class, 'create'])->middleware('can:add role')->name('roles.create');
        Route::post('/roles', [RoleController::class, 'store'])->middleware('can:add role')->name('roles.store');
        Route::get('/roles/{role}', [RoleController::class, 'show'])->middleware('can:view role')->name('roles.show');
        Route::post('/roles/delete/{role}', [RoleController::class, 'destroy'])->middleware('can:delete role')->name('roles.destroy');
        Route::get('/roles/{role}/edit', [RoleController::class, 'edit'])->middleware('can:edit role')->name('roles.edit');
        Route::post('/roles/permissions', [RoleController::class, 'getPermissionsByRole'])->name('roles.permissions');
        Route::post('/roles/{role}', [RoleController::class, 'update'])->middleware('can:edit role')->name('roles.update');

        // ------------------------------------
        // Menu
        // ------------------------------------
        Route::get('/menus', [MenuController::class, 'index'])->middleware('can:view menu')->name('menus.index');
        Route::get('/menus/create', [MenuController::class, 'create'])->middleware('can:add menu')->name('menus.create');
        Route::post('/menus', [MenuController::class, 'store'])->middleware('can:add menu')->name('menus.store');
        Route::get('/menus/{menu}', [MenuController::class, 'show'])->middleware('can:view menu')->name('menus.show');
        Route::get('/menus/{menu}/edit', [MenuController::class, 'edit'])->middleware('can:edit menu')->name('menus.edit');
        Route::post('/menus/delete/{menu}', [MenuController::class, 'destroy'])->middleware('can:delete menu')->name('menus.destroy');
        Route::post('/menus/update-order', [MenuController::class, 'updateOrder'])->middleware('can:view menu')->name('menus.updateOrder');
        Route::post('/menus/{menu}', [MenuController::class, 'update'])->middleware('can:edit menu')->name('menus.update');

        // ------------------------------------
        // Site Settings
        // ------------------------------------
        Route::post('/site-settings/{siteSetting}', [SiteSettingController::class, 'update'])->middleware(['can:edit site setting'])->name('site-settings.update');
        Route::get('/site-settings', [SiteSettingController::class, 'index'])->middleware(['can:edit site setting'])->name('site-settings.index');


        Route::get('/profile', [ProfileController::class, 'index'])->name('profile.index');
        Route::post('/profile/update', [ProfileController::class, 'update'])->name('profile.update');
        Route::post('/profile/change-password', [ProfileController::class, 'changePassword'])->name('profile.change-password');

        // ------------------------------------
        // Page
        // ------------------------------------
        Route::get('/pages', [PageController::class, 'index'])->middleware('can:view page')->name('pages.index');
        Route::post('/pages/fetch-for-datatable', [PageController::class, 'fetchForDatatable'])->middleware('can:view page')->name('pages.fetch-for-datatable');
        Route::post('/pages/delete/{page}', [PageController::class, 'destroy'])->middleware('can:delete page')->name('pages.destroy');
        Route::get('/pages/create', [PageController::class, 'create'])->middleware('can:add page')->name('pages.create');
        Route::post('/pages', [PageController::class, 'store'])->middleware('can:add page')->name('pages.store');
        Route::get('/pages/{page}', [PageController::class, 'show'])->middleware('can:view page')->name('pages.show');
        Route::get('/pages/{page}/edit', [PageController::class, 'edit'])->middleware('can:edit page')->name('pages.edit');
        Route::post('/pages/{page}', [PageController::class, 'update'])->middleware('can:edit page')->name('pages.update');


        Route::post('/pages/publish/{page}', [PageController::class, 'publish'])->middleware('can:publish page')->name('pages.publish');

        // ------------------------------------
        // Slider
        // ------------------------------------
        Route::get('/sliders', [SliderController::class, 'index'])->middleware('can:view slider')->name('sliders.index');
        Route::post('/sliders/fetch-for-datatable', [SliderController::class, 'fetchForDatatable'])->middleware('can:view slider')->name('sliders.fetch-for-datatable');
        Route::post('/sliders/delete/{slider}', [SliderController::class, 'destroy'])->middleware('can:delete slider')->name('sliders.destroy');
        Route::get('/sliders/create', [SliderController::class, 'create'])->middleware('can:add slider')->name('sliders.create');
        Route::post('/sliders', [SliderController::class, 'store'])->middleware('can:add slider')->name('sliders.store');
        Route::get('/sliders/{slider}', [SliderController::class, 'show'])->middleware('can:view slider')->name('sliders.show');
        Route::get('/sliders/{slider}/edit', [SliderController::class, 'edit'])->middleware('can:edit slider')->name('sliders.edit');
        Route::post('/sliders/{slider}', [SliderController::class, 'update'])->middleware('can:edit slider')->name('sliders.update');
        Route::post('/sliders/publish/{slider}', [SliderController::class, 'publish'])->middleware('can:publish slider')->name('sliders.publish');

        // ------------------------------------
        // Announcement
        // ------------------------------------
        Route::get('/announcements', [AnnouncementController::class, 'index'])->middleware('can:view announcement')->name('announcements.index');
        Route::post('/announcements/fetch-for-datatable', [AnnouncementController::class, 'fetchForDatatable'])->middleware('can:view announcement')->name('announcements.fetch-for-datatable');
        Route::post('/announcements/delete/{announcement}', [AnnouncementController::class, 'destroy'])->middleware('can:delete announcement')->name('announcements.destroy');
        Route::get('/announcements/create', [AnnouncementController::class, 'create'])->middleware('can:add announcement')->name('announcements.create');
        Route::post('/announcements', [AnnouncementController::class, 'store'])->middleware('can:add announcement')->name('announcements.store');
        Route::get('/announcements/{announcement}', [AnnouncementController::class, 'show'])->middleware('can:view announcement')->name('announcements.show');
        Route::get('/announcements/{announcement}/edit', [AnnouncementController::class, 'edit'])->middleware('can:edit announcement')->name('announcements.edit');
        Route::post('/announcements/{announcement}', [AnnouncementController::class, 'update'])->middleware('can:edit announcement')->name('announcements.update');
        Route::post('/announcements/approve/{announcement}', [AnnouncementController::class, 'approve'])->middleware('can:approve announcement')->name('announcements.approve');
        Route::post('/announcements/publish/{announcement}', [AnnouncementController::class, 'publish'])->middleware('can:publish announcement')->name('announcements.publish');

        // ------------------------------------
        // Government Portal
        // ------------------------------------
        Route::get('/our-partners', [OurPartnerController::class, 'index'])->middleware('can:view our partner')->name('our-partners.index');
        Route::post('/our-partners/fetch-for-datatable', [OurPartnerController::class, 'fetchForDatatable'])->middleware('can:view our partner')->name('our-partners.fetch-for-datatable');
        Route::post('/our-partners/delete/{ourPartner}', [OurPartnerController::class, 'destroy'])->middleware('can:delete our partner')->name('our-partners.destroy');
        Route::get('/our-partners/create', [OurPartnerController::class, 'create'])->middleware('can:add our partner')->name('our-partners.create');
        Route::post('/our-partners', [OurPartnerController::class, 'store'])->middleware('can:add our partner')->name('our-partners.store');
        Route::get('/our-partners/{ourPartner}/edit', [OurPartnerController::class, 'edit'])->middleware('can:edit our partner')->name('our-partners.edit');
        Route::post('/our-partners/{ourPartner}', [OurPartnerController::class, 'update'])->middleware('can:edit our partner')->name('our-partners.update');
        Route::post('/our-partners/approve/{ourPartner}', [OurPartnerController::class, 'approve'])->middleware('can:approve our partner')->name('our-partners.approve');
        Route::post('/our-partners/publish/{ourPartner}', [OurPartnerController::class, 'publish'])->middleware('can:publish our partner')->name('our-partners.publish');


        // ------------------------------------
        // Contact Details
        // ------------------------------------
        Route::get('/contact-details', [ContactDetailController::class, 'index'])->middleware('can:view contact detail')->name('contact-details.index');
        Route::post('/contact-details/fetch-for-datatable', [ContactDetailController::class, 'fetchForDatatable'])->middleware('can:view contact detail')->name('contact-details.fetch-for-datatable');
        Route::post('/contact-details/delete/{contactDetail}', [ContactDetailController::class, 'destroy'])->middleware('can:delete contact detail')->name('contact-details.destroy');
        Route::get('/contact-details/create', [ContactDetailController::class, 'create'])->middleware('can:add contact detail')->name('contact-details.create');
        Route::post('/contact-details', [ContactDetailController::class, 'store'])->middleware('can:add contact detail')->name('contact-details.store');
        Route::get('/contact-details/{contactDetail}', [ContactDetailController::class, 'show'])->middleware('can:view contact detail')->name('contact-details.show');
        Route::get('/contact-details/{contactDetail}/edit', [ContactDetailController::class, 'edit'])->middleware('can:edit contact detail')->name('contact-details.edit');
        Route::post('/contact-details/{contactDetail}', [ContactDetailController::class, 'update'])->middleware('can:edit contact detail')->name('contact-details.update');
        Route::post('/contact-details/approve/{contactDetail}', [ContactDetailController::class, 'approve'])->middleware('can:approve contact detail')->name('contact-details.approve');
        Route::post('/contact-details/publish/{contactDetail}', [ContactDetailController::class, 'publish'])->middleware('can:publish contact detail')->name('contact-details.publish');

        // ------------------------------------
        // Contact Queries
        // ------------------------------------
        Route::get('/contact-queries', [QueryController::class, 'index'])->middleware('can:view contact query')->name('contact-queries.index');
        Route::post('/contact-queries/fetch-for-datatable', [QueryController::class, 'fetchForDatatable'])->middleware('can:view contact query')->name('contact-queries.fetch-for-datatable');
        Route::post('/contact-queries/delete/{query}', [QueryController::class, 'destroy'])->middleware('can:delete contact query')->name('contact-queries.destroy');

        // ------------------------------------
        // Social Media
        // ------------------------------------
        Route::get('/social-medias', [SocialMediaController::class, 'index'])->middleware('can:view social media')->name('social-medias.index');
        Route::post('/social-medias/fetch-for-datatable', [SocialMediaController::class, 'fetchForDatatable'])->middleware('can:view social media')->name('social-medias.fetch-for-datatable');
        Route::post('/social-medias/delete/{socialMedia}', [SocialMediaController::class, 'destroy'])->middleware('can:delete social media')->name('social-medias.destroy');
        Route::get('/social-medias/create', [SocialMediaController::class, 'create'])->middleware('can:add social media')->name('social-medias.create');
        Route::post('/social-medias', [SocialMediaController::class, 'store'])->middleware('can:add social media')->name('social-medias.store');
        Route::get('/social-medias/{socialMedia}', [SocialMediaController::class, 'show'])->middleware('can:view social media')->name('social-medias.show');
        Route::get('/social-medias/{socialMedia}/edit', [SocialMediaController::class, 'edit'])->middleware('can:edit social media')->name('social-medias.edit');
        Route::post('/social-medias/{socialMedia}', [SocialMediaController::class, 'update'])->middleware('can:edit social media')->name('social-medias.update');

        // ------------------------------------
        // Media Library
        // ------------------------------------
        Route::get('/medias', [MediaController::class, 'index'])->middleware('can:view media')->name('medias.index');
        Route::post('/medias/fetch-for-datatable', [MediaController::class, 'fetchForDatatable'])->middleware('can:view media')->name('medias.fetch-for-datatable');
        Route::post('/medias/delete/{mediaLibrary}', [MediaController::class, 'destroy'])->middleware('can:delete media')->name('medias.destroy');
        Route::get('/medias/create', [MediaController::class, 'create'])->middleware('can:add media')->name('medias.create');
        Route::post('/medias', [MediaController::class, 'store'])->middleware('can:add media')->name('medias.store');
        Route::get('/medias/{mediaLibrary}', [MediaController::class, 'show'])->middleware('can:view media')->name('medias.show');
        Route::get('/medias/{mediaLibrary}/edit', [MediaController::class, 'edit'])->middleware('can:edit media')->name('medias.edit');
        Route::post('/medias/{mediaLibrary}', [MediaController::class, 'update'])->middleware('can:edit media')->name('medias.update');
        Route::post('/medias/approve/{mediaLibrary}', [MediaController::class, 'approve'])->middleware('can:approve media')->name('medias.approve');
        Route::post('/medias/publish/{mediaLibrary}', [MediaController::class, 'publish'])->middleware('can:publish media')->name('medias.publish');

        // ------------------------------------
        // Authentication Log
        // ------------------------------------
        Route::get('/authentication-logs', [AuthenticationLogController::class, 'index'])->middleware('can:view authentication log')->name('authentication-logs.index');
        Route::post('/authentication-logs/fetch-for-datatable', [AuthenticationLogController::class, 'fetchForDatatable'])->middleware('can:view authentication log')->name('authentication-logs.fetch-for-datatable');


        // ------------------------------------
        // Feedback
        // ------------------------------------
        // Route::get('feedbacks/export/excel', [FeedbackController::class, 'exportExcel'])->name('feedbacks.export.excel');
        // Route::get('/feedbacks', [FeedbackController::class, 'index'])->middleware('can:view feedback')->name('feedbacks.index');
        // Route::post('/feedbacks/fetch-for-datatable', [FeedbackController::class, 'fetchForDatatable'])->middleware('can:view feedback')->name('feedbacks.fetch-for-datatable');
        // Route::post('/feedbacks/delete/{feedback}', [FeedbackController::class, 'destroy'])->middleware('can:delete feedback')->name('feedbacks.destroy');
        // Route::get('/feedbacks/{feedback}', [FeedbackController::class, 'show'])->middleware('can:view feedback')->name('feedbacks.show');


        // ------------------------------------
        // Email Logs
        // ------------------------------------
        Route::get('/email-logs', [EmailLogController::class, 'index'])->middleware('can:view email log')->name('email-logs.index');
        Route::post('/email-logs/fetch-for-datatable', [EmailLogController::class, 'fetchForDatatable'])->middleware('can:view email log')->name('email-logs.fetch-for-datatable');

        // ------------------------------------
        // SMS Logs
        // ------------------------------------
        Route::get('/sms-logs', [SmsLogController::class, 'index'])->middleware('can:view sms log')->name('sms-logs.index');
        Route::post('/sms-logs/fetch-for-datatable', [SmsLogController::class, 'fetchForDatatable'])->middleware('can:view sms log')->name('sms-logs.fetch-for-datatable');

        // ------------------------------------
        // File View
        // ------------------------------------
        Route::get('/file-view', [FileViewController::class, 'showBackendFile'])->name('backend.file.view');

        // ------------------------------------
        // Category
        // ------------------------------------
        Route::post('/categories/delete/{category}', [CategoryController::class, 'destroy'])->middleware('can:delete category')->name('categories.destroy');
        Route::get('/categories/create', [CategoryController::class, 'create'])->middleware('can:add category')->name('categories.create');
        Route::get('/categories/{category}/edit', [CategoryController::class, 'edit'])->middleware('can:edit category')->name('categories.edit');
        Route::post('/categories/update-order', [CategoryController::class, 'updateOrder'])->middleware('can:view category')->name('categories.update-order');
        Route::post('/categories/{category}', [CategoryController::class, 'update'])->middleware('can:edit category')->name('categories.update');
        Route::get('/categories/{category}', [CategoryController::class, 'show'])->middleware('can:view category')->name('categories.show');
        Route::post('/categories', [CategoryController::class, 'store'])->middleware('can:add category')->name('categories.store');
        Route::get('/categories', [CategoryController::class, 'index'])->middleware('can:view category')->name('categories.index');

        // ------------------------------------
        // Slider
        // ------------------------------------
        Route::get('/sizes', [SizeController::class, 'index'])->middleware('can:view size')->name('sizes.index');
        Route::post('/sizes/fetch-for-datatable', [SizeController::class, 'fetchForDatatable'])->middleware('can:view size')->name('sizes.fetch-for-datatable');
        Route::post('/sizes/delete/{size}', [SizeController::class, 'destroy'])->middleware('can:delete size')->name('sizes.destroy');
        Route::post('/sizes', [SizeController::class, 'store'])->middleware('can:add size')->name('sizes.store');
        Route::get('/sizes/fetch-one/{size}', [SizeController::class, 'fetchOne'])->middleware('can:view size')->name('sizes.fetch-one');
        Route::post('/sizes/{size}', [SizeController::class, 'update'])->middleware('can:edit size')->name('sizes.update');

        // ------------------------------------
        // Material
        // ------------------------------------
        Route::get('/materials', [MaterialController::class, 'index'])->middleware('can:view material')->name('materials.index');
        Route::post('/materials/fetch-for-datatable', [MaterialController::class, 'fetchForDatatable'])->middleware('can:view material')->name('materials.fetch-for-datatable');
        Route::post('/materials/delete/{material}', [MaterialController::class, 'destroy'])->middleware('can:delete material')->name('materials.destroy');
        Route::post('/materials', [MaterialController::class, 'store'])->middleware('can:add material')->name('materials.store');
        Route::get('/materials/fetch-one/{material}', [MaterialController::class, 'fetchOne'])->middleware('can:view material')->name('materials.fetch-one');
        Route::post('/materials/{material}', [MaterialController::class, 'update'])->middleware('can:edit material')->name('materials.update');

        // ------------------------------------
        // Brand
        // ------------------------------------
        Route::get('/brands', [BrandController::class, 'index'])->middleware('can:view brand')->name('brands.index');
        Route::post('/brands/fetch-for-datatable', [BrandController::class, 'fetchForDatatable'])->middleware('can:view brand')->name('brands.fetch-for-datatable');
        Route::post('/brands/delete/{brand}', [BrandController::class, 'destroy'])->middleware('can:delete brand')->name('brands.destroy');
        Route::post('/brands', [BrandController::class, 'store'])->middleware('can:add brand')->name('brands.store');
        Route::get('/brands/fetch-one/{brand}', [BrandController::class, 'fetchOne'])->middleware('can:view brand')->name('brands.fetch-one');
        Route::post('/brands/{brand}', [BrandController::class, 'update'])->middleware('can:edit brand')->name('brands.update');


        // ------------------------------------
        // Offer
        // ------------------------------------
        Route::get('/offers', [OfferController::class, 'index'])->middleware('can:view offer')->name('offers.index');
        Route::post('/offers/fetch-for-datatable', [OfferController::class, 'fetchForDatatable'])->middleware('can:view offer')->name('offers.fetch-for-datatable');
        Route::post('/offers/delete/{offer}', [OfferController::class, 'destroy'])->middleware('can:delete offer')->name('offers.destroy');
        Route::post('/offers', [OfferController::class, 'store'])->middleware('can:add offer')->name('offers.store');
        Route::get('/offers/fetch-one/{offer}', [OfferController::class, 'fetchOne'])->middleware('can:view offer')->name('offers.fetch-one');
        Route::post('/offers/{offer}', [OfferController::class, 'update'])->middleware('can:edit offer')->name('offers.update');


        // ------------------------------------
        // Coupon
        // ------------------------------------
        Route::get('/coupons', [CouponController::class, 'index'])->middleware('can:view coupon')->name('coupons.index');
        Route::post('/coupons/fetch-for-datatable', [CouponController::class, 'fetchForDatatable'])->middleware('can:view coupon')->name('coupons.fetch-for-datatable');
        Route::post('/coupons/delete/{coupon}', [CouponController::class, 'destroy'])->middleware('can:delete coupon')->name('coupons.destroy');
        Route::post('/coupons', [CouponController::class, 'store'])->middleware('can:add coupon')->name('coupons.store');
        Route::get('/coupons/fetch-one/{coupon}', [CouponController::class, 'fetchOne'])->middleware('can:view coupon')->name('coupons.fetch-one');
        Route::post('/coupons/{coupon}', [CouponController::class, 'update'])->middleware('can:edit coupon')->name('coupons.update');


        // ------------------------------------
        // Payment Gateway
        // ------------------------------------
        Route::get('/payment-gateways', [PaymentGatewayController::class, 'index'])->middleware('can:view payment gateway')->name('payment-gateways.index');
        Route::post('/payment-gateways/fetch-for-datatable', [PaymentGatewayController::class, 'fetchForDatatable'])->middleware('can:view payment gateway')->name('payment-gateways.fetch-for-datatable');
        Route::post('/payment-gateways/delete/{paymentGateway}', [PaymentGatewayController::class, 'destroy'])->middleware('can:delete payment gateway')->name('payment-gateways.destroy');
        Route::post('/payment-gateways', [PaymentGatewayController::class, 'store'])->middleware('can:add payment gateway')->name('payment-gateways.store');
        Route::get('/payment-gateways/fetch-one/{paymentGateway}', [PaymentGatewayController::class, 'fetchOne'])->middleware('can:view payment gateway')->name('payment-gateways.fetch-one');
        Route::post('/payment-gateways/{paymentGateway}', [PaymentGatewayController::class, 'update'])->middleware('can:edit payment gateway')->name('payment-gateways.update');

        // ------------------------------------
        // Payment Method
        // ------------------------------------
        Route::get('/payment-methods', [PaymentMethodController::class, 'index'])->middleware('can:view payment method')->name('payment-methods.index');
        Route::post('/payment-methods/fetch-for-datatable', [PaymentMethodController::class, 'fetchForDatatable'])->middleware('can:view payment method')->name('payment-methods.fetch-for-datatable');
        Route::get('/payment-methods/fetch-one/{id}', [PaymentMethodController::class, 'fetchOne'])->middleware('can:view payment method')->name('payment-methods.fetch-one');
        Route::post('/payment-methods/delete/{paymentMethod}', [PaymentMethodController::class, 'destroy'])->middleware('can:delete payment method')->name('payment-methods.destroy');
        Route::get('/payment-methods/create', [PaymentMethodController::class, 'create'])->middleware('can:add payment method')->name('payment-methods.create');
        Route::post('/payment-methods', [PaymentMethodController::class, 'store'])->middleware('can:add payment method')->name('payment-methods.store');
        Route::get('/payment-methods/{paymentMethod}', [PaymentMethodController::class, 'show'])->middleware('can:view payment method')->name('payment-methods.show');
        Route::get('/payment-methods/{paymentMethod}/edit', [PaymentMethodController::class, 'edit'])->middleware('can:edit payment method')->name('payment-methods.edit');
        Route::post('/payment-methods/{paymentMethod}', [PaymentMethodController::class, 'update'])->middleware('can:edit payment method')->name('payment-methods.update');
        Route::post('/payment-methods/publish/{paymentMethod}', [PaymentMethodController::class, 'publish'])->middleware('can:publish payment method')->name('payment-methods.publish');



        // ------------------------------------
        // Currencies
        // ------------------------------------
        Route::get('/currencies', [App\Http\Controllers\Secure\CurrencyController::class, 'index'])->middleware('can:view currency')->name('currencies.index');
        Route::post('/currencies/fetch-for-datatable', [App\Http\Controllers\Secure\CurrencyController::class, 'fetchForDatatable'])->middleware('can:view currency')->name('currencies.fetch-for-datatable');
        Route::post('/currencies/delete/{currency}', [App\Http\Controllers\Secure\CurrencyController::class, 'destroy'])->middleware('can:delete currency')->name('currencies.destroy');
        Route::post('/currencies', [App\Http\Controllers\Secure\CurrencyController::class, 'store'])->middleware('can:add currency')->name('currencies.store');
        Route::get('/currencies/fetch-one/{currency}', [App\Http\Controllers\Secure\CurrencyController::class, 'fetchOne'])->middleware('can:view currency')->name('currencies.fetch-one');
        Route::post('/currencies/{currency}', [App\Http\Controllers\Secure\CurrencyController::class, 'update'])->middleware('can:edit currency')->name('currencies.update');

        // ------------------------------------
        // Product Types
        // ------------------------------------
        Route::get('/product-types', [ProductTypeController::class, 'index'])->middleware('can:view product type')->name('product-types.index');
        Route::post('/product-types/fetch-for-datatable', [ProductTypeController::class, 'fetchForDatatable'])->middleware('can:view product type')->name('product-types.fetch-for-datatable');
        Route::post('/product-types/delete/{productType}', [ProductTypeController::class, 'destroy'])->middleware('can:delete product type')->name('product-types.destroy');
        Route::post('/product-types', [ProductTypeController::class, 'store'])->middleware('can:add product type')->name('product-types.store');
        Route::get('/product-types/fetch-one/{productType}', [ProductTypeController::class, 'fetchOne'])->middleware('can:view product type')->name('product-types.fetch-one');
        Route::post('/product-types/{productType}', [ProductTypeController::class, 'update'])->middleware('can:edit product type')->name('product-types.update');

        // ------------------------------------
        // Product
        // ------------------------------------
        Route::post('/products/delete/{product}', [ProductController::class, 'destroy'])->middleware('can:delete product')->name('products.destroy');
        Route::post('/products/publish/{product}', [ProductController::class, 'publish'])->middleware('can:publish product')->name('products.publish');
        Route::post('/products/image/{id}', [ProductController::class, 'deleteImage'])->middleware('can:edit product')->name('products.image.delete');
        Route::post('/products/fetch-for-datatable', [ProductController::class, 'fetchForDatatable'])->middleware('can:view product')->name('products.fetch-for-datatable');
        Route::post('/products/{product}', [ProductController::class, 'update'])->middleware('can:edit product')->name('products.update');
        Route::post('/products', [ProductController::class, 'store'])->middleware('can:add product')->name('products.store');
        Route::get('/products', [ProductController::class, 'index'])->middleware('can:view product')->name('products.index');
        Route::get('/products/create', [ProductController::class, 'create'])->middleware('can:add product')->name('products.create');
        Route::get('/products/{product}', [ProductController::class, 'show'])->middleware('can:view product')->name('products.show');
        Route::get('/products/{product}/edit', [ProductController::class, 'edit'])->middleware('can:edit product')->name('products.edit');

        // ------------------------------------
        // Unit Types
        // ------------------------------------
        Route::get('/unit-types', [UnitTypeController::class, 'index'])->middleware('can:view unit type')->name('unit-types.index');
        Route::post('/unit-types/fetch-for-datatable', [UnitTypeController::class, 'fetchForDatatable'])->middleware('can:view unit type')->name('unit-types.fetch-for-datatable');
        Route::post('/unit-types/delete/{unitType}', [UnitTypeController::class, 'destroy'])->middleware('can:delete unit type')->name('unit-types.destroy');
        Route::post('/unit-types', [UnitTypeController::class, 'store'])->middleware('can:add unit type')->name('unit-types.store');
        Route::get('/unit-types/fetch-one/{unitType}', [UnitTypeController::class, 'fetchOne'])->middleware('can:view unit type')->name('unit-types.fetch-one');
        Route::post('/unit-types/{unitType}', [UnitTypeController::class, 'update'])->middleware('can:edit unit type')->name('unit-types.update');

        // ------------------------------------
        // Intended Uses
        // ------------------------------------
        Route::get('/intended-uses', [IntendedUseController::class, 'index'])->middleware('can:view intended use')->name('intended-uses.index');
        Route::post('/intended-uses/fetch-for-datatable', [IntendedUseController::class, 'fetchForDatatable'])->middleware('can:view intended use')->name('intended-uses.fetch-for-datatable');
        Route::post('/intended-uses/delete/{intendedUse}', [IntendedUseController::class, 'destroy'])->middleware('can:delete intended use')->name('intended-uses.destroy');
        Route::post('/intended-uses', [IntendedUseController::class, 'store'])->middleware('can:add intended use')->name('intended-uses.store');
        Route::get('/intended-uses/fetch-one/{intendedUse}', [IntendedUseController::class, 'fetchOne'])->middleware('can:view intended use')->name('intended-uses.fetch-one');
        Route::post('/intended-uses/{intendedUse}', [IntendedUseController::class, 'update'])->middleware('can:edit intended use')->name('intended-uses.update');

        // ------------------------------------
        // Country
        // ------------------------------------
        Route::get('/countries', [CountryController::class, 'index'])->middleware('can:view country')->name('countries.index');
        Route::post('/countries/fetch-for-datatable', [CountryController::class, 'fetchForDatatable'])->middleware('can:view country')->name('countries.fetch-for-datatable');
        Route::post('/countries/delete/{country}', [CountryController::class, 'destroy'])->middleware('can:delete country')->name('countries.destroy');
        Route::post('/countries', [CountryController::class, 'store'])->middleware('can:add country')->name('countries.store');
        Route::get('/countries/fetch-one/{country}', [CountryController::class, 'fetchOne'])->middleware('can:view country')->name('countries.fetch-one');
        Route::post('/countries/{country}', [CountryController::class, 'update'])->middleware('can:edit country')->name('countries.update');

        // ------------------------------------
        // State
        // ------------------------------------
        Route::get('/states', [StateController::class, 'index'])->middleware('can:view state')->name('states.index');
        Route::post('/states/fetch-for-datatable', [StateController::class, 'fetchForDatatable'])->middleware('can:view state')->name('states.fetch-for-datatable');
        Route::post('/states/delete/{state}', [StateController::class, 'destroy'])->middleware('can:delete state')->name('states.destroy');
        Route::post('/states', [StateController::class, 'store'])->middleware('can:add state')->name('states.store');
        Route::get('/states/fetch-one/{state}', [StateController::class, 'fetchOne'])->middleware('can:view state')->name('states.fetch-one');
        Route::post('/states/{state}', [StateController::class, 'update'])->middleware('can:edit state')->name('states.update');

        // ------------------------------------
        // Order Controller
        // ------------------------------------
        Route::get('/orders/view/{id}', [OrderController::class, 'viewOrderDetails'])->middleware('can:view order')->name('orders.view');
        Route::post('/orders/change-status/{id}', [OrderController::class, 'changeOrderStatus'])->middleware('can:view order')->name('orders.change-status');
        Route::post('/orders/fetch-for-admin-datatable', [OrderController::class, 'fetchAllAdminOrdersForDatatable'])->middleware('can:view order')->name('orders.fetch-for-admin-datatable');
        Route::get('/orders', [OrderController::class, 'adminOrders'])->middleware('can:view order')->name('orders.index');


        // ------------------------------------
        // Return Policies
        // ------------------------------------
        Route::get('/return-policies', [ReturnPolicyController::class, 'index'])->middleware('can:view return policy')->name('return-policies.index');
        Route::post('/return-policies/fetch-for-datatable', [ReturnPolicyController::class, 'fetchForDatatable'])->middleware('can:view return policy')->name('return-policies.fetch-for-datatable');
        Route::get('/return-policies/create', [ReturnPolicyController::class, 'create'])->middleware('can:add return policy')->name('return-policies.create');
        Route::post('/return-policies', [ReturnPolicyController::class, 'store'])->middleware('can:add return policy')->name('return-policies.store');
        Route::get('/return-policies/{returnPolicy}/edit', [ReturnPolicyController::class, 'edit'])->middleware('can:edit return policy')->name('return-policies.edit');
        Route::post('/return-policies/{returnPolicy}', [ReturnPolicyController::class, 'update'])->middleware('can:edit return policy')->name('return-policies.update');
        Route::post('/return-policies/delete/{returnPolicy}', [ReturnPolicyController::class, 'destroy'])->middleware('can:delete return policy')->name('return-policies.destroy');

        // ------------------------------------
        // Order Product Shipping Details
        // ------------------------------------
        // ------------------------------------
        // Order Product Shipping Details
        // ------------------------------------
        Route::post('/order-product-shipping-details/store', [OrderProductShippingDetailController::class, 'store'])->middleware('can:edit order')->name('order-product-shipping-details.store');

        // Order Cancellation Requests (Admin)
        Route::group(['prefix' => 'order-cancellation-requests', 'as' => 'order-cancellation-requests.'], function () {
            Route::get('/', [OrderCancellationRequestController::class, 'index'])->middleware('can:view order cancellation request')->name('index');
            Route::post('fetch-data', [OrderCancellationRequestController::class, 'fetchForDatatable'])->middleware('can:view order cancellation request')->name('fetch-data');
            Route::post('message/store', [OrderCancellationRequestController::class, 'storeMessage'])->middleware('can:edit order cancellation request')->name('message.store');
            Route::post('status/update/{id}', [OrderCancellationRequestController::class, 'updateStatus'])->middleware('can:edit order cancellation request')->name('status.update');
        });

        // Refunds
        Route::group(['prefix' => 'refunds', 'as' => 'refunds.'], function () {
            Route::get('/', [RefundController::class, 'index'])->middleware('can:view refund')->name('index');
            Route::get('fetch-data', [RefundController::class, 'fetchForDatatable'])->middleware('can:view refund')->name('fetch-for-datatable');
            Route::get('fetch-one/{id}', [RefundController::class, 'fetchOne'])->middleware('can:view refund')->name('fetch-one');
            Route::post('update-status/{id}', [RefundController::class, 'updateStatus'])->middleware('can:edit refund')->name('update-status');
        });

        // ------------------------------------
        // Newsletter
        // ------------------------------------
        Route::get('/newsletters', [SubscribeNewsletterController::class, 'index'])->middleware('can:view newsletter')->name('newsletters.index');
        Route::post('/newsletters/fetch-for-datatable', [SubscribeNewsletterController::class, 'fetchForDatatable'])->middleware('can:view newsletter')->name('newsletters.fetch-for-datatable');
        Route::post('/newsletters/delete/{newsletter}', [SubscribeNewsletterController::class, 'destroy'])->middleware('can:delete newsletter')->name('newsletters.destroy');

        // ------------------------------------
        // Customer Reviews
        // ------------------------------------
        Route::group(['prefix' => 'customer-reviews', 'as' => 'customer-reviews.'], function () {
            Route::get('/', [CustomerReviewController::class, 'index'])->middleware('can:view customer review')->name('index');
            Route::post('fetch-data', [CustomerReviewController::class, 'fetchForDatatable'])->middleware('can:view customer review')->name('fetch-for-datatable');
            Route::post('delete/{id}', [CustomerReviewController::class, 'destroy'])->middleware('can:delete customer review')->name('destroy');
            Route::post('update-status/{id}', [CustomerReviewController::class, 'updateStatus'])->middleware('can:edit customer review')->name('update-status');
        });
    });

    // ------------------------------------
    // User routes moved to user.php
    // ------------------------------------


    Route::post('captcha/refresh', [CaptchaController::class, 'refresh'])->name('captcha.refresh');

    Route::middleware('throttle:40,1')->group(function () {
        // Check session ping
        Route::post('/session/ping', [LogoutController::class, 'checkSession'])->middleware(['auth', 'sessionTimeout'])->name('session.ping');
        Route::get('/logout-other-devices', [LogoutController::class, 'logOutOtherDevices'])->name('logout.other-devices');
    });

    Route::middleware('throttle:15,1')->group(function () {
        Route::post('/login/check', [LoginController::class, 'checkLogin'])->name('login.check');
    });

    Route::middleware(['throttle:20,2'])->group(function () {
        Route::post('/login/verify-otp', [LoginController::class, 'verifyOtp'])->name('login.verify-otp');
    });

    Route::middleware('throttle:10,2')->group(function () {
        Route::post('/login/resend-otp', [LoginController::class, 'resendOtp'])->name('login.resend-otp');
    });

    // Reset Password Routes
    Route::get('/reset-password/{token}', [LoginController::class, 'showResetPasswordForm'])->name('backend-reset-password.form');
    Route::middleware('throttle:10,10')->group(function () {
        Route::post('/reset-password/update', [LoginController::class, 'updatePassword'])->name('backend-reset-password.update');
    });

    Route::get('/secure-login', [LoginController::class, 'index'])->name('login');

    // ===============================================================
    // Fetch routes for public parts
    // ===============================================================
    Route::post('/states/country/{countryId}', [StateController::class, 'fetchUsingCountry'])->name('states.fetch-by-country');
});

// User routes
require base_path('routes/user.php');

// Website routes
require base_path('routes/website.php');
