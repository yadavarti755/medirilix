@extends('layouts.app_layout')

@section('content')
<!-- [ Page Header ] start -->
<x-page-header title="{{ $pageTitle ?? 'Refunds' }}" />

<!-- [ Page Header ] end -->
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <div class="dt-responsive">
                    <table id="datatable" class="table table-bordered table-striped w-100">
                        <thead>
                            <tr>
                                <th>Order #</th>
                                <th>Product</th>
                                <th>Amount</th>
                                <th>Status</th>
                                <th>Created At</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

@can('edit refund')
<!-- Status Update Modal -->
<div class="modal fade" id="formModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Update Refund Status</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="form">
                @csrf
                <div class="modal-body">
                    <div class="form-group row mb-3">
                        <div class="col-12">
                            <label class="form-label">Status <span class="text-danger">*</span></label>
                            <select class="form-select" id="refund_status" name="refund_status" required>
                                @foreach($refund_statuses as $key => $status)
                                @if(str_contains($key, 'REFUND'))
                                <option value="{{ $key }}">{{ $status }}</option>
                                @endif
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="form-group row mb-3">
                        <div class="col-12">
                            <label class="form-label">Remarks</label>
                            <textarea class="form-control" id="remarks" name="remarks" rows="3"></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary" id="formSubmitBtn">Save changes</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endcan
@endsection

@section('pages-scripts')
<script @cspNonce>
    let dataTable;
    let recordId;
    $(document).ready(function() {
        dataTable = $('#datatable').DataTable({
            processing: true,
            serverSide: true,
            ordering: false,
            scrollX: true,
            ajax: {
                url: "{{ route('refunds.fetch-for-datatable') }}",
                type: "GET",
                data: function(d) {
                    d._token = $('meta[name="csrf-token"]').attr('content');
                }
            },
            columns: [{
                    data: 'order_number',
                    name: 'order_number'
                },
                {
                    data: 'product_name',
                    name: 'product_name'
                },
                {
                    data: 'refund_amount',
                    name: 'refund_amount'
                },
                {
                    data: 'refund_status',
                    name: 'refund_status'
                },
                {
                    data: 'created_at',
                    name: 'created_at'
                },
                {
                    data: 'action',
                    name: 'action',
                    orderable: false,
                    searchable: false
                }
            ]
        });

        $('#formModal').on('hidden.bs.modal', function() {
            $('#form').trigger('reset');
            recordId = null;
        });

        @can('edit refund')
        $(document).on('click', '.btn-edit-status', function() {
            var id = $(this).data('id');
            if (id) {
                $.ajax({
                    url: "{{ route('refunds.fetch-one', ':id') }}".replace(':id', id),
                    type: 'GET',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(response) {
                        if (response.success) {
                            var data = response.data;
                            recordId = data.id;
                            $('#refund_status').val(data.refund_status);
                            $('#remarks').val(data.remarks);
                            $('#formModal').modal('show');
                        } else {
                            toastr.error("Error while fetching data");
                        }
                    },
                    error: function(xhr) {
                        Swal.fire({
                            title: "Error!",
                            text: "Something went wrong. Please try again.",
                            icon: "error"
                        });
                    }
                });
            }
        });

        $('#form').on('submit', function(e) {
            e.preventDefault();
            if (!recordId) return;

            var formData = new FormData(this);
            var url = "{{ route('refunds.update-status', ':id') }}".replace(':id', recordId);

            $.ajax({
                url: url,
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    if (response.success) {
                        $('#formModal').modal('hide');
                        Swal.fire({
                            title: 'Success',
                            text: response.message,
                            icon: 'success'
                        }).then(() => dataTable.ajax.reload());
                    } else {
                        Swal.fire({
                            title: 'Error',
                            text: response.message,
                            icon: 'error'
                        });
                    }
                },
                error: function(xhr) {
                    Swal.fire({
                        title: 'Error',
                        text: 'Something went wrong',
                        icon: 'error'
                    });
                }
            });
        });
        @endcan
    });
</script>
@endsection