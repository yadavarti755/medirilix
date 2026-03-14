$("#add_new_address").on("click", function () {
    $("#hidden_operation_type").val("ADD");
    $("#hidden_address_id").val("");
    $("#address_form").trigger("reset");
    $("#address-modal").modal("show");
});

$(document).on("change", "#country", function () {
    let id = $(this).val();
    fetchStatesUsingCountry(id, "state");
});

// On changing pin code
// $("#pin_code").on("keyup", function() {
//     let pincode = $(this).val();
//     if (pincode && pincode.length == 6) {
//         $.ajax({
//             url: base_url + "/pincode",
//             type: "POST",
//             data: {
//                 pincode: pincode,
//                 _token: $("meta[name=csrf-token]").attr("content")
//             },
//             beforeSend: function() {},
//             complete: function() {},
//             success: function(response) {
//                 if (response.status == true) {
//                     let data = response.data;
//                     $("#city").val(data.city_name);
//                     $('#state option[value="' + data.state_id + '"]').prop(
//                         "selected",
//                         true
//                     );
//                 } else {
//                     toastr.error("Something went wrong. Please try again.");
//                 }
//             }
//         });
//     }
// });

// Save New Address
$("#address_form").validate({
    errorElement: "span",
    errorClass: "text-danger validation-error",
    rules: {
        name: {
            required: true,
        },
        phone_number: {
            required: true,
        },
        address: {
            required: true,
        },
        locality: {
            required: true,
        },
        pin_code: {
            required: true,
        },
        city: {
            required: true,
        },
        state: {
            required: true,
        },
        country: {
            required: true,
        },
    },
    submitHandler: function (form, event) {
        event.preventDefault();
        var formData = new FormData(document.getElementById("address_form"));
        var url = base_url + "/address/save";
        if ($("#hidden_operation_type").val() == "EDIT") {
            url = base_url + "/address/update";
        }
        $.ajax({
            url: url,
            type: "POST",
            processData: false,
            contentType: false,
            data: formData,
            beforeSend: function () {},
            complete: function () {},
            success: function (response) {
                if (response.success) {
                    Swal.fire({
                        title: "Success!",
                        text: response.message,
                        icon: "success",
                        confirmButtonText: "OK",
                    }, function () {
                        window.location.reload();
                    });
                } else {
                    toastr.error(response.message);
                }
            },
            error: function (xhr) {
                if (xhr.status === 422) {
                    let errors = xhr.responseJSON.errors;
                    let errorMessages = Object.values(errors)
                        .flat()
                        .join("<br>");
                    Swal.fire({
                        title: "Validation Error",
                        html: errorMessages,
                        icon: "error",
                    });
                } else {
                    Swal.fire({
                        title: "Error!",
                        text:
                            xhr.responseJSON.message ||
                            "Something went wrong. Please try again.",
                        icon: "error",
                    });
                }
            },
        });
    },
});

// Edit Address
$(document).on("click", ".btn_edit_address", function () {
    var id = $(this).attr("id");

    $.ajax({
        url: base_url + "/ajax/address/get-single-address",
        type: "POST",
        data: {
            id: id,
            _token: $("meta[name=csrf-token]").attr("content"),
        },
        success: function (response) {
            if (response.status == true) {
                let address = response.address;

                $("#hidden_address_id").val(address.id);
                $("#hidden_operation_type").val("EDIT");
                $("#name").val(address.person_name);
                $("#phone_number").val(address.person_contact_number);
                $("#alt_phone_number").val(address.person_alt_contact_number);
                $("#address").val(address.address);
                $("#locality").val(address.locality);
                $("#pin_code").val(address.pincode);
                $("#landmark").val(address.landmark);
                $("#city").val(address.city);

                $("#state option[ value = '" + address.state + "' ]").prop(
                    "selected",
                    true,
                );

                $("#address-modal").modal("show");
            } else if (response.status == false) {
                toastr.error(response.message);
            } else {
                toastr.error(response.message);
            }
        },
        error: function (errors) {
            toastr.error("Server is not responding. Please try again.");
        },
    });
});

// Delete Address
$(document).on("click", ".btn_delete_address", function () {
    var id = $(this).attr("id");

    if (id) {
        Swal.fire({
            icon: "question",
            title: "Are you sure?",
            text: "You want to delete the image?",
            showCancelButton: true,
            confirmButtonColor: "#d33",
            cancelButtonColor: "#555",
            confirmButtonText: "Delete",
            cancelButtonText: "Cancel",
        }).then((result) => {
            /* Read more about isConfirmed, isDenied below */
            if (result.value) {
                $.ajax({
                    url: base_url + "/ajax/address/delete",
                    type: "POST",
                    data: {
                        id: id,
                        _token: $("meta[name=csrf-token]").attr("content"),
                    },
                    success: function (response) {
                        if (response.status == true) {
                            $.confirm({
                                title: "Address Deleted",
                                type: "green",
                                content: response.message,
                                buttons: {
                                    ok: {
                                        text: "Ok",
                                        btnClass: "btn-custom",
                                        keys: ["enter"],
                                        action: function () {
                                            window.location.reload();
                                        },
                                    },
                                },
                            });
                        } else if (response.status == false) {
                            toastr.error(response.message);
                        } else {
                            toastr.error(response.message);
                        }
                    },
                    error: function (errors) {
                        toastr.error(
                            "Server is not responding. Please try again.",
                        );
                    },
                });
            }
        });
    } else {
        toastr.error("Something went wrong. Please try again.");
    }
});
