@extends('admin.layouts.main') {{-- Update the path to where `main.blade.php` is located --}}

@section('title', __('Change Password'))

@section('page-title')
    <h2>{{ __('Change Password') }}</h2>
@endsection

@section('content')
    <!-- Change Password Form -->
    <div class="container">
        <div id="changePasswordFormContainer">
            <form action="{{ route('change-password.update') }}" class="form-horizontal" id="changePasswordForm"
                enctype="multipart/form-data" method="POST" data-parsley-validate>
                @csrf
                <div class="card">
                    {{-- <div class="card-header">
                        <h5 class="card-title">{{ __('CHANGE_PASSWORD') }}</h5>
                    </div> --}}
                    <div class="card-body">
                        <div id="validationErrors" class="alert alert-danger d-none"></div>
                        <div class="row">
                            <div class="col-sm-12 mt-3">
                                <div class="form-group mandatory">
                                    <label for="old_password" class="form-label">{{ __('page.CURRENT_PASSWORD') }}</label>
                                    <input type="password" name="old_password" id="old_password"
                                        class="form-control form-control-solid"
                                        placeholder="{{ __('page.CURRENT_PASSWORD') }}" required />
                                </div>
                            </div>
                            <div class="col-sm-12 mt-3">
                                <div class="form-group mandatory">
                                    <label for="new_password" class="form-label">{{ __('page.NEW_PASSWORD') }}</label>
                                    <input type="password" name="new_password" id="new_password"
                                        class="form-control form-control-solid" placeholder="{{ __('page.NEW_PASSWORD') }}"
                                        data-parsley-minlength="8" data-parsley-uppercase="1" data-parsley-lowercase="1"
                                        data-parsley-number="1" data-parsley-special="1" required />
                                </div>
                            </div>
                            <div class="col-sm-12 mt-3">
                                <div class="form-group mandatory">
                                    <label for="confirm_password"
                                        class="form-label">{{ __('page.CONFIRM_PASSWORD') }}</label>
                                    <input type="password" id="confirm_password" name="confirm_password"
                                        class="form-control form-control-solid"
                                        placeholder="{{ __('page.CONFIRM_PASSWORD') }}" data-parsley-equalto="#new_password"
                                        required />
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card-footer">
                        <button type="submit"
                            class="btn btn-primary waves-effect waves-light">{{ __('page.SAVE') }}</button>
                    </div>
                </div>
            </form>
            <input type="hidden" id="current_locale" value="{{ app()->getLocale() }}">
        </div>

    </div>
@endsection

@section('css')
    <!-- Additional CSS if needed -->
@endsection

@section('js')
    <!-- Additional JavaScript if needed -->
@endsection
