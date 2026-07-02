@extends('admin.layouts.main')
@section('title')
    {{ __('page.SYSTEM_UPDATE') }}
@endsection
@section('pre-title')
    {{ __('page.SYSTEM_UPDATE') }}
@endsection
@section('page-title')
    <div class="row g-2 align-items-center">
        <div class="col">
            <!-- Page pre-title -->
            <div class="page-pretitle">
                <a href="{{ url('admin/dashboard') }}"> {{ __('page.SYSTEM_UPDATE') }}/</a>
                <a href="{{ url('admin/settings') }}"> {{ __('page.SETTINGS') }}
                    /</a>
                @yield('pre-title')
            </div>
            <h2 class="page-title">
                @yield('title')
            </h2>
        </div>
    </div>
@endsection
@section('content')
    <section class="section">

        <div class="alert alert-primary alert-dismissible" role="alert">
            {{-- Add this to tags --}}
            {{ __('message.CLEAR_YOUR_BROWSER_CACHE_BY_PRESSINH') }} <kbd> CTRL+F5 </kbd>
            {{ __('message.AFTER_UPDATING_THE_SYSTEM') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        <div class="alert alert-primary alert-dismissible fade show" role="alert">
            <p class="mb-1">
                ⚠️ Make sure that <strong>post_max_size</strong> and <strong>upload_max_filesize</strong> settings on your
                server are always greater than the size of files you want to upload.
                If they are smaller, file uploads may fail.
            </p>

            <p class="mb-0">
                For complete instructions, please follow our -->
                <a href="https://newshunt.infinitietech.com/public/documentation/web/index.html" target="_blank"
                    class="text-primary text-decoration-underline">
                   <strong> documentation </strong>
                </a> .
            </p>

            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>


        <div class="card">

            <form class="create-form" action="{{ route('system-update.update') }}" method="POST"
                enctype="multipart/form-data">
                {{ csrf_field() }}

                <h4 class="card-header">
                    <div class="card-title">
                        <span class=" text-primary"> {{ __('page.CURRENT_VERSION') }}
                            <span class="badge bg-danger-lt">
                                {{ $system_version->version ?? '1.0.0' }}
                            </span>
                        </span>
                    </div>
                </h4>

                <div class="card-body">
                    <div class="row mt-1">
                        <div class="col-12">
                            <input name="update_file" hidden id="update-file" type="file" class="zip-pond">
                        </div>
                    </div>
                </div>
                <div class="card-footer text-center d-flex justify-content-center align-items-center">
                    {{-- Language update --}}
                    <button type="submit" name="btnAdd1" value="btnAd" class="btn btn-primary w-25 col-form-label">
                        {{ __('page.UPDATE_THE_SYSTEM') }}
                    </button>
                </div>
            </form>
        </div>
    </section>
@endsection
