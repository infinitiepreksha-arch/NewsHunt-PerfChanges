<!-- include jquery & bootstrap js -->
<script defer src="{{ asset('front_end/' . $theme . '/js/libs/jquery.min.js') }}?v=<?= time() ?>"></script>
{{-- <script defer src="{{ asset('front_end/' . $theme . '/js/libs/bootstrap.min.js') }}?v=<?= time() ?>"></script> --}}
<script defer src="{{ asset('front_end/' . $theme . '/js/libs/bootstrap.bundle.min.js') }}?v=<?= time() ?>"></script>

<!-- include scripts -->
<script defer src="{{ asset('front_end/' . $theme . '/js/libs/anime.min.js') }}"></script>
<script defer src="{{ asset('front_end/' . $theme . '/js/libs/swiper-bundle.min.js') }}?v=<?= time() ?>"></script>
<script defer src="{{ asset('front_end/' . $theme . '/js/libs/scrollmagic.min.js') }}"></script>
<script defer src="{{ asset('front_end/' . $theme . '/js/helpers/data-attr-helper.js') }}"></script>
<script defer src="{{ asset('front_end/' . $theme . '/js/helpers/swiper-helper.js') }}"></script>
<script defer src="{{ asset('front_end/' . $theme . '/js/helpers/anime-helper.js') }}"></script>
<script defer src="{{ asset('front_end/' . $theme . '/js/helpers/anime-helper-defined-timelines.js') }}"></script>
<script defer src="{{ asset('front_end/' . $theme . '/js/uikit-components-bs.js') }}"></script>

<!-- include app script -->
<script defer src="{{ asset('front_end/' . $theme . '/js/app.js') }}?v=<?= time() ?>"></script>
<script defer src="{{ asset('front_end/' . $theme . '/js/sweetalert2.js') }}?v=<?= time() ?>"></script>
<script defer src="{{ asset('front_end/' . $theme . '/js/front-custom.js') }}?v=<?= time() ?>"></script>

<script defer src="{{ asset('front_end/' . $theme . '/js/custom/weather-custom.js') }}?v=<?= time() ?>"></script>
<script defer src="{{ asset('front_end/' . $theme . '/js/custom/share.js') }}?v=<?= time() ?>"></script>
<script defer src="{{ asset('front_end/' . $theme . '/js/custom/custom.js') }}?v=<?= time() ?>"></script>
<script defer src="{{ asset('front_end/' . $theme . '/js/custom/custom-reactions.js') }}?v=<?= time() ?>"></script>
<script defer src="{{ asset('front_end/' . $theme . '/js/custom/contact-us.js') }}?v=<?= time() ?>"></script>
<script defer src="{{ asset('front_end/' . $theme . '/js/custom/search-news.js') }}?v=<?= time() ?>"></script>


{{-- custom script --}}
<script defer src="{{ asset('front_end/' . $theme . '/js/custom/custom-jquery.js') }}?v=<?= time() ?>"></script>

{{-- Toaster message --}}
<script defer src="{{ asset('front_end/' . $theme . '/izitoast/dist/js/iziToast.js') }}"></script>
<script defer src="{{ asset('front_end/' . $theme . '/izitoast/dist/js/iziToast.min.js') }}"></script>


<script defer src="{{ asset('front_end/' . $theme . '/js/sweeteralert/sweetalert2.all.min.js') }}"></script>
<script defer src="{{ asset('front_end/' . $theme . '/js/custom/my-account.js') }}?v=<?= time() ?>"></script>

<script
    src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/js/bootstrap-datepicker.min.js?v=<?= time() ?>">
</script>
<script src="https://unpkg.com/@lottiefiles/dotlottie-wc@0.6.2/dist/dotlottie-wc.js?v=<?= time() ?>" type="module">
</script>
<script src="https://unpkg.com/@dotlottie/player-component@latest/dist/dotlottie-player.js?v=<?= time() ?>"></script>
<script src="https://cdn.jsdelivr.net/npm/@dotlottie/wc/dist/dotlottie-wc.js?v=<?= time() ?>"></script>
<script>
    // Schema toggle via URL
    const queryString = window.location.search;
    const urlParams = new URLSearchParams(queryString);
    const getSchema = urlParams.get("schema");
    if (getSchema === "dark") {
        setDarkMode(1);
    } else if (getSchema === "light") {
        setDarkMode(0);
    }
</script>
