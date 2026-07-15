if (typeof window.iziToast === 'undefined') {
    window.iziToast = {
        success: function (options) {
            console.log('iziToast.success fallback:', options.title || options);
        },
        error: function (options) {
            console.warn('iziToast.error fallback:', options.title || options);
        }
    };
}

(function () {
    try {
        var url = new URL(window.location.href);
        if (url.searchParams.has('refresh')) {
            url.searchParams.delete('refresh');
            window.history.replaceState({}, document.title, url.pathname + url.search);
        }
    } catch (e) { }
})();

$(document).ready(function () {
    function submitLoginForm(emailError) {
        $.ajax({
            url: $('#login-modle-form').attr('action'),
            type: 'POST',
            data: $('#login-modle-form').serialize(),
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function (response) {
                if (response.status === 'success') {
                    iziToast.success({
                        title: response.message,
                        position: 'topCenter',
                    });
                    $('#uc-account-modal .uc-modal-close-default').trigger('click');
                    setTimeout(() => {
                        location.reload();
                    }, 1500);

                } else if (response.data == 'email') {
                    emailError.text("Please Enter valid Email");
                    emailError.removeClass("d-none");
                    $('#password-login-error').addClass("d-none");

                } else {
                    $('#password-login-error').text("Please Enter valid Password");
                    emailError.add("d-none");
                    $('#password-login-error').removeClass("d-none");
                }
            },
            error: function (xhr, status, error) {
                console.error('Login failed:', error);
            }
        });
    }

    document.addEventListener('DOMContentLoaded', function () {
        const loginForm = document.getElementById('login-modle-form');
        const emailError = document.getElementById('email-login-error');
        const passwordError = document.getElementById('password-login-error');

        // Create or find success message area
        let successMsg = document.getElementById('login-success-message');
        if (!successMsg) {
            successMsg = document.createElement('div');
            successMsg.id = 'login-success-message';
            successMsg.className = 'text-success fs-7 mt-2 d-none';
            loginForm.insertBefore(successMsg, loginForm.querySelector('button'));
        }

        loginForm.addEventListener('submit', function (e) {
            e.preventDefault();

            // Reset previous messages
            emailError.textContent = '';
            passwordError.textContent = '';
            emailError.classList.add('d-none');
            passwordError.classList.add('d-none');
            successMsg.textContent = '';
            successMsg.classList.add('d-none');

            const formData = new FormData(this);

            fetch(loginForm.action, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value,
                    'Accept': 'application/json'
                },
                body: formData
            })
                .then(res => res.json())
                .then(data => {
                    if (data.error) {
                        // 🔹 Handle Laravel validation errors
                        if (data.errors) {
                            if (data.errors.email) {
                                emailError.textContent = data.errors.email[0];
                                emailError.classList.remove('d-none');
                            }
                            if (data.errors.password) {
                                passwordError.textContent = data.errors.password[0];
                                passwordError.classList.remove('d-none');
                            }
                        }
                        // 🔹 Handle custom backend errors
                        else if (data.data === 'email') {
                            emailError.textContent = data.message;
                            emailError.classList.remove('d-none');
                        } else if (data.data === 'password') {
                            passwordError.textContent = data.message;
                            passwordError.classList.remove('d-none');
                        }
                    } else if (data.status === 'success') {
                        // ✅ Show success message (using iziToast)
                        iziToast.success({
                            title: data.message,
                            position: 'topCenter',
                            timeout: 1500,
                        });

                        // Also show inside the form if you want
                        successMsg.textContent = data.message;
                        successMsg.classList.remove('d-none');

                        // Close modal if open
                        $('#uc-account-modal .uc-modal-close-default').trigger('click');

                        // Redirect after short delay
                        setTimeout(() => {
                            window.location.href = data.redirect;
                        }, 1500);
                    }
                })
                .catch(() => {
                    iziToast.error({
                        title: 'Something went wrong. Please try again.',
                        position: 'topCenter',
                    });
                });
        });
    });


    /* User register */
    $('#register-form').on('click', function (e) {
        e.preventDefault();
        const email = $('#login-email').val();
        const emailError = $('#email-login-error');
        emailError.text('');

        if (email === "") {
            emailError.text("Please enter Email");
            return false;
        }

        const password = $('#login-password').val();
        if (password === "") {
            $('#password-login-error').text("Please enter Password");
            return false;
        }
        const emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        if (!emailPattern.test(email)) {
            emailError.text("Please enter a valid Email");
            return false;
        }
    });
});

// <><><><><><><> ADD JS FOR REGISTRATION FORM ON FRONTEND <><><><><><><>
$(document).ready(function () {
    $('#register-user-form').on('submit', function (e) {
        e.preventDefault();

        // Reset error messages
        $('.text-danger').addClass('d-none').text('');

        $.ajax({
            url: $(this).attr('action'),
            method: $(this).attr('method'),
            data: $(this).serialize(),
            dataType: 'json',
            success: function (response) {
                if (response.status === 'success') {
                    iziToast.success({
                        title: response.message,
                        position: 'topCenter',
                    });

                    // Close modal if exists
                    $('#uc-account-modal .uc-modal-close-default').trigger('click');

                    // Redirect after short delay
                    setTimeout(() => {
                        window.location.href = response.redirect || '/';
                    }, 1500);
                }
            },
            error: function (xhr) {
                if (xhr.status === 422) {
                    const errors = xhr.responseJSON.errors;

                    if (errors.name) {
                        $('#name-register-error').removeClass('d-none').text(errors.name[0]);
                    }
                    if (errors.email) {
                        $('#email-register-error').removeClass('d-none').text(errors.email[0]);
                    }
                    if (errors.password) {
                        $('#password-register-error').removeClass('d-none').text(errors.password[0]);
                    }
                    if (errors.password_confirmation) {
                        $('#confirm-password-register-error').removeClass('d-none').text(errors.password_confirmation[0]);
                    }
                    if (errors.accept_terms) {
                        $('#check_terms').removeClass('d-none').text(errors.accept_terms[0]);
                    }
                } else {
                    iziToast.error({
                        title: 'Something went wrong. Please try again.',
                        position: 'topCenter',
                    });
                }
            }
        });
    });
});
// <><><><><><><> END JS FOR REGISTRATION FORM ON FRONTEND <><><><><><><>

