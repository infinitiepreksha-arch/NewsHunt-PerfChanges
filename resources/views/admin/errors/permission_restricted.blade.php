@extends('admin.layouts.main')

@section('title')
    {{ __('page.CREDIT_PACKS') }}
@endsection

@section('pre-title')
    {{ __('page.CREDIT_PACKS') }}
@endsection

@section('content')
<div class="col-12 text-center py-5">
    
    <h1 class="display-1 fw-bold text-danger">403</h1>
    <h1 class="fw-bold mb-0 text-danger">Access Denied</h1>

    <div class="d-flex justify-content-center mb-0">
        <div class="col-6 col-md-8 col-lg-4">
            <img src="{{ asset('assets/images/access_Denied/no permission.png') }}" alt="Access Denied">
        </div>
    </div>

    <div class="d-inline-block mb-3">
        <h3 class="text-danger mb-0">
            You do not have permission to access this page.
        </h3>
    </div>

    <!-- Dashboard Button -->
    <div class="mt-4">
        <a href="{{ url('admin/dashboard') }}" class="btn btn-primary px-4 py-2">
            <i class="fa fa-home me-2"></i> Back to Dashboard
        </a>
    </div>

</div>
@endsection