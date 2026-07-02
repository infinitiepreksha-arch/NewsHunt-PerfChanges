@extends('front_end.' . $theme . '.layout.main')
@section('body')
    <div class="share-div"></div>
    <div id="wrapper" class="wrap overflow-hidden-x">
        <!-- Breadcrumb -->
        <div class="breadcrumbs panel z-1 py-2 bg-gray-25 dark:bg-gray-100 dark:bg-opacity-5 dark:text-white">
            <div class="container max-w-xl">
                <ul class="breadcrumb nav-x justify-center gap-1 fs-7 sm:fs-6 m-0">
                    <li><a href="{{ url('home') }}" data-uc-tooltip="Home"> {{ __('frontend-labels.home.title') }}</a></li>
                    <li><i class="unicon-chevron-right opacity-50"></i></li>
                    <li><span class="opacity-70">{{ $title }}</span></li>
                </ul>

                <div class="section py-3 sm:py-6 lg:py-9">
                    <div class="container max-w-xl">
                        <div class="panel vstack gap-1 sm:gap-6 lg:gap-9">
                            <header class="page-header panel vstack text-center">
                                <h1 class="headingtag h3 lg:h1">{{ $title }}</h1>
                            </header>

                            <!-- Custom Ads Introduction Section -->
                            <div
                                class="custom-ads-intro panel bg-white dark:bg-gray-800 p-4 sm:p-6 lg:p-8 rounded-lg shadow-sm mb-6 mt-4">
                                <div class="text-center vstack gap-4">
                                    @if (!$hasCreatedAd)
                                        <div>
                                            <div
                                                class="icon-wrapper mx-auto w-16 h-16 bg-primary bg-opacity-10 rounded-full flex items-center justify-center mb-4">
                                                <i class="unicon-megaphone text-primary text-2xl"></i>
                                            </div>
                                            <h2 class="h3 sm:h2 text-gray-900 dark:text-white mb-3">
                                                {{ __('frontend-labels.sponsor_ads.launch_campaigns') }}
                                            </h2>
                                            <p
                                                class="fs-5 text-gray-600 dark:text-gray-300 max-w-2xl mx-auto leading-relaxed mb-4">
                                                {{ __('frontend-labels.sponsor_ads.launch_campaigns_description') }}
                                            </p>
                                        </div>
                                        @if (auth()->check())
                                            <div class="panel text-center">
                                                <a href="{{ route('smart-ads.index') }}"
                                                    class="animate-btn btn btn-md btn-primary text-none gap-0">
                                                    <span>{{ __('frontend-labels.sponsor_ads.manage_my_ads') }}</span>
                                                    <i class="icon icon-narrow unicon-arrow-right fw-bold"></i>
                                                </a>
                                            </div>
                                        @else
                                            <!-- User is NOT logged in -->
                                            <div class="panel text-center">
                                                <a href="#uc-account-modal" data-uc-toggle
                                                    class="animate-btn btn btn-md btn-primary text-none gap-0">
                                                    <span>{{ __('frontend-labels.sponsor_ads.manage_my_ads') }}</span>
                                                    <i class="icon icon-narrow unicon-arrow-right fw-bold"></i>
                                                </a>
                                            </div>
                                        @endif
                                    @else
                                        @if ($smartAdsDetail = $smartAdsDetails->first())
                                            @if ($smartAdsDetail->ad_publish_status === 'approved' && $smartAdsDetail->payment_status === 'pending')
                                                <div class="flex flex-col items-center justify-center">
                                                    <dotlottie-player
                                                        src="{{ asset('front_end/classic/images/place-holser/ad_approval.json') }}"
                                                        background="transparent" speed="1" loop autoplay
                                                        style="width: 100%; max-width: 500px; height: auto; aspect-ratio: 5/4; display: block; margin: 0 auto;">
                                                    </dotlottie-player>
                                                    <h1 class="h6 sm:h6 lg:h6">
                                                        {{ __('frontend-labels.sponsor_ads.request_approved') }}
                                                    </h1>
                                                </div>
                                                {{-- Action Buttons --}}
                                                <div class="panel">
                                                    <div class="row g-2">
                                                        {{-- Manage Ads Button --}}
                                                        <div class="col-md-6">
                                                            @if (auth()->check())
                                                                <a href="{{ route('smart-ads.index') }}"
                                                                    class="animate-btn btn btn-md btn-primary text-none gap-0 w-100">
                                                                    <span>{{ __('frontend-labels.sponsor_ads.manage_my_ads') }}</span>
                                                                    <i
                                                                        class="icon icon-narrow unicon-arrow-right fw-bold"></i>
                                                                </a>
                                                            @else
                                                                <a href="#uc-account-modal" data-uc-toggle
                                                                    class="animate-btn btn btn-md btn-primary text-none gap-0 w-100">
                                                                    <span>{{ __('frontend-labels.sponsor_ads.manage_my_ads') }}</span>
                                                                    <i
                                                                        class="icon icon-narrow unicon-arrow-right fw-bold"></i>
                                                                </a>
                                                            @endif
                                                        </div>

                                                        {{-- Payment Button --}}
                                                        @if (
                                                            $smartAdsDetail->ad_publish_status === 'approved' &&
                                                                $smartAdsDetail->payment_status === 'pending' &&
                                                                $smartAdsDetail->total_price > 0)
                                                            <div class="col-md-6">
                                                                <form action="{{ route('payment.form') }}" method="GET"
                                                                    id="payment-form-{{ $smartAdsDetail->id }}">
                                                                    <input type="hidden" name="smart_ad_id"
                                                                        value="{{ $smartAdsDetail->smart_ad_id }}">
                                                                    <input type="hidden" name="ad_details_id"
                                                                        value="{{ $smartAdsDetail->id }}">
                                                                    <input type="hidden" name="amount"
                                                                        value="{{ $smartAdsDetail->total_price }}">

                                                                    <button type="submit"
                                                                        class="animate-btn btn btn-md btn-success text-none gap-0 w-100"
                                                                        id="payment-btn-{{ $smartAdsDetail->id }}">
                                                                        <span>{{ __('frontend-labels.sponsor_ads.make_payment') }}</span>
                                                                        <i
                                                                            class="icon icon-narrow unicon-credit-card fw-bold"></i>
                                                                    </button>
                                                                </form>
                                                            </div>
                                                        @endif
                                                    </div>

                                                    {{-- Timer Message --}}
                                                    @if (
                                                        $smartAdsDetail->ad_publish_status === 'approved' &&
                                                            $smartAdsDetail->payment_status === 'pending' &&
                                                            $smartAdsDetail->total_price > 0)
                                                        <div class="row mt-2">
                                                            <div class="col-12 text-center">
                                                                @if ($smartAdsDetail->remaining_time > 0)
                                                                    <p class="text-danger mb-0">
                                                                        {{ __('frontend-labels.sponsor_ads.payment_expires_in') }}
                                                                        <span id="timer-{{ $smartAdsDetail->id }}"
                                                                            data-seconds="{{ $smartAdsDetail->remaining_time }}"
                                                                            data-btn="payment-btn-{{ $smartAdsDetail->id }}">
                                                                        </span>
                                                                    </p>
                                                                @else
                                                                    <p class="text-danger mb-0">
                                                                        {{ __('frontend-labels.sponsor_ads.payment_deadline_expired') }}
                                                                    </p>
                                                                    <script>
                                                                        document.addEventListener("DOMContentLoaded", function() {
                                                                            let expiredBtn = document.getElementById("payment-btn-{{ $smartAdsDetail->id }}");
                                                                            if (expiredBtn) expiredBtn.disabled = true;
                                                                        });
                                                                    </script>
                                                                @endif
                                                            </div>
                                                        </div>
                                                    @endif
                                                </div>
                                            @elseif ($smartAdsDetail->ad_publish_status === 'pending' && $smartAdsDetail->payment_status === 'pending')
                                                <div class="flex flex-col items-center justify-center">
                                                    <dotlottie-player
                                                        src="{{ asset('front_end/classic/images/place-holser/emailsent.json') }}"
                                                        background="transparent" speed="1" loop autoplay
                                                        style="width: 100%; max-width: 500px; height: auto; aspect-ratio: 5/4; display: block; margin: 0 auto;">
                                                    </dotlottie-player>
                                                    <h1 class="h6 sm:h6 lg:h6">
                                                        {{ __('frontend-labels.sponsor_ads.request_submitted_on') }}
                                                        {{ $createdAtFormatted }}.
                                                        {{ __('frontend-labels.sponsor_ads.status_update_message') }}
                                                    </h1>

                                                </div>
                                                @if (auth()->check())
                                                    <div class="panel text-center">
                                                        <a href="{{ route('smart-ads.index') }}"
                                                            class="animate-btn btn btn-md btn-primary text-none gap-0">
                                                            <span>{{ __('frontend-labels.sponsor_ads.manage_my_ads') }}</span>
                                                            <i class="icon icon-narrow unicon-arrow-right fw-bold"></i>
                                                        </a>
                                                    </div>
                                                @else
                                                    <!-- User is NOT logged in -->
                                                    <div class="panel text-center">
                                                        <a href="#uc-account-modal" data-uc-toggle
                                                            class="animate-btn btn btn-md btn-primary text-none gap-0">
                                                            <span>{{ __('frontend-labels.sponsor_ads.manage_my_ads') }}</span>
                                                            <i class="icon icon-narrow unicon-arrow-right fw-bold"></i>
                                                        </a>
                                                    </div>
                                                @endif
                                            @elseif ($smartAdsDetail->ad_publish_status === 'rejected' && $smartAdsDetail->payment_status === 'pending')
                                                <div>
                                                    <div
                                                        class="icon-wrapper mx-auto w-16 h-16 bg-primary bg-opacity-10 rounded-full flex items-center justify-center mb-4">
                                                        <i class="unicon-megaphone text-primary text-2xl"></i>
                                                    </div>
                                                    <h2 class="h3 sm:h2 text-gray-900 dark:text-white mb-3">
                                                        {{ __('frontend-labels.sponsor_ads.launch_campaigns') }}
                                                    </h2>
                                                    <p
                                                        class="fs-5 text-gray-600 dark:text-gray-300 max-w-2xl mx-auto leading-relaxed mb-4">
                                                        {{ __('frontend-labels.sponsor_ads.launch_campaigns_description') }}
                                                    </p>
                                                </div>
                                                @if (auth()->check())
                                                    <div class="panel text-center">
                                                        <a href="{{ route('smart-ads.index') }}"
                                                            class="animate-btn btn btn-md btn-primary text-none gap-0">
                                                            <span>{{ __('frontend-labels.sponsor_ads.manage_my_ads') }}</span>
                                                            <i class="icon icon-narrow unicon-arrow-right fw-bold"></i>
                                                        </a>
                                                    </div>
                                                @else
                                                    <!-- User is NOT logged in -->
                                                    <div class="panel text-center">
                                                        <a href="#uc-account-modal" data-uc-toggle
                                                            class="animate-btn btn btn-md btn-primary text-none gap-0">
                                                            <span>{{ __('frontend-labels.sponsor_ads.manage_my_ads') }}</span>
                                                            <i class="icon icon-narrow unicon-arrow-right fw-bold"></i>
                                                        </a>
                                                    </div>
                                                @endif
                                            @elseif (
                                                $smartAdsDetail->ad_publish_status === 'approved' &&
                                                    $smartAdsDetail->payment_status === 'success' &&
                                                    \Carbon\Carbon::parse($smartAdsDetail->end_date)->isFuture())
                                                <div class="flex flex-col items-center justify-center">
                                                    <dotlottie-player
                                                        src="{{ asset('front_end/classic/images/place-holser/payments_ads.json') }}"
                                                        background="transparent" speed="1" loop autoplay
                                                        style="width: 100%; max-width: 500px; height: auto; aspect-ratio: 5/4; display: block; margin: 0 auto;">
                                                    </dotlottie-player>
                                                    <h1 class="h6 sm:h6 lg:h6">
                                                        {{ __('frontend-labels.sponsor_ads.payment_success') }} <br>
                                                        {{ __('frontend-labels.sponsor_ads.ad_live_from') }}
                                                        {{ \Carbon\Carbon::parse($smartAdsDetail->start_date)->format('d M Y') }}
                                                        {{ __('frontend-labels.sponsor_ads.ad_live_to') }}to
                                                        {{ \Carbon\Carbon::parse($smartAdsDetail->end_date)->format('d M Y') }}.
                                                        {{ __('frontend-labels.sponsor_ads.confirmation_email') }}
                                                    </h1>

                                                </div>
                                                @if (auth()->check())
                                                    <div class="panel text-center">
                                                        <a href="{{ route('smart-ads.index') }}"
                                                            class="animate-btn btn btn-md btn-primary text-none gap-0">
                                                            <span>{{ __('frontend-labels.sponsor_ads.manage_my_ads') }}</span>
                                                            <i class="icon icon-narrow unicon-arrow-right fw-bold"></i>
                                                        </a>
                                                    </div>
                                                @endif
                                            @elseif (
                                                $smartAdsDetail->ad_publish_status === 'approved' &&
                                                    $smartAdsDetail->payment_status === 'success' &&
                                                    !\Carbon\Carbon::parse($smartAdsDetail->end_date)->isFuture())
                                                <div>
                                                    <div
                                                        class="icon-wrapper mx-auto w-16 h-16 bg-primary bg-opacity-10 rounded-full flex items-center justify-center mb-4">
                                                        <i class="unicon-megaphone text-primary text-2xl"></i>
                                                    </div>
                                                    <h2 class="h3 sm:h2 text-gray-900 dark:text-white mb-3">
                                                        {{ __('frontend-labels.sponsor_ads.launch_campaigns') }}
                                                    </h2>
                                                    <p
                                                        class="fs-5 text-gray-600 dark:text-gray-300 max-w-2xl mx-auto leading-relaxed mb-4">
                                                        {{ __('frontend-labels.sponsor_ads.launch_campaigns_description') }}
                                                    </p>
                                                </div>
                                                @if (auth()->check())
                                                    <div class="panel text-center">
                                                        <a href="{{ route('smart-ads.index') }}"
                                                            class="animate-btn btn btn-md btn-primary text-none gap-0">
                                                            <span>{{ __('frontend-labels.sponsor_ads.manage_my_ads') }}</span>
                                                            <i class="icon icon-narrow unicon-arrow-right fw-bold"></i>
                                                        </a>
                                                    </div>
                                                @else
                                                    <!-- User is NOT logged in -->
                                                    <div class="panel text-center">
                                                        <a href="#uc-account-modal" data-uc-toggle
                                                            class="animate-btn btn btn-md btn-primary text-none gap-0">
                                                            <span>{{ __('frontend-labels.sponsor_ads.manage_my_ads') }}</span>
                                                            <i class="icon icon-narrow unicon-arrow-right fw-bold"></i>
                                                        </a>
                                                    </div>
                                                @endif
                                            @endif
                                        @endif
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
