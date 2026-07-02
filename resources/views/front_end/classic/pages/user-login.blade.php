<!DOCTYPE html>
<html lang="zxx">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description"
        content="News5 a clean, modern and pixel-perfect multipurpose blogging HTML5 website template.">
    <meta name="theme-color" content="#2757fd">
    <title>{{ $title . ' | ' . $appName }}</title>

    <meta property="og:locale" content="en_US">
    <meta property="og:type" content="website">
    <meta property="og:title" content="News5">
    <meta property="og:description"
        content="Full-featured, professional-looking news, editorial and magazine website template.">
    <meta property="og:url" content="https://unistudio.co/html/news5/">
    <meta property="og:site_name" content="News5">
    <meta property="og:image" content="https://unistudio.co/html/news5/assets/images/common/seo-image.jpg">
    <meta property="og:image:width" content="1180">
    <meta property="og:image:height" content="600">
    <meta property="og:image:type" content="image/png">

    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="News5">
    <meta name="twitter:description"
        content="Full-featured, professional-looking news, editorial and magazine website template.">
    <meta name="twitter:image" content="https://unistudio.co/html/news5/assets/images/common/seo-image.jpg">

    <link rel="canonical" href="https://unistudio.co/html/news5/">

    @include('front_end.classic.layout.style')
    
    <!-- CSRF Token Meta Tag -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <!-- Dynamic Theme Color Injection -->
    <style>
        :root {
            --color-primary: {{ $web_theme_primary_colour ?? '#e62323' }};
            --font-family-primary: '{{ $web_font ?? 'Poppins' }}', sans-serif;
        }
    </style>
</head>

<body class="uni-body panel bg-white text-gray-900 dark:bg-black dark:text-gray-200 overflow-x-hidden">
    <div class="backtotop-wrap position-fixed bottom-0 end-0 z-99 m-2 vstack">
        <div class="darkmode-trigger cstack w-40px h-40px rounded-circle text-none bg-gray-100 dark:bg-gray-700 dark:text-white"
            data-darkmode-toggle="">
            <label class="switch">
                <span class="sr-only">{{ __('frontend-labels.frontend_login.dark_mode_toggle') }}</span>
                <input type="checkbox">
                <span class="slider fs-5"></span>
            </label>
        </div>
        <a class="btn btn-sm btn-news-hunt text-white w-40px h-40px rounded-circle" href="to_top" data-uc-backtotop>
            <i class="icon-2 unicon-chevron-up"></i>
        </a>
    </div>
    <div id="wrapper" class="wrap overflow-x-hidden">
        <div id="sign-in" class="sign-in section panel overflow-hidden">
            <div class="section-outer panel">
                <div class="section-inner panel">
                    <div class="row child-cols-12 lg:child-cols-12 g-0" data-uc-grid>
                        <div>
                            <div class="panel vstack md:items-center justify-center h-screen overflow-hidden">
                                <div class="panel py-4 px-2">
                                    <div class="panel vstack gap-3 w-100 sm:w-350px mx-auto text-center"
                                        data-anime="targets: >*; translateY: [24, 0]; opacity: [0, 1]; easing: easeInOutExpo; duration: 750; delay: anime.stagger(100);">
                                        <h1 class="h4 sm:h3">{{ __('frontend-labels.frontend_login.user_login_title') }}
                                        </h1>
                                        <div class="panel h-24px">
                                            <hr
                                                class="position-absolute top-50 start-50 translate-middle hr m-0 w-100 dark:opacity-30">
                                        </div>

                                        <form id="loginForm" method="POST" action="{{ route('user.login') }}"
                                            class="vstack gap-2">
                                            @csrf
                                            <!-- Error Alerts Container -->
                                            <div id="errorContainer"></div>

                                            <!-- Email Input -->
                                            <input
                                                class="form-control form-control-sm h-40px w-full fs-6 bg-white dark:bg-opacity-0 dark:text-white dark:border-gray-300 dark:border-opacity-30"
                                                type="email" name="email"
                                                placeholder="{{ __('frontend-labels.frontend_login.email_placeholder') }}"
                                                value="{{ old('email') }}">
                                            <div class="hstack text-danger fw-bold fs-7 sm:fs-6 email-error"
                                                style="display: none;"></div>

                                            <!-- Password Input -->
                                            <input
                                                class="form-control form-control-sm h-40px w-full fs-6 bg-white dark:bg-opacity-0 dark:text-white dark:border-gray-300 dark:border-opacity-30"
                                                type="password" name="password"
                                                placeholder="{{ __('frontend-labels.frontend_login.password_placeholder') }}"
                                                autocomplete="current-password">
                                            <div class="hstack text-danger fw-bold fs-7 sm:fs-6 password-error"
                                                style="display: none;"></div>

                                            <!-- Remember Me Checkbox -->
                                            <div class="hstack justify-between text-start">
                                                <div class="form-check text-start">
                                                    <input id="form_remember_me"
                                                        class="form-check-input      bg-white dark:bg-opacity-0 dark:text-white dark:border-gray-300 dark:border-opacity-30"
                                                        type="checkbox" name="remember">
                                                    <label for="form_remember_me"
                                                        class="hstack justify-between form-check-label fs-6">{{ __('frontend-labels.frontend_login.remember_me') }}?</label>
                                                </div>
                                                <a href="{{ route('password.request') }}"
                                                    class="uc-link fs-6">{{ __('frontend-labels.frontend_login.forgot_password') }}</a>
                                            </div>

                                            <!-- Submit Button -->
                                            <button class="btn btn-primary btn-sm mt-1" type="submit" id="loginBtn">
                                                {{ __('frontend-labels.frontend_login.login_button') }}</button>
                                        </form>

                                        <div class="panel h-24px">
                                            <hr class="position-absolute top-50 start-50 translate-middle hr m-0 w-100">
                                            <span
                                                class="position-absolute top-50 start-50 translate-middle px-1 fs-7 text-uppercase">Or</span>
                                        </div>
                                        <div id="firebase-config" style="display:none"
                                            data-config="{{ base64_encode(json_encode($firebaseConfig ?? [])) }}">
                                        </div>

                                        <div class="hstack gap-2">
                                            <a id="google-login-btn"
                                                class="hstack items-center justify-center flex-1 gap-1 h-40px text-none rounded border border-gray-900
     dark:bg-gray-800 dark:border-white dark:border-opacity-15 border-opacity-10">
                                                <i class="icon icon-1 unicon-logo-google"></i>
                                                <span>{{ __('frontend-labels.auth.sign_in_google') }}</span>
                                            </a>
                                        </div>

                                        <div id="google-login-error"
                                            style="color:red; margin-top:0.5rem; display:none;"></div>

                                        <p> {{ __('frontend-labels.frontend_login.dont_have_account') }}<a
                                                class="uc-link"
                                                href="{{ route('register') }}">{{ __('frontend-labels.frontend_login.register_now') }}</a>
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @include('front_end.classic.layout.script')
    <script src="{{ asset('assets/js/login/auth-forms.js') }}"></script>
</body>

</html>
