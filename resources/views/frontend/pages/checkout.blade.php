@extends('frontend.layouts.master')
@section('content')
<section class="breadcrumb-section py-4">
   <div class="container">
      <nav aria-label="breadcrumb">
         <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
            <li class="breadcrumb-item"><a href="{{ route('customer.cart.index') }}">My Cart</a></li>
            <li class="breadcrumb-item active">Checkout</li>
         </ol>
      </nav>
   </div>
</section>
<div class="profile-dashboard-section custom-margin">
   <div class="container">
      <div class="row gy-4">
         <div class="col-lg-3 col-md-12 col-sm-12">
            @include('frontend.customer.sidebar')
         </div>
         <div class="col-lg-5 col-md-12 col-sm-12">
            <div class="billing-address">
               <div class="title mb-4">
                  <h4>Billing Address</h4>
               </div>
               @php($cart_items = Cart::instance(auth()->user()->id)->content())
               <form id="add-to-order-form" class="order-checkout-form" action="{{ route('customer.checkout.store') }}" method="post" autocomplete="off">
                  @csrf
                  <input type="hidden" value="{{Cart::subtotalFloat()}}" name="sub_total" readonly />
                  <div class="row gy-4">
                     <div class="col-lg-6 col-md-6 col-sm-6">
                        <div class="name">
                           <label for="name">Name:</label><br>
                           <input type="text" name="name" id="name" value="{{ auth()->user()->name }}" required><br>
                           <span class="error-message pt-3 text-danger"></span>
                        </div>
                     </div>
                     <div class="col-lg-6 col-md-6 col-sm-6">
                        <div class="number ">
                           <label for="number">Contact Number</label><br>
                           <input type="number" name="number" id="number" value="{{ auth()->user()->phone }}" required><br>
                           <span class="error-message pt-3 text-danger"></span>
                        </div>
                     </div>
                     <div class="col-lg-6 col-md-6 col-sm-6">
                        <div class="email ">
                           <label for="email">E-mail</label><br>
                           <input type="email" name="email" id="email" value="{{ auth()->user()->email }}" required><br>
                           <span class="error-message pt-3 text-danger"></span>
                        </div>
                     </div>
                     <div class="col-lg-6 col-md-6 col-sm-6">
                        <div class="provience ">
                           <label for="province"> Province </label>
                           <select name="provience" id="provience">
                              <option value="">Select Province</option>
                              @if ($provinces->isNotEmpty())
                              @foreach ($provinces as $province)
                              <option value="{{ $province->id }}" @if ($province_id==$province->id) {{ "selected='selected'" }} @endif>
                                 {{ $province->province_name }}
                              </option>
                              @endforeach
                              @endif
                           </select>
                           @error('provience')
                           <span class="error-message pt-3 text-danger">{{ $errors->first('provience') }}</span>
                           @enderror
                        </div>
                     </div>
                     <div class="col-lg-6 col-md-6 col-sm-6">
                        <div class="district">
                           <label for="district">District</label>
                           <select name="district" id="district">
                              <option value="">Select District</option>
                           </select>
                           @error('district')
                           <span class="error-message pt-3 text-danger">{{ $errors->first('district') }}</span>
                           @enderror
                        </div>
                     </div>
                     <div class="col-lg-6 col-md-6 col-sm-6">
                        <div class="street ">
                           <label for="street">Street/Tole</label><br>
                           <input type="text" id="street" value="{{ auth()->user()->address }}"><br>
                           <span class="error-message pt-3 text-danger"></span>
                        </div>
                     </div>
                  </div>
            </div>
            <div class="billing-address shipping-address mt-4">
               <div class="title mb-4 d-flex align-items-start flex-column">
                  <h4 class="mb-3">Shipping Address</h4>
                  <div class="same-as-billing-address">
                     <input type="checkbox" id="same-as-billing-address" name="same-as-billing-address" value="Yes">
                     <label class="ps-1" for="same-as-billing-address"> Shipping Address same as Billing
                        Address</label><br>
                  </div>
               </div>
               <div class="row gy-4">
                  <div class="col-lg-6 col-md-6 col-sm-6">
                     <div class="name">
                        <label for="name">Name:</label><br>
                        <input type="text" name="name" id="name" class="name" required><br>
                        @error('name')
                        <span class="text-danger">
                           {{ $errors->first('name') }}
                        </span>
                        @enderror
                     </div>
                  </div>
                  <div class="col-lg-6 col-md-6 col-sm-6">
                     <div class="number ">
                        <label for="number">Contact Number</label><br>
                        <input type="number" name="number" id="number" class="number" required><br>
                        @error('number')
                        <span class="text-danger">
                           {{ $errors->first('number') }}
                        </span>
                        @enderror
                     </div>
                  </div>
                  <div class="col-lg-6 col-md-6 col-sm-6">
                     <div class="email ">
                        <label for="email">E-mail</label><br>
                        <input type="email" name="email" id="email" class="email" required><br>
                        @error('email')
                        <span class="text-danger">
                           {{ $errors->first('email') }}
                        </span>
                        @enderror
                     </div>
                  </div>
                  <div class="col-lg-6 col-md-6 col-sm-6">
                     <div class="provience ">
                        <label for="province"> Province </label>
                        <select name="provience" id="provience" class="provienceShippingAddress">
                           <option value="">Select Province</option>
                           @if ($provinces->isNotEmpty())
                           @foreach ($provinces as $province)
                           <option value="{{ $province->id }}">
                              {{ $province->province_name }}
                           </option>
                           @endforeach
                           @endif
                        </select>
                        @error('provience')
                        <span class="error-message pt-3 text-danger">{{ $errors->first('provience') }}</span>
                        @enderror
                     </div>
                  </div>
                  <div class="col-lg-6 col-md-6 col-sm-6">
                     <div class="district">
                        <label for="district">District</label>
                        <select name="district" id="district" class="districtShippingAddress">
                           <option value="">Select District</option>
                        </select>
                        @error('district')
                        <span class="error-message pt-3 text-danger">{{ $errors->first('district') }}</span>
                        @enderror
                     </div>
                  </div>
                  <div class="col-lg-6 col-md-6 col-sm-6">
                     <div class="street ">
                        <label for="street">Street/Tole</label><br>
                        <input type="text" id="street" name="street" class="street"><br>
                        @error('street')
                        <span class="text-danger">
                           {{ $errors->first('street') }}
                        </span>
                        @enderror
                     </div>
                  </div>
                  <div class="bd-title col-lg-12 col-md-12 col-sm-12">
                     <h4>Payment</h4>
                  </div>
                  <div class="col-lg-12 col-md-12 col-sm-12">
                     <div class="cash-on-delivery">
                        <div class="form-check">
                           <input class="form-check-input" type="radio" value="cod" name="payment_method" id="payment_method_cod" {{ (isset($preselect_esewa) && $preselect_esewa) ? '' : 'checked' }}>
                           <label class="form-check-label" for="payment_method_cod">Cash on delivery</label>
                           <br>
                           <input class="form-check-input" type="radio" value="esewa" name="payment_method" id="payment_method_esewa" {{ (isset($preselect_esewa) && $preselect_esewa) ? 'checked' : '' }}>
                           <label class="form-check-label" for="payment_method_esewa"> Esewa</label>
                           @error('payment_method')
                           <div class="text-danger">
                              {{ $errors->first('payment_method') }}
                           </div>
                           @enderror
                        </div>
                     </div>
                  </div>
                  <div class="col-lg-12 col-md-12 col-sm-12">
                     <div class="terms">
                        <div class="form-check">
                           <input class="form-check-input {{ $errors->has('remember') ? 'is-invalid' : '' }}" type="checkbox" name="remember" id="termsconditions_chk" required>
                           <label class="form-check-label" for="termsconditions_chk">
                              I have read and agree to the websites <a class="text-danger" href="{{ route('frontend.pages_details', ['terms-condition']) }}" target="_blank">Terms &
                                 Conditions</a>
                           </label>
                           @error('remember')
                           <span class="text-danger">
                              {{ $errors->first('remember') }}
                           </span>
                           @enderror
                        </div>
                     </div>
                  </div>
               </div>
               <div class="shopping-action-button justify-content-start ">
                  <div class="button">
                     <input type="submit" value="Order Now" class="order-now-button">
                  </div>
               </div>
               </form>
            </div>
         </div>
         <div class="col-lg-4 col-md-12 col-sm-12">
            <div class="bd-table-product billing-address">
               <div class="bd-title">
                  <h4>Your Order</h4>
               </div>
               <table class="my-order-table table">
                  <thead>
                     <tr>
                        <th scope="col">Product</th>
                        <th scope="col">Quantity</th>
                        <th scope="col">Size</th>
                        <th scope="col">Total</th>
                     </tr>
                  </thead>
                  <tbody>
                     @forelse ($cart_items as $item)
                     <tr>
                        <td>{{ $item->name }}</td>
                        <td>{{ $item->qty }}</td>
                        <td>{{ $item->options[0] }}</td>
                        <td>{{ $item->price }}</td>
                     </tr>
                     @empty
                     <tr>
                        <td colspan="4" class="text-center">
                           Your cart is empty
                        </td>
                     </tr>
                     @endforelse
                  </tbody>
               </table>
               <table class=" conclusiion-table table">
                  <tbody>
                     <!-- <tr>
                        <td>Sub-Total</td>
                        <td class="order_subtotal" data-price="{{ Cart::subtotalFloat() }}">
                           Rs.{{ Cart::subtotalFloat() }}
                        </td>
                     </tr> -->
                     <!-- <tr>
                        <td>Shipping Cost</td>
                        <td>Rs. <span id="shipping_charge">0</span></td>
                     </tr> -->
                     <tr>
                        <td>Total</td>
                        <td>Rs.<span id="order_total_price">{{ Cart::subtotalFloat() }}</span></td>
                     </tr>
                  </tbody>
               </table>
            </div>
         </div>
      </div>
   </div>
</div>
@endsection