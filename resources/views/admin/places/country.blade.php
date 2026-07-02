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
                <a href="{{url('admin/dashboard')}}">Home/</a>
                @yield('pre-title')
            </div>
            <h2 class="page-title">
                @yield('title')
            </h2>
        </div>
        <!-- Page title actions -->
        <div class="col-auto ms-auto d-print-none">
            <a class="btn btn-primary" href="#" data-bs-toggle="modal" data-bs-target="#countryModal">+
                {{ __('IMPORT_COUNTRIES') }} </a>
        </div>
    </div>
@endsection

@section('content')
    <section class="section">
        <div class="card">
            <div class="card-body">
                <div class="row">
                    <div class="col-12">
                        <table class="table table-bordered text-nowrap border-bottom" id="Counitry-list"
                            data-url="{{ route('countries.show', 1) }}">
                            <thead>
                                <tr>
                                    <th class="wd-15p border-bottom-0">{{ __('ID') }}</th>
                                    <th class="wd-15p border-bottom-0">{{ __('NAME') }}</th>
                                    <th class="wd-20p border-bottom-0">{{ __('FLAG') }}</th>
                                    <th class="wd-15p border-bottom-0">{{ __('ACTION') }}</th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                    <label for="name" id="Channel_id" value="list-channel"></label>
                </div>
            </div>
        </div>

        <div id="countryModal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel1" aria-hidden="true" aria-label="Import Countries Modal">
            <div class="modal-dialog modal-dialog-scrollable modal-fullscreen">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="myModalLabel1">{{ __('IMPORT_COUNTRy_DATA') }}</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-4">
                            <div class="input-group">
                                <input type="text" id="countrySearch" class="form-control" placeholder="Search countries...">
                                <span class="input-group-text"><i class="bi bi-search"></i></span>
                            </div>
                        </div>
                        <form class="create-form" action="{{ route('countries.import') }}" method="POST" data-success-function="successFunction">
                            @csrf
                            <div class="row g-3" id="countryList">
                                @foreach ($countries as $country)
                                    <div class="col-md-3 country-item" data-name="{{ strtolower($country['name']) }}">
                                        <div class="d-flex align-items-center mb-2">
                                            <input type="checkbox" id="{{ $country['id'] }}" name="countries[]"
                                                value="{{ $country['id'] }}"
                                                {{ $country['is_already_exists'] ? 'checked disabled' : '' }}
                                                class="form-check-input me-2">
                                            <label for="{{ $country['id'] }}"
                                                class="form-label">{{ $country['name'] . ' ' . $country['emoji'] }}</label>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                            <div class="text-end mt-3">
                                <input type="submit" value="{{ __('SAVE') }}" class="btn btn-primary">
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
            <!-- /.modal-content -->
        </div>
    </section>
@endsection
