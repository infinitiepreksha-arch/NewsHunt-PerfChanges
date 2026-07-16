@if (!empty($posts) && $posts->isNotEmpty())
    @foreach ($posts as $top_post)
        <div class="swiper-slide" data-post-id="{{ $top_post->id }}">
            <div>
                <article class="post type-post panel uc-transition-toggle gap-2">
                    <div class="row child-cols g-2" data-uc-grid>
                        <div class="col-auto">
                            <div class="post-media panel overflow-hidden max-w-64px min-w-64px">
                                <div class="featured-image bg-gray-25 dark:bg-gray-800 ratio ratio-1x1">
                                    @if ($top_post->type == 'video' || $top_post->type == 'youtube')
                                        <a href="{{ url('posts/' . $top_post->slug) }}" class="position-cover">
                                            <img class="media-cover image uc-transition-scale-up uc-transition-opaque lazy-img"
                                                src="data:image/gif;base64,R0lGODlhAQABAIAAAAAAAP///yH5BAEAAAAALAAAAAABAAEAAAIBRAA7"
                                                data-src="{{ $top_post->video_thumb ?? $defaultImage }}"
                                                alt="{{ $top_post->title ?? '' }}"
                                                loading="lazy">
                                            <div class="post-category hstack gap-narrow justify-center align-items-center text-white">
                                                <a class="text-none" href="{{ url('topics/' . $top_post->slug) }}" title="{{ $top_post->name }}"><i class="bi bi-play-circle font-size-45"></i></a>
                                            </div>
                                        </a>
                                    @elseif($top_post->type == 'audio')
                                        <a href="{{ url('posts/' . $top_post->slug) }}" class="position-cover">
                                            <img class="media-cover image uc-transition-scale-up uc-transition-opaque lazy-img"
                                                src="data:image/gif;base64,R0lGODlhAQABAIAAAAAAAP///yH5BAEAAAAALAAAAAABAAEAAAIBRAA7"
                                                data-src="{{ $top_post->image ?? $defaultImage }}"
                                                alt="{{ $top_post->title ?? '' }}"
                                                loading="lazy">
                                            <div class="post-category hstack gap-narrow justify-center align-items-center text-white">
                                            </div>
                                        </a>
                                    @elseif($top_post->type == 'post')
                                        <a href="{{ url('posts/' . $top_post->slug) }}" class="position-cover">
                                            <img class="media-cover image uc-transition-scale-up uc-transition-opaque lazy-img"
                                                src="data:image/gif;base64,R0lGODlhAQABAIAAAAAAAP///yH5BAEAAAAALAAAAAABAAEAAAIBRAA7"
                                                data-src="{{ $top_post->image ?? $defaultImage }}"
                                                alt="{{ $top_post->title ?? '' }}"
                                                loading="lazy">
                                            <div class="post-category hstack gap-narrow justify-center align-items-center text-white">
                                            </div>
                                        </a>
                                    @endif
                                </div>
                            </div>
                        </div>
                        <div>
                            <div class="post-header panel vstack gap-1">
                                <h3 class="post-title h6 hover:text-primary m-0 text-truncate-2">
                                    <a class="text-none duration-150" href="{{ url('posts/' . $top_post->slug) }}" title="{{ $top_post->title ?? '' }}">{{ $top_post->title }}</a>
                                </h3>
                            </div>
                            @if ($top_post->channel != null)
                                <a href="{{ url('channels/' . $top_post->channel->slug) }}" class="post-comments text-none hstack gap-narrow">
                                    <img src="{{ url('storage/images/' . $top_post->channel->logo) }}" alt="channel logo" title="{{ $top_post->channel->name ?? '' }}" class="rounded-pill h-20px" width="20" height="20">
                                    <span title="{{ $top_post->channel->name ?? '' }}">{{ $top_post->channel->name }}</span>
                                </a>
                            @endif
                        </div>
                    </div>
                </article>
            </div>
        </div>
    @endforeach
@endif
