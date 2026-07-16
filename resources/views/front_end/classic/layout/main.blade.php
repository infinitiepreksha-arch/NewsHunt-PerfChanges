<!DOCTYPE html>
<html lang="{{ $langCode ?? 'en' }}" dir="{{ $dir ?? 'ltr' }}">

<head>
    <script>
        document.documentElement.classList.add("preloader-active");
        document.addEventListener("DOMContentLoaded", function() {
            const loader = document.getElementById("page-preloader");
            if (loader) {
                loader.style.opacity = "0";
                loader.style.visibility = "hidden";
                setTimeout(function() {
                    loader.remove();
                }, 500);
            }
            document.documentElement.classList.remove("preloader-active");
            // Programmatic lazy loading for YouTube iframes and lazy images
            window.lazyLoadElements = function() {
                const lazyElements = document.querySelectorAll("iframe.lazy-iframe, img.lazy-img:not(#news-language-modal img, #web-language-modal img)");
                if ("IntersectionObserver" in window) {
                    const observer = new IntersectionObserver((entries, obs) => {
                        entries.forEach(entry => {
                            if (entry.isIntersecting) {
                                const el = entry.target;
                                
                                // Check if the element is currently hidden in the layout (e.g. inside closed modals/tabs)
                                const rect = el.getBoundingClientRect();
                                if (rect.width === 0 && rect.height === 0) {
                                    return;
                                }
                                
                                if (el.getAttribute("data-src")) {
                                    el.src = el.getAttribute("data-src");
                                }
                                el.classList.remove("lazy-iframe", "lazy-img");
                                obs.unobserve(el);
                            }
                        });
                    }, { rootMargin: "0px 0px 400px 0px" });
                    lazyElements.forEach(el => observer.observe(el));
                } else {
                    lazyElements.forEach(el => {
                        if (el.getAttribute("data-src")) {
                            el.src = el.getAttribute("data-src");
                        }
                    });
                }
            };
            window.lazyLoadElements();

            // Load lazy images inside language modals only when they are opened
            ['news-language-modal', 'web-language-modal'].forEach(function(modalId) {
                const modal = document.getElementById(modalId);
                if (modal) {
                    modal.addEventListener('beforeshow', function() {
                        modal.querySelectorAll('img.lazy-img').forEach(function(img) {
                            if (img.getAttribute('data-src')) {
                                img.src = img.getAttribute('data-src');
                                img.classList.remove('lazy-img');
                            }
                        });
                    });
                }
            });

            // Click handler to load YouTube iframes on-demand (Phase 5)
            document.addEventListener('click', function(e) {
                const placeholder = e.target.closest('.youtube-placeholder');
                if (placeholder) {
                    const videoUrl = placeholder.getAttribute('data-video-url');
                    const separator = videoUrl.includes('?') ? '&' : '?';
                    const autoplayUrl = videoUrl + separator + 'autoplay=1';
                    
                    const iframe = document.createElement('iframe');
                    iframe.setAttribute('width', '100%');
                    iframe.setAttribute('height', '100%');
                    iframe.setAttribute('src', autoplayUrl);
                    iframe.setAttribute('title', 'YouTube video player');
                    iframe.setAttribute('frameborder', '0');
                    iframe.setAttribute('allow', 'accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share');
                    iframe.setAttribute('allowfullscreen', 'true');
                    iframe.className = 'media-cover';
                    
                    const container = placeholder.parentElement;
                    container.innerHTML = '';
                    container.appendChild(iframe);
                }
            });
        });
    </script>
    <style>
        .uc-pageloader {
            position: fixed;
            top: 0; left: 0; bottom: 0; right: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            z-index: 999999;
            background-color: #ffffff;
            transition: opacity 0.5s ease, visibility 0.5s ease;
        }
        html.uc-dark .uc-pageloader {
            background-color: #131313;
        }
        .uc-pageloader .loading {
            display: inline-block;
            position: relative;
            width: 40px;
            height: 40px;
        }
        .uc-pageloader .loading div {
            box-sizing: border-box;
            display: block;
            position: absolute;
            width: 40px;
            height: 40px;
            margin: 0;
            border: 4px solid transparent;
            border-radius: 50%;
            animation: uc-loading 1s cubic-bezier(0.5, 0, 0.5, 1) infinite;
            border-top-color: {{ $web_theme_primary_colour ?? '#e62323' }};
        }
        .uc-pageloader .loading div:nth-child(1) { animation-delay: -0.1s; }
        .uc-pageloader .loading div:nth-child(2) { animation-delay: -0.2s; }
        .uc-pageloader .loading div:nth-child(3) { animation-delay: -0.3s; }
        @keyframes uc-loading {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
        html.preloader-active {
            overflow: hidden !important;
        }
    </style>
    <meta charset="UTF-8">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="preconnect" href="https://api.openweathermap.org">
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
    <div id="page-preloader" class="uc-pageloader">
        <div class="loading">
            <div></div><div></div><div></div><div></div>
        </div>
    </div>
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