// <><><><><><> START NEWS LANGUAGE MODEL JS <><><><><><><>
document.addEventListener('DOMContentLoaded', function () {
    const modal = document.getElementById('news-language-modal');
    const closeModalButton = modal.querySelector('.uc-modal-close-default');
    const saveButton = document.getElementById('save-news-languages');
    const isLoggedIn = !!document.querySelector('meta[name="user-id"]'); // Adjust based on your auth setup
    const localStorageKey = 'selected_news_language';
    const modalTimestampKey = 'last_modal_shown'; // New key for tracking modal display
    const toggleButton = document.querySelector('button[data-uc-toggle="#news-language-modal"]');
    const eightHoursInMs = 8 * 60 * 60 * 1000; // 8 hours in milliseconds

    // Get the previously selected language (from local storage for non-logged-in users)
    const getStoredLanguage = () => (isLoggedIn ? null : localStorage.getItem(localStorageKey));

    // Set the stored language
    const setStoredLanguage = (languageId) => {
        if (!isLoggedIn && languageId) {
            localStorage.setItem(localStorageKey, languageId);
        }
    };

    // Clear stored language (simulating cache clear)
    const clearStoredLanguage = () => {
        if (!isLoggedIn) {
            localStorage.removeItem(localStorageKey);
        }
    };

    // Initialize checkboxes based on stored or default language
    const initializeCheckboxes = () => {
        const storedLanguageId = getStoredLanguage();
        const checkboxes = document.querySelectorAll('.language-follow');
        let hasSelection = false;

        checkboxes.forEach(checkbox => {
            const languageId = checkbox.getAttribute('data-news-language-id');
            const isActive = checkbox.closest('.post-meta').querySelector('.h6').dataset.isActive === '1'; // Add 
            //  to Blade data attribute

            if (storedLanguageId) {
                checkbox.checked = languageId === storedLanguageId;
                if (checkbox.checked) hasSelection = true;
            } else if (!hasSelection && isActive && !checkbox.checked) {
                checkbox.checked = true; // Default to is_active only if no selection
                hasSelection = true;
            }
        });
    };

    // Check if modal should be shown
    const shouldShowModal = () => {
        const lastShown = localStorage.getItem(modalTimestampKey);
        if (!lastShown) {
            return true; // No timestamp, show modal (first visit)
        }
        const timeElapsed = Date.now() - parseInt(lastShown, 10);
        return timeElapsed >= eightHoursInMs; // Show if 8 hours have passed
    };

    // Update the modal timestamp
    const updateModalTimestamp = () => {
        localStorage.setItem(modalTimestampKey, Date.now().toString());
    };

    // Function to open the modal
    const openModal = () => {
        console.log('Attempting to open modal after 1-second delay');
        if (toggleButton && modal) {
            console.log('Button and modal found:', toggleButton, modal);
            // Reset modal state
            modal.style.display = ''; // Clear inline display styles
            modal.classList.remove('uc-modal-open', 'hide', 'fade'); // Remove potential modal classes
            toggleButton.removeAttribute('disabled'); // Ensure button is clickable

            // Trigger the toggle button click
            toggleButton.click();

            // Fallback: Directly show the modal if click doesn’t work
            if (modal.classList.contains('uc-modal')) {
                modal.style.display = 'block'; // Show modal
                modal.classList.add('uc-modal-open'); // Add custom modal open class
                document.body.classList.add('uc-modal-active'); // Add body class for overlay
            }

            // Update timestamp when modal is shown
            updateModalTimestamp();
        } else {
            console.error('Toggle button or modal not found in DOM');
        }
    };

    // Open modal after 1-second delay if conditions are met
    if (shouldShowModal()) {
        setTimeout(openModal, 1000);
    }

    // Initialize checkboxes
    initializeCheckboxes();

    // Handle checkbox behavior
    document.querySelectorAll('.language-follow').forEach(function (checkbox) {
        checkbox.addEventListener('change', function () {
            const checkedBoxes = document.querySelectorAll('.language-follow:checked');

            if (checkedBoxes.length === 0 && !this.checked) {
                this.checked = true;
                return;
            }

            // Uncheck all other checkboxes
            document.querySelectorAll('.language-follow').forEach(function (cb) {
                if (cb !== checkbox) {
                    cb.checked = false;
                }
            });

            const languageId = this.checked ? this.getAttribute('data-news-language-id') : null;

            if (this.checked) {
                setStoredLanguage(languageId); // Persist selection
                fetch('/follow-unfollow-language', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    },
                    body: JSON.stringify({ news_language_id: languageId }),
                })
                    .then(response => response.json())
                    .then(data => {
                        console.log(data.message);
                    });
            }
        });
    });

    // Handle Save button
    saveButton.addEventListener('click', function () {
        const checkedLanguage = document.querySelector('.language-follow:checked');
        if (checkedLanguage) {
            const languageId = checkedLanguage.getAttribute('data-news-language-id');
            setStoredLanguage(languageId);

            fetch('/follow-unfollow-language', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                },
                body: JSON.stringify({ news_language_id: languageId }),
            })
                .then(response => response.json())
                .then(data => {
                    iziToast.success({
                        title: data.message,
                        position: 'topRight',
                    });
                    modal.style.display = 'none';
                    document.body.classList.remove('uc-modal-active'); // Remove body class
                    updateModalTimestamp();
                    var refreshUrl = new URL(window.location.href);
                    refreshUrl.searchParams.set('refresh', Date.now().toString());
                    window.location.href = refreshUrl.toString();
                });
        } else {
            iziToast.error({
                title: 'Please select at least one language.',
                position: 'topRight',
            });
        }
    });

    // Handle modal close button
    closeModalButton.addEventListener('click', function () {
        const checkedLanguage = document.querySelector('.language-follow:checked');

        if (!checkedLanguage) {
            iziToast.error({
                title: 'Please select at least one language before closing.',
                position: 'topRight',
            });
            return;
        }

        modal.style.display = 'none';
        document.body.classList.remove('uc-modal-active'); // Remove body class
        updateModalTimestamp(); // Update timestamp on close
    });
});
// <><><><><><> END NEWS LANGUAGE JS <><><><><><><>

