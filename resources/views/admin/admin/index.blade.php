@extends('admin.layouts.main')

@section('title')
    {{ $title }}
@endsection
@section('pre-title')
    {{ $title }}
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
        <!-- Page title actions -->
        @can('create-adminuser')
            <div class="col-auto ms-auto d-print-none">
                <a class="btn btn-primary" href="{{ route('admin-users.create') }}">{{ __('page.CREATE_ADMIN') }}</a>
            </div>
        @endcan
    </div>
@endsection

@section('content')
    <section class="section">
        @can('list-adminuser')
            <div class="card">
                <div class="card-body">
                    <div class="row">
                        <div class="col-12">
                            <table class="table table-bordered text-nowrap border-bottom" id="admin_user_list"
                                data-url="{{ route('admin-users.show', 1) }}">
                                <thead>
                                    <tr>
                                        <th class="wd-15p border-bottom-0">{{ __('global.ID') }}</th>
                                        <th class="wd-15p border-bottom-0">{{ __('global.NAME') }}</th>
                                        <th class="wd-15p border-bottom-0">{{ __('global.ROLE') }}</th>
                                        <th class="wd-20p border-bottom-0">{{ __('global.EMAIL') }}</th>
                                        <th class="wd-15p border-bottom-0">{{ __('global.STATUS') }}</th>
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
                                    <img src="{{ asset('assets/images/access_Denied/no permission.png') }}"
                                        alt="Access Denied">
                                </div>
                            </div>

                            <div class="d-inline-block">
                                <h3 class="text-danger mb-0">You do not have permission to view the list of Admin.
                                </h3>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endcan
    </section>
@endsection
