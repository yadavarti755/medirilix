function fetchStatesUsingCountry(countryId, elementId = null) {
    $.ajax({
        url: base_url + "/states/country/" + countryId,
        type: "POST",
        data: {
            _token: $("meta[name=csrf-token]").attr("content"),
        },
        success: function (response) {
            if (response.status == true) {
                let data = response.data;

                let options = '<option value="">Select State</option>';
                data.forEach((row) => {
                    options +=
                        '<option value="' +
                        row.id +
                        '">' +
                        row.name +
                        "</option>";
                });

                $("#" + elementId).html(options);
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
}
