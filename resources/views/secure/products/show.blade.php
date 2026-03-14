@extends('layouts.app_layout')

@section('content')

<x-page-header title="{{ $pageTitle }}" :backButton="true" />

<div class="row">
    <div class="col-12">
        <div class="row">
            <!-- Left Column: Main Information -->
            <div class="col-lg-8">

                <!-- Basic Information -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Basic Information</h5>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label class="form-label fw-bold">Product Name:</label>
                            <p class="text-muted">{{ $product->name }}</p>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold">Description:</label>
                            <div class="border p-2 rounded bg-light">
                                {!! $product->description !!}
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Media -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Product Media</h5>
                    </div>
                    <div class="card-body">
                        <!-- Featured Image -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <label class="form-label fw-bold">Featured Image:</label>
                                <div>
                                    @if($product->featured_image)
                                    <img src="{{ $product->featured_image_full_path }}" alt="Featured Image" class="img-thumbnail" style="max-height: 200px;">
                                    @else
                                    <span class="text-muted">No featured image.</span>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <hr>

                        <!-- Gallery Images -->
                        <div class="row">
                            <div class="col-12">
                                <label class="form-label fw-bold">Gallery Images:</label>
                                @if($product->images && count($product->images) > 0)
                                <div class="row">
                                    @foreach($product->images as $img)
                                    <div class="col-6 col-md-3 mb-3 text-center">
                                        <div class="card h-100 border">
                                            <div class="card-body p-2 d-flex align-items-center justify-content-center">
                                                <img src="{{ $img->full_path }}" class="img-fluid" style="max-height: 100px; object-fit: cover;">
                                            </div>
                                        </div>
                                    </div>
                                    @endforeach
                                </div>
                                @else
                                <p class="text-muted">No gallery images available.</p>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Pricing & Inventory -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Pricing & Inventory</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="fw-bold">MRP:</label>
                                <p>${{ number_format($product->mrp, 2) }}</p>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="fw-bold">Selling Price:</label>
                                <p>${{ number_format($product->selling_price, 2) }}</p>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="fw-bold">Quantity:</label>
                                <p>{{ $product->quantity }}</p>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="fw-bold">Unit Quantity:</label>
                                <p>{{ $product->unit_quantity }}</p>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="fw-bold">Unit Type:</label>
                                <p>{{ $product->unitType->name ?? 'N/A' }}</p>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="fw-bold">Stock Status:</label>
                                <p>{{ Config::get('constants.stock_availability')[$product->stock_availability] ?? 'N/A' }}</p>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="fw-bold">Listing Type:</label>
                                <p>{{ Config::get('constants.product_listing_type')[$product->product_listing_type] ?? 'N/A' }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- SEO (Meta Data) -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="card-title mb-0">SEO Configuration</h5>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label class="fw-bold">Meta Keywords:</label>
                            <p>{{ $product->meta_keywords ?? 'N/A' }}</p>
                        </div>
                        <div class="mb-3">
                            <label class="fw-bold">Meta Description:</label>
                            <p>{{ $product->meta_description ?? 'N/A' }}</p>
                        </div>
                    </div>
                </div>

                <!-- Other Specifications -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Additional Specifications</h5>
                    </div>
                    <div class="card-body">
                        @if($product->otherSpecifications && count($product->otherSpecifications) > 0)
                        <table class="table table-bordered table-sm">
                            <thead>
                                <tr>
                                    <th>Label</th>
                                    <th>Value</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($product->otherSpecifications as $spec)
                                <tr>
                                    <td>{{ $spec->label }}</td>
                                    <td>{{ $spec->value }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                        @else
                        <p class="text-muted">No additional specifications.</p>
                        @endif
                    </div>
                </div>

            </div>

            <!-- Right Column: Sidebar -->
            <div class="col-lg-4">

                <!-- Publish Action -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Status</h5>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label class="fw-bold">Publish Status:</label>
                            <div>
                                @if($product->is_published == 1)
                                <span class="badge bg-success">Published</span>
                                @else
                                <span class="badge bg-secondary">Draft</span>
                                @endif
                            </div>
                        </div>
                        @can('edit product')
                        <div class="d-grid gap-2">
                            <a href="{{ route('products.edit', $product->id) }}" class="btn btn-warning">
                                <i class="fa fa-edit me-1"></i> Edit Product
                            </a>
                        </div>
                        @endcan
                    </div>
                </div>

                <!-- Organization -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Organization</h5>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label class="fw-bold">Category:</label>
                            <p>{{ $product->category->name ?? 'N/A' }}</p>
                        </div>
                        <div class="mb-3">
                            <label class="fw-bold">Brand:</label>
                            <p>{{ $product->brand->name ?? 'N/A' }}</p>
                        </div>
                        <div class="mb-3">
                            <label class="fw-bold">Product Type:</label>
                            <p>{{ $product->productType->name ?? 'N/A' }}</p>
                        </div>
                    </div>
                </div>

                <!-- Identifiers -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Identifiers</h5>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label class="fw-bold">UPC:</label>
                            <p>{{ $product->upc ?? 'N/A' }}</p>
                        </div>
                        <div class="mb-3">
                            <label class="fw-bold">MPN:</label>
                            <p>{{ $product->mpn ?? 'N/A' }}</p>
                        </div>
                        <div class="mb-3">
                            <label class="fw-bold">Model:</label>
                            <p>{{ $product->model ?? 'N/A' }}</p>
                        </div>
                        <div class="mb-3">
                            <label class="fw-bold">Expiration Date:</label>
                            <p>{{ $product->expiration_date ? \Carbon\Carbon::parse($product->expiration_date)->format('Y-m-d') : 'N/A' }}</p>
                        </div>
                    </div>
                </div>

                <!-- Attributes -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Attributes</h5>
                    </div>
                    <div class="card-body">
                        <!-- Sizes Removed -->
                        <div class="mb-3">
                            <label class="fw-bold">Material:</label>
                            <p>{{ $product->material->name ?? 'N/A' }}</p>
                        </div>
                        <div class="mb-3">
                            <label class="fw-bold">Intended Use:</label>
                            <p>{{ $product->intendedUse->name ?? 'N/A' }}</p>
                        </div>
                        <div class="mb-3">
                            <label class="fw-bold">Country of Origin:</label>
                            <p>{{ $product->country->name ?? 'N/A' }}</p>
                        </div>
                    </div>
                </div>

                <!-- Compliance -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Compliance & Policies</h5>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label class="fw-bold">Return Policy:</label>
                            <p>
                                {{ $product->return_till_days ?? 0 }} Days Return
                                ({{ $product->return_description ?? 'No description' }})
                            </p>
                        </div>
                        <div class="mb-3">
                            <label class="fw-bold">California Prop 65:</label>
                            <p>{{ $product->california_prop_65_warning ?? 'N/A' }}</p>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>

@endsection