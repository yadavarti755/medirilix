@extends('layouts.app_layout')

@section('content')
<!-- [ Page Header ] start -->
@php
$addRoute = null; // Audit Logs typically don't have a 'create' button
@endphp
<x-page-header title="{{ $pageTitle ?? 'Audit Logs' }}" button="" />
<!-- [ Page Header ] end -->

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <div class="dt-responsive">
                    <table id="auditlog-datatable" class="table table-striped table-bordered nowrap">
                        <thead>
                            <tr>
                                <th>Sl. No.</th>
                                <th>Log Name</th>
                                <th>Event</th>
                                <th>Causer</th>
                                <th>Subject Type</th>
                                <th>Subject ID</th>
                                <th>Description</th>
                                <th>Changes</th>
                                <th>Created At</th>
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
        $('#auditlog-datatable').DataTable({
            processing: true,
            serverSide: true,
            ordering: false,
            scrollX: true,
            ajax: {
                url: "{{ route('audit-logs.fetch-for-datatable') }}",
                type: "POST",
                data: function(d) {
                    d._token = $('meta[name="csrf-token"]').attr('content');
                }
            },
            columns: [{
                    data: null,
                    name: 'id',
                    render: function(data, type, row, meta) {
                        return meta.row + 1;
                    }
                },
                {
                    data: 'log_name',
                    name: 'log_name'
                },
                {
                    data: 'event',
                    name: 'event'
                },
                {
                    data: 'causer',
                    name: 'causer',
                    render: function(data, type, row) {
                        return data ? data.name : '—';
                    }
                },
                {
                    data: 'subject_type',
                    name: 'subject_type'
                },
                {
                    data: 'subject_id',
                    name: 'subject_id'
                },
                {
                    data: 'description',
                    name: 'description'
                },
                {
                    data: 'changes',
                    name: 'changes'
                },
                {
                    data: 'created_at',
                    name: 'created_at'
                }
            ]
        });
    });
</script>
@endsection