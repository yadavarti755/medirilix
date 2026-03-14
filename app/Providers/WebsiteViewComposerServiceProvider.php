<?php

namespace App\Providers;

use Auth;
use App\Models\Category;
use App\Models\ContactDetail;
use App\Models\Menu;
use App\Models\SocialMedia;
use App\Models\VisitorStat;
use App\Services\PaymentMethodService;
use App\Services\WishlistService;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\ServiceProvider;

class WebsiteViewComposerServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        // Bind the 'frontend.menu' data to the frontend layout
        view()->composer('*', function ($view) {
            $userWishlistIDs = [];

            if (Auth::check()) {
                $wishlistService = new WishlistService();
                $userWishlists = $wishlistService->findAll([
                    'user_id' => Auth::user()->id,
                ]);
                $userWishlists = $userWishlists->toArray();
                $userWishlistIDs = array_column($userWishlists, 'product_id');
            }
            $view->with('userWishlistIDs', $userWishlistIDs);
        });

        view()->composer('layouts.website_layout', function ($view) {
            $menus = Menu::getHeaderParentMenus();
            $contactDetail = ContactDetail::getPrimaryContactDetails();
            $socialLinks = SocialMedia::getSocialMediaLinks();
            $footerMenus = Menu::getFooterParentMenus();
            $quickLinks = Menu::getQuickLinksMenus();
            $informationMenus = Menu::getInformationMenus();

            $visitorCount = Cache::remember('visitor_total_count', 300, function () {
                return VisitorStat::value('total_count') ?? 0;
            });

            $categories = Category::get();

            $paymentMethodService = new PaymentMethodService();
            $paymentMethods = $paymentMethodService->findForPublic();

            $view->with('menus', $menus);
            $view->with('contactDetail', $contactDetail);
            $view->with('socialLinks', $socialLinks);
            $view->with('footerMenus', $footerMenus);
            $view->with('quickLinks', $quickLinks);
            $view->with('informationMenus', $informationMenus);
            $view->with('visitorCount', $visitorCount);
            $view->with('categories', $categories);
            $view->with('paymentMethods', $paymentMethods);

            $view->with('cartCount', count(session('cart', [])));
        });

        view()->composer('components.website.page-header', function ($view) {
            $currentUrl = url()->current();
            $menusForBreadcrumb = Menu::getAllParentMenus();
            $breadcrumb = findMenuPath($menusForBreadcrumb, $currentUrl);
            $view->with('breadcrumb', $breadcrumb);
        });
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}
