@extends('admin.layouts.main')
@section('title')
    {{ __('page.PAYMENT_GATEWAY') }}
@endsection
@section('pre-title')
    {{ __('page.PAYMENT_GATEWAY') }}
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
    <div class="row">
        <!-- Left Sidebar - Keep your original structure -->
        <div class="col-lg-4 col-md-12 mb-3">
            <div class="card admin_cards">
                <section class="section">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="card admin_cards">
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-12">
                                            <div class="accordion" id="paymentGatewayAccordion">
                                                @php
                                                    $gateways = [
                                                        [
                                                            'key' => 'stripe',
                                                            'name' => __('page.STRIPE_SETTING'),
                                                            'class' => 'primary',
                                                            'icon' => 'fab fa-stripe',
                                                        ],
                                                        [
                                                            'key' => 'razorpay',
                                                            'name' => __('page.RAZORPAY_SETTING'),
                                                            'class' => 'success',
                                                            'icon' => 'bi bi-r-square',
                                                        ],
                                                        [
                                                            'key' => 'applepay',
                                                            'name' => __('page.APPLE_PAY_SETTING'),
                                                            'class' => 'warning',
                                                            'svg' => 'fab fa-apple',
                                                        ],
                                                    ];
                                                @endphp

                                                <div class="accordion" id="paymentGatewayAccordion">
                                                    @foreach ($gateways as $gateway)
                                                        <div class="card mb-3 admin_cards border">
                                                            <h2 id="gatewayHeading{{ $gateway['key'] }}">
                                                                <button
                                                                    class="accordion-button payment-btn  w-100 text-start"
                                                                    type="button" data-gateway="{{ $gateway['key'] }}"
                                                                    data-bs-target="#{{ $gateway['key'] }}-form"
                                                                    aria-expanded="false"
                                                                    aria-controls="{{ $gateway['key'] }}-form">
                                                                    <div
                                                                        class="d-flex flex-row align-items-center border-0">
                                                                        <div class="p-4 border border admin_cards mb-0">
                                                                            <i
                                                                                class="{{ $gateway['icon'] ?? $gateway['svg'] }} fa-2x text-{{ $gateway['class'] }}"></i>
                                                                        </div>
                                                                        <span class="ms-2">
                                                                            {{ $gateway['name'] }}
                                                                        </span>
                                                                    </div>
                                                                </button>
                                                            </h2>
                                                        </div>
                                                    @endforeach
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>
            </div>
        </div>

        <!-- Right Content Area -->
        <div class="col-lg-8 col-md-12">
            <div class="card mb-0 admin_cards">
                <div class="card-body">
                    <form class="stripe_create_form" action="{{ route('payment-gateway.store') }}" method="post"
                        enctype="multipart/form-data">
                        @csrf
                        <div class="row d-flex">
                            {{-- Stripe Payment Gateway START --}}
                            <div class="col-md-12 gateway-form collapse" id="stripe-form">
                                <div class="card h-100 admin_cards border-0">
                                    <div class="card-header">
                                        <h3 class="card-title">{{ __('page.STRIPE_SETTING') }}</h3>
                                    </div>
                                    <div class="card-body">
                                        <div class="form-group row">
                                            <div class="col-sm-12">
                                                <label for="stripe_currency_code"
                                                    class="col-sm-12 form-check-label mt-2 mb-3">{{ __('page.SELECT_CURRENCY_FOR_STRIPE') }}<span
                                            class="text-danger">*</span></label>
                                                <select name="gateway[Stripe][currency_code]" id="stripe_currency_code"
                                                    data-currency-code="{{ $paymentGateway['Stripe']['currency_code'] ?? '' }}"
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
                                                    <option value="BAM">BAM - Bosnia-Herzegovina Convertible Mark
                                                    </option>
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
                                                <span class="text-danger" id="stripe_currency_code-error"></span>

                                            </div>
                                            <label for="stripe_currency_symbol"
                                                class="col-sm-12 form-check-label mt-3">{{ __('page.STRIPE_CURRENCY_SYMBOL') }}<span
                                            class="text-danger">*</span></label>
                                            <div class="col-sm-12 mt-2">
                                                <input id="stripe_currency_symbol" name="gateway[Stripe][currency_symbol]"
                                                    type="text" class="form-control" readonly
                                                    value="{{ $paymentGateway['Stripe']['currency_symbol'] ?? '' }}">
                                                <span class="parsley-required"><strong
                                                        id="stripe_currency_symbol-error"></strong></span>
                                            </div>
                                            <label for="stripe_secret_key"
                                                class="col-sm-12 form-check-label mt-3">{{ __('page.STRIPE_SECRET_KEY') }}<span
                                            class="text-danger">*</span></label>
                                            <div class="col-sm-12 mt-2">
                                                <input id="stripe_secret_key" name="gateway[Stripe][stripe_secret]"
                                                    type="text" class="form-control"
                                                    placeholder="{{ __('page.STRIPE_SECRET_KEY') }}"
                                                    value="{{ $paymentGateway['Stripe']['stripe_secret'] ?? '' }}">
                                                <span class="parsley-required"><strong
                                                        id="stripe_stripe_secret-error"></strong></span>
                                            </div>
                                            <label for="stripe_publishable_key"
                                                class="col-sm-12 form-check-label mt-3">{{ __('page.STRIPE_PUBLISHABLE_KEY') }}<span
                                            class="text-danger">*</span></label>
                                            <div class="col-sm-12 mt-2">
                                                <input id="stripe_publishable_key"
                                                    name="gateway[Stripe][stripe_publishable]" type="text"
                                                    class="form-control"
                                                    placeholder="{{ __('page.STRIPE_PUBLISHABLE_KEY') }}"
                                                    value="{{ $paymentGateway['Stripe']['stripe_publishable'] ?? '' }}">
                                                <span class="parsley-required"><strong
                                                        id="stripe_stripe_publishable-error"></strong></span>
                                            </div>
                                            <label for="stripe_webhook_secret"
                                                class="col-sm-12 form-check-label mt-3">{{ __('page.STRIPE_WEBHOOK_SECRET') }}<span
                                            class="text-danger">*</span></label>
                                            <div class="col-sm-12 mt-2">
                                                <input id="stripe_webhook_secret"
                                                    name="gateway[Stripe][webhook_secret_key]" type="text"
                                                    class="form-control"
                                                    placeholder="{{ __('page.STRIPE_WEBHOOK_SECRET') }}"
                                                    value="{{ $paymentGateway['Stripe']['webhook_secret_key'] ?? '' }}">
                                                <span class="parsley-required"><strong
                                                        id="stripe_webhook_secret_key-error"></strong></span>
                                            </div>
                                            <label for="stripe_webhook_url"
                                                class="col-sm-12 form-check-label mt-3">{{ __('page.STRIPE_WEBHOOK_URL') }}<span
                                            class="text-danger">*</span></label>
                                            <div class="col-sm-12 mt-2">
                                                <input id="stripe_webhook_url" name="gateway[Stripe][webhook_url]"
                                                    type="text" class="form-control"
                                                    placeholder="{{ __('page.STRIPE_WEBHOOK_URL') }}"
                                                    value="{{ url('/webhook/stripe') }}" >
                                                <span class="parsley-required"><strong
                                                        id="stripe_webhook_url-error"></strong></span>
                                            </div>
                                            <label for="" class="col-sm-12 form-check-label mt-2"
                                                id='lbl_stripe'>{{ __('page.STATUS') }}</label>
                                            <div class="col-sm-2 col-md-12 col-xs-12 mt-2">
                                                <div class="form-check form-switch">
                                                    <input type="hidden" name="gateway[Stripe][status]"
                                                        id="stripe_gateway"
                                                        value="{{ $paymentGateway['Stripe']['status'] ?? 0 }}">
                                                    <input class="form-check-input switch-input status-switch"
                                                        type="checkbox" role="switch" name='op'
                                                        {{ isset($paymentGateway['Stripe']['status']) && $paymentGateway['Stripe']['status'] == '1' ? 'checked' : '' }}
                                                        id="switch_stripe_gateway" aria-label="switch_stripe_gateway"
                                                        aria-checked="{{ isset($paymentGateway['Stripe']['status']) && $paymentGateway['Stripe']['status'] == '1' ? 'true' : 'false' }}">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-12 d-flex justify-content-end">
                                            <button type="submit"
                                                class="btn btn-primary me-1 mb-3">{{ __('page.SAVE') }}</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            {{-- Stripe Payment Gateway END --}}
                        </div>
                    </form>
                    <form class="razorpay_create_form" action="{{ route('payment-gateway.store') }}" method="post"
                        enctype="multipart/form-data">
                        @csrf
                        {{-- Razorpay Payment Gateway START --}}
                        <div class="col-md-12 gateway-form collapse" id="razorpay-form">
                            <div class="card h-100 admin_cards border-0">
                                <div class="card-header">
                                    <h3 class="card-title">{{ __('page.RAZORPAY_SETTING') }}</h3>
                                </div>
                                <div class="card-body">
                                    <div class="form-group row">
                                        <div class="col-sm-12">
                                            <label for="razorpay_currency_code"
                                                class="col-sm-12 form-check-label mt-2 mb-3">{{ __('page.SELECT_CURRENCY_FOR_RAZORPAY') }}<span
                                            class="text-danger">*</span></label>
                                            <select name="gateway[Razorpay][currency_code]" id="razorpay_currency_code"
                                                data-currency-code="{{ $paymentGateway['Razorpay']['currency_code'] ?? '' }}"
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
                                            <span class="text-danger" id="razorpay_currency_code-error"></span>
                                        </div>
                                        <label for="razorpay_currency_symbol"
                                            class="col-sm-12 form-check-label mt-3">{{ __('page.RAZORPAY_CURRENCY_SYMBOL') }}<span
                                            class="text-danger">*</span></label>
                                        <div class="col-sm-12 mt-2">
                                            <input id="razorpay_currency_symbol" name="gateway[Razorpay][currency_symbol]"
                                                type="text" class="form-control"
                                                value="{{ $paymentGateway['Razorpay']['currency_symbol'] ?? '' }}">
                                            <span class="parsley-required"><strong
                                                    id="razorpay_currency_symbol-error"></strong></span>
                                        </div>
                                        <label for="razorpay_secret_key"
                                            class="col-sm-12 form-check-label mt-3">{{ __('page.RAZORPAY_SECRET_KEY') }}<span
                                            class="text-danger">*</span></label>
                                        <div class="col-sm-12 mt-2">
                                            <input id="razorpay_secret_key" name="gateway[Razorpay][secret_key]"
                                                type="text" class="form-control"
                                                placeholder="{{ __('page.RAZORPAY_SECRET_KEY') }}"
                                                value="{{ $paymentGateway['Razorpay']['secret_key'] ?? '' }}">
                                            <span class="parsley-required"><strong
                                                    id="razorpay_secret_key-error"></strong></span>
                                        </div>
                                        <label for="razorpay_public_key"
                                            class="col-sm-12 form-check-label mt-3">{{ __('page.RAZORPAY_PUBLIC_KEY') }}<span
                                            class="text-danger">*</span></label>
                                        <div class="col-sm-12 mt-2">
                                            <input id="razorpay_public_key" name="gateway[Razorpay][publishable_key]"
                                                type="text" class="form-control"
                                                placeholder="{{ __('page.RAZORPAY_PUBLIC_KEY') }}"
                                                value="{{ $paymentGateway['Razorpay']['publishable_key'] ?? '' }}">
                                            <span class="parsley-required"><strong
                                                    id="razorpay_publishable_key-error"></strong></span>
                                        </div>
                                        <label for="razorpay_webhook_secret"
                                            class="col-sm-12 form-check-label mt-3">{{ __('page.RAZORPAY_WEBHOOK_SECRET') }}<span
                                            class="text-danger">*</span></label>
                                        <div class="col-sm-12 mt-2">
                                            <input id="razorpay_webhook_secret"
                                                name="gateway[Razorpay][webhook_secret_key]" type="text"
                                                class="form-control"
                                                placeholder="{{ __('page.RAZORPAY_WEBHOOK_SECRET') }}"
                                                value="{{ $paymentGateway['Razorpay']['webhook_secret_key'] ?? '' }}">
                                            <span class="parsley-required"><strong
                                                    id="razorpay_webhook_secret_key-error"></strong></span>
                                        </div>
                                        <label for="razorpay_webhook_url"
                                            class="col-sm-12 form-check-label mt-3">{{ __('page.RAZORPAY_WEBHOOK_URL') }}<span
                                            class="text-danger">*</span></label>
                                        <div class="col-sm-12 mt-2">
                                            <input id="razorpay_webhook_url" name="gateway[Razorpay][webhook_url]"
                                                type="text" class="form-control"
                                                placeholder="{{ __('page.RAZORPAY_WEBHOOK_URL') }}"
                                                value="{{ url('/webhook/razorpay') }}">
                                        </div>
                                        <label for="" class="col-sm-12 form-check-label mt-2"
                                            id='lbl_stripe'>{{ __('page.STATUS') }}</label>
                                        <div class="col-sm-2 col-md-12 col-xs-12 mt-2">
                                            <div class="form-check form-switch">
                                                <input type="hidden" name="gateway[Razorpay][status]"
                                                    id="razorpay_gateway"
                                                    value="{{ $paymentGateway['Razorpay']['status'] ?? 0 }}">
                                                <input class="form-check-input switch-input status-switch" type="checkbox"
                                                    role="switch" name='op'
                                                    {{ isset($paymentGateway['Razorpay']['status']) && $paymentGateway['Razorpay']['status'] == '1' ? 'checked' : '' }}
                                                    id="switch_razorpay_gateway" aria-label="switch_razorpay_gateway"
                                                    aria-checked="{{ isset($paymentGateway['Razorpay']['status']) && $paymentGateway['Razorpay']['status'] == '1' ? 'true' : 'false' }}">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-12 d-flex justify-content-end">
                                        <button type="submit"
                                            class="btn btn-primary me-1 mb-3">{{ __('page.SAVE') }}</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        {{-- Razorpay Payment Gateway END --}}
                    </form>

                    <form class="apple_pay_create_form" action="{{ route('payment-gateway.store') }}" method="post"
                        enctype="multipart/form-data">
                        @csrf
                        <div class="row d-flex">
                            {{-- Apple Pay Payment Gateway START --}}
                            <div class="col-md-12 gateway-form" id="applepay-form" style="display: none;">
                                <div class="card h-100 admin_cards">
                                    <div class="card-header">
                                        <h3 class="card-title">{{ __('page.APPLE_PAY_SETTING') }}</h3>
                                    </div>
                                    <div class="card-body">
                                        <div class="form-group row">
                                            <div class="col-sm-6">
                                                <label for="applepay_currency_code"
                                                    class="form-check-label mt-2 mb-3">{{ __('page.SELECT_CURRENCY_FOR_APPLEPAY') }}<span
                                            class="text-danger">*</span></label>
                                                <select name="gateway[applepay][currency_code]"
                                                    id="applepay_currency_code"
                                                    data-currency-code="{{ $paymentGateway['applepay']['currency_code'] ?? '' }}"
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
                                                    <option value="BAM">BAM - Bosnia-Herzegovina Convertible Mark
                                                    </option>
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
                                                <span class="parsley-required"><strong
                                                        id="apple_pay_currency_code-error"></strong></span>
                                            </div>
                                            <div class="col-sm-6">
                                                <label for="applepay_currency_symbol"
                                                    class="form-check-label mt-2 mb-3">{{ __('page.APPLEPAY_CURRENCY_SYMBOL') }}<span
                                            class="text-danger">*</span></label>
                                                <input id="applepay_currency_symbol"
                                                    name="gateway[applepay][currency_symbol]" type="text"
                                                    class="form-control" readonly
                                                    value="{{ $paymentGateway['applepay']['currency_symbol'] ?? '' }}">
                                                <span class="parsley-required"><strong
                                                        id="apple_pay_currency_symbol-error"></strong></span>
                                            </div>

                                            <div class="row">
                                                <!-- Apple Shared Secret -->
                                                <div class="col-sm-6">
                                                    <label for="apple_shared_secret"
                                                        class="form-label mt-2">{{ __('page.APPLE_SHARED_SECRET') }}<span
                                            class="text-danger">*</span></label>
                                                    <input id="apple_shared_secret"
                                                        name="gateway[applepay][apple_shared_secret]" type="text"
                                                        class="form-control"
                                                        value="{{ $paymentGateway['applepay']['apple_shared_secret'] ?? '' }}">
                                                    <span class="parsley-required"><strong
                                                            id="apple_pay_apple_shared_secret-error"></strong></span>
                                                </div>

                                                <!-- Apple Issuer ID -->
                                                <div class="col-sm-6">
                                                    <label for="apple_issuer_id"
                                                        class="form-label mt-2">{{ __('page.APPLE_ISSUER_ID') }}<span
                                            class="text-danger">*</span></label>
                                                    <input id="apple_issuer_id" name="gateway[applepay][apple_issuer_id]"
                                                        type="text" class="form-control"
                                                        value="{{ $paymentGateway['applepay']['apple_issuer_id'] ?? '' }}">
                                                    <span class="parsley-required"><strong
                                                            id="apple_pay_apple_issuer_id-error"></strong></span>
                                                </div>

                                                <!-- Apple Key ID -->
                                                <div class="col-sm-6">
                                                    <label for="apple_key_id"
                                                        class="form-label mt-2">{{ __('page.APPLE_KEY_ID') }}<span
                                            class="text-danger">*</span></label>
                                                    <input id="apple_key_id" name="gateway[applepay][apple_key_id]"
                                                        type="text" class="form-control"
                                                        value="{{ $paymentGateway['applepay']['apple_key_id'] ?? '' }}">
                                                    <span class="parsley-required"><strong
                                                            id="apple_pay_apple_key_id-error"></strong></span>
                                                </div>

                                                <!-- Apple Bundle ID -->
                                                <div class="col-sm-6">
                                                    <label for="apple_bundle_id"
                                                        class="form-label mt-2">{{ __('page.APPLE_BUNDLE_ID') }}<span
                                            class="text-danger">*</span></label>
                                                    <input id="apple_bundle_id" name="gateway[applepay][apple_bundle_id]"
                                                        type="text" class="form-control"
                                                        value="{{ $paymentGateway['applepay']['apple_bundle_id'] ?? '' }}">
                                                    <span class="parsley-required"><strong
                                                            id="apple_pay_apple_bundle_id-error"></strong></span>
                                                </div>

                                                <!-- Apple API Key Path -->
                                                <div class="col-sm-6 col-md-6">
                                                    <label for=""
                                                        class="form-label col-12">{{ __('page.APPLE_API_KEY_FILE') }}<span
                                            class="text-danger">*</span></label>
                                                    <input name="apple_api_key_file" class="form-control col-12"
                                                        type="file" accept=".p8"
                                                        {{ !empty($paymentGateway['applepay']['apple_api_key_path']) ? '' : 'required' }}>
                                                    <small class="form-text text-muted">Upload Apple API Key file (.p8
                                                        format)</small>
                                                    {{-- Show existing file if available --}}
                                                    @if (isset($paymentGateway['applepay']['apple_api_key_path']) && $paymentGateway['applepay']['apple_api_key_path'])
                                                        <div class="mt-2">
                                                            <p class="text-muted mt-1">
                                                                Current File:
                                                                <strong>{{ basename($paymentGateway['applepay']['apple_api_key_path']) }}</strong>
                                                            </p>
                                                        </div>
                                                    @endif
                                                </div>


                                                <!-- Apple Environment -->
                                                <div class="col-sm-6">
                                                    <label for="apple_environment"
                                                        class="form-label mt-2">{{ __('page.APPLE_ENVIRONMENT') }}</label>
                                                    <select id="apple_environment"
                                                        name="gateway[applepay][apple_environment]" class="form-select">
                                                        <option value="sandbox"
                                                            {{ ($paymentGateway['applepay']['apple_environment'] ?? '') == 'Sandbox' ? 'selected' : '' }}>
                                                            Sandbox
                                                        </option>
                                                        <option value="production"
                                                            {{ ($paymentGateway['applepay']['apple_environment'] ?? '') == 'Production' ? 'selected' : '' }}>
                                                            Production
                                                        </option>
                                                    </select>
                                                </div>

                                                <div class="col-sm-6">
                                                    <label for="" class="form-check-label mt-2"
                                                        id='lbl_applepay'>{{ __('page.STATUS') }}</label>
                                                    <div class="form-check form-switch">
                                                        <input type="hidden" name="gateway[applepay][status]"
                                                            id="applepay_gateway"
                                                            value="{{ $paymentGateway['applepay']['status'] ?? 0 }}">
                                                        <input class="form-check-input switch-input status-switch"
                                                            type="checkbox" role="switch" name='op'
                                                            {{ isset($paymentGateway['applepay']['status']) && $paymentGateway['applepay']['status'] == '1' ? 'checked' : '' }}
                                                            id="switch_applepay_gateway"
                                                            aria-label="switch_applepay_gateway"
                                                            aria-checked="{{ isset($paymentGateway['applepay']['status']) && $paymentGateway['applepay']['status'] == '1' ? 'true' : 'false' }}">
                                                    </div>
                                                </div>
                                            </div>

                                        </div>
                                        <div class="col-12 d-flex justify-content-end">
                                            <button type="submit"
                                                class="btn btn-primary me-1 mb-3">{{ __('page.SAVE') }}</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>

                </div>
            </div>
        </div>
    </div>
@endsection
