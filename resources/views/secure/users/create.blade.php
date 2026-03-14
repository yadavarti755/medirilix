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

                <form id="userForm">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label">Name: <span class="text-danger">*</span></label>
                        <input type="text" name="name" class="form-control" />
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Email: <span class="text-danger">*</span></label>
                        <input type="email" name="email" class="form-control" />
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Mobile Number: <span class="text-danger">*</span></label>
                        <input type="text" name="mobile_number" class="form-control" />
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Assign Roles: <span class="text-danger">*</span></label>
                        <div class="d-flex gap-4 flex-wrap">
                            @foreach ($roles as $role)
                            <div class="d-flex gap-2">
                                <input type="radio" name="roles" value="{{ $role->name }}" id="{{ $role->name }}" class="role-select form-check-input" />
                                <label for="{{ $role->name }}">{{ $role->name }}</label>
                            </div>
                            @endforeach
                        </div>
                        <div id="roles-error" class="text-danger"></div> <!-- Custom error placement -->
                    </div>

                    <!-- Add this new section for permissions -->
                    <!-- <div class="mb-3 permissions-container" style="display: none;">
                        <hr>
                        <div class="d-flex gap-2 mb-3">
                            <label class="form-label">Role Permissions:</label>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="selectAll" name="selectAll">
                                <label class="form-check-label" for="selectAll">Select All</label>
                            </div>
                        </div>
                        <div id="permissions-list" class="d-flex flex-wrap gap-3 w-100"> -->
                    <!-- Permissions will be loaded here -->
                    <!-- </div>
                    </div> -->

                    <div class="text-center">
                        <button type="submit" class="btn btn-primary"><i class="fa fa-plus"></i> Create User</button>
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
        $("#userForm").validate({
            rules: {
                name: {
                    required: true
                },
                email: {
                    required: true,
                    email: true
                },
                mobile_number: {
                    required: true
                },
                password: {
                    required: true,
                    minlength: 6
                },
                roles: {
                    required: true
                }
            },
            messages: {
                name: "Please enter a name",
                email: "Please enter a valid email",
                password: {
                    required: "Password is required",
                    minlength: "Password must be at least 6 characters long"
                },
                roles: "Select at least one role"
            },
            errorPlacement: function(error, element) {
                if (element.attr("name") === "roles") {
                    error.appendTo("#roles-error"); // Append error below checkboxes
                } else {
                    error.insertAfter(element);
                }
            },
            submitHandler: function(form) {
                $.ajax({
                    url: "{{ route('users.store') }}",
                    method: "POST",
                    data: $(form).serialize(),
                    success: function(response) {
                        if (response.success) {
                            Swal.fire({
                                title: "Success!",
                                text: response.message,
                                icon: "success",
                                confirmButtonText: "OK"
                            }).then(() => {
                                window.location.href = "{{ route('users.index') }}";
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
            }
        });

        /**
         * On click select all
         */
        /** $(document).on('click', '#selectAll', function() {
            if ($(this).is(':checked')) {
                $('.permissions').prop('checked', true);
                $('.selectGroup').prop('checked', true);
            } else {
                $('.permissions').prop('checked', false);
                $('.selectGroup').prop('checked', false);
            }
        })

        $(document).on('click', '.selectGroup', function() {
            let groupName = $(this).data('group-name');
            if ($(this).is(':checked')) {
                $('.permissions_for_' + groupName).prop('checked', true);
            } else {
                $('.permissions_for_' + groupName).prop('checked', false);
            }
        })


        // Add event listener for role selection
        $(".role-select").on("change", function() {
            const roleName = $(this).val();
            if (roleName) {
                fetchRolePermissions(roleName);
            }
        });

        // Function to fetch permissions for a role
        function fetchRolePermissions(roleName) {
            $.ajax({
                url: "{{ route('roles.permissions') }}",
                method: "POST",
                data: {
                    role: roleName,
                    _token: $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    if (response.success) {
                        displayPermissions(response.permissions, response.permissionGroups);
                    } else {
                        toastr.error("Failed to load permissions");
                    }
                },
                error: function() {
                    toastr.error("Error loading permissions");
                }
            });
        }

        // Function to display permissions grouped by category
        function displayPermissions(permissions, permissionGroups) {
            const container = $("#permissions-list");
            container.empty();

            if (permissions.length === 0) {
                container.html("<p>No permissions assigned to this role.</p>");
                $(".permissions-container").show();
                return;
            }

            // Group permissions by their category
            let html = '';

            Object.keys(permissionGroups).forEach(group => {
                html += `
                <div class="w-100">
                    <div class="d-flex align-items-center">
                        <div class="form-check">
                            <input class="form-check-input selectGroup ${group.replace(' ', '')}" type="checkbox" data-group-name="${group.replace(' ', '')}" name="selectGroup" id="selectGroup_${group.replace(' ', '')}">
                        </div>
                        <label for="selectGroup_${group.replace(' ', '')}" class="mt-1 fw-semibold">${group} Permissions</label>
                    </div>
                    <hr class="mb-0 mt-2">
                </div>
                <div class="ps-4 d-flex flex-wrap gap-3 mb-3 w-100">`;

                permissionGroups[group].forEach(permission => {
                    const isChecked = permissions.includes(permission.name) ? 'checked' : '';
                    html += `
                    <div class="form-check w-25">
                        <input type="checkbox" name="permissions[]" value="${permission.name}" id="${permission.name}" class="form-check-input permissions permissions_for_${group}">
                        <label class="form-check-label" for="${permission.name}">${permission.name}</label>
                    </div>`;
                });

                html += `</div>`;
            });

            container.html(html);
            $(".permissions-container").show();
        }
            */
    });
</script>
@endsection