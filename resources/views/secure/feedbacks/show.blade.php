@extends('layouts.app_layout')

@section('content')
<!-- [ Page Header ] start -->
<x-page-header title="{{ $pageTitle }}" :backButton="true" />
<!-- [ Page Header ] end -->

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <div class="row">

                    <div class="col-md-4 col-sm-6 col-12 card py-2 bg-light mb-3">
                        <label class="form-label">Name:</label>
                        <p class="mb-0">{{ $feedback->name ?? '—' }}</p>
                    </div>

                    <div class="col-md-4 col-sm-6 col-12 card py-2 bg-light mb-3">
                        <label class="form-label">Email:</label>
                        <p class="mb-0">{{ $feedback->email ?? '—' }}</p>
                    </div>

                    <div class="col-md-4 col-sm-6 col-12 card py-2 bg-light mb-3">
                        <label class="form-label">Mobile No.:</label>
                        <p class="mb-0">{{ $feedback->mobile_no ?? '—' }}</p>
                    </div>

                    <div class="col-12 card py-2 bg-light mb-3">
                        <label class="form-label">Message:</label>
                        <p class="mb-0">{{ $feedback->message ?? '—' }}</p>
                    </div>

                </div>
            </div>
        </div>

    </div>
</div>
@endsection

@section('pages-scripts')
@endsection