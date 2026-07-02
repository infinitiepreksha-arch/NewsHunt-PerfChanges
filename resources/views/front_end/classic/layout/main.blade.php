<!DOCTYPE html>
<html lang="{{ $langCode ?? 'en' }}" dir="{{ $dir ?? 'ltr' }}">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? '' }} | {{ $webTitle->value ?? '' }}</title>
    <link rel="icon" href="{{ $favicon ?? asset('assets/images/logo/favicon.png') }}" type="image/x-icon" />
    <meta name="description" content="{{ $meta_description->value ?? 'A clean, modern News providing website.' }}">
    <meta name="keywords"
        content="{{ $meta_keywords->value ?? 'news, website design, digital product, marketing, agency' }}">
    <link rel="canonical" href="{{ url()->current() }}">
    <meta name="theme-color" content="#2757fd">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <meta property="og:locale" content="en_US">
    <meta property="og:type" content="website">
    <meta property="og:title"
        content="{{ $post_title ?? ($seo_title->value ?? 'Stay Informed: Your Daily Source for Breaking News and In-Depth Analysis') }}">
    <meta property="og:description"
        content="{{ $description ?? ($meta_description->value ?? 'Explore the latest news and insights from around the world.') }}">
    <meta property="og:url" content="{{ url()->current() }}">
    <meta property="og:site_name" content="{{ $webTitle->value ?? '' }}">
    <meta property="og:image" content="{{ $favicon ?? '' }}">
    <meta property="og:image:width" content="1180">
    <meta property="og:image:height" content="600">
    <meta property="og:image:type" content="image/png">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <script src="https://unpkg.com/wavesurfer.js"></script>
    {{-- intl-tel-input CSS/JS --}}
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/18.5.3/css/intlTelInput.css" />
    <script src="https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/18.5.3/js/intlTelInput.min.js"></script>
    <!-- Twitter Card Tags -->
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:url" content="{{ url()->current() }}">
    <meta name="twitter:title"
        content="{{ $post_title ?? 'Stay Informed: Your Daily Source for Breaking News and In-Depth Analysis' }}">
    <meta name="twitter:description"
        content="{{ $description ?? 'Explore the latest news and insights from around the world.' }}">
    <meta name="twitter:image" content="{{ $image ?? '' }}">

    <link rel="canonical" href="{{ url()->current() }}">

    @if (!empty($web_font))
        <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family={{ str_replace(' ', '+', $web_font) }}:wght@300;400;500;600;700;800;900&display=swap">
    @endif

    @include('front_end.' . $theme . '.layout.style')
    @yield('style')

    <!-- Dynamic Theme Color Injection -->
    <style>
        :root {
            --color-primary: {{ $web_theme_primary_colour ?? '#e62323' }};
            --font-family-primary: "{{ $web_font ?? 'Poppins' }}", sans-serif;
            /* Ensure these derive from primary if the theme supports it */
            --font-text-family: var(--font-family-primary);
            --font-heading-family: var(--font-family-primary);
            --font-display-family: var(--font-family-primary);
            --heading-font-typeface: var(--font-family-primary);
        }

        ::selection {
            background: var(--color-primary);
            color: #fff;
              text-shadow: none
        }

        /* Force font family on third-party components like iziToast */
        .iziToast,
        .iziToast *,
        .iziToast-texts,
        .iziToast-message,
        .iziToast-title,
        .story-lower-third,
        .story-middle-third-content,
        .story-middle2-css,
        .next-story-title-css {
            font-family: var(--font-family-primary) !important;
        }
    </style>
</head>

<body class="uni-body panel bg-white text-gray-900 dark:bg-black dark:text-white text-opacity-50 overflow-x-hidden">
    @include('front_end.' . $theme . '.layout.header')
    <!-- Left Sidebar Ad -->
    <div id="left-sidebar-ad" class="sidebar-ad fixed-left d-none d-xl-block"></div>

    <!-- Right Sidebar Ad -->
    <div id="right-sidebar-ad" class="sidebar-ad fixed-right d-none d-xl-block"></div>

    <input type="hidden" id="popup-status" value="{{ $application_download_popup_on_web->value ?? 0 }}">
    <input type="hidden" id="sponsor-rotation-duration" value="{{ $sponsor_ad_rotation_time->value ?? 0 }}">

    <input type="hidden" id="web-theme-colour" value="{{ $web_theme_primary_colour ?? '#e62323' }}">
    <input type="hidden" id="free-trial-status" value="{{ $free_trial_status ?? 0 }}">
    <input type="hidden" id="free-trial-post-limit" value="{{ $free_trial_post_limit ?? 0 }}">
    <input type="hidden" id="free-trial-story-limit" value="{{ $free_trial_story_limit ?? 0 }}">
    <input type="hidden" id="free-trial-epaper-limit" value="{{ $free_trial_epaper_limit ?? 0 }}">
    <input type="hidden" id="current-page-type" value="{{ $currentPageType ?? '' }}">


    @if (!empty($application_download_popup_on_web) && $application_download_popup_on_web->value == 1)
        <div id="android-scheme" class="d-none">
            {{ $app_scheme->value ?? '' }}
        </div>
        <div id="ios-scheme" class="d-none">
            {{ $ios_shceme->value ?? '' }}
        </div>
        <div id="android-link" class="d-none">
            {{ $play_store_link->value ?? '' }}
        </div>
        <div id="ios-link" class="d-none">
            {{ $app_store_link->value ?? '' }}
        </div>
    @endif

    @yield('body')

    @include('front_end.' . $theme . '.layout.footer')

    @include('front_end.' . $theme . '.layout.script')

    @yield('script')

</body>
