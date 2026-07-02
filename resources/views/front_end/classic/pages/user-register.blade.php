<!DOCTYPE html>
<html lang="zxx">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="{{ $favicon ?? asset('assets/images/logo/favicon.png') }}" type="image/x-icon" />
    <meta name="description"
        content="News5 a clean, modern and pixel-perfect multipurpose blogging HTML5 website template.">
    <meta name="theme-color" content="#2757fd">
    <title>{{ $title . ' | ' . $appName }}</title>
    <!-- Open Graph Tags -->
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

    <!-- Twitter Card Tags -->
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="News5">
    <meta name="twitter:description"
        content="Full-featured, professional-looking news, editorial and magazine website template.">
    <meta name="twitter:image" content="https://unistudio.co/html/news5/assets/images/common/seo-image.jpg">

    <link rel="canonical" href="https://unistudio.co/html/news5/">

    @include('front_end.' . $theme . '.layout.style')
    <style>
        :root {
            --color-primary: {{ $web_theme_primary_colour ?? '#e62323' }};
            --font-family-primary: '{{ $web_font ?? 'Poppins' }}', sans-serif;
        }
    </style>
</head>

<body class="uni-body panel bg-white text-gray-900 dark:bg-black dark:text-gray-200 overflow-x-hidden">
    <!--  Bottom Actions Sticky -->
    <div class="backtotop-wrap position-fixed bottom-0 end-0 z-99 m-2 vstack">
        <div class="darkmode-trigger cstack w-40px h-40px rounded-circle text-none bg-gray-100 dark:bg-gray-700 dark:text-white"
            data-darkmode-toggle="">
            <label class="switch">
                <span class="sr-only"> {{ __('frontend-labels.register.dark_mode_toggle') }}</span>
                <input type="checkbox">
                <span class="slider fs-5"></span>
            </label>
        </div>
        <a class="btn btn-sm btn-news-hunt text-white w-40px h-40px rounded-circle" href="to_top" data-uc-backtotop>
            <i class="icon-2 unicon-chevron-up"></i>
        </a>
    </div>

    <!-- Wrapper start -->
    <div id="wrapper" class="wrap overflow-x-hidden">

        <!-- Section start -->
        <div id="sign-in" class="sign-in section panel overflow-hidden">
            <div class="section-outer panel">
                <div class="section-inner panel">
                    <div class="row child-cols-12 lg:child-cols-12 g-0" data-uc-grid>
                        <div>
                            <div class="panel vstack md:items-center justify-center h-screen overflow-hidden">
                                <div class="panel py-4 px-2">
                                    <div class="panel vstack gap-3 w-100 sm:w-350px mx-auto text-center"
                                        data-anime="targets: >*; translateY: [24, 0]; opacity: [0, 1]; easing: easeInOutExpo; duration: 750; delay: anime.stagger(100);">
                                        <h1 class="h4 sm:h3">{{ __('frontend-labels.register.create_account_title') }}
                                        </h1>
                                        <form id="registerForm" method="POST" action="{{ route('register') }}"
                                            class="vstack gap-2">
                                            @csrf
                                            <!-- Error Alerts Container -->
                                            <div id="errorContainer"></div>

                                            <!-- Name Input -->
                                            <input
                                                class="form-control form-control-sm h-40px w-full fs-6 bg-white dark:bg-opacity-0 dark:text-white dark:border-gray-300 dark:border-opacity-30"
                                                type="text" name="name"
                                                placeholder="{{ __('frontend-labels.register.placeholder_name') }}"
                                                value="{{ old('name') }}">
                                            <div class="hstack text-danger fw-bold fs-7 sm:fs-6 name-error" style="display: none;"></div>

                                            <!-- Email Input -->
                                            <input
                                                class="form-control form-control-sm h-40px w-full fs-6 bg-white dark:bg-opacity-0 dark:text-white dark:border-gray-300 dark:border-opacity-30"
                                                type="email" name="email"
                                                placeholder="{{ __('frontend-labels.register.placeholder_email') }}"
                                                value="{{ old('email') }}">
                                            <div class="hstack text-danger fw-bold fs-7 sm:fs-6 email-error" style="display: none;"></div>

                                            <!-- Password Input -->
                                            <input
                                                class="form-control form-control-sm h-40px w-full fs-6 bg-white dark:bg-opacity-0 dark:text-white dark:border-gray-300 dark:border-opacity-30"
                                                type="password" name="password"
                                                placeholder="{{ __('frontend-labels.register.placeholder_password') }}"
                                                autocomplete="new-password">
                                            <div class="hstack text-danger fw-bold fs-7 sm:fs-6 password-error" style="display: none;"></div>

                                            <!-- Password Confirmation Input -->
                                            <input
                                                class="form-control form-control-sm h-40px w-full fs-6 bg-white dark:bg-opacity-0 dark:text-white dark:border-gray-300 dark:border-opacity-30"
                                                type="password" name="password_confirmation"
                                                placeholder="{{ __('frontend-labels.register.placeholder_confirm_password') }}"
                                                autocomplete="new-password">
                                            <div class="hstack text-danger fw-bold fs-7 sm:fs-6 password_confirmation-error" style="display: none;">
                                            </div>

                                            <!-- Remember Me Checkbox -->
                                            <div class="hstack justify-between text-start">
                                                <div class="form-check text-start">
                                                    <input id="form_accept_terms"
                                                        class="form-check-input rounded bg-white dark:bg-opacity-0 dark:text-white dark:border-gray-300 dark:border-opacity-30"
                                                        type="checkbox" name="accept_terms">
                                                    <label for="form_accept_terms"
                                                        class="hstack justify-between form-check-label fs-6">
                                                        {{ __('frontend-labels.register.accept_terms_label') }} <a
                                                            href="{{ url('/terms-and-condition') }}"
                                                            class="uc-link ms-narrow">
                                                            {{ __('frontend-labels.register.terms_of_use') }}</a>.</label>
                                                </div>
                                            </div>
                                            <div class="hstack text-danger fw-bold fs-7 sm:fs-6 accept_terms-error" style="display: none;"></div>

                                            <!-- Submit Button -->
                                            <button class="btn btn-primary btn-sm mt-1" type="submit"
                                                id="registerBtn">
                                                {{ __('frontend-labels.register.register_button') }}
                                            </button>
                                        </form>
                                        <p> {{ __('frontend-labels.register.already_have_account') }} <a
                                                class="uc-link" href="{{ route('login') }}">
                                                {{ __('frontend-labels.register.login_link') }}</a></p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Section end -->
    </div>

    <!-- Wrapper end -->

    @include('front_end.' . $theme . '.layout.script')
    <script src="{{ asset('assets/js/login/auth-forms.js') }}"></script>
</body>

</html>
