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
                <a href="{{ url('admin/videos') }}">{{ __('page.VIDEO_POSTS') }}/</a>
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
            <form action="{{ url($url) }}" class="form-horizontal" enctype="multipart/form-data" id="editVideoPostForm"
                method="{{ $method }}" data-parsley-validate>
                @csrf
                <div class="card-body">
                    <div class="row row-cards">

                        <div class="col-sm-12 col-md-12">
                            <label for="add-post-title" class="form-label col-12">{{ __('page.TITLE') }}<span
                                    class="text-danger">*</span></label>
                            <input type="text" name="title" class="form-control" placeholder="Please enter post title"
                                value="{{ $post->title ?? '' }}" id="add-post-title">
                            <span class="text-danger mt-1 d-block"><strong id="title-error"></strong></span>
                        </div>

                        <div class="form-group mt-3">
                            <label for="add-post-description" class="form-label">{{ __('page.POST_DESCRIPTION') }}<span
                                    class="text-danger">*</span></label>
                            <textarea name="description" class="form-control" placeholder="Please enter post description" id="tinymce_editor"
                                aria-label="tinymce_editor" rows="3" >{{ $post->description ?? '' }}</textarea>
                            <span class="text-danger mt-1 d-block"><strong id="description-error"></strong></span>
                        </div>
                        <div class="alert alert-primary mb-0 rounded py-2 ms-2">
                            <i class="fas fa-info-circle me-2"></i>
                            {{ __('page.SELECT_NEWSLANGUAGE_FIRST') }}
                        </div>
                        <div class="col-sm-6 col-md-6 mt-3">
                            <label for="news_language_id" class="form-label">{{ __('page.SELECT_NEWSLANGUAGE') }}<span
                                    class="text-danger">*</span></label>
                            <select class="form-control form-select select2" id="news_language_id" name="news_language_id"
                                required>
                                <option value="" disabled selected>{{ __('page.SELECT_NEWS_LANGUAGE') }}</option>
                                @foreach ($news_languages as $news_language)
                                    <option value="{{ $news_language->id }}"
                                        {{ isset($post->news_language_id) && $post->news_language_id == $news_language->id ? 'selected' : '' }}>
                                        {{ $news_language->name }}
                                    </option>
                                @endforeach
                            </select>
                            <span class="text-danger mt-1 d-block"><strong id="news_language_id-error"></strong></span>
                        </div>
                        <div class="col-sm-6 col-md-6 mt-3 d-none">
                            <label for="channel_id" class="form-label">{{ __('page.SELECT_CHANNEL') }}<span
                                    class="text-danger">*</span></label>
                            <select id="add_channel_id" class="form-control form-select channel-custom-select"
                                name="channel_id">
                                <option value="" disabled selected>{{ __('page.SELECT_CHANNEL') }}</option>
                                @foreach ($channel_filters as $channel)
                                    <option value="{{ $channel->id }}"
                                        {{ isset($post->channel_id) ? ($channel->id == $post->channel_id ? 'selected' : '') : '' }}>
                                        {{ $channel->name }}</option>
                                @endforeach
                            </select>
                            <span class="text-danger mt-1 d-block"><strong id="channel_id-error"></strong></span>
                        </div>


                        <input type="hidden" name="post_type" value="video" id="select_type_posts">
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

                        </div>

                        <div class="col-sm-6 col-md-6" id="posts_image_upload">
                            <label for="post-image-input" class="form-label">{{ __('page.IMAGE') }}<span
                                    class="text-danger">*</span></label>
                            <input type="file" name="image" id="post-image-input" class="form-control"
                                accept="image/*">
                            <span class="text-danger mt-1 d-block"><strong id="image-error"></strong></span>
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

                        <div class="col-sm-6 col-md-6 d-none" id="video_thumbnail">
                            <label for="video-thumb-input" class="form-label">{{ __('page.THUMBNAIL') }}<span
                                    class="text-danger">*</span></label>
                            <input type="file" name="thumb_image" id="video-thumb-input" class="form-control"
                                accept="image/*">
                            <span class="text-danger mt-1 d-block"><strong id="thumb_image-error"></strong></span>
                            <div class="mt-3">
                                <img id="video-thumb-preview"
                                    src="{{ $post->video_thumb ?? asset('assets/images/no_image_available.png') }}"
                                    alt="img Preview" class="img-preview img-fluid">
                            </div>
                            <!-- Hidden thumb image container for cropping -->
                            <div id="thumb-cropper-container" class="d-none">
                                <img id="video-thumb-cropped" src="" alt="Crop img" />
                            </div>
                        </div>

                        <div class="col-sm-6 col-md-6 d-none" id="video_file">
                            <label for="post-image-input" class="form-label">{{ __('page.VIDEO') }}<span
                                    class="text-danger">*</span></label>
                            <input type="file" name="video" id="post-image-input" class="form-control"
                                accept="video/*" onchange="readChapterVideo(this)";>
                            <span class="text-danger mt-1 d-block"><strong id="video-error"></strong></span>
                            <div class="mt-3">
                                <video class="video-thumb preview-video" width="300" height="150"
                                    controls="controls">
                                    <source src="{{ $post->video ?? '' }}" id="video-preview" type="video/mp4">
                                    <track src="descriptions_en.vtt" kind="descriptions" srclang="en"
                                        label="English Descriptions">
                                </video>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer gap-2">
                        <a href="{{ url('admin/videos') }}" id="video_back_button"
                            class="btn btn-secondary">{{ __('page.BACK') }}</a>
                        <button type="submit" id="video_update_submite_button"
                            class="btn btn-primary waves-effect waves-light">{{ __('page.SAVE') }}</button>
                    </div>
                </div>
            </form>
        </div>
    </section>
@endsection
