@if (!$stories->isEmpty())
    @foreach ($stories as $story)
        <div id="storyDetailsModal-{{ $story->id }}" class="modal modal-blur fade" tabindex="-1" role="dialog" aria-label="Story Details Modal"
            aria-labelledby="storyDetailsModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="storyDetailsModalLabel">{{ __('STORY TOPIC') }}:
                            {{ $story->topic->name ?? 'Uncategorized' }}</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <p>{{ __('Story Topic') }}: {{ $story->topic->name ?? 'Uncategorized' }}</p>
                        @if ($story->story_slides->isEmpty())
                            <div class="alert alert-info text-center">
                                {{ __('No slides found for this story.') }}
                            </div>
                        @else
                            <div id="carousel-{{ $story->id }}" class="carousel slide carousel-fade" data-bs-ride="carousel">
                                <div class="carousel-inner">
                                    @foreach ($story->story_slides as $index => $slide)
                                        <div class="carousel-item {{ $index == 0 ? 'active' : '' }}">
                                            <img class="d-block w-100" src="{{ asset('storage/' . $slide->image) }}"
                                                alt="{{ $slide->title }}" class="story-model-css"
                                                onerror="this.onerror=null;this.src='{{ asset('images/placeholder.png') }}';">
                                            <div class="position-absolute bottom-0 start-0 mb-2 ms-2 text-white px-2 py-1 rounded">
                                                <small>{{ $slide->created_at->diffForHumans() }}</small>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                            <div class="carousel-indicators mt-3 d-flex justify-content-center">
                                @foreach ($story->story_slides as $index => $slide)
                                    <img src="{{ asset('storage/' . $slide->image) }}"
                                         class="{{ $index == 0 ? 'active' : '' }}"
                                         data-bs-target="#carousel-{{ $story->id }}"
                                         data-bs-slide-to="{{ $index }}"
                                         aria-label="Slide {{ $index + 1 }}"
                                         style="width: 60px; height: 40px; object-fit: cover; cursor: pointer; margin: 0 5px;" />
                                @endforeach
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    @endforeach
@endif
