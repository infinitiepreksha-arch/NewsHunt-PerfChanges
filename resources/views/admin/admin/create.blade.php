@extends('admin.layouts.main')

@section('title')
    {{__('page.CREATE_ADMIN')}}
@endsection
@section('pre-title')
    {{__('page.ADMIN_MANAGEMENT')}}
@endsection

@section('page-title')
    <div class="row g-2 align-items-center">
        <div class="col">
            <!-- Page pre-title -->
            <div class="page-pretitle">
            <a href="{{url('admin/dashboard')}}">{{ __('page.HOME') }}/</a>
            <a href="{{url('admin/admin-users')}}">{{ __('page.ADMIN') }}/</a>
                @yield('pre-title')
            </div>
            <h2 class="page-title">
                @yield('title')
            </h2>
        </div>
    </div>
@endsection

@section('page-title')
    <div class="page-title">
        <div class="row">
            <div class="col-12 col-md-6 order-md-1 order-last">
                <h4>@yield('title')</h4>
            </div>
            <div class="col-12 col-md-6 order-md-2 order-first"></div>
        </div>
    </div>
@endsection

@section('content')
    <section class="section">
        <div class="col-12">
            <div class="card">
                <form action="{{ route('admin-users.store') }}" class="form-horizontal" id="addAdminUserForm" method="POST" data-parsley-validate>
                    @csrf
                    <div class="card-body">
                        <h3 class="card-title">{{ __('page.CREATE_ADMIN') }}</h3>
                        <div class="row row-cards">
                            <div class="col-sm-6 col-md-6">
                                <div class="mb-3">
                                    <label for="role" class="form-label col-12 ">{{ __('page.ROLE') }}</label>
                                    <select name="role" id="role" class="form-control" >
                                        <option value="">--{{ __('page.SELECT_ROLE') }}--</option>
                                        @foreach ($roles as $role)
                                            <option value="{{ $role->name }}">{{ $role->name }}</option>
                                        @endforeach
                                    </select>
                                    <span class="parsley-required"><strong id="role-error"></strong></span>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="name" class="form-label col-12 ">{{ __('page.NAME') }}</label>
                                    <input type="text" id="name" class="form-control col-12" placeholder="{{ __('page.ENTER_NAME') }}"
                                        name="name" >
                                    <span class="parsley-required"><strong id="name-error"></strong></span>
                                </div>
                            </div>

                            <div class="col-sm-6 col-md-6">
                                <div class="mb-3">
                                    <label for="email" class="form-label col-12 ">{{ __('page.EMAIL') }}</label>
                                    <input type="email" id="email" class="form-control col-12" placeholder="{{__('page.ENTER_EMAIL')}}"
                                        name="email" >
                                    <span class="parsley-required"><strong id="email-error"></strong></span>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="password" class="form-label col-12 ">{{ __('page.PASSWORD') }}</label>
                                    <input type="password" id="password" class="form-control col-12" placeholder="{{__('page.ENTER_PASSWORD')}}"
                                        name="password"
                                        >
                                    <span class="parsley-required"><strong id="password-error"></strong></span>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="password_confirmation" class="form-label col-12 ">{{ __('page.CONFIRM_PASSWORD') }}</label>
                                    <input type="password" id="password_confirmation" class="form-control col-12" placeholder="{{__('page.ENTER_CONFIRM_PASSWORD')}}"
                                        name="password_confirmation"
                                        >
                                    <span class="parsley-required"><strong id="password_confirmation-error"></strong></span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card-footer">
                        <button type="submit" id="submite_button"
                            class="btn btn-primary waves-effect waves-light">{{ __('page.SAVE') }}</button>
                    </div>
                </form>
            </div>
        </div>
    </section>
@endsection
