@extends('admin.layouts.main')

@section('title')
    {{ __('page.NOTIFICATION_SETTINGS') }}
@endsection
@section('pre-title')
    {{ __('page.NOTIFICATION_SETTINGS') }}
@endsection

@section('page-title')
    <div class="row g-2 align-items-center">
        <div class="col">
            <div class="page-pretitle">
                <a href="{{ url('admin/dashboard') }}">{{ __('page.HOME') }}/</a>
                <a href="{{ url('admin/settings') }}">{{ __('page.SETTINGS') }}/</a>
                @yield('pre-title')
            </div>
            <h2 class="page-title mt-2 m-1">
                @yield('title')
            </h2>
        </div>
        <!-- Page title actions -->
        <div class="col-auto ms-auto d-print-none">
        </div>
    </div>
@endsection
@section('content')
    <section class="section m-1">
        <div class="card admin_cards">
            <div class="card-header">
                <h3 class="card-title">
                    {{ __('page.NOTIFICATION_DETAILS') }}
                    <i class="bi bi-info-circle-fill text-muted m-1 cursor-pointer" data-bs-toggle="tooltip"
                        data-bs-placement="right"
                        title="Configure automatic notifications, daily limits, and limits during RSS feed synchronization.">
                    </i>
                </h3>
            </div>
            <form id="notification-setting-modal" action="{{ route('settings.notification-settings.store') }}"
                method="POST">
                @csrf
                <div class="card-body">
                    <div class="row row-cards">

                        <div class="col-sm-12 col-md-12 mb-3">
                            <label class="form-label">{{ __('page.AUTOMATIC_NOTIFICATIONS') }}</label>
                            <div class="form-check form-switch">
                                <input type="hidden" name="automatic_notifications" value="0">
                                <input class="form-check-input" type="checkbox" name="automatic_notifications"
                                    value="1" {{ ($settings['automatic_notifications'] ?? 1) == 1 ? 'checked' : '' }}>
                                <span
                                    class="form-check-label">{{ __('page.ENABLE_DISABLE_AUTOMATIC_NOTIFICATIONS') }}</span>
                            </div>
                        </div>

                        <div class="col-sm-6 col-md-6">
                            <label for="daily_notification_limit"
                                class="form-label col-12 ">{{ __('page.DAILY_NOTIFICATION_LIMIT') }}</label>
                            <input name="daily_notification_limit" type="number" class="form-control"
                                placeholder="{{ __('page.DAILY_NOTIFICATION_LIMIT') }}" id="daily_notification_limit"
                                value="{{ $settings['daily_notification_limit'] ?? 100 }}" min="0">
                            <span class="parsley-required"><strong id="daily_notification_limit-error"></strong></span>
                            <small class="text-muted">{{ __('page.MAX_NOTIFICATIONS_SENT_PER_DAY') }}</small>
                        </div>
                    </div>
                </div>
                <div class="card-footer d-flex justify-content-end">
                    <button type="submit" class="btn btn-primary waves-effect waves-light">{{ __('page.SAVE') }}</button>
                </div>
            </form>
        </div>
    </section>
@endsection
