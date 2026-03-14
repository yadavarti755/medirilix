@extends('layouts.app_layout')

@section('content')
<!-- [ Page Header ] start -->
<x-page-header title="{{ $pageTitle }}" :backButton="true" />
<!-- [ Page Header ] end -->

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <form action="" method="POST" id="categoryEditForm" enctype="multipart/form-data">
                    @csrf

                    <div class="row">
                        <div class="form-group col-md-6 col-12">
                            <label class="form-label" for="parent_id">Parent Category</label>
                            <select name="parent_id" id="parent_id" class="form-control">
                                <option value="">None</option>
                                @php renderCategoryOptionsForEdit($categories, $category->parent_id) @endphp
                            </select>
                        </div>

                        <div class="form-group col-md-6 col-12">
                            <label class="form-label" for="order">Order <span class="text-danger">*</span></label>
                            <input type="number" name="order" id="order" class="form-control"
                                value="{{ old('order', $category->order) }}">
                        </div>

                        <div class="form-group col-md-6 col-12">
                            <label class="form-label" for="image">Image</label>
                            <input type="file" name="image" id="image" class="form-control file-input">
                            @if($category->image)
                            <div class="mt-2">
                                <img src="{{ $category->image_path }}" alt="Category Image"
                                    class="img-thumbnail" width="100">
                            </div>
                            @endif
                        </div>

                        <div class="form-group col-md-6 col-12">
                            <label class="form-label" for="name">Name <span class="text-danger">*</span></label>
                            <input type="text" name="name" id="name" class="form-control"
                                value="{{ old('name', $category->name) }}">
                        </div>

                        <div class="form-group col-md-6 col-12">
                            <label class="form-label" for="is_published">Is Published <span class="text-danger">*</span></label>
                            <select name="is_published" id="is_published" class="form-control">
                                <option value="">Select</option>
                                <option value="1" {{ $category->is_published == 1 ? 'selected' : '' }}>Publish</option>
                                <option value="0" {{ $category->is_published == 0 ? 'selected' : '' }}>Unpublish</option>
                            </select>
                        </div>

                        <div class="form-group col-md-6 col-12">
                            <label class="form-label" for="description">Description</label>
                            <textarea name="description" id="description" class="form-control" rows="3">{{ old('description', $category->description) }}</textarea>
                        </div>
                    </div>

                    <div class="text-center mt-3">
                        <button type="submit" class="btn btn-success">
                            <i class="fas fa-save"></i> Update
                        </button>
                        <a href="{{ route('categories.index') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left"></i> Cancel
                        </a>
                    </div>

                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@section('pages-scripts')
<script @cspNonce>
    $(document).ready(function() {
        // Initialize jQuery Validation
        $('#categoryEditForm').validate({
            rules: {
                order: {
                    required: true,
                    number: true
                },
                name: {
                    required: true,
                    maxlength: 255
                },
                description: {
                    maxlength: 500
                }
            },
            submitHandler: function(form) {
                $.ajax({
                    url: "{{ route('categories.update', $category->id) }}",
                    type: "POST",
                    data: new FormData(document.getElementById('categoryEditForm')),
                    processData: false,
                    contentType: false,
                    success: function(response) {
                        if (response.success) {
                            Swal.fire({
                                title: "Updated!",
                                text: response.message,
                                icon: "success",
                                confirmButtonText: "OK"
                            }).then(() => {
                                window.location.href = "{{ route('categories.index') }}";
                            });
                        } else {
                            toastr.error(response.message);
                        }
                    },
                    error: function(xhr) {
                        if (xhr.status === 422) {
                            var errors = xhr.responseJSON.errors;
                            var errorMessages = [];
                            $.each(errors, function(key, value) {
                                errorMessages.push(value[0]);
                            });
                            toastr.error(errorMessages.join('<br>'));
                        } else {
                            toastr.error('An unexpected error occurred.');
                        }
                    }
                });
            }
        });
    });
</script>
@endsection