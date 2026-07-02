@extends('admin.layouts.main')

@section('title')
    {{ $title }}
@endsection
@section('pre-title')
    {{ $pre_title }}
@endsection

@section('page-title')
    <div class="row g-2 align-items-center mb-4">
        <div class="col">
            <div class="page-pretitle text-muted fs-6">
                <a href="{{ url('admin/dashboard') }}" class="text-decoration-none text-primary">{{ __('page.HOME') }}</a> /
                <a href="{{ url('admin/roles') }}"
                    class="text-decoration-none text-primary">{{ __('page.ROLE_MANAGEMENTS') }}</a> /
                @yield('pre-title')
            </div>
            <h2 class="page-title fw-bold">
                @yield('title')
            </h2>
        </div>
    </div>
@endsection

@section('content')
    <div class="content-wrapper">
        <div class="row justify-content-center">
            <div class="col-lg-12">
                <div class="card shadow-sm border-0 rounded-3">
                    <div class="card-body p-4">
                        @if (isset($role))
                            <form action="{{ route('roles.update', $role->id) }}" method="POST" id="editRoleForm" data-parsley-validate  enctype="multipart/form-data">
                                @csrf
                                @method('PUT')
                                <div class="row gy-4">
                                    <!-- Role Name -->
                                    <div class="col-12">
                                        <div class="form-group">
                                            <label for="name" class="form-label fw-medium">{{ __('page.NAME') }} <span
                                                    class="text-danger">*</span></label>
                                            <input type="text" name="name" id="name" class="form-control"
                                                placeholder="{{ __('page.NAME') }}" value="{{ $role->name }}">
                                            <span class="parsley-required"><strong id="name-error"></strong></span>
                                        </div>
                                    </div>

                                    <!-- Global Select All -->
                                    <div class="col-12">
                                        <div class="form-check mb-3">
                                            <input class="form-check-input" type="checkbox" id="selectAllGlobal">
                                            <label class="form-check-label fw-medium" for="selectAllGlobal">
                                                Select All Permissions
                                            </label>
                                        </div>
                                    </div>
                                    <span class="parsley-required d-block mb-3"><strong id="permission-error"></strong></span>

                                    <!-- Permissions -->
                                    @php $moduleIndex = 0; @endphp
                                    @foreach ($groupedPermissions as $group => $permissions)
                                        @php
                                            $moduleIndex++;
                                            $moduleClass = 'module-' . $moduleIndex;
                                        @endphp
                                        <div class="col-12 mb-4">

                                            <h2 class="fw-bold mb-3">{{ $group }}</h2>

                                            <!-- Module Select All -->
                                            @if (!empty($group))
                                                <div class="form-check mb-3">
                                                    <input class="form-check-input select-all-module" type="checkbox"
                                                        id="selectAllModule{{ $moduleIndex }}">
                                                    <label class="form-check-label fw-medium"
                                                        for="selectAllModule{{ $moduleIndex }}">
                                                        Select All for {{ $group }}
                                                    </label>
                                                </div>
                                            @endif

                                            <div class="row">
                                                @if (!empty($group))
                                                    @foreach ($permissions as $permission)
                                                        <div class="col-md-4 col-sm-6 mb-3">
                                                            <div class="p-3 h-100 border rounded shadow-sm">
                                                                <div class="d-flex align-items-center">
                                                                    <!-- Toggle -->
                                                                    <div class="form-check form-switch m-0">
                                                                        <input
                                                                            class="form-check-input me-3 permission-checkbox {{ $moduleClass }}"
                                                                            type="checkbox" name="permission[]"
                                                                            value="{{ $permission->name }}"
                                                                            id="permission-{{ $permission->id }}"
                                                                            {{ $permission->is_checked ? 'checked' : '' }}>
                                                                    </div>
                                                                    <!-- Label & Text -->
                                                                    <div>
                                                                        <label class="form-check-label fw-medium mb-0"
                                                                            for="permission-{{ $permission->id }}">
                                                                            {{ $permission->label }}
                                                                        </label>
                                                                        <div>
                                                                            <small class="text-muted">
                                                                                {{ __('Permission for ') . $group }}
                                                                            </small>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    @endforeach
                                                @else
                                                    <div class="p-0">
                                                @endif
                                            </div>
                                        </div>
                                    @endforeach
                                    

                                    <!-- Save Button -->
                                    <div class="col-12 text-end">
                                        <a href="{{ url('admin/roles') }}" id="back_button"
                                            class="btn btn-secondary">{{ __('page.BACK') }}</a>
                                        <button type="submit" id='submite_button'
                                            class="btn btn-primary waves-effect waves-light">{{ __('page.SAVE') }}
                                        </button>
                                    </div>
                                </div>
                            </form>
                        @else
                            <p class="text-danger fw-medium p-3 bg-danger bg-opacity-10 rounded-3">
                                Error: Role not found.
                            </p>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
