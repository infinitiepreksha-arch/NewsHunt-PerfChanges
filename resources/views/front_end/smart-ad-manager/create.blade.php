@extends('front_end.layouts.app')

@section('content')
    <div class="container px-6 mx-auto grid">
        @if (
            !$hasCreatedAd ||
                (is_object($smartAdsDetail) &&
                    ($smartAdsDetail->ad_publish_status === 'rejected' ||
                        ($smartAdsDetail->ad_publish_status === 'approved' &&
                            $smartAdsDetail->payment_status === 'success' &&
                            !\Carbon\Carbon::parse($smartAdsDetail->end_date)->isFuture()))))
            <h2 class="my-6 text-2xl font-semibold text-gray-700 dark:text-gray-200">
                {{ $title }}
            </h2>
            <form action="{{ route('smart-ads.store') }}" method="POST" enctype="multipart/form-data" id="ad-form">
                @csrf
                <div class="px-4 py-3 mb-8 bg-white rounded shadow-md dark:bg-gray-800">

                    <!-- Advertisement Name -->
                    <label class="block text-sm p-3">
                        <span class="text-gray-700 dark:text-gray-400">{{ __('frontend-labels.sponsor_ads.ad_name') }}</span>
                        <input
                            class="block w-full mt-1 text-sm dark:border-gray-600 dark:bg-gray-700 focus:border-purple-400 focus:outline-none focus:shadow-outline-purple dark:text-gray-300 dark:focus:shadow-outline-gray form-input rounded"
                            placeholder="{{ __('frontend-labels.sponsor_ads.ad_name') }}" name="name" id="title"
                            value="{{ old('name') }}" />
                        @error('name')
                            <span class="text-xs text-red-800 dark:text-red-400">
                                {{ $message }}
                            </span>
                        @enderror
                    </label>

                    <!-- Ad Type Selection -->
                    <div x-data="{
                        adType: '{{ !empty(old('adType')) ? old('adType') : 'image' }}'
                    }">
                        <div class="w-max flex  p-3">
                            <label class="dark:text-white text-gray-700 my-1 flex items-center">
                                <input x-model="adType" type="radio" name="adType" value="image" class="mr-2 w-4 h-4"
                                    checked="">
                                <span>{{ __('frontend-labels.sponsor_ads.image_ad') }}</span>
                            </label>
                        </div>

                        <div x-show="adType == 'image'">
                            <div class=" p-3">
                                <label class="block text-sm mb-4">
                                    <span
                                        class="dark:text-white text-gray-700 text-gray-400 font-medium">{{ __('frontend-labels.sponsor_ads.upload_ad_images') }}</span>
                                </label>

                                <!-- Horizontal Image Upload -->
                                <div class="mb-6">
                                    <h3 class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-3">
                                        {{ __('frontend-labels.sponsor_ads.horizontal_image') }} <span
                                            class="text-red-500">*</span>
                                        <span
                                            class="text-xs font-normal text-gray-500">{{ __('frontend-labels.sponsor_ads.horizontal_image_hint') }}</span>
                                    </h3>

                                    <div id="upload-area-horizontal"
                                        {{-- class="border border-2 mb-5 border-dashed border-gray-300 rounded p-6 text-center cursor-pointer transition-all duration-300 hover:border-purple-400 hover:bg-gray-50 dark:border-gray-600 dark:hover:border-purple-400 dark:hover:bg-gray-700" --}}
                                        class="mb-5 border-dashed p-6 text-center cursor-pointer transition-all duration-300 hover:border-purple-400 hover:bg-gray-50 dark:border-gray-600 dark:bg-gray-700 form-textarea focus:border-purple-400 focus:outline-none focus:shadow-outline-purple dark:focus:shadow-outline-gray dark:hover:bg-gray-700 rounded"
                                        onclick="document.getElementById('horizontal-image').click();">

                                        <!-- Upload Placeholder -->
                                        <div id="upload-placeholder-horizontal">
                                            <div class="mb-3 p-5 rounded">
                                                <svg class="mx-auto h-5 w-5 text-gray-400 mt-5 mb-5" stroke="currentColor"
                                                    fill="none" viewBox="0 0 48 48">
                                                    <path
                                                        d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02"
                                                        stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                                </svg>
                                                <p class="text-gray-600 dark:text-gray-400">
                                                    {{ __('frontend-labels.sponsor_ads.click_to_select_horizontal_image_or') }}
                                                    <span
                                                        class="text-purple-600 font-medium">{{ __('frontend-labels.sponsor_ads.browse') }}</span>
                                                </p>
                                                <p class="text-xs text-gray-500 dark:text-gray-300 mt-1">
                                                    {{ __('frontend-labels.sponsor_ads.horizontal_image_format') }}
                                                </p>
                                            </div>
                                        </div>

                                        <!-- Horizontal Image Preview -->
                                        <div id="image-preview-horizontal" class="hidden p-3">
                                            <div class="relative inline-block">
                                                <div class="bg-white dark:bg-gray-800 p-2 rounded shadow-lg">
                                                    <img id="preview-image-horizontal" src=""
                                                        alt="Horizontal Preview"
                                                        class="max-w-full max-h-32 object-contain mx-auto rounded">
                                                </div>
                                                <button type="button"
                                                    class="absolute -top-2 -right-2 bg-red-500 text-white rounded-full w-6 h-6 flex items-center justify-center text-xs hover:bg-red-600"
                                                    onclick="removeImage(event, 'horizontal')"
                                                    title="Remove Image">×</button>
                                            </div>
                                            <div class="mt-2 space-y-1">
                                                <div class="text-sm text-gray-600 dark:text-gray-400"
                                                    id="file-name-horizontal"></div>
                                                <div class="flex justify-center gap-2 text-xs">
                                                    <span id="file-size-horizontal"
                                                        class="bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-300 px-2 py-1 rounded"></span>
                                                    <span id="dimensions-horizontal"
                                                        class="bg-green-100 dark:bg-green-900 text-green-700 dark:text-green-300 px-2 py-1 rounded"></span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <input type="file" name="horizontal_image" id="horizontal-image" class="hidden"
                                        accept="image/*" onchange="handleFileSelect(event, 'horizontal')">
                                    <div id="error-message-horizontal"
                                        class="text-sm text-red-600 dark:text-red-400 mt-2 p-2 bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded hidden">
                                    </div>
                                </div>

                                <!-- Vertical Image Upload -->
                                <div class="mb-6">
                                    <h3 class="text-sm font-medium text-gray-700 dark:text-gray-300 mt-4">
                                        {{ __('frontend-labels.sponsor_ads.vertical_image') }} <span
                                            class="text-red-500">*</span>
                                        <span
                                            class="text-xs font-normal text-gray-500">{{ __('frontend-labels.sponsor_ads.vertical_image_hint') }}</span>
                                    </h3>

                                    <div id="upload-area-vertical"
                                        {{-- class=" mt-4 border border-2 border-dashed border-gray-300 rounded p-6 text-center cursor-pointer transition-all duration-300 hover:border-purple-400 hover:bg-gray-50 dark:border-gray-600 dark:hover:border-purple-400 dark:hover:bg-gray-700" --}}
                                        class=" mt-4  p-6 text-center cursor-pointer transition-all duration-300 hover:border-purple-400 hover:bg-gray-50 dark:border-gray-600 dark:bg-gray-700 form-textarea focus:border-purple-400 focus:outline-none focus:shadow-outline-purple dark:focus:shadow-outline-gray dark:hover:bg-gray-700 rounded"
                                        onclick="document.getElementById('vertical-image').click();">

                                        <!-- Upload Placeholder -->
                                        <div id="upload-placeholder-vertical">
                                            <div class="mb-3 p-5 rounded">
                                                <svg class="mx-auto h-5 w-5 text-gray-400 mt-5 mb-5" stroke="currentColor"
                                                    fill="none" viewBox="0 0 48 48">
                                                    <path
                                                        d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02"
                                                        stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                                </svg>
                                                <p class="text-gray-600 dark:text-gray-400">
                                                    {{ __('frontend-labels.sponsor_ads.click_to_select_vertical_image_or') }}
                                                    <span
                                                        class="text-purple-600 font-medium">{{ __('frontend-labels.sponsor_ads.browse') }}</span>
                                                </p>
                                                <p class="text-xs text-gray-500 dark:text-gray-300 mt-1">
                                                    {{ __('frontend-labels.sponsor_ads.vertical_image_format') }}
                                                </p>
                                            </div>
                                        </div>

                                        <!-- Vertical Image Preview -->
                                        <div id="image-preview-vertical" class="hidden p-3">
                                            <div class="relative inline-block">
                                                <div class="bg-white dark:bg-gray-800 p-2 rounded shadow-lg">
                                                    <img id="preview-image-vertical" src="" alt="Vertical Preview"
                                                        class="max-w-full max-h-32 object-contain mx-auto rounded">
                                                </div>
                                                <button type="button"
                                                    class="absolute -top-2 -right-2 bg-red-500 text-white rounded-full w-6 h-6 flex items-center justify-center text-xs hover:bg-red-600"
                                                    onclick="removeImage(event, 'vertical')"
                                                    title="Remove Image">×</button>
                                            </div>
                                            <div class="mt-2 space-y-1">
                                                <div class="text-sm text-gray-600 dark:text-gray-400"
                                                    id="file-name-vertical"></div>
                                                <div class="flex justify-center gap-2 text-xs">
                                                    <span id="file-size-vertical"
                                                        class="bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-300 px-2 py-1 rounded"></span>
                                                    <span id="dimensions-vertical"
                                                        class="bg-green-100 dark:bg-green-900 text-green-700 dark:text-green-300 px-2 py-1 rounded"></span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <input type="file" name="vertical_image" id="vertical-image" class="hidden"
                                        accept="image/*" onchange="handleFileSelect(event, 'vertical')">
                                    <div id="error-message-vertical"
                                        class="text-sm text-red-600 dark:text-red-400 mt-2 p-2 bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded hidden">
                                    </div>
                                </div>

                                @error('horizontal_image')
                                    <span class="text-xs text-red-600 dark:text-red-400 block mb-2">{{ $message }}</span>
                                @enderror
                                @error('vertical_image')
                                    <span class="text-xs text-red-600 dark:text-red-400 block mb-2">{{ $message }}</span>
                                @enderror
                            </div>

                            <!-- Ad Body -->
                            <div class="mt-4 p-3">
                                <label class="block text-sm">
                                    <span
                                        class="text-gray-700 dark:text-gray-400">{{ __('frontend-labels.sponsor_ads.ad_body') }}</span>
                                    <textarea
                                        class="block w-full mt-1 text-sm dark:text-gray-300 dark:border-gray-600 dark:bg-gray-700 form-textarea focus:border-purple-400 focus:outline-none focus:shadow-outline-purple dark:focus:shadow-outline-gray rounded"
                                        rows="7" placeholder="{{ __('frontend-labels.sponsor_ads.form_body') }}" name="body">{{ old('body') }}</textarea>
                                    @error('body')
                                        <span class="text-xs text-red-600 dark:text-red-400">
                                            {{ $message }}
                                        </span>
                                    @enderror
                                </label>
                            </div>

                            <!-- Image URL -->
                            <label class="block text-sm mt-4 p-3">
                                <span
                                    class="text-gray-700 dark:text-gray-400">{{ __('frontend-labels.sponsor_ads.image_url') }}</span>
                                <input
                                    class="block w-full mt-1 text-sm dark:border-gray-600 dark:bg-gray-700 focus:border-purple-400 focus:outline-none focus:shadow-outline-purple dark:text-gray-300 dark:focus:shadow-outline-gray form-input rounded"
                                    placeholder="{{ __('frontend-labels.sponsor_ads.image_url') }}" name="imageUrl"
                                    value="{{ old('imageURL') }}" />
                                <p class="mt-1 text-sm text-gray-500 dark:text-gray-300">
                                    {{ __('frontend-labels.sponsor_ads.image_url_hint') }}
                                </p>
                                @error('imageUrl')
                                    <span class="text-xs text-red-600 dark:text-red-400">
                                        {{ $message }}
                                    </span>
                                @enderror
                            </label>

                            <!-- Image Alt -->
                            <label class="block text-sm mt-4 p-3">
                                <span
                                    class="text-gray-700 dark:text-gray-400">{{ __('frontend-labels.sponsor_ads.image_alt') }}</span>
                                <input
                                    class="block w-full mt-1 text-sm dark:border-gray-600 dark:bg-gray-700 focus:border-purple-400 focus:outline-none focus:shadow-outline-purple dark:text-gray-300 dark:focus:shadow-outline-gray form-input rounded"
                                    placeholder="{{ __('frontend-labels.sponsor_ads.image_alt') }}" name="imageAlt"
                                    value="{{ old('imageAlt') }}" />
                                <p class="mt-1 text-sm text-gray-500 dark:text-gray-300">
                                    {{ __('frontend-labels.sponsor_ads.image_alt_hint') }}
                                </p>
                                @error('imageAlt')
                                    <span class="text-xs text-red-600 dark:text-red-400">
                                        {{ $message }}
                                    </span>
                                @enderror
                            </label>
                        </div>
                    </div>

                    <!-- Placements Section -->
                    <div class="mt-6 space-y-6 p-3">
                        <!-- App Ads Placement -->
                        <div>
                            <h3 class="text-xl font-semibold text-gray-800 dark:text-gray-200 mb-4">
                                {{ __('frontend-labels.sponsor_ads.app_ads_placement') }}</h3>
                            @if (isset($customAdsSettings['category_news_page_placement_status']) &&
                                    $customAdsSettings['category_news_page_placement_status'] == '1')
                                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                                    <!-- Splash Screen -->
                                    <div
                                        class="bg-white dark:bg-gray-800 rounded shadow p-4 mt-2 mt-2 mt-2 mt-2 mt-2 mt-2 mt-2">
                                        <label class="inline-flex items-center">
                                            <input type="checkbox" name="app_ads_placement[]"
                                                value="app_category_news_page"
                                                class="form-checkbox h-5 w-5 text-purple-600 rounded border-gray-300  placement-checkbox"
                                                data-price="{{ $customAdsSettings['category_news_page_price'] }}"
                                                onchange="calculateTotal()">
                                            <div class="ml-3">
                                                <span
                                                    class="block text-gray-800 dark:text-gray-200 font-medium">{{ __('frontend-labels.sponsor_ads.splash_screen') }}</span>
                                                <span
                                                    class="text-green-500 text-sm">({{ $customAdsSettings['currency_symbol'] }}{{ $customAdsSettings['category_news_page_price'] }}{{ __('frontend-labels.sponsor_ads.per_day') }})
                                                </span>
                                            </div>
                                        </label>
                                    </div>
                            @endif

                            @if (isset($customAdsSettings['topics_page_placement_status']) &&
                                    $customAdsSettings['topics_page_placement_status'] == '1')
                                <!-- Topics Page -->
                                <div class="bg-white dark:bg-gray-800 rounded shadow p-4 mt-2 mt-2 mt-2 mt-2 mt-2 mt-2">
                                    <label class="inline-flex items-center">
                                        <input type="checkbox" name="app_ads_placement[]" value="topics_page"
                                            class="form-checkbox h-5 w-5 text-purple-600 rounded border-gray-300  placement-checkbox"
                                            data-price="{{ $customAdsSettings['topics_page_price'] }}"
                                            onchange="calculateTotal()">
                                        <div class="ml-3">
                                            <span
                                                class="block text-gray-800 dark:text-gray-200 font-medium">{{ __('frontend-labels.sponsor_ads.topics_page') }}</span>
                                            <span
                                                class="text-green-500 text-sm">({{ $customAdsSettings['currency_symbol'] }}{{ $customAdsSettings['topics_page_price'] }}{{ __('frontend-labels.sponsor_ads.per_day') }})
                                            </span>
                                        </div>
                                    </label>
                                </div>
                            @endif

                            @if (isset($customAdsSettings['after_weather_section_placement_status']) &&
                                    $customAdsSettings['after_weather_section_placement_status'] == '1')
                                <!-- After Weather Section -->
                                <div class="bg-white dark:bg-gray-800 rounded shadow p-4 mt-2 mt-2 mt-2 mt-2 mt-2 mt-2">
                                    <label class="inline-flex items-center">
                                        <input type="checkbox" name="app_ads_placement[]" value="after_weather_card"
                                            class="form-checkbox h-5 w-5 text-purple-600 rounded border-gray-300  placement-checkbox"
                                            data-price="{{ $customAdsSettings['after_weather_section_price'] }}"
                                            onchange="calculateTotal()">
                                        <div class="ml-3">
                                            <span
                                                class="block text-gray-800 dark:text-gray-200 font-medium">{{ __('frontend-labels.sponsor_ads.under_weather_card') }}
                                            </span>
                                            <span
                                                class="text-green-500 text-sm">({{ $customAdsSettings['currency_symbol'] }}{{ $customAdsSettings['after_weather_section_price'] }}{{ __('frontend-labels.sponsor_ads.per_day') }})
                                            </span>
                                        </div>
                                    </label>
                                </div>
                            @endif

                            @if (isset($customAdsSettings['above_recommendations_section_placement_status']) &&
                                    $customAdsSettings['above_recommendations_section_placement_status'] == '1')
                                <!-- Above Recommendations -->
                                <div class="bg-white dark:bg-gray-800 rounded shadow p-4 mt-2 mt-2 mt-2 mt-2 mt-2 mt-2">
                                    <label class="inline-flex items-center">
                                        <input type="checkbox" name="app_ads_placement[]" value="above_recommendations"
                                            class="form-checkbox h-5 w-5 text-purple-600 rounded border-gray-300  placement-checkbox"
                                            data-price="{{ $customAdsSettings['above_recommendations_section_price'] }}"
                                            onchange="calculateTotal()">
                                        <div class="ml-3">
                                            <span
                                                class="block text-gray-800 dark:text-gray-200 font-medium">{{ __('frontend-labels.sponsor_ads.above_recommendations') }}
                                            </span>
                                            <span
                                                class="text-green-500 text-sm">({{ $customAdsSettings['currency_symbol'] }}{{ $customAdsSettings['above_recommendations_section_price'] }}{{ __('frontend-labels.sponsor_ads.per_day') }})
                                            </span>
                                        </div>
                                    </label>
                                </div>
                            @endif

                            @if (isset($customAdsSettings['all_channels_placement_status']) &&
                                    $customAdsSettings['all_channels_placement_status'] == '1')
                                <!-- Banner Slider -->
                                <div class="bg-white dark:bg-gray-800 rounded shadow p-4 mt-2 mt-2 mt-2 mt-2 mt-2 mt-2">
                                    <label class="inline-flex items-center">
                                        <input type="checkbox" name="app_ads_placement[]" value="all_channels"
                                            class="form-checkbox h-5 w-5 text-purple-600 rounded border-gray-300  placement-checkbox"
                                            data-price="{{ $customAdsSettings['all_channels_price'] }}"
                                            onchange="calculateTotal()">
                                        <div class="ml-3">
                                            <span
                                                class="block text-gray-800 dark:text-gray-200 font-medium">{{ __('frontend-labels.sponsor_ads.all_channels') }}</span>
                                            <span
                                                class="text-green-500 text-sm">({{ $customAdsSettings['currency_symbol'] }}{{ $customAdsSettings['all_channels_price'] }}{{ __('frontend-labels.sponsor_ads.per_day') }})
                                            </span>
                                        </div>
                                    </label>
                                </div>
                            @endif

                            @if (isset($customAdsSettings['splash_screen_page_placement_status']) &&
                                    $customAdsSettings['splash_screen_page_placement_status'] == '1')
                                <!-- Post Detail Page -->
                                <div class="bg-white dark:bg-gray-800 rounded shadow p-4 mt-2 mt-2 mt-2 mt-2 mt-2 mt-2">
                                    <label class="inline-flex items-center">
                                        <input type="checkbox" name="app_ads_placement[]" value="splash_screen "
                                            class="form-checkbox h-5 w-5 text-purple-600 rounded border-gray-300  placement-checkbox"
                                            data-price="{{ $customAdsSettings['splash_screen_page_price'] }}"
                                            onchange="calculateTotal()">
                                        <div class="ml-3">
                                            <span
                                                class="block text-gray-800 dark:text-gray-200 font-medium">{{ __('frontend-labels.sponsor_ads.search_floating_page') }}</span>
                                            <span
                                                class="text-green-500 text-sm">({{ $customAdsSettings['currency_symbol'] }}{{ $customAdsSettings['splash_screen_page_price'] }}{{ __('frontend-labels.sponsor_ads.per_day') }})
                                            </span>
                                        </div>
                                    </label>
                                </div>
                            @endif

                            @if (isset($customAdsSettings['app_banner_slider_placement_status']) &&
                                    $customAdsSettings['app_banner_slider_placement_status'] == '1')
                                <!-- Banner Slider -->
                                <div class="bg-white dark:bg-gray-800 rounded shadow p-4 mt-2 mt-2 mt-2 mt-2 mt-2 mt-2">
                                    <label class="inline-flex items-center">
                                        <input type="checkbox" name="app_ads_placement[]" value="app_banner_slider"
                                            class="form-checkbox h-5 w-5 text-purple-600 rounded border-gray-300  placement-checkbox"
                                            data-price="{{ $customAdsSettings['app_banner_slider_price'] }}"
                                            onchange="calculateTotal()">
                                        <div class="ml-3">
                                            <span
                                                class="block text-gray-800 dark:text-gray-200 font-medium">{{ __('frontend-labels.sponsor_ads.banner_slider') }}</span>
                                            <span
                                                class="text-green-500 text-sm">({{ $customAdsSettings['currency_symbol'] }}{{ $customAdsSettings['app_banner_slider_price'] }}{{ __('frontend-labels.sponsor_ads.per_day') }})
                                            </span>
                                        </div>
                                    </label>
                                </div>
                            @endif

                            @if (isset($customAdsSettings['channels_page_floating_placement_status']) &&
                                    $customAdsSettings['channels_page_floating_placement_status'] == '1')
                                <!-- Channels Page Floating -->
                                <div class="bg-white dark:bg-gray-800 rounded shadow p-4 mt-2 mt-2 mt-2 mt-2 mt-2 mt-2">
                                    <label class="inline-flex items-center">
                                        <input type="checkbox" name="app_ads_placement[]" value="channels_floating"
                                            class="form-checkbox h-5 w-5 text-purple-600 rounded border-gray-300  placement-checkbox"
                                            data-price="{{ $customAdsSettings['channels_page_floating_price'] }}"
                                            onchange="calculateTotal()">
                                        <div class="ml-3">
                                            <span
                                                class="block text-gray-800 dark:text-gray-200 font-medium">{{ __('frontend-labels.sponsor_ads.channels_page_floating') }}</span>
                                            <span
                                                class="text-green-500 text-sm">({{ $customAdsSettings['currency_symbol'] }}{{ $customAdsSettings['channels_page_floating_price'] }}{{ __('frontend-labels.sponsor_ads.per_day') }})
                                            </span>
                                        </div>
                                    </label>
                                </div>
                            @endif

                            @if (isset($customAdsSettings['discover_page_floating_placement_status']) &&
                                    $customAdsSettings['discover_page_floating_placement_status'] == '1')
                                <!-- Discover Page Floating -->
                                <div class="bg-white dark:bg-gray-800 rounded shadow p-4 mt-2 mt-2 mt-2 mt-2 mt-2 mt-2">
                                    <label class="inline-flex items-center">
                                        <input type="checkbox" name="app_ads_placement[]" value="discover_floating"
                                            class="form-checkbox h-5 w-5 text-purple-600 rounded border-gray-300  placement-checkbox"
                                            data-price="{{ $customAdsSettings['discover_page_floating_price'] }}"
                                            onchange="calculateTotal()">
                                        <div class="ml-3">
                                            <span
                                                class="block text-gray-800 dark:text-gray-200 font-medium">{{ __('frontend-labels.sponsor_ads.discover_page_floating') }}</span>
                                            <span
                                                class="text-green-500 text-sm">({{ $customAdsSettings['currency_symbol'] }}{{ $customAdsSettings['discover_page_floating_price'] }}{{ __('frontend-labels.sponsor_ads.per_day') }})
                                            </span>
                                        </div>
                                    </label>
                                </div>
                            @endif

                            @if (isset($customAdsSettings['video_page_floating_placement_status']) &&
                                    $customAdsSettings['video_page_floating_placement_status'] == '1')
                                <!-- Video Page Floating -->
                                <div class="bg-white dark:bg-gray-800 rounded shadow p-4 mt-2 mt-2 mt-2 mt-2 mt-2 mt-2">
                                    <label class="inline-flex items-center">
                                        <input type="checkbox" name="app_ads_placement[]" value="video_floating"
                                            class="form-checkbox h-5 w-5 text-purple-600 rounded border-gray-300  placement-checkbox"
                                            data-price="{{ $customAdsSettings['video_page_floating_price'] }}"
                                            onchange="calculateTotal()">
                                        <div class="ml-3">
                                            <span
                                                class="block text-gray-800 dark:text-gray-200 font-medium">{{ __('frontend-labels.sponsor_ads.video_page_floating') }}</span>
                                            <span
                                                class="text-green-500 text-sm">({{ $customAdsSettings['currency_symbol'] }}{{ $customAdsSettings['video_page_floating_price'] }}{{ __('frontend-labels.sponsor_ads.per_day') }})
                                            </span>
                                        </div>
                                    </label>
                                </div>
                            @endif

                            @if (isset($customAdsSettings['after_read_more_placement_status']) &&
                                    $customAdsSettings['after_read_more_placement_status'] == '1')
                                <!-- After Read More -->
                                <div class="bg-white dark:bg-gray-800 rounded shadow p-4 mt-2 mt-2 mt-2 mt-2 mt-2 mt-2">
                                    <label class="inline-flex items-center">
                                        <input type="checkbox" name="app_ads_placement[]" value="after_read_more"
                                            class="form-checkbox h-5 w-5 text-purple-600 rounded border-gray-300  placement-checkbox"
                                            data-price="{{ $customAdsSettings['after_read_more_price'] }}"
                                            onchange="calculateTotal()">
                                        <div class="ml-3">
                                            <span
                                                class="block text-gray-800 dark:text-gray-200 font-medium">{{ __('frontend-labels.sponsor_ads.after_read_more') }}</span>
                                            <span
                                                class="text-green-500 text-sm">({{ $customAdsSettings['currency_symbol'] }}{{ $customAdsSettings['after_read_more_price'] }}{{ __('frontend-labels.sponsor_ads.per_day') }})
                                            </span>
                                        </div>
                                    </label>
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- Web Ads Placement -->
                    <div class="mt-8">
                        <h3 class="text-xl font-semibold text-gray-800 dark:text-gray-200 mb-4">
                            {{ __('frontend-labels.sponsor_ads.web_ads_placement') }}</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                            @if (isset($customAdsSettings['header_placement_status']) && $customAdsSettings['header_placement_status'] == '1')
                                <!-- Header -->
                                <div class="bg-white dark:bg-gray-800 rounded shadow p-4 mt-2 mt-2 mt-2 mt-2 mt-2 mt-2">
                                    <label class="inline-flex items-center">
                                        <input type="checkbox" name="web_ads_placement[]" value="header"
                                            class="form-checkbox h-5 w-5 text-purple-600 rounded border-gray-300  placement-checkbox"
                                            data-price="{{ $customAdsSettings['header_price'] }}"
                                            onchange="calculateTotal()">
                                        <div class="ml-3">
                                            <span
                                                class="block text-gray-800 dark:text-gray-200 font-medium">{{ __('frontend-labels.sponsor_ads.header') }}</span>
                                            <span
                                                class="text-green-500 text-sm">({{ $customAdsSettings['currency_symbol'] }}{{ $customAdsSettings['header_price'] }}{{ __('frontend-labels.sponsor_ads.per_day') }})
                                            </span>
                                        </div>
                                    </label>
                                </div>
                            @endif

                            @if (isset($customAdsSettings['footer_placement_status']) && $customAdsSettings['footer_placement_status'] == '1')
                                <!-- Footer -->
                                <div class="bg-white dark:bg-gray-800 rounded shadow p-4 mt-2 mt-2 mt-2 mt-2 mt-2 mt-2">
                                    <label class="inline-flex items-center">
                                        <input type="checkbox" name="web_ads_placement[]" value="footer"
                                            class="form-checkbox h-5 w-5 text-purple-600 rounded border-gray-300  placement-checkbox"
                                            data-price="{{ $customAdsSettings['footer_price'] }}"
                                            onchange="calculateTotal()">
                                        <div class="ml-3">
                                            <span
                                                class="block text-gray-800 dark:text-gray-200 font-medium">{{ __('frontend-labels.sponsor_ads.footer') }}</span>
                                            <span
                                                class="text-green-500 text-sm">({{ $customAdsSettings['currency_symbol'] }}{{ $customAdsSettings['footer_price'] }}{{ __('frontend-labels.sponsor_ads.per_day') }})
                                            </span>
                                        </div>
                                    </label>
                                </div>
                            @endif

                            @if (isset($customAdsSettings['left_sidebar_placement_status']) &&
                                    $customAdsSettings['left_sidebar_placement_status'] == '1')
                                <!-- Left Sidebar -->
                                <div class="bg-white dark:bg-gray-800 rounded shadow p-4 mt-2 mt-2 mt-2 mt-2 mt-2 mt-2">
                                    <label class="inline-flex items-center">
                                        <input type="checkbox" name="web_ads_placement[]" value="left_sidebar"
                                            class="form-checkbox h-5 w-5 text-purple-600 rounded border-gray-300  placement-checkbox"
                                            data-price="{{ $customAdsSettings['left_sidebar_price'] }}"
                                            onchange="calculateTotal()">
                                        <div class="ml-3">
                                            <span
                                                class="block text-gray-800 dark:text-gray-200 font-medium">{{ __('frontend-labels.sponsor_ads.left_sidebar') }}</span>
                                            <span
                                                class="text-green-500 text-sm">({{ $customAdsSettings['currency_symbol'] }}{{ $customAdsSettings['left_sidebar_price'] }}{{ __('frontend-labels.sponsor_ads.per_day') }})
                                            </span>
                                        </div>
                                    </label>
                                </div>
                            @endif

                            @if (isset($customAdsSettings['right_sidebar_placement_status']) &&
                                    $customAdsSettings['right_sidebar_placement_status'] == '1')
                                <!-- Right Sidebar -->
                                <div class="bg-white dark:bg-gray-800 rounded shadow p-4 mt-2 mt-2 mt-2 mt-2 mt-2 mt-2">
                                    <label class="inline-flex items-center">
                                        <input type="checkbox" name="web_ads_placement[]" value="right_sidebar"
                                            class="form-checkbox h-5 w-5 text-purple-600 rounded border-gray-300  placement-checkbox"
                                            data-price="{{ $customAdsSettings['right_sidebar_price'] }}"
                                            onchange="calculateTotal()">
                                        <div class="ml-3">
                                            <span
                                                class="block text-gray-800 dark:text-gray-200 font-medium">{{ __('frontend-labels.sponsor_ads.right_sidebar') }}</span>
                                            <span
                                                class="text-green-500 text-sm">({{ $customAdsSettings['currency_symbol'] }}{{ $customAdsSettings['right_sidebar_price'] }}{{ __('frontend-labels.sponsor_ads.per_day') }})
                                            </span>
                                        </div>
                                    </label>
                                </div>
                            @endif

                            @if (isset($customAdsSettings['banner_slider_placement_status']) &&
                                    $customAdsSettings['banner_slider_placement_status'] == '1')
                                <!-- Banner Slider -->
                                <div class="bg-white dark:bg-gray-800 rounded shadow p-4 mt-2 mt-2 mt-2 mt-2 mt-2 mt-2">
                                    <label class="inline-flex items-center">
                                        <input type="checkbox" name="web_ads_placement[]" value="banner_slider"
                                            class="form-checkbox h-5 w-5 text-purple-600 rounded border-gray-300  placement-checkbox"
                                            data-price="{{ $customAdsSettings['banner_slider_price'] }}"
                                            onchange="calculateTotal()">
                                        <div class="ml-3">
                                            <span class="block text-gray-800 dark:text-gray-200 font-medium">Banner
                                                Slider</span>
                                            <span
                                                class="text-green-500 text-sm">({{ $customAdsSettings['currency_symbol'] }}{{ $customAdsSettings['banner_slider_price'] }}{{ __('frontend-labels.sponsor_ads.per_day') }})
                                            </span>
                                        </div>
                                    </label>
                                </div>
                            @endif

                            @if (isset($customAdsSettings['post_detail_page_placement_status']) &&
                                    $customAdsSettings['post_detail_page_placement_status'] == '1')
                                <!-- Post Detail Page -->
                                <div class="bg-white dark:bg-gray-800 rounded shadow p-4 mt-2 mt-2 mt-2 mt-2 mt-2 mt-2">
                                    <label class="inline-flex items-center">
                                        <input type="checkbox" name="web_ads_placement[]" value="post_detail"
                                            class="form-checkbox h-5 w-5 text-purple-600 rounded border-gray-300  placement-checkbox"
                                            data-price="{{ $customAdsSettings['post_detail_page_price'] }}"
                                            onchange="calculateTotal()">
                                        <div class="ml-3">
                                            <span
                                                class="block text-gray-800 dark:text-gray-200 font-medium">{{ __('frontend-labels.sponsor_ads.post_detail_page') }}
                                            </span>
                                            <span
                                                class="text-green-500 text-sm">({{ $customAdsSettings['currency_symbol'] }}{{ $customAdsSettings['post_detail_page_price'] }}{{ __('frontend-labels.sponsor_ads.per_day') }})
                                            </span>
                                        </div>
                                    </label>
                                </div>
                            @endif

                            @if (isset($customAdsSettings['latest_placement_status']) && $customAdsSettings['latest_placement_status'] == '1')
                                <!-- Latest -->
                                <div class="bg-white dark:bg-gray-800 rounded shadow p-4 mt-2 mt-2 mt-2 mt-2 mt-2 mt-2">
                                    <label class="inline-flex items-center">
                                        <input type="checkbox" name="web_ads_placement[]" value="latest"
                                            class="form-checkbox h-5 w-5 text-purple-600 rounded border-gray-300  placement-checkbox"
                                            data-price="{{ $customAdsSettings['latest_price'] }}"
                                            onchange="calculateTotal()">
                                        <div class="ml-3">
                                            <span
                                                class="block text-gray-800 dark:text-gray-200 font-medium">{{ __('frontend-labels.sponsor_ads.latest') }}</span>
                                            <span
                                                class="text-green-500 text-sm">({{ $customAdsSettings['currency_symbol'] }}{{ $customAdsSettings['latest_price'] }}{{ __('frontend-labels.sponsor_ads.per_day') }})
                                            </span>
                                        </div>
                                    </label>
                                </div>
                            @endif

                            @if (isset($customAdsSettings['popular_placement_status']) && $customAdsSettings['popular_placement_status'] == '1')
                                <!-- Popular -->
                                <div class="bg-white dark:bg-gray-800 rounded shadow p-4 mt-2 mt-2 mt-2 mt-2 mt-2 mt-2">
                                    <label class="inline-flex items-center">
                                        <input type="checkbox" name="web_ads_placement[]" value="popular"
                                            class="form-checkbox h-5 w-5 text-purple-600 rounded border-gray-300  placement-checkbox"
                                            data-price="{{ $customAdsSettings['popular_price'] }}"
                                            onchange="calculateTotal()">
                                        <div class="ml-3">
                                            <span
                                                class="block text-gray-800 dark:text-gray-200 font-medium">{{ __('frontend-labels.sponsor_ads.popular') }}</span>
                                            <span
                                                class="text-green-500 text-sm">({{ $customAdsSettings['currency_symbol'] }}{{ $customAdsSettings['popular_price'] }}{{ __('frontend-labels.sponsor_ads.per_day') }})
                                            </span>
                                        </div>
                                    </label>
                                </div>
                            @endif

                            @if (isset($customAdsSettings['posts_placement_status']) && $customAdsSettings['posts_placement_status'] == '1')
                                <!-- Posts -->
                                <div class="bg-white dark:bg-gray-800 rounded shadow p-4 mt-2 mt-2 mt-2 mt-2 mt-2 mt-2">
                                    <label class="inline-flex items-center">
                                        <input type="checkbox" name="web_ads_placement[]" value="posts"
                                            class="form-checkbox h-5 w-5 text-purple-600 rounded border-gray-300  placement-checkbox"
                                            data-price="{{ $customAdsSettings['posts_price'] }}"
                                            onchange="calculateTotal()">
                                        <div class="ml-3">
                                            <span
                                                class="block text-gray-800 dark:text-gray-200 font-medium">{{ __('frontend-labels.sponsor_ads.posts') }}</span>
                                            <span
                                                class="text-green-500 text-sm">({{ $customAdsSettings['currency_symbol'] }}{{ $customAdsSettings['posts_price'] }}{{ __('frontend-labels.sponsor_ads.per_day') }})
                                            </span>
                                        </div>
                                    </label>
                                </div>
                            @endif

                            @if (isset($customAdsSettings['topic_posts_placement_status']) &&
                                    $customAdsSettings['topic_posts_placement_status'] == '1')
                                <!-- Topic Posts -->
                                <div class="bg-white dark:bg-gray-800 rounded shadow p-4 mt-2 mt-2 mt-2 mt-2 mt-2 mt-2">
                                    <label class="inline-flex items-center">
                                        <input type="checkbox" name="web_ads_placement[]" value="topic_posts"
                                            class="form-checkbox h-5 w-5 text-purple-600 rounded border-gray-300  placement-checkbox"
                                            data-price="{{ $customAdsSettings['topic_posts_price'] }}"
                                            onchange="calculateTotal()">
                                        <div class="ml-3">
                                            <span
                                                class="block text-gray-800 dark:text-gray-200 font-medium">{{ __('frontend-labels.sponsor_ads.topic_posts') }}</span>
                                            <span
                                                class="text-green-500 text-sm">({{ $customAdsSettings['currency_symbol'] }}{{ $customAdsSettings['topic_posts_price'] }}{{ __('frontend-labels.sponsor_ads.per_day') }})
                                            </span>
                                        </div>
                                    </label>
                                </div>
                            @endif

                            @if (isset($customAdsSettings['videos_placement_status']) && $customAdsSettings['videos_placement_status'] == '1')
                                <!-- Videos -->
                                <div class="bg-white dark:bg-gray-800 rounded shadow p-4 mt-2 mt-2 mt-2 mt-2 mt-2 mt-2">
                                    <label class="inline-flex items-center">
                                        <input type="checkbox" name="web_ads_placement[]" value="videos"
                                            class="form-checkbox h-5 w-5 text-purple-600 rounded border-gray-300  placement-checkbox"
                                            data-price="{{ $customAdsSettings['videos_price'] }}"
                                            onchange="calculateTotal()">
                                        <div class="ml-3">
                                            <span
                                                class="block text-gray-800 dark:text-gray-200 font-medium">{{ __('frontend-labels.sponsor_ads.videos') }}</span>
                                            <span
                                                class="text-green-500 text-sm">({{ $customAdsSettings['currency_symbol'] }}{{ $customAdsSettings['videos_price'] }}{{ __('frontend-labels.sponsor_ads.per_day') }})
                                            </span>
                                        </div>
                                    </label>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
                <!-- Schedule Section -->
                <div class="mt-6 border-t pt-4 p-3">
                    <h3 class="text-lg font-medium text-gray-700 dark:text-gray-200 mb-4">
                        {{ __('frontend-labels.sponsor_ads.ad_schedule') }}</h3>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <label class="block text-sm p-1">
                            <span
                                class="text-gray-700 dark:text-gray-400">{{ __('frontend-labels.sponsor_ads.start_date') }}</span>
                            <input
                                class="block w-full mt-1 text-sm dark:border-gray-600 dark:bg-gray-700 focus:border-purple-400 focus:outline-none focus:shadow-outline-purple dark:text-gray-300 dark:focus:shadow-outline-gray form-input rounded"
                                type="date" name="start_date" id="start_date" value="{{ old('start_date') }}" />
                            @error('start_date')
                                <span class="text-xs text-red-600 dark:text-red-400">
                                    {{ $message }}
                                </span>
                            @enderror
                        </label>

                        <label class="block text-sm p-1">
                            <span
                                class="text-gray-700 dark:text-gray-400">{{ __('frontend-labels.sponsor_ads.end_date') }}</span>
                            <input
                                class="block w-full mt-1 text-sm dark:border-gray-600 dark:bg-gray-700 focus:border-purple-400 focus:outline-none focus:shadow-outline-purple dark:text-gray-300 dark:focus:shadow-outline-gray form-input rounded"
                                type="date" name="end_date" id="end_date" value="{{ old('end_date') }}" />
                            @error('end_date')
                                <span class="text-xs text-red-600 dark:text-red-400">
                                    {{ $message }}
                                </span>
                            @enderror
                        </label>
                    </div>
                </div>

                <!-- Price Summary Section -->
                <div class="mt-6 border-t pt-4 p-3" id="price-summary" style="display: none;">
                    <h3 class="text-lg font-medium text-gray-700 dark:text-gray-200 mb-4">
                        {{ __('frontend-labels.sponsor_ads.price_summary') }}</h3>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <!-- Selected Placements -->
                        <div>
                            <span
                                class="text-gray-700 dark:text-gray-400 text-sm">{{ __('frontend-labels.sponsor_ads.selected_placements') }}</span>
                            <div id="selected-placements" class="text-sm mt-1 text-black dark:text-white"></div>
                        </div>

                        <!-- Duration -->
                        <div>
                            <span
                                class="text-gray-700 dark:text-gray-400 text-sm">{{ __('frontend-labels.sponsor_ads.duration') }}:</span>
                            <div id="duration-display" class="text-sm mt-1 text-black dark:text-white">
                                {{ __('frontend-labels.sponsor_ads.not_selected') }}
                            </div>
                        </div>

                        <!-- Daily Rate -->
                        <div>
                            <span
                                class="text-gray-700 dark:text-gray-400 text-sm">{{ __('frontend-labels.sponsor_ads.daily_rate') }}:</span>
                            <div id="daily-rate" class="text-sm mt-1 text-black dark:text-white">
                                {{ __('frontend-labels.sponsor_ads.total_amount') }}</div>
                        </div>

                        <!-- Total Amount -->
                        <div>
                            <span
                                class="text-gray-700 dark:text-gray-400 text-sm">{{ __('frontend-labels.sponsor_ads.total_amount') }}:</span>
                            <div id="total-amount" class="text-sm mt-1 text-black dark:text-white">
                                {{ __('frontend-labels.sponsor_ads.total_amount') }}</div>
                        </div>
                    </div>

                    <!-- Hidden Fields for Submission -->
                    <input type="hidden" name="total_price" id="total_price" value="0">
                    <input type="hidden" name="daily_price" id="daily_price" value="0">
                    <input type="hidden" name="total_days" id="total_days" value="0">
                </div>

                <!-- Contact Information Section -->
                <div class="mt-6 border-t pt-4 p-3">
                    <h3 class="text-lg font-medium text-gray-700 dark:text-gray-200 mb-4">
                        {{ __('frontend-labels.sponsor_ads.contact_information') }}</h3>

                    <label class="block text-sm mt-4">
                        <span
                            class="text-gray-700 dark:text-gray-400">{{ __('frontend-labels.sponsor_ads.contact_name') }}</span>
                        <input
                            class="block w-full mt-1 text-sm dark:border-gray-600 dark:bg-gray-700 focus:border-purple-400 focus:outline-none focus:shadow-outline-purple dark:text-gray-300 dark:focus:shadow-outline-gray form-input rounded"
                            placeholder="{{ __('frontend-labels.sponsor_ads.contact_person_name') }}"
                            name="contact_name" id="contact_name" value="{{ old('contact_name') }}" />
                        @error('contact_name')
                            <span class="text-xs text-red-600 dark:text-red-400">
                                {{ $message }}
                            </span>
                        @enderror
                    </label>

                    <label class="block text-sm mt-4">
                        <span
                            class="text-gray-700 dark:text-gray-400">{{ __('frontend-labels.sponsor_ads.mobile_number') }}</span>
                        <input
                            class="block w-full mt-1 text-sm dark:border-gray-600 dark:bg-gray-700 focus:border-purple-400 focus:outline-none focus:shadow-outline-purple dark:text-gray-300 dark:focus:shadow-outline-gray form-input rounded"
                            placeholder="{{ __('frontend-labels.sponsor_ads.mobile_number') }}" name="mobile_number"
                            type="number" value="{{ old('mobile_number') }}" />
                        @error('mobile_number')
                            <span class="text-xs text-red-600 dark:text-red-400">
                                {{ $message }}
                            </span>
                        @enderror
                    </label>

                    <label class="block text-sm mt-4">
                        <span
                            class="text-gray-700 dark:text-gray-400">{{ __('frontend-labels.sponsor_ads.contact_email') }}</span>
                        <input
                            class="block w-full mt-1 text-sm dark:border-gray-600 dark:bg-gray-700 focus:border-purple-400 focus:outline-none focus:shadow-outline-purple dark:text-gray-300 dark:focus:shadow-outline-gray form-input rounded"
                            placeholder="{{ __('frontend-labels.sponsor_ads.contact_email') }}" name="contact_email"
                            id="contact_email" type="email" value="{{ old('contact_email') }}" />
                        @error('contact_email')
                            <span class="text-xs text-red-600 dark:text-red-400">
                                {{ $message }}
                            </span>
                        @enderror
                    </label>
                </div>

                <!-- Submit Button -->
                <div class="my-3">
                    <button type="submit" id="submit-btn"
                        class="inline-flex items-center rounded-md bg-purple-600 border border-transparent  px-3 py-2 text-sm font-medium leading-4 text-white shadow-sm  focus:outline-none ">
                        {{ __('frontend-labels.sponsor_ads.create_advertisement') }}
                    </button>
                </div>
            </form>
            <input type="hidden" name="form_type" value="create" />
        @else
            @if (is_object($smartAdsDetail) &&
                    $smartAdsDetail->ad_publish_status === 'pending' &&
                    $smartAdsDetail->payment_status === 'pending')
                <div class="lottie_css_div1 flex flex-col items-center justify-center text-center space-y-4">
                    <div class="lottie_css">
                        <dotlottie-player src="{{ asset('front_end/classic/images/place-holser/emailsent.json') }}"
                            background="transparent" speed="1" loop autoplay>
                        </dotlottie-player>
                    </div>
                    <h2
                        class="text-base sm:text-lg md:text-xl font-semibold text-gray-800 transition-colors duration-150 hover:text-gray-800 dark:hover:text-gray-200 dark:text-gray-100 max-w-2xl">
                        {{ __('frontend-labels.sponsor_ads.request_submitted_on') }}{{ $createdAtFormatted }}.
                        {{ __('frontend-labels.sponsor_ads.status_update_message') }}
                    </h2>
                </div>
            @elseif(is_object($smartAdsDetail) &&
                    $smartAdsDetail->ad_publish_status === 'approved' &&
                    $smartAdsDetail->payment_status === 'pending')
                <div class="lottie_css_div1 flex flex-col items-center justify-center text-center space-y-4">
                    <div class="lottie_css">
                        <dotlottie-player src="{{ asset('front_end/classic/images/place-holser/ad_approval.json') }}"
                            background="transparent" speed="1" loop autoplay>
                        </dotlottie-player>
                    </div>
                    <h2
                        class="text-base sm:text-lg md:text-xl font-semibold text-gray-800 transition-colors duration-150 hover:text-gray-800 dark:hover:text-gray-200 dark:text-gray-100 max-w-2xl">
                        {{ __('frontend-labels.sponsor_ads.request_approved') }}
                        {{-- {{ __('frontend-labels.sponsor_ads.approval_instructions') }} --}}
                    </h2>
                </div>
            @elseif(is_object($smartAdsDetail) &&
                    $smartAdsDetail->ad_publish_status === 'approved' &&
                    $smartAdsDetail->payment_status === 'success' &&
                    \Carbon\Carbon::parse($smartAdsDetail->end_date)->isFuture())
                <div class="lottie_css_div1  flex flex-col items-center justify-center text-center space-y-4">
                    <div class="lottie_css">
                        <dotlottie-player src="{{ asset('front_end/classic/images/place-holser/payments_ads.json') }}"
                            background="transparent" speed="1" loop autoplay>
                        </dotlottie-player>

                    </div>
                    <h2
                        class="text-base sm:text-lg md:text-xl font-semibold text-gray-800 transition-colors duration-150 hover:text-gray-800 dark:hover:text-gray-200 dark:text-gray-100 max-w-2xl">
                        {{ __('frontend-labels.sponsor_ads.payment_success') }} <br>
                        {{ __('frontend-labels.sponsor_ads.ad_live_period') }}
                        {{ \Carbon\Carbon::parse($smartAdsDetail->start_date)->format('d M Y') }}
                        {{ __('frontend-labels.sponsor_ads.to') }}
                        {{ \Carbon\Carbon::parse($smartAdsDetail->end_date)->format('d M Y') }}.
                        {{ __('frontend-labels.sponsor_ads.confirmation_email_message') }}
                    </h2>
                </div>
            @endif
        @endif
    </div>
@endsection

@section('scripts')
    <script src="https://unpkg.com/@dotlottie/player-component@latest/dist/dotlottie-player.js"></script>
@endsection
