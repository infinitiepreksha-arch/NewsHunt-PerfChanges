@if (!empty($posts) && $posts->isNotEmpty())
    @foreach ($posts as $lifestylePosts)
        <div>
            <article class="post type-post panel uc-transition-toggle vstack gap-1">
                <div class="post-media panel overflow-hidden">
                    <div class="featured-image bg-gray-25 dark:bg-gray-800 ratio ratio-16x9">
                        <a href="{{ url('posts/' . $lifestylePosts->slug) }}" class="position-cover">
                            @if ($lifestylePosts->type == 'post')
                                <img class="media-cover image uc-transition-scale-up uc-transition-opaque lazy-img"
                                    src="data:image/gif;base64,R0lGODlhAQABAIAAAAAAAP///yH5BAEAAAAALAAAAAABAAEAAAIBRAA7"
                                    data-src="{{ $lifestylePosts->image ?? $defaultImage }}"
                                    alt="{{ $lifestylePosts->title ?? '' }}"
                                    title="{{ $lifestylePosts->title ?? '' }}"
                                    loading="lazy">
                            @elseif($lifestylePosts->type == 'youtube' || $lifestylePosts->type == 'video')
                                <img class="media-cover image uc-transition-scale-up uc-transition-opaque lazy-img"
                                    src="data:image/gif;base64,R0lGODlhAQABAIAAAAAAAP///yH5BAEAAAAALAAAAAABAAEAAAIBRAA7"
                                    data-src="{{ $lifestylePosts->video_thumb ?? $defaultImage }}"
                                    alt="{{ $lifestylePosts->title ?? '' }}"
                                    title="{{ $lifestylePosts->title ?? '' }}"
                                    loading="lazy">
                                <div class="post-category hstack gap-narrow justify-center align-items-center text-white">
                                    <a class="text-none" href="{{ url('posts/' . $lifestylePosts->slug) }}" title="{{ $lifestylePosts->title }}"><i class="bi bi-play-circle font-size-45"></i></a>
                                </div>
                            @elseif($lifestylePosts->type == 'audio')
                                <img class="media-cover image uc-transition-scale-up uc-transition-opaque lazy-img"
                                    src="data:image/gif;base64,R0lGODlhAQABAIAAAAAAAP///yH5BAEAAAAALAAAAAABAAEAAAIBRAA7"
                                    data-src="{{ $lifestylePosts->image ?? $defaultImage }}"
                                    alt="{{ $lifestylePosts->title ?? '' }}"
                                    title="{{ $lifestylePosts->title ?? '' }}"
                                    loading="lazy">
                                <div class="post-category hstack gap-narrow justify-center align-items-center text-white">
                                    <a class="text-none" href="{{ url('posts/' . $lifestylePosts->slug) }}" title="{{ $lifestylePosts->title }}"><i class="bi bi-play-circle font-size-45"></i></a>
                                </div>
                            @else
                                <img class="media-cover image uc-transition-scale-up uc-transition-opaque lazy-img"
                                    src="data:image/gif;base64,R0lGODlhAQABAIAAAAAAAP///yH5BAEAAAAALAAAAAABAAEAAAIBRAA7"
                                    data-src="{{ $lifestylePosts->video_thumb ?? $defaultImage }}"
                                    alt="{{ $lifestylePosts->title ?? '' }}"
                                    title="{{ $lifestylePosts->title ?? '' }}"
                                    loading="lazy">
                                <div class="post-category hstack gap-narrow justify-center align-items-center text-white">
                                    <a class="text-none" href="{{ url('posts/' . $lifestylePosts->slug) }}" title="{{ $lifestylePosts->title }}"><i class="bi bi-play-circle font-size-45"></i></a>
                                </div>
                            @endif
                        </a>
                    </div>
                </div>
                <div class="post-header panel vstack gap-narrow">
                    <h3 class="post-title h6 m-0 text-truncate-2">
                        <a class="text-none hover:text-primary duration-150"
                            href="{{ url('posts/' . $lifestylePosts->slug) }}"
                            title="{{ $lifestylePosts->title ?? '' }}">{{ $lifestylePosts->title ?? '' }}</a>
                    </h3>
                    <div class="post-meta panel hstack justify-start gap-1 fs-7 ft-tertiary fw-medium text-gray-900 dark:text-white text-opacity-60 d-none md:d-flex z-1 d-none md:d-block">
                        <div>
                            <div class="post-date hstack gap-narrow">
                                <span title="{{ $lifestylePosts->publish_date ?? $lifestylePosts->pubdate }}">{{ $lifestylePosts->publish_date ?? $lifestylePosts->pubdate }}</span>
                            </div>
                        </div>
                        <div>
                            <a href="{{ url('posts/' . $lifestylePosts->slug) }}#comment-form"
                                class="post-comments text-none hstack gap-narrow"
                                title="Comments">
                                <i class="icon-narrow unicon-chat"></i>
                                <span>{{ $lifestylePosts->comment ?? '' }}</span>
                                <i class="bi bi-eye fs-5 ms-1" title="Views"></i>
                                <span title="Views">{{ $lifestylePosts->view_count }}</span>
                                <i class="bi bi-heart-fill ms-1"></i>
                                <span>{{ $lifestylePosts->reaction ?? '' }}</span>
                            </a>
                        </div>
                    </div>
                </div>
            </article>
        </div>
    @endforeach
@endif
