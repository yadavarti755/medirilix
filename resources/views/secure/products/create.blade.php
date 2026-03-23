@extends('layouts.app_layout')

@section('content')

<x-page-header title="{{ $pageTitle }}" :backButton="true" />

<div class="row">
    <div class="col-12">
        <form id="productForm" enctype="multipart/form-data">
            @csrf

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
                                <label class="form-label">Product Name <span class="text-danger">*</span></label>
                                <input type="text" name="name" class="form-control" placeholder="Enter product name" required />
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Description</label>
                                <textarea name="description" id="page-editor" class="form-control" rows="6" placeholder="Detailed product description..."></textarea>
                            </div>
                        </div>
                    </div>

                    <!-- Media -->
                    <div class="card mb-4">
                        <div class="card-header">
                            <h5 class="card-title mb-0">Product Media</h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6 col-12 mb-3">
                                    <label class="form-label">Featured Image <span class="text-danger">*</span></label>
                                    <div class="image-col">
                                        <input type="file" name="featured_image" class="featured_image_upload" accept=".jpg,.jpeg,.png,.gif,.webp" />
                                        <small class="text-danger">Allowed: jpg, jpeg, png, gif, webp | Max: 2MB</small>
                                    </div>
                                </div>
                                <div class="col-12 mb-3">
                                    <label class="form-label">Gallery Images</label>
                                    <div class="image-col">
                                        <div class="dropzone" id="gallery-dropzone" style="border: 2px dashed #0087F7; border-radius: 5px; background: white; min-height: 150px; padding: 20px;"></div>
                                        <small class="text-danger">Drag and drop multiple images for the product gallery. You can add more images anytime.</small>
                                    </div>
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
                                    <label class="form-label">MRP <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <span class="input-group-text">$</span>
                                        <input type="number" name="mrp" class="form-control" placeholder="0.00" required />
                                    </div>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Selling Price <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <span class="input-group-text">$</span>
                                        <input type="number" name="selling_price" class="form-control" placeholder="0.00" required />
                                    </div>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Quantity <span class="text-danger">*</span></label>
                                    <input type="number" name="quantity" class="form-control" placeholder="0" required />
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Stock Status</label>
                                    <select name="stock_availability" class="form-control">
                                        @foreach (Config::get('constants.stock_availability') as $key => $type)
                                        <option value="{{ $key }}" {{ $key == 1 ? 'selected' : '' }}>{{ $type }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Listing Type</label>
                                    <select name="product_listing_type" class="form-control">
                                        <option value="">Select Type</option>
                                        @foreach (Config::get('constants.product_listing_type') as $key => $type)
                                        <option value="{{ $key }}">{{ $type }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- SEO (Meta Data) -->
                    <div class="card mb-4">
                        <div class="card-header">
                            <h5 class="card-title mb-0">SEO Configuration (Optional)</h5>
                        </div>
                        <div class="card-body">
                            <div class="mb-3">
                                <label class="form-label">Meta Keywords</label>
                                <input type="text" name="meta_keywords" class="form-control" placeholder="keyword1, keyword2, keyword3" />
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Meta Description</label>
                                <textarea name="meta_description" class="form-control" rows="3" placeholder="Brief description for search engines"></textarea>
                            </div>
                        </div>
                    </div>

                    <!-- Other Specifications -->
                    <div class="card mb-4">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h5 class="card-title mb-0">Additional Specifications</h5>
                            <button type="button" class="btn btn-sm btn-info" id="add-spec-btn">
                                <i class="fa fa-plus"></i> Add Spec
                            </button>
                        </div>
                        <div class="card-body">
                            <div id="other-specs-container">
                                <!-- Dynamic Rows -->
                            </div>
                            <small class="text-muted">Add custom key-value pairs for technical specifications.</small>
                        </div>
                    </div>

                </div>

                <!-- Right Column: Sidebar -->
                <div class="col-lg-4">

                    <!-- Publish Action -->
                    <div class="card mb-4">
                        <div class="card-header">
                            <h5 class="card-title mb-0">Publish</h5>
                        </div>
                        <div class="card-body">
                            <div class="mb-3">
                                <label class="form-label">Status</label>
                                <select name="is_published" class="form-control form-select">
                                    <option value="1">Published</option>
                                    <option value="0">Draft</option>
                                </select>
                            </div>
                            <div class="d-grid gap-2">
                                <button type="submit" class="btn btn-primary btn-lg">
                                    <i class="fa fa-save me-1"></i> Save Product
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Organization -->
                    <div class="card mb-4">
                        <div class="card-header">
                            <h5 class="card-title mb-0">Organization</h5>
                        </div>
                        <div class="card-body">
                            <div class="mb-3">
                                <div class="d-flex justify-content-between align-items-center mb-1">
                                    <label class="form-label mb-0">Category <span class="text-danger">*</span></label>
                                    <button type="button" class="btn btn-sm btn-outline-primary py-0 px-2" data-bs-toggle="modal" data-bs-target="#categoryCreateModal">
                                        <i class="fa fa-plus"></i> Add
                                    </button>
                                </div>
                                <select name="category_id" class="form-control select2" required>
                                    <option value="">Select Category</option>
                                    @foreach ($categories as $cat)
                                    <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Brand</label>
                                <select name="brand_id" class="form-control select2">
                                    <option value="">Select Brand</option>
                                    @foreach ($brands as $brand)
                                    <option value="{{ $brand->id }}">{{ $brand->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Product Type</label>
                                <select name="type_id" class="form-control">
                                    <option value="">Select Type</option>
                                    @foreach ($productTypes as $type)
                                    <option value="{{ $type->id }}">{{ $type->name }}</option>
                                    @endforeach
                                </select>
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
                                <label class="form-label">UPC</label>
                                <input type="text" name="upc" class="form-control" placeholder="Universal Product Code" />
                            </div>
                            <div class="mb-3">
                                <label class="form-label">MPN</label>
                                <input type="text" name="mpn" class="form-control" placeholder="Manufacture Part Number" />
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Model</label>
                                <input type="text" name="model" class="form-control" placeholder="Model Number" />
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Expiration Date</label>
                                <input type="date" name="expiration_date" class="form-control" />
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Unit Quantity</label>
                                <input type="number" name="unit_quantity" class="form-control" placeholder="1" />
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Unit Type</label>
                                <select name="unit_type_id" class="form-control">
                                    <option value="">Select Unit</option>
                                    @foreach ($unitTypes as $unitType)
                                    <option value="{{ $unitType->id }}">{{ $unitType->name }}</option>
                                    @endforeach
                                </select>
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
                                <label class="form-label">Material</label>
                                <select name="material_id" class="form-control">
                                    <option value="">Select Material</option>
                                    @foreach ($materials as $m)
                                    <option value="{{ $m->id }}">{{ $m->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Intended Use</label>
                                <select name="intended_use_id" class="form-control">
                                    <option value="">Select Usage</option>
                                    @foreach ($intendedUses as $use)
                                    <option value="{{ $use->id }}">{{ $use->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Country of Origin</label>
                                <select name="country_of_origin" class="form-control">
                                    <option value="">Select Country</option>
                                    @foreach($countries as $country)
                                    <option value="{{ $country->id }}">{{ $country->name }}</option>
                                    @endforeach
                                </select>
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
                                <label class="form-label">Return Policy</label>
                                <select name="return_policy_id" class="form-control">
                                    <option value="">Select Policy</option>
                                    @foreach ($returnPolicies as $policy)
                                    <option value="{{ $policy->id }}" {{ old('return_policy_id') == $policy->id ? 'selected' : '' }}>{{ $policy->title }} ({{ $policy->return_till_days }} Days)</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">California Prop 65</label>
                                <textarea name="california_prop_65_warning" class="form-control" rows="2" placeholder="Warning text if applicable"></textarea>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </form>
    </div>
</div>

<!-- Category Create Modal -->
<div class="modal fade" id="categoryCreateModal" tabindex="-1" aria-labelledby="categoryCreateModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="categoryCreateModalLabel">Add New Category</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <x-backend.category-form :categories="$categories" />
            </div>
        </div>
    </div>
</div>

@endsection

@section('pages-scripts')
<script @cspNonce>
    $(document).ready(function() {

        // Initialize Select2
        if ($('.select2').length > 0) {
            $('.select2').select2({
                width: '100%',
                placeholder: "Select an option",
                allowClear: true
            });
        }

        // Initialize Dropzone
        Dropzone.autoDiscover = false;
        var galleryDropzone = new Dropzone("#gallery-dropzone", {
            url: "/", // Dummy URL as we handle submission manually
            autoProcessQueue: false,
            uploadMultiple: true,
            parallelUploads: 100,
            maxFiles: 20,
            acceptedFiles: 'image/*',
            addRemoveLinks: true,
            dictDefaultMessage: '<i class="fa fa-cloud-upload fa-3x mb-3 text-primary"></i><br>Drag and drop images here or click to upload',
        });

        // Dynamic Specifications
        let specIndex = 0;

        function addSpecRow() {
            let html = `
                <div class="row mb-2 spec-row align-items-center">
                    <div class="col-5">
                        <input type="text" name="other_specs[${specIndex}][label]" class="form-control" placeholder="Label (e.g. Color)" required>
                    </div>
                    <div class="col-5">
                        <input type="text" name="other_specs[${specIndex}][value]" class="form-control" placeholder="Value (e.g. Red)" required>
                    </div>
                    <div class="col-2 text-end">
                        <button type="button" class="btn btn-outline-danger btn-sm remove-spec-btn"><i class="fa fa-trash"></i></button>
                    </div>
                </div>
            `;
            $('#other-specs-container').append(html);
            specIndex++;
        }

        $('#add-spec-btn').click(function() {
            addSpecRow();
        });

        $(document).on('click', '.remove-spec-btn', function() {
            $(this).closest('.spec-row').remove();
        });

        // Form Validation & Submission
        $("#productForm").validate({
            rules: {
                name: {
                    required: true
                },
                category_id: {
                    required: true
                },
                mrp: {
                    required: true
                },
                selling_price: {
                    required: true
                },
                quantity: {
                    required: true
                },
                featured_image: {
                    required: true
                }
            },
            messages: {
                name: "Please enter product name",
                category_id: "Please select a category",
                featured_image: "Please upload a featured image"
            },
            errorElement: 'span',
            errorPlacement: function(error, element) {
                error.addClass('invalid-feedback');
                element.closest('.mb-3').append(error);
                element.closest('.input-group').parent().append(error); // For input groups
            },
            highlight: function(element, errorClass, validClass) {
                $(element).addClass('is-invalid');
            },
            unhighlight: function(element, errorClass, validClass) {
                $(element).removeClass('is-invalid');
            },
            submitHandler: function(form) {
                let formData = new FormData(form);
                let submitBtn = $(form).find('button[type="submit"]');
                let originalText = submitBtn.html();

                // Sync CKEditor data
                if (window.editors) {
                    window.editors.forEach(({
                        editor,
                        name,
                        id
                    }) => {
                        if (id == 'page-editor') {
                            const editorContent = editor.getData();
                            formData.set(name, editorContent);
                        }
                    });
                }

                submitBtn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Saving...');

                // Append Dropzone files
                galleryDropzone.getQueuedFiles().forEach(file => {
                    formData.append('multiple_product_image[]', file);
                });

                $.ajax({
                    url: "{{ route('products.store') }}",
                    method: "POST",
                    data: formData,
                    contentType: false,
                    processData: false,
                    success: function(response) {
                        if (response.success) {
                            Swal.fire({
                                title: "Success!",
                                text: response.message,
                                icon: "success",
                                confirmButtonText: "Okay"
                            }).then(() => {
                                window.location.href = "{{ route('products.index') }}";
                            });
                        } else {
                            toastr.error(response.message);
                            submitBtn.prop('disabled', false).html(originalText);
                        }
                    },
                    error: function(xhr) {
                        submitBtn.prop('disabled', false).html(originalText);
                        if (xhr.status === 422) {
                            let errors = xhr.responseJSON.errors;
                            let message = Object.values(errors).flat().join("<br>");
                            Swal.fire({
                                title: "Validation Error",
                                html: message,
                                icon: "error"
                            });
                        } else {
                            Swal.fire({
                                title: "Error!",
                                text: xhr.responseJSON.message || "Something went wrong.",
                                icon: "error"
                            });
                        }
                    }
                });
            }
        });
    });
</script>
@yield('category_form_script')
@endsection