@extends('admin.layouts.main')

@section('title')
    {{ $title }}
@endsection

@section('pre-title')
    {{ $pre_title }}
@endsection

@section('page-title')
    <div class="row g-2 align-items-center">
        <div class="col">
            <div class="page-pretitle">
                <a href="{{ url('admin/dashboard') }}">{{ __('page.HOME') }}/</a>
                @yield('pre-title')
            </div>
            <h2 class="page-title">{{ $title }}</h2>
        </div>
        <!-- Page title actions -->

        @can('create-emailtemplate')
            <div class="col-auto ms-auto d-print-none">
                <a class="btn btn-primary" href="{{ route('email-template.create') }}">
                    {{ __('page.CREATE_EMAIL_TEMPLATE') }}
                </a>
            </div>
        @endcan
    </div>
@endsection

@section('content')
    <section class="section">
        @can('list-emailtemplate')
            <div class="card">
                <div class="card-body">
                    <div class="row">
                        <div class="col-12 overflow-x-scroll">
                            <div class="d-flex justify-content-end mb-3">
                                <div class="input-icon">
                                    <div class="col-auto d-print-none">
                                        <div class="nav-item dropdown">
                                            <select id="template_status" class="form-select mb-1">
                                                <option value="*" selected>{{ __('page.ALL') }}</option>
                                                <option value="active">{{ __('page.ACTIVE') }}</option>
                                                <option value="inactive">{{ __('page.INACTIVE') }}</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <table class="table table-bordered text-nowrap border-bottom" id="EmailTemplate_list"
                                data-url="{{ route('email-template.datatable') }}">
                                <thead>
                                    <tr>
                                        <th class="wd-10p border-bottom-0">{{ __('global.ID') }}</th>
                                        <th class="wd-20p border-bottom-0">{{ __('global.TITLE') }}</th>
                                        <th class="wd-15p border-bottom-0">{{ __('global.SLUG') }}</th>
                                        <th class="wd-10p border-bottom-0">{{ __('page.POST_COUNT') }}</th>
                                        <th class="wd-10p border-bottom-0">{{ __('page.LAYOUT_WIDTH') }}</th>
                                        <th class="wd-15p border-bottom-0">{{ __('global.STATUS') }}</th>
                                        <th class="wd-10p border-bottom-0">{{ __('global.CREATED_AT') }}</th>
                                        <th class="wd-10p border-bottom-0">{{ __('global.ACTION') }}</th>
                                    </tr>
                                </thead>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            <input type="hidden" id="template_status_url" value="{{ route('email-template.update-status') }}">
        @else
            <div class="card mt-3">
                <div class="card-body">
                    <div class="row">
                        <div class="col-12 text-center py-5">
                            <h1 class="display-1 fw-bold text-danger">403</h1>
                            <h1 class="fw-bold mb-0 text-danger">Access Denied</h1>
                            <div class="d-flex justify-content-center mb-0">
                                <div class="col-8 col-md-8 col-lg-4">
                                    <img src="{{ asset('assets/images/access_Denied/no permission.png') }}"
                                        alt="Access Denied">
                                </div>
                            </div>

                            <div class="d-inline-block">
                                <h3 class="text-danger mb-0">You do not have permission to view the list of Email Template
                                    Details.
                                </h3>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endcan
    </section>
@endsection
