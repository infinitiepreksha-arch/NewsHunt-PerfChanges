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
    </div>
@endsection

@section('content')
    <section class="gradient-custom">
        @can('list-blocked-comment')
            <div class="container my-5 py-5">
                <div class="row d-flex justify-content-center">
                    <div class="col-md-12 col-lg-10 col-xl-12">
                        <div class="card">
                            <div class="card-body p-4">
                                <div class="row">
                                    <div class="col-12">
                                        <table class="ms-2 table table-bordered text-nowrap border-bottom"
                                            id="blocked_comments_table" data-url="{{ route('blocked-comments.show', 0) }}">
                                            <thead>
                                                <tr>
                                                    <th class="wd-10p border-bottom-0">{{ __('global.ID') }}</th>
                                                    <th class="wd-15p border-bottom-0">{{ __('global.BLOCKER_USER') }}</th>
                                                    <th class="wd-15p border-bottom-0">{{ __('global.COMMENT_OWNER') }}</th>
                                                    <th class="wd-25p border-bottom-0">{{ __('global.COMMENT') }}</th>
                                                    <th class="wd-20p border-bottom-0">{{ __('global.REASON') }}</th>
                                                    <th class="wd-15p border-bottom-0">{{ __('global.DATE') }}</th>
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
                    <h3 class="text-danger mb-0">You do not have permission to view the list of Blocked Comments.
                    </h3>
                </div>
            </div>
        @endcan
    </section>
@endsection
