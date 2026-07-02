@extends('admin.layouts.main')

@section('title')
    {{ __('page.SEND_NOTIFICATION') }}
@endsection
@section('pre-title')
    {{ __('page.SEND_NOTIFICATION') }}
@endsection
@section('page-title')
    <div class="row g-2 align-items-center">
        <div class="col">
            <!-- Page pre-title -->
            <div class="page-pretitle">
                <a href="{{ url('admin/dashboard') }}">{{ __('page.HOME') }}/</a>
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
    <div class="row">
        <section class="section">
            <div class="row">
                @can('create-notification')
                    <div class="col-md-6">
                        <div class="card">
                            <form action="{{ route('notification.store') }}" class="create-form needs-validation" method="post"
                                data-parsley-validate enctype="multipart/form-data">
                                <div class="card-body">
                                    @can('view-users-notification')
                                    @else
                                        <p class="alert alert-primary">You do not have permission to select or view specific users,
                                            which is why you can only
                                            send
                                            notifications
                                            to all users and not to a selected user.</p>
                                    @endcan
                                    <textarea id="user_id" name="user_id" class="d-none position-absolute" aria-label="user_id"></textarea>
                                    <textarea id="fcm_id" name="fcm_id" class="d-none position-absolute" aria-label="fcm_id_id"></textarea>

                                    <div class="form-group row">
                                        <div class="col-md-12 col-sm-12">
                                            <label for="send_to" class="form-label">{{ __('global.SELECT_USER') }} </label>
                                            <select id="send_to" name="send_to" class="form-control form-select" required>
                                                <option value="all">{{ __('global.ALL') }}</option>
                                                @can('view-users-notification')
                                                    <option value="selected">{{ __('global.SELECTED_ONLY') }}</option>
                                                @endcan
                                            </select>
                                        </div>
                                    </div>

                                    <div class="form-group row mt-3">
                                        <div class="col-md-12 col-sm-12">
                                            <label for="title" class="form-label">{{ __('global.TITLE') }} <span
                                                    class="text-danger">*</span></label>
                                            <input name="title" id="title" type="text" class="form-control"
                                                placeholder={{ __('global.TITLE') }} required>
                                        </div>
                                    </div>
                                    <div class="form-group row mt-3">
                                        <div class="col-md-12 col-sm-12">
                                            <label for="url" class="form-label">
                                                {{ __('global.URL') }} 
                                            </label>
                                            <input name="url" id="url" type="text" class="form-control"
                                                placeholder="https://example.com" value="{{ old('url') }}">
                                        </div>
                                    </div>
                                    <div class="form-group row mt-3">
                                        <div class="col-md-12">
                                            <label for="message" class="form-label">{{ __('global.MESSAGE') }} <span
                                                    class="text-danger">*</span></label>
                                            <textarea id="message" name="message" class="form-control" placeholder={{ __('global.MESSAGE') }} required></textarea>
                                        </div>
                                    </div>

                                    @can('upload-image-notification')
                                        <div class="form-group row mt-3" id="show_image">
                                            <div class="col-md-12 col-sm-12">
                                                <label for="image" class="form-label">{{ __('global.IMAGE') }}</label>
                                                <input id="Notification_img" name="file" type="file" id="image"
                                                    accept="image/*" class="form-control">
                                                <p id="img_error_msg" class="d-none badge rounded-pill bg-danger"></p>
                                                <div class="mt-3">
                                                    <img id="Notification_preview"
                                                        src="{{ asset('assets/images/no_image_available.png') }}"
                                                        class="img-privew" alt="">
                                                </div>
                                            </div>
                                        </div>
                                    @else
                                        <div class="form-group row mt-3">
                                            <div class="col-md-12 col-sm-12">
                                                <label for="image" class="form-label">{{ __('global.IMAGE') }}</label>
                                                <div class="alert alert-warning mb-0 rounded py-2">
                                                    <i class="fas fa-exclamation-triangle me-2"></i>
                                                    {{ __('message.NO_PERMISSION_IMAGE_NOTIFICATION') }}
                                                </div>
                                            </div>
                                        </div>
                                    @endcan
                                    <div class="col-md-12 d-flex justify-content-end mt-3">
                                        <button class="btn btn-primary" type="submit"
                                            name="submit">{{ __('global.SUBMIT') }}</button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                @else
                    <div class="col-md-6">
                        <div class="card card mt-3">
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-12 text-center py-5">
                                        <h1 class="display-1 fw-bold text-danger">403</h1>
                                        <h1 class="fw-bold mb-0 text-danger">Access Denied</h1>
                                        <div class="d-flex justify-content-center mb-0">
                                            <div class="col-4 col-md-6 col-lg-2">
                                                <img src="{{ asset('assets/images/access_Denied/no permission.png') }}"
                                                    alt="Access Denied">
                                            </div>
                                        </div>

                                        <div class="d-inline-block">
                                            <h3 class="text-danger mb-0">You do not have permission to Create Notification.
                                            </h3>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endcan

                @can('view-users-notification')
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-12">
                                        <table class="table table-bordered text-nowrap border-bottom" id="user_list_data"
                                            data-url="{{ route('userList') }}">
                                            <thead>
                                                <tr>
                                                    <th class="wd-1p border-bottom-0">
                                                        <input type="checkbox" id="select_all">
                                                    </th>
                                                    <th class="wd-15p border-bottom-0">{{ __('global.ID') }}</th>
                                                    <th class="wd-20p border-bottom-0">{{ __('global.NAME') }}</th>
                                                    <th class="wd-15p border-bottom-0">{{ __('global.NUMBER') }}</th>
                                                </tr>
                                            </thead>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @else
                    <div class="col-md-6">
                        <div class="card mt-3">
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-12 text-center py-5">
                                        <h1 class="display-1 fw-bold text-danger">403</h1>
                                        <h1 class="fw-bold mb-0 text-danger">Access Denied</h1>
                                        <div class="d-flex justify-content-center mb-0">
                                            <div class="col-4 col-md-6 col-lg-2">
                                                <img src="{{ asset('assets/images/access_Denied/no permission.png') }}"
                                                    alt="Access Denied">
                                            </div>
                                        </div>

                                        <div class="d-inline-block">
                                            <h3 class="text-danger mb-0">You do not have permission to view the list of
                                                Users.
                                            </h3>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endcan
            </div>
            @can('list-notification')
                <div class="card mt-3">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-12">
                                <div id="toolbar" class="mb-2">
                                </div>
                                <table class="table table-bordered text-nowrap border-bottom mt-3" id="notificationTable"
                                    data-url="{{ route('notification.show', 1) }}">
                                    <thead>
                                        <tr>
                                            <th class="wd-10p border-bottom-0">{{ __('global.ID') }}</th>
                                            <th class="wd-20p border-bottom-0">{{ __('global.TITLE') }}</th>
                                            <th class="wd-20p border-bottom-0">{{ __('global.SEND TO') }}</th>
                                            <th class="wd-15p border-bottom-0">{{ __('global.ACTION') }}</th>
                                        </tr>
                                    </thead>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            @else
                <div class="card mt-3">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-12 text-center py-5">
                                <h1 class="display-1 fw-bold text-danger">403</h1>
                                <h1 class="fw-bold mb-0 text-danger">Access Denied</h1>
                                <div class="d-flex justify-content-center mb-0">
                                    <div class="col-4 col-md-6 col-lg-2">
                                        <img src="{{ asset('assets/images/access_Denied/no permission.png') }}"
                                            alt="Access Denied">
                                    </div>
                                </div>

                                <div class="d-inline-block">
                                    <h3 class="text-danger mb-0">You do not have permission to view the list of Notifications.
                                    </h3>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endcan
        </section>
    </div>
@endsection
