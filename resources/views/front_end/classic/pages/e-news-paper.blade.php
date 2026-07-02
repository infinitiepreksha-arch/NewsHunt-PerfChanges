@extends('front_end.' . $theme . '.layout.main')
@section('body')
    @if ($subscriptionLimitReached)
        <!-- Bootstrap Subscription Limit Reached and Free Trial Modal -->
        <div id="subscriptionLimitFreeTrialModal" class="modal modal-blur fade p-5" tabindex="-1" role="dialog"
            aria-label="Daily free trial and subscription limit reached"
            aria-labelledby="subscriptionLimitFreeTrialModalLabel" aria-hidden="true"
            data-bs-keyboard="false">
            <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
                <div
                    class="modal-content bg-light text-dark dark:bg-gray-800 dark:text-white shadow-lg rounded-2 border-0 text-center">

                    <div class="modal-header border-0 justify-content-center p-4">

                    </div>
                    <div class="modal-body d-flex flex-column align-items-center justify-content-center">
                        <div class="display-4 mb-3">⏳</div>
                        <h3 class="modal-title fw-bold mb-3 " id="subscriptionLimitFreeTrialModalLabel">
                            {{ __('frontend-labels.limits.daily_trial_and_subscription_reached') }}</h3>
                        <p class="fs-5 mb-2">{{ __('frontend-labels.limits.unlock_access_message') }}</p>
                    </div>

                    <div class="modal-footer justify-content-center border-0 pt-0">
                        <a href="{{ url('membership') }}"
                            class="btn btn-primary btn-lg rounded-pill px-3 px-sm-4 fw-semibold shadow-sm mb-2 w-100 w-sm-auto text-center">
                            {{ __('frontend-labels.limits.buy_membership_plan') }}
                        </a>
                    </div>
                </div>
            </div>
        </div>
    @endif

    {{-- Always render dailyLimitModal if user is eligible, JS will handle the showing --}}
    @if ($isDailyLimitEligible || $dailyLimitReached || session('daily_limit_reached'))
        <!-- Modal for Daily Limit Reached -->
        <div id="dailyLimitModal" class="modal modal-blur fade p-5" tabindex="-1" role="dialog"
            aria-label="Daily Limit Reached" aria-labelledby="dailyLimitModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
                <div
                    class="modal-content bg-light text-dark dark:bg-gray-800 dark:text-white shadow-lg rounded-2 border-0 text-center">
                    <div class="modal-header border-0 justify-content-center p-4">
                    </div>
                    <div class="modal-body d-flex flex-column align-items-center justify-content-center">
                        <div class="display-4 mb-3">⏳</div>

                        <h3 class="modal-title fw-bold mb-3" id="dailyLimitModalLabel">
                            {{ __('frontend-labels.limits.daily_limit_reached') }}</h3>
                        <p class="fs-5 mb-2">{{ __('frontend-labels.limits.daily_limit_message') }}</p>
                        <p class="mb-0 text-muted">{{ __('frontend-labels.limits.unlock_access_message') }}</p>
                    </div>
                    <div class="modal-footer justify-content-center border-0 pt-0">
                        <a href="{{ url('membership') }}"
                            class="btn btn-primary btn-lg rounded-pill px-4 fw-semibold shadow-sm mb-2">
                            {{ __('frontend-labels.limits.buy_membership_plan') }}
                        </a>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <div id="wrapper" class="wrap overflow-hidden-x" 
        data-daily-limit="{{ $dailyLimitReached ? '1' : '0' }}"
        data-subscription-limit="{{ $subscriptionLimitReached ? '1' : '0' }}"
        data-has-subscription="{{ (auth()->user() && auth()->user()->subscription) ? '1' : '0' }}"
        data-daily-limit-value="{{ $freeTrialLimit }}" 
        data-is-daily-eligible="{{ $isDailyLimitEligible ? '1' : '0' }}"
        data-content-type="epaper">
        <div class="breadcrumbs panel z-1 py-2 bg-gray-25 dark:bg-gray-100 dark:bg-opacity-5 dark:text-white">
            <div class="container max-w-xl">
                <ul class="breadcrumb nav-x justify-center gap-1 fs-7 sm:fs-6 m-0">
                    <li><a href="{{ url('home') }}">{{ __('frontend-labels.home.title') }}</a></li>
                    <li><i class="unicon-chevron-right opacity-50"></i></li>
                    <li><a href="{{ route('e-newspaper.index') }}">{{ $title }}</a></li>
                </ul>
            </div>
        </div>
        <div class="section py-3 sm:py-6 lg:py-9" id="e-newspaper-page"
            data-newspapers-url="{{ route('e-newspaper.index') }}">
            <div class="container max-w-xl">
                <div class="panel vstack gap-1 sm:gap-6 lg:gap-9">
                    <header class="page-header panel vstack text-center">
                        <h1 class="h3 lg:h1 mb-4">{{ $title }}</h1>
                    </header>
                    <div class="vstack sm:hstack justify-between items-center gap-2 sm:gap-4">
                        <div class="panel text-center sm:text-start">
                            <span id="newspaper-count" class="fs-6 m-0 opacity-60">Showing {{ $e_newspapers->count() }}
                                e-newspapers.</span>
                        </div>
                        <div class="d-flex flex-wrap gap-2">
                            {{-- <div class="hstack gap-1 fs-6 filtering"> --}}
                            <div class="d-flex flex-column flex-sm-row gap-1 fs-6 filtering">
                                <span>Filter by:</span>
                                <select name="topic" id="topic_filter" data-epaper-url="{{ route('e-newspaper.index') }}"
                                    class="form-select form-control-xs fs-6 w-150px dark:bg-gray-900 dark:text-white dark:border-gray-700">
                                    <option value="" {{ request()->query('topic') == '' ? 'selected' : '' }}>All
                                        Topics</option>
                                    @foreach ($epapertopics as $topic)
                                        <option value="{{ $topic->slug }}"
                                            {{ request()->query('topic') == $topic->slug ? 'selected' : '' }}>
                                            {{ $topic->name }}
                                        </option>
                                    @endforeach
                                </select>
                                <select name="channel" id="channel_filter"
                                    data-epaper-url="{{ route('e-newspaper.index') }}"
                                    class="form-select form-control-xs fs-6 w-150px dark:bg-gray-900 dark:text-white dark:border-gray-700">
                                    <option value="" {{ request()->query('channel') == '' ? 'selected' : '' }}>All
                                        Channels</option>
                                    @foreach ($epaperChannels as $channel)
                                        <option value="{{ $channel->slug }}"
                                            {{ request()->query('channel') == $channel->slug ? 'selected' : '' }}>
                                            {{ $channel->name }}
                                        </option>
                                    @endforeach
                                </select>

                                <div class="position-relative">
                                    <input onclick="document.getElementById('date-picker').showPicker()" type="date"
                                        id="date-picker" name="date"
                                        class="form-control form-control-xs fs-6 w-150px dark:bg-gray-900 dark:text-white dark:border-gray-700"
                                        value="{{ request()->query('date') }}">
                                    <i onclick="document.getElementById('date-picker').showPicker()"></i>
                                </div>

                            </div>
                        </div>
                    </div>

                    <div class="panel text-center no-data-panel" id="no-data-panel" style="display: none;">
                        <img class="w-100 h-300px object-contain image uc-transition-opaque"
                            src="{{ asset('front_end/classic/images/place-holser/no-data.png') }}"
                            alt="No Magazines Found">
                        <h1 class="h5 m-0 my-2">No Newspapers Found</h1>
                    </div>


                    <div class="row child-cols-12 sm:child-cols-6 lg:child-cols-4 xl:child-cols-3 g-0 sm:g-2 xl:g-3 col-match uc-grid"
                        data-uc-grid="" id="newspapers-container">
                        @foreach ($e_newspapers as $e_newspaper)
                            <div class="newspaper-item" data-date="{{ $e_newspaper->date }}">
                                <article
                                    class="post type-post panel hstack sm:vstack items-start gap-2 sm:gap-0 p-2 sm:p-0 overflow-hidden text-gray-900 dark:text-white bg-white dark:bg-gray-900 rounded border">
                                    <div class="post-media panel overflow-hidden w-200px sm:w-100 order-1 sm:order-0">
                                        <figure
                                            class="featured-image m-0 ratio ratio-3x2 sm:ratio-16x9 uc-transition-toggle overflow-hidden bg-gray-25 dark:bg-gray-800">
                                            <img class="media-cover image uc-transition-scale-up uc-transition-opaque"
                                                src="{{ asset('storage/' . $e_newspaper->thumbnail) }}"
                                                data-src="{{ asset('storage/' . $e_newspaper->thumbnail) }}" alt="image"
                                                loading="lazy">
                                            <a href="{{ route('e-newspaper.pdf', $e_newspaper->id) }}" target="_blank"
                                                class="position-cover read-more-link"
                                                data-daily-limit="{{ $dailyLimitReached ? '1' : '0' }}"
                                                data-subscription-limit="{{ $subscriptionLimitReached ? '1' : '0' }}"
                                                data-caption="image"></a>
                                        </figure>
                                        @if (empty(request()->route('topic')))
                                            <div
                                                class="post-category hstack gap-narrow position-absolute top-0 start-0 m-1 fs-7 fw-bold h-15px px-1 rounded-1 shadow-xs bg-white text-primary">
                                                <a class="text-none"
                                                    href="{{ url('topics/' . $e_newspaper->topic->slug) }}"
                                                    title="{{ $e_newspaper->topic->name }}">{{ $e_newspaper->topic->name }}</a>
                                            </div>
                                        @endif
                                    </div>
                                    <div
                                        class="post-header panel vstack justify-between gap-1 sm:gap-2 p-0 sm:p-2 mt-narrow sm:mt-0 w-100">
                                        <div class="post-top panel vstack items-start gap-2">
                                            <div class="meta">
                                                <div class="d-flex justify-between gap-2">
                                                    <div>
                                                        <div class="d-flex gap-1">
                                                            <a href="{{ url('channels/' . $e_newspaper->channel->slug) }}"
                                                                title="{{ $e_newspaper->channel_name }}">
                                                                <img src="{{ url('storage/images/' . $e_newspaper->channel->logo) }}"
                                                                    alt="Channel Logo" class="h-20px">
                                                            </a>
                                                            <a href="{{ url('channels/' . $e_newspaper->channel->slug) }}"
                                                                class="text-black dark:text-white text-none fw-bold"
                                                                title="{{ $e_newspaper->channel->name }}">{{ $e_newspaper->channel->name }}</a>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <h3 class="post-title h6 sm:h5 m-0 text-truncate-2">
                                                <a class="text-none read-more-link"
                                                    href="{{ route('e-newspaper.pdf', $e_newspaper->id) }}"
                                                    target="_blank"
                                                    data-daily-limit="{{ $dailyLimitReached ? '1' : '0' }}"
                                                    data-subscription-limit="{{ $subscriptionLimitReached ? '1' : '0' }}">
                                                </a>
                                            </h3>
                                        </div>
                                        <div
                                            class="post-bottom panel hstack gap-2 fs-7 mt-narrow sm:mt-0 text-black dark:text-white text-opacity-60">
                                            <div>
                                                <div class="post-date hstack gap-narrow">
                                                    <i class="icon-narrow unicon-time"></i>
                                                    <span>{{ $e_newspaper->date }}</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </article>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Pagination (only shown in grid view) -->
    @if ($e_newspapers->hasPages())
        <div class="nav-pagination mb-5">
            <ul class="nav-x uc-pagination hstack gap-1 justify-center ft-secondary" data-uc-margin="">
                {{ $e_newspapers->links('vendor.custom-pagination') }}
            </ul>
        </div>
    @endif
@endsection
