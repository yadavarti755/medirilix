@extends('layouts.app_layout')

@section('content')
<!-- [ Page Header ] start -->
@php
$button = '<a href="'.route('orders.index').'" class="btn btn-secondary btn-sm"><i class="fas fa-arrow-left"></i> Back to Orders</a>';
@endphp
<x-page-header title="{{ $pageTitle }}" :button="$button" />
<!-- [ Page Header ] end -->

<div class="row">
    <div class="col-12">
        <ul class="nav nav-tabs mb-3" id="mainOrderTabs" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link active" id="order-details-tab" data-bs-toggle="tab" data-bs-target="#order-details" type="button" role="tab" aria-controls="order-details" aria-selected="true">Order Details</button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="ordered-products-tab" data-bs-toggle="tab" data-bs-target="#ordered-products" type="button" role="tab" aria-controls="ordered-products" aria-selected="false">Ordered Products</button>
            </li>
        </ul>

        <div class="tab-content" id="mainOrderTabsContent">
            <!-- Tab 1: Order Details -->
            <div class="tab-pane fade show active" id="order-details" role="tabpanel" aria-labelledby="order-details-tab">
                <div class="row">
                    <div class="col-lg-6">
                        <!-- Order Information -->
                        <div class="card mb-3">
                            <div class="card-header">
                                <h5 class="mb-0">Order Information</h5>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <h6 class="text-muted">Order Number</h6>
                                        <p class="fw-bold">{{ $order->order_number }}</p>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <h6 class="text-muted">Order Date</h6>
                                        <p class="fw-bold">{{ \Carbon\Carbon::parse($order->order_date)->format('d M, Y') }}</p>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <h6 class="text-muted">Payment Type</h6>
                                        <p class="fw-bold">
                                            @if(strtoupper($order->payment_type) == Config::get('constants.payment_options')['ONLINE'])
                                            <span class="badge bg-success">Online</span>
                                            @elseif(strtoupper($order->payment_type) == Config::get('constants.payment_options')['PAYPAL'])
                                            <span class="badge bg-success">PayPal</span>
                                            @elseif(strtoupper($order->payment_type) == Config::get('constants.payment_options')['RAZORPAY'])
                                            <span class="badge bg-success">Razorpay</span>
                                            @else
                                            <span class="badge bg-warning text-dark">COD</span>
                                            @endif
                                        </p>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <h6 class="text-muted">Payment Status</h6>
                                        <p class="fw-bold">
                                            @php
                                            $paymentStatus = $order->payment_status;
                                            $paymentColor = $paymentStatus instanceof \App\Enums\PaymentStatus ? $paymentStatus->color() : 'bg-secondary';
                                            $paymentLabel = $paymentStatus instanceof \App\Enums\PaymentStatus ? $paymentStatus->label() : $paymentStatus;
                                            @endphp
                                            <span class="badge {{ $paymentColor }}">
                                                {{ $paymentLabel }}
                                            </span>
                                        </p>
                                    </div>
                                    <div class="col-md-6">
                                        <h6 class="text-muted">Order Status</h6>
                                        <p class="fw-bold">
                                            @php
                                            $orderStatus = $order->order_status;
                                            $statusColor = $orderStatus instanceof \App\Enums\OrderStatus ? $orderStatus->color() : 'bg-secondary';
                                            $statusLabel = $orderStatus instanceof \App\Enums\OrderStatus ? $orderStatus->label() : $orderStatus;
                                            @endphp
                                            <span class="badge {{ $statusColor }}">
                                                {{ $statusLabel }}
                                            </span>
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Change Order Status -->
                        <div class="card mb-3">
                            <div class="card-header">
                                <h5 class="mb-0">Change Order Status</h5>
                            </div>
                            <div class="card-body">
                                <form action="{{ route('orders.change-status', Crypt::encryptString($order->id)) }}" method="POST" id="form_change_order_status">
                                    @csrf
                                    <div class="mb-3">
                                        <label for="order_change_status" class="form-label">Order Status</label>
                                        <select class="form-select" id="order_change_status" name="order_status" required>
                                            <option value="" selected disabled>Select Status</option>
                                            @foreach(\App\Enums\OrderStatus::cases() as $status)
                                            <option value="{{ $status->value }}" {{ $order->order_status === $status ? 'selected' : '' }}>{{ $status->label() }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="mb-3">
                                        <label for="remarks" class="form-label">Remarks (Optional)</label>
                                        <textarea class="form-control" id="remarks" name="remarks" rows="3"></textarea>
                                    </div>
                                    <button type="submit" class="btn btn-primary w-100">Update Status</button>
                                </form>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-6">
                        <!-- Shipping Address -->
                        <div class="card mb-3">
                            <div class="card-header">
                                <h5 class="mb-0">Shipping Details</h5>
                            </div>
                            <div class="card-body">
                                @if($orderAddress)
                                <p class="fw-bold mb-1">{{ $orderAddress->person_name }}</p>
                                <p class="mb-1">{{ $orderAddress->address }}</p>
                                <p class="mb-1">{{ $orderAddress->city }}, {{ $orderAddress->state ?? '' }}</p>
                                <p class="mb-1">{{ $orderAddress->country ?? '' }} - {{ $orderAddress->pincode }}</p>
                                <hr>
                                <p class="mb-1"><i class="fas fa-phone-alt me-2"></i> {{ $orderAddress->person_contact_number }}</p>
                                @if($orderAddress->person_alt_contact_number)
                                <p class="mb-1"><i class="fas fa-phone me-2"></i> {{ $orderAddress->person_alt_contact_number }} (Alt)</p>
                                @endif
                                @else
                                <p class="text-muted">No shipping address found.</p>
                                @endif
                            </div>
                        </div>

                        <!-- Order History -->
                        <div class="card mb-3">
                            <div class="card-header">
                                <h5 class="mb-0">Order History</h5>
                            </div>
                            <div class="card-body p-0">
                                <div class="table-responsive">
                                    <table class="table table-hover mb-0">
                                        <thead>
                                            <tr>
                                                <th>Status</th>
                                                <th>Date</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @forelse($orderTrackingStatus as $history)
                                            <tr>
                                                <td>
                                                    @php
                                                    $historyStatus = \App\Enums\OrderStatus::tryFrom($history->order_status) ?? $history->order_status;
                                                    $historyColor = $historyStatus instanceof \App\Enums\OrderStatus ? $historyStatus->color() : 'bg-secondary';
                                                    $historyLabel = $historyStatus instanceof \App\Enums\OrderStatus ? $historyStatus->label() : $history->order_status;
                                                    @endphp
                                                    <span class="badge {{ $historyColor }}">
                                                        {{ $historyLabel }}
                                                    </span>
                                                    @if($history->remarks)
                                                    <br><small class="text-muted">{{ $history->remarks }}</small>
                                                    @endif
                                                </td>
                                                <td>
                                                    <small>{{ \Carbon\Carbon::parse($history->status_changed_date)->format('d M, Y h:i A') }}</small>
                                                </td>
                                            </tr>
                                            @empty
                                            <tr>
                                                <td colspan="2" class="text-center">No history available.</td>
                                            </tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Totals Table -->
                    <div class="col-12">
                        <div class="card mb-3">
                            <div class="card-header">
                                <h5 class="mb-0">Order Totals</h5>
                            </div>
                            <div class="card-body p-0">
                                <table class="table table-bordered mb-0">
                                    <tfoot>
                                        <tr>
                                            <td class="text-end fw-bold">Subtotal</td>
                                            <td class="text-end" width="20%">{{ $siteSettings->currency->symbol }}{{ number_format($order->subtotal_price, 2) }}</td>
                                        </tr>
                                        @if($order->discount_amount > 0)
                                        <tr>
                                            <td class="text-end fw-bold text-success">Coupon Discount ({{ $order->coupon_code }})</td>
                                            <td class="text-end text-success">- {{ $siteSettings->currency->symbol }}{{ number_format($order->discount_amount, 2) }}</td>
                                        </tr>
                                        @endif
                                        @if($order->shipping_charges > 0 || $order->tax_amount > 0)
                                        <tr>
                                            <td class="text-end fw-bold">Shipping Charges</td>
                                            <td class="text-end">{{ $siteSettings->currency->symbol }}{{ number_format($order->shipping_charges, 2) }}</td>
                                        </tr>
                                        <tr>
                                            <td class="text-end fw-bold">Tax</td>
                                            <td class="text-end">{{ $siteSettings->currency->symbol }}{{ number_format($order->tax_amount, 2) }}</td>
                                        </tr>
                                        @else
                                        <tr>
                                            <td class="text-end fw-bold">Additional Charges (Shipping/Tax)</td>
                                            <td class="text-end">{{ $siteSettings->currency->symbol }}{{ number_format($order->additional_charges, 2) }}</td>
                                        </tr>
                                        @endif
                                        <tr>
                                            <td class="text-end fw-bold">Grand Total</td>
                                            <td class="text-end fw-bold fs-5 text-primary">{{ $siteSettings->currency->symbol }}{{ number_format($order->total_price, 2) }}</td>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Tab 2: Ordered Products -->
            <div class="tab-pane fade" id="ordered-products" role="tabpanel" aria-labelledby="ordered-products-tab">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">Product Details</h5>
                    </div>
                    <div class="card-body">
                        <div class="accordion" id="productAccordion">
                            @foreach($orderProductsList as $index => $item)
                            <div class="accordion-item">
                                <h2 class="accordion-header" id="headingProduct-{{ $item->id }}">
                                    <button
                                        class="accordion-button {{ $index != 0 ? 'collapsed' : '' }}"
                                        type="button"
                                        data-bs-toggle="collapse"
                                        data-bs-target="#collapseProduct-{{ $item->id }}"
                                        aria-expanded="{{ $index == 0 ? 'true' : 'false' }}"
                                        aria-controls="collapseProduct-{{ $item->id }}">
                                        {{ $item->product_name }}

                                        @php
                                        $pendingCount = $item->cancellationRequests->where('status', 'Pending')->count();
                                        @endphp

                                        @if($pendingCount > 0)
                                        <span class="badge bg-danger rounded-pill ms-2">{{ $pendingCount }}</span>
                                        @endif
                                    </button>
                                </h2>

                                <div
                                    id="collapseProduct-{{ $item->id }}"
                                    class="accordion-collapse collapse {{ $index == 0 ? 'show' : '' }}"
                                    aria-labelledby="headingProduct-{{ $item->id }}"
                                    data-bs-parent="#productAccordion">
                                    <div class="accordion-body p-3">

                                        <!-- Product Details -->
                                        <div class="row align-items-center mb-3">
                                            <div class="col-md-1">
                                                <img src="{{ asset('storage/' . Config::get('file_paths')['PRODUCT_IMAGE_PATH'] . '/' . $item->product_featured_image) }}"
                                                    alt="Product Image"
                                                    class="img-thumbnail"
                                                    style="width: 60px; height: 60px; object-fit: cover;">
                                            </div>

                                            <div class="col-md-4">
                                                <p class="mb-0 fw-bold">{{ $item->product_name }}</p>
                                                @if($item->size)
                                                <small class="text-muted d-block">Size: {{ $item->size }}</small>
                                                @endif
                                                @if($item->material)
                                                <small class="text-muted d-block">Material: {{ $item->material }}</small>
                                                @endif
                                            </div>

                                            <div class="col-md-4 text-center">
                                                <span class="fw-bold text-primary d-block">
                                                    Price: {{ $siteSettings->currency->symbol }}{{ number_format($item->price, 2) }}
                                                </span>

                                                <span class="fw-bold d-block">Qty: {{ $item->quantity }}</span>

                                                @if($item->discount_amount > 0)
                                                <span class="d-block text-success" title="Coupon Discount applied on this item">
                                                    Discount: -{{ $siteSettings->currency->symbol }}{{ number_format($item->discount_amount, 2) }}
                                                </span>
                                                @endif

                                                <span class="d-block text-primary fw-bold">
                                                    Sub Total: {{ $siteSettings->currency->symbol }}{{ number_format($item->total_price - $item->discount_amount, 2) }}
                                                </span>
                                            </div>

                                            <div class="col-md-2 text-end">
                                                @php
                                                $itemStatus = $item->product_order_status;
                                                $itemColor = $itemStatus instanceof \App\Enums\OrderStatus ? $itemStatus->color() : 'bg-secondary';
                                                $itemLabel = $itemStatus instanceof \App\Enums\OrderStatus ? $itemStatus->label() : ($itemStatus ?? 'PENDING');
                                                @endphp

                                                <span class="badge {{ $itemColor }} mb-2 d-block py-2">
                                                    {{ $itemLabel }}
                                                </span>

                                                <button
                                                    type="button"
                                                    class="btn btn-xs btn-outline-primary btn_change_product_status"
                                                    data-id="{{ Crypt::encryptString($item->id) }}"
                                                    data-shipment-details="{{ $item->shippingDetail ? $item->shippingDetail->shipping_details : '' }}"
                                                    data-dhl-tracking-id="{{ $item->shippingDetail ? $item->shippingDetail->dhl_tracking_id : '' }}"
                                                    data-current-status="{{ $itemStatus instanceof \App\Enums\OrderStatus ? $itemStatus->value : $itemStatus }}">
                                                    Change Status / Details
                                                </button>
                                            </div>
                                        </div>

                                        @if($item->shippingDetail)
                                        <div class="alert alert-light border py-2">
                                            <div>
                                                <div>
                                                    <span class="fw-bold text-dark me-2">Shipment Details:</span>
                                                    <div class="text-muted">{{ $item->shippingDetail->shipping_details }}</div>
                                                </div>
                                                @if($item->shippingDetail->dhl_tracking_id)
                                                <div class="mt-2">
                                                    <span class="fw-bold text-dark me-2">DHL Tracking ID:</span>
                                                    <span class="text-primary fw-bold">{{ $item->shippingDetail->dhl_tracking_id }}</span>
                                                    <a href="https://www.dhl.com/en/express/tracking.html?AWB={{ $item->shippingDetail->dhl_tracking_id }}" target="_blank" class="btn btn-info btn-xs ms-2">
                                                        <i class="fas fa-external-link-alt"></i> Track on DHL
                                                    </a>
                                                </div>
                                                @if(isset($item->dhl_tracking_data) && $item->dhl_tracking_data)
                                                <div class="mt-2 p-2 bg-white border rounded">
                                                    <h6 class="mb-1 text-primary"><i class="fas fa-shipping-fast"></i> DHL Status</h6>
                                                    @php
                                                        $shipment = $item->dhl_tracking_data['shipments'][0] ?? null;
                                                        $status = $shipment['status']['status'] ?? 'Unknown';
                                                        $location = $shipment['status']['location']['address']['addressLocality'] ?? '';
                                                        $updateTime = isset($shipment['status']['timestamp']) ? \Carbon\Carbon::parse($shipment['status']['timestamp'])->format('d M, Y h:i A') : '';
                                                    @endphp
                                                    <div class="small">
                                                        <strong>Status:</strong> {{ $status }} <br>
                                                        @if($location) <strong>Location:</strong> {{ $location }} <br> @php @endphp @endif
                                                        @if($updateTime) <strong>Last Update:</strong> {{ $updateTime }} @php @endphp @endif
                                                    </div>
                                                </div>
                                                @endif
                                                @endif
                                                @if($item->shippingDetail->shipment_photo)
                                                <div class="mt-2">
                                                    <a href="{{ $item->shippingDetail->shipment_photo_full_path }}" target="_blank" class="btn btn-secondary btn-sm">
                                                        <i class="fas fa-image"></i> View Photo
                                                    </a>
                                                </div>
                                                @endif
                                            </div>
                                        </div>
                                        @endif

                                        <hr>

                                        <!-- Cancellation Request Section (Admin View) -->
                                        <div class="cancellation-request-section">
                                            @if($item->cancellationRequests->count() > 0)
                                            <h6 class="fw-bold text-danger mb-2">
                                                <i class="fas fa-exclamation-circle me-1"></i> Cancellation Requests
                                            </h6>

                                            <div class="accordion" id="accordionCancelAdmin-{{ $item->id }}">
                                                @foreach($item->cancellationRequests as $reqIndex => $req)
                                                {{-- existing inner accordion unchanged --}}
                                                @endforeach
                                            </div>
                                            @endif
                                        </div>

                                    </div>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Change Product Status Modal -->
<div class="modal fade" id="changeProductStatusModal" tabindex="-1" aria-labelledby="changeProductStatusModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="changeProductStatusModalLabel">Change Product Status</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="form_change_product_status">
                    <input type="hidden" name="product_list_id" id="modal_product_list_id">

                    <div class="mb-3">
                        <label for="order_status" class="form-label">Order Status</label>
                        <select class="form-select" id="order_status" name="order_status">
                            <option value="" selected disabled>Select Status</option>
                            @foreach(\App\Enums\OrderStatus::cases() as $status)
                            <option value="{{ $status->value }}">{{ $status->label() }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="shipment_photos" class="form-label">Shipment Photos</label>
                        <input class="form-control" type="file" id="shipment_photos" name="shipment_photos">
                    </div>

                    <div class="mb-3">
                        <label for="shipment_details" class="form-label">Shipment Details</label>
                        <textarea class="form-control" id="shipment_details" name="shipment_details" rows="3"></textarea>
                    </div>

                    <div class="mb-3">
                        <label for="dhl_tracking_id" class="form-label text-primary fw-bold">DHL Tracking ID</label>
                        <input type="text" class="form-control border-primary" id="dhl_tracking_id" name="dhl_tracking_id" placeholder="Enter DHL Tracking Number">
                        <small class="text-muted">Enter this to enable automated DHL tracking for this product.</small>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" id="btn_save_product_status">Save changes</button>
            </div>
        </div>
    </div>
</div>
@endsection

@section('pages-scripts')
<script @cspNonce>
    $('.btn_change_product_status').click(function(event) {
        var productId = $(this).data('id');
        var shipmentDetails = $(this).data('shipment-details');
        var dhlTrackingId = $(this).data('dhl-tracking-id');
        var currentStatus = $(this).data('current-status');

        $('#form_change_product_status').trigger('reset');
        $('#modal_product_list_id').val(productId);

        // Pre-fill form
        if (currentStatus) {
            $('#order_status').val(currentStatus);
        }
        if (shipmentDetails) {
            $('#shipment_details').val(shipmentDetails);
        }
        if (dhlTrackingId) {
            $('#dhl_tracking_id').val(dhlTrackingId);
        }

        $('#changeProductStatusModal').modal('show');
    })

    $('#btn_save_product_status').click(function() {
        var formData = new FormData(document.getElementById("form_change_product_status"));

        var status = $('#order_status').val();
        if (!status) {
            toastr.error('Please select order status.');
            return;
        }

        $.ajax({
            url: "{{ route('order-product-shipping-details.store') }}",
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            beforeSend: function() {
                $('#btn_save_product_status').prop('disabled', true);
            },
            success: function(response) {
                if (response.success) {
                    // Reset form
                    $('#form_change_product_status').trigger('reset');
                    Swal.fire({
                        icon: 'success',
                        title: 'Success',
                        text: response.message,
                        confirmButtonText: 'OK'
                    }).then((result) => {
                        window.location.reload();
                    });
                    $('#changeProductStatusModal').modal('hide');
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: response.message
                    });
                }
            },
            error: function(xhr) {
                var errorMessage = 'Something went wrong';
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    errorMessage = xhr.responseJSON.message;
                }
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: errorMessage
                });
            },
            complete: function() {
                $('#btn_save_product_status').prop('disabled', false);
            }
        });
    });

    $('#form_change_order_status').submit(function(e) {
        e.preventDefault();
        var form = $(this);
        var url = form.attr('action');
        var formData = new FormData(this);

        $.ajax({
            url: url,
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            beforeSend: function() {
                form.find('button[type="submit"]').prop('disabled', true).text('Updating...');
            },
            success: function(response) {
                if (response.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Success',
                        text: response.message,
                        confirmButtonText: 'OK'
                    }).then((result) => {
                        window.location.reload();
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: response.message
                    });
                }
            },
            error: function(xhr) {
                var errorMessage = 'Something went wrong';
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    errorMessage = xhr.responseJSON.message;
                }
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: errorMessage
                });
            },
            complete: function() {
                form.find('button[type="submit"]').prop('disabled', false).text('Update Status');
            }
        });
    });

    // Send Message (Admin)
    $(document).on('submit', '.form-send-message', function(e) {
        e.preventDefault();
        var form = $(this);
        var requestId = form.data('request-id');
        var formData = new FormData(this);
        formData.append('order_cancellation_request_id', requestId);

        $.ajax({
            url: "{{ route('order-cancellation-requests.message.store') }}",
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                if (response.success) {
                    var chatBox = $('#chat-box-' + requestId);
                    if (chatBox.length === 0) {
                        window.location.reload();
                        return;
                    }
                    chatBox.append(response.html);
                    chatBox.scrollTop(chatBox[0].scrollHeight);
                    form.trigger('reset');
                } else {
                    toastr.error(response.message);
                }
            },
            error: function() {
                toastr.error('Failed to send message');
            }
        });
    });

    // Close Request (Admin)
    $(document).on('click', '.btn-close-request', function() {
        var requestId = $(this).data('id');
        Swal.fire({
            title: 'Are you sure?',
            text: "Do you want to close this cancellation request?",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes, close it!'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: "{{ route('order-cancellation-requests.status.update', ':id') }}".replace(':id', requestId),
                    type: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}',
                        status: 'Closed'
                    },
                    success: function(response) {
                        if (response.success) {
                            Swal.fire(
                                'Closed!',
                                response.message,
                                'success'
                            ).then(() => {
                                window.location.reload();
                            });
                        } else {
                            Swal.fire('Error!', response.message, 'error');
                        }
                    },
                    error: function() {
                        Swal.fire('Error!', 'Something went wrong.', 'error');
                    }
                });
            }
        });
    });
</script>
@endsection