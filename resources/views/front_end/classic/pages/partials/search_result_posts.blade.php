<div class="mb-4 text-start">
    <div class="fs-6 text-black dark:text-white">
        @if ($getPosts->total() > 0)
            Showing <span class="fw-bold">{{ $getPosts->firstItem() }}</span> – <span class="fw-bold">{{ $getPosts->lastItem() }}</span> of <span class="fw-bold text-primary">{{ $getPosts->total() }}</span> results
            @if (request()->filled('search') || !empty($searchQuery))
                for <span class="fw-bold">"{{ $searchQuery ?? request('search') }}"</span>
            @elseif (request()->route('topic'))
                under <span class="fw-bold">"{{ request()->route('topic') }}"</span> category
            @elseif (request()->route('channel'))
                under <span class="fw-bold">"{{ request()->route('channel') }}"</span> channel
            @endif
        @else
            No results found
            @if (request()->filled('search') || !empty($searchQuery))
                for <span class="fw-bold">"{{ $searchQuery ?? request('search') }}"</span>
            @elseif (request()->route('topic'))
                under <span class="fw-bold">"{{ request()->route('topic') }}"</span> category
            @elseif (request()->route('channel'))
                under <span class="fw-bold">"{{ request()->route('channel') }}"</span> channel
            @endif
        @endif
    </div>
</div>

@if (!empty($getPosts[0]))
    <div class="panel">
        <div id="posts-ad-container" class="row child-cols-12 sm:child-cols-6 lg:child-cols-4 col-match gy-4 xl:gy-6 gx-2 sm:gx-4">
            @foreach ($getPosts as $post)
                <div id="postRender">
                    <article class="post type-post panel vstack gap-2">
                        <div class="post-image panel overflow-hidden">
                            <figure class="featured-image m-0 ratio ratio-16x9 rounded uc-transition-toggle overflow-hidden bg-gray-25 dark:bg-gray-800">
                                <a href="{{ $post->url }}" class="position-cover" title="{{ $post->title }}">
                                    @if ($post->type == 'video')
                                        <img class="media-cover image uc-transition-scale-up uc-transition-opaque" src="{{ $post->video_thumb }}" data-src="{{ $post->video_thumb }}" alt="{{ $post->title }}" loading="lazy">
                                        <div class="post-category hstack gap-narrow justify-center align-items-center text-white">
                                            <span class="text-none"><i class="bi bi-play-circle font-size-45"></i></span>
                                        </div>
                                    @elseif($post->type == 'audio')
                                        <img class="media-cover image uc-transition-scale-up uc-transition-opaque" src="{{ $post->image }}" data-src="{{ $post->image }}" alt="{{ $post->title }}" loading="lazy">
                                        <div class="post-category hstack gap-narrow justify-center align-items-center text-white">
                                            <span class="text-none"><i class="bi bi-music-note-beamed font-size-45"></i></span>
                                        </div>
                                    @elseif($post->type == 'youtube')
                                        <img class="media-cover image uc-transition-scale-up uc-transition-opaque" src="{{ $post->image }}" data-src="{{ $post->image }}" alt="{{ $post->title }}" loading="lazy">
                                        <div class="post-category hstack gap-narrow justify-center align-items-center text-white">
                                            <span class="text-none"><i class="bi bi-play-circle font-size-45"></i></span>
                                        </div>
                                    @elseif($post->type == 'story')
                                        <img class="media-cover image uc-transition-scale-up uc-transition-opaque" src="{{ $post->image }}" data-src="{{ $post->image }}" alt="{{ $post->title }}" loading="lazy">
                                        <div class="post-category hstack gap-narrow justify-center align-items-center text-white">
                                            <span class="text-none"><i class="bi bi-images font-size-45"></i></span>
                                        </div>
                                    @elseif($post->type == 'paper' || $post->type == 'magazine')
                                        <img class="media-cover image uc-transition-scale-up uc-transition-opaque" src="{{ $post->image }}" data-src="{{ $post->image }}" alt="{{ $post->title }}" loading="lazy">
                                        <div class="post-category hstack gap-narrow justify-center align-items-center text-white">
                                            <span class="text-none"><i class="bi bi-file-earmark-pdf font-size-45"></i></span>
                                        </div>
                                    @else
                                        <img class="media-cover image uc-transition-scale-up uc-transition-opaque" src="{{ $post->image }}" data-src="{{ $post->image }}" alt="{{ $post->title }}" loading="lazy">
                                    @endif
                                </a>
                            </figure>
                            @if (!empty($post->topic_slug))
                                <div class="post-category hstack gap-narrow position-absolute top-0 start-0 m-1 fs-7 fw-bold h-15px px-1 rounded-1 shadow-xs bg-white text-primary">
                                    <a class="text-none" href="{{ url('topics/' . $post->topic_slug) }}" title="{{ $post->topic_name }}">{{ $post->topic_name }}</a>
                                </div>
                            @endif
                        </div>
                        <div class="post-header panel vstack gap-1 lg:gap-2">
                            <h3 class="post-title h6 sm:h6 xl:h5 m-0 text-truncate-2 m-0">
                                <a class="text-none" href="{{ $post->url }}" title="{{ $post->title }}">{{ $post->title }}</a>
                            </h3>
                            <div>
                                <div class="post-meta panel fs-7 fw-medium text-gray-900 dark:text-white text-opacity-60">
                                    <div class="meta">
                                        <div class="d-flex justify-between gap-2">
                                            @if(!empty($post->channel_slug))
                                                <div>
                                                    <div class="d-flex gap-1">
                                                        <a href="{{ url('channels/' . $post->channel_slug) }}" title="{{ $post->channel_name }}"><img src="{{ url('storage/images/' . $post->channel_logo) }}" alt="Channel Logo" class="h-20px"></a>
                                                        <a href="{{ url('channels/' . $post->channel_slug) }}" class="text-black dark:text-white text-none fw-bold" title="{{ $post->channel_name }}">{{ $post->channel_name }}</a>
                                                    </div>
                                                </div>
                                            @else
                                                <div></div>
                                            @endif
                                            <div>
                                                <div class="post-comments text-none hstack gap-narrow gap-1">
                                                    @if(in_array($post->type, ['post', 'video', 'youtube', 'audio']))
                                                        <a href="{{ $post->url }}#comment-form" class="post-comments text-none hstack gap-narrow" title="Comments">
                                                            <i class="icon-narrow unicon-chat"></i>
                                                            <span>{{ $post->comment }}</span>
                                                        </a>
                                                    @endif
                                                    <i class="bi bi-eye fs-5" title="Views"></i>
                                                    <span title="Views">{{ $post->view_count }}</span>

                                                    @if(in_array($post->type, ['post', 'video', 'youtube', 'audio']))
                                                        <i class="bi bi-heart-fill ms-1"></i>
                                                        <span>{{ $post->reaction ?? '' }}</span>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                        <div>
                                            <div class="post-date hstack gap-narrow mt-1">
                                                <span title="{{ $post->publish_date_news }}">{{ $post->publish_date ?? $post->pubdate }}</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </article>
                </div>
            @endforeach
        </div>
        <div class="nav-pagination pt-3 mt-6 lg:mt-2">
            <ul class="nav-x uc-pagination hstack gap-1 justify-center ft-secondary" data-uc-margin="">
                {{ $getPosts->links('vendor.custom-pagination') }}
            </ul>
        </div>
    </div>
@else
    <div class="p-3">
        <img class="object-fit-cover mx-h-50px image uc-transition-opaque" src="{{ asset('front_end/classic/images/place-holser/no-data.png') }}" alt="No Data Found">
    </div>
@endif
