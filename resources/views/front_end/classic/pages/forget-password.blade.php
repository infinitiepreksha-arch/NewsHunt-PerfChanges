<!DOCTYPE html>
<html lang="zxx">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ 'Reset Password | News Hunt' }}</title>
    <link rel="icon" href="{{ $favicon ?? asset('assets/images/logo/favicon.png') }}" type="image/x-icon" />
    <meta name="description"
        content="News5 a clean, modern and pixel-perfect multipurpose blogging HTML5 website template.">
    <meta name="theme-color" content="#2757fd">

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

    @include('front_end.classic.layout.style')
</head>

<body class="uni-body panel bg-white text-gray-900 dark:bg-black dark:text-gray-200 overflow-x-hidden">
    <!--  Bottom Actions Sticky -->
    <div class="backtotop-wrap position-fixed bottom-0 end-0 z-99 m-2 vstack">
        <div class="darkmode-trigger cstack w-40px h-40px rounded-circle text-none bg-gray-100 dark:bg-gray-700 dark:text-white"
            data-darkmode-toggle="">
            <label class="switch">
                <span class="sr-only">Dark mode toggle</span>
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
                                <div
                                    data-anime="targets: >*; translateY: [-24, 0]; opacity: [0, 1]; easing: easeInOutCubic; duration: 750; delay: anime.stagger(100);">
                                    <div class="uc-logo cstack mx-auto mb-6 lg:mb-8">
                                        <a href="{{url('home')}}">
                                            <img class="w-100px lg:w-128px text-dark dark:text-white hover:text-primary transition-color duration-150 d-none dark:d-block" src="{{ asset('front_end/classic/images/custom/LoginLight.png') }}" alt="Sign in">
                                            <img class="w-100px lg:w-128px text-dark dark:text-white hover:text-primary transition-color duration-150 d-block dark:d-none" src="{{ asset('front_end/classic/images/custom/LoginDark.png') }}" alt="Sign in">
                                        </a>
                                    </div>
                                </div>
                                <div class="panel py-4 px-2">
                                    <div class="panel vstack gap-3 w-100 sm:w-350px mx-auto text-center" data-anime="targets: >*; translateY: [24, 0]; opacity: [0, 1]; easing: easeInOutExpo; duration: 750; delay: anime.stagger(100);">
                                        <h1 class="h4 sm:h3">Reset Password</h1>
                                        <form action="{{ url('password/form') }}" method="POST" data-parsley-validate class="create-form">
                                            @csrf
                                            <input type="hidden" name="id" value="{{ $user['id'] }}"/>
                                            <div class="row mt-3">
                                                <div class="col-12">
                                                    <div class="card-body">
                                                        <!-- New Password Field -->
                                                        <div class="mb-3 mandatory">
                                                            <div class="input-group">
                                                                <input type="password" name="password" id="password" class="form-control form-control-sm form-control-solid" placeholder="{{ __('New Password') }}" data-parsley-minlength="8" data-parsley-uppercase="1" data-parsley-lowercase="1" data-parsley-number="1" data-parsley-special="1" data-parsley-required />
                                                                <span class="input-group-text toggle-password cursor-pointer" onclick="togglePassword('password')"><i class="bi bi-eye" id="togglePasswordIcon1"></i></span>
                                                            </div>
                                                            @error('password')
                                                                <div class="text-danger mt-1">{{ $message }}</div>
                                                            @enderror
                                                        </div>

                                                        <div class="mb-3 mandatory">
                                                            <div class="input-group">
                                                                <input type="password" id="password_confirmation" name="password_confirmation" class="form-control form-control-sm form-control-solid" placeholder="{{ __('Confirm Password') }}" data-parsley-equalto="#password" data-parsley-required />
                                                                <span class="input-group-text toggle-password cursor-pointer" onclick="togglePassword('password_confirmation')"><i class="bi bi-eye" id="togglePasswordIcon2"></i></span>
                                                            </div>
                                                            @error('password_confirmation')
                                                                <div class="text-danger mt-1">{{ $message }}</div>
                                                            @enderror
                                                        </div>
                                                        
                                                        <button class="btn btn-primary btn-sm mt-1" type="submit">Reset</button>
                                                    </div>
                                                </div>
                                            </div>
                                        </form>
                                        <p>Have no account yet? <a class="uc-link" href="{{route('register')}}">Register</a></p>
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

    @include('front_end.classic.layout.script')
</body>
</html>


