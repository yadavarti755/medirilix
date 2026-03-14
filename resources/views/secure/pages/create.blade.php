<!-- resources/views/secure/pages/create.blade.php -->

@extends('layouts.app_layout')

@section('content')
<!-- [ Page Header ] start -->
<x-page-header title="{{ $pageTitle }}" :backButton="true" />
<!-- [ Page Header ] end -->

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <p>
                    All (<span class="text-danger">*</span>) marked fields are mandatory.
                </p>
                <form id="pageForm" action="" method="POST">
                    @csrf

                    <div class="row">
                        <div class="col-md-6 col-12 mb-3">
                            <label class="form-label">Title: <span class="text-danger">*</span></label>
                            <input type="text" name="title" class="form-control" />
                        </div>

                        <div class="col-md-6 col-12 mb-3">
                            <label class="form-label">Menu:</label>
                            <select name="menu_id" class="form-control">
                                <option value="">Select Menu</option>
                                @foreach($menus as $menu)
                                <option value="{{ $menu->id }}">
                                    @if ($menu->parent_id)
                                    &nbsp;&nbsp;&nbsp;&nbsp; {{ $menu->title }}
                                    @else
                                    {{ $menu->title }}
                                    @endif
                                </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="col-md-12 col-12 mb-3">
                        <div class="mb-3">
                            <label class="form-label">Content (En):</label>
                            <textarea name="content" class="form-control" id="page-editor"></textarea>
                        </div>
                    </div>

                    <div class="text-center">
                        <button type="submit" class="btn btn-primary"><i class="fa fa-plus"></i> Create Page</button>
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
        $('#pageForm').validate({
            rules: {
                title: {
                    required: true,
                    maxlength: 255
                }
            },
            submitHandler: function(form) {
                let formData = new FormData(document.getElementById('pageForm'))
                // Loop through each editor instance to add its data to FormData
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

                $.ajax({
                    url: "{{ route('pages.store') }}",
                    type: "POST",
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function(response) {
                        if (response.success) {
                            Swal.fire({
                                title: "Success!",
                                text: response.message,
                                icon: "success",
                                confirmButtonText: "OK"
                            }).then(() => {
                                window.location.href = response.redirect_url;
                            });
                        } else {
                            toastr.error(response.message);
                        }
                    },
                    error: function(xhr) {
                        if (xhr.status === 422) {
                            let errors = xhr.responseJSON.errors;
                            let errorMessages = Object.values(errors).flat().join("<br>");
                            Swal.fire({
                                title: "Validation Error",
                                html: errorMessages,
                                icon: "error"
                            });
                        } else {
                            Swal.fire({
                                title: "Error!",
                                text: xhr.responseJSON.message || "Something went wrong. Please try again.",
                                icon: "error"
                            });
                        }
                    }
                });
            }
        });
    });
</script>
@endsection