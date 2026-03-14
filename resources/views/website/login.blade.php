@extends('layouts.website_layout')

@section('content')

@include('components.website.page-header')

<section class="cart-section">
    <div class="container py-5">
        <div class="row g-4">

            <div class="col-12" id="main-content" tabindex="-1">
                <div class="row justify-content-center">
                    <div class="col-md-8 col-lg-5">
                        <div class="card border-0 shadow-sm">
                            <div class="card-body p-4">
                                <div class="text-center py-5">
                                    <h5 class="mb-3">Welcome to {{ config('app.name') }}</h5>
                                    <p class="text-muted mb-4">Please login to access your account.</p>
                                    <button type="button" class="btn btn-custom px-5 py-3 rounded-4" data-bs-toggle="modal" data-bs-target="#modalLoginForm">
                                        <i class="fa fa-user me-2"></i> Login Now
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    </div>
    </div>
</section>

@endsection

@section('pages-scripts')
@section('pages-scripts')
<script @cspNonce>
    $(document).ready(function() {
        @if(session('error'))
        Swal.fire({
            icon: 'error',
            title: 'Error',
            text: "{{ session('error') }}",
            confirmButtonText: "OK"
        });
        @endif

        @if(session('success'))
        Swal.fire({
            icon: 'success',
            title: 'Success',
            text: "{{ session('success') }}",
            confirmButtonText: "OK"
        });
        @endif
    });
</script>

@endsection