@extends('admin.layouts.main')

@section('title')
    {{ __('page.LANGUAGES') }}
@endsection

@section('pre-title')
    {{ __('page.LANGUAGES') }}
@endsection

@section('page-title')
    <div class="row g-2 align-items-center">
        <div class="col"> <!-- Page pre-title -->
            <div class="page-pretitle"> <a href="{{ url('admin/dashboard') }}">Home/</a> <a
                    href="{{ url('admin/settings') }}">{{ __('page.SETTINGS') }}/</a> <a
                    href="{{ url('admin/language') }}">{{ __('page.LANGUAGES') }}</a> </div>
            <h2 class="page-title"> @yield('title') </h2>
        </div>
        <div class="col-auto ms-auto d-print-none"> <button class="btn btn-primary add_btn" data-bs-target='#languageAddModal'
                data-bs-toggle='modal' title='Create'>{{ __('page.ADD_LANGUAGE') }}</button> </div>
        <div class="col-auto ms-auto d-print-none"> </div>
    </div>
@endsection

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-4 col-md-12 mb-3">
                <div class="card">
                    <section class="section">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="card admin_cards">
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-12">
                                                <div class="accordion" id="languageAccordion">
                                                    @if ($languages->isNotEmpty())
                                                        @foreach ($languages as $language)
                                                            <div
                                                                class="card mb-3 {{ $selected_language_id == $language->id ? ' border border-primary p-2' : '' }}">
                                                                <h2 id="languageHeading{{ $language->id }}">
                                                                    <button
                                                                        class="accordion-button collapsed select-language"
                                                                        type="button" data-bs-toggle="collapse"
                                                                        data-bs-target="#languageCollapse{{ $language->id }}"
                                                                        aria-expanded="{{ $selected_language_id == $language->id ? 'true' : 'false' }}"
                                                                        aria-controls="languageCollapse{{ $language->id }}"
                                                                        data-code="{{ $language->code }}"
                                                                        onclick="window.location.href='{{ route('language.index') }}?language_id={{ $language->id }}'">
                                                                        <div class="card">
                                                                            <p class="m-2 mt-2 language_image_css">
                                                                                <img src="{{ $language->image }}"
                                                                                    alt="{{ $language->name }}">
                                                                            </p>
                                                                        </div>
                                                                        <span class="me-2 m-2">
                                                                            Language: {{ $language->name }}
                                                                            ({{ $language->code }})
                                                                        </span>
                                                                    </button>
                                                                </h2>
                                                            </div>
                                                        @endforeach
                                                    @else
                                                        <div class="alert alert-warning" role="alert">
                                                            No languages available. Please add a language to continue.
                                                        </div>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </section>
                </div>
            </div>
            <div class="col-lg-8 col-md-12 mb-3">
                <div class="card p-3 rounded">
                    <!-- Tabs Navigation -->
                    <ul class="nav nav-pills justify-content-between border-0 mb-3" id="languageTabs" role="tablist">
                        <li class="nav-item flex-fill p-1" role="presentation">
                            <button
                                class="nav-link active border w-100 py-3 fw-semibold rounded-3 d-flex align-items-center justify-content-center fs-4"
                                id="admin-panel-tab" data-bs-toggle="tab" data-bs-target="#admin-panel" type="button"
                                role="tab" aria-controls="admin-panel" aria-selected="true">
                                <i class="bi bi-gear me-2"></i> Admin Panel Labels
                            </button>
                        </li>

                        <li class="nav-item flex-fill p-1" role="presentation">
                            <button
                                class="nav-link border w-100 py-3 fw-semibold rounded-3 d-flex align-items-center justify-content-center fs-4"
                                id="frontend-tab" data-bs-toggle="tab" data-bs-target="#frontend" type="button"
                                role="tab" aria-controls="frontend" aria-selected="false">
                                <i class="bi bi-window me-2"></i> Frontend Labels
                            </button>
                        </li>

                        <li class="nav-item flex-fill p-1" role="presentation">
                            <button
                                class="nav-link border w-100 py-3 fw-semibold rounded-3 d-flex align-items-center justify-content-center fs-4"
                                id="upload-file-tab" data-bs-toggle="tab" data-bs-target="#upload-file" type="button"
                                role="tab" aria-controls="upload-file" aria-selected="false">
                                <i class="bi bi-upload me-2"></i> Upload File
                            </button>
                        </li>
                    </ul>

                    <!-- Tabs Content -->
                    <div class="tab-content" id="languageTabsContent">
                        <!-- Admin Panel Tab -->
                        <div class="tab-pane fade show active" id="admin-panel" role="tabpanel"
                            aria-labelledby="admin-panel-tab">
                            <form action="{{ route('language.store') }}" method="POST" id="languageLabels"
                                enctype="multipart/form-data">
                                @csrf

                                <input type="hidden" name="language_id" id="selected_language_id"
                                    value="{{ $selected_language_id }}">
                                <input type="hidden" name="tab_type" value="admin_panel">

                                <div class="card p-3 mb-3 m-1">
                                    <h4 class="m-2">{{ __('Admin Panel Translations') }}</h4>
                                </div>

                                <div class="row border p-2 m-1 rounded">
                                      {!! create_label('translations[message][DASHBOARD]', 'Messages: Dashboard', $language_code ?? '') !!}
                                    {!! create_label('translations[message][USERS]', 'Messages: Users', $language_code ?? '') !!}
                                    {!! create_label('translations[message][TOPICS]', 'Messages: Topics', $language_code ?? '') !!}
                                    {!! create_label('translations[message][CHANNELS]', 'Messages: Channels', $language_code ?? '') !!}
                                    {!! create_label('translations[message][POSTS]', 'Messages: Posts', $language_code ?? '') !!}
                                    {!! create_label(
                                        'translations[message][POSTS_VS_VIDEOS_COUNT]',
                                        'Messages: Posts vs Videos count',
                                        $language_code ?? '',
                                    ) !!}
                                    {!! create_label(
                                        'translations[message][MOST_LIKED:_POST_VS_VIDEO]',
                                        'Messages: Most Liked : Posts vs Video',
                                        $language_code ?? '',
                                    ) !!}
                                    {!! create_label(
                                        'translations[message][MOST_FOLLOWED: CHANNELS]',
                                        'Messages: Most Followed : Channels',
                                        $language_code ?? '',
                                    ) !!}
                                    {!! create_label(
                                        'translations[message][SUBSCRIPTION_CHART]',
                                        'Messages: Subscription Chart',
                                        $language_code ?? '',
                                    ) !!}
                                    {!! create_label(
                                        'translations[message][TRANSACTION_CHART]',
                                        'Messages: Transaction Chart',
                                        $language_code ?? '',
                                    ) !!}
                                    {!! create_label('translations[message][MOST_LIKED_POST]', 'Messages: Most Liked Post', $language_code ?? '') !!}
                                    {!! create_label(
                                        'translations[message][MOST_VIEWED_STORY]',
                                        'Messages: Most Viewed Story',
                                        $language_code ?? '',
                                    ) !!}

                                    {{-- <><><><><><><><><><> END DASHBOARD LABELS <><><><><><><><><><> --}}

                                    {{-- <><><><><><><><><><> POSTS LABELS <><><><><><><><><><> --}}
                                    {!! create_label('translations[page][HOME]', 'Page: Home', $language_code ?? '') !!}
                                    {!! create_label('translations[page][CREATE_POSTS]', 'Page: Create Posts', $language_code ?? '') !!}
                                    {!! create_label('translations[page][EDIT_POST]', 'Page: Edit Posts', $language_code ?? '') !!}
                                    {!! create_label('translations[page][NEWS_POSTS]', 'Page: News Posts', $language_code ?? '') !!}
                                    {!! create_label('translations[page][LOADING]', 'Page: Loading', $language_code ?? '') !!}
                                    {!! create_label('translations[page][SELECT_FILTER]', 'Page: Select Filter', $language_code ?? '') !!}
                                    {!! create_label('translations[page][MOST_RECENT]', 'Page: Most Recent', $language_code ?? '') !!}
                                    {!! create_label('translations[page][MOST_READ]', 'Page: Most Read', $language_code ?? '') !!}
                                    {!! create_label('translations[page][MOST_LIKED]', 'Page: Most Liked', $language_code ?? '') !!}
                                    {!! create_label('translations[page][VIDEO_POSTS]', 'Page: Video Posts', $language_code ?? '') !!}
                                    {!! create_label('translations[page][SELECT_CHANNEL]', 'Page: Select Channel', $language_code ?? '') !!}
                                    {!! create_label('translations[page][ALL]', 'Page: All', $language_code ?? '') !!}
                                    {!! create_label('translations[page][SELECT_TOPIC]', 'Page: Select Topic', $language_code ?? '') !!}
                                    {!! create_label('translations[page][SEARCH]', 'Page: Search', $language_code ?? '') !!}
                                    {!! create_label('translations[page][POST_DESCRIPTION]', 'Page: Post Description', $language_code ?? '') !!}
                                    {!! create_label('translations[page][DESCRIPTION]', 'Page: Description', $language_code ?? '') !!}
                                    {!! create_label('translations[page][EDIT]', 'Page: Edit', $language_code ?? '') !!}
                                    {!! create_label('translations[page][DELETE]', 'Page: Delete', $language_code ?? '') !!}
                                    {!! create_label('translations[page][CLOSE]', 'Page: Close', $language_code ?? '') !!}

                                    {!! create_label('translations[page][DETAILS]', 'Page: Details', $language_code ?? '') !!}
                                    {!! create_label('translations[page][TITLE]', 'Page: Title', $language_code ?? '') !!}
                                    {!! create_label('translations[page][POST_DESCRIPTION]', 'Page: Post Description', $language_code ?? '') !!}
                                    {!! create_label('translations[page][SELECT_NEWSLANGUAGE]', 'Page: Select NewsLanguage', $language_code ?? '') !!}
                                    {!! create_label('translations[page][SELECT_NEWS_LANGUAGE]', 'Page: Select News Language', $language_code ?? '') !!}
                                    {!! create_label('translations[page][SELECT_CHANNEL]', 'Page: Select Channel', $language_code ?? '') !!}
                                    {!! create_label('translations[page][SELECT_TOPIC]', 'Page: Select Topic', $language_code ?? '') !!}
                                    {!! create_label('translations[page][TYPE]', 'Page: Type', $language_code ?? '') !!}
                                    {!! create_label('translations[page][POST]', 'Page: Post', $language_code ?? '') !!}
                                    {!! create_label('translations[page][VIDEO]', 'Page: Video', $language_code ?? '') !!}
                                    {!! create_label('translations[page][STATUS]', 'Page: Status', $language_code ?? '') !!}
                                    {!! create_label('translations[page][ACTIVE]', 'Page: Active', $language_code ?? '') !!}
                                    {!! create_label('translations[page][INACTIVE]', 'Page: Inactive', $language_code ?? '') !!}
                                    {!! create_label('translations[page][IMAGE]', 'Page: Image', $language_code ?? '') !!}
                                    {!! create_label('translations[page][THUMBNAIL]', 'Page: Thumbnail', $language_code ?? '') !!}
                                    {!! create_label('translations[page][BACK]', 'Page: Back', $language_code ?? '') !!}
                                    {!! create_label('translations[page][SAVE]', 'Page: Save', $language_code ?? '') !!}
                                    {!! create_label('translations[page][CHOOSE_POST_FORMAT]', 'Page: Choose Post Format', $language_code ?? '') !!}
                                    {{-- <><><><><><><><><><> END POSTS LABELS <><><><><><><><><><> --}}

                                    {{-- <><><><><><><><><><> NEWS_LANGUAGE LABELS <><><><><><><><><><> --}}
                                    {!! create_label('translations[page][ADD_NEWS_LANGUAGE]', 'Page: Add News Language', $language_code ?? '') !!}
                                    {!! create_label('translations[page][NEWS_LANGUAGE_NAME]', 'Page: News Language Name', $language_code ?? '') !!}
                                    {!! create_label('translations[page][NEWS_LANGUAGE_CODE]', 'Page: News Language Code', $language_code ?? '') !!}
                                    {!! create_label('translations[page][NEWS_LANGUAGE_IMAGE]', 'Page: News Language Image', $language_code ?? '') !!}
                                    {!! create_label('translations[page][UPDATE_NEWS_LANGUAGE]', 'Page: Update News Language', $language_code ?? '') !!}
                                    {!! create_label(
                                        'translations[page][UPDATE_NEWS_LANGUAGE_NAME]',
                                        'Page: Update News Language Name',
                                        $language_code ?? '',
                                    ) !!}
                                    {!! create_label(
                                        'translations[page][UPDATE_NEWS_LANGUAGE_CODE]',
                                        'Page: Update News Language Code',
                                        $language_code ?? '',
                                    ) !!}
                                    {!! create_label(
                                        'translations[page][UPDATE_NEWS_LANGUAGE_IMAGE]',
                                        'Page: Update News Language Image',
                                        $language_code ?? '',
                                    ) !!}
                                    {!! create_label('translations[page][SELECT_STATUS]', 'Page: Select Status', $language_code ?? '') !!}
                                    {!! create_label('translations[page][ACTIVE]', 'Page: Active', $language_code ?? '') !!}
                                    {!! create_label('translations[page][INACTIVE]', 'Page: Inactive', $language_code ?? '') !!}
                                    {!! create_label('translations[page][CLOSE]', 'Page: Close', $language_code ?? '') !!}
                                    {!! create_label('translations[page][SAVE]', 'Page: Save', $language_code ?? '') !!}
                                    {!! create_label('translations[page][HOME]', 'Page: Home', $language_code ?? '') !!}
                                    {!! create_label('translations[page][CREATE_NEWSLANGUAGE]', 'Page: Create News Language', $language_code ?? '') !!}
                                    {!! create_label(
                                        'translations[page][NEWS_LANGUAGE_ACCESS_BLOCKED]',
                                        'Page: Access to News Language section is blocked.',
                                        $language_code ?? '',
                                    ) !!}
                                    {!! create_label(
                                        'translations[page][NEWS_LANGUAGE_SETTING]',
                                        'Page: Go to News Language Settings',
                                        $language_code ?? '',
                                    ) !!}
                                    {!! create_label(
                                        'translations[page][DRAG_DROP_INSTRUCTION]',
                                        'Page: You can drag and drop the rows below to reorder the news languages.',
                                        $language_code ?? '',
                                    ) !!}
                                    {!! create_label(
                                        'translations[page][NO_NEWS_LANGUAGES_FOUND]',
                                        'Page: No news languages found.',
                                        $language_code ?? '',
                                    ) !!}
                                    {!! create_label('translations[global][ID]', 'Global: ID', $language_code ?? '') !!}
                                    {!! create_label('translations[global][IMAGE]', 'Global: Image', $language_code ?? '') !!}
                                    {!! create_label('translations[global][NAME]', 'Global: Name', $language_code ?? '') !!}
                                    {!! create_label('translations[global][CODE]', 'Global: Code', $language_code ?? '') !!}
                                    {!! create_label('translations[global][DEFAULT_LANGUAGE]', 'Global: Default Language', $language_code ?? '') !!}
                                    {!! create_label('translations[global][STATUS]', 'Global: Status', $language_code ?? '') !!}
                                    {!! create_label('translations[global][ACTION]', 'Global: Action', $language_code ?? '') !!}
                                    {!! create_label('translations[global][YES]', 'Global: Yes', $language_code ?? '') !!}
                                    {!! create_label('translations[global][NO]', 'Global: No', $language_code ?? '') !!}
                                    {{-- <><><><><><><><><><> END NEWS_LANGUAGE LABELS <><><><><><><><><><> --}}

                                    {{-- <><><><><><><><><><> STORY LABELS <><><><><><><><><><> --}}
                                    {!! create_label('translations[page][STORIES]', 'Page: Stories', $language_code ?? '') !!}
                                    {!! create_label('translations[page][CREATE_STORY]', 'Page: Create Story', $language_code ?? '') !!}
                                    {!! create_label('translations[page][EDIT_STORY]', 'Page: Edit Story', $language_code ?? '') !!}
                                    {!! create_label('translations[page][DELETE]', 'Page: Delete', $language_code ?? '') !!}
                                    {!! create_label('translations[page][TOPIC_:]', 'Page: Topic :', $language_code ?? '') !!}
                                    {!! create_label('translations[page][VIEW_STORY]', 'Page: View Story', $language_code ?? '') !!}
                                    {!! create_label('translations[page][HOME]', 'Home', $language_code ?? '') !!}
                                    {!! create_label('translations[page][STORIES]', 'Stories', $language_code ?? '') !!}
                                    {!! create_label('translations[page][CREATE_STORY]', 'Page: Create Story', $language_code ?? '') !!}
                                    {!! create_label('translations[page][SELECT_STORY_TOPIC]', 'Select Story Topic', $language_code ?? '') !!}
                                    {!! create_label('translations[page][ADD_SLIDES_CONTENT]', 'Add Slides Content', $language_code ?? '') !!}
                                    {!! create_label('translations[page][ARRANGE_SLIDE_ORDER]', 'Arrange Slide Order', $language_code ?? '') !!}
                                    {!! create_label('translations[page][APPLY_ANIMATIONS]', 'Apply Animations', $language_code ?? '') !!}
                                    {!! create_label('translations[page][REVIEW_SUBMIT]', 'Review & Submit', $language_code ?? '') !!}
                                    {!! create_label('translations[page][STORY_DETAILS]', 'Story Details', $language_code ?? '') !!}
                                    {!! create_label('translations[page][STORY TITLE]', 'Story Title', $language_code ?? '') !!}
                                    {!! create_label(
                                        'translations[page][PLEASE_ENTER_A_STORY_TITLE]',
                                        'Please enter a story title',
                                        $language_code ?? '',
                                    ) !!}
                                    {!! create_label('translations[page][SELECT_TOPIC]', 'Select Topic', $language_code ?? '') !!}
                                    {!! create_label('translations[page][PLEASE_SELECT_A_TOPIC]', 'Please select a topic', $language_code ?? '') !!}
                                    {!! create_label('translations[page][ADD_SLIDES]', 'Add Slides', $language_code ?? '') !!}
                                    {!! create_label('translations[page][ADD_NEW_SLIDE]', 'Add New Slide', $language_code ?? '') !!}
                                    {!! create_label(
                                        'translations[page][NO_SLIDES_AVAILABLE]',
                                        'No slides available. Please add at least one slide.',
                                        $language_code ?? '',
                                    ) !!}
                                    {!! create_label('translations[page][ORDER_SLIDES]', 'Order Slides', $language_code ?? '') !!}
                                    {!! create_label('translations[page][ADD_ANIMATIONS]', 'Add Animations', $language_code ?? '') !!}
                                    {!! create_label('translations[page][ANIMATION_TYPE]', 'Animation Type', $language_code ?? '') !!}
                                    {!! create_label('translations[page][DELAY_SECONDS]', 'Delay (seconds)', $language_code ?? '') !!}
                                    {!! create_label('translations[page][DURATION_SECONDS]', 'Duration (seconds)', $language_code ?? '') !!}
                                    {!! create_label('translations[page][NO_DELAY]', 'No Delay', $language_code ?? '') !!}
                                    {!! create_label('translations[page][ONE_SECOND]', '1 Second', $language_code ?? '') !!}
                                    {!! create_label('translations[page][TWO_SECONDS]', '2 Seconds', $language_code ?? '') !!}
                                    {!! create_label('translations[page][THREE_SECONDS]', '3 Seconds', $language_code ?? '') !!}
                                    {!! create_label('translations[page][PREVIEW]', 'Preview', $language_code ?? '') !!}
                                    {!! create_label(
                                        'translations[page][ANIMATION_PREVIEW_PLACEHOLDER]',
                                        'Animation preview will be shown here.',
                                        $language_code ?? '',
                                    ) !!}
                                    {!! create_label('translations[page][SAVE_STORY]', 'Save Story', $language_code ?? '') !!}
                                    {!! create_label(
                                        'translations[page][REVIEW_BEFORE_SAVING]',
                                        'Please review your story before saving.',
                                        $language_code ?? '',
                                    ) !!}
                                    {!! create_label(
                                        'translations[page][ONCE_SAVED_EDITABLE]',
                                        'Once saved, you can still edit your story.',
                                        $language_code ?? '',
                                    ) !!}
                                    {!! create_label('translations[page][PREVIOUS]', 'Previous', $language_code ?? '') !!}
                                    {!! create_label('translations[page][NEXT]', 'Next', $language_code ?? '') !!}
                                    {!! create_label('translations[page][UPDATE_STORY]', 'Page: Update Story', $language_code ?? '') !!}
                                    {!! create_label('translations[page][EDIT_SLIDES]', 'Page: Edit Slides', $language_code ?? '') !!}
                                    {!! create_label('translations[page][ADD_ANOTHER_SLIDE]', 'Page: Add Another Slide', $language_code ?? '') !!}
                                    {!! create_label('translations[page][SLIDE_TITLE]', 'Page: Slide Title', $language_code ?? '') !!}
                                    {!! create_label('translations[page][SLIDE_DESCRIPTION]', 'Page: Slide Description', $language_code ?? '') !!}
                                    {!! create_label('translations[page][SLIDE_IMAGE]', 'Page: Slide Image', $language_code ?? '') !!}

                                    {!! create_label('translations[page][IMAGE_UPLOAD_TYPE]', 'Page: Select Slider Image Upload Type', $language_code ?? '') !!}
                                    {!! create_label('translations[page][RANDOM_SIZE]', 'Page: Random Size', $language_code ?? '') !!}
                                    {!! create_label('translations[page][FIXED_SIZE]', 'Page: Fixed Size', $language_code ?? '') !!}
                                    {{-- <><><><><><><><><><> END STORY LABELS <><><><><><><><><><> --}}

                                    {{-- <><><><><><><><><><> CHANNELS LABELS <><><><><><><><><><> --}}
                                    {!! create_label('translations[page][CREATE_CHANNEL]', 'Pages: Create Channel', $language_code ?? '') !!}
                                    {!! create_label('translations[page][SELECT_STATUS]', 'Pages: Select Status', $language_code ?? '') !!}
                                    {!! create_label('translations[page][ALL]', 'Pages: All', $language_code ?? '') !!}
                                    {!! create_label('translations[page][ACTIVE]', 'Pages: Active', $language_code ?? '') !!}
                                    {!! create_label('translations[page][INACTIVE]', 'Pages: Inactive', $language_code ?? '') !!}
                                    {!! create_label('translations[message][ADD_CHANNEL]', 'Add Channel', $language_code ?? '') !!}
                                    {!! create_label('translations[message][EDIT_CHANNEL]', 'Edit Channel', $language_code ?? '') !!}
                                    {!! create_label('translations[message][CHANNEL_NAME]', 'Channel Name', $language_code ?? '') !!}
                                    {!! create_label('translations[message][CHANNEL_DESCRIPTION]', 'Channel Description', $language_code ?? '') !!}
                                    {!! create_label('translations[message][STATUS]', 'Status', $language_code ?? '') !!}
                                    {!! create_label('translations[message][SELECT_STATUS]', 'Select Status', $language_code ?? '') !!}
                                    {!! create_label('translations[message][ACTIVE]', 'Active', $language_code ?? '') !!}
                                    {!! create_label('translations[message][INACTIVE]', 'Inactive', $language_code ?? '') !!}
                                    {!! create_label('translations[message][LOGO]', 'Logo', $language_code ?? '') !!}
                                    {!! create_label('translations[message][CLOSE]', 'Close', $language_code ?? '') !!}
                                    {!! create_label('translations[message][SAVE]', 'Save', $language_code ?? '') !!}
                                    {!! create_label('translations[global][ID]', 'Global: ID', $language_code ?? '') !!}
                                    {!! create_label('translations[global][LOGO]', 'Global: Logo', $language_code ?? '') !!}
                                    {!! create_label('translations[global][NAME]', 'Global: Name', $language_code ?? '') !!}
                                    {!! create_label('translations[global][SLUG]', 'Global: Slug', $language_code ?? '') !!}
                                    {!! create_label('translations[global][DESCRIPTION]', 'Global: Description', $language_code ?? '') !!}
                                    {!! create_label('translations[global][STATUS]', 'Global: Status', $language_code ?? '') !!}
                                    {!! create_label('translations[global][FOLLOWERS]', 'Global: Followers', $language_code ?? '') !!}
                                    {!! create_label('translations[global][ACTION]', 'Global: Action', $language_code ?? '') !!}
                                    {!! create_label('translations[global][CHANNELS]', 'Global: Channels', $language_code ?? '') !!}
                                    {!! create_label('translations[global][TOPICS]', 'Global: Topics', $language_code ?? '') !!}
                                    {!! create_label('translations[global][FEED_URL]', 'Global: Feed URL', $language_code ?? '') !!}
                                    {!! create_label('translations[global][DATA_FORMAT]', 'Global: Data Format', $language_code ?? '') !!}
                                    {!! create_label('translations[global][SYNC_INTERVAL]', 'Global: Sync Interval', $language_code ?? '') !!}
                                    {!! create_label('translations[global][SYNC]', 'Global: Sync', $language_code ?? '') !!}

                                    {{-- <><><><><><><><><><> END CHANNELS LABELS <><><><><><><><><><> --}}

                                    {{-- <><><><><><><><><><>  TOPICS LABELS <><><><><><><><><><> --}}

                                    {!! create_label('translations[page][CREATE_TOPIC]', 'Create Topic', $language_code ?? '') !!}
                                    {!! create_label('translations[page][SELECT_STATUS]', 'Select Status', $language_code ?? '') !!}
                                    {!! create_label('translations[page][ALL]', 'All Topics', $language_code ?? '') !!}
                                    {!! create_label('translations[page][ACTIVE]', 'Active Topics', $language_code ?? '') !!}
                                    {!! create_label('translations[page][INACTIVE]', 'Inactive Topics', $language_code ?? '') !!}

                                    {!! create_label('translations[global][ID]', 'ID', $language_code ?? '') !!}
                                    {!! create_label('translations[global][IMAGE]', 'Image', $language_code ?? '') !!}
                                    {!! create_label('translations[global][NAME]', 'Name', $language_code ?? '') !!}
                                    {!! create_label('translations[global][SLUG]', 'Slug', $language_code ?? '') !!}
                                    {!! create_label('translations[global][STATUS]', 'Status', $language_code ?? '') !!}
                                    {!! create_label('translations[global][ACTION]', 'Action', $language_code ?? '') !!}

                                    {!! create_label('translations[message][ADD_TOPIC]', 'Add Topic', $language_code ?? '') !!}
                                    {!! create_label('translations[message][EDIT_TOPIC]', 'Edit Topic', $language_code ?? '') !!}
                                    {!! create_label('translations[message][TOPIC_NAME]', 'Topic Name', $language_code ?? '') !!}
                                    {!! create_label(
                                        'translations[message][PLEASE_ENTER_TOPIC_NAME]',
                                        'Please enter topic name',
                                        $language_code ?? '',
                                    ) !!}
                                    {!! create_label(
                                        'translations[message][PLEASE_ENTER_CHANNEL_NAME]',
                                        'Please enter channel name',
                                        $language_code ?? '',
                                    ) !!}
                                    {!! create_label('translations[message][STATUS]', 'Status', $language_code ?? '') !!}
                                    {!! create_label('translations[message][SELECT_STATUS]', 'Select Status', $language_code ?? '') !!}
                                    {!! create_label('translations[message][ACTIVE]', 'Active', $language_code ?? '') !!}
                                    {!! create_label('translations[message][INACTIVE]', 'Inactive', $language_code ?? '') !!}
                                    {!! create_label('translations[message][LOGO]', 'Logo', $language_code ?? '') !!}
                                    {!! create_label('translations[message][CLOSE]', 'Close', $language_code ?? '') !!}
                                    {!! create_label('translations[message][SAVE]', 'Save', $language_code ?? '') !!}

                                    {{-- <><><><><><><><><><> END TOPICS LABELS <><><><><><><><><><> --}}


                                    {{-- <><><><><><><><><><> RSSFEED LABELS <><><><><><><><><><> --}}
                                    {!! create_label('translations[page][HOME]', 'Home', $language_code ?? '') !!}
                                    {!! create_label('translations[page][SYNC_FEEDS]', 'Sync Feeds', $language_code ?? '') !!}
                                    {!! create_label('translations[page][CREATE]', 'Create RSS Feed', $language_code ?? '') !!}
                                    {!! create_label('translations[page][SELECT_STATUS]', 'Select Status', $language_code ?? '') !!}
                                    {!! create_label('translations[page][ALL]', 'All Feeds', $language_code ?? '') !!}
                                    {!! create_label('translations[page][ACTIVE]', 'Active', $language_code ?? '') !!}
                                    {!! create_label('translations[page][INACTIVE]', 'Inactive', $language_code ?? '') !!}
                                    {!! create_label('translations[page][CREATE_RSS_FEED]', 'Create Rss Feed', $language_code ?? '') !!}

                                    <!-- Global Labels -->
                                    {!! create_label('translations[global][ID]', 'ID', $language_code ?? '') !!}
                                    {!! create_label('translations[global][STATUS]', 'Status', $language_code ?? '') !!}
                                    {!! create_label('translations[global][SYNC]', 'Sync', $language_code ?? '') !!}
                                    {!! create_label('translations[global][ACTION]', 'Action', $language_code ?? '') !!}
                                    {!! create_label('translations[message][ADD_RSS_FEED]', 'Add RSS Feed', $language_code ?? '') !!}
                                    {!! create_label('translations[message][EDIT_RSS_FEED]', 'Edit RSS Feed', $language_code ?? '') !!}
                                    {!! create_label('translations[message][SELECT_NEWS_LANGUAGE]', 'Select News Language', $language_code ?? '') !!}
                                    {!! create_label('translations[message][FEED_URL]', 'Feed URL', $language_code ?? '') !!}
                                    {!! create_label(
                                        'translations[message][PLEASE_ENTER_RSS_FEED_URL]',
                                        'Please enter RSS feed URL',
                                        $language_code ?? '',
                                    ) !!}
                                    {!! create_label('translations[message][SELECT_CHANNEL]', 'Select Channel', $language_code ?? '') !!}
                                    {!! create_label('translations[message][SELECT_TOPIC]', 'Select Topic', $language_code ?? '') !!}
                                    {!! create_label('translations[message][SYNC_INTERVAL]', 'Sync Interval', $language_code ?? '') !!}
                                    {!! create_label(
                                        'translations[message][PLEASE_ENTER_IN_MINUTES]',
                                        'Please enter time in minutes',
                                        $language_code ?? '',
                                    ) !!}
                                    {!! create_label('translations[message][DATA_FORMAT]', 'Data Format', $language_code ?? '') !!}
                                    {!! create_label('translations[message][SELECT_FORMAT]', 'Select Format', $language_code ?? '') !!}
                                    {!! create_label('translations[message][STATUS]', 'Status', $language_code ?? '') !!}
                                    {!! create_label('translations[message][ACTIVE]', 'Active', $language_code ?? '') !!}
                                    {!! create_label('translations[message][INACTIVE]', 'Inactive', $language_code ?? '') !!}
                                    {{-- <><><><><><><><><><> END  RSSFEED LABELS <><><><><><><><><><> --}}


                                    {{-- <><><><><><><><><><> USERS LABELS <><><><><><><><><><> --}}
                                    {!! create_label('translations[page][USERS]', 'Users', $language_code ?? '') !!}
                                    {!! create_label('translations[page][CREATE]', 'Create User', $language_code ?? '') !!}
                                    {!! create_label('translations[page][LOADING]', 'Loading...', $language_code ?? '') !!}
                                    {!! create_label('translations[page][All users]', 'All Users', $language_code ?? '') !!}
                                    {!! create_label('translations[page][ACTIVE]', 'Active', $language_code ?? '') !!}
                                    {!! create_label('translations[page][INACTIVE]', 'Inactive', $language_code ?? '') !!}
                                    {!! create_label('translations[page][DELETED]', 'Deleted', $language_code ?? '') !!}
                                    {!! create_label('translations[page][SEARCH]', 'Search...', $language_code ?? '') !!}
                                    {!! create_label('translations[message][USER_NAME]', 'User Name', $language_code ?? '') !!}
                                    {!! create_label('translations[message][EMAIL]', 'Email', $language_code ?? '') !!}
                                    {!! create_label('translations[message][PASSWORD]', 'Password', $language_code ?? '') !!}
                                    {!! create_label('translations[message][CONFIRM_PASSWORD]', 'Confirm Password', $language_code ?? '') !!}
                                    {!! create_label('translations[message][PHONE]', 'Phone', $language_code ?? '') !!}
                                    {!! create_label('translations[message][STATUS]', 'Status', $language_code ?? '') !!}
                                    {!! create_label('translations[message][SELECT_STATUS]', 'Select Status', $language_code ?? '') !!}
                                    {!! create_label('translations[message][PROFILE]', 'Profile', $language_code ?? '') !!}
                                    {!! create_label('translations[message][SAVE]', 'Save', $language_code ?? '') !!}
                                    {!! create_label('translations[message][EDIT_USER]', 'Edit User', $language_code ?? '') !!}
                                    {{-- <><><><><><><><><><> END  USERS LABELS <><><><><><><><><><> --}}

                                    {{-- <><><><><><><><><><> MEMBERSHIP LABELS <><><><><><><><><><> --}}
                                    {!! create_label('translations[message][ADD_PRICING_PLAN]', 'Add Pricing Plan', $language_code ?? '') !!}
                                    {!! create_label('translations[message][BASIC_PLAN_INFO]', 'Basic Plan Information', $language_code ?? '') !!}
                                    {!! create_label('translations[message][PLAN_NAME]', 'Plan Name', $language_code ?? '') !!}
                                    {!! create_label('translations[message][PLAN_SLUG]', 'Plan Slug', $language_code ?? '') !!}
                                    {!! create_label('translations[message][PLAN_DESC]', 'Plan Description', $language_code ?? '') !!}
                                    {!! create_label('translations[message][PLAN_STATUS]', 'Status', $language_code ?? '') !!}
                                    {!! create_label('translations[message][PLAN_ACTIVE]', 'Active', $language_code ?? '') !!}
                                    {!! create_label('translations[message][PLAN_FEATURES]', 'Plan Features', $language_code ?? '') !!}
                                    {!! create_label('translations[message][NO_OF_ARTICLES]', 'Number of Articles', $language_code ?? '') !!}
                                    {!! create_label('translations[message][NO_OF_STORIES]', 'Number of Stories', $language_code ?? '') !!}
                                    {!! create_label('translations[message][ADS_FREE]', 'Ads Free Experience', $language_code ?? '') !!}
                                    {!! create_label('translations[message][TENURE_INFO]', 'Tenure Information', $language_code ?? '') !!}
                                    {!! create_label('translations[message][TENURE_NAME]', 'Tenure Name', $language_code ?? '') !!}
                                    {!! create_label('translations[message][TENURE_DURATION]', 'Duration (months)', $language_code ?? '') !!}
                                    {!! create_label('translations[message][TENURE_PRICE]', 'Price', $language_code ?? '') !!}
                                    {!! create_label('translations[message][ADD_ANOTHER_TENURE]', 'Add Another Tenure Option', $language_code ?? '') !!}
                                    {!! create_label('translations[message][SAVE_PLAN]', 'Save Plan', $language_code ?? '') !!}
                                    {!! create_label('translations[message][MEMBERSHIP_PLANS]', 'Membership Plans', $language_code ?? '') !!}

                                    {{-- <><><><><><><><><><> END MEMBERSHIP LABELS <><><><><><><><><><> --}}

                                    {{-- <><><><><><><><><><> SUBCRIPTION LABELS <><><><><><><><><><> --}}

                                    {!! create_label('translations[global][ID]', 'ID', $language_code ?? '') !!}
                                    {!! create_label('translations[global][USER]', 'User', $language_code ?? '') !!}
                                    {!! create_label('translations[global][PLAN]', 'Plan', $language_code ?? '') !!}
                                    {!! create_label('translations[global][FEATURE ID]', 'Feature ID', $language_code ?? '') !!}
                                    {!! create_label('translations[global][DURATION]', 'Duration', $language_code ?? '') !!}
                                    {!! create_label('translations[global][START DATE]', 'Start Date', $language_code ?? '') !!}
                                    {!! create_label('translations[global][END DATE]', 'End Date', $language_code ?? '') !!}
                                    {!! create_label('translations[global][STATUS]', 'Status', $language_code ?? '') !!}
                                    {!! create_label('translations[page][SUBSCRIPTIONS]', 'Subscriptions', $language_code ?? '') !!}
                                    {!! create_label('translations[page][HOME]', 'Home', $language_code ?? '') !!}
                                    {!! create_label('translations[message][NO_SUBSCRIPTION]', 'No Subscription Found', $language_code ?? '') !!}

                                    {{-- <><><><><><><><><><> END SUBCRIPTION LABELS <><><><><><><><><><> --}}

                                    {{-- <><><><><><><><><><> TRANSACTIONS LABELS <><><><><><><><><><> --}}

                                    {!! create_label('translations[global][ID]', 'ID', $language_code ?? '') !!}
                                    {!! create_label('translations[global][USER]', 'User', $language_code ?? '') !!}
                                    {!! create_label('translations[global][TRANSACTION_ID]', 'Transaction ID', $language_code ?? '') !!}
                                    {!! create_label('translations[global][PAYMENT_GATEWAY]', 'Payment Gateway', $language_code ?? '') !!}
                                    {!! create_label('translations[global][DATE]', 'Date', $language_code ?? '') !!}
                                    {!! create_label('translations[global][ACTIONS]', 'Actions', $language_code ?? '') !!}
                                    {!! create_label('translations[global][NO_USER]', 'No User', $language_code ?? '') !!}
                                    {!! create_label('translations[global][NO_TRANSACTION_FOUND]', 'No transactions found', $language_code ?? '') !!}
                                    {!! create_label('translations[page][HOME]', 'Home', $language_code ?? '') !!}
                                    {!! create_label('translations[page][ALL_TRANSACTIONS]', 'All Transactions', $language_code ?? '') !!}

                                    {{-- <><><><><><><><><><> END TRANSACTIONS LABELS <><><><><><><><><><> --}}

                                    {{-- <><><><><><><><><><> SUBSCRIBERSLABELS <><><><><><><><><><> --}}

                                    {!! create_label('translations[page][SUBSCRIBERS]', 'Subscribers', $language_code ?? '') !!}
                                    {{-- <><><><><><><><><><> END SUBSCRIBERS LABELS <><><><><><><><><><> --}}

                                    {{-- <><><><><><><><><><> SEND_NOTIFICATION LABELS <><><><><><><><><><> --}}

                                    {!! create_label('translations[page][SEND_NOTIFICATION]', 'Send Notification', $language_code ?? '') !!}
                                    {!! create_label('translations[page][HOME]', 'Home', $language_code ?? '') !!}

                                    {!! create_label('translations[global][ID]', 'ID', $language_code ?? '') !!}
                                    {!! create_label('translations[global][TITLE]', 'Title', $language_code ?? '') !!}
                                    {!! create_label('translations[global][MESSAGE]', 'Message', $language_code ?? '') !!}
                                    {!! create_label('translations[global][IMAGE]', 'Image', $language_code ?? '') !!}
                                    {!! create_label('translations[global][SUBMIT]', 'Submit', $language_code ?? '') !!}
                                    {!! create_label('translations[global][SELECT_USER]', 'Select User', $language_code ?? '') !!}
                                    {!! create_label('translations[global][ALL]', 'All', $language_code ?? '') !!}
                                    {!! create_label('translations[global][SELECTED_ONLY]', 'Selected Only', $language_code ?? '') !!}
                                    {!! create_label('translations[global][NAME]', 'Name', $language_code ?? '') !!}
                                    {!! create_label('translations[global][NUMBER]', 'Number', $language_code ?? '') !!}
                                    {!! create_label('translations[global][SLUG]', 'Slug', $language_code ?? '') !!}
                                    {!! create_label('translations[global][SEND TO]', 'Send To', $language_code ?? '') !!}
                                    {!! create_label('translations[global][ACTION]', 'Action', $language_code ?? '') !!}
                                    {{-- <><><><><><><><><><> END SEND_NOTIFICATION LABELS <><><><><><><><><><> --}}

                                    {{-- <><><><><><><><><><> REPORTED COMMENTS LABELS <><><><><><><><><><> --}}
                                    {!! create_label('translations[page][HOME]', 'Home', $language_code ?? '') !!}
                                    {!! create_label('translations[page][COMMENTS]', 'Comments', $language_code ?? '') !!}
                                    {!! create_label('translations[global][ID]', 'ID', $language_code ?? '') !!}
                                    {!! create_label('translations[global][USERNAME]', 'Username', $language_code ?? '') !!}
                                    {!! create_label('translations[global][REASON_TYPE]', 'Reason Type', $language_code ?? '') !!}
                                    {!! create_label('translations[global][REPORT]', 'Report', $language_code ?? '') !!}
                                    {!! create_label('translations[global][COMMENT]', 'Comment', $language_code ?? '') !!}
                                    {!! create_label('translations[global][DATE]', 'Date', $language_code ?? '') !!}
                                    {!! create_label('translations[global][ACTION]', 'Action', $language_code ?? '') !!}

                                    {!! create_label('translations[global][BLOCKER_USER]', 'Blocker User', $language_code ?? '') !!}
                                    {!! create_label('translations[global][COMMENT_OWNER]', 'Comment Owner', $language_code ?? '') !!}
                                    {!! create_label('translations[global][REASON]', 'Block Reason', $language_code ?? '') !!}
                                    {{-- <><><><><><><><><><> END REPORTED COMMENTS LABELS <><><><><><><><><><> --}}

                                    {{-- <><><><><><><><><><> CONTACT US LABELS <><><><><><><><><><> --}}
                                    {!! create_label('translations[page][HOME]', 'Home', $language_code ?? '') !!}
                                    {!! create_label('translations[global][ID]', 'ID', $language_code ?? '') !!}
                                    {!! create_label('translations[global][NAME]', 'Name', $language_code ?? '') !!}
                                    {!! create_label('translations[global][CONTACT_NUMBER]', 'Contact Number', $language_code ?? '') !!}
                                    {!! create_label('translations[global][EMAIL]', 'Email', $language_code ?? '') !!}
                                    {!! create_label('translations[global][MESSAGE]', 'Message', $language_code ?? '') !!}
                                    {!! create_label('translations[global][ACTION]', 'Action', $language_code ?? '') !!}
                                    {!! create_label('translations[page][CONTACT_US_DETAILS]', 'Contact Us Details', $language_code ?? '') !!}
                                    {!! create_label('translations[page][NAME]', 'Name', $language_code ?? '') !!}
                                    {!! create_label('translations[page][EMAIL]', 'Email', $language_code ?? '') !!}
                                    {!! create_label('translations[page][MOBILE]', 'Mobile', $language_code ?? '') !!}
                                    {!! create_label('translations[page][MESSAGE]', 'Message', $language_code ?? '') !!}

                                    {{-- <><><><><><><><><><> CONTACT US LABELS <><><><><><><><><><> --}}

                                    {{-- <><><><><><><><><><> ROLE MANAGEMENTS LABELS <><><><><><><><><><> --}}
                                    {!! create_label('translations[page][ROLE_MANAGEMENTS]', 'Role Managements', $language_code ?? '') !!}
                                    {!! create_label('translations[page][ADMIN_USER]', 'Admin Users', $language_code ?? '') !!}
                                    {!! create_label('translations[page][CREATE_ROLE]', 'Create Role', $language_code ?? '') !!}
                                    {!! create_label('translations[page][EDIT_ROLE]', 'Edit Role', $language_code ?? '') !!}
                                    {!! create_label('translations[page][HOME]', 'Home', $language_code ?? '') !!}
                                    {!! create_label('translations[global][ID]', 'ID', $language_code ?? '') !!}
                                    {!! create_label('translations[global][NAME]', 'Name', $language_code ?? '') !!}
                                    {!! create_label('translations[global][PERMISSIONS]', 'Permissions', $language_code ?? '') !!}
                                    {!! create_label('translations[global][SUBMIT]', 'Submit', $language_code ?? '') !!}
                                    {!! create_label('translations[global][ACTION]', 'Action', $language_code ?? '') !!}
                                    {!! create_label('translations[global][EDIT]', 'Edit', $language_code ?? '') !!}
                                    {!! create_label('translations[global][DELETE]', 'Delete', $language_code ?? '') !!}
                                    {{-- <><><><><><><><><><> END ROLE MANAGEMENTS LABELS <><><><><><><><><><> --}}

                                    {{-- <><><><><><><><><><> ADMIN MANAGEMENTS LABELS <><><><><><><><><><> --}}
                                    {!! create_label('translations[page][CREATE_ADMIN]', 'Page: Create Admin', $language_code ?? '') !!}
                                    {!! create_label('translations[page][EDIT_STAFF]', 'Page: Edit Staff', $language_code ?? '') !!}
                                    {!! create_label('translations[page][ROLE]', 'Page: Role', $language_code ?? '') !!}
                                    {!! create_label('translations[page][SELECT_ROLE]', 'Page: Select Role', $language_code ?? '') !!}
                                    {!! create_label('translations[page][PASSWORD_RESET]', 'Page: Password Reset', $language_code ?? '') !!}
                                    {!! create_label('translations[page][NEWS_PASSWORD]', 'Page: New Password', $language_code ?? '') !!}
                                    {!! create_label('translations[page][CONFIRM_PASSWORD]', 'Page: Confirm Password', $language_code ?? '') !!}
                                    {!! create_label('translations[page][CREATE_ADMIN]', 'Create Admin', $language_code ?? '') !!}
                                    {!! create_label('translations[page][ADMIN_MANAGEMENT]', 'Admin Management', $language_code ?? '') !!}
                                    {!! create_label('translations[page][HOME]', 'Home', $language_code ?? '') !!}
                                    {!! create_label('translations[page][ADMIN]', 'Admin', $language_code ?? '') !!}
                                    {!! create_label('translations[page][ROLE]', 'Role', $language_code ?? '') !!}
                                    {!! create_label('translations[page][SELECT_ROLE]', 'Select Role', $language_code ?? '') !!}
                                    {!! create_label('translations[page][NAME]', 'Name', $language_code ?? '') !!}
                                    {!! create_label('translations[page][ENTER_NAME]', 'Enter Name', $language_code ?? '') !!}
                                    {!! create_label('translations[page][EMAIL]', 'Email', $language_code ?? '') !!}
                                    {!! create_label('translations[page][ENTER_EMAIL]', 'Enter Email', $language_code ?? '') !!}
                                    {!! create_label('translations[page][PASSWORD]', 'Password', $language_code ?? '') !!}
                                    {!! create_label('translations[page][ENTER_PASSWORD]', 'Enter Password', $language_code ?? '') !!}
                                    {!! create_label('translations[page][SAVE]', 'Save', $language_code ?? '') !!}
                                    {!! create_label('translations[page][ADMIN_MANAGEMENT]', 'Admin Management', $language_code ?? '') !!}

                                    {{-- <><><><><><><><><><> END ADMIN MANAGEMENTS LABELS <><><><><><><><><><> --}}

                                    {{-- <><><><><><><><><><>  SETTINGS LABELS <><><><><><><><><><> --}}
                                    {!! create_label('translations[page][SYSTEM_SETTINGS]', 'Page: System Settings', $language_code ?? '') !!}
                                    {!! create_label('translations[page][PAYMENT_GATEWAY]', 'Page: Payment Gateway', $language_code ?? '') !!}
                                    {!! create_label('translations[page][ABOUT_US]', 'Page: About Us', $language_code ?? '') !!}
                                    {!! create_label('translations[page][TERMS_CONDITIONS]', 'Page: Terms & Conditions', $language_code ?? '') !!}
                                    {!! create_label(
                                        'translations[page][NEWS_LANGUAGE_SETTINGS]',
                                        'Page: News Language Settings',
                                        $language_code ?? '',
                                    ) !!}
                                    {!! create_label('translations[page][BASIC_COMPANY_SETUP]', 'Page: Basic Company Setup', $language_code ?? '') !!}
                                    {!! create_label(
                                        'translations[page][LOGO_MANAGEMENT_AND_WEATHER_API_KEY_SETTING]',
                                        'Page: Logo Management & Weather API Key Setting',
                                        $language_code ?? '',
                                    ) !!}
                                    {!! create_label(
                                        'translations[page][SOCIAL_LINKS_AND_OTHER_SETTINGS]',
                                        'Page: Social Links & Other Settings',
                                        $language_code ?? '',
                                    ) !!}
                                    {!! create_label('translations[page][PRIVACY_POLICY]', 'Page: Privacy Policy', $language_code ?? '') !!}
                                    {!! create_label('translations[page][LANGUAGES]', 'Page: Languages', $language_code ?? '') !!}
                                    {!! create_label('translations[page][LOG_VIEWER]', 'Page: Log Viewer', $language_code ?? '') !!}
                                    {!! create_label('translations[page][WEB_THEME]', 'Page: Web Theme', $language_code ?? '') !!}
                                    {!! create_label('translations[page][SYSTEM_UPDATE]', 'Page: System Update', $language_code ?? '') !!}
                                    {!! create_label('translations[page][FIREBASE]', 'Page: Firebase', $language_code ?? '') !!}
                                    {!! create_label('translations[page][CRONJOB_INFO]', 'Page: Cronjob Info', $language_code ?? '') !!}
                                    {!! create_label('translations[page][SETTINGS]', 'Page: Settings', $language_code ?? '') !!}
                                    {!! create_label('translations[page][POPULAR_POST_TIME_RANGE_SETTING]', 'Page: Popular Post Time Range Settings', $language_code ?? '') !!}
                                    {!! create_label('translations[page][POPULAR_POST_TIME_RANGE ]', 'Page: Popular Post Time Range', $language_code ?? '') !!}
                                    {{-- <><><><><><><><><><> END SETTING LABELS <><><><><><><><><><> --}}

                                    {{-- <><><><><><><><><><> COMPANY_DETAILS  LABELS <><><><><><><><><><> --}}
                                    {!! create_label('translations[page][COMPANY_DETAILS]', 'Page: Company Details', $language_code ?? '') !!}
                                    {!! create_label('translations[page][COMPANY_NAME]', 'Page: Company Name', $language_code ?? '') !!}
                                    {!! create_label('translations[page][EMAIL]', 'Page: Email', $language_code ?? '') !!}
                                    {!! create_label('translations[page][ENTER_EMAIL]', 'Page: Enter Email', $language_code ?? '') !!}
                                    {!! create_label('translations[page][CONTACT_NUMBER]', 'Page: Contact Number', $language_code ?? '') !!}
                                    {!! create_label('translations[page][ADDRESS]', 'Page: Address', $language_code ?? '') !!}
                                    {!! create_label('translations[page][ENTER_ADDRESS]', 'Page: Enter Address', $language_code ?? '') !!}
                                    {!! create_label('translations[page][SAVE]', 'Page: Save', $language_code ?? '') !!}
                                    {!! create_label('translations[page][COMPANY_DETAILS_HINT]', 'Page: Company Details Hint', $language_code ?? '') !!}
                                    {!! create_label('translations[page][BASIC_COMPANY_SETUP]', 'Page: Basic Company Setup', $language_code ?? '') !!}
                                    
                                    {{-- <><><><><><><><><><> END COMPANY_DETAILS LABELS <><><><><><><><><><> --}}

                                    {{-- <><><><><><><><><><> CRON_JOB_INFO  LABELS <><><><><><><><><><> --}}
                                    {!! create_label('translations[page][CRON_JOB_INFO]', 'Page: Cron Job Info', $language_code ?? '') !!}
                                    {!! create_label('translations[page][HOME]', 'Page: Home', $language_code ?? '') !!}
                                    {{-- <><><><><><><><><><> END CRON_JOB_INFO  LABELS <><><><><><><><><><> --}}

                                    {{-- <><><><><><><><><><> CRON_JOB_INFO  LABELS <><><><><><><><><><> --}}
                                    {!! create_label('translations[page][FIREBASE_DETAILS]', 'Page: Firebase Details', $language_code ?? '') !!}
                                    {!! create_label('translations[page][API_KEY]', 'Page: API Key', $language_code ?? '') !!}
                                    {!! create_label('translations[page][AUTH_DOMAIN]', 'Page: Auth Domain', $language_code ?? '') !!}
                                    {!! create_label('translations[page][PROJECT_ID]', 'Page: Project ID', $language_code ?? '') !!}
                                    {!! create_label('translations[page][STORAGE_BUCKET]', 'Page: Storage Bucket', $language_code ?? '') !!}
                                    {!! create_label('translations[page][MESSAGING_SENDER_ID]', 'Page: Messaging Sender ID', $language_code ?? '') !!}
                                    {!! create_label('translations[page][APP_ID]', 'Page: App ID', $language_code ?? '') !!}
                                    {!! create_label('translations[page][MEASUREMENT_ID]', 'Page: Measurement ID', $language_code ?? '') !!}
                                    {!! create_label('translations[page][SERVICE_ACCOUNT_FILE]', 'Page: Service Account File', $language_code ?? '') !!}
                                    {!! create_label('translations[page][FIREBASE_SETTINGS]', 'Page: Firebase Settings', $language_code ?? '') !!}
                                    {!! create_label('translations[page][ADD_LANGUAGE]', 'Page: Add Language', $language_code ?? '') !!}
                                    {{-- <><><><><><><><><><> CRON_JOB_INFO  LABELS <><><><><><><><><><> --}}

                                    {{-- <><><><><><><><><><> LANGUAGE  LABELS <><><><><><><><><><> --}}
                                    {!! create_label('translations[page][CREATE_LANGUAGE]', 'Page: Create Language', $language_code ?? '') !!}
                                    {!! create_label('translations[page][LANGUAGE_NAME]', 'Page: Language Name', $language_code ?? '') !!}
                                    {!! create_label('translations[page][IN_ENGLISH]', 'Page: In English', $language_code ?? '') !!}
                                    {!! create_label('translations[page][LANGUAGE_CODE]', 'Page: Language Code', $language_code ?? '') !!}
                                    {!! create_label('translations[page][IMAGE]', 'Page: Image', $language_code ?? '') !!}
                                    {!! create_label('translations[page][RTL]', 'Page: RTL (Right to Left)', $language_code ?? '') !!}
                                    {{-- <><><><><><><><><><> END LANGUAGE  LABELS <><><><><><><><><><> --}}

                                    {{-- <><><><><><><><><><> SOCIAL_LINKS_AND_OTHER_SETTINGS  LABELS <><><><><><><><><><> --}}
                                    {!! create_label(
                                        'translations[page][SOCIAL_LINKS_AND_OTHER_SETTINGS]',
                                        'Page: Social Links and AWS Configuration',
                                        $language_code ?? '',
                                    ) !!}
                                    {!! create_label('translations[page][SOCIAL_MEDIA_LINKS]', 'Page: Social Media Links', $language_code ?? '') !!}
                                    {!! create_label('translations[page][OTHER_SETTINGS]', 'Page: Other Settings', $language_code ?? '') !!}
                                    {!! create_label('translations[page][HOME]', 'Page: Home', $language_code ?? '') !!}
                                    {!! create_label('translations[page][SETTINGS]', 'Page: Settings', $language_code ?? '') !!}

                                    {!! create_label('translations[page][INSTAGRAM_LINK]', 'Page: Instagram Link', $language_code ?? '') !!}
                                    {!! create_label(
                                        'translations[page][ENTER_INSTAGRAM_LINK]',
                                        'Page: Enter Instagram Profile Link',
                                        $language_code ?? '',
                                    ) !!}
                                    {!! create_label('translations[page][X_LINK]', 'Page: X (Twitter) Link', $language_code ?? '') !!}
                                    {!! create_label('translations[page][ENTER_X_LINK]', 'Page: Enter X Profile Link', $language_code ?? '') !!}
                                    {!! create_label('translations[page][FACEBOOK_LINK]', 'Page: Facebook Link', $language_code ?? '') !!}
                                    {!! create_label(
                                        'translations[page][ENTER_FACEBOOK_LINK]',
                                        'Page: Enter Facebook Profile Link',
                                        $language_code ?? '',
                                    ) !!}
                                    {!! create_label('translations[page][LINKEDIN_LINK]', 'Page: LinkedIn Link', $language_code ?? '') !!}
                                    {!! create_label(
                                        'translations[page][ENTER_LINKEDIN_LINK]',
                                        'Page: Enter LinkedIn Profile Link',
                                        $language_code ?? '',
                                    ) !!}
                                    {!! create_label('translations[page][PINTEREST_LINK]', 'Page: Pinterest Link', $language_code ?? '') !!}
                                    {!! create_label(
                                        'translations[page][ENTER_PINTEREST_LINK]',
                                        'Page: Enter Pinterest Profile Link',
                                        $language_code ?? '',
                                    ) !!}

                                    {!! create_label('translations[page][PLAY_STORE_LINK]', 'Page: Play Store Link', $language_code ?? '') !!}
                                    {!! create_label(
                                        'translations[page][EMTER_PLAY_STORE_LINK]',
                                        'Page: Enter Play Store Link',
                                        $language_code ?? '',
                                    ) !!}
                                    {!! create_label('translations[page][APP_STORE_LINK]', 'Page: App Store Link', $language_code ?? '') !!}
                                    {!! create_label('translations[page][EMTER_APP_STORE_LINK]', 'Page: Enter App Store Link', $language_code ?? '') !!}
                                    {!! create_label('translations[page][ANDROID_SCHEME]', 'Page: Android Scheme', $language_code ?? '') !!}
                                    {!! create_label('translations[page][EMTER_ANDROID_SCHEME]', 'Page: Enter Android Scheme', $language_code ?? '') !!}
                                    {!! create_label('translations[page][IOS_SCHEME]', 'Page: iOS Scheme', $language_code ?? '') !!}
                                    {!! create_label('translations[page][EMTER_IOS_SCHEME]', 'Page: Enter iOS Scheme', $language_code ?? '') !!}
                                    {!! create_label('translations[page][SYSTEM_HEALTH]', 'Page: System Health', $language_code ?? '') !!}
                                    {!! create_label('translations[page][APP_ADMOB_AND_WEATHER_KEY_SETUP]', 'Page: App AdMob and Weather Key Setup', $language_code ?? '') !!}
                                    {!! create_label('translations[page][ADMOB_KEYS_SETUP]', 'Page: AdMob Keys Setup', $language_code ?? '') !!}
                                    {!! create_label('translations[page][IOS_ADMOB_KEYS_SETUP]', 'Page: AdMob Keys Setup', $language_code ?? '') !!}
                                    {!! create_label('translations[page][APPLICATION_DOWNLOAD_POPUP]', 'Application Download Popup', $language_code ?? '') !!}

                                    {!! create_label('translations[page][ANDROID_ADMOB_APP_ID]', 'Android AdMob App ID', $language_code ?? '') !!}
                                    {!! create_label('translations[page][ANDROID_BANNER_AD_KEY]', 'Android Banner Ad Key', $language_code ?? '') !!}
                                    {!! create_label('translations[page][ANDROID_INTERSTITIAL_AD_KEY]', 'Android Interstitial Ad Key', $language_code ?? '') !!}
                                    {!! create_label('translations[page][ANDROID_OPEN_AD_KEY]', 'Android Open Ad Key', $language_code ?? '') !!}

                                    {!! create_label('translations[page][IOS_ADMOB_APP_ID]', 'iOS AdMob App ID', $language_code ?? '') !!}
                                    {!! create_label('translations[page][IOS_BANNER_AD_KEY]', 'iOS Banner Ad Key', $language_code ?? '') !!}
                                    {!! create_label('translations[page][IOS_INTERSTITIAL_AD_KEY]', 'iOS Interstitial Ad Key', $language_code ?? '') !!}
                                    {!! create_label('translations[page][IOS_OPEN_AD_KEY]', 'iOS Open Ad Key', $language_code ?? '') !!}

                                    {!! create_label(
                                        'translations[page][HOW_MANY_DAYS_OLD_POSTS_SHOULD_BE_KEPT]',
                                        'Page: How many days old posts should be kept?',
                                        $language_code ?? '',
                                    ) !!}
                                    {!! create_label(
                                        'translations[page][HOW_MANY_DAYS_OLD_VIDEO_POSTS_SHOULD_BE_KEPT]',
                                        'Page: How many days old videos should be kept?',
                                        $language_code ?? '',
                                    ) !!}
                                    {!! create_label('translations[page][ENTER_IN_DAYS]', 'Page: Enter in days', $language_code ?? '') !!}
                                    {!! create_label(
                                        'translations[page][PLEASE_ENTER_MORE_THAN_10_DAYS]',
                                        'Page: Please enter more than 10 days',
                                        $language_code ?? '',
                                    ) !!}
                                    {!! create_label(
                                        'translations[page][HOW_MANY_DAYS_OLD_NOTIFICATIONS_SHOULD_BE_KEPT]',
                                        'Page: How many days old notifications should be kept?',
                                        $language_code ?? '',
                                    ) !!}

                                    {!! create_label('translations[page][APP_NAME]', 'Page: App Name', $language_code ?? '') !!}
                                    {!! create_label('translations[page][ENTER_APP_NAME]', 'Page: Enter App Name', $language_code ?? '') !!}
                                    {!! create_label('translations[page][MAINTENANCE_MODE]', 'Page: Maintenance Mode', $language_code ?? '') !!}
                                    {!! create_label('translations[page][FREE_TRIAL_MODE]', 'Page: Free Trial Mode', $language_code ?? '') !!}
                                    {!! create_label(
                                        'translations[page][FREE_TRIAL_POST_LIMIT]',
                                        'Page: Free Trial Post Limit',
                                        $language_code ?? '',
                                    ) !!}
                                    {!! create_label('translations[page][ENTER_POST_LIMIT]', 'Page: Enter Post Limit', $language_code ?? '') !!}
                                    {!! create_label(
                                        'translations[page][NUMBER_OF_POSTS_FREE_TRIAL_USERS_CAN_VIEW]',
                                        'Page: Number of posts free trial users can view',
                                        $language_code ?? '',
                                    ) !!}
                                    {!! create_label(
                                        'translations[page][FREE_TRIAL_STORY_LIMIT]',
                                        'Page: Free Trial Story Limit',
                                        $language_code ?? '',
                                    ) !!}
                                    {!! create_label('translations[page][ENTER_STORY_LIMIT]', 'Page: Enter Story Limit', $language_code ?? '') !!}
                                    {!! create_label(
                                        'translations[page][NUMBER_OF_STORIES_FREE_TRIAL_USERS_CAN_VIEW]',
                                        'Page: Number of stories free trial users can view',
                                        $language_code ?? '',
                                    ) !!}


                                    {{-- <><><><><><><><><><> END SOCIAL_LINKS_AND_OTHER_SETTINGS  LABELS <><><><><><><><><><> --}}

                                    {{-- <><><><><><><><><><> LOGO_MANAGEMENT_AND_WEATHER_API_KEY_SETTING  LABELS <><><><><><><><><><> --}}
                                    {!! create_label(
                                        'translations[page][LOGO_MANAGEMENT_AND_WEATHER_API_KEY_SETTING]',
                                        'Page: Logo Management and Weather API Key Setting',
                                        $language_code ?? '',
                                    ) !!}
                                    {!! create_label('translations[page][LOGO_IMAGES]', 'Page: Logo Images', $language_code ?? '') !!}
                                    {!! create_label('translations[page][FIREBASE]', 'Page: Firebase', $language_code ?? '') !!}
                                    {!! create_label('translations[page][SIDEBAR_LOGO]', 'Page: Sidebar Logo', $language_code ?? '') !!}
                                    {!! create_label('translations[page][WEB_SETTINGS]', 'Page: Web Settings', $language_code ?? '') !!}
                                    {!! create_label('translations[page][NEWS_LABEL]', 'Page: News Label', $language_code ?? '') !!}
                                    {!! create_label('translations[page][WEATHER_API_KEY]', 'Page: Weather API Key', $language_code ?? '') !!}
                                    {!! create_label(
                                        'translations[page][ENTER_WEATHER_API_KEY]',
                                        'Page: Enter Weather API Key',
                                        $language_code ?? '',
                                    ) !!}
                                    {!! create_label('translations[page][LIGHT_LOGO]', 'Page: Light Logo', $language_code ?? '') !!}
                                    {!! create_label('translations[page][DARK_LOGO]', 'Page: Dark Logo', $language_code ?? '') !!}
                                    {!! create_label('translations[page][DEFAULT_IMAGES]', 'Page: Default Images', $language_code ?? '') !!}
                                    {!! create_label('translations[page][ADD_DEFAULT_IMAGE]', 'Page: Add Default Image', $language_code ?? '') !!}

                                     {!! create_label('translations[page][THEME_COLOR_CUSTOMIZATION]', 'Page: Theme Color Customization', $language_code ?? '') !!}
                                     {!! create_label('translations[page][WEB_THEME_PRIMARY_COLOUR]', 'Page: Web Theme Primary Color', $language_code ?? '') !!}
                                     {!! create_label('translations[page][APP_THEME_PRIMARY_COLOUR]', 'Page: App Theme Primary Color', $language_code ?? '') !!}
                                     {!! create_label('translations[page][CHOOSE_WEB_THEME_PRIMARY_COLOUR]', 'Page: Choose Web Theme Primary Color', $language_code ?? '') !!}
                                     {!! create_label('translations[page][CHOOSE_APP_THEME_PRIMARY_COLOUR]', 'Page: Choose App Theme Primary Color', $language_code ?? '') !!}
                                     {!! create_label('translations[page][APP_FONT]', 'App Font', $language_code ?? '') !!}
                                    {!! create_label('translations[page][CHOOSE_APP_FONT]', 'Choose App Font', $language_code ?? '') !!}
                                    {!! create_label('translations[page][SELECT_FONT_FAMILY]', 'Select Font Family', $language_code ?? '') !!}
                                    {!! create_label('translations[page][WEB_FONT]', 'Web Font', $language_code ?? '') !!}
                                    {!! create_label('translations[page][CHOOSE_WEB_FONT]', 'Choose Web Font', $language_code ?? '') !!}

                                    {{-- <><><><><><><><><><> END LOGO_MANAGEMENT_AND_WEATHER_API_KEY_SETTING  LABELS <><><><><><><><><><> --}}

                                    {{-- <><><><><><><><><><> SUBSCRIPTION_MODEL_AND_HEADER_FOOTER_SCRIPT_SETTING_  LABELS <><><><><><><><><><> --}}
                                    {!! create_label(
                                        'translations[page][SUBSCRIPTION_MODEL_AND_HEADER_FOOTER_SCRIPT_SETTING]',
                                        'Page: Subscription Model and Header/Footer Script Setting',
                                        $language_code ?? '',
                                    ) !!}
                                    {!! create_label('translations[page][SUBSCRIBE_MODEL]', 'Page: Subscribe Model', $language_code ?? '') !!}
                                    {!! create_label('translations[page][MODEL_TITLE]', 'Page: Model Title', $language_code ?? '') !!}
                                    {!! create_label('translations[page][MODEL_SUB_TITLE]', 'Page: Model Sub Title', $language_code ?? '') !!}
                                    {!! create_label('translations[page][MODEL_STATUS]', 'Page: Model Status', $language_code ?? '') !!}
                                    {!! create_label('translations[page][MODEL_IMAGE]', 'Page: Model Image', $language_code ?? '') !!}
                                    {!! create_label('translations[page][SCRIPTS]', 'Page: Scripts', $language_code ?? '') !!}
                                    {!! create_label('translations[page][HEADER_SCRIPT]', 'Page: Header Script', $language_code ?? '') !!}
                                    {!! create_label('translations[page][FOOTER_SCRIPT]', 'Page: Footer Script', $language_code ?? '') !!}
                                    {!! create_label('translations[page][INSERT_HEADER_SCRIPT]', 'Page: Insert Header Script', $language_code ?? '') !!}
                                    {!! create_label('translations[page][INSERT_FOOTER_SCRIPT]', 'Page: Insert Footer Script', $language_code ?? '') !!}
                                    {!! create_label(
                                        'translations[page][SUBSCRIBE_MODEL_TITLE]',
                                        'Page: Subscribe Model Title',
                                        $language_code ?? '',
                                    ) !!}
                                    {!! create_label(
                                        'translations[page][SUBSCRIBE_MODEL_SUB_TITLE]',
                                        'Page: Subscribe Model Sub Title',
                                        $language_code ?? '',
                                    ) !!}
                                    {{-- <><><><><><><><><><> END SUBSCRIPTION_MODEL_AND_HEADER_FOOTER_SCRIPT_SETTING  LABELS <><><><><><><><><><> --}}


                                    {{-- <><><><><><><><><><> END LOGO_MANAGEMENT_AND_WEATHER_API_KEY_SETTING  LABELS <><><><><><><><><><> --}}
                                    {!! create_label(
                                        'translations[page][NEWS_LANGUAGE_SETTINGS]',
                                        'Page: News Language Settings',
                                        $language_code ?? '',
                                    ) !!}
                                    {!! create_label(
                                        'translations[page][ENABLE_NEWS_LANGUAGES]',
                                        'Page: Enable News Languages',
                                        $language_code ?? '',
                                    ) !!}
                                    {!! create_label(
                                        'translations[page][DEFAULT_NEWS_LANGUAGE]',
                                        'Page: Default News Language',
                                        $language_code ?? '',
                                    ) !!}
                                    {{-- <><><><><><><><><><> END LOGO_MANAGEMENT_AND_WEATHER_API_KEY_SETTING  LABELS <><><><><><><><><><> --}}

                                    {{-- <><><><><><><><><><> END STRIPE_SETTING and RAZORPAY_SETTING   LABELS <><><><><><><><><><> --}}
                                    {!! create_label('translations[page][STRIPE_SETTING]', 'Page: Stripe Settings', $language_code ?? '') !!}
                                    {!! create_label(
                                        'translations[page][SELECT_CURRENCY_FOR_STRIPE]',
                                        'Page: Select Currency for Stripe',
                                        $language_code ?? '',
                                    ) !!}
                                    {!! create_label(
                                        'translations[page][STRIPE_CURRENCY_SYMBOL]',
                                        'Page: Stripe Currency Symbol',
                                        $language_code ?? '',
                                    ) !!}
                                    {!! create_label('translations[page][STRIPE_SECRET_KEY]', 'Page: Stripe Secret Key', $language_code ?? '') !!}
                                    {!! create_label(
                                        'translations[page][STRIPE_PUBLISHABLE_KEY]',
                                        'Page: Stripe Publishable Key',
                                        $language_code ?? '',
                                    ) !!}
                                    {!! create_label(
                                        'translations[page][STRIPE_WEBHOOK_SECRET]',
                                        'Page: Stripe Webhook Secret',
                                        $language_code ?? '',
                                    ) !!}
                                    {!! create_label('translations[page][STRIPE_WEBHOOK_URL]', 'Page: Stripe Webhook URL', $language_code ?? '') !!}
                                    {!! create_label('translations[page][RAZORPAY_SETTING]', 'Page: Razorpay Settings', $language_code ?? '') !!}
                                    {!! create_label(
                                        'translations[page][SELECT_CURRENCY_FOR_RAZORPAY]',
                                        'Page: Select Currency for Razorpay',
                                        $language_code ?? '',
                                    ) !!}
                                    {!! create_label(
                                        'translations[page][RAZORPAY_CURRENCY_SYMBOL]',
                                        'Page: Razorpay Currency Symbol',
                                        $language_code ?? '',
                                    ) !!}
                                    {!! create_label('translations[page][RAZORPAY_SECRET_KEY]', 'Page: Razorpay Secret Key', $language_code ?? '') !!}
                                    {!! create_label('translations[page][RAZORPAY_PUBLIC_KEY]', 'Page: Razorpay Public Key', $language_code ?? '') !!}
                                    {!! create_label(
                                        'translations[page][RAZORPAY_WEBHOOK_SECRET]',
                                        'Page: Razorpay Webhook Secret',
                                        $language_code ?? '',
                                    ) !!}
                                    {!! create_label('translations[page][RAZORPAY_WEBHOOK_URL]', 'Page: Razorpay Webhook URL', $language_code ?? '') !!}
                                    {!! create_label('translations[page][STATUS]', 'Page: Status', $language_code ?? '') !!}
                                    {{-- <><><><><><><><><><> END STRIPE_SETTING and RAZORPAY_SETTING   LABELS <><><><><><><><><><> --}}

                                    {!! create_label('translations[page][IS_DEFAULT]', 'Page: Is Default', $language_code ?? '') !!}
                                    {!! create_label('translations[page][ADD_THEME]', 'Page: Add Theme', $language_code ?? '') !!}
                                    {!! create_label('translations[page][EDIT_THEME]', 'Page: Edit Theme', $language_code ?? '') !!}
                                    {!! create_label('translations[page][THEME_NAME]', 'Page: Theme Name', $language_code ?? '') !!}
                                    {!! create_label('translations[page][SELECT_STATUS]', 'Page: Select Status', $language_code ?? '') !!}
                                    {!! create_label('translations[page][LOGO]', 'Page: Logo', $language_code ?? '') !!}
                                    {!! create_label(
                                        'translations[message][PLEASE_ENTER_THEME_NAME]',
                                        'Message: Please Enter Theme Name',
                                        $language_code ?? '',
                                    ) !!}

                                    {!! create_label('translations[page][CURRENT_VERSION]', 'Page: Current Version', $language_code ?? '') !!}
                                    {!! create_label('translations[page][UPDATE_THE_SYSTEM]', 'Page: Update the System', $language_code ?? '') !!}
                                    {!! create_label(
                                        'translations[message][CLEAR_YOUR_BROWSER_CACHE_BY_PRESSINH]',
                                        'Message: Clear your browser cache by pressing',
                                        $language_code ?? '',
                                    ) !!}
                                    {!! create_label(
                                        'translations[message][AFTER_UPDATING_THE_SYSTEM]',
                                        'Message: After updating the system',
                                        $language_code ?? '',
                                    ) !!}

                                    {!! create_label('translations[page][DASHBOARD]', 'Page: Dashboard', $language_code ?? '') !!}
                                    {!! create_label('translations[page][POSTS]', 'Page: Posts', $language_code ?? '') !!}
                                    {!! create_label('translations[page][STORIES]', 'Page: Stories', $language_code ?? '') !!}
                                    {!! create_label('translations[page][NEWS_LANGUAGES]', 'Page: News Languages', $language_code ?? '') !!}
                                    {!! create_label('translations[page][CHANNELS]', 'Page: Channels', $language_code ?? '') !!}
                                    {!! create_label('translations[page][TOPICS]', 'Page: Topics', $language_code ?? '') !!}
                                    {!! create_label('translations[page][RSS_FEEDS]', 'Page: Rss Feeds', $language_code ?? '') !!}
                                    {!! create_label('translations[page][USERS]', 'Page: Rss Users', $language_code ?? '') !!}
                                    {!! create_label('translations[page][MEMBERSHIP_PLANS]', 'Page: Membership Plans', $language_code ?? '') !!}
                                    {!! create_label('translations[page][SUBSCRIPTIONS]', 'Page:Subscriptions', $language_code ?? '') !!}
                                    {!! create_label('translations[page][TRANSACTIONS]', 'Page: Transactions', $language_code ?? '') !!}
                                    {!! create_label('translations[page][SUBSCRIBERS]', 'Page: Subscribers', $language_code ?? '') !!}
                                    {!! create_label('translations[page][NOTIFICATION]', 'Page: Notification', $language_code ?? '') !!}
                                    {!! create_label('translations[page][REPORTED_COMMENTS]', 'Page: Reported Comments', $language_code ?? '') !!}

                                    {!! create_label('translations[page][BLOCKED_COMMENTS]', 'Page: Blocked Comments', $language_code ?? '') !!}

                                    {!! create_label('translations[page][CONTACT_US]', 'Page: Contact Us', $language_code ?? '') !!}
                                    {!! create_label('translations[page][ADMIN_USERS]', 'Page: Admin Users', $language_code ?? '') !!}
                                    {!! create_label('translations[page][ROLES]', 'Page: Roles', $language_code ?? '') !!}
                                    {!! create_label('translations[page][ADMINS]', 'Page: Admins', $language_code ?? '') !!}
                                    {!! create_label('translations[page][CHANGE_PASSWORD]', 'Change Password', $language_code ?? '') !!}
                                    {!! create_label('translations[page][UPDATE_PROFILE]', 'Update Profile ', $language_code ?? '') !!} {{-- Typo fixed from "UPDATE_PROFILE" --}}
                                    {!! create_label('translations[page][LOGOUT]', 'Logout', $language_code ?? '') !!}
                                    {!! create_label('translations[page][CURRENT_PASSWORD]', 'Current Password', $language_code ?? '') !!}
                                    {!! create_label('translations[page][NEW_PASSWORD]', 'New Password', $language_code ?? '') !!}
                                    {!! create_label('translations[page][PROFILE]', 'Profile', $language_code ?? '') !!}


                                    {!! create_label('translations[page][HOME]', 'Page: Home', $language_code ?? '') !!}
                                    {!! create_label('translations[page][CREATE_VIDEOS]', 'Page: Create Videos', $language_code ?? '') !!}
                                    {!! create_label('translations[page][CREATE_YOUTUBE_VIDEO]', 'Page: Create YouTube Video', $language_code ?? '') !!}
                                    {!! create_label('translations[page][LOADING]', 'Page: Loading', $language_code ?? '') !!}
                                    {!! create_label('translations[page][VIDEO_POSTS]', 'Page: Video Posts', $language_code ?? '') !!}
                                    {!! create_label('translations[page][YOUTUBE_VIDEO]', 'Page: YouTube Video', $language_code ?? '') !!}
                                    {!! create_label('translations[page][SELECT_CHANNEL]', 'Page: Select Channel', $language_code ?? '') !!}
                                    {!! create_label('translations[page][ALL]', 'Page: All', $language_code ?? '') !!}
                                    {!! create_label('translations[page][SEARCH]', 'Page: Search', $language_code ?? '') !!}
                                    {!! create_label('translations[page][POST_DESCRIPTION]', 'Page: Post Description', $language_code ?? '') !!}
                                    {!! create_label('translations[page][DESCRIPTION]', 'Page: Description', $language_code ?? '') !!}
                                    {!! create_label('translations[page][EDIT_VIDEO]', 'Page: Edit Video', $language_code ?? '') !!}
                                    {!! create_label('translations[page][EDIT_YOUTUBE]', 'Page: Edit YouTube', $language_code ?? '') !!}
                                    {!! create_label('translations[page][DELETE]', 'Page: Delete', $language_code ?? '') !!}
                                    {!! create_label('translations[page][CLOSE]', 'Page: Close', $language_code ?? '') !!}

                                    {!! create_label('translations[page][HOME]', 'Page: Home', $language_code ?? '') !!}
                                    {!! create_label('translations[page][VIDEOS]', 'Page: Videos', $language_code ?? '') !!}
                                    {!! create_label('translations[page][DETAILS]', 'Page: Details', $language_code ?? '') !!}
                                    {!! create_label('translations[page][TITLE]', 'Page: Title', $language_code ?? '') !!}
                                    {!! create_label('translations[page][POST_DESCRIPTION]', 'Page: Post Description', $language_code ?? '') !!}
                                    {!! create_label(
                                        'translations[page][SELECT_NEWSLANGUAGE]',
                                        'Page: Select News Language (label)',
                                        $language_code ?? '',
                                    ) !!}
                                    {!! create_label(
                                        'translations[page][SELECT_NEWS_LANGUAGE]',
                                        'Page: Select News Language (placeholder)',
                                        $language_code ?? '',
                                    ) !!}
                                    {!! create_label('translations[page][SELECT_CHANNEL]', 'Page: Select Channel', $language_code ?? '') !!}
                                    {!! create_label('translations[page][TYPE]', 'Page: Type', $language_code ?? '') !!}
                                    {!! create_label('translations[page][VIDEO]', 'Page: Video', $language_code ?? '') !!}
                                    {!! create_label('translations[page][STATUS]', 'Page: Status', $language_code ?? '') !!}
                                    {!! create_label('translations[page][ACTIVE]', 'Page: Active', $language_code ?? '') !!}
                                    {!! create_label('translations[page][INACTIVE]', 'Page: Inactive', $language_code ?? '') !!}
                                    {!! create_label('translations[page][IMAGE]', 'Page: Image', $language_code ?? '') !!}
                                    {!! create_label('translations[page][THUMBNAIL]', 'Page: Thumbnail', $language_code ?? '') !!}
                                    {!! create_label('translations[page][BACK]', 'Page: Back', $language_code ?? '') !!}
                                    {!! create_label('translations[page][SAVE]', 'Page: Save', $language_code ?? '') !!}

                                    {!! create_label('translations[page][HOME]', 'Page: Home', $language_code ?? '') !!}
                                    {!! create_label('translations[page][VIDEOS]', 'Page: Videos', $language_code ?? '') !!}
                                    {!! create_label('translations[page][DETAILS]', 'Page: Details', $language_code ?? '') !!}
                                    {!! create_label('translations[page][TITLE]', 'Page: Title', $language_code ?? '') !!}
                                    {!! create_label('translations[page][POST_DESCRIPTION]', 'Page: Post Description', $language_code ?? '') !!}
                                    {!! create_label('translations[page][SELECT_NEWSLANGUAGE]', 'Page: Select News Language', $language_code ?? '') !!}
                                    {!! create_label('translations[page][SELECT_NEWS_LANGUAGE]', 'Page: Select News Language', $language_code ?? '') !!}
                                    {!! create_label('translations[page][SELECT_CHANNEL]', 'Page: Select Channel', $language_code ?? '') !!}
                                    {!! create_label('translations[page][TYPE]', 'Page: Type', $language_code ?? '') !!}
                                    {!! create_label('translations[page][YOUTUBE]', 'Page: YouTube', $language_code ?? '') !!}
                                    {!! create_label('translations[page][STATUS]', 'Page: Status', $language_code ?? '') !!}
                                    {!! create_label('translations[page][ACTIVE]', 'Page: Active', $language_code ?? '') !!}
                                    {!! create_label('translations[page][INACTIVE]', 'Page: Inactive', $language_code ?? '') !!}
                                    {!! create_label('translations[page][IMAGE]', 'Page: Image', $language_code ?? '') !!}
                                    {!! create_label('translations[page][THUMBNAIL]', 'Page: Thumbnail', $language_code ?? '') !!}
                                    {!! create_label('translations[page][VIDEO_URL]', 'Page: Video URL', $language_code ?? '') !!}
                                    {!! create_label('translations[page][BACK]', 'Page: Back', $language_code ?? '') !!}
                                    {!! create_label('translations[page][SAVE]', 'Page: Save', $language_code ?? '') !!}

                                    {!! create_label('translations[page][HOME]', 'Page: Home', $language_code ?? '') !!}
                                    {!! create_label('translations[page][VIDEOS]', 'Page: Videos', $language_code ?? '') !!}
                                    {!! create_label('translations[page][DETAILS]', 'Page: Details', $language_code ?? '') !!}
                                    {!! create_label('translations[page][TITLE]', 'Page: Title', $language_code ?? '') !!}
                                    {!! create_label('translations[page][POST_DESCRIPTION]', 'Page: Post Description', $language_code ?? '') !!}
                                    {!! create_label('translations[page][SELECT_NEWSLANGUAGE]', 'Page: Select News Language', $language_code ?? '') !!}
                                    {!! create_label(
                                        'translations[page][SELECT_NEWS_LANGUAGE]',
                                        'Page: Select News Language Placeholder',
                                        $language_code ?? '',
                                    ) !!}
                                    {!! create_label('translations[page][SELECT_CHANNEL]', 'Page: Select Channel', $language_code ?? '') !!}
                                    {!! create_label('translations[page][TYPE]', 'Page: Type', $language_code ?? '') !!}
                                    {!! create_label('translations[page][YOUTUBE]', 'Page: YouTube', $language_code ?? '') !!}
                                    {!! create_label('translations[page][VIDEO]', 'Page: Video', $language_code ?? '') !!}
                                    {!! create_label('translations[page][VIDEO_URL]', 'Page: Video URL', $language_code ?? '') !!}
                                    {!! create_label('translations[page][STATUS]', 'Page: Status', $language_code ?? '') !!}
                                    {!! create_label('translations[page][ACTIVE]', 'Page: Active', $language_code ?? '') !!}
                                    {!! create_label('translations[page][INACTIVE]', 'Page: Inactive', $language_code ?? '') !!}
                                    {!! create_label('translations[page][IMAGE]', 'Page: Image', $language_code ?? '') !!}
                                    {!! create_label('translations[page][THUMBNAIL]', 'Page: Thumbnail', $language_code ?? '') !!}
                                    {!! create_label('translations[page][BACK]', 'Page: Back', $language_code ?? '') !!}
                                    {!! create_label('translations[page][SAVE]', 'Page: Save', $language_code ?? '') !!}
                                    {!! create_label('translations[page][E_NEWSPAPERS_AND_MAGAZINES]', 'Page: E-Newspapers', $language_code ?? '') !!}
                                    {!! create_label(
                                        'translations[page][CREATE_E_NEWSPAPER_AND_MAGAZINE]',
                                        'Page: Create E-Newspaper',
                                        $language_code ?? '',
                                    ) !!}
                                    {!! create_label('translations[page][EDIT_E_NEWSPAPER]', 'Page: Edit E-Newspaper', $language_code ?? '') !!}
                                    {!! create_label('translations[page][EDIT_VIDEOS]', 'Page: Edit Videos', $language_code ?? '') !!}
                                    {!! create_label('translations[page][BACKGROUND_IMAGE]', 'Page: Background Image', $language_code ?? '') !!}

                                    {!! create_label(
                                        'translations[page][EMAIL_TEMPLATE_DETAILS]',
                                        'Page: Email Template Details',
                                        $language_code ?? '',
                                    ) !!}
                                    {!! create_label(
                                        'translations[page][CREATE_EMAIL_TEMPLATE]',
                                        'Page: Create Email Template',
                                        $language_code ?? '',
                                    ) !!}
                                    {!! create_label('translations[page][EDIT_EMAIL_TEMPLATE]', 'Page: Edit Email Template', $language_code ?? '') !!}
                                    {!! create_label('translations[page][HOME]', 'Page: Home', $language_code ?? '') !!}
                                    {!! create_label(
                                        'translations[page][CREATE_EMAIL_TEMPLATE]',
                                        'Page: Create Email Template',
                                        $language_code ?? '',
                                    ) !!}
                                    {!! create_label('translations[page][ALL]', 'Page: All', $language_code ?? '') !!}
                                    {!! create_label('translations[page][ACTIVE]', 'Page: Active', $language_code ?? '') !!}
                                    {!! create_label('translations[page][INACTIVE]', 'Page: Inactive', $language_code ?? '') !!}
                                    {!! create_label('translations[page][POST_COUNT]', 'Page: Post Count', $language_code ?? '') !!}
                                    {!! create_label('translations[page][LAYOUT_WIDTH]', 'Page: Layout Width', $language_code ?? '') !!}
                                    {!! create_label('translations[global][ID]', 'Global: ID', $language_code ?? '') !!}
                                    {!! create_label('translations[global][TITLE]', 'Global: Title', $language_code ?? '') !!}
                                    {!! create_label('translations[global][SLUG]', 'Global: Slug', $language_code ?? '') !!}
                                    {!! create_label('translations[page][POST_COUNT]', 'Page: Post Count', $language_code ?? '') !!}
                                    {!! create_label('translations[page][LAYOUT_WIDTH]', 'Page: Layout Width', $language_code ?? '') !!}
                                    {!! create_label('translations[global][STATUS]', 'Global: Status', $language_code ?? '') !!}
                                    {!! create_label('translations[global][CREATED_AT]', 'Global: Created At', $language_code ?? '') !!}
                                    {!! create_label('translations[global][ACTION]', 'Global: Action', $language_code ?? '') !!}
                                    {!! create_label('translations[page][EMAIL_TEMPLATE]', 'Page: Email Template', $language_code ?? '') !!}
                                    {!! create_label(
                                        'translations[page][SMTP_MAIL_CONFIGURATION]',
                                        'Page: SMTP Mail Configuration',
                                        $language_code ?? '',
                                    ) !!}
                                    {!! create_label(
                                        'translations[page][ADD_E_NEWS_PAPER_IMAGE]',
                                        'Page: Add E-Newspaper Image',
                                        $language_code ?? '',
                                    ) !!}
                                    {!! create_label(
                                        'translations[page][FREE_TRIAL_E_PAPER_AND_MAGAZINES_LIMIT]',
                                        'Page: Free Trial E-Paper and Magazines Limit',
                                        $language_code ?? '',
                                    ) !!}
                                    {!! create_label(
                                        'translations[page][ENTER_E_PAPER_AND_MAGAZINES_LIMIT]',
                                        'Page: Enter E-Paper and Magazines Limit',
                                        $language_code ?? '',
                                    ) !!}
                                    {!! create_label(
                                        'translations[page][NO_OF_E_PAPER_AND_MAGAZINES_FREE_TRIAL_USERS_CAN_VIEW]',
                                        'Page: Number of E-Papers and Magazines Free Trial Users Can View',
                                        $language_code ?? '',
                                    ) !!}
                                    {!! create_label('translations[page][ENEWS_PAPER_TITLE]', 'Page: E-News Paper Title', $language_code ?? '') !!}
                                    {!! create_label(
                                        'translations[page][ENEWS_PAPER_TITLE_TOOLTIP]',
                                        'Page: Provide the API key for accessing weather data on your website.',
                                        $language_code ?? '',
                                    ) !!}
                                    {!! create_label('translations[page][VIDEOS]', 'Page: Videos', $language_code ?? '') !!}
                                    {!! create_label('translations[page][TOPICS]', 'Page: Topics', $language_code ?? '') !!}
                                    {!! create_label('translations[page][RSS_FEEDS]', 'Page: RSS Feeds', $language_code ?? '') !!}
                                    {!! create_label('translations[page][E_NEWSPAPERS_AND_MAGAZINES]', 'Page: E-Newspapers', $language_code ?? '') !!}
                                    {!! create_label('translations[page][EMAIL_TEMPLATE]', 'Page: Email Template', $language_code ?? '') !!}
                                    {!! create_label('translations[page][APPLE_PAY_SETTING]', 'Apple Pay Setting', $language_code ?? '') !!}
                                    {!! create_label(
                                        'translations[page][SELECT_CURRENCY_FOR_APPLEPAY]',
                                        'Select Currency for Apple Pay',
                                        $language_code ?? '',
                                    ) !!}
                                    {!! create_label('translations[page][APPLEPAY_CURRENCY_SYMBOL]', 'Currency Symbol', $language_code ?? '') !!}
                                    {!! create_label('translations[page][APPLE_SHARED_SECRET]', 'Apple Shared Secret', $language_code ?? '') !!}
                                    {!! create_label('translations[page][APPLE_ISSUER_ID]', 'Apple Issuer ID', $language_code ?? '') !!}
                                    {!! create_label('translations[page][APPLE_KEY_ID]', 'Apple Key ID', $language_code ?? '') !!}
                                    {!! create_label('translations[page][APPLE_BUNDLE_ID]', 'Apple Bundle ID', $language_code ?? '') !!}
                                    {!! create_label('translations[page][APPLE_API_KEY_FILE]', 'Apple API Key File', $language_code ?? '') !!}
                                    {!! create_label('translations[page][APPLE_ENVIRONMENT]', 'Apple Environment', $language_code ?? '') !!}
                                    {!! create_label('translations[page][STATUS]', 'Status', $language_code ?? '') !!}
                                    {!! create_label('translations[page][SAVE]', 'Save', $language_code ?? '') !!}
                                    {!! create_label('translations[page][WEBSTORIES]', 'Webstories', $language_code ?? '') !!}
                                    {!! create_label('translations[page][HOME]', 'Home', $language_code ?? '') !!}
                                    {!! create_label('translations[page][E_NEWSPAPERS_AND_MAGAZINES]', 'E-Newspapers', $language_code ?? '') !!}
                                    {!! create_label('translations[page][SELECT_NEWSLANGUAGE]', 'Select News Language', $language_code ?? '') !!}
                                    {!! create_label('translations[page][SELECT_NEWS_LANGUAGE]', 'Select News Language', $language_code ?? '') !!}
                                    {!! create_label('translations[page][SELECT_CHANNEL]', 'Select Channel', $language_code ?? '') !!}
                                    {!! create_label('translations[page][DATE]', 'Date', $language_code ?? '') !!}
                                    {!! create_label('translations[page][TYPE]', 'Type', $language_code ?? '') !!}
                                    {!! create_label('translations[page][NEWSPAPER]', 'Newspaper', $language_code ?? '') !!}
                                    {!! create_label('translations[page][MAGAZINE]', 'Magazine', $language_code ?? '') !!}
                                    {!! create_label('translations[page][PDF_FILE]', 'PDF File', $language_code ?? '') !!}
                                    {!! create_label('translations[page][THUMBNAIL]', 'Thumbnail', $language_code ?? '') !!}
                                    {!! create_label('translations[page][BACK]', 'Back', $language_code ?? '') !!}
                                    {!! create_label('translations[page][SAVE]', 'Save', $language_code ?? '') !!}

                                    {!! create_label(
                                        'translations[page][CUSTOM_ADVERTISING_SETTINGS]',
                                        'Custom Advertising Settings',
                                        $language_code ?? '',
                                    ) !!}
                                    {!! create_label('translations[page][HOME]', 'Home', $language_code ?? '') !!}
                                    {!! create_label('translations[page][SETTINGS]', 'Settings', $language_code ?? '') !!}
                                    {!! create_label(
                                        'translations[page][GLOBAL_CUSTOM_ADS_CONTROL]',
                                        'Global Custom Ads Control',
                                        $language_code ?? '',
                                    ) !!}
                                    {!! create_label('translations[page][MODEL_STATUS]', 'Model Status', $language_code ?? '') !!}
                                    {!! create_label('translations[page][WEB_PLACEMENT_POSITIONS]', 'Web Placement Positions', $language_code ?? '') !!}
                                    {!! create_label('translations[page][HEADER_PLACEMENT]', 'Header Placement', $language_code ?? '') !!}
                                    {!! create_label('translations[page][PRICE_PER_DAY]', 'Price Per Day', $language_code ?? '') !!}
                                    {!! create_label('translations[page][PLACEMENT_STATUS]', 'Placement Status', $language_code ?? '') !!}
                                    {!! create_label('translations[page][LEFT_SIDEBAR_PLACEMENT]', 'Left Sidebar Placement', $language_code ?? '') !!}
                                    {!! create_label('translations[page][RIGHT_SIDEBAR_PLACEMENT]', 'Right Sidebar Placement', $language_code ?? '') !!}
                                    {!! create_label('translations[page][FOOTER_PLACEMENT]', 'Footer Placement', $language_code ?? '') !!}
                                    {!! create_label('translations[page][WEB_PLACEMENT_POSITIONS]', 'Web Placement Positions', $language_code ?? '') !!}
                                    {!! create_label('translations[page][HEADER_PLACEMENT]', 'Header Placement', $language_code ?? '') !!}
                                    {!! create_label('translations[page][PRICE_PER_DAY]', 'Price Per Day', $language_code ?? '') !!}
                                    {!! create_label('translations[page][PLACEMENT_STATUS]', 'Placement Status', $language_code ?? '') !!}
                                    {!! create_label('translations[page][LEFT_SIDEBAR_PLACEMENT]', 'Left Sidebar Placement', $language_code ?? '') !!}
                                    {!! create_label('translations[page][BANNER_SLIDER_PLACEMENT]', 'Banner Slider Placement', $language_code ?? '') !!}

                                    {!! create_label(
                                        'translations[page][POST_DETAIL_PAGE_PLACEMENT]',
                                        'Post Detail Page Placement',
                                        $language_code ?? '',
                                    ) !!}
                                    {!! create_label('translations[page][LATEST_PLACEMENT]', 'Latest Placement', $language_code ?? '') !!}
                                    {!! create_label('translations[page][POPULAR_PLACEMENT]', 'Popular Placement', $language_code ?? '') !!}
                                    {!! create_label('translations[page][POSTS_PLACEMENT]', 'Posts Placement', $language_code ?? '') !!}
                                    {!! create_label('translations[page][TOPIC_POSTS_PLACEMENT]', 'Topic Posts Placement', $language_code ?? '') !!}
                                    {!! create_label('translations[page][VIDEOS_PLACEMENT]', 'Videos Placement', $language_code ?? '') !!}
                                    {!! create_label('translations[page][RIGHT_SIDEBAR_PLACEMENT]', 'Right Sidebar Placement', $language_code ?? '') !!}
                                    {!! create_label('translations[page][FOOTER_PLACEMENT]', 'Footer Placement', $language_code ?? '') !!}
                                    {!! create_label('translations[page][APP_PLACEMENT_POSITIONS]', 'App Placement Positions', $language_code ?? '') !!}
                                    {!! create_label('translations[page][SPLASH_SCREEN_PLACEMENT]', 'Splash Screen Placement', $language_code ?? '') !!}
                                    {!! create_label('translations[page][TOPICS_PAGE_PLACEMENT]', 'Topics Page Placement', $language_code ?? '') !!}
                                    {!! create_label(
                                        'translations[page][AFTER_WEATHER_SECTION_PLACEMENT]',
                                        'After Weather Section Placement',
                                        $language_code ?? '',
                                    ) !!}
                                    {!! create_label(
                                        'translations[page][ABOVE_RECOMMENDATIONS_SECTION_PLACEMENT]',
                                        'Above Recommendations Section Placement',
                                        $language_code ?? '',
                                    ) !!}
                                    {!! create_label('translations[page][APP_PLACEMENT_POSITIONS]', 'App Placement Positions', $language_code ?? '') !!}
                                    {!! create_label('translations[page][SPLASH_SCREEN_PLACEMENT]', 'Splash Screen Placement', $language_code ?? '') !!}
                                    {!! create_label('translations[page][TOPICS_PAGE_PLACEMENT]', 'Topics Page Placement', $language_code ?? '') !!}
                                    {!! create_label(
                                        'translations[page][AFTER_WEATHER_SECTION_PLACEMENT]',
                                        'After Weather Section Placement',
                                        $language_code ?? '',
                                    ) !!}
                                    {!! create_label(
                                        'translations[page][ABOVE_RECOMMENDATIONS_SECTION_PLACEMENT]',
                                        'Above Recommendations Section Placement',
                                        $language_code ?? '',
                                    ) !!}
                                    {!! create_label('translations[page][ALL_CHANNELS_PLACEMENT]', 'All Channels Placement', $language_code ?? '') !!}
                                    {!! create_label(
                                        'translations[page][splash_screen _AD_PLACEMENT]',
                                        'Search Page Floating Ad Placement',
                                        $language_code ?? '',
                                    ) !!}
                                    {!! create_label(
                                        'translations[page][CHANNELS_PAGE_FLOATING_AD_PLACEMENT]',
                                        'Channels Page Floating Ad Placement',
                                        $language_code ?? '',
                                    ) !!}
                                    {!! create_label(
                                        'translations[page][DISCOVER_PAGE_FLOATING_AD_PLACEMENT]',
                                        'Discover Page Floating Ad Placement',
                                        $language_code ?? '',
                                    ) !!}
                                    {!! create_label(
                                        'translations[page][VIDEO_PAGE_FLOATING_AD_PLACEMENT]',
                                        'Video Page Floating Ad Placement',
                                        $language_code ?? '',
                                    ) !!}
                                    {!! create_label(
                                        'translations[page][AFTER_READ_MORE_BUTTON_PLACEMENT]',
                                        'After Read More Button Placement',
                                        $language_code ?? '',
                                    ) !!}
                                    {!! create_label('translations[page][BANNER_SLIDER_PLACEMENT]', 'Banner Slider Placement', $language_code ?? '') !!}
                                    {!! create_label('translations[page][PRICE_PER_DAY]', 'Price Per Day', $language_code ?? '') !!}
                                    {!! create_label('translations[page][PLACEMENT_STATUS]', 'Placement Status', $language_code ?? '') !!}
                                    {!! create_label('translations[page][SAVE]', 'Save', $language_code ?? '') !!}
                                    {!! create_label(
                                        'translations[page][GLOBAL_CUSTOM_ADS_CONTROL]',
                                        'Global Custom Ads Control',
                                        $language_code ?? '',
                                    ) !!}
                                    {!! create_label('translations[page][SELECT_CURRENCY]', 'Select Currency', $language_code ?? '') !!}
                                    {!! create_label('translations[page][CURRENCY_SYMBOL]', 'Currency Symbol', $language_code ?? '') !!}
                                    {!! create_label('translations[page][MODEL_STATUS]', 'Model Status', $language_code ?? '') !!}
                                    {!! create_label('translations[page][PAYMENT_DEADLINE]', 'Payment Deadline', $language_code ?? '') !!}
                                    {!! create_label('translations[page][HOURS]', 'Hours', $language_code ?? '') !!}
                                    {!! create_label(
                                        'translations[page][PAYMENT_DEADLINE_NOTE]',
                                        'Specify the number of hours allowed before payment expires.',
                                        $language_code ?? '',
                                    ) !!}

                                    {!! create_label('translations[page][CUSTOM_ADS_REQUESTS]', 'Custom Ads Requests', $language_code ?? '') !!}
                                    {!! create_label('translations[page][AD_INFORMATION]', 'Ad Information', $language_code ?? '') !!}
                                    {!! create_label('translations[page][TOTAL_PRICE]', 'Total Price', $language_code ?? '') !!}
                                    {!! create_label('translations[page][PRICING_DETAILS]', 'Pricing Details', $language_code ?? '') !!}
                                    {!! create_label('translations[page][PRICE_SUMMARY]', 'Price Summary', $language_code ?? '') !!}
                                    {!! create_label('translations[page][AD_PLACEMENT]', 'Ad Placement', $language_code ?? '') !!}
                                    {!! create_label('translations[page][WEB_ADS_PLACEMENT]', 'Web Ads Placement', $language_code ?? '') !!}
                                    {!! create_label('translations[page][APP_ADS_PLACEMENT]', 'App Ads Placement', $language_code ?? '') !!}
                                    {!! create_label('translations[page][ANALYTICS_PAYMENT]', 'Analytics & Payment', $language_code ?? '') !!}
                                    {!! create_label('translations[page][CONTACT_INFORMATION]', 'Contact Information', $language_code ?? '') !!}
                                    {!! create_label('translations[page][DATE_INFORMATION]', 'Date Information', $language_code ?? '') !!}
                                    
                                    {!! create_label('translations[global][ID]', 'ID', $language_code ?? '') !!}
                                    {!! create_label('translations[global][USER]', 'User', $language_code ?? '') !!}
                                    {!! create_label('translations[global][TITLE]', 'Title', $language_code ?? '') !!}
                                    {!! create_label('translations[global][STATUS]', 'Status', $language_code ?? '') !!}
                                    {!! create_label('translations[global][ACTIONS]', 'Actions', $language_code ?? '') !!}
                                    {!! create_label('translations[global][START_DATE]', 'Start Date', $language_code ?? '') !!}
                                    {!! create_label('translations[global][END_DATE]', 'End Date', $language_code ?? '') !!}
                                    {!! create_label('translations[global][CREATED_AT]', 'Created At', $language_code ?? '') !!}
                                    {!! create_label('translations[global][UPDATED_AT]', 'Updated At', $language_code ?? '') !!}
                                    {!! create_label('translations[global][SUBMIT]', 'Submit', $language_code ?? '') !!}
                                    {!! create_label('translations[global][CANCEL]', 'Cancel', $language_code ?? '') !!}
                                    {!! create_label('translations[global][APPROVE]', 'Approve', $language_code ?? '') !!}
                                    {!! create_label('translations[global][REJECT]', 'Reject', $language_code ?? '') !!}
                                    {!! create_label('translations[global][ID]', 'ID', $language_code ?? '') !!}
                                    {!! create_label('translations[global][USER]', 'User', $language_code ?? '') !!}
                                    {!! create_label('translations[global][USER_ID]', 'User ID', $language_code ?? '') !!}
                                    {!! create_label('translations[global][TITLE]', 'Title', $language_code ?? '') !!}
                                    {!! create_label('translations[global][AD_TYPE]', 'Ad Type', $language_code ?? '') !!}
                                    {!! create_label('translations[global][VERTICAL_IMAGE]', 'Vertical Image', $language_code ?? '') !!}
                                    {!! create_label('translations[global][HORIZONTAL_IMAGE]', 'Horizontal Image', $language_code ?? '') !!}
                                    {!! create_label('translations[global][AD_PUBLISH_STATUS]', 'Ad Publish Status', $language_code ?? '') !!}
                                    {!! create_label('translations[global][PAYMENT_STATUS]', 'Payment Status', $language_code ?? '') !!}
                                    {!! create_label('translations[global][PRICING]', 'Pricing', $language_code ?? '') !!}
                                    {!! create_label('translations[global][CREATED_AT]', 'Created At', $language_code ?? '') !!}
                                    {!! create_label('translations[global][ACTION]', 'Action', $language_code ?? '') !!}
                                    {!! create_label('translations[global][PREVIEW]', 'Preview', $language_code ?? '') !!}
                                    {!! create_label('translations[global][SLUG]', 'Slug', $language_code ?? '') !!}
                                    {!! create_label('translations[global][URL]', 'URL', $language_code ?? '') !!}
                                    {!! create_label('translations[global][DAILY_PRICE]', 'Daily Price', $language_code ?? '') !!}
                                    {!! create_label('translations[global][TOTAL_DAYS]', 'Total Days', $language_code ?? '') !!}
                                    {!! create_label('translations[global][PRICE_SUMMARY]', 'Price Summary', $language_code ?? '') !!}
                                    {!! create_label('translations[global][WEB_ADS_PLACEMENT]', 'Web Ads Placement', $language_code ?? '') !!}
                                    {!! create_label('translations[global][APP_ADS_PLACEMENT]', 'App Ads Placement', $language_code ?? '') !!}
                                    {!! create_label('translations[global][TOTAL_CLICKS]', 'Total Clicks', $language_code ?? '') !!}
                                    {!! create_label('translations[global][VIEWS]', 'Views', $language_code ?? '') !!}
                                    {!! create_label('translations[global][PAYMENT_GATEWAY]', 'Payment Gateway', $language_code ?? '') !!}
                                    {!! create_label('translations[global][TRANSACTION_ID]', 'Transaction ID', $language_code ?? '') !!}
                                    {!! create_label('translations[global][CONTACT_NAME]', 'Contact Name', $language_code ?? '') !!}
                                    {!! create_label('translations[global][CONTACT_EMAIL]', 'Contact Email', $language_code ?? '') !!}
                                    {!! create_label('translations[global][CONTACT_PHONE]', 'Contact Phone', $language_code ?? '') !!}
                                    {!! create_label('translations[global][START_DATE]', 'Start Date', $language_code ?? '') !!}
                                    {!! create_label('translations[global][END_DATE]', 'End Date', $language_code ?? '') !!}
                                    {!! create_label('translations[global][UPDATED_AT]', 'Updated At', $language_code ?? '') !!}
                                    {!! create_label('translations[global][CLOSE]', 'Close', $language_code ?? '') !!}
                                    {!! create_label('translations[page][CUSTOM_ADS]', 'Custom Ads', $language_code ?? '') !!}
                                    {!! create_label('translations[page][HOME]', 'Home', $language_code ?? '') !!}
                                    {!! create_label('translations[global][ID]', 'ID', $language_code ?? '') !!}
                                    {!! create_label('translations[global][USER]', 'User', $language_code ?? '') !!}
                                    {!! create_label('translations[global][TITLE]', 'Title', $language_code ?? '') !!}
                                    {!! create_label('translations[global][AD_TYPE]', 'Ad Type', $language_code ?? '') !!}
                                    {!! create_label('translations[global][VERTICAL_IMAGE]', 'Vertical Image', $language_code ?? '') !!}
                                    {!! create_label('translations[global][HORIZONTAL_IMAGE]', 'Horizontal Image', $language_code ?? '') !!}
                                    {!! create_label('translations[global][AD_PUBLISH_STATUS]', 'Ad Publish Status', $language_code ?? '') !!}
                                    {!! create_label('translations[global][PAYMENT_STATUS]', 'Payment Status', $language_code ?? '') !!}
                                    {!! create_label('translations[global][PRICING]', 'Pricing', $language_code ?? '') !!}
                                    {!! create_label('translations[global][CREATED_AT]', 'Created At', $language_code ?? '') !!}
                                    {!! create_label('translations[global][ACTION]', 'Action', $language_code ?? '') !!}
                                    {!! create_label('translations[global][PREVIEW]', 'Preview', $language_code ?? '') !!}
                                    {!! create_label('translations[global][USER_ID]', 'User ID', $language_code ?? '') !!}
                                    {!! create_label('translations[global][SLUG]', 'Slug', $language_code ?? '') !!}
                                    {!! create_label('translations[global][URL]', 'URL', $language_code ?? '') !!}
                                    {!! create_label('translations[global][DAILY_PRICE]', 'Daily Price', $language_code ?? '') !!}
                                    {!! create_label('translations[global][TOTAL_DAYS]', 'Total Days', $language_code ?? '') !!}
                                    {!! create_label('translations[global][PRICE_SUMMARY]', 'Price Summary', $language_code ?? '') !!}
                                    {!! create_label('translations[global][WEB_ADS_PLACEMENT]', 'Web Ads Placement', $language_code ?? '') !!}
                                    {!! create_label('translations[global][APP_ADS_PLACEMENT]', 'App Ads Placement', $language_code ?? '') !!}
                                    {!! create_label('translations[global][TOTAL_CLICKS]', 'Total Clicks', $language_code ?? '') !!}
                                    {!! create_label('translations[global][VIEWS]', 'Views', $language_code ?? '') !!}
                                    {!! create_label('translations[global][PAYMENT_GATEWAY]', 'Payment Gateway', $language_code ?? '') !!}
                                    {!! create_label('translations[global][TRANSACTION_ID]', 'Transaction ID', $language_code ?? '') !!}
                                    {!! create_label('translations[global][CONTACT_NAME]', 'Contact Name', $language_code ?? '') !!}
                                    {!! create_label('translations[global][CONTACT_EMAIL]', 'Contact Email', $language_code ?? '') !!}
                                    {!! create_label('translations[global][CONTACT_PHONE]', 'Contact Phone', $language_code ?? '') !!}
                                    {!! create_label('translations[global][START_DATE]', 'Start Date', $language_code ?? '') !!}
                                    {!! create_label('translations[global][END_DATE]', 'End Date', $language_code ?? '') !!}
                                    {!! create_label('translations[global][UPDATED_AT]', 'Updated At', $language_code ?? '') !!}
                                    {!! create_label('translations[global][CLOSE]', 'Close', $language_code ?? '') !!}
                                    {!! create_label('translations[global][ROLE]', 'Role', $language_code ?? '') !!}
                                    {!! create_label(
                                        'translations[message][E_NEWSPAPER_CREATED_SUCCESSFULLY]',
                                        'E-Newspaper created successfully.',
                                        $language_code ?? '',
                                    ) !!}
                                    {!! create_label(
                                        'translations[message][MAGAZINE_CREATED_SUCCESSFULLY]',
                                        'Magazine created successfully.',
                                        $language_code ?? '',
                                    ) !!}
                                    {!! create_label(
                                        'translations[message][E_NEWSPAPER_DELETED_SUCCESSFULLY]',
                                        'E-Newspaper deleted successfully.',
                                        $language_code ?? '',
                                    ) !!}
                                    {!! create_label(
                                        'translations[message][E_NEWSPAPER_UPDATED_SUCCESSFULLY]',
                                        'E-Newspaper updated successfully.',
                                        $language_code ?? '',
                                    ) !!}
                                    {!! create_label('translations[page][VIEW_CURRENT_PDF]', 'View Current PDF', $language_code ?? '') !!}
                                    {!! create_label('translations[page][UPDATE]', 'Update', $language_code ?? '') !!}
                                    {!! create_label('translations[page][FOR_POSTS]', 'For Posts', $language_code ?? '') !!}
                                    {!! create_label('translations[page][FOR_SPONSOR_ADS]', 'For Sponsor Ads', $language_code ?? '') !!}
                                    {!! create_label(
                                        'translations[page][SPONSOR_EMAIL_TEMPLATES_DETAILS]',
                                        'Sponsor Email Templates Details',
                                        $language_code ?? '',
                                    ) !!}
                                    {!! create_label('translations[global][SUBJECT]', 'Subject', $language_code ?? '') !!}
                                    {!! create_label('translations[global][TYPE]', 'Type', $language_code ?? '') !!}
                                    {!! create_label('translations[page][LAYOUT_WIDTH]', 'Layout Width', $language_code ?? '') !!}
                                    {!! create_label('translations[global][CLOSING]', 'Closing', $language_code ?? '') !!}
                                    {!! create_label('translations[global][SIGNATURE]', 'Signature', $language_code ?? '') !!}
                                    {!! create_label('translations[global][FOOTER_TEXT]', 'Footer Text', $language_code ?? '') !!}
                                    {!! create_label('translations[page][CREDIT_PACKS]', 'Credit Packs', $language_code ?? '') !!}
                                    {!! create_label('translations[page][APPROVAL_COUNT]', 'Approval Count', $language_code ?? '') !!}
                                    {!! create_label('translations[page][MINUTES]', 'Minutes', $language_code ?? '') !!}
                                    {!! create_label(
                                        'translations[page][CATEGORY_NEWS_PAGE_PLACEMENT]',
                                        'Category News Page Placement',
                                        $language_code ?? '',
                                    ) !!}
                                    {!! create_label(
                                        'translations[page][SPLASH_SCREEN_AD_PLACEMENT]',
                                        'Splash Screen Ad Placement',
                                        $language_code ?? '',
                                    ) !!}
                                    {!! create_label('translations[page][CREDIT_PACKS]', 'Credit Packs', $language_code ?? '') !!}
                                    {!! create_label('translations[global][NAME]', 'Name', $language_code ?? '') !!}
                                    {!! create_label('translations[global][PRODUCT_ID]', 'Product ID', $language_code ?? '') !!}
                                    {!! create_label('translations[global][CREDITS]', 'Credits', $language_code ?? '') !!}
                                    {!! create_label('translations[global][PRICE]', 'Price', $language_code ?? '') !!}
                                    {!! create_label('translations[global][SAVINGS_PERCENT]', 'Savings Percent', $language_code ?? '') !!}
                                    {!! create_label('translations[global][TAGLINE]', 'Tagline', $language_code ?? '') !!}
                                    {!! create_label('translations[global][IS_POPULAR]', 'Is Popular', $language_code ?? '') !!}
                                    {!! create_label('translations[global][IS_BEST_VALUE]', 'Is Best Value', $language_code ?? '') !!}
                                    {!! create_label('translations[global][SUBMIT]', 'Submit', $language_code ?? '') !!}
                                    {!! create_label('translations[global][ACTION]', 'Action', $language_code ?? '') !!}
                                    {!! create_label('translations[page][SMTP_MAIL_CONFIGURATION]', 'SMTP Mail Configuration', $language_code ?? '') !!}
                                    {!! create_label('translations[page][MAIL_MAILER]', 'Mail Mailer', $language_code ?? '') !!}
                                    {!! create_label('translations[page][ENTER_MAIL_MAILER]', 'Enter Mail Mailer', $language_code ?? '') !!}
                                    {!! create_label('translations[page][MAIL_HOST]', 'Mail Host', $language_code ?? '') !!}
                                    {!! create_label('translations[page][ENTER_MAIL_HOST]', 'Enter Mail Host', $language_code ?? '') !!}
                                    {!! create_label('translations[page][MAIL_PORT]', 'Mail Port', $language_code ?? '') !!}
                                    {!! create_label('translations[page][ENTER_MAIL_PORT]', 'Enter Mail Port', $language_code ?? '') !!}
                                    {!! create_label('translations[page][MAIL_USERNAME]', 'Mail Username', $language_code ?? '') !!}
                                    {!! create_label('translations[page][ENTER_MAIL_USERNAME]', 'Enter Mail Username', $language_code ?? '') !!}
                                    {!! create_label('translations[page][MAIL_PASSWORD]', 'Mail Password', $language_code ?? '') !!}
                                    {!! create_label('translations[page][ENTER_MAIL_PASSWORD]', 'Enter Mail Password', $language_code ?? '') !!}
                                    {!! create_label('translations[page][MAIL_ENCRYPTION]', 'Mail Encryption', $language_code ?? '') !!}
                                    {!! create_label('translations[page][ENTER_MAIL_ENCRYPTION]', 'Enter Mail Encryption', $language_code ?? '') !!}
                                    {!! create_label('translations[page][MAIL_FROM_ADDRESS]', 'Mail From Address', $language_code ?? '') !!}
                                    {!! create_label('translations[page][ENTER_MAIL_FROM_ADDRESS]', 'Enter Mail From Address', $language_code ?? '') !!}
                                    {!! create_label('translations[page][MAIL_FROM_NAME]', 'Mail From Name', $language_code ?? '') !!}
                                    {!! create_label('translations[page][ENTER_MAIL_FROM_NAME]', 'Enter Mail From Name', $language_code ?? '') !!}
                                    {!! create_label('translations[page][SAVE]', 'Save', $language_code ?? '') !!}
                                    {!! create_label('translations[message][ACTIVE_USERS_CHART]', 'Active Users Chart', $language_code ?? '') !!}
                                    {!! create_label(
                                        'translations[page][NEWS_LANGUAGE_ACCESS_BLOCKED_ON_ENEWSPAPER]',
                                        'News languages access is currently not permitted. Please activate the toggle switch in system settings and create e-newspaper.',
                                        $language_code ?? '',
                                    ) !!}
                                    {!! create_label(
                                        'translations[message][NO_PERMISSION_TOPIC]',
                                        'You do not have permission to access this topic.',
                                        $language_code ?? '',
                                    ) !!}
                                    {!! create_label(
                                        'translations[message][NO_PERMISSION_CHANNEL]',
                                        'You do not have permission to access this channel.',
                                        $language_code ?? '',
                                    ) !!}
                                    {!! create_label(
                                        'translations[message][NO_PERMISSION_NEWSLANGUAGE]',
                                        'You do not have permission to access this news language.',
                                        $language_code ?? '',
                                    ) !!}
                                    {!! create_label(
                                        'translations[message][NO_PERMISSION_IMAGE_NOTIFICATION]',
                                        'You do not have permission to access image notifications.',
                                        $language_code ?? '',
                                    ) !!}
                                    {!! create_label(
                                        'translations[page][SELECT_NEWSLANGUAGE_FIRST]',
                                        'Page: Select News Language First',
                                        $language_code ?? '',
                                    ) !!}
                                    {!! create_label('translations[page][AUDIOS]', 'Audio', $language_code ?? '') !!}
                                    {!! create_label('translations[page][AUDIO]', 'Audio', $language_code ?? '') !!}
                                    {!! create_label('translations[message][CREATE_AUDIOS]', 'Create Audios', $language_code ?? '') !!}
                                    {!! create_label('translations[message][UPDATE_AUDIOS]', 'Update Audios', $language_code ?? '') !!}
                                    {!! create_label('translations[page][CREATE_REPORT_TYPES]', 'Create Report Types', $language_code ?? '') !!}
                                    {!! create_label('translations[message][ADD_REPORT_TYPE]', 'Add Report Type', $language_code ?? '') !!}
                                    {!! create_label('translations[message][REPORT_TYPES]', 'Report Types', $language_code ?? '') !!}
                                    {!! create_label('translations[message][REPORT_TYPE]', 'Report Type', $language_code ?? '') !!}
                                    {!! create_label('translations[message][ENTER_REPORT_TYPE]', 'Enter Report Type', $language_code ?? '') !!}
                                    {!! create_label('translations[message][SAVE]', 'Save', $language_code ?? '') !!}
                                    {!! create_label('translations[global][ID]', 'ID', $language_code ?? '') !!}
                                    {!! create_label('translations[global][TITLE]', 'Title', $language_code ?? '') !!}
                                    {!! create_label('translations[global][CREATED_AT]', 'Created At', $language_code ?? '') !!}
                                    {!! create_label('translations[global][UPDATED_AT]', 'Updated At', $language_code ?? '') !!}
                                    {!! create_label('translations[global][ACTION]', 'Action', $language_code ?? '') !!}
                                    {!! create_label('translations[page][AUDIO_FILE]', 'Audio File', $language_code ?? '') !!}
                                    {!! create_label('translations[page][EXTRA_IMAGES]', 'Extra Images', $language_code ?? '') !!}

                                    {!! create_label('translations[page][ad_information]', 'Ad Information', $language_code ?? '') !!}
                                    {!! create_label('translations[page][sample_ad_title]', 'Sample Ad Title', $language_code ?? '') !!}
                                    {!! create_label('translations[page][ad_description_placeholder]', 'Ad Description Placeholder', $language_code ?? '') !!}
                                    {!! create_label('translations[page][total_price]', 'Total Price', $language_code ?? '') !!}
                                    {!! create_label('translations[page][no_image_available]', 'No Image Available', $language_code ?? '') !!}
                                    {!! create_label('translations[page][pricing_details]', 'Pricing Details', $language_code ?? '') !!}
                                    {!! create_label('translations[page][ad_placement]', 'Ad Placement', $language_code ?? '') !!}
                                    {!! create_label('translations[page][analytics_payment]', 'Analytics & Payment', $language_code ?? '') !!}
                                    {!! create_label('translations[page][contact_information]', 'Contact Information', $language_code ?? '') !!}
                                    {!! create_label('translations[page][date_information]', 'Date Information', $language_code ?? '') !!}
                                    {!! create_label('translations[page][no_permission_custom_ads]', 'No Permission for Custom Ads', $language_code ?? '') !!}
                                    {!! create_label('translations[page][MEMBERSHIP_PLANS_DISPLAY_NOTE]', 'Membership Plans Display Note', $language_code ?? '') !!}
                                    {!! create_label('translations[page][FREE_TRIAL_MODE_NOTE]', 'When a user needs a completely free membership, enable free trial mode. All values will be set to -1, indicating no limits for the user, including Free Trial Post Limit, Free Trial Story Limit, and Free Trial E-Paper and Magazines Limit.', $language_code ?? '') !!}
                                    
                                    {!! create_label('translations[message][GOOGLE_ADSENSE_ANALYTICS_CHART]', 'Google AdSense Analytics Chart', $language_code ?? '') !!}
                                    {!! create_label('translations[message][TOTAL_IMPRESSIONS]', 'Total Impressions', $language_code ?? '') !!}
                                    {!! create_label('translations[message][TOTAL_CLICKS]', 'Total Clicks', $language_code ?? '') !!}
                                    {!! create_label('translations[message][TOTAL_EARNINGS]', 'Total Earnings', $language_code ?? '') !!}
                                    {!! create_label('translations[message][AVERAGE_CTR]', 'Average CTR', $language_code ?? '') !!}
                                    {!! create_label('translations[message][CLICK_THROUGH_RATE]', 'Click-Through Rate', $language_code ?? '') !!}
                                    {!! create_label('translations[page][IMAGE_POSTS]', 'Image Posts', $language_code ?? '') !!}
                                    {!! create_label('translations[page][VIDEO_POSTS]', 'Video Posts', $language_code ?? '') !!}
                                    {!! create_label('translations[page][AUDIO_POSTS]', 'Audio Posts', $language_code ?? '') !!}
                                    {!! create_label('translations[page][WEB_STORIES]', 'Web Stories', $language_code ?? '') !!}
                                    {!! create_label('translations[page][SPONSORED_ADS]', 'Sponsored Ads', $language_code ?? '') !!}
                                    {!! create_label('translations[page][POST_EMAILS]', 'Post Emails', $language_code ?? '') !!}
                                    {!! create_label('translations[page][SPONSOR_ADS_EMAILS]', 'Sponsor Ads Emails', $language_code ?? '') !!}
                                    {!! create_label('translations[message][VIEW_COMMENTS]', 'View Comments', $language_code ?? '') !!}
                                    {!! create_label('translations[message][SEND_NOTIFICATION]', 'Send Notification', $language_code ?? '') !!}
                                    {!! create_label('translations[message][READ_MORE]', 'To Read More', $language_code ?? '') !!}
                                    {!! create_label('translations[message][CLICK_HERE]', 'Click Here', $language_code ?? '') !!}
                                    {!! create_label('translations[page][UPDATE_PLAN]', 'Update Plan', $language_code ?? '') !!}
                                    {!! create_label('translations[page][GENERAL_SETTINGS]', 'General Settings', $language_code ?? '') !!}
                                    {!! create_label('translations[page][SEO_TITLE]', 'SEO Title', $language_code ?? '') !!}
                                    {!! create_label('translations[page][META_DESCRIPTION]', 'Meta Description', $language_code ?? '') !!}
                                    {!! create_label('translations[page][META_KEYWORDS]', 'Meta Keywords', $language_code ?? '') !!}
                                    {!! create_label('translations[page][LIGHT_LOGO_SIZE]', 'Light Logo Size', $language_code ?? '') !!}
                                    {!! create_label('translations[page][DARK_LOGO_SIZE]', 'Dark Logo Size', $language_code ?? '') !!}
                                    {!! create_label('translations[page][SPONSOR_AD_ROTATION_TIME]', 'Sponsor Ad Rotation Time', $language_code ?? '') !!}
                                    {!! create_label('translations[page][CUSTOM_AD_FEATURE]', 'Custom Ad Feature', $language_code ?? '') !!}
                                    {!! create_label('translations[page][GOOGLE_ADSENSE_CONFIGURATION]', 'Google AdSense Configuration', $language_code ?? '') !!}
                                    {!! create_label('translations[page][SETTINGS]', 'Settings', $language_code ?? '') !!}
                                    {!! create_label('translations[page][ADSENSE_CLIENT_ID]', 'AdSense Client ID', $language_code ?? '') !!}
                                    {!! create_label('translations[page][ADSENSE_CLIENT_SECRET]', 'AdSense Client Secret', $language_code ?? '') !!}
                                    {!! create_label('translations[page][ADSENSE_REDIRECT_URI]', 'AdSense Redirect URI', $language_code ?? '') !!}
                                    {!! create_label('translations[page][SAVE_CONFIGURATION]', 'Save Configuration', $language_code ?? '') !!}

                                    {!! create_label('translations[page][CREATE_REPORTED_COMMENT]', 'Create Reported Comment Type', $language_code ?? '') !!}

                                    {!! create_label('translations[page][CHANNEL_NOT_AVAILABLE_FOR_NEWS_LANGUAGE]', 'For this news language, channel is not available. Please create a channel or select another news language.', $language_code ?? '') !!}
                                    {!! create_label('translations[page][TOPIC_NOT_AVAILABLE_FOR_NEWS_LANGUAGE]', 'For this news language, topic is not available. Please create a topic or select another news language.', $language_code ?? '') !!}
                                    {!! create_label('translations[page][CHANNEL_AND_TOPIC_NOT_AVAILABLE_FOR_NEWS_LANGUAGE]', 'Channel and topic are not available for the selected news language. Please create them or select another news language.', $language_code ?? '') !!}
                                   
                                    {!! create_label('translations[page][WEATHER_CARD_MODE]', 'Enable or Disable Weather Card Popup.', $language_code ?? '') !!}
                                    {!! create_label('translations[page][COOKIES_POPUP_MODE]', 'Enable or Disable GDPR Card Popup.', $language_code ?? '') !!}

                                    {!! create_label('translations[page][NOTIFICATION_SETTINGS]', 'Notification Settings', $language_code ?? '') !!}
                                    {!! create_label('translations[page][NOTIFICATION_DETAILS ]', 'Notification Details', $language_code ?? '') !!}
                                    {!! create_label('translations[page][AUTOMATIC_NOTIFICATIONS]', 'Automatic Notifications', $language_code ?? '') !!}
                                    {!! create_label('translations[page][DAILY_NOTIFICATION_LIMIT]', 'Daily Notification Limit', $language_code ?? '') !!}
                                    {!! create_label('translations[page][MAX_NOTIFICATIONS_SENT_PER_DAY]', 'Max Notifications Sent Per Day', $language_code ?? '') !!}
                                    {!! create_label('translations[page][ENABLE_DISABLE_AUTOMATIC_NOTIFICATIONS]', 'Enable / Disable Automatic Notifications', $language_code ?? '') !!}

                                </div>
                                <div class="row mt-3">
                                    <div class="col-12">
                                        <button type="submit" class="btn btn-primary">{{ __('page.SAVE') }}</button>
                                    </div>
                                </div>
                            </form>
                        </div>

                        <!-- Frontend Tab -->
                        <div class="tab-pane fade" id="frontend" role="tabpanel" aria-labelledby="frontend-tab">
                            <form action="{{ route('language.store') }}" method="POST" id="frontendLabels"
                                enctype="multipart/form-data">
                                @csrf

                                <input type="hidden" name="language_id" value="{{ $selected_language_id }}">
                                <input type="hidden" name="tab_type" value="frontend">

                                <div class="card p-3 mb-3 m-1">
                                    <h4 class="m-2">{{ __('Frontend Translations') }}</h4>
                                </div>

                                <div class="row border p-2 m-1 rounded">
                                    {!! create_label('translations[frontend-labels][aboutus][title]', 'About Us', $language_code ?? '') !!}
                                    {!! create_label('translations[frontend-labels][newsaudios][title]', 'News Audios', $language_code ?? '') !!}
                                    {!! create_label('translations[frontend-labels][newsaudios][explore_more_audio_content]', 'Explore More Audio Content', $language_code ?? '') !!}
                                    {!! create_label('translations[frontend-labels][newsaudios][more_audio]', 'More Audio', $language_code ?? '') !!}

                                    {!! create_label('translations[frontend-labels][contactus][title]', 'Contact Us', $language_code ?? '') !!}
                                    {!! create_label('translations[frontend-labels][contactus][thank_you_message]', 'Thank you for contacting us. We will get back to you soon.', $language_code ?? '') !!}

                                    {!! create_label('translations[frontend-labels][enewspapers][title]', 'E-Newspapers', $language_code ?? '') !!}
                                    {!! create_label('translations[frontend-labels][magazines][title]', 'Magazines', $language_code ?? '') !!}
                                    {!! create_label('translations[frontend-labels][privacy_policy][title]', 'Privacy Policy', $language_code ?? '') !!}
                                    {!! create_label('translations[frontend-labels][terms_and_conditions][title]', 'Terms and Conditions', $language_code ?? '') !!}
                                    {!! create_label('translations[frontend-labels][myaccount][title]', 'My Account', $language_code ?? '') !!}
                                    {!! create_label('translations[frontend-labels][followings][title]', 'Followings', $language_code ?? '') !!}
                                    {!! create_label('translations[frontend-labels][favorite][title]', 'Bookmarks', $language_code ?? '') !!}
                                    {!! create_label('translations[frontend-labels][mysubscription][title]', 'My Subscription', $language_code ?? '') !!}
                                    {!! create_label('translations[frontend-labels][transaction_details][title]', 'Transaction Details', $language_code ?? '') !!}
                                    {!! create_label('translations[frontend-labels][home][title]', 'Home', $language_code ?? '') !!}
                                    {!! create_label('translations[frontend-labels][home][read_all]', 'Read All', $language_code ?? '') !!}
                                    {!! create_label('translations[frontend-labels][home][most_read]', 'Most Read', $language_code ?? '') !!}
                                    {!! create_label('translations[frontend-labels][home][from_the_channels_you_may_followed]', 'From the Channels You May Have Followed', $language_code ?? '') !!}
                                    {!! create_label('translations[frontend-labels][home][latest_news_videos]', 'Latest News Videos', $language_code ?? '') !!}
                                    {!! create_label('translations[frontend-labels][home][prev]', 'Prev', $language_code ?? '') !!}
                                    {!! create_label('translations[frontend-labels][home][next]', 'Next', $language_code ?? '') !!}
                                    {!! create_label('translations[frontend-labels][home][latest]', 'Latest', $language_code ?? '') !!}
                                    {!! create_label('translations[frontend-labels][home][read_more]', 'Read More', $language_code ?? '') !!}
                                    {!! create_label('translations[frontend-labels][home][popular_now]', 'Popular Now', $language_code ?? '') !!}
                                    {!! create_label('translations[frontend-labels][home][email_required]', 'Email field is required.', $language_code ?? '') !!}
                                    {!! create_label('translations[frontend-labels][home][email_taken]', 'This email is already registered.', $language_code ?? '') !!}
                                    {!! create_label('translations[frontend-labels][home][email_invalid]', 'Please enter a valid email address.', $language_code ?? '') !!}
                                    {!! create_label('translations[frontend-labels][home][email_subscribed]', 'Subscribed', $language_code ?? '') !!}
                                    {!! create_label('translations[frontend-labels][home][your_email_address]', 'Your Email Address', $language_code ?? '') !!}
                                    {!! create_label('translations[frontend-labels][home][subscribe]', 'Subscribe', $language_code ?? '') !!}
                                    {!! create_label('translations[frontend-labels][home][dont_worry_we_dont_spam]', "Don't worry, we don't spam", $language_code ?? '') !!}
                                    {!! create_label('translations[frontend-labels][home][success_message]', 'Subscriber added successfully', $language_code ?? '') !!}
                                    {!! create_label('translations[frontend-labels][home][location]', 'Long Island City', $language_code ?? '') !!}
                                    {!! create_label('translations[frontend-labels][home][temperature]', '-0.79°C', $language_code ?? '') !!}
                                    {!! create_label('translations[frontend-labels][home][stormy]', 'Stormy', $language_code ?? '') !!}
                                    {!! create_label('translations[frontend-labels][home][wind_speed]', '4.12 km/h', $language_code ?? '') !!}
                                    {!! create_label('translations[frontend-labels][home][humidity]', '60%', $language_code ?? '') !!}
                                    {!! create_label('translations[frontend-labels][home][precipitation]', '0.2h', $language_code ?? '') !!}
                                    {!! create_label('translations[frontend-labels][home][all]', 'all', $language_code ?? '') !!}
                                    {!! create_label('translations[frontend-labels][home][sponsored]', 'Sponsored', $language_code ?? '') !!}
                                    {!! create_label('translations[frontend-labels][home][quick_links]', 'Quick Links', $language_code ?? '') !!}
                                    {!! create_label('translations[frontend-labels][home][top_posts]', 'Top Posts', $language_code ?? '') !!}


                                    {!! create_label('translations[frontend-labels][membership][title]', 'Membership', $language_code ?? '') !!}
                                    {!! create_label('translations[frontend-labels][membership][month]', 'Month', $language_code ?? '') !!}
                                    {!! create_label('translations[frontend-labels][membership][ads-free]', 'Ads Free', $language_code ?? '') !!}
                                    {!! create_label('translations[frontend-labels][membership][articles]', 'Articles', $language_code ?? '') !!}
                                    {!! create_label('translations[frontend-labels][membership][stories]', 'Stories', $language_code ?? '') !!}
                                    {!! create_label('translations[frontend-labels][membership][e-paper-magazines]', 'E-Paper & Magazines', $language_code ?? '') !!}
                                    {!! create_label('translations[frontend-labels][membership][buy-now]', 'Buy Now', $language_code ?? '') !!}

                                    {!! create_label('translations[frontend-labels][payment_gateway][title]', 'Payment Gateway', $language_code ?? '') !!}
                                    {!! create_label('translations[frontend-labels][payment_gateway][you_are_almost_there]', 'You are almost there', $language_code ?? '') !!}
                                    {!! create_label('translations[frontend-labels][payment_gateway][plan]', 'Plan', $language_code ?? '') !!}
                                    {!! create_label('translations[frontend-labels][payment_gateway][smart_ad_id]', 'Smart Ad ID', $language_code ?? '') !!}
                                    {!! create_label('translations[frontend-labels][payment_gateway][smart_detail_id]', 'Smart Detail ID', $language_code ?? '') !!}
                                    {!! create_label('translations[frontend-labels][payment_gateway][amount]', 'Amount', $language_code ?? '') !!}
                                    {!! create_label('translations[frontend-labels][payment_gateway][tenure_id]', 'Tenure', $language_code ?? '') !!}
                                    {!! create_label('translations[frontend-labels][payment_gateway][pay_with_stripe]', 'Pay with Stripe', $language_code ?? '') !!}
                                    {!! create_label('translations[frontend-labels][payment_gateway][pay_with_razorpay]', 'Pay with Razorpay', $language_code ?? '') !!}
                                    {!! create_label('translations[frontend-labels][payment_cancel][payment_not_completed]', 'Your payment was not completed. Please try again.', $language_code ?? '') !!}
                                    {!! create_label('translations[frontend-labels][payment_cancel][return_membership]', 'Return to Membership', $language_code ?? '') !!}
                                    {!! create_label('translations[frontend-labels][payment_cancel][return_home]', 'Return to Home', $language_code ?? '') !!}
                                    
                                    {!! create_label('translations[frontend-labels][payment_with_razorpay][title]', 'Payment with Razorpay', $language_code ?? '') !!}
                                    {!! create_label('translations[frontend-labels][payment_with_razorpay][processing_payment]', 'Processing Payment', $language_code ?? '') !!}
                                    {!! create_label('translations[frontend-labels][payment_with_razorpay][plan]', 'Plan', $language_code ?? '') !!}
                                    {!! create_label('translations[frontend-labels][payment_with_razorpay][amount]', 'Amount', $language_code ?? '') !!}
                                    {!! create_label('translations[frontend-labels][payment_with_razorpay][processing_razorpay_payment]', 'Processing Razorpay Payment...', $language_code ?? '') !!}
                                    {!! create_label('translations[frontend-labels][payment_with_razorpay][smart_ad_id]', 'Smart Ad Id', $language_code ?? '') !!}
                                    {!! create_label('translations[frontend-labels][payment_with_razorpay][ad_details_id]', 'Ad Details Id', $language_code ?? '') !!}

                                    {!! create_label('translations[frontend-labels][payment_success][title]', 'Payment Success', $language_code ?? '') !!}
                                    {!! create_label('translations[frontend-labels][payment_success][thank_you]', 'Thank You', $language_code ?? '') !!}
                                    {!! create_label('translations[frontend-labels][payment_success][success_message]', 'Your payment has been successfully completed.', $language_code ?? '') !!}
                                    {!! create_label('translations[frontend-labels][payment_success][amount]', 'Amount', $language_code ?? '') !!}
                                    {!! create_label('translations[frontend-labels][payment_success][payment_method]', 'Payment Method', $language_code ?? '') !!}
                                    {!! create_label('translations[frontend-labels][payment_success][transaction_id]', 'Transaction ID', $language_code ?? '') !!}
                                    {!! create_label('translations[frontend-labels][payment_success][date]', 'Date', $language_code ?? '') !!}
                                    {!! create_label('translations[frontend-labels][payment_success][status]', 'Status', $language_code ?? '') !!}
                                    {!! create_label('translations[frontend-labels][payment_success][view_subscription]', 'View Subscription', $language_code ?? '') !!}
                                    {!! create_label('translations[frontend-labels][payment][smart_ad_id]', 'Smart Ad Id', $language_code ?? '') !!}

                                    {!! create_label('translations[frontend-labels][payment_cancel][title]', 'Payment Cancelled', $language_code ?? '') !!}
                                    {!! create_label('translations[frontend-labels][payment_cancel][payment_not_completed]', 'Your payment was not completed. Please try again.', $language_code ?? '') !!}
                                    {!! create_label('translations[frontend-labels][payment_cancel][go_to_membership]', 'Go to Membership', $language_code ?? '') !!}
                                    {!! create_label('translations[frontend-labels][payment_cancel][return_home]', 'Return to Home', $language_code ?? '') !!}

                                    {!! create_label('translations[frontend-labels][channels][title]', 'Channels', $language_code ?? '') !!}
                                    {!! create_label('translations[frontend-labels][channels][subscribed_success]', 'You have successfully subscribed to this channel', $language_code ?? '') !!}
                                    {!! create_label('translations[frontend-labels][channels][unsubscribed_success]', 'You have successfully unsubscribed from this channel', $language_code ?? '') !!}

                                    {!! create_label('translations[frontend-labels][sponsor_ads][title]', 'Sponsor Ads', $language_code ?? '') !!}   
                                    {!! create_label('translations[frontend-labels][sponsor_ads][dashboard]', 'Dashboard', $language_code ?? '') !!}
                                    {!! create_label('translations[frontend-labels][sponsor_ads][launch_campaigns]', 'Launch Your Sponsor Ad Campaigns', $language_code ?? '') !!}
                                    {!! create_label('translations[frontend-labels][sponsor_ads][launch_campaigns_description]', 'Promote your brand through our self-serve ad platform. Submit your creatives, select placements on web and app, and track performance in real time. Whether you’re targeting splash screens, sidebars, or content areas, we\'ve got you covered.', $language_code ?? '') !!}
                                    {!! create_label('translations[frontend-labels][sponsor_ads][manage_my_ads]', 'Manage My Ads', $language_code ?? '') !!}
                                    {!! create_label('translations[frontend-labels][sponsor_ads][request_approved]', 'Your request was approved. You will receive further instructions via email.', $language_code ?? '') !!}
                                    {!! create_label('translations[frontend-labels][sponsor_ads][make_payment]', 'Make Payment', $language_code ?? '') !!}
                                    {!! create_label('translations[frontend-labels][sponsor_ads][payment_expires_in]', 'Payment expires in', $language_code ?? '') !!}
                                    {!! create_label('translations[frontend-labels][sponsor_ads][payment_deadline_expired]', 'Payment deadline expired', $language_code ?? '') !!}
                                    {!! create_label('translations[frontend-labels][sponsor_ads][request_submitted_on]', 'Your request was submitted on', $language_code ?? '') !!}
                                    {!! create_label('translations[frontend-labels][sponsor_ads][status_update_message]', 'The status will be updated shortly, and you will be informed via email.', $language_code ?? '') !!}
                                    {!! create_label('translations[frontend-labels][sponsor_ads][payment_success]', 'Congratulations! Your payment was successful.', $language_code ?? '') !!}
                                    {!! create_label('translations[frontend-labels][sponsor_ads][ad_live_from]', 'Your ad is now live on our website from', $language_code ?? '') !!}
                                    {!! create_label('translations[frontend-labels][sponsor_ads][ad_live_to]', 'to', $language_code ?? '') !!}
                                    {!! create_label('translations[frontend-labels][sponsor_ads][confirmation_email]', 'You will also receive confirmation details via email.', $language_code ?? '') !!}
                                    {!! create_label('translations[frontend-labels][sponsor_ads][clicks]', 'Clicks', $language_code ?? '') !!}
                                    {!! create_label('translations[frontend-labels][sponsor_ads][no_ads_found]', 'No Ads Found', $language_code ?? '') !!}
                                    {!! create_label('translations[frontend-labels][sponsor_ads][showing]', 'Showing', $language_code ?? '') !!}
                                    {!! create_label('translations[frontend-labels][sponsor_ads][of]', 'of', $language_code ?? '') !!}
                                    {!! create_label('translations[frontend-labels][sponsor_ads][sponsor_ads_details]', 'Sponsor Ads Details', $language_code ?? '') !!}
                                    {!! create_label('translations[frontend-labels][sponsor_ads][transaction_details]', 'Transaction Details', $language_code ?? '') !!}
                                    {!! create_label('translations[frontend-labels][sponsor_ads][ad_name]', 'Ad Name', $language_code ?? '') !!}
                                    {!! create_label('translations[frontend-labels][sponsor_ads][amount]', 'Amount', $language_code ?? '') !!}
                                    {!! create_label('translations[frontend-labels][sponsor_ads][currency]', 'Currency', $language_code ?? '') !!}
                                    {!! create_label('translations[frontend-labels][sponsor_ads][gateway]', 'Gateway', $language_code ?? '') !!}
                                    {!! create_label('translations[frontend-labels][sponsor_ads][transaction_id]', 'Transaction ID', $language_code ?? '') !!}
                                    {!! create_label('translations[frontend-labels][sponsor_ads][start_date]', 'Start Date', $language_code ?? '') !!}
                                    {!! create_label('translations[frontend-labels][sponsor_ads][end_date]', 'End Date', $language_code ?? '') !!}
                                    {!! create_label('translations[frontend-labels][sponsor_ads][status]', 'Status', $language_code ?? '') !!}
                                    {!! create_label('translations[frontend-labels][sponsor_ads][paid_at]', 'Paid At', $language_code ?? '') !!}
                                    {!! create_label('translations[frontend-labels][sponsor_ads][no_transactions_found]', 'No Transactions Found', $language_code ?? '') !!}
                                    {!! create_label('translations[frontend-labels][sponsor_ads][terms_conditions]', 'Terms & Conditions', $language_code ?? '') !!}
                                    {!! create_label('translations[frontend-labels][sponsor_ads][privacy_policy]', 'Privacy Policy', $language_code ?? '') !!}
                                    {!! create_label('translations[frontend-labels][sponsor_ads][copyright]', 'Copyright', $language_code ?? '') !!}
                                    {!! create_label('translations[frontend-labels][sponsor_ads][update_profile]', 'Update Profile', $language_code ?? '') !!}
                                    {!! create_label('translations[frontend-labels][sponsor_ads][change_password]', 'Change Password', $language_code ?? '') !!}
                                    {!! create_label('translations[frontend-labels][sponsor_ads][logout]', 'Logout', $language_code ?? '') !!}
                                    {!! create_label('translations[frontend-labels][sponsor_ads][image_ad]', 'Image Ad', $language_code ?? '') !!}
                                    {!! create_label('translations[frontend-labels][sponsor_ads][upload_ad_images]', 'Upload Ad Images', $language_code ?? '') !!}
                                    {!! create_label('translations[frontend-labels][sponsor_ads][horizontal_image]', 'Horizontal Image', $language_code ?? '') !!}
                                    {!! create_label('translations[frontend-labels][sponsor_ads][horizontal_image_hint]', '(1920×753px)', $language_code ?? '') !!}
                                    {!! create_label('translations[frontend-labels][sponsor_ads][click_to_select_horizontal_image_or]', 'Click to select a horizontal image or', $language_code ?? '') !!}
                                    {!! create_label('translations[frontend-labels][sponsor_ads][browse]', 'Browse', $language_code ?? '') !!}
                                    {!! create_label('translations[frontend-labels][sponsor_ads][horizontal_image_format]', '1920×753px • PNG, JPG, GIF, WebP (MAX 10MB)', $language_code ?? '') !!}
                                    {!! create_label('translations[frontend-labels][sponsor_ads][vertical_image]', 'Vertical Image', $language_code ?? '') !!}
                                    {!! create_label('translations[frontend-labels][sponsor_ads][vertical_image_hint]', '(740×500px)', $language_code ?? '') !!}
                                    {!! create_label('translations[frontend-labels][sponsor_ads][click_to_select_vertical_image_or]', 'Click to select a vertical image or', $language_code ?? '') !!}
                                    {!! create_label('translations[frontend-labels][sponsor_ads][vertical_image_format]', '740×500px • PNG, JPG, GIF, WebP (MAX 10MB)', $language_code ?? '') !!}
                                    {!! create_label('translations[frontend-labels][sponsor_ads][ad_body]', 'Ad Body', $language_code ?? '') !!}
                                    {!! create_label('translations[frontend-labels][sponsor_ads][image_url]', 'Image URL', $language_code ?? '') !!}
                                    {!! create_label('translations[frontend-labels][sponsor_ads][image_url_hint]', 'The URL where the ad should redirect when clicked', $language_code ?? '') !!}
                                    {!! create_label('translations[frontend-labels][sponsor_ads][image_alt]', 'Image Alt Text', $language_code ?? '') !!}
                                    {!! create_label('translations[frontend-labels][sponsor_ads][image_alt_hint]', 'The "alt" attribute for the image', $language_code ?? '') !!}
                                    {!! create_label('translations[frontend-labels][sponsor_ads][app_ads_placement]', 'App Ads Placement', $language_code ?? '') !!}
                                    {!! create_label('translations[frontend-labels][sponsor_ads][splash_screen]', 'Splash Screen', $language_code ?? '') !!}
                                    {!! create_label('translations[frontend-labels][sponsor_ads][topics_page]', 'Topics Page', $language_code ?? '') !!}
                                    {!! create_label('translations[frontend-labels][sponsor_ads][under_weather_card]', 'Under Weather Card', $language_code ?? '') !!}
                                    {!! create_label('translations[frontend-labels][sponsor_ads][above_recommendations]', 'Above Recommendations', $language_code ?? '') !!}
                                    {!! create_label('translations[frontend-labels][sponsor_ads][all_channels]', 'All Channels', $language_code ?? '') !!}
                                    {!! create_label('translations[frontend-labels][sponsor_ads][search_floating_page]', 'Floating Search Page', $language_code ?? '') !!}
                                    {!! create_label('translations[frontend-labels][sponsor_ads][banner_slider]', 'Banner Slider', $language_code ?? '') !!}
                                    {!! create_label('translations[frontend-labels][sponsor_ads][channels_page_floating]', 'Floating Channels Page', $language_code ?? '') !!}
                                    {!! create_label('translations[frontend-labels][sponsor_ads][discover_page_floating]', 'Floating Discover Page', $language_code ?? '') !!}
                                    {!! create_label('translations[frontend-labels][sponsor_ads][video_page_floating]', 'Floating Video Page', $language_code ?? '') !!}
                                    {!! create_label('translations[frontend-labels][sponsor_ads][after_read_more]', 'After Read More', $language_code ?? '') !!}
                                    {!! create_label('translations[frontend-labels][sponsor_ads][contact_person_name]','Contact Person Name', $language_code ?? '') !!}
                                    {!! create_label('translations[frontend-labels][sponsor_ads][mobile_number]','Mobile Number', $language_code ?? '') !!}
                                    {!! create_label('translations[frontend-labels][sponsor_ads][contact_email]', 'Contact Email', $language_code ?? '') !!}
                                    {!! create_label('translations[frontend-labels][sponsor_ads][form_body]','Body of the Form', $language_code ?? '') !!}
                                    {!! create_label('translations[frontend-labels][sponsor_ads][per_day]', '/day', $language_code ?? '') !!}
                                    {!! create_label('translations[frontend-labels][sponsor_ads][web_ads_placement]', 'Web Ads Placement', $language_code ?? '') !!}
                                    {!! create_label('translations[frontend-labels][sponsor_ads][header]', 'Header', $language_code ?? '') !!}
                                    {!! create_label('translations[frontend-labels][sponsor_ads][footer]', 'Footer', $language_code ?? '') !!}
                                    {!! create_label('translations[frontend-labels][sponsor_ads][left_sidebar]', 'Left Sidebar', $language_code ?? '') !!}
                                    {!! create_label('translations[frontend-labels][sponsor_ads][right_sidebar]', 'Right Sidebar', $language_code ?? '') !!}
                                    {!! create_label('translations[frontend-labels][sponsor_ads][post_detail_page]', 'Post Detail Page', $language_code ?? '') !!}
                                    {!! create_label('translations[frontend-labels][sponsor_ads][latest]', 'Latest Section', $language_code ?? '') !!}
                                    {!! create_label('translations[frontend-labels][sponsor_ads][popular]', 'Popular Section', $language_code ?? '') !!}
                                    {!! create_label('translations[frontend-labels][sponsor_ads][posts]', 'Posts Section', $language_code ?? '') !!}
                                    {!! create_label('translations[frontend-labels][sponsor_ads][topic_posts]', 'Topic Posts Section', $language_code ?? '') !!}
                                    {!! create_label('translations[frontend-labels][sponsor_ads][videos]', 'Videos Section', $language_code ?? '') !!}
                                    {!! create_label('translations[frontend-labels][sponsor_ads][ad_schedule]', 'Ad Schedule', $language_code ?? '') !!}
                                    {!! create_label('translations[frontend-labels][sponsor_ads][start_date]', 'Start Date', $language_code ?? '') !!}
                                    {!! create_label('translations[frontend-labels][sponsor_ads][end_date]', 'End Date', $language_code ?? '') !!}
                                    {!! create_label('translations[frontend-labels][sponsor_ads][price_summary]', 'Price Summary', $language_code ?? '') !!}
                                    {!! create_label('translations[frontend-labels][sponsor_ads][selected_placements]', 'Selected Placements', $language_code ?? '') !!}
                                    {!! create_label('translations[frontend-labels][sponsor_ads][duration]', 'Duration', $language_code ?? '') !!}
                                    {!! create_label('translations[frontend-labels][sponsor_ads][not_selected]', 'Not Selected', $language_code ?? '') !!}
                                    {!! create_label('translations[frontend-labels][sponsor_ads][daily_rate]', 'Daily Rate', $language_code ?? '') !!}
                                    {!! create_label('translations[frontend-labels][sponsor_ads][total_amount]', 'Total Amount', $language_code ?? '') !!}
                                    {!! create_label('translations[frontend-labels][sponsor_ads][contact_information]', 'Contact Information', $language_code ?? '') !!}
                                    {!! create_label('translations[frontend-labels][sponsor_ads][contact_name]', 'Contact Name', $language_code ?? '') !!}
                                    {!! create_label('translations[frontend-labels][sponsor_ads][mobile_number]', 'Mobile Number', $language_code ?? '') !!}
                                    {!! create_label('translations[frontend-labels][sponsor_ads][contact_email]', 'Contact Email', $language_code ?? '') !!}
                                    {!! create_label('translations[frontend-labels][sponsor_ads][create_advertisement]', 'Create Advertisement', $language_code ?? '') !!}
                                    {!! create_label('translations[frontend-labels][sponsor_ads][approval_instructions]', 'You will receive further instructions via email.', $language_code ?? '') !!}
                                    {!! create_label('translations[frontend-labels][sponsor_ads][ad_live_period]', 'Your ad is live on our site from', $language_code ?? '') !!}
                                    {!! create_label('translations[frontend-labels][sponsor_ads][to]', 'to', $language_code ?? '') !!}
                                    {!! create_label('translations[frontend-labels][sponsor_ads][confirmation_email_message]', 'You will also receive confirmation details via email.', $language_code ?? '') !!}
                                    {!! create_label('translations[frontend-labels][sponsor_ads][current_password]', 'Current Password', $language_code ?? '') !!}
                                    {!! create_label('translations[frontend-labels][sponsor_ads][new_password]', 'New Password', $language_code ?? '') !!}
                                    {!! create_label('translations[frontend-labels][sponsor_ads][confirm_new_password]', 'Confirm New Password', $language_code ?? '') !!}
                                    {!! create_label('translations[frontend-labels][sponsor_ads][update_password]', 'Update Password', $language_code ?? '') !!}
                                    {!! create_label('translations[frontend-labels][sponsor_ads][current_password]','Enter Current Password', $language_code ?? '') !!}
                                    {!! create_label('translations[frontend-labels][sponsor_ads][new_password]', 'Enter New Password', $language_code ?? '') !!}
                                    {!! create_label('translations[frontend-labels][sponsor_ads][confirm_new_password]', 'Re-enter New Password', $language_code ?? '') !!}
                                    {!! create_label('translations[frontend-labels][sponsor_ads][placeholder_full_name]', 'Enter Your Full Name',$language_code ?? '') !!}
                                    {!! create_label('translations[frontend-labels][sponsor_ads][full_name]', 'Full Name', $language_code ?? '') !!}
                                    {!! create_label('translations[frontend-labels][sponsor_ads][email]', 'Email', $language_code ?? '') !!}
                                    {!! create_label('translations[frontend-labels][sponsor_ads][profile_image]', 'Profile Image', $language_code ?? '') !!}
                                    {!! create_label('translations[frontend-labels][sponsor_ads][update_profile_btn]', 'Update Profile', $language_code ?? '') !!}
                                    {!! create_label('translations[frontend-labels][sponsor_ads][clicks_summary]', 'Clicks Summary', $language_code ?? '') !!}
                                    {!! create_label('translations[frontend-labels][sponsor_ads][today_so_far]', 'Today So Far', $language_code ?? '') !!}
                                    {!! create_label('translations[frontend-labels][sponsor_ads][yesterday]', 'Yesterday', $language_code ?? '') !!}
                                    {!! create_label('translations[frontend-labels][sponsor_ads][last_7_days]', 'Last 7 Days', $language_code ?? '') !!}
                                    {!! create_label('translations[frontend-labels][sponsor_ads][this_month]', 'This Month', $language_code ?? '') !!}
                                    {!! create_label('translations[frontend-labels][sponsor_ads][click_report_by_date]', 'Click Report By Date', $language_code ?? '') !!}

                                    {!! create_label('translations[frontend-labels][topics][title]', 'Topics', $language_code ?? '') !!}
                                    {!! create_label('translations[frontend-labels][topics][showing]', 'Showing', $language_code ?? '') !!}
                                    {!! create_label('translations[frontend-labels][topics][to]', 'to', $language_code ?? '') !!}
                                    {!! create_label('translations[frontend-labels][topics][posts_out_of]', 'posts out of', $language_code ?? '') !!}
                                    {!! create_label('translations[frontend-labels][topics][total]', 'total', $language_code ?? '') !!}
                                    {!! create_label('translations[frontend-labels][topics][under]', 'under', $language_code ?? '') !!}
                                    {!! create_label('translations[frontend-labels][topics][category]', 'category', $language_code ?? '') !!}
                                    {!! create_label('translations[frontend-labels][topics][all_topics]', 'All Topics', $language_code ?? '') !!}
                                    {!! create_label('translations[frontend-labels][posts][all_posts]', 'All Posts', $language_code ?? '') !!}


                                    {!! create_label('translations[frontend-labels][login][title]', 'Login', $language_code ?? '') !!}
                                    {!! create_label('translations[frontend-labels][register][title]', 'Register', $language_code ?? '') !!}
                                    {!! create_label('translations[frontend-labels][news_videos][title]', 'News Videos', $language_code ?? '') !!}
                                    {!! create_label('translations[frontend-labels][news_videos][showing]', 'Showing Text', $language_code ?? '') !!}
                                    {!! create_label('translations[frontend-labels][news_videos][videos_out_of]', 'Videos Out Of Text', $language_code ?? '') !!}
                                    {!! create_label('translations[frontend-labels][news_videos][total]', 'Total Text', $language_code ?? '') !!}
                                    {!! create_label('translations[frontend-labels][news_videos][sort_by]', 'Sort By', $language_code ?? '') !!}
                                    {!! create_label('translations[frontend-labels][news_videos][newest]', 'Newest', $language_code ?? '') !!}
                                    {!! create_label('translations[frontend-labels][news_videos][oldest]', 'Oldest', $language_code ?? '') !!}
                                    


                                    {!! create_label('translations[frontend-labels][web_stories][title]', 'Web Stories', $language_code ?? '') !!}
                                    {!! create_label('translations[frontend-labels][web_stories][explore_more_stories]', 'Explore More Stories', $language_code ?? '') !!}
                                    {!! create_label('translations[frontend-labels][web_stories][discover_more_stories]', 'Discover more stories', $language_code ?? '') !!}
                                    {!! create_label('translations[frontend-labels][web_stories][browse_all_stories]', 'Browse All Stories', $language_code ?? '') !!}

                                    {!! create_label('translations[frontend-labels][breadcrumb][search]', 'Search', $language_code ?? '') !!}
                                    {!! create_label('translations[frontend-labels][my-account][account_info]', 'Account Info', $language_code ?? '') !!}
                                    {!! create_label('translations[frontend-labels][my-account][remove_account]', 'Remove Account', $language_code ?? '') !!}
                                    {!! create_label('translations[frontend-labels][my-account][personal_information]', 'Personal Information', $language_code ?? '') !!}
                                    {!! create_label('translations[frontend-labels][my-account][name]', 'Name', $language_code ?? '') !!}
                                    {!! create_label('translations[frontend-labels][my-account][phone]', 'Phone', $language_code ?? '') !!}
                                    {!! create_label('translations[frontend-labels][my-account][email]', 'Email', $language_code ?? '') !!}
                                    {!! create_label('translations[frontend-labels][buttons][submit]', 'Submit', $language_code ?? '') !!}
                                    {!! create_label('translations[frontend-labels][my-account][edit_profile]', 'Edit Profile', $language_code ?? '') !!}
                                    {!! create_label('translations[frontend-labels][my-account][update_profile]', 'Update Profile', $language_code ?? '') !!}
                                    {!! create_label('translations[frontend-labels][followings][unfollow]', 'Unfollow', $language_code ?? '') !!}
                                    {!! create_label('translations[frontend-labels][favorite][filter_by_type]', 'Filter by type:', $language_code ?? '') !!}
                                    {!! create_label('translations[frontend-labels][favorite][all]', 'All', $language_code ?? '') !!}
                                    {!! create_label('translations[frontend-labels][favorite][articles]', 'Articles', $language_code ?? '') !!}
                                    {!! create_label('translations[frontend-labels][favorite][videos]', 'Videos', $language_code ?? '') !!}
                                    {!! create_label('translations[frontend-labels][favorite][youtubes]', 'YouTube', $language_code ?? '') !!}
                                    {!! create_label('translations[frontend-labels][favorite][audios]', 'Audio', $language_code ?? '') !!}
                                    {!! create_label('translations[frontend-labels][favorite][article]', 'Article', $language_code ?? '') !!}
                                    {!! create_label('translations[frontend-labels][favorite][video]', 'Video', $language_code ?? '') !!}
                                    {!! create_label('translations[frontend-labels][favorite][youtube]', 'YouTube', $language_code ?? '') !!}
                                    {!! create_label('translations[frontend-labels][favorite][audio]', 'Audio', $language_code ?? '') !!}
                                    {!! create_label('translations[frontend-labels][transaction_details][pay_id]', 'Payment ID', $language_code ?? '') !!}
                                    {!! create_label('translations[frontend-labels][transaction_details][amount]', 'Amount', $language_code ?? '') !!}
                                    {!! create_label('translations[frontend-labels][transaction_details][date]', 'Date', $language_code ?? '') !!}
                                    {!! create_label('translations[frontend-labels][transaction_details][no_transaction_found]', 'No Transaction Found', $language_code ?? '') !!}
                                    {!! create_label('translations[frontend-labels][transaction_details][no_transaction_message]', 'You have not made any transactions yet.', $language_code ?? '') !!}
                                    {!! create_label('translations[frontend-labels][transaction_details][explore_membership_plans]', 'Explore Membership Plans', $language_code ?? '') !!}
                                    {!! create_label('translations[frontend-labels][mysubscription][new]', 'New', $language_code ?? '') !!}
                                    {!! create_label('translations[frontend-labels][mysubscription][subscription_hub]', 'Subscription Hub', $language_code ?? '') !!}
                                    {!! create_label('translations[frontend-labels][mysubscription][your_subscription_details]', 'Your Subscription Details', $language_code ?? '') !!}
                                    {!! create_label('translations[frontend-labels][mysubscription][plan_name]', 'Plan Name', $language_code ?? '') !!}
                                    {!! create_label('translations[frontend-labels][mysubscription][start_date]', 'Start Date', $language_code ?? '') !!}
                                    {!! create_label('translations[frontend-labels][mysubscription][end_date]', 'End Date', $language_code ?? '') !!}
                                    {!! create_label('translations[frontend-labels][mysubscription][status]', 'Status', $language_code ?? '') !!}
                                    {!! create_label('translations[frontend-labels][mysubscription][articles]', 'Articles', $language_code ?? '') !!}
                                    {!! create_label('translations[frontend-labels][mysubscription][stories]', 'Stories', $language_code ?? '') !!}
                                    {!! create_label('translations[frontend-labels][mysubscription][e_paper_and_magazines]', 'E-Paper & Magazines', $language_code ?? '') !!}
                                    {!! create_label('translations[frontend-labels][mysubscription][total_viewed_items]', 'Total Viewed Items', $language_code ?? '') !!}
                                    {!! create_label('translations[frontend-labels][mysubscription][remaining_limits_articles]', 'Remaining Article Limits', $language_code ?? '') !!}
                                    {!! create_label('translations[frontend-labels][mysubscription][remaining_limits_stories]', 'Remaining Story Limits', $language_code ?? '') !!}
                                    {!! create_label('translations[frontend-labels][mysubscription][remaining_limits_epaper]', 'Remaining E-Paper Limits', $language_code ?? '') !!}
                                    {!! create_label('translations[frontend-labels][mysubscription][no_subscription_found]', 'No Subscription Found', $language_code ?? '') !!}
                                    {!! create_label('translations[frontend-labels][mysubscription][no_subscription_message]', 'You currently have no active subscription. Please subscribe to access exclusive content.', $language_code ?? '') !!}
                                    {!! create_label('translations[frontend-labels][mysubscription][explore_membership_plans]', 'Explore Membership Plans', $language_code ?? '') !!}
                                    {!! create_label('translations[frontend-labels][subscription][enews_limit_message]', 'Your subscription limit for e-newspapers is complete.', $language_code ?? '') !!}
                                    {!! create_label('translations[frontend-labels][subscription][magazine_limit_message]', 'Your subscription limit for magazines is complete.', $language_code ?? '') !!}

                                    {!! create_label('translations[frontend-labels][limits][daily_limit_reached]', 'Daily Limit Reached', $language_code ?? '') !!}
                                    {!! create_label('translations[frontend-labels][limits][daily_limit_message]', 'You have reached your free posting limit for today.', $language_code ?? '') !!}
                                    {!! create_label('translations[frontend-labels][limits][unlock_access_message]', 'Unlock unlimited access by choosing a subscription plan that suits you best.', $language_code ?? '') !!}
                                    {!! create_label('translations[frontend-labels][limits][buy_membership_plan]', '🚀 Buy Subscription Plan', $language_code ?? '') !!}
                                    {!! create_label('translations[frontend-labels][limits][subscription_limit_reached]', 'Subscription Limit Reached', $language_code ?? '') !!}
                                    {!! create_label('translations[frontend-labels][limits][subscription_free_trial_message]', 'Your subscription posting limit has been reached. Your free trial period starts now.', $language_code ?? '') !!}

                                    {!! create_label('translations[frontend-labels][post_detailpage][copy_link_success]', 'Post link successfully copied to clipboard!', $language_code ?? '') !!}
                                    {!! create_label('translations[frontend-labels][post_detailpage][you]', 'You', $language_code ?? '') !!}
                                    {!! create_label('translations[frontend-labels][post_detailpage][click_here_to]', 'Click here to', $language_code ?? '') !!}
                                    {!! create_label('translations[frontend-labels][post_detailpage][related_topics]', 'Related Topics', $language_code ?? '') !!}
                                    {!! create_label('translations[frontend-labels][post_detailpage][share]', 'Share', $language_code ?? '') !!}
                                    {!! create_label('translations[frontend-labels][post_detailpage][prev_article]', 'Previous Article', $language_code ?? '') !!}
                                    {!! create_label('translations[frontend-labels][post_detailpage][next_article]', 'Next Article', $language_code ?? '') !!}
                                    {!! create_label('translations[frontend-labels][post_detailpage][related]', 'Related', $language_code ?? '') !!}
                                    {!! create_label('translations[frontend-labels][post_detailpage][updates]', 'Updates', $language_code ?? '') !!}
                                    {!! create_label('translations[frontend-labels][post_detailpage][no_description_available]', 'No Description Available', $language_code ?? '') !!}
                                    {!! create_label('translations[frontend-labels][search][for]', 'For', $language_code ?? '') !!}
                                    {!! create_label('translations[frontend-labels][search][showing]', 'Showing', $language_code ?? '') !!}
                                    {!! create_label('translations[frontend-labels][search][for]', 'For', $language_code ?? '') !!}
                                    {!! create_label('translations[frontend-labels][search][title]', 'Search', $language_code ?? '') !!}
                                    {!! create_label('translations[frontend-labels][search][to]', 'To', $language_code ?? '') !!}
                                    {!! create_label('translations[frontend-labels][search][posts_out_of]', 'Posts out of', $language_code ?? '') !!}
                                    {!! create_label('translations[frontend-labels][search][total]', 'Total', $language_code ?? '') !!}
                                    {!! create_label('translations[frontend-labels][search][for_search]', 'For Search', $language_code ?? '') !!}
                                    {!! create_label('translations[frontend-labels][search][under]', 'Under', $language_code ?? '') !!}
                                    {!! create_label('translations[frontend-labels][search][category]', 'Category', $language_code ?? '') !!}

                                    {!! create_label('translations[frontend-labels][filters][title]', 'Filters', $language_code ?? '') !!}
                                    {!! create_label('translations[frontend-labels][filters][other_filters]', 'Other Filters', $language_code ?? '') !!}
                                    {!! create_label('translations[frontend-labels][filters][most_liked]', 'Most Liked', $language_code ?? '') !!}
                                    {!! create_label('translations[frontend-labels][filters][most_recent]', 'Most Recent', $language_code ?? '') !!}
                                    {!! create_label('translations[frontend-labels][filters][channels_followed]', 'Channels Followed', $language_code ?? '') !!}
                                    {!! create_label('translations[frontend-labels][filters][apply]', 'Apply', $language_code ?? '') !!}
                                    {!! create_label('translations[frontend-labels][filters][clear]', 'Clear', $language_code ?? '') !!}


                                    {!! create_label('translations[frontend-labels][commentbox][comments]', 'Comments', $language_code ?? '') !!}
                                    {!! create_label('translations[frontend-labels][commentbox][leave_a_comment]', 'Leave a Comment', $language_code ?? '') !!}
                                    {!! create_label('translations[frontend-labels][commentbox][first_name]', 'First Name', $language_code ?? '') !!}
                                    {!! create_label('translations[frontend-labels][commentbox][your_email]', 'Your Email', $language_code ?? '') !!}
                                    {!! create_label('translations[frontend-labels][commentbox][add_your_comment]', 'Add Your Comment', $language_code ?? '') !!}
                                    {!! create_label('translations[frontend-labels][commentbox][your_comment]', 'Your Comment', $language_code ?? '') !!}
                                    {!! create_label('translations[frontend-labels][commentbox][send]', 'Send', $language_code ?? '') !!}
                                    {!! create_label('translations[frontend-labels][commentbox][name_change_not_allowed]', 'Cannot Change Name Message', $language_code ?? '') !!}

                                    {!! create_label('translations[frontend-labels][frontend_login][user_login_title]', 'User Login', $language_code ?? '') !!}
                                    {!! create_label('translations[frontend-labels][frontend_login][email_placeholder]', 'Enter your email', $language_code ?? '') !!}
                                    {!! create_label('translations[frontend-labels][frontend_login][password_placeholder]', 'Enter your password', $language_code ?? '') !!}
                                    {!! create_label('translations[frontend-labels][frontend_login][forgot_password]', 'Forgot Password?', $language_code ?? '') !!}
                                    {!! create_label('translations[frontend-labels][frontend_login][dont_have_account]', 'Don’t have an account?', $language_code ?? '') !!}
                                    {!! create_label('translations[frontend-labels][frontend_login][register_now]', 'Register Now', $language_code ?? '') !!}
                                    {!! create_label('translations[frontend-labels][frontend_login][remember_me]', 'Remember Me', $language_code ?? '') !!}
                                    {!! create_label('translations[frontend-labels][frontend_login][login_button]', 'Login', $language_code ?? '') !!}
                                    {!! create_label('translations[frontend-labels][common][see_more]', 'See More', $language_code ?? '') !!}


                                    {!! create_label('translations[frontend-labels][register][create_account_title]', 'Create Account Title', $language_code ?? '') !!}

                                    {!! create_label('translations[frontend-labels][register][placeholder_name]', 'Name Placeholder', $language_code ?? '') !!}
                                    {!! create_label('translations[frontend-labels][register][placeholder_email]', 'Email Placeholder', $language_code ?? '') !!}
                                    {!! create_label('translations[frontend-labels][register][placeholder_password]', 'Password Placeholder', $language_code ?? '') !!}
                                    {!! create_label('translations[frontend-labels][register][placeholder_confirm_password]', 'Confirm Password Placeholder', $language_code ?? '') !!}

                                    {!! create_label('translations[frontend-labels][register][accept_terms_label]', 'Accept Terms Label', $language_code ?? '') !!}
                                    {!! create_label('translations[frontend-labels][register][terms_of_use]', 'Terms of Use Text', $language_code ?? '') !!}

                                    {!! create_label('translations[frontend-labels][register][register_button]', 'Register Button Text', $language_code ?? '') !!}

                                    {!! create_label('translations[frontend-labels][register][already_have_account]', 'Already Have Account Text', $language_code ?? '') !!}
                                    {!! create_label('translations[frontend-labels][register][login_link]', 'Login Link Text', $language_code ?? '') !!}

                                    {!! create_label('translations[frontend-labels][register][dark_mode_toggle]', 'Dark Mode Toggle Text', $language_code ?? '') !!}
                                    {!! create_label('translations[frontend-labels][auth][sign_in]', 'Sign In', $language_code ?? '') !!}
                                    {!! create_label('translations[frontend-labels][auth][sign_up]', 'Sign Up', $language_code ?? '') !!}
                                    {!! create_label('translations[frontend-labels][auth][reset_password]', 'Reset Password', $language_code ?? '') !!}
                                    {!! create_label('translations[frontend-labels][auth][terms_of_use]', 'Terms of Use', $language_code ?? '') !!}
                                    {!! create_label('translations[frontend-labels][auth][sign_in_google]', 'Sign in with Google', $language_code ?? '') !!}

                                    {!! create_label('translations[frontend-labels][validation][email_required]', 'Email is required', $language_code ?? '') !!}
                                    {!! create_label('translations[frontend-labels][validation][email_invalid]', 'Enter a valid email address', $language_code ?? '') !!}

                                    {!! create_label('translations[frontend-labels][validation][password_required]', 'Password is required', $language_code ?? '') !!}
                                    {!! create_label('translations[frontend-labels][validation][password_min]', 'Password must be at least 8 characters', $language_code ?? '') !!}
                                    {!! create_label('translations[frontend-labels][validation][login_success]', 'Successfully logged in with Google', $language_code ?? '') !!}
                                    {!! create_label('translations[frontend-labels][validation][profile_updated]', 'Profile Updated Successfully', $language_code ?? '') !!}
                                    {!! create_label('translations[frontend-labels][validation][invalid_email]', 'Invalid email', $language_code ?? '') !!}
                                    {!! create_label('translations[frontend-labels][validation][invalid_password]', 'Invalid password', $language_code ?? '') !!}
                                    {!! create_label('translations[frontend-labels][validation][user_removed]', 'User removed successfully', $language_code ?? '') !!}


                                    {!! create_label('translations[frontend-labels][orders][order_changed_success]', 'Order Changed Successfully', $language_code ?? '') !!}
                                    {!! create_label('translations[frontend-labels][orders][status_updated_success]', 'Status Updated Successfully', $language_code ?? '') !!}

                                    {!! create_label('translations[frontend-labels][favorites][post_pinned_success]', 'Post pinned successfully.', $language_code ?? '') !!}
                                    {!! create_label('translations[frontend-labels][favorites][post_unpinned_success]', 'Post unpinned successfully.', $language_code ?? '') !!}

                                    {!! create_label('translations[frontend-labels][settings][language_updated_success]', 'Language updated successfully.', $language_code ?? '') !!}
                                    {!! create_label('translations[frontend-labels][settings][language_changed_success]', 'Language changed successfully', $language_code ?? '') !!}
                                    {!! create_label('translations[frontend-labels][bookmarks][added_success]', 'Post added to bookmarks successfully.', $language_code ?? '') !!}
                                    {!! create_label('translations[frontend-labels][bookmarks][removed_success]', 'Post removed from bookmarks successfully.', $language_code ?? '') !!}

                                    {!! create_label('translations[frontend-labels][reactions][added_success]', 'Reaction added successfully!', $language_code ?? '') !!}

                                    {!! create_label('translations[frontend-labels][comments][removed_by_admin]', 'Your comment has been removed by the admin.', $language_code ?? '') !!}
                                    {!! create_label('translations[frontend-labels][comments][stored_successfully]', 'Comment stored successfully.', $language_code ?? '') !!}
                                    {!! create_label('translations[frontend-labels][comments][updated_successfully]', 'Comment updated successfully.', $language_code ?? '') !!}
                                    {!! create_label('translations[frontend-labels][comments][deleted_successfully]', 'Comment deleted successfully.', $language_code ?? '') !!}
                                    {!! create_label('translations[frontend-labels][comments][already_reported]', 'Already Reported Comment Message', $language_code ?? '') !!}

                                    {!! create_label('translations[frontend-labels][Registration_validation][name_required]', 'Name is required', $language_code ?? '') !!}
                                    {!! create_label('translations[frontend-labels][Registration_validation][email_required]', 'Email is required', $language_code ?? '') !!}
                                    {!! create_label('translations[frontend-labels][Registration_validation][email_invalid]', 'Invalid email', $language_code ?? '') !!}
                                    {!! create_label('translations[frontend-labels][Registration_validation][email_exists]', 'Email already exists', $language_code ?? '') !!}
                                    {!! create_label('translations[frontend-labels][Registration_validation][password_required]', 'Password is required', $language_code ?? '') !!}
                                    {!! create_label('translations[frontend-labels][Registration_validation][password_confirmed]', 'Password confirmation does not match', $language_code ?? '') !!}
                                    {!! create_label('translations[frontend-labels][Registration_validation][password_min]', 'Password must be at least 8 characters', $language_code ?? '') !!}
                                    {!! create_label('translations[frontend-labels][Registration_validation][password_format]', 'Password format is invalid', $language_code ?? '') !!}
                                    {!! create_label('translations[frontend-labels][Registration_validation][accept_terms_required]', 'You must accept terms and conditions', $language_code ?? '') !!}
                                    {!! create_label('translations[frontend-labels][Registration_validation][accept_terms_accepted]', 'Terms must be accepted', $language_code ?? '') !!}
                                    {!! create_label('translations[frontend-labels][Registration_validation][register_success]', 'Registered successfully', $language_code ?? '') !!}

                                    {!! create_label('translations[frontend-labels][webstory][daily_limit_reached]', 'Daily limit reached', $language_code ?? '') !!}
                                    {!! create_label('translations[frontend-labels][webstory][daily_limit_message]', 'You have reached your daily limit for viewing web stories.', $language_code ?? '') !!}
                                    {!! create_label('translations[frontend-labels][webstory][unlock_unlimited_access]', 'Unlock unlimited access by subscribing.', $language_code ?? '') !!}
                                    {!! create_label('translations[frontend-labels][webstory][buy_membership_plan]', 'Buy Membership Plan', $language_code ?? '') !!}
                                    {!! create_label('translations[frontend-labels][webstory][subscription_limit_reached]', 'Subscription limit reached', $language_code ?? '') !!}
                                    {!! create_label('translations[frontend-labels][webstory][subscription_limit_message]', 'You have reached the limit of web stories for your current subscription.', $language_code ?? '') !!}
                                    {!! create_label('translations[frontend-labels][webstory][no_webstories_found]', 'No web stories found', $language_code ?? '') !!}
                                    {!! create_label('translations[frontend-labels][webstory][read_now]', 'Read Now', $language_code ?? '') !!}

                                    {!! create_label('translations[frontend-labels][gdpr][close]', 'Close Button', $language_code ?? '') !!}
                                    {!! create_label('translations[frontend-labels][gdpr][title]', 'GDPR Compliance Title', $language_code ?? '') !!}
                                    {!! create_label('translations[frontend-labels][gdpr][description]', 'GDPR Description Text', $language_code ?? '') !!}
                                    {!! create_label('translations[frontend-labels][gdpr][privacy_policy]', 'Privacy Policy Text', $language_code ?? '') !!}
                                    {!! create_label('translations[frontend-labels][gdpr][and]', 'And Text', $language_code ?? '') !!}
                                    {!! create_label('translations[frontend-labels][gdpr][terms_of_service]', 'Terms of Service Text', $language_code ?? '') !!}
                                    {!! create_label('translations[frontend-labels][gdpr][accept]', 'Accept Button Text', $language_code ?? '') !!}

                                    {!! create_label('translations[frontend-labels][contactus][leave_a_message]', 'Leave a Message Heading', $language_code ?? '') !!}
                                    {!! create_label('translations[frontend-labels][contactus][first_name]', 'First Name Input Placeholder', $language_code ?? '') !!}
                                    {!! create_label('translations[frontend-labels][contactus][last_name]', 'Last Name Input Placeholder', $language_code ?? '') !!}
                                    {!! create_label('translations[frontend-labels][contactus][your_email]', 'Your Email Input Placeholder', $language_code ?? '') !!}
                                    {!! create_label('translations[frontend-labels][contactus][enter_mobile_number]', 'Enter Mobile Number Input Placeholder', $language_code ?? '') !!}
                                    {!! create_label('translations[frontend-labels][contactus][describe_your_issue]', 'Describe Your Issue Textarea Placeholder', $language_code ?? '') !!}

                                    {!! create_label('translations[frontend-labels][contactus][email_required]', 'Email Required Validation Message', $language_code ?? '') !!}
                                    {!! create_label('translations[frontend-labels][contactus][email_invalid]', 'Email Invalid Validation Message', $language_code ?? '') !!}
                                    {!! create_label('translations[frontend-labels][contactus][phone_invalid]', 'Phone Invalid Validation Message', $language_code ?? '') !!}
                                    {!! create_label('translations[frontend-labels][contactus][message_required]', 'Message Required Validation Message', $language_code ?? '') !!}

                                    {!! create_label('translations[frontend-labels][contactus][send]', 'Send Button Text', $language_code ?? '') !!}
                                    {!! create_label('translations[frontend-labels][labels][last_updated]', 'Last Updated Label', $language_code ?? '') !!}
                                    {!! create_label('translations[frontend-labels][language_settings][language_settings]', 'Language Settings Heading', $language_code ?? '') !!}
                                    {!! create_label('translations[frontend-labels][language_settings][news_language]', 'News Language Label', $language_code ?? '') !!}
                                    {!! create_label('translations[frontend-labels][language_settings][web_language]', 'Web Language Label', $language_code ?? '') !!}
                                    {!! create_label('translations[frontend-labels][language_settings][save]', 'Save Button Text', $language_code ?? '') !!}
                                    {!! create_label('translations[frontend-labels][language_settings][select_website_language]', 'Select Website Language Placeholder', $language_code ?? '') !!}

                                    {!! create_label('translations[frontend-labels][comment_report][report_comment]', 'Report Comment', $language_code ?? '') !!}
                                    {!! create_label('translations[frontend-labels][comment_report][select_reason]', 'Select Reason', $language_code ?? '') !!}
                                    {!! create_label('translations[frontend-labels][comment_report][additional_details]', 'Additional Details', $language_code ?? '') !!}
                                    {!! create_label('translations[frontend-labels][comment_report][additional_info_placeholder]', 'Additional Info Placeholder', $language_code ?? '') !!}
                                    {!! create_label('translations[frontend-labels][comment_report][other]', 'Other', $language_code ?? '') !!}
                                    {!! create_label('translations[frontend-labels][comment_report][custom_reason_placeholder]', 'Custom Reason Placeholder', $language_code ?? '') !!}
                                    {!! create_label('translations[frontend-labels][comment_report][send_report]', 'Send Report Button', $language_code ?? '') !!}
                                    {!! create_label('translations[frontend-labels][comment_report][report_submitted_success]', 'Thank you! Your report has been submitted successfully.', $language_code ?? '') !!}
                                    {!! create_label('translations[frontend-labels][comment_report][block_comment]', 'Block Comment', $language_code ?? '') !!}
                                    {!! create_label('translations[frontend-labels][comment_report][block_reason]', 'Block Reason', $language_code ?? '') !!}
                                    {!! create_label('translations[frontend-labels][comment_report][block_reason_placeholder]', 'Block Reason Placeholder', $language_code ?? '') !!}
                                    {!! create_label('translations[frontend-labels][comment_report][send_block]', 'Send Block Button', $language_code ?? '') !!}
                                    {!! create_label('translations[frontend-labels][commentbox][delete_title]', 'Are you sure?', $language_code ?? '') !!}
                                    {!! create_label('translations[frontend-labels][commentbox][delete_text]', 'You want to delete this comment..!', $language_code ?? '') !!}
                                    {!! create_label('translations[frontend-labels][commentbox][delete_button]', 'Remove', $language_code ?? '') !!}
                                    {!! create_label('translations[frontend-labels][commentbox][cancel_button]', 'Cancel', $language_code ?? '') !!}
                                   

                                </div>
                                <div class="row mt-3">
                                    <div class="col-12">
                                        <button type="submit" class="btn btn-primary">{{ __('page.SAVE') }}</button>
                                    </div>
                                </div>
                            </form>
                        </div>
                        <!-- Upload File Tab -->
                        <div class="tab-pane fade" id="upload-file" role="tabpanel" aria-labelledby="upload-file-tab">
                            <form action="{{ route('language.upload-file') }}" method="POST" id="uploadFileForm"
                                enctype="multipart/form-data">
                                @csrf

                                <input type="hidden" name="language_id" value="{{ $selected_language_id }}">

                                <div class="card mb-3">
                                    <div class="card-header">
                                        <h3 class="card-title">Upload Translation Files</h3>
                                    </div>
                                    <div class="card-body">

                                        <!-- Sample Files Section -->
                                        <div class="mb-4">
                                            <h4 class="mb-3">Sample Files (English)</h4>
                                            <div class="row g-3">
                                                <div class="col-lg-6">
                                                    <div class="card">
                                                        <div class="card-header">
                                                            <h4 class="card-title">Admin Panel Files</h4>
                                                        </div>
                                                        <div class="list-group list-group-flush">
                                                            <div class="list-group-item list-group-item-action">
                                                                <div class="row align-items-center">
                                                                    <div class="col-auto">
                                                                        <span class="avatar">📄</span>
                                                                    </div>
                                                                    <div class="col text-truncate">
                                                                        <div class="text-reset d-block">message.php</div>
                                                                        <div class="text-muted text-truncate mt-n1">Sample
                                                                            message translations</div>
                                                                    </div>
                                                                    <div class="col-auto">
                                                                        <a
                                                                            href="{{ route('language.download-sample', ['type' => 'message']) }}">
                                                                            <span
                                                                                class="badge bg-primary text-white">Download</span>
                                                                        </a>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="list-group-item list-group-item-action">
                                                                <div class="row align-items-center">
                                                                    <div class="col-auto">
                                                                        <span class="avatar">📄</span>
                                                                    </div>
                                                                    <div class="col text-truncate">
                                                                        <div class="text-reset d-block">page.php</div>
                                                                        <div class="text-muted text-truncate mt-n1">Sample
                                                                            page translations</div>
                                                                    </div>
                                                                    <div class="col-auto">
                                                                        <a
                                                                            href="{{ route('language.download-sample', ['type' => 'page']) }}">
                                                                            <span
                                                                                class="badge bg-primary text-white">Download</span>
                                                                        </a>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="list-group-item list-group-item-action">
                                                                <div class="row align-items-center">
                                                                    <div class="col-auto">
                                                                        <span class="avatar">📄</span>
                                                                    </div>
                                                                    <div class="col text-truncate">
                                                                        <div class="text-reset d-block">global.php</div>
                                                                        <div class="text-muted text-truncate mt-n1">Sample
                                                                            global translations</div>
                                                                    </div>
                                                                    <div class="col-auto">
                                                                        <a
                                                                            href="{{ route('language.download-sample', ['type' => 'global']) }}">
                                                                            <span
                                                                                class="badge bg-primary text-white">Download</span>
                                                                        </a>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-lg-6">
                                                    <div class="card">
                                                        <div class="card-header">
                                                            <h4 class="card-title">Frontend Files</h4>
                                                        </div>
                                                        <div class="list-group list-group-flush">
                                                            <div class="list-group-item list-group-item-action">
                                                                <div class="row align-items-center">
                                                                    <div class="col-auto">
                                                                        <span class="avatar">📄</span>
                                                                    </div>
                                                                    <div class="col text-truncate">
                                                                        <div class="text-reset d-block">frontend-labels.php
                                                                        </div>
                                                                        <div class="text-muted text-truncate mt-n1">Sample
                                                                            frontend translations</div>
                                                                    </div>
                                                                    <div class="col-auto">
                                                                        <a
                                                                            href="{{ route('language.download-sample', ['type' => 'frontend-labels']) }}">
                                                                            <span
                                                                                class="badge bg-primary text-white">Download</span>
                                                                        </a>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <hr class="my-4">
                                        <!-- Current Uploaded Files -->
                                        @if ($selected_language && ($selected_language->admin_panel_files || $selected_language->web_files))
                                            <div id="uploadedFilesSection" class="mb-4">
                                                @php
                                                    $hasAdmin =
                                                        $selected_language->admin_panel_files &&
                                                        count($selected_language->admin_panel_files) > 0;
                                                    $hasWeb =
                                                        $selected_language->web_files &&
                                                        count($selected_language->web_files) > 0;
                                                @endphp

                                                @if ($hasAdmin || $hasWeb)
                                                    <h4 class="mb-3">Currently Uploaded Files</h4>
                                                @endif

                                                {{-- Admin Panel Files --}}
                                                @if ($hasAdmin)
                                                    <div class="mb-3 admin-section">
                                                        <h5 class="text-muted mb-2">Admin Panel Files</h5>
                                                        <div class="list-group">
                                                            @foreach ($selected_language->admin_panel_files as $file)
                                                                <div class="list-group-item">
                                                                    <div class="row align-items-center">
                                                                        <div class="col-auto">
                                                                            <span class="avatar bg-blue-lt">
                                                                                <svg xmlns="http://www.w3.org/2000/svg"
                                                                                    class="icon" width="24"
                                                                                    height="24" viewBox="0 0 24 24"
                                                                                    stroke-width="2" stroke="currentColor"
                                                                                    fill="none" stroke-linecap="round"
                                                                                    stroke-linejoin="round">
                                                                                    <path stroke="none" d="M0 0h24v24H0z"
                                                                                        fill="none" />
                                                                                    <path d="M14 3v4a1 1 0 0 0 1 1h4" />
                                                                                    <path
                                                                                        d="M17 21h-10a2 2 0 0 1 -2 -2v-14a2 2 0 0 1 2 -2h7l5 5v11a2 2 0 0 1 -2 2z" />
                                                                                    <path d="M9 17h6" />
                                                                                    <path d="M9 13h6" />
                                                                                </svg>
                                                                            </span>
                                                                        </div>
                                                                        <div class="col text-truncate">
                                                                            <div class="text-reset d-block">
                                                                                {{ basename($file) }}</div>
                                                                        </div>
                                                                        <div class="col-auto">
                                                                            <a href="{{ route('language.download-file', ['id' => $selected_language_id, 'file' => basename($file), 'type' => 'admin_panel']) }}"
                                                                                class="btn btn-sm btn-primary rounded">
                                                                                Download
                                                                            </a>
                                                                            @if ($selected_language_id != '1')
                                                                                <a href="{{ route('language.delete-file', ['id' => $selected_language_id, 'file' => basename($file), 'type' => 'admin_panel']) }}"
                                                                                    class="btn btn-sm btn-danger ms-1 rounded delete-file"
                                                                                    data-url="{{ route('language.delete-file', ['id' => $selected_language_id, 'file' => basename($file), 'type' => 'admin_panel']) }}">
                                                                                    Delete
                                                                                </a>
                                                                            @endif
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            @endforeach
                                                        </div>
                                                    </div>
                                                @endif

                                                {{-- Frontend Files --}}
                                                @if ($hasWeb)
                                                    <div class="mb-3 web-section">
                                                        <h5 class="text-muted mb-2">Frontend Files</h5>
                                                        <div class="list-group">
                                                            @foreach ($selected_language->web_files as $file)
                                                                <div class="list-group-item">
                                                                    <div class="row align-items-center">
                                                                        <div class="col-auto">
                                                                            <span class="avatar bg-success-lt">
                                                                                <svg xmlns="http://www.w3.org/2000/svg"
                                                                                    class="icon" width="24"
                                                                                    height="24" viewBox="0 0 24 24"
                                                                                    stroke-width="2" stroke="currentColor"
                                                                                    fill="none" stroke-linecap="round"
                                                                                    stroke-linejoin="round">
                                                                                    <path stroke="none" d="M0 0h24v24H0z"
                                                                                        fill="none" />
                                                                                    <path d="M14 3v4a1 1 0 0 0 1 1h4" />
                                                                                    <path
                                                                                        d="M17 21h-10a2 2 0 0 1 -2 -2v-14a2 2 0 0 1 2 -2h7l5 5v11a2 2 0 0 1 -2 2z" />
                                                                                    <path d="M9 17h6" />
                                                                                    <path d="M9 13h6" />
                                                                                </svg>
                                                                            </span>
                                                                        </div>
                                                                        <div class="col text-truncate">
                                                                            <div class="text-reset d-block">
                                                                                {{ basename($file) }}</div>
                                                                        </div>
                                                                        <div class="col-auto">
                                                                            <a href="{{ route('language.download-file', ['id' => $selected_language_id, 'file' => basename($file), 'type' => 'web']) }}"
                                                                                class="btn btn-sm btn-primary rounded">
                                                                                Download
                                                                            </a>
                                                                            @if ($selected_language_id != '1')
                                                                                <a href="{{ route('language.delete-file', ['id' => $selected_language_id, 'file' => basename($file), 'type' => 'web']) }}"
                                                                                    class="btn btn-sm btn-danger ms-1 rounded delete-file"
                                                                                    data-url="{{ route('language.delete-file', ['id' => $selected_language_id, 'file' => basename($file), 'type' => 'web']) }}">
                                                                                    Delete
                                                                                </a>
                                                                            @endif
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            @endforeach
                                                        </div>
                                                    </div>
                                                @endif
                                            </div>
                                        @endif


                                        <!-- File Upload Section -->
                                        <div class="mb-3">
                                            <h4 class="mb-3">Upload Translation Files (.php)</h4>
                                            <p class="text-muted">Download the sample files above, translate them, and
                                                upload here.</p>

                                            <div class="row g-3">
                                                <!-- Message File -->
                                                <div class="col-md-6">
                                                    <div class="card">
                                                        <div class="card-body">
                                                            <h5 class="card-title">message.php</h5>
                                                            <p class="text-muted small">Admin panel message translations
                                                            </p>
                                                            <input type="hidden" name="files[0][type]" value="message">
                                                            <input type="file" name="files[0][file]"
                                                                class="form-control" accept=".php">
                                                        </div>
                                                    </div>
                                                </div>

                                                <!-- Page File -->
                                                <div class="col-md-6">
                                                    <div class="card">
                                                        <div class="card-body">
                                                            <h5 class="card-title">page.php</h5>
                                                            <p class="text-muted small">Admin panel page translations</p>
                                                            <input type="hidden" name="files[1][type]" value="page">
                                                            <input type="file" name="files[1][file]"
                                                                class="form-control" accept=".php">
                                                        </div>
                                                    </div>
                                                </div>

                                                <!-- Global File -->
                                                <div class="col-md-6">
                                                    <div class="card">
                                                        <div class="card-body">
                                                            <h5 class="card-title">global.php</h5>
                                                            <p class="text-muted small">Admin panel global translations</p>
                                                            <input type="hidden" name="files[2][type]" value="global">
                                                            <input type="file" name="files[2][file]"
                                                                class="form-control" accept=".php">
                                                        </div>
                                                    </div>
                                                </div>

                                                <!-- Frontend Labels File -->
                                                <div class="col-md-6">
                                                    <div class="card">
                                                        <div class="card-body">
                                                            <h5 class="card-title">frontend-labels.php</h5>
                                                            <p class="text-muted small">Frontend website translations</p>
                                                            <input type="hidden" name="files[3][type]"
                                                                value="frontend-labels">
                                                            <input type="file" name="files[3][file]"
                                                                class="form-control" accept=".php">
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                    </div>
                                    <div class="card-footer text-end">
                                        <button type="submit" class="btn btn-primary">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24"
                                                height="24" viewBox="0 0 24 24" stroke-width="2"
                                                stroke="currentColor" fill="none" stroke-linecap="round"
                                                stroke-linejoin="round">
                                                <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                                <path d="M4 17v2a2 2 0 0 0 2 2h12a2 2 0 0 0 2 -2v-2" />
                                                <path d="M7 9l5 -5l5 5" />
                                                <path d="M12 4l0 12" />
                                            </svg>
                                            Upload Files
                                        </button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @include('admin.models.language-add-edit')
@endsection
