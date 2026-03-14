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

                    <div class="col-md-6 card py-2 bg-light mb-3">
                        <label class="form-label"><strong>Title (English):</strong></label>
                        <p class="mb-0">{{ $announcement->title ?? '—' }}</p>
                    </div>

                    <div class="col-md-6 card py-2 bg-light mb-3">
                        <label class="form-label"><strong>Title (Hindi):</strong></label>
                        <p class="mb-0">{{ $announcement->title_hi ?? '—' }}</p>
                    </div>

                    <div class="col-md-6 card py-2 bg-light mb-3">
                        <label class="form-label"><strong>Description (English):</strong></label>
                        <p class="mb-0">{{ $announcement->description ?? '—' }}</p>
                    </div>

                    <div class="col-md-6 card py-2 bg-light mb-3">
                        <label class="form-label"><strong>Description (Hindi):</strong></label>
                        <p class="mb-0">{{ $announcement->description_hi ?? '—' }}</p>
                    </div>

                    <div class="col-md-6 card py-2 bg-light mb-3">
                        <label class="form-label"><strong>Type:</strong></label>
                        <p class="mb-0">{{ ucfirst($announcement->file_or_link) ?? '—' }}</p>
                    </div>

                    @if ($announcement->file_or_link === 'file')
                    <div class="col-md-6 card py-2 bg-light mb-3">
                        <label class="form-label"><strong>File (English):</strong></label>
                        @if ($announcement->file_name)
                        <p class="mb-0">
                            <a href="{{ asset('storage/' . Config::get('file_paths')['ANNOUNCEMENT_FILE_EN_PATH'] . '/' . $announcement->file_name) }}" target="_blank">View Document</a>
                        </p>
                        @else
                        <p class="text-muted mb-0">No file uploaded.</p>
                        @endif
                    </div>

                    <div class="col-md-6 card py-2 bg-light mb-3">
                        <label class="form-label"><strong>File (Hindi):</strong></label>
                        @if ($announcement->file_name_hi)
                        <p class="mb-0">
                            <a href="{{ asset('storage/' . Config::get('file_paths')['ANNOUNCEMENT_FILE_HI_PATH'] . '/' . $announcement->file_name_hi) }}" target="_blank">View Document</a>
                        </p>
                        @else
                        <p class="text-muted mb-0">No Hindi file uploaded.</p>
                        @endif
                    </div>
                    @elseif ($announcement->file_or_link === 'link')
                    <div class="col-md-6 card py-2 bg-light mb-3">
                        <label class="form-label"><strong>Page Link:</strong></label>
                        <p class="mb-0">
                            <a href="{{ $announcement->page_link }}" target="_blank">{{ $announcement->page_link }}</a>
                        </p>
                    </div>
                    @endif

                    <div class="col-md-6 card py-2 bg-light mb-3">
                        <label class="form-label"><strong>Homepage Status:</strong></label>
                        <p class="mb-0">
                            {!! $announcement->status
                            ? '<span class="badge bg-primary">Active</span>'
                            : '<span class="badge bg-warning text-dark">Inactive</span>' !!}
                        </p>
                    </div>

                </div>
            </div>
        </div>

        <hr>

        <div class="card card-body">
            <div class="row">
                <div class="col-md-6 card py-2 bg-light mb-3">
                    <label class="form-label"><strong>Approval Status:</strong></label>
                    <p class="mb-0">
                        @if ($announcement->is_approved == 1)
                        <span class="badge bg-success">Approved</span>
                        @elseif ($announcement->is_approved == 2)
                        <span class="badge bg-danger">Rejected</span>
                        @else
                        <span class="badge bg-warning">Pending</span>
                        @endif
                    </p>
                </div>

                <div class="col-md-6 card py-2 bg-light mb-3">
                    <label class="form-label"><strong>Publish Status:</strong></label>
                    <p class="mb-0">
                        {!! $announcement->is_published
                        ? '<span class="badge bg-primary">Published</span>'
                        : '<span class="badge bg-warning text-dark">Not Published</span>' !!}
                    </p>
                </div>

                <div class="col-md-6 card py-2 bg-light mb-3">
                    <label class="form-label"><strong>Remarks:</strong></label>
                    <p class="mb-0">
                        {{ $announcement->remarks ?  $announcement->remarks :'No remarks available' }}
                    </p>
                </div>
            </div>
        </div>

        <div class="row">
            @can('approve announcement')
            <div class="col-md-6 col-12">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">
                            <i class="fa fa-check"></i> Approval Form
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-12">
                                <form id="approveForm">
                                    @csrf


                                    <div class="mb-3">
                                        <label class="form-label">Status <span class="text-danger">*</span></label>
                                        <select name="is_approved" class="form-control" required>
                                            <option value="">-- Select Status --</option>
                                            <option value="1">Approve</option>
                                            <option value="2">Reject</option>
                                        </select>
                                    </div>

                                    <div class="mb-3">
                                        <label class="form-label">Remarks</label>
                                        <textarea name="remarks" class="form-control" rows="3" required></textarea>
                                    </div>

                                    <div class="text-center">
                                        <button type="submit" class="btn btn-success">
                                            <i class="fa fa-check"></i> Submit
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            @endcan
            @can('publish announcement')
            <div class="col-md-6 col-12">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">
                            <i class="fa fa-paper-plane"></i> Publish Form
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-12">
                                <form id="publishForm">
                                    @csrf


                                    <div class="mb-3">
                                        <label class="form-label">Publish Status <span class="text-danger">*</span></label>
                                        <select name="is_published" class="form-control" required>
                                            <option value="">-- Select Option --</option>
                                            <option value="1">Publish</option>
                                            <option value="0">Unpublish</option>
                                        </select>
                                    </div>

                                    <div class="text-center">
                                        <button type="submit" class="btn btn-success">
                                            <i class="fa fa-upload"></i> Submit
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            @endcan
        </div>

    </div>
