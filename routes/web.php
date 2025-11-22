<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;

/** Frontend Starts **/
Route::get("/", "Frontend\FrontendController@home")->name("home");

Route::get("user/login", "Frontend\FrontendController@login")->name(
    "user.login"
);

Route::get("forgot-password", "Frontend\FrontendController@forgotPasswordIndex")->name(
    "user.forgotPasswordIndex"
);
Route::post("user/emailVerify", "Frontend\FrontendController@emailVerify")->name(
    "user.email.verify"
);

Route::get("reset-password", "Frontend\FrontendController@emailSubmit")->name(
    "user.password.reset"
);

Route::post("user/changePassword/{email}", "Frontend\FrontendController@changePasswordSubmit")->name(
    "user.changePassword.submit"
);
Route::post(
    "user/login/submit",
    "Frontend\FrontendController@loginSubmit"
)->name("user.login.submit");


Route::post(
    "user/register/submit",
    "Frontend\FrontendController@registerSubmit"
)->name("user.register.submit");
Route::get("user/verify", "Frontend\FrontendController@verify")->name(
    "user.verify"
);
Route::get(
    "verify/email/{slug}",
    "Frontend\FrontendController@verify_email"
)->name("verify_email");
Route::get(
    "resend/email/{slug}",
    "Frontend\FrontendController@resend_email"
)->name("resend_email");

//google

Route::get(
    "login/google",
    "Frontend\FrontendController@redirectToGoogle"
)->name("login.google");

Route::get(
    "login/google/callback",
    "Frontend\FrontendController@handleGoogleCallback"
);

//Facebook
Route::get(
    "login/facebook",
    "Frontend\FrontendController@redirectToFacebook"
)->name("login.facebook");

Route::get(
    "login/facebook/callback",
    "Frontend\FrontendController@handleFacebookCallback"
);

Route::get(
    "category/{slug}",
    "Frontend\FrontendController@single_category"
)->name("category");

Route::get(
    "product-details/{slug}",
    "Frontend\FrontendController@main_product"
)->name("main_product");


Route::get(
    "products/{suitablefor}",
    "Frontend\FrontendController@productsSuitableFor"
)->name("productsSuitableFor");

Route::get(
    "products/{suitablefor}/{category}",
    "Frontend\FrontendController@productsSuitableForWithCategory"
)->name("productsSuitableForWithCategory");

Route::get("products/{slug}", "Frontend\FrontendController@products")->name(
    "products"
);
Route::get("product/{slug}", "Frontend\FrontendController@main_product")->name(
    "main_product"
);
Route::get("products_search", "Frontend\SearchController@products_search");
Route::get("contact", "Frontend\FrontendController@contact")->name("contact");
Route::post("contact_details", "Frontend\FrontendController@contact_details")->name("contact_details");

Route::get("getForm", "Frontend\FrontendController@getForm");
Route::get("getDistricts", "Customer\CustomerboardController@getDistricts");

Route::get("about-us", "Frontend\FrontendController@aboutus")->name(
    "frontend.aboutus"
);

Route::get("blogs", "Frontend\FrontendController@blogs")->name(
    "frontend.blogs"
);

Route::get("blog/{slug}", "Frontend\FrontendController@blog_details")->name(
    "frontend.blog_details"
);

Route::get("page/{slug}", "Frontend\FrontendController@pages_details")->name(
    "frontend.pages_details"
);

Route::get("brands", "Frontend\FrontendController@brands")->name(
    "frontend.brands"
);

Route::get("brands/{slug}", "Frontend\FrontendController@brand_details")->name(
    "frontend.brand_details"
);

Route::get("search", "Frontend\SearchController@site_search")->name(
    "frontend.site_search"
);

Route::post("chat", "Frontend\FrontendController@chat")->name("frontend.chat");

// Test route for search functionality
Route::get("test-search", function() {
    return response()->json(['status' => 'Search route is working']);
});

