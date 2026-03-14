@extends('layouts.app_layout')

@section('content')
<div class="row">
    <!-- Metric Cards -->
    <!-- Metric Cards -->
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="dashboard-card-white">
            <div class="card-header-flex">
                <h5 class="card-title">NEW ORDERS</h5>
                <div class="card-icon icon-primary">
                    <i class="ti ti-shopping-cart"></i>
                </div>
            </div>
            <h3 class="card-count">{{ $data['new_orders'] }}</h3>
        </div>
    </div>
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="dashboard-card-white">
            <div class="card-header-flex">
                <h5 class="card-title">CANCELLATION REQS</h5>
                <div class="card-icon icon-danger">
                    <i class="ti ti-ban"></i>
                </div>
            </div>
            <h3 class="card-count">{{ $data['new_cancellation_requests'] }}</h3>
        </div>
    </div>
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="dashboard-card-white">
            <div class="card-header-flex">
                <h5 class="card-title">RETURN REQS</h5>
                <div class="card-icon icon-warning">
                    <i class="ti ti-arrow-back-up"></i>
                </div>
            </div>
            <h3 class="card-count">{{ $data['new_return_requests'] }}</h3>
        </div>
    </div>
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="dashboard-card-white">
            <div class="card-header-flex">
                <h5 class="card-title">TOTAL PRODUCTS</h5>
                <div class="card-icon icon-success">
                    <i class="ti ti-box"></i>
                </div>
            </div>
            <h3 class="card-count">{{ $data['total_products'] }}</h3>
        </div>
    </div>

    <!-- Second Row Stats -->
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="dashboard-card-white">
            <div class="card-header-flex">
                <h5 class="card-title">TOTAL CATEGORIES</h5>
                <div class="card-icon icon-info">
                    <i class="ti ti-list"></i>
                </div>
            </div>
            <h3 class="card-count">{{ $data['total_categories'] }}</h3>
        </div>
    </div>
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="dashboard-card-white">
            <div class="card-header-flex">
                <h5 class="card-title">TOTAL ORDERS</h5>
                <div class="card-icon icon-dark">
                    <i class="ti ti-shopping-cart"></i>
                </div>
            </div>
            <h3 class="card-count">{{ $data['total_orders'] }}</h3>
        </div>
    </div>
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="dashboard-card-white">
            <div class="card-header-flex">
                <h5 class="card-title">DELIVERED ORDERS</h5>
                <div class="card-icon icon-success">
                    <i class="ti ti-truck-delivery"></i>
                </div>
            </div>
            <h3 class="card-count">{{ $data['delivered_orders'] }}</h3>
        </div>
    </div>
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="dashboard-card-white">
            <div class="card-header-flex">
                <h5 class="card-title">MONTHLY REVENUE</h5>
                <div class="card-icon icon-primary">
                    <i class="ti ti-currency-rupee"></i>
                </div>
            </div>
            <h3 class="card-count">{{ number_format($data['current_month_revenue'], 2) }}</h3>
        </div>
    </div>

    <!-- Charts -->
    <div class="col-lg-12">
        <div class="card">
            <div class="card-header d-flex align-items-center justify-content-between">
                <h5>Monthly Revenue Analysis</h5>
                <select class="form-select form-select-sm w-auto" id="revenue-year-filter">
                    @for($i = date('Y'); $i >= date('Y') - 4; $i--)
                    <option value="{{ $i }}">{{ $i }}</option>
                    @endfor
                </select>
            </div>
            <div class="card-body">
                <div id="revenue-bar-chart"></div>
            </div>
        </div>
    </div>

    <div class="col-lg-6">
        <div class="card">
            <div class="card-header d-flex align-items-center justify-content-between">
                <h5>Monthly Orders Analysis</h5>
                <select class="form-select form-select-sm w-auto" id="orders-year-filter">
                    @for($i = date('Y'); $i >= date('Y') - 4; $i--)
                    <option value="{{ $i }}">{{ $i }}</option>
                    @endfor
                </select>
            </div>
            <div class="card-body">
                <div id="monthly-orders-bar-chart"></div>
            </div>
        </div>
    </div>

    <div class="col-lg-6">
        <div class="card">
            <div class="card-header">
                <h5>Yearly Orders Analysis</h5>
            </div>
            <div class="card-body">
                <div id="yearly-orders-bar-chart"></div>
            </div>
        </div>
    </div>

