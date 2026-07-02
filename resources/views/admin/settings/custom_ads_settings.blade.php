@extends('admin.layouts.main')
@section('title')
    {{ __('page.CUSTOM_ADVERTISING_SETTINGS') }}
@endsection
@section('pre-title')
    {{ __('page.CUSTOM_ADVERTISING_SETTINGS') }}
@endsection
@section('page-title')
    <div class="row g-2 align-items-center">
        <div class="col">
            <div class="page-pretitle">
                <a href="{{ url('admin/dashboard') }}">{{ __('page.HOME') }}/</a>
                <a href="{{ url('admin/settings') }}">{{ __('page.SETTINGS') }}/</a>
                {{ __('page.CUSTOM_ADVERTISING_SETTINGS') }}
            </div>
        </div>
        <div class="col-auto ms-auto d-print-none">
        </div>
    </div>
@endsection
@section('content')
    <section class="section m-2">
        <form class="customAdSettingForm" action="{{ route('settings.custom_ad_setting') }}" method="post"
            enctype="multipart/form-data">
            @csrf
            <div class="col-md-12">
                <div class="card mt-3 admin_cards">
                    <div class="card-header">
                        <h3 class="card-title">{{ __('page.GLOBAL_CUSTOM_ADS_CONTROL') }}</h3>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-sm-12">
                                <label for="custom_ads_currency_code"
                                    class="col-sm-12 form-check-label mt-2 mb-3">{{ __('page.SELECT_CURRENCY') }}<span
                                        class="text-danger">*</span></label>
                                <select id="custom_ads_currency_code" name="currency_code"
                                    data-currency-code="{{ $settings['currency_code'] ?? '' }}"
                                    class="select2 form-select form-control-sm">
                                    <option value="USD">USD - United States Dollar</option>
                                    <option value="AED">AED - United Arab Emirates Dirham</option>
                                    <option value="AFN">AFN - Afghan Afghani</option>
                                    <option value="ALL">ALL - Albanian Lek</option>
                                    <option value="AMD">AMD - Armenian Dram</option>
                                    <option value="ANG">ANG - Netherlands Antillean Guilder</option>
                                    <option value="AOA">AOA - Angolan Kwanza</option>
                                    <option value="ARS">ARS - Argentine Peso</option>
                                    <option value="AUD">AUD - Australian Dollar</option>
                                    <option value="AWG">AWG - Aruban Florin</option>
                                    <option value="AZN">AZN - Azerbaijani Manat</option>
                                    <option value="BAM">BAM - Bosnia-Herzegovina Convertible Mark</option>
                                    <option value="BBD">BBD - Barbadian Dollar</option>
                                    <option value="BDT">BDT - Bangladeshi Taka</option>
                                    <option value="BGN">BGN - Bulgarian Lev</option>
                                    <option value="BMD">BMD - Bermudian Dollar</option>
                                    <option value="BND">BND - Brunei Dollar</option>
                                    <option value="BOB">BOB - Bolivian Boliviano</option>
                                    <option value="BRL">BRL - Brazilian Real</option>
                                    <option value="BSD">BSD - Bahamian Dollar</option>
                                    <option value="BWP">BWP - Botswana Pula</option>
                                    <option value="BYN">BYN - Belarusian Ruble</option>
                                    <option value="BZD">BZD - Belize Dollar</option>
                                    <option value="CAD">CAD - Canadian Dollar</option>
                                    <option value="CDF">CDF - Congolese Franc</option>
                                    <option value="CHF">CHF - Swiss Franc</option>
                                    <option value="CNY">CNY - Chinese Yuan</option>
                                    <option value="COP">COP - Colombian Peso</option>
                                    <option value="CRC">CRC - Costa Rican Colón</option>
                                    <option value="CVE">CVE - Cape Verdean Escudo</option>
                                    <option value="CZK">CZK - Czech Koruna</option>
                                    <option value="DKK">DKK - Danish Krone</option>
                                    <option value="DOP">DOP - Dominican Peso</option>
                                    <option value="DZD">DZD - Algerian Dinar</option>
                                    <option value="EGP">EGP - Egyptian Pound</option>
                                    <option value="ETB">ETB - Ethiopian Birr</option>
                                    <option value="EUR">EUR - Euro</option>
                                    <option value="FJD">FJD - Fijian Dollar</option>
                                    <option value="FKP">FKP - Falkland Islands Pound</option>
                                    <option value="GBP">GBP - British Pound Sterling</option>
                                    <option value="GEL">GEL - Georgian Lari</option>
                                    <option value="GIP">GIP - Gibraltar Pound</option>
                                    <option value="GMD">GMD - Gambian Dalasi</option>
                                    <option value="GTQ">GTQ - Guatemalan Quetzal</option>
                                    <option value="GYD">GYD - Guyanese Dollar</option>
                                    <option value="HKD">HKD - Hong Kong Dollar</option>
                                    <option value="HNL">HNL - Honduran Lempira</option>
                                    <option value="HTG">HTG - Haitian Gourde</option>
                                    <option value="HUF">HUF - Hungarian Forint</option>
                                    <option value="IDR">IDR - Indonesian Rupiah</option>
                                    <option value="ILS">ILS - Israeli New Shekel</option>
                                    <option value="INR">INR - Indian Rupee</option>
                                    <option value="ISK">ISK - Icelandic Króna</option>
                                    <option value="JMD">JMD - Jamaican Dollar</option>
                                    <option value="KES">KES - Kenyan Shilling</option>
                                    <option value="KGS">KGS - Kyrgyzstani Som</option>
                                    <option value="KHR">KHR - Cambodian Riel</option>
                                    <option value="KYD">KYD - Cayman Islands Dollar</option>
                                    <option value="KZT">KZT - Kazakhstani Tenge</option>
                                    <option value="LAK">LAK - Lao Kip</option>
                                    <option value="LBP">LBP - Lebanese Pound</option>
                                    <option value="LKR">LKR - Sri Lankan Rupee</option>
                                    <option value="LRD">LRD - Liberian Dollar</option>
                                    <option value="LSL">LSL - Lesotho Loti</option>
                                    <option value="MAD">MAD - Moroccan Dirham</option>
                                    <option value="MDL">MDL - Moldovan Leu</option>
                                    <option value="MKD">MKD - Macedonian Denar</option>
                                    <option value="MMK">MMK - Myanmar Kyat</option>
                                    <option value="MNT">MNT - Mongolian Tögrög</option>
                                    <option value="MOP">MOP - Macanese Pataca</option>
                                    <option value="MUR">MUR - Mauritian Rupee</option>
                                    <option value="MVR">MVR - Maldivian Rufiyaa</option>
                                    <option value="MWK">MWK - Malawian Kwacha</option>
                                    <option value="MXN">MXN - Mexican Peso</option>
                                    <option value="MYR">MYR - Malaysian Ringgit</option>
                                    <option value="MZN">MZN - Mozambican Metical</option>
                                    <option value="NAD">NAD - Namibian Dollar</option>
                                    <option value="NGN">NGN - Nigerian Naira</option>
                                    <option value="NIO">NIO - Nicaraguan Córdoba</option>
                                    <option value="NOK">NOK - Norwegian Krone</option>
                                    <option value="NPR">NPR - Nepalese Rupee</option>
                                    <option value="NZD">NZD - New Zealand Dollar</option>
                                    <option value="PAB">PAB - Panamanian Balboa</option>
                                    <option value="PEN">PEN - Peruvian Sol</option>
                                    <option value="PGK">PGK - Papua New Guinean Kina</option>
                                    <option value="PHP">PHP - Philippine Peso</option>
                                    <option value="PKR">PKR - Pakistani Rupee</option>
                                    <option value="PLN">PLN - Polish Złoty</option>
                                    <option value="QAR">QAR - Qatari Riyal</option>
                                    <option value="RON">RON - Romanian Leu</option>
                                    <option value="RSD">RSD - Serbian Dinar</option>
                                    <option value="RUB">RUB - Russian Ruble</option>
                                    <option value="SAR">SAR - Saudi Riyal</option>
                                    <option value="SBD">SBD - Solomon Islands Dollar</option>
                                    <option value="SCR">SCR - Seychellois Rupee</option>
                                    <option value="SEK">SEK - Swedish Krona</option>
                                    <option value="SGD">SGD - Singapore Dollar</option>
                                    <option value="SHP">SHP - Saint Helena Pound</option>
                                    <option value="SLE">SLE - Sierra Leonean Leone</option>
                                    <option value="SOS">SOS - Somali Shilling</option>
                                    <option value="SRD">SRD - Surinamese Dollar</option>
                                    <option value="STD">STD - São Tomé and Príncipe Dobra</option>
                                    <option value="SZL">SZL - Swazi Lilangeni</option>
                                    <option value="THB">THB - Thai Baht</option>
                                    <option value="TJS">TJS - Tajikistani Somoni</option>
                                    <option value="TOP">TOP - Tongan Paʻanga</option>
                                    <option value="TRY">TRY - Turkish Lira</option>
                                    <option value="TTD">TTD - Trinidad and Tobago Dollar</option>
                                    <option value="TWD">TWD - New Taiwan Dollar</option>
                                    <option value="TZS">TZS - Tanzanian Shilling</option>
                                    <option value="UAH">UAH - Ukrainian Hryvnia</option>
                                    <option value="UYU">UYU - Uruguayan Peso</option>
                                    <option value="UZS">UZS - Uzbekistani Som</option>
                                    <option value="WST">WST - Samoan Tālā</option>
                                    <option value="XCD">XCD - East Caribbean Dollar</option>
                                    <option value="YER">YER - Yemeni Rial</option>
                                    <option value="ZAR">ZAR - South African Rand</option>
                                    <option value="ZMW">ZMW - Zambian Kwacha</option>

                                </select>
                            </div>
                            <div class="col-sm-12 mt-2">
                                <label for="custom_ads_currency_symbol"
                                    class="col-sm-12 form-check-label">{{ __('page.CURRENCY_SYMBOL') }}</label>
                                <input id="custom_ads_currency_symbol" name="currency_symbol" type="text"
                                    class="form-control" value="{{ $settings['currency_symbol'] ?? '' }}">
                                <span class="parsley-required" id="custom_currency_symbol-error"></span>
                            </div>
                            <div class="col-sm-6 form-group mandatory mt-3">
                                <label for="" class="form-label mt-2 mt-2">{{__('page.CUSTOM_AD_FEATURE')}}</label>
                                <div class="form-check form-switch">
                                    <input type="hidden" name="enable_custom_ads_status" id="enable_custom_ads_status"
                                        class="checkbox-toggle-switch-input"
                                        value="{{ $settings['enable_custom_ads_status'] ?? 0 }}">
                                    <input class="form-check-input checkbox-toggle-switch" type="checkbox" role="switch"
                                        {{ !empty($settings['enable_custom_ads_status']) && $settings['enable_custom_ads_status'] == '1' ? 'checked' : '' }}
                                        id="switch_custom_ads_status_mode"
                                        aria-checked="{{ !empty($settings['enable_custom_ads_status']) && $settings['enable_custom_ads_status'] == '1' ? 'true' : 'false' }}">
                                </div>
                                <div class="row">
                                    <div class="col-sm-12 form-group">
                                        <label for="approval_limit_for_admin"
                                            class="form-label mt-2 mt-2">{{ __('page.APPROVAL_COUNT') }}<span
                                                class="text-danger">*</span></label>
                                        <input type="number" min="5" id="approval_limit_for_admin"
                                            name="approval_limit_for_admin" class="form-control"
                                            value="{{ $settings['approval_limit_for_admin'] ?? '' }}">
                                        <span class="parsley-required" id="custom_approval_limit_for_admin-error"></span>
                                    </div>
                                    <div class="col-sm-12 form-group">
                                        <label for="sponsor_ad_rotation_time"
                                            class="form-label mt-2 mt-2">{{ __('page.SPONSOR_AD_ROTATION_TIME') }}<span
                                                class="text-danger">*</span></label>
                                        <input type="number" id="sponsor_ad_rotation_time"
                                            name="sponsor_ad_rotation_time" class="form-control"
                                            value="{{ $settings['sponsor_ad_rotation_time'] ?? '' }}">
                                        <span class="parsley-required" id="custom_sponsor_ad_rotation_time-error"></span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-12 mt-2 form-group">
                                <label class="form-label mt-2 mt-2">{{ __('page.PAYMENT_DEADLINE') }}<span
                                        class="text-danger">*</span></label>
                                <div class="input-group">
                                    <!-- Hours -->
                                    <input type="number" min="0" id="payment_deadline_hours"
                                        name="payment_deadline_hours" class="form-control"
                                        value="{{ $settings['payment_deadline_hours'] ?? 2 }}">
                                    <span class="input-group-text">{{ __('page.HOURS') }}</span>

                                    <!-- Minutes -->
                                    <input type="number" min="0" max="59" id="payment_deadline_minutes"
                                        name="payment_deadline_minutes" class="form-control"
                                        value="{{ $settings['payment_deadline_minutes'] ?? 0 }}">
                                    <span class="input-group-text">{{ __('page.MINUTES') }}</span>
                                </div>
                                <span class="parsley-required" id="custom_payment_deadline_hours-error"></span>
                                <span class="parsley-required" id="custom_payment_deadline_minutes-error"></span>
                                <small class="text-muted">{{ __('page.PAYMENT_DEADLINE_NOTE') }}</small>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card mt-3 admin_cards">
                    <!-- Web Ads Placements -->
                    <div class="card-header">
                        <h3 class="card-title">{{ __('page.WEB_PLACEMENT_POSITIONS') }}</h3>
                    </div>
                    <div class="row">
                        <!-- Header Placement -->
                        <div class="col-md-3">
                            <div class="card mt-3 admin_cards">
                                <div class="card-header">
                                    <h3 class="card-title">{{ __('page.HEADER_PLACEMENT') }}</h3>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-sm-12 form-group">
                                            <label for="header_price"
                                                class="form-label mt-2 mt-2">{{ __('page.PRICE_PER_DAY') }}<span
                                                    class="text-danger">*</span></label>
                                            <input type="number" step="0.01" min="0" id="header_price"
                                                name="header_price" class="form-control" placeholder="0"
                                                value="{{ $settings['header_price'] ?? '' }}">
                                            <span class="parsley-required" id="custom_header_price-error"></span>
                                        </div>
                                    </div>
                                    <label for=""
                                        class="form-label mt-2 mt-2">{{ __('page.PLACEMENT_STATUS') }}</label>
                                    <div class="form-check form-switch">
                                        <input type="hidden" name="header_placement_status" id="header_placement_status"
                                            class="checkbox-toggle-switch-input"
                                            value="{{ $settings['header_placement_status'] ?? 0 }}">
                                        <input class="form-check-input checkbox-toggle-switch" type="checkbox"
                                            role="switch"
                                            {{ !empty($settings['header_placement_status']) && $settings['header_placement_status'] == '1' ? 'checked' : '' }}
                                            id="switch_header_placement_status"
                                            aria-checked="{{ !empty($settings['header_placement_status']) && $settings['header_placement_status'] == '1' ? 'true' : 'false' }}">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- Left Sidebar Placement -->
                        <div class="col-md-3">
                            <div class="card mt-3 admin_cards">
                                <div class="card-header">
                                    <h3 class="card-title">{{ __('page.LEFT_SIDEBAR_PLACEMENT') }}</h3>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-sm-12 form-group">
                                            <label for="left_sidebar_price"
                                                class="form-label mt-2 mt-2">{{ __('page.PRICE_PER_DAY') }}<span
                                                    class="text-danger">*</span></label>
                                            <input type="number" step="0.01" min="0" id="left_sidebar_price"
                                                name="left_sidebar_price" class="form-control" placeholder="0"
                                                value="{{ $settings['left_sidebar_price'] ?? '' }}">
                                            <span class="parsley-required" id="custom_left_sidebar_price-error"></span>
                                        </div>
                                    </div>
                                    <label for=""
                                        class="form-label mt-2 mt-2">{{ __('page.PLACEMENT_STATUS') }}</label>
                                    <div class="form-check form-switch">
                                        <input type="hidden" name="left_sidebar_placement_status"
                                            id="left_sidebar_placement_status" class="checkbox-toggle-switch-input"
                                            value="{{ $settings['left_sidebar_placement_status'] ?? 0 }}">
                                        <input class="form-check-input checkbox-toggle-switch" type="checkbox"
                                            role="switch"
                                            {{ !empty($settings['left_sidebar_placement_status']) && $settings['left_sidebar_placement_status'] == '1' ? 'checked' : '' }}
                                            id="switch_left_sidebar_placement_status"
                                            aria-checked="{{ !empty($settings['left_sidebar_placement_status']) && $settings['left_sidebar_placement_status'] == '1' ? 'true' : 'false' }}">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- Banner Slider Placement -->
                        <div class="col-md-3">
                            <div class="card mt-3 admin_cards">
                                <div class="card-header">
                                    <h3 class="card-title">{{ __('page.BANNER_SLIDER_PLACEMENT') }}</h3>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-sm-12 form-group">
                                            <label for="banner_slider_price"
                                                class="form-label mt-2 mt-2">{{ __('page.PRICE_PER_DAY') }}<span
                                                    class="text-danger">*</span></label>
                                            <input type="number" step="0.01" min="0" id="banner_slider_price"
                                                name="banner_slider_price" class="form-control" placeholder="0"
                                                value="{{ $settings['banner_slider_price'] ?? '' }}">
                                            <span class="parsley-required" id="custom_banner_slider_price-error"></span>
                                        </div>
                                    </div>
                                    <label for=""
                                        class="form-label mt-2 mt-2">{{ __('page.PLACEMENT_STATUS') }}</label>
                                    <div class="form-check form-switch">
                                        <input type="hidden" name="banner_slider_placement_status"
                                            id="banner_slider_placement_status" class="checkbox-toggle-switch-input"
                                            value="{{ $settings['banner_slider_placement_status'] ?? 0 }}">
                                        <input class="form-check-input checkbox-toggle-switch" type="checkbox"
                                            role="switch"
                                            {{ !empty($settings['banner_slider_placement_status']) && $settings['banner_slider_placement_status'] == '1' ? 'checked' : '' }}
                                            id="switch_banner_slider_placement_status"
                                            aria-checked="{{ !empty($settings['banner_slider_placement_status']) && $settings['banner_slider_placement_status'] == '1' ? 'true' : 'false' }}">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- Post Detail Page Placement -->
                        <div class="col-md-3">
                            <div class="card mt-3 admin_cards">
                                <div class="card-header">
                                    <h3 class="card-title">{{ __('page.POST_DETAIL_PAGE_PLACEMENT') }}</h3>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-sm-12 form-group">
                                            <label for="post_detail_page_price"
                                                class="form-label mt-2 mt-2">{{ __('page.PRICE_PER_DAY') }}<span
                                                    class="text-danger">*</span></label>
                                            <input type="number" step="0.01" min="0"
                                                id="post_detail_page_price" name="post_detail_page_price"
                                                class="form-control" placeholder="0"
                                                value="{{ $settings['post_detail_page_price'] ?? '' }}">
                                            <span class="parsley-required"
                                                id="custom_post_detail_page_price-error"></span>
                                        </div>
                                    </div>
                                    <label for=""
                                        class="form-label mt-2 mt-2">{{ __('page.PLACEMENT_STATUS') }}</label>
                                    <div class="form-check form-switch">
                                        <input type="hidden" name="post_detail_page_placement_status"
                                            id="post_detail_page_placement_status" class="checkbox-toggle-switch-input"
                                            value="{{ $settings['post_detail_page_placement_status'] ?? 0 }}">
                                        <input class="form-check-input checkbox-toggle-switch" type="checkbox"
                                            role="switch"
                                            {{ !empty($settings['post_detail_page_placement_status']) && $settings['post_detail_page_placement_status'] == '1' ? 'checked' : '' }}
                                            id="switch_post_detail_page_placement_status"
                                            aria-checked="{{ !empty($settings['post_detail_page_placement_status']) && $settings['post_detail_page_placement_status'] == '1' ? 'true' : 'false' }}">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- Latest Placement -->
                        <div class="col-md-3">
                            <div class="card mt-3 admin_cards">
                                <div class="card-header">
                                    <h3 class="card-title">{{ __('page.LATEST_PLACEMENT') }}</h3>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-sm-12 form-group">
                                            <label for="latest_price"
                                                class="form-label mt-2 mt-2">{{ __('page.PRICE_PER_DAY') }}<span
                                                    class="text-danger">*</span></label>
                                            <input type="number" step="0.01" min="0" id="latest_price"
                                                name="latest_price" class="form-control" placeholder="0"
                                                value="{{ $settings['latest_price'] ?? '' }}">
                                            <span class="parsley-required" id="custom_latest_price-error"></span>
                                        </div>
                                    </div>
                                    <label for=""
                                        class="form-label mt-2 mt-2">{{ __('page.PLACEMENT_STATUS') }}</label>
                                    <div class="form-check form-switch">
                                        <input type="hidden" name="latest_placement_status" id="latest_placement_status"
                                            class="checkbox-toggle-switch-input"
                                            value="{{ $settings['latest_placement_status'] ?? 0 }}">
                                        <input class="form-check-input checkbox-toggle-switch" type="checkbox"
                                            role="switch"
                                            {{ !empty($settings['latest_placement_status']) && $settings['latest_placement_status'] == '1' ? 'checked' : '' }}
                                            id="switch_latest_placement_status"
                                            aria-checked="{{ !empty($settings['latest_placement_status']) && $settings['latest_placement_status'] == '1' ? 'true' : 'false' }}">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- Popular Placement -->
                        <div class="col-md-3">
                            <div class="card mt-3 admin_cards">
                                <div class="card-header">
                                    <h3 class="card-title">{{ __('page.POPULAR_PLACEMENT') }}</h3>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-sm-12 form-group">
                                            <label for="popular_price"
                                                class="form-label mt-2 mt-2">{{ __('page.PRICE_PER_DAY') }}<span
                                                    class="text-danger">*</span></label>
                                            <input type="number" step="0.01" min="0" id="popular_price"
                                                name="popular_price" class="form-control" placeholder="0"
                                                value="{{ $settings['popular_price'] ?? '' }}">
                                            <span class="parsley-required" id="custom_popular_price-error"></span>
                                        </div>
                                    </div>
                                    <label for=""
                                        class="form-label mt-2 mt-2">{{ __('page.PLACEMENT_STATUS') }}</label>
                                    <div class="form-check form-switch">
                                        <input type="hidden" name="popular_placement_status"
                                            id="popular_placement_status" class="checkbox-toggle-switch-input"
                                            value="{{ $settings['popular_placement_status'] ?? 0 }}">
                                        <input class="form-check-input checkbox-toggle-switch" type="checkbox"
                                            role="switch"
                                            {{ !empty($settings['popular_placement_status']) && $settings['popular_placement_status'] == '1' ? 'checked' : '' }}
                                            id="switch_popular_placement_status"
                                            aria-checked="{{ !empty($settings['popular_placement_status']) && $settings['popular_placement_status'] == '1' ? 'true' : 'false' }}">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- Posts Placement -->
                        <div class="col-md-3">
                            <div class="card mt-3 admin_cards">
                                <div class="card-header">
                                    <h3 class="card-title">{{ __('page.POSTS_PLACEMENT') }}</h3>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-sm-12 form-group">
                                            <label for="posts_price"
                                                class="form-label mt-2 mt-2">{{ __('page.PRICE_PER_DAY') }}<span
                                                    class="text-danger">*</span></label>
                                            <input type="number" step="0.01" min="0" id="posts_price"
                                                name="posts_price" class="form-control" placeholder="0"
                                                value="{{ $settings['posts_price'] ?? '' }}">
                                            <span class="parsley-required" id="custom_posts_price-error"></span>
                                        </div>
                                    </div>
                                    <label for=""
                                        class="form-label mt-2 mt-2">{{ __('page.PLACEMENT_STATUS') }}</label>
                                    <div class="form-check form-switch">
                                        <input type="hidden" name="posts_placement_status" id="posts_placement_status"
                                            class="checkbox-toggle-switch-input"
                                            value="{{ $settings['posts_placement_status'] ?? 0 }}">
                                        <input class="form-check-input checkbox-toggle-switch" type="checkbox"
                                            role="switch"
                                            {{ !empty($settings['posts_placement_status']) && $settings['posts_placement_status'] == '1' ? 'checked' : '' }}
                                            id="switch_posts_placement_status"
                                            aria-checked="{{ !empty($settings['posts_placement_status']) && $settings['posts_placement_status'] == '1' ? 'true' : 'false' }}">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- Topic Posts Placement -->
                        <div class="col-md-3">
                            <div class="card mt-3 admin_cards">
                                <div class="card-header">
                                    <h3 class="card-title">{{ __('page.TOPIC_POSTS_PLACEMENT') }}</h3>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-sm-12 form-group">
                                            <label for="topic_posts_price"
                                                class="form-label mt-2 mt-2">{{ __('page.PRICE_PER_DAY') }}<span
                                                    class="text-danger">*</span></label>
                                            <input type="number" step="0.01" min="0" id="topic_posts_price"
                                                name="topic_posts_price" class="form-control" placeholder="0"
                                                value="{{ $settings['topic_posts_price'] ?? '' }}">
                                            <span class="parsley-required" id="custom_topic_posts_price-error"></span>
                                        </div>
                                    </div>
                                    <label for=""
                                        class="form-label mt-2 mt-2">{{ __('page.PLACEMENT_STATUS') }}</label>
                                    <div class="form-check form-switch">
                                        <input type="hidden" name="topic_posts_placement_status"
                                            id="topic_posts_placement_status" class="checkbox-toggle-switch-input"
                                            value="{{ $settings['topic_posts_placement_status'] ?? 0 }}">
                                        <input class="form-check-input checkbox-toggle-switch" type="checkbox"
                                            role="switch"
                                            {{ !empty($settings['topic_posts_placement_status']) && $settings['topic_posts_placement_status'] == '1' ? 'checked' : '' }}
                                            id="switch_topic_posts_placement_status"
                                            aria-checked="{{ !empty($settings['topic_posts_placement_status']) && $settings['topic_posts_placement_status'] == '1' ? 'true' : 'false' }}">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- Videos Placement -->
                        <div class="col-md-3">
                            <div class="card mt-3 admin_cards">
                                <div class="card-header">
                                    <h3 class="card-title">{{ __('page.VIDEOS_PLACEMENT') }}</h3>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-sm-12 form-group">
                                            <label for="videos_price"
                                                class="form-label mt-2 mt-2">{{ __('page.PRICE_PER_DAY') }}<span
                                                    class="text-danger">*</span></label>
                                            <input type="number" step="0.01" min="0" id="videos_price"
                                                name="videos_price" class="form-control" placeholder="0"
                                                value="{{ $settings['videos_price'] ?? '' }}">
                                            <span class="parsley-required" id="custom_videos_price-error"></span>
                                        </div>
                                    </div>
                                    <label for=""
                                        class="form-label mt-2 mt-2">{{ __('page.PLACEMENT_STATUS') }}</label>
                                    <div class="form-check form-switch">
                                        <input type="hidden" name="videos_placement_status" id="videos_placement_status"
                                            class="checkbox-toggle-switch-input"
                                            value="{{ $settings['videos_placement_status'] ?? 0 }}">
                                        <input class="form-check-input checkbox-toggle-switch" type="checkbox"
                                            role="switch"
                                            {{ !empty($settings['videos_placement_status']) && $settings['videos_placement_status'] == '1' ? 'checked' : '' }}
                                            id="switch_videos_placement_status"
                                            aria-checked="{{ !empty($settings['videos_placement_status']) && $settings['videos_placement_status'] == '1' ? 'true' : 'false' }}">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- Right Sidebar Placement -->
                        <div class="col-md-3">
                            <div class="card mt-3 admin_cards">
                                <div class="card-header">
                                    <h3 class="card-title">{{ __('page.RIGHT_SIDEBAR_PLACEMENT') }}</h3>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-sm-12 form-group">
                                            <label for="right_sidebar_price"
                                                class="form-label mt-2 mt-2">{{ __('page.PRICE_PER_DAY') }}<span
                                                    class="text-danger">*</span></label>
                                            <input type="number" step="0.01" min="0" id="right_sidebar_price"
                                                name="right_sidebar_price" class="form-control" placeholder="0"
                                                value="{{ $settings['right_sidebar_price'] ?? '' }}">
                                            <span class="parsley-required" id="custom_right_sidebar_price-error"></span>
                                        </div>
                                    </div>
                                    <label for=""
                                        class="form-label mt-2 mt-2">{{ __('page.PLACEMENT_STATUS') }}</label>
                                    <div class="form-check form-switch">
                                        <input type="hidden" name="right_sidebar_placement_status"
                                            id="right_sidebar_placement_status" class="checkbox-toggle-switch-input"
                                            value="{{ $settings['right_sidebar_placement_status'] ?? 0 }}">
                                        <input class="form-check-input checkbox-toggle-switch" type="checkbox"
                                            role="switch"
                                            {{ !empty($settings['right_sidebar_placement_status']) && $settings['right_sidebar_placement_status'] == '1' ? 'checked' : '' }}
                                            id="switch_right_sidebar_placement_status"
                                            aria-checked="{{ !empty($settings['right_sidebar_placement_status']) && $settings['right_sidebar_placement_status'] == '1' ? 'true' : 'false' }}">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- Footer Placement -->
                        <div class="col-md-3">
                            <div class="card mt-3 admin_cards">
                                <div class="card-header">
                                    <h3 class="card-title">{{ __('page.FOOTER_PLACEMENT') }}</h3>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-sm-12 form-group">
                                            <label for="footer_price"
                                                class="form-label mt-2 mt-2">{{ __('page.PRICE_PER_DAY') }}<span
                                                    class="text-danger">*</span></label>
                                            <input type="number" step="0.01" min="0" id="footer_price"
                                                name="footer_price" class="form-control" placeholder="0"
                                                value="{{ $settings['footer_price'] ?? '' }}">
                                            <span class="parsley-required" id="custom_footer_price-error"></span>
                                        </div>
                                    </div>
                                    <label for=""
                                        class="form-label mt-2 mt-2">{{ __('page.PLACEMENT_STATUS') }}</label>
                                    <div class="form-check form-switch">
                                        <input type="hidden" name="footer_placement_status" id="footer_placement_status"
                                            class="checkbox-toggle-switch-input"
                                            value="{{ $settings['footer_placement_status'] ?? 0 }}">
                                        <input class="form-check-input checkbox-toggle-switch" type="checkbox"
                                            role="switch"
                                            {{ !empty($settings['footer_placement_status']) && $settings['footer_placement_status'] == '1' ? 'checked' : '' }}
                                            id="switch_footer_placement_status"
                                            aria-checked="{{ !empty($settings['footer_placement_status']) && $settings['footer_placement_status'] == '1' ? 'true' : 'false' }}">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card mt-3 admin_cards">
                    <div class="card-header">
                        <h3 class="card-title">{{ __('page.APP_PLACEMENT_POSITIONS') }}</h3>
                    </div>
                    <div class="row">
                        <!-- category_news_page Placement -->
                        <div class="col-md-3">
                            <div class="card mt-3 admin_cards">
                                <div class="card-header">
                                    <h3 class="card-title">{{ __('page.CATEGORY_NEWS_PAGE_PLACEMENT') }}</h3>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-sm-12 form-group">
                                            <label for="category_news_page_price"
                                                class="form-label mt-2 mt-2">{{ __('page.PRICE_PER_DAY') }}<span
                                                    class="text-danger">*</span></label>
                                            <input type="number" step="0.01" min="0"
                                                id="category_news_page_price" name="category_news_page_price"
                                                class="form-control" placeholder="0"
                                                value="{{ $settings['category_news_page_price'] ?? '' }}">
                                            <span class="parsley-required"
                                                id="custom_category_news_page_price-error"></span>
                                        </div>
                                    </div>
                                    <label for=""
                                        class="form-label mt-2 mt-2">{{ __('page.PLACEMENT_STATUS') }}</label>
                                    <div class="form-check form-switch">
                                        <input type="hidden" name="category_news_page_placement_status"
                                            id="category_news_page_placement_status" class="checkbox-toggle-switch-input"
                                            value="{{ $settings['category_news_page_placement_status'] ?? 0 }}">
                                        <input class="form-check-input checkbox-toggle-switch" type="checkbox"
                                            role="switch"
                                            {{ !empty($settings['category_news_page_placement_status']) && $settings['category_news_page_placement_status'] == '1' ? 'checked' : '' }}
                                            id="switch_category_news_page_placement_status"
                                            aria-checked="{{ !empty($settings['category_news_page_placement_status']) && $settings['category_news_page_placement_status'] == '1' ? 'true' : 'false' }}">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- Topics Page Placement -->
                        <div class="col-md-3">
                            <div class="card mt-3 admin_cards">
                                <div class="card-header">
                                    <h3 class="card-title">{{ __('page.TOPICS_PAGE_PLACEMENT') }}</h3>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-sm-12 form-group">
                                            <label for="topics_page_price"
                                                class="form-label mt-2 mt-2">{{ __('page.PRICE_PER_DAY') }}<span
                                                    class="text-danger">*</span></label>
                                            <input type="number" step="0.01" min="0" id="topics_page_price"
                                                name="topics_page_price" class="form-control" placeholder="0"
                                                value="{{ $settings['topics_page_price'] ?? '' }}">
                                            <span class="parsley-required" id="custom_topics_page_price-error"></span>
                                        </div>
                                    </div>
                                    <label for=""
                                        class="form-label mt-2 mt-2">{{ __('page.PLACEMENT_STATUS') }}</label>
                                    <div class="form-check form-switch">
                                        <input type="hidden" name="topics_page_placement_status"
                                            id="topics_page_placement_status" class="checkbox-toggle-switch-input"
                                            value="{{ $settings['topics_page_placement_status'] ?? 0 }}">
                                        <input class="form-check-input checkbox-toggle-switch" type="checkbox"
                                            role="switch"
                                            {{ !empty($settings['topics_page_placement_status']) && $settings['topics_page_placement_status'] == '1' ? 'checked' : '' }}
                                            id="switch_topics_page_placement_status"
                                            aria-checked="{{ !empty($settings['topics_page_placement_status']) && $settings['topics_page_placement_status'] == '1' ? 'true' : 'false' }}">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- After Weather Section Placement -->
                        <div class="col-md-3">
                            <div class="card mt-3 admin_cards">
                                <div class="card-header">
                                    <h3 class="card-title">{{ __('page.AFTER_WEATHER_SECTION_PLACEMENT') }}</h3>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-sm-12 form-group">
                                            <label for="after_weather_section_price"
                                                class="form-label mt-2 mt-2">{{ __('page.PRICE_PER_DAY') }}<span
                                                    class="text-danger">*</span></label>
                                            <input type="number" step="0.01" min="0"
                                                id="after_weather_section_price" name="after_weather_section_price"
                                                class="form-control" placeholder="0"
                                                value="{{ $settings['after_weather_section_price'] ?? '' }}">
                                            <span class="parsley-required"
                                                id="custom_after_weather_section_price-error"></span>
                                        </div>
                                    </div>
                                    <label for=""
                                        class="form-label mt-2 mt-2">{{ __('page.PLACEMENT_STATUS') }}</label>
                                    <div class="form-check form-switch">
                                        <input type="hidden" name="after_weather_section_placement_status"
                                            id="after_weather_section_placement_status"
                                            class="checkbox-toggle-switch-input"
                                            value="{{ $settings['after_weather_section_placement_status'] ?? 0 }}">
                                        <input class="form-check-input checkbox-toggle-switch" type="checkbox"
                                            role="switch"
                                            {{ !empty($settings['after_weather_section_placement_status']) && $settings['after_weather_section_placement_status'] == '1' ? 'checked' : '' }}
                                            id="switch_after_weather_section_placement_status"
                                            aria-checked="{{ !empty($settings['after_weather_section_placement_status']) && $settings['after_weather_section_placement_status'] == '1' ? 'true' : 'false' }}">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- Above Recommendations Section Placement -->
                        <div class="col-md-3">
                            <div class="card mt-3 admin_cards">
                                <div class="card-header">
                                    <h3 class="card-title">{{ __('page.ABOVE_RECOMMENDATIONS_SECTION_PLACEMENT') }}</h3>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-sm-12 form-group">
                                            <label for="above_recommendations_section_price"
                                                class="form-label mt-2 mt-2">{{ __('page.PRICE_PER_DAY') }}<span
                                                    class="text-danger">*</span></label>
                                            <input type="number" step="0.01" min="0"
                                                id="above_recommendations_section_price"
                                                name="above_recommendations_section_price" class="form-control"
                                                placeholder="0"
                                                value="{{ $settings['above_recommendations_section_price'] ?? '' }}">
                                            <span class="parsley-required"
                                                id="custom_above_recommendations_section_price-error"></span>
                                        </div>
                                    </div>
                                    <label for=""
                                        class="form-label mt-2 mt-2">{{ __('page.PLACEMENT_STATUS') }}</label>
                                    <div class="form-check form-switch">
                                        <input type="hidden" name="above_recommendations_section_placement_status"
                                            id="above_recommendations_section_placement_status"
                                            class="checkbox-toggle-switch-input"
                                            value="{{ $settings['above_recommendations_section_placement_status'] ?? 0 }}">
                                        <input class="form-check-input checkbox-toggle-switch" type="checkbox"
                                            role="switch"
                                            {{ !empty($settings['above_recommendations_section_placement_status']) && $settings['above_recommendations_section_placement_status'] == '1' ? 'checked' : '' }}
                                            id="switch_above_recommendations_section_placement_status"
                                            aria-checked="{{ !empty($settings['above_recommendations_section_placement_status']) && $settings['above_recommendations_section_placement_status'] == '1' ? 'true' : 'false' }}">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- All Channels Placement -->
                        <div class="col-md-3">
                            <div class="card mt-3 admin_cards">
                                <div class="card-header">
                                    <h3 class="card-title">{{ __('page.ALL_CHANNELS_PLACEMENT') }}</h3>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-sm-12 form-group">
                                            <label for="all_channels_price"
                                                class="form-label mt-2 mt-2">{{ __('page.PRICE_PER_DAY') }}<span
                                                    class="text-danger">*</span></label>
                                            <input type="number" step="0.01" min="0" id="all_channels_price"
                                                name="all_channels_price" class="form-control" placeholder="0"
                                                value="{{ $settings['all_channels_price'] ?? '' }}">
                                            <span class="parsley-required" id="custom_all_channels_price-error"></span>
                                        </div>
                                    </div>
                                    <label for=""
                                        class="form-label mt-2 mt-2">{{ __('page.PLACEMENT_STATUS') }}</label>
                                    <div class="form-check form-switch">
                                        <input type="hidden" name="all_channels_placement_status"
                                            id="all_channels_placement_status" class="checkbox-toggle-switch-input"
                                            value="{{ $settings['all_channels_placement_status'] ?? 0 }}">
                                        <input class="form-check-input checkbox-toggle-switch" type="checkbox"
                                            role="switch"
                                            {{ !empty($settings['all_channels_placement_status']) && $settings['all_channels_placement_status'] == '1' ? 'checked' : '' }}
                                            id="switch_all_channels_placement_status"
                                            aria-checked="{{ !empty($settings['all_channels_placement_status']) && $settings['all_channels_placement_status'] == '1' ? 'true' : 'false' }}">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- App Search Page Floating Ad Placement -->
                        <div class="col-md-3">
                            <div class="card mt-3 admin_cards">
                                <div class="card-header">
                                    <h3 class="card-title">{{ __('page.SPLASH_SCREEN_AD_PLACEMENT') }}</h3>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-sm-12 form-group">
                                            <label for="splash_screen_page_price"
                                                class="form-label mt-2 mt-2">{{ __('page.PRICE_PER_DAY') }}<span
                                                    class="text-danger">*</span></label>
                                            <input type="number" step="0.01" min="0"
                                                id="splash_screen_page_price" name="splash_screen_page_price"
                                                class="form-control" placeholder="0"
                                                value="{{ $settings['splash_screen_page_price'] ?? '' }}">
                                            <span class="parsley-required"
                                                id="custom_splash_screen_page_price-error"></span>
                                        </div>
                                    </div>
                                    <label for=""
                                        class="form-label mt-2 mt-2">{{ __('page.PLACEMENT_STATUS') }}</label>
                                    <div class="form-check form-switch">
                                        <input type="hidden" name="splash_screen_page_placement_status"
                                            id="splash_screen_page_placement_status" class="checkbox-toggle-switch-input"
                                            value="{{ $settings['splash_screen_page_placement_status'] ?? 0 }}">
                                        <input class="form-check-input checkbox-toggle-switch" type="checkbox"
                                            role="switch"
                                            {{ !empty($settings['splash_screen_page_placement_status']) && $settings['splash_screen_page_placement_status'] == '1' ? 'checked' : '' }}
                                            id="switch_splash_screen_page_placement_status"
                                            aria-checked="{{ !empty($settings['splash_screen_page_placement_status']) && $settings['splash_screen_page_placement_status'] == '1' ? 'true' : 'false' }}">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- Channels Page Floating Ad Placement -->
                        <div class="col-md-3">
                            <div class="card mt-3 admin_cards">
                                <div class="card-header">
                                    <h3 class="card-title">{{ __('page.CHANNELS_PAGE_FLOATING_AD_PLACEMENT') }}</h3>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-sm-12 form-group">
                                            <label for="channels_page_floating_price"
                                                class="form-label mt-2 mt-2">{{ __('page.PRICE_PER_DAY') }}<span
                                                    class="text-danger">*</span></label>
                                            <input type="number" step="0.01" min="0"
                                                id="channels_page_floating_price" name="channels_page_floating_price"
                                                class="form-control" placeholder="0"
                                                value="{{ $settings['channels_page_floating_price'] ?? '' }}">
                                            <span class="parsley-required"
                                                id="custom_channels_page_floating_price-error"></span>
                                        </div>
                                    </div>
                                    <label for=""
                                        class="form-label mt-2 mt-2">{{ __('page.PLACEMENT_STATUS') }}</label>
                                    <div class="form-check form-switch">
                                        <input type="hidden" name="channels_page_floating_placement_status"
                                            id="channels_page_floating_placement_status"
                                            class="checkbox-toggle-switch-input"
                                            value="{{ $settings['channels_page_floating_placement_status'] ?? 0 }}">
                                        <input class="form-check-input checkbox-toggle-switch" type="checkbox"
                                            role="switch"
                                            {{ !empty($settings['channels_page_floating_placement_status']) && $settings['channels_page_floating_placement_status'] == '1' ? 'checked' : '' }}
                                            id="switch_channels_page_floating_placement_status"
                                            aria-checked="{{ !empty($settings['channels_page_floating_placement_status']) && $settings['channels_page_floating_placement_status'] == '1' ? 'true' : 'false' }}">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- Discover Page Floating Ad Placement -->
                        <div class="col-md-3">
                            <div class="card mt-3 admin_cards">
                                <div class="card-header">
                                    <h3 class="card-title">{{ __('page.DISCOVER_PAGE_FLOATING_AD_PLACEMENT') }}</h3>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-sm-12 form-group">
                                            <label for="discover_page_floating_price"
                                                class="form-label mt-2 mt-2">{{ __('page.PRICE_PER_DAY') }}<span
                                                    class="text-danger">*</span></label>
                                            <input type="number" step="0.01" min="0"
                                                id="discover_page_floating_price" name="discover_page_floating_price"
                                                class="form-control" placeholder="0"
                                                value="{{ $settings['discover_page_floating_price'] ?? '' }}">
                                            <span class="parsley-required"
                                                id="custom_discover_page_floating_price-error"></span>
                                        </div>
                                    </div>
                                    <label for=""
                                        class="form-label mt-2 mt-2">{{ __('page.PLACEMENT_STATUS') }}</label>
                                    <div class="form-check form-switch">
                                        <input type="hidden" name="discover_page_floating_placement_status"
                                            id="discover_page_floating_placement_status"
                                            class="checkbox-toggle-switch-input"
                                            value="{{ $settings['discover_page_floating_placement_status'] ?? 0 }}">
                                        <input class="form-check-input checkbox-toggle-switch" type="checkbox"
                                            role="switch"
                                            {{ !empty($settings['discover_page_floating_placement_status']) && $settings['discover_page_floating_placement_status'] == '1' ? 'checked' : '' }}
                                            id="switch_discover_page_floating_placement_status"
                                            aria-checked="{{ !empty($settings['discover_page_floating_placement_status']) && $settings['discover_page_floating_placement_status'] == '1' ? 'true' : 'false' }}">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- Video Page Floating Ad Placement -->
                        <div class="col-md-3">
                            <div class="card mt-3 admin_cards">
                                <div class="card-header">
                                    <h3 class="card-title">{{ __('page.VIDEO_PAGE_FLOATING_AD_PLACEMENT') }}</h3>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-sm-12 form-group">
                                            <label for="video_page_floating_price"
                                                class="form-label mt-2 mt-2">{{ __('page.PRICE_PER_DAY') }}<span
                                                    class="text-danger">*</span></label>
                                            <input type="number" step="0.01" min="0"
                                                id="video_page_floating_price" name="video_page_floating_price"
                                                class="form-control" placeholder="0"
                                                value="{{ $settings['video_page_floating_price'] ?? '' }}">
                                            <span class="parsley-required"
                                                id="custom_video_page_floating_price-error"></span>
                                        </div>
                                    </div>
                                    <label for=""
                                        class="form-label mt-2 mt-2">{{ __('page.PLACEMENT_STATUS') }}</label>
                                    <div class="form-check form-switch">
                                        <input type="hidden" name="video_page_floating_placement_status"
                                            id="video_page_floating_placement_status" class="checkbox-toggle-switch-input"
                                            value="{{ $settings['video_page_floating_placement_status'] ?? 0 }}">
                                        <input class="form-check-input checkbox-toggle-switch" type="checkbox"
                                            role="switch"
                                            {{ !empty($settings['video_page_floating_placement_status']) && $settings['video_page_floating_placement_status'] == '1' ? 'checked' : '' }}
                                            id="switch_video_page_floating_placement_status"
                                            aria-checked="{{ !empty($settings['video_page_floating_placement_status']) && $settings['video_page_floating_placement_status'] == '1' ? 'true' : 'false' }}">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- After Read More Button Placement -->
                        <div class="col-md-3">
                            <div class="card mt-3 admin_cards">
                                <div class="card-header">
                                    <h3 class="card-title">{{ __('page.AFTER_READ_MORE_BUTTON_PLACEMENT') }}</h3>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-sm-12 form-group">
                                            <label for="after_read_more_price"
                                                class="form-label mt-2">{{ __('page.PRICE_PER_DAY') }}<span
                                                    class="text-danger">*</span></label>
                                            <input type="number" step="0.01" min="0"
                                                id="after_read_more_price" name="after_read_more_price"
                                                class="form-control" placeholder="0"
                                                value="{{ $settings['after_read_more_price'] ?? '' }}">
                                            <span class="parsley-required" id="custom_after_read_more_price-error"></span>
                                        </div>
                                    </div>
                                    <label for=""
                                        class="form-label mt-2">{{ __('page.PLACEMENT_STATUS') }}</label>
                                    <div class="form-check form-switch">
                                        <input type="hidden" name="after_read_more_placement_status"
                                            id="after_read_more_placement_status" class="checkbox-toggle-switch-input"
                                            value="{{ $settings['after_read_more_placement_status'] ?? 0 }}">
                                        <input class="form-check-input checkbox-toggle-switch" type="checkbox"
                                            role="switch"
                                            {{ !empty($settings['after_read_more_placement_status']) && $settings['after_read_more_placement_status'] == '1' ? 'checked' : '' }}
                                            id="switch_after_read_more_placement_status"
                                            aria-checked="{{ !empty($settings['after_read_more_placement_status']) && $settings['after_read_more_placement_status'] == '1' ? 'true' : 'false' }}">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- App Banner Slider Placement -->
                        <div class="col-md-3">
                            <div class="card mt-3 admin_cards">
                                <div class="card-header">
                                    <h3 class="card-title">{{ __('page.BANNER_SLIDER_PLACEMENT') }}</h3>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-sm-12 form-group">
                                            <label for="app_banner_slider_price"
                                                class="form-label mt-2">{{ __('page.PRICE_PER_DAY') }}<span
                                                    class="text-danger">*</span></label>
                                            <input type="number" step="0.01" min="0"
                                                id="app_banner_slider_price" name="app_banner_slider_price"
                                                class="form-control" placeholder="0"
                                                value="{{ $settings['app_banner_slider_price'] ?? '' }}">
                                            <span class="parsley-required"
                                                id="custom_app_banner_slider_price-error"></span>
                                        </div>
                                    </div>
                                    <label for=""
                                        class="form-label mt-2 mt-2">{{ __('page.PLACEMENT_STATUS') }}</label>
                                    <div class="form-check form-switch">
                                        <input type="hidden" name="app_banner_slider_placement_status"
                                            id="app_banner_slider_placement_status" class="checkbox-toggle-switch-input"
                                            value="{{ $settings['app_banner_slider_placement_status'] ?? 0 }}">
                                        <input class="form-check-input checkbox-toggle-switch" type="checkbox"
                                            role="switch"
                                            {{ !empty($settings['app_banner_slider_placement_status']) && $settings['app_banner_slider_placement_status'] == '1' ? 'checked' : '' }}
                                            id="switch_app_banner_slider_placement_status"
                                            aria-checked="{{ !empty($settings['app_banner_slider_placement_status']) && $settings['app_banner_slider_placement_status'] == '1' ? 'true' : 'false' }}">
                                    </div>
                                </div>
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
    </section>
@endsection
