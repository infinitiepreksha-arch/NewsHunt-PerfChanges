@extends('admin.layouts.main')
@section('title')
    {{ __('page.APP_ADMOB_AND_WEATHER_KEY_SETUP') }}
@endsection
@section('pre-title')
    {{ __('page.APP_ADMOB_AND_WEATHER_KEY_SETUP') }}
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
    <section class="section m-2">
        <div class="card admin_cards">
            <form action="{{ route('settings.app-keys-settings') }}" method="post" id="createCompanySetupForm"
                enctype="multipart/form-data">
                @csrf
                <div class="row d-flex mb-3">
                    {{-- <div class="card admin_cards"> --}}
                    <div class="card-header">
                        <h3 class="card-title">{{ __('page.ANDROID_ADMOB_KEYS_SETUP') }}
                            <i class="bi bi-info-circle-fill text-muted m-1 cursor-pointer" data-bs-toggle="tooltip"
                                data-bs-placement="right" title="{{ __('page.COMPANY_DETAILS_HINT') }}"></i>
                        </h3>

                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-sm-12 form-group mandatory">
                                <label for="android_admob_app_id" class="col-sm-6 col-md-6 form-label">
                                    {{ __('page.ANDROID_ADMOB_APP_ID') }}<span class="text-danger">*</span>
                                </label>
                                <input name="android_admob_app_id" type="text" class="form-control"
                                    id="android_admob_app_id" placeholder="{{ __('page.ANDROID_ADMOB_APP_ID') }}"
                                    value="{{ config('app.demo_mode') ? '' : $settings['android_admob_app_id'] ?? '' }}" />
                                <span class="parsley-required"><strong id="android-admob-app-id-error"></strong></span>
                            </div>

                            <div class="col-sm-12 form-group mandatory mt-3">
                                <label for="android_banner_ad_key" class="col-sm-12 col-md-6 form-label mt-1">
                                    {{ __('page.ANDROID_BANNER_AD_KEY') }}<span class="text-danger">*</span>
                                </label>
                                <input id="android_banner_ad_key" name="android_banner_ad_key" type="text"
                                    class="form-control" placeholder="{{ __('page.ANDROID_BANNER_AD_KEY') }}"
                                    value="{{ config('app.demo_mode') ? '' : $settings['android_banner_ad_key'] ?? '' }}" />
                                <span class="parsley-required"><strong id="android-banner-ad-key-error"></strong></span>
                            </div>

                            <div class="col-sm-12 form-group mandatory mt-3">
                                <label for="android_interstitial_ad_key" class="col-sm-12 col-md-6 form-label mt-1">
                                    {{ __('page.ANDROID_INTERSTITIAL_AD_KEY') }}<span class="text-danger">*</span>
                                </label>
                                <input id="android_interstitial_ad_key" name="android_interstitial_ad_key" type="text"
                                    class="form-control" placeholder="{{ __('page.ANDROID_INTERSTITIAL_AD_KEY') }}"
                                    value="{{ config('app.demo_mode') ? '' : $settings['android_interstitial_ad_key'] ?? '' }}">
                                <span class="parsley-required"><strong
                                        id="android-interstitial-ad-key-error"></strong></span>
                            </div>


                            <div class="col-sm-12 form-group mandatory mt-3">
                                <label for="android_open_ad_key" class="col-sm-12 col-md-6 form-label mt-1">
                                    {{ __('page.ANDROID_OPEN_AD_KEY') }}<span class="text-danger">*</span>
                                </label>
                                <input id="android_open_ad_key" name="android_open_ad_key" type="text"
                                    class="form-control" placeholder="{{ __('page.ANDROID_OPEN_AD_KEY') }}"
                                    value="{{ config('app.demo_mode') ? '' : $settings['android_open_ad_key'] ?? '' }}">
                                <span class="parsley-required"><strong id="android-open-ad-key-error"></strong></span>
                            </div>

                        </div>
                    </div>
                    {{-- </div> --}}
                </div>
                <div class="row d-flex mb-3">
                    {{-- <div class="card admin_cards"> --}}
                    <div class="card-header">
                        <h3 class="card-title">{{ __('page.IOS_ADMOB_KEYS_SETUP') }}
                            <i class="bi bi-info-circle-fill text-muted m-1 cursor-pointer" data-bs-toggle="tooltip"
                                data-bs-placement="right" title="{{ __('page.COMPANY_DETAILS_HINT') }}"></i>
                        </h3>

                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-sm-12 form-group mandatory">
                                <label for="ios_admob_app_id" class="col-sm-6 col-md-6 form-label">
                                    {{ __('page.IOS_ADMOB_APP_ID') }}<span class="text-danger">*</span>
                                </label>
                                <input name="ios_admob_app_id" type="text" class="form-control" id="ios_admob_app_id"
                                    placeholder="{{ __('page.IOS_ADMOB_APP_ID') }}"
                                    value="{{ config('app.demo_mode') ? '' : $settings['ios_admob_app_id'] ?? '' }}" />
                                <span class="parsley-required"><strong id="ios-admob-app-id-error"></strong></span>
                            </div>

                            <div class="col-sm-12 form-group mandatory mt-3">
                                <label for="ios_banner_ad_key" class="col-sm-12 col-md-6 form-label mt-1">
                                    {{ __('page.IOS_BANNER_AD_KEY') }}<span class="text-danger">*</span>
                                </label>
                                <input id="ios_banner_ad_key" name="ios_banner_ad_key" type="text" class="form-control"
                                    placeholder="{{ __('page.IOS_BANNER_AD_KEY') }}"
                                    value="{{ config('app.demo_mode') ? '' : $settings['ios_banner_ad_key'] ?? '' }}" />
                                <span class="parsley-required"><strong id="ios-banner-ad-key-error"></strong></span>
                            </div>

                            <div class="col-sm-12 form-group mandatory mt-3">
                                <label for="ios_interstitial_ad_key" class="col-sm-12 col-md-6 form-label mt-1">
                                    {{ __('page.IOS_INTERSTITIAL_AD_KEY') }}<span class="text-danger">*</span>
                                </label>
                                <input id="ios_interstitial_ad_key" name="ios_interstitial_ad_key" type="text"
                                    class="form-control" placeholder="{{ __('page.IOS_INTERSTITIAL_AD_KEY') }}"
                                    value="{{ config('app.demo_mode') ? '' : $settings['ios_interstitial_ad_key'] ?? '' }}">
                                <span class="parsley-required"><strong id="ios-interstitial-ad-key-error"></strong></span>
                            </div>


                            <div class="col-sm-12 form-group mandatory mt-3">
                                <label for="ios_open_ad_key" class="col-sm-12 col-md-6 form-label mt-1">
                                    {{ __('page.IOS_OPEN_AD_KEY') }}<span class="text-danger">*</span>
                                </label>
                                <input id="ios_open_ad_key" name="ios_open_ad_key" type="text" class="form-control"
                                    placeholder="{{ __('page.IOS_OPEN_AD_KEY') }}"
                                    value="{{ config('app.demo_mode') ? '' : $settings['ios_open_ad_key'] ?? '' }}">
                                <span class="parsley-required"><strong id="ios-open-ad-key-error"></strong></span>
                            </div>

                        </div>
                    </div>

                    <div>
                        <div class="card-header">
                            <h3 class="card-title">{{ __('page.WEATHER_API_KEY') }}</h3>
                        </div>

                        <div class="card-body">
                            <div class="row">

                                <div class="col-sm-12 form-group mandatory mt-3">
                                    <label for="weather_api_key" class="form-label ">{{ __('page.WEATHER_API_KEY') }}
                                        <i class="bi bi-info-circle-fill text-muted m-1 cursor-pointer"
                                            data-bs-toggle="tooltip" data-bs-placement="right"
                                            title="{{ __('Provide the API key for accessing weather data on your website.') }}"></i><span
                                            class="m-2"><a href="https://home.openweathermap.org/">Visit
                                                openweathermap.org</a></span>
                                    </label>
                                    <input class="form-control" type="text" name="weather_api_key"
                                        placeholder={{ __('page.ENTER_WEATHER_API_KEY') }}
                                        value="{{ config('app.demo_mode') ? '' : $settings['weather_api_key'] ?? '' }}"
                                        id="weather_api_key">
                                    <span class="parsley-required"><strong id="weather_api_key-error"></strong></span>
                                </div>

                                <div class="col-sm-6 form-group mandatory mt-3">
                                    <label for="" class="form-label">
                                        {{ __('page.WEATHER_CARD_MODE') }}
                                    </label>
                                    <div class="form-check form-switch">
                                        <input type="hidden" name="weather_card_status" id="weather_card_status"
                                            class="checkbox-toggle-switch-input"
                                            value="{{ $settings['weather_card_status'] ?? 0 }}">
                                        <input class="form-check-input checkbox-toggle-switch" type="checkbox"
                                            role="switch"
                                            {{ !empty($settings['weather_card_status']) && $settings['weather_card_status'] == '1' ? 'checked' : '' }}
                                            id="switch_maintenance_mode"
                                            aria-checked="{{ !empty($settings['weather_card_status']) && $settings['weather_card_status'] == '1' ? 'true' : 'false' }}">
                                    </div>
                                </div>

                                <div class="col-sm-6 form-group mandatory mt-3">
                                    <label for="" class="form-label">
                                        {{ __('page.COOKIES_POPUP_MODE') }}
                                        <small class="text-muted d-block">
                                    </label>
                                    <div class="form-check form-switch">
                                        <input type="hidden" name="cookies_popup_status" id="cookies_popup_status"
                                            class="checkbox-toggle-switch-input"
                                            value="{{ $settings['cookies_popup_status'] ?? 0 }}">
                                        <input class="form-check-input checkbox-toggle-switch" type="checkbox"
                                            role="switch"
                                            {{ !empty($settings['cookies_popup_status']) && $settings['cookies_popup_status'] == '1' ? 'checked' : '' }}
                                            id="switch_maintenance_mode"
                                            aria-checked="{{ !empty($settings['cookies_popup_status']) && $settings['cookies_popup_status'] == '1' ? 'true' : 'false' }}">
                                    </div>
                                </div>

                            </div>
                        </div>

                    </div>
                    {{-- </div> --}}
                </div>

                <div class="col-12 mt-3 d-flex justify-content-end mb-5">
                    <button class="btn btn-primary me-1 mb-1" type="submit"
                        name="submit">{{ __('page.SAVE') }}</button>
                </div>
            </form>
        </div>
    </section>


    {{-- <section class="section m-2">
        <div>
            <form action="{{ route('settings.store') }}" method="post" method="post" enctype="multipart/form-data">
                @csrf
                <div class="row d-flex mb-3">

                    <div class="card mt-3 admin_cards m-2">
                        <div class="card-header">
                            <h3 class="card-title">{{ __('page.WEATHER_API_KEY') }}</h3>
                        </div>

                        <div class="card-body">
                            <div class="row">

                                <div class="col-sm-12 form-group mandatory mt-3">
                                    <label for="weather_api_key" class="form-label ">{{ __('page.WEATHER_API_KEY') }}
                                        <i class="bi bi-info-circle-fill text-muted m-1 cursor-pointer"
                                            data-bs-toggle="tooltip" data-bs-placement="right"
                                            title="{{ __('Provide the API key for accessing weather data on your website.') }}"></i><span
                                            class="m-2"><a href="https://home.openweathermap.org/">Visit
                                                openweathermap.org</a></span>
                                    </label>
                                    <input class="form-control" type="text" name="weather_api_key"
                                        placeholder={{ __('page.ENTER_WEATHER_API_KEY') }}
                                        value="{{ config('app.demo_mode') ? '' : $settings['weather_api_key'] ?? '' }}"
                                        id="weather_api_key">
                                </div>

                                <div class="col-sm-6 form-group mandatory mt-3">
                                    <label for="" class="form-label">
                                        {{ __('page.WEATHER_CARD_MODE') }}
                                    </label>
                                    <div class="form-check form-switch">
                                        <input type="hidden" name="weather_card_status" id="weather_card_status"
                                            class="checkbox-toggle-switch-input"
                                            value="{{ $settings['weather_card_status'] ?? 0 }}">
                                        <input class="form-check-input checkbox-toggle-switch" type="checkbox"
                                            role="switch"
                                            {{ !empty($settings['weather_card_status']) && $settings['weather_card_status'] == '1' ? 'checked' : '' }}
                                            id="switch_maintenance_mode"
                                            aria-checked="{{ !empty($settings['weather_card_status']) && $settings['weather_card_status'] == '1' ? 'true' : 'false' }}">
                                    </div>
                                </div>

                                <div class="col-sm-6 form-group mandatory mt-3">
                                    <label for="" class="form-label">
                                        {{ __('page.COOKIES_POPUP_MODE') }}
                                        <small class="text-muted d-block">
                                    </label>
                                    <div class="form-check form-switch">
                                        <input type="hidden" name="cookies_popup_status" id="cookies_popup_status"
                                            class="checkbox-toggle-switch-input"
                                            value="{{ $settings['cookies_popup_status'] ?? 0 }}">
                                        <input class="form-check-input checkbox-toggle-switch" type="checkbox"
                                            role="switch"
                                            {{ !empty($settings['cookies_popup_status']) && $settings['cookies_popup_status'] == '1' ? 'checked' : '' }}
                                            id="switch_maintenance_mode"
                                            aria-checked="{{ !empty($settings['cookies_popup_status']) && $settings['cookies_popup_status'] == '1' ? 'true' : 'false' }}">
                                    </div>
                                </div>

                                <div class="col-12 mt-3 d-flex justify-content-end">
                                    <button class="btn btn-primary me-1 mb-1" type="submit"
                                        name="submit">{{ __('page.SAVE') }}</button>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
            </form>
        </div>
    </section> --}}
@endsection
