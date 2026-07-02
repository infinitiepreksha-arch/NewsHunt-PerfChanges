@extends('front_end.' . $theme . '.layout.main')
@section('body')

    <div id="wrapper" class="wrap overflow-hidden-x">
        <div class="breadcrumbs panel z-1 py-2 bg-gray-25 dark:bg-gray-100 dark:bg-opacity-5 dark:text-white">
            <div class="container max-w-xl">
                <ul class="breadcrumb nav-x justify-center gap-1 fs-7 sm:fs-6 m-0">
                    <li><a href="{{ url('home') }}">{{ __('frontend-labels.home.title') }}</a></li>
                    <li><i class="unicon-chevron-right opacity-50"></i></li>
                    <li><span class="opacity-50">{{ $title }}</span></li>
                </ul>
            </div>
        </div>
        @if ($channelData)
            <div class="section py-3 sm:py-6 lg:py-9">
                <div class="container max-w-xl">
                    <div class="panel vstack gap-3 sm:gap-6 lg:gap-9">
                        <header class="page-header panel vstack text-center">
                            <h1 class="h3 lg:h1" title="{{ $title }}">{{ $title }}</h1>
                        </header>
                        <div class="row g-4 xl:g-8">
                            <div class="col">
                                <div class="panel text-center">
                                    <div
                                        class="row child-cols-12 sm:child-cols-6 lg:child-cols-3 col-match gy-4 xl:gy-6 gx-2 sm:gx-4">
                                        @foreach ($channelData as $channel)
                                            <div id="postRender">
                                                <article class="post type-post panel vstack gap-2">
                                                    <div class="post-image panel overflow-hidden">
                                                        <figure
                                                            class="featured-image m-0 ratio ratio-16x9 rounded uc-transition-toggle overflow-hidden bg-gray-25 dark:bg-gray-800">
                                                            <a href="{{ url('channels/' . $channel->slug) }}"
                                                                class="position-cover">
                                                                <img class="media-cover image uc-transition-scale-up uc-transition-opaque"
                                                                    src="{{ url('storage/images/' . $channel->logo) }}"
                                                                    data-src="{{ url('storage/images/' . $channel->logo) }}"
                                                                    alt="{{ $channel->title }}" loading="lazy" >
                                                            </a>
                                                        </figure>
                                                        {{-- <div
                                                            class="position-absolute top-0 end-0 w-150px h-150px rounded-top-end bg-gradient-45 from-transparent via-transparent to-black opacity-50">
                                                        </div> --}}
                                                    </div>
                                                    <div class="post-header panel vstack gap-1 lg:gap-2">
                                                        <h3 class="post-title h6 sm:h5 xl:h4 m-0 text-truncate-2">
                                                            <a class="text-none"
                                                                href="{{ url('channels/' . $channel->slug) }}">{{ $channel->name }}</a>
                                                        </h3>
                                                        <div>
                                                            <div
                                                                class="post-meta panel hstack fs-7 fw-medium text-gray-900 dark:text-white text-opacity-60 d-none md:d-flex justify-between">
                                                                <div class="hstack gap-1">
                                                                    <i class="bi bi-person-plus fs-3"></i>
                                                                    <span>{{ $channel->follow_count }}</span>
                                                                </div>
                                                                <div class="hstack gap-1">
                                                                    @if (auth()->check())
                                                                        @if ($channel->is_followed == '1')
                                                                            <button
                                                                                class="btn btn-primary btn-xs px-1 py-1 channel-follow"
                                                                                data-channel-id="{{ $channel->id }}"
                                                                                data-uc-tooltip="Followed">Unfollow</button>
                                                                        @else
                                                                            <button
                                                                                class="btn btn-outline-primary btn-xs px-1 py-1 channel-follow"
                                                                                id="channel_{{ $channel->id }}"
                                                                                data-channel-id="{{ $channel->id }}"
                                                                                data-uc-tooltip="Follow">follow</button>
                                                                        @endif
                                                                    @else
                                                                        <a class="btn btn-outline-primary btn-xs px-1 py-1"
                                                                            href="#uc-account-modal" data-uc-toggle
                                                                            data-uc-tooltip="Follow">Follow</a>
                                                                    @endif
                                                                </div>
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
                                            {{ $channelData->links('vendor.custom-pagination') }}
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @else
            <div class="panel text-center">
                <img class="w-100 h-500px object-contain image uc-transition-opaque"
                    src="{{ asset('front_end/classic/images/place-holser/no-data.png') }}" alt="No Transactions Found">
            </div>
        @endif
    </div>
@endsection
