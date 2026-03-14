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
                    <p class="text-muted small mb-0">Items you have saved for later</p>
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
                    @forelse($wishlists as $wishlist)
                    <div class="card mb-3 card-hover border-0 shadow-sm">
                        <div class="card-body p-3">
                            <div class="row align-items-center">
                                <div class="col-lg-4 col-md-5 mb-3 mb-md-0">
                                    <div class="d-flex align-items-center">
                                        <a href="{{ route('product-details', $wishlist->product->slug) }}" class="flex-shrink-0">
                                            <img src="{{ asset('storage/' . Config::get('file_paths')['PRODUCT_IMAGE_PATH'] . '/' . $wishlist->product->featured_image) }}"
                                                alt="{{ $wishlist->product->name }}"
                                                class="rounded-3 border"
                                                style="width: 80px; height: 80px; object-fit: cover;">
                                        </a>
                                        <div class="ms-3">
                                            <a href="{{ route('product-details', $wishlist->product->slug) }}" class="text-decoration-none text-dark">
                                                <h6 class="fw-bold mb-1">{{ Str::limit($wishlist->product->name, 40) }}</h6>
                                            </a>
                                            <p class="text-muted small mb-0">Added on {{ $wishlist->created_at->format('d M, Y') }}</p>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-lg-3 col-md-3 mb-3 mb-md-0">
                                    <h6 class="text-muted text-uppercase fw-semibold small mb-1 ls-1">Price</h6>
                                    @if($wishlist->product->mrp > $wishlist->product->selling_price)
                                    <div class="d-flex align-items-baseline">
                                        <span class="fw-bold text-dark fs-5 me-2">₹{{ number_format($wishlist->product->selling_price, 2) }}</span>
                                        <small class="text-muted text-decoration-line-through">₹{{ number_format($wishlist->product->mrp, 2) }}</small>
                                    </div>
                                    @else
                                    <span class="fw-bold text-dark fs-5">₹{{ number_format($wishlist->product->selling_price, 2) }}</span>
                                    @endif
                                </div>

                                <div class="col-lg-2 col-md-2 mb-3 mb-md-0">
                                    @if($wishlist->product->stock_availability == 1)
                                    <span class="badge bg-success-subtle text-success rounded-pill px-3 py-2">In Stock</span>
                                    @else
                                    <span class="badge bg-danger-subtle text-danger rounded-pill px-3 py-2">Out of Stock</span>
                                    @endif
                                </div>

                                <div class="col-lg-3 col-md-2 text-md-end">
                                    <button type="button"
                                        class="btn btn-outline-danger btn-sm rounded-pill px-3 btn_remove_wishlist shadow-sm"
                                        data-id="{{ $wishlist->id }}"
                                        title="Remove from Wishlist">
                                        <i class="fas fa-trash-alt me-1"></i> Remove
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                    @empty
                    <div class="card shadow-none border border-dashed rounded-4 p-5 text-center bg-light">
                        <div class="card-body">
                            <div class="mb-4 text-secondary opacity-50">
                                <i class="fas fa-heart-broken fa-3x"></i>
                            </div>
                            <h5 class="fw-bold text-dark">Your Wishlist is Empty</h5>
                            <p class="text-muted mb-4">You haven't added any items to your wishlist yet.</p>
                            <a href="{{ url('/') }}" class="btn btn-primary rounded-pill px-4 shadow-sm">Continue Shopping</a>
                        </div>
                    </div>
                    @endforelse
                </div>
            </div>
        </div><!-- /.container-fluid -->
    </div>
    <!-- /.content -->
</div>
<!-- /.content-wrapper -->
@endsection

@section('pages-scripts')
<script @cspNonce>
    $(document).ready(function() {
        $('.btn_remove_wishlist').click(function() {
            var wishlistId = $(this).data('id');
            var card = $(this).closest('.card');

            Swal.fire({
                title: 'Are you sure?',
                text: "You won't be able to revert this!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Yes, remove it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: "{{ route('user.wishlist.destroy') }}",
                        type: 'POST',
                        data: {
                            id: wishlistId,
                            _token: '{{ csrf_token() }}'
                        },
                        success: function(response) {
                            if (response.success) {
                                Swal.fire(
                                    'Removed!',
                                    response.message,
                                    'success'
                                ).then(() => {
                                    card.fadeOut(300, function() {
                                        $(this).remove();
                                        // Optional: Check if no cards left, show empty state (reload for simplicity)
                                        if ($('.card.rounded-4').length === 0) {
                                            location.reload();
                                        }
                                    });
                                });
                            } else {
                                Swal.fire(
                                    'Error!',
                                    response.message,
                                    'error'
                                );
                            }
                        },
                        error: function(xhr) {
                            var msg = 'Something went wrong';
                            if (xhr.responseJSON && xhr.responseJSON.message) {
                                msg = xhr.responseJSON.message;
                            }
                            Swal.fire(
                                'Error!',
                                msg,
                                'error'
                            );
                        }
                    });
                }
            })
        });
    });
</script>
@endsection