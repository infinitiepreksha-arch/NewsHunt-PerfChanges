@extends('admin.layouts.main')
@section('title')
    {{ __('page.TERMS_CONDITIONS') }}
@endsection
@section('pre-title')
    {{ __('page.TERMS_CONDITIONS') }}
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
    <section class="section m-2">
        <div class="card admin_cards">
            <form action="{{ route('settings.terms_and_conditions') }}" method="post" id="terms_and_conditions_form"
                class="create-form-without-reset">
                @csrf
                <div class="card-body">
                    <div class="row form-group">

                        <span class="help-block text-danger d-none" id="terms_and_conditions_error">
                            <strong>Please enter Terms and conditions</strong>
                        </span>
                        <div class="col-md-12 mb-5 position-relative">
                            <a href="{{ route('preview.termsconditions') }}" target="_blank" rel="noopener"
                                class="btn btn-outline-primary position-absolute end-0 top-0 me-3 mt-2 shadow-sm"
                                style="z-index: 10;" title="{{ __('page.PREVIEW') }} ({{ __('page.OPEN_NEW_TAB') }})">
                                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="currentColor"
                                    class="bi bi-eye" viewBox="0 0 16 16">
                                    <path
                                        d="M16 8s-3-5.5-8-5.5S0 8 0 8s3 5.5 8 5.5S16 8 16 8zM1.173 8a13.133 13.133 0 0 1 1.66-2.043C4.12 4.668 5.88 3.5 8 3.5c2.12 0 3.879 1.168 5.168 2.457A13.133 13.133 0 0 1 14.828 8c-.058.087-.122.183-.195.288-.335.48-.83 1.12-1.465 1.755C11.879 11.332 10.119 12.5 8 12.5c-2.12 0-3.879-1.168-5.168-2.457A13.134 13.134 0 0 1 1.172 8z" />
                                    <path
                                        d="M8 5.5a2.5 2.5 0 1 0 0 5 2.5 2.5 0 0 0 0-5zM4.5 8a3.5 3.5 0 1 1 7 0 3.5 3.5 0 0 1-7 0z" />
                                </svg>
                                <span class="d-none d-md-inline ms-1">{{ __('page.PREVIEW') }}</span>
                                <span class="d-inline d-md-none">{{ __('page.VIEW') }}</span>
                            </a>
                        </div>
                        <div class="col-md-12 mt-4">
                            <textarea id="tinymce_editor" name="terms_conditions" class="form-control col-md-7 col-xs-12"
                                aria-label="tinymce_editor">{{ $settings['terms_conditions'] ?? '' }}</textarea>
                        </div>
                    </div>
                    <div class="col-12 d-flex justify-content-end mt-3">
                        <button class="btn btn-primary me-1 mb-1" type="submit" id="terms_and_conditions_submit"
                            name="submit">{{ __('page.SAVE') }}</button>
                    </div>
                </div>
            </form>
        </div>
    </section>
@endsection 
