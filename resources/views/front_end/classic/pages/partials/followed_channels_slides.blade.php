@if (!empty($channelFollowed))
    @foreach ($channelFollowed as $mostRead)
        <div class="swiper-slide" data-post-id="{{ $mostRead->id }}">
            <div>
                <article class="post type-post panel uc-transition-toggle vstack gap-2">
                    <div class="post-media panel overflow-hidden">
                        <div class="featured-image bg-gray-25 dark:bg-gray-800 ratio ratio-3x2">
                            @if ($mostRead->type == 'video' || $mostRead->type == 'youtube')
                                <a href="{{ url('posts/' . $mostRead->slug) }}" class="position-cover">
                                    <img class="media-cover image uc-transition-scale-up uc-transition-opaque lazy-img"
                                        src="data:image/gif;base64,R0lGODlhAQABAIAAAAAAAP///yH5BAEAAAAALAAAAAABAAEAAAIBRAA7"
                                        data-src="{{ $mostRead->video_thumb ?? $defaultImage }}"
                                        alt="{{ $mostRead->title ?? '' }}"
                                        title="{{ $mostRead->title ?? '' }}"
                                        loading="lazy">
                                    <div class="post-category hstack gap-narrow justify-center align-items-center text-white">
                                        <a class="text-none" href="{{ url('posts/' . $mostRead->slug) }}" title="{{ $mostRead->title }}"><i class="bi bi-play-circle font-size-45"></i></a>
                                    </div>
                                </a>
                            @elseif($mostRead->type == 'post')
                                <a href="{{ url('posts/' . $mostRead->slug) }}" class="position-cover">
                                    <img class="media-cover image uc-transition-scale-up uc-transition-opaque lazy-img"
                                        src="data:image/gif;base64,R0lGODlhAQABAIAAAAAAAP///yH5BAEAAAAALAAAAAABAAEAAAIBRAA7"
                                        data-src="{{ $mostRead->image ?? $defaultImage }}"
                                        alt="{{ $mostRead->title ?? '' }}"
                                        title="{{ $mostRead->title ?? '' }}"
                                        loading="lazy">
                                </a>
                            @elseif($mostRead->type == 'audio')
                                <a href="{{ url('posts/' . $mostRead->slug) }}" class="position-cover">
                                    <img class="media-cover image uc-transition-scale-up uc-transition-opaque lazy-img"
                                        src="data:image/gif;base64,R0lGODlhAQABAIAAAAAAAP///yH5BAEAAAAALAAAAAABAAEAAAIBRAA7"
                                        data-src="{{ $mostRead->image ?? $defaultImage }}"
                                        alt="{{ $mostRead->title ?? '' }}"
                                        title="{{ $mostRead->title ?? '' }}"
                                        loading="lazy">
                                </a>
                                <div class="post-category hstack gap-narrow justify-center align-items-center text-white">
                                    <a class="text-none" href="{{ url('posts/' . $mostRead->slug) }}" title="{{ $mostRead->title }}"><i class="bi bi-play-circle font-size-45"></i></a>
                                </div>
                            @endif
                        </div>
                    </div>
                    <div class="post-header panel vstack gap-1">
                        <h3 class="post-title h6 m-0 text-truncate-2 hover:text-primary">
                            <a class="text-none duration-150" href="{{ url('posts/' . $mostRead->slug) }}" title="{{ $mostRead->title ?? '' }}">{{ $mostRead->title ?? '' }}</a>
                        </h3>
                        @if ($mostRead->channel != null)
                            <div class="post-meta panel hstack justify-start gap-1 fs-7 ft-tertiary fw-medium text-gray-900 dark:text-white text-opacity-60 d-none md:d-flex z-1">
                                <div>
                                    <div class="post-date hstack gap-narrow">
                                        <a href="{{ url('channels/' . $mostRead->channel->slug) }}" class="post-comments text-none hstack gap-narrow channel-button" title="{{ $mostRead->channel->name ?? '' }}">
                                            <span>{{ $mostRead->channel->name ?? '' }}</span>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        @endif
                        <div class="post-meta panel hstack justify-between gap-1 fs-7 ft-tertiary fw-medium text-gray-900 dark:text-white text-opacity-60 d-none md:d-flex z-1">
                            <div>
                                <div class="post-date hstack gap-narrow">
                                    <span title="{{ $mostRead->publish_date_news }}">{{ $mostRead->publish_date ?? $mostRead->pubdate }}</span>
                                </div>
                            </div>
                            <div>
                                <a href="{{ url('posts/' . $mostRead->slug) }}#comment-form" class="post-comments text-none hstack gap-narrow" title="Comments">
                                    <i class="icon-narrow unicon-chat" title="Comments"></i>
                                    <span title="Comments">{{ $mostRead->comment }}</span>
                                </a>
                            </div>
                            <div title="Views">
                                <i class="bi bi-eye fs-5"></i>
                                <span>{{ $mostRead->view_count }}</span>
                            </div>
                            <div title="Reaction">
                                <i class="bi bi-heart-fill ms-1"></i>
                                <span>{{ $mostRead->reaction }}</span>
                            </div>
                        </div>
                    </div>
                </article>
            </div>
        </div>
    @endforeach
@endif
