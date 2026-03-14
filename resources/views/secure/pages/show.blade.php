@extends('layouts.app_layout')

@section('content')
<!-- [ Page Header ] start -->
<x-page-header title="View Page" :backButton="true" />
<!-- [ Page Header ] end -->

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6 col-12 mb-3 card py-2 bg-light mb-3">
                        <label class="form-label">Title:</label>
                        <p class="mb-0">{{ $page->title }}</p>
                    </div>

                    <div class="col-md-6 col-12 mb-3 card py-2 bg-light mb-3">
                        <label class="form-label">Menu:</label>
                        <p class="mb-0">
                            {{ optional($page->menu)->title ?? 'N/A' }}
                        </p>
                    </div>
                </div> <!-- row -->
            </div> <!-- card-body -->
        </div> <!-- card -->

        <div class="card card-body">
            <div class="row">
                <div class="col-md-12 col-12 mb-3">
                    <ul class="nav nav-tabs mb-3 page-creation-tab" id="myTab" role="tablist">
                        <li class="nav-item">
                            <a class="nav-link active" id="home-tab" data-bs-toggle="tab" href="#home"
                                role="tab" aria-controls="home" aria-selected="true"><i class="ti ti-file"></i> Page Content</a>
                        </li>
                    </ul>

                    <div class="tab-content" id="myTabContent">
                        <div class="tab-pane fade show active" id="home" role="tabpanel" aria-labelledby="home-tab">
                            <div class="mb-3" style="max-height: 400px; overflow-y: auto;">
                                <label class="form-label">Content:</label>
                                <div class="border p-3">
                                    @if ($page->content)
                                    {!! $page->content !!}
                                    @else
                                    <span class="text-danger"> No content available</span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div> <!-- tab-content -->
                </div> <!-- col-12 -->
            </div>
        </div>

        <div class="card card-body">
            <div class="row">


                <div class="col-md-6 card py-2 bg-light mb-3">
                    <label class="form-label"><strong>Publish Status:</strong></label>
                    <p class="mb-0">
                        {!! $page->is_published
                        ? '<span class="badge bg-success">Published</span>'
                        : '<span class="badge bg-warning text-dark">Not Published</span>' !!}
                    </p>
                </div>


            </div>
        </div>

        <div class="row">

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

    </div> <!-- col -->
</div> <!-- row -->
@endsection

@section('pages-scripts')


@can('publish government portal')
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
                    url: "{{ route('pages.publish', $page->id) }}",
                    type: "POST",
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function(response) {
                        Swal.fire("Success!", response.message, "success").then(() => {
                            window.location.href = "{{ route('pages.index') }}";
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