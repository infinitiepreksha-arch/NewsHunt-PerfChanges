@extends('front_end.' . $theme . '.layout.main')
@section('body')

    <div id="wrapper" class="wrap overflow-hidden-x">
        <div class="breadcrumbs panel z-1 py-2 bg-gray-25 dark:bg-gray-100 dark:bg-opacity-5 dark:text-white">
            <div class="container max-w-xl">
                <ul class="breadcrumb nav-x justify-center gap-1 fs-7 sm:fs-6 m-0">
                    <li><a href="{{ url('home') }}">{{ __('frontend-labels.home.title') }}</a></li>
                    <li><i class="unicon-chevron-right opacity-50"></i></li>
                    <li><a href="{{ route('videos.frontend.index') }}">{{$title}}</a></li>

                </ul>
            </div>
        </div>
        <div class="section py-3 sm:py-6 lg:py-9" id="video-page" data-videos-url="{{ route('videos.frontend.index') }}">
            <div class="container max-w-xl">
                <div class="panel vstack gap-1 sm:gap-6 lg:gap-9">
                    <header class="page-header panel vstack text-center">
                        <h1 class="h3 lg:h1 mb-4">{{$title}}</h1>

                    </header>
                    <div class="vstack sm:hstack justify-between items-center gap-2 sm:gap-4">
                        <div class="panel text-center sm:text-start">
                            <span id="video-count" class="fs-6 m-0 opacity-60"> {{ __('frontend-labels.news_videos.showing') }} {{ $videos->count() }}    {{ __('frontend-labels.news_videos.videos_out_of') }}
                                {{ $videos->total() }}  {{ __('frontend-labels.news_videos.total') }}.</span>
                        </div>
                        
                        <div class="hstack gap-1 fs-6 filtering">
                                <span>{{ __('frontend-labels.news_videos.sort_by') }}:</span>
                                <select name="sort" id="sort_by"
                                    data-videos-url="{{ route('videos.frontend.index') }}"
                                    class="form-select form-control-xs fs-6 w-150px dark:bg-gray-900 dark:text-white dark:border-gray-700">
                                    <option value="newest" {{ request()->get('sort') == 'newest' ? 'selected' : '' }}>
                                        {{ __('frontend-labels.news_videos.newest') }}</option>
                                    <option value="oldest" {{ request()->get('sort') == 'oldest' ? 'selected' : '' }}>
                                        {{ __('frontend-labels.news_videos.oldest') }}</option>
                                </select>
                                
                            </div>
                    </div>

                    <div class="hidden lg:block">
                        <div id="videos-container"class="row child-cols-12 sm:child-cols-4 col-match gy-4 xl:gy-6 gx-3 sm:gx-4 filter-cat-results">
                            @if ($videos->isNotEmpty())
                                @foreach ($videos as $video)
                                    <div>
                                        <article
                                            class="post type-post panel vstack gap-2 f-cat border p-2 dark:bg-gray-800  bg-white rounded">
                                            <div class="post-image panel overflow-hidden">
                                                <figure
                                                    class="featured-image m-0 ratio ratio-16x9 rounded uc-transition-toggle overflow-hidden bg-gray-25 dark:bg-gray-800">
                                                    <a href="{{ $video->slug ? url('posts/' . $video->slug) : '/' }}"
                                                        class="position-cover">
                                                        <img class="media-cover image uc-transition-scale-up uc-transition-opaque"
                                                            src="{{ $video->video_thumb ?? '' }}"
                                                            data-src="{{ $video->video_thumb ?? '' }}"
                                                            alt="{{ $video->title ?? '' }}"
                                                            title="{{ $video->title ?? '' }}" loading="lazy" >
                                                        <div
                                                            class="post-category hstack gap-narrow justify-center align-items-center text-white">
                                                            <a class="text-none"
                                                                href="{{ $video->slug ? url('posts/' . $video->slug) : '#' }}"
                                                                title="{{ $video->title ?? '' }}"><i
                                                                    class="bi bi-play-circle font-size-45"></i></a>
                                                        </div>
                                                    </a>
                                                </figure>

                                            </div>
                                            <div
                                                class="post-header panel vstack gap-1 lg:gap-2  rounded dark:bg-gray-800 bg-white">
                                                <h5 class="post-title h6 sm:h5 xl:h5 m-0 text-truncate-2 m-0 ">
                                                    <a href="{{ $video->slug ? url('posts/' . $video->slug) : '/' }}"
                                                        class="text-none hover:text-primary duration-150"
                                                        title="{{ $video->title ?? 'No Channel' }}">
                                                        <span>{{ $video->title ?? 'No Channel' }}</span>
                                                    </a>
                                                </h5>
                                                <div>
                                                    <div
                                                        class="post-meta panel fs-7 fw-medium text-gray-900 dark:text-white text-opacity-60">
                                                        <div class="meta">
                                                            <div class="d-flex justify-between gap-2">
                                                                <div>
                                                                    <div class="d-flex gap-1">
                                                                        <a href="{{ $video->channel ? url('channels/' . $video->channel->slug) : '#' }}"
                                                                            title="{{ $video->channel->name ?? 'No Channel' }}">
                                                                            <img src="{{ $video->channel ? url('storage/images/' . $video->channel->logo) : '' }}"
                                                                                alt="Channel Logo" class="h-20px">
                                                                        </a>
                                                                        <a href="{{ $video->channel ? url('channels/' . $video->channel->slug) : '#' }}"
                                                                            class="text-black dark:text-white text-none fw-bold"
                                                                            title="{{ $video->channel->name ?? 'No Channel' }}">
                                                                            {{ $video->channel->name ?? 'No Channel' }}
                                                                        </a>
                                                                    </div>
                                                                </div>
                                                                <div>
                                                                    <div
                                                                        class="post-comments text-none hstack gap-narrow gap-1">
                                                                        <a href="{{ $video->slug ? url('posts/' . $video->slug) : '#' }}#comment-form"
                                                                            class="post-comments text-none hstack gap-narrow"
                                                                            title="Comments">
                                                                            <i class="icon-narrow unicon-chat"></i>
                                                                            <span>{{ $video->comment ?? 0 }}</span>
                                                                        </a>
                                                                        <i class="bi bi-eye fs-5" title="Views"></i>
                                                                        <span
                                                                            title="Views">{{ $video->view_count ?? '0' }}</span>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div>
                                                                <div class="post-date hstack gap-narrow mt-1">
                                                                    <span
                                                                        title="{{ $video->publish_date_news }}">{{ $video->publish_date ?? $video->pubdate }}</span>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </article>
                                    </div>
                                @endforeach
                            @endif
                        </div>
                    </div>

                    <!-- Pagination -->
                    <div id="pagination-container">
                        @if ($videos->hasPages())
                            <div class="nav-pagination pt-3 mt-6 lg:mt-9">
                                <ul class="nav-x uc-pagination hstack gap-1 justify-center ft-secondary" data-uc-margin="">
                                    {{ $videos->links('vendor.custom-pagination') }}
                                </ul>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