</div>
@endsection

@section('pages-scripts')
<!-- [Page Specific JS] start -->
<script src="{{ asset('assets/js/plugins/apexcharts.min.js') }}"></script>
<script @cspNonce>
    document.addEventListener("DOMContentLoaded", function() {

        // --- Revenue Chart ---
        var revenueChartOptions = {
            series: [{
                name: 'Revenue',
                data: []
            }],
            chart: {
                type: 'bar',
                height: 350,
                toolbar: {
                    show: false
                }
            },
            plotOptions: {
                bar: {
                    horizontal: false,
                    columnWidth: '55%',
                    endingShape: 'rounded'
                }
            },
            dataLabels: {
                enabled: false
            },
            stroke: {
                show: true,
                width: 2,
                colors: ['transparent']
            },
            xaxis: {
                categories: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec']
            },
            yaxis: {
                title: {
                    text: 'Amount'
                }
            },
            fill: {
                opacity: 1
            },
            tooltip: {
                y: {
                    formatter: function(val) {
                        return "$ " + val
                    }
                }
            },
            colors: ['#4680ff']
        };
        var revenueChart = new ApexCharts(document.querySelector("#revenue-bar-chart"), revenueChartOptions);
        revenueChart.render();

        function fetchRevenueData(year) {
            fetch("{{ route('admin.dashboard.revenue-chart') }}?year=" + year)
                .then(response => response.json())
                .then(data => {
                    revenueChart.updateSeries([{
                        data: data.data
                    }]);
                });
        }

        document.getElementById('revenue-year-filter').addEventListener('change', function() {
            fetchRevenueData(this.value);
        });
        fetchRevenueData('{{ date("Y") }}'); // Initial Load


        // --- Monthly Orders Chart ---
        var monthlyOrdersOptions = {
            series: [{
                name: 'Orders',
                data: []
            }],
            chart: {
                type: 'bar',
                height: 350,
                toolbar: {
                    show: false
                }
            },
            plotOptions: {
                bar: {
                    horizontal: false,
                    columnWidth: '55%',
                    endingShape: 'rounded'
                }
            },
            dataLabels: {
                enabled: false
            },
            colors: ['#E58A00'],
            xaxis: {
                categories: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec']
            }
        };
        var monthlyOrdersChart = new ApexCharts(document.querySelector("#monthly-orders-bar-chart"), monthlyOrdersOptions);
        monthlyOrdersChart.render();

        function fetchMonthlyOrdersData(year) {
            fetch("{{ route('admin.dashboard.orders-chart') }}?year=" + year)
                .then(response => response.json())
                .then(data => {
                    monthlyOrdersChart.updateSeries([{
                        data: data.data
                    }]);
                });
        }

        document.getElementById('orders-year-filter').addEventListener('change', function() {
            fetchMonthlyOrdersData(this.value);
        });
        fetchMonthlyOrdersData('{{ date("Y") }}');


        // --- Yearly Orders Chart ---
        var yearlyOrdersOptions = {
            series: [{
                name: 'Orders',
                data: []
            }],
            chart: {
                type: 'bar',
                height: 350,
                toolbar: {
                    show: false
                }
            },
            colors: ['#2ca87f'],
            plotOptions: {
                bar: {
                    borderRadius: 4,
                    horizontal: true,
                }
            },
            dataLabels: {
                enabled: false
            },
            xaxis: {
                categories: []
            }
        };
        var yearlyOrdersChart = new ApexCharts(document.querySelector("#yearly-orders-bar-chart"), yearlyOrdersOptions);
        yearlyOrdersChart.render();

        fetch("{{ route('admin.dashboard.yearly-orders-chart') }}")
            .then(response => response.json())
            .then(data => {
                yearlyOrdersChart.updateOptions({
                    xaxis: {
                        categories: data.labels
                    }
                });
                yearlyOrdersChart.updateSeries([{
                    data: data.data
                }]);
            });
    });
</script>
<!-- [Page Specific JS] end -->
@endsection