/** Customer Starts **/
Route::namespace("Customer")
    ->prefix("customer")
    ->name("customer.")
    ->group(function () {
        Route::group(["middleware" => ["auth"]], function () {
            Route::get("dashboard", "CustomerboardController@index")->name(
                "dashboard"
            );

            Route::get(
                "account-detail",
                "CustomerboardController@account_detail"
            )->name("account_detail");

            Route::put("customer/{id}", "CustomerboardController@update")->name(
                "update"
            );

            Route::get(
                "logout",
                "CustomerboardController@logout"
            )->name("logout");
            Route::resource("order", "OrderController");
            Route::get("order_items/{id}", "OrderController@order_items");
            
            Route::get(
                "reset_password_without_token",
                "ResetPasswordController@show_password_reset_form"
            )->name("show_password_reset_form");

            Route::post(
                "reset_password_without_token",
                "ResetPasswordController@reset_password"
            )->name("reset_password");

            Route::resource("cart", "CartController");
            Route::resource("wishlist", "WishlistController");
            Route::resource("review", "ReviewController");

            Route::get("checkout", "CheckoutController@index")->name("checkout.index");
            Route::post("checkout", "CheckoutController@store")->name("checkout.store");
            Route::get("checkout/finish", "CheckoutController@finish")->name("checkout.finish");

        });
    });
/** Customer Ends **/

/** Frontend Ends **/

/** Backend Starts **/
Auth::routes(["register" => false]);
Route::namespace("Admin")
    ->prefix("admin")
    ->name("admin.")
    ->group(function () {
        Route::group(["middleware" => ["auth", "admin"]], function () {
            Route::get("/dashboard", "DashboardController@index")->name(
                "dashboard"
            );
            Route::resource("category", "CategoryController");

            Route::resource("brand", "BrandController");

            Route::resource("navbar", "NavbarController");

            Route::resource("banner", "BannerController");

            Route::resource("shipping", "ShippingController");

            Route::resource("order", "OrderController");

            Route::resource("aboutus", "AboutUsController");

            Route::resource("blogs", "BlogsController");

            Route::resource("video", "VideoController");

            Route::resource("testimonials", "TestimonialsController");

            Route::resource("pages", "PagesController");

            Route::resource("homepageextra", "HomepageextraController");

            Route::get(
                "order/order-item/{id}",
                "OrderController@orderitem"
            )->name("order.orderitem");

            Route::resource("notification", "NotificationController");

            Route::resource("setting", "SettingController");

            Route::resource("product", "ProductController");

            Route::get(
                "product/{id}/attribute",
                "ProductAttributeController@index"
            )->name("product_attribute.index");

            Route::get(
                "product/{id}/attribute/create",
                "ProductAttributeController@create"
            )->name("product_attribute.create");

            Route::put(
                "product/attribute/store",
                "ProductAttributeController@store"
            )->name("product_attribute.store");

            Route::get(
                "product/{id}/attribute/edit",
                "ProductAttributeController@edit"
            )->name("product_attribute.edit");

            Route::put(
                "product/{id}/attribute/update",
                "ProductAttributeController@update"
            )->name("product_attribute.update");

            Route::delete(
                "product/{id}/attribute/delete",
                "ProductAttributeController@destroy"
            )->name("product_attribute.destroy");

            Route::get(
                "reset_password_without_token",
                "ResetPasswordController@show_password_reset_form"
            )->name("show_password_reset_form");

            Route::post(
                "reset_password_without_token",
                "ResetPasswordController@reset_password"
            )->name("reset_password");
        });
    });
/** Backend Ends **/
Route::get('sitemap.xml', 'Frontend\SitemapController@index')->name('frontend.sitemap');
Route::post("ckeditor", "Admin\CkEditorController@upload")->name("upload");
Route::get("getUploadedFiles", "Admin\CkEditorController@getUploadedFiles")->name("getUploadedFiles");


// esewa 
Route::get("pay/esewa-success", "Customer\CheckoutController@esewasuccess")->name("pay.esewa-success");
Route::get("pay/esewa-fail", "Customer\CheckoutController@esewafail")->name("pay.esewa-fail");

// eSewa Simulation
Route::get("esewa/simulation/{order_id}", function($order_id) {
    $order = \App\Models\Order::findOrFail($order_id);
    return view('frontend.pages.esewa_simulation', compact('order'));
})->name("esewa.simulation")->middleware(['auth']);