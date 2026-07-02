@extends('front_end.' . $theme . '.layout.main')
@section('body')
    <!-- Wrapper start -->
    <div id="wrapper" class="wrap overflow-hidden-x">
        <div class="breadcrumbs panel z-1 py-2 bg-gray-25 dark:bg-gray-100 dark:bg-opacity-5 dark:text-white">
            <div class="container max-w-xl">
                <ul class="breadcrumb nav-x justify-center gap-1 fs-7 sm:fs-6 m-0">
                    <li><a href="{{ url('home') }}" title="Home">{{ __('frontend-labels.home.title') }}</a></li>
                    <li><i class="unicon-chevron-right opacity-50"></i></li>
                    @if (!empty(request()->route('topic')))
                        <li><a href="{{ url('topics') }}"
                                title="{{ __('frontend-labels.topics.title') }}"><span>{{ __('frontend-labels.topics.title') }}</span></a>
                        </li>
                        <li><i class="unicon-chevron-right opacity-50"></i></li>
                        <li><span class="opacity-70" title="{{ $title ?? '' }}">{{ $title ?? '' }}</span></li>
                    @else
                        <li><span class="opacity-70"
                                title="{{ $post_lable->value ?? '' }}">{{ $post_lable->value ?? '' }}</span></li>
                    @endif
                </ul>
            </div>
        </div>
        @if ($getPosts)
            <div class="section py-3 sm:py-6 lg:py-9">
                <div class="container max-w-xl">
                    <div class="panel vstack gap-3 sm:gap-6 lg:gap-9">
                        <header class="page-header panel vstack text-center">
                            @if (!empty(request()->route('topic')))
                                <h1 class="h3 lg:h1" title="{{ $title ?? '' }}">{{ __('frontend-labels.topics.title') }}:
                                    {{ $title ?? '' }}</h1>
                            @else
                                <h1 class="h3 lg:h1" title="{{ $post_lable->value ?? '' }}">{{ $post_lable->value ?? '' }}
                                </h1>
                            @endif
                            <span class="m-0 opacity-60">
                                {{ __('frontend-labels.topics.showing') }}
                                {{ $getPosts->firstItem() ?? '0' }} {{ __('frontend-labels.topics.to') }}
                                {{ $getPosts->lastItem() ?? '0' }} {{ __('frontend-labels.topics.posts_out_of') }}

                                {{ $getPosts->total() ?? '0' }} {{ __('frontend-labels.topics.total') }}
                                {{ __('frontend-labels.topics.under') }}
                                <br class="d-block lg:d-none">
                                "{{ request()->route('topic') ?? 'Posts' }}" {{ __('frontend-labels.topics.category') }}
                                .
                            </span>
                        </header>
                        <div class="row g-4 xl:g-8">
                            <div class="col">
                                <div class="panel">

                                    <div id="topic_post_ads"
                                        class="row child-cols-12 sm:child-cols-6 lg:child-cols-4 col-match gy-4 xl:gy-6 gx-2 sm:gx-4">

                                        @foreach ($getPosts as $post)
                                            <div id="postRender">
                                                <article class="post type-post panel vstack gap-2">
                                                    <div class="post-image panel overflow-hidden">
                                                        <figure
                                                            class="featured-image m-0 ratio ratio-16x9 rounded uc-transition-toggle overflow-hidden bg-gray-25 dark:bg-gray-800">
                                                            <a href="{{ url('posts/' . $post->slug) }}"
                                                                class="position-cover">
                                                                @if ($post->type == 'post')
                                                                    <img class="media-cover image uc-transition-scale-up uc-transition-opaque"
                                                                        src="{{ $post->image  }}"
                                                                        data-src="{{ $post->image  }}"
                                                                        alt="{{ $post->title }}" loading="lazy">
                                                                @elseif ($post->type == 'audio')
                                                                    <img class="media-cover image uc-transition-scale-up uc-transition-opaque"
                                                                        src="{{ $post->image  }}"
                                                                        data-src="{{ $post->image  }}"
                                                                        alt="{{ $post->title }}" loading="lazy">
                                                                        <div
                                                                        class="post-category hstack gap-narrow justify-center align-items-center text-white">
                                                                        <a class="text-none"
                                                                            href="{{ url('topics/' . $post->topic_slug) }}"
                                                                            title="{{ $post->topic_name }}"><i
                                                                                class="bi bi-play-circle font-size-45"></i></a>
                                                                    </div>
                                                                @elseif ($post->type == 'video' || $post->type == 'youtube')
                                                                    <img class="media-cover image uc-transition-scale-up uc-transition-opaque"
                                                                        src="{{ $post->video_thumb  }}"
                                                                        data-src="{{ $post->video_thumb  }}"
                                                                        alt="{{ $post->title }}" loading="lazy">
                                                                    <div
                                                                        class="post-category hstack gap-narrow justify-center align-items-center text-white">
                                                                        <a class="text-none"
                                                                            href="{{ url('topics/' . $post->topic_slug) }}"
                                                                            title="{{ $post->topic_name }}"><i
                                                                                class="bi bi-play-circle font-size-45"></i></a>
                                                                    </div>
                                                                @endif
                                                            </a>
                                                        </figure>
                                                        @if (empty(request()->route('topic')))
                                                            <div
                                                                class="post-category hstack gap-narrow position-absolute top-0 start-0 m-1 fs-7 fw-bold h-24px px-1 rounded-1 shadow-xs bg-white text-primary">
                                                                <a class="text-none"
                                                                    href="{{ url('posts/' . $post->topic_slug) }}"
                                                                    title="{{ $post->topic_name ?? '' }}">{{ $post->topic_name ?? '' }}</a>
                                                            </div>
                                                        @endif
                                                        {{-- <div
                                                            class="position-absolute top-0 end-0 w-150px h-150px rounded-top-end bg-gradient-45 from-transparent via-transparent to-black opacity-50">
                                                        </div> --}}
                                                    </div>
                                                    <div class="post-header panel vstack gap-1 lg:gap-2">
                                                        <h3 class="post-title h6 sm:h5 xl:h4 m-0 text-truncate-2 m-0">
                                                            <a class="text-none"
                                                                href="{{ url('posts/' . $post->slug) }}">{{ $post->title }}</a>
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
                                                                                    title="Commetns">
                                                                                    <i class="icon-narrow unicon-chat"></i>
                                                                                    <span>{{ $post->comment }}</span>
                                                                                </a>
                                                                                <i class="bi bi-eye fs-5"
                                                                                    title="views"></i>
                                                                                <span
                                                                                    title="views">{{ $post->view_count ?? '0' }}</span>
                                                                                <i class="bi bi-heart-fill  ms-1"></i>
                                                                                <span>{{ $post->reaction ?? '' }}</span>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                    <div>
                                                                        <div class="post-date hstack gap-narrow mt-1">
                                                                            <span
                                                                                title="{{ $post->publish_date ?? $post->pubdate }}">{{ $post->publish_date ?? $post->pubdate }}</span>
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
                                            {{ $getPosts->links('vendor.custom-pagination') }}
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
