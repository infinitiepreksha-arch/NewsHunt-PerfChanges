/**
 * Search Sidebar - Recent Searches, AJAX Tab Search
 */
(function () {
    'use strict';

    const STORAGE_KEY = 'recentSearches';
    const MAX_RECENT = 10;

    // Wait for DOM ready
    function init() {
        // Initialize Search Result Page Instant AJAX Filter Engine independently
        initSearchPageFilterEngine();

        // ─── DOM Elements for Sidebar Search Modal ─────────────────
        var searchInput = document.getElementById('sidebar_search_input');
        var searchForm = document.getElementById('search-form-data');
        var recentSection = document.getElementById('recent-searches-section');
        var recentList = document.getElementById('recent-searches-list');
        var noRecentMsg = document.getElementById('no-recent-searches');
        var clearAllBtn = document.getElementById('clear-all-recent-searches');
        var resultsSection = document.getElementById('search-results-section');
        var resultsTitle = document.getElementById('search-results-title');
        var resultsList = document.getElementById('search-results-list');
        var noResultsMsg = document.getElementById('no-results-message');
        var loadingEl = document.getElementById('search-loading');
        var tabLinks = document.querySelectorAll('.search-tab-link');
        var suggestionsSection = document.getElementById('suggestions-section');
        var suggestionsList = document.getElementById('suggestions-list');

        // Guard: if search modal elements not found, bail out from modal init only
        if (!searchInput || !searchForm || !recentSection) return;

        var currentSearchQuery = '';
        var activeTab = 'all';

        // ─── Recent Searches (localStorage) ───────────────────
        function getRecentSearches() {
            try {
                return JSON.parse(localStorage.getItem(STORAGE_KEY)) || [];
            } catch (e) {
                return [];
            }
        }

        function saveRecentSearches(searches) {
            localStorage.setItem(STORAGE_KEY, JSON.stringify(searches));
        }

        function addRecentSearch(term) {
            if (!term || !term.trim()) return;
            term = term.trim();
            var searches = getRecentSearches();
            // Remove duplicate (case-insensitive)
            searches = searches.filter(function (s) { return s.toLowerCase() !== term.toLowerCase(); });
            // Add to top
            searches.unshift(term);
            // Keep max
            if (searches.length > MAX_RECENT) {
                searches = searches.slice(0, MAX_RECENT);
            }
            saveRecentSearches(searches);
        }

        function removeRecentSearch(term) {
            var searches = getRecentSearches();
            searches = searches.filter(function (s) { return s.toLowerCase() !== term.toLowerCase(); });
            saveRecentSearches(searches);
            renderRecentSearches();
        }

        function clearAllRecentSearches() {
            saveRecentSearches([]);
            renderRecentSearches();
        }

        function renderRecentSearches() {
            var searches = getRecentSearches();
            recentList.innerHTML = '';

            if (searches.length === 0) {
                noRecentMsg.style.display = 'block';
                clearAllBtn.style.display = 'none';
                return;
            }

            noRecentMsg.style.display = 'none';
            clearAllBtn.style.display = 'inline-block';

            searches.forEach(function (term) {
                var item = document.createElement('div');
                item.className = 'hstack justify-between items-center px-2 py-1 rounded-1 bg-gray-25 dark:bg-gray-800 hover:bg-gray-25 dark:hover:bg-gray-800 transition-all duration-150 cursor-pointer';

                var textSpan = document.createElement('span');
                textSpan.className = 'fs-7 fw-medium text-truncate flex-1 recent-search-keyword';
                textSpan.textContent = term;
                textSpan.style.cursor = 'pointer';
                textSpan.addEventListener('click', function () {
                    searchInput.value = term;
                    performSearch(term);
                });

                var removeBtn = document.createElement('button');
                removeBtn.type = 'button';
                removeBtn.className = 'btn border-0 p-0 ms-2 fs-7 text-danger opacity-60 hover:opacity-100 btn-close';
                removeBtn.title = 'Remove';
                removeBtn.addEventListener('click', function (e) {
                    e.stopPropagation();
                    removeRecentSearch(term);
                });

                item.appendChild(textSpan);
                item.appendChild(removeBtn);
                recentList.appendChild(item);
            });
        }

        // ─── Helpers ──────────────────────────────────────────
        function getBaseUrl() {
            var action = searchForm.getAttribute('action');
            if (action) {
                // If action is http://.../posts or /posts, remove the trailing /posts
                return action.split('?')[0].replace(/\/posts\/?$/, '');
            }
            return window.location.origin;
        }

        // ─── Suggestions Logic ─────────────────────────────────
        function fetchSuggestions(query) {
            if (!query || query.length < 2) {
                if (suggestionsSection) suggestionsSection.style.display = 'none';
                return;
            }

            var baseUrl = getBaseUrl();
            var url = baseUrl + '/posts/autocomplete?search=' + encodeURIComponent(query);

            console.log('Fetching suggestions from:', url);

            fetch(url, {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                }
            })
                .then(function (response) { return response.json(); })
                .then(function (data) {
                    console.log('Suggestions data:', data);
                    if (data && data.length > 0) {
                        renderSuggestions(data);
                    } else {
                        if (suggestionsSection) suggestionsSection.style.display = 'none';
                    }
                })
                .catch(function (err) {
                    console.error('Suggestions error:', err);
                });
        }

        function renderSuggestions(suggestions) {
            if (!suggestionsList || !suggestionsSection) return;

            suggestionsList.innerHTML = '';
            recentSection.style.display = 'none';
            suggestionsSection.style.display = 'block';

            suggestions.forEach(function (item) {
                var div = document.createElement('div');
                div.className = 'hstack gap-2 p-2 rounded-1 bg-gray-25 dark:bg-gray-800 hover:bg-gray-25 dark:hover:bg-gray-800 transition-all duration-150 cursor-pointer';
                div.style.cursor = 'pointer';

                var html = '';
                if (item.image) {
                    html += '<div class="flex-shrink-0" style="width:40px; height:30px; overflow:hidden; border-radius:4px;">';
                    html += '<img src="' + escapeHtml(item.image) + '" style="width:100%; height:100%; object-fit:cover;">';
                    html += '</div>';
                } else {
                    html += '<div class="flex-shrink-0 d-flex align-items-center justify-content-center bg-gray-100 dark:bg-gray-700" style="width:40px; height:30px; border-radius:4px;">';
                    html += '<i class="bi bi-search fs-8 opacity-50"></i>';
                    html += '</div>';
                }
                html += '<span class="fs-7 fw-medium text-truncate">' + escapeHtml(item.title) + '</span>';

                div.innerHTML = html;
                div.addEventListener('click', function () {
                    searchInput.value = item.title;
                    performSearch(item.title);
                });

                suggestionsList.appendChild(div);
            });
        }

        // ─── Search Logic ─────────────────────────────────────
        function performSearch(query) {
            if (!query || !query.trim()) {
                showRecentSearches();
                return;
            }

            query = query.trim();
            currentSearchQuery = query;

            // Save to recent
            addRecentSearch(query);

            // Show results section, hide recent & suggestions
            recentSection.style.display = 'none';
            if (suggestionsSection) suggestionsSection.style.display = 'none';
            resultsSection.style.display = 'block';
            resultsTitle.textContent = 'Results for "' + query + '"';

            // Reset to "All" tab and load data
            setActiveTab('all');
            fetchTabResults('all');
        }

        function showRecentSearches() {
            currentSearchQuery = '';
            recentSection.style.display = 'block';
            if (suggestionsSection) suggestionsSection.style.display = 'none';
            resultsSection.style.display = 'none';
            resultsList.innerHTML = '';
            noResultsMsg.style.display = 'none';
            renderRecentSearches();
        }

        function setActiveTab(tab) {
            activeTab = tab;
            tabLinks.forEach(function (link) {
                if (link.getAttribute('data-tab') === tab) {
                    link.classList.add('active');
                } else {
                    link.classList.remove('active');
                }
            });
        }

        function fetchTabResults(tab) {
            if (!currentSearchQuery) return;

            // Show loading, clear previous
            loadingEl.style.display = 'block';
            resultsList.innerHTML = '';
            noResultsMsg.style.display = 'none';

            var baseUrl = getBaseUrl();
            var url = baseUrl + '/posts/ajax-search?search=' + encodeURIComponent(currentSearchQuery) + '&tab=' + encodeURIComponent(tab);

            fetch(url, {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                }
            })
                .then(function (response) { return response.json(); })
                .then(function (data) {
                    loadingEl.style.display = 'none';

                    var results = data.results || [];

                    if (results.length === 0) {
                        noResultsMsg.style.display = 'block';
                        resultsList.innerHTML = '';
                        return;
                    }

                    noResultsMsg.style.display = 'none';
                    renderResults(results);
                })
                .catch(function (err) {
                    loadingEl.style.display = 'none';
                    noResultsMsg.style.display = 'block';
                    resultsList.innerHTML = '';
                    console.error('Search error:', err);
                });
        }

        function renderResults(results) {
            resultsList.innerHTML = '';

            results.forEach(function (item) {
                var card = document.createElement('a');
                card.href = item.url;
                card.className = 'hstack gap-2 p-2 rounded-1 text-none text-dark dark:text-white bg-gray-25 dark:bg-gray-800 hover:bg-gray-25 dark:hover:bg-gray-800 transition-all duration-150 search-result-item';
                card.style.textDecoration = 'none';

                var html = '';

                // Image
                if (item.image) {
                    html += '<div class="flex-shrink-0 search-result-image" style="width:60px; height:45px; overflow:hidden; border-radius:4px;">';
                    html += '<img src="' + escapeHtml(item.image) + '" alt="' + escapeHtml(item.title) + '" style="width:100%; height:100%; object-fit:cover;" loading="lazy">';
                    html += '</div>';
                } else {
                    html += '<div class="flex-shrink-0 search-result-image d-flex align-items-center justify-content-center bg-gray-100 dark:bg-gray-700" style="width:60px; height:45px; border-radius:4px;">';
                    if (item.type === 'topic' || item.type === 'Topic') {
                        html += '<i class="bi bi-tag fs-5 opacity-50"></i>';
                    } else if (item.type === 'channel' || item.type === 'Channel') {
                        html += '<i class="bi bi-broadcast fs-5 opacity-50"></i>';
                    } else {
                        html += '<i class="bi bi-newspaper fs-5 opacity-50"></i>';
                    }
                    html += '</div>';
                }

                // Text content
                html += '<div class="flex-1 overflow-hidden">';
                html += '<p class="m-0 fs-7 fw-bold text-truncate-2 search-result-title">' + escapeHtml(item.title) + '</p>';

                var meta = [];
                if (item.type) {
                    meta.push('<span class="badge bg-primary bg-opacity-15 text-white fs-8 fw-bold px-1 rounded-1">' + escapeHtml(item.type) + '</span>');
                }

                if (meta.length > 0) {
                    html += '<div class="hstack gap-1 fs-8 mt-narrow">' + meta.join(' · ') + '</div>';
                }

                html += '</div>';

                card.innerHTML = html;
                resultsList.appendChild(card);
            });
        }

        function escapeHtml(str) {
            if (!str) return '';
            var div = document.createElement('div');
            div.appendChild(document.createTextNode(str));
            return div.innerHTML;
        }

        // ─── Event Listeners ──────────────────────────────────

        // Form submit — prevent redirect, do AJAX search in sidebar
        searchForm.addEventListener('submit', function (e) {
            e.preventDefault();
            e.stopPropagation();
            var query = searchInput.value;
            if (query && query.trim()) {
                performSearch(query);
            }
            return false;
        });

        // Clear All button
        if (clearAllBtn) {
            clearAllBtn.addEventListener('click', function () {
                clearAllRecentSearches();
            });
        }

        // Tab clicks — AJAX only when tab is clicked
        tabLinks.forEach(function (link) {
            link.addEventListener('click', function (e) {
                e.preventDefault();
                e.stopPropagation();
                var tab = this.getAttribute('data-tab');
                if (tab === activeTab) return; // don't reload same tab
                setActiveTab(tab);
                fetchTabResults(tab);
            });
        });

        // Search input — when cleared, show recent searches. When typing, show suggestions. On Enter, redirect to search results page.
        var debounceTimer;
        searchInput.addEventListener('input', function () {
            var val = this.value.trim();
            clearTimeout(debounceTimer);
            if (val === '') {
                showRecentSearches();
            } else {
                debounceTimer = setTimeout(function () {
                    fetchSuggestions(val);
                }, 300);
            }
        });

        searchInput.addEventListener('keydown', function (e) {
            if (e.key === 'Enter' || e.keyCode === 13) {
                e.preventDefault();
                var query = this.value ? this.value.trim() : '';
                if (query) {
                    var baseUrl = getBaseUrl();
                    window.location.href = baseUrl + '/posts?search=' + encodeURIComponent(query);
                }
            }
        });

        // When the offcanvas opens, render the recent searches & focus
        var searchModal = document.getElementById('uc-search-modal');
        if (searchModal) {
            var observer = new MutationObserver(function (mutations) {
                mutations.forEach(function (mutation) {
                    if (mutation.type === 'attributes' && mutation.attributeName === 'class') {
                        if (searchModal.classList.contains('uc-open')) {
                            if (!currentSearchQuery || searchInput.value.trim() === '') {
                                showRecentSearches();
                            }
                            setTimeout(function () {
                                searchInput.focus();
                            }, 300);
                        }
                    }
                });
            });
            observer.observe(searchModal, { attributes: true });
        }

        // Drag to scroll for search tabs (Slider behavior)
        if (searchTabsNav) {
            var isDown = false;
            var startX;
            var scrollLeft;

            searchTabsNav.addEventListener('mousedown', function (e) {
                isDown = true;
                searchTabsNav.style.cursor = 'grabbing';
                startX = e.pageX - searchTabsNav.offsetLeft;
                scrollLeft = searchTabsNav.scrollLeft;
            });
            searchTabsNav.addEventListener('mouseleave', function () {
                isDown = false;
                searchTabsNav.style.cursor = 'grab';
            });
            searchTabsNav.addEventListener('mouseup', function () {
                isDown = false;
                searchTabsNav.style.cursor = 'grab';
            });
            searchTabsNav.addEventListener('mousemove', function (e) {
                if (!isDown) return;
                e.preventDefault();
                var x = e.pageX - searchTabsNav.offsetLeft;
                var walk = (x - startX) * 2; // scroll speed
                searchTabsNav.scrollLeft = scrollLeft - walk;
            });
        }

        // Render recent searches on first load
        renderRecentSearches();

        // Initialize Search Result Page Instant AJAX Filter Engine
        initSearchPageFilterEngine();
    }

    // ─── Instant AJAX Filter Engine for Search Result Page (/posts) ───
    function initSearchPageFilterEngine() {
        var postsContainer = document.getElementById('posts-container');
        if (!postsContainer) return;

        var pageSearchInput = document.getElementById('page_search_input');
        var channelAllBoxes = document.querySelectorAll('.channel-all-checkbox');
        var channelItemBoxes = document.querySelectorAll('.channel-item-checkbox');
        var topicItemBoxes = document.querySelectorAll('.topic-item-checkbox');
        var sortRadios = document.querySelectorAll('.sort-filter-radio');
        var clearBtnDesktop = document.getElementById('btn-clear-filters-desktop');
        var clearBtnMobile = document.getElementById('btn-clear-filters-mobile');

        var searchDebounceTimer;

        function syncInputState(selector, sourceElement) {
            var val = sourceElement.value;
            var isChecked = sourceElement.checked;
            document.querySelectorAll(selector).forEach(function (el) {
                if (el.value === val) {
                    el.checked = isChecked;
                }
            });
        }

        function escapeHtml(str) {
            if (!str) return '';
            return String(str)
                .replace(/&/g, '&amp;')
                .replace(/</g, '&lt;')
                .replace(/>/g, '&gt;')
                .replace(/"/g, '&quot;')
                .replace(/'/g, '&#039;');
        }

        // Channel "All" Checkbox handler
        channelAllBoxes.forEach(function (allBox) {
            allBox.addEventListener('change', function () {
                var isChecked = this.checked;
                channelAllBoxes.forEach(function (b) { b.checked = isChecked; });
                if (isChecked) {
                    channelItemBoxes.forEach(function (cb) { cb.checked = false; });
                }
                triggerAjaxFetch();
            });
        });

        // Channel Item Checkboxes handler
        channelItemBoxes.forEach(function (itemBox) {
            itemBox.addEventListener('change', function () {
                syncInputState('.channel-item-checkbox', this);

                var checkedItems = document.querySelectorAll('.channel-item-checkbox:checked');
                if (checkedItems.length > 0) {
                    channelAllBoxes.forEach(function (b) { b.checked = false; });
                } else {
                    channelAllBoxes.forEach(function (b) { b.checked = true; });
                }
                triggerAjaxFetch();
            });
        });

        // Topic Item Checkboxes handler
        topicItemBoxes.forEach(function (topicBox) {
            topicBox.addEventListener('change', function () {
                syncInputState('.topic-item-checkbox', this);
                triggerAjaxFetch();
            });
        });

        // Sort Radios handler
        sortRadios.forEach(function (radio) {
            radio.addEventListener('change', function () {
                syncInputState('.sort-filter-radio', this);
                triggerAjaxFetch();
            });
        });

        // Search Input handler (300ms debounce)
        if (pageSearchInput) {
            pageSearchInput.addEventListener('input', function () {
                clearTimeout(searchDebounceTimer);
                searchDebounceTimer = setTimeout(function () {
                    triggerAjaxFetch();
                }, 300);
            });
        }

        // Clear Filters Buttons handler
        function clearFilters() {
            if (pageSearchInput) pageSearchInput.value = '';
            channelAllBoxes.forEach(function (b) { b.checked = true; });
            channelItemBoxes.forEach(function (cb) { cb.checked = false; });
            topicItemBoxes.forEach(function (tb) { tb.checked = false; });
            sortRadios.forEach(function (r) {
                r.checked = (r.value === 'most-recent');
            });
            triggerAjaxFetch();
        }

        if (clearBtnDesktop) clearBtnDesktop.addEventListener('click', clearFilters);
        if (clearBtnMobile) clearBtnMobile.addEventListener('click', clearFilters);

        // Pagination Ajax handler
        document.addEventListener('click', function (e) {
            var pageLink = e.target.closest('#posts-container .uc-pagination a');
            if (pageLink && pageLink.href) {
                e.preventDefault();
                fetchFilteredPosts(pageLink.href, true);
            }
        });

        // Popstate handler for browser back/forward buttons
        window.addEventListener('popstate', function () {
            fetchFilteredPosts(window.location.href, false);
        });

        function triggerAjaxFetch() {
            var baseUrl = window.location.origin + window.location.pathname;
            var params = new URLSearchParams();

            var searchVal = pageSearchInput ? pageSearchInput.value.trim() : '';
            if (searchVal) params.append('search', searchVal);

            var checkedChannels = document.querySelectorAll('.channel-item-checkbox:checked');
            checkedChannels.forEach(function (cb) {
                params.append('channel[]', cb.value);
            });

            var checkedTopics = document.querySelectorAll('.topic-item-checkbox:checked');
            checkedTopics.forEach(function (tb) {
                params.append('topic[]', tb.value);
            });

            var checkedSort = document.querySelector('.sort-filter-radio:checked');
            if (checkedSort && checkedSort.value) {
                params.append('filter', checkedSort.value);
            }

            var queryString = params.toString();
            var targetUrl = baseUrl + (queryString ? '?' + queryString : '');
            fetchFilteredPosts(targetUrl, true);
        }

        function renderPagination(pagination) {
            if (!pagination || pagination.last_page <= 1) return '';

            var currentPage = pagination.current_page;
            var lastPage = pagination.last_page;
            var prevUrl = pagination.prev_page_url;
            var nextUrl = pagination.next_page_url;

            function buildPageUrl(p) {
                var baseUrl = window.location.origin + window.location.pathname;
                var params = new URLSearchParams();

                var searchVal = pageSearchInput ? pageSearchInput.value.trim() : '';
                if (searchVal) params.append('search', searchVal);

                var checkedChannels = document.querySelectorAll('.channel-item-checkbox:checked');
                checkedChannels.forEach(function (cb) {
                    params.append('channel[]', cb.value);
                });

                var checkedTopics = document.querySelectorAll('.topic-item-checkbox:checked');
                checkedTopics.forEach(function (tb) {
                    params.append('topic[]', tb.value);
                });

                var checkedSort = document.querySelector('.sort-filter-radio:checked');
                if (checkedSort && checkedSort.value) {
                    params.append('filter', checkedSort.value);
                }

                params.set('page', p);
                return baseUrl + '?' + params.toString();
            }

            var html = '<div class="nav-pagination pt-4 xl:pt-6 mt-4 border-top">';
            html += '<ul class="nav-x uc-pagination hstack gap-1 justify-center ft-secondary" data-uc-margin="">';

            // Chevron Left (Previous)
            if (prevUrl) {
                html += '<li><a href="' + escapeHtml(prevUrl) + '"><span class="icon icon-1 unicon-chevron-left"></span></a></li>';
            } else {
                html += '<li class="uc-disabled"><span class="icon icon-1 unicon-chevron-left"></span></li>';
            }

            // Ellipsis before first page
            if (currentPage > 3) {
                html += '<li><a href="' + escapeHtml(buildPageUrl(1)) + '">1</a></li>';
                html += '<li class="uc-disabled"><span>…</span></li>';
            }

            // Page Numbers around currentPage (-1 to +1)
            var startPage = Math.max(1, currentPage - 1);
            var endPage = Math.min(lastPage, currentPage + 1);

            for (var i = startPage; i <= endPage; i++) {
                if (i === currentPage) {
                    html += '<li class="uc-active"><a class="uc-active" href="' + escapeHtml(buildPageUrl(i)) + '">' + i + '</a></li>';
                } else {
                    html += '<li><a href="' + escapeHtml(buildPageUrl(i)) + '">' + i + '</a></li>';
                }
            }

            // Ellipsis after last page
            if (currentPage < lastPage - 2) {
                html += '<li class="uc-disabled"><span>…</span></li>';
                html += '<li><a href="' + escapeHtml(buildPageUrl(lastPage)) + '">' + lastPage + '</a></li>';
            }

            // Chevron Right (Next)
            if (nextUrl) {
                html += '<li><a href="' + escapeHtml(nextUrl) + '"><span class="icon icon-1 unicon-chevron-right"></span></a></li>';
            } else {
                html += '<li class="uc-disabled"><span class="icon icon-1 unicon-chevron-right"></span></li>';
            }

            html += '</ul></div>';
            return html;
        }

        function renderPostsGrid(posts, pagination) {
            if (!posts || posts.length === 0) {
                postsContainer.innerHTML = '<div id="content-area" class="rounded-lg p-4"><div class="text-center py-5"><p class="text-muted">No posts found matching your criteria.</p></div></div>';
                return;
            }

            var baseUrl = window.location.origin;
            var html = '<div id="content-area" class="rounded-lg p-4">';
            html += '<div class="panel">';
            html += '<div id="posts-ad-container" class="row child-cols-12 sm:child-cols-6 lg:child-cols-4 col-match gy-4 xl:gy-6 gx-2 sm:gx-4">';

            posts.forEach(function (post) {
                var postUrl = baseUrl + '/posts/' + post.slug;
                var topicUrl = post.topic_slug ? baseUrl + '/topics/' + post.topic_slug : '#';
                var channelUrl = post.channel_slug ? baseUrl + '/channels/' + post.channel_slug : '#';

                var imgUrl = post.image;
                if (post.type === 'video' || post.type === 'youtube') {
                    imgUrl = post.video_thumb || post.image;
                }

                html += '<div id="postRender">';
                html += '<article class="post type-post panel vstack gap-2">';

                // Image container
                html += '<div class="post-image panel overflow-hidden">';
                html += '<figure class="featured-image m-0 ratio ratio-16x9 rounded uc-transition-toggle overflow-hidden bg-gray-25 dark:bg-gray-800">';
                html += '<a href="' + postUrl + '" class="position-cover" title="' + escapeHtml(post.title) + '">';
                html += '<img class="media-cover image uc-transition-scale-up uc-transition-opaque" src="' + escapeHtml(imgUrl) + '" alt="' + escapeHtml(post.title) + '" loading="lazy">';

                if (post.type === 'video' || post.type === 'youtube' || post.type === 'audio') {
                    html += '<div class="post-category hstack gap-narrow justify-center align-items-center text-white">';
                    html += '<a class="text-none" href="' + topicUrl + '" title="' + escapeHtml(post.topic_name) + '"><i class="bi bi-play-circle font-size-45"></i></a>';
                    html += '</div>';
                }
                html += '</a></figure>';

                // Topic Tag Badge (Top Left Overlay)
                if (post.topic_name) {
                    html += '<div class="post-category hstack gap-narrow position-absolute top-0 start-0 m-1 fs-7 fw-bold h-15px px-1 rounded-1 shadow-xs bg-white text-primary">';
                    html += '<a class="text-none" href="' + topicUrl + '" title="' + escapeHtml(post.topic_name) + '">' + escapeHtml(post.topic_name) + '</a>';
                    html += '</div>';
                }
                html += '</div>';

                // Post Header Title
                html += '<div class="post-header panel vstack gap-1 lg:gap-2">';
                html += '<h3 class="post-title h6 sm:h6 xl:h5 m-0 text-truncate-2 m-0">';
                html += '<a class="text-none" href="' + postUrl + '" title="' + escapeHtml(post.title) + '">' + escapeHtml(post.title) + '</a>';
                html += '</h3>';
                html += '</div>';

                // Post Footer Meta (Channel Logo/Name & Metric Counters)
                html += '<div><div class="post-meta panel fs-7 fw-medium text-gray-900 dark:text-white text-opacity-60">';
                html += '<div class="meta"><div class="d-flex justify-between gap-2">';

                // Channel Brand
                html += '<div><div class="d-flex gap-1">';
                if (post.channel_logo) {
                    html += '<a href="' + channelUrl + '" title="' + escapeHtml(post.channel_name) + '"><img src="' + escapeHtml(post.channel_logo) + '" alt="Channel Logo" class="h-20px"></a>';
                }
                if (post.channel_name) {
                    html += '<a href="' + channelUrl + '" class="text-black dark:text-white text-none fw-bold" title="' + escapeHtml(post.channel_name) + '">' + escapeHtml(post.channel_name) + '</a>';
                }
                html += '</div></div>';

                html += '<div></div>';

                // Metric Counters (Comments, Views, Likes)
                html += '<div><div class="post-comments text-none hstack gap-narrow gap-1">';
                html += '<a href="' + postUrl + '#comment-form" class="post-comments text-none hstack gap-narrow" title="Comments"><i class="icon-narrow unicon-chat"></i><span>' + (post.comment || 0) + '</span></a>';
                html += '<i class="bi bi-eye fs-5" title="Views"></i><span title="Views">' + (post.view_count || 0) + '</span>';
                html += '<i class="bi bi-heart-fill ms-1" title="Likes"></i><span>' + (post.reaction || post.favorite || 0) + '</span>';
                html += '</div></div>';

                html += '</div>';

                // Date
                html += '<div><div class="post-date hstack gap-narrow mt-1"><span>' + escapeHtml(post.publish_date) + '</span></div></div>';

                html += '</div></div></div>';
                html += '</article></div>';
            });

            html += '</div>';
            html += renderPagination(pagination);
            html += '</div></div>';
            postsContainer.innerHTML = html;
        }

        function fetchFilteredPosts(targetUrl, updateHistory) {
            postsContainer.style.opacity = '0.5';

            fetch(targetUrl, {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                }
            })
                .then(function (response) { return response.json(); })
                .then(function (data) {
                    postsContainer.style.opacity = '1';

                    if (data.success && data.posts) {
                        renderPostsGrid(data.posts, data.pagination);
                    }

                    if (data.pagination) {
                        var elFirst = document.getElementById('counter-first');
                        var elLast = document.getElementById('counter-last');
                        var elTotal = document.getElementById('counter-total');
                        var elSubtitle = document.getElementById('search-query-sentence');

                        if (elFirst) elFirst.textContent = data.pagination.first_item;
                        if (elLast) elLast.textContent = data.pagination.last_item;
                        if (elTotal) elTotal.textContent = data.pagination.total;

                        if (elSubtitle) {
                            if (data.search_query && data.search_query.trim() !== '') {
                                elSubtitle.innerHTML = ' for <strong>"' + escapeHtml(data.search_query) + '"</strong>';
                            } else {
                                elSubtitle.innerHTML = '';
                            }
                        }
                    }

                    if (updateHistory) {
                        window.history.pushState({}, '', targetUrl);
                    }
                })
                .catch(function (err) {
                    postsContainer.style.opacity = '1';
                    console.error('Filter AJAX error:', err);
                });
        }
    }

    // Run init when DOM is ready
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }
})();
