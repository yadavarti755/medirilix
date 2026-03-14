<?php

namespace App\Repositories;

use App\Models\Order;
use App\Models\OrderCancellationRequest;
use App\Models\ReturnRequest;
use App\Models\Product;
use App\Models\Category;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DashboardRepository
{
    public function getNewOrdersCount()
    {
        return Order::where('order_status', 'PLACED')->count();
    }

    public function getNewCancellationRequestsCount()
    {
        return OrderCancellationRequest::where('status', 'Pending')->count();
    }

    public function getNewReturnRequestsCount()
    {
        return ReturnRequest::where('return_status', 'RETURN_REQUESTED')->count();
    }

    public function getTotalProductsCount()
    {
        return Product::count();
    }

    public function getTotalCategoriesCount()
    {
        return Category::count();
    }

    public function getTotalOrdersCount()
    {
        return Order::count();
    }

    public function getDeliveredOrdersCount()
    {
        return Order::where('order_status', 'DELIVERED')->count();
    }

    public function getTotalRevenue()
    {
        return Order::where('order_status', 'DELIVERED')->sum('total_price');
    }

    public function getCurrentMonthRevenue()
    {
        return Order::where('order_status', 'DELIVERED')
            ->whereMonth('order_date', Carbon::now()->month)
            ->whereYear('order_date', Carbon::now()->year)
            ->sum('total_price');
    }

    public function getCurrentYearRevenue()
    {
        return Order::where('order_status', 'DELIVERED')
            ->whereYear('order_date', Carbon::now()->year)
            ->sum('total_price');
    }

    public function getRevenueChartData($year)
    {
        return Order::where('order_status', 'DELIVERED')
            ->whereYear('order_date', $year)
            ->select(
                DB::raw('sum(total_price) as sum'),
                DB::raw("DATE_FORMAT(order_date,'%m') as month")
            )
            ->groupBy('month')
            ->get();
    }

    public function getOrdersChartData($year)
    {
        return Order::whereYear('order_date', $year)
            ->select(
                DB::raw('count(id) as count'),
                DB::raw("DATE_FORMAT(order_date,'%m') as month")
            )
            ->groupBy('month')
            ->get();
    }

    public function getYearlyOrdersChartData($startYear)
    {
        $allowedStatuses = ['PLACED', 'PROCESSING', 'SHIPPED', 'DELIVERED'];

        return Order::whereYear('order_date', '>=', $startYear)
            ->whereIn('order_status', $allowedStatuses)
            ->select(
                DB::raw('count(id) as count'),
                DB::raw("DATE_FORMAT(order_date,'%Y') as year")
            )
            ->groupBy('year')
            ->orderBy('year', 'asc')
            ->get();
    }
}
