@extends('front_end.' . $theme . '.layout.main')
@section('body')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">

    <!-- Breadcrumbs -->
    <div id="wrapper" class="wrap overflow-hidden-x">
        <div class="breadcrumbs panel z-1 py-2 bg-gray-25 dark:bg-gray-100 dark:bg-opacity-5 dark:text-white">
            <div class="container max-w-xl">
                <ul class="breadcrumb nav-x justify-center gap-1 fs-7 sm:fs-6 m-0">
                    <li><a href="{{ url('home') }}">{{ __('frontend-labels.home.title') }}</a></li>
                    <li><i class="unicon-chevron-right opacity-50"></i></li>
                    <li><a href="{{ url('membership') }}">{{ __('frontend-labels.membership.title') }}</a></li>
                    <li><i class="unicon-chevron-right opacity-50"></i></li>
                    <li><span class="opacity-70">{{ $title }}</span></li>
                </ul>
            </div>
        </div>
    </div>

    <!-- Page Header -->
    <div class="section py-3 sm:py-6 lg:py-9">
        <div class="container max-w-xl">
            <div class="panel vstack gap-1 sm:gap-6 lg:gap-9">
                <header class="page-header panel vstack text-center">
                    <h1 class="headingtag h3 lg:h1">{{ $title }}</h1>
                </header>
            </div>
        </div>
    </div>
    @if (session('error'))
        <div class="alert alert-danger text-center mb-4">
            {{ session('error') }}
        </div>
    @endif

    <div class="section py-3">
        <div class="container max-w-md mx-auto">
            <div class="panel bg-white dark:bg-gray-800 shadow-lg rounded-lg overflow-hidden">
                <div class="p-6">
                    <!-- Cancel Message -->
                    <div class="flex flex-col items-center justify-center">
                        <dotlottie-player src="{{ asset('front_end/classic/images/place-holser/Payment Failed.json') }}"
                            background="transparent" speed="1" loop autoplay
                            style="width: 100%; max-width: 300px; height: auto; aspect-ratio: 5/4; display: block; margin: 0 auto;">
                        </dotlottie-player>
                    </div>
                    <div class="text-center mb-6">
                        <h2 class="text-2xl font-bold mb-2 dark:text-white">{{ $title }}</h2>
                        <p class="text-gray-600 dark:text-gray-300">
                            {{ __('frontend-labels.payment_cancel.payment_not_completed') }}</p>
                    </div>

                    <!-- Further Actions -->
                    <div class="text-center space-y-6">
                        <div class="space-y-3">
                            <a href="{{ url('membership') }}" class="btn btn-primary w-full block mb-4">
                                <i class="bi bi-arrow-left"></i>
                                <span>{{ __('frontend-labels.payment_cancel.go_to_membership') }}</span>
                            </a>

                            <a href="{{ url('home') }}" class="btn btn-outline-primary w-full block">
                                <i class="bi bi-house-door"></i>
                                <span>{{ __('frontend-labels.payment_cancel.return_home') }}</span>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
