<!-- resources/views/front_end/{theme}/pages/stripe_checkout.blade.php -->

@extends('front_end.' . $theme . '.layout.main')

@section('content')
    <div class="container">
        <h2>Complete Your Payment</h2>

        <form action="{{ route('payment.stripe.create') }}" method="POST">
            @csrf
            <input type="hidden" name="amount" value="{{ $amount }}"> <!-- Amount from backend -->
            <input type="hidden" name="plan_id" value="{{ $plan_id }}">
            <button type="submit" class="btn btn-success">Pay with Stripe</button>
        </form>
    </div>
@endsection
