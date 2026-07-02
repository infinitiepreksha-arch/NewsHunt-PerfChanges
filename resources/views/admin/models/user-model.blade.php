{{-- Create user data Model --}}
<div id="userCreateModal" class="modal fade" tabindex="-1" aria-labelledby="myModalLabel2" aria-hidden="true">
    <div class="modal-dialog">
        <form action="{{ route('users.store') }}" class="form-horizontal" id="user-add-form" enctype="multipart/form-data"
            method="POST">
            @csrf
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="myModalLabel1">{{ __('message.USER_NAME') }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-12 mb-3">
                            <label for="add-user-name" class="form-label">{{ __('message.USER_NAME') }}<span
                                    class="text-danger">*</span></label>
                            <input type="text" id="add-user-name" name="name" class="form-control"
                                placeholder="Please enter user name" value="{{ $user->name ?? '' }}">
                            {{-- <span class="text-danger d-none" id="name-error-message"></span> --}}
                            <span class="parsley-required"><strong id="name-error"></strong></span>
                        </div>
                        <div class="col-12 mt-3">
                            <label for="add-user-email" class="form-label">{{ __('message.EMAIL') }}<span
                                    class="text-danger">*</span></label>
                            <input type="email" id="add-user-email" name="email" class="form-control"
                                placeholder="Please enter email" value="">
                            {{-- <span class="text-danger d-none" id="email-error-message"></span> --}}
                            <span class="parsley-required"><strong id="email-error"></strong></span>
                        </div>
                        <div class="col-12 mt-3">
                            <label for="password" class="form-label">{{ __('message.PASSWORD') }}<span
                                    class="text-danger">*</span></label>
                            <input type="password" id="password" name="password" class="form-control"
                                placeholder="Please enter password" value="">
                            {{-- <strong class="text-danger d-none" id="password-error-message"></strong> --}}
                            <span class="parsley-required"><strong id="password-error"></strong></span>
                        </div>
                        <div class="col-12 mt-3">
                            <label for="password-confirm" class="form-label">{{ __('message.CONFIRM_PASSWORD') }}<span
                                    class="text-danger">*</span></label>
                            <input type="password" id="password-confirm" name="password_confirmation"
                                class="form-control" placeholder="Please enter confirm password" value="">
                            {{-- <strong class="text-danger d-none" id="password-confirm-error-message"></strong> --}}
                            <span class="parsley-required"><strong id="password-confirm-error"></strong></span>
                        </div>
                        <div class="col-12 mt-3">
                            <label for="phone" class="form-label">{{ __('message.PHONE') }}<span
                                    class="text-danger">*</span></label>
                            <input type="tel" id="phone" name="phone" class="form-control"
                                placeholder="Please enter phone" inputmode="numeric">
                            {{-- <span class="parsley-required d-none" id="phone-error-message"></span> --}}
                            <span class="parsley-required"><strong id="phone-error"></strong></span>
                        </div>

                        <div class="form-group mt-2">
                            <label for="add-user-status" class="form-label">{{ __('message.STATUS') }}<span
                                    class="text-danger">*</span></label>
                            <select class="form-control form-select" name="status" id="add-user-status">
                                <option value="" selected>{{ __('message.SELECT_STATUS') }}</option>
                                <option value="active">{{ __('message.ACTIVE') }}</option>
                                <option value="inactive">{{ __('message.INACTIVE') }}</option>
                            </select>
                            <span class="parsley-required"><strong id="status-error"></strong></span>
                        </div>
                        <div class="col-12 mt-3">
                            <label for="add-user-profile-img" class="form-label">{{ __('message.PROFILE') }}<span
                                    class="text-danger">*</span></label>
                            <input type="file" name="profile" id="add-user-profile-img" class="mt-3 form-control"
                                accept=".jpg, .jpeg, .png, .svg">
                            {{-- <span class="text-danger"><strong id="profile-error-message"></strong></span> --}}
                            <span class="parsley-required"><strong id="profile-error"></strong></span>
                            <div class="mt-3">
                                <img id="add-user-profile-preview"
                                    src="{{ asset('assets/images/no_image_available.png') }}" alt="Logo Preview"
                                    class="img-preview">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit"
                        class="btn btn-primary waves-effect waves-light">{{ __('message.SAVE') }}</button>
                </div>
            </div>
        </form>
    </div>
</div>


{{-- Edite user data Model --}}
<div id="userEditModal" class="modal fade" tabindex="-1" aria-labelledby="myModalLabel1" aria-hidden="true">
    <div class="modal-dialog">
        <form action="users/3" class="form-horizontal" id="user-edit-form" enctype="multipart/form-data"
            method="POST" data-parsley-validate>
            @csrf
            @method('patch')
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="myModalLabel1">{{ __('message.EDIT_USER') }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-12mb-3">
                            <label for="edit-user-name" class="form-label">{{ __('message.USER_NAME') }}<span
                                    class="text-danger">*</span></label>
                            <input type="text" id="edit-user-name" name="name" class="form-control"
                                placeholder="Please enter user name" value="{{ $user->name ?? '' }}">
                            {{-- <span class="text-danger d-none" id="name-error-message"></span> --}}
                            <span class="parsley-required"><strong id="name-error"></strong></span>
                        </div>

                        <div class="col-12 mt-3">
                            <label for="user-email" class="form-label">{{ __('message.EMAIL') }}</label>
                            <input type="email" id="user-email" name="email" class="form-control"
                                placeholder="Please enter email" value="" disabled>
                        </div>

                        <div class="col-12 important mt-3">
                            <label for="phone" class="form-label">{{ __('message.PHONE') }}<span
                                    class="text-danger">*</span></label>
                            <input type="tel" id="edit-phone" name="phone" class="form-control"
                                placeholder="Please enter phone" inputmode="numeric" pattern="[0-9]*"
                                title="Please enter a valid phone number">
                            {{-- <span class="text-danger d-none" id="phone-error-message"></span> --}}
                            <span class="parsley-required"><strong id="phone-error"></strong></span>

                        </div>

                        <div class="form-group mt-2">
                            <label for="status" class="form-label">{{ __('message.STATUS') }}</label>
                            <select class="form-control form-select" name="status" id="status">
                                <option value="" disabled selected>{{ __('message.SELECT_STATUS') }}</option>
                                <option value="active">{{ __('message.ACTIVE') }}</option>
                                <option value="inactive">{{ __('message.INACTIVE') }}</option>
                            </select>
                            {{-- <span class="text-danger">
                                <strong id="status-error-message"></strong>
                            </span> --}}
                        </div>

                        <div class="col-12 mt-3">
                            <label for="" class="form-label">{{ __('message.PROFILE') }}<span
                                    class="text-danger">*</span></label>
                            <input type="file" name="profile" id="user-profile-img" class="mt-3 form-control"
                                accept=".jpg, .jpeg, .png, .svg">
                            {{-- <span class="text-danger d-none" id="user-profile-error-message"></span> --}}
                            <div class="mt-3 ">
                                <img id="user-profile-privew"
                                    src="{{ asset('assets/images/no_image_available.png') }}" alt="Logo Preview"
                                    class="img-privew">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit"
                        class="btn btn-primary waves-effect waves-light">{{ __('message.SAVE') }}</button>
                </div>
            </div>
        </form>
    </div>
</div>
{{-- Block User Model --}}
<div id="userBlockModal" class="modal fade" tabindex="-1" aria-labelledby="userBlockModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <form action="" class="form-horizontal" id="user-block-form" method="POST">
            @csrf
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="userBlockModalLabel">Block User</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-12 mb-3">
                            <label class="form-label">Block Type <span class="text-danger">*</span></label>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="block_type" id="block_comment"
                                    value="comment" checked>
                                <label class="form-check-label" for="block_comment">
                                    Comment Block (Soft Block)
                                </label>
                                <small class="text-muted d-block">User can login but cannot comment.</small>
                            </div>
                            <div class="form-check mt-2">
                                <input class="form-check-input" type="radio" name="block_type" id="block_full"
                                    value="full">
                                <label class="form-check-label" for="block_full">
                                    Full Block (Hard Block)
                                </label>
                                <small class="text-muted d-block">User cannot login or access the system.</small>
                            </div>
                        </div>
                        <div class="col-12 mt-3">
                            <label for="block_reason" class="form-label">Reason for Blocking</label>
                            <textarea id="block_reason" name="block_reason" class="form-control" rows="3" placeholder="Enter reason..."></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-danger">Block User</button>
                </div>
            </div>
        </form>
    </div>
</div>
@section('script')
    <script src="{{ asset('assets/js/custom/user-data-update.js') }}?v=<?= time() ?>"></script>
@endsection
