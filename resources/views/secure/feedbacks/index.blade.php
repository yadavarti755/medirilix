@extends('layouts.app_layout')

@section('content')
<!-- [ Page Header ] start -->
<x-page-header title="{{ $pageTitle }}" button="{!! (isset($button))?$button:'' !!}" />
<!-- [ Page Header ] end -->

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h5>Filter Feedbacks</h5>
            </div>
            <div class="card-body">
                <div class="row align-items-end">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="start_date">Start Date</label>
                            <input type="date" class="form-control" id="start_date" name="start_date">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="end_date">End Date</label>
                            <input type="date" class="form-control" id="end_date" name="end_date">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <button type="button" id="filter-btn" class="btn btn-primary">
                                <i class="feather icon-filter"></i> Filter
                            </button>
                            <button type="button" id="reset-btn" class="btn btn-secondary">
                                <i class="feather icon-refresh-cw"></i> Reset
                            </button>
                            <button type="button" id="export-btn" class="btn btn-success">
                                <i class="feather icon-download"></i> Export to Excel
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <div class="dt-responsive">
                    <table id="feedbacks-datatable" class="table table-striped table-bordered">
                        <thead>
                            <tr>
                                <th>Sl. No.</th>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Mobile No.</th>
                                <th width="30%">Message</th>
                                <th>Submitted On</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('pages-scripts')
<script @cspNonce>
    $(document).ready(function() {
        var feedbackTable = $('#feedbacks-datatable').DataTable({
            processing: true,
            serverSide: true,
            ordering: false,
            ajax: {
                url: "{{ route('feedbacks.fetch-for-datatable') }}",
                type: "POST",
                data: function(d) {
                    d._token = $('meta[name="csrf-token"]').attr('content');
                    d.start_date = $('#start_date').val();
                    d.end_date = $('#end_date').val();
                }
            },
            columns: [{
                    data: null,
                    name: 'id',
                    render: function(data, type, row, meta) {
                        return meta.row + 1;
                    },
                    width: '8%',
                },
                {
                    data: 'name',
                    name: 'name',
                },
                {
                    data: 'email',
                    name: 'email',
                },
                {
                    data: 'mobile_no',
                    name: 'mobile_no',
                },
                {
                    data: 'message_brief',
                    name: 'message',
                },
                {
                    data: 'created_at_formatted',
                    name: 'created_at',
                },
                {
                    data: 'action',
                    name: 'action',
                    orderable: false,
                    searchable: false
                }
            ]
        });

        // Delete feedback
        $(document).on('click', '.delete-feedback', function() {
            let contactDetailId = $(this).data('id');

            Swal.fire({
                title: "Are you sure?",
                text: "You want to delete this feedback.",
                icon: "warning",
                showCancelButton: true,
                confirmButtonText: "Yes",
                cancelButtonText: "Cancel"
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: "{{ route('feedbacks.destroy', ':id') }}".replace(':id', contactDetailId),
                        type: "POST",
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        beforeSend: function() {
                            showLoader();
                        },
                        success: function(response) {
                            hideLoader();
                            Swal.fire("Deleted!", response.message, "success");
                            $('#feedbacks-datatable').DataTable().ajax.reload();
                        },
                        error: function() {
                            hideLoader();
                            Swal.fire("Error!", "Something went wrong!", "error");
                        }
                    });
                }
            });
        });

        // Filter button
        $('#filter-btn').on('click', function() {
            const startDate = $('#start_date').val();
            const endDate = $('#end_date').val();

            if (startDate && endDate && new Date(startDate) > new Date(endDate)) {
                Swal.fire("Error!", "End date must be greater than or equal to start date.", "error");
                return false;
            }

            feedbackTable.ajax.reload();
        });

        // Reset button
        $('#reset-btn').on('click', function() {
            $('#start_date').val('');
            $('#end_date').val('');
            feedbackTable.ajax.reload();
        });

        // Export button
        $('#export-btn').on('click', function() {
            const startDate = $('#start_date').val();
            const endDate = $('#end_date').val();

            if (startDate && endDate && new Date(startDate) > new Date(endDate)) {
                Swal.fire("Error!", "End date must be greater than or equal to start date.", "error");
                return false;
            }

            let exportUrl = "{{ route('feedbacks.export.excel') }}";
            const params = new URLSearchParams();

            if (startDate) {
                params.append('start_date', startDate);
            }
            if (endDate) {
                params.append('end_date', endDate);
            }

            if (params.toString()) {
                exportUrl += '?' + params.toString();
            }

            window.open(exportUrl, '_blank');
        });
    });
</script>
@endsection