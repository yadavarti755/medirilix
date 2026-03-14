@extends('layouts.user_layout')

@section('content')
<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">

    <!-- Content Header (Page header) -->
    <div class="page-heading mb-4">
        <div class="container-fluid">
            <div class="row">
                <div class="col-sm-12 col-12">
                    <h4 class="m-0 text-dark fw-bold page-title">{{$pageTitle}}</h4>
                    <p class="text-muted small mb-0">Track and manage your recent purchases</p>
                </div><!-- /.col -->
            </div><!-- /.row -->
        </div><!-- /.container -->
    </div>
    <!-- /.content-header -->

    <!-- Main content -->
    <div class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    @forelse($orders as $order)
                    <div class="card mb-3 card-hover">
                        <div class="card-body p-4">
                            <div class="row align-items-center">
                                <div class="col-lg-3 col-md-4 mb-3 mb-md-0">
                                    <div class="d-flex align-items-center">
                                        <div class="icon-shape bg-primary-subtle text-primary rounded-circle p-3 me-3">
                                            <i class="fas fa-shopping-bag fa-lg"></i>
                                        </div>
                                        <div>
                                            <h6 class="text-muted text-uppercase fw-semibold small mb-1 ls-1">Order Number</h6>
                                            <h6 class="fw-bold mb-0 text-dark">#{{ $order->order_number }}</h6>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-lg-3 col-md-4 mb-3 mb-md-0">
                                    <h6 class="text-muted text-uppercase fw-semibold small mb-1 ls-1">Date Placed</h6>
                                    <p class="mb-0 fw-medium text-dark">{{ $order->created_at->format('d M, Y') }}</p>
                                    <small class="text-muted">{{ $order->created_at->format('h:i A') }}</small>
                                </div>

                                <div class="col-lg-2 col-md-4 mb-3 mb-md-0">
                                    <h6 class="text-muted text-uppercase fw-semibold small mb-1 ls-1">Total Amount</h6>
                                    <p class="mb-0 fw-bold text-dark fs-5">{{ $siteSettings->currency->symbol }}{{ number_format($order->total_price, 2) }}</p>
                                </div>

                                <div class="col-lg-2 col-md-6 mb-3 mb-md-0">
                                    <h6 class="text-muted text-uppercase fw-semibold small mb-1 ls-1">Status</h6>
                                    @php
                                    $orderStatus = $order->order_status;
                                    $statusColor = $orderStatus instanceof \App\Enums\OrderStatus ? $orderStatus->color() : 'bg-secondary';
                                    $statusLabel = $orderStatus instanceof \App\Enums\OrderStatus ? $orderStatus->label() : ($orderStatus ?? 'Unknown');
                                    @endphp
                                    <span class="badge rounded-pill px-3 py-2 {{ $statusColor }}">
                                        {{ $statusLabel }}
                                    </span>
                                </div>

                                <div class="col-lg-2 col-md-6 text-md-end">
                                    <a href="{{ route('user.orders.view', Crypt::encryptString($order->id)) }}"
                                        class="btn btn-outline-primary rounded-pill px-4 shadow-sm">
                                        Details <i class="fas fa-arrow-right ms-2"></i>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                    @empty
                    <div class="card shadow-none border border-dashed rounded-4 p-5 text-center bg-light">
                        <div class="card-body">
                            <div class="mb-4 text-muted">
                                <i class="fas fa-box-open fa-3x"></i>
                            </div>
                            <h4 class="fw-bold text-dark">No Orders Found</h4>
                            <p class="text-muted mb-4">You haven't placed any orders yet.</p>
                            <a href="{{ url('/') }}" class="btn btn-primary rounded-pill px-4">Start Shopping</a>
                        </div>
                    </div>
                    @endforelse

                    <!-- Pagination -->
                    @if($orders->hasPages())
                    <div class="d-flex justify-content-center mt-4">
                        <!-- Add pagination with bootstrap 5 -->
                        {{ $orders->links('pagination::bootstrap-5') }}
                    </div>
                    @endif
                </div>
            </div>
        </div><!-- /.container-fluid -->
    </div>
    <!-- /.content -->
</div>
<!-- /.content-wrapper -->
@endsection