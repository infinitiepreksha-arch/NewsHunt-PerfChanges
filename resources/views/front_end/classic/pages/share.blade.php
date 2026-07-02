<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Shere | {{$webTitle->value ?? ''}}</title>
    <link rel="icon" href="{{ $favicon ?? asset('assets/images/logo/favicon.png') }}" type="image/x-icon" />
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
</head>
<body>
    <div class="content-wrapper">
    <input type="hidden" id="play_store_link" value="{{$play_store_link->value}}">
    <input type="hidden" id="app_store_link" value="{{$play_store_link->value}}">
    </div>
</body>
</html>
