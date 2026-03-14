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
                    <table id="auditlog-datatable" class="table table-striped table-bordered w-100">
                        <thead>
                            <tr>
                                <th>Sl. No.</th>
                                <th>Name</th>
                                <th>Role</th>
                                <th>Login At</th>
                                <th>Logout At</th>
                                <th>IP Address</th>
                                <th>Browser</th>
                                <th>Platform</th>
                                <th></th>
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
                url: "{{ route('authentication-logs.fetch-for-datatable') }}",
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
                    data: 'authenticatable.name',
                    name: 'authenticatable.name'
                },
                {
                    data: 'roles',
                    name: 'roles'
                },
                {
                    data: 'login_at',
                    name: 'login_at'
                },
                {
                    data: 'logout_at',
                    name: 'logout_at'
                },
                {
                    data: 'ip_address',
                    name: 'ip_address'
                },
                {
                    data: 'browser',
                    name: 'browser'
                },
                {
                    data: 'platform',
                    name: 'platform'
                },
                {
                    data: 'login_successful_desc',
                    name: 'login_successful'
                },

            ]
        });
    });
</script>
@endsection