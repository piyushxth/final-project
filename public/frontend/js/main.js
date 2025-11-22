var tablesInDescription = document.querySelectorAll(".blog-content-page table");

tablesInDescription.forEach(function (table) {
    var parentDiv = document.createElement("div");
    parentDiv.classList.add("table-wrapper");

    table.parentNode.insertBefore(parentDiv, table);
    parentDiv.appendChild(table);
});
$(document).ready(function () {
    var baseUrl = $("body").attr("data-siteurl");
    $(".far.fa-heart").on("click", function () {
        $(this).removeClass("far").addClass("fas");
    });
    $(".send_message_btn").on("click", function (e) {
        e.preventDefault();
        var formaction = $("#homecontact_form").attr("action");
        var formdata = $("#homecontact_form").serialize();
        $.ajax({
            url: formaction,
            method: "POST",
            data: formdata,
            beforeSend: function () {
                $(".send_message_btn")
                    .html(
                        "<i class='fas fa-spinner fa-pulse fa-1x'></i> Send Message"
                    )
                    .prop("disabled", false);
            },
            success: function (response) {
                if (response) {
                    if (response.success) {
                        showMessage(response.success, "success");
                    } else {
                        showMessage(response.error, "error");
                    }
                }
                $(".send_message_btn")
                    .html("<i class='fas pe-3 fa-paper-plane'></i>Send Message")
                    .prop("disabled", false);
            },
            error: function (response) {
                if (
                    typeof response.responseJSON.errors !== "" &&
                    response.responseJSON.errors
                ) {
                    $(".error").remove();
                    $.each(response.responseJSON.errors, function (key, value) {
                        showMessage(value, "error");
                    });
                    $(".send_message_btn")
                        .html(
                            "<i class='fas pe-3 fa-paper-plane'></i>Send Message"
                        )
                        .prop("disabled", false);
                }
            },
        });
    });

    $(".ratings").attr("data-rating", function () {
        var id = $(this).attr("id");
        var data_rating = $(this).attr("data-rating");
        for (let i = 0; i < data_rating; i++) {
            $(".ratings#" + id + "").append("<i class='fas fa-star'></i>");
        }
    });
    $(".orderItemsShow").on("click", function () {
        var id = $(this).attr("slug-data");
        $.ajax({
            headers: {
                "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
            },
            url: baseUrl + "/customer/order_items/" + id,
            method: "get",
            success: function (response) {
                $(".order-table-showModel").html(response.viewOrderDetails);
                $("#orderItemsModel").modal("show");
            },
            error: function (result) {
                console.log(result);
                showMessage("Server error occured", "error");
            },
        });
    });

    if ($(".provienceShippingAddress").length > 0) {
        getDistrictsShippingAddress();
        $(".provienceShippingAddress").on("change", function () {
            getDistrictsShippingAddress();
            $("input.street").val("");
        });
    }

    function getDistrictsShippingAddress() {
        var baseUrl = $("body").attr("data-siteurl");
        var provience = $(".provienceShippingAddress").find(":selected").val();
        var getDistrictsUrl = baseUrl + "/getDistricts?state_id=" + provience;
        $.get(
            getDistrictsUrl,
            function (data) {
                $(".districtShippingAddress").html(data.district_options);
            },
            "json"
        );
    }

    $("#same-as-billing-address").on("click", function () {
        if ($(this).is(":checked")) {
            if (
                $("input#number").attr("value") == "" ||
                $("#district").val() == "" ||
                $("#provience").val() == "" ||
                $("input#street").attr("value") == ""
            ) {
                showMessage("Sorry, Add Account Information First", "error");
                window.location.href = "/customer/account-detail";
            } else {
                var name = $("input#name").attr("value");
                var number = $("input#number").attr("value");
                var provience = $("#provience").val();
                var district = $("#district").val();
                var email = $("input#email").attr("value");
                var street = $("input#street").attr("value");
                $("input.name").val(name);
                $("input.number").val(number);
                $("input.email").val(email);
                $("input.street").val(street);
                $(
                    ".provienceShippingAddress option[value='" +
                        provience +
                        "']"
                ).attr("selected", "selected");
                getDistrictsShippingAddress();
            }
        } else {
            $("input.name").val("");
            $("input.number").val("");
            $("input.email").val("");
            $("input.street").val("");
            $(".provienceShippingAddress option:selected").removeAttr(
                "selected"
            );
            $(".districtShippingAddress option:selected").removeAttr(
                "selected"
            );
        }
    });

    $(".buy-now").on("click", function (e) {
        e.preventDefault();
        var baseUrl = $("body").attr("data-siteurl");
        var size = "";
        var item_stock = "";
        var id = $("input.product_qty.qty_active").attr("data-id");
        var sp = $("input.product_qty.qty_active").attr("data-sp");
        var title = $("input.product_qty.qty_active").attr("data-title");
        var qty = $("input.product_qty.qty_active").val();
        size = $(".product-sizes-listing.active").attr("data-size");
        item_stock = $(".product-sizes-listing.active").attr("data-stock");

        $.ajax({
            headers: {
                "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
            },
            url: baseUrl + "/customer/cart",
            method: "POST",
            data: {
                id: id,
                title: title,
                sale_price: sp,
                quantity: qty,
                size: size,
            },
            beforeSend: function () {
                if (isCustomerLoggedIn() == false) {
                    showMessage(
                        "Sorry, Item cannot be added to cart. Please login first",
                        "error"
                    );
                    return false;
                } else {
                    if (qty > item_stock) {
                        showMessage("Item qty is out of stock", "error");
                        return false;
                    }
                    if (
                        item_stock == "" ||
                        item_stock == null ||
                        item_stock == "0"
                    ) {
                        showMessage(
                            "Item is out of stock. Please try with another size",
                            "error"
                        );
                        return false;
                    }
                }
            },
            success: function (response) {
                if (response.result == "added") {
                    $("#cart-count").text(response.cartItemCount);
                    window.location.href = "/customer/checkout";
                    showMessage("Item added to cart", "success");
                } else if (response.result == "exist") {
                    showMessage("Item exists in cart", "error");
                } else if (response.result == "outOfStock") {
                    showMessage("Item out of stock", "error");
                } else {
                    showMessage("Server error occured", "error");
                }
            },
            error: function (result) {
                console.log(result);
                showMessage("Server error occured", "error");
            },
        });
    });

    //add to wishlist
    $(document).on("click", ".add-to-wishlist", function (e) {
        e.preventDefault();
        var size = "";
        var id = $("input.product_qty.qty_active").attr("data-id");
        var sp = $("input.product_qty.qty_active").attr("data-sp");
        var title = $("input.product_qty.qty_active").attr("data-title");
        var qty = $("input.product_qty.qty_active").val();
        size = $("input.product_qty.qty_active").attr("data-size");

        $.ajax({
            headers: {
                "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
            },
            url: baseUrl + "/customer/wishlist",
            method: "POST",
            data: {
                id: id,
                title: title,
                sale_price: sp,
                quantity: qty,
                size: size,
            },
            beforeSend: function () {
                if (isCustomerLoggedIn() == false) {
                    showMessage(
                        "Sorry, Item cannot be added to wishlist. Please login first",
                        "error"
                    );
                    return false;
                }
            },
            success: function (response) {
                if (response.result == "added") {
                    $("#wishlist-count").text(response.wishlistItemCount);
                    showMessage("Item added to wishlist", "success");
                } else if (response.result == "exist") {
                    showMessage("Item exists in wishlist", "error");
                } else {
                    showMessage("Server error occured", "error");
                }
            },
            error: function (result) {
                showMessage("Server error occured", "error");
            },
        });
    });


    $(document).on("mouseover", ".product-card", function (e) {
        if ($("body").hasClass("main_product")) {
            $("button.product-sizes-listing").removeClass("active");
        }
        $("div.product-sizes-listing").removeClass("active");
        $("input.product_qty").removeClass("qty_active");
        var pcount = $(this).attr("data-pcount");
        var sectionName = $(this).attr("data-section");
        $("#" + sectionName + "Div" + pcount).addClass("active");
        $("#" + sectionName + pcount).addClass("qty_active");
    });


    $(document).on("mouseout", ".product-card", function (e) {
        if ($("body").hasClass("main_product")) {
            $("button.product-sizes-listing:first").addClass("active");
        }
    });


    $(document).on("mouseover", ".add-to-cart", function (e) {
        $("div.product-sizes-listing").removeClass("active");
        $("input.product_qty").removeClass("qty_active");
        $(this).prev("input.product_qty").addClass("qty_active");
    });

    /* size selections starts */
    $("button.product-sizes-listing").on("click", function (e) {
        e.preventDefault();
        var selectedsizeid = $(this).attr("id");
        $("button.product-sizes-listing").removeClass("active");
        $("#" + selectedsizeid).addClass("active");
    });
    /*size selection ends*/

    /* product search starts */
    $(".filter_suitable_for").on("click", function () {
        productSearch("");
    });

    $(".filter_categories").on("click", function () {
        productSearch("");
    });

    $(".filter_sizes").on("click", function () {
        productSearch("");
    });

    if ($("#ajaxLoadedProducts").length > 0) {
        $("body").on("click", ".pagination a", function (e) {
            e.preventDefault();
            var pagination_url = $(this).attr("href");
            if (pagination_url.includes("filter=yes")) {
                productSearch(pagination_url);
            } else {
                window.location.href = pagination_url;
            }
        });
    }
    /* product search ends */

    //Add to Cart
    $(document).on("click", ".addToCartAjax", function (e) {
        e.preventDefault();
        var baseUrl = $("body").attr("data-siteurl");
        var size = "";
        var item_stock = "";
        var wishlistitem = "";
        var id = $("input.product_qty.qty_active").attr("data-id");
        var sp = $("input.product_qty.qty_active").attr("data-sp");
        var title = $("input.product_qty.qty_active").attr("data-title");
        var qty = $("input.product_qty.qty_active").val();
        size = $("input.product_qty.qty_active").attr("data-size");
        item_stock = $("input.product_qty.qty_active").attr("data-stock");
        wishlistitem = $("input.product_qty.qty_active").attr(
            "data-wishlistitem"
        );
        if (size == "" && item_stock == "") {
            showMessage(
                "Sorry, Item cannot be added to cart. Product with selected size is out of stock",
                "error"
            );
            return false;
        } else if (size == "") {
            showMessage(
                "Sorry, Item cannot be added to cart. Product size do not exists",
                "error"
            );
            return false;
        } else if (item_stock == "") {
            showMessage(
                "Sorry, Item cannot be added to cart. Product is out of stock",
                "error"
            );
            return false;
        }

        $.ajax({
            headers: {
                "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
            },
            url: baseUrl + "/customer/cart",
            method: "POST",
            data: {
                id: id,
                title: title,
                sale_price: sp,
                quantity: qty,
                size: size,
                wishlistitem: wishlistitem,
            },
            beforeSend: function () {
                if (isCustomerLoggedIn() == false) {
                    showMessage(
                        "Sorry, Item cannot be added to cart. Please login first",
                        "error"
                    );
                    return false;
                } else {
                    if (
                        item_stock == "" ||
                        item_stock == null ||
                        item_stock == "0"
                    ) {
                        showMessage(
                            "Item is out of stock. Please try with another size",
                            "error"
                        );
                        return false;
                    }
                }
            },
            success: function (response) {
                if (response.result == "added") {
                    $("#cart-count").text(response.cartItemCount);
                    $("#wishlist-count").text(response.wishListItemCount);
                    showMessage("Item added to cart", "success");
                } else if (response.result == "exist") {
                    showMessage("Item exists in cart", "error");
                } else if (response.result == "outOfStock") {
                    showMessage("Item out of stock", "error");
                } else {
                    showMessage("Server error occured", "error");
                }
                if (wishlistitem != "") {
                    setTimeout(function () {
                        location.reload(true);
                    }, 4000);
                }
            },
            error: function (result) {
                showMessage("Server error occured", "error");
            },
        });
    });

    if ($("#priceRange").length > 0) {
        var slider = document.getElementById("priceRange");
        slider.step = "100";
        var output = document.getElementById("selectedprice");
        output.innerHTML = inrNumberFormat(slider.value);
        slider.oninput = function () {
            output.innerHTML = inrNumberFormat(this.value);
            productSearch("");
        };
    }
    if ($("#provience").length > 0) {
        getDistricts();
        $("#provience").on("change", function () {
            getDistricts();
        });
    }

    $(".update-parent-form").on("change", function (e) {
        $(this).closest("form").submit();
    });

    var detectMobCheck = detectMob();
    if (detectMobCheck == false) {
        $(".nav-Search-bar").hide();
    }

    // Search functionality
    $('.search-icon').on('click', function(e) {
        e.stopPropagation();
        $('.search-box').toggle();
        $('.search-box input[name="keywords"]').focus();
    });

    // Close search when clicking outside
    $(document).on('click', function(e) {
        if (!$(e.target).closest('.nav-Search-bar').length) {
            $('.search-box').hide();
        }
    });

    // Live search functionality
    $('.search-box input[name="keywords"]').on('keyup', function() {
        var keywords = $(this).val();
        var baseUrl = $("body").attr("data-siteurl");
        
        if (keywords.length >= 2) {
            $.ajax({
                url: baseUrl + '/search',
                method: 'GET',
                data: { keywords: keywords },
                success: function(response) {
                    // Display search results
                    $('.search-results').html(response).show();
                },
                error: function() {
                    $('.search-results').hide();
                }
            });
        } else {
            $('.search-results').hide();
        }
    });

    showLoggedInMenu(false);
    $(document).on("keydown", function (event) {
        if (event.key == "Escape") {
            closeAuthModal();
        }
    });

    $(".popupbtn").click(function () {
        var baseUrl = $("body").attr("data-siteurl");
        var formToOpen = $(this).attr("data-openmodal");
        $("#auth-modal").modal("show");
        activateForm(formToOpen);
    });

    $(document).on("click", "#customer_signin", function (e) {
        e.preventDefault();
        var formaction = $("#loginform").attr("action");
        var formdata = $("#loginform").serialize();
        $("#customer_signin")
            .html("<i class='fas fa-spinner fa-pulse fa-2x'></i>")
            .prop("disabled", true);
        $.ajax({
            url: formaction,
            method: "post",
            data: formdata,
            success: function (response) {
                if (response) {
                    $("#customer_signin")
                        .html("SIGN IN")
                        .prop("disabled", false);
                    if (response.success) {
                        if (response.redirect_url != "") {
                            window.location.href = response.redirect_url;
                            showLoggedInMenu(true);
                        } else {
                            closeAuthModal();
                            $("#cart-count").text(response.cartItemCount);
                            $("#wishlist-count").text(
                                response.wishlistItemCount
                            );
                            showLoggedInMenu(true);
                        }
                        showMessage(response.success, "success");
                    } else {
                        $("#customer_signin")
                            .html("SIGN IN")
                            .prop("disabled", false);
                        showMessage(response.error, "error");
                    }
                }
            },
            error: function (response) {
                if (
                    typeof response.responseJSON.errors !== "" &&
                    response.responseJSON.errors
                ) {
                    if (response.responseJSON.errors.password) {
                        showMessage(
                            response.responseJSON.errors.password,
                            "error"
                        );
                    }
                    if (response.responseJSON.errors.email) {
                        showMessage(
                            response.responseJSON.errors.email,
                            "error"
                        );
                    }
                }
                $("#customer_signin").html("SIGN IN").prop("disabled", false);
            },
        });
    });

    $(document).on("click", "#customer_register", function (e) {
        e.preventDefault();
        var formaction = $("#registerform").attr("action");
        var formdata = $("#registerform").serialize();
        $("#customer_register")
            .html("<i class='fas fa-spinner fa-pulse fa-2x'></i>")
            .prop("disabled", true);
        $.ajax({
            url: formaction,
            method: "post",
            data: formdata,
            success: function (response) {
                if (response) {
                    $("#customer_register")
                        .html("REGISTER")
                        .prop("disabled", false);
                    if (response.success) {
                        if (response.redirect_url != "") {
                            window.location.href = response.redirect_url;
                            showLoggedInMenu(true);
                        } else {
                            closeAuthModal();
                            showLoggedInMenu(true);
                        }
                        showMessage(response.success, "success");
                    } else {
                        $("#customer_register")
                            .html("REGISTER")
                            .prop("disabled", false);
                        showMessage(response.error, "error");
                    }
                }
            },
            error: function (response) {
                if (
                    typeof response.responseJSON.errors !== "" &&
                    response.responseJSON.errors
                ) {
                    if (response.responseJSON.errors.terms_condition) {
                        showMessage(
                            response.responseJSON.errors.terms_condition,
                            "error"
                        );
                    }
                    if (response.responseJSON.errors.confirmpassword) {
                        showMessage(
                            response.responseJSON.errors.confirmpassword.join(
                                "<br/>"
                            ),
                            "error"
                        );
                    }
                    if (response.responseJSON.errors.password) {
                        showMessage(
                            response.responseJSON.errors.password.join("<br/>"),
                            "error"
                        );
                    }
                    if (response.responseJSON.errors.email) {
                        showMessage(
                            response.responseJSON.errors.email.join("<br/>"),
                            "error"
                        );
                    }
                    if (response.responseJSON.errors.name) {
                        showMessage(response.responseJSON.errors.name, "error");
                    }
                }
                $("#customer_register")
                    .html("REGISTER")
                    .prop("disabled", false);
            },
        });
    });

    if ($("body").hasClass("main_product")) {
        var detectMobCheck = detectMob();
        if (detectMobCheck == false) {
            $(".picZoomer").picZoomer();
        }
        processRatingReviews();
        $(".submit_review").click(function (e) {
            e.preventDefault();
            var ratingsOk = false;
            var reviewOk = false;
            if (isCustomerLoggedIn() == false) {
                showMessage(
                    "Sorry, review cannot be submitted. Please login first",
                    "error"
                );
                return false;
            } else {
                var dataArray = $("form.submit_review_form").serializeArray();
                var ratingValue = dataArray[2].value;
                var reviewValue = dataArray[3].value;
                var actionUrl = $("form.submit_review_form").attr("action");

                if (reviewValue == "") {
                    showMessage("Review is required", "error");
                    reviewOk = false;
                } else {
                    reviewOk = true;
                }

                if (ratingValue == "" || ratingValue == 0) {
                    showMessage("Rating is required", "error");
                    $("#rating-value").val(0);
                    ratingsOk = false;
                } else {
                    ratingsOk = true;
                }

                if (ratingsOk == true || reviewOk == true) {
                    $.ajax({
                        type: "POST",
                        url: actionUrl,
                        data: dataArray,
                        dataType: "json",
                        encode: true,
                        success: function (response) {
                            if (response.error) {
                                showMessage(response.error, "error");
                            } else {
                                showMessage(response.success, "success");
                            }
                        },
                        error: function (response) {
                            showMessage("Server error occured", "error");
                        },
                    });
                }
            }
        });

        $("#review").keyup(function () {
            var reviewValue = $(this).val();
            if (reviewValue != "" || typeof reviewValue !== "undefined") {
            } else {
                showMessage("Review is required", "error");
            }
        });
    }

    //  banner-slider
    $(".banner-slider").slick({
        slidesToShow: 1,
        slidesToScroll: 1,
        fade: true,
        prevArrow:
            " <button type='button' class='slick-prev'><i class='fas fa-angle-left'></i></button>",
        nextArrow:
            " <button type='button' class='slick-next'><i class='fas fa-angle-right'></i></button>",
    });
    // product-slider
    $(".product-slider").slick({
        slidesToShow: 4,
        slidesToScroll: 1,
        arrows: true,
        prevArrow:
            " <button type='button' class='slick-prev'><i class='fas fa-angle-left'></i></button>",
        nextArrow:
            " <button type='button' class='slick-next'><i class='fas fa-angle-right'></i></button>",
        responsive: [
            {
                breakpoint: 1170,
                settings: {
                    slidesToShow: 3,
                },
            },
            {
                breakpoint: 992,
                settings: {
                    slidesToShow: 2,
                },
            },
            {
                breakpoint: 768,
                settings: {
                    slidesToShow: 1,
                },
            },
        ],
    });
    // testimonials slider
    $(".testimonials-slider").slick({
        slidesToShow: 1,
        slidesToScroll: 1,
        prevArrow:
            " <button type='button' class='slick-prev'><i class='fas fa-angle-left'></i></button>",
        nextArrow:
            " <button type='button' class='slick-next'><i class='fas fa-angle-right'></i></button>",
    });
    // product details preview slider
    $(".products-images-nav").slick({
        slidesToShow: 4,
        arrows: true,
        slidesToScroll: 3,
        prevArrow:
            " <button type='button' class='slick-prev'><i class='fas fa-angle-left'></i></button>",
        nextArrow:
            " <button type='button' class='slick-next'><i class='fas fa-angle-right'></i></button>",
    });

    // Popup Video
    $(".vpop").on("click", function (e) {
        e.preventDefault();
        $(
            "#video-popup-overlay,#video-popup-iframe-container,#video-popup-container,#video-popup-close"
        ).show();
        var srchref = "",
            autoplay = "",
            id = $(this).data("id");
        if ($(this).data("type") == "vimeo")
            var srchref = "//player.vimeo.com/video/";
        else if ($(this).data("type") == "youtube")
            var srchref = "https://www.youtube.com/embed/";
        if ($(this).data("autoplay") == true) autoplay = "?autoplay=1";
        $("#video-popup-iframe").attr("src", srchref + id + autoplay);
        $("#video-popup-iframe").on("load", function () {
            $("#video-popup-container").show();
        });
    });
    $("#video-popup-close, #video-popup-overlay").on("click", function (e) {
        $(
            "#video-popup-iframe-container, #video-popup-container,#video-popup-close,#video-popup-overlay"
        ).hide();
        $("#video-popup-iframe").attr("src", "");
    });
    // tooltip
    const tooltipTriggerList = document.querySelectorAll(
        '[data-bs-toggle="tooltip"]'
    );
    const tooltipList = [...tooltipTriggerList].map(
        (tooltipTriggerEl) => new bootstrap.Tooltip(tooltipTriggerEl)
    );
    // toggle search bar
    $(".sticky-search-icon").click(function () {
        $(".search-box").toggle();
    });

    //adding active class on product preview images list
    $(document).on("click", ".product-preview-list", function () {
        $(".product-preview-list").removeClass("active");
        $(this).addClass("active");
        var $pic = $(this).find("img");
        $(".picZoomer-pic").attr("src", $pic.attr("src"));
    });
    //  adding active class on navigation bar
    $(document).on("click", "#navbarSupportedContent ul li a ", function () {
        $("#navbarSupportedContent ul li a").removeClass("active");
        $(this).addClass("active");
    });
    //  Adding active class on sizes of product categories page
    $(document).on("click", ".sizes button", function () {
        $(".sizes button").removeClass("active");
        $(this).addClass("active");
    });
    // animation while scrolling
    $(".about-right").waypoint(
        function (direction) {
            $(".about-right").addClass(
                "animate__animated animate__fadeInLeft "
            );
        },
        {
            offset: "70%",
        }
    );
    $(".about-left").waypoint(
        function (direction) {
            $(".about-left").addClass(
                "animate__animated animate__fadeInRight "
            );
        },
        {
            offset: "70%",
        }
    );

    $(".trending-section-animated").waypoint(
        function (direction) {
            $(".trending-section-animated").addClass(
                "animate__animated animate__zoomIn"
            );
        },
        {
            offset: "70%",
        }
    );
    $(".bag-animated").waypoint(
        function (direction) {
            $(".bag-animated").addClass(
                "animate__animated animate__fadeInLeft "
            );
        },
        {
            offset: "70%",
        }
    );
    $(".shoes-animated").waypoint(
        function (direction) {
            $(".shoes-animated").addClass(
                "animate__animated animate__fadeInRight "
            );
        },
        {
            offset: "70%",
        }
    );
    $(".highlight").waypoint(
        function (direction) {
            $(".highlight").addClass("animate__animated animate__fadeInUp ");
        },
        {
            offset: "70%",
        }
    );
    $(".new-arrivals-animated").waypoint(
        function (direction) {
            $(".new-arrivals-animated").addClass(
                "animate__animated animate__zoomIn "
            );
        },
        {
            offset: "70%",
        }
    );
    $(".new-feature").waypoint(
        function (direction) {
            $(".new-feature").addClass("animate__animated animate__fadeInUp ");
        },
        {
            offset: "70%",
        }
    );
    $(".men-section").waypoint(
        function (direction) {
            $(".men-section").addClass(
                "animate__animated animate__fadeInTopLeft "
            );
        },
        {
            offset: "70%",
        }
    );
    $(".kid-section").waypoint(
        function (direction) {
            $(".kid-section").addClass(
                "animate__animated animate__fadeInTopRight"
            );
        },
        {
            offset: "70%",
        }
    );
    $(".women-section").waypoint(
        function (direction) {
            $(".women-section").addClass(
                "animate__animated animate__fadeInBottomRight"
            );
        },
        {
            offset: "70%",
        }
    );
    $(".video-section").waypoint(
        function (direction) {
            $(".video-section").addClass(
                "animate__animated animate__fadeInBottomRight"
            );
        },
        {
            offset: "90%",
        }
    );
    $(".testimonal-animated").waypoint(
        function (direction) {
            $(".testimonal-animated").addClass(
                "animate__animated animate__slideInUp"
            );
        },
        {
            offset: "90%",
        }
    );
    $(".blog-animated").waypoint(
        function (direction) {
            $(".blog-animated").addClass(
                "animate__animated animate__fadeInLeft"
            );
        },
        {
            offset: "70%",
        }
    );
});

