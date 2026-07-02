@extends('admin.layouts.main')

@section('title')
    {{ __('page.FIREBASE_SETTINGS') }}
@endsection
@section('pre-title')
    {{ __('page.FIREBASE_SETTINGS') }}
@endsection

@section('page-title')
    <div class="row g-2 align-items-center">
        <div class="col">
            <div class="page-pretitle">
                <a href="{{ url('admin/dashboard') }}">{{ __('page.HOME') }}/</a>
                <a href="{{ url('admin/settings') }}">{{ __('page.SETTINGS') }}/</a>
                @yield('pre-title')
            </div>
            <h2 class="page-title mt-2 m-1">
                @yield('title')
            </h2>
        </div>
        <!-- Page title actions -->
        <div class="col-auto ms-auto d-print-none">
        </div>
    </div>
@endsection
@section('content')
    <section class="section m-1">
        <div class="card admin_cards">
            <div class="card-header">
                <h3 class="card-title">
                    {{ __('page.FIREBASE_DETAILS') }}
                    <i class="bi bi-info-circle-fill text-muted m-1 cursor-pointer" data-bs-toggle="tooltip"
                        data-bs-placement="right"
                        title="Enter your Firebase project credentials including API key, Auth domain, Project ID, Storage bucket, Messaging sender ID, App ID, Measurement ID, and upload your service account file.">
                    </i>
                </h3>
                <span class="m-2 text-info ">
                    <a href="https://console.firebase.google.com/">Visit
                        console.firebase.google.com</a></span>

            </div>
            <form class="create-form-without-reset" action="{{ route('settings.firebase.update') }}" method="POST">
                <div class="card-body">
                    <div class="row row-cards">

                        <div class="col-sm-6 col-md-6">
                            <label for="role" class="form-label col-12 ">{{ __('page.API_KEY') }}</label>
                            <input name="apiKey" type="text" class="form-control" placeholder="{{ __('page.API_KEY') }}"
                                id="apiKey" value="{{ $settings['apiKey'] ?? '' }}" required="">
                        </div>

                        <div class="col-md-6">
                            <label for="name" class="form-label col-12 ">{{ __('page.AUTH_DOMAIN') }}</label>
                            <input type="text" required name="authDomain" class="form-control col-12"
                                placeholder="{{ __('page.AUTH_DOMAIN') }}" id="authDomain"
                                value="{{ $settings['authDomain'] ?? '' }}">
                        </div>

                        <div class="col-sm-6 col-md-6">
                            <label for="projectId" class="form-label col-12 ">{{ __('page.PROJECT_ID') }}</label>
                            <input type="text" id="projectId" class="form-control col-12"
                                placeholder="{{ __('page.PROJECT_ID') }}" name="projectId"
                                value="{{ $settings['projectId'] ?? '' }}" data-parsley-required="true">
                        </div>
                        <div class="col-md-6">
                            <label for="storageBucket" class="form-label col-12 ">{{ __('page.STORAGE_BUCKET') }}</label>
                            <input type="text" id="storageBucket" class="form-control col-12"
                                placeholder="{{ __('page.STORAGE_BUCKET') }}" name="storageBucket"
                                value="{{ $settings['storageBucket'] ?? '' }}" data-parsley-required="true">
                        </div>

                        <div class="col-sm-6 col-md-6">
                            <label for="messagingSenderId"
                                class="form-label col-12 ">{{ __('page.MESSAGING_SENDER_ID') }}</label>
                            <input type="text" id="messagingSenderId" class="form-control col-12"
                                placeholder="{{ __('page.MESSAGING_SENDER_ID') }}" name="messagingSenderId"
                                value="{{ $settings['messagingSenderId'] ?? '' }}" data-parsley-required="true">
                        </div>
                        <div class="col-md-6">
                            <label for="appId" class="form-label col-12 ">{{ __('page.APP_ID') }}</label>
                            <input type="text" id="appId" class="form-control col-12"
                                placeholder="{{ __('page.APP_ID') }}" name="appId"
                                value="{{ $settings['appId'] ?? '' }}" data-parsley-required="true">
                        </div>

                        <div class="col-sm-6 col-md-6">
                            <label for="measurementId" class="form-label col-12 ">{{ __('page.MEASUREMENT_ID') }}</label>
                            <input type="text" id="measurementId" class="form-control col-12"
                                placeholder="{{ __('page.MEASUREMENT_ID') }}" name="measurementId"
                                value="{{ $settings['measurementId'] ?? '' }}" data-parsley-required="true">
                        </div>
                        <div class="col-sm-6 col-md-6">
                            <label for="" class="form-label col-12">{{ __('page.SERVICE_ACCOUNT_FILE') }}</label>
                            <input name="service_file" class="form-control col-12" type="file" class="form-control"
                                required>
                        </div>
                    </div>
                </div>
                <div class="card-footer d-flex justify-content-end">
                    <button type="submit" class="btn btn-primary waves-effect waves-light">{{ __('page.SAVE') }}</button>
                </div>
            </form>
        </div>
    </section>
@endsection
