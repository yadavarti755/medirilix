@props(['categories'])

<form action="" method="POST" id="categoryForm" enctype="multipart/form-data">
    @csrf
    <div class="row">
        <div class="form-group col-md-6 col-12 mb-3">
            <label class="form-label" for="parent_id">Parent Category</label>
            <select name="parent_id" id="parent_id" class="form-control">
                <option value="">None</option>
                @php renderCategoryOptions($categories) @endphp
            </select>
        </div>
        <div class="form-group col-md-6 col-12 mb-3">
            <label class="form-label" for="order">Order <span class="text-danger">*</span></label>
            <input type="number" name="order" id="order" class="form-control">
        </div>
        <div class="form-group col-md-6 col-12 mb-3">
            <label class="form-label" for="image">Image <span class="text-danger">*</span> </label>
            <input type="file" name="image" id="image" class="form-control file-input">
        </div>
        <div class="form-group col-md-6 col-12 mb-3">
            <label class="form-label" for="name">Name <span class="text-danger">*</span> </label>
            <input type="text" name="name" id="name" class="form-control">
        </div>
        <div class="form-group col-md-6 col-12 mb-3">
            <label class="form-label" for="is_published">Is Published <span class="text-danger">*</span> </label>
            <select name="is_published" id="is_published" class="form-control">
                <option value="">Select</option>
                <option value="1">Publish</option>
                <option value="0">Unpublish</option>
            </select>
        </div>
        <div class="form-group col-12 mb-3">
            <label class="form-label" for="description">Description</label>
            <textarea name="description" id="description" class="form-control" rows="3"></textarea>
        </div>
    </div>
    <div class="text-center">
        <button type="submit" class="btn btn-success">
            <i class="fas fa-save"></i> Save Category
        </button>
    </div>
</form>

@section('category_form_script')
<script @cspNonce>
    $(document).ready(function() {
        // Initialize jQuery Validation
        $('#categoryForm').validate({
            rules: {
                parent_id: {
                    required: false
                },
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
                let submitBtn = $(form).find('button[type="submit"]');
                let originalText = submitBtn.html();
                submitBtn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Saving...');

                $.ajax({
                    url: "{{ route('categories.store') }}",
                    type: "POST",
                    data: new FormData(form),
                    processData: false,
                    contentType: false,
                    success: function(response) {
                        submitBtn.prop('disabled', false).html(originalText);
                        
                        if (response.success) {
                            // Check if form is inside a modal
                            if ($(form).parents('.modal').length) {
                                // Close modal and reset form
                                $('#categoryForm')[0].reset();
                                $(form).parents('.modal').modal('hide');
                                
                                // Append new category to the category dropdown if available
                                if (response.category) {
                                    let newOption = new Option(response.category.name, response.category.id, true, true);
                                    $('select[name="category_id"]').append(newOption).trigger('change');
                                    // Also update parent_id in this component just in case
                                    let newParentOption = new Option(response.category.name, response.category.id, false, false);
                                    $('#parent_id').append(newParentOption);
                                }
                                toastr.success(response.message);
                            } else {
                                // Not in modal (e.g. categories/create page), so reload the page
                                Swal.fire({
                                    title: "Success!",
                                    text: response.message,
                                    icon: "success",
                                    confirmButtonText: "OK"
                                }).then(() => {
                                    window.location.reload();
                                });
                            }
                        } else {
                            toastr.error(response.message);
                        }
                    },
                    error: function(xhr) {
                        submitBtn.prop('disabled', false).html(originalText);
                        
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
