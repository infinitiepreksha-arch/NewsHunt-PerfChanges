@extends('front_end.' . $theme . '.layout.main')
@section('body')

    <div id="wrapper" class="wrap overflow-hidden-x">
        <div class="breadcrumbs panel z-1 py-2 bg-gray-25 dark:bg-gray-100 dark:bg-opacity-5 dark:text-white">
            <div class="container max-w-xl">
                <ul class="breadcrumb nav-x justify-center gap-1 fs-7 sm:fs-6 m-0">
                    <li><a href="{{ url('home') }}">{{ __('frontend-labels.home.title') }}</a></li>
                    <li><i class="unicon-chevron-right opacity-50"></i></li>
                    <li><a href="{{ route('audios.frontend.index') }}">{{$title}}</a></li>
                </ul>
            </div>
        </div>
        <div class="section py-3 sm:py-6 lg:py-9" id="audio-page" data-audios-url="{{ route('audios.frontend.index') }}">
            <div class="container max-w-xl">
                <div class="panel vstack gap-1 sm:gap-6 lg:gap-9">
                    <header class="page-header panel vstack text-center">
                        <h1 class="h3 lg:h1 mb-4">{{$title}}</h1>
                    </header>

                    <div class="vstack sm:hstack justify-between items-center gap-2 sm:gap-4">
                        <div class="panel text-center sm:text-start">
                            <span id="audio-count" class="fs-6 m-0 opacity-60">
                                Showing {{ $audios->count() }} audios out of {{ $audios->total() }} total.
                            </span>
                        </div>
                    </div>

                    <div class="hidden lg:block">
                        <div id="audios-container" class="row gy-4 gx-3">
                            @if ($audios->isNotEmpty())
                                @foreach ($audios as $audio)
                                    <div class="col-12 col-sm-6 col-md-4 col-lg-2">
                                        <a href="{{ url('posts/' . $audio->slug) }}" title="{{ $audio->title ?? '' }}">
                                            <article
                                                class="post type-post panel border p-2 dark:bg-gray-800 bg-white rounded">
                                                <div
                                                    class="panel uc-transition-toggle vstack text-center overflow-hidden rounded border border-white border-opacity-10">
                                                    <figure
                                                        class="featured-image m-0 ratio ratio-3x4 rounded-0 overflow-hidden bg-gray-25 dark:bg-gray-800">
                                                        <img class="media-cover image uc-transition-scale-up uc-transition-opaque"
                                                            src="{{ $audio->image }}" data-src="{{ $audio->image }} "
                                                            alt="Security" loading="lazy" >
                                                        <a href="{{ url('posts/' . $audio->slug) }}" class="position-cover"
                                                            data-caption="Security"></a>
                                                    </figure>
                                                    <div class="overlay position-cover z-0 bg-black bg-opacity-50"></div>
                                                    <!-- Audio Player Icon -->
                                                    <div class="position-absolute top-50 start-50 translate-middle">
                                                        <div
                                                            class="rounded-circle d-flex align-items-center justify-content-center shadow-lg audio-play-overlay">
                                                            <svg xmlns="http://www.w3.org/2000/svg" width="30"
                                                                height="30" viewBox="0 0 24 24" fill="currentColor"
                                                                class="text-black">
                                                                <path d="M8 5v14l11-7z"></path>
                                                            </svg>
                                                        </div>
                                                    </div>
                                                    <div
                                                        class="position-absolute bottom-0 vstack justify-end gap-1 lg:gap-2 h-3/4 w-100 p-2 bg-gradient-to-t from-red-600 to-transparent">
                                                        <marquee class="html-marquee" direction="left" behavior="scroll"
                                                            scrollamount="3">
                                                            <span
                                                                class="fs-5 lg:fs-4 fw-bold text-white m-0">{{ $audio->title }}</span>
                                                        </marquee>
                                                        <a href="#"
                                                            class="btn btn-2xs border-white border-opacity-25 fs-7 text-white rounded-1">{{ $audio->topic->name }}</a>
                                                    </div>
                                                    <a class="position-cover text-none z-1"
                                                        href="{{ url('posts/' . $audio->slug) }}"></a>
                                                </div>
                                            </article>
                                        </a>
                                    </div>
                                @endforeach
                            @else
                                <div class="col-12 text-center">
                                    <p>No audios available.</p>
                                </div>
                            @endif
                        </div>
                    </div>
                    <div id="pagination-container">
                        @if ($audios->hasPages())
                            <div class="nav-pagination pt-3 mt-6 lg:mt-9">
                                <ul class="nav-x uc-pagination hstack gap-1 justify-center ft-secondary" data-uc-margin="">
                                    {{ $audios->links('vendor.custom-pagination') }}
                                </ul>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
