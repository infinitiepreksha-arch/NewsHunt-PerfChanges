<!DOCTYPE html>
<html :class="{ 'dark': dark }" x-data="data()" lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta charset="UTF-8">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? '' }} | {{ $webTitle->value ?? '' }}</title>
    <link rel="icon" href="{{ $favicon ?? asset('assets/images/logo/favicon.png') }}" type="image/x-icon" />
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap"
        rel="stylesheet" />
    <link href="{{ asset('assets/css/app.css') }}?v=<?= time() ?>" rel="stylesheet">
    <link href="{{ asset('assets/css/prism.css') }}?v=<?= time() ?>" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('front_end/classic/css/custom.css') }}?v=<?= time() ?>">
    @yield('styles')
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <script src="{{ asset('assets/js/custom/banner-manager.js') }}?v=<?= time() ?>"></script>
    <script defer src="{{ asset('front_end/classic/js/custom/custom-ads.js') }}?v=<?= time() ?>"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.9.3/Chart.min.js" defer></script>
    <script src="{{ asset('assets/js/custom/smart-banner.min.js') }}?v=<?= time() ?>"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/izitoast/dist/css/iziToast.min.css">
    <script src="https://cdn.jsdelivr.net/npm/izitoast/dist/js/iziToast.min.js"></script>
    @livewireStyles
</head>

<body>
    <div class="flex h-screen bg-gray-50 dark:bg-gray-900" :class="{ 'overflow-hidden': isSideMenuOpen }">
        <!-- Desktop sidebar -->
        @include('front_end.layouts.partials.desktop-sidebar')
        <!-- Mobile sidebar -->
        <!-- Backdrop -->
        @include('front_end.layouts.partials.mobile-sidebar')
        <div class="flex flex-col flex-1 w-full">
            @include('front_end.layouts.partials.header')
            <main class="h-full overflow-y-auto">
                @yield('content')
            </main>
        </div>
    </div>
    @yield('scripts')
    @livewireScripts
    @include('front_end.layouts.partials.footer')
</body>

</html>
