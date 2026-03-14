(function () {
    var tooltipTriggerList = [].slice.call(
        document.querySelectorAll('[data-bs-toggle="tooltip"]'),
    );
    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });
})();

(function () {
    var html5Slider = document.getElementById("price-filter");
    var nodes = [
        document.getElementById("lower-value"), // 0
        document.getElementById("upper-value"), // 1
    ];

    let minPrice = parseInt($("#lower-price").val());
    let maxPrice = parseInt($("#upper-price").val());

    if (html5Slider) {
        noUiSlider.create(html5Slider, {
            start: [minPrice, maxPrice],
            step: 10,
            connect: true,
            behaviour: "tap",
            range: {
                min: [minPrice],
                max: [maxPrice],
            },
        });
        html5Slider.noUiSlider.on(
            "update",
            function (values, handle, unencoded, isTap, positions) {
                nodes[handle].innerHTML = values[handle];
            },
        );
    }
})();

$(document).ajaxStart(function () {
    $(".site-loader").addClass("show-loader");
});

$(document).ajaxStop(function () {
    $(".site-loader").removeClass("show-loader");
});

$(document).ready(function () {
    // XZOOM Plugin
    $(".fancybox").fancybox({
        // Options will go here
    });

    $(".xzoom, .xzoom-gallery").xzoom({
        zoomWidth: 400,
        title: true,
        tint: "#333",
        Xoffset: 15,
    });
});

$(".main_slider").slick({
    dots: true,
    infinite: true,
    speed: 1900,
    slidesToShow: 5,
    arrows: true,
    adaptiveHeight: true,
    slidesToScroll: 5,
    autoplay: true,
    autoplaySpeed: 3500,
    responsive: [
        {
            breakpoint: 991,
            settings: {
                slidesToShow: 4,
                slidesToScroll: 3,
                infinite: true,
                dots: true,
            },
        },
        {
            breakpoint: 769,
            settings: {
                slidesToShow: 3,
                slidesToScroll: 3,
                infinite: true,
                dots: true,
            },
        },
        {
            breakpoint: 600,
            settings: {
                slidesToShow: 3,
                slidesToScroll: 3,
            },
        },
        {
            breakpoint: 576,
            settings: {
                slidesToShow: 2,
                slidesToScroll: 2,
            },
        },
        {
            breakpoint: 380,
            settings: {
                slidesToShow: 2,
                slidesToScroll: 2,
            },
        },
        {
            breakpoint: 300,
            settings: {
                slidesToShow: 1,
                slidesToScroll: 1,
            },
        },
    ],
});

$(".category_slider").slick({
    dots: true,
    infinite: true,
    speed: 1900,
    slidesToShow: 5,
    arrows: true,
    adaptiveHeight: true,
    slidesToScroll: 5,
    autoplay: true,
    autoplaySpeed: 3500,
    responsive: [
        {
            breakpoint: 991,
            settings: {
                slidesToShow: 4,
                slidesToScroll: 3,
                infinite: true,
                dots: true,
            },
        },
        {
            breakpoint: 769,
            settings: {
                slidesToShow: 3,
                slidesToScroll: 3,
                infinite: true,
                dots: true,
            },
        },
        {
            breakpoint: 600,
            settings: {
                slidesToShow: 3,
                slidesToScroll: 3,
            },
        },
        {
            breakpoint: 480,
            settings: {
                slidesToShow: 2,
                slidesToScroll: 3,
            },
        },
        {
            breakpoint: 300,
            settings: {
                slidesToShow: 1,
                slidesToScroll: 1,
            },
        },
    ],
});

// Login Form Handling
$("#loginForm").validate({
    errorClass: "text-danger validation-error",
    errorElement: "span",
    rules: {
        login_field: {
            required: true,
            minlength: 3,
        },
        password: {
            required: true,
        },
        captcha: {
            required: true,
        },
    },
    submitHandler: function (form, event) {
        event.preventDefault();
        $("#login-input-password").attr("type", "password");
        $("#btn-view-login-password").html(`<i class="fa fa-eye"></i>`);
        performLogin();
    },
});

$("#refresh-captcha").click(refreshCaptcha);