// <><><><><><> START WEB LANGUAGE JS <><><><><><><><>
document.addEventListener('DOMContentLoaded', function () {
    const modal = document.getElementById('web-language-modal');
    const closeModalButton = modal.querySelector('.uc-modal-close-default');
    const saveButton = document.getElementById('save-web-languages');
    const isLoggedIn = !!document.querySelector('meta[name="user-id"]'); // Based on your auth setup
    const localStorageKey = 'selected_web_language_code';
    const modalTimestampKey = 'last_web_modal_shown';
    const toggleButton = document.querySelector('button[data-uc-toggle="#web-language-modal"]');
    const eightHoursInMs = 8 * 60 * 60 * 1000; // 8 hours in milliseconds

    // Get stored language for guest users
    const getStoredLanguage = () => (isLoggedIn ? null : localStorage.getItem(localStorageKey));

    // Store selected language for guest users
    const setStoredLanguage = (languageCode) => {
        if (!isLoggedIn && languageCode) {
            localStorage.setItem(localStorageKey, languageCode);
        }
    };

    // Clear stored language (simulate cache clear)
    const clearStoredLanguage = () => {
        if (!isLoggedIn) {
            localStorage.removeItem(localStorageKey);
        }
    };

    // Initialize checkboxes (load saved language or fallback to system language)
    const initializeCheckboxes = () => {
        const storedLanguageCode = getStoredLanguage();
        const checkboxes = document.querySelectorAll('.language-web');

        checkboxes.forEach(checkbox => {
            const languageCode = checkbox.getAttribute('data-web-language-code');
            checkbox.checked = (languageCode === storedLanguageCode);
        });

        // If no checkbox selected, fallback to system default (Laravel app locale)
        const hasChecked = Array.from(checkboxes).some(cb => cb.checked);
        if (!hasChecked) {
            fetch('/get-default-locale')
                .then(response => response.json())
                .then(data => {
                    const defaultLocale = data.locale || 'en';
                    checkboxes.forEach(cb => {
                        if (cb.getAttribute('data-web-language-code') === defaultLocale) {
                            cb.checked = true;
                        }
                    });
                });
        }
    };

    // Check if modal should appear again (every 8 hours)
    const shouldShowModal = () => {
        const lastShown = localStorage.getItem(modalTimestampKey);
        if (!lastShown) return true;
        const timeElapsed = Date.now() - parseInt(lastShown, 10);
        return timeElapsed >= eightHoursInMs;
    };

    const updateModalTimestamp = () => {
        localStorage.setItem(modalTimestampKey, Date.now().toString());
    };

    // Open modal after 15s delay
    const openModal = () => {
        if (toggleButton && modal) {
            modal.style.display = '';
            modal.classList.remove('uc-modal-open', 'hide', 'fade');
            toggleButton.removeAttribute('disabled');
            toggleButton.click();

            if (modal.classList.contains('uc-modal')) {
                modal.style.display = 'block';
                modal.classList.add('uc-modal-open');
                document.body.classList.add('uc-modal-active');
            }

            updateModalTimestamp();
        }
    };

    if (shouldShowModal()) {
        setTimeout(openModal, 15000);
    }

    // Initialize checkboxes when DOM ready
    initializeCheckboxes();

    // Handle checkbox behavior (only one language)
    document.querySelectorAll('.language-web').forEach(function (checkbox) {
        checkbox.addEventListener('change', function () {
            const checkedBoxes = document.querySelectorAll('.language-web:checked');
            if (checkedBoxes.length === 0 && !this.checked) {
                this.checked = true;
                return;
            }

            // Uncheck others
            document.querySelectorAll('.language-web').forEach(function (cb) {
                if (cb !== checkbox) cb.checked = false;
            });

            const languageCode = this.checked ? this.getAttribute('data-web-language-code') : null;
            if (this.checked && languageCode) {
                setStoredLanguage(languageCode);
                fetch('/set-web-language', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    },
                    body: JSON.stringify({ language_code: languageCode }),
                })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            console.log(data.message);
                        }
                    });
            }
        });
    });

    // Save button
    saveButton.addEventListener('click', function () {
        const checkedLanguage = document.querySelector('.language-web:checked');
        const languageCode = checkedLanguage ? checkedLanguage.getAttribute('data-web-language-code') : null;

        if (languageCode) {
            setStoredLanguage(languageCode);
            fetch('/set-web-language', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                },
                body: JSON.stringify({ language_code: languageCode }),
            })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        iziToast.success({
                            title: data.message,
                            position: 'topRight',
                        });
                        modal.style.display = 'none';
                        document.body.classList.remove('uc-modal-active');
                        updateModalTimestamp();
                        var refreshUrl = new URL(window.location.href);
                        refreshUrl.searchParams.set('refresh', Date.now().toString());
                        window.location.href = refreshUrl.toString();
                    } else {
                        iziToast.error({
                            title: data.message || 'Failed to change language.',
                            position: 'topRight',
                        });
                    }
                });
        } else {
            iziToast.error({
                title: 'Please select at least one language.',
                position: 'topRight',
            });
        }
    });

    // Close modal
    closeModalButton.addEventListener('click', function () {
        const checkedLanguage = document.querySelector('.language-web:checked');

        if (!checkedLanguage) {
            iziToast.error({
                title: 'Please select at least one language before closing.',
                position: 'topRight',
            });
            return;
        }

        modal.style.display = 'none';
        document.body.classList.remove('uc-modal-active');
        updateModalTimestamp();
    });
});
// <><><><><><> END WEB LANGUAGE JS <><><><><><><><>
// <><><><><><> START DYNAMIC AJAX NAVBAR DROPDOWNS & CAROUSELS <><><><><><><>
$(document).ready(function () {
    var activeDropdownXhr = null;

    console.log("Initializing dynamic AJAX dropdowns & carousels...");

    // HTML Builder for Navbar Category Dropdown Posts
    function buildTopicDropdownPostHtml(post) {
        var mediaHtml = '';
        var postUrl = '/posts/' + post.slug;
        var titleAttr = (post.title || '').replace(/"/g, '&quot;');

        if (post.type === 'post') {
            mediaHtml = `
                <img class="media-cover image uc-transition-scale-up uc-transition-opaque lazy-img"
                    src="data:image/gif;base64,R0lGODlhAQABAIAAAAAAAP///yH5BAEAAAAALAAAAAABAAEAAAIBRAA7"
                    data-src="${post.image}"
                    alt="${titleAttr}"
                    title="${titleAttr}"
                    loading="lazy">`;
        } else if (post.type === 'youtube' || post.type === 'video') {
            mediaHtml = `
                <img class="media-cover image uc-transition-scale-up uc-transition-opaque lazy-img"
                    src="data:image/gif;base64,R0lGODlhAQABAIAAAAAAAP///yH5BAEAAAAALAAAAAABAAEAAAIBRAA7"
                    data-src="${post.video_thumb || post.image}"
                    alt="${titleAttr}"
                    title="${titleAttr}"
                    loading="lazy">
                <div class="post-category hstack gap-narrow justify-center align-items-center text-white">
                    <a class="text-none" href="${postUrl}" title="${titleAttr}"><i class="bi bi-play-circle font-size-45"></i></a>
                </div>`;
        } else if (post.type === 'audio') {
            mediaHtml = `
                <img class="media-cover image uc-transition-scale-up uc-transition-opaque lazy-img"
                    src="data:image/gif;base64,R0lGODlhAQABAIAAAAAAAP///yH5BAEAAAAALAAAAAABAAEAAAIBRAA7"
                    data-src="${post.image}"
                    alt="${titleAttr}"
                    title="${titleAttr}"
                    loading="lazy">
                <div class="post-category hstack gap-narrow justify-center align-items-center text-white">
                    <a class="text-none" href="${postUrl}" title="${titleAttr}"><i class="bi bi-play-circle font-size-45"></i></a>
                </div>`;
        } else {
            mediaHtml = `
                <img class="media-cover image uc-transition-scale-up uc-transition-opaque lazy-img"
                    src="data:image/gif;base64,R0lGODlhAQABAIAAAAAAAP///yH5BAEAAAAALAAAAAABAAEAAAIBRAA7"
                    data-src="${post.video_thumb || post.image}"
                    alt="${titleAttr}"
                    title="${titleAttr}"
                    loading="lazy">
                <div class="post-category hstack gap-narrow justify-center align-items-center text-white">
                    <a class="text-none" href="${postUrl}" title="${titleAttr}"><i class="bi bi-play-circle font-size-45"></i></a>
                </div>`;
        }

        var dateTitle = post.publish_date_news || post.publish_date || post.pubdate || '';
        var dateText = post.publish_date || post.pubdate || '';

        return `
            <div>
                <article class="post type-post panel uc-transition-toggle vstack gap-1">
                    <div class="post-media panel overflow-hidden">
                        <div class="featured-image bg-gray-25 dark:bg-gray-800 ratio ratio-16x9">
                            <a href="${postUrl}" class="position-cover">
                                ${mediaHtml}
                            </a>
                        </div>
                    </div>
                    <div class="post-header panel vstack gap-narrow">
                        <h3 class="post-title h6 m-0 text-truncate-2">
                            <a class="text-none hover:text-primary duration-150"
                                href="${postUrl}"
                                title="${titleAttr}">${post.title || ''}</a>
                        </h3>
                        <div class="post-meta panel hstack justify-start gap-1 fs-7 ft-tertiary fw-medium text-gray-900 dark:text-white text-opacity-60 d-none md:d-flex z-1 d-none md:d-block">
                            <div>
                                <div class="post-date hstack gap-narrow">
                                    <span title="${dateTitle}">${dateText}</span>
                                </div>
                            </div>
                            <div>
                                <a href="${postUrl}#comment-form"
                                    class="post-comments text-none hstack gap-narrow"
                                    title="Comments">
                                    <i class="icon-narrow unicon-chat"></i>
                                    <span>${post.comment || 0}</span>
                                    <i class="bi bi-eye fs-5 ms-1" title="Views"></i>
                                    <span title="Views">${post.view_count || 0}</span>
                                    <i class="bi bi-heart-fill ms-1"></i>
                                    <span>${post.reaction || 0}</span>
                                </a>
                            </div>
                        </div>
                    </div>
                </article>
            </div>`;
    }

    // HTML Builder for Most Read Slides
    function buildMostReadSlideHtml(post) {
        var mediaHtml = '';
        var postUrl = '/posts/' + post.slug;
        var titleAttr = (post.title || '').replace(/"/g, '&quot;');

        if (post.type === 'video' || post.type === 'youtube') {
            mediaHtml = `
                <a href="${postUrl}" class="position-cover">
                    <img class="media-cover image uc-transition-scale-up uc-transition-opaque lazy-img"
                        src="data:image/gif;base64,R0lGODlhAQABAIAAAAAAAP///yH5BAEAAAAALAAAAAABAAEAAAIBRAA7"
                        data-src="${post.video_thumb || post.image}"
                        alt="${titleAttr}"
                        title="${titleAttr}"
                        loading="lazy">
                    <div class="post-category hstack gap-narrow justify-center align-items-center text-white">
                        <a class="text-none" href="${postUrl}" title="${titleAttr}"><i class="bi bi-play-circle font-size-45"></i></a>
                    </div>
                </a>`;
        } else if (post.type === 'post') {
            mediaHtml = `
                <a href="${postUrl}" class="position-cover">
                    <img class="media-cover image uc-transition-scale-up uc-transition-opaque lazy-img"
                        src="data:image/gif;base64,R0lGODlhAQABAIAAAAAAAP///yH5BAEAAAAALAAAAAABAAEAAAIBRAA7"
                        data-src="${post.image}"
                        alt="${titleAttr}"
                        title="${titleAttr}"
                        loading="lazy">
                </a>`;
        } else if (post.type === 'audio') {
            mediaHtml = `
                <a href="${postUrl}" class="position-cover">
                    <img class="media-cover image uc-transition-scale-up uc-transition-opaque lazy-img"
                        src="data:image/gif;base64,R0lGODlhAQABAIAAAAAAAP///yH5BAEAAAAALAAAAAABAAEAAAIBRAA7"
                        data-src="${post.image}"
                        alt="${titleAttr}"
                        title="${titleAttr}"
                        loading="lazy">
                    <div class="post-category hstack gap-narrow justify-center align-items-center text-white">
                        <a class="text-none" href="${postUrl}" title="${titleAttr}"><i class="bi bi-play-circle font-size-45"></i></a>
                    </div>
                </a>`;
        }

        var channelHtml = '';
        if (post.channel) {
            channelHtml = `
                <div class="post-meta panel hstack justify-start gap-1 fs-7 ft-tertiary fw-medium text-gray-900 dark:text-white text-opacity-60 d-none md:d-flex z-1 d-none md:d-block">
                    <div>
                        <div class="post-date hstack gap-narrow">
                            <a href="/channels/${post.channel.slug}" class="post-comments text-none hstack gap-narrow channel-button" title="${(post.channel.name || '').replace(/"/g, '&quot;')}">
                                <span>${post.channel.name || ''}</span>
                            </a>
                        </div>
                    </div>
                </div>`;
        }

        var dateTitle = post.publish_date_news || post.publish_date || post.pubdate || '';
        var dateText = post.publish_date || post.pubdate || '';

        return `
            <div class="swiper-slide" data-post-id="${post.id}">
                <div>
                    <article class="post type-post panel uc-transition-toggle vstack gap-2">
                        <div class="post-media panel overflow-hidden">
                            <div class="featured-image bg-gray-25 dark:bg-gray-800 ratio ratio-3x2">
                                ${mediaHtml}
                            </div>
                        </div>
                        <div class="post-header panel vstack gap-1">
                            <h3 class="post-title h6 m-0 text-truncate-2 hover:text-primary">
                                <a class="text-none duration-150" href="${postUrl}" title="${titleAttr}">${post.title || ''}</a>
                            </h3>
                            ${channelHtml}
                            <div class="post-meta panel hstack justify-between gap-1 fs-7 ft-tertiary fw-medium text-gray-900 dark:text-white text-opacity-60 d-none md:d-flex z-1 d-none md:d-block">
                                <div>
                                    <div class="post-date hstack gap-narrow">
                                        <span title="${dateTitle}">${dateText}</span>
                                    </div>
                                </div>
                                <div>
                                    <a href="${postUrl}#comment-form" class="post-comments text-none hstack gap-narrow" title="Comments">
                                        <i class="icon-narrow unicon-chat" title="Comments"></i>
                                        <span title="Comments">${post.comment || 0}</span>
                                    </a>
                                </div>
                                <div title="Views">
                                    <i class="bi bi-eye fs-5"></i>
                                    <span>${post.view_count || 0}</span>
                                </div>
                                <div title="Reaction">
                                    <i class="bi bi-heart-fill"></i>
                                    <span>${post.reaction || 0}</span>
                                </div>
                            </div>
                        </div>
                    </article>
                </div>
            </div>`;
    }

    // HTML Builder for Web Story Slides
    function buildStorySlideHtml(story) {
        var segmentsHtml = '';
        if (story.story_slides && story.story_slides.length > 0) {
            story.story_slides.forEach(function () {
                segmentsHtml += '<div class="progress-segment flex-grow-1 h-1 bg-white bg-opacity-50 story-dashed-css"></div>';
            });
        }

        var storyUrl = '/webstories/' + (story.topic ? story.topic.slug : 'default') + '/' + story.slug;
        var titleAttr = (story.title || '').replace(/"/g, '&quot;');
        var firstSlideImage = story.first_slide_image || '/storage/default.jpg';

        return `
            <div class="swiper-slide px-1" data-post-id="${story.id}">
                <div class="card bg-white dark:bg-gray-800 d-flex flex-column" id="card_style">
                    <a href="${storyUrl}" target="_blank" class="position-relative d-block">
                        <img src="data:image/gif;base64,R0lGODlhAQABAIAAAAAAAP///yH5BAEAAAAALAAAAAABAAEAAAIBRAA7"
                            data-src="${firstSlideImage}"
                            class="card-img-top lazy-img"
                            alt="${titleAttr}"
                            title="${titleAttr}">
                        <div class="story-progress-container position-absolute bottom-0 start-0 w-100 px-1 pb-2">
                            <div class="progress-segments d-flex gap-1">
                                ${segmentsHtml}
                            </div>
                        </div>
                        <span class="visual-stories-icon position-absolute top-2 end-1 p-1 rounded-circle dark:text-white text-white bg-gray-800">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24">
                                <path fill="currentColor" d="M7 20V4h10v16zm-4-2V6h2v12zm16 0V6h2v12z" />
                            </svg>
                        </span>
                    </a>
                    <div id="card_title" class="card-footer text-gray-900 dark:text-white d-flex flex-column h-100">
                        <h3 class="post-title h6 m-0 text-truncate-2 hover:text-primary">
                            <a class="text-none duration-150" target="_blank"
                                href="${storyUrl}"
                                title="${titleAttr}">
                                ${story.title || ''}
                            </a>
                        </h3>
                        <div class="mt-2 text-muted fs-7">
                            ${story.publish_date || ''}
                        </div>
                    </div>
                </div>
            </div>`;
    }

    // HTML Builder for Top Posts Slides
    function buildTopPostSlideHtml(post) {
        var mediaHtml = '';
        var postUrl = '/posts/' + post.slug;
        var titleAttr = (post.title || '').replace(/"/g, '&quot;');

        if (post.type === 'video' || post.type === 'youtube') {
            mediaHtml = `
                <a href="${postUrl}" class="position-cover">
                    <img class="media-cover image uc-transition-scale-up uc-transition-opaque lazy-img"
                        src="data:image/gif;base64,R0lGODlhAQABAIAAAAAAAP///yH5BAEAAAAALAAAAAABAAEAAAIBRAA7"
                        data-src="${post.video_thumb || post.image}"
                        alt="${titleAttr}"
                        loading="lazy">
                    <div class="post-category hstack gap-narrow justify-center align-items-center text-white">
                        <a class="text-none" href="/topics/${post.slug}" title="${titleAttr}"><i class="bi bi-play-circle font-size-45"></i></a>
                    </div>
                </a>`;
        } else if (post.type === 'audio') {
            mediaHtml = `
                <a href="${postUrl}" class="position-cover">
                    <img class="media-cover image uc-transition-scale-up uc-transition-opaque lazy-img"
                        src="data:image/gif;base64,R0lGODlhAQABAIAAAAAAAP///yH5BAEAAAAALAAAAAABAAEAAAIBRAA7"
                        data-src="${post.image}"
                        alt="${titleAttr}"
                        loading="lazy">
                    <div class="post-category hstack gap-narrow justify-center align-items-center text-white">
                    </div>
                </a>`;
        } else if (post.type === 'post') {
            mediaHtml = `
                <a href="${postUrl}" class="position-cover">
                    <img class="media-cover image uc-transition-scale-up uc-transition-opaque lazy-img"
                        src="data:image/gif;base64,R0lGODlhAQABAIAAAAAAAP///yH5BAEAAAAALAAAAAABAAEAAAIBRAA7"
                        data-src="${post.image}"
                        alt="${titleAttr}"
                        loading="lazy">
                    <div class="post-category hstack gap-narrow justify-center align-items-center text-white">
                    </div>
                </a>`;
        }

        var channelHtml = '';
        if (post.channel) {
            var logoUrl = post.channel.logo ? ('/storage/images/' + post.channel.logo) : '/assets/images/logo/default_logo.png';
            channelHtml = `
                <a href="/channels/${post.channel.slug}" class="post-comments text-none hstack gap-narrow">
                    <img src="${logoUrl}" alt="channel logo" title="${(post.channel.name || '').replace(/"/g, '&quot;')}" class="rounded-pill h-20px">
                    <span title="${(post.channel.name || '').replace(/"/g, '&quot;')}">${post.channel.name || ''}</span>
                </a>`;
        }

        return `
            <div class="swiper-slide" data-post-id="${post.id}">
                <div>
                    <article class="post type-post panel uc-transition-toggle gap-2">
                        <div class="row child-cols g-2" data-uc-grid>
                            <div class="col-auto">
                                <div class="post-media panel overflow-hidden max-w-64px min-w-64px">
                                    <div class="featured-image bg-gray-25 dark:bg-gray-800 ratio ratio-1x1">
                                        ${mediaHtml}
                                    </div>
                                </div>
                            </div>
                            <div>
                                <div class="post-header panel vstack gap-1">
                                    <h3 class="post-title h6 hover:text-primary m-0 text-truncate-2">
                                        <a class="text-none duration-150" href="${postUrl}" title="${titleAttr}">${post.title || ''}</a>
                                    </h3>
                                </div>
                                ${channelHtml}
                            </div>
                        </div>
                    </article>
                </div>
            </div>`;
    }

    // HTML Builder for Followed Channels Slides
    function buildFollowedChannelSlideHtml(post) {
        var mediaHtml = '';
        var postUrl = '/posts/' + post.slug;
        var titleAttr = (post.title || '').replace(/"/g, '&quot;');

        if (post.type === 'video' || post.type === 'youtube') {
            mediaHtml = `
                <a href="${postUrl}" class="position-cover">
                    <img class="media-cover image uc-transition-scale-up uc-transition-opaque lazy-img"
                        src="data:image/gif;base64,R0lGODlhAQABAIAAAAAAAP///yH5BAEAAAAALAAAAAABAAEAAAIBRAA7"
                        data-src="${post.video_thumb || post.image}"
                        alt="${titleAttr}"
                        title="${titleAttr}"
                        loading="lazy">
                    <div class="post-category hstack gap-narrow justify-center align-items-center text-white">
                        <a class="text-none" href="${postUrl}" title="${titleAttr}"><i class="bi bi-play-circle font-size-45"></i></a>
                    </div>
                </a>`;
        } else if (post.type === 'post') {
            mediaHtml = `
                <a href="${postUrl}" class="position-cover">
                    <img class="media-cover image uc-transition-scale-up uc-transition-opaque lazy-img"
                        src="data:image/gif;base64,R0lGODlhAQABAIAAAAAAAP///yH5BAEAAAAALAAAAAABAAEAAAIBRAA7"
                        data-src="${post.image}"
                        alt="${titleAttr}"
                        title="${titleAttr}"
                        loading="lazy">
                </a>`;
        } else if (post.type === 'audio') {
            mediaHtml = `
                <a href="${postUrl}" class="position-cover">
                    <img class="media-cover image uc-transition-scale-up uc-transition-opaque lazy-img"
                        src="data:image/gif;base64,R0lGODlhAQABAIAAAAAAAP///yH5BAEAAAAALAAAAAABAAEAAAIBRAA7"
                        data-src="${post.image}"
                        alt="${titleAttr}"
                        title="${titleAttr}"
                        loading="lazy">
                </a>
                <div class="post-category hstack gap-narrow justify-center align-items-center text-white">
                    <a class="text-none" href="${postUrl}" title="${titleAttr}"><i class="bi bi-play-circle font-size-45"></i></a>
                </div>`;
        }

        var channelHtml = '';
        if (post.channel) {
            channelHtml = `
                <div class="post-meta panel hstack justify-start gap-1 fs-7 ft-tertiary fw-medium text-gray-900 dark:text-white text-opacity-60 d-none md:d-flex z-1">
                    <div>
                        <div class="post-date hstack gap-narrow">
                            <a href="/channels/${post.channel.slug}" class="post-comments text-none hstack gap-narrow channel-button" title="${(post.channel.name || '').replace(/"/g, '&quot;')}">
                                <span>${post.channel.name || ''}</span>
                            </a>
                        </div>
                    </div>
                </div>`;
        }

        var dateTitle = post.publish_date_news || post.publish_date || post.pubdate || '';
        var dateText = post.publish_date || post.pubdate || '';

        return `
            <div class="swiper-slide" data-post-id="${post.id}">
                <div>
                    <article class="post type-post panel uc-transition-toggle vstack gap-2">
                        <div class="post-media panel overflow-hidden">
                            <div class="featured-image bg-gray-25 dark:bg-gray-800 ratio ratio-3x2">
                                ${mediaHtml}
                            </div>
                        </div>
                        <div class="post-header panel vstack gap-1">
                            <h3 class="post-title h6 m-0 text-truncate-2 hover:text-primary">
                                <a class="text-none duration-150" href="${postUrl}" title="${titleAttr}">${post.title || ''}</a>
                            </h3>
                            ${channelHtml}
                            <div class="post-meta panel hstack justify-between gap-1 fs-7 ft-tertiary fw-medium text-gray-900 dark:text-white text-opacity-60 d-none md:d-flex z-1">
                                <div>
                                    <div class="post-date hstack gap-narrow">
                                        <span title="${dateTitle}">${dateText}</span>
                                    </div>
                                </div>
                                <div>
                                    <a href="${postUrl}#comment-form" class="post-comments text-none hstack gap-narrow" title="Comments">
                                        <i class="icon-narrow unicon-chat" title="Comments"></i>
                                        <span title="Comments">${post.comment || 0}</span>
                                    </a>
                                </div>
                                <div title="Views">
                                    <i class="bi bi-eye fs-5"></i>
                                    <span>${post.view_count || 0}</span>
                                </div>
                                <div title="Reaction">
                                    <i class="bi bi-heart-fill ms-1"></i>
                                    <span>${post.reaction || 0}</span>
                                </div>
                            </div>
                        </div>
                    </article>
                </div>
            </div>`;
    }

    // Bind beforeshow and hide events directly on each topic dropdown using native JS (UIkit events do not bubble)
    document.querySelectorAll('.topic-dropdown').forEach(function (dropdownEl) {
        var dropdown = $(dropdownEl);
        var topicId = dropdown.data('topic-id');
        console.log("Configuring event listeners for topic dropdown ID:", topicId);

        dropdownEl.addEventListener('beforeshow', function () {
            console.log("beforeshow event triggered for topic ID:", topicId);
            if (dropdown.hasClass('loaded') || dropdown.hasClass('loading')) {
                console.log("Dropdown already loaded or loading for topic ID:", topicId);
                return;
            }

            dropdown.addClass('loading');
            console.log("Fetching AJAX posts for topic ID:", topicId);

            activeDropdownXhr = $.ajax({
                url: '/topic-posts/' + topicId,
                type: 'GET',
                dataType: 'json',
                success: function (response) {
                    console.log("Successfully fetched posts for topic ID:", topicId);
                    if (response.success && response.posts) {
                        var html = '';
                        response.posts.forEach(function (post) {
                            html += buildTopicDropdownPostHtml(post);
                        });
                        dropdown.find('.dropdown-loader').remove();
                        dropdown.find('.dropdown-content-wrapper').html(html);
                        dropdown.removeClass('loading').addClass('loaded');
                        if (window.lazyLoadElements) {
                            window.lazyLoadElements();
                        }
                    }
                },
                error: function (xhr, status, error) {
                    if (status !== 'abort') {
                        console.error("Error fetching posts for topic ID " + topicId + ":", error);
                        dropdown.removeClass('loading');
                    } else {
                        console.log("AJAX request aborted for topic ID:", topicId);
                    }
                }
            });
        });

        dropdownEl.addEventListener('hide', function () {
            console.log("hide event triggered for topic ID:", topicId);
            if (dropdown.hasClass('loading') && !dropdown.hasClass('loaded') && activeDropdownXhr) {
                console.log("Aborting pending AJAX request for topic ID:", topicId);
                activeDropdownXhr.abort();
                activeDropdownXhr = null;
                dropdown.removeClass('loading');
            }
        });
    });

    // Initialize Lazy Loading for Swiper sliders
    window.initLazySliderLoad = function (options) {
        var swiperEl = document.querySelector(options.swiperId);
        if (!swiperEl) {
            console.warn("Swiper container element not found:", options.swiperId);
            return;
        }

        var swiperInstance = swiperEl.swiper;
        var nextButton = $(options.nextButtonSelector);
        var hasMore = true;
        var isLoading = false;

        if (!swiperInstance) {
            console.log("Swiper instance not found for " + options.swiperId + ", retrying...");
            // Retry in case Swiper initialization is deferred
            setTimeout(function () {
                window.initLazySliderLoad(options);
            }, 100);
            return;
        }

        console.log("Swiper instance successfully connected for", options.swiperId);

        function fetchNextChunk() {
            if (!hasMore || isLoading) {
                return;
            }

            console.log("Fetching next chunk of slides for", options.swiperId);
            isLoading = true;
            $(swiperEl).addClass('loading-slides');
            var currentSlidesCount = swiperInstance.slides.length;
            var displayedIds = [];
            $(swiperEl).find('.swiper-slide').each(function () {
                var id = $(this).attr('data-post-id');
                if (id) {
                    displayedIds.push(id);
                }
            });

            $.ajax({
                url: options.ajaxUrl,
                type: 'GET',
                data: {
                    offset: currentSlidesCount,
                    displayed_ids: displayedIds
                },
                dataType: 'json',
                success: function (response) {
                    console.log("Slides batch fetched successfully for", options.swiperId, response);
                    if (response.success && response.posts) {
                        var slidesHtml = '';
                        response.posts.forEach(function (post) {
                            if (options.swiperId === '#most-read-swiper') {
                                slidesHtml += buildMostReadSlideHtml(post);
                            } else if (options.swiperId === '#web-stories-swiper') {
                                slidesHtml += buildStorySlideHtml(post);
                            } else if (options.swiperId === '#top-posts-swiper') {
                                slidesHtml += buildTopPostSlideHtml(post);
                            } else if (options.swiperId === '#followed-channels-swiper') {
                                slidesHtml += buildFollowedChannelSlideHtml(post);
                            }
                        });
                        swiperInstance.appendSlide(slidesHtml);
                        swiperInstance.update();
                        hasMore = response.has_more;
                        if (window.lazyLoadElements) {
                            window.lazyLoadElements();
                        }
                    }
                    isLoading = false;
                    $(swiperEl).removeClass('loading-slides');
                    if (!hasMore) {
                        console.log("No more slides available for", options.swiperId);
                        nextButton.addClass('disabled');
                    }
                },
                error: function (xhr, status, error) {
                    console.error("Error fetching slides for " + options.swiperId + ":", error);
                    isLoading = false;
                    $(swiperEl).removeClass('loading-slides');
                }
            });
        }

        // Fetch when reaching near the end during swipes/drags
        swiperInstance.on('slideChange', function () {
            fetchNextChunk();
        });

        // Fetch when clicking next near the end of loaded slides
        nextButton.on('click', function () {
            fetchNextChunk();
        });
    };

    // Instantiate for Most Read, Web Stories, Top Posts, and Followed Channels sliders
    window.initLazySliderLoad({
        swiperId: '#most-read-swiper',
        nextButtonSelector: '#most-read-swiper ~ * .nav-next, #most-read-swiper ~ .nav-next',
        ajaxUrl: '/most-read-remaining'
    });

    window.initLazySliderLoad({
        swiperId: '#web-stories-swiper',
        nextButtonSelector: '#web-stories-swiper .swiper-next, #web-stories-swiper ~ * .swiper-next',
        ajaxUrl: '/web-stories-remaining'
    });

    window.initLazySliderLoad({
        swiperId: '#top-posts-swiper',
        nextButtonSelector: '#top-posts-swiper ~ * .nav-next, #top-posts-swiper ~ .nav-next',
        ajaxUrl: '/top-posts-remaining'
    });

    window.initLazySliderLoad({
        swiperId: '#followed-channels-swiper',
        nextButtonSelector: '#followed-channels-swiper ~ * .nav-next, #followed-channels-swiper ~ .nav-next',
        ajaxUrl: '/followed-channels-remaining'
    });
});
// <><><><><><> END DYNAMIC AJAX NAVBAR DROPDOWNS & CAROUSELS <><><><><><><><>
