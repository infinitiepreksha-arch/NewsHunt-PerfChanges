<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="shortcut icon" href="{{ $favicon ?? url('assets/images/logo/logo.png') }}" type="image/x-icon">
    <title>Login</title>
    <link href="{{ asset('assets/css/googleapis/googleapis.css') }}" rel="stylesheet">

    @include('admin.layouts.include')
    @yield('css')
</head>

<body>
    <script src="{{ asset('assets/dist/js/demo-theme.min.js') }}"></script>

    @yield('content')
    <script type="text/javascript" src="{{ asset('assets/js/jquery.min.js') }}"></script>

    <script type="text/javascript" src="{{ asset('public/assets/js/login/custom.js') }}"></script>
    <script type="text/javascript" src="{{ asset('assets/js/custom/forget-password.js') }}?v=<?= time() ?>"></script>

</body>

</html>
