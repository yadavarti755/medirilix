<?php

namespace App\Http\Controllers\Secure;

use App\Http\Controllers\Controller;
use App\Services\DashboardService;
use App\Models\Order;
use App\Models\Wishlist;
use Illuminate\Http\Request;
use Carbon\Carbon;

class DashboardController extends Controller
{
    protected $service;

    public function __construct()
    {
        $this->service = new DashboardService();
    }

    public function userDashboard()
    {
        // Get the recent orders
        $user_id = auth()->user()->id;
        $recentOrders = Order::where('user_id', $user_id)->orderBy('id', 'desc')->take(5)->get();
        $pageTitle = 'Dashboard';

        $all_products = [];

        $userWishlists = Wishlist::where('user_id', $user_id)->get();

        $userWishlistIDs = $userWishlists->pluck('product_id')->toArray();

        return view('secure.dashboard.user', compact('recentOrders', 'pageTitle', 'userWishlistIDs', 'all_products', 'userWishlists'));
    }

    public function adminDashboard()
    {
        $data = $this->service->getAdminDashboardStats();
        $pageTitle = 'Dashboard';
        return view('secure.dashboard.admin', compact('data', 'pageTitle'));
    }

    public function getRevenueChartData(Request $request)
    {
        $year = $request->input('year', Carbon::now()->year);
        $result = $this->service->getRevenueChartData($year);
        return response()->json($result);
    }

    public function getOrdersChartData(Request $request)
    {
        $year = $request->input('year', Carbon::now()->year);
        $result = $this->service->getOrdersChartData($year);
        return response()->json($result);
    }

    public function getYearlyOrdersChartData()
    {
        $result = $this->service->getYearlyOrdersChartData();
        return response()->json($result);
    }
}
