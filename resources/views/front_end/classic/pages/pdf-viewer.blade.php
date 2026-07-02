<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="{{ $favicon ?? asset('assets/images/logo/favicon.png') }}" type="image/x-icon" />
    <meta name="description"
        content="News5 a clean, modern and pixel-perfect multipurpose blogging HTML5 website template.">
    <meta name="theme-color" content="#2757fd">
    <title>{{ $title . ' | ' . $appName }}</title>
    <link rel="stylesheet" href="{{ $flipbookAssets['whiteBookCss'] }}?v=<?= time() ?>">
    <link rel="stylesheet" href="{{ $flipbookAssets['shortWhiteBookCss'] }}?v=<?= time() ?>">
    <link rel="stylesheet" href="{{ $flipbookAssets['shortBlackBookCss'] }}?v=<?= time() ?>">
    <link rel="stylesheet" href="{{ $flipbookAssets['blackBookCss'] }}?v=<?= time() ?>">
    <link rel="stylesheet" href="{{ $flipbookAssets['fontAwesomeCss'] }}?v=<?= time() ?>">
    <style>
        :root {
            --enews-bg-image: url('{{ asset('storage/' . $e_newspaper->background_image) }}');
        }
    </style>
    <link rel="stylesheet" href="{{ asset('front_end/classic/css/epaper-css/epaper.css') }}?v=<?= time() ?>"
        as="style">
</head>

<body>
    <div id="wrapper" 
         data-page="pdf-viewer"
         data-daily-limit-value="{{ $freeTrialLimit }}" 
         data-is-daily-eligible="{{ $isDailyLimitEligible ? '1' : '0' }}"
         data-subscription-limit="{{ $subscriptionLimitReached ? '1' : '0' }}"
         data-has-subscription="{{ (auth()->user() && auth()->user()->subscription) ? '1' : '0' }}"
         data-redirect-url="{{ $e_newspaper->type === 'paper' ? route('e-newspaper.index') : route('e-magazine.index') }}"
         data-content-type="epaper">
    <div id="container"></div>
    <script>
        const flipbookAssets = @json($flipbookAssets);
        const pdfUrl = @json($pdfUrl);

        window.PDFJS_LOCALE = {
            pdfJsWorker: flipbookAssets.pdfWorker,
            pdfJsCMapUrl: flipbookAssets.pdfCMapUrl
        };
    </script>

    <!-- Load all dependencies -->
    <script src="{{ $flipbookAssets['jquery'] }}?v=<?= time() ?>"></script>
    <script src="{{ $flipbookAssets['three'] }}?v=<?= time() ?>"></script>
    <script src="{{ $flipbookAssets['pdf'] }}?v=<?= time() ?>"></script>
    <script src="{{ $flipbookAssets['html2canvas'] }}?v=<?= time() ?>"></script>
    <script src="{{ $flipbookAssets['flipBook'] }}?v=<?= time() ?>"></script>
    <script src="{{ $flipbookAssets['defaultView'] }}?v=<?= time() ?>"></script>

    <script src="{{ asset('front_end/classic/js/custom/enews-paper.js') }}?v=<?= time() ?>"></script>
    
    <!-- Load Bootstrap and Custom JS for limit tracking -->
    <script src="{{ asset('front_end/classic/js/libs/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ asset('front_end/classic/js/custom/custom.js') }}?v=<?= time() ?>"></script>
</body>

</html>