// Incrase decrease value button in add to card button
function increaseValue(selector_name, count) {
    var value = parseInt(
        document.getElementById(selector_name + count).value,
        10
    );
    value = isNaN(value) ? 1 : value;
    value++;
    document.getElementById(selector_name + count).value = value;
}

function decreaseValue(selector_name, count) {
    var value = parseInt(
        document.getElementById(selector_name + count).value,
        10
    );
    value = isNaN(value) ? 1 : value;
    value < 1 ? (value = 1) : "";
    value--;
    document.getElementById(selector_name + count).value = value;
}

// sticky-header
$(window).scroll(function () {
    if ($(window).scrollTop() >= 300) {
        $(".main-header").addClass("sticky-header");
        $(".nav-Search-bar").addClass("nav-sticky-bar");
    } else {
        $(".main-header").removeClass("sticky-header");
        $(".nav-Search-bar").removeClass("nav-sticky-bar");
    }
});

window.onload = function () {
    var imgs = document.getElementsByClassName(" product-preview-list");
    for (var i = 0; i < imgs.length; i++) {
        var img = imgs[i];
        img.onclick = function () {
            newSrc = this.src;
            focus = document.getElementById("products-images");
            focus.src = newSrc;
        };
    }
};


function detectMob() {
    var isMobile = /iphone|ipod|android|ie|blackberry|fennec/.test(
        navigator.userAgent.toLowerCase()
    );
    return isMobile;
}

