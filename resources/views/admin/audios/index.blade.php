@extends('admin.layouts.main')

@section('title')
    {{ $title }}
@endsection
@section('pre-title')
    {{ $title }}
@endsection
@section('page-title')
    <div class="row g-2 align-items-center">
        <div class="col">
            <div class="page-pretitle">
                <a href="{{ url('admin/dashboard') }}">{{ __('page.HOME') }}/</a>
                @yield('pre-title')
            </div>
            <h2 class="page-title">
                @yield('title')
            </h2>
        </div>

        @can('create-AudioPost')
            <div class="col-auto ms-auto d-print-none">
                <a class="btn btn-primary" href="{{ route('audios.create') }}">{{ __('page.CREATE_AUDIOS') }}</a>
            </div>
        @endcan
    </div>
@endsection
@section('content') 
    <section class="section">
        <div class="col-12 mt-0">
            <div class="card admin_cards">
                <div class="card-body">
                    <div class="page-header d-print-none">
                        <div class="container-xl">
                            <div class="row g-2 align-items-center">
                                @can('list-AudioPost')
                                    <div class="col-md-6 d-print-none">
                                        <div class="d-flex flex-column flex-md-row">
                                            <div class="me-2 col-md-4">
                                                <select id="select-channel" class="form-select mb-2">
                                                    <option value="" disabled selected>
                                                        {{ __('page.SELECT_CHANNEL') }}</option>
                                                    <option value="*">{{ __('page.ALL') }}</option>
                                                    @foreach ($channel_filters as $channel)
                                                        <option value="{{ $channel->id }}">{{ $channel->name }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div class="me-2 col-md-4">
                                                <select id="select-topic" class="form-select mb-2">
                                                    <option value="" disabled selected>
                                                        {{ __('page.SELECT_TOPIC') }}</option>
                                                    <option value="*">{{ __('page.ALL') }}</option>
                                                    @foreach ($topics as $topic)
                                                        <option value="{{ $topic->id }}">{{ $topic->name }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div class="me-2 col-md-4">
                                                <div class="input-icon">
                                                    <input id="search-input" type="text" class="form-control"
                                                        placeholder="{{ __('page.SEARCH') }}">
                                                    <span class="input-icon-addon">
                                                        <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24"
                                                            height="24" viewBox="0 0 24 24" stroke-width="2"
                                                            stroke="currentColor" fill="none" stroke-linecap="round"
                                                            stroke-linejoin="round">
                                                            <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                                            <path d="M10 10m-7 0a7 7 0 1 0 14 0a7 7 0 1 0 -14 0" />
                                                            <path d="M21 21l-6 -6" />
                                                        </svg>
                                                    </span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    @can('delete-AudioPost')
                                        <div class="col-12 col-md-auto ms-md-auto d-print-none mt-3 mt-md-0">
                                            <div class="d-flex flex-wrap align-items-center gap-1">
                                                <div class="d-none border rounded py-2 bg-primary text-white p-2"
                                                    id="select-all-audio-posts">
                                                    <div class="form-check mb-0">
                                                        <input type="checkbox" id="select-all-audio-checkbox"
                                                            class="form-check-input">
                                                        <label for="select-all-audio-checkbox"
                                                            class="form-check-label fw-medium pe-2">
                                                            {{ __('page.SELECT_ALL') }}
                                                        </label>
                                                    </div>
                                                </div>

                                                <!-- Bulk Delete Button (Initially Hidden) -->
                                                <div class="d-none" id="bulk-audio-delete-btn">
                                                    <button class="btn btn-danger" id="bulk-audio-delete-action">
                                                        <i class="bi bi-trash me-2"></i> {{ __('page.DELETE') }}
                                                        (<span id="selected-count-badge">0</span>)
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    @endcan
                                @else
                                    <div class="col-12 text-center py-5">
                                        <h1 class="display-1 fw-bold text-danger">403</h1>
                                        <h1 class="fw-bold mb-0 text-danger">Access Denied</h1>
                                        <div class="d-flex justify-content-center mb-0">
                                            <div class="col-6 col-md-8 col-lg-4">
                                                <img src="{{ asset('assets/images/access_Denied/no permission.png') }}"
                                                    alt="Access Denied">
                                            </div>
                                        </div>

                                        <div class="d-inline-block">
                                            <h3 class="text-danger mb-0">You do not have permission to view the list of
                                                Audio
                                                Posts.
                                            </h3>
                                        </div>
                                    @endcan
                                </div>
                            </div>
                        </div>
                        @can('list-AudioPost')
                            <div class="page-body">
                                <div class="container-xl" id="post_card_hover">
                                    <div id="audios-container" class="row row-cards" data-url="{{ route('audios.show', 1) }}">
                                        <div id="posts-skeleton-loader" class="row row-cards">
                                            @for ($i = 0; $i < 12; $i++)
                                                <div class="col-sm-4 col-lg-3">
                                                    <div class="card card-sm">
                                                        <div class="skeleton-loader skeleton-loader-height"></div>
                                                        <div class="card-body">
                                                            <span class="card-title skeleton-loader"></span>
                                                            <div class="d-flex align-items-center mt-2">
                                                                <div class="skeleton-loader channel-post-icone"></div>
                                                                <div>
                                                                    <div class="skeleton-loader"></div>
                                                                    <div class="skeleton-loader text-secondary"></div>
                                                                </div>
                                                                <div class="ms-auto">
                                                                    <b
                                                                        class="text-secondary skeleton-loader skeleton-custom-width"></b>
                                                                    <b
                                                                        class="ms-3 text-secondary skeleton-loader skeleton-custom-width"></b>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            @endfor
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-4 mt-1">
                                            <div id="total-audio-posts" class="text-secondary mt-1">
                                                {{ __('page.LOADING') }}
                                            </div>
                                        </div>

                                        <div class="col-md-8">
                                            <div class="mt-3 d-flex">
                                                <ul class="pagination ms-auto" id="audio-pagination-container"></ul>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endcan
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Post Description Modal -->
    <div class="modal modal-blur fade modal-right" id="post-description" tabindex="-1" role="dialog" aria-hidden="true"
        aria-label="Post Description Modal">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">{{ __('page.POST_DESCRIPTION') }}
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="card card-sm">
                        <div id="post-media">
                            <img id="post-image" src="{{ asset('assets/images/no_image_available.png') }}"
                                class="w-100 h-100" alt="Post-Img">
                        </div>
                        <div class="card-body">
                            <h5 id="post-title" class="card-title">Title</h5>
                            <div class="d-flex align-items-center mt-2">
                                <img id="channel-logo" src="{{ asset('assets/images/no_image_available.png') }}"
                                    class="channel-post-icone" alt="Channel Logo">
                                <div>
                                    <div id="channel-name"></div>
                                    <div id="post-date" class="text-secondary"></div>
                                </div>
                            </div>
                            <div id="audio-player-container" class="mb-3 mt-3 d-none">
                                <audio id="audio-player" controls class="w-100">
                                    <source src="" type="audio/mpeg">
                                    Your browser does not support the audio element.
                                </audio>
                            </div>
                            <div class="d-flex ms-auto gap-2 mb-2">
                                <b id="view-count" class="text-secondary ms-1">
                                    <i class="bi bi-eye-fill"></i>
                                </b>
                                <b id="view-comments" class="text-secondary ms-1">
                                    <i class="bi bi-chat-left-text-fill"></i>
                                </b>
                                <b id="reaction-count" class="text-secondary ms-1">

                                </b>
                            </div>
                            <hr class="mt-0 mb-2">
                            <b>{{ __('page.DESCRIPTION') }}:</b>
                            <p id="post-description-text" class="line-clamp-3 mb-1"></p>
                            <div class="d-flex justify-content-between flex-wrap gap-2 mt-2">
                                <span class="text-start mt-2">
                                    <a href="javascript:void(0)" id="read-more-btn" class="text-primary fw-bold" style="display: none;">Read more</a>
                                </span>
                                <div>
                                    @can('view-comment-any-AudioPost')
                                        <a class="btn btn-primary btn-sm rounded mt-2" href="#" id="comments_url"
                                            data-base-url="{{ route('comments.index') }}">{{ __('message.VIEW_COMMENTS') }}</a>
                                    @endcan

                                    @can('update-AudioPost')
                                        <a class="btn btn-primary btn-sm rounded mt-2" href="#"
                                            id="edit-audio-btn">{{ __('page.EDIT') }}</a>
                                    @endcan

                                    @can('send-notification-any-AudioPost')
                                        <a class="btn btn-primary btn-sm rounded mt-2" href="#"
                                            id="notification-audio-btn">{{ __('message.SEND_NOTIFICATION') }}</a>
                                    @endcan

                                    @can('delete-AudioPost')
                                        <a class="btn btn-danger btn-sm delete-form delete-form-reload rounded mt-2"
                                            id="post_delete_url" href="">{{ __('page.DELETE') }}</a>
                                    @endcan
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <a href="#" class="btn btn-primary" data-bs-dismiss="modal">{{ __('page.CLOSE') }}</a>
                </div>
            </div>
        </div>
    </div>
    <input type="hidden" value="{{ route('audios.store') }}" id="customAudioStore">
@endsection
