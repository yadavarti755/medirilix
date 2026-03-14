@extends('layouts.app_layout')

@section('content')
<!-- [ Page Header ] start -->
<x-page-header title="Edit Page" :backButton="true" />
<!-- [ Page Header ] end -->

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <p>All (<span class="text-danger">*</span>) marked fields are mandatory.</p>
                <form id="pageForm" action="" method="POST" enctype="multipart/form-data">
                    @csrf


                    <div class="row">
                        <div class="col-md-6 col-12  mb-3">
                            <label class="form-label">Title: <span class="text-danger">*</span></label>
                            <input type="text" name="title" value="{{ old('title', $page->title) }}" class="form-control" />
                        </div>

                        <div class="col-md-6 col-12  mb-3">
                            <label class="form-label">Menu:</label>
                            <select name="menu_id" class="form-control">
                                <option value="">Select Menu</option>
                                @foreach($menus as $menu)
                                <option value="{{ $menu->id }}" {{ $page->menu_id == $menu->id ? 'selected' : '' }}>
                                    @if ($menu->parent_id)
                                    &nbsp;&nbsp;&nbsp;&nbsp; {{ $menu->title }}
                                    @else
                                    {{ $menu->title }}
                                    @endif
                                </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-12 col-12 mb-3">
                            <div class="mb-3">
                                <label class="form-label">Content:</label>
                                <textarea name="content" class="form-control" id="page-editor" contenteditable="true">{!! $page->content !!}</textarea>
                            </div>
                        </div>
                    </div>

                    <div class="text-center">
                        <button type="submit" class="btn btn-success"><i class="fa fa-save"></i> Update Page</button>
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

                // Submit the ajax request
                $.ajax({
                    url: "{{ route('pages.update', $page->id) }}",
                    type: "POST",
                    data: formData,
                    contentType: false,
                    processData: false,
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(response) {
                        if (response.success) {
                            Swal.fire({
                                title: "Updated!",
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