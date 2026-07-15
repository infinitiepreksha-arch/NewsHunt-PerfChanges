@if (!empty($stories) && $stories->isNotEmpty())
    @foreach ($stories as $story)
        <div class="swiper-slide px-1" data-post-id="{{ $story->id }}">
            <div class="card bg-white dark:bg-gray-800 d-flex flex-column" id="card_style">
                <a href="{{ url('webstories/' . $story->topic->slug . '/' . $story->slug) }}" target="_blank" class="position-relative d-block">
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
                    <div class="story-progress-container position-absolute bottom-0 start-0 w-100 px-1 pb-2">
                        <div class="progress-segments d-flex gap-1">
                            @foreach ($story->story_slides as $slide)
                                <div class="progress-segment flex-grow-1 h-1 bg-white bg-opacity-50 story-dashed-css"></div>
                            @endforeach
                        </div>
                    </div>
                    <span class="visual-stories-icon position-absolute top-2 end-1 p-1 rounded-circle dark:text-white text-white bg-gray-800">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24">
                            <path fill="currentColor" d="M7 20V4h10v16zm-4-2V6h2v12zm16 0V6h2v12z" />
                        </svg>
                    </span>
                </a>
                <div id="card_title" class="card-footer text-gray-900 dark:text-white d-flex flex-column h-100">
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
