@extends('layouts.app_layout')

@section('content')
<!-- [ Page Header ] start -->
<x-page-header title="{{ $pageTitle }}" />
<!-- [ Page Header ] end -->

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <div class="dt-responsive">
                    <table id="datatable" class="table table-striped table-bordered w-100">
                        <thead>
                            <tr>
                                <th>Sl. No.</th>
                                <th>User</th>
                                <th>Product</th>
                                <th>Reason</th>
                                <th>Status</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Modal --}}
<div class="modal fade" id="formModal" tabindex="-1" aria-labelledby="formModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title fs-5" id="formModalLabel">Update Return Status</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form class="form" id="form">
                <div class="modal-body">
                    @csrf
                    <div class="form-group row">
                        <div class="col-12">
                            <label for="return_status" class="form-label">Status <span class="text-danger">*</span></label>
                            <select name="return_status" id="return_status" class="form-control me-2" required>
                                @foreach (Config::get('constants.order_status_codes') as $code)
                                @if(str_contains($code, 'RETURN'))
                                <option value="{{ $code }}">{{ $code }}</option>
                                @endif
                                @endforeach
                                <option value="{{Config::get('constants.order_status_codes')['REFUND_INITIATED']}}">{{Config::get('constants.order_status_codes')['REFUND_INITIATED']}}</option>
                            </select>
                        </div>
                    </div>
                    <div class="form-group row mt-3" id="pickup-details-group" style="display: none;">
                        <div class="col-12">
                            <label for="return_pickup_details" class="form-label">Pickup Details <span class="text-danger">*</span></label>
                            <textarea name="return_pickup_details" id="return_pickup_details" class="form-control" rows="3"></textarea>
                        </div>
                    </div>

                    {{-- Refund Details --}}
                    <div class="form-group row mt-3" id="refund-details-group" style="display: none;">
                        <div class="col-12">
                            <label for="refund_amount" class="form-label">Refund Amount <span class="text-danger">*</span></label>
                            <input type="number" step="0.01" name="refund_amount" id="refund_amount" class="form-control mb-2" placeholder="Enter refund amount">

                            <label for="remarks" class="form-label">Refund Remarks</label>
                            <textarea name="remarks" id="remarks" class="form-control" rows="2" placeholder="Internal remarks for refund"></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary" id="formSubmitBtn">Update</button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection

@section('pages-scripts')
<script @cspNonce>
    let dataTable;
    let recordId;
    let productPrice = 0; // Store product price

    $(document).ready(function() {

        dataTable = $('#datatable').DataTable({
            processing: true,
            serverSide: true,
            ordering: false,
            scrollX: true,
            ajax: {
                url: "{{ route('return-requests.fetch.data') }}",
                type: "GET",
                data: function(d) {
                    d._token = $('meta[name="csrf-token"]').attr('content');
                }
            },
            columns: [{
                    data: null,
                    name: 'id',
                    width: '5%',
                    render: function(data, type, row, meta) {
                        return meta.row + 1;
                    }
                },
                {
                    data: 'user_name',
                    name: 'user_name'
                },
                {
                    data: 'product_name',
                    name: 'product_name'
                },
                {
                    data: 'return_reason',
                    name: 'return_reason'
                },
                {
                    data: 'return_status',
                    name: 'return_status'
                },
                {
                    data: 'action',
                    name: 'action',
                    width: '10%',
                    orderable: false,
                    searchable: false
                }
            ]
        });

        $('#formModal').on('hidden.bs.modal', function() {
            $('#form').trigger('reset');
            $('#pickup-details-group').hide();
            $('#refund-details-group').hide(); // Hide refund group
            $('#return_pickup_details').prop('required', false);
            $('#refund_amount').prop('required', false);
        });

        $('#return_status').on('change', function() {
            toggleDetails();
        });

        function toggleDetails() {
            let status = $('#return_status').val();

            // Pickup Details
            if (status === 'RETURN_APPROVED') {
                $('#pickup-details-group').show();
                $('#return_pickup_details').prop('required', true);
            } else {
                $('#pickup-details-group').hide();
                $('#return_pickup_details').prop('required', false);
            }

            // Refund Details
            if (status === "{{ Config::get('constants.order_status_codes')['REFUND_INITIATED'] ?? 'REFUND_INITIATED' }}") {
                $('#refund-details-group').show();
                $('#refund_amount').prop('required', true);
                if (!$('#refund_amount').val() && productPrice) {
                    $('#refund_amount').val(productPrice);
                }
            } else {
                $('#refund-details-group').hide();
                $('#refund_amount').prop('required', false);
            }
        }

        // On submitting the form
        $('#form').on('submit', function(event) {
            event.preventDefault();
            if (!recordId) return;

            var formData = new FormData(document.getElementById('form'));
            let url = "{{ route('return-requests.update-status', ':id') }}".replace(':id', recordId);
            formData.append('_method', 'PUT');

            $.ajax({
                url: url,
                type: 'POST',
                data: formData,
                contentType: false,
                processData: false,
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    if (response.success) {
                        Swal.fire({
                            title: "Success!",
                            text: response.message,
                            icon: "success",
                            confirmButtonText: "OK"
                        }).then(() => {
                            $('#formModal').modal('hide');
                            dataTable.ajax.reload();
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
        });

        // On click update status button
        $(document).on('click', '.btn-update-status', function() {
            var id = $(this).data('id');
            if (id) {
                $.ajax({
                    url: "{{ route('return-requests.fetch.one', ':id') }}".replace(':id', id),
                    type: 'GET',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(response) {
                        if (response.success == true) {
                            var data = response.data;
                            recordId = data.id;
                            $('#return_status').val(data.return_status);
                            $('#return_pickup_details').val(data.return_pickup_details);

                            // Store price
                            if (data.order_product_list) {
                                let total = parseFloat(data.order_product_list.total_price || 0);
                                let discount = parseFloat(data.order_product_list.discount_amount || 0);
                                let tax = parseFloat(data.order_product_list.tax_amount || 0);
                                productPrice = (total - discount + tax).toFixed(2);
                            } else {
                                productPrice = 0;
                            }

                            toggleDetails();

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
    });
</script>
@endsection