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
                <a href="{{ url('admin/dashboard') }}">Home/</a>
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
        @can('list-subscribers')
            <div class="card">
                <div class="card-body">
                    <div class="row">
                        <div class="col-12">
                            <table class="table table-bordered text-nowrap border-bottom  ms-2" id="subscribers-table"
                                data-url="{{ route('subscriber.show') }}">
                                <thead>
                                    <tr>
                                        <th class="wd-15p border-bottom-0">{{ __('global.ID') }}</th>
                                        <th class="wd-15p border-bottom-0">{{ __('global.EMAIL') }}</th>
                                    </tr>
                                </thead>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        @endcan
    </section>
@endsection
