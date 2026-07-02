@extends('admin.layouts.main')

@section('title')
    {{ __('page.SETTINGS') }}
@endsection
@section('pre-title')
    {{ __('page.SETTINGS') }}
@endsection

@section('page-title')
    <div class="row g-2 align-items-center">
        <div class="col">
            <!-- Page pre-title -->
            <div class="page-pretitle">
                <a href="{{ url('admin/dashboard') }}">{{ __('page.HOME') }}/</a>
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
    <!-- No Results Panel (Initially Hidden) -->
    <div class="panel text-center" id="noResultsPanel">
        <img class="object-contain image uc-transition-opaque image-page"
            src="{{ asset('front_end/classic/images/place-holser/not-data.png') }}" alt="No Transactions Found">
        <div>
            <a href="{{ route('settings.index') }}" class="btn btn-primar setting_btn">Go to Settings</a>
        </div>
    </div>
    <div class="row">
        @can('basic-company-setup-settings')
            <div class="col-sm-6 col-lg-4 mt-0 setting-card" data-title="GENERAL_SETTINGS">
                <div class="card admin_cards">
                    <a href="{{ route('settings.company_setup') }}"
                        class="link-offset-2 link-underline link-underline-opacity-0 setting_cards">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div class="d-flex align-items-center p-2 rounded company_svg">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="45" height="45" fill="currentColor"
                                        class="bi bi-buildings" viewBox="0 0 16 16">
                                        <path
                                            d="M14.763.075A.5.5 0 0 1 15 .5v15a.5.5 0 0 1-.5.5h-3a.5.5 0 0 1-.5-.5V14h-1v1.5a.5.5 0 0 1-.5.5h-9a.5.5 0 0 1-.5-.5V10a.5.5 0 0 1 .342-.474L6 7.64V4.5a.5.5 0 0 1 .276-.447l8-4a.5.5 0 0 1 .487.022M6 8.694 1 10.36V15h5zM7 15h2v-1.5a.5.5 0 0 1 .5-.5h2a.5.5 0 0 1 .5.5V15h2V1.309l-7 3.5z" />
                                        <path
                                            d="M2 11h1v1H2zm2 0h1v1H4zm-2 2h1v1H2zm2 0h1v1H4zm4-4h1v1H8zm2 0h1v1h-1zm-2 2h1v1H8zm2 0h1v1h-1zm2-2h1v1h-1zm0 2h1v1h-1zM8 7h1v1H8zm2 0h1v1h-1zm2 0h1v1h-1zM8 5h1v1H8zm2 0h1v1h-1zm2 0h1v1h-1zm0-2h1v1h-1z" />
                                    </svg>
                                </div>
                                <div class="h3 ms-3 mb-0">{{ __('page.GENERAL_SETTINGS') }}</div>
                            </div>
                        </div>
                    </a>
                </div>
            </div>
        @endcan
        <div class="col-sm-6 col-lg-4 mt-0 setting-card" data-title="POPULAR_POST_TIME_RANGE_SETTING">
            <div class="card admin_cards">
                <a href="{{ route('settings.popular-post-setting') }}"
                    class="link-offset-2 link-underline link-underline-opacity-0 setting_cards">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="d-flex align-items-center p-2 rounded company_svg">
                                <svg xmlns="http://www.w3.org/2000/svg" width="45" height="45" fill="currentColor"
                                    class="bi bi-buildings" viewBox="0 0 16 16">
                                    <path
                                        d="M14.763.075A.5.5 0 0 1 15 .5v15a.5.5 0 0 1-.5.5h-3a.5.5 0 0 1-.5-.5V14h-1v1.5a.5.5 0 0 1-.5.5h-9a.5.5 0 0 1-.5-.5V10a.5.5 0 0 1 .342-.474L6 7.64V4.5a.5.5 0 0 1 .276-.447l8-4a.5.5 0 0 1 .487.022M6 8.694 1 10.36V15h5zM7 15h2v-1.5a.5.5 0 0 1 .5-.5h2a.5.5 0 0 1 .5.5V15h2V1.309l-7 3.5z" />
                                    <path
                                        d="M2 11h1v1H2zm2 0h1v1H4zm-2 2h1v1H2zm2 0h1v1H4zm4-4h1v1H8zm2 0h1v1h-1zm-2 2h1v1H8zm2 0h1v1h-1zm2-2h1v1h-1zm0 2h1v1h-1zM8 7h1v1H8zm2 0h1v1h-1zm2 0h1v1h-1zM8 5h1v1H8zm2 0h1v1h-1zm2 0h1v1h-1zm0-2h1v1h-1z" />
                                </svg>
                            </div>
                            <div class="h3 ms-3 mb-0">{{ __('page.POPULAR_POST_TIME_RANGE_SETTING') }}</div>
                        </div>
                    </div>
                </a>
            </div>
        </div>

        {{-- @can('logo-management-and-web-settings')
            <div class="col-sm-6 col-lg-4 mt-0 setting-card" data-title="LOGO_MANAGEMENT_AND_WEB_SETTINGS">
                <div class="card admin_cards">
                    <a href="{{ route('settings.logo_management_and_web_settings') }}"
                        class="link-offset-2 link-underline link-underline-opacity-0 setting_cards">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div class="d-flex align-items-center p-2 rounded logo_svg_css">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="45" height="45" fill="currentColor"
                                        class="bi bi-images" viewBox="0 0 16 16">
                                        <path d="M4.502 9a1.5 1.5 0 1 0 0-3 1.5 1.5 0 0 0 0 3" />
                                        <path
                                            d="M14.002 13a2 2 0 0 1-2 2h-10a2 2 0 0 1-2-2V5A2 2 0 0 1 2 3a2 2 0 0 1 2-2h10a2 2 0 0 1 2 2v8a2 2 0 0 1-1.998 2M14 2H4a1 1 0 0 0-1 1h9.002a2 2 0 0 1 2 2v7A1 1 0 0 0 15 11V3a1 1 0 0 0-1-1M2.002 4a1 1 0 0 0-1 1v8l2.646-2.354a.5.5 0 0 1 .63-.062l2.66 1.773 3.71-3.71a.5.5 0 0 1 .577-.094l1.777 1.947V5a1 1 0 0 0-1-1z" />
                                    </svg>
                                </div>
                                <div class="h3 ms-3 mb-0">{{ __('page.LOGO_MANAGEMENT_AND_WEATHER_API_KEY_SETTING') }}</div>
                            </div>
                        </div>
                    </a>
                </div>
            </div>
        @endcan --}}

        @can('system-health-settings')
            <div class="col-sm-6 col-lg-4 mt-0 setting-card" data-title="SYSTEM_HEALTH">
                <div class="card admin_cards">
                    <a href="{{ route('system-health-monitoring') }}"
                        class="link-offset-2 link-underline link-underline-opacity-0 setting_cards">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div class="d-flex align-items-center p-2 rounded update_svg">
                                    <i class="fas fa-h-square icon_font_size"></i>
                                </div>
                                <div class="h3 ms-3 m-2">{{ __('page.SYSTEM_HEALTH') }}</div>
                            </div>
                        </div>
                    </a>
                </div>
            </div>
        @endcan

        {{-- @can('social-link-and-other-settings')
            <div class="col-sm-6 col-lg-4 mt-0 setting-card" data-title="SOCIAL_LINKS_AND_OTHER_SETTINGS">
                <div class="card admin_cards">
                    <a href="{{ route('settings.links_and_aws_setup') }}"
                        class="link-offset-2 link-underline link-underline-opacity-0 setting_cards">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div class="d-flex align-items-center p-2 rounded links_css">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="45" height="45" fill="currentColor"
                                        class="bi bi-instagram" viewBox="0 0 16 16">
                                        <path
                                            d="M8 0C5.829 0 5.556.01 4.703.048 3.85.088 3.269.222 2.76.42a3.9 3.9 0 0 0-1.417.923A3.9 3.9 0 0 0 .42 2.76C.222 3.268.087 3.85.048 4.7.01 5.555 0 5.827 0 8.001c0 2.172.01 2.444.048 3.297.04.852.174 1.433.372 1.942.205.526.478.972.923 1.417.444.445.89.719 1.416.923.51.198 1.09.333 1.942.372C5.555 15.99 5.827 16 8 16s2.444-.01 3.298-.048c.851-.04 1.434-.174 1.943-.372a3.9 3.9 0 0 0 1.416-.923c.445-.445.718-.891.923-1.417.197-.509.332-1.09.372-1.942C15.99 10.445 16 10.173 16 8s-.01-2.445-.048-3.299c-.04-.851-.175-1.433-.372-1.941a3.9 3.9 0 0 0-.923-1.417A3.9 3.9 0 0 0 13.24.42c-.51-.198-1.092-.333-1.943-.372C10.443.01 10.172 0 7.998 0zm-.717 1.442h.718c2.136 0 2.389.007 3.232.046.78.035 1.204.166 1.486.275.373.145.64.319.92.599s.453.546.598.92c.11.281.24.705.275 1.485.039.843.047 1.096.047 3.231s-.008 2.389-.047 3.232c-.035.78-.166 1.203-.275 1.485a2.5 2.5 0 0 1-.599.919c-.28.28-.546.453-.92.598-.28.11-.704.24-1.485.276-.843.038-1.096.047-3.232.047s-2.39-.009-3.233-.047c-.78-.036-1.203-.166-1.485-.276a2.5 2.5 0 0 1-.92-.598 2.5 2.5 0 0 1-.6-.92c-.109-.281-.24-.705-.275-1.485-.038-.843-.046-1.096-.046-3.233s.008-2.388.046-3.231c.036-.78.166-1.204.276-1.486.145-.373.319-.64.599-.92s.546-.453.92-.598c.282-.11.705-.24 1.485-.276.738-.034 1.024-.044 2.515-.045zm4.988 1.328a.96.96 0 1 0 0 1.92.96.96 0 0 0 0-1.92m-4.27 1.122a4.109 4.109 0 1 0 0 8.217 4.109 4.109 0 0 0 0-8.217m0 1.441a2.667 2.667 0 1 1 0 5.334 2.667 2.667 0 0 1 0-5.334" />
                                    </svg>
                                </div>
                                <div class="h3 ms-3 mb-0">{{ __('page.SOCIAL_LINKS_AND_OTHER_SETTINGS') }}</div>
                            </div>
                        </div>
                    </a>
                </div>
            </div>
        @endcan --}}


        @can('newslanguage-settings')
            <div class="col-sm-6 col-lg-4 mt-0 setting-card" data-title="NEWS_LANGUAGE_SETTINGS">
                <div class="card admin_cards ">
                    <a href="{{ route('settings.newslanguage_section') }}"
                        class="link-offset-2 link-underline link-underline-opacity-0 setting_cards">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div class="d-flex align-items-center p-2 rounded cronjob_svg gap-3">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="45" height="45" fill="currentColor"
                                        class="bi bi-translate" viewBox="0 0 16 16">
                                        <path
                                            d="M4.545 6.714 4.11 8H3l1.862-5h1.284L8 8H6.833l-.435-1.286zm1.634-.736L5.5 3.956h-.049l-.679 2.022z" />
                                        <path
                                            d="M0 2a2 2 0 0 1 2-2h7a2 2 0 0 1 2 2v3h3a2 2 0 0 1 2 2v7a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2v-3H2a2 2 0 0 1-2-2zm2-1a1 1 0 0 0-1 1v7a1 1 0 0 0 1 1h7a1 1 0 0 0 1-1V2a1 1 0 0 0-1-1zm7.138 9.995q.289.451.63.846c-.748.575-1.673 1.001-2.768 1.292.178.217.451.635.555.867 1.125-.359 2.08-.844 2.886-1.494.777.665 1.739 1.165 2.93 1.472.133-.254.414-.673.629-.89-1.125-.253-2.057-.694-2.82-1.284.681-.747 1.222-1.651 1.621-2.757H14V8h-3v1.047h.765c-.318.844-.74 1.546-1.272 2.13a6 6 0 0 1-.415-.492 2 2 0 0 1-.94.31" />
                                    </svg>
                                </div>
                                <div class="h3 ms-3 mb-0">{{ __('page.NEWS_LANGUAGE_SETTINGS') }}</div>
                            </div>
                        </div>
                    </a>
                </div>
            </div>
        @endcan

        {{-- @can('subscription-model-and-header/footer-script-settings')
            <div class="col-sm-6 col-lg-4 mt-0 setting-card" data-title="SUBSCRIPTION_MODEL_AND_HEADER_FOOTER_SCRIPT_SETTING_">
                <div class="card admin_cards">
                    <a href="{{ route('settings.subscription-model') }}"
                        class="link-offset-2 link-underline link-underline-opacity-0 setting_cards">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div class="d-flex align-items-center p-2 rounded logo_svg_css">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="45" height="45" fill="currentColor"
                                        class="bi bi-filetype-json" viewBox="0 0 16 16">
                                        <path fill-rule="evenodd"
                                            d="M14 4.5V11h-1V4.5h-2A1.5 1.5 0 0 1 9.5 3V1H4a1 1 0 0 0-1 1v9H2V2a2 2 0 0 1 2-2h5.5zM4.151 15.29a1.2 1.2 0 0 1-.111-.449h.764a.58.58 0 0 0 .255.384q.105.073.25.114.142.041.319.041.245 0 .413-.07a.56.56 0 0 0 .255-.193.5.5 0 0 0 .084-.29.39.39 0 0 0-.152-.326q-.152-.12-.463-.193l-.618-.143a1.7 1.7 0 0 1-.539-.214 1 1 0 0 1-.352-.367 1.1 1.1 0 0 1-.123-.524q0-.366.19-.639.192-.272.528-.422.337-.15.777-.149.456 0 .779.152.326.153.5.41.18.255.2.566h-.75a.56.56 0 0 0-.12-.258.6.6 0 0 0-.246-.181.9.9 0 0 0-.37-.068q-.324 0-.512.152a.47.47 0 0 0-.185.384q0 .18.144.3a1 1 0 0 0 .404.175l.621.143q.326.075.566.211a1 1 0 0 1 .375.358q.135.222.135.56 0 .37-.188.656a1.2 1.2 0 0 1-.539.439q-.351.158-.858.158-.381 0-.665-.09a1.4 1.4 0 0 1-.478-.252 1.1 1.1 0 0 1-.29-.375m-3.104-.033a1.3 1.3 0 0 1-.082-.466h.764a.6.6 0 0 0 .074.27.5.5 0 0 0 .454.246q.285 0 .422-.164.137-.165.137-.466v-2.745h.791v2.725q0 .66-.357 1.005-.355.345-.985.345a1.6 1.6 0 0 1-.568-.094 1.15 1.15 0 0 1-.407-.266 1.1 1.1 0 0 1-.243-.39m9.091-1.585v.522q0 .384-.117.641a.86.86 0 0 1-.322.387.9.9 0 0 1-.47.126.9.9 0 0 1-.47-.126.87.87 0 0 1-.32-.387 1.55 1.55 0 0 1-.117-.641v-.522q0-.386.117-.641a.87.87 0 0 1 .32-.387.87.87 0 0 1 .47-.129q.265 0 .47.129a.86.86 0 0 1 .322.387q.117.255.117.641m.803.519v-.513q0-.565-.205-.973a1.46 1.46 0 0 0-.59-.63q-.38-.22-.916-.22-.534 0-.92.22a1.44 1.44 0 0 0-.589.628q-.205.407-.205.975v.513q0 .562.205.973.205.407.589.626.386.217.92.217.536 0 .917-.217.384-.22.589-.626.204-.41.205-.973m1.29-.935v2.675h-.746v-3.999h.662l1.752 2.66h.032v-2.66h.75v4h-.656l-1.761-2.676z" />
                                    </svg>
                                </div>
                                <div class="h3 ms-3 mb-0">{{ __('page.SUBSCRIPTION_MODEL_AND_HEADER_FOOTER_SCRIPT_SETTING') }}
                                </div>
                            </div>
                        </div>
                    </a>
                </div>
            </div>
        @endcan --}}

        @can('google-adsense-configuration')
            <div class="col-sm-6 col-lg-4 mt-0 setting-card" data-title="GOOGLE_ADSENSE_CONFIGURATION_SETTING">
                <div class="card admin_cards">
                    <a href="{{ route('settings.google-adsense-configuration') }}"
                        class="link-offset-2 link-underline link-underline-opacity-0 setting_cards">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div class="d-flex align-items-center p-2 rounded logo_svg_css">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="45" height="45" fill="currentColor"
                                        class="bi bi-google" viewBox="0 0 16 16">
                                        <path
                                            d="M15.545 6.558a9.4 9.4 0 0 1 .139 1.626c0 2.434-.87 4.492-2.384 5.953C11.722 15.645 9.626 16.5 7 16.5 3.134 16.5 0 13.366 0 9.5S3.134 2.5 7 2.5c2.094 0 3.843.86 5.047 2.03l-2.045 1.97C8.85 5.45 7.134 4.78 5.5 5.78 3.457 7.1 2.855 9.64 4.174 11.684c1.32 2.044 3.86 2.646 5.904 1.327 1.213-.785 1.905-2.035 2.07-3.293H7V6.558h8.545z" />
                                    </svg>
                                </div>
                                <div class="h3 ms-3 mb-0">
                                    {{ __('page.GOOGLE_ADSENSE_CONFIGURATION') }}
                                </div>
                            </div>
                        </div>
                    </a>
                </div>
            </div>
        @endcan


        @can('smtp-mail-configuration-settings')
            <div class="col-sm-6 col-lg-4 mt-0 setting-card" data-title="SMTP_MAIL_CONFIGURATION">
                <div class="card admin_cards">
                    <a href="{{ route('settings.smtp_mail_configuration') }}"
                        class="link-offset-2 link-underline link-underline-opacity-0 setting_cards">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div class="d-flex align-items-center p-2 rounded links_css">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="45" height="45" fill="currentColor"
                                        class="bi bi-envelope" viewBox="0 0 16 16">
                                        <path
                                            d="M0 4a2 2 0 0 1 2-2h12a2 2 0 0 1 2 2v8a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2zm2-1a1 1 0 0 0-1 1v.217l7 4.2 7-4.2V4a1 1 0 0 0-1-1zm13 2.383-4.708 2.825L15 11.105zm-.034 6.876-5.64-3.471L8 9.583l-1.326-.795-5.64 3.47A1 1 0 0 0 2 13h12a1 1 0 0 0 .966-.741M1 11.105l4.708-2.897L1 5.383z" />
                                    </svg>
                                </div>
                                <div class="h3 ms-3 mb-0">{{ __('page.SMTP_MAIL_CONFIGURATION') }}</div>
                            </div>
                        </div>
                    </a>
                </div>
            </div>
        @endcan

        @can('payment-gateway-settings')
            <div class="col-sm-6 col-lg-4 setting-card" data-title="PAYMENT_GATEWAY">
                <div class="card admin_cards">
                    <a href="{{ route('payment-gateway.index') }}"
                        class="link-offset-2 link-underline link-underline-opacity-0 setting_cards">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div class="d-flex align-items-center p-2 rounded payment_svg">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="45" height="45"
                                        fill="currentColor" class="bi bi-credit-card" viewBox="0 0 16 16">
                                        <path d="M0 4a2 2 0 0 1 2-2h12a2 2 0 0 1 2 2v1H0V4z" />
                                        <path
                                            d="M0 7v5a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V7H0zm3 2.5a.5.5 0 0 1 .5-.5h2a.5.5 0 0 1 0 1h-2a.5.5 0 0 1-.5-.5z" />
                                    </svg>
                                </div>
                                <div class="h3 ms-3 mb-0">{{ __('page.PAYMENT_GATEWAY') }}</div>
                            </div>
                        </div>
                    </a>
                </div>
            </div>
        @endcan


        @can('custom-advertising-settings')
            <div class="col-sm-6 col-lg-4 setting-card" data-title="CUSTOM_ADVERTISING_SETTINGS">
                <div class="card admin_cards">
                    <a href="{{ route('settings.custom_ads_settings') }}"
                        class="link-offset-2 link-underline link-underline-opacity-0 setting_cards">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div class="d-flex align-items-center p-2 rounded payment_svg">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="45" height="45"
                                        fill="currentColor" class="bi bi-badge-ad" viewBox="0 0 16 16">
                                        <path
                                            d="m3.7 11 .47-1.542h2.004L6.644 11h1.261L5.901 5.001H4.513L2.5 11zm1.503-4.852.734 2.426H4.416l.734-2.426zm4.759.128c-1.059 0-1.753.765-1.753 2.043v.695c0 1.279.685 2.043 1.74 2.043.677 0 1.222-.33 1.367-.804h.057V11h1.138V4.685h-1.16v2.36h-.053c-.18-.475-.68-.77-1.336-.77zm.387.923c.58 0 1.002.44 1.002 1.138v.602c0 .76-.396 1.2-.984 1.2-.598 0-.972-.449-.972-1.248v-.453c0-.795.37-1.24.954-1.24z" />
                                        <path
                                            d="M14 3a1 1 0 0 1 1 1v8a1 1 0 0 1-1 1H2a1 1 0 0 1-1-1V4a1 1 0 0 1 1-1zM2 2a2 2 0 0 0-2 2v8a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V4a2 2 0 0 0-2-2z" />
                                    </svg>
                                </div>
                                <div class="h3 ms-3 mb-0">{{ __('page.CUSTOM_ADVERTISING_SETTINGS') }}</div>
                            </div>
                        </div>
                    </a>
                </div>
            </div>
        @endcan

        @can('credit-packs-settings')
            <div class="col-sm-6 col-lg-4 setting-card" data-title="CREDIT_PACKS">
                <div class="card admin_cards">
                    <a href="{{ route('credit-packs.index') }}"
                        class="link-offset-2 link-underline link-underline-opacity-0 setting_cards">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div class="d-flex align-items-center p-2 rounded payment_svg">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="45" height="45"
                                        fill="currentColor" class="bi bi-wallet2" viewBox="0 0 16 16">
                                        <path
                                            d="M12.136.326A.5.5 0 0 1 12.5.5v1h1a1.5 1.5 0 0 1 1.5 1.5v11A1.5 1.5 0 0 1 13.5 15h-11A1.5 1.5 0 0 1 1 13.5v-11A1.5 1.5 0 0 1 2.5 1h9V.5a.5.5 0 0 1 .636-.474zM2.5 2A.5.5 0 0 0 2 2.5v11a.5.5 0 0 0 .5.5h11a.5.5 0 0 0 .5-.5v-11a.5.5 0 0 0-.5-.5h-11z" />
                                        <path d="M3 3h10v2H3V3zm0 3h10v2H3V6zm0 3h10v2H3V9z" />
                                    </svg>
                                </div>
                                <div class="h3 ms-3 mb-0">{{ __('page.CREDIT_PACKS') }}</div>
                            </div>
                        </div>
                    </a>
                </div>
            </div>
        @endcan

        @can('language-translation-settings')
            <div class="col-sm-6 col-lg-4 mt-0 setting-card" data-title="LANGUAGES">
                <div class="card admin_cards">
                    <a href="{{ route('language.index') }}"
                        class="link-offset-2 link-underline link-underline-opacity-0 setting_cards">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div class="d-flex align-items-center p-2 rounded language_svg">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="45" height="45"
                                        fill="currentColor" class="bi bi-translate" viewBox="0 0 16 16">
                                        <path
                                            d="M4.545 6.714 4.11 8H3l1.862-5h1.284L8 8H6.833l-.435-1.286zm1.634-.736L5.5 3.956h-.049l-.679 2.022z" />
                                        <path
                                            d="M0 2a2 2 0 0 1 2-2h7a2 2 0 0 1 2 2v3h3a2 2 0 0 1 2 2v7a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2v-3H2a2 2 0 0 1-2-2zm2-1a1 1 0 0 0-1 1v7a1 1 0 0 0 1 1h7a1 1 0 0 0 1-1V2a1 1 0 0 0-1-1zm7.138 9.995q.289.451.63.846c-.748.575-1.673 1.001-2.768 1.292.178.217.451.635.555.867 1.125-.359 2.08-.844 2.886-1.494.777.665 1.739 1.165 2.93 1.472.133-.254.414-.673.629-.89-1.125-.253-2.057-.694-2.82-1.284.681-.747 1.222-1.651 1.621-2.757H14V8h-3v1.047h.765c-.318.844-.74 1.546-1.272 2.13a6 6 0 0 1-.415-.492 2 2 0 0 1-.94.31" />
                                    </svg>
                                </div>
                                <div class="h3 ms-3 mb-0">{{ __('page.LANGUAGES') }}</div>
                            </div>
                        </div>
                    </a>
                </div>
            </div>
        @endcan

        @can('app-admob-and-weather-settings')
            <div class="col-sm-6 col-lg-4 mt-0 setting-card" data-title="APP_ADMOB_AND_WEATHER_KEY_SETUP">
                <div class="card admin_cards">
                    <a href="{{ route('settings.app-keys-settings') }}"
                        class="link-offset-2 link-underline link-underline-opacity-0 setting_cards">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div class="d-flex align-items-center p-2 rounded language_svg">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="45" height="45"
                                        fill="currentColor" class="bi bi-translate" viewBox="0 0 16 16">
                                        <path
                                            d="M4.545 6.714 4.11 8H3l1.862-5h1.284L8 8H6.833l-.435-1.286zm1.634-.736L5.5 3.956h-.049l-.679 2.022z" />
                                        <path
                                            d="M0 2a2 2 0 0 1 2-2h7a2 2 0 0 1 2 2v3h3a2 2 0 0 1 2 2v7a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2v-3H2a2 2 0 0 1-2-2zm2-1a1 1 0 0 0-1 1v7a1 1 0 0 0 1 1h7a1 1 0 0 0 1-1V2a1 1 0 0 0-1-1zm7.138 9.995q.289.451.63.846c-.748.575-1.673 1.001-2.768 1.292.178.217.451.635.555.867 1.125-.359 2.08-.844 2.886-1.494.777.665 1.739 1.165 2.93 1.472.133-.254.414-.673.629-.89-1.125-.253-2.057-.694-2.82-1.284.681-.747 1.222-1.651 1.621-2.757H14V8h-3v1.047h.765c-.318.844-.74 1.546-1.272 2.13a6 6 0 0 1-.415-.492 2 2 0 0 1-.94.31" />
                                    </svg>
                                </div>
                                <div class="h3 ms-3 mb-0">{{ __('page.APP_ADMOB_AND_WEATHER_KEY_SETUP') }}</div>
                            </div>
                        </div>
                    </a>
                </div>
            </div>
        @endcan

        @can('about-us-settings')
            <div class="col-sm-6 col-lg-4 setting-card" data-title="ABOUT_US">
                <div class="card admin_cards">
                    <a href="{{ route('settings.about-us.index') }}"
                        class="link-offset-2 link-underline link-underline-opacity-0 setting_cards">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div class="d-flex align-items-center p-2 rounded about-us">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="45" height="45"
                                        fill="currentColor" class="bi bi-info-circle" viewBox="0 0 16 16">
                                        <path d="M8 15A7 7 0 1 1 8 1a7 7 0 0 1 0 14m0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16" />
                                        <path
                                            d="m8.93 6.588-2.29.287-.082.38.45.083c.294.07.352.176.288.469l-.738 3.468c-.194.897.105 1.319.808 1.319.545 0 1.178-.252 1.465-.598l.088-.416c-.2.176-.492.246-.686.246-.275 0-.375-.193-.304-.533zM9 4.5a1 1 0 1 1-2 0 1 1 0 0 1 2 0" />
                                    </svg>
                                </div>
                                <div class="h3 ms-3 mb-0">{{ __('page.ABOUT_US') }}</div>
                            </div>
                        </div>
                    </a>
                </div>
            </div>
        @endcan

        @can('terms-conditions-settings')
            <div class="col-sm-6 col-lg-4 mt-0 setting-card" data-title="TERMS_CONDITIONS">
                <div class="card admin_cards">
                    <a href="{{ route('settings.terms-conditions.index') }}"
                        class="link-offset-2 link-underline link-underline-opacity-0 setting_cards">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div class="d-flex align-items-center p-2 rounded ">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="45" height="45"
                                        fill="currentColor" class="bi bi-file-earmark-fill" viewBox="0 0 16 16">
                                        <path
                                            d="M4 0h5.293A1 1 0 0 1 10 .293L13.707 4a1 1 0 0 1 .293.707V14a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V2a2 2 0 0 1 2-2m5.5 1.5v2a1 1 0 0 0 1 1h2z" />
                                    </svg>
                                </div>
                                <div class="h3 ms-3 mb-0">{{ __('page.TERMS_CONDITIONS') }}</div>

                            </div>
                        </div>
                    </a>
                </div>
            </div>
        @endcan


        @can('privacy-policy-settings')
            <div class="col-sm-6 col-lg-4 mt-0 setting-card" data-title="PRIVACY_POLICY">
                <div class="card admin_cards">
                    <a href="{{ route('settings.privacy-policy.index') }}"
                        class="link-offset-2 link-underline link-underline-opacity-0 setting_cards">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div class="d-flex align-items-center p-2 rounded privacy_svg">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="45" height="45"
                                        fill="currentColor" class="bi bi-shield-shaded" viewBox="0 0 16 16">
                                        <path fill-rule="evenodd"
                                            d="M8 14.933a1 1 0 0 0 .1-.025q.114-.034.294-.118c.24-.113.547-.29.893-.533a10.7 10.7 0 0 0 2.287-2.233c1.527-1.997 2.807-5.031 2.253-9.188a.48.48 0 0 0-.328-.39c-.651-.213-1.75-.56-2.837-.855C9.552 1.29 8.531 1.067 8 1.067zM5.072.56C6.157.265 7.31 0 8 0s1.843.265 2.928.56c1.11.3 2.229.655 2.887.87a1.54 1.54 0 0 1 1.044 1.262c.596 4.477-.787 7.795-2.465 9.99a11.8 11.8 0 0 1-2.517 2.453 7 7 0 0 1-1.048.625c-.28.132-.581.24-.829.24s-.548-.108-.829-.24a7 7 0 0 1-1.048-.625 11.8 11.8 0 0 1-2.517-2.453C1.928 10.487.545 7.169 1.141 2.692A1.54 1.54 0 0 1 2.185 1.43 63 63 0 0 1 5.072.56" />
                                    </svg>
                                </div>
                                <div class="h3 ms-3 mb-0">{{ __('page.PRIVACY_POLICY') }}</div>
                            </div>
                        </div>
                    </a>
                </div>
            </div>
        @endcan



        @can('error-logs-view-settings')
            <div class="col-sm-6 col-lg-4 mt-0 setting-card" data-title="LOG_VIEWER">
                <div class="card admin_cards">
                    <a href="{{ route('settings.error-logs.index') }}"
                        class="link-offset-2 link-underline link-underline-opacity-0 setting_cards">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div class="d-flex align-items-center p-2 rounded log_svg">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="45" height="45"
                                        fill="currentColor" class="bi bi-file-earmark-ruled" viewBox="0 0 16 16">
                                        <path
                                            d="M14 14V4.5L9.5 0H4a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h8a2 2 0 0 0 2-2M9.5 3A1.5 1.5 0 0 0 11 4.5h2V9H3V2a1 1 0 0 1 1-1h5.5zM3 12v-2h2v2zm0 1h2v2H4a1 1 0 0 1-1-1zm3 2v-2h7v1a1 1 0 0 1-1 1zm7-3H6v-2h7z" />
                                    </svg>
                                </div>
                                <div class="h3 ms-3 mb-0">{{ __('page.LOG_VIEWER') }}</div>
                            </div>
                        </div>
                    </a>
                </div>
            </div>
        @endcan

        @can('system-update-settings')
            <div class="col-sm-6 col-lg-4 mt-0 setting-card" data-title="SYSTEM_UPDATE">
                <div class="card admin_cards">
                    <a href="{{ route('system-update.index') }}"
                        class="link-offset-2 link-underline link-underline-opacity-0 setting_cards">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div class="d-flex align-items-center p-2 rounded update_svg">
                                    <i class="fas fa-sync-alt icon_font_size"></i>
                                </div>
                                <div class="h3 ms-3 m-2">{{ __('page.SYSTEM_UPDATE') }}</div>
                            </div>
                        </div>
                    </a>
                </div>
            </div>
        @endcan

        @can('firebase-settings')
            <div class="col-sm-6 col-lg-4 mt-0 setting-card" data-title="FIREBASE">
                <div class="card admin_cards">
                    <a href="{{ route('settings.firebase.index') }}"
                        class="link-offset-2 link-underline link-underline-opacity-0 setting_cards">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div class="d-flex align-items-center p-2 rounded firebase_svg gap-3">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="45" height="45"
                                        fill="currentColor" class="bi bi-fire" viewBox="0 0 16 16">
                                        <path
                                            d="M8 16c3.314 0 6-2 6-5.5 0-1.5-.5-4-2.5-6 .25 1.5-1.25 2-1.25 2C11 4 9 .5 6 0c.357 2 .5 4-2 6-1.25 1-2 2.729-2 4.5C2 14 4.686 16 8 16m0-1c-1.657 0-3-1-3-2.75 0-.75.25-2 1.25-3C6.125 10 7 10.5 7 10.5c-.375-1.25.5-3.25 2-3.5-.179 1-.25 2 1 3 .625.5 1 1.364 1 2.25C11 14 9.657 15 8 15" />
                                    </svg>
                                </div>
                                <div class="h3 ms-3 mb-0">{{ __('page.FIREBASE') }}</div>
                            </div>
                        </div>
                    </a>
                </div>
            </div>
        @endcan

        @can('notification-settings')
            <div class="col-sm-6 col-lg-4 mt-0 setting-card" data-title="NOTIFICATION_SETTINGS">
                <div class="card admin_cards">
                    <a href="{{ route('settings.notification-settings') }}"
                        class="link-offset-2 link-underline link-underline-opacity-0 setting_cards">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div class="d-flex align-items-center p-2 rounded cronjob_svg gap-3">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="45" height="45" fill="currentColor" class="bi bi-bell-fill" viewBox="0 0 16 16">
                                        <path d="M8 16a2 2 0 0 0 2-2H6a2 2 0 0 0 2 2m.995-14.901a1 1 0 1 0-1.99 0A5 5 0 0 0 3 6c0 1.098-.5 6-2 7h14c-1.5-1-2-5.902-2-7 0-2.42-1.72-4.44-4.005-4.901"/>
                                    </svg>
                                </div>
                                <div class="h3 ms-3 mb-0">{{ __('page.NOTIFICATION_SETTINGS') }}</div>
                            </div>
                        </div>
                    </a>
                </div>
            </div>
        @endcan

        @can('cronjob/info-in-settings')
            <div class="col-sm-6 col-lg-4 mt-0 setting-card" data-title="CRONJOB_INFO">
                <div class="card admin_cards ">
                    <a href="{{ route('settings.cronjob.info') }}"
                        class="link-offset-2 link-underline link-underline-opacity-0 setting_cards">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div class="d-flex align-items-center p-2 rounded cronjob_svg gap-3">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="45" height="45"
                                        fill="currentColor" class="bi bi-clock-fill" viewBox="0 0 16 16">
                                        <path
                                            d="M16 8A8 8 0 1 1 0 8a8 8 0 0 1 16 0M8 3.5a.5.5 0 0 0-1 0V9a.5.5 0 0 0 .252.434l3.5 2a.5.5 0 0 0 .496-.868L8 8.71z" />
                                    </svg>
                                </div>
                                <div class="h3 ms-3 mb-0">{{ __('page.CRONJOB_INFO') }}</div>
                            </div>
                        </div>
                    </a>
                </div>
            </div>
        @endcan
    </div>
    </section>
@endsection
