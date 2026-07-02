@extends('front_end.' . $theme . '.layout.main')
@section('body')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <div id="wrapper" class="wrap overflow-hidden-x">
        <div class="breadcrumbs panel z-1 py-2 bg-gray-25 dark:bg-gray-100 dark:bg-opacity-5 dark:text-white">
            <div class="container max-w-xl">
                <ul class="breadcrumb nav-x justify-center gap-1 fs-7 sm:fs-6 m-0">
                    <li><a href="{{ url('home') }}">{{ __('frontend-labels.home.title') }}</a></li>
                    <li><i class="unicon-chevron-right opacity-50"></i></li>
                    <li><span class="opacity-70">{{ $title }}</span></li>
                </ul>
            </div>
        </div>
    </div>
    @if ($free_trial_status == '0')
        @if (!$membership_data->isEmpty())
            <div class="section py-3 sm:py-6 lg:py-9">
                <div class="container max-w-xl">
                    <div class="panel vstack gap-1 sm:gap-6 lg:gap-9">
                        <header class="page-header panel vstack text-center">
                            <h1 class="headingtag h3 lg:h1">{{ $title }}</h1>
                        </header>
                    </div>
                </div>
            </div>
            <div class="container py-5">
                <div class="row justify-content-center g-4">
                    @foreach ($membership_data as $plan)
                        <div class="col-lg-4 col-md-6">
                            <div
                                class="card h-100 shadow-sm border-0 dark:border dark:border-gray-600 dark:bg-gray-800 text-black dark:text-white p-4">
                                <div class="card-body d-flex flex-column">
                                    <div class="mb-4 text-center">
                                        <h2 class="fw-bold mb-2 f-1">{{ $plan->name }}</h2>

                                        @if ($plan->planTenures->isNotEmpty())
                                            @php
                                                $lowestTenure = $plan->planTenures->sortBy('price')->first();
                                            @endphp
                                            <div class="mb-1 text-center">
                                                <span class="fw-bold" style="font-size: 45px">
                                                    <span
                                                        class="fs-10">{{ $currency }}</span>{{ number_format($lowestTenure->price) }}
                                                </span>
                                                <span class="fs-6 text-muted">
                                                    /{{ $lowestTenure->duration }}
                                                    {{ __('frontend-labels.membership.month') }}
                                                    {{ $lowestTenure->duration > 1 ? 's' : '' }}
                                                </span>
                                            </div>
                                        @endif
                                    </div>

                                    <ul class="list-unstyled mb-1">
                                        @if (isset($plan->features_plan))
                                            @if ($plan->features_plan->is_ads_free)
                                                <li class="mb-2">
                                                    <i
                                                        class="bi bi-check-circle-fill text-primary me-2"></i>{{ __('frontend-labels.membership.ads-free') }}

                                                </li>
                                            @endif

                                            @if ($plan->features_plan->number_of_articles > 0)
                                                <li class="mb-2">
                                                    <i
                                                        class="bi bi-check-circle-fill text-primary me-2"></i>{{ $plan->features_plan->number_of_articles }}
                                                    {{ __('frontend-labels.membership.articles') }}
                                                </li>
                                            @endif

                                            @if ($plan->features_plan->number_of_stories > 0)
                                                <li class="mb-2">
                                                    <i
                                                        class="bi bi-check-circle-fill text-primary me-2"></i>{{ $plan->features_plan->number_of_stories }}
                                                    {{ __('frontend-labels.membership.stories') }}
                                                </li>
                                            @endif

                                            @if ($plan->features_plan->number_of_e_papers_and_magazines > 0)
                                                <li class="mb-2">
                                                    <i
                                                        class="bi bi-check-circle-fill text-primary me-2"></i>{{ $plan->features_plan->number_of_e_papers_and_magazines }}
                                                    {{ __('frontend-labels.membership.e-paper-magazines') }}
                                                </li>
                                            @endif
                                        @endif
                                    </ul>

                                    @if ($plan->planTenures->count() > 1)
                                        <div class="mb-4">
                                            <select
                                                class="form-select tenure-selector border py-2 px-3 bg-light dark:text-white dark:bg-gray-800 text-black "
                                                data-plan-id="{{ $plan->id }}" data-plan-name="{{ $plan->name }}">
                                                @foreach ($plan->planTenures as $tenure)
                                                    <option value="{{ $tenure->id }}" data-price="{{ $tenure->price }}"
                                                        data-duration="{{ $tenure->duration }}" class="dark:text-white">
                                                        {{ $tenure->name ?? $tenure->duration . ' ' . __('month') . ($tenure->duration > 1 ? 's' : '') }}
                                                        - {{ $currency }}{{ number_format($tenure->price, 2) }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                    @elseif ($plan->planTenures->isNotEmpty())
                                        @php $tenure = $plan->planTenures->first(); @endphp
                                        <div class="mb-4">
                                            <div class="form-control tenure-selector border py-2 px-3 bg-light dark:text-white dark:bg-gray-800 text-black"
                                                data-plan-id="{{ $plan->id }}" data-plan-name="{{ $plan->name }}"
                                                data-tenure-id="{{ $tenure->id }}" data-price="{{ $tenure->price }}"
                                                data-duration="{{ $tenure->duration }}">
                                                {{ $tenure->name ?? $tenure->duration . ' ' . __('month') . ($tenure->duration > 1 ? 's' : '') }}
                                                - {{ $currency }}{{ number_format($tenure->price, 2) }}
                                            </div>
                                        </div>
                                    @endif

                                    @if (!$user || !$user->subscription || $user->subscription->status !== 'active')
                                        <div class="mt-auto">
                                            @if (auth()->check())
                                                <!-- User is logged in -->
                                                <form action="{{ route('payment.form') }}" method="GET"
                                                    class="plan-form">
                                                    <input type="hidden" name="plan" value="{{ $plan->name }}">
                                                    <input type="hidden" name="tenure_id" class="tenure-id-input"
                                                        value="{{ $plan->planTenures->isNotEmpty() ? $plan->planTenures->sortBy('price')->first()->id : '' }}">
                                                    <input type="hidden" name="amount" class="amount-input"
                                                        value="{{ $plan->planTenures->isNotEmpty() ? $plan->planTenures->sortBy('price')->first()->price : 0 }}">
                                                    <button type="submit"
                                                        class="btn btn-primary fw-bold w-100 rounded-pill py-1">{{ __('frontend-labels.membership.buy-now') }}</button>
                                                </form>
                                            @else
                                                <!-- User is NOT logged in -->
                                                <a href="#uc-account-modal" data-uc-toggle
                                                    class="btn btn-primary fw-bold w-100 rounded-pill py-1 text-none">
                                                    {{ __('frontend-labels.membership.buy-now') }}
                                                </a>
                                            @endif
                                        </div>
                                    @endif

                                </div>
                            </div>
                        </div>
                    @endforeach

                </div>
            </div>
        @else
            <div class="panel text-center">
                <img class="w-100 h-500px object-contain image uc-transition-opaque"
                    src="{{ asset('front_end/classic/images/place-holser/no-data.png') }}" alt="No Transactions Found">
            </div>
        @endif
    @endif
@endsection
