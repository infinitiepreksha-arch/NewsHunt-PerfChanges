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
                <a href="{{ url('admin/dashboard') }}" class="text-decoration-none ">{{ __('page.HOME') }}</a> /
                <a href="{{ url('admin/roles') }}" class="text-decoration-none ">{{ __('page.ROLE_MANAGEMENTS') }}</a> /
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
                <div class="card admin_cards border-0 rounded">
                    <div class="card-body p-4">
                        <form action="{{ route('roles.store') }}" method="POST" id="addRoleForm" data-parsley-validate  enctype="multipart/form-data">
                            @csrf
                            <div class="row gy-4">
                                <!-- Role Name -->
                                <div class="col-12">
                                    <div class="form-group">
                                        <label for="name" class="form-label fw-medium">{{ __('page.NAME') }} <span
                                                class="text-danger">*</span></label>
                                        <input type="text" name="name" id="name" class="form-control"
                                            placeholder="{{ __('page.NAME') }}" value="{{ old('name') }}">
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
                                @foreach ($groupedPermissions as $module => $actions)
                                    @php
                                        $moduleIndex++;
                                        $moduleClass = 'module-' . $moduleIndex;
                                    @endphp
                                    <div class="col-12 mb-4">
                                        <h2 class="fw-bold mb-3">{{ $module }}</h2>

                                        <!-- Module Select All -->
                                        @if (!empty($module))
                                            <div class="form-check mb-3">
                                                <input class="form-check-input select-all-module" type="checkbox"
                                                    id="selectAllModule{{ $moduleIndex }}">

                                                <label class="form-check-label fw-medium"
                                                    for="selectAllModule{{ $moduleIndex }}">
                                                    Select All for {{ $module }}
                                                </label>
                                            </div>
                                        @endif
                                        <div class="row">
                                            @if (!empty($module))
                                                @foreach ($actions as $permission)
                                                    <div class="col-md-4 col-sm-6 mb-3">
                                                        <div class="p-3 h-100 border rounded shadow-sm">
                                                            <div class="d-flex align-items-center">
                                                                <!-- Toggle -->
                                                                <div class="form-check form-switch m-0">
                                                                    <input
                                                                        class="form-check-input me-3 permission-checkbox {{ $moduleClass }}"
                                                                        type="checkbox" name="permission[]"
                                                                        value="{{ $permission->name }}"
                                                                        id="permission-{{ $permission->id }}">
                                                                </div>
                                                                <!-- Label & Text -->
                                                                <div>
                                                                    <label class="form-check-label fw-medium mb-0"
                                                                        for="permission-{{ $permission->id }}">
                                                                        {{ $permission->label }}
                                                                    </label>

                                                                    <div>
                                                                        <small>
                                                                            {{ __('Permission for ') . $module }}
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
                                    <button type="submit" id="submite_button" class="btn btn-primary me-1 mb-1">
                                        <i class="fas fa-save me-2"></i>{{ __('SUBMIT') }}
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
