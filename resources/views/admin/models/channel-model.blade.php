<!-- Add Channel Modal -->
<div id="addChannelModal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="addChannelModalLabel" aria-label="Add Channel Modal"
    aria-hidden="true">
    <div class="modal-dialog">
        <form action="{{ route('channels.store') }}" class="form-horizontal" enctype="multipart/form-data"
            id="addChannelForm" method="POST" data-parsley-validate>
            @csrf
            <div class="modal-content">

                <div class="modal-header">
                    <h5 class="modal-title" id="addChannelModalLabel">{{ __('message.ADD_CHANNEL') }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    @can('select-newslanguage-for-channel')
                        <div class="form-group">
                            <label for="news_language_id" class="form-label">{{ __('page.SELECT_NEWSLANGUAGE') }}<span
                                    class="text-danger">*</span></label>
                            <select class="form-control form-select select2" id="news_language_id" name="news_language_id"
                                required>
                                <option value="" disabled selected>{{ __('page.SELECT_NEWS_LANGUAGE') }}</option>
                                @foreach ($news_languages as $news_language)
                                    <option value="{{ $news_language->id }}"
                                        {{ isset($post->news_language_id) && $post->news_language_id == $news_language->id ? 'selected' : '' }}>
                                        {{ $news_language->name }}
                                    </option>
                                @endforeach
                            </select>
                            <span class="text-danger">
                                <strong id="news_language_id-error-message"></strong>
                            </span>
                        </div>
                    @else
                        <div class="form-group">
                            <label for="news_language_id" class="form-label">{{ __('page.SELECT_NEWSLANGUAGE') }}<span
                                    class="text-danger">*</span></label>
                            <div class="alert alert-warning mb-0 rounded py-2">
                                <i class="fas fa-exclamation-triangle me-2"></i>
                                {{ __('message.NO_PERMISSION_NEWSLANGUAGE') }}
                            </div>
                        </div>
                    @endcan
                    <div class="form-group mt-3">
                        <label for="add-channel-name" class="form-label">{{ __('message.CHANNEL_NAME') }}<span
                                class="text-danger">*</span></label>
                        <input type="text" name="name" class="form-control"
                            placeholder="Please enter channel name" id="add-channel-name" required>
                        <span class="text-danger">
                            <strong id="name-error-message"></strong>
                        </span>
                        <span class="text-danger">
                            <strong id="name.unique-error-message"></strong>
                        </span>
                    </div>
                    <div class="form-group mt-3">
                        <label for="add-channel-description"
                            class="form-label">{{ __('message.CHANNEL_DESCRIPTION') }}<span
                                class="text-danger">*</span></label>
                        <textarea name="description" class="form-control" placeholder="Please enter channel description"
                            id="add-channel-description" rows="3" required></textarea>
                        <span class="text-danger">
                            <strong id="description-error-message"></strong>
                        </span>
                    </div>
                    @can('update-status-channel')
                        <div class="form-group mt-3">
                            <label for="add-channel-status" class="form-label">{{ __('message.STATUS') }}</label>
                            <select class="form-control form-select" name="status" id="add-channel-status">
                                <option value="active">{{ __('message.ACTIVE') }}</option>
                                <option value="inactive">{{ __('message.INACTIVE') }}</option>
                            </select>
                            <span class="text-danger">
                                <strong id="status-error-message"></strong>
                            </span>
                        </div>
                    @endcan
                    <div class="form-group mt-3">
                        <label for="" class="form-label">{{ __('message.LOGO') }}<span
                                class="text-danger">*</span></label>
                        <input type="file" name="logo" id="channel-logo-input" class="form-control"
                            accept="image/*">
                        <span class="text-danger">
                            <strong id="logo-error-message"></strong>
                        </span>
                        <div class="mt-3">
                            <img id="channel-logo-privew" src="{{ asset('assets/images/no_image_available.png') }}"
                                alt="Logo Preview" class="img-privew">
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary"
                        data-bs-dismiss="modal">{{ __('message.CLOSE') }}</button>
                    <button type="submit" class="btn btn-primary">{{ __('message.SAVE') }}</button>
                </div>
            </div>
        </form>
    </div>
</div>



<!-- Edit Channel Modal -->
<div id="editChannelModal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="editChannelModalLabel" aria-label="Edit Channel Modal"
    aria-hidden="true">
    <div class="modal-dialog">
        <form action="{{ route('channels.update', 0) }}" class="form-horizontal" enctype="multipart/form-data"
            id="editChannelForm" method="POST" data-parsley-validate>
            @csrf
            @method('PUT')
            <input type="hidden" name="id" id="channel-id">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editChannelModalLabel">{{ __('message.EDIT_CHANNEL') }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    @can('select-newslanguage-for-channel')
                        <div class="form-group">
                            <label for="edit_news_language_id"
                                class="form-label">{{ __('page.SELECT_NEWSLANGUAGE') }}<span
                                    class="text-danger">*</span></label>
                            <select class="form-control form-select select2" id="edit_news_language_id"
                                name="news_language_id" required>
                                <option value="" disabled>{{ __('page.SELECT_NEWS_LANGUAGE') }}</option>
                                @foreach ($news_languages as $news_language)
                                    <option value="{{ $news_language->id }}"
                                        {{ isset($topic->news_language_id) && $topic->news_language_id == $news_language->id ? 'selected' : '' }}>
                                        {{ $news_language->name }}
                                    </option>
                                    <span class="text-danger">
                                        <strong id="edit-news-language-error-message"></strong>
                                    </span>
                                @endforeach
                            </select>
                        </div>
                    @else
                        <div class="form-group">
                            <label for="edit_news_language_id"
                                class="form-label">{{ __('page.SELECT_NEWSLANGUAGE') }}<span
                                    class="text-danger">*</span></label>
                            <div class="alert alert-warning mb-0 rounded py-2">
                                <i class="fas fa-exclamation-triangle me-2"></i>
                                {{ __('message.NO_PERMISSION_NEWSLANGUAGE') }}
                            </div>
                        </div>
                    @endcan
                    <div class="form-group mt-3">
                        <label for="" class="form-label">{{ __('message.CHANNEL_NAME') }}<span
                                class="text-danger">*</span></label>
                        <input type="text" name="name" class="form-control"
                            placeholder="Please enter channel name" id="edit-channel-name" required>
                        @if ($errors->has('name'))
                            <span class="text-danger">
                                <strong>{{ $errors->first('name') }}</strong>
                            </span>
                        @endif

                        <span class="text-danger">
                            <strong id="edit-name-error-message"></strong>
                        </span>

                    </div>
                    <div class="form-group mt-3">
                        <label for="edit-channel-description"
                            class="form-label">{{ __('message.CHANNEL_DESCRIPTION') }}<span
                                class="text-danger">*</span></label>
                        <textarea name="description" class="form-control" placeholder="Please enter channel description"
                            id="edit-channel-description" rows="3" required></textarea>
                        @if ($errors->has('description'))
                            <span class="text-danger">
                                <strong>{{ $errors->first('description') }}</strong>
                            </span>
                        @endif
                    </div>
                    <div class="form-group mt-3">
                        <label for="edit-channel-status" class="form-label">{{ __('message.STATUS') }}</label>
                        <select class="form-control form-select" name="status" id="edit-channel-status">
                            <option value="">{{ __('message.SELECT_STATUS') }}</option>
                            <option value="active">{{ __('message.ACTIVE') }}</option>
                            <option value="inactive">{{ __('message.INACTIVE') }}</option>
                        </select>
                        @if ($errors->has('status'))
                            <span class="text-danger">
                                <strong>{{ $errors->first('status') }}</strong>
                            </span>
                        @endif
                    </div>

                    <div class="form-group mt-3">
                        <label for="edit-channel-logo" class="form-label">{{ __('message.LOGO') }}<span
                                class="text-danger">*</span></label>
                        <input type="file" name="logo" id="edit-channel-logo" class="form-control"
                            accept="image/*">
                        <div class="mt-3">
                            <img id="edit-channel-privew" src="{{ asset('assets/images/no_image_available.png') }}"
                                alt="Logo Preview" class="edit_chen_img">
                            @if ($errors->has('logo'))
                                <span class="text-danger">
                                    <strong>{{ $errors->first('logo') }}</strong>
                                </span>
                            @endif
                        </div>

                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary"
                        data-bs-dismiss="modal">{{ __('message.CLOSE') }}</button>
                    <button type="submit" class="btn btn-primary">{{ __('message.SAVE') }}</button>
                </div>
            </div>
        </form>
    </div>
</div>
