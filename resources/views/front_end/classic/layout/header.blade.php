@php
    $title = '';
@endphp


{{-- <><><><><><><><><><> START SEARCH MODAL (CENTERED) <><><><><><><><><><>  --}}
<div id="uc-search-modal" data-uc-modal="overlay: true;" class="uc-modal" tabindex="-1">
    <div class="uc-modal-dialog bg-white text-dark dark:bg-gray-900 dark:text-white rounded-lg overflow-hidden"
        role="dialog" aria-modal="true">
        <header class="uc-modal-header hstack justify-between items-center p-3 border-bottom  bg-white dark:bg-gray-900">
            <h5 class="m-0 fw-bold dark:text-white">{{ __('frontend-labels.search.title') }}</h5>
            <button
                class="uc-modal-close-default p-0 icon-3 btn border-0 dark:text-white dark:text-opacity-50 hover:text-primary hover:rotate-90 duration-150 transition-all focus:outline-none"
                type="button" aria-label="Close search">
                <i class="unicon-close"></i>
            </button>
        </header>

        <div class="p-4">
            {{-- Search Input --}}
            <div class="search-input-wrap mb-4">
                <form
                    class="hstack gap-2 border rounded-xl px-3 py-0 bg-gray-25 dark:bg-gray-800 dark:border-gray-700 transition-all duration-200 focus-within:ring-2 focus-within:ring-primary focus-within:ring-opacity-50"
                    method="GET" id="search-form-data" action="{{ route('posts.search') }}"
                    onsubmit="return handleSearchSubmit(event)">
                    <span class="d-inline-flex justify-center items-center w-24px h-24px opacity-50 text-primary">
                        <i class="unicon-search icon-2"></i>
                    </span>
                    <input type="search" name="search" id="sidebar_search_input"
                        class="form-control-plaintext ms-1 fs-6 w-full dark:text-white border-0 bg-transparent focus:outline-none"
                        placeholder="Search news, topics, channels…" aria-label="Search" autocomplete="off">
                </form>
            </div>

            {{-- Recent Searches Section --}}
            <div id="recent-searches-section" class="mb-4">
                <div class="hstack justify-between items-center mb-2">
                    <h6 class="m-0 fw-bold fs-7 text-uppercase text-gray-500 dark:text-gray-400 tracking-wider">Recent
                        Searches</h6>
                    <button type="button" class="btn btn-sm border-0 p-0 fs-7 text-primary fw-bold hover:underline"
                        id="clear-all-recent-searches" style="display:none;">
                        Clear All
                    </button>
                </div>
                <div id="recent-searches-list" class="vstack gap-1">
                    {{-- Dynamically populated --}}
                </div>
                <div id="no-recent-searches" class="text-center py-4 opacity-50 fs-7  rounded-lg" style="display:none;">
                    No recent searches yet.
                </div>
            </div>
            
             {{-- Search Suggestion Section --}}
             <div id="suggestions-section" class="mb-4" style="display:none;">
                <div class="hstack justify-between items-center mb-2">
                    <h6 class="m-0 fw-bold fs-7 text-uppercase text-gray-500 dark:text-gray-400 tracking-wider">Suggestions</h6>
                </div>
                <div id="suggestions-list" class="vstack gap-1">
                    {{-- Dynamically populated --}}
                </div>
            </div>

            {{-- Search Results Section (hidden until search) --}}
            <div id="search-results-section" style="display:none;">
                <div class="hstack justify-between items-center mb-3">
                    <h6 class="m-0 fw-bold fs-7 text-uppercase text-gray-500 dark:text-gray-400"
                        id="search-results-title">Results</h6>
                </div>
                {{-- Tabs (Two-row Grid Slider) --}}
                <ul class="nav nav-x gap-2 search-tabs-grid scroll-smooth mb-3 pb-2 border-bottom dark:border-gray-700"
                    id="searchTabsNav">
                    <li><a href="#"
                            class="search-tab-link active fs-7 fw-bold px-3 py-1.5 text-none transition-all duration-200 rounded-1 p-1 bg-gray-25 hover:text-white  dark:bg-gray-800 hover:bg-primary"
                            data-tab="all">All</a></li>
                    <li><a href="#"
                            class="search-tab-link fs-7 fw-bold px-3 py-1.5  text-none transition-all duration-200 rounded-1 p-1 bg-gray-25 hover:text-white  dark:bg-gray-800 hover:bg-primary"
                            data-tab="post">Articles</a></li>
                    <li><a href="#"
                            class="search-tab-link fs-7 fw-bold px-3 py-1.5  text-none transition-all duration-200 rounded-1 p-1 bg-gray-25 hover:text-white  dark:bg-gray-800 hover:bg-primary"
                            data-tab="video">Videos</a></li>
                    <li><a href="#"
                            class="search-tab-link fs-7 fw-bold px-3 py-1.5  text-none transition-all duration-200 rounded-1 p-1 bg-gray-25 hover:text-white  dark:bg-gray-800 hover:bg-primary"
                            data-tab="audio">Audio</a></li>
                    <li><a href="#"
                            class="search-tab-link fs-7 fw-bold px-3 py-1.5  text-none transition-all duration-200 rounded-1 p-1 bg-gray-25 hover:text-white  dark:bg-gray-800 hover:bg-primary"
                            data-tab="channels">Channels</a></li>
                    <li><a href="#"
                            class="search-tab-link fs-7 fw-bold px-3 py-1.5  text-none transition-all duration-200 rounded-1 p-1 bg-gray-25 hover:text-white  dark:bg-gray-800 hover:bg-primary"
                            data-tab="topics">Topics</a></li>
                </ul>
                {{-- Tab Content --}}
                <div id="search-tab-content" class="search-tab-content scroll-smooth"
                    style="overflow-y:auto; max-height: 450px;">
                    <div id="search-loading" class="text-center py-6" style="display:none;">
                        <div class="spinner-border spinner-border-sm text-primary" role="status">
                            <span class="visually-hidden"></span>
                        </div>
                        <span class="ms-2 fs-7 fw-medium"></span>
                    </div>
                    <div id="search-results-list" class="vstack gap-2">
                        {{-- AJAX results will be injected here --}}
                    </div>
                    <div id="no-results-message" class="text-center py-6 bg-gray-25 dark:bg-gray-800 rounded-lg"
                        style="display:none;">
                        <i class="bi bi-search fs-1 opacity-20 d-block mb-2"></i>
                        <p class="fw-bold mb-1">No results matching your query</p>
                        <p class="fs-7 opacity-60">Try different keywords or check spelling.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
{{-- <><><><><><><><><><> END SEARCH MODAL (CENTERED) <><><><><><><><><><>  --}}

