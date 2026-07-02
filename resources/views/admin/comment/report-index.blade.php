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
                {{ __('page.COMMENTS') }}
            </div>
            <h2 class="page-title">
                {{ $title }}
            </h2>
        </div>
        <!-- Page title actions -->
        @can('create-enewspapaer')
            <div class="col-auto ms-auto d-print-none gap-1">
                <div class="col-auto ms-auto d-print-none gap-1">
                    <a class="btn btn-primary"
                        href="{{ route('report-comments.create') }}">{{ __('page.CREATE_REPORTED_COMMENT') }}
                    </a>
                </div>
            </div>
        @endcan
    </div>
@endsection

@section('content')
    <section class="gradient-custom">
        @can('list-reported-comment')
            <div class="container my-5 py-5">
                <div class="row d-flex justify-content-center">
                    <div class="col-md-12 col-lg-10 col-xl-12">
                        <div class="card">
                            <div class="card-body p-4">
                                <div class="row">
                                    <div class="col-12">
                                        <table class=" ms-2 table table-bordered text-nowrap border-bottom"
                                            id="report_comments_table" data-url="{{ route('report-comments.show', 0) }}">
                                            <thead>
                                                <tr>
                                                    <th class="wd-15p border-bottom-0">{{ __('global.ID') }}</th>
                                                    <th class="wd-15p border-bottom-0">{{ __('global.USERNAME') }}</th>
                                                    <th class="wd-20p border-bottom-0">{{ __('global.REASON_TYPE') }}</th>
                                                    <th class="wd-20p border-bottom-0">{{ __('global.REPORT') }}</th>
                                                    <th class="wd-20p border-bottom-0">{{ __('global.COMMENT') }}</th>
                                                    <th class="wd-15p border-bottom-0">{{ __('global.DATE') }}</th>
                                                    <th class="wd-15p border-bottom-0">{{ __('global.ACTION') }}</th>
                                                </tr>
                                            </thead>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @else
            <div class="col-12 text-center py-5">
                <h1 class="display-1 fw-bold text-danger">403</h1>
                <h1 class="fw-bold mb-0 text-danger">Access Denied</h1>
                <div class="d-flex justify-content-center mb-0">
                    <div class="col-6 col-md-8 col-lg-4">
                        <img src="{{ asset('assets/images/access_Denied/no permission.png') }}" alt="Access Denied">
                    </div>
                </div>

                <div class="d-inline-block">
                    <h3 class="text-danger mb-0">You do not have permission to view the list of Reported Comment.
                    </h3>
                </div>
            </div>
        @endcan
    </section>
@endsection
