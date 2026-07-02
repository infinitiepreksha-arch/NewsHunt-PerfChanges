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
                                                <h6 class="fs-4 lg:fs-5 xl:fs-5 fw-medium">
                                                    <a class="text-none text-dark dark:text-white hover:text-primary duration-150"
                                                        href="#" title="{{ $title }}">
                                                        <i class="bi bi-wallet2 fs-3"> </i>
                                                        {{ $title }}
                                                    </a>
                                                </h6>
                                            </article>
                                        </div>
                                        <div>
                                            <article class="post type-post panel d-flex gap-2">
                                                <h6 class="fs-4 lg:fs-5 xl:fs-5 fw-medium opacity-60">
                                                    <a class="text-none text-dark dark:text-white hover:text-primary duration-150"
                                                        href="{{ url('my-account/subscription') }}"
                                                        title="{{ __('frontend-labels.mysubscription.title') }}">
                                                        <svg width="24px" height="24px" viewBox="0 0 24 24"
                                                            xmlns="http://www.w3.org/2000/svg" fill="#000000">
                                                            <path
                                                                d="M14,6a7.17,7.17,0,0,0-1,.08A4.49,4.49,0,0,0,4,6.5V7A2,2,0,0,0,2,9v9a1.94,1.94,0,0,0,2,2H8.73A8,8,0,1,0,14,6ZM6,6.5a2.51,2.51,0,0,1,5-.24V7H6ZM14,20a6,6,0,1,1,6-6A6,6,0,0,1,14,20Zm-1.5-8v1h4a1,1,0,0,1,1,1v3a1,1,0,0,1-1,1H15v1H13V18H10.5V16h5V15h-4a1,1,0,0,1-1-1V11a1,1,0,0,1,1-1H13V9h2v1h2.5v2Z">
                                                            </path>
                                                        </svg></i> {{ __('frontend-labels.mysubscription.title') }}
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
                                                    href="#" title="{{ __('frontend-labels.favorite.title') }}">
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
                                                    <h6 class="fs-4 lg:fs-5 xl:fs-5 fw-medium dark:text-white ">
                                                        <a class="text-none text-dark dark:text-white hover:text-primary duration-150"
                                                            href="{{ url('my-account/transaction') }}"
                                                            title="{{ $title }}">
                                                            <i class="bi bi-wallet2 fs-3"></i> </i>
                                                            {{ $title }}
                                                        </a>
                                                    </h6>
                                                </article>
                                            </div>
                                            <div>
                                                <article class="post type-post panel d-flex gap-2">
                                                    <h6 class="fs-4 lg:fs-6 xl:fs-4 fw-medium opacity-60">
                                                        <a class="text-none text-dark dark:text-white hover:text-primary duration-150"
                                                            href="{{ url('my-account/subscription') }}"
                                                            title="{{ __('frontend-labels.mysubscription.title') }}">
                                                            <svg width="24px" height="24px" viewBox="0 0 24 24"
                                                                xmlns="http://www.w3.org/2000/svg" fill="#000000">
                                                                <path
                                                                    d="M14,6a7.17,7.17,0,0,0-1,.08A4.49,4.49,0,0,0,4,6.5V7A2,2,0,0,0,2,9v9a1.94,1.94,0,0,0,2,2H8.73A8,8,0,1,0,14,6ZM6,6.5a2.51,2.51,0,0,1,5-.24V7H6ZM14,20a6,6,0,1,1,6-6A6,6,0,0,1,14,20Zm-1.5-8v1h4a1,1,0,0,1,1,1v3a1,1,0,0,1-1,1H15v1H13V18H10.5V16h5V15h-4a1,1,0,0,1-1-1V11a1,1,0,0,1,1-1H13V9h2v1h2.5v2Z">
                                                                </path>
                                                            </svg> {{ __('frontend-labels.mysubscription.title') }}
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
                                <div class="d-flex d-lg-none justify-end">
                                    <a class="btn btn-primary btn-sm" href="#mobile-view-sidbar"
                                        data-uc-toggle>{{ __('frontend-labels.my-account.account_info') }}</a>
                                </div>
                                <div id="content-area" class="rounded-lg">
                                    <div id="transactions">
                                        <h4 class="mb-3 mt-3">
                                            <strong class="text-black dark:text-white">{{ $title }}</strong>
                                        </h4>

                                        <div class="panel">
                                            @if ($transactions)
                                                <div class="row g-4">
                                                    @forelse ($transactions as $transaction)
                                                        <div class="col-12 col-md-6 col-lg-4">
                                                            <div
                                                                class="card border-0 shadow-sm h-100 hover:shadow-md transition-all duration-300
                                                                bg-light dark:bg-gray-900 dark:text-white position-relative overflow-hidden">

                                                                <!-- Top border status color -->
                                                                <div class="position-absolute top-0 start-0 w-100"
                                                                    style="height: 5px; background: {{ $transaction->status === 'success' ? '#b81c1c' : '#6c757d' }};">
                                                                </div>

                                                                <div class="card-body pt-4">
                                                                    <h5 class="card-title mb-3 text-black dark:text-white">
                                                                        <strong>{{ $transaction->plan_name }}</strong>
                                                                    </h5>

                                                                    <ul class="list-unstyled  text-black dark:text-white">
                                                                        <li class="mb-2">
                                                                            <i class="fas fa-receipt me-2"></i>
                                                                            <strong>{{ __('frontend-labels.transaction_details.pay_id') }}::</strong>
                                                                            {{ $transaction->transaction_id }}
                                                                        </li>
                                                                        <li class="mb-2">
                                                                            <i class="fas fa-dollar-sign me-2"></i>
                                                                            <strong>{{ __('frontend-labels.transaction_details.amount') }}:</strong>
                                                                            {{ number_format($transaction->amount, 2) }}
                                                                        </li>
                                                                        <li class="mb-2">
                                                                            <i class="fas fa-calendar-alt me-2"></i>
                                                                            <strong>{{ __('frontend-labels.transaction_details.date') }}:</strong>
                                                                            {{ $transaction->created_at->format('d M Y') }}
                                                                        </li>
                                                                    </ul>

                                                                    <div class="mt-3 m-2">
                                                                        <span
                                                                            class="badge p-1 
                                                                            {{ $transaction->status === 'success' ? 'bg-success' : 'bg-secondary' }}">
                                                                            {{ ucfirst($transaction->status) }}
                                                                        </span>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    @empty
                                                        <div class="panel text-center">
                                                            <img class="w-100 h-300px object-contain image uc-transition-opaque"
                                                                src="{{ asset('front_end/classic/images/place-holser/no-data.png') }}"
                                                                alt="No Transactions Found">
                                                            <h1 class="h3 m-0 my-2">
                                                                {{ __('frontend-labels.transaction_details.no_transaction_found') }}
                                                            </h1>
                                                            <p class="fs-6 mb-2 md:fs-5">
                                                                {{ __('frontend-labels.transaction_details.no_transaction_message') }}
                                                            </p>
                                                            <a href="{{ route('membership.index') }}"
                                                                class="animate-btn btn btn-md btn-primary text-none gap-0">
                                                                <span>{{ __('frontend-labels.transaction_details.explore_membership_plans') }}</span>
                                                                <i class="icon icon-narrow unicon-arrow-right fw-bold"></i>
                                                            </a>
                                                        </div>
                                                    @endforelse
                                                </div>
                                            @else
                                                <div class="panel text-center">
                                                    <img class="w-100 h-300px object-contain image uc-transition-opaque"
                                                        src="{{ asset('front_end/classic/images/place-holser/no-data.png') }}"
                                                        alt="No Transactions Found">
                                                    <h1 class="h3 m-0 my-2">
                                                        {{ __('frontend-labels.transaction_details.no_transaction_found') }}
                                                    </h1>
                                                    <p class="fs-6 mb-2 md:fs-5">
                                                        {{ __('frontend-labels.transaction_details.no_transaction_message') }}
                                                    </p>
                                                    <a href="{{ route('membership.index') }}"
                                                        class="animate-btn btn btn-md btn-primary text-none gap-0">
                                                        <span>{{ __('frontend-labels.transaction_details.explore_membership_plans') }}</span>
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
    </div>
@endsection
