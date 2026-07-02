<!-- Add Rss Feed Modal -->
<div id="addRssFeedModal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="addRssFeedModalLabel"
    aria-label="Add RSS Feed Modal" aria-hidden="true">
    <div class="modal-dialog">
        <form action="#" class="form-horizontal" enctype="multipart/form-data" id="addRssFeedForm" method="POST"
            data-parsley-validate>
            @csrf
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addRssFeedModalLabel">{{ __('message.ADD_RSS_FEED') }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <div class="modal-body">
                    @can('select-newslanguage-for-rssfeed')
                        <div class="alert alert-info mb-0 rounded py-2">
                            <i class="fas fa-info-circle me-2"></i>
                            {{ __('page.SELECT_NEWSLANGUAGE_FIRST') }}
                        </div>
                        <div class="form-group mt-3">
                            <label for="news_language_id" class="form-label">{{ __('message.SELECT_NEWS_LANGUAGE') }}<span
                                    class="text-danger">*</span></label>
                            <select class="form-control form-select select2" id="news_language_id" name="news_language_id">
                                <option value="" disabled selected>{{ __('message.SELECT_NEWS_LANGUAGE') }}
                                </option>
                                @foreach ($news_languages as $news_language)
                                    <option value="{{ $news_language->id }}">{{ $news_language->name }}</option>
                                @endforeach
                            </select>
                            @error('news_language_id')
                                <span class="help-block text-danger">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                    @else
                        <div class="form-group mt-3">
                            <label for="news_language_id" class="form-label">{{ __('message.SELECT_NEWS_LANGUAGE') }}<span
                                    class="text-danger">*</span></label>
                            <div class="alert alert-warning mb-0 rounded py-2">
                                <i class="fas fa-exclamation-triangle me-2"></i>
                                {{ __('message.NO_PERMISSION_NEWSLANGUAGE') }}
                            </div>
                        </div>
                    @endcan

                    <div class="form-group mt-3">
                        <label for="" class="form-label">{{ __('message.FEED_URL') }}<span
                                class="text-danger">*</span></label>
                        <input type="text" name="rss_feed_url" class="form-control"
                            placeholder="{{ __('message.PLEASE_ENTER_RSS_FEED_URL') }}" required>
                        @error('rss_feed_url')
                            <span class="help-block text-danger">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>

                    @can('select-topic-for-rssfeed')
                        <div class="form-group mt-3 d-none channel-none">
                            <label for="channels_id" class="form-label">{{ __('message.SELECT_CHANNEL') }}<span
                                    class="text-danger">*</span></label>
                            <select id="add_channel_id" class="form-control form-select select2" id="channels_id"
                                name="channel_id">
                                <option value="" disabled selected>{{ __('message.SELECT_CHANNEL') }}</option>
                                @foreach ($channels_lists as $channel)
                                    <option value="{{ $channel->id }}">{{ $channel->name }}</option>
                                @endforeach
                            </select>
                            @error('add_channel_id')
                                <span class="help-block text-danger">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                    @else
                        <div class="form-group mt-3">
                            <label for="channels_id" class="form-label">{{ __('message.SELECT_CHANNEL') }}<span
                                    class="text-danger">*</span></label>
                            <div class="alert alert-warning mb-0 rounded py-2">
                                <i class="fas fa-exclamation-triangle me-2"></i>
                                {{ __('message.NO_PERMISSION_CHANNEL') }}
                            </div>
                        </div>
                    @endcan

                    @can('select-topic-for-rssfeed')
                        <div class="form-group mt-3 d-none topic-none">
                            <label for="topics_id" class="form-label">{{ __('message.SELECT_TOPIC') }}<span
                                    class="text-danger">*</span></label>
                            <select class="form-control form-select select2" id="select-topic" name="topic_id">
                                <option value="" disabled selected>{{ __('message.SELECT_TOPIC') }}</option>
                                @foreach ($topics_lists as $topic)
                                    <option value="{{ $topic->id }}">{{ $topic->name }}</option>
                                @endforeach
                            </select>
                            @error('select-topic')
                                <span class="help-block text-danger">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                    @else
                        <div class="form-group mt-3">
                            <label for="topics_id" class="form-label">{{ __('message.SELECT_TOPIC') }}<span
                                    class="text-danger">*</span></label>
                            <div class="alert alert-warning mb-0 rounded py-2">
                                <i class="fas fa-exclamation-triangle me-2"></i>
                                {{ __('message.NO_PERMISSION_TOPIC') }}
                            </div>
                        </div>
                    @endcan

                    <div class="form-group mt-3">
                        <label for="" class="form-label">{{ __('message.SYNC_INTERVAL') }} <small>(Please
                                insert time
                                in minuts)</small><span class="text-danger">*</span></label>
                        <input type="number" min="0" oninput="this.value = Math.abs(this.value)"
                            name="sync_interval" class="form-control"
                            placeholder="{{ __('message.PLEASE_ENTER_IN_MINUTES') }}" required>
                        @error('sync_interval')
                            <span class="help-block text-danger">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                    <div class="form-group mt-3">
                        <label for="" class="form-label">{{ __('message.DATA_FORMAT') }}<span
                                class="text-danger">*</span></label>
                        <select class="form-control form-select" name="data_formate">
                            <option value="" disabled selected>{{ __('message.SELECT_FORMAT') }}</option>
                            <option value="XML">XML</option>
                            <option value="JSON">JSON</option>
                        </select>
                        @error('data_formate')
                            Topic
                        @enderror
                    </div>
                    <div class="form-group mt-3">
                        <label for="description_type" class="form-label">{{ __('message.DESCRIPTION_TYPE') }} <span
                                class="text-danger">*</span></label>
                        <select class="form-control form-select" name="description_type" id="description_type"
                            required>
                            <option value="" disabled selected>{{ __('message.SELECT_DESCRIPTION_TYPE') }}
                            </option>
                            <option value="description-tag">Description Tag</option>
                            <option value="content-encoded">Content Encoded</option>
                            <option value="media:description">media:description</option>
                        </select>
                        @error('description_type')
                            <span class="help-block text-danger">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                    <div class="form-group mt-3">
                        <label for="" class="form-label">{{ __('message.STATUS') }}<span
                                class="text-danger">*</span></label>
                        <select class="form-control form-select" name="status">
                            <option value="" disabled selected>{{ __('message.SELECT_STATUS') }}</option>
                            <option value="active">{{ __('message.ACTIVE') }}</option>
                            <option value="inactive">{{ __('message.INACTIVE') }}</option>
                        </select>
                        @error('status')
                            <span class="help-block text-danger">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
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

