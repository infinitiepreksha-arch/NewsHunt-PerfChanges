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
                <a href="{{ route('e-newspapers.index') }}">{{ __('page.E_NEWSPAPERS_AND_MAGAZINES') }}/</a>
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

                    <form action="{{ route('e-newspapers.update', $e_newspaper->id) }}" method="POST"
                        enctype="multipart/form-data">
                        @csrf
                        @method('PUT')

                        <div class="card-body">
                            <div class="row row-cards">
                                <div class="alert alert-info mb-0 rounded py-2 ms-2">
                                    <i class="fas fa-info-circle me-2"></i>
                                    {{ __('page.SELECT_NEWSLANGUAGE_FIRST') }}
                                </div>
                                <div class="col-sm-6 col-md-6 mb-2">
                                    <label for="news_language_id"
                                        class="form-label mb-2">{{ __('page.SELECT_NEWSLANGUAGE') }}</label>
                                    <select class="form-control form-select select2" id="news_language_id"
                                        name="news_language_id" required>
                                        @foreach ($newsLanguages as $news_language)
                                            <option value="{{ $news_language->id }}"
                                                {{ $e_newspaper->news_language_id == $news_language->id ? 'selected' : '' }}>
                                                {{ $news_language->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="col-sm-6 col-md-6 mb-2">
                                    <label for="channel_id" class="form-label mb-2">{{ __('page.SELECT_CHANNEL') }}</label>
                                    <select id="add_channel_id" class="form-control form-select" name="channel_id" required>
                                        <option value="" disabled selected>{{ __('page.SELECT_CHANNEL') }}</option>
                                        @foreach ($channel_filters as $channel)
                                            <option value="{{ $channel->id }}"
                                                {{ $e_newspaper->channel_id == $channel->id ? 'selected' : '' }}>
                                                {{ $channel->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="col-sm-6 col-md-6 mb-2">
                                    <label for="topic_id" class="form-label">{{ __('page.SELECT_TOPIC') }}<span
                                            class="text-danger">*</span></label>
                                    <select id="select-topic" class="form-control form-select" name="topic_id">
                                        <option value="" selected>{{ __('page.SELECT_TOPIC') }}</option>
                                        @foreach ($news_topics as $topic)
                                            <option value="{{ $topic->id }}"
                                                {{ isset($e_newspaper->topic_id) ? ($topic->id == $e_newspaper->topic_id ? 'selected' : '') : '' }}>
                                                {{ $topic->name }}</option>
                                        @endforeach
                                    </select>
                                    <span class="text-danger"><strong id="topic_id-error"></strong></span>
                                </div>

                                <div class="col-sm-6 col-md-6 mt-3 mb-2">
                                    <label for="date" class="form-label mb-2">{{ __('page.DATE') }}</label>
                                    <input type="date" name="date" class="form-control" id="date"
                                        value="{{ old('date', $e_newspaper->date) }}" required>
                                </div>

                                <div class="col-sm-6 col-md-6 mt-4 mb-2">
                                    <label for="type" class="form-label mb-2">{{ __('page.TYPE') }}</label>
                                    <select id="type" class="form-control form-select" name="type" required>
                                        <option value="paper" {{ $e_newspaper->type == 'paper' ? 'selected' : '' }}>
                                            {{ __('page.NEWSPAPER') }}</option>
                                        <option value="magazine" {{ $e_newspaper->type == 'magazine' ? 'selected' : '' }}>
                                            {{ __('page.MAGAZINE') }}</option>
                                    </select>
                                </div>

                                <div class="col-sm-6 col-md-6 mt-3 mb-2">

                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <label for="pdf_file" class="form-label mb-0">
                                            {{ __('page.PDF_FILE') }}
                                        </label>

                                        @if (($e_newspaper->type == 'paper' && $e_newspaper->pdf_path) || $e_newspaper->type == 'magazine')
                                            @if ($e_newspaper->type == 'paper')
                                                <a href="{{ route('e-newspaper.pdf', $e_newspaper->id) }}" target="_blank"
                                                    class="btn btn-sm btn-outline-primary p-1 rounded">
                                                    {{ __('page.VIEW_CURRENT_PDF') }}
                                                </a>
                                            @else
                                                <a href="{{ route('e-magazine.pdf', $e_newspaper->id) }}" target="_blank"
                                                    class="btn btn-sm btn-outline-primary p-1 rounded">
                                                    {{ __('page.VIEW_CURRENT_PDF') }}
                                                </a>
                                            @endif
                                        @endif
                                    </div>

                                    <input name="pdf_file" type="file" class="form-control" accept=".pdf">

                                </div>


                                <div class="col-sm-6 col-md-6 mt-3 mb-2">
                                    <label for="thumbnail" class="form-label mb-2">{{ __('page.THUMBNAIL') }}</label>
                                    <input name="thumbnail" type="file" class="form-control" accept="image/*">
                                    @if ($e_newspaper->thumbnail)
                                        <div class="mt-2">
                                            <img src="{{ asset('storage/' . $e_newspaper->thumbnail) }}"
                                                alt="Thumbnail Preview" class="img-preview img-fluid" width="150">
                                        </div>
                                    @endif
                                </div>

                                <div class="col-sm-6 col-md-6 mt-3 mb-2">
                                    <label for="background_image"
                                        class="form-label mb-2">{{ __('page.BACKGROUND_IMAGE') }}</label>
                                    <input name="background_image" type="file" class="form-control" accept="image/*">
                                    @if ($e_newspaper->background_image)
                                        <div class="mt-2">
                                            <img src="{{ asset('storage/' . $e_newspaper->background_image) }}"
                                                alt="background image Preview" class="img-preview img-fluid" width="150">
                                        </div>
                                    @endif
                                </div>

                            </div>

                            <div class="modal-footer gap-2">
                                <a href="{{ route('e-newspapers.index') }}"
                                    class="btn btn-secondary">{{ __('page.BACK') }}</a>
                                <button type="submit" class="btn btn-primary">{{ __('page.UPDATE') }}</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
