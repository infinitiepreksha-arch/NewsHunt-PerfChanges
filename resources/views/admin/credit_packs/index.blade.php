@extends('admin.layouts.main')

@section('title')
    {{ __('page.CREDIT_PACKS') }}
@endsection
@section('pre-title')
    {{ __('page.CREDIT_PACKS') }}
@endsection
@section('page-title')
    <div class="row g-2 align-items-center">
        <div class="col">
            <div class="page-pretitle">
                <a href="{{ url('admin/dashboard') }}">{{ __('page.HOME') }}</a>
                <a href="{{ url('admin/settings') }}">{{ __('page.SETTINGS') }}</a>
                @yield('pre-title')
            </div>
            <h2 class="page-title">
                @yield('title')
            </h2>
        </div>
    </div>
@endsection

@section('content')
    <div class="row">
        <section class="section">
            @can('credit-pack-create')
                <div class="row">
                    <div class="col-md-12">
                        <div class="card">
                            <form id="creditPackForm" action="{{ route('credit-packs.store') }}" method="post"
                                enctype="multipart/form-data">
                                @csrf
                                <div class="card-body">

                                    <div class="form-group row mt-2">
                                        <div class="col-md-12">
                                            <label for="name" class="form-label">{{ __('global.NAME') }} <span
                                                    class="text-danger">*</span></label>
                                            <input name="name" id="name" type="text" class="form-control"
                                                value="{{ old('name') }}">
                                            <span class="parsley-required error-text name_error"></span>
                                        </div>
                                    </div>

                                    <div class="form-group row mt-3">
                                        <div class="col-md-12">
                                            <label for="product_id" class="form-label">{{ __('global.PRODUCT_ID') }} <span
                                                    class="text-danger">*</span></label>
                                            <input name="product_id" id="product_id" type="text" class="form-control"
                                                value="{{ old('product_id') }}">
                                            <span class="parsley-required error-text product_id_error"></span>
                                        </div>
                                    </div>

                                    <div class="form-group row mt-3">
                                        <div class="col-md-6">
                                            <label for="credits" class="form-label">{{ __('global.CREDITS') }} <span
                                                    class="text-danger">*</span></label>
                                            <input name="credits" id="credits" type="number" class="form-control"
                                                value="{{ old('credits') }}">
                                            <span class="parsley-required error-text credits_error"></span>
                                        </div>
                                        <div class="col-md-6">
                                            <label for="price" class="form-label">{{ __('global.PRICE') }} <span
                                                    class="text-danger">*</span></label>
                                            <input name="price" id="price" type="number" class="form-control"
                                                value="{{ old('price') }}">
                                            <span class="parsley-required error-text price_error"></span>
                                        </div>
                                    </div>

                                    <div class="form-group row mt-3">
                                        <div class="col-md-6">
                                            <label for="savings_percent"
                                                class="form-label">{{ __('global.SAVINGS_PERCENT') }}</label>
                                            <input name="savings_percent" id="savings_percent" type="number"
                                                class="form-control" value="{{ old('savings_percent') }}">
                                            <span class="parsley-required error-text savings_percent_error"></span>
                                        </div>
                                    </div>

                                    <div class="form-group row mt-3">
                                        <div class="col-md-12">
                                            <label for="tagline" class="form-label">{{ __('global.TAGLINE') }}</label>
                                            <textarea id="tagline" name="tagline" class="form-control">{{ old('tagline') }}</textarea>
                                            <span class="parsley-required error-text tagline_error"></span>
                                        </div>
                                    </div>

                                    <div class="form-group row mt-3">
                                        <div class="col-md-6">
                                            <label class="form-check">
                                                <input class="form-check-input" type="checkbox" name="is_popular" value="1"
                                                    {{ old('is_popular') ? 'checked' : '' }}>
                                                <span class="form-check-label">{{ __('global.IS_POPULAR') }}</span>
                                            </label>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-check">
                                                <input class="form-check-input" type="checkbox" name="is_best_value"
                                                    value="1" {{ old('is_best_value') ? 'checked' : '' }}>
                                                <span class="form-check-label">{{ __('global.IS_BEST_VALUE') }}</span>
                                            </label>
                                        </div>
                                    </div>

                                    <div class="col-md-12 d-flex justify-content-end mt-3">
                                        <button class="btn btn-primary" type="submit">{{ __('global.SUBMIT') }}</button>
                                    </div>
                                </div>
                            </form>

                        </div>
                    </div>

                </div>
                <!-- CREDIT PACKS TABLE -->
                <div class="col-md-12 mt-5">
                    <div class="card">
                        <div class="card-body">
                            <table class="table table-bordered text-nowrap border-bottom mt-3" id="creditPackTable"
                                data-url="{{ route('credit-packs.index') }}">
                                <thead>
                                    <tr>
                                        <th>{{ __('global.ID') }}</th>
                                        <th>{{ __('global.NAME') }}</th>
                                        <th>{{ __('global.PRODUCT_ID') }}</th>
                                        <th>{{ __('global.CREDITS') }}</th>
                                        <th>{{ __('global.PRICE') }}</th>
                                        <th>{{ __('global.SAVINGS_PERCENT') }}</th>
                                        <th class="text-center">{{ __('global.IS_POPULAR') }}</th>
                                        <th class="text-center">{{ __('global.IS_BEST_VALUE') }}</th>
                                        <th>{{ __('global.ACTION') }}</th>
                                    </tr>
                                </thead>
                            </table>
                        </div>
                    </div>
                </div>
            @endcan
        </section>
    </div>
@endsection
