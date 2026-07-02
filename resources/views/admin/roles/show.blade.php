@extends('admin.layouts.main')

@section('title')
    {{__('SHOW_ROLE')}}
@endsection
@section('pre-title')
    {{__('ROLE_MANAGEMENTS')}}
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
            <a class="btn btn-primary" href="{{ route('roles.index') }}"> {{ __('BACK') }}</a>
        </div>
    </div>
@endsection

@section('content')

    <div class="content-wrapper">

        <div class="row grid-margin">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-xs-12 col-sm-12 col-md-12">
                                <div class="form-group">
                                    <strong>{{__('NAME')}}:</strong>
                                    {{ $role->name }}
                                </div>
                            </div>
                            <div class="col-xs-12 col-sm-12 col-md-12">
                                <div class="row">
                                    @if(!empty($rolePermissions))
                                        @foreach($rolePermissions as $v)
                                            <div class="col-lg-3 col-sm-12 col-xs-12 col-md-3">
                                                <label for="" class="label label-success">{{ $v->name }}</label>
                                            </div>
                                        @endforeach
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
