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
            <div class="page-pretitle">
                <a href="{{ url('admin/dashboard') }}">Home/</a>
                @yield('pre-title')
            </div>
            <h2 class="page-title">
                @yield('title')
            </h2>
        </div>
        @can('create-topic')
            <div class="col-auto ms-auto d-print-none">
                <a class="btn btn-primary" href="#" data-bs-toggle="modal"
                    data-bs-target="#addTopicModal">{{ __('page.CREATE_TOPIC') }}</a>
            </div>
        @endcan
    </div>
@endsection

@section('content')
    <section class="section">
        @can('list-topic')
            <div class="card">
                <div class="card-body">
                    <div class="row">
                        <div class="col-12">
                            <div class="d-flex justify-content-end mb-3">
                                <div class="input-icon">
                                    <div class="col-auto d-print-none">
                                        <div class="nav-item dropdown">
                                            <select id="topics_status" class="form-select mb-1">
                                                <option value="*" disabled selected>
                                                    {{ __('page.SELECT_STATUS') }}</option>
                                                <option value="*">{{ __('page.ALL') }}</option>
                                                <option value="active">{{ __('page.ACTIVE') }}</option>
                                                <option value="inactive">{{ __('page.INACTIVE') }}</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <table class="table table-bordered text-nowrap border-bottom" id="list-topic"
                                data-url="{{ route('topics.show', 1) }}">
                                <thead>
                                    <tr>
                                        <th class="wd-15p border-bottom-0">{{ __('global.ID') }}</th>
                                        <th class="wd-15p border-bottom-0">{{ __('global.IMAGE') }}</th>
                                        <th class="wd-15p border-bottom-0">{{ __('global.NAME') }}</th>
                                        <th class="wd-20p border-bottom-0">{{ __('global.SLUG') }}</th>
                                        <th class="wd-15p border-bottom-0">{{ __('global.STATUS') }}</th>
                                        <th class="wd-15p border-bottom-0">{{ __('global.ACTION') }}</th>
                                    </tr>
                                </thead>
                            </table>
                        </div>
                        <label for="name" id="Channel_id" value="list-channel"></label>
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
                    <h3 class="text-danger mb-0">You do not have permission to view the list of Topics.
                    </h3>
                </div>
            </div>
        @endcan
        <input type="hidden" id="topic_status_url" value="{{ route('topic.update.status') }}">
    </section>
    @include('admin.models.topic-model')
@endsection