{{-- <><><><><><><><><><> START MENU MODEL <><><><><><><><><><>  --}}
<div id="uc-menu-panel" data-uc-offcanvas="overlay: true;">

    <div class="uc-offcanvas-bar bg-white text-dark dark:bg-gray-900 dark:text-white">
        <header class="uc-offcanvas-header hstack justify-between items-center pb-4 bg-white dark:bg-gray-900">
            <div class="uc-logo">
                <a href="{{ url('home') }}" class="h5 text-none text-gray-900 dark:text-white">
                    {{-- Dark --}}
                    <img class="img-fluid w-auto text-dark dark:text-white hover:text-primary transition-color duration-150 d-block dark:d-none header-img-max-height"
                        src="{{ $dark_logo != null ? url('storage/' . $dark_logo->value) : asset('assets/images/logo/DarkLogo.png') }}"
                        fetchpriority="high" alt="Light">
                    {{-- Light --}}
                    <img class="img-fluid w-auto text-dark dark:text-white hover:text-primary transition-color duration-150 d-none dark:d-block header-img-max-height"
                        src="{{ $light_logo != null ? url('storage/' . $light_logo->value) : asset('assets/images/logo/LightLogo.png') }}"
                        fetchpriority="high" alt="Dark">
                </a>
            </div>
            <button
                class="uc-offcanvas-close p-0 icon-3 btn border-0 dark:text-white dark:text-opacity-50 hover:text-primary hover:rotate-90 duration-150 transition-all"
                type="button">
                <i class="unicon-close"></i>
            </button>
        </header>

        <div class="panel">
            {{-- <form method="GET" action="{{ route('posts.search') }}" onsubmit="return modifyQuery(this)"
                class="form-icon-group vstack gap-1 mb-3">
                <input type="search" name="search" id="globle_search" class="form-control form-control-md fs-6"
                    placeholder="{{ __('frontend-labels.search.title') }}..">
                <span class="form-icon text-gray">
                    <i class="unicon-search icon-1"></i>
                </span>
            </form> --}}
            <ul class="nav-y gap-narrow fw-bold fs-5" data-uc-nav>
                <li class="uc-parent">
                    <a href="#">{{ __('frontend-labels.channels.title') }}</a>
                    <ul class="uc-nav-sub" data-uc-nav="">
                        @if (!empty($channels))
                            @foreach ($channels as $channel)
                                <li class="d-flex" title="{{ $channel->name ?? '' }}"><a
                                        href="{{ url('channels/' . $channel->slug) }}">{{ $channel->name ?? '' }}</a>
                                </li>
                            @endforeach
                        @endif
                        <li title="{{ __('frontend-labels.sponsor_ads.all_channels') }}"><a
                                href="{{ url('channels') }}"
                                aria-label="View all channels">{{ __('frontend-labels.sponsor_ads.all_channels') }}</a>
                        </li>
                    </ul>
                </li>
                <li class="uc-parent">
                    <a href="#"> {{ __('frontend-labels.topics.title') }}</a>
                    <ul class="uc-nav-sub" data-uc-nav="">
                        @if (!empty($topics))
                            @foreach ($topics as $topic)
                                <li title="{{ $topic->name ?? '' }}"><a
                                        href="{{ url('topics/' . $topic->slug) }}">{{ $topic->name ?? '' }}</a></li>
                            @endforeach
                        @endif
                        <li title="{{ __('frontend-labels.topics.all_topics') }}"><a
                                href="{{ url('topics') }}">{{ __('frontend-labels.topics.all_topics') }}</a></li>
                    </ul>
                </li>

                <li title="Web Stories"><a
                        href="{{ url('webstories') }}">{{ __('frontend-labels.web_stories.title') }}</a></li>
                <li title="E-Newspaper"><a href="{{ url('e-newspaper') }}">{{ __('frontend-labels.enewspapers.title') }}</a></li>
                <li title="E-Magazine"><a href="{{ url('e-magazine') }}">{{ __('frontend-labels.magazines.title') }}</a></li>
                <li class="hr opacity-10 my-1"></li>
                <li title="{{ __('frontend-labels.posts.all_posts') }}"><a href="{{ url('posts') }}"
                        aria-label="Browse all posts">{{ __('frontend-labels.posts.all_posts') }}</a></li>

                @if (isset($socialsettings['enable_custom_ads_status']) && $socialsettings['enable_custom_ads_status'] == '1')
                    <li title="{{ __('frontend-labels.sponsor_ads.title') }}"><a
                            href="{{ url('sponsor-ads') }}">{{ __('frontend-labels.sponsor_ads.title') }}</a></li>
                @endif

                @if ($free_trial_status == '0')
                    <li title="{{ __('frontend-labels.membership.title') }}"><a
                            href="{{ url('membership') }}">{{ __('frontend-labels.membership.title') }}</a></li>
                            
                            <li title="{{ __('frontend-labels.mysubscription.title') }}"><a
                                href="{{ url('my-account/subscription') }}">{{ __('frontend-labels.mysubscription.title') }}</a>
                            </li>
                            <li title="{{ __('frontend-labels.transaction_details.title') }}"><a
                                href="{{ url('my-account/transaction') }}">{{ __('frontend-labels.transaction_details.title') }}</a>
                            </li>
                            @endif
                <li title="{{ __('frontend-labels.terms_and_conditions.title') }}"><a
                        href="{{ url('terms-and-condition') }}">{{ __('frontend-labels.terms_and_conditions.title') }}</a>
                </li>
                <li title="{{ __('frontend-labels.privacy_policy.title') }}"><a
                        href="{{ url('privacy-policies') }}">{{ __('frontend-labels.privacy_policy.title') }}</a>
                </li>
                <li title="{{ __('frontend-labels.contactus.title') }}"><a
                        href="{{ url('contact-us') }}">{{ __('frontend-labels.contactus.title') }}</a></li>
                <li title="{{ __('frontend-labels.aboutus.title') }}"><a
                        href="{{ url('about-us') }}">{{ __('frontend-labels.aboutus.title') }}</a></li>
                @if (auth()->check())
                    <li title="{{ __('frontend-labels.my-account.account_info') }}"><a
                            href="{{ url('my-account') }}">{{ __('frontend-labels.my-account.account_info') }}</a>
                    <li title="{{ __('frontend-labels.sponsor_ads.logout') }}"><a href="{{ route('logout') }}"
                            onclick="event.preventDefault(); document.getElementById('logout-form').submit();">{{ __('frontend-labels.sponsor_ads.logout') }}</a>
                    </li>
                    <form id="logout-form" action="{{ route('logout') }}" method="POST">
                        @csrf
                    </form>
                @else
                    <li title="{{ __('frontend-labels.frontend_login.login_button') }}"><a
                            class="uc-account-trigger position-relative btn btn-sm border-0 p-0 gap-narrow duration-0 dark:text-white"
                            href="#uc-account-modal"
                            data-uc-toggle>{{ __('frontend-labels.frontend_login.login_button') }}</a></li>
                    <li title="{{ __('frontend-labels.register.title') }}"><a href="#uc-account-modal"
                            class="open-signup-modal-mobile"
                            data-uc-toggle>{{ __('frontend-labels.register.title') }}</a></li>
                @endif
            </ul>
            <ul class="social-icons nav-x mt-4">
                <li>
                    <a href="{{ $socialsettings['instagram_link'] ?? '' }}"><i
                            class="icon icon-2 unicon-logo-instagram" aria-label="Instagram"></i></a>
                    <a href="{{ $socialsettings['x_link'] ?? '' }}"><i class="icon icon-2 unicon-logo-x-filled"
                            aria-label="X"></i></a>
                    <a href="{{ $socialsettings['facebook_link'] ?? '' }}"><i
                            class="icon icon-2 unicon-logo-facebook" aria-label="Facebook"></i></a>
                    <a href="{{ $socialsettings['linkedin_link'] ?? '' }}"><i
                            class="icon icon-2 unicon-logo-linkedin" aria-label="LinkedIn"></i></a>
                    <a href="{{ $socialsettings['pinterest_link'] ?? '' }}"><i
                            class="icon icon-2 unicon-logo-pinterest" aria-label="Pinterest"></i></a>
                </li>
            </ul>
        </div>
    </div>
</div>
{{-- <><><><><><><><><><> END MENU MODEL <><><><><><><><><><>  --}}

