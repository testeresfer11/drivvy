@extends('layouts.app')

@section('content')
<form action="{{ route('payment') }}" method="post" id="payment-form">
    @csrf
    <input type="text" name="amount" placeholder="Amount" required>
    <script src="https://checkout.stripe.com/checkout.js" class="stripe-button"
            data-key="{{ config('services.stripe.key') }}"
            data-description="Payment"
            data-amount="1000"  {{-- Amount in cents --}}
            data-locale="auto"></script>
</form>

@endsection