function processRatingReviews() {
    // STAR RATINGS
    const stars = document.querySelector(".ratings").children;
    const ratingValue = document.querySelector("#rating-value");
    let index;
    for (let i = 0; i < stars.length; i++) {
        stars[i].addEventListener("mouseover", function () {
            // console.log(i)
            for (let j = 0; j < stars.length; j++) {
                stars[j].className = "far fa-star";
            }
            for (let j = 0; j <= i; j++) {
                stars[j].className = "fas fa-star";
            }
        });
        stars[i].addEventListener("click", function () {
            ratingValue.value = i + 1;
            index = i;
        });
        stars[i].addEventListener("mouseout", function () {
            for (let j = 0; j < stars.length; j++) {
                stars[j].className = "far fa-star";
            }
            for (let j = 0; j <= index; j++) {
                stars[j].className = "fas fa-star";
            }
        });
    }
}

function activateForm(formToOpen) {
    var baseUrl = $("body").attr("data-siteurl");
    if ($(".form-title").hasClass("active")) {
        $(".form-title").removeClass("active");
    }
    if (formToOpen == "loginform") {
        $(".login-modal").addClass("active");
    } else {
        $(".register-modal").addClass("active");
    }
    $(".user_registration_login_form").html(
        "<div class='text-center'><i class='fas fa-spinner fa-pulse fa-3x'></i></div>"
    );
    var getFormUrl = baseUrl + "/getForm?type=" + formToOpen;
    $.get(
        getFormUrl,
        function (data) {
            $(".user_registration_login_form").html(data.form_html);
            $(".loginform_text").html(data.p_text);
        },
        "json"
    );
}