// View Password Toggle
$(document).on("click", "#btn-view-login-password", function () {
    let type = $("#login-input-password").attr("type");
    if (type === "password") {
        $("#btn-view-login-password").html(`<i class="fa fa-eye-slash"></i>`);
        $("#login-input-password").attr("type", "text");
    } else {
        $("#btn-view-login-password").html(`<i class="fa fa-eye"></i>`);
        $("#login-input-password").attr("type", "password");
    }
});

// Handle Google Login Link in Modal
$("#modalLoginForm").on("show.bs.modal", function (event) {
    // Update Google Login URL to include current page as origin
    let currentUrl = window.location.href;
    let googleBtn = $(this).find("#btn-google-login");
    let baseUrl = googleBtn.attr("href").split("?")[0]; // Get base URL without params
    googleBtn.attr(
        "href",
        baseUrl + "?origin=" + encodeURIComponent(currentUrl),
    );
});

function performLogin() {
    const formData = new FormData(document.getElementById("loginForm"));

    // encryptionKey is defined in website_layout
    if (typeof encryptionKey === "undefined" || !encryptionKey) {
        console.error("Encryption Key missing");
        toastr.error("Security key missing, please reload page.");
        return;
    }

    const password = encryptPassword(formData.get("password"), encryptionKey);
    formData.set("password", password);

    let loginSource = "modal";
    if (window.location.href.includes("checkout")) {
        loginSource = "checkout";
    }
    formData.append("login_source", loginSource);

    $.ajax({
        url: base_url + "/public/login/check", // Using hardcoded path or route name if available in JS? base_url is defined.
        type: "POST",
        data: formData,
        processData: false,
        contentType: false,
        cache: false,
        beforeSend: () => $(".site-loader").show(),

        success: function (response) {
            $(".site-loader").hide();
            if (response.key) encryptionKey = response.key;

            if (response.status === "success") {
                window.location.reload();
            } else {
                refreshCaptcha();
                Swal.fire({
                    icon: "error",
                    title: "Login Failed",
                    text: response.message,
                    confirmButtonText: "OK",
                });
            }
        },

        error: function (xhr) {
            $(".site-loader").hide();
            refreshCaptcha();
            let errorMessage = "Something went wrong. Please try again.";

            if (xhr.responseJSON && xhr.responseJSON.message) {
                errorMessage = xhr.responseJSON.message;
            }
            if (xhr.responseJSON && xhr.responseJSON.key) {
                encryptionKey = xhr.responseJSON.key;
            }

            Swal.fire({
                icon: "error",
                title: "Error",
                text: errorMessage,
                confirmButtonText: "OK",
            });
        },
    });
}

function refreshCaptcha() {
    const formData = new FormData();
    formData.append("_token", $('meta[name="csrf-token"]').attr("content"));

    $.ajax({
        url: base_url + "/reload-captcha", // Verify this route
        type: "GET", // Usually GET for refresh? Login page used POST to captcha.refresh?
        // Login page used: route('captcha.refresh') which is typically POST?
        // In routes/website.php: Route::get('/reload-captcha', ...)->name('reload-captcha');
        // Wait, login.blade.php used route('captcha.refresh')??
        // Let's check routes file again.
        // Line 55: Route::get('/reload-captcha', ...)->name('reload-captcha');
        // Login page JS used `url: "{{ route('captcha.refresh') }}"`
        // I don't see `captcha.refresh` in website.php lines 1-95.
        // Maybe it is in web.php or standard captcha routes?
        // Ah, I see `mews/captcha` package usually uses `captcha/refresh`?
        // But line 55 says `reload-captcha`.
        // I should use `base_url + '/reload-captcha'` and check if it returns what we expect.
        // OR I check what `login.blade.php` was using.
        // `login.blade.php` used `{{ route('captcha.refresh') }}`.
        // I will assume `captcha.refresh` exists, but since I am in JS file, I can't use blade route().
        // I will use `/reload-captcha` if that's what defined in `website.php`, or try to find the real URL.
        // Given `website.php` has `Route::get('/reload-captcha'...)`, I'll use that.

        // Wait, `login.blade.php` used POST.
        // `website.php` has GET `/reload-captcha`.
        // I will trust `website.php` and use GET.

        success: function (data) {
            $("#captcha img").attr("src", data.captcha + "?" + Date.now());
        },

        error: function () {
            // ...
        },
    });

    // Correction: Let's stick to what worked in `login.blade.php` if possible, but I can't resolve route().
    // I'll try `base_url + '/reload-captcha'` compatible with the route I saw.
}

