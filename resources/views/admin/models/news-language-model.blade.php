<!-- Create News Language Modal -->
<div id="addNewsLanguageModal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="addNewsLanguageModalLabel" aria-label="Add News Language Modal"
    aria-hidden="true">
    <div class="modal-dialog">
        <form action="{{ route('admin.news-languages.store') }}" class="form-horizontal" enctype="multipart/form-data"
            id="addNewsLanguageForm" method="POST" data-parsley-validate>
            @csrf
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addNewsLanguageModalLabel">{{ __('page.ADD_NEWS_LANGUAGE') }}<span
                            class="text-danger">*</span></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <div class="modal-body">
                    <div class="form-group mt-3">
                        <label for="news_language_name" class="form-label">{{ __('page.NEWS_LANGUAGE_NAME') }}<span
                                class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="news_language_name" name="news_language_name"
                            placeholder="Enter news language" required>
                        @error('news_language_name')
                            <span class="help-block parsley-required ">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>

                    <div class="form-group mt-3">
                        <label for="news_language_code" class="form-label">{{ __('page.NEWS_LANGUAGE_CODE') }}<span
                                class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="news_language_code" name="news_language_code"
                            placeholder="Enter language code (e.g., en, gu, mr)" required>
                        @error('news_language_code')
                            <span class="help-block parsley-required">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>

                    <div class="form-group mt-3">
                        <label class="form-label">{{ __('page.STATUS') }}</label>
                        <select class="form-control form-select" name="status">
                            <option value="active">{{ __('page.ACTIVE') }}</option>
                            <option value="inactive">{{ __('page.INACTIVE') }}</option>
                        </select>
                        <span class="help-block text-danger">
                            <strong id="status-error-message"></strong>
                        </span>
                        
                    </div>

                    <div class="form-group mt-3">
                        <label for="news_languages_image" class="form-label">{{ __('page.NEWS_LANGUAGE_IMAGE') }}<span
                                class="text-danger">*</span></label>
                        <input type="file" name="news_languages_image" id="news_languages_image" class="form-control"
                            accept="image/*">
                        <div class="mt-3">
                            <img src="{{ asset('assets/images/no_image_available.png') }}"
                                id="news_languages_image_preview" alt="Image Preview"
                                class="edit_chen_img news_languages_image_css">
                            @error('news_languages_image')
                                <span class="parsley-required">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary"
                        data-bs-dismiss="modal">{{ __('page.CLOSE') }}</button>
                    <button type="submit" class="btn btn-primary">{{ __('page.SAVE') }}</button>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Edit News Language Modal -->
@foreach ($news_languages as $news_language)
    <div id="editNewsLanguageModal_{{ $news_language->id }}" class="modal fade" tabindex="-1" role="dialog" aria-label="Edit News Language Modal"
        aria-labelledby="editNewsLanguageModalLabel_{{ $news_language->id }}" aria-hidden="true">
        <div class="modal-dialog">
            <form id="editNewsLanguageForm_{{ $news_language->id }}" method="POST" enctype="multipart/form-data"
                action="{{ route('news_languages.update', $news_language->id) }}">
                {{ csrf_field() }}
                {{ method_field('PUT') }}

                <input type="hidden" name="id" value="{{ $news_language->id }}">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="editNewsLanguageModalLabel_{{ $news_language->id }}">
                            {{ __('page.UPDATE_NEWS_LANGUAGE') }}</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>

                    <div class="modal-body">
                        <div class="form-group mt-3">
                            <label for="news_language_name_{{ $news_language->id }}"
                                class="form-label">{{ __('page.UPDATE_NEWS_LANGUAGE_NAME') }}<span
                                            class="text-danger">*</span></label>
                            <input type="text" class="form-control"
                                id="news_language_name_{{ $news_language->id }}" name="news_language_name"
                                placeholder="Enter news language" value="{{ $news_language->name }}" required>
                        </div>

                        <div class="form-group mt-3">
                            <label for="news_language_code_{{ $news_language->id }}"
                                class="form-label">{{ __('page.UPDATE_NEWS_LANGUAGE_CODE') }}<span
                                            class="text-danger">*</span></label>
                            <input type="text" class="form-control"
                                id="news_language_code_{{ $news_language->id }}" name="news_language_code"
                                placeholder="Enter language code (e.g., en, gu, mr)"
                                value="{{ $news_language->code }}" required>
                        </div>


                        <div class="form-group mt-3">
                            <label class="form-label">{{ __('page.STATUS') }}</label>
                            <select class="form-control form-select" name="status">
                                <option value="active">{{ __('page.ACTIVE') }}</option>
                                <option value="inactive">{{ __('page.INACTIVE') }}</option>
                            </select>
                            <span class="help-block text-danger">
                                <strong id="status-error-message"></strong>
                            </span>
                        </div>

                        <div class="form-group mt-3">
                            <label for="news_languages_image_{{ $news_language->id }}"
                                class="form-label">{{ __('page.UPDATE_NEWS_LANGUAGE_IMAGE') }}<span
                                            class="text-danger">*</span></label>
                            <input type="file" name="news_languages_image"
                                id="news_languages_image_{{ $news_language->id }}" class="form-control"
                                accept="image/*">
                            <div class="mt-3">
                                <img src="{{ $news_language->image ? asset('storage/' . $news_language->image) : asset('assets/images/no_image_available.png') }}"
                                    id="news_languages_image_preview_{{ $news_language->id }}" alt="Image Preview"
                                    class="edit_chen_img news_languages_image_css">
                            </div>
                        </div>
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary"
                            data-bs-dismiss="modal">{{ __('page.CLOSE') }}</button>
                        <button type="submit" class="btn btn-primary">{{ __('page.SAVE') }}</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endforeach