<!-- Edit Rss Feed Modal -->
<div id="editRssFeedModal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="editRssFeedModalLabel"
    aria-label="Edit RSS Feed Modal" aria-hidden="true">
    <div class="modal-dialog">
        <form action="{{ route('rss-feeds.update', 0) }}" class="form-horizontal" enctype="multipart/form-data"
            id="editRssFeedForm" method="POST" data-parsley-validate>
            @csrf
            @method('PUT')
            <input type="hidden" name="id" id="rss-feed-id">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editRssFeedModalLabel">{{ __('message.EDIT_RSS_FEED') }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="alert alert-info mb-0 rounded py-2">
                        <i class="fas fa-info-circle me-2"></i>
                        {{ __('page.SELECT_NEWSLANGUAGE_FIRST') }}
                    </div>
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

                    <div class="form-group mt-3">
                        <label for="" class="form-label">{{ __('message.FEED_URL') }}<span
                                class="text-danger">*</span></label>
                        <input type="text" name="rss_feed_url" class="form-control" id="edit_feed_url"
                            placeholder="{{ __('PLEASE_ENTER_RSS_FEED_URL') }}" required>
                        @error('rss_feed_url')
                            <span class="help-block text-danger">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                    <div class="form-group mt-3">
                        <label for="" class="form-label">{{ __('message.SELECT_CHANNEL') }}<span
                                class="text-danger">*</span></label>
                        <select class="form-control form-select" name="channel_id" id="edit_channel_name">
                            <option value="" disabled selected>{{ __('message.SELECT_CHANNEL') }}</option>
                            @foreach ($channels_lists as $channel)
                                <option value="{{ $channel->id }}">{{ $channel->name }}</option>
                            @endforeach
                        </select>
                        @error('channel_id')
                            <span class="help-block text-danger">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                    <div class="form-group mt-3">
                        <label for="" class="form-label">{{ __('message.SELECT_TOPIC') }}<span
                                class="text-danger">*</span></label>
                        <select class="form-control form-select" name="topic_id" id="edit_topic_name">
                            <option value="" disabled selected>{{ __('message.SELECT_TOPIC') }}</option>
                            @foreach ($topics_lists as $topic)
                                <option value="{{ $topic->id }}">{{ $topic->name }}</option>
                            @endforeach
                        </select>
                        @error('topic_id')
                            <span class="help-block text-danger">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                    <div class="form-group mt-3">
                        <label for="" class="form-label">{{ __('message.SYNC_INTERVAL') }} <small>(Please
                                insert time
                                in minuts)</small><span class="text-danger">*</span></label>
                        <input type="number" min="0" oninput="this.value = Math.abs(this.value)"
                            name="sync_interval" id="edit_sync_interval" class="form-control"
                            placeholder="{{ __('message.PLEASE_ENTER_IN_MINUTES') }}" required>
                        @error('sync_interval')
                            <span class="help-block text-danger">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                    <div class="form-group mt-3">
                        <label for="" class="form-label">{{ __('message.DATA_FORMAT') }}<span
                                class="text-danger">*</span></label>
                        <select class="form-control form-select" name="data_formate" id="edit_data_formate">
                            <option value="" disabled selected>{{ __('message.SELECT_FORMAT') }}</option>
                            <option value="XML">XML</option>
                            <option value="JSON">JSON</option>
                        </select>
                        @error('data_formate')
                            <span class="help-block text-danger">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                    <div class="form-group mt-3">
                        <label for="edit_description_type" class="form-label">{{ __('message.DESCRIPTION_TYPE') }}
                            <span class="text-danger">*</span></label>
                        <select class="form-control form-select" name="description_type" id="edit_description_type">
                            <option value="" disabled selected>{{ __('message.SELECT_DESCRIPTION_TYPE') }}
                            </option>
                            <option value="description-tag">Description Tag</option>
                            <option value="content-encoded">Content Encoded</option>
                            <option value="media:description">media:description</option>
                        </select>
                        @error('description_type')
                            <span class="help-block text-danger">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                    <div class="form-group mt-3">
                        <label for="" class="form-label">{{ __('message.STATUS') }}</label>
                        <select class="form-control form-select" name="status" id="edit_status">
                            <option value="" disabled selected>{{ __('message.SELECT_STATUS') }}</option>
                            <option value="active">{{ __('message.ACTIVE') }}</option>
                            <option value="inactive">{{ __('message.INACTIVE') }}</option>
                        </select>
                        @error('status')
                            <span class="help-block text-danger">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
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
