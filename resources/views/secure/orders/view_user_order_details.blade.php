@extends('layouts.user_layout')

@section('content')
<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
    <!-- Main content -->
    <div class="content">
        <div class="container container-fixed-lg">
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
                                    <div class="card shadow border-0 mb-3">
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
                                                    </p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="card shadow border-0 mb-3">
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

                                <div class="col-lg-6">
                                    <!-- Shipping Address -->
                                    <div class="card shadow border-0 mb-3">
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
                                    <div class="card shadow border-0 mb-3">
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
                            </div>
                        </div>

                        <!-- Tab 2: Ordered Products -->
                        <div class="tab-pane fade" id="ordered-products" role="tabpanel" aria-labelledby="ordered-products-tab">
                            <div class="card shadow border-0">
                                <div class="card-header">
                                    <h5 class="mb-0">Product Details</h5>
                                </div>
                                <div class="card-body">
                                    <ul class="nav nav-tabs" id="productTabs" role="tablist">
                                        @foreach($orderProductsList as $index => $item)
                                        <li class="nav-item" role="presentation">
                                            <button class="nav-link {{ $index == 0 ? 'active' : '' }}" id="product-tab-{{ $item->id }}" data-bs-toggle="tab" data-bs-target="#product-content-{{ $item->id }}" type="button" role="tab" aria-controls="product-content-{{ $item->id }}" aria-selected="{{ $index == 0 ? 'true' : 'false' }}">
                                                {{ Str::limit($item->product_name, 20) }}
                                            </button>
                                        </li>
                                        @endforeach
                                    </ul>
                                    <div class="tab-content border-start border-end border-bottom p-3" id="productTabsContent">
                                        @foreach($orderProductsList as $index => $item)
                                        <div class="tab-pane fade {{ $index == 0 ? 'show active' : '' }}" id="product-content-{{ $item->id }}" role="tabpanel" aria-labelledby="product-tab-{{ $item->id }}">
                                            <!-- Product Details -->
                                            <div class="row align-items-center mb-3">
                                                <div class="col-md-2">
                                                    @if(file_exists(public_path('storage/' . Config::get('file_paths')['PRODUCT_IMAGE_PATH'] . '/' . $item->product_featured_image)))
                                                    <img src="{{ asset('storage/' . Config::get('file_paths')['PRODUCT_IMAGE_PATH'] . '/' . $item->product_featured_image) }}"
                                                        alt="Product Image"
                                                        class="img-thumbnail"
                                                        style="width: 80px; height: 80px; object-fit: cover;">
                                                    @else
                                                    <img src="{{ asset('storage/' . Config::get('file_paths')['PRODUCT_IMAGE_PATH'] . '/default.png') }}"
                                                        alt="Product Image"
                                                        class="img-thumbnail"
                                                        style="width: 80px; height: 80px; object-fit: cover;">
                                                    @endif
                                                </div>
                                                <div class="col-md-5">
                                                    <p class="mb-0 fw-bold">{{ $item->product_name }}</p>
                                                    @if($item->size)
                                                    <small class="text-muted d-block">Size: {{ $item->size }}</small>
                                                    @endif
                                                    @if($item->material)
                                                    <small class="text-muted d-block">Material: {{ $item->material }}</small>
                                                    @endif
                                                </div>
                                                <div class="col-md-3">
                                                    <p class="mb-0 fw-bold">Price: {{ $siteSettings->currency->symbol }}{{ number_format($item->price, 2) }}</p>

                                                    <small class="text-muted">Qty: {{ $item->quantity }}</small>

                                                    @if($item->discount_amount > 0)
                                                    <small class="d-block text-success" title="Coupon Discount applied on this item">
                                                        Discount: - {{ $siteSettings->currency->symbol }}{{ number_format($item->discount_amount, 2) }}
                                                    </small>
                                                    @endif

                                                    <p class="mb-0 fw-bold">Sub Total: {{ $siteSettings->currency->symbol }}{{ number_format($item->total_price, 2) - number_format($item->discount_amount, 2) }}</p>
                                                </div>
                                                <div class="col-md-2 text-end">
                                                    @php
                                                    $itemStatus = $item->product_order_status;
                                                    $itemColor = $itemStatus instanceof \App\Enums\OrderStatus ? $itemStatus->color() : 'bg-secondary';
                                                    $itemLabel = $itemStatus instanceof \App\Enums\OrderStatus ? $itemStatus->label() : ($itemStatus ?? 'PENDING');
                                                    @endphp
                                                    <span class="badge {{ $itemColor }}">
                                                        {{ $itemLabel }}
                                                    </span>
                                                </div>
                                            </div>

                                            @if($item->shippingDetail)
                                            <div class="alert alert-light border">
                                                <p class="d-block fw-bold text-dark mb-1">Shipment Details:</p>
                                                @if($item->shippingDetail->shipping_details)
                                                <p class="d-block text-muted mb-1">{{ $item->shippingDetail->shipping_details }}</p>
                                                @endif
                                                @if($item->shippingDetail->shipment_photo)
                                                <a href="{{ $item->shippingDetail->shipment_photo_full_path }}" target="_blank" class="text-primary text-decoration-none">
                                                    <i class="fas fa-image"></i> View Photo
                                                </a>
                                                @endif
                                            </div>
                                            @endif

                                            <hr>

                                            @php
                                            // Cancellation Logic
                                            $timeLimit = config('app.cancellation_time_limit_hours', env('CANCELLATION_TIME_LIMIT_HOURS', 24));
                                            $allowedCancelStatuses = ['PLACED', 'PROCESSING', 'SHIPPED'];
                                            // Check time limit AND Allowed Status
                                            $canCancel = \Carbon\Carbon::parse($order->created_at)->diffInHours(now()) <= $timeLimit && in_array($order->order_status->value ?? $order->order_status, $allowedCancelStatuses);

                                                $cancellationMethod = config('app.cancellation_method', env('CANCELLATION_METHOD', 'REQUEST'));
                                                $hasCancellationRequest = $item->cancellationRequests->count() > 0;

                                                // Return Logic
                                                $returnDays = $item->product ? $item->product->return_till_days : 0;
                                                $allowedReturnStatuses = ['DELIVERED']; // Only when delivered
                                                $canReturn = false;
                                                // Check Return Days (Time) AND Allowed Status (DELIVERED)
                                                if ($returnDays > 0) {
                                                $canReturn = \Carbon\Carbon::parse($order->created_at)->addDays($returnDays)->isFuture() && in_array($order->order_status->value ?? $order->order_status, $allowedReturnStatuses);
                                                }
                                                $hasReturnRequest = $item->returnRequest;

                                                // Statuses where return workflow is active/relevant
                                                $isReturnableStatus = !in_array($item->order_status, ['CANCELLED', 'RETURN_REQUESTED', 'RETURN_APPROVED', 'RETURN_REJECTED', 'RETURN_CANCELLED']);

                                                // If already returned or requested, show return section regardless of time limit
                                                // But if creating NEW request, enforce status and time.
                                                @endphp

                                                @if($hasCancellationRequest || $canCancel || $hasReturnRequest || ($canReturn && $isReturnableStatus))
                                                <div class="row">
                                                    <!-- Cancellation Section -->
                                                    @if($item->cancellationRequests->count() > 0 || $canCancel)
                                                    <div class="col-12 mb-3">
                                                        <div class="card border shadow-sm">
                                                            <div class="card-header bg-light py-2 d-flex justify-content-between align-items-center">
                                                                <h6 class="mb-0 fw-bold text-danger"><i class="fas fa-ban me-1"></i> Cancellation Requests</h6>
                                                                @if($canCancel)
                                                                <button class="btn btn-sm btn-outline-danger btn-request-cancellation"
                                                                    data-product-list-id="{{ $item->id }}"
                                                                    data-cancellation-method="{{ $cancellationMethod }}">
                                                                    <i class="fas fa-plus me-1"></i> New Request
                                                                </button>
                                                                @endif
                                                            </div>
                                                            <div class="card-body py-3">
                                                                @if($item->cancellationRequests->count() > 0)
                                                                <div class="accordion" id="accordionCancel-{{ $item->id }}">
                                                                    @foreach($item->cancellationRequests as $reqIndex => $req)
                                                                    <div class="accordion-item">
                                                                        <h2 class="accordion-header" id="heading-{{ $req->id }}">
                                                                            <button class="accordion-button {{ $reqIndex !== 0 ? 'collapsed' : '' }}" type="button" data-bs-toggle="collapse" data-bs-target="#collapse-{{ $req->id }}" aria-expanded="{{ $reqIndex === 0 ? 'true' : 'false' }}" aria-controls="collapse-{{ $req->id }}">
                                                                                <span class="badge {{ $req->status == 'Closed' ? 'bg-secondary' : 'bg-warning' }} me-2">{{ $req->status }}</span>
                                                                                <span class="fw-bold me-2">{{ $req->cancelReason->title ?? 'N/A' }}</span>
                                                                                <small class="text-muted ms-auto">{{ $req->created_at->format('d M, Y') }}</small>
                                                                            </button>
                                                                        </h2>
                                                                        <div id="collapse-{{ $req->id }}" class="accordion-collapse collapse {{ $reqIndex === 0 ? 'show' : '' }}" aria-labelledby="heading-{{ $req->id }}" data-bs-parent="#accordionCancel-{{ $item->id }}">
                                                                            <div class="accordion-body">
                                                                                <p class="small text-muted mb-2"><strong>Description:</strong> {{ $req->description }}</p>

                                                                                <!-- Chat Box -->
                                                                                <div class="chat-box border rounded bg-light mb-2" style="height: fit-content; max-height: 200px; overflow-y: auto; padding: 10px;" id="chat-box-{{ $req->id }}">
                                                                                    @if($req->messages->count() > 0)
                                                                                    @foreach($req->messages as $msg)
                                                                                    <div class="d-flex {{ $msg->message_by == auth()->user()->id ? 'justify-content-end' : 'justify-content-start' }} mb-2">
                                                                                        <div class="{{ $msg->message_by == auth()->user()->id ? 'bg-primary text-white' : 'bg-white text-dark border' }} p-2 rounded-3" style="max-width: 85%;">
                                                                                            <p class="mb-0 small">{{ $msg->message }}</p>
                                                                                            <small class="{{ $msg->message_by == auth()->user()->id ? 'text-white-50' : 'text-muted' }}" style="font-size: 0.7rem;">
                                                                                                {{ $msg->created_at->diffForHumans() }}
                                                                                            </small>
                                                                                        </div>
                                                                                    </div>
                                                                                    @endforeach
                                                                                    @else
                                                                                    <p class="text-center text-muted small mb-0">No messages yet.</p>
                                                                                    @endif
                                                                                </div>

                                                                                <!-- Actions -->
                                                                                <div class="d-flex justify-content-between align-items-center mt-2">
                                                                                    @if($req->status != 'Closed')
                                                                                    <form class="form-send-message flex-grow-1 me-2" data-request-id="{{ $req->id }}">
                                                                                        @csrf
                                                                                        <div class="input-group input-group-sm">
                                                                                            <input type="text" class="form-control" name="message" placeholder="Type a message..." required>
                                                                                            <button class="btn btn-primary" type="submit"><i class="fas fa-paper-plane"></i></button>
                                                                                        </div>
                                                                                    </form>
                                                                                    <button class="btn btn-sm btn-outline-secondary btn-close-request-user" data-id="{{ $req->id }}">Close</button>
                                                                                    @else
                                                                                    <div class="alert alert-secondary py-1 w-100 text-center small mb-0">Request Closed</div>
                                                                                    @endif
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                    @endforeach
                                                                </div>
                                                                @else
                                                                <p class="text-muted small mb-0">No cancellation requests found.</p>
                                                                @endif
                                                            </div>
                                                        </div>
                                                    </div>
                                                    @endif

                                                    <!-- Return Section -->
                                                    @if($hasReturnRequest || ($canReturn && $isReturnableStatus))
                                                    <div class="col-12 mb-3">
                                                        <div class="card border">
                                                            <div class="card-header bg-light py-2">
                                                                <h6 class="mb-0 fw-bold text-primary"><i class="fas fa-undo me-1"></i> Return</h6>
                                                            </div>
                                                            <div class="card-body py-3">
                                                                @if($hasReturnRequest)
                                                                <!-- Existing Return Request -->
                                                                <div class="existing-return-request">
                                                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                                                        <p class="fw-bold mb-0">Request Status:</p>
                                                                        <span class="badge {{ $item->returnRequest->return_status == 'RETURN_APPROVED' ? 'bg-success' : ($item->returnRequest->return_status == 'RETURN_REJECTED' ? 'bg-danger' : 'bg-warning') }}">
                                                                            {{ $item->returnRequest->return_status }}
                                                                        </span>
                                                                    </div>
                                                                    <div class="alert alert-light border mb-0">
                                                                        <p class="mb-1"><strong>Reason:</strong> {{ $item->returnRequest->returnReason ? $item->returnRequest->returnReason->title : 'N/A' }}</p>
                                                                        <p class="mb-0 small text-muted">{{ $item->returnRequest->return_description }}</p>
                                                                        @if($item->returnRequest->return_pickup_details)
                                                                        <hr class="my-2">
                                                                        <p class="mb-0 small fw-bold">Pickup Details:</p>
                                                                        <p class="mb-0 small">{{ $item->returnRequest->return_pickup_details }}</p>
                                                                        @endif
                                                                    </div>
                                                                </div>
                                                                @else
                                                                <!-- Create Return Request -->
                                                                <button class="btn btn-sm btn-outline-primary" type="button" data-bs-toggle="collapse" data-bs-target="#returnRequestForm-{{ $item->id }}">
                                                                    Request Return
                                                                </button>
                                                                <div class="collapse mt-3" id="returnRequestForm-{{ $item->id }}">
                                                                    <div class="card card-body bg-light">
                                                                        <h6 class="fw-bold mb-3">Submit Return Request</h6>
                                                                        <form class="form-create-return-request" data-product-list-id="{{ $item->id }}">
                                                                            @csrf
                                                                            <div class="mb-2">
                                                                                <label class="form-label small">Return Reason</label>
                                                                                <select class="form-control " name="return_list_id" required>
                                                                                    <option value="">Select Reason</option>
                                                                                    @foreach($returnReasons as $reason)
                                                                                    <option value="{{ $reason->id }}">{{ $reason->title }}</option>
                                                                                    @endforeach
                                                                                </select>
                                                                            </div>
                                                                            <div class="mb-2">
                                                                                <label class="form-label small">Description</label>
                                                                                <textarea class="form-control " name="return_description" rows="2" placeholder="Additional details..."></textarea>
                                                                            </div>
                                                                            <button type="submit" class="btn btn-primary btn-sm w-100">Submit Return Request</button>
                                                                        </form>
                                                                    </div>
                                                                </div>
                                                                @endif
                                                            </div>
                                                        </div>
                                                    </div>
                                                    @endif
                                                </div>
                                                @endif
                                        </div>
                                        @endforeach
                                    </div>
                                </div>
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

<!-- Cancellation Request Modal -->
<div class="modal fade" id="cancelRequestModal" tabindex="-1" aria-labelledby="cancelRequestModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="cancelRequestModalLabel">Submit Cancellation Request</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="form-create-request-modal">
                    @csrf
                    <input type="hidden" name="order_product_list_id" id="modal_order_product_list_id">
                    <div class="mb-3">
                        <label for="cancel_reason_id" class="form-label">Reason</label>
                        <select class="form-control" name="cancel_reason_id" id="modal_cancel_reason_id" required>
                            <option value="">Select Reason</option>
                            @foreach($cancelReasons as $reason)
                            <option value="{{ $reason->id }}">{{ $reason->title }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="description" class="form-label">Description</label>
                        <textarea class="form-control" name="description" id="modal_description" rows="3" required placeholder="Detailed explanation..."></textarea>
                    </div>
                    <div class="d-grid">
                        <button type="submit" class="btn btn-danger" id="btn-submit-cancel-request">Submit Request</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@section('pages-scripts')
<script @cspNonce>
    $(document).ready(function() {
        // Open Cancellation Modal
        $(document).on('click', '.btn-request-cancellation', function() {
            var productListId = $(this).data('product-list-id');
            $('#modal_order_product_list_id').val(productListId);
            $('#form-create-request-modal').trigger('reset');
            $('#cancelRequestModal').modal('show');
        });

        // Submit Cancellation Request (Modal)
        $('#form-create-request-modal').submit(function(e) {
            e.preventDefault();
            var form = $(this);
            var formData = new FormData(this);

            $.ajax({
                url: "{{ route('order.cancel.store') }}",
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                beforeSend: function() {
                    $('#btn-submit-cancel-request').prop('disabled', true).text('Submitting...');
                },
                success: function(response) {
                    if (response.success) {
                        $('#cancelRequestModal').modal('hide');
                        Swal.fire({
                            icon: 'success',
                            title: 'Success',
                            text: response.message
                        }).then(() => {
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
                    var msg = 'Something went wrong';
                    if (xhr.responseJSON && xhr.responseJSON.message) {
                        msg = xhr.responseJSON.message;
                    } else if (xhr.responseJSON && xhr.responseJSON.errors) {
                        msg = Object.values(xhr.responseJSON.errors).join('\n');
                    }
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: msg
                    });
                },
                complete: function() {
                    $('#btn-submit-cancel-request').prop('disabled', false).text('Submit Request');
                }
            });
        });

        // Send Message
        $(document).on('submit', '.form-send-message', function(e) {
            e.preventDefault();
            var form = $(this);
            var requestId = form.data('request-id');
            var formData = new FormData(this);
            formData.append('order_cancellation_request_id', requestId);

            $.ajax({
                url: "{{ route('order.cancel.message.store') }}",
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

        // Close Request (User)
        $(document).on('click', '.btn-close-request-user', function() {
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
                        url: "{{ route('order.cancel.status.update', ':id') }}".replace(':id', requestId),
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

        // Create Return Request
        $('.form-create-return-request').submit(function(e) {
            e.preventDefault();
            var form = $(this);
            var productListId = form.data('product-list-id');
            var formData = new FormData(this);
            formData.append('order_product_list_id', productListId);

            $.ajax({
                url: "{{ route('user.return-requests.store') }}",
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                beforeSend: function() {
                    form.find('button[type="submit"]').prop('disabled', true).text('Submitting...');
                },
                success: function(response) {
                    if (response.success) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Success',
                            text: response.message
                        }).then(() => {
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
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'Something went wrong: ' + (xhr.responseJSON ? xhr.responseJSON.message : xhr.statusText)
                    });
                },
                complete: function() {
                    form.find('button[type="submit"]').prop('disabled', false).text('Submit Return Request');
                }
            });
        });
    });
</script>
@endsection