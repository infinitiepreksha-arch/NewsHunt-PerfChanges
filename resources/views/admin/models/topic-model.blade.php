<!-- Add Topic Modal -->
<div id="addTopicModal" class="modal fade" tabindex="-1" role="dialog" aria-label="Add Topic Modal" 
    aria-labelledby="addTopicModalLabel"aria-hidden="true">
    <div class="modal-dialog">
        <form action="{{ route('topics.store') }}" class="form-horizontal" enctype="multipart/form-data" id="addTopicForm"
            method="POST" data-parsley-validate>
            @csrf
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addTopicModalLabel">{{ __('message.ADD_TOPIC') }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    @can('select-newslanguage-for-topic')
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
                                <strong id="name-error-message"></strong>
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
                        <label class="form-label">{{ __('message.TOPIC_NAME') }}<span
                                class="text-danger">*</span></label>
                        <input type="text" name="name" class="form-control col-sm-6 col-md-6"
                            placeholder="{{ __('message.PLEASE_ENTER_CHANNEL_NAME') }}" value="" required>
                        @if ($errors->has('name'))
                            <span class="help-block text-danger">
                                <strong id="name-error-message"></strong>
                            </span>
                        @endif
                    </div>
                    <div class="form-group mt-3">
                        <label class="form-label">{{ __('message.STATUS') }}</label>
                        <select class="form-control form-select" name="status">
                            <option value="active">{{ __('message.ACTIVE') }}</option>
                            <option value="inactive">{{ __('message.INACTIVE') }}</option>
                        </select>
                        <span class="help-block text-danger">
                            <strong id="status-error-message"></strong>
                        </span>
                    </div>
                    <div class="form-group mt-3">
                        <label for="topic-logo-input" class="form-label">{{ __('message.LOGO') }}<span
                                class="text-danger">*</span></label>
                        <input type="file" name="logo" id="topic-logo-input" class="form-control"
                            accept="image/*">
                        <span class="text-danger">
                            <strong id="logo-error-message"></strong>
                        </span>
                        <div class="mt-3">
                            <img id="topic-logo-privew" src="{{ asset('assets/images/no_image_available.png') }}"
                                alt="Logo Preview" class="img-privew">
                        </div>

                        <!-- Hidden container for cropping (initially hidden) -->
                        <div id="cropper-container" class="d-none">
                            <img id="cropper-image" src="" alt="Crop Image" />
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



<!-- Edit Topic Modal -->
<div id="editTopicModal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="editTopicModalLabel" aria-label="Edit Topic Modal"
    aria-hidden="true">
    <div class="modal-dialog">
        <form action="{{ route('topics.update', 0) }}" class="form-horizontal" enctype="multipart/form-data"
            id="edit-Topic-Form" method="POST" data-parsley-validate>
            @csrf
            @method('PUT')
            <input type="hidden" name="id" id="topic-id">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editTopicModalLabel">{{ __('message.EDIT_TOPIC') }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    @can('select-newslanguage-for-topic')
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
                        <label class="form-label">{{ __('message.TOPIC_NAME') }}<span
                                class="text-danger">*</span></label>
                        <input type="text" id="edit-topic-name" name="name"
                            class="form-control col-sm-6 col-md-6"
                            placeholder="{{ __('message.PLEASE_ENTER_TOPIC_NAME') }}" required>
                        @if ($errors->has('name'))
                            <span class="help-block text-danger">
                                <strong id="edit-name-error-message"></strong>
                            </span>
                        @endif
                    </div>
                    <div class="form-group mt-3">
                        <label class="form-label">{{ __('message.STATUS') }}</label>
                        <select id="edit-topic-status" class="form-control form-select" name="status">
                            <option value="active">{{ __('message.ACTIVE') }}</option>
                            <option value="inactive">{{ __('message.INACTIVE') }}</option>
                        </select>
                        <span class="help-block text-danger">
                            <strong id="edit-status-error-message"></strong>
                        </span>
                    </div>
                    <div class="form-group mt-3">
                        <label for="" class="form-label">{{ __('message.LOGO') }}<span
                                class="text-danger">*</span></label>
                        <input type="file" name="logo" id="edit-topic-logo-input" class="form-control"
                            accept="image/*">
                        <span class="text-danger">
                            <strong id="logo-error-message"></strong>
                        </span>
                        <div class="mt-3">
                            <img id="edit-topic-logo-privew"
                                src="{{ asset('assets/images/no_image_available.png') }}" alt="Logo Preview"
                                class="img-privew">
                        </div>

                        <!-- Hidden container for cropping (initially hidden) -->
                        <div id="edit-cropper-container" class="d-none">
                            <img id="edit-cropper-image" src="" alt="Crop img" />
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
