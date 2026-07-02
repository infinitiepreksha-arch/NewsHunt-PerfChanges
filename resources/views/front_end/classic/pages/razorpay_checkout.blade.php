@extends('front_end.' . $theme . '.layout.main')

@section('title', $title | 'News Hunt')
<link rel="icon" href="{{ $favicon ?? asset('assets/images/logo/favicon.png') }}" type="image/x-icon" />

@section('body')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    @if ($plan)
        <!-- Breadcrumbs -->
        <div id="wrapper" class="wrap overflow-hidden-x">
            <div class="breadcrumbs panel z-1 py-2 bg-gray-25 dark:bg-gray-100 dark:bg-opacity-5 dark:text-white">
                <div class="container max-w-xl">
                    <ul class="breadcrumb nav-x justify-center gap-1 fs-7 sm:fs-6 m-0">
                        <li><a href="{{ url('home') }}">{{ __('frontend-labels.home.title') }}</a></li>
                        <li><i class="unicon-chevron-right opacity-50"></i></li>
                        <li><a href="{{ url('membership') }}">{{ __('frontend-labels.membership.title') }}</a></li>
                        <li><i class="unicon-chevron-right opacity-50"></i></li>
                        <li><span class="opacity-70">{{ $title }}</span>
                        </li>
                    </ul>
                </div>
            </div>
        </div>

        <!-- Page Header -->
        <div class="section py-3 sm:py-6 lg:py-9">
            <div class="container max-w-xl">
                <div class="panel vstack gap-1 sm:gap-6 lg:gap-9">
                    <header class="page-header panel vstack text-center">
                        <h1 class="headingtag h3 lg:h1">{{ $title }}</h1>
                    </header>
                </div>
            </div>
        </div>
        <script src="https://checkout.razorpay.com/v1/checkout.js"></script>

        <div class="container py-5 text-center">
            <h2>{{ __('frontend-labels.payment_with_razorpay.processing_payment') }}</h2>
            <p><strong>{{ __('frontend-labels.payment_with_razorpay.plan') }}:</strong> {{ $plan }}</p>
            <p><strong>{{ __('frontend-labels.payment_with_razorpay.amount') }}:</strong> ₹{{ $amount }}</p>
        </div>

        <form name="razorpayForm">
            @csrf
            <input type="hidden" name="amount" value="{{ $amount }}">
            <input type="hidden" name="plan" value="{{ $plan }}">
        </form>

        <script src="https://checkout.razorpay.com/v1/checkout.js"></script>
        <script>
            async function startRazorpayPayment() {
                // Step 1: create order first
                const orderRes = await fetch("{{ route('razorpay.order.create') }}", {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/json",
                        "X-CSRF-TOKEN": "{{ csrf_token() }}"
                    },
                    body: JSON.stringify({
                        amount: "{{ $amount }}"
                    })
                });

                const orderData = await orderRes.json();

                // Step 2: open Razorpay checkout with order_id
                var options = {
                    "key": "{{ $setting->razorpay_key }}",
                    "amount": orderData.amount,
                    "currency": orderData.currency,
                    "name": "{{ $plan }} Plan",
                    "description": "Subscription Payment",
                    "order_id": orderData.order_id, // ✅ added
                    "handler": function(response) {
                        fetch("{{ route('razorpay.callback') }}", {
                                method: "POST",
                                headers: {
                                    "Content-Type": "application/json",
                                    "X-CSRF-TOKEN": "{{ csrf_token() }}"
                                },
                                body: JSON.stringify({
                                    razorpay_payment_id: response.razorpay_payment_id,
                                    razorpay_order_id: response.razorpay_order_id,
                                    razorpay_signature: response.razorpay_signature,
                                    amount: "{{ $amount }}",
                                    plan: "{{ $plan }}",
                                    plan_id: "{{ $plan_id }}",
                                    tenure_id: "{{ $tenure_id }}",
                                })
                            })
                            .then(res => res.json())
                            .then(data => {
                                if (data.status === "success") {
                                    window.location.href =
                                        "{{ route('payment.success') }}?user_id={{ auth()->id() }}&tenure_id={{ $tenure_id }}";
                                } else {
                                    alert("Payment failed to save.");
                                }
                            });
                    },
                    "prefill": {
                        "email": "{{ auth()->user()->email ?? '' }}",
                        "name": "{{ auth()->user()->name ?? '' }}"
                    },
                    "theme": {
                        "color": "#3399cc"
                    }
                };

                var rzp = new Razorpay(options);
                rzp.open();
            }

            startRazorpayPayment();
        </script>
    @elseif ($smart_ad_id && $ad_details_id)
        <!-- Breadcrumbs -->
        <div id="wrapper" class="wrap overflow-hidden-x">
            <div class="breadcrumbs panel z-1 py-2 bg-gray-25 dark:bg-gray-100 dark:bg-opacity-5 dark:text-white">
                <div class="container max-w-xl">
                    <ul class="breadcrumb nav-x justify-center gap-1 fs-7 sm:fs-6 m-0">
                        <li><a href="{{ url('home') }}">{{ __('frontend-labels.home.title') }}</a></li>
                        <li><i class="unicon-chevron-right opacity-50"></i></li>
                        <li><a href="{{ url('sponsor-ads') }}">{{ __('frontend-labels.sponsor_ads.title') }}</a></li>
                        <li><i class="unicon-chevron-right opacity-50"></i></li>
                        <li><span class="opacity-70">{{ $title }}</span>
                        </li>
                    </ul>
                </div>
            </div>
        </div>

        <!-- Page Header -->
        <div class="section py-3 sm:py-6 lg:py-9">
            <div class="container max-w-xl">
                <div class="panel vstack gap-1 sm:gap-6 lg:gap-9">
                    <header class="page-header panel vstack text-center">
                        <h1 class="headingtag h3 lg:h1">{{ $title }}</h1>
                    </header>
                </div>
            </div>
        </div>
        <script src="https://checkout.razorpay.com/v1/checkout.js"></script>

        <div class="container py-5 text-center">
            <h2>{{ __('frontend-labels.payment_with_razorpay.processing_razorpay_payment') }}</h2>
            <p><strong>{{ __('frontend-labels.payment_with_razorpay.smart_ad_id') }}:</strong> {{ $smart_ad_id }}</p>
            <p><strong>{{ __('frontend-labels.payment_with_razorpay.ad_details_id') }}:</strong> {{ $ad_details_id }}</p>
            <p><strong>{{ __('frontend-labels.payment_with_razorpay.amount') }}:</strong> ₹{{ $amount }}</p>
        </div>

        <form name="razorpayForm">
            @csrf
            <input type="hidden" name="amount" value="{{ $amount }}">
            <input type="hidden" name="smart_ad_id" value="{{ $smart_ad_id }}">
            <input type="hidden" name="ad_details_id" value="{{ $ad_details_id }}">
        </form>

        <script src="https://checkout.razorpay.com/v1/checkout.js"></script>
        <script>
            async function startSmartAdsPayment() {
                const orderRes = await fetch("{{ route('razorpay.order.create') }}", {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/json",
                        "X-CSRF-TOKEN": "{{ csrf_token() }}"
                    },
                    body: JSON.stringify({
                        amount: "{{ $amount }}"
                    })
                });

                const orderData = await orderRes.json();

                var options = {
                    "key": "{{ $setting->razorpay_key }}",
                    "amount": orderData.amount,
                    "currency": orderData.currency,
                    "name": "Smart Ads",
                    "description": "Subscription Payment",
                    "order_id": orderData.order_id, // ✅ added
                    "handler": function(response) {
                        fetch("{{ route('razorpay.callback') }}", {
                                method: "POST",
                                headers: {
                                    "Content-Type": "application/json",
                                    "X-CSRF-TOKEN": "{{ csrf_token() }}"
                                },
                                body: JSON.stringify({
                                    razorpay_payment_id: response.razorpay_payment_id,
                                    razorpay_order_id: response.razorpay_order_id,
                                    razorpay_signature: response.razorpay_signature,
                                    amount: "{{ $amount }}",
                                    smart_ad_id: "{{ $smart_ad_id }}",
                                    ad_details_id: "{{ $ad_details_id }}",
                                })
                            })
                            .then(res => res.json())
                            .then(data => {
                                if (data.status === "success") {
                                    window.location.href =
                                        "{{ route('payment.success') }}?smart_ad_id={{ $smart_ad_id }}&ad_details_id={{ $ad_details_id }}";
                                } else {
                                    alert("Payment failed to save.");
                                }
                            });
                    },
                    "prefill": {
                        "email": "{{ auth()->user()->email ?? '' }}",
                        "name": "{{ auth()->user()->name ?? '' }}"
                    },
                    "theme": {
                        "color": "#3399cc"
                    }
                };

                var rzp = new Razorpay(options);
                rzp.open();
            }

            startSmartAdsPayment();
        </script>
    @endif
@endsection
