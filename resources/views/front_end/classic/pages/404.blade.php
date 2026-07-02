<!DOCTYPE html>
<html lang="zxx">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description"
        content="News5 a clean, modern and pixel-perfect multipurpose blogging HTML5 website template.">
    <meta name="theme-color" content="#2757fd">
    {{-- <title>{{ $title . ' | ' . $appName }}</title> --}}

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
</head>

<body class="uni-body panel bg-white text-gray-900 dark:bg-black dark:text-gray-200 overflow-x-hidden">
    <div class="backtotop-wrap position-fixed bottom-0 end-0 z-99 m-2 vstack">
        <div class="darkmode-trigger cstack w-40px h-40px rounded-circle text-none bg-gray-100 dark:bg-gray-700 dark:text-white"
            data-darkmode-toggle="">
            <label class="switch">
                <span class="sr-only">{{ __('frontend-labels.login.dark_mode_toggle') }}</span>
                <input type="checkbox">
                <span class="slider fs-5"></span>
            </label>
        </div>
        <a class="btn btn-sm btn-news-hunt text-white w-40px h-40px rounded-circle" href="to_top" data-uc-backtotop>
            <i class="icon-2 unicon-chevron-up"></i>
        </a>
    </div>
    <div id="wrapper" class="wrap overflow-x-hidden">
        <div class="section py-6 lg:py-8 xl:py-10">
            <div class="container max-w-xl">
                <div class="panel vstack justify-center items-center gap-2 sm:gap-4 text-center">
                    <h2 class="display-5 sm:display-3 lg:display-2 xl:display-1 text-primary mt-5">404</h2>
                    <h1 class="h3 sm:h1 m-0">Page not found</h1>
                    <a href="index.html" class="animate-btn btn btn-md btn-primary text-none gap-0">
                        <span>Go back home</span>
                        <i class="icon icon-narrow unicon-arrow-left fw-bold"></i>
                    </a>
                </div>
            </div>
        </div>
    </div>

    @include('front_end.classic.layout.script')
</body>

</html>
