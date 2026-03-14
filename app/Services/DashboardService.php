<?php

namespace App\Services;

use App\Repositories\DashboardRepository;
use Carbon\Carbon;

class DashboardService
{
    protected $repository;

    public function __construct()
    {
        $this->repository = new DashboardRepository();
    }

    public function getAdminDashboardStats()
    {
        return [
            'new_orders' => $this->repository->getNewOrdersCount(),
            'new_cancellation_requests' => $this->repository->getNewCancellationRequestsCount(),
            'new_return_requests' => $this->repository->getNewReturnRequestsCount(),
            'total_products' => $this->repository->getTotalProductsCount(),
            'total_categories' => $this->repository->getTotalCategoriesCount(),
            'total_orders' => $this->repository->getTotalOrdersCount(),
            'delivered_orders' => $this->repository->getDeliveredOrdersCount(),
            'total_revenue' => $this->repository->getTotalRevenue(),
            'current_month_revenue' => $this->repository->getCurrentMonthRevenue(),
            'current_year_revenue' => $this->repository->getCurrentYearRevenue(),
        ];
    }

    public function getRevenueChartData($year)
    {
        $revenues = $this->repository->getRevenueChartData($year);

        $monthlyRevenue = array_fill(0, 12, 0);
        foreach ($revenues as $revenue) {
            $index = intval($revenue->month) - 1;
            $monthlyRevenue[$index] = $revenue->sum;
        }

        return [
            'data' => $monthlyRevenue,
            'year' => $year
        ];
    }

    public function getOrdersChartData($year)
    {
        $orders = $this->repository->getOrdersChartData($year);

        $monthlyOrders = array_fill(0, 12, 0);
        foreach ($orders as $order) {
            $index = intval($order->month) - 1;
            $monthlyOrders[$index] = $order->count;
        }

        return [
            'data' => $monthlyOrders,
            'year' => $year
        ];
    }

    public function getYearlyOrdersChartData()
    {
        $currentYear = Carbon::now()->year;
        $startYear = $currentYear - 4; // Last 5 years

        $orders = $this->repository->getYearlyOrdersChartData($startYear);

        $years = [];
        $data = [];

        // Initialize with 0 for last 5 years
        for ($i = $startYear; $i <= $currentYear; $i++) {
            $years[] = $i;
            $data[] = 0;
        }

        foreach ($orders as $order) {
            $key = array_search($order->year, $years);
            if ($key !== false) {
                $data[$key] = $order->count;
            }
        }

        return [
            'labels' => $years,
            'data' => $data
        ];
    }
}
