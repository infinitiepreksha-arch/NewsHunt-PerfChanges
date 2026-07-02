@extends('front_end.' . $theme . '.layout.main')
@section('body')
    <!-- Wrapper start -->
    <div id="wrapper" class="wrap overflow-hidden-x">
        <div class="breadcrumbs panel z-1 py-2 bg-gray-25 dark:bg-gray-100 dark:bg-opacity-5 dark:text-white">
            <div class="container max-w-xl">
                <ul class="breadcrumb nav-x justify-center gap-1 fs-7 sm:fs-6 m-0">
                    <li><a href="{{ url('home') }}" title="Home">{{ __('frontend-labels.home.title') }}</a></li>
                    <li><i class="unicon-chevron-right opacity-50"></i></li>
                    <li><span class="opacity-70"
                            title="{{ $title }}">{{$title}}</span>
                    </li>
                </ul>
            </div>
        </div>
        @if ($front_topics)
            <div class="section py-3 sm:py-6 lg:py-9">
                <div class="container max-w-xl">
                    <div class="panel vstack gap-3 sm:gap-6 lg:gap-9">
                        <header class="page-header panel vstack text-center">
                            <h1 class="h3 lg:h1" title="{{$title}}">
                                {{$title}}</h1>
                            @if (!empty(request()->route('topic')))
                            @else
                                <h1 class="h3 lg:h1" title="{{ $post_lable->value ?? '' }}">{{ $post_lable->value ?? '' }}
                                </h1>
                            @endif
                        </header>
                        <div class="row g-4 xl:g-8">
                            <div class="col">
                                <div class="panel">
                                    <div
                                        class="row child-cols-12 sm:child-cols-4 lg:child-cols-3 col-match gy-4 xl:gy-6 gx-2 sm:gx-4">
                                        @foreach ($front_topics as $topic)
                                            <div id="postRender">
                                                <article class="post type-post panel vstack gap-2">
                                                    <div class="post-image panel overflow-hidden">
                                                        <figure
                                                            class="featured-image m-0 ratio ratio-16x9 rounded uc-transition-toggle overflow-hidden bg-gray-25 dark:bg-gray-800">
                                                            <a href="{{ url('topics/' . $topic->slug) }}"
                                                                class="position-cover">
                                                                <img class="media-cover image uc-transition-scale-up uc-transition-opaque"
                                                                    src="{{ $topic->logo ? asset('storage/images/' . $topic->logo) : asset('storage/' . $placeholder_image->value) }}"
                                                                    alt="{{ $topic->name }}" loading="lazy" >
                                                            </a>
                                                        </figure>
                                                        {{-- <div
                                                            class="position-absolute top-0 end-0 w-150px h-150px rounded-top-end bg-gradient-45 from-transparent via-transparent to-black opacity-50">
                                                        </div> --}}
                                                    </div>
                                                    <div class="post-header panel vstack gap-1 lg:gap-2">
                                                        <h3 class="post-title h6 sm:h6 xl:h4 m-0 text-truncate-2 m-0">
                                                            <a class="text-none"
                                                                href="{{ url('topics/' . $topic->slug) }}">{{ $topic->name }}</a>
                                                        </h3>
                                                        <div>
                                                            <div
                                                                class="post-meta panel fs-7 fw-medium text-gray-900 dark:text-white text-opacity-60">
                                                                <div class="meta">
                                                                    <div class="d-flex justify-between gap-2">
                                                                        <div>
                                                                            <div class="d-flex gap-1">
                                                                            </div>
                                                                        </div>
                                                                        <div>
                                                                        </div>
                                                                    </div>
                                                                    <div>
                                                                        <div class="post-date hstack gap-narrow mt-1">
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
                                    <div class="nav-pagination pt-3 mt-6 lg:mt-9">
                                        <ul class="nav-x uc-pagination hstack gap-1 justify-center ft-secondary"
                                            data-uc-margin="">
                                            {{ $front_topics->links('vendor.custom-pagination') }}
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
        <!-- Newsletter -->
    </div>
    <!-- Wrapper end -->
@endsection
