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
                        <label class="form-label">Type:</label>
                        <p class="mb-0">
                            @php
                            $types = [
                            1 => 'Facebook', 2 => 'YouTube', 3 => 'LinkedIn',
                            4 => 'Instagram', 5 => 'Twitter', 6 => 'Pinterest',
                            7 => 'Snapchat', 8 => 'TikTok', 9 => 'Other'
                            ];
                            @endphp
                            {{ $types[$socialMedia->type] ?? '—' }}
                        </p>
                    </div>

                    <div class="col-md-6 col-12 card py-2 bg-light mb-3">
                        <label class="form-label">Name:</label>
                        <p class="mb-0">{{ $socialMedia->name ?? '—' }}</p>
                    </div>

                    <div class="col-md-6 col-12 card py-2 bg-light mb-3">
                        <label class="form-label">URL:</label>
                        <p class="mb-0">{{ $socialMedia->url ?? '—' }}</p>
                    </div>

                    <div class="col-md-6 col-12 card py-2 bg-light mb-3">
                        <label class="form-label">Icon Class:</label>
                        <p class="mb-0">{{ $socialMedia->icon_class ?? '—' }}</p>
                    </div>

                </div>
            </div>
        </div>

    </div>
</div>
@endsection

@section('pages-scripts')
@endsection