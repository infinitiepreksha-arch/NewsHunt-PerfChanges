@extends('admin.layouts.main')

@section('title')
    {{ __('SYSTEM_SETTINGS') }}
@endsection
@section('page-pretitle')
    {{ __('System Settings') }}
@endsection

@section('page-title')
    <div class="row g-2 align-items-center">
        <div class="col">
            <div class="page-pretitle">
                <a href="{{ url('admin/dashboard') }}">{{ __('HOME') }}/</a>
                <a href="{{ url('admin/settings') }}">{{ __('SETTINGS') }}/</a>
                @yield('title')
            </div>
            <h4 class="page-title">@yield('title')</h4>
        </div>
        <div class="col-auto ms-auto d-print-none">
        </div>
    </div>
@endsection

@section('content')
    <section class="section">
        <form class="create-form-without-reset" action="{{ route('settings.store') }}" method="post"
            enctype="multipart/form-data" data-success-function="SystemSuccessFunction" data-parsley-validate>
            @csrf
            <div class="row d-flex mb-3">
                <div class="col-md-4">
                    <div class="card mt-3">
                        <div class="card-header">
                            <h3 class="card-title">{{ __('SCRIPTS') }}</h3>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-sm-12 form-group mandatory">
                                    <label for="firebase_project_id" class="form-label">{{ __('Header Script') }}</label>
                                    <textarea id="header_script" name="header_script" type="text" class="form-control"
                                        placeholder="{{ __('INSERT_HEADER_SCRIPT') }}">{{ $settings['header_script'] ?? '' }}</textarea>
                                </div>
                                <div class="col-sm-12 form-group mandatory mt-3">
                                    <label for="service_file" class="form-label">{{ __('Footer Script') }}</label>
                                    <textarea id="footer_script" name="footer_script" type="text" class="form-control"
                                        placeholder="{{ __('INSERT_FOOTER_SCRIPT') }}">{{ $settings['footer_script'] ?? '' }}</textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-8">
                    <div class="card mt-3">
                        <div class="card-header">
                            <h3 class="card-title">{{ __('SUBSCRIBE_MODEL') }}</h3>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-sm-6 form-group mandatory">
                                    <label for="subscribe_model_title"
                                        class="form-label ">{{ __('MODEL_TITLE') }}</label>
                                    <textarea id="subscribe_model_title" name="subscribe_model_title" class="form-control"
                                        placeholder={{ __('subscribe_model_title') }} required>{{ $settings['subscribe_model_title'] ?? '' }}</textarea>
                                </div>
                                <div class="col-sm-6 form-group mandatory">
                                    <label for="subscribe_model_sub_title"
                                        class="form-label ">{{ __('MODEL_SUB_TITLE') }}</label>
                                    <textarea id="subscribe_model_sub_title" name="subscribe_model_sub_title" class="form-control"
                                        placeholder={{ __('subscribe_model_sub_title') }} required>{{ $settings['subscribe_model_sub_title'] ?? '' }}</textarea>
                                </div>

                                <div class="col-sm-6 form-group mandatory mt-3">
                                    <label for="" class="form-label">{{ __('MODEL_STATUS') }}</label>
                                    <div class="form-check form-switch">
                                        <input type="hidden" name="subscribe_model_status" id="subscribe_model_status"
                                            class="checkbox-toggle-switch-input"
                                            value="{{ $settings['subscribe_model_status'] ?? 0 }}">
                                        <input class="form-check-input checkbox-toggle-switch" type="checkbox"
                                            role="switch"
                                            {{ !empty($settings['subscribe_model_status']) && $settings['subscribe_model_status'] == '1' ? 'checked' : '' }}
                                            id="switch_maintenance_mode"
                                            aria-checked="{{ !empty($settings['subscribe_model_status']) && $settings['subscribe_model_status'] == '1' ? 'true' : 'false' }}">
                                    </div>
                                </div>
                                <div class="col-sm-6 form-group mandatory">
                                    <label for="" class=" col-form-label ">{{ __('MODEL_IMAGE') }}</label>
                                    <input class="filepond" type="file" name="subscribe_model_image"
                                        id="subscribe_model_image">
                                    <img src="{{ $settings['subscribe_model_image'] ?? '' }}"
                                        data-custom-image="{{ asset('assets/images/logo/favicon.png') }}"
                                        class="img-privew mt-2 favicon_icon" alt="">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-12 d-grid gap-2 d-md-flex justify-content-md-end">
                        <button type="submit" value="btnAdd"
                            class="btn btn-primary me-1 mb-3">{{ __('SAVE') }}</button>
                    </div>
                </div>
            </div>

        </form>
    </section>
@endsection
