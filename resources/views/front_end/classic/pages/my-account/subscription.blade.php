@extends('front_end.' . $theme . '.layout.main')

@section('body')
    <!-- Wrapper start -->
    <div id="wrapper" class="wrap overflow-hidden-x">
        <div class="breadcrumbs panel z-1 py-2 bg-gray-25 dark:bg-gray-100 dark:bg-opacity-5 dark:text-white">
            <div class="container max-w-xl">
                <ul class="breadcrumb nav-x justify-center gap-1 fs-7 sm:fs-6 m-0">
                    <li><a href="{{ url('/home') }}" title="Go back to home">{{ __('frontend-labels.home.title') }}</a></li>
                    <li><i class="unicon-chevron-right opacity-50"></i></li>
                    <li><a href="{{ url('my-account') }}"
                            title="{{ __('frontend-labels.my-account.account_info') }}">{{ __('frontend-labels.my-account.account_info') }}</a>
                    </li>
                    <li><i class="unicon-chevron-right opacity-50"></i></li>
                    <li><span class="opacity-50">{{ $title }}</span></li>
                </ul>
            </div>
        </div>

        <div class="section py-3 sm:py-6 lg:py-9">
            <div class="container max-w-xl">
                <div class="panel vstack gap-3 sm:gap-6 lg:gap-3">
                    {{-- Mobile view sidebar --}}
                    <div id="mobile-view-sidbar" data-uc-offcanvas="overlay: true;">
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
                                <div class="dashboard-tab">
                                    <div class="block-content panel row sep-x gx-4 gy-3 lg:gy-2">
                                        <div>
                                            <article class="post type-post panel d-flex gap-2">
                                                <h6 class="fs-4 lg:fs-5 xl:fs-5 fw-medium opacity-60">
                                                    <a class="text-none text-dark dark:text-white hover:text-primary duration-150"
                                                        href="{{ url('my-account') }}"
                                                        title="{{ __('frontend-labels.my-account.account_info') }}">
                                                        <i class="bi bi-person-circle fs-3"> </i>
                                                        {{ __('frontend-labels.my-account.account_info') }}
                                                    </a>
                                                </h6>
                                            </article>
                                        </div>
                                        <div>
                                            <article class="post type-post panel d-flex gap-2">
                                                <h6 class="fs-4 lg:fs-5 xl:fs-5 fw-medium opacity-60">
                                                    <a class="text-none text-dark dark:text-white hover:text-primary duration-150"
                                                        href="{{ url('my-account/followings') }}"
                                                        title="{{ __('frontend-labels.followings.title') }}">
                                                        <i class="bi bi-youtube fs-3"> </i>
                                                        {{ __('frontend-labels.followings.title') }}

                                                    </a>
                                                </h6>
                                            </article>
                                        </div>

                                        <div>
                                            <article class="post type-post panel d-flex gap-2">
                                                <h6 class="fs-4 lg:fs-5 xl:fs-5 fw-medium opacity-60">
                                                    <a class="text-none text-dark dark:text-white hover:text-primary duration-150"
                                                        href="{{ url('my-account/bookmarks') }}"
                                                        title="{{ __('frontend-labels.favorite.title') }}">
                                                        <i class="bi bi-bookmark fs-3 dark:text-white"> </i>
                                                        {{ __('frontend-labels.favorite.title') }}
                                                    </a>
                                                </h6>
                                            </article>
                                        </div>
                                        <div>
                                            <article class="post type-post panel d-flex gap-2">
                                                <h6 class="fs-4 lg:fs-5 xl:fs-5 fw-medium opacity-60">
                                                    <a class="text-none text-dark dark:text-white hover:text-primary duration-150"
                                                        href="{{ url('my-account/transaction') }}"
                                                        title="{{ __('frontend-labels.transaction_details.title') }}">
                                                        <i class="bi bi-wallet2 fs-3"> </i>
                                                        {{ __('frontend-labels.transaction_details.title') }}
                                                    </a>
                                                </h6>
                                            </article>
                                        </div>
                                        <div>
                                            <article class="post type-post panel d-flex gap-2">
                                                <h6 class="fs-4 lg:fs-5 xl:fs-5 fw-medium">
                                                    <a class="text-none text-dark dark:text-white hover:text-primary duration-150"
                                                        href="#" title="{{ $title }}">
                                                        <svg width="24px" height="24px" viewBox="0 0 24 24"
                                                            xmlns="http://www.w3.org/2000/svg" fill="#000000">
                                                            <path
                                                                d="M14,6a7.17,7.17,0,0,0-1,.08A4.49,4.49,0,0,0,4,6.5V7A2,2,0,0,0,2,9v9a1.94,1.94,0,0,0,2,2H8.73A8,8,0,1,0,14,6ZM6,6.5a2.51,2.51,0,0,1,5-.24V7H6ZM14,20a6,6,0,1,1,6-6A6,6,0,0,1,14,20Zm-1.5-8v1h4a1,1,0,0,1,1,1v3a1,1,0,0,1-1,1H15v1H13V18H10.5V16h5V15h-4a1,1,0,0,1-1-1V11a1,1,0,0,1,1-1H13V9h2v1h2.5v2Z">
                                                            </path>
                                                        </svg></i> {{ $title }}
                                                    </a>
                                                </h6>
                                            </article>
                                        </div>
                                        @if (auth()->user()->id !== 1)
                                            <div>
                                                <article class="post type-post panel d-flex gap-2">
                                                    <h6 class="fs-4 lg:fs-5 xl:fs-5 fw-medium opacity-60">
                                                        <a class="text-none text-dark dark:text-white hover:text-primary duration-150"
                                                            title="{{ __('frontend-labels.my-account.remove_account') }}"
                                                            id="user-delete-account">
                                                            <i class="bi bi-person-fill-slash fs-3"> </i>
                                                            {{ __('frontend-labels.my-account.remove_account') }}
                                                        </a>
                                                    </h6>
                                                </article>
                                            </div>
                                        @endif
                                        <div>
                                            <article class="post type-post panel d-flex gap-2">
                                                <h6 class="fs-4 lg:fs-5 xl:fs-5 fw-medium opacity-60">
                                                    <form id="logout-form" action="{{ route('logout') }}" method="POST"
                                                        class="d-none">
                                                        @csrf
                                                    </form>
                                                    <a class="text-none text-dark dark:text-white hover:text-primary duration-150"
                                                        href="#"
                                                        onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                                        <i class="bi bi-box-arrow-right fs-3"> </i>
                                                        {{ __('frontend-labels.my-account.remove_account') }}
                                                    </a>
                                                </h6>
                                            </article>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="d-flex align-items-stretch gap-1">
                            <div class="col-12 col-sm-12 col-md-12 col-lg-3 mt-2 mb-2 d-none d-lg-block">
                                <!-- Dashboard sidebar -->
                                <div class="dashboard-sidebar bg-block rounded-lg mb-2 p-3 h-100">
                                    <div class="profile-top text-center mb-4">
                                        <div class="mb-3 mt-2">
                                            <img class="profile-image rounded-circle blur-up lazyloaded w-100px h-100px user-sidebar-img"
                                                data-src="{{ auth()->user()->profile ?? asset('front_end/classic/images/avatars/04.png') }}"
                                                src="{{ auth()->user()->profile ?? asset('front_end/classic/images/avatars/04.png') }}"
                                                alt="user" data-uc-tooltip="Profile">
                                        </div>
                                        <div class="profile-detail dark:text-white">
                                            <h3>{{ auth()->user()->name }}</h3>
                                            <span>{{ auth()->user()->email }}</span>
                                        </div>
                                    </div>
                                    <div class="dashboard-tab">
                                        <div class="block-content panel row sep-x gx-4 gy-3 lg:gy-2">
                                            <div>
                                                <a class="text-none hover:text-primary duration-150"
                                                    href="{{ url('my-account') }}"
                                                    title="{{ __('frontend-labels.my-account.account_info') }}">
                                                    <article class="post type-post panel d-flex gap-2">
                                                        <h4 class="fs-4 lg:fs-6 xl:fs-4 fw-medium"><i
                                                                class="bi bi-person-circle fs-3"></i>
                                                            {{ __('frontend-labels.my-account.account_info') }}</h4>
                                                    </article>
                                                </a>
                                            </div>
                                            <div>
                                                <a class="text-none hover:text-primary duration-150"
                                                    href="{{ url('my-account/followings') }}"
                                                    title="{{ __('frontend-labels.followings.title') }}">
                                                    <article class="post type-post panel d-flex gap-2">
                                                        <h4 class="fs-4 lg:fs-6 xl:fs-4 fw-medium"><i
                                                                class="bi bi-youtube fs-3"></i>
                                                            {{ __('frontend-labels.followings.title') }}</h4>
                                                    </article>
                                                </a>
                                            </div>

                                            <div>
                                                <a class="text-none text-dark dark:text-white hover:text-primary duration-150""
                                                    href="{{ url('my-account/bookmarks') }}"
                                                    title="{{ __('frontend-labels.favorite.title') }}">
                                                    <article class="post type-post panel d-flex gap-2">
                                                        <h4 class="fs-4 lg:fs-5 xl:fs-5 fw-medium opacity-60"><i
                                                                class="bi bi-bookmark fs-3"></i>
                                                            {{ __('frontend-labels.favorite.title') }}
                                                        </h4>
                                                    </article>
                                                </a>
                                            </div>
                                            <div>
                                                <article class="post type-post panel d-flex gap-2">
                                                    <h6 class="fs-4 lg:fs-5 xl:fs-5 fw-medium opacity-60">
                                                        <a class="text-none text-dark dark:text-white hover:text-primary duration-150"
                                                            href="{{ url('my-account/transaction') }}"
                                                            title="{{ __('frontend-labels.transaction_details.title') }}">
                                                            <i class="bi bi-wallet2 fs-3"></i> </i>
                                                            {{ __('frontend-labels.transaction_details.title') }}
                                                        </a>
                                                    </h6>
                                                </article>
                                            </div>
                                            <div>
                                                <article class="post type-post panel d-flex gap-2">
                                                    <h6 class="fs-4 lg:fs-6 xl:fs-4 fw-medium dark:text-white">
                                                        <a class="text-none text-dark dark:text-white hover:text-primary duration-150"
                                                            href="#" title="{{ $title }}">
                                                            <svg width="24px" height="24px" viewBox="0 0 24 24"
                                                                xmlns="http://www.w3.org/2000/svg" fill="#000000">
                                                                <path
                                                                    d="M14,6a7.17,7.17,0,0,0-1,.08A4.49,4.49,0,0,0,4,6.5V7A2,2,0,0,0,2,9v9a1.94,1.94,0,0,0,2,2H8.73A8,8,0,1,0,14,6ZM6,6.5a2.51,2.51,0,0,1,5-.24V7H6ZM14,20a6,6,0,1,1,6-6A6,6,0,0,1,14,20Zm-1.5-8v1h4a1,1,0,0,1,1,1v3a1,1,0,0,1-1,1H15v1H13V18H10.5V16h5V15h-4a1,1,0,0,1-1-1V11a1,1,0,0,1,1-1H13V9h2v1h2.5v2Z">
                                                                </path>
                                                            </svg> {{ $title }}
                                                        </a>
                                                    </h6>
                                                </article>
                                            </div>
                                            @if (auth()->user()->id !== 1)
                                                <div>
                                                    <a class="text-none hover:text-primary duration-150"
                                                        title="{{ __('frontend-labels.my-account.remove_account') }}"
                                                        id="user-delete-account">
                                                        <article class="post type-post panel d-flex gap-2">
                                                            <h4 class="fs-4 lg:fs-6 xl:fs-4 fw-medium">
                                                                <i class="bi bi-person-fill-slash fs-3"></i>
                                                                {{ __('frontend-labels.my-account.remove_account') }}
                                                            </h4>
                                                        </article>
                                                    </a>
                                                </div>
                                            @endif
                                            <div>
                                                <div>
                                                    <a class="text-none hover:text-primary duration-150" href="#"
                                                        onclick="event.preventDefault(); document.getElementById('logout-form').submit();"
                                                        title="{{ __('frontend-labels.my-account.remove_account') }}">
                                                        <article class="post type-post panel d-flex gap-2">
                                                            <h4 class="fs-4 lg:fs-6 xl:fs-4 fw-medium">
                                                                <form id="logout-form" action="{{ route('logout') }}"
                                                                    method="POST" class="d-none">
                                                                    @csrf
                                                                </form>
                                                                <i class="bi bi-box-arrow-right fs-3"></i>
                                                                {{ __('frontend-labels.my-account.remove_account') }}
                                                            </h4>
                                                        </article>
                                                    </a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-12 col-sm-12 col-md-12 col-lg-9 mt-2 mb-2 h-100">
                                <div class="row">
                                    <div>
                                        <div class="d-flex d-lg-none justify-end">
                                            <a class="btn btn-primary btn-sm" href="#mobile-view-sidbar"
                                                data-uc-toggle>{{ __('frontend-labels.my-account.account_info') }}</a>
                                        </div>
                                        <h4 class="mb-3 mt-3">
                                            <strong class="text-black dark:text-white">{{ $title }}</strong>
                                        </h4>
                                        @if ($subscription)
                                            <div class="card dark:border-gray-800 mt-4 ">
                                                <div class="card-body p-4 dark:bg-gray-800 dark:text-white">
                                                    <div class="d-flex align-items-center mb-4">
                                                        <div class="me-3 position-relative">
                                                            <div
                                                                class="bg-white text-primary rounded-circle p-3 shadow-sm">
                                                                <i class="bi bi-card-checklist fs-4"></i>
                                                            </div>
                                                            <div
                                                                class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-warning text-dark animate__animated animate__pulse animate__infinite">
                                                                {{ __('frontend-labels.mysubscription.new') }}</div>
                                                        </div>
                                                        <div>
                                                            <h5 class="fw-bold dark:text-white text-black">
                                                                {{ __('frontend-labels.mysubscription.subscription_hub') }}
                                                            </h5>
                                                        </div>
                                                    </div>
                                                    <h3
                                                        class="mb-4   dark:bg-gray-800  dark:text-white text-black text-center ">
                                                        {{ __('frontend-labels.mysubscription.your_subscription_details') }}
                                                    </h3>


                                                    <ul
                                                        class="list-unstyled dark:bg-gray-white dark:text-white text-black">
                                                        <ul class="list-unstyled">
                                                            <li
                                                                class="mb-2 d-flex justify-content-between align-items-center transition-all hover-scale hstack justify-between">
                                                                <strong>{{ __('frontend-labels.mysubscription.plan_name') }}</strong>
                                                                <span
                                                                    class="badge bg-light text-primary shadow-sm rounded-pill fs-15">{{ $subscription->plan->name ?? 'N/A' }}</span>
                                                            </li>
                                                            <li
                                                                class="mb-2 d-flex justify-content-between align-items-center transition-all hover-scale hstack justify-between">
                                                                <strong>{{ __('frontend-labels.mysubscription.start_date') }}</strong>
                                                                <span
                                                                    class=" ms-3">{{ $subscription->start_date->format('d M Y') ?? 'N/A' }}</span>
                                                            </li>
                                                            <li
                                                                class="mb-2 d-flex justify-content-between align-items-center transition-all hover-scale hstack justify-between">
                                                                <strong>{{ __('frontend-labels.mysubscription.end_date') }}</strong>
                                                                <span
                                                                    class=" ms-3">{{ $subscription->end_date->format('d M Y') ?? 'N/A' }}</span>
                                                            </li>
                                                            <li
                                                                class="mb-2 d-flex justify-content-between align-items-center transition-all hover-scale hstack justify-between">
                                                                <strong>{{ __('frontend-labels.mysubscription.status') }}</strong>
                                                                @php
                                                                    $status = strtolower(
                                                                        $subscription->status ?? 'N/A',
                                                                    );
                                                                    $badgeClass = match ($status) {
                                                                        'active' => 'bg-success',
                                                                        'expired' => 'bg-danger',
                                                                        'upcoming' => 'bg-info',
                                                                        'pending' => 'bg-warning',
                                                                        default => 'bg-secondary',
                                                                    };
                                                                @endphp
                                                                <span
                                                                    class="badge {{ $badgeClass }} shadow-sm rounded-pill fs-15 px-3 mt-2 ms-3">
                                                                    {{ ucfirst($status) }}
                                                                </span>
                                                            </li>

                                                        </ul>


                                                        <li class="mb-4">
                                                            <div
                                                                class="d-flex justify-content-between mb-2 justify-between">
                                                                <strong>{{ __('frontend-labels.mysubscription.articles') }}</strong>
                                                                <span
                                                                    class="text-primary fw-bold">{{ $subscription->article_count }}
                                                                    /
                                                                    {{ $subscription->feature->number_of_articles ?? 'N/A' }}</span>
                                                            </div>
                                                            <div class="progress glow-effect"s>
                                                                <div class="progress-bar progress-bar-striped progress-bar-animated bg-gradient-primary"
                                                                    role="progressbar"
                                                                    style="width: {{ ($subscription->article_count / max($subscription->feature->number_of_articles, 1)) * 100 }}%; border-radius: 10px;"
                                                                    aria-valuenow="{{ $subscription->article_count }}"
                                                                    aria-valuemin="0"
                                                                    aria-valuemax="{{ $subscription->feature->number_of_articles ?? 1 }}">
                                                                </div>
                                                            </div>
                                                        </li>

                                                        <li>
                                                            <div
                                                                class="d-flex justify-content-between mb-2 justify-between">
                                                                <strong>{{ __('frontend-labels.mysubscription.stories') }}</strong>
                                                                <span
                                                                    class="text-primary fw-bold">{{ $subscription->story_count }}
                                                                    /
                                                                    {{ $subscription->feature->number_of_stories ?? 'N/A' }}</span>
                                                            </div>
                                                            <div class="progress glow-effect mb-2">
                                                                <div class="progress-bar progress-bar-striped progress-bar-animated bg-gradient-primary"
                                                                    role="progressbar"
                                                                    style="width: {{ ($subscription->story_count / max($subscription->feature->number_of_stories, 1)) * 100 }}%; border-radius: 10px;"
                                                                    aria-valuenow="{{ $subscription->story_count }}"
                                                                    aria-valuemin="0"
                                                                    aria-valuemax="{{ $subscription->feature->number_of_stories ?? 1 }}">
                                                                </div>
                                                            </div>
                                                        </li>
                                                        <li>
                                                            <div
                                                                class="d-flex justify-content-between mb-2 justify-between">
                                                                <strong>{{ __('frontend-labels.mysubscription.e_paper_and_magazines') }}</strong>
                                                                <span
                                                                    class="text-primary fw-bold">{{ $subscription->e_paper_count }}
                                                                    /
                                                                    {{ $subscription->feature->number_of_e_papers_and_magazines ?? 'N/A' }}</span>
                                                            </div>
                                                            <div class="progress glow-effect mb-2">
                                                                <div class="progress-bar progress-bar-striped progress-bar-animated bg-gradient-primary"
                                                                    role="progressbar"
                                                                    style="width: {{ ($subscription->e_paper_count / max($subscription->feature->number_of_e_papers_and_magazines, 1)) * 100 }}%; border-radius: 10px;"
                                                                    aria-valuenow="{{ $subscription->e_paper_count }}"
                                                                    aria-valuemin="0"
                                                                    aria-valuemax="{{ $subscription->feature->number_of_e_papers_and_magazines ?? 1 }}">
                                                                </div>
                                                            </div>
                                                        </li>
                                                        <li>
                                                            <div
                                                                class="d-flex justify-content-between mb-2 justify-between">
                                                                <strong>{{ __('frontend-labels.mysubscription.total_viewed_items') }}</strong>
                                                                <span
                                                                    class="text-warning fw-bold ">{{ $subscription->article_count + $subscription->story_count + $subscription->e_paper_count }}
                                                                </span>
                                                            </div>
                                                        </li>
                                                        <li>
                                                            <div
                                                                class="d-flex justify-content-between mb-2 justify-between">
                                                                <strong>{{ __('frontend-labels.mysubscription.remaining_limits_articles') }}</strong>
                                                                <span
                                                                    class="text-primary fw-bold">{{ $subscription->feature->number_of_articles - $subscription->article_count ?? 'N/A' }}</span>
                                                            </div>
                                                        </li>
                                                        <li>
                                                            <div
                                                                class="d-flex justify-content-between mb-2 justify-between">

                                                                <strong>{{ __('frontend-labels.mysubscription.remaining_limits_stories') }}</strong>
                                                                <span
                                                                    class="text-primary fw-bold ">{{ $subscription->feature->number_of_stories - $subscription->story_count ?? 'N/A' }}</span>
                                                            </div>
                                                        </li>
                                                        <li>
                                                            <div
                                                                class="d-flex justify-content-between mb-2 justify-between">

                                                                <strong>{{ __('frontend-labels.mysubscription.remaining_limits_epaper') }}</strong>
                                                                <span
                                                                    class="text-primary fw-bold ">{{ $subscription->feature->number_of_e_papers_and_magazines - $subscription->e_paper_count ?? 'N/A' }}</span>
                                                            </div>
                                                        </li>
                                                    </ul>
                                                </div>
                                            </div>
                                        @else
                                            <div class="panel text-center">
                                                <img class="w-100 h-300px object-contain image uc-transition-opaque"
                                                    src="{{ asset('front_end/classic/images/place-holser/no-data.png') }}"
                                                    alt="No Transactions Found">
                                                <h1 class="h3 m-0 my-2">
                                                    {{ __('frontend-labels.mysubscription.no_subscription_found') }}</h1>
                                                <p class="fs-6 mb-2 md:fs-5">
                                                    {{ __('frontend-labels.mysubscription.no_subscription_message') }}
                                                </p>
                                                <a href="{{ route('membership.index') }}"
                                                    class="animate-btn btn btn-md btn-primary text-none gap-0">
                                                    <span>{{ __('frontend-labels.mysubscription.explore_membership_plans') }}</span>
                                                    <i class="icon icon-narrow unicon-arrow-right fw-bold"></i>
                                                </a>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
