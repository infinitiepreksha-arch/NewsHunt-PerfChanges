@extends('admin.layouts.main')
@section('title')
    {{ __('page.POPULAR_POST_TIME_RANGE_SETTING') }}
@endsection
@section('pre-title')
    {{ __('page.POPULAR_POST_TIME_RANGE_SETTING') }}
@endsection
@section('page-title')
    <div class="row g-2 align-items-center">
        <div class="col">
            <!-- Page pre-title -->
            <div class="page-pretitle">
                <a href="{{ url('admin/dashboard') }}">{{ __('page.HOME') }}/</a>
                <a href="{{ url('admin/settings') }}">{{ __('page.SETTINGS') }}/</a>
                @yield('pre-title')
            </div>
            <h2 class="page-title">
                @yield('title')
            </h2>
        </div>
        <!-- Page title actions -->
        <div class="col-auto ms-auto d-print-none">
        </div>
    </div>
@endsection

@section('content')
    <section class="section">
        <form action="{{ route('settings.store') }}" method="post"  enctype="multipart/form-data">
            @csrf
            <div class="row d-flex mb-3">

                <div class="card mt-3 admin_cards m-2">
                    <div class="card-header">
                        <h3 class="card-title">{{ __('page.WEB_SETTINGS') }}</h3>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-sm-12 form-group mandatory mt-3">
                                <label for="popular_post_range" class="form-label">
                                    {{ __('page.POPULAR_POST_TIME_RANGE') }}
                                    <i class="bi bi-info-circle-fill text-muted m-1 cursor-pointer" data-bs-toggle="tooltip"
                                        data-bs-placement="right"
                                        title="{{ __('Once added, the popular posts will be displayed according to the option you select.') }}">
                                    </i>
                                </label>

                                @php
                                    $popularRange = $settings['popular_post_range'] ?? '24_hours';
                                @endphp

                                <select class="form-select" name="popular_post_range" id="popular_post_range">
                                    <option value="24 hours" {{ $popularRange == '24 hours' ? 'selected' : '' }}>24 Hours
                                    </option>
                                    <option value="48 hours" {{ $popularRange == '48 hours' ? 'selected' : '' }}>48 Hours
                                    </option>
                                    <option value="72 hours" {{ $popularRange == '72 hours' ? 'selected' : '' }}>72 Hours
                                    </option>
                                    <option value="1 week" {{ $popularRange == '1 week' ? 'selected' : '' }}>1 Week
                                    </option>
                                    <option value="1 month" {{ $popularRange == '1 month' ? 'selected' : '' }}>1 Month
                                    </option>
                                    <option value="1 year" {{ $popularRange == '1 year' ? 'selected' : '' }}>1 Year
                                    </option>
                                    <option value="lifetime" {{ $popularRange == 'lifetime' ? 'selected' : '' }}>Lifetime
                                    </option>
                                </select>
                            </div>

                            <div class="col-12 mt-3 d-flex justify-content-end">
                                <button class="btn btn-primary me-1 mb-1" type="submit"
                                    name="submit">{{ __('page.SAVE') }}</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </section>
@endsection
