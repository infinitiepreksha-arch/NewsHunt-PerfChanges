@extends('admin.layouts.main')

@section('title')
    {{__('PERMISSION')}}
@endsection
@section('pre-title')
    {{__('PERMISSION')}}
@endsection

@section('page-title')
    <div class="row g-2 align-items-center">
        <div class="col">
            <!-- Page pre-title -->
            <div class="page-pretitle">
                @yield('pre-title')
            </div>
            <h2 class="page-title">
                @yield('title')
            </h2>
        </div>
        <!-- Page title actions -->

        <div class="col-auto ms-auto d-print-none">
            <a class="btn btn-primary" href="#" data-bs-toggle="modal" data-bs-target="#addPermissionModal">{{ __('ADD_PERMISSION') }}</a>
        </div>
    </div>
@endsection
@section('content')

<div class="card">
    @can('list-role')
    <div class="card-body">
        <div class="row">
            <div class="col-12">
                <table class="table table-bordered text-nowrap border-bottom" id="permissionAjex" data-url="{{ route('permission.show',1) }}">
                    <thead>
                        <tr>
                            <th class="wd-15p border-bottom-0">{{__('ID')}}</th>
                            <th class="wd-15p border-bottom-0">{{__('NAME')}}</th>
                            <th class="wd-20p border-bottom-0">{{__('GUARD NAME') }}</th>
                            <th class="wd-15p border-bottom-0">{{__('ACTION')}}</th>
                        </tr>
                    </thead>
                </table>
            </div>
            <input for="name" id="Channel_id" value="list-channel">
        </div>
    </div>
    @endcan
</div>

<!-- Add Permission Modal -->
<div id="addPermissionModal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="addPermissionModalLabel" aria-label="Add Permission Modal"
    aria-hidden="true">
    <div class="modal-dialog">
        <form action="{{ route('permission.store') }}" class="form-horizontal" enctype="multipart/form-data"
            id="add-Permission-Form" method="POST" data-parsley-validate>
            @csrf
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addPermissionModalLabel">{{ __('ADD_PERMISSION') }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label class="form-label">{{ __('PERMISSION_NAME') }}</label>
                        <input type="text" id="add-permission-name" name="name" class="form-control col-sm-6 col-md-6"
                            placeholder="{{ __('PLEASE_ENTER_PERMISSION_NAME') }}" required>
                    </div>
                    <div class="form-group mt-3">
                        <label class="form-label">{{ __('GUARD_NAME') }}</label>
                        <input type="text" id="add-permission-guard-name" name="guard_name" class="form-control col-sm-6 col-md-6"
                            placeholder="{{ __('PLEASE_ENTER_GUARD_NAME') }}" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary"
                        data-bs-dismiss="modal">{{ __('CLOSE') }}</button>
                    <button type="submit" class="btn btn-primary">{{ __('SAVE') }}</button>
                </div>
            </div>
        </form>
    </div>
</div>


<!-- Edit Permission Modal -->
<div id="editPermissionModal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="editPermissionModalLabel" aria-label="Edit Permission Modal"
    aria-hidden="true">
    <div class="modal-dialog">
        <form action="{{ route('permission.update', 1) }}" class="form-horizontal" enctype="multipart/form-data"
            id="edit-Permission-Form" method="POST" data-parsley-validate>
            @csrf
            @method('PUT')
            <input type="hidden" name="id" id="permission_id">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editPermissionModalLabel">{{ __('EDIT_PERMISSION') }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label class="form-label">{{ __('PERMISSION_NAME') }}</label>
                        <input type="text" id="edit-permission-name" name="name" class="form-control col-sm-6 col-md-6"
                            placeholder="{{ __('PLEASE_ENTER_PERMISSION_NAME') }}" required>
                    </div>
                    <div class="form-group mt-3">
                        <label class="form-label">{{ __('GUARD_NAME') }}</label>
                        <input type="text" id="edit-permission-guard-name" name="guard_name" class="form-control col-sm-6 col-md-6"
                            placeholder="{{ __('PLEASE_ENTER_GUARD_NAME') }}" required>
                    </div>
                    
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary"
                        data-bs-dismiss="modal">{{ __('CLOSE') }}</button>
                    <button type="submit" class="btn btn-primary">{{ __('SAVE') }}</button>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection
