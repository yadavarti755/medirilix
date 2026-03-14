@extends('layouts.app_layout')

@section('content')
<x-page-header title="Edit User" :backButton="true" />

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <form id="editUserForm">
                    @csrf


                    <div class="mb-3">
                        <label class="form-label">Name:</label>
                        <input type="text" name="name" class="form-control" value="{{ $user->name }}" />
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Email:</label>
                        <input type="email" name="email" class="form-control" value="{{ $user->email }}" />
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Mobile Number: <span class="text-danger">*</span></label>
                        <input type="text" name="mobile_number" class="form-control" value="{{ $user->mobile_number }}" />
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Assign Roles:</label>
                        <div class="d-flex gap-4">
                            @foreach ($roles as $role)
                            <div>
                                <input type="radio" name="roles" value="{{ $role->name }}" class="role-select" id="{{ $role->name }}"
                                    {{ in_array($role->name, $userRoles) ? 'checked' : '' }} />
                                <label for="{{ $role->name }}">{{ $role->name }}</label>
                            </div>
                            @endforeach
                        </div>
                        <div id="roles-error" class="text-danger"></div>
                    </div>

                    <!-- Add this new section for permissions -->
                    <!-- <div class="mb-3 permissions-container" style="display: none;">
                        <label class="form-label">Role Permissions:</label>
                        <div id="permissions-list" class="d-flex flex-wrap gap-3"> -->
                    <!-- Permissions will be loaded here -->
                    <!-- </div>
                    </div> -->

                    <div class="text-center">
                        <button type="submit" class="btn btn-primary"><i class="fa fa-save"></i> Update User</button>
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
        $("#editUserForm").validate({
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
                roles: {
                    required: true
                }
            },
            messages: {
                name: "Please enter a name",
                email: "Please enter a valid email",
                roles: "Select at least one role"
            },
            errorPlacement: function(error, element) {
                if (element.attr("name") === "roles") {
                    error.appendTo("#roles-error");
                } else {
                    error.insertAfter(element);
                }
            },
            submitHandler: function(form) {
                $.ajax({
                    url: "{{ route('users.update', $user->id) }}",
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

        // $(document).on('click', '#selectAll', function() {
        //     if ($(this).is(':checked')) {
        //         $('.permissions').prop('checked', true);
        //         $('.selectGroup').prop('checked', true);
        //     } else {
        //         $('.permissions').prop('checked', false);
        //         $('.selectGroup').prop('checked', false);
        //     }
        // })

        // $(document).on('click', '.selectGroup', function() {
        //     let groupName = $(this).data('group-name');
        //     if ($(this).is(':checked')) {
        //         $('.permissions_for_' + groupName).prop('checked', true);
        //     } else {
        //         $('.permissions_for_' + groupName).prop('checked', false);
        //     }
        // })

        // // Store user's current permissions from the server


        // Load permissions for the initially selected role
        // const initialRole = $("input[name='roles']:checked").val();
        // if (initialRole) {
        //     fetchRolePermissions(initialRole);
        // }

        // // Add event listener for role selection
        // $(".role-select").on("change", function() {
        //     const roleName = $(this).val();
        //     if (roleName) {
        //         fetchRolePermissions(roleName);
        //     }
        // });

        // // Function to fetch permissions for a role
        // function fetchRolePermissions(roleName) {
        //     $.ajax({
        //         url: "{{ route('roles.permissions') }}",
        //         method: "POST",
        //         data: {
        //             role: roleName,
        //             _token: $('meta[name="csrf-token"]').attr('content')
        //         },
        //         success: function(response) {
        //             if (response.success) {
        //                 displayPermissions(response.permissions, response.permissionGroups);
        //             } else {
        //                 toastr.error("Failed to load permissions");
        //             }
        //         },
        //         error: function() {
        //             toastr.error("Error loading permissions");
        //         }
        //     });
        // }

        // // Function to display permissions grouped by category
        // function displayPermissions(rolePermissions, permissionGroups) {
        //     const container = $("#permissions-list");
        //     container.empty();
        //     if (Object.keys(permissionGroups).length === 0) {
        //         container.html("<p>No permissions available for this role.</p>");
        //         return;
        //     }

        //     // Group permissions by their category
        //     let html = '';

        //     Object.keys(permissionGroups).forEach(group => {
        //         html += `
        //             <div class="w-100">
        //                 <div class="d-flex align-items-center">
        //                     <div class="form-check">
        //                         <input class="form-check-input selectGroup" type="checkbox" 
        //                                data-group-name="${group.replace(' ', '')}" name="selectGroup" 
        //                                id="selectGroup_${group.replace(' ', '')}">
        //                     </div>
        //                     <label for="selectGroup_${group.replace(' ', '')}" class="mt-1 fw-semibold">${group} Permissions</label>
        //                 </div>
        //                 <hr class="mb-0 mt-2">
        //             </div>
        //             <div class="ps-4 d-flex flex-wrap gap-3 mb-3 w-100">`;

        //         let allCheckedInGroup = true;

        //         permissionGroups[group].forEach(permission => {
        //             // Check if this permission is either in the role's permissions OR in the user's direct permissions
        //             const isInRole = rolePermissions.includes(permission.name);
        //             const isUserPermission = userPermissions.includes(permission.name);
        //             const isChecked = isUserPermission ? 'checked' : (isInRole ? 'checked' : '');

        //             if (!isUserPermission && !isInRole) {
        //                 allCheckedInGroup = false;
        //             }

        //             html += `
        //                 <div class="form-check w-25">
        //                     <input type="checkbox" name="permissions[]" value="${permission.name}" 
        //                            id="${permission.name}" ${isChecked}
        //                            class="form-check-input permissions permissions_for_${group.replace(' ', '')}">
        //                     <label class="form-check-label cursor-pointer" for="${permission.name}">${permission.name}</label>
        //                 </div>`;
        //         });

        //         html += `</div>`;

        //         // Update the HTML to check the group checkbox if all permissions in the group are checked
        //         if (allCheckedInGroup) {
        //             html = html.replace(`id="selectGroup_${group.replace(' ', '')}"`, `id="selectGroup_${group.replace(' ', '')}" checked`);
        //         }
        //     });
        //     container.html(html);

        //     $(".permissions-container").show();
        // }

        // // Add event handlers for group checkboxes
        // $(".selectGroup").on("change", function() {
        //     const groupName = $(this).data("group-name");
        //     const isChecked = $(this).prop("checked");
        //     $(`.permissions_for_${groupName}`).prop("checked", isChecked);
        // });

        // // Add event handlers for individual permission checkboxes
        // $(".permissions").on("change", function() {
        //     const groupName = $(this).attr("class").split(" ").find(cls => cls.startsWith("permissions_for_")).replace("permissions_for_", "");
        //     const allChecked = $(`.permissions_for_${groupName}`).length === $(`.permissions_for_${groupName}:checked`).length;
        //     $(`#selectGroup_${groupName}`).prop("checked", allChecked);
        // });
    });
</script>
@endsection