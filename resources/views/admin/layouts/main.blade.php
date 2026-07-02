{{-- TODO change title and description --}}
<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover" />
    <meta http-equiv="X-UA-Compatible" content="ie=edge" />
    <title>@yield('title') | {{ $app_name ?? config('app.name') }}</title>
    <meta name="msapplication-TileColor" content="#0054a6" />
    <meta name="theme-color" content="#0054a6" />
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent" />
    <meta name="apple-mobile-web-app-capable" content="yes" />
    <meta name="mobile-web-app-capable" content="yes" />
    <meta name="HandheldFriendly" content="True" />
    <meta name="MobileOptimized" content="320" />
    <link rel="icon" href="{{ $favicon ?? asset('assets/images/logo/favicon.png') }}" type="image/x-icon" />
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <meta name="description" content="Desc" />
    <meta name="canonical" content="https://jsp.io/demo/layout-combo.html">
    <meta name="twitter:image:src" content="https://jsp.io/demo/static/og.png">
    <meta name="twitter:site" content="@jsp_ui">
    <meta name="twitter:card" content="summary">
    <meta name="twitter:title"
        content="jsp: Premium and Open Source dashboard template with responsive and high quality UI.">
    <meta name="twitter:description"
        content="jsp comes with tons of well-designed components and features. Start your adventure with jsp and make your dashboard great again. For free!">
    <meta property="og:image" content="https://jsp.io/demo/static/og.png">
    <meta property="og:image:width" content="1280">
    <meta property="og:image:height" content="640">
    <meta property="og:site_name" content="@yield('title') || {{ config('app.name') }}">
    <meta property="og:type" content="object">
    <meta property="og:title" content="title">
    <meta property="og:description" content="Desc">
    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />
    <!-- CSS files -->
    @include('admin.layouts.include')
    @yield('css')
</head>

<body data-bs-theme="dark" class="layout-fluid">
    <script src="{{ asset('assets/dist/js/demo-theme.min.js') }}"></script>
    <div class="page">
        @include('admin.layouts.sidebar')
        <!-- Sidebar -->
        @include('admin.layouts.topbar')

        <div class="page-wrapper">
            <div class="container-xl">
                <div class="page-header d-print-none" id="page_header">
                    @yield('page-title')
                </div>
                <div class="page-body">
                    @yield('content')
                </div>
            </div>
            @include('admin.layouts.footer')
        </div>
    </div>

    @include('admin.layouts.footer_script')
    @yield('js')
    @yield('script')
</body>

</html>
