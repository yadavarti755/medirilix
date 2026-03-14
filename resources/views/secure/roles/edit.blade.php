@extends('layouts.app_layout')

@section('content')
<!-- [ Page Header ] start -->
<x-page-header title="{{ $pageTitle }}" :backButton="true" />

<!-- [ Page Header ] end -->
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <form id="roleForm">
                    @csrf

                    <input type="hidden" name="role_id" value="{{ $role->id }}">

                    <div class="row">
                        <div class="mb-3 col-md-6 col-sm-6 col-12">
                            <label class="form-label">Role Name:</label>
                            <input type="text" name="name" class="form-control" placeholder="Enter role name" value="{{ $role->name }}" required />
                        </div>
                        <div class="mb-3 col-md-6 col-sm-6 col-12">
                            <label class="form-label">Landing Page URL:</label>
                            <input type="text" name="landing_page_url" class="form-control" placeholder="Enter landing page url" value="{{ $role->landing_page_url }}" />
                            <small class="text-danger">
                                Ex: /secure/dashboard/division
                            </small>
                        </div>
                    </div>

                    <div class="mb-3">
                        <div class="d-flex mb-3">
                            <label class="form-label me-3">Permissions:</label>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="selectAll">
                                <label class="form-check-label" for="selectAll">Select All</label>
                            </div>
                        </div>

                        <div class="form-check d-flex gap-3 flex-wrap flex-column">
                            @foreach($permissionGroups as $group => $permissions)
                            <div class="w-100">
                                <div class="d-flex align-items-center">
                                    <div class="form-check">
                                        <input class="form-check-input selectGroup" type="checkbox" data-group-name="{{ str_replace(' ', '', $group) }}" id="selectGroup_{{ str_replace(' ', '', $group) }}">
                                    </div>
                                    <label for="selectGroup_{{ str_replace(' ', '', $group) }}" class="mt-1 fw-semibold">{{ $group }} Permissions</label>
                                </div>
                                <hr class="mb-0 mt-2">
                            </div>

                            <div class="ps-4 d-flex flex-wrap gap-3">
                                @foreach($permissions as $permission)
                                <div class="form-check w-25">
                                    <input type="checkbox" name="permissions[]" value="{{ $permission->name }}" id="{{ $permission->name }}" class="form-check-input permissions permissions_for_{{ str_replace(' ', '', $group) }}"
                                        @if(in_array($permission->name, $rolePermissions)) checked @endif>
                                    <label class="form-check-label cursor-pointer" for="{{ $permission->name }}">{{ $permission->name }}</label>
                                </div>
                                @endforeach
                            </div>
                            @endforeach
                        </div>

                        <div id="permissions-error" class="text-danger"></div>
                    </div>

                    <div class="text-center mt-4">
                        <button type="submit" class="btn btn-success">
                            <i class="fa fa-save"></i> Update Role
                        </button>
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
        // Handle "Select All"
        $(document).on('click', '#selectAll', function() {
            $('.permissions').prop('checked', $(this).is(':checked'));
        });

        // Handle Group Selection
        $(document).on('click', '.selectGroup', function() {
            let groupName = $(this).data('group-name');
            $('.permissions_for_' + groupName).prop('checked', $(this).is(':checked'));
        });

        // Validate and Submit Form via AJAX
        $("#roleForm").validate({
            rules: {
                name: {
                    required: true,
                    minlength: 3
                },
                // "permissions[]": {
                //     required: true
                // }
            },
            errorPlacement: function(error, element) {
                if (element.attr("name") === "permissions[]") {
                    error.appendTo("#permissions-error");
                } else {
                    error.insertAfter(element);
                }
            },
            messages: {
                name: {
                    required: "Please enter a role name",
                    minlength: "Role name must be at least 3 characters"
                },
                "permissions[]": "Select at least one permission"
            },
            submitHandler: function(form) {
                $.ajax({
                    url: "{{ route('roles.update', $role->id) }}",
                    type: "POST",
                    data: $("#roleForm").serialize(),
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    beforeSend: function() {
                        showLoader();
                    },
                    success: function(response) {
                        hideLoader();
                        Swal.fire({
                            title: 'Success',
                            text: response.message,
                            icon: 'success',
                            confirmButtonText: 'OK'
                        }).then((result) => {
                            if (result.isConfirmed) {
                                window.location.href = "{{ route('roles.index') }}";
                            }
                        });
                    },
                    error: function(xhr) {
                        hideLoader();
                        if (xhr.status === 422) {
                            let errors = xhr.responseJSON.errors;
                            let errorMessage = Object.values(errors).map(err => err[0]).join('<br>');
                            Swal.fire('Validation Error', errorMessage, 'error');
                        } else {
                            Swal.fire('Error', 'Something went wrong. Try again.', 'error');
                        }
                    }
                });
            }
        });
    });
</script>
@endsection