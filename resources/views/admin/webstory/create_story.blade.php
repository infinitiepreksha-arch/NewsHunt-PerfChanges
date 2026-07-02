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
    <div class="card admin_cards">
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
            <form id="storyForm" action="{{ route('stories.store') }}" method="POST" enctype="multipart/form-data" class="card border p-3">
                @csrf
                <!-- Step 1: Story Details -->
                <div id="step1" class="step-content">
                    <h3 class="card-title mb-4">{{ __('page.STORY_DETAILS') }}</h3>
                    <div class="mb-3">
                        <label class="form-label required">{{ __('page.STORY TITLE') }}</label>
                        <input type="text" name="title" class="form-control">
                        <div class="invalid-feedback parsley-required error-text"></div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label required">{{ __('page.IMAGE_UPLOAD_TYPE') ?? 'Image Upload Type' }}</label>
                        <div class="d-flex gap-3">
                            <label class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="image_size_type" value="fixed" checked>
                                <span class="form-check-label">{{ __('page.FIXED_SIZE') ?? 'Fixed Size' }} (1080 x 1920)</span>
                            </label>
                            <label class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="image_size_type" value="random">
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
                            <label for="news_language_id" class="form-label">{{ __('page.SELECT_NEWSLANGUAGE') }}<span
                                    class="text-danger">*</span></label>
                            <select class="form-control form-select select2" id="news_language_id" name="news_language_id">
                                <option value="" disabled selected>{{ __('page.SELECT_NEWS_LANGUAGE') }}</option>
                                @foreach ($news_languages as $news_language)
                                    <option value="{{ $news_language->id }}"
                                        {{ isset($selected_language_id) && $selected_language_id == $news_language->id ? 'selected' : '' }}>
                                        {{ $news_language->name }}
                                    </option>
                                @endforeach
                            </select>
                            <span class="invalid-feedback parsley-required error-text">
                                <strong id="news_language_id-error-message "></strong>
                            </span>
                        </div>
                    @else
                        <div class="form-group mb-3">
                            <label for="news_language_id" class="form-label">{{ __('page.SELECT_NEWSLANGUAGE') }}</label>
                            <div class="alert alert-warning mb-0 rounded py-2">
                                <i class="fas fa-exclamation-triangle me-2"></i>
                                {{ __('message.NO_PERMISSION_NEWSLANGUAGE') }}
                            </div>
                        </div>
                    @endcan

                    @can('select-topic-for-story')
                        <div class="mb-3 topic-none d-none">
                            <label class="form-label required">{{ __('page.SELECT_TOPIC') }}</label>
                            <select name="topic_id" id="select-topic" class="form-select">
                                <option value="">{{ __('page.SELECT_TOPIC') }}</option>
                                @foreach ($topic as $single_topic)
                                    <option value="{{ $single_topic->id }}">{{ $single_topic->name }}</option>
                                @endforeach
                            </select>
                            <div class="invalid-feedback parsley-required error-text"></div>
                        </div>
                    @else
                        <div class="mb-3">
                            <label class="form-label required">{{ __('page.SELECT_TOPIC') }}</label>
                            <div class="alert alert-warning mb-0 rounded py-2">
                                <i class="fas fa-exclamation-triangle me-2"></i>
                                {{ __('message.NO_PERMISSION_TOPIC') }}
                            </div>
                        </div>
                    @endcan
                </div>


                <!-- Step 2: Add Slides -->
                <div id="step2" class="step-content d-none">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h3 class="card-title mb-0 fw-bold">{{ __('page.ADD_SLIDES') }}</h3>
                        <button type="button" class="btn btn-primary" id="addMoreSlides">
                            {{ __('page.ADD_NEW_SLIDE') }}
                        </button>
                    </div>

                    <div id="noSlidesMessage" class="text-center">
                        <div class="d-flex justify-content-center mb-0">
                            <div class="col-6 col-md-8 col-lg-4">
                                <img src="{{ asset('assets/images/access_Denied/emptyImage.png') }}"
                                alt="Empty Data">
                            </div>
                        </div>
                    </div>

                    <div class="accordion" id="accordionSlides">
                        <!-- Slides will be added here dynamically -->
                    </div>
                </div>

                <!-- Step 3: Order Slides -->
                <div id="step3" class="step-content d-none">
                    <h3 class="card-title mb-4">{{ __('page.ORDER_SLIDES') }}</h3>
                    <div id="slides-order" class="example-list">
                        <!-- Slides will be populated here for ordering -->
                    </div>
                </div>

                <!-- Step 4: Animations -->
                <div id="step4" class="step-content d-none">
                    <div class="row">
                        <div class="col-md-8">
                            <h3 class="card-title mb-4">{{ __('page.ADD_ANIMATIONS') }}</h3>
                            <div class="accordion" id="animationAccordion">
                                <!-- Title Animation -->
                                <div class="accordion-animation">
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
                                                <label class="form-label">{{ __('page.ANIMATION_TYPE') }}</label>
                                                <select class="form-select" name="title_animation">
                                                    <option value="fade-in">Fade In</option>
                                                    <option value="slide-up">Slide Up</option>
                                                    <option value="slide-down">Slide Down</option>
                                                </select>
                                            </div>
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <label class="form-label">{{ __('page.DELAY_SECONDS') }}</label>
                                                    <select class="form-select delay-select" data-target="title_delay">
                                                        <option value="0">{{ __('page.NO_DELAY') }}</option>
                                                        <option value="1">{{ __('page.ONE_SECOND') }}</option>
                                                        <option value="2">{{ __('page.TWO_SECONDS') }}</option>
                                                        <option value="3">{{ __('page.THREE_SECONDS') }}</option>
                                                    </select>
                                                    <input type="number" name="title_delay" hidden>
                                                </div>
                                                <div class="col-md-6">
                                                    <label class="form-label">{{ __('page.DURATION_SECONDS') }}</label>
                                                    <select class="form-select duration-select"
                                                        data-target="title_duration">
                                                        <option value="1">{{ __('page.ONE_SECOND') }}</option>
                                                        <option value="2">{{ __('page.TWO_SECONDS') }}</option>
                                                        <option value="3">{{ __('page.THREE_SECONDS') }}</option>
                                                    </select>
                                                    <input type="number" name="title_duration" hidden>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Description Animation -->
                                <div class="accordion-animation">
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
                                                <label class="form-label">{{ __('page.ANIMATION_TYPE') }}</label>
                                                <select class="form-select" name="description_animation">
                                                    <option value="fade-in">Fade In</option>
                                                    <option value="slide-up">Slide Up</option>
                                                    <option value="slide-down">Slide Down</option>
                                                </select>
                                            </div>
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <label class="form-label">{{ __('page.DURATION_SECONDS') }}</label>
                                                    <select class="form-select delay-select"
                                                        data-target="description_delay">
                                                        <option value="0">{{ __('page.NO_DELAY') }}</option>
                                                        <option value="1">{{ __('page.ONE_SECOND') }}</option>
                                                        <option value="2">{{ __('page.TWO_SECONDS') }}</option>
                                                        <option value="3">{{ __('page.THREE_SECONDS') }}</option>
                                                    </select>
                                                    <input type="number" name="description_delay" hidden>
                                                </div>
                                                <div class="col-md-6">
                                                    <label class="form-label">Duration (seconds)</label>
                                                    <select class="form-select duration-select"
                                                        data-target="description_duration">
                                                        <option value="1">{{ __('page.ONE_SECOND') }}</option>
                                                        <option value="2">{{ __('page.TWO_SECONDS') }}</option>
                                                        <option value="3">{{ __('page.THREE_SECONDS') }}</option>
                                                    </select>
                                                    <input type="number" name="description_duration" hidden>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Image Animation -->
                                <div class="accordion-animation">
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
                                                <label class="form-label">{{ __('page.ANIMATION_TYPE') }}</label>
                                                <select class="form-select" name="image_animation">
                                                    <option value="fade-in">Fade In</option>
                                                    <option value="zoom-in">Zoom In</option>
                                                    <option value="slide-in">Slide In</option>
                                                </select>
                                            </div>
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <label class="form-label">{{ __('page.DELAY_SECONDS') }}</label>
                                                    <select class="form-select delay-select" data-target="image_delay">
                                                        <option value="0">{{ __('page.NO_DELAY') }}</option>
                                                        <option value="1">{{ __('page.ONE_SECOND') }}</option>
                                                        <option value="2">{{ __('page.TWO_SECONDS') }}</option>
                                                        <option value="3">{{ __('page.THREE_SECONDS') }}</option>
                                                    </select>
                                                    <input type="number" name="image_delay" hidden>
                                                </div>
                                                <div class="col-md-6">
                                                    <label class="form-label">{{ __('page.DURATION_SECONDS') }}s</label>
                                                    <select class="form-select duration-select"
                                                        data-target="image_duration">
                                                        <option value="1">{{ __('page.ONE_SECOND') }}</option>
                                                        <option value="2">{{ __('page.TWO_SECONDS') }}</option>
                                                        <option value="3">{{ __('page.THREE_SECONDS') }}</option>
                                                    </select>
                                                    <input type="number" name="image_duration" hidden>
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
                    <h3 class="card-title mb-4">{{ __('page.SAVE_STORY') }}</h3>
                    <p>{{ __('page.REVIEW_BEFORE_SAVING') }}</p>
                    <p>{{ __('page.ONCE_SAVED_EDITABLE') }}</p>
                </div>

                <!-- Navigation Buttons -->
                <div class="d-flex justify-content-between mt-4">
                    <button type="button" class="btn btn-secondary navigations-button-css" id="prevStep">
                        {{ __('page.PREVIOUS') }}
                    </button>
                    <button type="button" class="btn btn-primary navigations-button-css" id="nextStep">
                        {{ __('page.NEXT') }}
                    </button>
                    <button type="submit" class="btn btn-success navigations-button-css" id="submitForm">
                        {{ __('page.SAVE_STORY') }}
                    </button>
                </div>
            </form>
        </div>
    </div>
@section('script')
    <script type="text/javascript" src="{{ asset('/assets/js/custom/create_story/story.js') }}?v=<?= time() ?>"></script>
@endsection
@endsection