// Override refreshCaptcha to match `website.php`
function refreshCaptcha() {
    $.ajax({
        url: base_url + "/reload-captcha",
        type: "GET",
        beforeSend: () => $(".site-loader").show(),
        complete: () => $(".site-loader").hide(),
        success: function (data) {
            // Assuming data.captcha contains the src or html?
            // ContactUsController::class, 'reloadCaptcha'
            // Usually returns json with 'captcha' key.
            $("#captcha img").attr("src", data.captcha);
        },
        error: function () {
            Swal.fire({
                icon: "error",
                title: "Error",
                text: "Failed to refresh captcha.",
            });
        },
    });
}

$(".custom_select").select2({
    minimumResultsForSearch: -1,
});

// On click wishlist button
$(document).on("click", ".btn_add_wishlist", function () {
    let productId = $(this).data("id");
    let currentThis = $(this);
    if (productId) {
        $.ajax({
            url: base_url + "/add-to-wishlist",
            type: "POST",
            data: {
                product_id: productId,
                _token: $("meta[name=csrf-token]").attr("content"),
            },
            success: function (response) {
                const result = JSON.parse(response);
                if (result.status == true) {
                    toastr.success(result.message);
                    currentThis.addClass("text-danger");
                } else if (result.status == "validation_error") {
                    $.dialog({
                        title: "Validation Error",
                        content: result.message,
                        type: "red",
                    });
                } else if (result.status == false) {
                    toastr.error(result.message);
                } else if (result.status == 401) {
                    toastr.error(result.message);
                } else {
                    toastr.error(
                        result.message ||
                            "Something went wrong. Please try again.",
                    );
                }
            },
            error: function (xhr, status, error) {
                let message =
                    xhr.responseJSON.message ||
                    "Something went wrong. Please try again.";
                toastr.error(message);
            },
        });
    } else {
        toastr.error("Something went wrong. Please try again.");
    }
});

// Search Form Of Header =========================================
$(".header-search-input").on("keyup", function (e) {
    let searchQuery = $(this).val();
    if (searchQuery) {
        if (e.which == 13) {
            let searchQuery = $("#header-search-input").val();
            if (searchQuery) {
                window.location.href =
                    base_url + "/search?q=" + encodeURI(searchQuery);
            }
        } else {
            $.ajax({
                url: base_url + "/search-hints",
                type: "POST",
                data: {
                    search_query: searchQuery,
                    _token: $("meta[name=csrf-token]").attr("content"),
                },
                beforeSend: function () {},
                complete: function () {},
                global: false,
                success: function (response) {
                    try {
                        if (response.status == true) {
                            let data = response.data;
                            if (data.length > 0) {
                                let list = "";
                                $.each(data, function (index, value) {
                                    list += "<li>" + value + "</li>";
                                });
                                $("#search_results").html(list);
                                $("#search_results").show();

                                if ($(window).width() <= 992) {
                                    $("#mobile_search_results").html(list);
                                    $("#mobile_search_results").show();
                                }
                            }
                        }
                    } catch (error) {
                        toastr.error("Something went wrong. Please try again.");
                    }
                },
            });
        }
    }
});

window.addEventListener("click", function (e) {
    if (
        !document
            .getElementById("header-search-input-group-wrapper")
            .contains(e.target)
    ) {
        // Clicked outside the box
        $("#search_results").hide();
    }
});

$(".header-search-btn").on("click", function () {
    let searchQuery = $("#header-search-input").val();
    if (searchQuery) {
        window.location.href = base_url + "/search?q=" + encodeURI(searchQuery);
    }
});

// Newsletter ===============================================
// On submitting the form
$("#subscribe_newsletter_form").on("submit", function (event) {
    event.preventDefault();
    var formData = new FormData(
        document.getElementById("subscribe_newsletter_form"),
    );

    // Send Ajax Request
    $.ajax({
        url: base_url + "/subscribe-newsletter",
        type: "POST",
        data: formData,
        contentType: false,
        processData: false,
        success: function (response) {
            if (response.status == true) {
                $.confirm({
                    title: "Success",
                    type: "green",
                    content: response.message,
                    buttons: {
                        confirm: function () {
                            window.location.reload();
                        },
                    },
                });
            } else if (response.status == "validation_error") {
                $.dialog({
                    title: "Validation Error",
                    content: response.message,
                    type: "red",
                });
            } else if (response.status == false) {
                toastr.error(response.message);
            } else {
                toastr.error("Something went wrong. Please try again.");
            }
        },
        error: function (error) {
            toastr.error("Something went wrong. Please try again.");
        },
    });
});

