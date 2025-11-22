@extends('frontend.layouts.master')
@section('content')
<section class="breadcrumb-section py-4">
   <div class="container">
      <nav aria-label="breadcrumb">
         <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
            <li class="breadcrumb-item"><a href="{{ route('customer.cart.index') }}">My Cart</a></li>
            <li class="breadcrumb-item"><a href="{{ route('customer.checkout.index') }}">Checkout</a></li>
            <li class="breadcrumb-item active">eSewa Payment</li>
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
            <div class="payment-section">
               <div class="title mb-4">
                  <h4>eSewa Payment Simulation</h4>
               </div>
               
               <div class="payment-details card p-4 mb-4">
                  <h5>Order Summary</h5>
                  <table class="table">
                     <tr>
                        <td>Order Number:</td>
                        <td>{{ $order->order_number }}</td>
                     </tr>
                     <tr>
                        <td>Total Amount:</td>
                        <td>Rs. {{ number_format($order->total_amount, 2) }}</td>
                     </tr>
                  </table>
               </div>
               
               <div class="esewa-simulation card p-4">
                  <h5>eSewa Payment Gateway Simulation</h5>
                  <p class="text-muted">This is a simulation of the eSewa payment process for demonstration purposes.</p>
                  
                  <div class="payment-form">
                     <form id="esewa-simulation-form" action="{{ route('pay.esewa-success') }}" method="GET">
                        <input type="hidden" name="order_id" value="{{ $order->id }}">
                        <input type="hidden" name="amt" value="{{ $order->total_amount }}">
                        <input type="hidden" name="refId" value="SIM{{ strtoupper(\Illuminate\Support\Str::random(8)) }}">
                        
                        <div class="mb-3">
                           <label for="esewa_username" class="form-label">eSewa Username</label>
                           <input type="text" class="form-control" id="esewa_username" name="esewa_username" placeholder="Enter your eSewa username" required>
                        </div>
                        
                        <div class="mb-3">
                           <label for="esewa_pin" class="form-label">eSewa PIN</label>
                           <input type="password" class="form-control" id="esewa_pin" name="esewa_pin" placeholder="Enter your eSewa PIN" required>
                        </div>
                        
                        <div class="mb-3">
                           <label for="phone_number" class="form-label">Phone Number</label>
                           <input type="text" class="form-control" id="phone_number" name="phone_number" placeholder="Enter your phone number" value="{{ auth()->user()->phone }}" required>
                        </div>
                        
                        <div class="d-flex gap-3 mt-4">
                           <button type="submit" class="btn btn-success" id="pay-button">Pay Rs. {{ number_format($order->total_amount, 2) }}</button>
                           <a href="{{ route('pay.esewa-fail') }}" class="btn btn-danger">Cancel Payment</a>
                        </div>
                     </form>
                  </div>
               </div>
               
               <div class="security-note mt-4">
                  <div class="alert alert-info">
                     <h6><i class="fas fa-shield-alt"></i> Secure Payment</h6>
                     <p>This is a simulation for demonstration purposes only. In a real implementation, this would redirect to the actual eSewa payment gateway.</p>
                  </div>
               </div>
            </div>
         </div>
      </div>
   </div>
</div>

<style>
.payment-section .card {
   border: 1px solid #e0e0e0;
   border-radius: 8px;
   box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

.payment-section h5 {
   color: #333;
   border-bottom: 1px solid #eee;
   padding-bottom: 10px;
   margin-bottom: 15px;
}

.esewa-simulation {
   background-color: #f8f9fa;
}

.security-note .alert {
   border-left: 4px solid #17a2b8;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
   const payButton = document.getElementById('pay-button');
   const form = document.getElementById('esewa-simulation-form');
   
   form.addEventListener('submit', function(e) {
      e.preventDefault();
      
      // Show loading state
      payButton.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Processing...';
      payButton.disabled = true;
      
      // Simulate payment processing delay
      setTimeout(function() {
         // In a real implementation, this would submit to the actual eSewa gateway
         // For simulation, we'll just submit the form
         form.submit();
      }, 2000);
   });
});
</script>
@endsection