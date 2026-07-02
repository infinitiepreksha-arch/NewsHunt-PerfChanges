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
                    <header class="page-header panel vstack text-center">
                        <h1 class="h3 lg:h1">{{ $title }}</h1>
                        <span class="m-0 opacity-60">
                            {{ __('frontend-labels.search.showing') }} {{ $getPosts->firstItem() ?? '0' }}
                            {{ __('frontend-labels.search.to') }} {{ $getPosts->lastItem() ?? '0' }}
                            {{ $post_label->value ?? '' }} {{ __('frontend-labels.search.posts_out_of') }}
                            {{ $getPosts->total() ?? '0' }} {{ __('frontend-labels.search.total') }}
                            @if (request()->filled('search'))
                                {{ __('frontend-labels.search.for_search') }} "{{ request('search') }}"
                            @else
                                {{ __('frontend-labels.search.under') }} <br
                                    class="d-block lg:d-none">"{{ request()->route('topic') ?? ($post_label->value ?? '') }}"
                                {{ __('frontend-labels.search.category') }}
                            @endif
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
                                                {{ __('frontend-labels.channels.title') }}
                                                {{ request()->input('search') }}</a>
                                            <ul class="uc-nav-sub" data-uc-nav="">
                                                @if (!empty($channels))
                                                    @foreach ($channels as $channel)
                                                        <div class="d-flex gap-1">
                                                            <input type="checkbox" id="{{ $channel->slug }}"
                                                                class="form-check-input rounded-0 dark:bg-gray-800 dark:border-white hover:text-primary dark:border-opacity-15"
                                                                name="channel[]" value="{{ $channel->slug }}"
                                                                {{ in_array($channel->slug, (array) request()->input('channel', [])) ? 'checked' : '' }}>
                                                            <label for="{{ $channel->slug }}">{{ $channel->name }}</label>
                                                        </div>
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
                                                                class="form-check-input rounded-0 dark:bg-gray-800 dark:border-white dark:border-opacity-15"
                                                                name="topic[]" id="{{ $topic->slug }}"
                                                                value="{{ $topic->slug }}"
                                                                {{ in_array($topic->slug, (array) request()->input('topic', [])) ? 'checked' : '' }}>
                                                            <label for="{{ $topic->slug }}">{{ $topic->name }}</label>
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
                                            <input type="checkbox" name="most-read"
                                                class="form-check-input rounded-0 dark:bg-gray-900 dark:border-white dark:border-opacity-15"
                                                id="most-read" value="most-read"
                                                {{ request()->input('most-read') ? 'checked' : '' }}>
                                            <label for="most-read">{{ __('frontend-labels.home.most_read') }}</label>
                                        </div>
                                        <div class="d-flex gap-1">
                                            <input type="checkbox"
                                                class="form-check-input rounded-0 dark:bg-gray-900 dark:border-white dark:border-opacity-15"
                                                name="most-liked" id="most-liked" value="most-liked"
                                                {{ request()->input('most-liked') ? 'checked' : '' }}>
                                            <label for="most-liked">{{ __('frontend-labels.filters.most_liked') }}</label>
                                        </div>
                                        <div class="d-flex gap-1">
                                            <input type="checkbox"
                                                class="form-check-input rounded-0 dark:bg-gray-900 dark:border-white dark:border-opacity-15"
                                                name="most-recent" id="most-recent" value="most-recent"
                                                {{ request()->input('most-recent') ? 'checked' : '' }}>
                                            <label
                                                for="most-recent">{{ __('frontend-labels.filters.most_recent') }}</label>
                                        </div>
                                        <div class="d-flex gap-1">
                                            <input type="checkbox"
                                                class="form-check-input rounded-0 dark:bg-gray-900 dark:border-white dark:border-opacity-15"
                                                name="channels-followed" id="channels-followed" value="channels-followed"
                                                {{ request()->input('channels-followed') ? 'checked' : '' }}>
                                            <label
                                                for="channels-followed">{{ __('frontend-labels.filters.channels_followed') }}</label>
                                        </div>
                                        @if (request()->filled('search'))
                                            <div class="d-flex gap-1">
                                                <input type="hidden"
                                                    class="form-check-input rounded-0 dark:bg-gray-900 dark:border-white dark:border-opacity-15"
                                                    name="search" value="{{ request()->input('search') ?? '' }}">
                                            </div>
                                        @endif
                                    </ul>
                                    <ul class="social-icons nav-x mt-4">
                                    </ul>
                                    <div class="d-flex justify-between mt-3">
                                        <button type="submit"
                                            class="btn btn-primary btn-sm">{{ __('frontend-labels.filters.apply') }}</button>
                                        <a href="{{ url('posts') }}"
                                            class="btn btn-outline-primary btn-sm text-primary">{{ __('frontend-labels.filters.clear') }}</a>
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
                                                <h5 class="h5 mb-0 text-black dark:text-white">
                                                    {{ __('frontend-labels.channels.title') }}</h5>
                                                <div
                                                    class="scrollable-container bg-gray-450 dark:bg-gray-100 dark:bg-opacity-5 mt-0 px-2 border p-2 rounded mt-2">
                                                    @foreach ($channels as $channel)
                                                        @if ($channel->name === 'All')
                                                            <a href="{{ url('posts') }}"
                                                                class="d-flex align-items-center gap-1 text-black dark:text-white hover:text-primary"
                                                                style="text-decoration: none;">
                                                                <input type="checkbox"
                                                                    class="form-check-input rounded-0 dark:bg-gray-800 dark:border-white dark:border-opacity-15"
                                                                    {{ empty(request()->input('channel', [])) ? 'checked' : '' }}>
                                                                {{ $channel->name }}
                                                            </a>
                                                        @else
                                                            <label
                                                                class="d-flex align-items-center gap-1 text-black dark:text-white hover:text-primary">
                                                                <input type="checkbox" id="{{ $channel->slug }}"
                                                                    class="form-check-input rounded-0 dark:bg-gray-800 dark:border-white dark:border-opacity-15"
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
                                                                class="form-check-input rounded-0 dark:bg-gray-600 dark:border-white dark:border-opacity-15"
                                                                name="topic[]" id="{{ $topic->slug }}"
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
                                                                class="form-check-input rounded-0 dark:bg-gray-800  dark:border-white dark:border-opacity-15"
                                                                id="most-read" value="most-read"
                                                                {{ request()->input('filter') === 'most-read' ? 'checked' : '' }}>
                                                            {{ __('frontend-labels.home.most_read') }}
                                                        </label>
                                                    </div>

                                                    <div class="d-flex gap-1">
                                                        <label
                                                            class="d-flex align-items-center gap-1 text-black dark:text-white hover:text-primary">
                                                            <input type="radio" name="filter"
                                                                class="form-check-input rounded-0 dark:bg-gray-800 dark:border-white dark:border-opacity-15"
                                                                id="most-liked" value="most-liked"
                                                                {{ request()->input('filter') === 'most-liked' ? 'checked' : '' }}>
                                                            {{ __('frontend-labels.filters.most_liked') }}
                                                        </label>
                                                    </div>

                                                    <div class="d-flex gap-1">
                                                        <label
                                                            class="d-flex align-items-center gap-1 text-black dark:text-white hover:text-primary">
                                                            <input type="radio" name="filter"
                                                                class="form-check-input rounded-0 dark:bg-gray-800 dark:border-white dark:border-opacity-15"
                                                                id="most-recent" value="most-recent"
                                                                {{ request()->input('filter') === 'most-recent' ? 'checked' : '' }}>
                                                            {{ __('frontend-labels.filters.most_recent') }}
                                                        </label>
                                                    </div>
                                                    @if (auth()->check())
                                                        <div class="d-flex gap-1">
                                                            <label
                                                                class="d-flex align-items-center gap-1 hover:text-primary">
                                                                <input type="radio" name="filter"
                                                                    class="form-check-input rounded-0 dark:bg-gray-800 dark:border-white dark:border-opacity-15"
                                                                    id="channels-followed" value="channels-followed"
                                                                    {{ request()->input('filter') === 'channels-followed' ? 'checked' : '' }}>
                                                                {{ __('frontend-labels.filters.channels_followed') }}
                                                            </label>
                                                        </div>
                                                    @endif
                                                    @if (request()->filled('search'))
                                                        <div class="d-flex gap-1">
                                                            <input type="hidden"
                                                                class="form-check-input rounded-0 dark:bg-gray-800 dark:border-white dark:border-opacity-15"
                                                                name="search"
                                                                value="{{ request()->input('search') ?? '' }}">
                                                        </div>
                                                    @endif
                                                </div>
                                            </div>
                                            <div class="d-flex justify-between mt-3">
                                                <button type="submit"
                                                    class="btn btn-primary btn-sm">{{ __('frontend-labels.filters.apply') }}</button>
                                                <a href="{{ url('posts') }}"
                                                    class="btn btn-outline-primary btn-sm">{{ __('frontend-labels.filters.clear') }}</a>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>

                            <div class="col-12 col-sm-12 col-md-12 col-lg-9 mt-2 mb-2">
                                @if (!empty($getPosts[0]))
                                    <div class="d-flex d-lg-none justify-end">
                                        <a class="btn btn-primary btn-sm" href="#uc-filter-panel"
                                            data-uc-toggle>{{ __('frontend-labels.filters.title') }}
                                        </a>
                                    </div>
                                    <div id="content-area" class="rounded-lg p-4">
                                        <div class="panel">
                                            <div id="posts-ad-container"
                                                class="row child-cols-12 sm:child-cols-6 lg:child-cols-4 col-match gy-4 xl:gy-6 gx-2 sm:gx-4">
                                                @foreach ($getPosts as $post)
                                                    <div id="postRender">
                                                        <article class="post type-post panel vstack gap-2">
                                                            <div class="post-image panel overflow-hidden">
                                                                <figure
                                                                    class="featured-image m-0 ratio ratio-16x9 rounded uc-transition-toggle overflow-hidden bg-gray-25 dark:bg-gray-800">
                                                                    <a href="{{ url('posts/' . $post->slug) }}"
                                                                        class="position-cover"
                                                                        title="{{ $post->title }}">

                                                                        @if ($post->type == 'video')
                                                                            <img class="media-cover image uc-transition-scale-up uc-transition-opaque"
                                                                                src="{{ $post->video_thumb }}"
                                                                                data-src="{{ $post->video_thumb }}"
                                                                                alt="{{ $post->title }}" loading="lazy">
                                                                            <div
                                                                                class="post-category hstack gap-narrow justify-center align-items-center text-white">
                                                                                <a class="text-none"
                                                                                    href="{{ url('topics/' . $post->topic_slug) }}"
                                                                                    title="{{ $post->topic_name }}"><i
                                                                                        class="bi bi-play-circle font-size-45"></i></a>
                                                                            </div>
                                                                        @elseif($post->type == 'post')
                                                                            <img class="media-cover image uc-transition-scale-up uc-transition-opaque"
                                                                                src="{{ $post->image }}"
                                                                                data-src="{{ $post->image }}"
                                                                                alt="{{ $post->title }}" loading="lazy">
                                                                        @elseif($post->type == 'audio')
                                                                            <img class="media-cover image uc-transition-scale-up uc-transition-opaque"
                                                                                src="{{ $post->image }}"
                                                                                data-src="{{ $post->image }}"
                                                                                alt="{{ $post->title }}" loading="lazy">
                                                                                 <div
                                                                                class="post-category hstack gap-narrow justify-center align-items-center text-white">
                                                                                <a class="text-none"
                                                                                    href="{{ url('topics/' . $post->topic_slug) }}"
                                                                                    title="{{ $post->topic_name }}"><i
                                                                                        class="bi bi-play-circle font-size-45"></i></a>
                                                                            </div>
                                                                        @elseif($post->type == 'youtube')
                                                                            <img class="media-cover image uc-transition-scale-up uc-transition-opaque"
                                                                                src="{{ $post->image }}"
                                                                                data-src="{{ $post->image }}"
                                                                                alt="{{ $post->title }}" loading="lazy">
                                                                                <div
                                                                                class="post-category hstack gap-narrow justify-center align-items-center text-white">
                                                                                <a class="text-none"
                                                                                    href="{{ url('topics/' . $post->topic_slug) }}"
                                                                                    title="{{ $post->topic_name }}"><i
                                                                                        class="bi bi-play-circle font-size-45"></i></a>
                                                                        @else
                                                                            <img class="media-cover image uc-transition-scale-up uc-transition-opaque"
                                                                                src="{{ $post->image }}"
                                                                                data-src="{{ $post->image }}"
                                                                                alt="{{ $post->title }}" loading="lazy">
                                                                        @endif
                                                                    </a>
                                                                </figure>
                                                                @if (empty(request()->route('topic')))
                                                                    <div
                                                                        class="post-category hstack gap-narrow position-absolute top-0 start-0 m-1 fs-7 fw-bold h-15px px-1 rounded-1 shadow-xs bg-white text-primary">
                                                                        <a class="text-none"
                                                                            href="{{ url('topics/' . $post->topic_slug) }}"
                                                                            title="{{ $post->topic_name }}">{{ $post->topic_name }}</a>
                                                                    </div>
                                                                @endif
                                                            </div>
                                                            <div class="post-header panel vstack gap-1 lg:gap-2">
                                                                <h3
                                                                    class="post-title h6 sm:h6 xl:h5 m-0 text-truncate-2 m-0">
                                                                    <a class="text-none"
                                                                        href="{{ url('posts/' . $post->slug) }}"
                                                                        title="{{ $post->title }}">{{ $post->title }}</a>
                                                                </h3>
                                                                <div>
                                                                    <div
                                                                        class="post-meta panel fs-7 fw-medium text-gray-900 dark:text-white text-opacity-60">
                                                                        <div class="meta">
                                                                            <div class="d-flex justify-between gap-2">
                                                                                <div>
                                                                                    <div class="d-flex gap-1">
                                                                                        <a href="{{ url('channels/' . $post->channel_slug) }}"
                                                                                            title="{{ $post->channel_name }}"><img
                                                                                                src="{{ url('storage/images/' . $post->channel_logo) }}"
                                                                                                alt="Channel Logo"
                                                                                                class="h-20px"></a>
                                                                                        <a href="{{ url('channels/' . $post->channel_slug) }}"
                                                                                            class="text-black dark:text-white text-none fw-bold"
                                                                                            title="{{ $post->channel_name }}">{{ $post->channel_name }}</a>
                                                                                    </div>
                                                                                </div>
                                                                                <div>

                                                                                </div>
                                                                                <div>
                                                                                    <div
                                                                                        class="post-comments text-none hstack gap-narrow gap-1">
                                                                                        <a href="{{ url('posts/' . $post->slug) }}#comment-form"
                                                                                            class="post-comments text-none hstack gap-narrow"
                                                                                            title="Comments">
                                                                                            <i
                                                                                                class="icon-narrow unicon-chat"></i>
                                                                                            <span>{{ $post->comment }}</span>
                                                                                        </a>
                                                                                        <i class="bi bi-eye fs-5"
                                                                                            title="Views"></i>
                                                                                        <span
                                                                                            title="Views">{{ $post->view_count }}</span>

                                                                                        <i
                                                                                            class="bi bi-heart-fill ms-1"></i>
                                                                                        <span>{{ $post->reaction ?? '' }}</span>
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                            <div>

                                                                                <div
                                                                                    class="post-date hstack gap-narrow mt-1">
                                                                                    <span
                                                                                        title="{{ $post->publish_date_news }}">{{ $post->publish_date ?? $post->pubdate }}</span>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                        <div class="actions">
                                                                            <div class="hstack gap-1"></div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </article>
                                                    </div>
                                                @endforeach

                                            </div>
                                            <div class="nav-pagination pt-3 mt-6 lg:mt-2">
                                                <ul class="nav-x uc-pagination hstack gap-1 justify-center ft-secondary"
                                                    data-uc-margin="">
                                                    {{ $getPosts->links('vendor.custom-pagination') }}
                                                </ul>
                                            </div>
                                        </div>
                                    </div>
                                @else
                                    <div class="p-3">
                                        <img class="object-fit-cover mx-h-50px image uc-transition-opaque"
                                            src="{{ asset('front_end/classic/images/place-holser/no-data.png') }}"
                                            data-src="" alt="No Data Found">
                                    </div>
                                @endif
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
