@extends('admin.layouts.main')
@section('title')
    {{ __('page.GOOGLE_ADSENSE_CONFIGURATION') }}
@endsection
@section('pre-title')
    {{ __('page.GOOGLE_ADSENSE_CONFIGURATION') }}
@endsection
@section('page-title')
    <div class="row g-2 align-items-center">
        <div class="col">
            <div class="page-pretitle">
                <a href="{{ url('admin/dashboard') }}">{{ __('page.HOME') }}/</a>
                <a href="{{ url('admin/settings') }}">{{ __('page.SETTINGS') }}/</a>
                {{ __('page.GOOGLE_ADSENSE_CONFIGURATION') }}
            </div>
        </div>
        <div class="col-auto ms-auto d-print-none"></div>
    </div>
@endsection

@section('content')
    <div class="container-xl">
        <div class="row row-cards">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">{{ __('page.GOOGLE_ADSENSE_CONFIGURATION') }}</h3>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('settings.googleAdsenseSettings') }}" method="POST"
                            enctype="multipart/form-data" id="createCompanySetupForm">
                            @csrf
                            <div class="row">

                                <!-- Client ID -->
                                <div class="col-sm-12 form-group  mt-3">
                                    <label for="adsense_client_id" class="col-sm-6 col-md-6 form-label">
                                        {{ __('page.ADSENSE_CLIENT_ID') }}<span class="text-danger">*</span>
                                    </label>
                                    <input name="adsense_client_id" type="text" class="form-control"
                                        id="adsense_client_id" placeholder="{{ __('page.ADSENSE_CLIENT_ID') }}"
                                        value="{{ $settings['adsense_client_id'] ?? '' }}" />
                                    <span class="parsley-required"><strong id="adsense-client-id-error"></strong></span>
                                </div>

                                <!-- Client Secret -->
                                <div class="col-sm-12 form-group  mt-3">
                                    <label for="adsense_client_secret" class="col-sm-6 col-md-6 form-label">
                                        {{ __('page.ADSENSE_CLIENT_SECRET') }}<span class="text-danger">*</span>
                                    </label>
                                    <input name="adsense_client_secret" type="password" class="form-control"
                                        id="adsense_client_secret" placeholder="{{ __('page.ADSENSE_CLIENT_SECRET') }}"
                                        value="{{ $settings['adsense_client_secret'] ?? '' }}" />
                                    <span class="parsley-required"><strong id="adsense-client-secret-error"></strong></span>
                                </div>

                                <!-- Redirect URI -->
                                <div class="col-sm-12 form-group  mt-3">
                                    <label for="adsense_redirect_uri" class="col-sm-6 col-md-6 form-label">
                                        {{ __('page.ADSENSE_REDIRECT_URI') }}<span class="text-danger">*</span>
                                    </label>
                                    <input name="adsense_redirect_uri" type="url" class="form-control"
                                        id="adsense_redirect_uri" placeholder="{{ __('page.ADSENSE_REDIRECT_URI') }}"
                                        value="{{ $settings['adsense_redirect_uri'] ?? '' }}" />
                                    <span class="parsley-required"><strong id="adsense-redirect-uri-error"></strong></span>
                                </div>

                                <!-- Submit Button -->
                                <div class="col-sm-12 form-group mt-3">
                                    <button type="submit" class="btn btn-primary">
                                        {{ __('page.SAVE_CONFIGURATION') }}
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
