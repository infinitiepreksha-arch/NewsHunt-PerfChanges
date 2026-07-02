@extends('admin.layouts.main')
@section('title')
    {{ __('page.SUBSCRIPTION_MODEL_AND_HEADER_FOOTER_SCRIPT_SETTING') }}
@endsection
@section('pre-title')
    {{ __('page.SUBSCRIPTION_MODEL_AND_HEADER_FOOTER_SCRIPT_SETTING') }}
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
            <h2 class="page-title">
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
       <form action="{{ route('settings.subscription-store') }}" method="post" enctype="multipart/form-data">
            @csrf
            <div class="col-md-12">
                <div class="card mt-3">
                    <div class="card-header">
                        <h3 class="card-title">{{ __('page.SUBSCRIBE_MODEL') }}</h3>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-sm-6 form-group mandatory">
                                <label for="subscribe_model_title" class="form-label ">{{ __('page.MODEL_TITLE') }}</label>
                                <textarea id="subscribe_model_title" name="subscribe_model_title" class="form-control"
                                    placeholder="{{ __('page.SUBSCRIBE_MODEL_TITLE') }}" required>{{ $settings['subscribe_model_title'] ?? '' }}</textarea>
                            </div>
                            <div class="col-sm-6 form-group mandatory">
                                <label for="subscribe_model_sub_title"
                                    class="form-label ">{{ __('page.MODEL_SUB_TITLE') }}</label>
                                <textarea id="subscribe_model_sub_title" name="subscribe_model_sub_title" class="form-control"
                                    placeholder="{{ __('page.SUBSCRIBE_MODEL_SUB_TITLE') }}" required>{{ $settings['subscribe_model_sub_title'] ?? '' }}</textarea>
                            </div>

                            <div class="col-sm-6 form-group mandatory mt-3">
                                <label for="" class="form-label">{{ __('page.MODEL_STATUS') }}</label>
                                <div class="form-check form-switch">
                                    <input type="hidden" name="subscribe_model_status" id="subscribe_model_status"
                                        class="checkbox-toggle-switch-input"
                                        value="{{ $settings['subscribe_model_status'] ?? 0 }}">
                                    <input class="form-check-input checkbox-toggle-switch" type="checkbox" role="switch"
                                        {{ !empty($settings['subscribe_model_status']) && $settings['subscribe_model_status'] == '1' ? 'checked' : '' }}
                                        id="switch_maintenance_mode"
                                        aria-checked="{{ !empty($settings['subscribe_model_status']) && $settings['subscribe_model_status'] == '1' ? 'true' : 'false' }}">
                                </div>
                            </div>
                            <div class="col-sm-6 form-group mandatory">
                                <label for="" class=" col-form-label ">{{ __('page.MODEL_IMAGE') }}</label>
                                <input class="filepond" type="file" name="subscribe_model_image"
                                    id="subscribe_model_image">
                                <img src="{{ $settings['subscribe_model_image'] ?? '' }}"
                                    data-custom-image="{{ asset('assets/images/logo/favicon.png') }}"
                                    class="img-privew mt-2 favicon_icon" alt="">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-sm-12 mt-3 d-grid gap-2 d-md-flex justify-content-md-end">
                    <button type="submit" value="btnAdd" class="btn btn-primary me-1 mb-3">{{ __('page.SAVE') }}</button>
                </div>
            </div>
        </form>

        <form action="{{ route('settings.store') }}" method="post">
            @csrf
            <div class="col-md-12">
                <div class="card mt-3">
                    <div class="card-header">
                        <h3 class="card-title">{{ __('page.SCRIPTS') }}</h3>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-sm-12 form-group mandatory">
                                <label for="firebase_project_id" class="form-label">{{ __('page.HEADER_SCRIPT') }}</label>
                                <textarea id="header_script" name="header_script" type="text" class="form-control"
                                    placeholder="{{ __('page.INSERT_HEADER_SCRIPT') }}">{{ $settings['header_script'] ?? '' }}</textarea>
                            </div>
                            <div class="col-sm-12 form-group mandatory mt-3">
                                <label for="service_file" class="form-label">{{ __('page.FOOTER_SCRIPT') }}</label>
                                <textarea id="footer_script" name="footer_script" type="text" class="form-control"
                                    placeholder="{{ __('page.INSERT_FOOTER_SCRIPT') }}">{{ $settings['footer_script'] ?? '' }}</textarea>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
             <div class="col-sm-12 mt-3 d-grid gap-2 d-md-flex justify-content-md-end">
                    <button type="submit" value="btnAdd" class="btn btn-primary me-1 mb-3">{{ __('page.SAVE') }}</button>
                </div>
        </form>
    </section>
@endsection
