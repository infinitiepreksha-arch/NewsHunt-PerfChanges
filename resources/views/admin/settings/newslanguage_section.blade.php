@extends('admin.layouts.main')
@section('title')
    {{ __('page.NEWS_LANGUAGE_SETTINGS') }}
@endsection
@section('pre-title')
    {{ __('page.NEWS_LANGUAGE_SETTINGS') }}
@endsection
@section('page-title')
    <div class="row g-2 align-items-center">
        <div class="col">
            <!-- Page pre-title -->
            <div class="page-pretitle">
                <a href="{{ url('admin/dashboard') }}">{{ __('page.HOME') }}/</a>
                <a href="{{ url('admin/settings') }}">{{ __('page.SETTINGS') }}/</a>
                @yield('pre-title')
            </div>
            <h2 class="page-title mt-2 m-1">
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
        <form action="{{ route('settings.storeNewsLanguageStatus') }}" method="post">
            @csrf
            <div class="row d-flex mb-3">
                <div class="card mt-3 admin_cards m-2">
                    <div class="card-header">
                        <h3 class="card-title">{{ __('page.NEWS_LANGUAGE_SETTINGS') }}</h3>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-sm-6 form-group mt-3">
                                <label for="news_languages_toggle" data-bs-toggle="tooltip"
                                    class="form-label">{{ __('page.ENABLE_NEWS_LANGUAGES') }}
                                    <i class="bi bi-info-circle-fill m-1 text-muted cursor-pointer" data-bs-toggle="tooltip"
                                        data-bs-placement="right"
                                        title="{{ __('Toggle this to enable or disable the permission to create and manage multiple news languages.') }}"></i>
                                </label>
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" id="news_languages_toggle"
                                        name="news_languages_toggle" value="1"
                                        {{ isset($settings['news_languages_toggle']) && $settings['news_languages_toggle'] ? 'checked' : '' }}>
                                    <label class="form-check-label" for="news_languages_toggle"></label>
                                </div>
                            </div>

                            <div class="col-sm-6 form-group mt-3">
                                <label for="news_language" class="form-label">{{ __('page.DEFAULT_NEWS_LANGUAGE') }}
                                    <i class="bi bi-info-circle-fill m-1 text-muted cursor-pointer" data-bs-toggle="tooltip"
                                        data-bs-placement="right"
                                        title="{{ __('Displays the default news language when multi-language support is disabled.') }}"></i>
                                </label>
                                <input type="text" id="news_language" name="news_language" class="form-control"
                                    placeholder="{{ __('page.DEFAULT_NEWS_LANGUAGE') }}"
                                    value="{{ $activeNewsLanguage->name ?? '' }}" disabled>
                            </div>
                        </div>
                        <div class="col-12 mt-3 d-flex justify-content-end">
                            <button class="btn btn-primary me-1 mb-1" type="submit"
                                name="submit">{{ __('page.SAVE') }}</button>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </section>
@endsection