{{-- <><><><><><><><><><> START SUBSCRIPTION MODEL <><><><><><><><><><>  --}}
@if (isset($newsletterSettings) && $newsletterSettings['status'] == '1')
    <div id="uc-newsletter-modal" data-uc-modal="overlay: true" class="uc-modal newsletter-modal" tabindex="-1">
        <div class="uc-modal-dialog w-800px bg-white text-dark dark:bg-gray-900 dark:text-white rounded overflow-hidden"
            role="dialog" aria-modal="true" aria-label="Newsletter Subscription">
            <button aria-label="Close dialog"
                class="uc-modal-close-default p-0 icon-3 btn border-0 dark:text-white dark:text-opacity-50 hover:text-primary hover:rotate-90 duration-150 transition-all"
                type="button" id="closeNewsletterModal">
                <i class="unicon-close"></i>
            </button>
            <div class="row md:child-cols-6 col-match g-0">
                @if (!empty($newsletterSettings['image']))
                    <div class="d-none md:d-flex">
                        <div class="position-relative w-100 ratio-1x1">
                            <img class="media-cover" src="{{ asset('storage/' . $newsletterSettings['image']) }}"
                                alt="Newsletter image">

                        </div>
                    </div>
                @endif
                <div class="{{ empty($newsletterSettings['image']) ? 'col-12' : '' }}">
                    <div class="panel vstack self-center p-4 md:py-8 text-center">
                        <h3 class="h3 md:h2">{{ $newsletterSettings['title'] }}</h3>
                        <p class="ft-tertiary">{{ $newsletterSettings['subtitle'] }}</p>
                        <div class="panel mt-2 lg:mt-4">
                            <form class="vstack gap-1" method="post" action="{{ route('subscribe.store') }}">
                                @csrf
                                <input type="email" name="email" id="model-subscriber_email"
                                    data-email-required="{{ __('frontend-labels.home.email_required') }}"
                                    data-email-taken="{{ __('frontend-labels.home.email_taken') }}"
                                    data-email-invalid="{{ __('frontend-labels.home.email_invalid') }}"
                                    data-email-subscribed="{{ __('frontend-labels.home.email_subscribed') }}"
                                    class="form-control form-control-sm w-full fs-6 bg-white dark:border-white dark:border-gray-700 dark:text-dark"
                                    placeholder="{{ __('frontend-labels.home.your_email_address') }}.."
                                    required="">

                                <button type="submit" id="model-subscriber-button"
                                    class="btn btn-sm btn-primary fs-6 rounded-0">{{ __('frontend-labels.home.subscribe') }}</button>
                                <div id="model-subscriber-error-top" class="alert alert-danger d-none"></div>
                            </form>
                            <div id="newsletter-message" class="mt-2" style="display: none;"></div>
                            <p class="fs-7 mt-2">{{ __('frontend-labels.home.dont_worry_we_dont_spam') }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endif
{{-- <><><><><><><><><><> END SUBSCRIPTION MODEL <><><><><><><><><><>  --}}

{{-- <><><><><><><><><><> START CHANNEL FOLLOW  MODEL <><><><><><><><><><>  --}}
@if (session('first_login'))
    <div id="channels-follow-model" data-uc-modal="overlay: true">
        <div
            class="uc-modal-dialog w-800px bg-white text-dark dark:bg-gray-900 dark:text-white rounded overflow-hidden">
            <button aria-label="Close dialog"
                class="uc-modal-close-default p-0 icon-3 btn border-0 dark:text-white dark:text-opacity-50 hover:text-primary hover:rotate-90 duration-150 transition-all"
                type="button">
                <i class="unicon-close"></i>
            </button>
            <div class="row md:child-cols-6 col-match g-0">
                <div class="panel vstack self-center p-4 text-center">
                    <h3 class="h3 md:h2">{{ __('frontend-labels.channels.title') }}</h3>
                    <div class="panel mt-2 lg:mt-4">
                        <div class="mb-3">
                            <div class="form-selectgroup form-selectgroup-pills d-flex">
                                @foreach ($channels as $index => $channel)
                                    @if ($index < 1)
                                        @continue
                                    @endif
                                    <label for="" class="row form-selectgroup-item">
                                        <div class="mx-h-72px mx-w-150px">
                                            <img id="profile-image-preview"
                                                src="{{ isset($channel->logo) ? url('storage/images/' . $channel->logo) : asset('front_end/classic/images/avatars/04.png') }}"
                                                alt="Profile Preview"
                                                class="h-72px img-fluid mx-auto rounded-1-5 w-150px">
                                        </div>
                                        <div>
                                            <button class="btn btn-primary mt-2 custom-btn-xs channel-follow"
                                                data-channel-id="{{ $channel->id }}">
                                                Follow
                                            </button>
                                        </div>
                                    </label>
                                @endforeach
                            </div>
                            <div class="d-flex justify-end">
                                <button type="button" class="btn btn-sm btn-primary mt-2"
                                    id="done-button">Done</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endif
{{-- <><><><><><><><><><> END CHANNEL FOLLOW  MODEL <><><><><><><><><><>  --}}

{{-- <><><><><><><><><><> START MERGED LANGUAGE SELECT MODEL <><><><><><><><><><>  --}}
@if ($news_language_status === 'active' && (count($news_languages_overwrite) > 1 || count($web_languages) > 1))
    <div id="news-language-modal" class="uc-modal" data-uc-modal="overlay: true">
        <div class="uc-modal-dialog lg:max-w-800px bg-white text-dark dark:bg-gray-800 dark:text-white rounded">
            <button aria-label="Close dialog"
                class="uc-modal-close-default p-0 icon-3 btn border-0 dark:text-white dark:text-opacity-50 hover:text-primary hover:rotate-90 duration-150 transition-all"
                type="button">
                <i class="unicon-close"></i>
            </button>
            <div class="panel p-2 mt-10 text-right">
                <h5 class="mb-3 text-center"><strong
                        class="text-black dark:text-white">{{ __('frontend-labels.language_settings.language_settings') }}</strong>
                </h5>

                <ul class="nav justify-content-center mb-4" role="tablist">

                    <li class="nav-item col col-md-6 news_language_model_css" role="presentation">
                        <button class="nav-link active w-100 p-0 border-0 bg-transparent" id="news-language-tab"
                            data-bs-toggle="tab" data-bs-target="#news-language-content" type="button"
                            role="tab" aria-controls="news-language-content" aria-selected="true">
                            <div
                                class="p-2 rounded border fw-semibold 
                        text-center fs-6 fs-md-5 dark:bg-gray-700 dark:text-white ">
                                {{ __('frontend-labels.language_settings.news_language') }}
                            </div>
                        </button>
                    </li>

                    <li class="nav-item col col-md-6" role="presentation">
                        <button class="nav-link w-100 p-0 border-0 bg-transparent" id="web-language-tab"
                            data-bs-toggle="tab" data-bs-target="#web-language-content" type="button"
                            role="tab" aria-controls="web-language-content" aria-selected="false">
                            <div
                                class="p-2 rounded border fw-semibold 
                        text-center fs-6 fs-md-5 dark:bg-gray-700 dark:text-white ms-1">
                                {{ __('frontend-labels.language_settings.web_language') }}
                            </div>
                        </button>
                    </li>

                </ul>

                {{-- Tab Content --}}
                <div class="tab-content">
                    {{-- News Language Tab --}}
                    <div class="tab-pane fade show active" id="news-language-content" role="tabpanel"
                        aria-labelledby="news-language-tab">
                        <div class="panel text-center">
                            @if (!empty($news_languages_overwrite))
                                <div
                                    class="row child-cols-6 sm:child-cols-3 md:child-cols-3 lg:child-cols-3 xl:child-cols-4 col-match gy-4 xl:gy-6 gx-2 sm:gx-4">
                                    @foreach ($news_languages_overwrite as $news_language)
                                        @if ($news_language->status == 'active')
                                            <div>
                                                <div class="post-media panel overflow-hidden max-w-100% min-w-100%">
                                                    <div class="post-media panel overflow-hidden">
                                                        <label
                                                            class="featured-image bg-gray-25 dark:bg-gray-800 ratio ratio-3x2"
                                                            for="NewsLanguage_{{ $news_language->id }}">
                                                            <img data-src="{{ asset('storage/' . $news_language->image) ?? '' }}"
                                                                src="data:image/gif;base64,R0lGODlhAQABAIAAAAAAAP///yH5BAEAAAAALAAAAAABAAEAAAIBRAA7"
                                                                class="media-cover image uc-transition-scale-up uc-transition-opaque lazy-img"
                                                                alt="{{ $news_language->name }}" />
                                                        </label>
                                                    </div>
                                                    <div>
                                                        <div
                                                            class="post-meta panel hstack fs-7 fw-medium text-gray-900 dark:text-white text-opacity-60 d-flex justify-between">
                                                            <label class="hstack gap-1"
                                                                aria-label="Select news language"
                                                                for="NewsLanguage_{{ $news_language->id }}">
                                                                <i class="bi bi-translate fs-5"
                                                                    aria-label="Translate icon"></i>
                                                                <span for="NewsLanguage_{{ $news_language->id }}"
                                                                    class="h6 dark:text-white">{{ $news_language->name }}</span>
                                                            </label>

                                                            <input
                                                                class="form-check-input language-follow rounded-pill"
                                                                aria-label="Select news language" type="radio"
                                                                id="NewsLanguage_{{ $news_language->id }}"
                                                                data-news-language-id="{{ $news_language->id }}"
                                                                {{ in_array($news_language->id, $subscribedLanguageIds->toArray()) ? 'checked' : ($subscribedLanguageIds->isEmpty() && $news_language->is_active ? 'checked' : '') }}>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        @endif
                                    @endforeach
                                </div>
                            @else
                                <div>
                                    <img class="w-100 h-450px object-contain image uc-transition-opaque"
                                        src="{{ asset('front_end/classic/images/place-holser/no-data.png') }}"
                                        alt="No Data Found">
                                </div>
                            @endif
                        </div>
                        <div class="text-center mt-4">
                            <button id="save-news-languages"
                                class="btn btn-primary">{{ __('frontend-labels.language_settings.save') }}</button>
                        </div>
                    </div>

                    {{-- Web Language Tab --}}
                    <div class="tab-pane fade" id="web-language-content" role="tabpanel"
                        aria-labelledby="web-language-tab">
                        <div class="panel text-center">
                            @if (!empty($web_languages))
                                <div
                                    class="row child-cols-6 sm:child-cols-3 md:child-cols-3 lg:child-cols-3 xl:child-cols-4 col-match gy-4 xl:gy-6 gx-2 sm:gx-4">
                                    @foreach ($web_languages as $language)
                                        <div>
                                            <div class="post-media panel overflow-hidden max-w-100% min-w-100%">
                                                <div class="post-media panel overflow-hidden">
                                                    <label
                                                        class="featured-image bg-gray-25 dark:bg-gray-800 ratio ratio-3x2"
                                                        for="WebLanguage_{{ $language->code }}">
                                                        <img data-src="{{ $language->image }}"
                                                            src="data:image/gif;base64,R0lGODlhAQABAIAAAAAAAP///yH5BAEAAAAALAAAAAABAAEAAAIBRAA7"
                                                            class="media-cover image uc-transition-scale-up uc-transition-opaque lazy-img"
                                                            alt="{{ $language->name }}" />
                                                    </label>
                                                </div>
                                                <div>
                                                    <div
                                                        class="post-meta panel hstack fs-7 fw-medium text-gray-900 dark:text-white text-opacity-60 d-flex justify-between">
                                                        <label class="hstack gap-1"
                                                            for="WebLanguage_{{ $language->code }}">
                                                            <i class="bi bi-translate fs-5"
                                                                aria-label="Translate icon"></i>
                                                            <span for="WebLanguage_{{ $language->code }}"
                                                                class="h6 dark:text-white"
                                                                data-is-active="1">{{ $language->name }}</span>
                                                        </label>

                                                        <input class="form-check-input language-web rounded-pill"
                                                            type="radio" id="WebLanguage_{{ $language->code }}"
                                                            data-web-language-code="{{ $language->code }}"
                                                            {{ $language->code == $finalLanguageCode ? 'checked' : '' }}>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                <div>
                                    <img class="w-100 h-450px object-contain image uc-transition-opaque"
                                        src="{{ asset('front_end/classic/images/place-holser/no-data.png') }}"
                                        alt="No Data Found">
                                </div>
                            @endif
                        </div>
                        <div class="text-center mt-4">
                            <button id="save-web-languages"
                                class="btn btn-primary">{{ __('frontend-labels.language_settings.save') }}</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endif
{{-- <><><><><><><><><><> END MERGED LANGUAGE SELECT MODEL <><><><><><><><><><>  --}}

{{-- <><><><><><><><><><> START WEBLANGUAGE SELECT MODEL (Keep Original) <><><><><><><><><><>  --}}
@if (count($web_languages) > 1)
<div id="web-language-modal" class="uc-modal" data-uc-modal="overlay: true">
    <div class="uc-modal-dialog lg:max-w-800px bg-white text-dark dark:bg-gray-800 dark:text-white rounded">
        <button aria-label="Close dialog"
            class="uc-modal-close-default p-0 icon-3 btn border-0 dark:text-white dark:text-opacity-50 hover:text-primary hover:rotate-90 duration-150 transition-all"
            type="button">
            <i class="unicon-close"></i>
        </button>
        <div class="panel p-2 mt-10 text-right">
            <h5 class="mb-3 text-center"><strong
                    class="text-black dark:text-white">{{ __('frontend-labels.language_settings.select_website_language') }}</strong>
            </h5>
            <div class="panel text-center">
                @if (!empty($web_languages))
                    <div
                        class="row child-cols-6 sm:child-cols-3 md:child-cols-3 lg:child-cols-3 xl:child-cols-4 col-match gy-4 xl:gy-6 gx-2 sm:gx-4">
                        @foreach ($web_languages as $language)
                            <div>
                                <div class="post-media panel overflow-hidden max-w-100% min-w-100%">
                                    <div class="post-media panel overflow-hidden">
                                        <label class="featured-image bg-gray-25 dark:bg-gray-800 ratio ratio-3x2"
                                            for="WebLanguage_{{ $language->code }}">
                                            <img src="{{ $language->image }}"
                                                class="media-cover image uc-transition-scale-up uc-transition-opaque"
                                                alt="{{ $language->name }}" />
                                        </label>
                                    </div>
                                    <div>
                                        <div
                                            class="post-meta panel hstack fs-7 fw-medium text-gray-900 dark:text-white text-opacity-60 d-flex justify-between">
                                            <label class="hstack gap-1" for="WebLanguage_{{ $language->code }}">
                                                <i class="bi bi-translate fs-5" aria-label="Translate icon"></i>
                                                <span for="WebLanguage_{{ $language->code }}"
                                                    class="h6 dark:text-white"
                                                    data-is-active="1">{{ $language->name }}</span>
                                            </label>

                                            <input class="form-check-input language-web rounded-pill" type="radio"
                                                id="WebLanguage_{{ $language->code }}"
                                                data-web-language-code="{{ $language->code }}"
                                                {{ $language->code == $finalLanguageCode ? 'checked' : '' }}>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div>
                        <img class="w-100 h-450px object-contain image uc-transition-opaque"
                            src="{{ asset('front_end/classic/images/place-holser/no-data.png') }}"
                            alt="No Data Found">
                    </div>
                @endif
            </div>
            <div class="text-center mt-4">
                <button id="save-web-languages"
                    class="btn btn-primary">{{ __('frontend-labels.language_settings.save') }}</button>
            </div>
        </div>
    </div>
</div>
@endif
{{-- <><><><><><><><><><> END WEBLANGUAGE SELECT MODEL <><><><><><><><><><>  --}}

{{-- <><><><><><><><><><> START LOGIN OR REGISTRATION MODEL <><><><><><><><><><>  --}}
<div id="uc-account-modal" data-uc-modal="overlay: true">
    <div class="uc-modal-dialog lg:max-w-500px bg-white text-dark dark:bg-gray-800 dark:text-white rounded">
        <button aria-label="Close dialog"
            class="uc-modal-close-default p-0 icon-3 btn border-0 dark:text-white dark:text-opacity-50 hover:text-primary hover:rotate-90 duration-150 transition-all"
            type="button">
            <i class="unicon-close"></i>
        </button>
        <div class="panel vstack gap-2 md:gap-4 text-center">
            <ul class="account-tabs-nav nav-x justify-center h6 py-2 border-bottom d-none"
                data-uc-switcher="animation: uc-animation-slide-bottom-small, uc-animation-slide-top-small">
                <li><a href="#">{{ __('frontend-labels.auth.sign_in') }}</a></li>
                <li><a href="#">{{ __('frontend-labels.auth.sign_up') }}</a></li>
                <li><a href="#">{{ __('frontend-labels.auth.reset_password') }}</a></li>
                <li><a href="#">{{ __('frontend-labels.auth.terms_of_use') }}</a></li>
            </ul>
            <div
                class="account-tabs-content uc-switcher px-3 lg:px-4 py-4 lg:py-8 m-0 lg:mx-auto vstack justify-center items-center">
                <div class="w-100">
                    <div class="panel vstack justify-center items-center gap-2 sm:gap-4 text-center">
                        <h4 class="h5 lg:h4 m-0">{{ __('frontend-labels.frontend_login.login_button') }}</h4>
                        <div class="panel vstack gap-2 w-100 sm:w-350px mx-auto">
                            <form method="POST" action="{{ route('user.login') }}" id="login-modle-form"
                                class="vstack gap-2">
                                @csrf
                                <div class="mb-1">
                                    <input type="email" name="email" id="login-email"
                                        placeholder="{{ __('frontend-labels.frontend_login.email_placeholder') }}"
                                        class="form-control form-control-sm h-40px w-full fs-6 bg-white dark:bg-gray-800 dark:border-white dark:border-opacity-15">
                                    <span id="email-login-error"
                                        class="hstack text-danger fw-bold sm:fs-6 mt-1 d-none fs-7"
                                        data-email-required="{{ __('frontend-labels.validation.email_required') }}"
                                        data-email-invalid="{{ __('frontend-labels.validation.email_invalid') }}">
                                    </span>
                                </div>

                                <div class="mb-1">
                                    <input type="password" name="password" id="login-password"
                                        placeholder="{{ __('frontend-labels.frontend_login.password_placeholder') }}"
                                        class="form-control form-control-sm h-40px w-full fs-6 bg-white dark:bg-gray-800 dark:border-white dark:border-opacity-15"
                                        autocomplete="new-password">
                                    <span id="password-login-error"
                                        class="hstack text-danger fw-bold sm:fs-6 mt-1 d-none fs-7"
                                        data-password-required="{{ __('frontend-labels.validation.password_required') }}"
                                        data-password-min="{{ __('frontend-labels.validation.password_min') }}">
                                    </span>
                                </div>
                                <div class="hstack justify-between text-start">
                                    <div class="form-check text-start">
                                        <input id="form_remember_me"
                                            class="form-check-input      bg-white dark:bg-opacity-0 dark:text-white dark:border-gray-300 dark:border-opacity-30"
                                            type="checkbox" name="remember">
                                        <label for="form_remember_me"
                                            class="hstack justify-between form-check-label fs-6">{{ __('frontend-labels.frontend_login.remember_me') }}?</label>
                                    </div>
                                    <a href="{{ route('password.request') }}"
                                        class="uc-link fs-6">{{ __('frontend-labels.frontend_login.forgot_password') }}</a>
                                </div>


                                <button class="btn btn-primary btn-sm lg:mt-1"
                                    type="submit">{{ __('frontend-labels.frontend_login.login_button') }}</button>
                            </form>
                            <div class="panel h-24px">
                                <hr class="position-absolute top-50 start-50 translate-middle hr m-0 w-100">
                                <span
                                    class="position-absolute top-50 start-50 translate-middle px-1 fs-7 text-uppercase bg-white dark:bg-gray-800">Or</span>
                            </div>
                            <div id="firebase-config" style="display:none"
                                data-config="{{ base64_encode(json_encode($firebaseConfig ?? [])) }}"></div>

                            <div class="hstack gap-2">
                                <a id="google-login-btn"
                                    class="hstack items-center justify-center flex-1 gap-1 h-40px text-none rounded border border-gray-900
     dark:bg-gray-800 dark:border-white dark:border-opacity-15 border-opacity-10">
                                    <i class="icon icon-1 unicon-logo-google"></i>
                                    <span>{{ __('frontend-labels.auth.sign_in_google') }}</span>
                                </a>
                            </div>

                            <div id="google-login-error" style="color:red; margin-top:0.5rem; display:none;"></div>
                        </div>
                        <p class="fs-7 sm:fs-6">{{ __('frontend-labels.frontend_login.dont_have_account') }} <a
                                class="uc-link" href="#"
                                data-uc-switcher-item="1">{{ __('frontend-labels.frontend_login.register_now') }}</a>
                        </p>
                    </div>
                </div>
                <div class="w-100">
                    <div class="panel vstack justify-center items-center gap-2 sm:gap-4 text-center">
                        <h4 class="h5 lg:h4 m-0">{{ __('frontend-labels.register.create_account_title') }}</h4>
                        <div class="panel vstack gap-2 w-100 sm:w-350px mx-auto">
                            <form class="vstack gap-2" action="{{ route('user.register') }}"
                                id="register-user-form" method="POST">
                                @csrf
                                <div>
                                    <input
                                        class="form-control form-control-sm h-40px w-full fs-6 bg-white dark:bg-gray-800 dark:border-white dark:border-opacity-15"
                                        name="name" id="name-register" type="text"
                                        placeholder="{{ __('frontend-labels.register.placeholder_name') }}">
                                    <span class="hstack text-danger fw-bold d-none fs-7 sm:fs-6"
                                        id="name-register-error"></span>
                                </div>
                                <div>
                                    <input
                                        class="form-control form-control-sm h-40px w-full fs-6 bg-white dark:bg-gray-800 dark:border-white dark:border-opacity-15"
                                        name="email" id="email-register" type="email"
                                        placeholder="{{ __('frontend-labels.register.placeholder_email') }}">
                                    <span class="hstack text-danger fw-bold d-none fs-7 sm:fs-6"
                                        id="email-register-error"></span>
                                </div>
                                <div>
                                    <input
                                        class="form-control form-control-sm h-40px w-full fs-6 bg-white dark:bg-gray-800 dark:border-white dark:border-opacity-15"
                                        name="password" id="password-register" type="password"
                                        placeholder="{{ __('frontend-labels.register.placeholder_password') }}"
                                        autocomplete="new-password">
                                    <span class="hstack text-danger fw-bold d-none fs-7 sm:fs-6"
                                        id="password-register-error"></span>
                                </div>
                                <div>
                                    <input
                                        class="form-control form-control-sm h-40px w-full fs-6 bg-white dark:bg-gray-800 dark:border-white dark:border-opacity-15"
                                        name="password_confirmation" id="confirm-password-register" type="password"
                                        placeholder="{{ __('frontend-labels.register.placeholder_confirm_password') }}"
                                        autocomplete="new-password">
                                    <span class="hstack text-danger fw-bold d-none sm:fs-6"
                                        id="confirm-password-register-error"></span>
                                </div>
                                <div class="hstack text-start">
                                    <div class="form-check text-start">
                                        <input id="input_checkbox_accept_terms"
                                            class="form-check-input rounded-0 dark:bg-gray-800 dark:border-white dark:border-opacity-15"
                                            name="accept_terms" type="checkbox">
                                        <label for="input_checkbox_accept_terms"
                                            class="hstack justify-between form-check-label fs-7 sm:fs-6">
                                            {{ __('frontend-labels.register.accept_terms_label') }} <a href="#"
                                                class="uc-link ms-narrow"
                                                data-uc-switcher-item="3">{{ __('frontend-labels.register.terms_of_use') }}</a>.
                                        </label>
                                        <span class="text-danger fw-bold d-none"
                                            id="check_terms">{{ __('frontend-labels.register.please_accept_terms') }}</span>
                                    </div>
                                </div>
                                <button class="btn btn-primary btn-sm lg:mt-1" id="register-form-button"
                                    type="submit">{{ __('frontend-labels.register.register_button') }}</button>
                            </form>
                        </div>
                        <p class="fs-7 sm:fs-6">{{ __('frontend-labels.register.already_have_account') }} <a
                                class="uc-link" href="#"
                                data-uc-switcher-item="0">{{ __('frontend-labels.register.login_link') }}</a></p>
                    </div>
                </div>
                <div class="w-100">
                    <div class="panel vstack justify-center items-center gap-2 sm:gap-4 text-center">
                        <h4 class="h5 lg:h4 m-0">Reset password</h4>
                        <div class="panel w-100 sm:w-350px">
                            <form class="vstack gap-2">
                                <input
                                    class="form-control form-control-sm h-40px w-full fs-6 bg-white dark:bg-gray-800 dark:border-white dark:border-opacity-15"
                                    type="email" placeholder="Your email" required>
                                <div class="form-check text-start">
                                    <input
                                        class="form-check-input rounded-0 dark:bg-gray-800 dark:border-white dark:border-opacity-15"
                                        type="checkbox" id="inputCheckVerify" required>
                                    <label class="form-check-label fs-7 sm:fs-6" for="inputCheckVerify"> <span>I'm not
                                            a robot</span>. </label>
                                </div>
                                <button class="btn btn-primary btn-sm lg:mt-1" type="submit">Reset a
                                    password</button>
                            </form>
                        </div>
                        <p class="fs-7 sm:fs-6 mt-2 sm:m-0">Remember your password? <a class="uc-link" href="#"
                                data-uc-switcher-item="0">Log in</a></p>
                    </div>
                </div>
                <div class="w-100">
                    <div class="panel vstack justify-center items-center gap-2 sm:gap-4">
                        <h4 class="h5 lg:h4 m-0">Terms and conditions</h4>
                        <div class="page-content panel fs-6 text-start max-h-400px overflow-scroll">
                            {!! $termsOfCondition->value ?? '' !!}</div>
                        <p class="fs-7 sm:fs-6">Do you agree to our terms? <a class="uc-link" href="#"
                                data-uc-switcher-item="1">Sign up</a></p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
{{-- <><><><><><><><><><> END LOGIN OR REGISTRATION MODEL <><><><><><><><><><>  --}}

{{-- <><><><><><><><><><> START GDPR MODEL <><><><><><><><><><>  --}}
@if ($cookiesPopupStatus && $cookiesPopupStatus?->value == 1)
    <div id="uc-gdpr-notification" class="uc-gdpr-notification uc-notification uc-notification-bottom-left lg:m-2">
        <div class="uc-notification-message">
            <a id="uc-close-gdpr-notification" class="uc-notification-close" data-uc-close></a>
            <h2 class="h5 ft-primary fw-bold -ls-1 m-0">{{ __('frontend-labels.gdpr.title') }}</h2>
            <p class="fs-7 mt-1 mb-2">
                {{ __('frontend-labels.gdpr.description') }}
                <a href="{{ url('/privacy-policies') }}"
                    class="uc-link text-underline">{{ __('frontend-labels.gdpr.privacy_policy') }}</a>,
                {{ __('frontend-labels.gdpr.and') }}
                <a href="{{ url('/terms-and-condition') }}"
                    class="uc-link text-underline">{{ __('frontend-labels.gdpr.terms_of_service') }}</a>.
            </p>
            <button class="btn btn-sm btn-primary"
                id="uc-accept-gdpr">{{ __('frontend-labels.gdpr.accept') }}</button>
        </div>
    </div>
@endif
{{-- <><><><><><><><><><> END GDPR MODEL <><><><><><><><><><>  --}}

{{-- <><><><><><><><><><> START THEME CHANGE BUTTOM CODE <><><><><><><><><><>  --}}
<div class="backtotop-wrap position-fixed bottom-0 end-0 z-99 m-2 vstack">
    <div class="darkmode-trigger cstack w-40px h-40px rounded-circle text-none bg-gray-100 dark:bg-gray-700 dark:text-white"
        data-darkmode-toggle="">
        <label class="switch">
            <span class="sr-only">Dark mode toggle</span>
            <input type="checkbox">
            <span class="slider fs-5"></span>
        </label>
    </div>
    <a class="btn btn-sm bg-primary text-white w-40px h-40px rounded-circle" href="to_top" data-uc-backtotop
        aria-label="Back to top">
        <i class="icon-2 unicon-chevron-up"></i>
    </a>
</div>
{{-- <><><><><><><><><><> END THEME CHANGE BUTTOM CODE <><><><><><><><><><>  --}}

{{-- <><><><><><><><><><> START HEADER CODE <><><><><><><><><><>  --}}
<header class="uc-header header-seven uc-navbar-sticky-wrap z-999"
    data-uc-sticky="sel-target: .uc-navbar-container; cls-active: uc-navbar-sticky; cls-inactive: uc-navbar-transparent; end: !*;">
    <nav class="uc-navbar-container text-gray-900 dark:text-white fs-6 z-1">
        <div class="uc-top-navbar panel z-3 overflow-hidden bg-primary-600 swiper-parent"
            data-uc-navbar=" animation: uc-animation-slide-top-small; duration: 150;">
            <div class="container container-full">
                <div class="uc-navbar-item">
                    <div class="swiper swiper-ticker swiper-ticker-sep px-2"
                        data-uc-swiper="items: auto; gap: 32; center: true; center-bounds: true; autoplay: 10000; speed: 10000; autoplay-delay: 0.1; loop: true; allowTouchMove: false; freeMode: true; autoplay-disableOnInteraction: true;">
                        <div class="swiper-wrapper">
                            @foreach ($headerPosts as $headerpost)
                                <div class="swiper-slide text-white">
                                    <div class="type-post post panel">
                                        <a href="{{ url('posts/' . $headerpost->slug) }}"
                                            class="fs-7 fw-normal text-none text-inherit">{{ $headerpost->title }}</a>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="uc-center-navbar panel hstack z-2 min-h-48px d-none lg:d-flex"
            data-uc-navbar=" animation: uc-animation-slide-top-small; duration: 150;">
            <div class="container max-w-xl">
                <div class="navbar-container hstack border-bottom">
                    <div class="uc-navbar-center gap-2 lg:gap-3 flex-1">
                        <ul class="uc-navbar-nav gap-3 justify-between flex-1 fs-6 fw-bold">
                            <li>
                                <a href="#" aria-label="Finance"><span
                                        class="icon-1 unicon-finance"></span></a>
                                <div id="newsletter-dropdown"
                                    class="uc-navbar-dropdown ft-primary text-unset p-3 pb-4 rounded-0 hide-scrollbar"
                                    data-uc-drop=" offset: 0; boundary: !.navbar-container; stretch: x; animation: uc-animation-slide-top-small; duration: 150;">
                                    <div class="row child-cols col-match g-2">
                                        <div class="col-2">
                                            <ul class="uc-nav uc-navbar-dropdown-nav">
                                                @foreach ($topics as $index => $topic)
                                                    @if ($index < 0 || $index > 4)
                                                        @continue
                                                    @endif
                                                    <li><a
                                                            href="{{ url('topics/' . $topic->slug) }}">{{ $topic->name }}</a>
                                                    </li>
                                                @endforeach
                                            </ul>
                                        </div>
                                        <div class="col-2">
                                            <ul class="uc-nav uc-navbar-dropdown-nav">
                                                @foreach ($topics as $index => $topic)
                                                    @if ($index < 5 || $index > 9)
                                                        @continue
                                                    @endif
                                                    <li><a
                                                            href="{{ url('topics/' . $topic->slug) }}">{{ $topic->name }}</a>
                                                    </li>
                                                @endforeach
                                            </ul>
                                        </div>
                                        <div class="col-2">
                                            <ul class="uc-nav uc-navbar-dropdown-nav">

                                            </ul>
                                        </div>
                                        <div class="col-2">
                                            <ul class="uc-nav uc-navbar-dropdown-nav">

                                            </ul>
                                        </div>
                                        <div>
                                            <div class="uc-navbar-newsletter panel vstack">
                                                <h6 class="fs-6 ft-tertiary fw-medium">
                                                    {{ $socialsettings['app_name'] ?? 'Newshunt' }}</h6>
                                                <form class="hstack gap-1 bg-gray-300 bg-opacity-10" method="post"
                                                    action="{{ route('subscribe.store') }}">
                                                    @csrf
                                                    <input type="email" name="email" id="subscriber_email"
                                                        class="form-control-plaintext form-control-xs fs-6 dark:text-white"
                                                        data-email-required="{{ __('frontend-labels.home.email_required') }}"
                                                        data-email-taken="{{ __('frontend-labels.home.email_taken') }}"
                                                        data-email-invalid="{{ __('frontend-labels.home.email_invalid') }}"
                                                        data-email-subscribed="{{ __('frontend-labels.home.email_subscribed') }}"
                                                        placeholder="{{ __('frontend-labels.home.your_email_address') }}..">

                                                    <button type="button" id="web-subscriber-button"
                                                        class="btn btn-sm btn-primary fs-8 rounded-0 d-flex align-items-center gap-1 px-3 w-100">
                                                        <i
                                                            class="bi bi-envelope-plus"></i>{{ __('frontend-labels.home.subscribe') }}
                                                    </button>
                                                    <div id="subscriber-error-top" class="alert alert-danger d-none">
                                                    </div>
                                                </form>
                                                <p class="fs-7 mt-1">
                                                    {{ __('frontend-labels.home.dont_worry_we_dont_spam') }}</p>
                                                <ul class="nav-x gap-2 mt-3">
                                                    <li><a href="{{ $socialsettings['instagram_link'] ?? '' }}"><i
                                                                class="icon icon-2 unicon-logo-instagram"
                                                                aria-label="Instagram"></i></a></li>
                                                    <li><a href="{{ $socialsettings['x_link'] ?? '' }}"><i
                                                                class="icon icon-2 unicon-logo-x-filled"
                                                                aria-label="X"></i></a></li>
                                                    <li><a href="{{ $socialsettings['facebook_link'] ?? '' }}"><i
                                                                class="icon icon-2 unicon-logo-facebook"
                                                                aria-label="Facebook"></i></a></li>
                                                    <li><a href="{{ $socialsettings['linkedin_link'] ?? '' }}"><i
                                                                class="icon icon-2 unicon-logo-linkedin"
                                                                aria-label="LinkedIn"></i></a></li>
                                                    <li><a href="{{ $socialsettings['pinterest_link'] ?? '' }}"><i
                                                                class="icon icon-2 unicon-logo-pinterest"
                                                                aria-label="Pinterest"></i></a></li>
                                                </ul>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </li>
                            <li>
                                <a href="#">{{ __('frontend-labels.channels.title') }} <span
                                        data-uc-navbar-parent-icon></span></a>
                                <div class="uc-navbar-dropdown ft-primary text-unset p-3 pb-4 rounded-0 hide-scrollbar"
                                    data-uc-drop=" offset: 0; boundary: !.navbar-container; stretch: x; animation: uc-animation-slide-top-small; duration: 150;">
                                    <div class="row col-match g-2">
                                        <div class="w-1/5">
                                            <div class="uc-navbar-switcher-nav border-end">
                                                <ul class="uc-tab-left fs-6"
                                                    data-uc-tab="connect: #uc-navbar-switcher-tending; animation: uc-animation-slide-right-small, uc-animation-slide-left-small">

                                                    @foreach ($channels as $channel)
                                                        <li class="d-flex justify-between align-items-center">
                                                            <a class="text-start"
                                                                href="#">{{ $channel->name }}</a>
                                                            <a href="{{ url('channels/' . $channel->slug) }}"><i
                                                                    class="bi bi-chevron-right"></i></a>
                                                        </li>
                                                    @endforeach
                                                </ul>
                                            </div>
                                        </div>
                                        <div class="w-4/5">
                                            <div id="uc-navbar-switcher-tending"
                                                class="uc-navbar-switcher uc-switcher">
                                                @foreach ($channels as $channel)
                                                    <div>
                                                        <div class="row child-cols col-match g-2">
                                                            @foreach ($channel->posts as $fistChannelPost)
                                                                <div>
                                                                    <article
                                                                        class="post type-post panel uc-transition-toggle vstack gap-1">
                                                                        <div class="post-media panel overflow-hidden">
                                                                            <div
                                                                                class="featured-image bg-gray-25 dark:bg-gray-800 ratio ratio-16x9">
                                                                                <a href="{{ url('posts/' . $fistChannelPost->slug) }}"
                                                                                    class="position-cover">
                                                                                    @if ($fistChannelPost->type == 'post')
                                                                                        <img class="media-cover image uc-transition-scale-up uc-transition-opaque lazy-img"
                                                                                            src="data:image/gif;base64,R0lGODlhAQABAIAAAAAAAP///yH5BAEAAAAALAAAAAABAAEAAAIBRAA7"
                                                                                            data-src="{{ $fistChannelPost->image ?? $defaultImage }}"
                                                                                            alt="{{ $fistChannelPost->title ?? '' }}"
                                                                                            title="{{ $fistChannelPost->title ?? '' }}"
                                                                                            loading="lazy"
                                                                                            fetchpriority="high">
                                                                                    @elseif($fistChannelPost->type == 'audio')
                                                                                        <img class="media-cover image uc-transition-scale-up uc-transition-opaque lazy-img"
                                                                                            src="data:image/gif;base64,R0lGODlhAQABAIAAAAAAAP///yH5BAEAAAAALAAAAAABAAEAAAIBRAA7"
                                                                                            data-src="{{ $fistChannelPost->image ?? $defaultImage }}"
                                                                                            alt="{{ $fistChannelPost->title ?? '' }}"
                                                                                            title="{{ $fistChannelPost->title ?? '' }}"
                                                                                            loading="lazy"
                                                                                            fetchpriority="high">
                                                                                        <div
                                                                                            class="post-category hstack gap-narrow justify-center align-items-center text-white">
                                                                                            <a class="text-none"
                                                                                                href="{{ url('posts/' . $fistChannelPost->slug) }}"
                                                                                                title="{{ $fistChannelPost->title }}"><i
                                                                                                    class="bi bi-play-circle font-size-45"></i></a>
                                                                                        </div>
                                                                                    @elseif($fistChannelPost->type == 'youtube' || $fistChannelPost->type == 'video')
                                                                                        <img class="media-cover image uc-transition-scale-up uc-transition-opaque lazy-img"
                                                                                            src="data:image/gif;base64,R0lGODlhAQABAIAAAAAAAP///yH5BAEAAAAALAAAAAABAAEAAAIBRAA7"
                                                                                            data-src="{{ $fistChannelPost->video_thumb ?? $defaultImage }}"
                                                                                            alt="{{ $fistChannelPost->title ?? '' }}"
                                                                                            title="{{ $fistChannelPost->title ?? '' }}"
                                                                                            loading="lazy"
                                                                                            fetchpriority="high">
                                                                                        <div
                                                                                            class="post-category hstack gap-narrow justify-center align-items-center text-white">
                                                                                            <a class="text-none"
                                                                                                href="{{ url('posts/' . $fistChannelPost->slug) }}"
                                                                                                title="{{ $fistChannelPost->title }}"><i
                                                                                                    class="bi bi-play-circle font-size-45"></i></a>
                                                                                        </div>
                                                                                    @endif
                                                                                </a>
                                                                            </div>
                                                                        </div>
                                                                        <div
                                                                            class="post-header panel vstack gap-narrow">
                                                                            <h3
                                                                                class="post-title h6 m-0 text-truncate-2">
                                                                                <a class="text-none hover:text-primary duration-150"
                                                                                    href="{{ url('posts/' . $fistChannelPost->slug) }}"
                                                                                    title="{{ $fistChannelPost->title ?? '' }}">{{ $fistChannelPost->title ?? '' }}</a>
                                                                            </h3>
                                                                            <div
                                                                                class="post-meta panel hstack justify-start gap-1 fs-7 ft-tertiary fw-medium text-gray-900 dark:text-white text-opacity-60 d-none md:d-flex z-1 d-none md:d-block">
                                                                                <div>
                                                                                    <div
                                                                                        class="post-date hstack gap-narrow">
                                                                                        <span
                                                                                            title="{{ $fistChannelPost->publish_date_news }}">{{ $fistChannelPost->publish_date ?? $fistChannelPost->pubdate }}</span>
                                                                                    </div>
                                                                                </div>
                                                                                <div>
                                                                                    <a href="{{ url('posts/' . $fistChannelPost->slug) }}#comment-form"
                                                                                        class="post-comments text-none hstack gap-narrow"
                                                                                        title="commets">

                                                                                        <i
                                                                                            class="icon-narrow unicon-chat ms-1"></i>
                                                                                        <span>{{ $fistChannelPost->comment ?? '' }}</span>

                                                                                        <i class="bi bi-eye fs-5 ms-1"
                                                                                            title="Views"></i>
                                                                                        <span
                                                                                            title="Views">{{ $fistChannelPost->view_count }}</span>

                                                                                        <i
                                                                                            class="bi bi-heart-fill ms-1"></i>
                                                                                        <span>{{ $fistChannelPost->reaction ?? '' }}</span>
                                                                                    </a>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    </article>
                                                                </div>
                                                            @endforeach
                                                        </div>
                                                        <div class="text-end mt-1">
                                                            <a href="{{ url('channels/' . $channel->slug) }}"
                                                                class="text-black dark:text-white text-none fw-bold"
                                                                title=" {{ __('frontend-labels.common.see_more') }}">
                                                                {{ __('frontend-labels.common.see_more') }} <i
                                                                    class="bi bi-chevron-right"></i></a>
                                                        </div>
                                                    </div>
                                                @endforeach
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </li>

                            <li>
                                <a href="{{ url('webstories') }}">{{ __('frontend-labels.web_stories.title') }}</a>
                            </li>
                            @foreach ($topics as $topic)
                                <li>
                                    <a href="#">{{ $topic->name }}<span data-uc-navbar-parent-icon></span></a>
                                    <div class="uc-navbar-dropdown topic-dropdown ft-primary text-unset p-3 pb-4 rounded-0 hide-scrollbar"
                                        data-topic-id="{{ $topic->id }}"
                                        data-uc-drop="offset: 0; boundary: !.navbar-container; stretch: x; animation: uc-animation-slide-top-small; duration: 150;">
                                        <div>
                                            <div class="dropdown-loader w-full text-center py-4">
                                                <i class="bi bi-hourglass-split fs-3"></i>
                                                <p class="m-0 fs-7">Loading...</p>
                                            </div>
                                            <div class="row child-cols col-match g-2 dropdown-content-wrapper">
                                            </div>
                                        </div>
                                        <div class="text-end mt-1">
                                            <a href="{{ url('topics/' . $topic->slug) }}"
                                                class="text-black dark:text-white text-none fw-bold">
                                                {{ __('frontend-labels.common.see_more') }} <i
                                                    class="bi bi-chevron-right"></i></a>
                                        </div>
                                    </div>
                                </li>
                            @endforeach


                            <li>
                                <a href="#" aria-label="More options"><i
                                        class="icon-2 fw-medium unicon-overflow-menu-horizontal"></i></a>
                                <div class="uc-navbar-dropdown ft-primary text-unset p-3 rounded-0 hide-scrollbar"
                                    data-uc-drop=" offset: 0; boundary: !.navbar-container; stretch: x; animation: uc-animation-slide-top-small; duration: 150;">
                                    <div class="row child-cols g-4">
                                        <div>
                                            <div class="row child-cols g-4">
                                                <div>
                                                    <ul class="uc-nav uc-navbar-dropdown-nav">
                                                        <li class="uc-nav-header fs-6 ft-tertiary fw-medium mb-1"
                                                            title="{{ __('frontend-labels.channels.title') }}">
                                                            {{ __('frontend-labels.channels.title') }}</li>
                                                        @if (!empty($channels))
                                                            @foreach ($channels as $index => $channel)
                                                                @if ($index < 1 || $index > 3)
                                                                    @continue
                                                                @endif
                                                                <li class="d-flex"
                                                                    title="{{ $channel->name ?? '' }}"><a
                                                                        href="{{ url('channels/' . $channel->slug) }}">{{ $channel->name ?? '' }}</a>
                                                                </li>
                                                            @endforeach
                                                        @endif
                                                        <li class="d-flex"
                                                            title="{{ __('frontend-labels.sponsor_ads.all_channels') }}">
                                                            <a href="{{ url('channels') }}"
                                                                aria-label="View all channels">{{ __('frontend-labels.sponsor_ads.all_channels') }}</a>
                                                        </li>
                                                    </ul>
                                                </div>
                                                <div>
                                                    <ul class="uc-nav uc-navbar-dropdown-nav">
                                                        <li class="uc-nav-header fs-6 ft-tertiary fw-medium mb-1">
                                                            {{ __('frontend-labels.topics.title') }}</li>
                                                        @if (!empty($topics))
                                                            @foreach ($topics as $topic)
                                                                <li title="{{ $topic->name ?? '' }}"><a
                                                                        href="{{ url('topics/' . $topic->slug) }}">{{ $topic->name ?? '' }}</a>
                                                                </li>
                                                            @endforeach
                                                        @endif
                                                        <li class="d-flex"
                                                            title="{{ __('frontend-labels.topics.all_topics') }}"><a
                                                                href="{{ url('topics') }}">{{ __('frontend-labels.topics.all_topics') }}</a>
                                                        </li>
                                                    </ul>
                                                </div>
                                                <div>
                                                    <ul class="uc-nav uc-navbar-dropdown-nav">
                                                        <li class="uc-nav-header fs-6 ft-tertiary fw-medium mb-1">
                                                            {{ __('frontend-labels.home.quick_links') }}</li>
                                                        <li title="{{ __('frontend-labels.posts.all_posts') }}"><a
                                                                href="{{ url('posts') }}"
                                                                aria-label="Browse all posts">{{ __('frontend-labels.posts.all_posts') }}</a>
                                                        </li>
                                                        @if (isset($socialsettings['enable_custom_ads_status']) && $socialsettings['enable_custom_ads_status'] == '1')
                                                            <li
                                                                title="{{ __('frontend-labels.sponsor_ads.title') }}">
                                                                <a
                                                                    href="{{ url('sponsor-ads') }}">{{ __('frontend-labels.sponsor_ads.title') }}</a>
                                                            </li>
                                                        @endif

                                                        @if ($free_trial_status == '0')
                                                            <li title="{{ __('frontend-labels.membership.title') }}">
                                                                <a
                                                                    href="{{ url('membership') }}">{{ __('frontend-labels.membership.title') }}</a>
                                                            </li>
                                                        @endif
                                                        <li
                                                            title="{{ __('frontend-labels.terms_and_conditions.title') }}">
                                                            <a
                                                                href="{{ url('terms-and-condition') }}">{{ __('frontend-labels.terms_and_conditions.title') }}</a>
                                                        </li>
                                                        <li title="{{ __('frontend-labels.privacy_policy.title') }}">
                                                            <a
                                                                href="{{ url('privacy-policies') }}">{{ __('frontend-labels.privacy_policy.title') }}</a>
                                                        </li>
                                                        <li title="{{ __('frontend-labels.contactus.title') }}"><a
                                                                href="{{ url('contact-us') }}">{{ __('frontend-labels.contactus.title') }}</a>
                                                        </li>
                                                        <li title="{{ __('frontend-labels.aboutus.title') }}"><a
                                                                href="{{ url('about-us') }}">{{ __('frontend-labels.aboutus.title') }}</a>
                                                        </li>
                                                        @if (auth()->check())
                                                            <li>
                                                            <li
                                                                title="{{ __('frontend-labels.my-account.account_info') }}">
                                                                <a
                                                                    href="{{ url('my-account') }}">{{ __('frontend-labels.my-account.account_info') }}</a>
                                                            </li>
                                                            <form action="{{ url('logout') }}" method="POST"
                                                                class="d-inline">
                                                                @csrf
                                                                <button type="submit"
                                                                    class="bg-transparent border-0 cursor-pointer text-gray-800 dark:text-white dark:text-opacity-50 p-0 text-start"
                                                                    title="{{ __('frontend-labels.sponsor_ads.logout') }}">{{ __('frontend-labels.sponsor_ads.logout') }}</button>
                                                            </form>
                            </li>
                        @else
                            <li title="{{ __('frontend-labels.frontend_login.login_button') }}"><a
                                    class="uc-account-trigger position-relative btn btn-sm border-0 p-0 gap-narrow duration-0 dark:text-white"
                                    href="#uc-account-modal"
                                    data-uc-toggle>{{ __('frontend-labels.frontend_login.login_button') }}</a></li>
                            <li title="{{ __('frontend-labels.register.title') }}"><a href="#uc-account-modal"
                                    class="open-signup-modal"
                                    data-uc-toggle>{{ __('frontend-labels.register.title') }}</a></li>
                            @endif
                        </ul>
                    </div>
                </div>
            </div>
        </div>
        </div>
        </li>
        </ul>
        </div>
        </div>
        </div>
        </div>

        <div class="uc-bottom-navbar panel z-1">
            <div class="container max-w-xl">
                <div class="uc-navbar min-h-72px lg:min-h-100px"
                    data-uc-navbar=" animation: uc-animation-slide-top-small; duration: 150;">
                    <div class="uc-navbar-left">
                        <a class="uc-menu-trigger icon-2 d-lg-none" href="#uc-menu-panel"
                            data-uc-toggle aria-label="Menu"></a>
                    </div>

                    <div class="uc-navbar-center">
                        <div class="uc-logo">
                            <a href="{{ url('home') }}">
                                {{-- Mobile Logo --}}
                                <div class="d-block d-lg-none">
                                    <div class="d-flex align-items-center">
                                        <div class="uc-navbar-center">
                                            {{-- Dark (on light theme) --}}
                                            <img class="img-fluid w-auto text-dark dark:text-white hover:text-primary transition-color duration-150 d-block dark:d-none header-img-max-height"
                                                src="{{ $dark_logo != null ? url('storage/' . $dark_logo->value) : asset('assets/images/logo/DarkLogo.png') }}"
                                                fetchpriority="high" alt="Light">
                                            {{-- Light (on dark theme) --}}
                                            <img class="img-fluid w-auto text-dark dark:text-white hover:text-primary transition-color duration-150 d-none dark:d-block header-img-max-height"
                                                src="{{ $light_logo != null ? url('storage/' . $light_logo->value) : asset('assets/images/logo/LightLogo.png') }}"
                                                fetchpriority="high" alt="Dark">
                                        </div>
                                    </div>
                                </div>
                                {{-- Desktop Logo --}}
                                <div class="d-none d-lg-block">
                                    {{-- Dark --}}
                                    <img class="{{ $dark_logo_size->value ?? '400px' }}px text-dark dark:text-white hover:text-primary transition-color duration-150 d-block dark:d-none"
                                        src="{{ $dark_logo != null ? url('storage/' . $dark_logo->value) : asset('assets/images/logo/DarkLogo.png') }}"
                                        alt="Light">

                                    {{-- Light --}}
                                    <img class="{{ $light_logo_size->value ?? '400px' }}px text-dark dark:text-white hover:text-primary transition-color duration-150 d-none dark:d-block"
                                        src="{{ $light_logo != null ? url('storage/' . $light_logo->value) : asset('assets/images/logo/LightLogo.png') }}"
                                        alt="Dark">
                                </div>
                            </a>
                        </div>
                    </div>

                    <div class="uc-navbar-right gap-2 lg:gap-3">
                        @if (($news_language_status === 'active' && (count($news_languages_overwrite) > 1 || count($web_languages) > 1)) || ($news_language_status !== 'active' && count($web_languages) > 1))
                            @if ($news_language_status === 'active')
                            <a class="uc-menu-trigger icon-2 d-lg-none" href="#news-language-modal"
                                data-uc-toggle aria-label="Language">
                                <i class="bi bi-translate"></i>
                            </a>
                        @else
                            <a class="uc-menu-trigger icon-2 d-lg-none" href="#web-language-modal" data-uc-toggle
                                aria-label="Language">
                                <i class="bi bi-translate"></i>
                            </a>
                            @endif
                        @endif

                        <a class="uc-menu-trigger icon-2 d-lg-none" href="#uc-search-modal" data-uc-toggle
                            aria-label="Search">
                            <i class="unicon-search"></i>
                        </a>

                        <div class="uc-navbar-item d-none lg:d-inline-flex">
                            @if (auth()->check())
                                <div class="profile-container mt-1">
                                    <img class="w-32px h-32px rounded-circle object-fit-cover pointer-cursor"
                                        src="{{ auth()->user()->profile ?? asset('front_end/classic/images/avatars/04.png') }}"
                                        alt="User Profile" id="profileImage">
                                    <div class="dropdown-content dark:bg-black" id="dropdownMenu">
                                        <a href="{{ url('my-account') }}"
                                            class="dark:bg-gray-100 dark:bg-opacity-5 hover:text-primary dark:text-white"><i
                                                class="bi bi-person-circle"></i>{{ __('frontend-labels.my-account.account_info') }}</a>
                                        <a href="{{ url('my-account/followings') }}"
                                            class="dark:bg-gray-100 dark:bg-opacity-5 hover:text-primary dark:text-white"><i
                                                class="bi bi-youtube"></i>
                                            {{ __('frontend-labels.followings.title') }}</a>
                                        <a href="{{ url('my-account/bookmarks') }}"
                                            class="dark:bg-gray-100 dark:bg-opacity-5 hover:text-primary dark:text-white"><i
                                                class="bi bi-bookmark"></i>
                                            {{ __('frontend-labels.favorite.title') }}</a>
                                        @if ($free_trial_status == '0')
                                            <a href="{{ url('my-account/transaction') }}"
                                                class="dark:bg-gray-100 dark:bg-opacity-5 hover:text-primary dark:text-white"><i
                                                    class="bi bi-wallet2 "></i>
                                                {{ __('frontend-labels.transaction_details.title') }}</a>
                                            
                                            <a href="{{ url('my-account/subscription') }}"
                                                class="dark:bg-gray-100 dark:bg-opacity-5 hover:text-primary dark:text-white"><svg
                                                    width="19px" height="19px" viewBox="0 0 24 24"
                                                    xmlns="http://www.w3.org/2000/svg" fill="#000000">
                                                    <path
                                                        d="M14,6a7.17,7.17,0,0,0-1,.08A4.49,4.49,0,0,0,4,6.5V7A2,2,0,0,0,2,9v9a1.94,1.94,0,0,0,2,2H8.73A8,8,0,1,0,14,6ZM6,6.5a2.51,2.51,0,0,1,5-.24V7H6ZM14,20a6,6,0,1,1,6-6A6,6,0,0,1,14,20Zm-1.5-8v1h4a1,1,0,0,1,1,1v3a1,1,0,0,1-1,1H15v1H13V18H10.5V16h5V15h-4a1,1,0,0,1-1-1V11a1,1,0,0,1,1-1H13V9h2v1h2.5v2Z">
                                                    </path>
                                                </svg> {{ __('frontend-labels.mysubscription.title') }}</a>
                                        @endif
                                        <a href="#" class="dark:bg-gray-100 dark:bg-opacity-5 dark:text-white"
                                            id="logout-link">
                                            <form id="logout-form" action="{{ route('logout') }}" method="POST"
                                                class="d-none">@csrf</form>
                                            <i class="bi bi-box-arrow-right"></i>
                                            {{ __('frontend-labels.sponsor_ads.logout') }}
                                        </a>
                                    </div>
                                </div>
                            @else
                                <a class="uc-account-trigger position-relative btn btn-sm border-0 p-0 gap-narrow duration-0 dark:text-white"
                                    href="#uc-account-modal" data-uc-toggle aria-label="Login">
                                    <i class="icon icon-2 fw-medium unicon-user-avatar" aria-label="User avatar"></i>
                                </a>
                            @endif
                        </div>
                        <div class="uc-navbar-item d-none lg:d-inline-flex">
                            <a class="uc-search-trigger cstack text-none text-dark dark:text-white"
                                href="#uc-search-modal" data-uc-toggle aria-label="Search">
                                <i class="icon icon-2 fw-medium unicon-search" aria-label="Search icon"></i>
                            </a>
                        </div>
                        <div class="uc-navbar-item d-none lg:d-inline-flex">
                            <div class="uc-modes-trigger btn btn-xs w-32px h-32px p-0 border fw-normal rounded-circle dark:text-white hover:bg-gray-25 dark:hover:bg-gray-900"
                                data-darkmode-toggle="">
                                <label class="switch">
                                    <span class="sr-only">Dark toggle</span>
                                    <input type="checkbox">
                                    <span class="slider"></span>
                                </label>
                            </div>
                        </div>

                        @if (($news_language_status === 'active' && (count($news_languages_overwrite) > 1 || count($web_languages) > 1)) || ($news_language_status !== 'active' && count($web_languages) > 1))
                            @if ($news_language_status === 'active')
                            <div class="uc-navbar-item d-none lg:d-inline-flex">
                                <button class="btn btn-sm dark:text-white m-0 p-0 border-none"
                                    data-uc-toggle="#news-language-modal" aria-label="Change news language">
                                    <i class="bi bi-translate fs-5" aria-label="Translate icon"></i>
                                </button>
                            </div>
                        @else
                            <div class="uc-navbar-item d-none lg:d-inline-flex">
                                <button class="btn btn-sm dark:text-white m-0 p-0 border-none"
                                    data-uc-toggle="#web-language-modal" aria-label="Change website language">
                                    <i class="bi bi-translate fs-5" aria-label="Translate icon"></i>
                                </button>
                            </div>
                            @endif
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <input type="hidden" id="weather_api_key"
            value="{{ isset($weather_api_key->value) ? $weather_api_key->value : '' }}">
    </nav>

    @if (isset($header_script))
        {!! $header_script->value !!}
    @endif
    <script>
        window.channelsData = @json($channels);
        window.defaultImage = '{{ $defaultImage }}';
    </script>

</header>
{{-- <><><><><><><><><><> END HEADER CODE <><><><><><><><><><>  --}}
