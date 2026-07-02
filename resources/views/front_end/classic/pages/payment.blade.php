@extends('front_end.' . $theme . '.layout.main')

@section('body')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">

    <!-- Breadcrumbs -->
    <div id="wrapper" class="wrap overflow-hidden-x">
        <div class="breadcrumbs panel z-1 py-2 bg-gray-25 dark:bg-gray-100 dark:bg-opacity-5 dark:text-white">
            <div class="container max-w-xl">
                <ul class="breadcrumb nav-x justify-center gap-1 fs-7 sm:fs-6 m-0">
                    <li><a href="{{ url('home') }}">{{ __('frontend-labels.home.title') }}</a></li>
                    <li><i class="unicon-chevron-right opacity-50"></i></li>
                    <li><a href="{{ url('membership') }}">{{ __('frontend-labels.membership.title') }}</a></li>
                    <li><i class="unicon-chevron-right opacity-50"></i></li>
                    <li><span class="opacity-70">{{ $title }}</span></li>
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

    <!-- Payment Form -->
    <div class="section py-3">
        <div class="container max-w-md mx-auto">
            <div class="panel bg-white dark:bg-gray-800 shadow-lg rounded-lg overflow-hidden">
                <div class="p-6">
                    <h2 class="text-2xl font-bold text-center mb-6 dark:text-white">
                        {{ __('frontend-labels.payment_gateway.you_are_almost_there') }}</h2>

                    @if (session('error'))
                        <div class="alert alert-danger text-center">
                            {{ session('error') }}
                        </div>
                    @endif

                    <div class="mb-6 space-y-2">

                        {{-- Membership Payment --}}
                        @if (request('plan'))
                            <div class="flex justify-between items-center">
                                <span
                                    class="font-medium dark:text-gray-300">{{ __('frontend-labels.payment_gateway.plan') }}:</span>
                                <span class="font-bold dark:text-white">{{ request('plan') }}</span>
                            </div>
                        @endif

                        {{-- Smart Ad Payment --}}
                        @if (request('smart_ad_id'))
                            <div class="flex justify-between items-center">
                                <span
                                    class="font-medium dark:text-gray-300">{{ __('frontend-labels.payment_gateway.smart_ad_id') }}:</span>
                                <span class="font-bold dark:text-white">{{ request('smart_ad_id') }}</span>
                            </div>
                        @endif

                        @if (request('ad_details_id'))
                            <div class="flex justify-between items-center">
                                <span
                                    class="font-medium dark:text-gray-300">{{ __('frontend-labels.payment_gateway.smart_detail_id') }}:</span>
                                <span class="font-bold dark:text-white">{{ request('ad_details_id') }}</span>
                            </div>
                        @endif

                        <div class="flex justify-between items-center">
                            <span
                                class="font-medium dark:text-gray-300">{{ __('frontend-labels.payment_gateway.amount') }}:</span>
                            <span class="font-bold dark:text-white">{{ number_format(request('amount'), 2) }}</span>
                        </div>

                        @if ($tenure_id)
                            <div class="flex justify-between items-center">
                                <span
                                    class="font-medium dark:text-gray-300">{{ __('frontend-labels.payment_gateway.tenure_id') }}:</span>
                                <span class="font-bold dark:text-white">{{ $tenure_id }}</span>
                            </div>
                        @endif
                    </div>

                    <div class="space-y-4">
                        {{-- Stripe --}}
                        @if ($payment_setting['stripe'] ?? null)
                            <form action="{{ route('payment.stripe') }}" method="POST">
                                @csrf
                                <input type="hidden" name="amount" value="{{ request('amount') }}">
                                @if (request('plan'))
                                    <input type="hidden" name="plan" value="{{ request('plan') }}">
                                    <input type="hidden" name="tenure_id" value="{{ $tenure_id }}">
                                @endif
                                @if (request('smart_ad_id'))
                                    <input type="hidden" name="smart_ad_id" value="{{ request('smart_ad_id') }}">
                                @endif
                                @if (request('ad_details_id'))
                                    <input type="hidden" name="ad_details_id" value="{{ request('ad_details_id') }}">
                                @endif
                                <button type="submit" class="btn btn-outline-primary py-2 w-100 fw-bold"
                                    aria-label="Pay with Stripe">
                                    <i class="bi bi-credit-card-2-front"></i>
                                    <span>{{ __('frontend-labels.payment_gateway.pay_with_stripe') }}</span>
                                </button>
                            </form>
                        @endif

                        {{-- Razorpay --}}
                        @if ($payment_setting['razorpay'] ?? null)
                            <form action="{{ route('razorpay.process') }}" method="POST">
                                @csrf
                                <input type="hidden" name="amount" value="{{ request('amount') }}">
                                @if (request('plan'))
                                    <input type="hidden" name="plan" value="{{ request('plan') }}">
                                    <input type="hidden" name="tenure_id" value="{{ $tenure_id }}">
                                @endif
                                @if (request('smart_ad_id'))
                                    <input type="hidden" name="smart_ad_id" value="{{ request('smart_ad_id') }}">
                                @endif
                                @if (request('ad_details_id'))
                                    <input type="hidden" name="ad_details_id" value="{{ request('ad_details_id') }}">
                                @endif
                                <button type="submit" class="btn btn-outline-primary py-2 w-100 fw-bold"
                                    aria-label="Pay with Razorpay">
                                    <i class="bi bi-cash-coin"></i>
                                    <span>{{ __('frontend-labels.payment_gateway.pay_with_razorpay') }}</span>
                                </button>
                            </form>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
