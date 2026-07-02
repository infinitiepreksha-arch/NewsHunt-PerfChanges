@extends('admin.layouts.main')
@section('title')
    {{ __('page.SMTP_MAIL_CONFIGURATION') }}
@endsection
@section('pre-title')
    {{ __('page.SMTP_MAIL_CONFIGURATION') }}
@endsection
@section('page-title')
    <div class="row g-2 align-items-center">
        <div class="col">
            <!-- Page pre-title -->
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
            <form action="{{ route('settings.store') }}" method="post"
                enctype="multipart/form-data" data-success-function="SystemSuccessFunction" data-parsley-validate>
                @csrf
                <div class="card-body">
                    <div class="row">
                        <!-- Mail Mailer -->
                        <div class="col-md-6 form-group mandatory mt-3">
                            <label for="mail_mailer" class="form-label">{{ __('page.MAIL_MAILER') }}</label>
                            <input id="mail_mailer" name="mail_mailer" type="text" class="form-control"
                                placeholder="{{ __('page.ENTER_MAIL_MAILER') }}"
                                value="{{ $settings['mail_mailer'] ?? 'smtp' }}" required>
                        </div>

                        <!-- Mail Host -->
                        <div class="col-md-6 form-group mandatory mt-3">
                            <label for="mail_host" class="form-label">{{ __('page.MAIL_HOST') }}</label>
                            <input id="mail_host" name="mail_host" type="text" class="form-control"
                                placeholder="{{ __('page.ENTER_MAIL_HOST') }}" value="{{ $settings['mail_host'] ?? '' }}"
                                required>
                        </div>

                        <!-- Mail Port -->
                        <div class="col-md-6 form-group mandatory mt-3">
                            <label for="mail_port" class="form-label">{{ __('page.MAIL_PORT') }}</label>
                            <input id="mail_port" name="mail_port" type="number" class="form-control"
                                placeholder="{{ __('page.ENTER_MAIL_PORT') }}" value="{{ $settings['mail_port'] ?? '' }}"
                                min="1" required>
                        </div>

                        <!-- Mail Username -->
                        <div class="col-md-6 form-group mandatory mt-3">
                            <label for="mail_username" class="form-label">{{ __('page.MAIL_USERNAME') }}</label>
                            <input id="mail_username" name="mail_username" type="text" class="form-control"
                                placeholder="{{ __('page.ENTER_MAIL_USERNAME') }}"
                                value="{{ $settings['mail_username'] ?? '' }}" required>
                        </div>

                        <!-- Mail Password -->
                        <div class="col-md-6 form-group mt-3">
                            <label for="mail_password" class="form-label">{{ __('page.MAIL_PASSWORD') }}</label>
                            <input id="mail_password" name="mail_password" type="password" class="form-control"
                                placeholder="{{ __('page.ENTER_MAIL_PASSWORD') }}"
                                value="{{ $settings['mail_password'] ?? '' }}">
                        </div>

                        <!-- Mail Encryption -->
                        <div class="col-md-6 form-group mt-3">
                            <label for="mail_encryption" class="form-label">{{ __('page.MAIL_ENCRYPTION') }}</label>
                            <input id="mail_encryption" name="mail_encryption" type="text" class="form-control"
                                placeholder="{{ __('page.ENTER_MAIL_ENCRYPTION') }}"
                                value="{{ $settings['mail_encryption'] ?? '' }}">
                        </div>

                        <!-- Mail From Address -->
                        <div class="col-md-6 form-group mandatory mt-3">
                            <label for="mail_from_address" class="form-label">{{ __('page.MAIL_FROM_ADDRESS') }}</label>
                            <input id="mail_from_address" name="mail_from_address" type="email" class="form-control"
                                placeholder="{{ __('page.ENTER_MAIL_FROM_ADDRESS') }}"
                                value="{{ $settings['mail_from_address'] ?? '' }}" required>
                        </div>

                        <!-- Mail From Name -->
                        <div class="col-md-6 form-group mandatory mt-3">
                            <label for="mail_from_name" class="form-label">{{ __('page.MAIL_FROM_NAME') }}</label>
                            <input id="mail_from_name" name="mail_from_name" type="text" class="form-control"
                                placeholder="{{ __('page.ENTER_MAIL_FROM_NAME') }}"
                                value="{{ $settings['mail_from_name'] ?? '' }}" required>
                        </div>
                        <div class="col-12 mt-3 d-flex justify-content-end">
                            <button class="btn btn-primary me-1 mb-1" type="submit"
                                name="submit">{{ __('page.SAVE') }}</button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </section>
@endsection
