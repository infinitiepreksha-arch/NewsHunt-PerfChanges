@extends('front_end.' . $theme . '.layout.main')

@section('body')
    <!-- Wrapper start -->
    <div id="wrapper" class="wrap overflow-hidden-x">
        <div class="breadcrumbs panel z-1 py-2 bg-gray-25 dark:bg-gray-100 dark:bg-opacity-5 dark:text-white">
            <div class="container max-w-xl">
                <ul class="breadcrumb nav-x justify-center gap-1 fs-7 sm:fs-6 m-0">
                    <li><a href="{{ url('home') }}" title="Home">{{ __('frontend-labels.home.title') }}</a></li>
                    <li><i class="unicon-chevron-right opacity-50"></i></li>
                    @if (!empty($searchQuery))
                        <li><span class="opacity-70">{{ __('frontend-labels.search.title') }}</span></li>
                        <li><i class="unicon-chevron-right opacity-50"></i></li>
                        <li><span class="opacity-70" title="{{ $title }}">{{ __('frontend-labels.search.for') }}
                                {{ $title }}</span></li>
                    @else
                        <li><a href="{{ route('posts.search') }}">{{ __('frontend-labels.posts.all_posts') }}</a></li>
                    @endif
                </ul>
            </div>
        </div>
        <div class="section py-3 sm:py-6 lg:py-9">
            <div class="container max-w-xl">
                <div class="panel vstack gap-3 sm:gap-6 lg:gap-3">



                    <div id="uc-filter-panel" data-uc-offcanvas="overlay: true;">
                        <div class="uc-offcanvas-bar bg-white text-dark dark:bg-gray-900 dark:text-white">
                            <header
                                class="uc-offcanvas-header hstack justify-between items-center pb-4 bg-white dark:bg-gray-900">
                                <div class="uc-logo">
                                    <a href="{{ url('home') }}" class="h5 text-none text-gray-900 dark:text-white">
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
                                <form action="{{ route('posts.search') }}" method="GET" id="searchFormMobile">
                                    <div class="vstack gap-2">
                                        <!-- Search input -->
                                        <div class="mb-2">
                                            <h6 class="fs-6 fw-bold mb-1 text-black dark:text-white">Search Keyword</h6>
                                            <input type="text" name="search" class="form-control rounded dark:bg-gray-800 dark:border-gray-700 w-full" placeholder="Search..." value="{{ request('search') }}">
                                        </div>

                                        <!-- Sort by -->
                                        <div class="mb-2">
                                            <h6 class="fs-6 fw-bold mb-1 text-black dark:text-white">Sort by</h6>
                                            <select name="filter" class="form-select rounded dark:bg-gray-800 dark:border-gray-700 w-full" style="padding-top: 6px; padding-bottom: 6px; height: auto;">
                                                <option value="" {{ request('filter') == '' ? 'selected' : '' }}>Relevance</option>
                                                <option value="most-recent" {{ request('filter') == 'most-recent' ? 'selected' : '' }}>Newest</option>
                                                <option value="oldest" {{ request('filter') == 'oldest' ? 'selected' : '' }}>Oldest</option>
                                                <option value="most-read" {{ request('filter') == 'most-read' ? 'selected' : '' }}>Most Read</option>
                                                <option value="most-liked" {{ request('filter') == 'most-liked' ? 'selected' : '' }}>Most Liked</option>
                                                <option value="most-commented" {{ request('filter') == 'most-commented' ? 'selected' : '' }}>Most Commented</option>
                                            </select>
                                        </div>

                                        <!-- Post Type -->
                                        <div class="mb-2">
                                            <h6 class="fs-6 fw-bold mb-1 text-black dark:text-white">Post Type</h6>
                                            <div class="scrollable-container bg-gray-450 dark:bg-gray-100 dark:bg-opacity-5 px-2 border p-2 rounded">
                                                <label class="d-flex align-items-center gap-1 text-black dark:text-white hover:text-primary">
                                                    <input type="checkbox" name="post_type[]" value="post" class="form-check-input rounded-0 dark:bg-gray-800 dark:border-white" {{ in_array('post', (array)request('post_type', [])) ? 'checked' : '' }}>
                                                    Articles
                                                </label>
                                                <label class="d-flex align-items-center gap-1 text-black dark:text-white hover:text-primary">
                                                    <input type="checkbox" name="post_type[]" value="video" class="form-check-input rounded-0 dark:bg-gray-800 dark:border-white" {{ in_array('video', (array)request('post_type', [])) ? 'checked' : '' }}>
                                                    Videos
                                                </label>
                                                <label class="d-flex align-items-center gap-1 text-black dark:text-white hover:text-primary">
                                                    <input type="checkbox" name="post_type[]" value="youtube" class="form-check-input rounded-0 dark:bg-gray-800 dark:border-white" {{ in_array('youtube', (array)request('post_type', [])) ? 'checked' : '' }}>
                                                    YouTube
                                                </label>
                                                <label class="d-flex align-items-center gap-1 text-black dark:text-white hover:text-primary">
                                                    <input type="checkbox" name="post_type[]" value="audio" class="form-check-input rounded-0 dark:bg-gray-800 dark:border-white" {{ in_array('audio', (array)request('post_type', [])) ? 'checked' : '' }}>
                                                    Audios
                                                </label>
                                                <label class="d-flex align-items-center gap-1 text-black dark:text-white hover:text-primary">
                                                    <input type="checkbox" name="post_type[]" value="story" class="form-check-input rounded-0 dark:bg-gray-800 dark:border-white" {{ in_array('story', (array)request('post_type', [])) ? 'checked' : '' }}>
                                                    Web Stories
                                                </label>
                                                <label class="d-flex align-items-center gap-1 text-black dark:text-white hover:text-primary">
                                                    <input type="checkbox" name="post_type[]" value="paper" class="form-check-input rounded-0 dark:bg-gray-800 dark:border-white" {{ in_array('paper', (array)request('post_type', [])) ? 'checked' : '' }}>
                                                    Newspapers
                                                </label>
                                                <label class="d-flex align-items-center gap-1 text-black dark:text-white hover:text-primary">
                                                    <input type="checkbox" name="post_type[]" value="magazine" class="form-check-input rounded-0 dark:bg-gray-800 dark:border-white" {{ in_array('magazine', (array)request('post_type', [])) ? 'checked' : '' }}>
                                                    Magazines
                                                </label>
                                            </div>
                                        </div>

                                        <!-- Channels -->
                                        <div class="mb-2">
                                            <h6 class="fs-6 fw-bold mb-1 text-black dark:text-white">{{ __('frontend-labels.channels.title') }}</h6>
                                            <div class="scrollable-container bg-gray-450 dark:bg-gray-100 dark:bg-opacity-5 px-2 border p-2 rounded" style="max-height: 150px; overflow-y: auto;">
                                                @if (!empty($channels))
                                                    @foreach ($channels as $channel)
                                                        <label class="d-flex align-items-center gap-1 text-black dark:text-white hover:text-primary">
                                                            <input type="checkbox" id="mob-chan-{{ $channel->slug }}" class="form-check-input rounded-0 dark:bg-gray-800 dark:border-white" name="channel[]" value="{{ $channel->slug }}" {{ in_array($channel->slug, (array) request()->input('channel', [])) ? 'checked' : '' }}>
                                                            {{ $channel->name }}
                                                        </label>
                                                    @endforeach
                                                @endif
                                            </div>
                                        </div>

                                        <!-- Topics -->
                                        <div class="mb-2">
                                            <h6 class="fs-6 fw-bold mb-1 text-black dark:text-white">{{ __('frontend-labels.topics.title') }}</h6>
                                            <div class="scrollable-container bg-gray-450 dark:bg-gray-100 dark:bg-opacity-5 px-2 border p-2 rounded" style="max-height: 150px; overflow-y: auto;">
                                                @if (!empty($topics))
                                                    @foreach ($topics as $topic)
                                                        <label class="d-flex align-items-center gap-1 text-black dark:text-white hover:text-primary">
                                                            <input type="checkbox" name="topic[]" id="mob-top-{{ $topic->slug }}" value="{{ $topic->slug }}" class="form-check-input rounded-0 dark:bg-gray-800 dark:border-white" {{ in_array($topic->slug, (array) request()->input('topic', [])) ? 'checked' : '' }}>
                                                            {{ $topic->name }}
                                                        </label>
                                                    @endforeach
                                                @endif
                                            </div>
                                        </div>

                                        <!-- Followed by You -->
                                        @if (auth()->check())
                                            <div class="mb-2">
                                                <h6 class="fs-6 fw-bold mb-1 text-black dark:text-white">Followed by You</h6>
                                                <div class="scrollable-container bg-gray-450 dark:bg-gray-100 dark:bg-opacity-5 px-2 border p-2 rounded" style="max-height: 150px; overflow-y: auto;">
                                                    @if ($followedChannels->isNotEmpty() || $followedTopics->isNotEmpty())
                                                        @if ($followedChannels->isNotEmpty())
                                                            <div class="fw-bold fs-8 text-uppercase text-gray-500 mb-1">Channels</div>
                                                            @foreach ($followedChannels as $fChannel)
                                                                <label class="d-flex align-items-center gap-1 text-black dark:text-white hover:text-primary ms-2">
                                                                    <input type="checkbox" name="channel[]" value="{{ $fChannel->slug }}" class="form-check-input rounded-0 dark:bg-gray-800 dark:border-white" {{ in_array($fChannel->slug, (array)request('channel', [])) ? 'checked' : '' }}>
                                                                    {{ $fChannel->name }}
                                                                </label>
                                                            @endforeach
                                                        @endif
                                                        @if ($followedTopics->isNotEmpty())
                                                            <div class="fw-bold fs-8 text-uppercase text-gray-500 mt-2 mb-1">Topics</div>
                                                            @foreach ($followedTopics as $fTopic)
                                                                <label class="d-flex align-items-center gap-1 text-black dark:text-white hover:text-primary ms-2">
                                                                    <input type="checkbox" name="topic[]" value="{{ $fTopic->slug }}" class="form-check-input rounded-0 dark:bg-gray-800 dark:border-white" {{ in_array($fTopic->slug, (array)request('topic', [])) ? 'checked' : '' }}>
                                                                    {{ $fTopic->name }}
                                                                </label>
                                                            @endforeach
                                                        @endif
                                                    @else
                                                        <span class="fs-7 text-gray-500">No followed items yet.</span>
                                                    @endif
                                                </div>
                                            </div>
                                        @endif

                                        <div class="d-flex justify-between mt-3">
                                            <button type="submit" class="btn btn-primary btn-sm w-45">{{ __('frontend-labels.filters.apply') }}</button>
                                            <a href="javascript:void(0)" class="btn btn-outline-primary btn-sm text-primary w-45 text-center clear-filters-btn">{{ __('frontend-labels.filters.clear') }}</a>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>


                    <div class="row">
                        <div class="d-flex align-items-stretch gap-1">
                            <div class="col-12 col-sm-12 col-md-12 col-lg-3 mt-2 mb-2 d-none d-lg-block">
                                <!-- Dashboard sidebar -->
                                <div class="dashboard-sidebar bg-block rounded-lg mt-0 mb-2 p-3 h-auto border ">
                                    <div class="profile-top mb-4">
                                        <div class="profile-detail text-black dark:text-white  rounded">
                                            <h3 title="{{ __('frontend-labels.filters.title') }}">
                                                {{ __('frontend-labels.filters.title') }}</h3>
                                            <span></span>
                                        </div>
                                    </div>
                                    <div class="dashboard-tab">
                                        <form action="{{ route('posts.search') }}" method="GET" id="filterForm">
                                            <!-- Search Keyword -->
                                            <div class="mb-3">
                                                <h5 class="h5 mb-2 text-black dark:text-white">Search Keyword</h5>
                                                <input type="text" name="search" class="form-control rounded dark:bg-gray-800 dark:border-gray-700 w-full" placeholder="Search..." value="{{ request('search') }}">
                                            </div>

                                            <!-- Sort by -->
                                            <div class="mb-3">
                                                <h5 class="h5 mb-2 text-black dark:text-white">Sort by</h5>
                                                <select name="filter" class="form-select rounded dark:bg-gray-800 dark:border-gray-700 w-full" style="padding-top: 6px; padding-bottom: 6px; height: auto;">
                                                    <option value="" {{ request('filter') == '' ? 'selected' : '' }}>Relevance</option>
                                                    <option value="most-recent" {{ request('filter') == 'most-recent' ? 'selected' : '' }}>Newest</option>
                                                    <option value="oldest" {{ request('filter') == 'oldest' ? 'selected' : '' }}>Oldest</option>
                                                    <option value="most-read" {{ request('filter') == 'most-read' ? 'selected' : '' }}>Most Read</option>
                                                    <option value="most-liked" {{ request('filter') == 'most-liked' ? 'selected' : '' }}>Most Liked</option>
                                                    <option value="most-commented" {{ request('filter') == 'most-commented' ? 'selected' : '' }}>Most Commented</option>
                                                </select>
                                            </div>

                                            <!-- Post Type -->
                                            <div class="mb-3">
                                                <h5 class="h5 mb-0 text-black dark:text-white">Post Type</h5>
                                                <div class="scrollable-container bg-gray-450 dark:bg-gray-100 dark:bg-opacity-5 px-2 border p-2 rounded mt-2">
                                                    <label class="d-flex align-items-center gap-1 text-black dark:text-white hover:text-primary">
                                                        <input type="checkbox" name="post_type[]" value="post" class="form-check-input rounded-0 dark:bg-gray-800 dark:border-white" {{ in_array('post', (array)request('post_type', [])) ? 'checked' : '' }}>
                                                        Articles
                                                    </label>
                                                    <label class="d-flex align-items-center gap-1 text-black dark:text-white hover:text-primary">
                                                        <input type="checkbox" name="post_type[]" value="video" class="form-check-input rounded-0 dark:bg-gray-800 dark:border-white" {{ in_array('video', (array)request('post_type', [])) ? 'checked' : '' }}>
                                                        Videos
                                                    </label>
                                                    <label class="d-flex align-items-center gap-1 text-black dark:text-white hover:text-primary">
                                                        <input type="checkbox" name="post_type[]" value="youtube" class="form-check-input rounded-0 dark:bg-gray-800 dark:border-white" {{ in_array('youtube', (array)request('post_type', [])) ? 'checked' : '' }}>
                                                        YouTube
                                                    </label>
                                                    <label class="d-flex align-items-center gap-1 text-black dark:text-white hover:text-primary">
                                                        <input type="checkbox" name="post_type[]" value="audio" class="form-check-input rounded-0 dark:bg-gray-800 dark:border-white" {{ in_array('audio', (array)request('post_type', [])) ? 'checked' : '' }}>
                                                        Audios
                                                    </label>
                                                    <label class="d-flex align-items-center gap-1 text-black dark:text-white hover:text-primary">
                                                        <input type="checkbox" name="post_type[]" value="story" class="form-check-input rounded-0 dark:bg-gray-800 dark:border-white" {{ in_array('story', (array)request('post_type', [])) ? 'checked' : '' }}>
                                                        Web Stories
                                                    </label>
                                                    <label class="d-flex align-items-center gap-1 text-black dark:text-white hover:text-primary">
                                                        <input type="checkbox" name="post_type[]" value="paper" class="form-check-input rounded-0 dark:bg-gray-800 dark:border-white" {{ in_array('paper', (array)request('post_type', [])) ? 'checked' : '' }}>
                                                        Newspapers
                                                    </label>
                                                    <label class="d-flex align-items-center gap-1 text-black dark:text-white hover:text-primary">
                                                        <input type="checkbox" name="post_type[]" value="magazine" class="form-check-input rounded-0 dark:bg-gray-800 dark:border-white" {{ in_array('magazine', (array)request('post_type', [])) ? 'checked' : '' }}>
                                                        Magazines
                                                    </label>
                                                </div>
                                            </div>

                                            <!-- Channels -->
                                            <div class="mb-3">
                                                <h5 class="h5 mb-0 text-black dark:text-white">{{ __('frontend-labels.channels.title') }}</h5>
                                                <div class="scrollable-container bg-gray-450 dark:bg-gray-100 dark:bg-opacity-5 mt-0 px-2 border p-2 rounded mt-2" style="max-height: 200px; overflow-y: auto;">
                                                    @foreach ($channels as $channel)
                                                        @if ($channel->name === 'All')
                                                            <a href="javascript:void(0)" class="d-flex align-items-center gap-1 text-black dark:text-white hover:text-primary" style="text-decoration: none;">
                                                                <input type="checkbox" class="form-check-input rounded-0 dark:bg-gray-800 dark:border-white channel-all-checkbox" {{ empty(request()->input('channel', [])) ? 'checked' : '' }}>
                                                                {{ $channel->name }}
                                                            </a>
                                                        @else
                                                            <label class="d-flex align-items-center gap-1 text-black dark:text-white hover:text-primary">
                                                                <input type="checkbox" id="{{ $channel->slug }}" class="form-check-input rounded-0 dark:bg-gray-800 dark:border-white" name="channel[]" value="{{ $channel->slug }}" {{ in_array($channel->slug, (array) request()->input('channel', [])) ? 'checked' : '' }}>
                                                                {{ $channel->name }}
                                                            </label>
                                                        @endif
                                                    @endforeach
                                                </div>
                                            </div>

                                            <!-- Topics -->
                                            <div class="mb-3">
                                                <h5 class="h5 mt-2 mb-0 text-black dark:text-white">{{ __('frontend-labels.topics.title') }}</h5>
                                                <div class="scrollable-container bg-gray-450 dark:bg-gray-100 dark:bg-opacity-5 px-2 border p-2 rounded mt-2" style="max-height: 200px; overflow-y: auto;">
                                                    @foreach ($topics as $topic)
                                                        <label class="d-flex align-items-center gap-1 text-black dark:text-white hover:text-primary">
                                                            <input type="checkbox" class="form-check-input rounded-0 dark:bg-gray-600 dark:border-white" name="topic[]" id="{{ $topic->slug }}" value="{{ $topic->slug }}" {{ in_array($topic->slug, (array) request()->input('topic', [])) ? 'checked' : '' }}>
                                                            {{ $topic->name }}
                                                        </label>
                                                    @endforeach
                                                </div>
                                            </div>

                                            <!-- Followed by You -->
                                            @if (auth()->check())
                                                <div class="mb-3">
                                                    <h5 class="h5 mt-2 mb-0 text-black dark:text-white">Followed by You</h5>
                                                    <div class="scrollable-container bg-gray-450 dark:bg-gray-100 dark:bg-opacity-5 px-2 border p-2 rounded mt-2" style="max-height: 200px; overflow-y: auto;">
                                                        @if ($followedChannels->isNotEmpty() || $followedTopics->isNotEmpty())
                                                            @if ($followedChannels->isNotEmpty())
                                                                <div class="fw-bold fs-7 text-uppercase text-gray-500 mb-1">Channels</div>
                                                                @foreach ($followedChannels as $fChannel)
                                                                    <label class="d-flex align-items-center gap-1 text-black dark:text-white hover:text-primary ms-2">
                                                                        <input type="checkbox" name="channel[]" value="{{ $fChannel->slug }}" class="form-check-input rounded-0 dark:bg-gray-800 dark:border-white" {{ in_array($fChannel->slug, (array)request('channel', [])) ? 'checked' : '' }}>
                                                                        {{ $fChannel->name }}
                                                                    </label>
                                                                @endforeach
                                                            @endif
                                                            @if ($followedTopics->isNotEmpty())
                                                                <div class="fw-bold fs-7 text-uppercase text-gray-500 mt-2 mb-1">Topics</div>
                                                                @foreach ($followedTopics as $fTopic)
                                                                    <label class="d-flex align-items-center gap-1 text-black dark:text-white hover:text-primary ms-2">
                                                                        <input type="checkbox" name="topic[]" value="{{ $fTopic->slug }}" class="form-check-input rounded-0 dark:bg-gray-800 dark:border-white" {{ in_array($fTopic->slug, (array)request('topic', [])) ? 'checked' : '' }}>
                                                                        {{ $fTopic->name }}
                                                                    </label>
                                                                @endforeach
                                                            @endif
                                                        @else
                                                            <span class="fs-7 text-gray-500">No followed items yet.</span>
                                                        @endif
                                                    </div>
                                                </div>
                                            @endif

                                            <div class="d-flex justify-between mt-3">
                                                <button type="submit" class="btn btn-primary btn-sm">{{ __('frontend-labels.filters.apply') }}</button>
                                                <a href="javascript:void(0)" class="btn btn-outline-primary btn-sm clear-filters-btn">{{ __('frontend-labels.filters.clear') }}</a>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>

                            <div class="col-12 col-sm-12 col-md-12 col-lg-9 mt-2 mb-2">
                                <div class="d-flex d-lg-none justify-end">
                                    <a class="btn btn-primary btn-sm" href="#uc-filter-panel"
                                        data-uc-toggle>{{ __('frontend-labels.filters.title') }}
                                    </a>
                                </div>
                                <div id="content-area" class="rounded-lg p-4">
                                    @include('front_end.classic.pages.partials.search_result_posts')
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('script')
    <script defer src="{{ asset('front_end/' . $theme . '/js/custom/search-news.js') }}"></script>
@endsection
