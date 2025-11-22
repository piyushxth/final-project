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
         <div class="col-lg-9 col-md-12 col-sm-12">
            <div class="alert alert-success success-notification">
               <p><i class="fas fa-check-circle"></i> Payment successful! Your order has been placed successfully.</p>
               <p class="mt-2"><small><i class="fas fa-info-circle"></i> This is a simulation of the eSewa payment process for demonstration purposes.</small></p>
            </div>
            <div class="button d-flex mt-5 gap-3">
               <a href="{{ route('customer.order.index') }} " class="btn btn-primary">View My Order</a>
               <a href="{{ route('home') }}" class="btn btn-primary"> Continue Shopping</a>
            </div>
         </div>
      </div>
   </div>
</div>
@endsection