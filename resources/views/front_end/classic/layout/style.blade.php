 <!-- preload head styles -->
 <link rel="preload" href="{{ asset('front_end/' . $theme . '/css/unicons.min.css') }}" as="style">
 <link rel="preload" href="{{ asset('front_end/' . $theme . '/css/swiper-bundle.min.css') }}" as="style">

 <!-- preload footer scripts -->
 @if(app()->isProduction())
  <link rel="preload" href="{{ asset('front_end/' . $theme . '/js/theme-bundle.min.js') }}" as="script">
 @else
  <link rel="preload" href="{{ asset('front_end/' . $theme . '/js/libs/jquery.min.js') }}" as="script">
  <link rel="preload" href="{{ asset('front_end/' . $theme . '/js/libs/scrollmagic.min.js') }}" as="script">
  <link rel="preload" href="{{ versioned_asset('front_end/' . $theme . '/js/libs/swiper-bundle.min.js') }}" as="script">
  <link rel="preload" href="{{ asset('front_end/' . $theme . '/js/libs/anime.min.js') }}" as="script">
  <link rel="preload" href="{{ asset('front_end/' . $theme . '/js/helpers/data-attr-helper.js') }}" as="script">
  <link rel="preload" href="{{ asset('front_end/' . $theme . '/js/helpers/swiper-helper.js') }}" as="script">
  <link rel="preload" href="{{ asset('front_end/' . $theme . '/js/helpers/anime-helper.js') }}" as="script">
  <link rel="preload" href="{{ asset('front_end/' . $theme . '/js/helpers/anime-helper-defined-timelines.js') }}" as="script">
  <link rel="preload" href="{{ asset('front_end/' . $theme . '/js/uikit-components-bs.js') }}" as="script">
  <link rel="preload" href="{{ asset('front_end/' . $theme . '/js/app.js') }}" as="script">
 @endif

 <!-- app head for bootstrap core -->
 <script src="{{ versioned_asset('front_end/' . $theme . '/js/app-head-bs.js') }}"></script>

 <!-- include uni-core components -->
 <link rel="stylesheet" href="{{ asset('front_end/' . $theme . '/js/uni-core/css/uni-core.min.css') }}">

 <!-- include styles -->
 <link rel="stylesheet" href="{{ asset('front_end/' . $theme . '/css/unicons.min.css') }}">
 <link rel="stylesheet" href="{{ asset('front_end/' . $theme . '/css/prettify.min.css') }}">
 <link rel="stylesheet" href="{{ asset('front_end/' . $theme . '/css/swiper-bundle.min.css') }}">

 <!-- include main style -->
 <link rel="stylesheet" href="{{ versioned_asset('front_end/' . $theme . '/css/theme/demo-seven.min.css') }}">

 {{-- Toaster style --}}
 <link rel="stylesheet" href="{{ asset('front_end/' . $theme . '/izitoast/dist/css/iziToast.min.css') }}">

 <!-- include scripts -->
 <script src="{{ asset('front_end/' . $theme . '/js/uni-core/js/uni-core-bundle.min.js') }}"></script>

 <!-- Custom Style -->
 @if(app()->isProduction())
  <link rel="stylesheet" href="{{ versioned_asset('front_end/' . $theme . '/css/custom.min.css') }}" as="style">
 @else
  <link rel="stylesheet" href="{{ versioned_asset('front_end/' . $theme . '/css/custom.css') }}" as="style">
 @endif

 <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css" as="style">

 <!-- Bootstrap Icons -->
 <link rel="stylesheet" href="{{ versioned_asset('front_end/' . $theme . '/css/sweetalert2.min.css') }}" as="style">

 <!-- Bootstrap Icons -->
 <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-icons/1.10.0/font/bootstrap-icons.min.css">
