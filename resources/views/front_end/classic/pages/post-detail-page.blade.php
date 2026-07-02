@extends('front_end.' . $theme . '.layout.main')

@section('body')
    <div class="share-div"></div>
    @if ($subscriptionLimitReached)
        <!-- Bootstrap Subscription Limit Reached and Free Trial Modal -->
        <div id="subscriptionLimitFreeTrialModal" class="modal modal-blur fade p-5" tabindex="-1" role="dialog"
            aria-label="Daily free trial and subscription limit reached"
            aria-labelledby="subscriptionLimitFreeTrialModalLabel" aria-hidden="true"
            data-bs-keyboard="false">
            <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
                <div
                    class="modal-content bg-light text-dark dark:bg-gray-800 dark:text-white shadow-lg rounded-2 border-0 text-center">

                    <div class="modal-header border-0 justify-content-center p-4">

                    </div>
                    <div class="modal-body d-flex flex-column align-items-center justify-content-center">
                        <div class="display-4 mb-3">⏳</div>
                        <h3 class="modal-title fw-bold mb-3 " id="subscriptionLimitFreeTrialModalLabel">
                            {{ __('frontend-labels.limits.daily_trial_and_subscription_reached') }}</h3>
                        <p class="fs-5 mb-2">{{ __('frontend-labels.limits.unlock_access_message') }}</p>
                    </div>

                    <div class="modal-footer justify-content-center border-0 pt-0">
                        <a href="{{ url('membership') }}"
                            class="btn btn-primary btn-lg rounded-pill px-3 px-sm-4 fw-semibold shadow-sm mb-2 w-100 w-sm-auto text-center">
                            {{ __('frontend-labels.limits.buy_membership_plan') }}
                        </a>
                    </div>

                </div>
            </div>
        </div>
    @endif

    {{-- Always render dailyLimitModal if user is eligible, JS will show it --}}
    @if ($isDailyLimitEligible || $dailyLimitReached)
        <!-- Bootstrap Daily Limit Modal -->
        <div id="dailyLimitModal" class="modal modal-blur fade p-5" tabindex="-1" role="dialog"
            aria-label="Daily Limit Reached" aria-labelledby="dailyLimitModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
                <div
                    class="modal-content bg-light text-dark dark:bg-gray-800 dark:text-white shadow-lg rounded-2 border-0 text-center">

                    <div class="modal-header border-0 justify-content-center p-4">
                    </div>
                    <div class="modal-body d-flex flex-column align-items-center justify-content-center">
                        <div class="display-4 mb-3">⏳</div>
                        <h3 class="modal-title fw-bold mb-3 " id="dailyLimitModalLabel">
                            {{ __('frontend-labels.limits.daily_limit_reached') }}</h3>
                        <p class="fs-5 mb-2">{{ __('frontend-labels.limits.daily_limit_message') }}</p>
                        <p class="mb-0 text-muted">{{ __('frontend-labels.limits.unlock_access_message') }}</p>
                    </div>

                    <div class="modal-footer justify-content-center border-0 pt-0">
                        <a href="{{ url('membership') }}"
                            class="btn btn-primary btn-lg rounded-pill px-3 px-sm-4 fw-semibold shadow-sm mb-2 w-100 w-sm-auto text-center">
                            {{ __('frontend-labels.limits.buy_membership_plan') }}
                        </a>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <!-- Wrapper start -->
    <div id="wrapper" class="wrap overflow-hidden-x" 
         data-daily-limit-value="{{ $freeTrialLimit }}" 
         data-is-daily-eligible="{{ $isDailyLimitEligible ? '1' : '0' }}"
         data-content-type="post">
        <div class="breadcrumbs panel z-1 py-2 bg-gray-25 dark:bg-gray-100 dark:bg-opacity-5 dark:text-white">
            <div class="container max-w-xl">
                <ul class="breadcrumb nav-x justify-center gap-1 fs-7 sm:fs-6 m-0">
                    <li><a href="{{ url('home') }}" title="Home">{{ __('frontend-labels.home.title') }}</a></li>
                    <li><i class="unicon-chevron-right opacity-50"></i></li>
                    @if ($post->type === 'post')
                        <li><a href="{{ url('topics/' . $post->topic_slug) }}"
                                title="{{ $post->topic_name ?? '' }}">{{ $post->topic_name ?? '' }}</a></li>
                    @else
                        <li><a href="{{ url('topics/' . $post->topic_slug) }}"
                                title="{{ $post->channel_name ?? '' }}">{{ $post->channel_name ?? '' }}</a></li>
                    @endif

                    <li><i class="unicon-chevron-right opacity-50"></i></li>
                    <li><span class="opacity-50"
                            title="{{ $post_label->value ?? '' }}">{{ $post_label->value ?? '' }}</span></li>
                </ul>
            </div>
        </div>


        <article class="post type-post single-post py-4 lg:py-6 xl:py-9">
            <div class="container max-w-xl">
                <div class="post-header">
                    <div class="panel vstack gap-4 md:gap-6 xl:gap-5 text-center">
                        <div>
                            <h1 class="h4 sm:h2 lg:h1 xl:display-6">{{ $post->title ?? '' }}</h1>

                            <h4 class="row gap-1">
                                <div>
                                    <a href="{{ url('channels/' . $post->channel_slug) }}" class="text-none">
                                        <img src="{{ $post->channel_logo }}"
                                            alt="{{ url('channels/' . $post->channel_slug) }}" class="h-20px">
                                    </a>
                                    <a href="{{ url('channels/' . $post->channel_slug) }}" class="text-none">
                                        {{ $post->channel_name ?? '' }}</a>
                                </div>
                                <div title="{{ $post->publish_date_news }}">{{ $post->publish_date ?? $post->pubdate }}
                                </div>
                            </h4>

                            <ul class="post-share-icons nav-x gap-1 dark:text-white justify-center mt-2">
                                <li>
                                    <a class="btn btn-md p-0 border-gray-900 border-opacity-15 w-32px lg:w-48px h-32px lg:h-48px text-dark dark:text-white dark:border-white hover:bg-primary hover:border-primary hover:text-white rounded-circle"
                                        href="{{ $settings['instagram_link'] ?? '#' }}" aria-label="Instagram"><i
                                            class="unicon-logo-instagram icon-1"></i></a>
                                </li>
                                <li>
                                    <a class="btn btn-md p-0 border-gray-900 border-opacity-15 w-32px lg:w-48px h-32px lg:h-48px text-dark dark:text-white dark:border-white hover:bg-primary hover:border-primary hover:text-white rounded-circle"
                                        href="{{ $settings['x_link'] ?? '#' }}" aria-label="X"><i
                                            class="unicon-logo-x-filled icon-1"></i></a>
                                </li>
                                <li>
                                    <a class="btn btn-md p-0 border-gray-900 border-opacity-15 w-32px lg:w-48px h-32px lg:h-48px text-dark dark:text-white dark:border-white hover:bg-primary hover:border-primary hover:text-white rounded-circle"
                                        href="{{ $settings['facebook_link'] ?? '#' }}" aria-label="Facebook"><i
                                            class="unicon-logo-facebook icon-1"></i></a>
                                </li>
                                <li>
                                    <a class="btn btn-md p-0 border-gray-900 border-opacity-15 w-32px lg:w-48px h-32px lg:h-48px text-dark dark:text-white dark:border-white hover:bg-primary hover:border-primary hover:text-white rounded-circle"
                                        href="{{ $settings['linkedin_link'] ?? '#' }}" aria-label="LinkedIn"><i
                                            class="unicon-logo-linkedin icon-1"></i></a>
                                </li>
                                <li>
                                    <a class="btn btn-md p-0 border-gray-900 border-opacity-15 w-32px lg:w-48px h-32px lg:h-48px text-dark dark:text-white dark:border-white hover:bg-primary hover:border-primary hover:text-white rounded-circle"
                                        href="{{ $settings['pinterest_link'] ?? '#' }}" aria-label="Pinterest"><i
                                            class="unicon-logo-pinterest icon-1"></i></a>
                                </li>
                                <li>
                                    <a id="copyButton"
                                        data-message="{{ __('frontend-labels.post_detailpage.copy_link_success') }}"
                                        class="btn btn-md p-0 border-gray-900 border-opacity-15 w-32px lg:w-48px h-32px lg:h-48px text-dark dark:text-white dark:border-white hover:bg-primary hover:border-primary hover:text-white rounded-circle"
                                        aria-label="CopyPost" href="#">
                                        <i class="unicon-link icon-1"></i>
                                    </a>
                                </li>

                            </ul>
                        </div>

                        <div id="selective-blur-media" class="selective-blur {{ $dailyLimitReached ? 'blur-content' : '' }}" {{ $dailyLimitReached ? 'inert' : '' }}>
                            @if ($post->type === 'youtube')
                                <figure class="featured-image m-0">
                                    <figure
                                        class="featured-image m-0 ratio ratio-2x1 rounded uc-transition-toggle overflow-hidden bg-gray-25 dark:bg-gray-800">
                                        <iframe class="media-cover image uc-transition-scale-up uc-transition-opaque"
                                            width="1285" height="642" src="{{ $post->video }}" {{-- YouTube video embed URL --}}
                                            id="video_frame" frameborder="0" referrerpolicy="strict-origin-when-cross-origin"
                                            allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
                                            allowfullscreen>
                                        </iframe>
                                    </figure>
                                </figure>
                            @elseif ($post->type === 'audio')
                                <figure class="featured-image m-0">
                                    <figure
                                        class="featured-image m-0 ratio ratio-2x1 rounded uc-transition-toggle overflow-hidden bg-gray-25 dark:bg-gray-800">
                                        <a href="{{ url('posts/' . $post->slug) }}" class="position-cover">
                                            <img class="media-cover image uc-transition-scale-up uc-transition-opaque"
                                                src="{{ $image }}" alt="{{ $post->title ?? 'Post Image' }}"
                                                loading="lazy">
                                        </a>
                                    </figure>
                                </figure>
                                <div
                                    class="audio-controls mt-3 d-flex align-items-center gap-2 p-2 dark:bg-gray-800 rounded m-2 border">
                                    <button id="play-btn-{{ $post->id }}"
                                        class="play-button btn btn-outline-info audio_play_button_css rounded-circle d-flex align-items-center justify-content-center">
                                        <i id="play-icon-{{ $post->id }}" class="bi bi-play"
                                            style="font-size: 1.2rem;"></i>
                                        <i id="pause-icon-{{ $post->id }}" class="bi bi-pause"
                                            style="font-size: 1.2rem; display: none;"></i>
                                    </button>
                                    <div id="waveform-{{ $post->id }}" class="flex-grow-1" style="min-width: 0;"
                                        data-audio-url="{{ $post->audio }}"></div>
                                </div>
                            @elseif ($post->type != 'video')
                                <figure
                                    class="featured-image m-0 ratio ratio-2x1 rounded uc-transition-toggle overflow-hidden bg-gray-25 dark:bg-gray-800">

                                    <div class="swiper"
                                        data-uc-swiper="items: 1; active: 1; gap: 4; prev: .nav-prev; next: .nav-next; autoplay: 6000; parallax: true; fade: true; effect: fade; disable-class: d-none;">

                                        <div class="swiper-wrapper">
                                            @foreach ($postImages as $image)
                                                <div class="swiper-slide">
                                                    <article
                                                        class="post type-post panel uc-transition-toggle vstack gap-2 lg:gap-3 h-100 overflow-hidden uc-dark">
                                                        <div class="post-media panel overflow-hidden h-100">
                                                            <div
                                                                class="featured-image bg-gray-25 dark:bg-gray-800 h-100 d-none md:d-block">
                                                                <canvas class="h-100 w-100"></canvas>
                                                                <a href="{{ url('posts/' . $post->slug) }}"
                                                                    class="position-cover">
                                                                    <img class="media-cover image uc-transition-scale-up uc-transition-opaque"
                                                                        src="{{ $image }}"
                                                                        alt="{{ $post->title ?? 'Post Image' }}"
                                                                        loading="lazy">
                                                                </a>
                                                            </div>
                                                            <div
                                                                class="featured-image bg-gray-25 dark:bg-gray-800 ratio ratio-16x9 d-block md:d-none">
                                                                <a href="{{ url('posts/' . $post->slug) }}"
                                                                    class="position-cover">
                                                                    <img class="media-cover image uc-transition-scale-up uc-transition-opaque"
                                                                        src="{{ $image }}"
                                                                        alt="{{ $post->title ?? 'Post Image' }}"
                                                                        loading="lazy">
                                                                </a>
                                                            </div>
                                                        </div>
                                                        <div
                                                            class="position-cover bg-gradient-to-t from-black to-transparent opacity-90">
                                                        </div>
                                                    </article>
                                                </div>
                                            @endforeach
                                        </div>

                                        {{-- Navigation --}}
                                        <div
                                            class="swiper-nav nav-prev position-absolute top-50 start-0 translate-middle-y btn btn-alt-primary text-black rounded-circle p-0 mx-2 border-0 shadow-xs w-32px h-32px z-1 uc-hidden-hover">
                                            <i class="icon-1 unicon-chevron-left"></i>
                                        </div>
                                        <div
                                            class="swiper-nav nav-next position-absolute top-50 end-0 translate-middle-y btn btn-alt-primary text-black rounded-circle p-0 mx-2 border-0 shadow-xs w-32px h-32px z-1 uc-hidden-hover">
                                            <i class="icon-1 unicon-chevron-right"></i>
                                        </div>

                                    </div>
                                </figure>
                            @else
                                <div class="featured-image m-0">
                                    <div
                                        class="featured-image m-0 ratio ratio-2x1 rounded uc-transition-toggle overflow-hidden dark:bg-black light:bg-white">
                                        <div class="featured-video bg-gray-700">
                                            <video id="video-preview" class="media-cover video" controls preload="metadata"
                                                loop playsinline webkit-playsinline muted poster="{{ $post->video_thumb }}">
                                                <source
                                                    src="{{ $post->video }} ?? asset($defaultImage->value ?? 'public/front_end/classic/images/default/post-placeholder.jpg') }}"
                                                    type="video/mp4">
                                                <source
                                                    src="{{ $post->video }}?? asset($defaultImage->value ?? 'public/front_end/classic/images/default/post-placeholder.jpg') }}"
                                                    type="video/webm">
                                                <track src="descriptions_en.vtt" kind="descriptions" srclang="en"
                                                    label="English Descriptions">
                                            </video>
                                        </div>
                                    </div>
                                </div>
                            @endif
                        </div>
                    <div>
                    </div>
                    <div class="row">
                        <div class="d-flex justify-start gap-1">
                            <div>
                                <a id="open_reactores" class="text-none">
                                    @foreach ($getTopReactions as $getTopReaction)
                                        <b class="position-relative text-primary reaction-margin"
                                            id="emoji_loop_{{ $getTopReaction->count }}">
                                            <span class="fs-3 text-primary">{{ $getTopReaction->uuid }}</span>
                                        </b>
                                    @endforeach
                                    @if ($emoji && !$getTopReactions->contains('uuid', $emoji))
                                        <b class="position-relative text-primary reaction-margin"
                                            id="match_reaction_icons">
                                            <span class="fs-3 text-primary">{{ $emoji }}</span>
                                        </b>
                                    @else
                                        <b class="position-relative text-primary d-none reaction-margin"
                                            id="match_reaction_icons">
                                            <span class="fs-3 text-primary"></span>
                                        </b>
                                    @endif
                                </a>
                            </div>
                            <div>
                                @if ($emoji !== '')
                                    @if ($post->reaction == 1)
                                        <b id="emoji_count">{{ __('frontend-labels.post_detailpage.you') }}</b>
                                    @else
                                        <b id="emoji_count">{{ __('frontend-labels.post_detailpage.you') }} +
                                            {{ $post->reaction - 1 }}</b>
                                    @endif
                                @else
                                    <b id="emoji_count"> {{ $post->reaction == 0 ? '' : $post->reaction }}</b>
                                @endif

                            </div>
                        </div>
                        <div class="d-flex justify-between gap-1">
                            <div class="d-flex gap-1">
                                <h4><i class="bi bi-eye-fill"></i> {{ $post->view_count ?? '' }}
                                </h4>
                                <h4><i class="bi bi-bookmarks-fill"></i><span
                                        id="favorite_counts">{{ $post->favorite ?? '' }}</span>
                                </h4>

                                {{-- This part is for reactions --}}
                                <h4 class="cursor-pointer">

                                    @if ($emoji)
                                        <a id="reaction_open" class="text-none">
                                            <b class="fs-2 position-relative text-primary" id="reaction_icons"><span
                                                    class="reaction-uuid">{{ $emoji }}</span></b>
                                        </a>
                                    @else
                                        <a id="reaction_open" class="text-none">
                                            <b class="bi bi-hand-thumbs-up-fill fs-2 position-relative"
                                                id="reaction_icons"></b>
                                        </a>
                                    @endif
                                    @if (auth()->check())
                                        @if ($post->is_bookmark == 1)
                                            <a href="" id="bookmark-post" class="hover:text-primary">
                                                <i class="bi bi-bookmarks-fill fs-2"></i>
                                            </a>
                                        @else
                                            <a href="" id="bookmark-post" class="hover:text-primary">
                                                <i class="bi bi-bookmarks fs-2 justify-between"></i>
                                            </a>
                                        @endif
                                    @else
                                        <a href="#uc-account-modal" data-uc-toggle class="hover:text-primary">
                                            <i class="bi bi-bookmarks fs-2"></i>
                                        </a>
                                    @endif
                                    <div id="emoji-box"
                                        class="emoji-box mt-1 dark:bg-gray-100 dark:bg-opacity-5 text-primary gap-1 d-none">
                                        @if (auth()->check())
                                            @foreach ($reactions as $reaction)
                                                <span
                                                    onclick="reactToPost({{ $post->id }},'{{ $reaction->name }}','{{ $reaction->uuid }}','{{ $getTopReactions }}')"
                                                    class="emoji">{{ $reaction->uuid }}</span>
                                            @endforeach
                                        @else
                                            @foreach ($reactions as $reaction)
                                                <a href="#uc-account-modal" data-uc-toggle class="text-none">
                                                    <span>{{ $reaction->uuid }}</span>
                                                </a>
                                            @endforeach
                                        @endif
                                    </div>
                                    <div id="emoji_collaction" class="emoji-box col-auto mt-1 p-0 d-none">
                                        <div class="card">
                                            <div class="card-header dark:bg-black">
                                                <ul id="emojiTabs" class="nav nav-tabs card-header-tabs"
                                                    data-bs-toggle="tabs">
                                                    <!-- Tabs will be rendered here dynamically -->
                                                </ul>
                                            </div>
                                            <div class="card-body dark:bg-black">
                                                <div id="emojiContent" class="tab-content">
                                                    <!-- Content will be rendered here dynamically -->
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </h4>
                                {{-- End of reactions --}}
                            </div>
                            <div class="gap-1">

                                <select name="language"
                                    class="bg-gray-25 p-1 rounded text-right dark:bg-gray-800 dark:text-white translate-css ">
                                    <option value="af">Afrikaans</option>
                                    <option value="sq">Albanian</option>
                                    <option value="am">Amharic</option>
                                    <option value="ar">Arabic</option>
                                    <option value="hy">Armenian</option>
                                    <option value="az">Azerbaijani</option>
                                    <option value="eu">Basque</option>
                                    <option value="be">Belarusian</option>
                                    <option value="bn">Bengali</option>
                                    <option value="bs">Bosnian</option>
                                    <option value="bg">Bulgarian</option>
                                    <option value="ca">Catalan</option>
                                    <option value="ceb">Cebuano</option>
                                    <option value="ny">Chichewa</option>
                                    <option value="zh-cn">Chinese (Simplified)</option>
                                    <option value="zh-tw">Chinese (Traditional)</option>
                                    <option value="co">Corsican</option>
                                    <option value="hr">Croatian</option>
                                    <option value="cs">Czech</option>
                                    <option value="da">Danish</option>
                                    <option value="nl">Dutch</option>
                                    <option value="en">English</option>
                                    <option value="eo">Esperanto</option>
                                    <option value="et">Estonian</option>
                                    <option value="tl">Filipino</option>
                                    <option value="fi">Finnish</option>
                                    <option value="fr">French</option>
                                    <option value="fy">Frisian</option>
                                    <option value="gl">Galician</option>
                                    <option value="ka">Georgian</option>
                                    <option value="de">German</option>
                                    <option value="el">Greek</option>
                                    <option value="gu">Gujarati</option>
                                    <option value="ht">Haitian Creole</option>
                                    <option value="ha">Hausa</option>
                                    <option value="haw">Hawaiian</option>
                                    <option value="iw">Hebrew</option>
                                    <option value="hi">Hindi</option>
                                    <option value="hmn">Hmong</option>
                                    <option value="hu">Hungarian</option>
                                    <option value="is">Icelandic</option>
                                    <option value="ig">Igbo</option>
                                    <option value="id">Indonesian</option>
                                    <option value="ga">Irish</option>
                                    <option value="it">Italian</option>
                                    <option value="ja">Japanese</option>
                                    <option value="jw">Javanese</option>
                                    <option value="kn">Kannada</option>
                                    <option value="kk">Kazakh</option>
                                    <option value="km">Khmer</option>
                                    <option value="rw">Kinyarwanda</option>
                                    <option value="ko">Korean</option>
                                    <option value="ku">Kurdish (Kurmanji)</option>
                                    <option value="ky">Kyrgyz</option>
                                    <option value="lo">Lao</option>
                                    <option value="la">Latin</option>
                                    <option value="lv">Latvian</option>
                                    <option value="lt">Lithuanian</option>
                                    <option value="lb">Luxembourgish</option>
                                    <option value="mk">Macedonian</option>
                                    <option value="mg">Malagasy</option>
                                    <option value="ms">Malay</option>
                                    <option value="ml">Malayalam</option>
                                    <option value="mt">Maltese</option>
                                    <option value="mi">Maori</option>
                                    <option value="mr">Marathi</option>
                                    <option value="mn">Mongolian</option>
                                    <option value="my">Myanmar (Burmese)</option>
                                    <option value="ne">Nepali</option>
                                    <option value="no">Norwegian</option>
                                    <option value="or">Odia (Oriya)</option>
                                    <option value="ps">Pashto</option>
                                    <option value="fa">Persian</option>
                                    <option value="pl">Polish</option>
                                    <option value="pt">Portuguese</option>
                                    <option value="pa">Punjabi</option>
                                    <option value="ro">Romanian</option>
                                    <option value="ru">Russian</option>
                                    <option value="sm">Samoan</option>
                                    <option value="gd">Scots Gaelic</option>
                                    <option value="sr">Serbian</option>
                                    <option value="st">Sesotho</option>
                                    <option value="sn">Shona</option>
                                    <option value="sd">Sindhi</option>
                                    <option value="si">Sinhala</option>
                                    <option value="sk">Slovak</option>
                                    <option value="sl">Slovenian</option>
                                    <option value="so">Somali</option>
                                    <option value="es">Spanish</option>
                                    <option value="su">Sundanese</option>
                                    <option value="sw">Swahili</option>
                                    <option value="sv">Swedish</option>
                                    <option value="tg">Tajik</option>
                                    <option value="ta">Tamil</option>
                                    <option value="tt">Tatar</option>
                                    <option value="te">Telugu</option>
                                    <option value="th">Thai</option>
                                    <option value="tr">Turkish</option>
                                    <option value="tk">Turkmen</option>
                                    <option value="uk">Ukrainian</option>
                                    <option value="ur">Urdu</option>
                                    <option value="ug">Uyghur</option>
                                    <option value="uz">Uzbek</option>
                                    <option value="vi">Vietnamese</option>
                                    <option value="cy">Welsh</option>
                                    <option value="xh">Xhosa</option>
                                    <option value="yi">Yiddish</option>
                                    <option value="yo">Yoruba</option>
                                    <option value="zu">Zulu</option>
                                </select>

                            </div>
                        </div>
                    </div>
                </div>
            </div>
    </div>

    <div class="panel text-black dark:text-white mt-4 lg:mt-6 xl:mt-9">
        <div class="container max-w-xl">
            <div id="selective-blur-description" class="selective-blur {{ $dailyLimitReached ? 'blur-content' : '' }}" {{ $dailyLimitReached ? 'inert' : '' }}>
                <div class="post-content panel fs-6 md:fs-5 mb-4 " id="translateMe" data-uc-lightbox="animation: scale"
                    @if($dailyLimitReached) @readonly(true) inert @endif>
                    {!! $post->description
                        ? html_entity_decode($post->description)
                        : __('frontend-labels.post_detailpage.no_description_available') !!}
                </div>
            </div>
        <div class="row justify-content-center">
            <div class="col-md-12">
                <div class="text-center position-relative">
                    <div id="post-detail-ad"></div>
                </div>
            </div>
        </div>
        <div class="mt-5 translateMe">
            <div id="selective-blur-readmore" class="selective-blur {{ $dailyLimitReached ? 'blur-content' : '' }}" {{ $dailyLimitReached ? 'inert' : '' }}>
                <span>{{ __('frontend-labels.post_detailpage.click_here_to') }}
                    <a href="{{ $post->resource ?? '' }}" class="text-none hover:text-primary" target="_blank"
                        id="readMoreLink" data-daily-limit="{{ $dailyLimitReached ? '1' : '0' }}"
                        data-subscription-limit="{{ $subscriptionLimitReached ? '1' : '0' }}">
                        {{ __('frontend-labels.home.read_more') }}
                        <i class="bi bi-box-arrow-up-right"></i>
                    </a>
                </span>
            </div>
        <div
            class="post-footer panel vstack sm:hstack gap-3 justify-between justifybetween border-top py-4 mt-4 xl:py-9 xl:mt-0">
            <ul class="nav-x gap-narrow">
                <li><span
                        class="text-black dark:text-white me-narrow">{{ __('frontend-labels.post_detailpage.related_topics') }}:</span>
                </li>
                @if (!empty($topics))
                    @foreach ($topics as $index => $topic)
                        <li>
                            <a href="{{ url('topics/' . $topic->slug) }}"
                                class="uc-link gap-0 dark:text-white hover:text-primary">
                                {{ $topic->name }}
                                @if ($index < count($topics) - 1)
                                    <span class="text-black dark:text-white">,</span>
                                @endif
                            </a>
                        </li>
                    @endforeach
                @endif
            </ul>

            <ul class="post-share-icons nav-x gap-narrow mr-auto">
                <li class="me-1"><span
                        class="text-black dark:text-white">{{ __('frontend-labels.post_detailpage.share') }}:</span></li>
                <li>
                    <a class="btn btn-md btn-outline-gray-100 p-0 w-32px lg:w-40px h-32px lg:h-40px text-dark dark:text-white dark:border-gray-600 hover:bg-primary hover:border-primary hover:text-white rounded-circle"
                        href="{{ 'https://www.facebook.com/sharer/sharer.php?u=' . url()->current() }}"><i
                            class="unicon-logo-facebook icon-1"></i></a>
                </li>
                <li>
                    <a class="btn btn-md btn-outline-gray-100 p-0 w-32px lg:w-40px h-32px lg:h-40px text-dark dark:text-white dark:border-gray-600 hover:bg-primary hover:border-primary hover:text-white rounded-circle"
                        href="{{ 'https://twitter.com/intent/tweet?url=' . url()->current() }}"><i
                            class="unicon-logo-x-filled icon-1"></i></a>
                </li>
                <li>
                    <a id="copyButton_1" data-message="{{ __('frontend-labels.post_detailpage.copy_link_success') }}"
                        class="btn btn-md btn-outline-gray-100 p-0 w-32px lg:w-40px h-32px lg:h-40px text-dark dark:text-white dark:border-gray-600 hover:bg-primary hover:border-primary hover:text-white rounded-circle"
                        href="#"><i class="unicon-link icon-1"></i></a>
                </li>
            </ul>
        </div>

        <div class="post-navigation panel vstack sm:hstack justify-between gap-2 mt-8 xl:mt-9">
            <div class="new-post panel hstack w-100 sm:w-1/2">
                @if (!empty($previousPost))
                    <div class="panel hstack justify-center w-100px h-100px">
                        <figure
                            class="featured-image m-0 ratio ratio-1x1 rounded uc-transition-toggle overflow-hidden bg-gray-25 dark:bg-gray-800">

                            @if ($previousPost->type == 'post')
                                <img class="media-cover image uc-transition-scale-up uc-transition-opaque"
                                    src="{{ $previousPost->image ?? asset($defaultImage->value ?? 'public/front_end/classic/images/default/post-placeholder.jpg') }}"
                                    data-src="{{ $previousPost->image ?? asset($defaultImage->value ?? 'public/front_end/classic/images/default/post-placeholder.jpg') }}"
                                    alt="{{ $previousPost->title }}" loading="lazy">
                            @elseif ($previousPost->type == 'audio')
                                <img class="media-cover image uc-transition-scale-up uc-transition-opaque"
                                    src="{{ $previousPost->image ?? asset($defaultImage->value ?? 'public/front_end/classic/images/default/post-placeholder.jpg') }}"
                                    data-src="{{ $previousPost->image ?? asset($defaultImage->value ?? 'public/front_end/classic/images/default/post-placeholder.jpg') }}"
                                    alt="{{ $previousPost->title }}" loading="lazy">
                                <div class="post-category hstack gap-narrow justify-center align-items-center text-white">
                                    <a class="text-none" href="{{ url('posts/' . $previousPost->slug) }}"
                                        title="{{ $previousPost->title }}"><i
                                            class="bi bi-play-circle font-size-45"></i></a>
                                </div>
                            @elseif($previousPost->type == 'video' || $previousPost->type == 'youtube')
                                <img class="media-cover image uc-transition-scale-up uc-transition-opaque"
                                    src="{{ $previousPost->video_thumb ?? asset($defaultImage->value ?? 'public/front_end/classic/images/default/post-placeholder.jpg') }}"
                                    data-src="{{ $previousPost->video_thumb ?? asset($defaultImage->value ?? 'public/front_end/classic/images/default/post-placeholder.jpg') }}"
                                    alt="{{ $previousPost->title }}" loading="lazy">
                                <div class="post-category hstack gap-narrow justify-center align-items-center text-white">
                                    <a class="text-none" href="{{ url('posts/' . $previousPost->slug) }}"
                                        title="{{ $previousPost->title }}"><i
                                            class="bi bi-play-circle font-size-45"></i></a>
                                </div>
                            @endif
                            <a href="{{ url('posts/' . $previousPost->slug) }}" class="position-cover"
                                data-caption="{{ $previousPost->title }}"></a>
                        </figure>
                    </div>
                    <div class="panel vstack justify-center px-2 gap-1 w-1/3">
                        <span class="fs-7 opacity-900">{{ __('frontend-labels.post_detailpage.prev_article') }}</span>
                        <h6 class="h6 lg:h5 m-0">{{ $previousPost->title }}</h6>
                    </div>
                    <a href="{{ url('posts/' . $previousPost->slug) }}" class="position-cover"></a>
                @endif
            </div>
            <div class="new-post panel hstack w-100 sm:w-1/2">
                @if ($nextPost)
                    <div class="panel vstack justify-center px-2 gap-1 w-1/3 text-end">
                        <span class="fs-7 opacity-900">{{ __('frontend-labels.post_detailpage.next_article') }}</span>
                        <h6 class="h6 lg:h5 m-0">{{ $nextPost->title }}</h6>
                    </div>
                    <div class="panel hstack justify-center w-100px h-100px">
                        <figure
                            class="featured-image m-0 ratio ratio-1x1 rounded uc-transition-toggle overflow-hidden bg-gray-25 dark:bg-gray-800">
                            <a href="{{ url('posts/' . $nextPost->slug) }}" class="position-cover"
                                data-caption="{{ $nextPost->title }}">
                                @if ($nextPost->type == 'post')
                                    <img class="media-cover image uc-transition-scale-up uc-transition-opaque"
                                        src="{{ $nextPost->image ?? asset($defaultImage->value ?? 'public/front_end/classic/images/default/post-placeholder.jpg') }}"
                                        data-src="{{ $nextPost->image }}" alt="{{ $nextPost->title }}"
                                        loading="lazy">
                                @elseif ($nextPost->type == 'audio')
                                    <img class="media-cover image uc-transition-scale-up uc-transition-opaque"
                                        src="{{ $nextPost->image ?? asset($defaultImage->value ?? 'public/front_end/classic/images/default/post-placeholder.jpg') }}"
                                        data-src="{{ $nextPost->image }}" alt="{{ $nextPost->title }}"
                                        loading="lazy">
                                    <div
                                        class="post-category hstack gap-narrow justify-center align-items-center text-white">
                                        <a class="text-none" href="{{ url('posts/' . $nextPost->slug) }}"
                                            title="{{ $nextPost->title }}"><i
                                                class="bi bi-play-circle font-size-45"></i></a>
                                    </div>
                                @elseif($nextPost->type == 'video' || $nextPost->type == 'youtube')
                                    <img class="media-cover image uc-transition-scale-up uc-transition-opaque"
                                        src="{{ $nextPost->video_thumb ?? asset($defaultImage->value ?? 'public/front_end/classic/images/default/post-placeholder.jpg') }}"
                                        data-src="{{ $nextPost->video_thumb ?? asset($defaultImage->value ?? 'public/front_end/classic/images/default/post-placeholder.jpg') }}"
                                        alt="{{ $nextPost->title }}" loading="lazy">
                                    <div
                                        class="post-category hstack gap-narrow justify-center align-items-center text-white">
                                        <a class="text-none" href="{{ url('posts/' . $nextPost->slug) }}"
                                            title="{{ $nextPost->title }}"><i
                                                class="bi bi-play-circle font-size-45"></i></a>
                                    </div>
                                @endif
                            </a>
                        </figure>
                    </div>
                    <a href="{{ url('posts/' . $nextPost->slug) }}" class="position-cover"></a>
                @endif
            </div>
        </div>

        <div class="post-related panel border-top pt-2 mt-8 xl:mt-9">
            <h4 class="h5 xl:h4 mb-5 xl:mb-6">{{ __('frontend-labels.post_detailpage.related') }}
                {{ $post->topic_name }}
                {{ __('frontend-labels.post_detailpage.updates') }}:</h4>
            <div class="row child-cols-6 md:child-cols-3 gx-2 gy-4 sm:gx-3 sm:gy-6">
                @foreach ($relatedPosts as $reletedPost)
                    <div>
                        <article class="post type-post panel vstack gap-2">
                            <figure
                                class="featured-image m-0 ratio ratio-4x3 rounded uc-transition-toggle overflow-hidden bg-gray-25 dark:bg-gray-800">
                                <a href="{{ url('posts/' . $reletedPost->slug) }}" class="position-cover"
                                    data-caption="The Art of Baking: From Classic Bread to Artisan Pastries">
                                    @if ($reletedPost->type == 'post')
                                        <img class="media-cover image uc-transition-scale-up uc-transition-opaque"
                                            src="{{ $reletedPost->image ?? asset('public/front_end/classic/images/default/post-placeholder.jpg') }}"
                                            data-src="{{ $reletedPost->image ?? asset('public/front_end/classic/images/default/post-placeholder.jpg') }}"
                                            alt="The Art of Baking: From Classic Bread to Artisan Pastries"
                                            loading="lazy">
                                    @elseif ($reletedPost->type == 'audio')
                                        <img class="media-cover image uc-transition-scale-up uc-transition-opaque"
                                            src="{{ $reletedPost->image ?? asset('public/front_end/classic/images/default/post-placeholder.jpg') }}"
                                            data-src="{{ $reletedPost->image ?? asset('public/front_end/classic/images/default/post-placeholder.jpg') }}"
                                            alt="The Art of Baking: From Classic Bread to Artisan Pastries"
                                            loading="lazy">
                                        <div
                                            class="post-category hstack gap-narrow justify-center align-items-center text-white">
                                            <a class="text-none" href="{{ url('posts/' . $reletedPost->slug) }}"
                                                title="{{ $reletedPost->title }}"><i
                                                    class="bi bi-play-circle font-size-45"></i></a>
                                        </div>
                                    @elseif ($reletedPost->type == 'video' || $reletedPost->type == 'youtube')
                                        <img class="media-cover image uc-transition-scale-up uc-transition-opaque"
                                            src="{{ $reletedPost->video_thumb ?? asset('public/front_end/classic/images/default/post-placeholder.jpg') }}"
                                            data-src="{{ $reletedPost->video_thumb ?? asset('public/front_end/classic/images/default/post-placeholder.jpg') }}"
                                            alt="The Art of Baking: From Classic Bread to Artisan Pastries"
                                            loading="lazy">
                                        <div
                                            class="post-category hstack gap-narrow justify-center align-items-center text-white">
                                            <a class="text-none" href="{{ url('posts/' . $reletedPost->slug) }}"
                                                title="{{ $reletedPost->title }}"><i
                                                    class="bi bi-play-circle font-size-45"></i></a>
                                        </div>
                                    @endif
                                </a>
                            </figure>
                            <div class="post-header panel vstack gap-1">
                                <h5 class="h6 md:h5 text-truncate-2 m-0 hover:text-primary">
                                    <a class="text-none"
                                        href="{{ url('posts/' . $reletedPost->slug) }}">{{ $reletedPost->title }}</a>
                                </h5>
                            </div>
                            <div>
                                <div class="post-meta panel fs-7 fw-medium text-gray-900 dark:text-white text-opacity-60">
                                    <div class="meta">
                                        <div class="d-flex justify-between gap-2">
                                            <div>
                                                <div class="d-flex gap-1">
                                                    <a href="{{ url('channels/' . $reletedPost->channel_slug) }}"
                                                        title="{{ $reletedPost->channel_name }}"><img
                                                            src="{{ url('storage/images/' . $reletedPost->channel_logo) }}"
                                                            alt="Channel Logo" class="h-20px"></a>
                                                    <a href="{{ url('channels/' . $reletedPost->channel_slug) }}"
                                                        class="text-black dark:text-white text-none fw-bold"
                                                        title="{{ $reletedPost->channel_name }}">{{ $reletedPost->channel_name }}</a>
                                                </div>
                                            </div>

                                            <div>
                                                <div class="post-comments text-none hstack gap-narrow gap-1">
                                                    <a href="{{ url('posts/' . $reletedPost->slug) }}#comment-form"
                                                        class="post-comments text-none hstack gap-narrow"
                                                        title="Comments">
                                                        <i class="icon-narrow unicon-chat"></i>
                                                        <span>{{ $reletedPost->comment }}</span>
                                                    </a>
                                                    <i class="bi bi-eye fs-5" title="Views"></i>
                                                    <span title="Views">{{ $reletedPost->view_count }}</span>
                                                </div>
                                            </div>
                                        </div>
                                        <div>
                                            <div class="post-date hstack gap-narrow mt-1">
                                                <span
                                                    title="{{ $reletedPost->publish_date_news }}">{{ $reletedPost->publish_date ?? $reletedPost->pubdate }}</span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="actions">
                                        <div class="hstack gap-1"></div>
                                    </div>
                                </div>
                            </div>
                        </article>
                    </div>
                @endforeach
            </div>
        </div>
        <input type="hidden" id="post_id" value="{{ $post->id }}">
        <input type="hidden" id="user_id" value="{{ auth()->user()->id ?? '0' }}">
        <input type="hidden" id="sendDataUrl" value="{{ route('comments.store') }}">
        <input type="hidden" id="updateDataUrl" value="{{ route('comments.update') }}">
        <input type="hidden" id="reportDataUrl" value="{{ url('api/v1/comments/web/report') }}">
        <input type="hidden" id="checkReportDataUrl" value="{{ url('api/v1/commets/check-report') }}">
        <input type="hidden" id="reportReasonDataUrl" value="{{ url('api/v1/report-reason-web-types') }}">

        <div id="translation-data" class="d-none" type="hidden"
            data-report-comment="{{ __('frontend-labels.comment_report.report_comment') }}"
            data-select-reason="{{ __('frontend-labels.comment_report.select_reason') }}"
            data-additional-details="{{ __('frontend-labels.comment_report.additional_details') }}"
            data-additional-info-placeholder="{{ __('frontend-labels.comment_report.additional_info_placeholder') }}"
            data-other="{{ __('frontend-labels.comment_report.other') }}"
            data-custom-reason-placeholder="{{ __('frontend-labels.comment_report.custom_reason_placeholder') }}"
            data-send-report="{{ __('frontend-labels.comment_report.send_report') }}"
            data-block-comment="{{ __('frontend-labels.comment_report.block_comment') ?? 'Block Comment' }}"
            data-block-reason="{{ __('frontend-labels.comment_report.block_reason') ?? 'Block Reason' }}"
            data-block-reason-placeholder="{{ __('frontend-labels.comment_report.block_reason_placeholder') ?? 'Why are you blocking this comment?' }}"
            data-send-block="{{ __('frontend-labels.comment_report.send_block') ?? 'Block Comment' }}">
        </div>

        <div id="comment-delete-labels" class="d-none">
            <span id="swal-delete-title">{{ __('frontend-labels.commentbox.delete_title') }}</span>
            <span id="swal-delete-text">{{ __('frontend-labels.commentbox.delete_text') }}</span>
            <span id="swal-delete-button">{{ __('frontend-labels.commentbox.delete_button') }}</span>
            <span id="swal-cancel-button">{{ __('frontend-labels.commentbox.cancel_button') }}</span>
        </div>



        <div id="blog-comment" class="panel border-top pt-2 mt-8 xl:mt-9">
            <h4 class="h5 xl:h4 mb-5 xl:mb-6" id="comment-count">
                {{ __('frontend-labels.commentbox.comments') }} (<span id="count-num">0</span>)
            </h4>
            <div class="spacer-half"></div>
            <div class="mt-3 mb-3">
                <ol id="comments-list" class="list-group"></ol>
            </div>
            <h4 class="h5 xl:h4 mb-5 xl:mb-6">{{ __('frontend-labels.commentbox.leave_a_comment') }}</h4>
            <div class="comment_form_holder">
                <form id="comment-form" class="vstack gap-2" onsubmit="submitComment(event)">
                    @if (auth()->check())
                        <input
                            class="form-control form-control-sm h-40px w-full fs-6 bg-white dark:bg-opacity-0 dark:text-white dark:border-gray-300 dark:border-opacity-30"
                            type="text" name="name" id="name"
                            placeholder="{{ __('frontend-labels.commentbox.first_name') }}"
                            data-message="{{ __('frontend-labels.commentbox.name_change_not_allowed') }}"
                            value="{{ auth()->user()->name }}">
                        <input
                            class="form-control form-control-sm h-40px w-full fs-6 bg-white dark:bg-opacity-0 dark:text-white dark:border-gray-300 dark:border-opacity-30"
                            type="email" name="email" id="email"
                            placeholder="{{ __('frontend-labels.commentbox.your_email') }}"
                            data-message="{{ __('frontend-labels.commentbox.name_change_not_allowed') }}"
                            value="{{ auth()->user()->email }}">
                        <textarea
                            class="form-control h-250px w-full fs-6 bg-white dark:bg-opacity-0 dark:text-white dark:border-gray-300 dark:border-opacity-30"
                            name="comment" id="comment" title="{{ __('frontend-labels.commentbox.add_your_comment') }}"
                            placeholder="{{ __('frontend-labels.commentbox.your_comment') }}"></textarea>
                        <button class="btn btn-primary btn-sm mt-1"
                            type="submit">{{ __('frontend-labels.commentbox.send') }}</button>
                    @else
                        <textarea
                            class="form-control h-250px w-full fs-6 bg-white dark:bg-opacity-0 dark:text-white dark:border-gray-300 dark:border-opacity-30"
                            name="comment" id="comment" title="{{ __('frontend-labels.commentbox.add_your_comment') }}"
                            placeholder="{{ __('frontend-labels.commentbox.your_comment') }}"></textarea>
                        <a class="btn btn-primary btn-sm mt-1" href="#uc-account-modal"
                            data-uc-toggle>{{ __('frontend-labels.commentbox.send') }}</a>
                    @endif
                </form>
            </div>
        </div>
    </div>
    </div>
    </article>

    <!-- Newsletter -->
    <input type="hidden" id="user_id" value="{{ auth()->user()->id ?? '' }}">
    </div>
    <div class="share-div"></div>
    <!-- Wrapper end -->
@endsection

@section('script')
    <script defer src="{{ asset('front_end/' . $theme . '/js/custom/post-detail.js') }}?v=<?= time() ?>"></script>
@endsection
