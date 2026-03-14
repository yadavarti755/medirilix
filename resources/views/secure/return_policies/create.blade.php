@extends('layouts.app_layout')

@section('content')
<x-page-header title="{{ $pageTitle }}" :backButton="true" />

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <p>All (<span class="text-danger">*</span>) marked fields are required.</p>
                <form id="createForm">
                    @csrf
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Title <span class="text-danger">*</span></label>
                            <input type="text" name="title" class="form-control" required placeholder="e.g. 7 Days Return">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Return Days <span class="text-danger">*</span></label>
                            <input type="number" name="return_till_days" class="form-control" required min="0" placeholder="e.g. 7">
                        </div>
                        <div class="col-12 mb-3">
                            <label class="form-label">Description</label>
                            <textarea name="return_description" id="page-editor" class="form-control" rows="4"></textarea>
                        </div>
                        <div class="col-12 text-center mt-3">
                            <button type="submit" class="btn btn-primary">
                                <i class="fa fa-save"></i> Save
                            </button>
                        </div>
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
        $("#createForm").validate({
            rules: {
                title: {
                    required: true
                },
                return_till_days: {
                    required: true
                }
            },
            submitHandler: function(form) {
                let formData = new FormData(form);

                // Update CKEditor content to textarea
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

                $.ajax({
                    url: "{{ route('return-policies.store') }}",
                    type: "POST",
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function(response) {
                        if (response.success) {
                            Swal.fire({
                                title: "Success!",
                                text: response.message,
                                icon: "success"
                            }).then(() => {
                                window.location.href = "{{ route('return-policies.index') }}";
                            });
                        } else {
                            toastr.error(response.message);
                        }
                    },
                    error: function(xhr) {
                        if (xhr.status === 422) {
                            var errors = xhr.responseJSON.errors;
                            var message = Object.values(errors).flat().join("<br>");
                            Swal.fire('Validation Error', message, 'error');
                        } else {
                            Swal.fire('Error', 'Something went wrong', 'error');
                        }
                    }
                });
            }
        });
    });
</script>
@endsection