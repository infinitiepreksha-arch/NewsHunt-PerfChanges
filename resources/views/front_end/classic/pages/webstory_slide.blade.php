<!DOCTYPE html>
<html>
<title>{{ $story->title . ' | News Hunt' }}</title>

<head>
    <meta charset="utf-8">
    <link rel="icon" href="{{ $favicon ?? asset('assets/images/logo/favicon.png') }}" type="image/x-icon" />
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <script async src="https://cdn.ampproject.org/v0.js"></script>
    <link rel="stylesheet" href="https://newshunt.infinitietech.com/front_end/classic/css/custom.css" as="style">

    <link rel="canonical" href="{{ url()->current() }}">
    <script async custom-element="amp-story" src="https://cdn.ampproject.org/v0/amp-story-1.0.js"></script>
    <script async custom-element="amp-story-auto-analytics"
        src="https://cdn.ampproject.org/v0/amp-story-auto-analytics-0.1.js"></script>
    <style amp-boilerplate>
        body {
            -webkit-animation: -amp-start 8s steps(1, end) 0s 1 normal both;
            -moz-animation: -amp-start 8s steps(1, end) 0s 1 normal both;
            -ms-animation: -amp-start 8s steps(1, end) 0s 1 normal both;
            animation: -amp-start 8s steps(1, end) 0s 1 normal both
        }

        @-webkit-keyframes -amp-start {
            from {
                visibility: hidden
            }

            to {
                visibility: visible
            }
        }

        @-moz-keyframes -amp-start {
            from {
                visibility: hidden
            }

            to {
                visibility: visible
            }
        }

        @-ms-keyframes -amp-start {
            from {
                visibility: hidden
            }

            to {
                visibility: visible
            }
        }

        @-o-keyframes -amp-start {
            from {
                visibility: hidden
            }

            to {
                visibility: visible
            }
        }

        @keyframes -amp-start {
            from {
                visibility: hidden
            }

            to {
                visibility: visible
            }
        }
    </style>
    <noscript>
        <style amp-boilerplate>
            body {
                -webkit-animation: none;
                -moz-animation: none;
                -ms-animation: none;
                animation: none
            }
        </style>
    </noscript>
</head>

<body class="amp_story">

    <div id="wrapper" 
         data-page="webstory-viewer"
         data-daily-limit-value="{{ $freeTrialLimit }}" 
         data-redirect-url="{{ route('webstories.index') }}"
         data-is-daily-eligible="{{ $isDailyLimitEligible ? '1' : '0' }}"
         data-subscription-limit="{{ $subscriptionLimitReached ? '1' : '0' }}"
         data-has-subscription="{{ (auth()->user() && auth()->user()->subscription) ? '1' : '0' }}"
         data-content-type="story">
    @if ($story && $story->story_slides->isNotEmpty())
        <amp-story standalone title="{{ $story->title }}"
            publisher-logo-src="{{ asset('assets/images/logo/LightLogo.png') }}"
            poster-portrait-src="{{ optional($story->story_slides->first())->image ? asset('storage/' . $story->story_slides->first()->image) : asset('assets/images/no_image_available.png') }}">
            @foreach ($story->story_slides as $index => $slide)
                @php
                    $animationDetails = $animations[$slide->id] ?? [];
                @endphp
                <amp-story-page id="slide-{{ $index }}" auto-advance-after="5s">
                    <amp-story-grid-layer template="fill">
                        <div class="image-container">
                            <amp-img src="{{ asset('storage/' . $slide->image) }}" width="720" height="1280"
                                layout="responsive" alt="{{ $story->title }} - Slide {{ $index + 1 }}"
                                animate-in="{{ $animationDetails['image']['type'] == 'slide-in' ? 'fly-in-left' : $animationDetails['image']['type'] ?? 'fade-in' }}"
                                animate-in-delay="{{ $animationDetails['image']['delay'] ?? '0' }}s"
                                animate-in-duration="{{ $animationDetails['image']['duration'] ?? '1' }}s"
                                data-amp-story-animation="fade-in">
                            </amp-img>

                            <div class="overlay story-overlay-css"></div>
                        </div>
                    </amp-story-grid-layer>

                    <amp-story-grid-layer template="thirds">
                        @if ($index === 0)
                            <div class="amp-story-logo">
                                @if (!empty($socialsettings['light_logo']))
                                    <amp-img src="{{ asset('storage/' . $socialsettings['light_logo']) }}" width="100"
                                        height="20" layout="intrinsic" alt="Logo" animate-in="fade-in"
                                        animate-in-delay="0.3s">
                                    </amp-img>
                                @endif
                            </div>

                            <div grid-area="lower-third">
                                <p class="title-text story-lower-third" animate-in="fly-in-bottom"
                                    animate-in-delay="0.5s" animate-in-duration="0.8s">
                                    {{ $story->title }}
                                </p>
                            </div>
                        @else
                            <div grid-area="middle-third" class="content story-middle-third-content">
                                <p class="title-text story-middle-third"
                                    animate-in="{{ $animationDetails['title']['type'] == 'slide-down' ? 'fly-in-top' : 'fly-in-bottom' }}"
                                    animate-in-delay="{{ $animationDetails['title']['delay'] ?? '1.3' }}s"
                                    animate-in-duration="{{ $animationDetails['title']['duration'] ?? '1' }}s"
                                    data-amp-story-animation="true">
                                    {{ $slide->title }}
                                </p>

                            </div>
                            <div grid-area="middle-third " class="content story-middle2-css">

                                <p class="description-text story-description-css"
                                    animate-in="{{ $animationDetails['description']['type'] == 'slide-down' ? 'fly-in-top' : 'fly-in-bottom' }}"
                                    animate-in-delay="{{ $animationDetails['description']['delay'] ?? '1.3' }}s"
                                    animate-in-duration="{{ $animationDetails['description']['duration'] ?? '1' }}s"
                                    data-amp-story-animation="true">
                                    {{ $slide->description }}
                                </p>
                            </div>
                        @endif
                    </amp-story-grid-layer>
                </amp-story-page>
            @endforeach

            @if ($nextStory && optional($nextStory->story_slides->first())->image)
                <amp-story-page id="next-story-preview">
                    <amp-story-grid-layer template="fill">
                        <amp-img src="{{ asset('storage/' . $nextStory->story_slides->first()->image) }}"
                            width="720" height="1280" layout="responsive" alt="{{ $nextStory->title }}">
                        </amp-img>
                    </amp-story-grid-layer>
                    <amp-story-grid-layer template="thirds">
                        <div grid-area="lower-third">
                            <p class="next-story-title  next-story-title-css" animate-in="fade-in"
                                animate-in-delay="0.5s">
                                {{ $nextStory->title }}
                            </p>
                        </div>
                    </amp-story-grid-layer>
                    <amp-story-cta-layer>
                        <a href="{{ route('webstories.show', ['topic' => $nextStory->topic->slug, 'story' => $nextStory->slug]) }}"
                            class="button-link bg-red">
                            {{ __('frontend-labels.webstory.read_now') }}
                        </a>
                    </amp-story-cta-layer>
                </amp-story-page>
            @endif
        </amp-story>
    @else
        <div class="error-message">
            <h1>{{ __('frontend-labels.webstory.no_webstories_found') }}</h1>
        </div>
    @endif
    </div>
    <!-- Load jQuery and Bootstrap for limit tracking functionality -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="{{ asset('front_end/' . $theme . '/js/libs/bootstrap.bundle.min.js') }}"></script>
    <script defer src="{{ asset('front_end/' . $theme . '/js/custom/custom.js') }}"></script>
</body>

</html>
