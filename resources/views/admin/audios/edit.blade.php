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
                <a href="{{ url('admin/posts') }}">{{ __('page.AUDIO_POSTS') }}/</a>
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
            <form action="{{ url($url) }}" class="form-horizontal" enctype="multipart/form-data" id="editAudioPostForm"
                method="POST" data-parsley-validate>
                @csrf
                <div class="card-body">
                    <div class="row row-cards">
                        <div class="col-sm-12 col-md-12">
                            <label for="add-post-title" class="form-label col-12">{{ __('page.TITLE') }}<span
                                    class="text-danger">*</span></label>
                            <input type="text" name="title" class="form-control" placeholder="Please enter post title"
                                value="{{ $post->title ?? '' }}" id="add-post-title">
                            <span class="text-danger gap-1">
                                <strong id="title-error-message"></strong>
                            </span>
                        </div>

                        <div class="form-group mt-3">
                            <label for="add-post-description" class="form-label">{{ __('page.POST_DESCRIPTION') }}<span
                                    class="text-danger">*</span></label>
                            <textarea name="description" class="form-control" placeholder="Please enter post description" id="tinymce_editor"
                                aria-label="tinymce_editor" rows="3">{{ $post->description ?? '' }}</textarea>
                            <span class="text-danger">
                                <strong id="description-error-message"></strong>
                            </span>
                        </div>

                   @can('select-newslanguage-for-post')
                            <div class="alert alert-primary mb-0 rounded py-2 ms-2">
                                <i class="fas fa-info-circle me-2"></i>
                                {{ __('page.SELECT_NEWSLANGUAGE_FIRST') }}
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
                                <span class="text-danger">
                                    <strong id="news_language-error-message"></strong>
                                </span>
                            </div>
                        @else
                            <div class="col-sm-6 col-md-6 mt-3">
                                <label for="news_language_id" class="form-label">{{ __('page.SELECT_NEWSLANGUAGE') }}<span
                                        class="text-danger">*</span></label>
                                <div class="alert alert-warning mb-0 rounded py-2">
                                    <i class="fas fa-exclamation-triangle me-2"></i>
                                    {{ __('message.NO_PERMISSION_NEWSLANGUAGE') }}
                                </div>
                            </div>
                        @endcan


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
                                <span class="text-danger">
                                    <strong id="channel-error-message"></strong>
                                </span>
                            </div>
                        @else
                            <div class="col-sm-6 col-md-6 mt-3">
                                <label for="channel_id" class="form-label">{{ __('page.SELECT_CHANNEL') }}<span
                                        class="text-danger">*</span></label>
                                <div class="alert alert-warning mb-0 rounded py-2">
                                    <i class="fas fa-exclamation-triangle me-2"></i>
                                    {{ __('message.NO_PERMISSION_CHANNEL') }}
                                </div>
                            </div>
                        @endcan

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
                                <span class="text-danger">
                                    <strong id="topic-error-message"></strong>
                                </span>
                            </div>
                        @else
                            <div class="col-sm-6 col-md-6 mt-3">
                                <label for="topic_id" class="form-label">{{ __('page.SELECT_TOPIC') }}<span
                                        class="text-danger">*</span></label>
                                <div class="alert alert-warning mb-0 rounded py-2">
                                    <i class="fas fa-exclamation-triangle me-2"></i>
                                    {{ __('message.NO_PERMISSION_TOPIC') }}
                                </div>
                            </div>
                        @endcan

                        <input type="hidden" name="post_type" value="audio" id="select_type_posts">

                        <div class="col-sm-6 col-md-6">
                            <label for="add-post-status" class="form-label">{{ __('page.STATUS') }}</label>
                            <select class="form-control form-select" name="status" id="add-post-status">
                                <option
                                    value="active"{{ isset($post->status) ? ($post->status == 'active' ? 'selected' : '') : '' }}>
                                    {{ __('page.ACTIVE') }}</option>
                                <option
                                    value="inactive"{{ isset($post->status) ? ($post->status == 'inactive' ? 'selected' : '') : '' }}>
                                    {{ __('page.INACTIVE') }}</option>
                            </select>
                            <span class="text-danger">
                                <strong id="status-error-message"></strong>
                            </span>
                        </div>

                        {{-- Audio File --}}
                        <div class="col-sm-6 col-md-6 mt-3">
                            <label for="audio-file-input" class="form-label">{{ __('page.AUDIO_FILE') }}
                                <span class="text-danger">*</span></label>
                            <input type="file" name="audio" id="audio-file-input" class="form-control"
                                accept="audio/*">
                            <span class="text-danger"><strong id="audio-error-message"></strong></span>

                            <div class="mt-3">
                                <audio id="audio-preview" controls style="width: 100%;">
                                    <source src="{{ $post->audio ?? '' }}" type="audio/mpeg">
                                    {{ __('page.NO_AUDIO_SELECTED') }}
                                </audio>
                            </div>
                        </div>

                        <div class="col-sm-6 col-md-6" id="posts_image_upload">
                            <label for="audio-image-input" class="form-label">{{ __('page.IMAGE') }}</label>
                            <input type="file" name="image" id="audio-image-input" class="form-control"
                                accept="image/*">
                            <span class="text-danger">
                                <strong id="image-error-message"></strong>
                            </span>
                            <div class="mt-3">
                                <img id="audio-image-preview"
                                    src="{{ $post->image ?? asset('assets/images/no_image_available.png') }}"
                                    alt="img Preview" class="img-preview img-fluid">
                            </div>
                            <!-- Hidden post image container for cropping -->
                            <div id="cropper-container" class="d-none">
                                <img id="cropper-image" src="" alt="Crop img" />
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer gap-2">
                        <a href="{{ url('admin/audios') }}" id="audio_back_button"
                            class="btn btn-secondary">{{ __('page.BACK') }}</a>
                        <button type="submit" id="audio_update_submite_button"
                            class="btn btn-primary waves-effect waves-light">{{ __('page.SAVE') }}</button>
                    </div>
            </form>
        </div>
    </section>
@endsection
