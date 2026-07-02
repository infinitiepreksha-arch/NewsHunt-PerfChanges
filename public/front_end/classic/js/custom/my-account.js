

/* Unlike Post */
$(document).ready(function () {

    function unLikePost(postId, button) {
        $.ajax({
            url: '/posts/favorite',
            method: 'POST',
            contentType: 'application/json',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value,
                'Accept': 'application/json'
            },
            data: JSON.stringify({ id: postId }),
            success: function (response) {
                if (response.status === '0') {
                    iziToast.success({
                        title: response.message,
                        position: 'topCenter',
                    });
                    button.closest('#postRender').remove();
                    const element = $('#hide-div').find('#postRender').length

                    if (element == 0) {
                        $('.nav-pagination').remove();
                        $('.hide-div').remove();
                        $('#empty-state').removeClass('d-none');
                    }
                } else {
                    iziToast.success({
                        title: response.message,
                        position: 'topCenter',
                    });
                }
            },
            error: function (xhr) {
                console.log(xhr.error);
            }
        });
    }

    $(document).on('click', '.unlike-post-btn', function (event) {
        event.preventDefault();
        const postId = $(this).data('post-id');
        Swal.fire({
            title: 'Are you sure?',
            text: "You won't be able to revert this!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Remove',
            customClass: {
                popup: 'dark:bg-black dark:text-white'
            }
        }).then((result) => {
            if (result.isConfirmed) {
                unLikePost(postId, $(this));
            }
        });
    });
});

/* Channel Unsubscribe */
$(document).ready(function () {
    function followChannel(channelId, button) {
        $.ajax({
            url: '/follow/' + channelId,
            method: 'GET',
            success: function (response) {
                if (!response.error) {
                    iziToast.success({
                        title: response.message,
                        position: 'topCenter',
                    });
                    button.closest('#postRender').remove();
                    const element = $(html).find('#postRender').length

                    if (element == 0) {
                        $('.nav-pagination').remove();
                        $('.hide-div').remove();
                        $('#empty-state').removeClass('d-none');
                    }
                }
            },
            error: function (xhr) {
            }
        });
    }
    $('.channel-unfollow').on('click', function (event) {
        event.preventDefault();
        const channelId = $(this).data('channel-id');

        Swal.fire({
            title: 'Are you sure?',
            text: "You won't be able to revert this!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Unfollow',
            customClass: {
                popup: 'dark:bg-black dark:text-white'
            }
        }).then((result) => {
            if (result.isConfirmed) {
                followChannel(channelId, $(this));
            }
        });
    });
});

$(document).ready(function () {
    const phoneInput = document.querySelector("#phone_number");

    if (!phoneInput) return;

    const storedCountryCode = phoneInput.dataset.countryCode || "";
    const storedMobile = phoneInput.value || "";

    // Initialize intl-tel-input
    const iti = window.intlTelInput(phoneInput, {
        utilsScript: "https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.8/js/utils.js",
        initialCountry: storedCountryCode ? "" : "auto", // Use auto only when no stored country
        geoIpLookup: function (success, failure) {
            $.get("https://ipinfo.io", function () { }, "jsonp").always(function (resp) {
                const countryCode = (resp && resp.country) ? resp.country.toLowerCase() : "in";
                success(countryCode);
            });
        },
        preferredCountries: ['in', 'us', 'gb'],
        separateDialCode: false, // Changed to false to show dial code in input
        nationalMode: false, // Show international format
        autoPlaceholder: "off",
        formatOnDisplay: true // Format the number as it's displayed
    });

    // Set the phone number after initialization if it exists
    if (storedCountryCode && storedMobile) {
        // Remove any leading + or country code from mobile number
        let cleanMobile = storedMobile.replace(/^\+/, '').replace(new RegExp('^' + storedCountryCode), '');
        const fullNumber = '+' + storedCountryCode + cleanMobile;
        iti.setNumber(fullNumber);
    } else if (storedCountryCode) {
        // If we have a country code but no mobile, just set the dial code
        iti.setNumber('+' + storedCountryCode);
    }

    // Update the input to show only dial code when country changes
    phoneInput.addEventListener('countrychange', function () {
        // Clear the input and show only the dial code
        const dialCode = iti.getSelectedCountryData().dialCode;
        phoneInput.value = '+' + dialCode + ' ';

        // Set cursor position after the dial code
        setTimeout(() => {
            phoneInput.setSelectionRange(phoneInput.value.length, phoneInput.value.length);
        }, 0);
    });

    // Ensure the + sign and country code are always visible
    phoneInput.addEventListener('input', function (e) {
        let value = phoneInput.value;

        // If user deletes the +, add it back
        if (!value.startsWith('+')) {
            const dialCode = iti.getSelectedCountryData().dialCode;
            phoneInput.value = '+' + dialCode + value.replace(/^\+/, '');
        }
    });

    let emailToastShown = false;

    $("#email_profile").off("click").on("click", function (e) {
        e.preventDefault();
        if (!emailToastShown) {
            iziToast.error({
                title: "You cannot change your email.",
                position: "topCenter",
            });
            emailToastShown = true;
            setTimeout(() => { emailToastShown = false; }, 2000);
        }
        $(this).blur();
    });

    // Submit form with server-side validation only
    $('#user-account-form').off('submit').on('submit', function (e) {
        e.preventDefault();
        clearErrors();

        var isValid = true;

        var fullPhoneNumber = iti.getNumber();
        phoneInput.value = fullPhoneNumber;

        if (!iti.isValidNumber()) {
            displayError('phone_number', 'Invalid phone number');
            isValid = false;
        }

        var email = document.querySelector("#email_profile").value.trim();
        if (!validateEmail(email)) {
            displayError('email_profile', 'Invalid email address');
            isValid = false;
        }

        var name = document.querySelector("#user_name").value.trim();
        if (name === "") {
            displayError('user_name', 'The name field is required');
            isValid = false;
        }

        if (!isValid) {
            return;
        }

        // Prepare form data
        const formData = new FormData(this);
        formData.set("phone", iti.getNumber());

        const url = $(this).attr('action');
        const method = $(this).attr('method');

        $.ajax({
            url: url,
            type: method,
            data: formData,
            processData: false,
            contentType: false,
            success: function (response) {
                iziToast.success({
                    title: response.message || "Profile updated successfully",
                    position: 'topCenter',
                });

                // Update the data attributes with new values after successful update
                if (response.country_code && response.mobile) {
                    phoneInput.dataset.countryCode = response.country_code;
                    // Remove any leading + or country code from mobile number
                    let cleanMobile = response.mobile.replace(/^\+/, '').replace(new RegExp('^' + response.country_code), '');
                    const fullNumber = '+' + response.country_code + cleanMobile;
                    iti.setNumber(fullNumber);
                }
            },
            error: function (xhr) {
                if (xhr.responseJSON && xhr.responseJSON.errors) {
                    displayServerErrors(xhr.responseJSON.errors);
                } else if (xhr.responseJSON && xhr.responseJSON.message) {
                    iziToast.error({
                        title: xhr.responseJSON.message,
                        position: 'topCenter',
                    });
                } else {
                    iziToast.error({
                        title: 'Update failed',
                        message: 'An unexpected error occurred. Please try again.',
                        position: 'topCenter',
                    });
                }
            }
        });
    });

    // Utility to display error messages
    function displayError(field, message) {
        var fieldElement = document.querySelector(`#${field}`);
        if (!fieldElement) return;
        var errorContainer = fieldElement.closest('.mb-2').querySelector('.help-block');
        if (errorContainer) {
            errorContainer.innerHTML = `<strong>${message}</strong>`;
            errorContainer.style.display = 'block';
            errorContainer.classList.remove('d-none');
        }
    }

    // Utility to clear all error messages
    function clearErrors() {
        var errorElements = document.querySelectorAll('.help-block');
        errorElements.forEach(el => {
            el.innerHTML = '';
            el.style.display = 'none';
            el.classList.add('d-none');
        });
    }

    // Utility to validate email format
    function validateEmail(email) {
        var re = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        return re.test(email);
    }

    // Utility to display server-side validation errors
    function displayServerErrors(errors) {
        Object.keys(errors).forEach(field => {
            let fieldId = field;
            if (field === 'name') fieldId = 'user_name';
            if (field === 'phone') fieldId = 'phone_number';
            if (field === 'email') fieldId = 'email_profile';

            displayError(fieldId, errors[field][0]);
        });
    }
});

/* User confirm delete */
$('#user-delete-account').on('click', function (event) {
    Swal.fire({
        title: 'Are you sure?',
        text: "You won't be able to revert this!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Delete Account',
        customClass: {
            popup: 'dark:bg-black dark:text-white'
        }
    }).then((result) => {
        if (result.isConfirmed) {
            deleteUser(event);
        }
    });
});

function deleteUser(event) {
    event.preventDefault();

    $.ajax({
        url: '/delete-account',
        type: 'POST',
        data: {
            _token: $('meta[name="csrf-token"]').attr('content')
        },
        success: function (response) {
            if (response.error === false) {
                window.location.href = window.location.origin;
            } else {
                alert(response.message || "An error occurred while deleting the account.");
            }
        },
        error: function (xhr) {
        }
    });
}

document.addEventListener('DOMContentLoaded', function () {
    const filterSelect = document.getElementById('filter_by_type');
    let bookmarksUrl = "";
    if (filterSelect) {
        bookmarksUrl = filterSelect.dataset.bookmarksUrl || "";
    }
    const contentArea = document.getElementById('hide-div'); // The posts container
    const paginationContainer = document.querySelector('.nav-pagination'); // Pagination
    const panelContainer = document.querySelector('#favorites .panel.text-center'); // Parent for loading states
    const emptyState = document.getElementById('empty-state');

    if (!filterSelect || !contentArea) return;

    // Function to load bookmarks with filters
    function loadBookmarks(type = 'all', page = 1) {
        const url = new URL(bookmarksUrl, window.location.origin);
        url.searchParams.set('type', type);
        if (page > 1) {
            url.searchParams.set('page', page);
        }

        fetch(url.toString())
            .then(response => response.text())
            .then(html => {
                // Parse the new HTML to extract updated sections
                const parser = new DOMParser();
                const doc = parser.parseFromString(html, 'text/html');

                // Extract new posts row
                const newContent = doc.getElementById('hide-div');
                if (newContent) {
                    contentArea.innerHTML = newContent.innerHTML;
                } else {
                    contentArea.innerHTML = '<p>No posts found.</p>';
                }

                // Extract new pagination
                const newPagination = doc.querySelector('.nav-pagination');
                if (newPagination && paginationContainer) {
                    paginationContainer.innerHTML = newPagination.innerHTML;
                }

                // Handle empty state
                const newEmptyState = doc.getElementById('empty-state');
                if (newEmptyState && !newContent) {
                    emptyState.innerHTML = newEmptyState.innerHTML;
                    emptyState.classList.remove('d-none');
                } else {
                    emptyState.classList.add('d-none');
                }

                // Re-bind any event listeners for new content (e.g., pin/unpin, unlike)
                bindPostEvents();
            })
            .catch(error => {
                console.error('Error loading bookmarks:', error);
                contentArea.innerHTML = '<div class="text-center py-4 text-danger">Error loading content. Please try again.</div>';
            });
    }

    // Bind events to dynamically loaded posts (pin, unlike, etc.)
    function bindPostEvents() {
        // Use delegation for pin post so we don't need to re-bind every time
        // but since bindPostEvents is called manually, we keep it consistent.
        // Actually, we'll move delegation out of this function.
    }

    // Delegation for pin post
    $(document).off('click', '.pin-post-btn').on('click', '.pin-post-btn', function (e) {
        e.preventDefault();

        const btn = $(this);
        const postId = this.dataset.postId;
        const url = this.dataset.url;
        const csrf = this.dataset.csrf;

        fetch(url, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': csrf
            },
            body: JSON.stringify({ post_id: postId })
        })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    const icon = btn.find('i');

                    if (data.is_pinned) {
                        icon.removeClass().addClass('bi bi-pin-angle-fill text-primary fs-3');
                        btn.attr('title', 'Unpin Post');
                    } else {
                        icon.removeClass().addClass('bi bi-pin-angle fs-4');
                        btn.attr('title', 'Pin Post');
                    }

                    iziToast.success({
                        title: data.message,
                        position: 'topCenter',
                    });

                    loadBookmarks(filterSelect.value);
                }
            })
            .catch(error => console.error('Error toggling pin:', error));
    });

    // Initial bind for existing content
    bindPostEvents();

    // Listen to filter change
    filterSelect.addEventListener('change', function () {
        const selectedType = this.value;
        loadBookmarks(selectedType);
    });

    // Handle pagination clicks (if dynamic)
    document.addEventListener('click', function (e) {
        if (e.target.closest('.uc-pagination a')) {
            e.preventDefault();
            const link = e.target.closest('a');
            const url = new URL(link.href, window.location.origin);
            const type = filterSelect.value;
            url.searchParams.set('type', type);
            loadBookmarks(type, url.searchParams.get('page'));
        }
    });
});
document.addEventListener("DOMContentLoaded", function () {
    const emailField = document.getElementById("email_profile");
    const originalEmail = emailField.value;

    // typing block
    emailField.addEventListener("keydown", function (e) {
        e.preventDefault();
    });

    // paste block
    emailField.addEventListener("paste", function (e) {
        e.preventDefault();
    });

    // value change thay to revert
    emailField.addEventListener("input", function () {
        this.value = originalEmail;
    });
});