</div>
@endsection


@section('pages-scripts')
@can('approve announcement')
<script @cspNonce>
    $(document).ready(function() {
        // Approve Form Validation
        $("#approveForm").validate({
            rules: {
                is_approved: {
                    required: true,
                },
                remarks: {
                    required: function(element) {
                        return $("input[name='is_approved']:checked").val() == "0";
                    },
                    maxlength: 500
                }
            },
            messages: {
                is_approved: "Please select approval status.",
                remarks: {
                    required: "Remarks are required when rejecting.",
                    maxlength: "Remarks should not exceed 500 characters."
                }
            },
            submitHandler: function(form) {
                let formData = new FormData(form);

                $.ajax({
                    url: "{{ route('announcements.approve', $announcement->id) }}",
                    type: "POST",
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function(response) {
                        Swal.fire("Success!", response.message, "success").then(() => {
                            window.location.href = "{{ route('announcements.index') }}";
                        });
                    },
                    error: function(xhr) {
                        if (xhr.status === 422) {
                            let errors = xhr.responseJSON.errors;
                            let errorMsg = Object.values(errors).flat().join("<br>");
                            Swal.fire("Validation Error", errorMsg, "error");
                        } else {
                            Swal.fire("Error!", "Something went wrong!", "error");
                        }
                    }
                });

                return false;
            }
        });
    });
</script>
@endcan

@can('publish announcement')
<script @cspNonce>
    $(document).ready(function() {
        // Publish Form Validation
        $("#publishForm").validate({
            rules: {
                is_published: {
                    required: true,
                },
            },
            submitHandler: function(form) {
                let formData = new FormData(form);

                $.ajax({
                    url: "{{ route('announcements.publish', $announcement->id) }}",
                    type: "POST",
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function(response) {
                        Swal.fire("Success!", response.message, "success").then(() => {
                            window.location.href = "{{ route('announcements.index') }}";
                        });
                    },
                    error: function(xhr) {
                        if (xhr.status === 422) {
                            let errors = xhr.responseJSON.errors;
                            let errorMsg = Object.values(errors).flat().join("<br>");
                            Swal.fire("Validation Error", errorMsg, "error");
                        } else {
                            Swal.fire("Error!", "Something went wrong!", "error");
                        }
                    }
                });

                return false;
            }
        });
    });
</script>
@endcan
@endsection