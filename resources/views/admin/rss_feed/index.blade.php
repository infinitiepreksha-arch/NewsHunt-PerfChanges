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
            <h2 class="page-title">
                @yield('title')
            </h2>
        </div>
        <!-- Page title actions -->
        <div class="col-auto ms-auto d-print-none gap-1">
            @can('sync-all-rssfeed')
                <a class="btn btn-primary sync-btn fetch_all_feed" href="#"
                    id="fetch_rssfeed">{{ __('page.SYNC_FEEDS') }}</a>
            @endcan
            @can('create-rssfeed')
                <a class="btn btn-primary" href="#" data-bs-toggle="modal"
                    data-bs-target="#addRssFeedModal">{{ __('page.CREATE_RSS_FEED') }}</a>
            @endcan
        </div>
    </div>
@endsection
@section('content')
    <section class="section">
        @can('list-rssfeed')
            <div class="card">
                <div class="card-body">
                    <div class="row">
                        <div class="col-12">
                            <div class="d-flex justify-content-end mb-3">
                                <!-- Channel Filter -->
                                <div class="input-icon m-1">
                                    <div class="col-auto d-print-none">
                                        <select id="feed_channel" class="form-select mb-1">
                                            <option value="*" disabled selected>
                                                {{ __('page.SELECT_CHANNEL') }}</option>
                                            <option value="*">{{ __('page.ALL') }}</option>
                                            @foreach ($channels_lists as $channel)
                                                <option value="{{ $channel->id }}">{{ $channel->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <!-- Topic Filter -->
                                <div class="input-icon m-1">
                                    <div class="col-auto d-print-none">
                                        <select id="feed_topic" class="form-select mb-1">
                                            <option value="*" disabled selected>
                                                {{ __('page.SELECT_TOPIC') }}</option>
                                            <option value="*">{{ __('page.ALL') }}</option>
                                            @foreach ($topics_lists as $topic)
                                                <option value="{{ $topic->id }}">{{ $topic->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>

                                <div class="input-icon m-1">
                                    <div class="col-auto d-print-none">
                                        <div class="nav-item dropdown">
                                            <select id="feed_status" class="form-select mb-1">
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
                            <table class="table table-bordered text-nowrap border-bottom" id="rss-feed-list"
                                data-url="{{ route('rss-feeds.show', 1) }}">
                                <thead>
                                    <tr>
                                        <th class="wd-15p border-bottom-0">{{ __('global.ID') }}</th>
                                        <th class="wd-15p border-bottom-0">{{ __('global.CHANNELS') }}</th>
                                        <th class="wd-20p border-bottom-0">{{ __('global.TOPICS') }}</th>
                                        <th class="wd-15p border-bottom-0">{{ __('global.FEED_URL') }}</th>
                                        <th class="wd-15p border-bottom-0">{{ __('global.DATA_FORMAT') }}</th>
                                        <th class="wd-15p border-bottom-0">{{ __('global.SYNC_INTERVAL') }}</th>
                                        <th class="wd-15p border-bottom-0">{{ __('global.STATUS') }}</th>
                                        <th class="wd-15p border-bottom-0">{{ __('global.SYNC') }}</th>
                                        <th class="wd-15p border-bottom-0">{{ __('global.ACTION') }}</th>
                                    </tr>
                                </thead>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <input type="hidden" value="{{ route('rss-feeds.store') }}" id="rssfeedstore">
            <input type="hidden" value="{{ route('rsfeed.single-fetch') }}" id="rssfeedFetchSingle">
            <input type="hidden" id="channel_status_url" value="{{ route('rsfeed.update.status') }}">
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
                    <h3 class="text-danger mb-0">You do not have permission to view the list of Rss Feeds.
                    </h3>
                </div>
            </div>
        @endcan

    </section>
    @include('admin.models.rss-feed-model')
@endsection
