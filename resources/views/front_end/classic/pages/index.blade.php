@extends('front_end.' . $theme . '.layout.main')
@section('body')

    <style>
        /* Keeps next arrow active for AJAX fetching when Swiper/UIKit marks it disabled */
        .nav-next.disabled, .swiper-button-next.swiper-button-disabled {
            pointer-events: auto !important;
            cursor: pointer !important;
            opacity: 1 !important;
        }

        /* Custom class to hide navigation arrows when required (prevents UIKit/Swiper interference) */
        .swiper-nav-hidden {
            display: none !important;
        }

        /* Prevent vertical layout shift of Swiper slides before JS initialization */
        .swiper:not(.swiper-initialized) .swiper-slide:not(:first-child) {
            display: none !important;
        }
    </style>
    <div id="wrapper" class="wrap overflow-hidden-x">
        <!-- Top Posts Section start -->
        @if (isset($top_posts) && $top_posts->isNotEmpty())
        <div class="section panel overflow-hidden swiper-parent border-top">
            <div class="section-outer panel py-2 lg:py-4 dark:text-white">
                <div class="container max-w-xl">
                    <div class="section-inner panel vstack gap-2">
                        <div class="block-layout carousel-layout vstack gap-2 lg:gap-3 panel">
                            <div class="block-content panel">
                                <div class="swiper" id="top-posts-swiper"
                                    data-uc-swiper="items: 1; gap: 16; dots: .dot-nav; next: .nav-next; prev: .nav-prev; disable-class: disabled;"
                                    data-uc-swiper-s="items: 3; gap: 24;" data-uc-swiper-l="items: 4; gap: 24;">
                                    <div class="swiper-wrapper">
                                        @foreach ($top_posts as $top_post)
                                            <div class="swiper-slide" data-post-id="{{ $top_post->id }}">
                                                <div>
                                                    <article class="post type-post panel uc-transition-toggle gap-2">
                                                        <div class="row child-cols g-2" data-uc-grid>
                                                            <div class="col-auto">
                                                                <div
                                                                    class="post-media panel overflow-hidden max-w-64px min-w-64px">
                                                                    <div
                                                                        class="featured-image bg-gray-25 dark:bg-gray-800 ratio ratio-1x1">
                                                                        @if ($top_post->type == 'video' || $top_post->type == 'youtube')
                                                                            <a href="{{ url('posts/' . $top_post->slug) }}"
                                                                                class="position-cover">
                                                                                <img class="media-cover image uc-transition-scale-up uc-transition-opaque"
                                                                                     src="{{ $top_post->video_thumb ?? $defaultImage }}"
                                                                                     data-src="{{ $top_post->video_thumb ?? $defaultImage }}"
                                                                                     alt="Hidden Gems: Underrated Travel Destinations Around the World"
                                                                                     @if($loop->iteration > 4) loading="lazy" @endif
                                                                                     @if($loop->first) fetchpriority="high" @endif>
                                                                                <div
                                                                                    class="post-category hstack gap-narrow justify-center align-items-center text-white">
                                                                                    <a class="text-none"
                                                                                        href="{{ url('topics/' . $top_post->slug) }}"
                                                                                        title="{{ $top_post->name }}"><i
                                                                                            class="bi bi-play-circle font-size-45"></i></a>
                                                                                </div>
                                                                            </a>
                                                                        @elseif($top_post->type == 'audio')
                                                                            <a href="{{ url('posts/' . $top_post->slug) }}"
                                                                                class="position-cover">
                                                                                <img class="media-cover image uc-transition-scale-up uc-transition-opaque"
                                                                                    src="{{ $top_post->image ?? $defaultImage }}"
                                                                                    data-src="{{ $top_post->image ?? $defaultImage }}"
                                                                                    alt="Hidden Gems: Underrated Travel Destinations Around the World"
                                                                                    @if($loop->iteration > 4) loading="lazy" @endif
                                                                                    @if($loop->first) fetchpriority="high" @endif>
                                                                                <div
                                                                                    class="post-category hstack gap-narrow justify-center align-items-center text-white">
                                                                                </div>
                                                                            </a>
                                                                        @elseif($top_post->type == 'post')
                                                                            <a href="{{ url('posts/' . $top_post->slug) }}"
                                                                                class="position-cover">
                                                                                <img class="media-cover image uc-transition-scale-up uc-transition-opaque"
                                                                                    src="{{ $top_post->image ?? $defaultImage }}"
                                                                                    data-src="{{ $top_post->image ?? $defaultImage }}"
                                                                                    alt="Hidden Gems: Underrated Travel Destinations Around the World"
                                                                                    @if($loop->iteration > 4) loading="lazy" @endif
                                                                                    @if($loop->first) fetchpriority="high" @endif>
                                                                                <div
                                                                                    class="post-category hstack gap-narrow justify-center align-items-center text-white">
                                                                                </div>
                                                                            </a>
                                                                        @endif
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div>
                                                                <div class="post-header panel vstack gap-1">
                                                                    <h3
                                                                        class="post-title h6 hover:text-primary m-0 text-truncate-2">
                                                                        <a class="text-none duration-150"
                                                                            href="{{ url('posts/' . $top_post->slug) }}"
                                                                            title="{{ $top_post->title ?? '' }}">{{ $top_post->title }}</a>
                                                                    </h3>
                                                                </div>
                                                                @if ($top_post->channel != null)
                                                                    <a href="{{ url('channels/' . $top_post->channel->slug) }}"
                                                                        class="post-comments text-none hstack gap-narrow">
                                                                        <img src="{{ url('storage/images/' . $top_post->channel->logo) }}"
                                                                            alt="channel logo"
                                                                            title="{{ $top_post->channel->name ?? '' }}"
                                                                            class="rounded-pill h-20px"
                                                                            width="20"
                                                                            height="20">
                                                                        <span
                                                                            title="{{ $top_post->channel->name ?? '' }}">{{ $top_post->channel->name }}</span>
                                                                    </a>
                                                                @endif
                                                            </div>
                                                        </div>
                                                    </article>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                                <div
                                    class="swiper-nav nav-prev position-absolute top-50 start-0 translate-middle btn btn-alt-primary text-black rounded-circle p-0 border shadow-xs w-32px h-32px z-1">
                                    <i class="icon-1 unicon-chevron-left"></i>
                                </div>
                                <div
                                    class="swiper-nav nav-next position-absolute top-50 start-100 translate-middle btn btn-alt-primary text-black rounded-circle p-0 border shadow-xs w-32px h-32px z-1">
                                    <i class="icon-1 unicon-chevron-right"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @endif
        <!-- Top Posts Section end -->

        <!-- Banner Section start -->
        @if (isset($postBanners) && $postBanners->isNotEmpty())
        <div class="section panel mb-4 lg:mb-6">
            <div class="section-outer panel">
                <div class="container max-w-xl">
                    <div class="section-inner panel vstack gap-4">
                        <div class="section-content">
                            <div class="row child-col-12 lg:child-cols g-4 lg:g-6 col-match">
                                <div class="lg:col-9">
                                    <div class="block-layout slider-layout swiper-parent uc-dark">
                                        <div class="block-content panel uc-visible-toggle">
                                            <div class="swiper"
                                                data-uc-swiper="items: 1; active: 1; gap: 4; prev: .nav-prev; next: .nav-next; autoplay: 6000; parallax: true; fade: true; effect: fade; disable-class: d-none;">
                                                <div class="swiper-wrapper">
                                                    @foreach ($postBanners as $banner)
                                                        <div class="swiper-slide">
                                                            @if (
                                                                $banner->item_type === 'post' ||
                                                                    $banner->item_type === 'youtube' ||
                                                                    $banner->item_type === 'video' ||
                                                                    $banner->item_type === 'youtube')
                                                                {{-- POST CONTENT --}}
                                                                <article
                                                                    class="post type-post panel uc-transition-toggle vstack gap-2 lg:gap-3 h-100 overflow-hidden uc-dark">
                                                                    <div class="post-media panel overflow-hidden h-100">
                                                                        <div
                                                                            class="featured-image bg-gray-25 dark:bg-gray-800 h-100 d-none md:d-block">
                                                                            <canvas class="h-100 w-100"></canvas>
                                                                            <a href="{{ url('posts/' . $banner->slug) }}"
                                                                                class="position-cover">
                                                                                <img class="media-cover image uc-transition-scale-up uc-transition-opaque {{ !$loop->first ? 'lazy-img' : '' }}"
                                                                                     src="{{ $loop->first ? ($banner->image ?? $defaultImage) : 'data:image/gif;base64,R0lGODlhAQABAIAAAAAAAP///yH5BAEAAAAALAAAAAABAAEAAAIBRAA7' }}"
                                                                                     data-src="{{ $banner->image ?? $defaultImage }}"
                                                                                     alt="No img" @if($loop->first) fetchpriority="high" @endif
                                                                                     decoding="async">
                                                                            </a>
                                                                        </div>
                                                                        <div
                                                                            class="featured-image bg-gray-25 dark:bg-gray-800 ratio ratio-16x9 d-block md:d-none">
                                                                            <a href="{{ url('posts/' . $banner->slug) }}"
                                                                                class="position-cover">
                                                                                <img class="media-cover image uc-transition-scale-up uc-transition-opaque {{ !$loop->first ? 'lazy-img' : '' }}"
                                                                                     src="{{ $loop->first ? ($banner->image ?? $defaultImage) : 'data:image/gif;base64,R0lGODlhAQABAIAAAAAAAP///yH5BAEAAAAALAAAAAABAAEAAAIBRAA7' }}"
                                                                                     data-src="{{ $banner->image ?? $defaultImage }}"
                                                                                     alt="Solo Travel: Some Tips and Destinations for the Adventurous Explorer"
                                                                                     @if($loop->first) fetchpriority="high" @endif decoding="async">
                                                                            </a>
                                                                        </div>
                                                                    </div>
                                                                    <div
                                                                        class="position-cover bg-gradient-to-t from-black to-transparent opacity-90">
                                                                    </div>
                                                                    <a href="{{ url('posts/' . $banner->slug) }}"
                                                                        class="position-cover">
                                                                        <div class="post-header panel vstack justify-end items-start gap-1 p-2 sm:p-4 position-cover text-white"
                                                                            data-swiper-parallax-y="-24">
                                                                            <h3
                                                                                class="post-title h5 lg:h4 xl:h3 m-0 max-w-600px text-white text-truncate-2">
                                                                                <a class="text-none text-white"
                                                                                    href="{{ url('posts/' . $banner->slug) }}">{{ $banner->title ?? '' }}</a>
                                                                            </h3>
                                                                            <div>
                                                                                <div
                                                                                    class="post-meta panel hstack justify-between fs-7 text-white text-opacity-60">
                                                                                    <div class="meta">
                                                                                        <div class="d-flex gap-2">
                                                                                            <div>
                                                                                                <div
                                                                                                    class="justify-content-end">
                                                                                                    <span
                                                                                                        title="{{ $banner->publish_date_news ?? '' }}">
                                                                                                        {{ $banner->publish_date ?? $banner->pubdate }}
                                                                                                    </span>
                                                                                                </div>
                                                                                            </div>
                                                                                        </div>
                                                                                        <div class="d-flex mt-1 gap-2">
                                                                                            <div>
                                                                                                <div class="gap-1">
                                                                                                    @if ($banner->channel != null)
                                                                                                        <a href="{{ url('channels/' . $banner->channel->slug) }}"
                                                                                                            class="post-comments text-none hstack gap-narrow"
                                                                                                            title="{{ $banner->channel->name ?? '' }}">
                                                                                                             <img src="{{ url('storage/images/' . $banner->channel->logo ?? '') }}"
                                                                                                                 alt="chanel logo"
                                                                                                                 class="rounded h-20px"
                                                                                                                 decoding="async"
                                                                                                                 width="20"
                                                                                                                 height="20">
                                                                                                            {{ $banner->channel->name ?? '' }}
                                                                                                        </a>
                                                                                                    @endif
                                                                                                </div>
                                                                                            </div>
                                                                                            <div>
                                                                                                <a href="#post_comment"
                                                                                                    class="post-comments text-none hstack gap-narrow">
                                                                                                    <i
                                                                                                        class="icon-narrow unicon-chat"></i>
                                                                                                    <span>{{ $banner->comment }}</span>
                                                                                                    <i class="bi bi-eye fs-5 ms-1"
                                                                                                        title="Views"></i>
                                                                                                    <span
                                                                                                        title="Views">{{ $banner->view_count }}</span>
                                                                                                    <i
                                                                                                        class="bi bi-heart-fill ms-1"></i>
                                                                                                    <span>{{ $banner->reaction ?? '' }}</span>
                                                                                                </a>
                                                                                            </div>
                                                                                        </div>
                                                                                    </div>
                                                                                    <div class="actions">
                                                                                        <div class="hstack gap-1"></div>
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    </a>
                                                                </article>
                                                            @elseif($banner->item_type === 'ad')
                                                                <article
                                                                    class="post type-ad panel uc-transition-toggle vstack gap-2 lg:gap-3 h-100 overflow-hidden uc-dark">
                                                                    <div class="post-media panel overflow-hidden h-100">
                                                                        <div
                                                                            class="featured-image bg-gray-25 dark:bg-gray-800 h-100 d-none md:d-block">
                                                                            <canvas class="h-100 w-100"></canvas>
                                                                            <a id="bannerAdLink"
                                                                                href="{{ $banner->imageUrl ?? '#' }}"
                                                                                class="position-cover"
                                                                                @if ($banner->imageUrl) target="_blank" @endif>

                                                                                <!-- Dynamic Ad Image -->
                                                                                <img id="rotatingAdBanner"
                                                                                    class="media-cover image uc-transition-scale-up uc-transition-opaque"
                                                                                    src="" alt="Ad will load soon"
                                                                                    data-url="{{ url('/ads/random') }}"
                                                                                    loading="lazy">
                                                                            </a>
                                                                        </div>

                                                                        <!-- Mobile Version -->
                                                                        <div
                                                                            class="featured-image bg-gray-25 dark:bg-gray-800 ratio ratio-16x9 d-block md:d-none">
                                                                            <a id="bannerAdLinkMobile"
                                                                                href="{{ $banner->imageUrl ?? '#' }}"
                                                                                class="position-cover"
                                                                                @if ($banner->imageUrl) target="_blank" @endif>
                                                                                <img id="rotatingAdBannerMobile"
                                                                                    class="media-cover image uc-transition-scale-up uc-transition-opaque"
                                                                                    src="" alt="Ad will load soon"
                                                                                    data-url="{{ url('/ads/random') }}"
                                                                                    loading="lazy">
                                                                            </a>
                                                                        </div>
                                                                    </div>

                                                                    <!-- Overlay Info -->
                                                                    <div
                                                                        class="position-cover bg-gradient-to-t from-black to-transparent opacity-90">
                                                                    </div>
                                                                    <a id="bannerAdOverlay"
                                                                        href="{{ $banner->imageUrl ?? '#' }}"
                                                                        class="position-cover"
                                                                        @if ($banner->imageUrl) target="_blank" @endif>
                                                                        <div class="post-header panel vstack justify-end items-start gap-1 sm:p-4 position-cover text-white"
                                                                            data-swiper-parallax-y="-24">
                                                                            <div class="d-flex gap-2">
                                                                                <div>
                                                                                    <div
                                                                                        class="justify-content-end dark:bg-primary bg-primary dark:text-white rounded badge">
                                                                                        <span title="Sponsored Ads">
                                                                                            {{ __('frontend-labels.sponsor_ads.title') }}
                                                                                        </span>
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                            <h3 id="adTitle"
                                                                                class="post-title h5 lg:h4 xl:h3 m-0 max-w-600px text-white text-truncate-2">
                                                                            </h3>
                                                                        </div>
                                                                    </a>
                                                                </article>
                                                            @endif

                                                        </div>
                                                    @endforeach
                                                </div>
                                            </div>
                                            <div
                                                class="swiper-nav nav-prev position-absolute top-50 start-0 translate-middle-y btn btn-alt-primary text-black rounded-circle p-0 mx-2 border-0 shadow-xs w-32px h-32px z-1 uc-hidden-hover">
                                                <i class="icon-1 unicon-chevron-left"></i>
                                            </div>
                                            <div
                                                class="swiper-nav nav-next position-absolute top-50 end-0 translate-middle-y btn btn-alt-primary text-black rounded-circle p-0 mx-2 border-0 shadow-xs w-32px h-32px z-1 uc-hidden-hover">
                                                <i class="icon-1 unicon-chevron-right"></i>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                @if ($weatherCardStatus && $weatherCardStatus?->value == 1)
                                    <div class="lg:col-3 order-1">
                                        <div
                                            class="card text-body dark:bg-black bg-info-50  rounded-2  dark:shadow-sm bg-opacity-25 dark:text-white border-radius-10 border-none">
                                            <div class="card-body dark:bg-gray-900 rounded-2">
                                                <div class="d-flex justify-between">
                                                    <h4 id="currtime" class="dark:text-yellow-100 text-blue-800"></h4>
                                                    {{-- <p id="time" class="dark:text-yellow-100 text-blue-80"></p> --}}
                                                    <svg id="geo-icon" xmlns="http://www.w3.org/2000/svg" width="16"
                                                        height="16" onClick="getLocation()" fill="currentColor"
                                                        class="bi bi-crosshair2 hover:text-primary pointer-cursor"
                                                        viewBox="0 0 16 16">
                                                        <path
                                                            d="M8 0a.5.5 0 0 1 .5.5v.518A7 7 0 0 1 14.982 7.5h.518a.5.5 0 0 1 0 1h-.518A7 7 0 0 1 8.5 14.982v.518a.5.5 0 0 1-1 0v-.518A7 7 0 0 1 1.018 8.5H.5a.5.5 0 0 1 0-1h.518A7 7 0 0 1 7.5 1.018V.5A.5.5 0 0 1 8 0m-.5 2.02A6 6 0 0 0 2.02 7.5h1.005A5 5 0 0 1 7.5 3.025zm1 1.005A5 5 0 0 1 12.975 7.5h1.005A6 6 0 0 0 8.5 2.02zM12.975 8.5A5 5 0 0 1 8.5 12.975v1.005a6 6 0 0 0 5.48-5.48zM7.5 12.975A5 5 0 0 1 3.025 8.5H2.02a6 6 0 0 0 5.48 5.48zM10 8a2 2 0 1 0-4 0 2 2 0 0 0 4 0" />
                                                    </svg>
                                                </div>
                                                <h4 id="currdate" class="text-center mt-2 mb-2"></h4>
                                                <h1 class="flex-grow-1 text-center dark:text-primary-400 text-red-700"
                                                    id="weather-city">{{ __('frontend-labels.home.location') }}</h1>
                                                <p id="time" class="dark:text-yellow-100 text-center text-blue-80">
                                                </p>

                                                <p id="time" class="dark:text-yellow-100 text-blue-80"></p>
                                                <div>
                                                    <div class="d-flex flex-column text-center mt-2 mb-2">
                                                        <h1 class="mb-0 font-weight-bold dark:text-blue-300 text-blue-800 "
                                                            id="current-weather">
                                                            {{ __('frontend-labels.home.temperature') }}</h1>
                                                        <span class="small  dark:text-orange text-orange"
                                                            id="current-atmosphere">{{ __('frontend-labels.home.stormy') }}</span>
                                                    </div>
                                                </div>
                                                <div class="align-items-center">
                                                    <div class="row text-center mt-2">
                                                        <div>
                                                            <img src="https://mdbcdn.b-cdn.net/img/Photos/new-templates/bootstrap-weather/ilu1.webp"
                                                                id="weather-icon" width="100px" alt="">
                                                        </div>
                                                        <div class="col-4">
                                                            <i
                                                                class="bi bi-wind dark:text-primary text-primary-500 fs-4"></i>
                                                            <h6 id="wind-speed" class="mt-1 fw-semibold fs-6">
                                                                {{ __('frontend-labels.home.wind_speed') }}</h6>
                                                        </div>
                                                        <div class="col-4">
                                                            <i
                                                                class="bi bi-droplet-half dark:text-info text-info-800 fs-4"></i>
                                                            <div id="humidity" class="mt-1 fw-semibold fs-6">
                                                                {{ __('frontend-labels.home.humidity') }}</div>
                                                        </div>
                                                        <div class="col-4">
                                                            <i
                                                                class="bi bi-eye dark:text-warning text-warning-600 fs-4"></i>
                                                            <div id="visibility" class="mt-1 fw-semibold fs-6">
                                                                {{ __('frontend-labels.home.precipitation') }}</div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @else
                                    @if (isset($sidebarPosts) && $sidebarPosts->isNotEmpty())
                                    <div class="lg:col-3 order-1">
                                        <div class="block-layout list-layout vstack gap-2 lg:gap-3 panel overflow-hidden">
                                            <div class="block-header panel pt-1 border-top">
                                                <h2
                                                    class="h6 ft-tertiary fw-bold ls-0 text-uppercase m-0 text-black dark:text-white">
                                                    {{ __('frontend-labels.home.top_posts') }}
                                                </h2>
                                            </div>
                                            <div class="block-content">
                                                <div class="row child-cols-12 g-2 lg:g-4">
                                                    @foreach ($sidebarPosts as $post)
                                                        <article class="post type-post panel uc-transition-toggle mb-0">
                                                            <div class="row child-cols g-2" data-uc-grid>
                                                                <div>
                                                                    <div
                                                                        class="post-header panel vstack justify-between gap-1">
                                                                        <h3
                                                                            class="post-title h6 m-0 text-truncate-2 hover:text-primary">
                                                                            <a class="text-none duration-150"
                                                                                href="{{ url('posts/' . $post->slug) }}"
                                                                                title="{{ $post->title }}">
                                                                                {{ $post->title }}
                                                                            </a>
                                                                        </h3>
                                                                        <div
                                                                            class="post-date d-flex gap-narrow fs-7 mb-0 text-gray-900 dark:text-white text-opacity-60 justify-between">
                                                                            <span
                                                                                title="{{ $post->publish_date_news ?? '' }}">
                                                                                {{ $post->publish_date ?? $post->pubdate }}
                                                                            </span>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                <div class="col-auto">
                                                                    <div
                                                                        class="post-media panel overflow-hidden max-w-64px min-w-64px">
                                                                        <div
                                                                            class="featured-image bg-gray-25 dark:bg-gray-800 ratio ratio-1x1">
                                                                            <a href="{{ url('posts/' . $post->slug) }}"
                                                                                class="position-cover">
                                                                                @if ($post->type == 'video' || $post->type == 'youtube')
                                                                                    <img class="media-cover image uc-transition-scale-up uc-transition-opaque lazy-img"
                                                                                        data-src="{{ $post->video_thumb ?? $defaultImage }}" src="data:image/gif;base64,R0lGODlhAQABAIAAAAAAAP///yH5BAEAAAAALAAAAAABAAEAAAIBRAA7"
                                                                                        alt="{{ $post->title }}"
                                                                                        loading="lazy">
                                                                                    <div
                                                                                        class="post-category hstack gap-narrow justify-center align-items-center text-white">
                                                                                        <a class="text-none"
                                                                                            href="{{ url('topics/' . $post->slug) }}"
                                                                                            title="{{ $post->name }}"><i
                                                                                                class="bi bi-play-circle font-size-45"></i></a>
                                                                                    </div>
                                                                                @elseif ($post->type == 'audio')
                                                                                    <img class="media-cover image uc-transition-scale-up uc-transition-opaque lazy-img"
                                                                                        data-src="{{ $post->image ?? $defaultImage }}" src="data:image/gif;base64,R0lGODlhAQABAIAAAAAAAP///yH5BAEAAAAALAAAAAABAAEAAAIBRAA7"
                                                                                        alt="{{ $post->title }}"
                                                                                        loading="lazy">
                                                                                    <div
                                                                                        class="post-category hstack gap-narrow justify-center align-items-center text-white">
                                                                                        <a class="text-none"
                                                                                            href="{{ url('topics/' . $post->slug) }}"
                                                                                            title="{{ $post->name }}"><i
                                                                                                class="bi bi-play-circle font-size-45"></i></a>
                                                                                    </div>
                                                                                @elseif ($post->type == 'post')
                                                                                    <img class="media-cover image uc-transition-scale-up uc-transition-opaque lazy-img"
                                                                                        data-src="{{ $post->image ?? $defaultImage }}" src="data:image/gif;base64,R0lGODlhAQABAIAAAAAAAP///yH5BAEAAAAALAAAAAABAAEAAAIBRAA7"
                                                                                        alt="{{ $post->title }}"
                                                                                        loading="lazy">
                                                                                @endif
                                                                            </a>
                                                                        </div>
                                                                    </div>
                                                                </div>


                                                            </div>
                                                        </article>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
                                    @endif
                @endif
            </div>
        </div>
    </div>
    </div>
    </div>
    </div>
    @endif
    <!-- Banner Section end -->

    <div id="header-ad-container" class="text-center my-3"></div>

    <!-- Second Section start -->
    <div class="section panel overflow-hidden">
        <div class="section-outer panel">
            <div class="container max-w-xl">
                <div class="section-inner">
                    <div class="row child-cols-12 lg:child-cols g-4 lg:g-6 col-match" data-uc-grid>
                        {{-- News by topic --}}
                        @foreach ($frontTopics as $index => $topic)
                            @if ($index < 0 || $index > 4 || $topic->posts->isEmpty())
                                @continue
                            @endif
                            <div class="lg:col-4">
                                <div class="block-layout grid-layout vstack gap-2 lg:gap-3 panel overflow-hidden">
                                    <div class="block-header panel pt-1 border-top">
                                        <h2
                                            class="h6 ft-tertiary fw-bold ls-0 text-uppercase m-0 text-black dark:text-white">
                                            <a class="hstack d-inline-flex gap-0 text-none hover:text-primary duration-150"
                                                href="{{ url('topics/' . $topic->slug ?? '') }}"
                                                title="{{ $topic->name ?? '' }}">
                                                <span>{{ $topic->name }}</span>
                                                <i class="icon-1 fw-bold unicon-chevron-right"></i>
                                            </a>
                                        </h2>
                                    </div>
                                    <div class="block-content">
                                        <div class="row child-cols-12 g-2 lg:g-4 sep-x" data-uc-grid>
                                            @foreach ($topic->posts as $post)
                                                <article class="post type-post panel uc-transition-toggle">
                                                    <div class="row child-cols g-2 lg:g-3" data-uc-grid>
                                                        <div>
                                                            <div class="post-header panel vstack justify-between gap-1">
                                                                <h3
                                                                    class="post-title h6 m-0 text-truncate-2 hover:text-primary">
                                                                    <a class="text-none duration-150"
                                                                        href="{{ url('posts/' . $post->slug) }}"
                                                                        title="{{ $post->title }}">{{ $post->title ?? '' }}</a>
                                                                </h3>
                                                                <div
                                                                    class="post-date d-flex gap-narrow fs-7 mb-0 text-gray-900 dark:text-white text-opacity-60 justify-between">
                                                                    <span
                                                                        title="{{ $post->publish_date_news }}">{{ $post->publish_date ?? $post->pubdate }}</span>
                                                                    <a href="{{ url('channels/' . $post->channel_slug) }}"
                                                                        class="post-comments text-none hstack gap-narrow"
                                                                        title="{{ $post->name }}">
                                                                        <span class="ms-auto">{{ $post->name }}</span>
                                                                    </a>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="col-auto">
                                                            <div
                                                                class="post-media panel overflow-hidden max-w-72px min-w-72px">
                                                                <div
                                                                    class="featured-image bg-gray-25 dark:bg-gray-800 ratio ratio-1x1">
                                                                    <a href="{{ url('posts/' . $post->slug) }}"
                                                                        class="position-cover">
                                                                        @if ($post->type == 'video' || $post->type == 'youtube' || $post->type == 'audio')
                                                                            <img class="media-cover image uc-transition-scale-up uc-transition-opaque lazy-img"
                                                                                src="data:image/gif;base64,R0lGODlhAQABAIAAAAAAAP///yH5BAEAAAAALAAAAAABAAEAAAIBRAA7"
                                                                                data-src="{{ $post->image ?? $defaultImage }}"
                                                                                alt="{{ $post->title ?? '' }}"
                                                                                title="{{ $post->title ?? '' }}"
                                                                                loading="lazy">
                                                                        @elseif($post->type == 'post')
                                                                            <img class="media-cover image uc-transition-scale-up uc-transition-opaque lazy-img"
                                                                                src="data:image/gif;base64,R0lGODlhAQABAIAAAAAAAP///yH5BAEAAAAALAAAAAABAAEAAAIBRAA7"
                                                                                data-src="{{ $post->image ?? $defaultImage }}"
                                                                                alt="{{ $post->title ?? '' }}"
                                                                                title="{{ $post->title ?? '' }}"
                                                                                loading="lazy">
                                                                        @endif
                                                                    </a>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </article>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- E-News Paper --}}
    @if (!$enewspapers->isEmpty())
        <div class="section panel overflow-hidden swiper-parent">
            <div class="section-panel outer-panel py-4 lg-py-6 dark:text-white">
                <div class="container max-w-xl">
                    <div class="section-inner panel vstack gap-2">
                        <div class="block-panel layout carousel-panel layout vstack gap-2 lg-gap-3 panel">
                            <div class="block-panel header-panel pt-1 border-top">
                                <h2
                                    class="story-title h6 ft-tertiary fw-bold ls-0 text-uppercase m-0 text-black dark:text-white border-none p-0">
                                    {{ __('frontend-labels.enewspapers.title') }}

                                    <div class="block-header panel pt-1">
                                        <h2
                                            class="h6 ft-tertiary fw-bold ls-0 text-uppercase m-0 text-black dark:text-white">
                                            <a class="hstack d-inline-flex gap-0 text-none hover:text-primary duration-150"
                                                href={{ route('e-newspaper.index') }}>
                                                <span>{{ __('frontend-labels.home.read_all') }}</span>
                                                <i class="icon-1 fw-bold unicon-chevron-right"></i>
                                            </a>
                                        </h2>
                                    </div>
                                </h2>
                            </div>

                            <!-- Container for image and text overlay -->
                            <div class="swiper-slide swiper-slide-visible swiper-slide-fully-visible swiper-slide-active"
                                role="group">
                                @if (isset($getEnewsSettings))
                                    <article class="post type-post">
                                        <div class="featured-image epaper_css ratio ratio-1x1 sm:ratio-16x9">
                                            <img class="media-cover image epaper_css opacity-15 lazy-img"
                                                src="data:image/gif;base64,R0lGODlhAQABAIAAAAAAAP///yH5BAEAAAAALAAAAAABAAEAAAIBRAA7"
                                                data-src="{{ $getEnewsSettings['paperimage'] }}"
                                                alt="The Art of Baking: From Classic Bread to Artisan Pastries"
                                                loading="lazy">

                                        </div>
                                        <div
                                            class="post-content py-2 sm:py-4 md:py-6 xl:py-8 md:px-4 xl:px-6 position-absolute top-0 start-0 end-0 bottom-0 z-1 vstack justify-center items-center gap-2 text-center  bg-opacity-25">
                                            <h1 class="h5 md:h3 xl:h1 max-w-2/3 md:max-w-sm my-0 mx-auto  "><a
                                                    href="{{ route('e-newspaper.index') }}"
                                                    class="text-none text-black dark:text-white">{{ $getEnewsSettings['papertitle'] }}</a>
                                            </h1>
                                        </div>
                                    </article>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="section panel overflow-hidden swiper-parent mb-4">
            <div class="section-panel outer-panel lg-py-6 dark:text-white">
                <div class="container max-w-xl">
                    <div class="section-inner panel vstack gap-2">
                        <div class="block-panel layout carousel-panel layout vstack gap-2 lg-gap-3 panel ">
                            <div class="block-content overflow-hidden">
                                <div
                                    class="row child-cols-12 sm:child-cols-8 lg:child-cols-4 col-match gy-4 xl:gy-6 gx-2 sm:gx-4">
                                    @foreach ($enewspapers as $enewspaper)
                                        <div>
                                            <article
                                                class="post type-post panel vstack overflow-hidden h-100 text-gray-900 dark:text-white bg-white dark:bg-gray-900 rounded-1">
                                                <div class="post-media panel overflow-hidden">
                                                    <figure
                                                        class="featured-image m-0 ratio ratio-16x9 bg-gray-50 uc-transition-toggle overflow-hidden bg-gray-25 dark:bg-gray-800">
                                                        <img class="media-cover image uc-transition-scale-up uc-transition-opaque lazy-img"
                                                            src="data:image/gif;base64,R0lGODlhAQABAIAAAAAAAP///yH5BAEAAAAALAAAAAABAAEAAAIBRAA7"
                                                            data-src="{{ asset('storage/' . $enewspaper->thumbnail) }}"
                                                            alt="image" loading="lazy">
                                                        @if ($dailyLimitReached)
                                                            <!-- Daily limit reached: redirect to e-newspaper page -->
                                                            <a href="{{ url('e-newspaper') }}" target="_self"
                                                                class="position-cover" data-caption="image"></a>
                                                        @else
                                                            <a href="{{ route('e-newspaper.pdf', ['id' => $enewspaper->id]) }}"
                                                                class="position-cover" data-caption="image"
                                                                target="_blank"></a>
                                                        @endif
                                                    </figure>
                                                </div>
                                                <div class="post-header panel vstack justify-between ">
                                                    <div
                                                        class="post-meta panel fs-7 fw-medium text-gray-900 dark:text-white text-opacity-60">
                                                        <div class="meta">
                                                            <div class="d-flex justify-between gap-2 py-2 ps-1">
                                                                <div>
                                                                    <div class="d-flex gap-1">
                                                                        <a href="{{ url('channels/' . $enewspaper->channel->slug) }}"
                                                                            title="{{ $enewspaper->channel->name }}"><img
                                                                                data-src="{{ url('storage/images/' . $enewspaper->channel->logo) }}" src="data:image/gif;base64,R0lGODlhAQABAIAAAAAAAP///yH5BAEAAAAALAAAAAABAAEAAAIBRAA7"
                                                                                alt="Channel Logo" class="h-20px lazy-img" width="20" height="20"></a>
                                                                        <a href="{{ url('channels/' . $enewspaper->channel->slug) }}"
                                                                            class="text-black h6 dark:text-white text-none fw-bold"
                                                                            title="{{ $enewspaper->channel->name }}">{{ $enewspaper->channel->name }}</a>
                                                                    </div>
                                                                </div>
                                                                <div>
                                                                    <div class="post-date hstack gap-narrow">
                                                                        <span
                                                                            title="{{ $enewspaper->date }}">{{ $enewspaper->date }}</span>

                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="actions">
                                                                <div class="hstack gap-1"></div>
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
            </div>
        </div>
    @endif
    {{-- End E-News Paper --}}

    @if (!$magazines->isEmpty())
        <div class="section panel overflow-hidden swiper-parent">
            <div class="section-panel outer-panel lg-py-6 dark:text-white">
                <div class="container max-w-xl">
                    <div class="panel vstack gap-4 lg:gap-6 xl:gap-8">
                        <div class="block-panel header-panel  border-top">
                            <h2
                                class="story-title h6 ft-tertiary mt-3 mb-0 fw-bold ls-0 text-uppercase m-0 text-black dark:text-white border-none p-0">
                                {{ __('frontend-labels.magazines.title') }}

                                <div class="block-header panel">
                                    <h2 class="h6 ft-tertiary fw-bold ls-0 text-uppercase m-0 text-black dark:text-white">
                                        <a class="hstack d-inline-flex gap-0 text-none hover:text-primary duration-150"
                                            href="{{ route('e-magazine.index') }}">
                                            <span>{{ __('frontend-labels.home.read_all') }}</span>
                                            <i class="icon-1 fw-bold unicon-chevron-right"></i>
                                        </a>
                                    </h2>
                                </div>
                            </h2>
                        </div>
                        <div class="shop-lisiting row child-cols-6 lg:child-cols-3 col-match gy-4 lg:gy-8 gx-2 lg:gx-4">
                            @foreach ($magazines as $magazine)
                                <div>
                                    <article class="product type-product panel">
                                        <div class="vstack gap-2">
                                            <div class="panel">
                                                <figure
                                                    class="featured-image-magazine m-0 ratio ratio-3x4 overflow-hidden uc-transition-toggle bg-gray-25 dark:bg-gray-800">
                                                    <img class="media-cover image uc-transition-scale-up uc-transition-opaque lazy-img"
                                                        src="data:image/gif;base64,R0lGODlhAQABAIAAAAAAAP///yH5BAEAAAAALAAAAAABAAEAAAIBRAA7"
                                                        data-src="{{ asset('storage/' . $magazine->thumbnail) }}"
                                                        alt="image" loading="lazy">
                                                    @if ($dailyLimitReached)
                                                        <a href="{{ url('e-magazine') }}" target="_self"
                                                            class="position-cover" data-caption="image"></a>
                                                    @else
                                                        <a href="{{ route('e-magazine.pdf', ['id' => $magazine->id]) }}"
                                                            class="position-cover" data-caption="{{ $magazine->title }}"
                                                            target="_blank"></a>
                                                    @endif
                                                </figure>
                                            </div>
                                        </div>
                                        <div class="post-header panel vstack justify-between mt-2">
                                            <div class="d-none md:d-block">
                                                <div class="meta">
                                                    <div class="d-flex justify-between gap-2">
                                                        <div>
                                                            <div class="d-flex gap-1">
                                                                <a href="{{ url('channels/' . $magazine->channel->slug) }}"
                                                                    title="{{ $magazine->channel->name }}"><img
                                                                        data-src="{{ url('storage/images/' . $magazine->channel->logo) }}" src="data:image/gif;base64,R0lGODlhAQABAIAAAAAAAP///yH5BAEAAAAALAAAAAABAAEAAAIBRAA7"
                                                                        alt="Channel Logo" class="h-20px lazy-img" width="20" height="20"></a>
                                                                <a href="{{ url('channels/' . $magazine->channel->slug) }}"
                                                                    class="post-comments text-none hstack gap-narrow channel-button"
                                                                    title="ABP Live">
                                                                    <span
                                                                        class="ms-auto">{{ $magazine->channel->name }}</span>
                                                                </a>
                                                            </div>
                                                        </div>

                                                        <div class="post-date hstack gap-narrow">
                                                            <span
                                                                title="{{ $magazine->date }}">{{ $magazine->date ?? $magazine->date }}</span>

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
                    </div>
                </div>
            </div>
        </div>
    @endif
    {{-- End Magazine Section --}}

    <!-- Most Read Section start -->
    <div class="section panel overflow-hidden swiper-parent">
        <div class="section-outer panel py-4 lg:py-6 dark:text-white">
            <div class="container max-w-xl">
                <div class="section-inner panel vstack gap-2">
                    <div class="block-layout carousel-layout vstack gap-2 lg:gap-3 panel">
                        <div class="block-header panel pt-1 border-top">
                            <h2 class="h6 ft-tertiary fw-bold ls-0 text-uppercase m-0 text-black dark:text-white">
                                {{ __('frontend-labels.home.most_read') }}</h2>
                        </div>
                        <div class="block-content panel">
                            <div class="swiper" id="most-read-swiper"
                                data-uc-swiper="items: 2; gap: 16; dots: .dot-nav; next: .nav-next; prev: .nav-prev; disable-class: disabled;"
                                data-uc-swiper-s="items: 3; gap: 24;" data-uc-swiper-l="items: 5; gap: 24;">
                                <div class="swiper-wrapper">
                                    @if (!empty($mostReads))
                                        @foreach ($mostReads as $mostRead)
                                            <div class="swiper-slide" data-post-id="{{ $mostRead->id }}">
                                                <div>
                                                    <article
                                                        class="post type-post panel uc-transition-toggle vstack gap-2">
                                                        <div class="post-media panel overflow-hidden">
                                                            <div
                                                                class="featured-image bg-gray-25 dark:bg-gray-800 ratio ratio-3x2">
                                                                @if ($mostRead->type == 'video' || $mostRead->type == 'youtube')
                                                                    <a href="{{ url('posts/' . $mostRead->slug) }}"
                                                                        class="position-cover">
                                                                        <img class="media-cover image uc-transition-scale-up uc-transition-opaque lazy-img"
                                                                            src="data:image/gif;base64,R0lGODlhAQABAIAAAAAAAP///yH5BAEAAAAALAAAAAABAAEAAAIBRAA7"
                                                                            data-src="{{ $mostRead->video_thumb ?? $defaultImage }}"
                                                                            alt="{{ $mostRead->title ?? '' }}"
                                                                            title="{{ $mostRead->title ?? '' }}"
                                                                            loading="lazy">
                                                                        <div
                                                                            class="post-category hstack gap-narrow justify-center align-items-center text-white">
                                                                            <a class="text-none"
                                                                                href="{{ url('posts/' . $mostRead->slug) }}"
                                                                                title="{{ $mostRead->title }}"><i
                                                                                    class="bi bi-play-circle font-size-45"></i></a>
                                                                        </div>
                                                                    </a>
                                                                @elseif($mostRead->type == 'post')
                                                                    <a href="{{ url('posts/' . $mostRead->slug) }}"
                                                                        class="position-cover">
                                                                        <img class="media-cover image uc-transition-scale-up uc-transition-opaque lazy-img"
                                                                            src="data:image/gif;base64,R0lGODlhAQABAIAAAAAAAP///yH5BAEAAAAALAAAAAABAAEAAAIBRAA7"
                                                                            data-src="{{ $mostRead->image ?? $defaultImage }}"
                                                                            alt="{{ $mostRead->title ?? '' }}"
                                                                            title="{{ $mostRead->title ?? '' }}"
                                                                            loading="lazy">
                                                                    </a>
                                                                @elseif($mostRead->type == 'audio')
                                                                    <a href="{{ url('posts/' . $mostRead->slug) }}"
                                                                        class="position-cover">
                                                                        <img class="media-cover image uc-transition-scale-up uc-transition-opaque lazy-img"
                                                                            src="data:image/gif;base64,R0lGODlhAQABAIAAAAAAAP///yH5BAEAAAAALAAAAAABAAEAAAIBRAA7"
                                                                            data-src="{{ $mostRead->image ?? $defaultImage }}"
                                                                            alt="{{ $mostRead->title ?? '' }}"
                                                                            title="{{ $mostRead->title ?? '' }}"
                                                                            loading="lazy">
                                                                        <div
                                                                            class="post-category hstack gap-narrow justify-center align-items-center text-white">
                                                                            <a class="text-none"
                                                                                href="{{ url('posts/' . $mostRead->slug) }}"
                                                                                title="{{ $mostRead->title }}"><i
                                                                                    class="bi bi-play-circle font-size-45"></i></a>
                                                                        </div>
                                                                    </a>
                                                                @endif
                                                            </div>
                                                        </div>
                                                        <div class="post-header panel vstack gap-1">
                                                            <h3
                                                                class="post-title h6 m-0 text-truncate-2 hover:text-primary">
                                                                <a class="text-none duration-150"
                                                                    href="{{ url('posts/' . $mostRead->slug) }}"
                                                                    title="{{ $mostRead->title ?? '' }}">{{ $mostRead->title ?? '' }}</a>
                                                            </h3>
                                                            <div
                                                                class="post-meta panel hstack justify-start gap-1 fs-7 ft-tertiary fw-medium text-gray-900 dark:text-white text-opacity-60 d-none md:d-flex z-1 d-none md:d-block">
                                                                <div>
                                                                    <div class="post-date hstack gap-narrow">
                                                                        <a href="{{ url('channels/' . $mostRead->channel->slug) }}"
                                                                            class="post-comments text-none hstack gap-narrow channel-button"
                                                                            title="{{ $mostRead->channel->name ?? '' }}">
                                                                            <span>{{ $mostRead->channel->name ?? '' }}</span>
                                                                        </a>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div
                                                                class="post-meta panel hstack justify-between gap-1 fs-7 ft-tertiary fw-medium text-gray-900 dark:text-white text-opacity-60 d-none md:d-flex z-1 d-none md:d-block">
                                                                <div>
                                                                    <div class="post-date hstack gap-narrow">
                                                                        <span
                                                                            title="{{ $mostRead->publish_date_news }}">{{ $mostRead->publish_date ?? $mostRead->pubdate }}</span>
                                                                    </div>
                                                                </div>
                                                                <div>
                                                                    <a href="{{ url('posts/' . $mostRead->slug) }}#comment-form"
                                                                        class="post-comments text-none hstack gap-narrow"
                                                                        title="Comments">
                                                                        <i class="icon-narrow unicon-chat"
                                                                            title="Comments"></i>
                                                                        <span
                                                                            title="Comments">{{ $mostRead->comment }}</span>
                                                                    </a>
                                                                </div>
                                                                <div title="Views">
                                                                    <i class="bi bi-eye fs-5"></i>
                                                                    <span>{{ $mostRead->view_count }}</span>
                                                                </div>
                                                                <div title="Reaction">
                                                                    <i class="bi bi-heart-fill "></i>
                                                                    <span>{{ $mostRead->reaction }}</span>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </article>
                                                </div>
                                            </div>
                                        @endforeach
                                    @endif
                                </div>
                            </div>
                            <div
                                class="swiper-nav nav-prev position-absolute top-50 start-0 translate-middle btn btn-alt-primary text-black rounded-circle p-0 border shadow-xs w-32px lg:w-40px h-32px lg:h-40px z-1">
                                <i class="icon-1 unicon-chevron-left"></i>
                            </div>
                            <div
                                class="swiper-nav nav-next position-absolute top-50 start-100 translate-middle btn btn-alt-primary text-black rounded-circle p-0 border shadow-xs w-32px lg:w-40px h-32px lg:h-40px z-1">
                                <i class="icon-1 unicon-chevron-right"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Audio Post Section start -->
    @if (!$audioPosts->isEmpty())
        <div id="live_now" class="live_now section panel uc-dark swiper-parent">
            <div class="section-outer panel py-4 lg:py-6 bg-gray-900 text-white mt-5">
                <div class="container max-w-xl">
                    <div class="block-header panel pt-1 border-top">
                        <h2
                            class="story-title h6 ft-tertiary fw-bold ls-0 text-uppercase m-0 text-black dark:text-white border-none p-0">
                            {{ __('frontend-labels.newsaudios.title') }}
                            <div class="block-header panel pt-1">
                                <h2 class="h6 ft-tertiary fw-bold ls-0 text-uppercase m-0 text-black dark:text-white">
                                    <a class="hstack d-inline-flex gap-0 text-none hover:text-primary duration-150"
                                        href="{{ route('audios.frontend.index') }}">
                                        <span>{{ __('frontend-labels.home.read_all') }}</span>
                                        <i class="icon-1 fw-bold unicon-chevron-right"></i>
                                    </a>
                                </h2>
                            </div>
                        </h2>
                    </div>

                    <div class="row g-3 mt-2">
                        @foreach ($audioPosts as $audioPost)
                            <div class="col-md-6">
                                <div class="panel overflow-hidden rounded">
                                    <div id="audio-container">
                                        <a class="text-none duration-150" href="{{ url('posts/' . $audioPost->slug) }}"
                                            title="{{ $audioPost->title ?? '' }}">

                                            <div class="audio-list-item mb-3 border rounded-3 overflow-hidden shadow-sm audio-list-item-hover"
                                                data-id="{{ $audioPost->id }}">
                                                <div class="d-flex align-items-center p-3">
                                                    <div class="position-relative flex-shrink-0 me-3">
                                                        <img data-src="{{ $audioPost->image ?? asset('assets/images/no_image_available.png') }}" src="data:image/gif;base64,R0lGODlhAQABAIAAAAAAAP///yH5BAEAAAAALAAAAAABAAEAAAIBRAA7"
                                                            class="rounded shadow audio-thumbnail lazy-img"
                                                            alt="{{ $audioPost->title ?? 'Audio Post' }}"
                                                            onerror="this.onerror=null; this.src='{{ asset('assets/images/no_image_available.png') }}';">

                                                        <div class="position-absolute top-50 start-50 translate-middle">
                                                            <div
                                                                class="rounded-circle d-flex align-items-center justify-content-center shadow-lg audio-play-overlay">
                                                                <svg xmlns="http://www.w3.org/2000/svg" width="30"
                                                                    height="30" viewBox="0 0 24 24"
                                                                    fill="currentColor" class="text-black">
                                                                    <path d="M8 5v14l11-7z"></path>
                                                                </svg>
                                                            </div>
                                                        </div>

                                                    </div>

                                                    <div class="flex-grow-1 overflow-hidden">
                                                        <h5 class="mb-1 text-truncate-1 fw-bold">
                                                            {{ $audioPost->title }}
                                                        </h5>
                                                        <p
                                                            class="post-excrept ft-tertiary fs-6 text-gray-900 dark:text-white text-opacity-60 text-truncate-2 my-1">

                                                            {{ strip_tags(html_entity_decode($audioPost->description)) }}
                                                        </p>
                                                        <i class="bi bi-eye fs-5 ms-1" title="Views"></i>
                                                        <span title="Views">{{ $audioPost->view_count }}</span>

                                                        <i class="bi bi-heart-fill ms-1" title="Reaction"></i>
                                                        <span title="Views">{{ $audioPost->reaction }}</span>
                                                    </div>

                                                </div>
                                            </div>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div> {{-- row end --}}

                    <!-- Load More Section -->
                    @if ($audioPost->count() >= 4)
                        <div class="text-center mt-4 pt-2">
                            <a href="{{ route('audios.frontend.index') }}"
                                class="btn btn-lg btn-outline-primary rounded-pill px-4 px-md-5 py-2">
                                <i class="icon-narrow unicon-music me-2"></i>
                                <span
                                    class="d-none d-sm-inline">{{ __('frontend-labels.newsaudios.explore_more_audio_content') }}</span>
                                <span class="d-inline d-sm-none">{{ __('frontend-labels.newsaudios.more_audio') }}</span>
                                <i class="icon-narrow unicon-arrow-right ms-2"></i>
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    @endif
    <!-- Audio Post Section end -->

    <!-- WebStory Section Starts -->
    @if (!$stories->isEmpty())
        <div class="section panel overflow-hidden swiper-parent">
            <div class="section-outer panel py-4 lg:py-6 dark:text-white">
                <div class="container max-w-xl">
                    <div class="section-inner panel vstack gap-2">
                        <div class="block-header panel pt-1 border-top">
                            <h2
                                class="story-title h6 ft-tertiary fw-bold ls-0 text-uppercase m-0 text-black dark:text-white border-none p-0">
                                {{ __('frontend-labels.web_stories.title') }}

                                <div class="block-header panel pt-1">
                                    <h2 class="h6 ft-tertiary fw-bold ls-0 text-uppercase m-0 text-black dark:text-white">
                                        <a class="hstack d-inline-flex gap-0 text-none hover:text-primary duration-150"
                                            href="{{ url('webstories') }}">
                                            <span>{{ __('frontend-labels.home.read_all') }}</span>
                                            <i class="icon-1 fw-bold unicon-chevron-right"></i>
                                        </a>
                                    </h2>
                                </div>
                            </h2>
                        </div>
                        <div class="block-content panel">
                            <div class="swiper swiper-main swiper-active-visibility h-100 swiper-initialized swiper-horizontal swiper-watch-progress" id="web-stories-swiper"
                                data-uc-swiper="items: 1.25; active: 2; gap: 2; center: true; center-bounds: true; disable-class: d-none;"
                                data-uc-swiper-s="items: 4;" data-uc-swiper-l="items: 5;">
                                <div class="swiper-wrapper">
                                    @if (!empty($stories))
                                        @foreach ($stories as $story)
                                            <div class="swiper-slide px-1" data-post-id="{{ $story->id }}">
                                                <div class="card bg-white dark:bg-gray-800 d-flex flex-column"
                                                    id="card_style">

                                                    <a href="{{ url('webstories/' . $story->topic->slug . '/' . $story->slug) }}"
                                                        target="_blank" class="position-relative d-block">
                                                        @if ($story && $story->story_slides->isNotEmpty() && $story->story_slides->first()->image)
                                                            <img src="data:image/gif;base64,R0lGODlhAQABAIAAAAAAAP///yH5BAEAAAAALAAAAAABAAEAAAIBRAA7"
                                                                data-src="{{ asset('storage/' . $story->story_slides->first()->image) }}"
                                                                target="_blank" class="card-img-top lazy-img"
                                                                alt="{{ $story->title }}">
                                                        @else
                                                            <img src="data:image/gif;base64,R0lGODlhAQABAIAAAAAAAP///yH5BAEAAAAALAAAAAABAAEAAAIBRAA7"
                                                                data-src="{{ asset('storage/default.jpg') }}"
                                                                class="card-img-top lazy-img" alt="Default Image">
                                                        @endif
                                                        <div
                                                            class="story-progress-container position-absolute bottom-0 start-0 w-100 px-1 pb-2">
                                                            <div class="progress-segments d-flex gap-1">
                                                                @foreach ($story->story_slides as $slide)
                                                                    <div
                                                                        class="progress-segment flex-grow-1 h-1 bg-white bg-opacity-50 story-dashed-css">
                                                                    </div>
                                                                @endforeach
                                                            </div>
                                                        </div>
                                                        <span
                                                            class="visual-stories-icon position-absolute top-2 end-1 p-1 rounded-circle dark:text-white text-white bg-gray-800">
                                                            <svg xmlns="http://www.w3.org/2000/svg" width="24"
                                                                height="24" viewBox="0 0 24 24">
                                                                <path fill="currentColor"
                                                                    d="M7 20V4h10v16zm-4-2V6h2v12zm16 0V6h2v12z" />
                                                            </svg>
                                                        </span>
                                                    </a>
                                                    <div id="card_title"
                                                        class="card-footer text-gray-900 dark:text-white d-flex flex-column h-100">
                                                        <h3 class="post-title h6 m-0 text-truncate-2 hover:text-primary">
                                                            <a class="text-none duration-150" target="_blank"
                                                                href="{{ url('webstories/' . $story->topic->slug . '/' . $story->slug) }}"
                                                                title="{{ $story->title ?? '' }}">
                                                                {{ $story->title ?? '' }}
                                                            </a>
                                                        </h3>
                                                        <div class="mt-2 text-muted fs-7">
                                                            {{ $story->created_at->diffForHumans() }}
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                    @endif
                                </div>
                                <div
                                    class="swiper-nav swiper-prev btn btn-xs md:btn-sm p-0 btn btn-alt-primary w-24px md:w-32px xl:w-40px h-24px md:h-32px xl:h-40px bg-white dark:bg-gray-900 text-dark dark:text-white rounded-circle shadow-sm position-absolute top-50 start-0 translate-middle-y z-1">
                                    <i class="unicon-chevron-left icon-xs md:icon-1"></i>
                                </div>
                                <div
                                    class="swiper-nav swiper-next btn btn-xs md:btn-sm p-0 btn btn-alt-primary w-24px md:w-32px xl:w-40px h-24px md:h-32px xl:h-40px bg-white dark:bg-gray-900 text-dark dark:text-white rounded-circle shadow-sm position-absolute top-50 end-0 translate-middle-y z-1">
                                    <i class="unicon-chevron-right icon-xs md:icon-1"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif
    {{-- WebStory Section Ends  --}}

    <!-- Section start -->
    <div class="section panel overflow-hidden">
        <div class="section-outer panel">
            <div class="container max-w-xl">
                <div class="section-inner">
                    <div class="row child-cols-12 lg:child-cols g-4 lg:g-4 col-match" data-uc-grid>
                        @foreach ($frontTopics as $index => $topic)
                            @if ($index < 3 || $index > 5 || $topic->posts->isEmpty())
                                @continue
                            @endif
                            <div class="lg:col-4 order-1">
                                <div class="block-layout grid-layout vstack gap-2 lg:gap-3 panel overflow-hidden">
                                    <div class="block-header panel pt-1 border-top">
                                        <h2
                                            class="h6 ft-tertiary fw-bold ls-0 text-uppercase m-0 text-black dark:text-white hover:text-primary">
                                            <a class="hstack d-inline-flex gap-0 text-none duration-150"
                                                href="{{ url('topics/' . $topic->slug ?? '') }}"
                                                title="{{ $topic->name ?? '' }}">
                                                <span>{{ $topic->name ?? '' }}</span>
                                                <i class="icon-1 fw-bold unicon-chevron-right"></i>
                                            </a>
                                        </h2>
                                    </div>
                                    <div class="block-content">
                                        <div class="row child-cols-12 g-2 lg:g-4 sep-x" data-uc-grid>
                                            @foreach ($topic->posts as $post)
                                                <div>
                                                    <article class="post type-post panel uc-transition-toggle">
                                                        <div class="row child-cols g-2 lg:g-3" data-uc-grid>
                                                            <div>
                                                                <div
                                                                    class="post-header panel vstack justify-between gap-1">
                                                                    <h3
                                                                        class="post-title h6 m-0 text-truncate-2 hover:text-primary">
                                                                        <a class="text-none duration-150"
                                                                            href="{{ url('posts/' . $post->slug) }}"
                                                                            title="{{ $post->title ?? '' }}">
                                                                            {{ $post->title ?? '' }}
                                                                        </a>
                                                                    </h3>
                                                                    <div
                                                                        class="post-date d-flex gap-narrow fs-7 mb-0 text-gray-900 dark:text-white text-opacity-60 justify-between">
                                                                        <span
                                                                            title="{{ $post->publish_date_news }}">{{ $post->publish_date ?? $post->pubdate }}</span>
                                                                        <a href="{{ url('channels/' . $post->channel_slug) }}"
                                                                            class="post-comments text-none hstack gap-narrow channel-button"
                                                                            title="{{ $post->name ?? '' }}">
                                                                            <span
                                                                                class="ms-auto">{{ $post->name ?? '' }}</span>
                                                                        </a>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="col-auto">
                                                                <div
                                                                    class="post-media panel overflow-hidden max-w-72px min-w-72px">
                                                                    <div
                                                                        class="featured-image bg-gray-25 dark:bg-gray-800 ratio ratio-1x1">
                                                                        <a href="{{ url('posts/' . $post->slug) }}"
                                                                            class="position-cover">
                                                                            <img class="media-cover image uc-transition-scale-up uc-transition-opaque lazy-img"
                                                                                src="data:image/gif;base64,R0lGODlhAQABAIAAAAAAAP///yH5BAEAAAAALAAAAAABAAEAAAIBRAA7"
                                                                                data-src="{{ $post->image ?? $defaultImage }}"
                                                                                alt="{{ $post->title ?? '' }}"
                                                                                title="{{ $post->title ?? '' }}"
                                                                                loading="lazy">
                                                                        </a>
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
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Section end -->

    <!-- Topic  Section start -->
    <div class="section panel overflow-hidden">
        <div class="section-outer panel dark:text-white">
            <div class="container max-w-xl">
                <div class="section-inner">
                    <div class="row child-cols-12 lg:child-cols g-4 lg:g-6 col-match" data-uc-grid>
                        @if ($frontTopics->isNotEmpty())
                            @foreach ($frontTopics as $index => $topic)
                                @if ($index < 6 || $index > 8 || $topic->posts->isEmpty())
                                    @continue
                                @endif
                                <div class="lg:col-4">
                                    <div class="block-layout list-layout vstack gap-2 lg:gap-3 panel overflow-hidden">
                                        <div class="block-header panel pt-1 border-top">
                                            <h2
                                                class="h6 ft-tertiary fw-bold ls-0 text-uppercase m-0 text-black dark:text-white">
                                                <a class="hstack d-inline-flex gap-0 text-none hover:text-primary duration-150"
                                                    href="{{ url('topics/' . $topic->slug ?? '') }}">
                                                    <span>{{ $topic->name ?? '' }}</span>
                                                    <i class="icon-1 fw-bold unicon-chevron-right"></i>
                                                </a>
                                            </h2>
                                        </div>
                                        <div class="block-content">
                                            <div class="row child-cols-12 g-2 lg:g-4 sep-x" data-uc-grid>
                                                <div>
                                                    @foreach ($topic->posts as $index => $post)
                                                        @if ($index == 1)
                                                            @break
                                                        @endif
                                                        <article
                                                            class="post type-post panel uc-transition-toggle vstack gap-2 lg:gap-3 overflow-hidden uc-dark">
                                                            <div class="post-media panel overflow-hidden">
                                                                <div
                                                                    class="featured-image bg-gray-25 dark:bg-gray-800 ratio ratio-4x3">
                                                                    <img class="media-cover image uc-transition-scale-up uc-transition-opaque lazy-img"
                                                                        title="{{ $post->title }}"
                                                                        src="data:image/gif;base64,R0lGODlhAQABAIAAAAAAAP///yH5BAEAAAAALAAAAAABAAEAAAIBRAA7"
                                                                        data-src="{{ $post->image ?? $defaultImage }}"
                                                                        alt="{{ $post->title }}">
                                                                </div>
                                                            </div>
                                                            <div
                                                                class="position-cover bg-gradient-to-t from-black to-transparent opacity-90">
                                                            </div>
                                                            <div
                                                                class="post-header panel vstack justify-start items-start flex-column-reverse gap-1 p-2 position-cover text-white">
                                                                <div
                                                                    class="post-meta panel hstack justify-between fs-7 text-white text-opacity-60 mt-1">
                                                                    <div class="meta">
                                                                        <div class="hstack gap-2">
                                                                            <div>
                                                                                <a href="{{ url('channels/' . $post->channel_slug) }}"
                                                                                    class="post-comments text-none hstack gap-narrow channel-button"
                                                                                    title="{{ $post->name }}">
                                                                                    <span>{{ $post->name }}</span>
                                                                                </a>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                    <div class="actions">
                                                                        <div class="hstack gap-1">
                                                                            <a href="{{ url('posts/' . $post->slug) }}#post_comment"
                                                                                class="post-comments text-none hstack gap-narrow">
                                                                                <i class="icon-narrow unicon-chat"
                                                                                    title="Comments"></i>
                                                                                <span
                                                                                    title="Comments">{{ $post->comment }}</span>

                                                                                <i class="bi bi-eye fs-5 ms-1"
                                                                                    title="Views"></i>
                                                                                <span
                                                                                    title="Views">{{ $post->view_count }}</span>

                                                                                <div title="Reaction">
                                                                                    <i class="bi bi-heart-fill ms-1"></i>
                                                                                    <span>{{ $post->reaction }}</span>
                                                                                </div>
                                                                            </a>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                <h3
                                                                    class="post-title h6 lg:h5 m-0 m-0 max-w-600px text-white text-truncate-2">
                                                                    <a class="text-none text-white"
                                                                        href="{{ url('posts/' . $post->slug) }}"
                                                                        title="{{ $post->title }}">{{ $post->title }}</a>
                                                                </h3>

                                                                <div
                                                                    class="post-date hstack gap-narrow fs-7 text-gray-900 dark:text-white text-opacity-60 d-none md:d-flex">
                                                                    <span
                                                                        title="{{ $post->publish_date_news }}">{{ $post->publish_date ?? $post->pubdate }}</span>
                                                                </div>
                                                            </div>
                                                        </article>
                                                    @endforeach
                                                </div>
                                                @foreach ($topic->posts as $index => $post)
                                                    @if ($index < 1 || $index > 3)
                                                        @continue
                                                    @endif
                                                    <div>
                                                        <article class="post type-post panel uc-transition-toggle">
                                                            <div class="row child-cols g-2 lg:g-3" data-uc-grid>
                                                                <div>
                                                                    <div
                                                                        class="post-header panel vstack justify-between gap-1">
                                                                        <h3
                                                                            class="post-title h6 m-0 text-truncate-2 hover:text-primary">
                                                                            <a class="text-none duration-150"
                                                                                href="{{ url('posts/' . $post->slug) }}"
                                                                                title="{{ $post->title }}">{{ $post->title }}</a>
                                                                        </h3>
                                                                        <div
                                                                            class="post-date d-flex gap-narrow fs-7 text-gray-900 dark:text-white text-opacity-60 justify-between">

                                                                            <span
                                                                                title="{{ $post->publish_date_news }}">{{ $post->publish_date ?? $post->pubdate }}</span>
                                                                            <a href="{{ url('channels/' . $post->channel_slug) }}"
                                                                                class="post-comments text-none hstack gap-narrow channel-button"
                                                                                title="{{ $post->name }}">
                                                                                <span
                                                                                    class="ms-auto">{{ $post->name }}</span>
                                                                            </a>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                <div class="col-auto">
                                                                    <div
                                                                        class="post-media panel overflow-hidden max-w-72px min-w-72px">
                                                                        <div
                                                                            class="featured-image bg-gray-25 dark:bg-gray-800 ratio ratio-1x1">
                                                                            <a href="{{ url('posts/' . $post->slug) }}"
                                                                                class="position-cover">
                                                                                <img class="media-cover image uc-transition-scale-up uc-transition-opaque lazy-img"
                                                                                    src="data:image/gif;base64,R0lGODlhAQABAIAAAAAAAP///yH5BAEAAAAALAAAAAABAAEAAAIBRAA7"
                                                                                    data-src="{{ $post->image ?? $defaultImage }}"
                                                                                    alt="Tech Innovations Reshaping the Retail Landscape: AI Payments"
                                                                                    loading="lazy"
                                                                                    title="{{ $post->title }}">
                                                                            </a>
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
                            @endforeach
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Topic Section end -->

    <!-- User Followed Channels News -->
    @if (isset($channelFollowed) && count($channelFollowed) > 0)
        <div class="section panel overflow-hidden swiper-parent">
            <div class="section-outer panel py-4 lg:py-6 dark:text-white">
                <div class="container max-w-xl">
                    <div class="section-inner panel vstack gap-2">
                        <div class="block-layout carousel-layout vstack gap-2 lg:gap-3 panel">
                            <div class="d-flex justify-between">
                                <div class="block-header panel pt-1 border-top">
                                    <h2 class="h6 ft-tertiary fw-bold ls-0 text-uppercase m-0 text-black dark:text-white">
                                        {{ __('frontend-labels.home.from_the_channels_you_may_followed') }}</h2>
                                </div>
                                <div class="block-header panel pt-1">
                                    <h2 class="h6 ft-tertiary fw-bold ls-0 text-uppercase m-0 text-black dark:text-white">
                                        <a class="hstack d-inline-flex gap-0 text-none hover:text-primary duration-150"
                                            href="{{ url('posts') }}">
                                            <span>{{ __('frontend-labels.home.read_all') }}</span>
                                            <i class="icon-1 fw-bold unicon-chevron-right"></i>
                                        </a>
                                    </h2>
                                </div>
                            </div>
                            <div class="block-content panel">
                                <div class="swiper" id="followed-channels-swiper"
                                    data-uc-swiper="items: 2; gap: 16; dots: .dot-nav; next: .nav-next; prev: .nav-prev; disable-class: disabled;"
                                    data-uc-swiper-s="items: 3; gap: 24;" data-uc-swiper-l="items: 5; gap: 24;">
                                    <div class="swiper-wrapper">
                                        @include('front_end.classic.pages.partials.followed_channels_slides', ['channelFollowed' => $channelFollowed])
                                    </div>
                                </div>
                                <div
                                    class="swiper-nav nav-prev position-absolute top-50 start-0 translate-middle btn btn-alt-primary text-black rounded-circle p-0 border shadow-xs w-32px lg:w-40px h-32px lg:h-40px z-1">
                                    <i class="icon-1 unicon-chevron-left"></i>
                                </div>
                                <div
                                    class="swiper-nav nav-next position-absolute top-50 start-100 translate-middle btn btn-alt-primary text-black rounded-circle p-0 border shadow-xs w-32px lg:w-40px h-32px lg:h-40px z-1">
                                    <i class="icon-1 unicon-chevron-right"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif
    <!-- User Followed Channels Ends -->

    <!-- Video Section start -->
    @if (!$videoPosts->isEmpty())
        <div id="live_now" class="live_now section panel uc-dark swiper-parent">
            <div class="section-outer panel py-4 lg:py-6 bg-gray-900 text-white mt-5">

                <div class="container max-w-xl">
                    <div class="block-header panel pt-1 border-top">
                        <h2
                            class="story-title h6 ft-tertiary fw-bold ls-0 text-uppercase m-0 text-black dark:text-white border-none p-0">
                            {{ __('frontend-labels.home.latest_news_videos') }}

                            <div class="block-header panel pt-1">
                                <h2 class="h6 ft-tertiary fw-bold ls-0 text-uppercase m-0 text-black dark:text-white">
                                    <a class="hstack d-inline-flex gap-0 text-none hover:text-primary duration-150"
                                        href="{{ route('videos.frontend.index') }}">
                                        <span>{{ __('frontend-labels.home.read_all') }}</span>
                                        <i class="icon-1 fw-bold unicon-chevron-right"></i>
                                    </a>
                                </h2>
                            </div>
                        </h2>
                    </div>
                    <div
                        class="block-layout slider-thumbs-layout slider-thumbs panel vstack gap-2 lg:gap-3 panel overflow-hidden mt-2">
                        <div class="block-content">
                            <div class="row child-cols-12 g-2" data-uc-grid>
                                <div class="md:col-8 lg:col-9">
                                    <div class="panel overflow-hidden rounded">
                                        <div class="swiper swiper-main"
                                            data-uc-swiper="connect: .swiper-thumbs; items: 1; gap: 8; autoplay: 7000; parallax: true; fade: true; effect: fade; dots: .swiper-pagination; disable-class: last-slide;">

                                            <div class="swiper-wrapper">
                                                @foreach ($videoPosts as $videoPost)
                                                    <a href="{{ url('posts/' . $videoPost->slug) }}"
                                                        class="no-underline">
                                                        <div class="swiper-slide">
                                                            <article
                                                                class="post type-post h-250px md:h-350px lg:h-500px bg-black uc-dark">
                                                                <div
                                                                    class="post-media panel overflow-hidden position-cover">
                                                                    <div
                                                                        class="featured-video bg-gray-700 ratio ratio-3x2">
                                                                         @if ($videoPost->type === 'youtube')
                                                                            <div class="youtube-placeholder position-cover cursor-pointer" data-video-url="{{ $videoPost->video }}" style="cursor: pointer;">
                                                                                <img class="media-cover image {{ !$loop->first ? 'lazy-img' : '' }}"
                                                                                    src="{{ $loop->first ? ($videoPost->video_thumb ?? $videoPost->image ?? $defaultImage) : 'data:image/gif;base64,R0lGODlhAQABAIAAAAAAAP///yH5BAEAAAAALAAAAAABAAEAAAIBRAA7' }}"
                                                                                    data-src="{{ $videoPost->video_thumb ?? $videoPost->image ?? $defaultImage }}"
                                                                                    alt="{{ $videoPost->title }}"
                                                                                    @if($loop->first) fetchpriority="high" @endif
                                                                                    style="width: 100%; height: 100%; object-fit: cover;">
                                                                                <div class="position-cover d-flex align-items-center justify-content-center" style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.25); display: flex; align-items: center; justify-content: center;">
                                                                                    <div class="play-btn-rect" style="width: 68px; height: 48px; background-color: #ff0000; border-radius: 12px; display: flex; align-items: center; justify-content: center; transition: transform 0.2s;">
                                                                                        <svg viewBox="0 0 24 24" width="26" height="26" fill="white">
                                                                                            <path d="M8 5v14l11-7z"/>
                                                                                        </svg>
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                        @elseif ($videoPost->type == 'video')
                                                                            <video
                                                                                class="video-cover video-lazyload min-h-100px"
                                                                                preload="none" loop playsinline>
                                                                                <source src="{{ $videoPost->video }}"
                                                                                    data-src="{{ $videoPost->video ?? '' }}"
                                                                                    type="video/mp4">
                                                                                <source src="{{ $videoPost->video }}"
                                                                                    data-src="{{ $videoPost->video ?? '' }}"
                                                                                    type="video/webm">
                                                                            </video>
                                                                        @endif
                                                                    </div>
                                                                </div>
                                                                <div
                                                                    class="position-cover bg-gradient-to-t from-black to-transparent z-1 opacity-80">
                                                                </div>
                                                                <div
                                                                    class="post-header panel position-absolute bottom-0 vstack justify-between gap-2 xl:gap-4 max-300px lg:max-w-600px p-2 md:p-4 xl:p-6 z-1">
                                                                    <h3 class="post-title h4 lg:h3 xl:h2 m-0 text-truncate-2"
                                                                        data-swiper-parallax-x="-8">
                                                                        <a class="text-none"
                                                                            href="{{ url('posts/' . $videoPost->slug) }}">{{ $videoPost->title }}</a>
                                                                    </h3>
                                                                    <div data-swiper-parallax-x="8">
                                                                        <div
                                                                            class="post-meta panel hstack justify-between fs-7 fw-medium text-gray-900 dark:text-white text-opacity-60 d-none md:d-flex">
                                                                            <div class="meta">
                                                                                <div class="hstack">
                                                                                    <div>
                                                                                        <div
                                                                                            class="post-date post-author hstack text-black dark:text-white text-none fw-bold">
                                                                                            <span>{{ $videoPost->name }}</span>
                                                                                        </div>
                                                                                    </div>
                                                                                    <div>
                                                                                        <div
                                                                                            class="post-date post-author hstack">
                                                                                            <span>{{ $videoPost->publish_date ?? $videoPost->pubdate }}</span>
                                                                                        </div>
                                                                                    </div>
                                                                                    <div>
                                                                                        <a href="#post_comment"
                                                                                            class="post-comments text-none post-author hstack ">
                                                                                            <i class="icon-narrow unicon-chat"
                                                                                                title="Comment"></i>
                                                                                            <span
                                                                                                class="ms-1">{{ $videoPost->comment ?? '0' }}</span>
                                                                                            <i class="bi bi-eye fs-5 ms-1"
                                                                                                title="Views"></i>
                                                                                            <span
                                                                                                class="ms-1">{{ $videoPost->view_count ?? '0' }}</span>
                                                                                            <i class="bi bi-heart-fill ms-1"
                                                                                                title="Reaction"></i>
                                                                                            <span
                                                                                                class="ms-1">{{ $videoPost->reaction ?? '0' }}</span>
                                                                                        </a>

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
                                                    </a>
                                                @endforeach
                                            </div>
                                            <!-- Add Pagination -->
                                            <div
                                                class="swiper-pagination top-auto start-auto bottom-0 end-0 m-2 md:m-4 xl:m-6 text-white d-none md:d-inline-flex justify-end w-auto">
                                            </div>

                                        </div>
                                    </div>
                                </div>
                                <div class="md:col-4 lg:col-3">
                                    <div class="panel md:vstack gap-1 h-100">
                                        <!-- Slides thumbs -->
                                        <div class="swiper swiper-thumbs swiper-thumbs-progress rounded order-2"
                                            data-uc-swiper="items: 2;gap: 4;disable-class: last-slide;"
                                            data-uc-swiper-s="items: auto;direction: vertical;autoHeight: true;mousewheel: true;freeMode: false;watchSlidesVisibility: true;watchSlidesProgress: true;watchOverflow: true">
                                            <div class="swiper-wrapper md:flex-1">
                                                @foreach ($videoPosts as $videoPost)
                                                    <div
                                                        class="swiper-slide overflow-hidden rounded min-h-64px lg:min-h-100px">
                                                        <div class="swiper-slide-progress position-cover z-0">
                                                            <span></span>
                                                        </div>
                                                        <a href="{{ url('posts/' . $videoPost->slug) }}"
                                                            class="no-underline">
                                                            <article
                                                                class="post type-post panel uc-transition-toggle p-1 z-1">
                                                                <div class="row gx-1">
                                                                    <div class="col-auto post-media-wrap">
                                                                        <div
                                                                            class="post-media panel overflow-hidden w-40px lg:w-64px rounded">
                                                                            <div
                                                                                class="featured-video bg-gray-700 ratio ratio-3x4">
                                                                                <img class="video-cover min-h-100px lazy-img"
                                                                                    alt="Video Thumbnail"
                                                                                    src="data:image/gif;base64,R0lGODlhAQABAIAAAAAAAP///yH5BAEAAAAALAAAAAABAAEAAAIBRAA7"
                                                                                    data-src="{{ $videoPost->video_thumb ?? $videoPost->image }}" />
                                                                            </div>
                                                                            <div
                                                                                class="has-video-overlay position-absolute top-0 end-0 w-40px h-40px lg:w-64px lg:h-64px bg-gradient-45 from-transparent via-transparent to-black opacity-50">
                                                                            </div>
                                                                            <span
                                                                                class="cstack has-video-icon position-absolute top-50 start-50 translate-middle fs-6 w-40px h-40px text-white">
                                                                                <i
                                                                                    class="icon-narrow unicon-play-filled-alt"></i>
                                                                            </span>
                                                                        </div>
                                                                    </div>
                                                                    <div class="col">
                                                                        <p
                                                                            class="fs-6 m-0 text-truncate-2 text-gray-900 dark:text-white">
                                                                            {{ $videoPost->title }}</p>
                                                                    </div>
                                                                </div>
                                                            </article>
                                                        </a>
                                                    </div>
                                                @endforeach
                                            </div>
                                        </div>
                                        <!-- Tablet, Desktop and big screens nav -->
                                        <div
                                            class="swiper-prev btn btn-2xs lg:btn-xs btn-primary w-100 d-none md:d-flex order-1">
                                            {{ __('frontend-labels.home.prev') }}</div>
                                        <div
                                            class="swiper-next btn btn-2xs lg:btn-xs btn-primary w-100 d-none md:d-flex order-3">
                                            {{ __('frontend-labels.home.next') }}</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif
    <!-- Video Section end -->

    <!-- Latest Section start -->
    @if (isset($latesNews) && $latesNews->isNotEmpty())
    <div id="latest_news" class="latest-news section panel">
        <div class="section-outer panel py-4 lg:py-6">
            <div class="container max-w-xl">
                <div class="section-inner">
                    <div class="content-wrap row child-cols-12 g-4 lg:g-6" data-uc-grid>
                        <div class="md:col-9">
                            <div class="main-wrap panel vstack gap-3 lg:gap-6">
                                <div class="block-layout grid-layout vstack gap-2 panel overflow-hidden">
                                    <div class="block-header panel pt-1 border-top">
                                        <h2
                                            class="h6 ft-tertiary fw-bold ls-0 text-uppercase m-0 text-black dark:text-white">
                                            {{ __('frontend-labels.home.latest') }}</h2>
                                    </div>
                                    <div class="block-content">
                                        <div class="row child-cols-12 g-2 lg:g-4 sep-x">
                                            @foreach ($latesNews as $latest)
                                                <div>
                                                    <article class="post type-post panel uc-transition-toggle">
                                                        <div class="row child-cols g-2 lg:g-3" data-uc-grid>
                                                            <div class="col-auto">
                                                                <div
                                                                    class="post-media panel overflow-hidden max-w-150px min-w-100px lg:min-w-250px">
                                                                    <div
                                                                        class="featured-image bg-gray-25 dark:bg-gray-800 ratio ratio-3x2">
                                                                        @if ($latest->type == 'video' || $latest->type == 'youtube')
                                                                            <a href="{{ url('posts/' . $latest->slug) }}"
                                                                                class="position-cover">
                                                                                <img class="media-cover image uc-transition-scale-up uc-transition-opaque lazy-img"
                                                                                    src="data:image/gif;base64,R0lGODlhAQABAIAAAAAAAP///yH5BAEAAAAALAAAAAABAAEAAAIBRAA7"
                                                                                    data-src="{{ $latest->video_thumb }}"
                                                                                    alt="The Rise of AI-Powered Personal Assistants: How They Manage"
                                                                                    loading="lazy">
                                                                                <div
                                                                                    class="post-category hstack gap-narrow justify-center align-items-center text-white">
                                                                                    <a class="text-none"
                                                                                        href="{{ url('posts/' . $mostRead->slug) }}"
                                                                                        title="{{ $mostRead->title }}"><i
                                                                                            class="bi bi-play-circle font-size-45"></i></a>
                                                                                </div>
                                                                            </a>
                                                                        @elseif($latest->type == 'post')
                                                                            <a href="{{ url('posts/' . $latest->slug) }}"
                                                                                class="position-cover">
                                                                                <img class="media-cover image uc-transition-scale-up uc-transition-opaque lazy-img"
                                                                                    src="data:image/gif;base64,R0lGODlhAQABAIAAAAAAAP///yH5BAEAAAAALAAAAAABAAEAAAIBRAA7"
                                                                                    data-src="{{ $latest->image }}"
                                                                                    alt="The Rise of AI-Powered Personal Assistants: How They Manage"
                                                                                    loading="lazy">
                                                                            </a>
                                                                        @elseif($latest->type == 'audio')
                                                                            <a href="{{ url('posts/' . $latest->slug) }}"
                                                                                class="position-cover">
                                                                                <img class="media-cover image uc-transition-scale-up uc-transition-opaque lazy-img"
                                                                                    src="data:image/gif;base64,R0lGODlhAQABAIAAAAAAAP///yH5BAEAAAAALAAAAAABAAEAAAIBRAA7"
                                                                                    data-src="{{ $latest->image }}"
                                                                                    alt="The Rise of AI-Powered Personal Assistants: How They Manage"
                                                                                    loading="lazy">
                                                                            </a>
                                                                            <div
                                                                                    class="post-category hstack gap-narrow justify-center align-items-center text-white">
                                                                                    <a class="text-none"
                                                                                        href="{{ url('posts/' . $mostRead->slug) }}"
                                                                                        title="{{ $mostRead->title }}"><i
                                                                                            class="bi bi-play-circle font-size-45"></i></a>
                                                                                </div>
                                                                        @endif
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div>
                                                                <div
                                                                    class="post-header panel vstack justify-between gap-1">
                                                                    <h3
                                                                        class="post-title h5 lg:h4 m-0 text-truncate-2 hover:text-primary">
                                                                        <a class="text-none duration-150"
                                                                            href="{{ url('posts/' . $latest->slug) }}"
                                                                            title="{{ $latest->title ?? '' }}">{{ $latest->title ?? '' }}</a>
                                                                    </h3>
                                                                </div>
                                                                <p
                                                                    class="post-excrept ft-tertiary fs-6 text-gray-900 dark:text-white text-opacity-60 text-truncate-2 my-1">
                                                                    {{ strip_tags(html_entity_decode($latest->description)) }}
                                                                </p>
                                                                <div class="d-flex justify-between">
                                                                    <div class="mt-3">
                                                                        <a href="{{ $latest->channel ? url('channels/' . $latest->channel->slug) : '#' }}"
                                                                            class="post-comments text-none hstack gap-narrow"
                                                                            title="{{ $latest->channel->name ?? '' }}">
                                                                            @if ($latest->channel)
                                                                                <img data-src="{{ url('storage/images/' . $latest->channel->logo) }}" src="data:image/gif;base64,R0lGODlhAQABAIAAAAAAAP///yH5BAEAAAAALAAAAAABAAEAAAIBRAA7"
                                                                                    alt="channel logo"
                                                                                    class="rounded h-20px lazy-img"
                                                                                    width="20"
                                                                                    height="20">
                                                                            @else
                                                                                <img data-src="{{ url('storage/images/default-logo.png') }}" src="data:image/gif;base64,R0lGODlhAQABAIAAAAAAAP///yH5BAEAAAAALAAAAAABAAEAAAIBRAA7"
                                                                                    alt="default logo"
                                                                                    class="rounded h-20px lazy-img"
                                                                                    width="20"
                                                                                    height="20">
                                                                            @endif
                                                                            {{ $latest->channel->name ?? 'Default Channel Name' }}
                                                                        </a>
                                                                    </div>
                                                                    <div class="mt-3">
                                                                        <i class="bi bi-chat-left-text ms-1"
                                                                            title="Views"></i>
                                                                        <span>{{ $latest->comment ?? '0' }}</span>
                                                                        <i class="bi bi-eye fs-5 ms-1" title="Views"></i>
                                                                        <span>{{ $latest->view_count ?? '0' }}</span>
                                                                        <i class="bi bi-heart-fill ms-1"
                                                                            title="Reaction"></i>
                                                                        <span>{{ $latest->reaction ?? '0' }}</span>
                                                                    </div>
                                                                </div>
                                                            </div>


                                                        </div>
                                                    </article>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                    <div class="block-footer cstack lg:mt-2">
                                        <a href="{{ url('posts') }}"
                                            class="animate-btn gap-0 btn btn-sm btn-alt-primary bg-transparent text-black dark:text-white border w-100">
                                            <span>{{ __('frontend-labels.home.read_more') }}
                                                {{ $post_label->value ?? '' }}</span>
                                            <i class="icon icon-1 unicon-chevron-right"></i>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="md:col-3">
                            <div class="sidebar-wrap panel vstack gap-2 pb-2"
                                data-uc-sticky="end: .content-wrap; offset: 150; media: @m;">
                                <div class="widget popular-widget vstack gap-2 p-2 border">
                                    <div class="widget-title text-center">
                                        <h5
                                            class="ft-tertiary text-uppercase m-0 border text-black  p-2 dark:text-white rounded">
                                            {{ __('frontend-labels.home.popular_now') }}</h5>
                                    </div>
                                    <div class="widget-content">
                                        <div class="row child-cols-12 gx-4 gy-3 sep-x" data-uc-grid>
                                            @php $counter = 1; @endphp
                                            @foreach ($popularPosts as $popularPost)
                                                <div>
                                                    <article class="post type-post panel uc-transition-toggle">
                                                        <div class="row child-cols g-2 lg:g-3" data-uc-grid>
                                                            <div>
                                                                <div class="hstack items-start gap-3">
                                                                    <span
                                                                        class="h3 lg:h2 ft-tertiary fst-italic text-center text-primary m-0 min-w-24px">{{ $counter }}</span>
                                                                    <div
                                                                        class="post-header panel vstack justify-between gap-1">
                                                                        <h3 class="post-title h6 m-0">
                                                                            <a class="text-none hover:text-primary duration-150"
                                                                                href="{{ url('posts/' . $popularPost->slug) }}">
                                                                                {{ $popularPost->title }}
                                                                            </a>
                                                                        </h3>
                                                                        <div
                                                                            class="post-meta panel fs-7 text-gray-900 dark:text-white text-opacity-60">
                                                                            <div class="meta">
                                                                                <div class="d-flex justify-between gap-2">

                                                                                    <div class="post-date gap-narrow">
                                                                                        <span
                                                                                            title="{{ $popularPost->pubdate_news }}">{{ $popularPost->publish_date ?? $popularPost->pubdate }}</span>
                                                                                    </div>
                                                                                    <div>
                                                                                        <i class="bi bi-eye fs-5 ms-1"
                                                                                            title="Views"></i>
                                                                                        <span>{{ $popularPost->view_count ?? '0' }}</span>
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                            <div class="actions">
                                                                                <div class="hstack gap-1"></div>
                                                                            </div>

                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </article>
                                                </div>
                                                @php $counter++; @endphp
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                                <div>
                                    <div class="uc-navbar-newsletter panel vstack">
                                        <h6 class="fs-6 ft-tertiary fw-medium">
                                            {{ $socialsettings['app_name'] ?? 'Newshunt' }}</h6>

                                        <form class="vstack gap-1 bg-gray-300 bg-opacity-10" method="post"
                                            action="{{ route('subscribe.store') }}">
                                            @csrf
                                            <input type="email" name="email" id="index_subscriber_email"
                                                data-email-required="{{ __('frontend-labels.home.email_required') }}"
                                                data-email-taken="{{ __('frontend-labels.home.email_taken') }}"
                                                data-email-invalid="{{ __('frontend-labels.home.email_invalid') }}"
                                                data-email-subscribed="{{ __('frontend-labels.home.email_subscribed') }}"
                                                class="form-control-plaintext form-control-xs fs-6 dark:text-white"
                                                placeholder="{{ __('frontend-labels.home.your_email_address') }}">

                                            <button type="button" id="index-subscriber-button"
                                                class="btn btn-sm btn-primary fs-6 rounded-0">
                                                <i class="bi bi-envelope-plus"></i>
                                                {{ __('frontend-labels.home.subscribe') }}
                                            </button>
                                            <div id="subscriber-error-top-index" class="alert alert-danger d-none">
                                            </div>
                                        </form>

                                        <p class="fs-7 mt-1">
                                            {{ __('frontend-labels.home.dont_worry_we_dont_spam') }}</p>
                                        <ul class="nav-x gap-2 mt-3">
                                            <li><a href="{{ $socialsettings['instagram_link'] ?? '' }}"><i
                                                        class="icon icon-2 unicon-logo-instagram"
                                                        aria-label="Instagram"></i></a></li>
                                            <li><a href="{{ $socialsettings['x_link'] ?? '' }}"><i
                                                        class="icon icon-2 unicon-logo-x-filled" aria-label="X"></i></a>
                                            </li>
                                            <li><a href="{{ $socialsettings['facebook_link'] ?? '' }}"><i
                                                        class="icon icon-2 unicon-logo-facebook"
                                                        aria-label="Facebook"></i></a></li>
                                            <li><a href="{{ $socialsettings['linkedin_link'] ?? '' }}"><i
                                                        class="icon icon-2 unicon-logo-linkedin"
                                                        aria-label="LinkedIn"></i></a></li>
                                            <li><a href="{{ $socialsettings['pinterest_link'] ?? '' }}"><i
                                                        class="icon icon-2 unicon-logo-pinterest"
                                                        aria-label="Pinterest"></i></a></li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif

    <!-- Latest Section end -->
    </div>

    <!-- Wrapper end -->
@endsection
