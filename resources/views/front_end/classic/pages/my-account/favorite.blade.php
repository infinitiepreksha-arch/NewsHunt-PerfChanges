@extends('front_end.' . $theme . '.layout.main')
@section('body')
    <!-- Wrapper start -->
    <div id="wrapper" class="wrap overflow-hidden-x">
        <div class="breadcrumbs panel z-1 py-2 bg-gray-25 dark:bg-gray-100 dark:bg-opacity-5 dark:text-white">
            <div class="container max-w-xl">
                <ul class="breadcrumb nav-x justify-center gap-1 fs-7 sm:fs-6 m-0">
                    <li><a href="{{ url('/home') }}" title="Go back to home">{{ __('frontend-labels.home.title') }}</a>
                    </li>
                    <li><i class="unicon-chevron-right opacity-50"></i></li>
                    <li><a href="{{ url('my-account') }}"
                            title="{{ __('frontend-labels.my-account.account_info') }}">{{ __('frontend-labels.my-account.account_info') }}</a>
                    </li>
                    <li><i class="unicon-chevron-right opacity-50"></i></li>
                    <li><span class="opacity-50">{{ $title }}
                        </span></li>
                </ul>
            </div>
        </div>
        <div class="section py-3 sm:py-6 lg:py-9">
            <div class="container max-w-xl">
                <div class="panel vstack gap-3 sm:gap-6 lg:gap-3">
                    {{-- Mobile view sidebar --}}
                    <div id="mobile-view-sidbar" data-uc-offcanvas="overlay: true;">
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
                                <div class="dashboard-tab">
                                    <div class="block-content panel row sep-x gx-4 gy-3 lg:gy-2">
                                        <div>
                                            <article class="post type-post panel d-flex gap-2">
                                                <h6 class="fs-4 lg:fs-5 xl:fs-5 fw-medium opacity-60">
                                                    <a class="text-none text-dark dark:text-white hover:text-primary duration-150"
                                                        href="{{ url('my-account') }}"
                                                        title="{{ __('frontend-labels.my-account.account_info') }}">
                                                        <i class="bi bi-person-circle fs-3"> </i>
                                                        {{ __('frontend-labels.my-account.account_info') }}
                                                    </a>
                                                </h6>
                                            </article>
                                        </div>
                                        <div>
                                            <article class="post type-post panel d-flex gap-2">
                                                <h6 class="fs-4 lg:fs-5 xl:fs-5 fw-medium opacity-60">
                                                    <a class="text-none text-dark dark:text-white hover:text-primary duration-150"
                                                        href="{{ url('my-account/followings') }}"
                                                        title="{{ __('frontend-labels.followings.title') }}">
                                                        <i class="bi bi-youtube fs-3"> </i>
                                                        {{ __('frontend-labels.followings.title') }}
                                                    </a>
                                                </h6>
                                            </article>
                                        </div>

                                        <div>
                                            <article class="post type-post panel d-flex gap-2">
                                                <h6 class="fs-4 lg:fs-5 xl:fs-5 fw-medium">
                                                    <a class="text-none text-dark dark:text-white hover:text-primary duration-150"
                                                        href="#" title="{{ $title }}">
                                                        <i class="bi bi-bookmark fs-3 dark:text-white"> </i>
                                                        {{ $title }}
                                                    </a>
                                                </h6>
                                            </article>
                                        </div>
                                        @if ($free_trial_status == '0')
                                        <div>
                                            <article class="post type-post panel d-flex gap-2">
                                                <h6 class="fs-4 lg:fs-5 xl:fs-5 fw-medium opacity-60">
                                                    <a class="text-none text-dark dark:text-white hover:text-primary duration-150"
                                                        href="{{ url('my-account/transaction') }}"
                                                        title="{{ __('frontend-labels.transaction_details.title') }}">
                                                        <i class="bi bi-wallet2 fs-3"> </i>
                                                        {{ __('frontend-labels.transaction_details.title') }}
                                                    </a>
                                                </h6>
                                            </article>
                                        </div>
                                        
                                        <div>
                                            <article class="post type-post panel d-flex gap-2">
                                                <h6 class="fs-4 lg:fs-5 xl:fs-5 fw-medium opacity-60">
                                                    <a class="text-none text-dark dark:text-white hover:text-primary duration-150"
                                                        href="{{ url('my-account/subscription') }}"
                                                        title="{{ __('frontend-labels.mysubscription.title') }}">
                                                        <svg width="24px" height="24px" viewBox="0 0 24 24"
                                                            xmlns="http://www.w3.org/2000/svg" fill="#000000">
                                                            <path
                                                                d="M14,6a7.17,7.17,0,0,0-1,.08A4.49,4.49,0,0,0,4,6.5V7A2,2,0,0,0,2,9v9a1.94,1.94,0,0,0,2,2H8.73A8,8,0,1,0,14,6ZM6,6.5a2.51,2.51,0,0,1,5-.24V7H6ZM14,20a6,6,0,1,1,6-6A6,6,0,0,1,14,20Zm-1.5-8v1h4a1,1,0,0,1,1,1v3a1,1,0,0,1-1,1H15v1H13V18H10.5V16h5V15h-4a1,1,0,0,1-1-1V11a1,1,0,0,1,1-1H13V9h2v1h2.5v2Z">
                                                            </path>
                                                        </svg></i> {{ __('frontend-labels.mysubscription.title') }}
                                                    </a>
                                                </h6>
                                            </article>
                                        </div>
                                        @endif
                                        @if (auth()->user()->id !== 1)
                                            <div>
                                                <article class="post type-post panel d-flex gap-2">
                                                    <h6 class="fs-4 lg:fs-5 xl:fs-5 fw-medium opacity-60">
                                                        <a class="text-none text-dark dark:text-white hover:text-primary duration-150"
                                                            title="{{ __('frontend-labels.my-account.remove_account') }}"
                                                            id="user-delete-account">
                                                            <i class="bi bi-person-fill-slash fs-3"> </i>
                                                            {{ __('frontend-labels.my-account.remove_account') }}
                                                        </a>
                                                    </h6>
                                                </article>
                                            </div>
                                        @endif
                                        <div>
                                            <article class="post type-post panel d-flex gap-2">
                                                <h6 class="fs-4 lg:fs-5 xl:fs-5 fw-medium opacity-60">
                                                    <form id="logout-form" action="{{ route('logout') }}" method="POST"
                                                        class="d-none">
                                                        @csrf
                                                    </form>
                                                    <a class="text-none text-dark dark:text-white hover:text-primary duration-150"
                                                        href="#"
                                                        onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                                        <i class="bi bi-box-arrow-right fs-3"> </i>
                                                        {{ __('frontend-labels.my-account.remove_account') }}
                                                    </a>
                                                </h6>
                                            </article>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="d-flex align-items-stretch gap-1">
                            <div class="col-12 col-sm-12 col-md-12 col-lg-3 mt-2 mb-2 d-none d-lg-block">
                                <!-- Dashboard sidebar -->
                                <div class="dashboard-sidebar bg-block rounded-lg mb-2 p-3 h-100">
                                    <div class="profile-top text-center mb-4">
                                        <div class="mb-3 mt-2">
                                            <img class="profile-image rounded-circle blur-up lazyloaded w-100px h-100px user-sidebar-img"
                                                data-src="{{ auth()->user()->profile ?? asset('front_end/classic/images/avatars/04.png') }}"
                                                src="{{ auth()->user()->profile ?? asset('front_end/classic/images/avatars/04.png') }}"
                                                alt="user" data-uc-tooltip="Profile">
                                        </div>
                                        <div class="profile-detail dark:text-white">
                                            <h3>{{ auth()->user()->name }}</h3>
                                            <span>{{ auth()->user()->email }}</span>
                                        </div>
                                    </div>
                                    <div class="dashboard-tab">
                                        <div class="block-content panel row sep-x gx-4 gy-3 lg:gy-2">
                                            <div>
                                                <a class="text-none hover:text-primary duration-150"
                                                    href="{{ url('my-account') }}"
                                                    title="{{ __('frontend-labels.my-account.account_info') }}">
                                                    <article class="post type-post panel d-flex gap-2">
                                                        <h4 class="fs-4 lg:fs-6 xl:fs-4 fw-medium"><i
                                                                class="bi bi-person-circle fs-3"></i>
                                                            {{ __('frontend-labels.my-account.account_info') }}</h4>
                                                    </article>
                                                </a>
                                            </div>
                                            <div>
                                                <a class="text-none hover:text-primary duration-150"
                                                    href="{{ url('my-account/followings') }}"
                                                    title="{{ __('frontend-labels.followings.title') }}">
                                                    <article class="post type-post panel d-flex gap-2">
                                                        <h4 class="fs-4 lg:fs-6 xl:fs-4 fw-medium"><i
                                                                class="bi bi-youtube fs-3"></i>
                                                            {{ __('frontend-labels.followings.title') }}</h4>
                                                    </article>
                                                </a>
                                            </div>

                                            <div>
                                                <a class="text-none text-dark hover:text-primary duration-150"
                                                    href="#" title="{{ $title }}">
                                                    <article class="post type-post panel d-flex gap-2">
                                                        <h4 class="fs-4 lg:fs-6 xl:fs-4 fw-medium dark:text-white"><i
                                                                class="bi bi-bookmark fs-3 dark:text-white"></i>
                                                            {{ $title }}
                                                        </h4>
                                                    </article>
                                                </a>
                                            </div>
                                            @if ($free_trial_status == '0')
                                            <div>
                                                <article class="post type-post panel d-flex gap-2">
                                                    <h6 class="fs-4 lg:fs-5 xl:fs-5 fw-medium opacity-60">
                                                        <a class="text-none text-dark dark:text-white hover:text-primary duration-150"
                                                            href="{{ url('my-account/transaction') }}"
                                                            title="{{ __('frontend-labels.transaction_details.title') }}">
                                                            <i class="bi bi-wallet2 fs-3"></i> </i>
                                                            {{ __('frontend-labels.transaction_details.title') }}
                                                        </a>
                                                    </h6>
                                                </article>
                                            </div>
                                            <div>
                                                <article class="post type-post panel d-flex gap-2">
                                                    <h6 class="fs-4 lg:fs-5 xl:fs-5 fw-medium opacity-60">
                                                        <a class="text-none text-dark dark:text-white hover:text-primary duration-150"
                                                            href="{{ url('my-account/subscription') }}"
                                                            title="{{ __('frontend-labels.mysubscription.title') }}">
                                                            <svg width="24px" height="24px" viewBox="0 0 24 24"
                                                                xmlns="http://www.w3.org/2000/svg" fill="#000000">
                                                                <path
                                                                    d="M14,6a7.17,7.17,0,0,0-1,.08A4.49,4.49,0,0,0,4,6.5V7A2,2,0,0,0,2,9v9a1.94,1.94,0,0,0,2,2H8.73A8,8,0,1,0,14,6ZM6,6.5a2.51,2.51,0,0,1,5-.24V7H6ZM14,20a6,6,0,1,1,6-6A6,6,0,0,1,14,20Zm-1.5-8v1h4a1,1,0,0,1,1,1v3a1,1,0,0,1-1,1H15v1H13V18H10.5V16h5V15h-4a1,1,0,0,1-1-1V11a1,1,0,0,1,1-1H13V9h2v1h2.5v2Z">
                                                                </path>
                                                            </svg> {{ __('frontend-labels.mysubscription.title') }}
                                                        </a>
                                                    </h6>
                                                </article>
                                            </div>
                                            @endif
                                            @if (auth()->user()->id !== 1)
                                                <div>
                                                    <a class="text-none hover:text-primary duration-150"
                                                        title="{{ __('frontend-labels.my-account.remove_account') }}"
                                                        id="user-delete-account">
                                                        <article class="post type-post panel d-flex gap-2">
                                                            <h4 class="fs-4 lg:fs-6 xl:fs-4 fw-medium">
                                                                <i class="bi bi-person-fill-slash fs-3"></i>
                                                                {{ __('frontend-labels.my-account.remove_account') }}
                                                            </h4>
                                                        </article>
                                                    </a>
                                                </div>
                                            @endif
                                            <div>
                                                <div>
                                                    <a class="text-none hover:text-primary duration-150" href="#"
                                                        onclick="event.preventDefault(); document.getElementById('logout-form').submit();"
                                                        title="{{ __('frontend-labels.my-account.remove_account') }}">
                                                        <article class="post type-post panel d-flex gap-2">
                                                            <h4 class="fs-4 lg:fs-6 xl:fs-4 fw-medium">
                                                                <form id="logout-form" action="{{ route('logout') }}"
                                                                    method="POST" class="d-none">
                                                                    @csrf
                                                                </form>
                                                                <i class="bi bi-box-arrow-right fs-3"></i>
                                                                {{ __('frontend-labels.my-account.remove_account') }}
                                                            </h4>
                                                        </article>
                                                    </a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-12 col-sm-12 col-md-12 col-lg-9 mt-2 mb-2 h-100">
                                <div class="d-flex d-lg-none justify-end">
                                    <a class="btn btn-primary btn-sm" href="#mobile-view-sidbar"
                                        data-uc-toggle">{{ __('frontend-labels.my-account.account_info') }}</a>
                                </div>
                                <div id="content-area" class="rounded-lg p-4 h-100">
                                    <div id="favorites">
                                        <h4 title="{{ $title }}" class="mb-3">
                                            <strong class="text-black dark:text-white">{{ $title }}</strong>
                                        </h4>

                                        {{-- Filter Section --}}
                                        <div class="vstack sm:hstack justify-between items-center gap-2 sm:gap-4 mb-4">
                                            <div class="panel text-center sm:text-start">
                                                <span class="fs-6 m-0 opacity-60">
                                                </span>
                                            </div>

                                            <div class="hstack gap-1 fs-6 filtering">
                                                <span>{{ __('frontend-labels.favorite.filter_by_type') }}</span>
                                                <select name="type" id="filter_by_type"
                                                    data-bookmarks-url="{{ route('my-account.bookmarks') }}"
                                                    class="form-select form-control-xs fs-6 w-150px dark:bg-gray-900 dark:text-white dark:border-gray-700">
                                                    <option value="all" {{ $type == 'all' ? 'selected' : '' }}>
                                                        {{ __('frontend-labels.favorite.all') }}</option>
                                                    <option value="post" {{ $type == 'post' ? 'selected' : '' }}>
                                                        {{ __('frontend-labels.favorite.articles') }}</option>
                                                    <option value="video" {{ $type == 'video' ? 'selected' : '' }}>
                                                        {{ __('frontend-labels.favorite.videos') }}</option>
                                                    <option value="youtube" {{ $type == 'youtube' ? 'selected' : '' }}>
                                                        {{ __('frontend-labels.favorite.youtubes') }}</option>
                                                    <option value="audio" {{ $type == 'audio' ? 'selected' : '' }}>
                                                        {{ __('frontend-labels.favorite.audios') }}</option>
                                                </select>
                                            </div>
                                        </div>

                                        <div class="panel text-center">
                                            @if (!empty($favoritedPosts[0]))
                                                <div id="hide-div">

                                                    <div
                                                        class="row child-cols-12 sm:child-cols-6 lg:child-cols-4 col-match gy-4 xl:gy-6 gx-2 sm:gx-4">
                                                        @foreach ($favoritedPosts as $post)
                                                            <div id="postRender">
                                                                <article class="post type-post panel vstack gap-2">
                                                                    <div class="post-image panel overflow-hidden">
                                                                        <figure
                                                                            class="featured-image m-0 ratio ratio-16x9 rounded uc-transition-toggle overflow-hidden bg-gray-25 dark:bg-gray-800">
                                                                            <a href="{{ url('posts/' . $post->slug) }}"
                                                                                class="position-cover">
                                                                                @if ($post->type == 'post')
                                                                                    <img class="media-cover image uc-transition-scale-up uc-transition-opaque"
                                                                                        src="{{ $post->image }}"
                                                                                        data-src="{{ $post->image }}"
                                                                                        alt="{{ $post->title }}"
                                                                                        loading="lazy">
                                                                                @elseif ($post->type == 'audio')
                                                                                    <img class="media-cover image uc-transition-scale-up uc-transition-opaque"
                                                                                        src="{{ $post->image }}"
                                                                                        data-src="{{ $post->image }}"
                                                                                        alt="{{ $post->title }}"
                                                                                        loading="lazy">
                                                                                    <div
                                                                                        class="post-category hstack gap-narrow justify-center align-items-center text-white">
                                                                                        <a class="text-none"
                                                                                            href="{{ url('posts/' . $post->slug) }}"
                                                                                            alt="{{ $post->title }}"><i
                                                                                                class="bi bi-play-circle font-size-45"></i></a>
                                                                                    </div>
                                                                                @elseif($post->type == 'video' || $post->type == 'youtube')
                                                                                    <img class="media-cover image uc-transition-scale-up uc-transition-opaque"
                                                                                        src="{{ $post->video_thumb }}"
                                                                                        data-src="{{ $post->video_thumb }}"
                                                                                        alt="{{ $post->title }}"
                                                                                        loading="lazy">
                                                                                    <div
                                                                                        class="post-category hstack gap-narrow justify-center align-items-center text-white">
                                                                                        <a class="text-none"
                                                                                            href="{{ url('posts/' . $post->slug) }}"
                                                                                            alt="{{ $post->title }}"><i
                                                                                                class="bi bi-play-circle font-size-45"></i></a>
                                                                                    </div>
                                                                                @elseif($post->type == 'youtube')
                                                                                    <img class="media-cover image uc-transition-scale-up uc-transition-opaque"
                                                                                        src="{{ $post->image }}"
                                                                                        data-src="{{ $post->image }}"
                                                                                        alt="{{ $post->title }}"
                                                                                        loading="lazy">
                                                                                    <div
                                                                                        class="post-category hstack gap-narrow justify-center align-items-center text-white">
                                                                                        <a class="text-none"
                                                                                            href="{{ url('posts/' . $post->slug) }}"
                                                                                            alt="{{ $post->title }}"><i
                                                                                                class="bi bi-play-circle font-size-45"></i></a>
                                                                                    </div>
                                                                                @endif
                                                                            </a>
                                                                        </figure>
                                                                        @if (empty(request()->route('topic')))
                                                                            @if ($post->type == 'post')
                                                                                <div
                                                                                    class="post-category hstack gap-narrow position-absolute top-0 start-0 m-1 fs-7 fw-bold h-24px px-1 rounded-1 shadow-xs bg-white text-primary">
                                                                                    <a class="text-none"
                                                                                        title="{{ __('frontend-labels.favorite.article') }}">{{ __('frontend-labels.favorite.article') }}</a>
                                                                                </div>
                                                                            @elseif($post->type == 'video')
                                                                                <div
                                                                                    class="post-category hstack gap-narrow position-absolute top-0 start-0 m-1 fs-7 fw-bold h-24px px-1 rounded-1 shadow-xs bg-white text-primary">
                                                                                    <a class="text-none"
                                                                                        title="{{ __('frontend-labels.favorite.video') }}">{{ __('frontend-labels.favorite.video') }}</a>
                                                                                </div>
                                                                            @elseif($post->type == 'youtube')
                                                                                <div
                                                                                    class="post-category hstack gap-narrow position-absolute top-0 start-0 m-1 fs-7 fw-bold h-24px px-1 rounded-1 shadow-xs bg-white text-primary">
                                                                                    <a class="text-none"
                                                                                        title="{{ __('frontend-labels.favorite.youtube') }}">{{ __('frontend-labels.favorite.youtube') }}</a>
                                                                                </div>
                                                                            @elseif($post->type == 'audio')
                                                                                <div
                                                                                    class="post-category hstack gap-narrow position-absolute top-0 start-0 m-1 fs-7 fw-bold h-24px px-1 rounded-1 shadow-xs bg-white text-primary">
                                                                                    <a class="text-none"
                                                                                        title="{{ __('frontend-labels.favorite.audio') }}">{{ __('frontend-labels.favorite.audio') }}</a>
                                                                                </div>
                                                                            @endif
                                                                        @endif
                                                                        {{-- <div
                                                                            class="position-absolute top-0 end-0 w-150px h-150px rounded-top-end bg-gradient-45 from-transparent via-transparent to-black opacity-50">
                                                                        </div> --}}
                                                                    </div>
                                                                    <div class="post-header panel vstack gap-1 lg:gap-2">
                                                                        <h3
                                                                            class="post-title h6 sm:h3 xl:h6 m-0 text-truncate-2 m-0 text-start">
                                                                            <a class="text-none"
                                                                                href="{{ url('posts/' . $post->slug) }}"
                                                                                title="{{ $post->title }}">{{ $post->title }}</a>
                                                                        </h3>
                                                                        <div class="row row-col">
                                                                            <div
                                                                                class="post-meta fs-7 fw-medium text-gray-900 dark:text-white d-flex justify-between">
                                                                                <div class="d-flex gap-1">
                                                                                    <a href="{{ url('channels/' . $post->channel_slug) }}"
                                                                                        title="{{ $post->channel_name }}">
                                                                                        <img src="{{ $post->channel_logo }}"
                                                                                            alt="{{ $post->channel_name }}"
                                                                                            class="h-20px">
                                                                                    </a>
                                                                                    <a href="{{ url('channels/' . $post->channel_slug) }}"
                                                                                        title="{{ $post->channel_name }}"
                                                                                        class="text-black dark:text-white text-none fw-bold">
                                                                                        {{ $post->channel_name }}
                                                                                    </a>
                                                                                </div>
                                                                                <div class="text-end">
                                                                                    <a href="javascript:void(0);"
                                                                                        class="pin-post-btn text-none me-1"
                                                                                        data-post-id="{{ $post->id }}"
                                                                                        data-url="{{ route('favorites.togglePin') }}"
                                                                                        data-csrf="{{ csrf_token() }}"
                                                                                        title="{{ $post->is_pinned ? 'Unpin Post' : 'Pin Post' }}">
                                                                                        @if ($post->is_pinned)
                                                                                            <i
                                                                                                class="bi bi-pin-angle-fill text-primary fs-3"></i>
                                                                                        @else
                                                                                            <i
                                                                                                class="bi bi-pin-angle fs-4"></i>
                                                                                        @endif
                                                                                    </a>
                                                                                    <a href="javascript:void(0);"
                                                                                        class="post-comments text-none gap-narrow unlike-post-btn"
                                                                                        data-post-id="{{ $post->id }}"
                                                                                        title="Bookmark">
                                                                                        <i
                                                                                            class="bi bi-bookmark-fill fs-3"></i>
                                                                                    </a>
                                                                                </div>
                                                                            </div>

                                                                            <div class="post-date hstack gap-narrow">
                                                                                <span
                                                                                    title="{{ $post->publish_date ?? $post->pubdate }}">{{ $post->publish_date ?? $post->pubdate }}</span>
                                                                            </div>
                                                                        </div>

                                                                    </div>
                                                                </article>
                                                            </div>
                                                        @endforeach
                                                    </div>
                                                    <div class="nav-pagination pt-3 mt-6 lg:mt-9">
                                                        <ul class="nav-x uc-pagination hstack gap-1 justify-center ft-secondary"
                                                            data-uc-margin="">
                                                            {{ $favoritedPosts->appends(request()->query())->links('vendor.custom-pagination') }}
                                                        </ul>
                                                    </div>

                                                    <div class="mt-7 d-none" id="empty-state">
                                                        <img class="w-100 h-450px object-contain image uc-transition-opaque"
                                                            src="{{ asset('front_end/classic/images/place-holser/no-data.png') }}"
                                                            data-src="" alt="No Data Found">
                                                    </div>
                                                </div>
                                            @else
                                                <div>
                                                    <img class="w-100 h-450px object-contain image uc-transition-opaque"
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
            </div>
        </div>
    </div>
@endsection
@section('script')
    <script defer src="{{ asset('front_end/' . $theme . '/js/custom/my-account.js') }}"></script>
@endsection
