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
                        <li><span class="opacity-70"> {{ $title }}</span></li>
                    @else
                        <li><span class="opacity-70">{{ $title }}</span></li>
                    @endif
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
                                                <h6 class="fs-4 lg:fs-5 xl:fs-5 fw-medium">
                                                    <a class="text-none text-dark dark:text-white hover:text-primary duration-150"
                                                        href="{{ url('my-account') }}"
                                                        title="{{ __('frontend-labels.my-account.account_info') }}
">
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
                                                        <i class="bi bi-youtube fs-3">
                                                        </i>{{ __('frontend-labels.followings.title') }}
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
                                                        <i class="bi bi-bookmark fs-3">
                                                        </i>{{ __('frontend-labels.favorite.title') }}
                                                    </a>
                                                </h6>
                                            </article>
                                        </div>
                                        @if ($free_trial_status == '0')
                                        <div>
                                            <article class="post type-post panel d-flex gap-2">
                                                <h6 class="fs-4 lg:fs-5 xl:fs-5 fw-medium opacity-60">
                                                    <a class="text-none text-dark dark:text-white hover:text-primary duration-150"
                                                        href="{{ url('my-account/transaction') }}"
                                                        title="{{ __('frontend-labels.transaction_details.title') }}"">
                                                        <i class="bi bi-wallet2 fs-3"></i> </i>
                                                        {{ __('frontend-labels.transaction_details.title') }}
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
                                                        <i> <svg width="24px" height="24px" viewBox="0 0 24 24"
                                                                xmlns="http://www.w3.org/2000/svg" fill="#000000">
                                                                <path
                                                                    d="M14,6a7.17,7.17,0,0,0-1,.08A4.49,4.49,0,0,0,4,6.5V7A2,2,0,0,0,2,9v9a1.94,1.94,0,0,0,2,2H8.73A8,8,0,1,0,14,6ZM6,6.5a2.51,2.51,0,0,1,5-.24V7H6ZM14,20a6,6,0,1,1,6-6A6,6,0,0,1,14,20Zm-1.5-8v1h4a1,1,0,0,1,1,1v3a1,1,0,0,1-1,1H15v1H13V18H10.5V16h5V15h-4a1,1,0,0,1-1-1V11a1,1,0,0,1,1-1H13V9h2v1h2.5v2Z">
                                                                </path>
                                                            </svg> </i> {{ __('frontend-labels.mysubscription.title') }}
                                                    </a>
                                                </h6>
                                            </article>
                                        </div>
                                        @endif
                                        @if (auth()->user()->id !== 1)
                                            <div>
                                                <article class="post type-post panel d-flex gap-2">
                                                    <h6 class="fs-4 lg:fs-5 xl:fs-5 fw-medium opacity-60">
                                                        <a class="text-none text-dark dark:text-white hover:text-primary duration-150"
                                                            title="{{ __('frontend-labels.my-account.remove_account') }}"
                                                            id="user-delete-account">
                                                            <i class="bi bi-person-fill-slash fs-3">
                                                            </i>{{ __('frontend-labels.my-account.remove_account') }}
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
                                                <a class="text-none text-dark hover:text-primary duration-150"
                                                    href="#"
                                                    title="{{ __('frontend-labels.my-account.account_info') }}">
                                                    <article class="post type-post panel d-flex gap-2">
                                                        <h4 class="fs-4 lg:fs-6 xl:fs-4 fw-medium dark:text-white"><i
                                                                class="bi bi-person-circle fs-3"></i>
                                                            {{ __('frontend-labels.my-account.account_info') }}
                                                        </h4>
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
                                                <a class="text-none hover:text-primary duration-150"
                                                    href="{{ url('my-account/bookmarks') }}"
                                                    title="{{ __('frontend-labels.favorite.title') }}">
                                                    <article class="post type-post panel d-flex gap-2">
                                                        <h4 class="fs-4 lg:fs-6 xl:fs-4 fw-medium"><i
                                                                class="bi bi-bookmark fs-3"></i>
                                                            {{ __('frontend-labels.favorite.title') }}</h4>
                                                    </article>
                                                </a>
                                            </div>
                                            @if ($free_trial_status == '0')
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
                                                    <h6 class="fs-4 lg:fs-5 xl:fs-5 fw-medium opacity-60">
                                                        <a class="text-none text-dark dark:text-white hover:text-primary duration-150"
                                                            href="{{ url('my-account/subscription') }}"
                                                            title="{{ __('frontend-labels.mysubscription.title') }}">
                                                            <i class="bi bi-person-fill-slash fs-3"> </i>
                                                            {{ __('frontend-labels.mysubscription.title') }}
                                                        </a>
                                                    </h6>
                                                </article>
                                            </div>
                                            @endif
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
                                <div id="content-area" class="rounded-lg p-4 h-100">
                                    <div class="panel h-100">
                                        <div
                                            class="row child-cols-12 sm:child-cols-12 lg:child-cols-4 col-match gy-4 xl:gy-6 gx-2 sm:gx-4">
                                            <div class="box-title col-12">
                                                <form method="POST" action="{{ route('profile-update') }}"
                                                    id="user-account-form" enctype="multipart/form-data">
                                                    @csrf
                                                    <div class="contact-info position-relative text-center">
                                                        <div>
                                                            <img id="profile-image-preview"
                                                                src="{{ auth()->user()->profile ?? asset('front_end/classic/images/avatars/04.png') }}"
                                                                alt="Profile Preview"
                                                                class="img-fluid rounded-circle h-150px w-150px mx-auto">
                                                            <i class="bi bi-pencil-fill fs-6 position-absolute m-1 bg-primary text-white rounded-circle h-36px w-36px edit-icon"
                                                                onclick="document.getElementById('change-profile').click();"></i>
                                                        </div>
                                                        <input type="file" id="change-profile"
                                                            class="form-control py-1 w-full fs-6 dark:bg-black dark:text-dark d-none"
                                                            name="profile" value="" accept="image/*">
                                                        <strong
                                                            class="mt-1 text-center">{{ auth()->user()->name ?? 'N/A' }}</strong>
                                                    </div>
                                                    <div class="mt-3">
                                                        <h4 class="mb-3">
                                                            <strong>{{ __('frontend-labels.my-account.personal_information') }}</strong>
                                                        </h4>
                                                        <div class="row mt-3">

                                                            {{-- Name --}}
                                                            <div class="col-md-6 mb-2">
                                                                <label for="user_name"
                                                                    class="form-label">{{ __('frontend-labels.my-account.name') }}</label>
                                                                <input class="form-control form-control-sm" type="text"
                                                                    id="user_name" name="name"
                                                                    value="{{ auth()->user()->name ?? '' }}"
                                                                    placeholder="{{ __('frontend-labels.my-account.name') }}">
                                                                <strong class="help-block text-danger d-none"
                                                                    id="user_name_error"></strong>
                                                            </div>

                                                            {{-- Phone with intl-tel-input --}}
                                                            <div class="col-md-6 mb-2">
                                                                <label for="phone_number"
                                                                    class="form-label">{{ __('frontend-labels.my-account.phone') }}</label>
                                                                <div class="iti-container">
                                                                    <input class="form-control form-control-sm phone-input"
                                                                        id="phone_number" name="phone" type="tel"
                                                                        value="{{ old('phone', auth()->user()->mobile ?? '') }}"
                                                                        data-country-code="{{ auth()->user()->country_code ?? '' }}">
                                                                    <span class="help-block text-danger"
                                                                        style="display: none;">
                                                                        <strong></strong>
                                                                    </span>
                                                                </div>
                                                            </div>

                                                            {{-- Email (disabled) --}}
                                                            {{-- <div class="col-md-12 mb-2">
                                                                <label for="email"
                                                                    class="form-label">{{ __('frontend-labels.my-account.email') }}</label>
                                                                <input class="form-control form-control-sm" type="text"
                                                                    id="email_profile" name="email"
                                                                    value="{{ auth()->user()->email ?? '' }}"
                                                                    placeholder="{{ __('frontend-labels.my-account.email') }}">
                                                                <strong class="help-block text-danger d-none"
                                                                    id="email_error"></strong>
                                                            </div> --}}
                                                            <div class="col-md-12 mb-2">
                                                                <label for="email" class="form-label">
                                                                    {{ __('frontend-labels.my-account.email') }}
                                                                </label>

                                                                <input class="form-control form-control-sm" type="text"
                                                                    id="email_profile" name="email"
                                                                    value="{{ auth()->user()->email ?? '' }}"
                                                                    placeholder="{{ __('frontend-labels.my-account.email') }}">

                                                                <strong class="help-block text-danger d-none"
                                                                    id="email_error"></strong>
                                                            </div>

                                                            <div class="col-12">
                                                                <button class="btn btn-primary btn-xs"
                                                                    type="submit">{{ __('frontend-labels.my-account.submit') }}</button>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </form>
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
    </div>
    {{-- Edit Profile model --}}
    <div id="update-profile" data-uc-modal="overlay: true">
        <div class="uc-modal-dialog lg:max-w-500px bg-white text-dark dark:bg-gray-800 dark:text-white rounded">
            <button
                class="uc-modal-close-default p-0 icon-3 btn border-0 dark:text-white dark:text-opacity-50 hover:text-primary hover:rotate-90 duration-150 transition-all"
                type="button">
                <i class="unicon-close"></i>
            </button>
            <div class="panel vstack gap-2 md:gap-4 text-center">
                <div class="px-3 lg:px-4 py-4 lg:py-4 m-0 lg:mx-auto vstack justify-center items-center">
                    <div class="w-100">
                        <div class="panel vstack justify-center items-center gap-2 sm:gap-4 text-center">
                            <h4 class="h5 lg:h4 m-0">{{ __('frontend-labels.my-account.edit_profile') }}</h4>
                            <div class="panel vstack gap-2 w-100 sm:w-350px mx-auto">
                                <form method="POST" action="{{ route('profile-update') }}"
                                    class="vstack gap-2 user-model-img" enctype="multipart/form-data">
                                    @csrf
                                    <img id="profile-image-preview" src="{{ auth()->user()->profile ?? '' }}"
                                        alt="Profile Preview"
                                        class="img-fluid rounded-circle text-center h-100px w-100px">
                                    <input type="file" id="change-profile"
                                        class="form-control py-1 w-full fs-6 bg-white dark:border-white dark:border-gray-700 dark:text-dark"
                                        name="profile" accept="image/*">
                                    <input type="text"
                                        class="form-control py-1 w-full fs-6 bg-white dark:border-white dark:border-gray-700 dark:text-dark"
                                        name="name" value="{{ auth()->user()->name ?? '' }}" placeholder="Enter Name"
                                        required>
                                    <input type="email"
                                        class="form-control py-1 w-full fs-6 bg-white dark:border-white dark:border-gray-700 dark:text-dark"
                                        name="email" value="{{ auth()->user()->email ?? '' }}" disabled>
                                    <input type="text"
                                        class="form-control py-1 w-full fs-6 bg-white dark:border-white dark:border-gray-700 dark:text-dark"
                                        name="mobile"
                                        value="{{ auth()->user()->country_code . ' ' . auth()->user()->mobile }}"
                                        disabled>
                                    <button class="btn btn-primary btn-sm lg:mt-1"
                                        type="submit">{{ __('frontend-labels.my-account.update_profile') }}</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('script')
    <script defer src="{{ asset('front_end/' . $theme . '/js/custom/my-account.js') }}?v=<?= time() ?>"></script>
@endsection
