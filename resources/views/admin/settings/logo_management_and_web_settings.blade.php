@extends('admin.layouts.main')
@section('title')
    {{ __('page.LOGO_MANAGEMENT_AND_WEATHER_API_KEY_SETTING') }}
@endsection
@section('pre-title')
    {{ __('page.LOGO_MANAGEMENT_AND_WEATHER_API_KEY_SETTING') }}
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
                                    <i class="bi bi-info-circle-fill text-muted m-1 cursor-pointer" data-bs-toggle="tooltip"
                                        data-bs-placement="right"
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
                                    value="{{ $settings['news_label_place_holder'] ?? '' }}" id="news_label_place_holder">
                            </div>

                            <div class="col-sm-6 form-group mandatory mt-3">
                                <label for="light_logo" class="form-label ">{{ __('page.LIGHT_LOGO') }}
                                    <i class="bi bi-info-circle-fill text-muted m-1 cursor-pointer" data-bs-toggle="tooltip"
                                        data-bs-placement="right"
                                        title="{{ __('Upload an image for the logo displayed in the light theme of your website.') }}"></i>
                                </label>
                                <input class="filepond" type="file" name="light_logo" id="light_logo">
                                <img src="{{ $settings['light_logo'] ?? '' }}"
                                    data-custom-image="{{ asset('assets/images/logo/sidebar_logo.png') }}"
                                    class="img-privew" alt="">
                                <!-- Logo Size Dropdown -->
                                <label for="light_logo_size" class="form-label mt-2">{{ __('page.LIGHT_LOGO_SIZE') }}</label>
                                <select class="form-select" name="light_logo_size" id="light_logo_size">
                                    @php $lightSize = $settings['light_logo_size'] ?? ''; @endphp

                                    <option value="w-auto" {{ $lightSize == 'w-auto' ? 'selected' : '' }}>w-auto</option>
                                    <option value="w-10"  {{ $lightSize == 'w-10' ? 'selected' : '' }}>w-10</option>
                                    <option value="w-20"  {{ $lightSize == 'w-20' ? 'selected' : '' }}>w-20</option>
                                    <option value="w-40"  {{ $lightSize == 'w-40' ? 'selected' : '' }}>w-40</option>
                                    <option value="w-50"  {{ $lightSize == 'w-50' ? 'selected' : '' }}>w-50</option>
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
                                <label for="dark_logo_size" class="form-label mt-2">{{ __('page.DARK_LOGO_SIZE') }}</label>
                                <select class="form-select" name="dark_logo_size" id="dark_logo_size">
                                    @php $darkSize = $settings['dark_logo_size'] ?? ''; @endphp

                                    <option value="w-auto" {{ $darkSize == 'w-auto' ? 'selected' : '' }}>w-auto</option>
                                    <option value="w-10"  {{ $darkSize == 'w-10' ? 'selected' : '' }}>w-10</option>
                                    <option value="w-20"  {{ $darkSize == 'w-20' ? 'selected' : '' }}>w-20</option>
                                    <option value="w-40"  {{ $darkSize == 'w-40' ? 'selected' : '' }}>w-40</option>
                                    <option value="w-50"  {{ $darkSize == 'w-50' ? 'selected' : '' }}>w-50</option>
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
        </form>
    </section>
@endsection