function closeAuthModal() {
    if ($("#auth-modal").hasClass("show")) {
        $("#auth-modal").modal("hide");

        $("#auth-modal").on("hidden.bs.modal", function () {
            $(".error-message").text("");
        });
    } else {
        console.log("nothing to close");
    }
}

function showMessage(msg_content, msg_type) {
    if (msg_type == "error") {
        toastr.error(msg_content, "Error", {
            closeButton: true,
            positionClass: "toast-top-right",
            progressBar: true,
            showDuration: "3000",
        });
    }
    if (msg_type == "success") {
        toastr.success(msg_content, "Success", {
            closeButton: true,
            positionClass: "toast-top-right",
            progressBar: true,
            preventDuplicates: 1,
            showDuration: "3000",
        });
    }
}

function showLoggedInMenu(loggedin) {
    if (loggedin == true) {
        $("body").addClass("loggedin");
        $(".popupbtn").hide();
        $(".loggedin_menu").show();
    } else {
        if ($("body").hasClass("loggedin")) {
            $(".popupbtn").hide();
            $(".loggedin_menu").show();
        } else {
            $(".loggedin_menu").hide();
            $(".popupbtn").show();
        }
    }
}

function getDistricts() {
    var baseUrl = $("body").attr("data-siteurl");
    var provience = $("#provience").find(":selected").val();
    var getDistrictsUrl = baseUrl + "/getDistricts?state_id=" + provience;
    $.get(
        getDistrictsUrl,
        function (data) {
            $("#district").html(data.district_options);
        },
        "json"
    );
}

