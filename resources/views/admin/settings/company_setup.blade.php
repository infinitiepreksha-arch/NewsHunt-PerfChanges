@extends('admin.layouts.main')
@section('title')
    {{ __('page.GENERAL_SETTINGS') }}
@endsection
@section('pre-title')
    {{ __('page.GENERAL_SETTINGS') }}
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
        <div id="addcompanySetup">
            <form action="{{ route('settings.company_setup') }}" method="post" id="createCompanySetupForm"
                enctype="multipart/form-data">
                @csrf
                <div class="row d-flex mb-3">
                    <div class="card admin_cards">
                        <div class="card-header">
                            <h3 class="card-title">{{ __('page.COMPANY_DETAILS') }}
                                <i class="bi bi-info-circle-fill text-muted m-1 cursor-pointer" data-bs-toggle="tooltip"
                                    data-bs-placement="right" title="{{ __('page.COMPANY_DETAILS_HINT') }}"></i>
                            </h3>

                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-sm-12 form-group mandatory">
                                    <label for="company_name"
                                        class="col-sm-6 col-md-6 form-label">{{ __('page.COMPANY_NAME') }}<span
                                            class="text-danger">*</span></label>
                                    <input name="company_name" type="text" class="form-control" id="company_name"
                                        placeholder="{{ __('page.COMPANY_NAME') }}"
                                        value="{{ $settings['company_name'] ?? '' }}" />
                                    <span class="parsley-required"><strong id="company-name-error"></strong></span>
                                </div>
                                <div class="col-sm-12 form-group mandatory mt-3">
                                    <label for="company_email"
                                        class="col-sm-12 col-md-6 form-label mt-1">{{ __('page.EMAIL') }}<span
                                            class="text-danger">*</span></label>
                                    <input id="company_email" name="company_email" type="email" class="form-control"
                                        placeholder="{{ __('page.ENTER_EMAIL') }}"
                                        value="{{ $settings['company_email'] ?? '' }}" />
                                    <span class="parsley-required"><strong id="company-email-error"></strong></span>
                                </div>

                                <div class="col-sm-12 form-group mandatory mt-3">
                                    <label for="company_tel1"
                                        class="col-sm-12 col-md-6 form-label mt-1">{{ __('page.CONTACT_NUMBER') . ' 1' }}<span
                                            class="text-danger">*</span></label>
                                    <input id="company_tel1" name="company_tel1" type="text"
                                        class="form-control only-numbers"
                                        placeholder="{{ __('page.CONTACT_NUMBER') . ' 1' }}" maxlength="16"
                                        onKeyDown="if(this.value.length==16 && event.keyCode!=8) return false;"
                                        value="{{ $settings['company_tel1'] ?? '' }}">
                                    <span class="parsley-required"><strong id="company-tel1-error"></strong></span>
                                </div>

                                <div class="col-sm-12">
                                    <label for="company_tel2"
                                        class="col-sm-12 col-md-6 form-label mt-3">{{ __('page.CONTACT_NUMBER') . ' 2' }}</label>
                                    <input id="company_tel2" name="company_tel2" type="text"
                                        class="form-control only-numbers"
                                        placeholder="{{ __('page.CONTACT_NUMBER') . ' 2' }}" maxlength="16"
                                        onKeyDown="if(this.value.length==16 && event.keyCode!=8) return false;"
                                        value="{{ $settings['company_tel2'] ?? '' }}">
                                    <span class="parsley-required"><strong id="company-tel2-error"></strong></span>
                                </div>

                                <div class="col-sm-12">
                                    <label for="company_address"
                                        class="col-sm-12 col-md-6 form-label mt-3">{{ __('page.ADDRESS') }}<span
                                            class="text-danger">*</span></label>
                                    <textarea id="company_address" name="company_address" type="text" class="form-control"
                                        placeholder="{{ __('page.ENTER_ADDRESS') }}">{{ $settings['company_address'] ?? '' }}</textarea>
                                    <span class="parsley-required"><strong id="company-address-error"></strong></span>
                                </div>

                                <div class="col-sm-12 form-group mandatory">
                                    <label for="seo_title" class="col-sm-6 col-md-6 form-label mt-3">
                                        {{ __('page.SEO_TITLE') }}<span class="text-danger">*</span>
                                    </label>
                                    <input name="seo_title" type="text" class="form-control" id="seo_title"
                                        placeholder="{{ __('page.SEO_TITLE') }}"
                                        value="{{ $settings['seo_title'] ?? '' }}" />
                                    <span class="parsley-required"><strong id="seo-title-error"></strong></span>
                                </div>

                                <div class="col-sm-12 form-group mandatory">
                                    <label for="meta_description" class="col-sm-6 col-md-6 form-label mt-3">
                                        {{ __('page.META_DESCRIPTION') }}<span class="text-danger">*</span>
                                    </label>
                                    <input name="meta_description" type="text" class="form-control"
                                        id="meta_description" placeholder="{{ __('page.META_DESCRIPTION') }}"
                                        value="{{ $settings['meta_description'] ?? '' }}" />
                                    <span class="parsley-required"><strong id="meta-description-error"></strong></span>
                                </div>

                                <div class="col-sm-12 form-group mandatory">
                                    <label for="meta_keywords" class="col-sm-6 col-md-6 form-label mt-3">
                                        {{ __('page.META_KEYWORDS') }}<span class="text-danger">*</span>
                                    </label>
                                    <input name="meta_keywords" type="text" class="form-control" id="meta_keywords"
                                        placeholder="{{ __('page.META_KEYWORDS') }}"
                                        value="{{ $settings['meta_keywords'] ?? '' }}" />
                                    <span class="parsley-required"><strong id="meta-keywords-error"></strong></span>
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
        </div>
    </section>

    <section class="section">
        <form action="{{ route('settings.logo') }}" method="post" method="post" enctype="multipart/form-data">
            @csrf
            <div class="row d-flex mb-3">

                <div class="card mt-3 admin_cards m-2">
                    <div class="card-header">
                        <h3 class="card-title">{{ __('page.LOGO_IMAGES') }}</h3>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-sm-6 form-group mandatory">
                                <label for="" class=" col-form-label ">{{ __('page.FIREBASE') }}
                                    <i class="bi bi-info-circle-fill text-muted m-1 cursor-pointer"
                                        data-bs-toggle="tooltip" data-bs-placement="right"
                                        title="{{ __('Upload an image to be used as the icon displayed in the browser tab for your website.') }}"></i>
                                </label>
                                <input class="filepond" type="file" name="favicon_icon" id="favicon_icon">
                                <img src="{{ $settings['favicon_icon'] ?? '' }}"
                                    data-custom-image="{{ asset('assets/images/logo/favicon.png') }}"
                                    class="img-privew mt-2 favicon_icon" alt="">
                            </div>

                            <div class="col-sm-6 form-group mandatory mt-2">
                                <label for="" class="form-label ">{{ __('page.SIDEBAR_LOGO') }} <i
                                        class="bi bi-info-circle-fill text-muted m-1 cursor-pointer"
                                        data-bs-toggle="tooltip" data-bs-placement="right"
                                        title="{{ __('Upload an image to be displayed as the logo in the admin panel sidebar.') }}"></i></label>
                                <input class="filepond" type="file" name="company_logo" id="company_logo">
                                <img src="{{ $settings['company_logo'] ?? '' }}"
                                    data-custom-image="{{ asset('assets/images/logo/logo.png') }}"
                                    class="img-privew mt-2 company_logo" alt="">
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card mt-3 admin_cards m-2">
                    <div class="card-header">
                        <h3 class="card-title">{{ __('page.WEB_SETTINGS') }}</h3>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-sm-12 form-group mandatory mt-3">
                                <label for="" class="form-label ">{{ __('page.NEWS_LABEL') }}</label>
                                <input class="form-control" type="text" name="news_label_place_holder"
                                    value="{{ $settings['news_label_place_holder'] ?? '' }}"
                                    id="news_label_place_holder">
                            </div>

                            <div class="col-sm-6 form-group mandatory mt-3">
                                <label for="light_logo" class="form-label ">{{ __('page.LIGHT_LOGO') }}
                                    <i class="bi bi-info-circle-fill text-muted m-1 cursor-pointer"
                                        data-bs-toggle="tooltip" data-bs-placement="right"
                                        title="{{ __('Upload an image for the logo displayed in the light theme of your website.') }}"></i>
                                </label>
                                <input class="filepond" type="file" name="light_logo" id="light_logo">
                                <img src="{{ $settings['light_logo'] ?? '' }}"
                                    data-custom-image="{{ asset('assets/images/logo/sidebar_logo.png') }}"
                                    class="img-privew" alt="">
                                <!-- Logo Size Dropdown -->
                                <label for="light_logo_size"
                                    class="form-label mt-2">{{ __('page.LIGHT_LOGO_SIZE') }}</label>
                                <select class="form-select" name="light_logo_size" id="light_logo_size">
                                    @php $lightSize = $settings['light_logo_size'] ?? ''; @endphp

                                    <option value="w-auto" {{ $lightSize == 'w-auto' ? 'selected' : '' }}>w-auto</option>
                                    <option value="w-10" {{ $lightSize == 'w-10' ? 'selected' : '' }}>w-10</option>
                                    <option value="w-20" {{ $lightSize == 'w-20' ? 'selected' : '' }}>w-20</option>
                                    <option value="w-40" {{ $lightSize == 'w-40' ? 'selected' : '' }}>w-40</option>
                                    <option value="w-50" {{ $lightSize == 'w-50' ? 'selected' : '' }}>w-50</option>
                                    <option value="w-100" {{ $lightSize == 'w-100' ? 'selected' : '' }}>w-100</option>
                                    <option value="w-150" {{ $lightSize == 'w-150' ? 'selected' : '' }}>w-150</option>
                                    <option value="w-200" {{ $lightSize == 'w-200' ? 'selected' : '' }}>w-200</option>
                                    <option value="w-250" {{ $lightSize == 'w-250' ? 'selected' : '' }}>w-250</option>
                                    <option value="w-300" {{ $lightSize == 'w-300' ? 'selected' : '' }}>w-300</option>
                                    <option value="w-350" {{ $lightSize == 'w-350' ? 'selected' : '' }}>w-350</option>
                                    <option value="w-400" {{ $lightSize == 'w-400' ? 'selected' : '' }}>w-400</option>
                                    <option value="w-450" {{ $lightSize == 'w-450' ? 'selected' : '' }}>w-450</option>
                                    <option value="w-500" {{ $lightSize == 'w-500' ? 'selected' : '' }}>w-500</option>>
                                </select>

                            </div>

                            <div class="col-sm-6 form-group mandatory mt-3">
                                <label for="dark_logo" class="form-label ">{{ __('page.DARK_LOGO') }}<i
                                        class="bi bi-info-circle-fill text-muted m-1 cursor-pointer"
                                        data-bs-toggle="tooltip" data-bs-placement="right"
                                        title="{{ __('Upload an image for the logo displayed in the dark theme of your website.') }}"></i></label>
                                <input class="filepond" type="file" name="dark_logo" id="dark_logo">
                                <img src="{{ $settings['dark_logo'] ?? '' }}"
                                    data-custom-image="{{ asset('assets/images/logo/sidebar_logo.png') }}"
                                    class="img-privew" alt="">
                                <!-- Logo Size Dropdown -->
                                <label for="dark_logo_size"
                                    class="form-label mt-2">{{ __('page.DARK_LOGO_SIZE') }}</label>
                                <select class="form-select" name="dark_logo_size" id="dark_logo_size">
                                    @php $darkSize = $settings['dark_logo_size'] ?? ''; @endphp

                                    <option value="w-auto" {{ $darkSize == 'w-auto' ? 'selected' : '' }}>w-auto</option>
                                    <option value="w-10" {{ $darkSize == 'w-10' ? 'selected' : '' }}>w-10</option>
                                    <option value="w-20" {{ $darkSize == 'w-20' ? 'selected' : '' }}>w-20</option>
                                    <option value="w-40" {{ $darkSize == 'w-40' ? 'selected' : '' }}>w-40</option>
                                    <option value="w-50" {{ $darkSize == 'w-50' ? 'selected' : '' }}>w-50</option>
                                    <option value="w-100" {{ $darkSize == 'w-100' ? 'selected' : '' }}>w-100</option>
                                    <option value="w-150" {{ $darkSize == 'w-150' ? 'selected' : '' }}>w-150</option>
                                    <option value="w-200" {{ $darkSize == 'w-200' ? 'selected' : '' }}>w-200</option>
                                    <option value="w-250" {{ $darkSize == 'w-250' ? 'selected' : '' }}>w-250</option>
                                    <option value="w-300" {{ $darkSize == 'w-300' ? 'selected' : '' }}>w-300</option>
                                    <option value="w-350" {{ $darkSize == 'w-350' ? 'selected' : '' }}>w-350</option>
                                    <option value="w-400" {{ $darkSize == 'w-400' ? 'selected' : '' }}>w-400</option>
                                    <option value="w-450" {{ $darkSize == 'w-450' ? 'selected' : '' }}>w-450</option>
                                    <option value="w-500" {{ $darkSize == 'w-500' ? 'selected' : '' }}>w-500</option>

                                </select>

                            </div>

                        </div>
                    </div>
                </div>

                <div class="card mt-3 admin_cards m-2">
                    <div class="card-header">
                        <h3 class="card-title">{{ __('page.DEFAULT_IMAGES') }}</h3>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-sm-6 form-group mandatory mt-3">
                                <label for="default_image" class="form-label ">{{ __('page.ADD_DEFAULT_IMAGE') }}
                                    <i class="bi bi-info-circle-fill text-muted m-1 cursor-pointer"
                                        data-bs-toggle="tooltip" data-bs-placement="right"
                                        title="{{ __('Upload an image for the logo displayed in the post image.') }}"></i>
                                </label>
                                <input class="filepond" type="file" name="default_image" id="default_image">
                                <img src="{{ $settings['default_image'] ?? '' }}"
                                    data-custom-image="{{ asset('assets/images/logo/sidebar_logo.png') }}"
                                    class="img-privew" alt="">
                            </div>
                            <div class="col-sm-6 form-group mandatory mt-3">
                                <label for="enews_paper_image"
                                    class="form-label ">{{ __('page.ADD_E_NEWS_PAPER_IMAGE') }}
                                    <i class="bi bi-info-circle-fill text-muted m-1 cursor-pointer"
                                        data-bs-toggle="tooltip" data-bs-placement="right"
                                        title="{{ __('Choose an image that will appear as the E-Newspaper logo on the main banner.') }}"></i>
                                </label>
                                <input class="filepond" type="file" name="enews_paper_image" id="enews_paper_image">
                                <img src="{{ $settings['enews_paper_image'] ?? '' }}"
                                    data-custom-image="{{ asset('assets/images/logo/sidebar_logo.png') }}"
                                    class="img-privew" alt="">
                            </div>
                            <div class="col-sm-6 form-group mandatory mt-3">
                                <label for="enews_paper_title" class="form-label ">{{ __('page.ENEWS_PAPER_TITLE') }} <i
                                        class="bi bi-info-circle-fill text-muted m-1 cursor-pointer"
                                        data-bs-toggle="tooltip" data-bs-placement="right"
                                        title="{{ __('Enter the main heading or welcome text shown on the E-Newspaper section.') }}"></i>
                                    <span class="m-2"></span>
                                </label>
                                <input class="form-control" type="text" name="enews_paper_title"
                                    placeholder={{ __('page.ENEWS_PAPER_TITLE') }}
                                    value="{{ $settings['enews_paper_title'] ?? '' }}" id="enews_paper_title">
                            </div>

                            <div class="col-12 mt-3 d-flex justify-content-end">
                                <button class="btn btn-primary me-1 mb-1" type="submit"
                                    name="submit">{{ __('page.SAVE') }}</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card mt-3 admin_cards">
                <div class="card-header">
                    <h3 class="card-title">{{ __('page.THEME_COLOR_CUSTOMIZATION') }}
                        <i class="bi bi-info-circle-fill text-muted m-1 cursor-pointer" data-bs-toggle="tooltip"
                            data-bs-placement="right"
                            title="{{ __('Manually customize the primary colors for your web platform and mobile app for better brand identification.') }}"></i>
                    </h3>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-sm-6 form-group">
                            <label for="web_theme_primary_colour"
                                class="form-label">{{ __('page.WEB_THEME_PRIMARY_COLOUR') }}
                                <i class="bi bi-info-circle-fill text-muted m-1 cursor-pointer" data-bs-toggle="tooltip"
                                    data-bs-placement="right"
                                    title="{{ __('Choose a primary color that will reflect on your website theme.') }}"></i>
                            </label>
                            <input type="color" name="web_theme_primary_colour" id="web_theme_primary_colour"
                                class="form-control form-control-color w-100"
                                value="{{ $settings['web_theme_primary_colour'] ?? '#000000' }}"
                                title="{{ __('page.CHOOSE_WEB_THEME_PRIMARY_COLOUR') }}">
                        </div>
                        <div class="col-sm-6 form-group">
                            <label for="app_theme_primary_colour"
                                class="form-label">{{ __('page.APP_THEME_PRIMARY_COLOUR') }}
                                <i class="bi bi-info-circle-fill text-muted m-1 cursor-pointer" data-bs-toggle="tooltip"
                                    data-bs-placement="right"
                                    title="{{ __('Choose a primary color that will reflect on your mobile app theme.') }}"></i>
                            </label>
                            <input type="color" name="app_theme_primary_colour" id="app_theme_primary_colour"
                                class="form-control form-control-color w-100"
                                value="{{ $settings['app_theme_primary_colour'] ?? '#000000' }}"
                                title="{{ __('page.CHOOSE_APP_THEME_PRIMARY_COLOUR') }}">
                        </div>
                        <div class="col-sm-6 form-group mt-3">
                            <label for="web_font" class="form-label">{{ __('page.WEB_FONT') }}
                                <i class="bi bi-info-circle-fill text-muted m-1 cursor-pointer" data-bs-toggle="tooltip"
                                    data-bs-placement="right" title="{{ __('page.CHOOSE_WEB_FONT') }}"></i>
                            </label>
                            <div class="font-dropdown-container" data-target="web_font">
                                <div class="font-dropdown-trigger form-control d-flex align-items-center justify-content-between"
                                    style="cursor: pointer;">
                                    <span class="font-selected-text">{{ __('page.SELECT_FONT_FAMILY') }}</span>
                                    <i class="bi bi-chevron-down"></i>
                                </div>
                                <div class="font-dropdown-menu">
                                    <input type="text" class="form-control font-search-box"
                                        placeholder="Search fonts...">
                                    <div class="font-options-list" style="max-height: 300px; overflow-y: auto;">
                                        <!-- Options will be populated by JS -->
                                    </div>
                                </div>
                            </div>
                            <select name="web_font" id="web_font" class="google-fonts-dropdown" style="display: none;"
                                data-selected="{{ $settings['web_font'] ?? '' }}">
                                <option value="">{{ __('page.SELECT_FONT_FAMILY') }}</option>
                            </select>
                            <div id="web_font_preview" class="font-preview mt-2 card p-3 text-center h1">
                                Aa Bb Cc Dd Ee Ff Gg 1234567890
                            </div>
                        </div>
                        <div class="col-sm-6 form-group mt-3">
                            <label for="app_font" class="form-label">{{ __('page.APP_FONT') }}
                                <i class="bi bi-info-circle-fill text-muted m-1 cursor-pointer" data-bs-toggle="tooltip"
                                    data-bs-placement="right" title="{{ __('page.CHOOSE_APP_FONT') }}"></i>
                            </label>
                            <div class="font-dropdown-container" data-target="app_font">
                                <div class="font-dropdown-trigger form-control d-flex align-items-center justify-content-between"
                                    style="cursor: pointer;">
                                    <span class="font-selected-text">{{ __('page.SELECT_FONT_FAMILY') }}</span>
                                    <i class="bi bi-chevron-down"></i>
                                </div>
                                <div class="font-dropdown-menu">
                                    <input type="text" class="form-control font-search-box"
                                        placeholder="Search fonts...">
                                    <div class="font-options-list" style="max-height: 300px; overflow-y: auto;">
                                        <!-- Options will be populated by JS -->
                                    </div>
                                </div>
                            </div>
                            <select name="app_font" id="app_font" class="google-fonts-dropdown" style="display: none;"
                                data-selected="{{ $settings['app_font'] ?? '' }}">
                                <option value="">{{ __('page.SELECT_FONT_FAMILY') }}</option>
                            </select>
                            <div id="app_font_preview" class="font-preview mt-2 card p-3 text-center h1">
                                Aa Bb Cc Dd Ee Ff Gg 1234567890
                            </div>
                        </div>
                        <div class="col-12 mt-3 d-flex justify-content-end">
                            <button class="btn btn-primary me-1 mb-1" type="submit"
                                name="submit">{{ __('page.SAVE') }}</button>
                        </div>
                    </div>
                </div>
            </div>
    </section>
    </form>


    <section class="section m-2">
        <form action="{{ route('settings.subscription-store') }}" id="subscription-setting-modal" method="post"
            enctype="multipart/form-data">
            @csrf
            <div class="col-md-12">
                <div class="card mt-3">
                    <div class="card-header">
                        <h3 class="card-title">{{ __('page.SUBSCRIBE_MODEL') }}</h3>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-sm-6 form-group mandatory">
                                <label for="subscribe_model_title"
                                    class="form-label ">{{ __('page.MODEL_TITLE') }}</label>
                                <textarea id="subscribe_model_title" name="subscribe_model_title" class="form-control"
                                    placeholder="{{ __('page.SUBSCRIBE_MODEL_TITLE') }}">{{ $settings['subscribe_model_title'] ?? '' }}</textarea>
                                <span class="parsley-required"><strong id="subscribe_model_title-error"></strong></span>
                            </div>
                            <div class="col-sm-6 form-group mandatory">
                                <label for="subscribe_model_sub_title"
                                    class="form-label ">{{ __('page.MODEL_SUB_TITLE') }}</label>
                                <textarea id="subscribe_model_sub_title" name="subscribe_model_sub_title" class="form-control"
                                    placeholder="{{ __('page.SUBSCRIBE_MODEL_SUB_TITLE') }}">{{ $settings['subscribe_model_sub_title'] ?? '' }}</textarea>
                                <span class="parsley-required"><strong
                                        id="subscribe_model_sub_title-error"></strong></span>
                            </div>

                            <div class="col-sm-6 form-group mandatory mt-3">
                                <label for="" class="form-label">{{ __('page.MODEL_STATUS') }}</label>
                                <div class="form-check form-switch">
                                    <input type="hidden" name="subscribe_model_status" id="subscribe_model_status"
                                        class="checkbox-toggle-switch-input"
                                        value="{{ $settings['subscribe_model_status'] ?? 0 }}">
                                    <input class="form-check-input checkbox-toggle-switch" type="checkbox" role="switch"
                                        {{ !empty($settings['subscribe_model_status']) && $settings['subscribe_model_status'] == '1' ? 'checked' : '' }}
                                        id="switch_maintenance_mode"
                                        aria-checked="{{ !empty($settings['subscribe_model_status']) && $settings['subscribe_model_status'] == '1' ? 'true' : 'false' }}">
                                </div>
                            </div>
                            <div class="col-sm-6 form-group mandatory">
                                <label for="" class=" col-form-label ">{{ __('page.MODEL_IMAGE') }}</label>
                                <input class="filepond" type="file" name="subscribe_model_image"
                                    id="subscribe_model_image">
                                <img src="{{ $settings['subscribe_model_image'] ?? '' }}"
                                    data-custom-image="{{ asset('assets/images/logo/favicon.png') }}"
                                    class="img-privew mt-2 favicon_icon" alt="">
                                <span class="parsley-required"><strong id="subscribe_model_image-error"></strong></span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-sm-12 mt-3 d-grid gap-2 d-md-flex justify-content-md-end">
                    <button type="submit" value="btnAdd"
                        class="btn btn-primary me-1 mb-3">{{ __('page.SAVE') }}</button>
                </div>
            </div>
        </form>

        <form action="{{ route('settings.store') }}" method="post">
            @csrf
            <div class="col-md-12">
                <div class="card mt-3">
                    <div class="card-header">
                        <h3 class="card-title">{{ __('page.SCRIPTS') }}</h3>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-sm-12 form-group mandatory">
                                <label for="firebase_project_id"
                                    class="form-label">{{ __('page.HEADER_SCRIPT') }}</label>
                                <textarea id="header_script" name="header_script" type="text" class="form-control"
                                    placeholder="{{ __('page.INSERT_HEADER_SCRIPT') }}">{{ $settings['header_script'] ?? '' }}</textarea>
                            </div>
                            <div class="col-sm-12 form-group mandatory mt-3">
                                <label for="service_file" class="form-label">{{ __('page.FOOTER_SCRIPT') }}</label>
                                <textarea id="footer_script" name="footer_script" type="text" class="form-control"
                                    placeholder="{{ __('page.INSERT_FOOTER_SCRIPT') }}">{{ $settings['footer_script'] ?? '' }}</textarea>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-sm-12 mt-3 d-grid gap-2 d-md-flex justify-content-md-end">
                <button type="submit" value="btnAdd" class="btn btn-primary me-1 mb-3">{{ __('page.SAVE') }}</button>
            </div>
        </form>
    </section>

    <section class="section m-2">
        <!-- Social Media Links Form -->
        <form action="{{ route('settings.store') }}" method="post">
            @csrf
            <div class="row d-flex">
                <div class="card mt-3 admin_cards">
                    <div class="card-header">
                        <h3 class="card-title">{{ __('page.SOCIAL_MEDIA_LINKS') }}
                            <i class="bi bi-info-circle-fill text-muted m-1 cursor-pointer" data-bs-toggle="tooltip"
                                data-bs-placement="right"
                                title="{{ __('Add links to your official social media profiles to connect with your audience and increase engagement.') }}"></i>
                        </h3>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-sm-6 form-group mandatory">
                                <label for="instagram_link" class="form-label">{{ __('page.INSTAGRAM_LINK') }}</label>
                                <input id="instagram_link" name="instagram_link" type="url" class="form-control"
                                    placeholder="{{ __('page.ENTER_INSTAGRAM_LINK') }}"
                                    value="{{ $settings['instagram_link'] ?? '' }}">
                            </div>
                            <div class="col-sm-6 form-group mandatory">
                                <label for="x_link" class="form-label">{{ __('page.X_LINK') }}</label>
                                <input id="x_link" name="x_link" type="url" class="form-control"
                                    placeholder="{{ __('page.ENTER_X_LINK') }}"
                                    value="{{ $settings['x_link'] ?? '' }}">
                            </div>
                            <div class="col-sm-6 form-group mandatory mt-3">
                                <label for="facebook_link" class="form-label">{{ __('page.FACEBOOK_LINK') }}</label>
                                <input id="facebook_link" name="facebook_link" type="url" class="form-control"
                                    placeholder="{{ __('page.ENTER_FACEBOOK_LINK') }}"
                                    value="{{ $settings['facebook_link'] ?? '' }}">
                            </div>
                            <div class="col-sm-6 form-group mandatory mt-3">
                                <label for="linkedin_link" class="form-label">{{ __('page.LINKEDIN_LINK') }}</label>
                                <input id="linkedin_link" name="linkedin_link" type="url" class="form-control"
                                    placeholder="{{ __('page.ENTER_LINKEDIN_LINK') }}"
                                    value="{{ $settings['linkedin_link'] ?? '' }}">
                            </div>
                            <div class="col-sm-6 form-group mandatory mt-3">
                                <label for="pinterest_link" class="form-label">{{ __('page.PINTEREST_LINK') }}</label>
                                <input id="pinterest_link" name="pinterest_link" type="url" class="form-control"
                                    placeholder="{{ __('page.ENTER_PINTEREST_LINK') }}"
                                    value="{{ $settings['pinterest_link'] ?? '' }}">
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

        <form action="{{ route('settings.store') }}" method="post">
            @csrf
            <div class="row d-flex">
                <div class="card mt-3 admin_cards">
                    <div class="card-header">
                        <h3 class="card-title">{{ __('page.OTHER_SETTINGS') }}</h3>
                    </div>

                    <div class="card-body">
                        <div class="row">
                            <div class="col-sm-6 form-group mandatory">
                                <label for="play_store_link"
                                    class="form-label ">{{ __('page.PLAY_STORE_LINK') }}</label>
                                <input id="play_store_link" name="play_store_link" type="url" class="form-control"
                                    placeholder="{{ __('page.EMTER_PLAY_STORE_LINK') }}"
                                    value="{{ $settings['play_store_link'] ?? '' }}">
                            </div>
                            <div class="col-sm-6 form-group mandatory">
                                <label for="app_store_link" class="form-label ">{{ __('page.APP_STORE_LINK') }}</label>
                                <input id="app_store_link" name="app_store_link" type="url" class="form-control"
                                    placeholder="{{ __('page.EMTER_APP_STORE_LINK') }}"
                                    value="{{ $settings['app_store_link'] ?? '' }}">
                            </div>
                            <div class="col-sm-6 form-group mandatory mt-3">
                                <label for="android_shceme" class="form-label ">{{ __('page.ANDROID_SCHEME') }}</label>
                                <input id="android_shceme" name="android_shceme" type="text" class="form-control"
                                    placeholder="{{ __('page.EMTER_ANDROID_SCHEME') }}"
                                    value="{{ $settings['android_shceme'] ?? '' }}">
                            </div>
                            <div class="col-sm-6 form-group mandatory mt-3">
                                <label for="ios_shceme" class="form-label ">{{ __('page.IOS_SCHEME') }}</label>
                                <input id="ios_shceme" name="ios_shceme" type="text" class="form-control"
                                    placeholder="{{ __('page.EMTER_IOS_SCHEME') }}"
                                    value="{{ $settings['ios_shceme'] ?? '' }}">
                            </div>

                            <div class="col-sm-6 form-group mandatory mt-3">
                                <label for="keep_old_posts" class="form-label">
                                    {{ __('page.HOW_MANY_DAYS_OLD_POSTS_SHOULD_BE_KEPT') }}
                                </label>
                                <input id="keep_old_posts" name="keep_old_posts" type="number" class="form-control"
                                    placeholder="{{ __('page.ENTER_IN_DAYS') }}"
                                    value="{{ $settings['keep_old_posts'] ?? '' }}" min="-1" required>
                                <span class="fs-5 text-danger fw-bold">
                                    ({{ __('page.ENTER_MINUS_ONE_TO_NEVER_DELETE_OR_VALUE_FOR_AUTOMATIC_DELETE') }})
                                </span>
                            </div>

                            <div class="col-sm-6 form-group mandatory mt-3">
                                <label for="keep_old_video_posts " class="form-label">
                                    {{ __('page.HOW_MANY_DAYS_OLD_VIDEO_POSTS_SHOULD_BE_KEPT') }}
                                </label>
                                <input id="keep_old_video_posts" name="keep_old_video_posts" type="number"
                                    class="form-control" placeholder="{{ __('page.ENTER_IN_DAYS') }}"
                                    value="{{ $settings['keep_old_video_posts'] ?? '' }}" min="-1" required>
                                <span class="fs-5 text-danger fw-bold">
                                    ({{ __('page.ENTER_MINUS_ONE_TO_NEVER_DELETE_OR_VALUE_FOR_AUTOMATIC_DELETE') }})
                                </span>
                            </div>

                            <div class="col-sm-6 form-group mandatory mt-3">
                                <label for="keep_old_notification"
                                    class="form-label ">{{ __('page.HOW_MANY_DAYS_OLD_NOTIFICATIONS_SHOULD_BE_KEPT') }}</label>
                                <input id="keep_old_notification" name="keep_old_notification" type="number"
                                    class="form-control" placeholder="{{ __('page.ENTER_IN_DAYS') }}"
                                    value="{{ $settings['keep_old_notification'] ?? '' }}"
                                    oninput="this.value = Math.abs(this.value)" min="0" required>
                            </div>

                            <div class="form-group col-sm-6 col-md-6 mt-3">
                                <label for="app_name" class="form-label">{{ __('page.APP_NAME') }}</label>
                                <input id="app_name" name="app_name" type="text" class="form-control"
                                    placeholder="{{ __('page.ENTER_APP_NAME') }}"
                                    value="{{ $settings['app_name'] ?? '' }}" required>
                            </div>

                            <div class="col-sm-6 form-group mandatory mt-3">
                                <label for="" class="form-label">{{ __('page.MAINTENANCE_MODE') }}</label>
                                <div class="form-check form-switch">
                                    <input type="hidden" name="maintenance_mode" id="maintenance_mode"
                                        class="checkbox-toggle-switch-input"
                                        value="{{ $settings['maintenance_mode'] ?? 0 }}">
                                    <input class="form-check-input checkbox-toggle-switch" type="checkbox" role="switch"
                                        aria-checked="{{ $settings['maintenance_mode'] == 1 ? 'true' : 'false' }}"
                                        id="switch_maintenance_mode"
                                        {{ $settings['maintenance_mode'] == 1 ? 'checked' : '' }}>
                                </div>
                            </div>

                            <div class="col-sm-6 form-group mandatory mt-3">
                                <label for="switch_application_download_popup_on_web"
                                    class="form-label">{{ __('page.APPLICATION_DOWNLOAD_POPUP') }}</label>
                                <div class="form-check form-switch">
                                    <input type="hidden" name="application_download_popup_on_web"
                                        id="application_download_popup_on_web" class="checkbox-toggle-switch-input"
                                        value="{{ $settings['application_download_popup_on_web'] ?? 0 }}">
                                    <input class="form-check-input checkbox-toggle-switch" type="checkbox" role="switch"
                                        aria-checked="{{ isset($settings['application_download_popup_on_web']) && $settings['application_download_popup_on_web'] == 1 ? 'true' : 'false' }}"
                                        id="switch_application_download_popup_on_web"
                                        {{ isset($settings['application_download_popup_on_web']) && $settings['application_download_popup_on_web'] == 1 ? 'checked' : '' }}>
                                </div>
                            </div>

                            <div class="col-sm-12 form-group mandatory mt-3">
                                <label for="" class="form-label">{{ __('page.FREE_TRIAL_MODE') }}</label>
                                <div class="form-check form-switch">
                                    <input type="hidden" name="free_trial_status" id="free_trial_status"
                                        class="checkbox-toggle-switch-input"
                                        value="{{ $settings['free_trial_status'] ?? 0 }}">

                                    <input class="form-check-input checkbox-toggle-switch" type="checkbox" role="switch"
                                        aria-checked="{{ $settings['free_trial_status'] == 1 ? 'true' : 'false' }}"
                                        id="switch_free_trial_status"
                                        data-has-active-subscription="{{ $hasActiveSubscription ? '1' : '0' }}"
                                        {{ $settings['free_trial_status'] == 1 ? 'checked' : '' }}>
                                </div>
                                <div class="alert alert-danger form-text text-danger fw-bold">
                                    {{ __('page.FREE_TRIAL_MODE_NOTE') }}
                                </div>
                            </div>


                            <div class="col-sm-6 form-group mandatory mt-3">
                                <label for="free_trial_post_limit"
                                    class="form-label">{{ __('page.FREE_TRIAL_POST_LIMIT') }}</label>
                                <input id="free_trial_post_limit" name="free_trial_post_limit" type="number"
                                    class="form-control" placeholder="{{ __('page.ENTER_POST_LIMIT') }}"
                                    value="{{ $settings['free_trial_post_limit'] ?? '' }}"
                                    oninput="this.value = Math.abs(this.value)" min="-1" required>
                                <span class="fs-5">({{ __('page.NUMBER_OF_POSTS_FREE_TRIAL_USERS_CAN_VIEW') }})</span>
                            </div>
                            <div class="col-sm-6 form-group mandatory mt-3">
                                <label for="free_trial_story_limit"
                                    class="form-label">{{ __('page.FREE_TRIAL_STORY_LIMIT') }}</label>
                                <input id="free_trial_story_limit" name="free_trial_story_limit" type="number"
                                    class="form-control" placeholder="{{ __('page.ENTER_STORY_LIMIT') }}"
                                    value="{{ $settings['free_trial_story_limit'] ?? '' }}"
                                    oninput="this.value = Math.abs(this.value)" min="-1" required>
                                <span
                                    class="fs-5">({{ __('page.NUMBER_OF_STORIES_FREE_TRIAL_USERS_CAN_VIEW') }})</span>
                            </div>
                            <div class="col-sm-6 form-group mandatory mt-3">
                                <label for="free_trial_e_papers_and_magazines_limit"
                                    class="form-label">{{ __('page.FREE_TRIAL_E_PAPER_AND_MAGAZINES_LIMIT') }}</label>
                                <input id="free_trial_e_papers_and_magazines_limit"
                                    name="free_trial_e_papers_and_magazines_limit" type="number" class="form-control"
                                    placeholder="{{ __('page.ENTER_E_PAPER_AND_MAGAZINES_LIMIT') }}"
                                    value="{{ $settings['free_trial_e_papers_and_magazines_limit'] ?? '' }}"
                                    oninput="this.value = Math.abs(this.value)" min="-1" required>
                                <span
                                    class="fs-5">({{ __('page.NO_OF_E_PAPER_AND_MAGAZINES_FREE_TRIAL_USERS_CAN_VIEW') }})</span>
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
