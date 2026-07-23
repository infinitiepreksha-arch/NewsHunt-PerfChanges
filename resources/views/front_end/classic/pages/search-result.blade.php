@extends('front_end.' . $theme . '.layout.main')

@section('body')
    <!-- Wrapper start -->
    <div id="wrapper" class="wrap overflow-hidden-x">
        <div class="breadcrumbs panel z-1 py-2 bg-gray-25 dark:bg-gray-100 dark:bg-opacity-5 dark:text-white">
            <div class="container max-w-xl">
                <ul class="breadcrumb nav-x justify-center gap-1 fs-7 sm:fs-6 m-0">
                    <li><a href="{{ url('home') }}" title="Home">{{ __('frontend-labels.home.title') }}</a></li>
                    <li><i class="unicon-chevron-right opacity-50"></i></li>
                    @if (!empty($searchQuery))
                        <li><span class="opacity-70">{{ __('frontend-labels.search.title') }}</span></li>
                        <li><i class="unicon-chevron-right opacity-50"></i></li>
                        <li><span class="opacity-70" title="{{ $title }}">{{ __('frontend-labels.search.for') }}
                                {{ $title }}</span></li>
                    @else
                        <li><a href="{{ route('posts.search') }}">{{ __('frontend-labels.posts.all_posts') }}</a></li>
                    @endif
                </ul>
            </div>
        </div>
        <div class="section py-3 sm:py-6 lg:py-9">
            <div class="container max-w-xl">
                <div class="panel vstack gap-3 sm:gap-6 lg:gap-3">
                    <header class="page-header panel vstack text-center align-items-center mb-4">
                        <h1 class="h3 lg:h1" id="page-search-title">{{ __('frontend-labels.posts.all_posts') }}</h1>
                        <div class="w-100 my-2" style="max-width: 450px; margin: 0 auto;">
                            <input type="text" id="page_search_input" name="search" class="form-control rounded-pill px-4 py-2 border shadow-xs text-black dark:text-white dark:bg-gray-800" placeholder="{{ __('frontend-labels.search.title') }}..." value="{{ request('search') ?? '' }}">
                        </div>
                        <span class="m-0 opacity-60" id="sentence-subtitle">
                            {{ __('frontend-labels.search.showing') }} <span id="counter-first">{{ $getPosts->firstItem() ?? '0' }}</span>
                            {{ __('frontend-labels.search.to') }} <span id="counter-last">{{ $getPosts->lastItem() ?? '0' }}</span>
                            {{ $post_label->value ?? '' }} {{ __('frontend-labels.search.posts_out_of') }}
                            <span id="counter-total">{{ $getPosts->total() ?? '0' }}</span> {{ __('frontend-labels.search.total') }}
                            <span id="search-query-sentence">@if(request()->filled('search')) for <strong>"{{ request('search') }}"</strong>@endif</span>
                        </span>
                    </header>


                    <div id="uc-filter-panel" data-uc-offcanvas="overlay: true;">
                        <div class="uc-offcanvas-bar bg-white text-dark dark:bg-gray-900 dark:text-white">
                            <header
                                class="uc-offcanvas-header hstack justify-between items-center pb-4 bg-white dark:bg-gray-900">
                                <div class="uc-logo">
                                    <a href="{{ url('home') }}" class="h5 text-none text-gray-900 dark:text-white">
                                        <img class="img-fluid w-auto text-dark dark:text-white hover:text-primary transition-color duration-150 d-block dark:d-none header-img-max-height"
                                            src="{{ $dark_logo != null ? url('storage/' . $dark_logo->value) : asset('assets/images/logo/DarkLogo.png') }}"
                                            fetchpriority="high" alt="Light">
                                        {{-- Light --}}
                                        <img class="img-fluid w-auto text-dark dark:text-white hover:text-primary transition-color duration-150 d-none dark:d-block header-img-max-height"
                                            src="{{ $light_logo != null ? url('storage/' . $light_logo->value) : asset('assets/images/logo/LightLogo.png') }}"
                                            fetchpriority="high" alt="Dark">
                                    </a>
                                </div>
                                <button
                                    class="uc-offcanvas-close p-0 icon-3 btn border-0 dark:text-white dark:text-opacity-50 hover:text-primary hover:rotate-90 duration-150 transition-all"
                                    type="button">
                                    <i class="unicon-close"></i>
                                </button>
                            </header>

                            <div class="panel">
                                <form action="{{ route('posts.search') }}" method="GET" id="searchForm">
                                    <ul class="nav-y gap-narrow fw-bold fs-5" data-uc-nav>
                                        <li class="uc-parent">
                                            <a href="#">
                                                {{ __('frontend-labels.channels.title') }}</a>
                                            <ul class="uc-nav-sub" data-uc-nav="">
                                                @if (!empty($channels))
                                                    <div class="d-flex gap-1">
                                                        <input type="checkbox" id="mobile-channel-all"
                                                            class="form-check-input channel-all-checkbox rounded-0 dark:bg-gray-800 dark:border-white hover:text-primary dark:border-opacity-15"
                                                            value="all"
                                                            {{ empty(request()->input('channel', [])) || in_array('all', (array) request()->input('channel', [])) ? 'checked' : '' }}>
                                                        <label for="mobile-channel-all">All</label>
                                                    </div>
                                                    @foreach ($channels as $channel)
                                                        @if ($channel->name !== 'All')
                                                            <div class="d-flex gap-1">
                                                                <input type="checkbox" id="mobile-{{ $channel->slug }}"
                                                                    class="form-check-input channel-item-checkbox rounded-0 dark:bg-gray-800 dark:border-white hover:text-primary dark:border-opacity-15"
                                                                    name="channel[]" value="{{ $channel->slug }}"
                                                                    {{ in_array($channel->slug, (array) request()->input('channel', [])) ? 'checked' : '' }}>
                                                                <label for="mobile-{{ $channel->slug }}">{{ $channel->name }}</label>
                                                            </div>
                                                        @endif
                                                    @endforeach
                                                @endif
                                            </ul>
                                        </li>
                                        <li class="uc-parent">
                                            <a href="#">{{ __('frontend-labels.topics.title') }}</a>
                                            <ul class="uc-nav-sub" data-uc-nav="">
                                                @if (!empty($topics))
                                                    @foreach ($topics as $topic)
                                                        <div class="d-flex gap-1">
                                                            <input type="checkbox"
                                                                class="form-check-input topic-item-checkbox rounded-0 dark:bg-gray-800 dark:border-white dark:border-opacity-15"
                                                                name="topic[]" id="mobile-topic-{{ $topic->slug }}"
                                                                value="{{ $topic->slug }}"
                                                                {{ in_array($topic->slug, (array) request()->input('topic', [])) ? 'checked' : '' }}>
                                                            <label for="mobile-topic-{{ $topic->slug }}">{{ $topic->name }}</label>
                                                        </div>
                                                    @endforeach
                                                @endif
                                            </ul>
                                        </li>
                                        <li class="hr opacity-10 my-1"></li>
                                        <h5 class="text-dark fw-bold dark:text-white">
                                            {{ __('frontend-labels.filters.other_filters') }}
                                        </h5>
                                        <div class="d-flex gap-1">
                                            <input type="radio" name="filter"
                                                class="form-check-input sort-filter-radio rounded-0 dark:bg-gray-900 dark:border-white dark:border-opacity-15"
                                                id="mobile-most-read" value="most-read"
                                                {{ request()->input('filter') === 'most-read' ? 'checked' : '' }}>
                                            <label for="mobile-most-read">{{ __('frontend-labels.home.most_read') }}</label>
                                        </div>
                                        <div class="d-flex gap-1">
                                            <input type="radio" name="filter"
                                                class="form-check-input sort-filter-radio rounded-0 dark:bg-gray-900 dark:border-white dark:border-opacity-15"
                                                id="mobile-most-liked" value="most-liked"
                                                {{ request()->input('filter') === 'most-liked' ? 'checked' : '' }}>
                                            <label for="mobile-most-liked">{{ __('frontend-labels.filters.most_liked') }}</label>
                                        </div>
                                        <div class="d-flex gap-1">
                                            <input type="radio" name="filter"
                                                class="form-check-input sort-filter-radio rounded-0 dark:bg-gray-900 dark:border-white dark:border-opacity-15"
                                                id="mobile-most-recent" value="most-recent"
                                                {{ request()->input('filter') === 'most-recent' || !request()->has('filter') ? 'checked' : '' }}>
                                            <label
                                                for="mobile-most-recent">{{ __('frontend-labels.filters.most_recent') }}</label>
                                        </div>
                                    </ul>
                                    <div class="d-flex justify-between mt-3">
                                        <button type="button" id="btn-clear-filters-mobile"
                                            class="btn btn-outline-primary btn-sm w-100">{{ __('frontend-labels.filters.clear') }}</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>


                    <div class="row">
                        <div class="d-flex align-items-stretch gap-1">
                            <div class="col-12 col-sm-12 col-md-12 col-lg-3 mt-2 mb-2 d-none d-lg-block">
                                <!-- Dashboard sidebar -->
                                <div class="dashboard-sidebar bg-block rounded-lg mt-0 mb-2 p-3 h-auto border ">
                                    <div class="profile-top mb-4">
                                        <div class="profile-detail text-black dark:text-white  rounded">
                                            <h3 title="{{ __('frontend-labels.filters.title') }}">
                                                {{ __('frontend-labels.filters.title') }}</h3>
                                            <span></span>
                                        </div>
                                    </div>
                                    <div class="dashboard-tab">
                                        <form action="{{ route('posts.search') }}" method="GET" id="filterForm">
                                            <div>
                                                <h5 class="h5 mt-2 mb-0 text-black dark:text-white">
                                                    {{ __('frontend-labels.channels.title') }}</h5>
                                                <div
                                                    class="scrollable-container bg-gray-450 dark:bg-gray-100 dark:bg-opacity-5 mt-0 px-2 border p-2 rounded mt-2">
                                                    <label class="d-flex align-items-center gap-1 text-black dark:text-white hover:text-primary">
                                                        <input type="checkbox" id="desktop-channel-all"
                                                            class="form-check-input channel-all-checkbox rounded-0 dark:bg-gray-800 dark:border-white dark:border-opacity-15"
                                                            value="all"
                                                            {{ empty(request()->input('channel', [])) || in_array('all', (array) request()->input('channel', [])) ? 'checked' : '' }}>
                                                        All
                                                    </label>
                                                    @foreach ($channels as $channel)
                                                        @if ($channel->name !== 'All')
                                                            <label
                                                                class="d-flex align-items-center gap-1 text-black dark:text-white hover:text-primary">
                                                                <input type="checkbox" id="desktop-{{ $channel->slug }}"
                                                                    class="form-check-input channel-item-checkbox rounded-0 dark:bg-gray-800 dark:border-white dark:border-opacity-15"
                                                                    name="channel[]" value="{{ $channel->slug }}"
                                                                    {{ in_array($channel->slug, (array) request()->input('channel', [])) ? 'checked' : '' }}>
                                                                {{ $channel->name }}
                                                            </label>
                                                        @endif
                                                    @endforeach
                                                </div>
                                            </div>
                                            <div>
                                                <h5 class="h5 mt-2 mb-0 text-black dark:text-white">
                                                    {{ __('frontend-labels.topics.title') }}</h5>
                                                <div
                                                    class="scrollable-container bg-gray-450 dark:bg-gray-100 dark:bg-opacity-5 px-2 border p-2 rounded mt-2">
                                                    @foreach ($topics as $topic)
                                                        <label
                                                            class="d-flex align-items-center gap-1 text-black dark:text-white hover:text-primary">
                                                            <input type="checkbox"
                                                                class="form-check-input topic-item-checkbox rounded-0 dark:bg-gray-600 dark:border-white dark:border-opacity-15"
                                                                name="topic[]" id="desktop-topic-{{ $topic->slug }}"
                                                                value="{{ $topic->slug }}"
                                                                {{ in_array($topic->slug, (array) request()->input('topic', [])) ? 'checked' : '' }}>
                                                            {{ $topic->name }}
                                                        </label>
                                                    @endforeach
                                                </div>
                                            </div>
                                            <div>
                                                <h5 class="h5 mt-2 mb-0 text-black dark:text-white">
                                                    {{ __('frontend-labels.filters.other_filters') }}</h5>
                                                <div
                                                    class="scrollable-container bg-gray-450 dark:bg-gray-100 dark:bg-opacity-5 px-2 border p-2 rounded mt-2">
                                                    <div class="d-flex gap-1">
                                                        <label
                                                            class="d-flex align-items-center gap-1 text-black dark:text-white hover:text-primary">
                                                            <input type="radio" name="filter"
                                                                class="form-check-input sort-filter-radio rounded-0 dark:bg-gray-800  dark:border-white dark:border-opacity-15"
                                                                id="desktop-most-read" value="most-read"
                                                                {{ request()->input('filter') === 'most-read' ? 'checked' : '' }}>
                                                            {{ __('frontend-labels.home.most_read') }}
                                                        </label>
                                                    </div>

                                                    <div class="d-flex gap-1">
                                                        <label
                                                            class="d-flex align-items-center gap-1 text-black dark:text-white hover:text-primary">
                                                            <input type="radio" name="filter"
                                                                class="form-check-input sort-filter-radio rounded-0 dark:bg-gray-800 dark:border-white dark:border-opacity-15"
                                                                id="desktop-most-liked" value="most-liked"
                                                                {{ request()->input('filter') === 'most-liked' ? 'checked' : '' }}>
                                                            {{ __('frontend-labels.filters.most_liked') }}
                                                        </label>
                                                    </div>

                                                    <div class="d-flex gap-1">
                                                        <label
                                                            class="d-flex align-items-center gap-1 text-black dark:text-white hover:text-primary">
                                                            <input type="radio" name="filter"
                                                                class="form-check-input sort-filter-radio rounded-0 dark:bg-gray-800 dark:border-white dark:border-opacity-15"
                                                                id="desktop-most-recent" value="most-recent"
                                                                {{ request()->input('filter') === 'most-recent' || !request()->has('filter') ? 'checked' : '' }}>
                                                            {{ __('frontend-labels.filters.most_recent') }}
                                                        </label>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="d-flex justify-between mt-3">
                                                <button type="button" id="btn-clear-filters-desktop"
                                                    class="btn btn-outline-primary btn-sm w-100">{{ __('frontend-labels.filters.clear') }}</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>

                            <div class="col-12 col-sm-12 col-md-12 col-lg-9 mt-2 mb-2">
                                <div class="d-flex d-lg-none justify-end mb-2">
                                    <a class="btn btn-primary btn-sm" href="#uc-filter-panel"
                                        data-uc-toggle>{{ __('frontend-labels.filters.title') }}
                                    </a>
                                </div>
                                <div id="posts-container" class="position-relative">
                                    @include('front_end.' . $theme . '.pages.partials.posts-list')
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('script')
    <script defer src="{{ asset('front_end/' . $theme . '/js/custom/search-news.js') }}"></script>
@endsection
