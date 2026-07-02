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
                <a href="{{ url('admin/e-newspapers') }}">{{ __('page.E_NEWSPAPERS_AND_MAGAZINES') }}/</a>
                @yield('pre-title')
            </div>
            <h2 class="page-title">
                @yield('title')
            </h2>
        </div>
    </div>
@endsection

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">{{ $title }}</h3>
                    </div>
                    <!-- Add Channel Modal -->
                    <div id="addENewspaperModal">
                        <form action="{{ route('e-newspapers.store') }}" method="POST" enctype="multipart/form-data"
                            id="createENewspaperForm">
                            @csrf
                            <div class="card-body">
                                <div class="row row-cards">

                                    @can('select-newslanguage-for-enewspapaer')
                                        <div class="alert alert-info mb-0 rounded py-2 ms-2">
                                            <i class="fas fa-info-circle me-2"></i>
                                            {{ __('page.SELECT_NEWSLANGUAGE_FIRST') }}
                                        </div>
                                        <div class="col-sm-6 col-md-6 mb-2">
                                            <label for="news_language_id"
                                                class="form-label  mb-2">{{ __('page.SELECT_NEWSLANGUAGE') }}<span
                                                    class="text-danger">*</span></label>
                                            <select class="form-control form-select select2" id="news_language_id"
                                                name="news_language_id">
                                                <option value="" disabled selected>
                                                    {{ __('page.SELECT_NEWS_LANGUAGE') }}
                                                </option>
                                                @foreach ($newsLanguages as $news_language)
                                                    <option value="{{ $news_language->id }}"
                                                        {{ old('news_language_id') == $news_language->id ? 'selected' : '' }}>
                                                        {{ $news_language->name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            <span class="parsley-required"><strong id="news_language_id-error"></strong></span>

                                        </div>
                                    @else
                                        <div class="form-group">
                                            <label for="news_language_id"
                                                class="form-label  mb-2">{{ __('page.SELECT_NEWSLANGUAGE') }}<span
                                                    class="text-danger">*</span></label>
                                            <div class="alert alert-warning mb-0 rounded py-2">
                                                <i class="fas fa-exclamation-triangle me-2"></i>
                                                {{ __('message.NO_PERMISSION_NEWSLANGUAGE') }}
                                            </div>
                                        </div>
                                    @endcan

                                    @can('select-channel-for-enewspapaer')
                                        <div class="col-sm-6 col-md-6 mb-2 d-none">
                                            <label for="channel_id"
                                                class="form-label  mb-2">{{ __('page.SELECT_CHANNEL') }}<span
                                                    class="text-danger">*</span></label>
                                            <select id="add_channel_id" class="form-control form-select channel-custom-select"
                                                name="channel_id">
                                                <option value="" disabled selected>{{ __('page.SELECT_CHANNEL') }}
                                                </option>
                                                @foreach ($channel_filters as $channel)
                                                    <option value="{{ $channel->id }}"
                                                        {{ isset($post->channel_id) ? ($channel->id == $post->channel_id ? 'selected' : '') : '' }}>
                                                        {{ $channel->name }}</option>
                                                @endforeach
                                            </select>
                                            <span class="parsley-required">
                                                <strong id="channel_id-error"></strong>
                                            </span>
                                        </div>
                                    @else
                                        <div class="col-sm-6 col-md-6 mt-3">
                                            <label for="channel_id"
                                                class="form-label  mb-2">{{ __('page.SELECT_CHANNEL') }}<span
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
                                                <option value="" disabled selected>{{ __('page.SELECT_TOPIC') }}</option>
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
                                                <i
                                                    class="fas fa-exclamation-triangle me-2"></i>{{ __('message.NO_PERMISSION_TOPIC') }}
                                            </div>
                                               <span class="parsley-required"><strong id="topic_id-error"></strong></span>
                                        </div>
                                    @endcan

                                    <div class="col-sm-6 col-md-6 mt-3 mb-2">
                                        <label for="date" class="form-label  mb-2">{{ __('page.DATE') }}</label>
                                        <input type="date" name="date" class="form-control"
                                            value="{{ old('date', date('Y-m-d')) }}" id="date">
                                        <span class="parsley-required"><strong id="date-error"></strong></span>

                                    </div>

                                    <div class="col-sm-6 col-md-6 mt-3 mb-2">
                                        <label for="type" class="form-label  mb-2">{{ __('page.TYPE') }}</label>
                                        <select id="type" class="form-control form-select" name="type">
                                            <option value="paper" {{ old('type', 'paper') == 'paper' ? 'selected' : '' }}>
                                                {{ __('page.NEWSPAPER') }}</option>
                                            <option value="magazine" {{ old('type') == 'magazine' ? 'selected' : '' }}>
                                                {{ __('page.MAGAZINE') }}</option>
                                        </select>
                                        <span class="parsley-required"><strong id="type-error"></strong></span>

                                    </div>

                                    <div class="col-sm-6 col-md-6 mt-3 mb-2">
                                        <label for="update-file" class="form-label mb-2">{{ __('page.PDF_FILE') }}<span
                                                class="text-danger">*</span></label>
                                        <div class="col-12">
                                            <input name="pdf_file" hidden id="update-file" type="file" class="zip-pond"
                                                accept=".pdf">
                                        </div>
                                        <span class="parsley-required"><strong id="pdf_file-error"></strong></span>
                                    </div>

                                    <div class="col-sm-6 col-md-6 mt-3 mb-2">
                                        <label for="update-thumbnail"
                                            class="form-label mb-2">{{ __('page.THUMBNAIL') }}<span
                                                class="text-danger">*</span></label>
                                        <div class="col-12  mb-2">
                                            <input name="thumbnail" hidden id="update-thumbnail" type="file"
                                                class="zip-pond" accept="image/*">
                                        </div>
                                        <span class="parsley-required"><strong id="thumbnail-error"></strong></span>
                                    </div>

                                    <div class="col-sm-6 col-md-6 mt-3 mb-2">
                                        <label for="update-background"
                                            class="form-label mb-2">{{ __('page.BACKGROUND_IMAGE') }}<span
                                                class="text-danger">*</span></label>
                                        <div class="col-12  mb-2">
                                            <input name="background_image" hidden id="update-background" type="file"
                                                class="zip-pond" accept="image/*">
                                        </div>
                                        <span class="parsley-required"><strong id="background_image-error"></strong></span>
                                    </div>
                                </div>
                                <div class="modal-footer gap-2">
                                    <a href="{{ route('e-newspapers.index') }}" id="back_button"
                                        class="btn btn-secondary">{{ __('page.BACK') }}</a>
                                    <button type="submit" id="submite_button"
                                        class="btn btn-primary waves-effect waves-light">{{ __('page.SAVE') }}</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
