@extends('front_end.' . $theme . '.layout.main')
<title>{{ $topic->name . ' | News Hunt' }}</title>

@section('body')
    <!-- Modals for Limits -->
    @if ($subscriptionLimitReached)
        <div id="subscriptionLimitFreeTrialModal" class="modal modal-blur fade p-5" tabindex="-1" role="dialog"
            aria-label="Daily free trial and subscription limit reached"
            aria-labelledby="subscriptionLimitFreeTrialModalLabel" aria-hidden="true" data-bs-keyboard="false">
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

    @if ($isDailyLimitEligible || $dailyLimitReached)
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
                            {{ __('frontend-labels.webstory.daily_limit_reached') }}</h3>
                        <p class="fs-5 mb-2">{{ __('frontend-labels.webstory.daily_limit_message') }}</p>
                        <p class="mb-0 text-muted">{{ __('frontend-labels.webstory.unlock_unlimited_access') }}</p>
                    </div>
                    <div class="modal-footer justify-content-center border-0 pt-0">
                        <a href="{{ url('membership') }}"
                            class="btn btn-primary btn-lg rounded-pill px-4 fw-semibold shadow-sm mb-2">
                            {{ __('frontend-labels.webstory.buy_membership_plan') }}
                        </a>
                    </div>
                </div>
            </div>
        </div>
    @endif
    <div id="wrapper" class="wrap overflow-hidden-x"
         data-daily-limit-value="{{ $freeTrialLimit }}" 
         data-is-daily-eligible="{{ $isDailyLimitEligible ? '1' : '0' }}"
         data-subscription-limit="{{ $subscriptionLimitReached ? '1' : '0' }}"
         data-has-subscription="{{ (auth()->user() && auth()->user()->subscription) ? '1' : '0' }}"
         data-content-type="story">
        <div class="breadcrumbs panel z-1 py-2 bg-gray-25 dark:bg-gray-100 dark:bg-opacity-5 dark:text-white">
            <div class="container max-w-xl">
                <ul class="breadcrumb nav-x justify-center gap-1 fs-7 sm:fs-6 m-0">
                    <li><a href="{{ url('home') }}">{{ __('frontend-labels.home.title') }}</a></li>
                    <li><i class="unicon-chevron-right opacity-50"></i></li>
                    <li><a href="{{ route('webstories.index') }}"> {{ __('frontend-labels.web_stories.title') }}</a></li>
                    <li><i class="unicon-chevron-right opacity-50"></i></li>
                    <li><span class="opacity-70">{{ $topic->name }}</span></li>
                </ul>
            </div>
        </div>
        <div class="section py-3 sm:py-6 lg:py-9">
            <div class="container max-w-xl">
                <div class="panel vstack gap-1 sm:gap-6 lg:gap-9">
                    <header class="page-header panel vstack text-center">
                        <h1 class="h3 lg:h1 mb-4">{{ $topic->name }} {{ __('frontend-labels.web_stories.title') }}</h1>
                    </header>
                    <!-- Responsive Stories Display -->
                    <div class="hidden lg:block"> <!-- Grid view for larger screens (>=1288px) -->
                        <div class="row child-cols-6 lg:child-cols-3 col-match gy-4 lg:gy-8 gx-2 lg:gx-4">
                            @foreach ($stories as $story)
                                <div>
                                    <div class="card bg-white dark:bg-gray-800 d-flex flex-column">
                                        <a href="{{ url('webstories/' . $topic->slug . '/' . $story->slug) }}"
                                            target="_blank" class="position-relative d-block story-link">
                                            <img src="{{ asset('storage/' . $story->story_slides->first()->image) }}"
                                                class="card-img-top" alt="{{ $story->title }}">

                                            <div
                                                class="story-progress-container position-absolute bottom-0 start-0 w-100 px-1 pb-2">
                                                <div class="progress-segments d-flex gap-1">
                                                    @foreach ($story->story_slides as $index => $slide)
                                                        <div class="progress-segment flex-grow-1 h-1 bg-white bg-opacity-50 story-dashed-css"
                                                            "></div>
                                                    @endforeach
                                                </div>
                                            </div>
                                            <span
                                                class="visual-stories-icon position-absolute top-2 end-1 p-1 rounded-circle dark:text-white text-white bg-gray-800">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                                    viewBox="0 0 24 24">
                                                    <path fill="currentColor"
                                                        d="M7 20V4h10v16zm-4-2V6h2v12zm16 0V6h2v12z" />
                                                </svg>
                                            </span>
                                        </a>
                                        <div id="card_title"
                                            class="card-footer text-gray-900 dark:text-white d-flex flex-column h-100">
                                            <h3 class="post-title h6 m-0 text-truncate-2 hover:text-primary">
                                                <a class="text-none duration-150 story-link"
                                                    href="{{ url('webstories/' . $topic->slug . '/' . $story->slug) }}"
                                                    title="{{ $story->title ?? '' }}">{{ $story->title ?? '' }}</a>
                                            </h3>
                                            <div class=" mt-2 text-muted fs-7">
                                                {{ $story->created_at->diffForHumans() }}

                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                    <!-- Pagination (only shown in grid view) -->
                    @if ($stories->hasPages())
                        <div class="nav-pagination pt-3 mt-6 lg:mt-9">
                            <ul class="nav-x uc-pagination hstack gap-1 justify-center ft-secondary" data-uc-margin="">
                                {{ $stories->links('vendor.custom-pagination') }}
                            </ul>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
    <div class="bottom-wrap bg-gray-50 dark:bg-gray-900 py-6">
        <div class="container">
            <div class="text-center">
                <h2 class="h4 mb-4">{{ __('frontend-labels.web_stories.explore_more_stories') }}</h2>
                <p class="text-gray-600 dark:text-gray-400 mb-4">
                    {{ __('frontend-labels.web_stories.discover_more_stories') }}
                </p>
                <a href="{{ route('webstories.index') }}" class="btn btn-primary">
                  {{ __('frontend-labels.web_stories.browse_all_stories') }}
                </a>
            </div>
        </div>
    </div>
@endsection
