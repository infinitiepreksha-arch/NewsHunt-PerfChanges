@extends('front_end.' . getTheme() . '.layout.main')

@section('body')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <!-- Page Header -->
    <div id="wrapper" class="wrap overflow-hidden-x">
        <div class="breadcrumbs panel z-1 py-2 bg-gray-25 dark:bg-gray-100 dark:bg-opacity-5 ">
            <div class="container max-w-xl">
                <ul class="breadcrumb nav-x justify-center gap-1 fs-7 sm:fs-6 m-0">
                    <li><a href="{{ url('home') }}">{{ __('frontend-labels.home.title') }}</a></li>
                    <li><i class="unicon-chevron-right opacity-50"></i></li>
                    <li><a href="{{ url('membership') }}">{{ __('frontend-labels.membership.title') }}</a></li>
                    <li><i class="unicon-chevron-right opacity-50"></i></li>
                    <li><span class="opacity-70">{{$title}}</span></li>
                </ul>

                <!-- Page Header -->
                <div class="section py-3 sm:py-6 lg:py-9">
                    <div class="container max-w-xl">
                        <div class="panel vstack gap-1 sm:gap-6 lg:gap-9">
                            <header class="page-header panel vstack text-center">
                                <h1 class="headingtag h3 lg:h1">{{$title}}</h1>
                            </header>
                        </div>
                    </div>
                </div>

                <div class="section py-3">
                    <div class="container max-w-md mx-auto">
                        <div class="panel text-black dark:text-white dark:bg-gray-800 border rounded-lg overflow-hidden">
                            @if ($smartAdsPayments)
                                <div class="p-6">
                                    <div class="flex flex-col items-center justify-center">
                                        <dotlottie-player
                                            src="{{ asset('front_end/classic/images/place-holser/payments_ads.json') }}"
                                            background="transparent" speed="1" loop autoplay
                                            style="width: 100%; max-width: 300px; height: auto; aspect-ratio: 5/4; display: block; margin: 0 auto;">
                                        </dotlottie-player>
                                    </div>
                                    <!-- Success Message -->
                                    <div class="text-center mb-6">
                                        <h2 class="fw-bold">{{ __('frontend-labels.payment_success.thank_you') }}</h2>
                                        <p class="fw-bold">{{ __('frontend-labels.payment_success.success_message') }}</p>
                                    </div>

                                    <ul class="list-unstyled dark:bg-gray-white dark:text-white text-black">
                                        <ul class="list-unstyled">
                                            <li
                                                class="mb-2 d-flex justify-content-between align-items-center transition-all hover-scale hstack justify-between">
                                                <strong>{{ __('frontend-labels.payment_success.smart_ad_id') }}</strong>
                                                <span
                                                    class="badge bg-light text-primary shadow-sm  fs-15">{{ $smartAdsPayments->smart_ad_id ?? 'N/A' }}</span>
                                            </li>
                                            <li>
                                                <div class="d-flex justify-content-between mb-2 justify-between">
                                                    <strong>{{ __('frontend-labels.payment_success.amount') }}</strong>
                                                    <span
                                                        class=" fw-bold">{{ $smartAdsPayments->currency ?? 'N/A' }}</span>
                                                </div>
                                            </li>
                                            <li>
                                                <div class="d-flex justify-content-between mb-2 justify-between">

                                                    <strong>{{ __('frontend-labels.payment_success.payment_method') }}</strong>
                                                    <span
                                                        class="badge bg-white text-info  fs-15">{{ $smartAdsPayments->payment_gateway ?? 'N/A' }}</span>
                                                </div>
                                            </li>
                                            <li>
                                                <div
                                                    class="d-flex
                                                        justify-content-between mb-2 justify-between">

                                                    <strong>{{ __('frontend-labels.payment_success.transaction_id') }}</strong>
                                                    <span
                                                        class=" fw-bold ">{{ $smartAdsPayments->transaction_id ?? 'N/A' }}</span>
                                                </div>
                                            </li>
                                            <li>
                                                <div class="d-flex justify-content-between mb-2 justify-between">

                                                    <strong>{{ __('frontend-labels.payment_success.date') }}</strong>
                                                    <span
                                                        class=" fw-bold ">{{ $smartAdsPayments->paid_at->format('M d, Y H:i') }}</span>
                                                </div>
                                            </li>
                                        </ul>
                                    </ul>
                                </div>
                            @elseif ($membershipPayments)
                                <div class="p-6">
                                    <div class="flex flex-col items-center justify-center">
                                        <dotlottie-player
                                            src="{{ asset('front_end/classic/images/place-holser/payments_ads.json') }}"
                                            background="transparent" speed="1" loop autoplay
                                            style="width: 100%; max-width: 300px; height: auto; aspect-ratio: 5/4; display: block; margin: 0 auto;">
                                        </dotlottie-player>
                                    </div>
                                    <!-- Success Message -->
                                    <div class="text-center mb-6">

                                        <h2 class="fw-bold">{{ __('frontend-labels.payment_success.thank_you') }}</h2>
                                        <p class="fw-bold">{{ __('frontend-labels.payment_success.success_message') }}</p>
                                    </div>

                                    <ul class="list-unstyled dark:bg-gray-white dark:text-white text-black">
                                        <ul class="list-unstyled">

                                            <li>
                                                <div class="d-flex justify-content-between mb-2 justify-between">
                                                    <strong>{{ __('frontend-labels.payment_success.amount') }}</strong>
                                                    <span
                                                        class=" fw-bold">{{ $membershipPayments->amount ?? 'N/A' }}</span>
                                                </div>
                                            </li>
                                            <li>
                                                <div class="d-flex justify-content-between mb-2 justify-between">

                                                    <strong>{{ __('frontend-labels.payment_success.payment_method') }}</strong>
                                                    <span
                                                        class="badge bg-white text-info  fs-15">{{ $membershipPayments->payment_gateway ?? 'N/A' }}</span>
                                                </div>
                                            </li>
                                            <li>
                                                <div
                                                    class="d-flex
                                                        justify-content-between mb-2 justify-between">

                                                    <strong>{{ __('frontend-labels.payment_success.transaction_id') }}</strong>
                                                    <span
                                                        class=" fw-bold ">{{ $membershipPayments->transaction_id ?? 'N/A' }}</span>
                                                </div>
                                            </li>
                                            <li>
                                                <div class="d-flex justify-content-between mb-2 justify-between">

                                                    <strong>{{ __('frontend-labels.payment_success.status') }}</strong>
                                                    <span class=" fw-bold ">{{ $membershipPayments->status }}</span>
                                                </div>
                                            </li>
                                            <li>
                                                <div class="d-flex justify-content-between mb-2 justify-between">

                                                    <strong>{{ __('frontend-labels.payment_success.date') }}</strong>
                                                    <span
                                                        class=" fw-bold ">{{ $membershipPayments->created_at->format('M d, Y H:i') }}</span>
                                                </div>
                                            </li>
                                        </ul>
                                    </ul>
                                </div>
                            @else
                                <div class="text-center space-y-6">

                                    <div class="space-y-3 mt-2">
                                        <a href="{{ url('home') }}" class="btn btn-primary w-full block mb-4">
                                            <i class="bi bi-house-door"></i>
                                            <span>{{ __('frontend-labels.payment_cancel.return_home') }}</span>
                                        </a>

                                        <a href="{{ url('my-account/subscription') }}"
                                            class="btn btn-outline-primary w-full block">
                                            <i class="bi bi-person-badge"></i>
                                            <span>{{ __('frontend-labels.payment_success.view_subscription') }}</span>
                                        </a>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('script')
    <script src="https://unpkg.com/@dotlottie/player-component@latest/dist/dotlottie-player.js"></script>
@endsection