function inrNumberFormat(price_string) {
    var x = price_string.toString();
    var lastThree = x.substring(x.length - 3);
    var otherNumbers = x.substring(0, x.length - 3);
    if (otherNumbers != "") lastThree = "," + lastThree;
    var res = otherNumbers.replace(/\B(?=(\d{2})+(?!\d))/g, ",") + lastThree;
    return res;
}

function productSearch(pagination_url) {
    if (pagination_url == "") {
        var baseUrl = $("body").attr("data-siteurl");
        var url_arr = {};

        var suitableFor = $("input.filter_suitable_for:checked").map(
            function () {
                return $(this).val();
            }
        );

        var searchCategories = $("input.filter_categories:checked").map(
            function () {
                return $(this).val();
            }
        );

        var searchSizes = $("input.filter_sizes:checked").map(function () {
            return $(this).val();
        });

        url_arr["from_price"] = $("#priceRange").val();
        url_arr["to_price"] = $("#priceRange").attr("max");
        url_arr["suitableFor"] = suitableFor.get();
        url_arr["categories"] = searchCategories.get();
        url_arr["sizes"] = searchSizes.get();
        url_arr["filter"] = "yes";

        var queryparams = jQuery.param(url_arr);
        var finalUrl = baseUrl + "/products_search?" + queryparams;
    } else {
        var finalUrl = pagination_url;
    }

    $.ajax({
        type: "GET",
        url: finalUrl,
        beforeSend: function () {
            $("#ajaxLoadedProducts").html(
                "<div class='h-50 d-flex align-items-center justify-content-center'><i class='fas fa-spinner fa-pulse fa-4x'></i></div>"
            );
        },
        success: function (data) {
            $("#ajaxLoadedProducts").html(data.list_view);
        },
        error: function (data) {
            console.log("search failed");
        },
    });
}

function isCustomerLoggedIn() {
    if (!$("body").hasClass("loggedin")) {
        return false;
    } else {
        return true;
    }
}

// Live search functionality is now handled in the header file
// This section has been removed to avoid duplication);



