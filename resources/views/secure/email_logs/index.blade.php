@extends('layouts.app_layout')

@section('content')
<!-- [ Page Header ] start -->
<x-page-header title="{{ $pageTitle }}" button="" />
<!-- [ Page Header ] end -->

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <div class="dt-responsive">
                    <table id="email-logs-datatable" class="table table-striped table-bordered nowrap">
                        <thead>
                            <tr>
                                <th>Sl. No.</th>
                                <th>Recipient Name</th>
                                <th>Recipient Email</th>
                                <th>Email Type</th>
                                <th>Subject</th>
                                <th>Status</th>
                                <th>Error Message</th>
                                <th>Sent At</th>
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
        $('#email-logs-datatable').DataTable({
            processing: true,
            serverSide: true,
            ordering: false,
            scrollX: true,
            ajax: {
                url: "{{ route('email-logs.fetch-for-datatable') }}",
                type: "POST",
                data: function(d) {
                    d._token = $('meta[name="csrf-token"]').attr('content');
                }
            },
            columns: [{
                    data: null,
                    name: 'sl_no',
                    render: function(data, type, row, meta) {
                        return meta.row + 1;
                    },
                    width: '5%',
                },
                {
                    data: 'recipient_name',
                    name: 'recipient_name',
                    defaultContent: '<span class="text-muted">N/A</span>'
                },
                {
                    data: 'recipient_email',
                    name: 'recipient_email'
                },
                {
                    data: 'email_type',
                    name: 'email_type'
                },
                {
                    data: 'subject',
                    name: 'subject',
                    render: function(data) {
                        return data ? data : '<span class="text-muted">N/A</span>';
                    }
                },
                {
                    data: 'status',
                    name: 'status',
                    render: function(data) {
                        if (data === 'sent') {
                            return '<span class="badge bg-success">Sent</span>';
                        } else {
                            return '<span class="badge bg-danger">Failed</span>';
                        }
                    }
                },
                {
                    data: 'error_message',
                    name: 'error_message',
                    render: function(data) {
                        return data ? data : '<span class="text-muted">-</span>';
                    }
                },
                {
                    data: 'sent_at',
                    name: 'sent_at'
                }
            ]
        });
    });
</script>
@endsection