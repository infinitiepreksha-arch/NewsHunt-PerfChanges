@extends('admin.layouts.main')
@section('title')
    {{ __('page.SOCIAL_LINKS_AND_OTHER_SETTINGS') }}
@endsection
@section('pre-title')
    {{ __('page.SOCIAL_LINKS_AND_OTHER_SETTINGS') }}
@endsection
@section('page-title')
    <div class="row g-2 align-items-center">
        <div class="col">
            <!-- Page pre-title -->
            <div class="page-pretitle">
                <a href="{{ url('admin/dashboard') }}">{{ __('page.HOME') }}/</a>
                <a href="{{ url('admin/settings') }}">{{ __('page.SETTINGS') }}/</a>
                @yield('pre-title')
            </div>
            <h2 class="page-title mt-2 m-1">
                @yield('title')
            </h2>
        </div>
        <!-- Page title actions -->
        <div class="col-auto ms-auto d-print-none">
        </div>
    </div>
@endsection

@section('content')
    <section class="section m-2">
        <!-- Social Media Links Form -->
        <form action="{{ route('settings.store') }}" method="post">
            @csrf
            <div class="row d-flex">
                <div class="card mt-3 admin_cards">
                    <div class="card-header">
                        <h3 class="card-title">{{ __('page.SOCIAL_MEDIA_LINKS') }}
                            <i class="bi bi-info-circle-fill text-muted m-1 cursor-pointer" data-bs-toggle="tooltip"
                                data-bs-placement="right"
                                title="{{ __('Add links to your official social media profiles to connect with your audience and increase engagement.') }}"></i>
                        </h3>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-sm-6 form-group mandatory">
                                <label for="instagram_link" class="form-label">{{ __('page.INSTAGRAM_LINK') }}</label>
                                <input id="instagram_link" name="instagram_link" type="url" class="form-control"
                                    placeholder="{{ __('page.ENTER_INSTAGRAM_LINK') }}"
                                    value="{{ $settings['instagram_link'] ?? '' }}">
                            </div>
                            <div class="col-sm-6 form-group mandatory">
                                <label for="x_link" class="form-label">{{ __('page.X_LINK') }}</label>
                                <input id="x_link" name="x_link" type="url" class="form-control"
                                    placeholder="{{ __('page.ENTER_X_LINK') }}" value="{{ $settings['x_link'] ?? '' }}">
                            </div>
                            <div class="col-sm-6 form-group mandatory mt-3">
                                <label for="facebook_link" class="form-label">{{ __('page.FACEBOOK_LINK') }}</label>
                                <input id="facebook_link" name="facebook_link" type="url" class="form-control"
                                    placeholder="{{ __('page.ENTER_FACEBOOK_LINK') }}"
                                    value="{{ $settings['facebook_link'] ?? '' }}">
                            </div>
                            <div class="col-sm-6 form-group mandatory mt-3">
                                <label for="linkedin_link" class="form-label">{{ __('page.LINKEDIN_LINK') }}</label>
                                <input id="linkedin_link" name="linkedin_link" type="url" class="form-control"
                                    placeholder="{{ __('page.ENTER_LINKEDIN_LINK') }}"
                                    value="{{ $settings['linkedin_link'] ?? '' }}">
                            </div>
                            <div class="col-sm-6 form-group mandatory mt-3">
                                <label for="pinterest_link" class="form-label">{{ __('page.PINTEREST_LINK') }}</label>
                                <input id="pinterest_link" name="pinterest_link" type="url" class="form-control"
                                    placeholder="{{ __('page.ENTER_PINTEREST_LINK') }}"
                                    value="{{ $settings['pinterest_link'] ?? '' }}">
                            </div>
                        </div>
                        <div class="col-12 mt-3 d-flex justify-content-end">
                            <button class="btn btn-primary me-1 mb-1" type="submit"
                                name="submit">{{ __('page.SAVE') }}</button>
                        </div>
                    </div>
                </div>
            </div>
        </form>

        <form action="{{ route('settings.store') }}" method="post">
            @csrf
            <div class="row d-flex">
                <div class="card mt-3 admin_cards">
                    <div class="card-header">
                        <h3 class="card-title">{{ __('page.OTHER_SETTINGS') }}</h3>
                    </div>

                    <div class="card-body">
                        <div class="row">
                            <div class="col-sm-6 form-group mandatory">
                                <label for="play_store_link" class="form-label ">{{ __('page.PLAY_STORE_LINK') }}</label>
                                <input id="play_store_link" name="play_store_link" type="url" class="form-control"
                                    placeholder="{{ __('page.EMTER_PLAY_STORE_LINK') }}"
                                    value="{{ $settings['play_store_link'] ?? '' }}">
                            </div>
                            <div class="col-sm-6 form-group mandatory">
                                <label for="app_store_link" class="form-label ">{{ __('page.APP_STORE_LINK') }}</label>
                                <input id="app_store_link" name="app_store_link" type="url" class="form-control"
                                    placeholder="{{ __('page.EMTER_APP_STORE_LINK') }}"
                                    value="{{ $settings['app_store_link'] ?? '' }}">
                            </div>
                            <div class="col-sm-6 form-group mandatory mt-3">
                                <label for="android_shceme" class="form-label ">{{ __('page.ANDROID_SCHEME') }}</label>
                                <input id="android_shceme" name="android_shceme" type="text" class="form-control"
                                    placeholder="{{ __('page.EMTER_ANDROID_SCHEME') }}"
                                    value="{{ $settings['android_shceme'] ?? '' }}">
                            </div>
                            <div class="col-sm-6 form-group mandatory mt-3">
                                <label for="ios_shceme" class="form-label ">{{ __('page.IOS_SCHEME') }}</label>
                                <input id="ios_shceme" name="ios_shceme" type="text" class="form-control"
                                    placeholder="{{ __('page.EMTER_IOS_SCHEME') }}"
                                    value="{{ $settings['ios_shceme'] ?? '' }}">
                            </div>

                            <div class="col-sm-6 form-group mandatory mt-3">
                                <label for="keep_old_posts" class="form-label">
                                    {{ __('page.HOW_MANY_DAYS_OLD_POSTS_SHOULD_BE_KEPT') }}
                                </label>
                                <input id="keep_old_posts" name="keep_old_posts" type="number" class="form-control"
                                    placeholder="{{ __('page.ENTER_IN_DAYS') }}"
                                    value="{{ $settings['keep_old_posts'] ?? '' }}" min="-1" required>
                                <span class="fs-5 text-danger fw-bold">
                                    ({{ __('page.ENTER_MINUS_ONE_TO_NEVER_DELETE_OR_VALUE_FOR_AUTOMATIC_DELETE') }})
                                </span>
                            </div>

                            <div class="col-sm-6 form-group mandatory mt-3">
                                <label for="keep_old_video_posts " class="form-label">
                                    {{ __('page.HOW_MANY_DAYS_OLD_VIDEO_POSTS_SHOULD_BE_KEPT') }}
                                </label>
                                <input id="keep_old_video_posts" name="keep_old_video_posts" type="number"
                                    class="form-control" placeholder="{{ __('page.ENTER_IN_DAYS') }}"
                                    value="{{ $settings['keep_old_video_posts'] ?? '' }}" min="-1" required>
                                <span class="fs-5 text-danger fw-bold">
                                    ({{ __('page.ENTER_MINUS_ONE_TO_NEVER_DELETE_OR_VALUE_FOR_AUTOMATIC_DELETE') }})
                                </span>
                            </div>

                            <div class="col-sm-6 form-group mandatory mt-3">
                                <label for="keep_old_notification"
                                    class="form-label ">{{ __('page.HOW_MANY_DAYS_OLD_NOTIFICATIONS_SHOULD_BE_KEPT') }}</label>
                                <input id="keep_old_notification" name="keep_old_notification" type="number"
                                    class="form-control" placeholder="{{ __('page.ENTER_IN_DAYS') }}"
                                    value="{{ $settings['keep_old_notification'] ?? '' }}"
                                    oninput="this.value = Math.abs(this.value)" min="0" required>
                            </div>

                            <div class="form-group col-sm-6 col-md-6 mt-3">
                                <label for="app_name" class="form-label">{{ __('page.APP_NAME') }}</label>
                                <input id="app_name" name="app_name" type="text" class="form-control"
                                    placeholder="{{ __('page.ENTER_APP_NAME') }}"
                                    value="{{ $settings['app_name'] ?? '' }}" required>
                            </div>

                            <div class="col-sm-6 form-group mandatory mt-3">
                                <label for="" class="form-label">{{ __('page.MAINTENANCE_MODE') }}</label>
                                <div class="form-check form-switch">
                                    <input type="hidden" name="maintenance_mode" id="maintenance_mode"
                                        class="checkbox-toggle-switch-input"
                                        value="{{ $settings['maintenance_mode'] ?? 0 }}">
                                    <input class="form-check-input checkbox-toggle-switch" type="checkbox" role="switch"
                                        aria-checked="{{ $settings['maintenance_mode'] == 1 ? 'true' : 'false' }}"
                                        id="switch_maintenance_mode"
                                        {{ $settings['maintenance_mode'] == 1 ? 'checked' : '' }}>
                                </div>
                            </div>

                            <div class="col-sm-6 form-group mandatory mt-3">
                                <label for="switch_application_download_popup_on_web"
                                    class="form-label">{{ __('page.APPLICATION_DOWNLOAD_POPUP') }}</label>
                                <div class="form-check form-switch">
                                    <input type="hidden" name="application_download_popup_on_web"
                                        id="application_download_popup_on_web" class="checkbox-toggle-switch-input"
                                        value="{{ $settings['application_download_popup_on_web'] ?? 0 }}">
                                    <input class="form-check-input checkbox-toggle-switch" type="checkbox" role="switch"
                                        aria-checked="{{ isset($settings['application_download_popup_on_web']) && $settings['application_download_popup_on_web'] == 1 ? 'true' : 'false' }}"
                                        id="switch_application_download_popup_on_web"
                                        {{ isset($settings['application_download_popup_on_web']) && $settings['application_download_popup_on_web'] == 1 ? 'checked' : '' }}>
                                </div>
                            </div>

                            <div class="col-sm-12 form-group mandatory mt-3">
                                <label for="" class="form-label">{{ __('page.FREE_TRIAL_MODE') }}</label>
                                <div class="form-check form-switch">
                                    <input type="hidden" name="free_trial_status" id="free_trial_status"
                                        class="checkbox-toggle-switch-input"
                                        value="{{ $settings['free_trial_status'] ?? 0 }}">

                                    <input class="form-check-input checkbox-toggle-switch" type="checkbox" role="switch"
                                        aria-checked="{{ $settings['free_trial_status'] == 1 ? 'true' : 'false' }}"
                                        id="switch_free_trial_status"
                                        data-has-active-subscription="{{ $hasActiveSubscription ? '1' : '0' }}"
                                        {{ $settings['free_trial_status'] == 1 ? 'checked' : '' }}>
                                </div>
                                <div class="alert alert-danger form-text text-danger fw-bold">
                                    {{ __('page.FREE_TRIAL_MODE_NOTE') }}
                                </div>
                            </div>


                            <div class="col-sm-6 form-group mandatory mt-3">
                                <label for="free_trial_post_limit"
                                    class="form-label">{{ __('page.FREE_TRIAL_POST_LIMIT') }}</label>
                                <input id="free_trial_post_limit" name="free_trial_post_limit" type="number"
                                    class="form-control" placeholder="{{ __('page.ENTER_POST_LIMIT') }}"
                                    value="{{ $settings['free_trial_post_limit'] ?? '' }}"
                                    oninput="this.value = Math.abs(this.value)" min="-1" required>
                                <span class="fs-5">({{ __('page.NUMBER_OF_POSTS_FREE_TRIAL_USERS_CAN_VIEW') }})</span>
                            </div>
                            <div class="col-sm-6 form-group mandatory mt-3">
                                <label for="free_trial_story_limit"
                                    class="form-label">{{ __('page.FREE_TRIAL_STORY_LIMIT') }}</label>
                                <input id="free_trial_story_limit" name="free_trial_story_limit" type="number"
                                    class="form-control" placeholder="{{ __('page.ENTER_STORY_LIMIT') }}"
                                    value="{{ $settings['free_trial_story_limit'] ?? '' }}"
                                    oninput="this.value = Math.abs(this.value)" min="-1" required>
                                <span class="fs-5">({{ __('page.NUMBER_OF_STORIES_FREE_TRIAL_USERS_CAN_VIEW') }})</span>
                            </div>
                            <div class="col-sm-6 form-group mandatory mt-3">
                                <label for="free_trial_e_papers_and_magazines_limit"
                                    class="form-label">{{ __('page.FREE_TRIAL_E_PAPER_AND_MAGAZINES_LIMIT') }}</label>
                                <input id="free_trial_e_papers_and_magazines_limit"
                                    name="free_trial_e_papers_and_magazines_limit" type="number" class="form-control"
                                    placeholder="{{ __('page.ENTER_E_PAPER_AND_MAGAZINES_LIMIT') }}"
                                    value="{{ $settings['free_trial_e_papers_and_magazines_limit'] ?? '' }}"
                                    oninput="this.value = Math.abs(this.value)" min="-1" required>
                                <span
                                    class="fs-5">({{ __('page.NO_OF_E_PAPER_AND_MAGAZINES_FREE_TRIAL_USERS_CAN_VIEW') }})</span>
                            </div>
                        </div>
                        <div class="col-12 mt-3 d-flex justify-content-end">
                            <button class="btn btn-primary me-1 mb-1" type="submit"
                                name="submit">{{ __('page.SAVE') }}</button>
                        </div>
                    </div>
                </div>
            </div>
        </form>

    </section>
@endsection
