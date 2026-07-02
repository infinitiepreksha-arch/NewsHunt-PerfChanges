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
            <!-- Page pre-title -->
            <div class="page-pretitle">
                <a href="{{ url('admin/dashboard') }}">{{ __('page.HOME') }}/</a>
                @yield('pre-title')
            </div>
            <h2 class="page-title">
                @yield('title')
            </h2>
        </div>

        @can('create-role')
            <div class="col-auto ms-auto d-print-none">
                <a class="btn btn-primary" href="{{ route('roles.create') }}">{{ __('page.CREATE_NEW_ROLE') }}</a>
            </div>
        @endcan
    </div>
@endsection
@section('content')
    @can('list-role')
        <div class="card">
            <div class="card-body">
                <div class="row">
                    <div class="col-12">
                        <table class="table table-bordered text-nowrap border-bottom" id="roal-list"
                            data-url="{{ route('roles.show', 1) }}">
                            <thead>
                                <tr>
                                    <th class="wd-15p border-bottom-0">{{ __('global.ID') }}</th>
                                    <th class="wd-20p border-bottom-0">{{ __('global.NO') }}</th>
                                    <th class="wd-15p border-bottom-0">{{ __('global.NAME') }}</th>
                                    <th class="wd-15p border-bottom-0">{{ __('global.ACTION') }}</th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    @else
        <div class="card mt-3">
            <div class="card-body">
                <div class="row">
                    <div class="col-12 text-center py-5">
                        <h1 class="display-1 fw-bold text-danger">403</h1>
                        <h1 class="fw-bold mb-0 text-danger">Access Denied</h1>
                        <div class="d-flex justify-content-center mb-0">
                            <div class="col-8 col-md-8 col-lg-4">
                                <img src="{{ asset('assets/images/access_Denied/no permission.png') }}" alt="Access Denied">
                            </div>
                        </div>

                        <div class="d-inline-block">
                            <h3 class="text-danger mb-0">You do not have permission to view the list of Role Management.
                            </h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endcan
@endsection
