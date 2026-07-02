@extends('admin.layouts.main')

@section('title')
    {{ $title }}
@endsection
@section('pre-title')
    {{ $title }}
@endsection
@section('css')
    <style>
        /* Hide FilePond's internal file list/previews */
        .filepond--list {
            display: none !important;
        }
    </style>
@endsection
@section('page-title')
    <div class="row g-2 align-items-center">
        <div class="col">
            <div class="page-pretitle">
                <a href="{{ url('admin/dashboard') }}">{{ __('page.HOME') }}/</a>
                <a href="{{ url('admin/posts') }}">{{ __('page.IMAGE_POSTS') }}/</a>
                @yield('pre-title')
            </div>
            <h2 class="page-title">
                @yield('title')
            </h2>
        </div>
        <!-- Page title actions -->
        <div class="col-auto ms-auto d-print-none">
        </div>
    </div>
@endsection
@section('content')
    <section class="section">
        <div class="card admin_cards">
            <div class="card-header">
                <h3 class="card-title">{{ __('page.DETAILS') }}</h3>
            </div>
            {{-- @dd($hasExtraImagesCount) --}}
            <form action="{{ url($url) }}" class="form-horizontal" enctype="multipart/form-data"
                id="{{ $formID }}" method="{{ $method }}" data-parsley-validate
                data-has-extra-images="{{ $hasExtraImages ?? false }}"
                data-old-extra-images="{{ json_encode($post->images ?? []) }}"
                data-old-extra-images-count="{{ $hasExtraImagesCount }}">
                @csrf

                <div class="card-body">
                    <div class="row row-cards">
                        <!-- Title -->
                        <div class="col-sm-12 col-md-12">
                            <label for="add-post-title" class="form-label col-12">{{ __('page.TITLE') }}<span
                                    class="text-danger">*</span></label>
                            <input type="text" name="title" class="form-control" placeholder="Please enter post title"
                                value="{{ $post->title ?? '' }}" id="add-post-title">
                            <span class="parsley-required"><strong id="title-error"></strong></span>
                        </div>

                        <!-- Description -->
                        <div class="form-group mt-3">
                            <label for="add-post-description" class="form-label">{{ __('page.POST_DESCRIPTION') }}<span
                                    class="text-danger">*</span></label>
                            <textarea name="description" class="form-control" placeholder="Please enter post description" id="tinymce_editor"
                                aria-label="tinymce_editor" rows="3">{{ $post->description ?? '' }}</textarea>
                            <span class="parsley-required"><strong id="description-error"></strong></span>
                        </div>

                        <!-- News Language -->
                        @can('select-newslanguage-for-post')
                            <div class="alert alert-primary mb-0 rounded py-2 ms-2">
                                <i class="fas fa-info-circle me-2"></i>{{ __('page.SELECT_NEWSLANGUAGE_FIRST') }}
                            </div>
                            <div class="col-sm-6 col-md-6 mt-3">
                                <label for="news_language_id" class="form-label">{{ __('page.SELECT_NEWSLANGUAGE') }}<span
                                        class="text-danger">*</span></label>
                                <select class="form-control form-select select2" id="news_language_id" name="news_language_id">
                                    <option value="" disabled selected>{{ __('page.SELECT_NEWS_LANGUAGE') }}</option>
                                    @foreach ($news_languages as $news_language)
                                        <option value="{{ $news_language->id }}"
                                            {{ isset($post->news_language_id) && $post->news_language_id == $news_language->id ? 'selected' : '' }}>
                                            {{ $news_language->name }}
                                        </option>
                                    @endforeach
                                </select>
                                <span class="parsley-required"><strong id="news_language_id-error"></strong></span>
                            </div>
                        @else
                            <div class="col-sm-6 col-md-6 mt-3">
                                <label for="news_language_id" class="form-label">{{ __('page.SELECT_NEWSLANGUAGE') }}<span
                                        class="text-danger">*</span></label>
                                <div class="alert alert-warning mb-0 rounded py-2">
                                    <i
                                        class="fas fa-exclamation-triangle me-2"></i>{{ __('message.NO_PERMISSION_NEWSLANGUAGE') }}
                                </div>
                            </div>
                        @endcan

                        <!-- Channel -->
                        @can('select-channel-for-post')
                            <div class="col-sm-6 col-md-6 mt-3 d-none">
                                <label for="channel_id" class="form-label">{{ __('page.SELECT_CHANNEL') }}<span
                                        class="text-danger">*</span></label>
                                <select id="add_channel_id" class="form-control form-select channel-custom-select"
                                    name="channel_id">
                                    <option value="" selected>{{ __('page.SELECT_CHANNEL') }}</option>
                                    @foreach ($channel_filters as $channel)
                                        <option value="{{ $channel->id }}"
                                            {{ isset($post->channel_id) ? ($channel->id == $post->channel_id ? 'selected' : '') : '' }}>
                                            {{ $channel->name }}</option>
                                    @endforeach
                                </select>
                                <span class="parsley-required"><strong id="channel_id-error"></strong></span>
                            </div>
                        @else
                            <div class="col-sm-6 col-md-6 mt-3">
                                <label for="channel_id" class="form-label">{{ __('page.SELECT_CHANNEL') }}<span
                                        class="text-danger">*</span></label>
                                <div class="alert alert-warning mb-0 rounded py-2">
                                    <i class="fas fa-exclamation-triangle me-2"></i>{{ __('message.NO_PERMISSION_CHANNEL') }}
                                </div>
                            </div>
                        @endcan

                        <!-- Topic -->
                        @can('select-topic-for-post')
                            <div class="col-sm-6 col-md-6 mt-3 d-none">
                                <label for="topic_id" class="form-label">{{ __('page.SELECT_TOPIC') }}<span
                                        class="text-danger">*</span></label>
                                <select id="select-topic" class="form-control form-select" name="topic_id">
                                    <option value="" selected>{{ __('page.SELECT_TOPIC') }}</option>
                                    @foreach ($news_topics as $topic)
                                        <option value="{{ $topic->id }}"
                                            {{ isset($post->topic_id) ? ($topic->id == $post->topic_id ? 'selected' : '') : '' }}>
                                            {{ $topic->name }}</option>
                                    @endforeach
                                </select>
                                <span class="parsley-required"><strong id="topic_id-error"></strong></span>
                            </div>
                        @else
                            <div class="col-sm-6 col-md-6 mt-3">
                                <label for="topic_id" class="form-label">{{ __('page.SELECT_TOPIC') }}<span
                                        class="text-danger">*</span></label>
                                <div class="alert alert-warning mb-0 rounded py-2">
                                    <i class="fas fa-exclamation-triangle me-2"></i>{{ __('message.NO_PERMISSION_TOPIC') }}
                                </div>
                            </div>
                        @endcan

                        <!-- Post Type -->
                        <input type="hidden" name="post_type" value="post" id="select_type_posts">

                        <!-- Status -->
                        <div class="col-sm-6 col-md-6 mt-3">
                            <label for="add-post-status" class="form-label">{{ __('page.STATUS') }}</label>
                            <select class="form-control form-select" name="status" id="add-post-status">
                                <option value="active"
                                    {{ isset($post->status) ? ($post->status == 'active' ? 'selected' : '') : '' }}>
                                    {{ __('page.ACTIVE') }}</option>
                                <option value="inactive"
                                    {{ isset($post->status) ? ($post->status == 'inactive' ? 'selected' : '') : '' }}>
                                    {{ __('page.INACTIVE') }}</option>
                            </select>
                            <span class="parsley-required"><strong id="status-error"></strong></span>
                        </div>

                        <div class="col-sm-6 col-md-6" id="posts_image_upload">
                            <label for="post-image-input" class="form-label">{{ __('page.IMAGE') }}</label>
                            <input type="file" name="image" id="post-image-input" class="form-control"
                                accept="image/*">
                            <span class="parsley-required"><strong id="image-error"></strong></span>
                            <div class="mt-3">
                                <img id="post-image-preview"
                                    src="{{ $post->image ?? asset('assets/images/no_image_available.png') }}"
                                    alt="img Preview" class="img-preview img-fluid">
                            </div>
                            <!-- Hidden post image container for cropping -->
                            <div id="cropper-container" class="d-none">
                                <img id="cropper-image" src="" alt="Crop img" />
                            </div>
                        </div>

                        <!-- Three-Column Layout -->
                        <div class="col-12 mt-4">
                            <div class="row">
                                <div class="col-md-12 border border-secondary p-2 rounded mt-4" id="extra_images_option_container">
                                    <div class="card">
                                        <div class="card-body text-center border">
                                            <div class="mb-3">
                                                <i class="fas fa-images fa-3x text-primary"></i>
                                            </div>
                                            <h6 class="card-title">{{ __('page.EXTRA_IMAGES') }}</h6>
                                            <p class="card-text small text-muted">Add multiple images to your post with
                                                previews</p>
                                            <div class="form-check form-switch d-flex justify-content-center">
                                                <input class="form-check-input" type="checkbox" id="enableExtraImages"
                                                    style="width: 3rem; height: 1.5rem;"
                                                    {{ $hasExtraImages ?? false ? 'checked' : '' }}>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="accordion mt-2" id="imagesAccordion">
                                        <div class="accordion-item d-none" id="extraImagesSection">
                                            <h2 class="accordion-header" id="imagesHeading">
                                                <button class="accordion-button collapsed" type="button"
                                                    data-bs-toggle="collapse" data-bs-target="#imagesCollapse"
                                                    aria-expanded="false" aria-controls="imagesCollapse">
                                                    <i class="fas fa-images me-2"></i>{{ __('page.EXTRA_IMAGES') }}
                                                    <span class="badge bg-white text-primary border border-primary ms-2"
                                                        id="extraImagesCount">0</span>
                                                </button>
                                            </h2>
                                            <div id="imagesCollapse" class="accordion-collapse collapse"
                                                aria-labelledby="imagesHeading" data-bs-parent="#imagesAccordion">
                                                <div class="accordion-body">
                                                    <!-- Drag & Drop Zone -->
                                                    <div class="mb-4">
                                                        <label class="form-label fw-bold">{{ __('page.UPLOAD_IMAGES') != 'page.UPLOAD_IMAGES' ? __('page.UPLOAD_IMAGES') : 'Upload Images' }}</label>
                                                        <input type="file" name="extra_images[]" id="extra-images-dropzone" 
                                                            multiple 
                                                            data-allow-reorder="true"
                                                            data-max-file-size="5MB">
                                                        <span class="parsley-required"><strong id="extra_images-error"></strong></span>
                                                    </div>

                                                    <!-- Existing Images Preview (for Edit Mode) -->
                                                    <div id="existingImagesContainer" class="row g-2 mt-3">
                                                        <!-- Existing images will be appended here via JS -->
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer gap-2 mt-4">
                        <a href="{{ url('admin/posts') }}" id="back_button" class="btn btn-secondary">
                            <i class="fas fa-arrow-left me-2"></i>{{ __('page.BACK') }}
                        </a>
                        <button type="submit" id="submite_button" class="btn btn-primary waves-effect waves-light">
                            <i class="fas fa-save me-2"></i>{{ __('page.SAVE') }}
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </section>
@endsection
