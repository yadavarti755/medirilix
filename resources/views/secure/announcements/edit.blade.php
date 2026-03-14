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
                    All (<span class="text-danger">*</span>) marked fields are required.
                </p>
                <form id="announcementForm" enctype="multipart/form-data">
                    @csrf


                    <div class="row">
                        <div class="col-md-6 col-12 mb-3">
                            <label class="form-label">Title (English): <span class="text-danger">*</span></label>
                            <input type="text" name="title" class="form-control" value="{{ $announcement->title }}" />
                        </div>

                        <div class="col-md-6 col-12 mb-3">
                            <label class="form-label">Title (Hindi): <span class="text-danger">*</span></label>
                            <input type="text" name="title_hi" class="form-control" value="{{ $announcement->title_hi }}" />
                        </div>

                        <div class="col-md-6 col-12 mb-3">
                            <label class="form-label">Description (English):</label>
                            <textarea name="description" class="form-control" rows="3">{{ $announcement->description }}</textarea>
                        </div>

                        <div class="col-md-6 col-12 mb-3">
                            <label class="form-label">Description (Hindi):</label>
                            <textarea name="description_hi" class="form-control" rows="3">{{ $announcement->description_hi }}</textarea>
                        </div>

                        <div class="col-md-6 col-12 mb-3">
                            <label class="form-label">Type: <span class="text-danger">*</span></label>
                            <select name="file_or_link" id="file_or_link" class="form-control">
                                <option value="">-- Select --</option>
                                <option value="file" {{ $announcement->file_or_link == 'file' ? 'selected' : '' }}>File</option>
                                <option value="link" {{ $announcement->file_or_link == 'link' ? 'selected' : '' }}>Link</option>
                            </select>
                        </div>

                        {{-- File Upload --}}
                        <div class="col-md-6 col-12 mb-3 file-fields {{ $announcement->file_or_link == 'file' ? '' : 'd-none' }}">
                            <label class="form-label">File Name (English):</label>
                            <input type="file" name="file_name" class="form-control" />

                            @if ($announcement->file_name)
                            <div class="mt-2">
                                <strong>Current File:</strong>
                                <a href="{{ asset('storage/announcements/' . $announcement->file_name) }}" target="_blank">{{ $announcement->file_name }}</a>
                            </div>
                            @endif
                        </div>

                        <div class="col-md-6 col-12 mb-3 file-fields {{ $announcement->file_or_link == 'file' ? '' : 'd-none' }}">
                            <label class="form-label">File Name (Hindi):</label>
                            <input type="file" name="file_name_hi" class="form-control" />

                            @if ($announcement->file_name_hi)
                            <div class="mt-2">
                                <strong>Current Hindi File:</strong>
                                <a href="{{ asset('storage/announcements/' . $announcement->file_name_hi) }}" target="_blank">{{ $announcement->file_name_hi }}</a>
                            </div>
                            @endif
                        </div>

                        {{-- Page Link --}}
                        <div class="col-md-6 mb-3 link-field {{ $announcement->file_or_link == 'link' ? '' : 'd-none' }}">
                            <label class="form-label">Page Link:</label>
                            <input type="url" name="page_link" class="form-control" value="{{ $announcement->page_link }}" />
                        </div>

                        <div class="col-md-6 col-12 mb-3">
                            <label class="form-label">Homepage Status: <span class="text-danger">*</span></label>
                            <select name="status" id="status" class="form-control">
                                <option value="">-- Select --</option>
                                <option value="1" {{ $announcement->status == 1 ? 'selected' : '' }}>Active</option>
                                <option value="0" {{ $announcement->status == 0 ? 'selected' : '' }}>Inactive</option>
                            </select>
                        </div>

                        <div class="col-12 text-center mt-3">
                            <button type="submit" class="btn btn-primary"><i class="fa fa-save"></i> Update</button>
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
        // Toggle file/link fields based on type
        $('#file_or_link').on('change', function() {
            const type = $(this).val();
            if (type === 'file') {
                $('.file-fields').removeClass('d-none');
                $('.link-field').addClass('d-none');
            } else if (type === 'link') {
                $('.file-fields').addClass('d-none');
                $('.link-field').removeClass('d-none');
            } else {
                $('.file-fields, .link-field').addClass('d-none');
            }
        });

        $("#announcementForm").validate({
            rules: {
                title: {
                    required: true
                },
                title_hi: {
                    required: true
                },
                file_or_link: {
                    required: true
                }
            },
            submitHandler: function(form) {
                let formData = new FormData(form);

                $.ajax({
                    url: "{{ route('announcements.update', $announcement->id) }}",
                    method: "POST",
                    data: formData,
                    contentType: false,
                    processData: false,
                    success: function(response) {
                        if (response.success) {
                            Swal.fire({
                                title: "Updated!",
                                text: response.message,
                                icon: "success",
                                confirmButtonText: "OK"
                            }).then(() => {
                                window.location.href = "{{ route('announcements.index') }}";
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
                                text: "Something went wrong. Please try again.",
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