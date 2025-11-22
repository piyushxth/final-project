@extends('frontend.layouts.master')
@section('content')
<section class="breadcrumb-section py-4">
   <div class="container">
      <nav aria-label="breadcrumb">
         <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
            <li class="breadcrumb-item active">My Cart</li>
         </ol>
      </nav>
   </div>
</section>
<div class="profile-dashboard-section custom-margin">
   <div class="container">
      <div class="row gy-4">
         <div class="col-lg-3 col-md-4 col-sm-12">
            @include('frontend.customer.sidebar')
         </div>
         <div class="col-lg-9 col-md-8 col-sm-12">
            <div class="profile">
               @if(session()->has('success_msg'))
               <div class="mt-4 alert alert-success">
                  {{ session()->get('success_msg') }}
               </div>
               @endif
               @if(session()->has('error_msg'))
               <div class="mt-3 alert alert-error">
                  {{ session()->get('error_msg') }}
               </div>
               @endif
               @if($cartItems->isNotEmpty())
               <div class="title">
                  <h1 class="main-heading">My Cart</h1>
               </div>
               <div class="my-cart-table" >
                  <table>
                     <tr>
                        <th>S.N</th>
                        <th>Image</th>
                        <th>Name</th>
                        <th>Quantity</th>
                        <th>Size</th>
                        <th>Rate</th>
                        <th>Sub-Total</th>
                        <th>Action</th>
                     </tr>
                     @php($priceArray = [])
                     @foreach($cartItems as $cartItem)
                     @php($priceArray[$loop->iteration] = $cartItem->price * $cartItem->qty)
                     <tr>
                        <td>{{$loop->iteration}}</td>
                        <td>
                           <a href="{{ route('main_product', [$cartItem->model->slug]) }}" target="_blank">
                              <figure>
                              <img src="{{ (isset($cartItem->options[1])) && ($cartItem->options[1] != '') && file_exists(public_path('images/'.$cartItem->options[1])) ? asset('images/'.$cartItem->options[1]) : asset('images/default.png') }}" alt="{{ ($cartItem->name) }}">
                           </figure>
                           </a>
                        </td>
                        <td>
                           <a href="{{ route('main_product', [$cartItem->model->slug]) }}" target="_blank">
                              {{ ($cartItem->name) }}
                           </a>
                        </td>
                        <td>
                           <input type="number" min="1" value="{{ ($cartItem->qty) }}" autocomplete="off" />
                        </td>
                        <td>{{ ($cartItem->options[0]) }}</td>
                        <td>{{ env('DEFAULT_CURRENCY_SYMBOL','Rs.') }}{{ (formatcurrency($cartItem->price,'NPR')) }}</td>
                        <td>{{ env('DEFAULT_CURRENCY_SYMBOL','Rs.') }}{{ (formatcurrency($priceArray[$loop->iteration],'NPR')) }}</td>
                        <td>
                           <div class="wishlist-action-button justify-content-center">
                              <form action="{{ route('customer.cart.destroy', $cartItem->rowId) }}" method="POST">
                                 @csrf
                                 @method('delete')
                                 <button type="submit" class="delet-product" onclick="return confirm('Are you sure you want to delete?');">
                                 <i class="fas fa-trash-alt"></i>
                                 </button>
                              </form>
                           </div>
                        </td>
                     </tr>
                     @endforeach
                  </table>
               </div>
               <div class="total text-end mt-5">
                  <h6>Grand Total: <span class="ps-3">{{ env('DEFAULT_CURRENCY_SYMBOL','Rs.') }}{{ (formatcurrency(array_sum($priceArray),'NPR')) }}
                     </span>
                  </h6>
               </div>
               <div class="payment-options mt-4 p-3 bg-light rounded">
                  <h6><i class="fas fa-credit-card"></i> Payment Options</h6>
                  <p class="mb-2">Choose your preferred payment method:</p>
                  <ul class="list-unstyled">
                     <li><i class="fas fa-check-circle text-success"></i> Cash on Delivery - Pay when you receive your order</li>
                     <li><i class="fas fa-check-circle text-success"></i> eSewa - Secure online payment</li>
                  </ul>
               </div>
               <div class="shopping-action-button ">
                  <div class="button me-3">
                     <a  class="continue-shopping"  href="{{ route('home') }}"> Continue Shopping</a>
                  </div>
                  <div class="button ">
                     <a class="checkout" href="{{ route('customer.checkout.index') }}"> Proceed to Checkout</a>
                  </div>
                  <div class="button ms-3">
                     <a class="btn btn-success" href="{{ route('customer.checkout.index') }}?payment=esewa"> Proceed to Payment</a>
                  </div>
               </div>
               @else
               <div class="title">
                  <h4>My Cart</h4>
               </div>
               <div class="my-cart-table">
                  <p>No items on Cart</p>
               </div>
               @endif
            </div>
         </div>
      </div>
   </div>
</div>
@endsection