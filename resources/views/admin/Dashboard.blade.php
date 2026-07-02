@extends('admin.layouts.main')

@section('title')
    {{ __('message.DASHBOARD') }}
@endsection

@section('pre-title')
    {{ __('HOME') }}
@endsection

@section('page-title')
    <div class="row g-2 align-items-center">
        <div class="col">
            <div class="page-pretitle">
                @yield('pre-title')
            </div>
            <h2 class="page-title">
                @yield('title')
            </h2>
        </div>
        <div class="col-auto ms-auto d-print-none"></div>
    </div>
@endsection

@section('content')
    {{-- Start Card --}}
    <div class="page-body">
        <div class="container-xl">
            <div class="row row-deck row-cards ">
                <div class="col-sm-6 col-lg-3">
                    <div class="card admin_cards">
                        <a href="{{ route('users.index') }}" class="text-decoration-none headertopics">
                            <div class="card-body">
                                <div class="d-flex align-items-center">
                                    <div class="d-flex align-items-center p-2 rounded ">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="45" height="45"
                                            fill="currentColor" class="bi bi-people-fill" viewBox="0 0 16 16">
                                            <path
                                                d="M7 14s-1 0-1-1 1-4 5-4 5 3 5 4-1 1-1 1zm4-6a3 3 0 1 0 0-6 3 3 0 0 0 0 6m-5.784 6A2.24 2.24 0 0 1 5 13c0-1.355.68-2.75 1.936-3.72A6.3 6.3 0 0 0 5 9c-4 0-5 3-5 4s1 1 1 1zM4.5 8a2.5 2.5 0 1 0 0-5 2.5 2.5 0 0 0 0 5" />
                                        </svg>

                                    </div>

                                    <div class="h3 ms-2 mb-0">{{ __('message.USERS') }}</div>
                                    <div class="d-flex flex-column ms-auto text-end">
                                        <div class="h1 mb-0">{{ $user_count }}</div>
                                    </div>
                                </div>
                            </div>
                        </a>
                    </div>
                </div>
                <div class="col-sm-6 col-lg-3">
                    <div class="card admin_cards">
                        <a href="{{ route('topics.index') }}" class="text-decoration-none headertopics">
                            <div class="card-body">
                                <div class="d-flex align-items-center">
                                    <div class="d-flex align-items-center  p-2 rounded ">
                                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 352 512" width="48"
                                            height="48">
                                            <path fill="currentColor"
                                                d="M176 80c-52.94 0-96 43.06-96 96 0 8.84 7.16 16 16 16s16-7.16 16-16c0-35.3 28.72-64 64-64 8.84 0 16-7.16 16-16s-7.16-16-16-16zM96.06 459.17c0 3.15.93 6.22 2.68 8.84l24.51 36.84c2.97 4.46 7.97 7.14 13.32 7.14h78.85c5.36 0 10.36-2.68 13.32-7.14l24.51-36.84c1.74-2.62 2.67-5.7 2.68-8.84l.05-43.18H96.02l.04 43.18zM176 0C73.72 0 0 82.97 0 176c0 44.37 16.45 84.85 43.56 115.78 16.64 18.99 42.74 58.8 52.42 92.16v.06h48v-.12c-.01-4.77-.72-9.51-2.15-14.07-5.59-17.81-22.82-64.77-62.17-109.67-20.54-23.43-31.52-53.15-31.61-84.14-.2-73.64 59.67-128 127.95-128 70.58 0 128 57.42 128 128 0 30.97-11.24 60.85-31.65 84.14-39.11 44.61-56.42 91.47-62.1 109.46a47.507 47.507 0 0 0-2.22 14.3v.1h48v-.05c9.68-33.37 35.78-73.18 52.42-92.16C335.55 260.85 352 220.37 352 176 352 78.8 273.2 0 176 0z" />
                                        </svg>
                                    </div>
                                    <div class="h3 ms-2 mb-0">{{ __('message.TOPICS') }}</div>
                                    <div class="d-flex flex-column ms-auto text-end">
                                        <div class="h1 mb-0">{{ $topic_count }}</div>
                                    </div>
                                </div>
                            </div>
                        </a>
                    </div>
                </div>
                <div class="col-sm-6 col-lg-3">
                    <div class="card admin_cards">
                        <a href="{{ route('channels.index') }}" class="text-decoration-none headertopics">
                            <div class="card-body">
                                <div class="d-flex align-items-center">
                                    <div class="d-flex align-items-center  p-2 rounded ">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="45" height="45"
                                            fill="currentColor" class="bi bi-youtube" viewBox="0 0 16 16">
                                            <path
                                                d="M8.051 1.999h.089c.822.003 4.987.033 6.11.335a2.01 2.01 0 0 1 1.415 1.42c.101.38.172.883.22 1.402l.01.104.022.26.008.104c.065.914.073 1.77.074 1.957v.075c-.001.194-.01 1.108-.082 2.06l-.008.105-.009.104c-.05.572-.124 1.14-.235 1.558a2.01 2.01 0 0 1-1.415 1.42c-1.16.312-5.569.334-6.18.335h-.142c-.309 0-1.587-.006-2.927-.052l-.17-.006-.087-.004-.171-.007-.171-.007c-1.11-.049-2.167-.128-2.654-.26a2.01 2.01 0 0 1-1.415-1.419c-.111-.417-.185-.986-.235-1.558L.09 9.82l-.008-.104A31 31 0 0 1 0 7.68v-.123c.002-.215.01-.958.064-1.778l.007-.103.003-.052.008-.104.022-.26.01-.104c.048-.519.119-1.023.22-1.402a2.01 2.01 0 0 1 1.415-1.42c.487-.13 1.544-.21 2.654-.26l.17-.007.172-.006.086-.003.171-.007A100 100 0 0 1 7.858 2zM6.4 5.209v4.818l4.157-2.408z" />
                                        </svg>
                                    </div>
                                    <div class="h3 ms-2 mb-0">{{ __('message.CHANNELS') }}</div>
                                    <div class="d-flex flex-column ms-auto text-end">
                                        <div class="h1 mb-0">{{ $channel_count }}</div>
                                    </div>
                                </div>
                            </div>
                        </a>
                    </div>
                </div>
                <div class="col-sm-6 col-lg-3">
                    <div class="card admin_cards">
                        <a href="{{ route('posts.index') }}" class="text-decoration-none headertopics">
                            <div class="card-body">
                                <div class="d-flex align-items-center">
                                    <div class="d-flex align-items-center p-2 rounded ">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="45" height="45"
                                            fill="currentColor" class="bi bi-file-earmark-post-fill" viewBox="0 0 16 16">
                                            <path
                                                d="M9.293 0H4a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h8a2 2 0 0 0 2-2V4.707A1 1 0 0 0 13.707 4L10 .293A1 1 0 0 0 9.293 0M9.5 3.5v-2l3 3h-2a1 1 0 0 1-1-1m-5-.5H7a.5.5 0 0 1 0 1H4.5a.5.5 0 0 1 0-1m0 3h7a.5.5 0 0 1 .5.5v7a.5.5 0 0 1-.5.5h-7a.5.5 0 0 1-.5-.5v-7a.5.5 0 0 1 .5-.5" />
                                        </svg>
                                    </div>
                                    <div class="h3 ms-2 mb-0">&nbsp; {{ __('message.POSTS') }}</div>
                                    <div class="d-flex flex-column ms-auto text-end">
                                        <div class="h1 mb-0">{{ $post_count }}</div>
                                    </div>
                                </div>
                            </div>
                        </a>
                    </div>
                </div>

                <!-- Posts vs Videos Count Chart -->
                <div class="col-lg-6">
                    <div class="card admin_cards">
                        <div class="card-header">
                            <div class="row col-lg-12">
                                <div class="col-lg-6 mt-2">
                                    <h4>{{ __('message.POSTS_VS_VIDEOS_COUNT') }}</h4>
                                </div>
                                <div class="col-lg-6">
                                    <div class="d-flex justify-content-end">
                                        <div id="reportranges" class="form-select">
                                            <i class="fas fa-calendar-alt"></i>&nbsp;
                                            <span></span>&nbsp;<i class="fas fa-caret-down"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="chart-container">
                                <canvas id="combinedCharts" height="300"></canvas>
                            </div>
                        </div>
                    </div>
                </div>


                <div class="col-lg-6">
                    <div class="card admin_cards">
                        <div class="card-header">
                            <div class="row col-lg-12">
                                <div class="col-lg-6 mt-2">
                                    <h4>{{ __('message.MOST_LIKED:_POST_VS_VIDEO') }}</h4>
                                </div>
                                <div class="col-lg-6">
                                    <div class="d-flex justify-content-end">
                                        <div id="reportrange_liked" class="form-select">
                                            <i class="fas fa-calendar-alt"></i>&nbsp;
                                            <span></span>&nbsp;<i class="fas fa-caret-down"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="chart-container">
                                <canvas id="combinedCharts_liked" height="300"></canvas>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-6">
                    <div class="card admin_cards">
                        <div class="card-header">
                            <div class="row col-lg-12">
                                <div class="col-lg-6 mt-2">
                                    <h4>{{ __('message.MOST_FOLLOWED: CHANNELS') }}</h4>
                                </div>
                                <div class="col-lg-6">
                                    <div class="d-flex justify-content-end">
                                        <div id="reportrange_mostfollowed_channels" class="form-select">
                                            <i class="fas fa-calendar-alt"></i>
                                            <span></span> <i class="fas fa-caret-down"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="chart-container">
                                <canvas id="mostfollowed_channels" height="300"></canvas>
                            </div>
                            <!-- Section to display channel logos, names, and follower counts -->
                            <div id="channel-details" class="d-flex justify-content-around mt-3">
                                <!-- Dynamically populated via JavaScript -->
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-6">
                    <div class="card admin_cards">
                        <div class="card-header">
                            <div class="row col-lg-12">
                                <div class="col-lg-6 mt-2">
                                    <h4>{{ __('message.SUBSCRIPTION_CHART') }}</h4>
                                </div>
                                <div class="col-lg-6">
                                    <div class="d-flex justify-content-end">
                                        <div id="subscription_picker" class="form-select">
                                            <i class="fas fa-calendar-alt"></i>&nbsp;
                                            <span></span>&nbsp;<i class="fas fa-caret-down"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="chart-container">
                                <canvas id="subscription_chart" height="300"></canvas>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-6">
                    <div class="card admin_cards">
                        <div class="card-header">
                            <div class="row col-lg-12">
                                <div class="col-lg-6 mt-2">
                                    <h4>{{ __('message.TRANSACTION_CHART') }}</h4>
                                </div>
                                <div class="col-lg-6">
                                    <div class="d-flex justify-content-end">
                                        <div id="transaction_picker" class="form-select">
                                            <i class="fas fa-calendar-alt"></i>
                                            <span></span><i class="fas fa-caret-down"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="chart-container">
                                <canvas id="transactions_chart" height="300"></canvas>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-6">
                    <div class="card admin_cards">
                        <div class="card-header">
                            <div class="row col-lg-12">
                                <div class="col-lg-6 mt-2">
                                    <h4>{{ __('message.ACTIVE_USERS_CHART') }}</h4>
                                </div>
                                <div class="col-lg-6">
                                    <div class="d-flex justify-content-end">
                                        <div id="active_users_picker" class="form-select">
                                            <i class="fas fa-calendar-alt"></i>&nbsp;
                                            <span></span>&nbsp;<i class="fas fa-caret-down"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="chart-container">
                                <canvas id="active_users_chart" height="300"></canvas>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Google Analytics / AdSense Analytics Card --}}
                <div class="col-lg-12">
                    <div class="card admin_cards shadow-sm">
                        <div class="card-header">
                            <div class="row col-lg-12">
                                <div class="col-lg-6 mt-2">
                                    <h4>{{ __('message.GOOGLE_ADSENSE_ANALYTICS_CHART') }}</h4>
                                </div>
                                <div class="col-md-6">
                                    <div class="d-flex justify-content-end">
                                        <div id="ga_picker" class="form-select">
                                            <i class="fas fa-calendar-alt"></i>
                                            <span></span>&nbsp;
                                            <i class="fas fa-caret-down ms-1"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="card-body">
                            {{-- Chart Container --}}
                            <div class="chart-container mb-4">
                                <canvas id="ga_adsense_chart" height="300"></canvas>
                            </div>
                            {{-- Statistics Cards --}}
                            <div class="row text-center g-3">
                                <div class="col-md-3">
                                    <div class=" p-3 admin_cards border">
                                        <div class="d-flex justify-content-between align-items-start mb-2">
                                            <h4 class="text-muted mb-0">
                                                <i class="fas fa-eye text-primary"></i>
                                                {{ __('message.TOTAL_IMPRESSIONS') }}
                                            </h4>
                                            <span id="imp_trend"></span>
                                        </div>
                                        <h3 class="mb-0 fw-bold" id="ga_impressions">0</h3>
                                    </div>
                                </div>

                                <div class="col-md-3">
                                    <div class=" p-3 admin_cards border">
                                        <div class="d-flex justify-content-between align-items-start mb-2">
                                            <h4 class="text-muted mb-0">
                                                <i class="fas fa-mouse-pointer text-info"></i>
                                                {{ __('message.TOTAL_CLICKS') }}

                                            </h4>
                                            <span id="click_trend"></span>
                                        </div>
                                        <h3 class="mb-0 fw-bold" id="ga_clicks">0</h3>
                                    </div>
                                </div>

                                <div class="col-md-3">
                                    <div class=" p-3 admin_cards border">
                                        <div class="d-flex justify-content-between align-items-start mb-2">
                                            <h4 class="text-muted mb-0">
                                                <i class="fas fa-rupee-sign text-success"></i>
                                                {{ __('message.TOTAL_EARNINGS') }}
                                            </h4>
                                            <span id="earn_trend"></span>
                                        </div>
                                        <h3 class="mb-0 fw-bold">
                                            ₹<span id="ga_earnings">0.00</span>
                                        </h3>
                                    </div>
                                </div>

                                <div class="col-md-3">
                                    <div class=" p-2 admin_cards border">
                                        <div class="d-flex justify-content-between align-items-start mb-2">
                                            <h4 class="text-muted mb-0">
                                                <i class="fas fa-percentage text-warning"></i>
                                                {{ __('message.AVERAGE_CTR') }}

                                            </h4>
                                        </div>
                                        <h3 class="mb-0 fw-bold" id="ga_ctr">0%</h3>
                                        <small class="text-muted">{{ __('message.CLICK_THROUGH_RATE') }}</small>
                                    </div>
                                </div>
                            </div>

                            {{-- Additional Info --}}
                            {{-- <div class="row mt-4">
                                <div class="col-12">
                                    <div class="alert alert-info mb-0" role="alert">
                                        <i class="fas fa-info-circle"></i>
                                        <strong>Note:</strong> AdSense data is updated every few hours.
                                        Final earnings may vary based on invalid clicks and other factors.
                                    </div>
                                </div>
                            </div> --}}
                        </div>
                    </div>
                </div>

                {{-- Recent Posts --}}
                <div class="col-lg-6">
                    <div class="row row-cards">
                        <div class="col-12">
                            <div class="card admin_cards">
                                <div class="card-header"><strong>{{ __('message.MOST_LIKED_POST') }}</strong>
                                </div>
                                <div class="card-body card-body-scrollable card-body-scrollable-shadow">
                                    <div class="divide-y">

                                        @foreach ($mostLikedPosts as $post)
                                            <div>
                                                <div class="row">
                                                    @if ($post->type != 'video')
                                                        <div class="col-auto">
                                                            <img src="{{ $post->image ?? asset('public/front_end/classic/images/default/post-placeholder.jpg') }}"
                                                                alt="Post img" class="avatar">
                                                        </div>
                                                    @else
                                                        <div class="col-auto">
                                                            <img src="{{ $post->video_thumb ?? asset('public/front_end/classic/images/default/post-placeholder.jpg') }}"
                                                                alt="Post img" class="avatar">
                                                        </div>
                                                    @endif
                                                    <div class="col">
                                                        <div class="text-truncate ">
                                                            <strong>{{ $post->title }}</strong>
                                                        </div>
                                                        <div class="d-flex justify-content-between text-secondary">
                                                            <div>
                                                                <span>{{ $post->channel_name }}</span>
                                                                <span>{{ $post->publish_date }}</span>
                                                            </div>
                                                            <div>
                                                                <span class="me-3 mt-2">
                                                                    <i class="fa fa-eye" aria-hidden="true"></i>
                                                                    {{ $post->view_count }}
                                                                </span>
                                                                <span class="me-3 mt-2">
                                                                    <i class="fa fa-heart" aria-hidden="true"></i>
                                                                    {{ $post->reaction }}
                                                                </span>

                                                            </div>
                                                        </div>

                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Recent user --}}
                @if (!empty($mostViewedStories))
                    <div class="col-lg-6">
                        <div class="row row-cards">
                            <div class="col-12">
                                <div class="card admin_cards">
                                    <div class="card-header"><strong>{{ __('message.MOST_VIEWED_STORY') }}</strong>
                                    </div>
                                    <div class="card-body card-body-scrollable card-body-scrollable-shadow">
                                        <div class="divide-y">

                                            @foreach ($mostViewedStories as $mostViewedStorie)
                                                <div>
                                                    <div class="row">
                                                        <div class="col">
                                                            <div class="text-truncate-3 ">
                                                                <strong>{{ $mostViewedStorie->title }}</strong>
                                                            </div>
                                                            <div class="d-flex justify-content-between text-secondary">
                                                                <div>
                                                                    <span>
                                                                        {{ $mostViewedStorie->created_at->diffForHumans() }}</span>
                                                                </div>
                                                                <div>
                                                                    <span class="me-3 mt-2">
                                                                        <i class="fa fa-eye" aria-hidden="true"></i>
                                                                        {{ $mostViewedStorie->story_count }}
                                                                    </span>
                                                                </div>
                                                            </div>

                                                        </div>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection
