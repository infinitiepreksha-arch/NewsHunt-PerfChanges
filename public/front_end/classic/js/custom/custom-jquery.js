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
                    window.location.reload();
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
                        window.location.reload();
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

