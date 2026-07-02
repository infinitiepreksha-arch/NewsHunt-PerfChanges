/* Users Card rendering  */
$(document).ready(function () {
    let currentPage = 1;
    const usersPerPage = 8;
    let searchQuery = '';
    function loadUsers(page = 1, query = '') {
        const baseUrl = $('#userCards').data('url');
        if (!baseUrl) {
            return;
        }
        const userDataUrl = `${baseUrl}/${page}`;
        $.ajax({
            url: userDataUrl,
            method: 'GET',
            dataType: 'json',
            data: { search: query, status: $('#user_status').val() },
            success: function (data) {
                const users = Array.isArray(data.data) ? data.data : [];
                const totalUsers = data.total || 0;
                const $container = $('#userCards');
                const $userCount = $('#userCount');
                const isDemos = $('#isDemoModel').val();
                const $pagination = $('#pagination');
                const emptyimg = window.location.origin + '/assets/images/faces/2.jpg';
                $container.empty();

                if (users.length === 0 && page === 1) {
                    $userCount.text('No users found');
                } else {
                    $userCount.text(
                        `${(page - 1) * usersPerPage + 1}-${Math.min(page * usersPerPage, totalUsers)} ${trans('OF')} ${totalUsers} ${trans('PEOPLE')}`
                    );

                    users.forEach(user => {
                        let buttonsHtml = '';
                        let buttonHtml = '';
                        let emailData;
                        let mobileData;
                        const permDiv = document.getElementById('user-permissions');
                        const userPermissions = {
                            updateUser: permDiv.dataset.updateUser === "1",
                            deleteUser: permDiv.dataset.deleteUser === "1",
                            updateStatusUser: permDiv.dataset.updateStatusUser === "1"
                        };

                        if (user.deleted_at == null) {
                            switch (user.status) {
                                case 'all_users':
                                case 'active':
                                    if (userPermissions.updateUser) {
                                        buttonsHtml += `
                                    <a href="#" class="btn btn-outline-secondary d-flex align-items-center" 
                                       data-bs-target='#userEditModal' data-bs-toggle='modal' 
                                       data-id="${user.id || ''}" data-name="${user.name || ''}" data-email="${user.email || ''}"
                                       data-phone="${user.mobile || ''}" data-status="${user.status || ''}" data-country-code="${user.country_code || ''}" 
                                       data-profile="${user.profile || emptyimg}" title='Edit'>
                                        <i class='fa fa-pen'></i> &nbsp; ${trans('EDIT')}
                                    </a>`;
                                    }
                                    if (userPermissions.deleteUser) {
                                        buttonsHtml += `
                                    <a href="users/${user.id || '#'}" class="btn btn-outline-danger d-flex align-items-center delete-form user-delete-form-reload">
                                        <i class='fa fa-trash'></i> &nbsp; ${trans('DELETE')}
                                    </a>`;
                                    }

                                    if (user.is_blocked) {
                                        buttonsHtml += `
                                    <a href="javascript:void(0);" class="btn btn-outline-success d-flex align-items-center unblock-user" 
                                       data-id="${user.id || ''}" title='Unblock'>
                                        <i class='fa fa-unlock'></i> &nbsp; ${trans('UNBLOCK')}
                                    </a>`;
                                    } else {
                                        buttonsHtml += `
                                    <a href="javascript:void(0);" class="btn btn-outline-warning d-flex align-items-center block-user-btn" 
                                       data-id="${user.id || ''}" title='Block'>
                                        <i class='fa fa-ban'></i> &nbsp; ${trans('BLOCK')}
                                    </a>`;
                                    }
                                    break;

                                case 'inactive':
                                    if (userPermissions.updateStatusUser) {
                                        buttonHtml = `
                                    <a href="#" class="btn btn-outline-secondary d-flex align-items-center" 
                                       data-bs-target='#userEditModal' data-bs-toggle='modal' 
                                       data-id="${user.id || ''}" data-name="${user.name || ''}" data-email="${user.email || ''}"
                                       data-phone="${user.mobile || ''}"  data-status="${user.status || ''}" data-country-code="${user.country_code || ''}" 
                                       data-profile="${user.profile || emptyimg}" title='Edit'>
                                        <i class='fa fa-pen'></i> &nbsp; ${trans('Make it Active')}
                                    </a>`;
                                    }
                                    break;

                                default:
                                    if (userPermissions.updateStatusUser) {
                                        buttonsHtml = `
                                    <a href="admin/users/${user.id || '#'}" class="btn btn-outline-warning d-flex align-items-center retrive-delete delete-form-reload">
                                        <i class='fa fa-undo'></i> &nbsp; ${trans('RECOVER')}
                                    </a>`;
                                    }
                            }
                        } else {
                            if (userPermissions.updateStatusUser) {
                                buttonHtml = `
                            <a href="admin/users/${user.id || '#'}" class="btn btn-outline-warning d-flex align-items-center retrive-delete delete-form-reload">
                                <i class='fa fa-undo'></i> &nbsp; ${trans('RECOVER')}
                            </a>`;
                            }
                        }

                        // Email
                        if (user.email) {
                            if (isDemos == 'demo_off') {
                                emailData = `<div class="text-muted mb-1">${trans('EMAIL')}: ${user.email}</div>`;
                            } else {
                                emailData = `<div class="text-muted mb-1">${trans('EMAIL')}: adxxxxx@gmail.com</div>`;
                            }
                        } else {
                            emailData = `<div class="text-muted mb-1">${trans('EMAIL')}: Not Provided</div>`;
                        }

                        // Phone
                        if (user.mobile) {
                            if (isDemos == 'demo_off') {
                                mobileData = `<div class="text-muted">${trans('PHONE')}: ${user.country_code || ''} ${user.mobile}</div>`;
                            } else {
                                mobileData = `<div class="text-muted">${trans('PHONE')}: ${user.country_code || ''}XXXXXXXXXX</div>`;
                            }
                        } else {
                            mobileData = `<div class="text-muted">${trans('PHONE')}: Not Provided</div>`;
                        }

                        const cardHtml = `
                        <div class="col-md-6 col-lg-3 mb-4">
                            <div class="card pull-effect">
                                <div class="card-body p-4 text-center">
                                    <div class="avatar avatar-xl mb-3 mx-auto d-flex justify-content-center align-items-center">
                                        <img src="${user.profile || emptyimg}" class="img-fluid-avtar">
                                    </div>
                                    <h3 class="m-0 mb-2">
                                        <a href="#" class="text-decoration-none">${user.name || 'No Name'}</a>
                                    </h3>
                                    <div class="mb-3">
                                        ${emailData}
                                        ${mobileData}
                                        <div class="text-muted" data-status="${user.status}">
                                            ${trans('STATUS')}: 
                                            ${user.deleted_at == null
                                ? user.is_blocked
                                    ? `<span class="text-danger">Blocked (${user.block_type})</span>`
                                    : user.status == 'active'
                                        ? `<span class="text-success">Active</span>`
                                        : `<span class="text-muted">Inactive</span>`
                                : `<span class="text-danger">Deleted</span>`}
                                        </div>
                                    </div>
                                    <div class="mb-3">
                                        ${user.channel_count ? `<div class="text-muted">Channels Followed: <span id="follow-count">${user.channel_count}</span></div>` : ''}
                                    </div>
                                    <div class="d-flex justify-content-between mt-3">
                                        ${buttonsHtml || ''}
                                    </div>
                                        ${buttonHtml || ''}
                                </div>
                            </div>
                        </div>`;

                        $container.append(cardHtml);
                    });
                }

                // Pagination
                $pagination.empty();
                const totalPages = Math.ceil(totalUsers / usersPerPage);
                const createPageItem = (page, label = page, active = false, disabled = false) => `
                <li class="page-item ${active ? 'active' : ''} ${disabled ? 'disabled' : ''}">
                    <a class="page-link" href="javascript:void(0)" data-page="${page}">${label}</a>
                </li>
            `;

                let paginationHtml = '';
                paginationHtml += createPageItem(currentPage - 1, trans('PREVIOUS'), false, currentPage === 1);

                if (totalPages <= 5) {
                    for (let i = 1; i <= totalPages; i++) {
                        paginationHtml += createPageItem(i, i, currentPage === i);
                    }
                } else {
                    if (currentPage <= 3) {
                        for (let i = 1; i <= 3; i++) {
                            paginationHtml += createPageItem(i, i, currentPage === i);
                        }
                        paginationHtml += '<li class="page-item disabled"><span class="page-link">...</span></li>';
                        paginationHtml += createPageItem(totalPages);
                    } else if (currentPage >= totalPages - 2) {
                        paginationHtml += createPageItem(1);
                        paginationHtml += '<li class="page-item disabled"><span class="page-link">...</span></li>';
                        for (let i = totalPages - 2; i <= totalPages; i++) {
                            paginationHtml += createPageItem(i, i, currentPage === i);
                        }
                    } else {
                        paginationHtml += createPageItem(1);
                        paginationHtml += '<li class="page-item disabled"><span class="page-link">...</span></li>';
                        for (let i = currentPage - 1; i <= currentPage + 1; i++) {
                            paginationHtml += createPageItem(i, i, currentPage === i);
                        }
                        paginationHtml += '<li class="page-item disabled"><span class="page-link">...</span></li>';
                        paginationHtml += createPageItem(totalPages);
                    }
                }
                paginationHtml += createPageItem(currentPage + 1, trans('NEXT'), false, currentPage === totalPages || users.length === 0);

                $pagination.html(paginationHtml);
            },
            error: function (error) {
            }
        });
    }

    loadUsers();

    $('#searchUser').on('input', function () {
        searchQuery = $(this).val().toLowerCase();
        currentPage = 1;
        loadUsers(currentPage, searchQuery);
    });

    $('#pagination').on('click', '.page-link', function (e) {
        e.preventDefault();
        const page = parseInt($(this).data('page'));
        if (!isNaN(page) && page > 0) {
            currentPage = page;
            loadUsers(currentPage, searchQuery);
        }
    });

    $('#user_status').on('change', function () {
        currentPage = 1;
        loadUsers(currentPage, searchQuery);
    });

    /********* User Data ***********/
    /* Store User */
    /* Store User - Fixed Version */

    // Global variable to store iti instance
    let iti;

    function initializeIntlTelInput() {
        var input = document.querySelector("#phone");
        if (!input) return;

        // Destroy existing instance if it exists
        if (iti) {
            iti.destroy();
        }

        iti = window.intlTelInput(input, {
            utilsScript: "https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.8/js/utils.js",
            initialCountry: 'auto',
            geoIpLookup: function (success, failure) {
                success('in'); // Default to India
            },
            separateDialCode: true,
            nationalMode: false, // Allow full international format
        });

        // Add country change listener
        input.addEventListener("countrychange", function () {
            var fullNumber = iti.getNumber();
            input.value = fullNumber;
            // Clear any existing phone errors when country changes
            clearFieldError('phone');
        });

        // Add real-time validation on blur
        input.addEventListener('blur', function () {
            validatePhone();
        });

        // Clear error on input
        input.addEventListener('input', function () {
            clearFieldError('phone');
        });
    }

    // Initialize on page load
    initializeIntlTelInput();

    // Reinitialize on theme change (remove duplicate listeners)
    document.addEventListener('themeChanged', function () {
        initializeIntlTelInput();
    });

    // Remove the problematic phone input filter that was showing error on every keystroke
    // $('#phone').on('input', function () {
    //     this.value = this.value.replace(/[^0-9]/g, '');
    //     displayError('phone', 'Accept only number value');
    // });

    // Utility functions for error handling
    function displayError(field, message) {
        var errorContainer = $(`#${field}-error-message`);
        if (errorContainer.length) {
            errorContainer.text(message)
                .removeClass('d-none')
                .show();
        }
        // Also add is-invalid class to the field
        $(`#${field}, #add-user-${field}, #add-${field}`).removeClass('is-valid').addClass('is-invalid');
    }

    function clearFieldError(field) {
        var errorContainer = $(`#${field}-error-message`);
        if (errorContainer.length) {
            errorContainer.addClass('d-none').hide().text('');
        }
        $(`#${field}, #add-user-${field}, #add-${field}`).removeClass('is-invalid');
    }

    function clearErrors() {
        $('.text-danger').addClass('d-none').hide().text('');
        $('.form-control, .form-select').removeClass('is-invalid is-valid');
    }

    function validatePhone() {
        if (!iti) return true;

        if (!iti.isValidNumber()) {
            displayError('phone', 'Please enter a valid phone number for the selected country.');
            return false;
        } else {
            clearFieldError('phone');
            $('#phone').addClass('is-valid');
            return true;
        }
    }

    function validateStatus() {
        var status = $('#add-user-status').val();
        if (!status) {
            displayError('status', 'Status is required');
            return false;
        } else {
            clearFieldError('status');
            $('#add-user-status').addClass('is-valid');
            return true;
        }
    }

    function validateProfile() {
        var fileInput = $('#add-user-profile-img')[0];

        // Check if file is selected (if it's required)
        if (fileInput.files.length === 0) {
            displayError('profile', 'Profile image is required');
            return false;
        }

        // Validate file type
        const file = fileInput.files[0];
        const validTypes = ['image/jpeg', 'image/png', 'image/svg+xml'];

        if (!validTypes.includes(file.type)) {
            displayError('profile', 'Please select a valid image file (JPG, PNG, SVG).');
            return false;
        }

        // Validate file size (optional - e.g., max 5MB)
        const maxSize = 5 * 1024 * 1024; // 5MB
        if (file.size > maxSize) {
            displayError('profile', 'File size must be less than 5MB.');
            return false;
        }

        clearFieldError('profile');
        $('#add-user-profile-img').addClass('is-valid');
        return true;
    }

    // Form submission handler
    $('#user-add-form').on('submit', function (e) {
        e.preventDefault();
        clearErrors();

        var isValid = true;

        if (!validatePhone()) isValid = false;
        if (!validateStatus()) isValid = false;
        if (!validateProfile()) isValid = false;

        // If validation fails, stop here
        if (!isValid) {
            return false;
        }

        // Prepare form data
        let formElement = $(this);
        let formData = new FormData(this);

        // Set the full phone number
        if (iti && iti.getNumber) {
            formData.set('phone', iti.getNumber());
        }

        // Show loading state
        const submitBtn = formElement.find('button[type="submit"]');
        const originalText = submitBtn.text();
        submitBtn.prop('disabled', true).text('Saving...');

        $.ajax({
            url: formElement.attr('action'),
            type: formElement.attr('method'),
            data: formData,
            contentType: false,
            processData: false,

            success: function (response) {
                $("#userCreateModal").modal("hide");
                $("#user-add-form")[0].reset();

                if (response.status === 'success') {
                    showSuccessToast(response.message);
                } else if (response.status === 'error') {
                    showErrorToast(response.message);
                }
                clearErrors();
                loadUsers();
                setTimeout(function () {
                    location.reload();
                }, 2000);
            },
            error: function (xhr) {
                let errors = xhr.responseJSON?.errors;
                if (errors) {
                    $.each(errors, function (key, messages) {
                        let errorMessage = messages[0];
                        displayError(key, errorMessage);
                    });
                } else {
                    alert('An error occurred. Please try again.');
                }
            },
            complete: function () {
                // Restore button state
                submitBtn.prop('disabled', false).text(originalText);
            }
        });
    });

    // Real-time validation listeners
    $('#add-user-status').on('change', validateStatus);
    $('#add-user-profile-img').on('change', validateProfile);

    // Clear errors on input
    $('#add-user-name, #add-user-email').on('input', function () {
        const fieldName = $(this).attr('id').replace('add-user-', '').replace('add-', '');
        clearFieldError(fieldName);
    });

    $('#password, #password-confirm').on('input', function () {
        const fieldName = $(this).attr('id');
        clearFieldError(fieldName);
    });

    // File preview functionality
    $('#add-user-profile-img').on('change', function () {
        const file = this.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function (e) {
                $('#add-user-profile-preview').attr('src', e.target.result);
            }
            reader.readAsDataURL(file);
        }
    });

    // Updated displayError function to properly show error messages
    function displayError(field, message) {
        var errorContainer = $(`#${field}-error-message`);
        if (errorContainer.length) {
            errorContainer.text(message)
                .removeClass('d-none')  // Remove Bootstrap's d-none class
                .show();                // jQuery show method
        }
    }

    // Updated clearErrors function to properly hide error messages
    function clearErrors() {
        $('.text-danger').addClass('d-none').hide().text(''); // Clear text and hide
        $('.form-control').removeClass('is-invalid is-valid'); // Reset validation classes
    }

    // Your existing form submission handler with improved error handling
    $('#user-edit-form').on('submit', function (e) {
        e.preventDefault();
        clearErrors();

        var isValid = true;
        var iti = window.iti; // Make sure iti is properly referenced

        // Phone validation
        if (iti && iti.getNumber) {
            var fullPhoneNumber = iti.getNumber();

            if (!iti.isValidNumber()) {
                $('#edit-phone').removeClass('is-valid').addClass('is-invalid'); // Note: using edit-phone ID from HTML
                displayError('phone', 'Please enter a valid phone number for the selected country.');
                isValid = false;
            } else {
                $('#edit-phone').removeClass('is-invalid').addClass('is-valid');
            }
        } else {
            // Fallback if intl-tel-input is not initialized
            var phoneValue = $('#edit-phone').val().trim();
            if (phoneValue === '') {
                $('#edit-phone').removeClass('is-valid').addClass('is-invalid');
                displayError('phone', 'Phone number is required.');
                isValid = false;
            }
        }

        // Name validation
        var name = $('#edit-user-name').val().trim();
        if (name === '') {
            $('#edit-user-name').removeClass('is-valid').addClass('is-invalid');
            displayError('name', 'Name is required.');
            isValid = false;
        } else {
            $('#edit-user-name').removeClass('is-invalid').addClass('is-valid');
        }

        // File validation
        var fileInput = $('#user-profile-img')[0];
        if (fileInput.files.length > 0) {
            const file = fileInput.files[0];
            const validTypes = ['image/jpeg', 'image/png', 'image/svg+xml'];

            if (!validTypes.includes(file.type)) {
                displayError('user-profile', 'Please select a valid image file (JPG, PNG, SVG).');
                $('#user-profile-img').removeClass('is-valid').addClass('is-invalid');
                isValid = false;
            } else {
                $('#user-profile-img').removeClass('is-invalid').addClass('is-valid');
            }
        }

        // If validation fails, stop here and show errors
        if (!isValid) {
            return false;
        }

        // Proceed with AJAX submission
        let formElement = $(this);
        let formData = new FormData(this);

        // Set the full phone number if intl-tel-input is available
        if (iti && iti.getNumber) {
            formData.set('phone', iti.getNumber());
        }

        $.ajax({
            url: formElement.attr('action'),
            type: formElement.attr('method'),
            data: formData,
            contentType: false,
            processData: false,
            success: function (response) {
                $('#userEditModal').modal('hide');
                showSuccessToast(response.message);
                $('#user-edit-form')[0].reset();
                clearErrors();
                loadUsers();
            },
            error: function (xhr) {
                let errors = xhr.responseJSON?.errors;
                if (errors) {
                    $.each(errors, function (key, messages) {
                        let errorMessage = messages[0];
                        displayError(key, errorMessage);
                        $(`#${key}, #edit-${key}`).removeClass('is-valid').addClass('is-invalid');
                    });
                } else {
                    alert('An error occurred. Please try again.');
                }
            }
        });
    });


    // Initialize intl-tel-input for edit form
    function initializeIntlTelInputEdit() {
        var input = document.querySelector("#edit-phone"); // Match the ID from your HTML
        if (input) {
            // Destroy existing instance if it exists
            if (window.iti) {
                window.iti.destroy();
            }

            window.iti = window.intlTelInput(input, {
                utilsScript: "https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.8/js/utils.js",
                initialCountry: 'auto',
                geoIpLookup: function (success, failure) {
                    success('in'); // Default to India
                },
                separateDialCode: true,
                nationalMode: false,
            });

            // Add real-time validation
            input.addEventListener('blur', function () {
                if (window.iti.isValidNumber()) {
                    $(input).removeClass('is-invalid').addClass('is-valid');
                    $('#phone-error-message').addClass('d-none').hide();
                } else {
                    $(input).removeClass('is-valid').addClass('is-invalid');
                    displayError('phone', 'Please enter a valid phone number for the selected country.');
                }
            });

            // Clear error on input
            input.addEventListener('input', function () {
                $('#phone-error-message').addClass('d-none').hide();
                $(input).removeClass('is-invalid');
            });
        }
    }

    // Call the initialization function
    initializeIntlTelInputEdit();
    /* Retrieve the deleted User */
    function showSweetAlertConfirmRecover(url, method, opt, options = {}) {
        Swal.fire({
            title: opt.title,
            text: opt.text,
            icon: opt.icon,
            showCancelButton: opt.showCancelButton,
            confirmButtonColor: opt.confirmButtonColor,
            cancelButtonColor: opt.cancelButtonColor,
            confirmButtonText: opt.confirmButtonText,
            cancelButtonText: opt.cancelButtonText,
            customClass: {
                popup: 'dark:bg-black dark:text-white'
            }
        }).then((result) => {
            if (result.isConfirmed) {
                retriveAjaxRequest(method, url, options.data || null, null,
                    function successCallback(response) {
                        showSuccessToast(response.message);
                        opt.successCallBack(response);
                        loadUsers();
                    },
                    function errorCallback(response) {
                        showErrorToast(response.message);
                        opt.errorCallBack(response);
                    }
                );
            }
        });
    }

    function showRetrivePopupModal(url, options = {}) {
        let opt = {
            title: trans("Are you sure?"),
            text: trans("You won't get this user back."),
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: "#3085d6",
            cancelButtonColor: "#d33",
            confirmButtonText: trans("Yes, Recover!"),
            cancelButtonText: trans("Cancel"),
            successCallBack: function () { },
            errorCallBack: function (response) { },
            ...options,
        };
        showSweetAlertConfirmRecover(url, "POST", opt);
    }

    $(document).on("click", ".retrive-delete", function (e) {
        e.preventDefault();
        let userId = $(this).attr("href").split("/").pop();
        showRetrivePopupModal("/admin/users/" + userId + "/recover", {
            successCallBack: function () {
                $("#table_list").bootstrapTable("refresh");
            },
            errorCallBack: function (response) {
                showErrorToast(response.message);
            },
        });
    });

    function retriveAjaxRequest(type, url, data, beforeSendCallback = null, successCallback = null, errorCallback = null, finalCallback = null, processData = false) {
        if (!["post"].includes(type.toLowerCase())) {
            type = "POST";
        }

        $.ajax({
            type: type,
            url: url,
            data: data,
            cache: false,
            processData: processData,
            contentType: data instanceof FormData ? false : "application/json",
            dataType: 'json',
            beforeSend: function () {
                if (beforeSendCallback) {
                    beforeSendCallback();
                }
            },
            success: function (data) {
                if (!data.error) {
                    if (successCallback) {
                        successCallback(data);
                    }
                } else {
                    if (errorCallback) {
                        errorCallback(data);
                    }
                }
                if (finalCallback) {
                    finalCallback(data);
                }
            },
            error: function (jqXHR) {
                if (jqXHR.responseJSON) {
                    showErrorToast(jqXHR.responseJSON.message);
                }
                if (finalCallback) {
                    finalCallback();
                }
            }
        });
    }

    $(document).on('click', '.user-delete-form-reload', function (e) {
        e.preventDefault();
        showDeletePopupModal($(this).attr('href'), {
            successCallBack: function () {
                setTimeout(() => {
                    loadUsers();
                }, 1000);
            }
        })
    })

    // Block User Logic
    $(document).on('click', '.block-user-btn', function () {
        let userId = $(this).data('id');
        $('#user-block-form').attr('action', `/admin/users/${userId}/block`);
        $('#userBlockModal').modal('show');
    });

    $('#user-block-form').on('submit', function (e) {
        e.preventDefault();
        let formData = new FormData(this);
        let url = $(this).attr('action');

        $.ajax({
            url: url,
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function (response) {
                $('#userBlockModal').modal('hide');
                showSuccessToast(response.message);
                loadUsers();
            },
            error: function (xhr) {
                let errors = xhr.responseJSON?.errors;
                if (errors) {
                    $.each(errors, function (key, messages) {
                        showErrorToast(messages[0]);
                    });
                } else {
                    showErrorToast('An error occurred.');
                }
            }
        });
    });

    $(document).on('click', '.unblock-user', function () {
        let userId = $(this).data('id');
        Swal.fire({
            title: trans('Are you sure?'),
            text: trans('You want to unblock this user!'),
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: trans('Yes, Unblock!'),
            cancelButtonText: trans('Cancel')
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: `/admin/users/${userId}/unblock`,
                    type: 'POST',
                    data: { _token: $('input[name="_token"]').val() },
                    success: function (response) {
                        showSuccessToast(response.message);
                        loadUsers();
                    },
                    error: function () {
                        showErrorToast('An error occurred.');
                    }
                });
            }
        });
    });
});
