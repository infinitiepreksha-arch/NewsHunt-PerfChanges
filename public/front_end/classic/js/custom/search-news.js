/**
 * Search Sidebar - Recent Searches, AJAX Tab Search
 */
(function () {
    'use strict';

    const STORAGE_KEY = 'recentSearches';
    const MAX_RECENT = 10;

    // Wait for DOM ready
    function init() {
        // ─── DOM Elements ────────────────────────────────────────
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

        // Guard: if elements not found, bail out
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

        // Search input — when cleared, show recent searches. When typing, show suggestions.
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
    }

    // Run init when DOM is ready
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }

    // Also handle filterForm on search-result page (if exists)
    document.addEventListener('DOMContentLoaded', function () {
        var filterForm = document.getElementById('filterForm');
        if (filterForm) {
            filterForm.addEventListener('submit', function (e) {
                e.preventDefault();
                var checkboxes = document.querySelectorAll('input[name="channel[]"]:checked');
                var selectedChannels = Array.from(checkboxes).map(function (cb) { return cb.value; });
                var channelValue = selectedChannels.join('|');
                var hiddenInput = document.createElement('input');
                hiddenInput.type = 'hidden';
                hiddenInput.value = channelValue;
                var existingHidden = document.querySelector('input[name="selected_channels"]');
                if (existingHidden) existingHidden.remove();
                this.appendChild(hiddenInput);
                this.submit();
            });
        }
    });
})();
