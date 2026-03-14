@extends('layouts.user_layout')

@section('content')
<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">

    <!-- Content Header (Page header) -->
    <div class="page-heading mb-4">
        <div class="container-fluid">
            <div class="row align-items-center">
                <div class="col-sm-12 col-12">
                    <h4 class="m-0 text-dark fw-bold page-title">{{$pageTitle}}</h4>
                    <p class="text-muted small mb-0">Overview of your account activity</p>
                </div><!-- /.col -->
            </div><!-- /.row -->
        </div><!-- /.container -->
    </div>
    <!-- /.content-header -->

    <!-- Main content -->
    <div class="content">
        <div class="container-fluid">
            <div class="row g-4 mb-4">
                <!-- Statistics Cards -->
                <div class="col-md-3 col-sm-6 col-12">
                    <div class="card h-100 border-0 shadow-sm card-hover">
                        <div class="card-body">
                            <div class="d-flex align-items-center justify-content-between mb-3">
                                <div>
                                    <h6 class="text-muted text-uppercase fw-semibold small mb-1">Total Orders</h6>
                                    <h2 class="mb-0 fw-bold text-dark">{{ count($recentOrders) }}</h2>
                                </div>
                                <div class="icon-box bg-primary-subtle text-primary rounded-circle" style="width: 48px; height: 48px; display: flex; align-items: center; justify-content: center;">
                                    <i class="fas fa-shopping-bag fa-lg"></i>
                                </div>
                            </div>
                            <span class="badge bg-success-subtle text-success rounded-pill px-2 py-1 small">
                                <i class="fas fa-arrow-up me-1"></i> Updated Now
                            </span>
                        </div>
                    </div>
                </div>

                <div class="col-md-3 col-sm-6 col-12">
                    <div class="card h-100 border-0 shadow-sm card-hover">
                        <div class="card-body">
                            <div class="d-flex align-items-center justify-content-between mb-3">
                                <div>
                                    <h6 class="text-muted text-uppercase fw-semibold small mb-1">Wishlist</h6>
                                    <h2 class="mb-0 fw-bold text-dark">{{ count($userWishlists) }}</h2>
                                </div>
                                <div class="icon-box bg-danger-subtle text-danger rounded-circle" style="width: 48px; height: 48px; display: flex; align-items: center; justify-content: center;">
                                    <i class="fas fa-heart fa-lg"></i>
                                </div>
                            </div>
                            <span class="badge bg-secondary-subtle text-secondary rounded-pill px-2 py-1 small">
                                <i class="fas fa-star me-1"></i> Saved Items
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Recent Orders -->
            <div class="row mt-4">
                <div class="col-12">
                    <div class="card shadow-sm border-0">
                        <div class="card-header border-bottom-0 bg-white py-3 d-flex justify-content-between align-items-center">
                            <h5 class="card-title fw-bold mb-0">Recent Orders</h5>
                            <a href="{{ route('user.orders') }}" class="btn btn-sm btn-outline-primary rounded-pill px-3">View All</a>
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-hover align-middle mb-0">
                                    <thead class="bg-light">
                                        <tr>
                                            <th class="ps-4 text-uppercase text-secondary small fw-bold">Order ID</th>
                                            <th class="text-uppercase text-secondary small fw-bold">Date</th>
                                            <th class="text-uppercase text-secondary small fw-bold">Status</th>
                                            <th class="text-uppercase text-secondary small fw-bold">Total</th>
                                            <th class="text-end pe-4 text-uppercase text-secondary small fw-bold">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody class="border-top-0">
                                        @forelse($recentOrders as $order)
                                        <tr>
                                            <td class="ps-4 fw-medium text-dark">#{{ $order->order_number }}</td>
                                            <td class="text-muted">{{ $order->created_at->format('d M, Y') }}</td>
                                            <td>
                                                @php
                                                $orderStatus = $order->order_status;
                                                $statusClass = $orderStatus instanceof \App\Enums\OrderStatus ? $orderStatus->color() : 'bg-secondary-subtle text-secondary';
                                                $statusLabel = $orderStatus instanceof \App\Enums\OrderStatus ? $orderStatus->label() : ($orderStatus ?? 'Unknown');
                                                @endphp
                                                <span class="badge rounded-pill {{ $statusClass }} px-3 py-2">
                                                    {{ $statusLabel }}
                                                </span>
                                            </td>
                                            <td class="fw-bold text-dark">₹{{ number_format($order->final_price, 2) }}</td>
                                            <td class="text-end pe-4">
                                                <a href="{{ route('user.orders.view', Crypt::encryptString($order->id)) }}" class="btn btn-sm btn-light text-primary rounded-circle shadow-sm" style="width: 32px; height: 32px; padding: 0; display: inline-flex; align-items: center; justify-content: center;" title="View Details">
                                                    <i class="fas fa-chevron-right"></i>
                                                </a>
                                            </td>
                                        </tr>
                                        @empty
                                        <tr>
                                            <td colspan="5" class="text-center py-5">
                                                <div class="d-flex flex-column align-items-center">
                                                    <div class="bg-light rounded-circle p-3 mb-3 text-secondary">
                                                        <i class="fas fa-box-open fa-2x"></i>
                                                    </div>
                                                    <p class="text-muted mb-0">No recent orders found.</p>
                                                </div>
                                            </td>
                                        </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div><!-- /.container-fluid -->
    </div>
    <!-- /.content -->
</div>
<!-- /.content-wrapper -->
@endsection