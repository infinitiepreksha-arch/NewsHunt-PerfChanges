@extends('admin.layouts.main')

@section('title')
    {{ __('message.ADD_REPORT_TYPE') }}
@endsection
@section('pre-title')
    {{ __('message.ADD_REPORT_TYPE') }}
@endsection

@section('page-title')
    <div class="row g-2 align-items-center">
        <div class="col">
            <div class="page-pretitle">
                <a href="{{ url('admin/dashboard') }}">{{ __('page.HOME') }}/</a>
                <a href="{{ route('report-comments.index') }}">{{ __('message.REPORT_TYPES') }}</a>
            </div>
            <h2 class="page-title">{{ __('message.ADD_REPORT_TYPE') }}</h2>
        </div>
    </div>
@endsection

@section('content')
    <form action="{{ route('report-comments.store') }}" class="form-horizontal" id="addReportTypeForm" method="POST"
        data-parsley-validate>
        @csrf
        <div class="card mb-3">
            <div class="card-header">
                <h3 class="mb-0">{{ __('message.ADD_REPORT_TYPE') }}</h3>
            </div>
            <div class="card-body">
                <div id="report-types-container">
                    <div class="row mb-3 report-type-row">
                        <div class="col-md-11">
                            <div class="form-group">
                                <label for="title-0" class="form-label">{{ __('message.REPORT_TYPE') }}<span
                                        class="text-danger">*</span></label>
                                <input type="text" class="form-control" name="title[]" id="title-0"
                                    placeholder="{{ __('message.ENTER_REPORT_TYPE') }}">
                                <span class="parsley-required error-text" id="title-0-error"></span>
                            </div>
                        </div>
                        <div class="col-md-1 d-flex align-items-end">
                            <div class="form-group">
                                <button type="button" class="btn btn-success add-report-type">
                                    <i class="fas fa-plus"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card-footer">
                <button type="submit" class="btn btn-primary">{{ __('message.SAVE') }}</button>
            </div>
        </div>
    </form>

    <div class="col-md-12 mt-5">
        <div class="card">
            <div class="card-body">
                <table class="table table-bordered text-nowrap border-bottom mt-3" id="reportReasonTypeTable"
                    data-url="{{ route('report-comments.create') }}">
                    <thead>
                        <tr>
                            <th>{{ __('global.ID') }}</th>
                            <th>{{ __('global.TITLE') }}</th>
                            <th>{{ __('global.CREATED_AT') }}</th>
                            <th>{{ __('global.UPDATED_AT') }}</th>
                            <th>{{ __('global.ACTION') }}</th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>
@endsection
