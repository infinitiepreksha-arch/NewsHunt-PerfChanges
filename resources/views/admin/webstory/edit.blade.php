@extends('admin.layouts.main')

@section('title')
    {{ $title }}
@endsection

@section('pre-title')
    {{ $title }}
@endsection

@section('page-title')
    <div class="row g-2 align-items-center">
        <div class="col">
            <div class="page-pretitle">
                <a href="{{ url('admin/dashboard') }}">{{ __('page.HOME') }}/</a>
                <a href="{{ route('stories.publicIndex') }}">{{ __('page.STORIES') }}</a> /
                @yield('pre-title')
            </div>
            <h2 class="page-title mt-2">
                @yield('title')
            </h2>
        </div>
        <!-- Page title actions -->
        <div class="col-auto ms-auto d-print-none">
        </div>
    </div>
@endsection

@section('content')
    <div class="card">
        <div class="card-body">
            <div class="container-progress mb-4">
                <div class="steps steps-counter">
                    <a class="step-item text-decoration-none" data-step="1">
                        {{ __('page.SELECT_STORY_TOPIC') }}
                    </a>
                    <a class="step-item text-decoration-none" data-step="2">
                        {{ __('page.ADD_SLIDES_CONTENT') }}
                    </a>
                    <a class="step-item text-decoration-none" data-step="3">
                        {{ __('page.ARRANGE_SLIDE_ORDER') }}
                    </a>
                    <a class="step-item text-decoration-none" data-step="4">
                        {{ __('page.APPLY_ANIMATIONS') }}
                    </a>
                    <a class="step-item text-decoration-none" data-step="5">
                        {{ __('page.REVIEW_SUBMIT') }}
                    </a>
                </div>
            </div>

            <form id="storyForm" action="{{ route('stories.update', $story->id) }}" method="POST"  data-parsley-validate
                enctype="multipart/form-data">
                @csrf
                @method('PUT')

                <!-- Step 1: Story Details -->
                <div id="step1" class="step-content">
                    <h3 class="card-title mb-4">{{ __('page.STORY_DETAILS') }}</h3>

                    <div class="mb-3">
                        <label class="form-label">{{ __('page.STORY TITLE') }}<span class="text-danger">*</span></label>
                        <input type="text" name="title" class="form-control"
                            value="{{ old('title', $story->title ?? '') }}">
                        <span class="text-danger mt-1 d-block"><strong id="title-error"></strong></span>
                    </div>

                    <div class="mb-3">
                        <label class="form-label required">{{ __('page.IMAGE_UPLOAD_TYPE') ?? 'Image Upload Type' }}</label>
                        <div class="d-flex gap-3">
                            <label class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="image_size_type" value="fixed" {{ (old('image_size_type', $story->image_size_type ?? 'fixed') == 'fixed') ? 'checked' : '' }}>
                                <span class="form-check-label">{{ __('page.FIXED_SIZE') ?? 'Fixed Size' }} (1080 x 1920)</span>
                            </label>
                            <label class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="image_size_type" value="random" {{ (old('image_size_type', $story->image_size_type ?? 'fixed') == 'random') ? 'checked' : '' }}>
                                <span class="form-check-label">{{ __('page.RANDOM_SIZE') ?? 'Random Size' }}</span>
                            </label>
                        </div>
                    </div>

                    @can('select-newslanguage-for-story')
                        <div class="alert alert-info mb-0 rounded py-2 mb-2">
                            <i class="fas fa-info-circle me-2"></i>
                            {{ __('page.SELECT_NEWSLANGUAGE_FIRST') }}
                        </div>
                        <div class="form-group mb-3">
                            <label for="news_language_id"
                                class="form-label">{{ __('page.SELECT_NEWSLANGUAGE') }}<span class="text-danger">*</span></label>
                            <select class="form-control form-select select2" id="news_language_id" name="news_language_id">
                                <option value="" disabled>{{ __('page.SELECT_NEWS_LANGUAGE') }}</option>
                                @foreach ($news_languages as $news_language)
                                    <option value="{{ $news_language->id }}"
                                        {{ old('news_language_id', $story->news_language_id ?? ($selected_language_id ?? '')) == $news_language->id ? 'selected' : '' }}>
                                        {{ $news_language->name }}
                                    </option>
                                @endforeach
                            </select>
                            <span class="text-danger mt-1 d-block"><strong id="news_language_id-error"></strong></span>
                        </div>
                    @else
                        <div class="form-group mb-3">
                            <label for="news_language_id"
                                class="form-label">{{ __('page.SELECT_NEWSLANGUAGE') }}<span class="text-danger">*</span></label>
                            <div class="alert alert-warning mb-0 rounded py-2">
                                <i class="fas fa-exclamation-triangle me-2"></i>
                                {{ __('message.NO_PERMISSION_NEWSLANGUAGE') }}
                            </div>
                        </div>
                    @endcan

                    @can('select-topic-for-story')
                        <div class="mb-3 topic-none d-none">
                            <label class="form-label">{{ __('page.SELECT_TOPIC') }}<span class="text-danger">*</span></label>
                            <select name="topic_id" id="select-topic" class="form-select">
                                <option value="">{{ __('page.SELECT_TOPIC') }}</option>
                                {{-- Topics will be loaded dynamically based on selected language --}}
                                @if (isset($topic) && $topic->count() > 0)
                                    @foreach ($topic as $single_topic)
                                        <option value="{{ $single_topic->id }}"
                                            {{ old('topic_id', $story->topic_id ?? '') == $single_topic->id ? 'selected' : '' }}>
                                            {{ $single_topic->name }}
                                        </option>
                                    @endforeach
                                @endif
                            </select>
                            <span class="text-danger mt-1 d-block"><strong id="topic_id-error"></strong></span>
                        </div>
                    @else
                        <div class="mb-3">
                            <label class="form-label">{{ __('page.SELECT_TOPIC') }}<span class="text-danger">*</span></label>
                            <div class="alert alert-warning mb-0 rounded py-2">
                                <i class="fas fa-exclamation-triangle me-2"></i>
                                {{ __('message.NO_PERMISSION_TOPIC') }}
                            </div>
                        </div>
                    @endcan
                </div>

                <!-- Step 2: Edit Slides -->
                <div id="step2" class="step-content d-none">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h3 class="card-title mb-0">{{ __('page.EDIT_SLIDES') }}</h3>
                        <button type="button" class="btn btn-primary" id="addMoreSlides">
                            {{ __('page.ADD_ANOTHER_SLIDE') }}
                        </button>
                    </div>

                    <div class="accordion" id="accordionSlides">
                        @foreach ($story->story_slides as $index => $slide)
                            <div class="accordion-item">
                                <h2 class="accordion-header d-flex align-items-center justify-content-between">
                                    <button class="accordion-button" type="button" data-bs-toggle="collapse"
                                        data-bs-target="#collapseSlide{{ $index }}" aria-expanded="false">
                                        {{ $slide->title }}
                                    </button>
                                    <button type="button"
                                        class="btn btn-link text-danger delete-slide me-2 p-0 border-0 background-none"
                                        data-slide-index="{{ $index }}">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                            viewBox="0 0 24 24" fill="currentColor"
                                            class="icon icon-tabler icons-tabler-filled icon-tabler-trash">
                                            <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                            <path
                                                d="M20 6a1 1 0 0 1 .117 1.993l-.117 .007h-.081l-.919 11a3 3 0 0 1 -2.824 2.995l-.176 .005h-8c-1.598 0 -2.904 -1.249 -2.992 -2.75l-.005 -.167l-.923 -11.083h-.08a1 1 0 0 1 -.117 -1.993l.117 -.007h16z" />
                                            <path
                                                d="M14 2a2 2 0 0 1 2 2a1 1 0 0 1 -1.993 .117l-.007 -.117h-4l-.007 .117a1 1 0 0 1 -1.993 -.117a2 2 0 0 1 1.85 -1.995l.15 -.005h4z" />
                                        </svg>
                                    </button>
                                </h2>

                                <div id="collapseSlide{{ $index }}" class="accordion-collapse collapse"
                                    data-bs-parent="#accordionSlides">
                                    <div class="accordion-body">
                                        <div class="slide-entry mb-4 border p-3 rounded"
                                            data-slide-index="{{ $index }}">
                                            <input type="hidden" name="slides[{{ $index }}][id]"
                                                value="{{ $slide->id }}">

                                            <div class="mb-3">
                                                <label class="form-label">{{ __('page.SLIDE_TITLE') }}</label>
                                                <input type="text" name="slides[{{ $index }}][title]"
                                                    class="form-control"
                                                    value="{{ old('slides.' . $index . '.title', $slide->title) }}">
                                                <span class="text-danger mt-1 d-block"><strong id="slides.{{ $index }}.title-error"></strong></span>
                                            </div>

                                            <div class="mb-3">
                                                <label class="form-label">{{ __('page.SLIDE_DESCRIPTION') }}</label>

                                                <textarea name="slides[{{ $index }}][description]"
                                                    class="form-control" rows="3">{{ old('slides.' . $index . '.description', $slide->description) }}</textarea>
                                                <span class="text-danger mt-1 d-block"><strong id="slides.{{ $index }}.description-error"></strong></span>
                                            </div>

                                            <div class="mb-3">
                                                <label class="form-label">{{ __('page.SLIDE_IMAGE') }}</label>

                                                <input type="file" name="slides[{{ $index }}][image]"
                                                    class="form-control"
                                                    accept="image/*"
                                                    onchange="editPreviewImage(event, {{ $index }})">
                                                <span class="text-danger mt-1 d-block"><strong id="slides.{{ $index }}.image-error"></strong></span>

                                                <div class="mt-3">
                                                    <img id="imagePreview{{ $index }}"
                                                        src="{{ asset('storage/' . $slide->image) }}" alt="Slide Preview"
                                                        class="img-preview img-fluid admin-story-edit-image-css">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                <!-- Step 3: Order Slides -->
                <div id="step3" class="step-content d-none">
                    <h3 class="card-title mb-4">{{ __('page.ORDER_SLIDES') }}</h3>

                    <div id="slides-order" class="example-list">
                        @foreach ($story->story_slides as $index => $slide)
                            <div class="slide-preview" draggable="true" data-index="{{ $index }}">
                                <img src="{{ asset('storage/' . $slide->image) }}" alt="Slide Image Preview"
                                    class="ordering-image-edit-css" id="imagePreviewThumbnail{{ $index }}">
                                <span id="slideTitle{{ $index }}">{{ $slide->title }}</span>
                            </div>
                        @endforeach
                    </div>
                </div>
                <!-- Step 4: Animations -->
                <div id="step4" class="step-content d-none">
                    <div class="row">
                        <div class="col-md-8">
                            <h3 class="card-title mb-4">{{ __('page.ADD_ANIMATIONS') }}</h3>

                            <div class="accordion" id="animationAccordion">
                                <!-- Title Animation -->
                                <div class="accordion-item">
                                    <h2 class="accordion-header">
                                        <button class="accordion-button" type="button" data-bs-toggle="collapse"
                                            data-bs-target="#titleAnimation">
                                            Title Animation
                                        </button>
                                    </h2>
                                    <div id="titleAnimation" class="accordion-collapse collapse show"
                                        data-bs-parent="#animationAccordion">
                                        <div class="accordion-body">
                                            <div class="mb-3">
                                                @php
                                                    $titleAnimation =
                                                        $animations[$story->story_slides->first()->id]['title'] ?? [];
                                                @endphp
                                                <label class="form-label">{{ __('page.ANIMATION_TYPE') }}</label>

                                                <select class="form-select" name="title_animation">
                                                    <option value="fade-in"
                                                        {{ isset($titleAnimation['type']) && $titleAnimation['type'] == 'fade-in' ? 'selected' : '' }}>
                                                        Fade In</option>
                                                    <option value="slide-up"
                                                        {{ isset($titleAnimation['type']) && $titleAnimation['type'] == 'slide-up' ? 'selected' : '' }}>
                                                        Slide Up</option>
                                                    <option value="slide-down"
                                                        {{ isset($titleAnimation['type']) && $titleAnimation['type'] == 'slide-down' ? 'selected' : '' }}>
                                                        Slide Down</option>
                                                </select>
                                            </div>
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <label class="form-label">{{ __('page.DELAY_SECONDS') }}</label>

                                                    <select class="form-select delay-select" name="title_delay">
                                                        <option value="0"
                                                            {{ old('title_delay', $titleAnimation['delay'] ?? 0) == 0 ? 'selected' : '' }}>
                                                            {{ __('page.NO_DELAY') }}</option>
                                                        <option value="1"
                                                            {{ old('title_delay', $titleAnimation['delay'] ?? 0) == 1 ? 'selected' : '' }}>
                                                            {{ __('page.ONE_SECOND') }}</option>
                                                        <option value="2"
                                                            {{ old('title_delay', $titleAnimation['delay'] ?? 0) == 2 ? 'selected' : '' }}>
                                                            {{ __('page.TWO_SECONDS') }}</option>
                                                        <option value="3"
                                                            {{ old('title_delay', $titleAnimation['delay'] ?? 0) == 3 ? 'selected' : '' }}>
                                                            {{ __('page.THREE_SECONDS') }}</option>
                                                    </select>
                                                </div>

                                                <div class="col-md-6">
                                                    <label class="form-label">{{ __('page.DURATION_SECONDS') }}</label>

                                                    <select class="form-select duration-select" name="title_duration">
                                                        <option value="1"
                                                            {{ old('title_duration', $titleAnimation['duration'] ?? 1) == 1 ? 'selected' : '' }}>
                                                            {{ __('page.ONE_SECOND') }}</option>
                                                        <option value="2"
                                                            {{ old('title_duration', $titleAnimation['duration'] ?? 1) == 2 ? 'selected' : '' }}>
                                                            {{ __('page.TWO_SECONDS') }}</option>
                                                        <option value="3"
                                                            {{ old('title_duration', $titleAnimation['duration'] ?? 1) == 3 ? 'selected' : '' }}>
                                                            {{ __('page.THREE_SECONDS') }}</option>
                                                    </select>
                                                </div>

                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Description Animation -->
                                <div class="accordion-item">
                                    <h2 class="accordion-header">
                                        <button class="accordion-button collapsed" type="button"
                                            data-bs-toggle="collapse" data-bs-target="#descriptionAnimation">
                                            Description Animation
                                        </button>
                                    </h2>
                                    <div id="descriptionAnimation" class="accordion-collapse collapse"
                                        data-bs-parent="#animationAccordion">
                                        <div class="accordion-body">
                                            <div class="mb-3">
                                                @php
                                                    $descriptionAnimation =
                                                        $animations[$story->story_slides->first()->id]['description'] ??
                                                        [];
                                                @endphp
                                                <label class="form-label">{{ __('page.ANIMATION_TYPE') }}</label>
                                                <select class="form-select" name="description_animation">
                                                    <option value="fade-in"
                                                        {{ isset($descriptionAnimation['type']) && $descriptionAnimation['type'] == 'fade-in' ? 'selected' : '' }}>
                                                        Fade In</option>
                                                    <option value="slide-up"
                                                        {{ isset($descriptionAnimation['type']) && $descriptionAnimation['type'] == 'slide-up' ? 'selected' : '' }}>
                                                        Slide Up</option>
                                                    <option value="slide-down"
                                                        {{ isset($descriptionAnimation['type']) && $descriptionAnimation['type'] == 'slide-down' ? 'selected' : '' }}>
                                                        Slide Down</option>
                                                </select>
                                            </div>
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <label class="form-label">{{ __('page.DELAY_SECONDS') }}</label>
                                                    <select class="form-select delay-select" name="description_delay">
                                                        <option value="0"
                                                            {{ old('description_delay', $descriptionAnimation['delay'] ?? 0.2) == 0 ? 'selected' : '' }}>
                                                            {{ __('page.NO_DELAY') }}</option>
                                                        <option value="1"
                                                            {{ old('description_delay', $descriptionAnimation['delay'] ?? 0.2) == 1 ? 'selected' : '' }}>
                                                            {{ __('page.ONE_SECOND') }}</option>
                                                        <option value="2"
                                                            {{ old('description_delay', $descriptionAnimation['delay'] ?? 0.2) == 2 ? 'selected' : '' }}>
                                                            {{ __('page.TWO_SECONDS') }}</option>
                                                        <option value="3"
                                                            {{ old('description_delay', $descriptionAnimation['delay'] ?? 0.2) == 3 ? 'selected' : '' }}>
                                                            {{ __('page.THREE_SECONDS') }}</option>
                                                    </select>
                                                </div>
                                                <div class="col-md-6">
                                                    <label class="form-label">{{ __('page.DURATION_SECONDS') }}</label>
                                                    <select class="form-select duration-select"
                                                        name="description_duration">
                                                        <option value="1"
                                                            {{ old('description_duration', $descriptionAnimation['duration'] ?? 1) == 1 ? 'selected' : '' }}>
                                                            {{ __('page.ONE_SECOND') }}</option>
                                                        <option value="2"
                                                            {{ old('description_duration', $descriptionAnimation['duration'] ?? 1) == 2 ? 'selected' : '' }}>
                                                            {{ __('page.TWO_SECONDS') }}</option>
                                                        <option value="3"
                                                            {{ old('description_duration', $descriptionAnimation['duration'] ?? 1) == 3 ? 'selected' : '' }}>
                                                            {{ __('page.THREE_SECONDS') }}</option>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Image Animation -->
                                <div class="accordion-item">
                                    <h2 class="accordion-header">
                                        <button class="accordion-button collapsed" type="button"
                                            data-bs-toggle="collapse" data-bs-target="#imageAnimation">
                                            Image Animation
                                        </button>
                                    </h2>
                                    <div id="imageAnimation" class="accordion-collapse collapse"
                                        data-bs-parent="#animationAccordion">
                                        <div class="accordion-body">
                                            <div class="mb-3">
                                                @php
                                                    $imageAnimation =
                                                        $animations[$story->story_slides->first()->id]['image'] ?? [];
                                                @endphp
                                                <label class="form-label">{{ __('page.ANIMATION_TYPE') }}</label>
                                                <select class="form-select" name="image_animation">
                                                    <option value="fade-in"
                                                        {{ isset($imageAnimation['type']) && $imageAnimation['type'] == 'fade-in' ? 'selected' : '' }}>
                                                        Fade In</option>
                                                    <option value="zoom-in"
                                                        {{ isset($imageAnimation['type']) && $imageAnimation['type'] == 'zoom-in' ? 'selected' : '' }}>
                                                        Zoom In</option>
                                                    <option value="slide-in"
                                                        {{ isset($imageAnimation['type']) && $imageAnimation['type'] == 'slide-in' ? 'selected' : '' }}>
                                                        Slide In</option>
                                                </select>
                                            </div>
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <label class="form-label">{{ __('page.DELAY_SECONDS') }}</label>
                                                    <select class="form-select delay-select" name="image_delay">
                                                        <option value="0"
                                                            {{ old('image_delay', $imageAnimation['delay'] ?? 0.4) == 0 ? 'selected' : '' }}>
                                                            {{ __('page.NO_DELAY') }}</option>
                                                        <option value="1"
                                                            {{ old('image_delay', $imageAnimation['delay'] ?? 0.4) == 1 ? 'selected' : '' }}>
                                                            {{ __('page.ONE_SECOND') }}</option>
                                                        <option value="2"
                                                            {{ old('image_delay', $imageAnimation['delay'] ?? 0.4) == 2 ? 'selected' : '' }}>
                                                            {{ __('page.TWO_SECONDS') }}</option>
                                                        <option value="3"
                                                            {{ old('image_delay', $imageAnimation['delay'] ?? 0.4) == 3 ? 'selected' : '' }}>
                                                            {{ __('page.THREE_SECONDS') }}</option>
                                                    </select>
                                                </div>
                                                <div class="col-md-6">
                                                    <label class="form-label">{{ __('page.DURATION_SECONDS') }}</label>
                                                    <select class="form-select duration-select" name="image_duration">
                                                        <option value="1"
                                                            {{ old('image_duration', $imageAnimation['duration'] ?? 1) == 1 ? 'selected' : '' }}>
                                                            {{ __('page.ONE_SECOND') }}</option>
                                                        <option value="2"
                                                            {{ old('image_duration', $imageAnimation['duration'] ?? 1) == 2 ? 'selected' : '' }}>
                                                            {{ __('page.TWO_SECONDS') }}</option>
                                                        <option value="3"
                                                            {{ old('image_duration', $imageAnimation['duration'] ?? 1) == 3 ? 'selected' : '' }}>
                                                            {{ __('page.THREE_SECONDS') }}</option>
                                                    </select>
                                                </div>
                                            </div>

                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card">
                                <div class="card-header">
                                    <h4 class="card-title">{{ __('page.PREVIEW') }}</h4>
                                </div>
                                <div class="card-body">
                                    <div id="animation-preview" class="border rounded p-3">
                                        <div class="preview-placeholder text-center">
                                            <p class="text-muted">{{ __('page.ANIMATION_PREVIEW_PLACEHOLDER') }}</p>
                                        </div>
                                        <div id="previewContent" class="d-none">
                                            <h2 id="previewTitle"></h2>
                                            <p id="previewDescription"></p>
                                            <img id="previewImage" src="" alt="Preview Image"
                                                class="img-fluid" />
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Step 5: Submit Story -->
                <div id="step5" class="step-content d-none">
                    <h3 class="card-title mb-4">{{ __('page.REVIEW_SUBMIT') }}</h3>
                    <p>{{ __('page.REVIEW_BEFORE_SAVING') }}</p>
                </div>
                <!-- Navigation Buttons -->
                <div class="d-flex justify-content-between mt-4">
                    <button type="button" class="btn btn-secondary navigations-button-css" id="prevStep">
                        {{ __('page.PREVIOUS') }}
                    </button>
                    <button type="button" class="btn btn-primary" id="nextStep">
                        {{ __('page.NEXT') }}
                    </button>
                    <button type="submit" class="btn btn-primary navigations-button-css" id="submitForm">
                        {{ __('page.UPDATE_STORY') }}
                    </button>
                </div>
            </form>
        </div>
    </div>

@section('script')
    <script type="text/javascript" src="{{ asset('/assets/js/custom/update_story/story_edit.js') }}?v=<?= time() ?>"></script>
@endsection
@endsection
