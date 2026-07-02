@isset($smartAd)
    <div class="smart-banner-temp border rounded" banner-slug="{{ $smartAd->slug }}">
        <div class="ad-badge text-black dark:text-white">SPONSER ADS</div>

        @if ($smartAd->adType == 'image')
            <a href="{{ $smartAd->imageUrl ? $smartAd->imageUrl : '#' }}" target="_blank" class="add">
                <img class="image-ads"
                    src="https://static.vecteezy.com/system/resources/previews/029/127/289/non_2x/modern-abstract-banner-design-template-with-geometric-shapes-applicable-for-banners-placards-posters-flyers-vector.jpg"
                    alt="{{ $smartAd->imageAlt }}" />
            </a>
        @endif
    </div>
@endisset
