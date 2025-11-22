<?php

namespace App\Http\Controllers\Customer;

use Cixware\Esewa\Client, Cixware\Esewa\Config;
use App\Http\Controllers\Controller;
use App\Models\Districts;
use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\Shipping;
use App\Models\OrderItems;
use App\Models\User;
use App\Models\Provinces;
use App\Models\ProductSizes;
use App\Notifications\StatusNotification,
    App\Notifications\SendEmail;
use Auth, Str, Notification, Cart, Session, Mail;

class CheckoutController extends Controller
{
    private $esewa_success_url,
        $esewa_failure_url,
        $esewa_merchant_id;

    public function __construct()
    {
        $this->middleware(["XssSanitizer"]);
        $this->esewa_success_url = url("/") . "/pay/esewa-success";
        $this->esewa_failure_url = url("/") . "/pay/esewa-fail";
        $this->esewa_merchant_id = env("ESEWA_MERCHANT_CODE", "ES-ELN");
    }
    public function index(Request $request)
    {
        $title = "Checkout";
        $shipping = Shipping::get();
        $provinces = Provinces::orderBy("id", "ASC")->get();
        $province_id =
            \Illuminate\Support\Facades\Auth::user()->provience != null ? \Illuminate\Support\Facades\Auth::user()->provience : "";
        
        // If payment parameter is set to esewa, pre-select eSewa payment method
        $preselect_esewa = $request->has('payment') && $request->payment == 'esewa';
        
        return view(
            "frontend/pages/checkout",
            compact("title", "shipping", "provinces", "province_id", "preselect_esewa")
        );
    }

    public function store(Request $request)
    {
        // dd($request);
        $userdata = User::find(\Illuminate\Support\Facades\Auth::user()->id);
        $users = User::where("role", "admin")->first();

        $this->validate(

            $request,
            [
                "name" => "regex:/^([a-zA-Z]+)(\s[a-zA-Z]+)*$/|required",
                "number" => "required|digits:10",
                "email" => "required|regex:/(.+)@(.+)\.(.+)/i",
                "provience" => "required",
                "district" => "required",
                "street" => "required",
                "payment_method" => "required",
            ],
            [
                "name.required" => "Name is required",
                "name.regex" => "Name must be String",
                "number.required" => "Contact number is required",
                "number.digits" => "Contact number must be minimum 10 number",
                "email.required" => "Email is required",
                "email.regex" => "Invalid email format",
                "provience.required" => "Provience is required",
                "district.required" => "District  is required",
                "street.required" => "Street is required",
                "payment_method.required" => "Payment Method is required",
            ]
        );

        $input = $request->all();
        $input["name"] = $request->input("street");
        $input["price"] = $request->input("sub_total");
        $shipping = Shipping::create($input);
        if (!$shipping) {
            throw new \Exception('Failed to create shipping record');
        }
        $shippingId = $shipping->id; // Use the created shipping ID directly
        $inputOrder = $request->all();
        $inputOrder["order_number"] = "ORD-" . strtoupper(\Illuminate\Support\Str::random(10));
        $inputOrder["user_id"] = \Illuminate\Support\Facades\Auth::user()->id;
        $inputOrder["sub_total"] = $request->sub_total;
        $inputOrder["shipping_id"] = $shippingId;
        $inputOrder["total_amount"] = $request->input("sub_total");
        $inputOrder["phone"] = $request->input("number");
        $inputOrder["full_name"] = $request->input("name");
        $inputOrder["address"] = $request->input("street");

        try {
            $provienceShipping = Provinces::where('id', $request->input("provience"))->get("province_name");
            $districtShipping = Districts::where('id', $request->input("district"))->get("district_name");
            $provienceBilling = Provinces::where('id', \Illuminate\Support\Facades\Auth::user()->provience)->get("province_name");
            $districtBilling = Districts::where('id', \Illuminate\Support\Facades\Auth::user()->district)->get("district_name");
            $order = Order::create($inputOrder);
            if (!$order) {
                throw new \Exception('Failed to create order');
            }
            $orderId = $order->id; // Use the created order ID directly
            $request->request->add(['order_id' => $orderId]);
            $date = date($order->created_at);

            //Insert into OrderItems Table
            $cartItems = \Gloudemans\Shoppingcart\Facades\Cart::instance(\Illuminate\Support\Facades\Auth::user()->id)->content();
            
            // Check if cart is empty
            if ($cartItems->isEmpty()) {
                throw new \Exception('Cart is empty');
            }
            
            $orderItemsCreated = false;
            foreach ($cartItems as $item) {
                $orderItem = OrderItems::create([
                    "order_id" => $orderId,
                    "product_id" => $item->model->id,
                    "quantity" => $item->qty,
                    "size" => $item->options[0],
                    "price" => $item->price,
                    "product_attr_image" => $item->options[1],
                ]);
                
                if ($orderItem) {
                    $orderItemsCreated = true;
                }
            }

            $notification_details = [
                "title" => "New order created",
                "actionURL" => route("admin.order.show", $orderId),
                "fas" => "fa-file-alt",
            ];

            $email_details = [
                "first_name" => $request->input("name"),
                "email" => $request->input("email"),
                "phone" => $request->input("number"),
                "order_number" => $inputOrder["order_number"],
                "total_amount" => $inputOrder["total_amount"],
                "location" => $request->input("street"),
                "number" => $request->input("number"),
                "shipping_charge" => "0",
                "date" => $date,
                "provienceShipping" => $provienceShipping[0]['province_name'],
                "districtShipping" => $districtShipping[0]['district_name'],
                "provienceBilling" => $provienceBilling[0]['province_name'],
                "districtBilling" => $districtBilling[0]['district_name'],
            ];

            \Illuminate\Support\Facades\Notification::send($users, new StatusNotification($notification_details));
            \Illuminate\Support\Facades\Mail::to(\Illuminate\Support\Facades\Auth::user()->email)->send(
                new \App\Mail\OrderMailable($email_details)
            );

            //Remove Stock and Cart
            if ($orderItemsCreated) {
                foreach (\Gloudemans\Shoppingcart\Facades\Cart::instance(\Illuminate\Support\Facades\Auth::user()->id)->content() as $item) {
                    $remove_size = ProductSizes::where([
                        "product_id" => $item->model->id,
                        "size" => $item->options[0],
                    ])->firstOrFail();
                    $remove_size->stock = $remove_size->stock - $item->qty;
                    $remove_size->save();
                }
                \Gloudemans\Shoppingcart\Facades\Cart::destroy();
            }
            if ($inputOrder['payment_method'] == 'esewa') {
                // For eSewa, redirect to simulation page with the specific order ID
                return redirect()->route('esewa.simulation', ['order_id' => $orderId]);
            } else {
                return redirect()
                    ->route("customer.checkout.finish")
                    ->with("success_msg", "Order placed successfully");
            }
        } catch (\Exception $e) {
            return redirect()
                ->route("customer.checkout.finish")
                ->with("error_msg", "Order cannot be placed. Please try again");
        }
    }

