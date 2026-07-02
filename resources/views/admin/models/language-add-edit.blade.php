<div id="languageAddModal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel4" aria-label="Add Language Modal"
    aria-hidden="true">
    <div class="modal-dialog">
        <form action="{{ route('language.store_language') }}" class="form-horizontal" id="language-create-form"
            enctype="multipart/form-data" method="POST" data-parsley-validate>
            @csrf
            @method('POST')
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="myModalLabel4">{{ __('page.CREATE_LANGUAGE') }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-sm-12">
                            <div class="col-md-12 col-12">
                                <div class="form-group mandatory">
                                    <label for="add_name"
                                        class="form-label col-12">{{ __('page.LANGUAGE_NAME') }}</label>
                                    <input type="text" id="add_name" class="form-control col-12"
                                        placeholder="{{ __('page.LANGUAGE_NAME') }}" name="name"
                                        data-parsley-required="true">
                                </div>
                            </div>
                        </div>

                        <div class="col-sm-12 mt-3">
                            <div class="col-md-12 col-12">
                                <div class="form-group mandatory">
                                    <label for="add_name_in_english"
                                        class="form-label col-12">{{ __('page.LANGUAGE_NAME') }}({{ __('page.IN_ENGLISH') }})</label>
                                    <input type="text" id="add_name_in_english" name="name_in_english"
                                        class="form-control col-12" placeholder="{{ __('page.LANGUAGE_NAME') }}"
                                        data-parsley-required="true">
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-12 mt-3">
                            <div class="col-md-12 col-12">
                                <div class="form-group mandatory">
                                    <label for="add_code"
                                        class="form-label col-12">{{ __('page.LANGUAGE_CODE') }}</label>
                                    <input type="text" id="add_code" class="form-control col-12"
                                        placeholder="{{ __('page.LANGUAGE_CODE') }}" name="code"
                                        data-parsley-required="true">
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-12 col-md-12 form-group">
                            <label class="col-form-label ">{{ __('page.IMAGE') }}</label>
                            <div class="">
                                <input class="filepond" type="file" name="image" id="add_image">
                            </div>
                        </div>
                        <div class="col-sm-12 mt-3">
                            <div class="col-md-12 col-12">
                                <label for="add_rtl" class="form-label col-12">{{ __('page.RTL') }}</label>
                                <div class="form-group form-check form-switch">
                                    <input type="hidden" value="0" name="rtl" id="add_rtl">
                                    <input type="checkbox" class="form-check-input status-switch" id="add_rtl_switch"
                                        aria-label="add_rtl">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary waves-effect"
                        data-bs-dismiss="modal">{{ __('page.CLOSE') }}</button>
                    <button type="submit"
                        class="btn btn-primary waves-effect waves-light">{{ __('page.SAVE') }}</button>
                </div>
            </div>
        </form>
    </div>
</div>