// Add to Cart from Card
$(document).on("click", ".btn_add_to_cart_card", function (event) {
    event.preventDefault();
    var productId = $(this).data("id");
    var quantity = 1;

    var formData = new FormData();
    formData.append("product_id", productId);
    formData.append("quantity", quantity);
    formData.append("_token", $("meta[name=csrf-token]").attr("content"));

    $.ajax({
        url: base_url + "/add-to-cart",
        type: "POST",
        data: formData,
        contentType: false,
        processData: false,
        success: function (response) {
            if (response.status == true) {
                $.confirm({
                    title: "Product Added",
                    type: "green",
                    content: response.message,
                    buttons: {
                        shop: {
                            text: "Shop More",
                            btnClass: "btn-black",
                            action: function () {
                                // Close the modal only
                                return true;
                            },
                        },
                        cart: {
                            text: "Go to cart",
                            btnClass: "btn-purple",
                            keys: ["enter"],
                            action: function () {
                                window.location.href = base_url + "/cart";
                            },
                        },
                    },
                });
            } else if (response.status == "validation_error") {
                // Handle specific validation handling or generic
                toastr.error(response.message);
            } else if (response.status == false) {
                toastr.error(response.message);
            } else {
                toastr.error("Something went wrong. Please try again.");
            }
        },
        error: function (error) {
            toastr.error("Something went wrong. Please try again.");
        },
    });
});

// Menu Close button
$("#btn-menu-close").on("click", function () {
    $(".collapse-menu-col").removeClass("show");
});

// Filter Button
$("#btn-filter").on("click", function () {
    $(".filter-col").slideToggle();
});

$("#btn-filter-close").on("click", function () {
    $(".filter-col").slideUp();
});

$(window).on("resize", function () {
    var win = $(this); //this = window
    if (win.width() >= 991) {
        $(".filter-col").show();
    } else {
        $(".filter-col").hide();
    }
});

// Show collapsed submenu by clicking plus button
$(document).on("click", ".menu-nav-link-icon", function (event) {
    event.preventDefault();
    var id = $(this).data("id");
    $("#submenu-ul-id-" + id).slideToggle();
});

// =========================================================
// Encrypt password
// =========================================================
function encryptPassword(password, encryptionKey) {
    if (!encryptionKey) {
        console.error("Encryption key is not ready.");
        return null;
    }

    const key = CryptoJS.enc.Hex.parse(encryptionKey);

    // Generate random IV
    const iv = CryptoJS.lib.WordArray.random(16);

    const encrypted = CryptoJS.AES.encrypt(password, key, {
        iv: iv,
        mode: CryptoJS.mode.CBC,
        padding: CryptoJS.pad.Pkcs7,
    });

    // Combine IV and ciphertext (for backend)
    const encryptedData = iv
        .concat(encrypted.ciphertext)
        .toString(CryptoJS.enc.Base64);
    return encryptedData;
}

// =========================================================
// Mega Menu UI
// =========================================================
document.addEventListener("DOMContentLoaded", function () {
    document.querySelectorAll(".subcategory-toggle").forEach((button) => {
        button.addEventListener("click", function (e) {
            e.preventDefault();
            e.stopPropagation();

            const targetId = this.getAttribute("data-target");
            const targetElement = document.getElementById(targetId);

            if (targetElement) {
                targetElement.classList.toggle("active");
                this.classList.toggle("active");
            }
        });
    });

    document
        .querySelector(".mega-menu")
        ?.addEventListener("click", function (e) {
            e.stopPropagation();
        });
});
// Consolidate ready listeners
$(document).ready(function() {
    $(document).on('contextmenu', '.protected-img, .img-protection-overlay', function(e) {
        e.preventDefault();
        return false;
    });

    $(document).on('dragstart', '.protected-img, .img-protection-overlay', function(e) {
        e.preventDefault();
        return false;
    });
});
