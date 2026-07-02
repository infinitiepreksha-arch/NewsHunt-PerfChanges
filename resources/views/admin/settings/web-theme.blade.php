@extends('admin.layouts.main')
@section('title')
{{ __('page.WEB_THEME') }}
@endsection
@section('pre-title')
{{ __('page.WEB_THEME') }}
@endsection
@section('page-title')
    <div class="row g-2 align-items-center">
        <div class="col">
            <!-- Page pre-title -->
            <div class="page-pretitle">
                <a href="{{url('admin/dashboard')}}">{{__('page.HOME')}}/</a>
                <a href="{{url('admin/settings')}}">{{__('page.SETTINGS')}}/</a>
                @yield('pre-title')
            </div>
            <h2 class="page-title mt-2 m-1">
                @yield('title')
            </h2>
        </div>
        <!-- Page title actions -->
        <div class="col-auto ms-auto d-print-none">
            <a class="btn btn-primary" href="#" data-bs-toggle="modal" data-bs-target="#addWebTheme">{{ __('page.CREATE') }}</a>
        </div>
    </div>
@endsection
@section('content')
<section class="section m-2">
    <div class="card admin_cards">
        <div class="card-body">
            <div class="row">
                <div class="col-12">
                    <table class="table table-bordered text-nowrap border-bottom" id="theme_table" data-url="{{ route('web_theme.show',1) }}">
                        <thead>
                            <tr>
                                <th class="wd-15p border-bottom-0">{{ __('global.ID') }}</th>
                                <th class="wd-15p border-bottom-0">{{ __('global.IMAGE') }}</th>
                                <th class="wd-20p border-bottom-0">{{ __('global.NAME') }}</th>
                                <th class="wd-15p border-bottom-0">{{ __('global.SLUG') }}</th>
                                <th class="wd-15p border-bottom-0">{{ __('global.IS_DEFAULT') }}</th>
                                <th class="wd-15p border-bottom-0">{{ __('global.ACTION') }}</th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <input type="hidden" id="theme_status_url" value="{{route('web_theme.update.status')}}">
</section>
@include('admin.models.web-theme-model')
@endsection
