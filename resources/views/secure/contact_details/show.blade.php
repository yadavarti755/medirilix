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

                    <div class="col-md-6 col-12 card py-2 bg-light mb-3">
                        <label class="form-label">Address:</label>
                        <p class="mb-0">{{ $contactDetail->address ?? '—' }}</p>
                    </div>

                    <div class="col-md-6 col-12 card py-2 bg-light mb-3">
                        <label class="form-label">Phone Numbers:</label>
                        <p class="mb-0">{{ $contactDetail->phone_numbers ?? '—' }}</p>
                    </div>

                    <div class="col-md-6 col-12 card py-2 bg-light mb-3">
                        <label class="form-label">Email IDs:</label>
                        <p class="mb-0">{{ $contactDetail->email_ids ?? '—' }}</p>
                    </div>

                    <div class="col-md-6 col-12 card py-2 bg-light mb-3">
                        <label class="form-label">Primary Contact:</label>
                        <p class="mb-0">{{ $contactDetail->is_primary ? 'Yes' : 'No' }}</p>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>
@endsection

@section('pages-scripts')
@endsection