    public function finish(Request $request)
    {
        $title = "Finish | Checkout";
        return view(
            "frontend/pages/checkout_complete",
            compact("title")
        );
    }

    public function payProcess(Request $request)
    {
        // Get the latest order for the user
        $order = \App\Models\Order::where('user_id', \Illuminate\Support\Facades\Auth::user()->id)->latest()->first();
        
        // Check if order exists
        if ($order) {
            // Redirect to simulation page instead of actual eSewa gateway
            return redirect()->route('esewa.simulation', ['order_id' => $order->id]);
        } else {
            // If no order found, redirect back with error
            return redirect()
                ->route("customer.checkout.index")
                ->with("error_msg", "Order not found. Please try again.");
        }
    }

    public function _processEsewa($gateway_configuration, $request)
    {
        $total_amount = (int) $request->sub_total;
        $o_id = $request->order_id;

        // Initialize eSewa client
        $esewa = new \Cixware\Esewa\Client($gateway_configuration);

        // Process the payment
        $esewa->process($o_id, $total_amount, 0, 0, 0);

        // Dump all request data for debugging
        // dd($request->all());
    }


    public function esewasuccess(Request $request)
    {
        // For simulation, we get order_id from the request (not from eSewa response)
        $order_id = $request->input("order_id") ?? $request->query("order_id");
        
        // If not in request, try to get from session (fallback)
        if (!$order_id) {
            $order_id = session('esewa_order_id');
        }
      
        $payment_gateway = \Illuminate\Support\Str::ucfirst("esewa");
        $title = "Payment Success";
        
        // Find order by ID
        $order_details = Order::where([
            "id" => $order_id,
            "payment_method" => "esewa",
        ])->first();
        
        // For simulation, we'll assume payment is always successful
        if ($order_details && $order_details->payment_status == "unpaid") {
            $order_details->payment_status = "paid";
            $order_details->updated_at = now()->format("Y-m-d H:i:s");
            $order_details->update();
            
            // Update order status to confirmed
            $order_details->status = "confirmed";
            $order_details->update();
            
            return view(
                "frontend.pages.paymentsuccess",
                compact(
                    "title"
                )
            );
        } 
        
        // If order already paid or not found, still show success for simulation
        return view(
            "frontend.pages.paymentsuccess",
            compact(
                "title"
            )
        );
    }

    public function esewafail(Request $request)
    {
        $title = "Payment Failed";
        // For simulation, we'll show the failure page but allow users to try again
        return view(
            "frontend.pages.paymentfail",
            compact(
                "title"
            )
        );
    }
}
