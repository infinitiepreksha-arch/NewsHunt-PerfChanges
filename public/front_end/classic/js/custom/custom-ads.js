// <><><><><><><> START JS FOR UPDATE PROFILE ON SPONSOR ADS PAGE <><><><><><><>
document.addEventListener("DOMContentLoaded", function () {
    const form = document.getElementById("validationEditProfile");
    if (!form) return; // Exit if form not found

    form.addEventListener("submit", function (e) {
        e.preventDefault();

        let url = form.getAttribute("action");
        let formData = new FormData(form);

        document.querySelectorAll('.error-text').forEach(el => el.textContent = "");

        fetch(url, {
            method: "POST",
            headers: {
                "X-CSRF-TOKEN": document.querySelector('input[name="_token"]').value,
                "Accept": "application/json"
            },
            body: formData
        })
            .then(response => response.json())
            .then(data => {
                if (data.errors) {
                    for (let field in data.errors) {
                        let errorSpan = document.querySelector(`.${field}_error`);
                        if (errorSpan) errorSpan.textContent = data.errors[field][0];
                    }
                } else if (data.success) {
                    if (typeof $ !== 'undefined' && $.fn.modal) {
                        $('#validationEditProfileModel').modal('hide');
                    }
                    if (typeof Swal !== 'undefined') {
                        iziToast.success({
                            title: 'Your profile has been successfully updated.',
                            position: 'topRight',
                            timeout: 3000,
                            close: false,
                            progressBar: true,
                            onClosing: function () {
                                window.location.href = data.redirect ?? '/smart-ads';
                            }
                        });
                    } else {
                        window.location.href = data.redirect ?? '/smart-ads';
                    }
                }
            })
            .catch(err => {
                console.error(err);
                iziToast.error({
                    title: 'Error',
                    message: 'Something went wrong. Please try again.',
                    position: 'topRight',
                    timeout: 5000
                });
            });
    });
});
// <><><><><><><> END JS OF UPDATE PROFILE ON SPONSOR ADS PAGE <><><><><><><>


// <><><><><><><> START JS FOR CREATE SPONSOR ADS REQUEST FORM <><><><><><><>

const MAX_FILE_SIZE = 10 * 1024 * 1024; // 10MB
const ALLOWED_TYPES = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif', 'image/webp'];

// Required dimensions for each image type
const REQUIRED_DIMENSIONS = {
    horizontal: {
        width: 1920,
        height: 753
    },
    vertical: {
        width: 740,
        height: 500
    }
};

// Track upload states
const uploadState = {
    horizontal: false,
    vertical: false
};

// Validation rules
const validationRules = {
    name: {
        required: true,
        minLength: 3
    },
    body: {
        required: true,
        minLength: 10
    },
    imageUrl: {
        required: true,
        url: true
    },
    imageAlt: {
        required: true,
        minLength: 3
    },
    contact_name: {
        required: true,
        minLength: 3
    },
    mobile_number: {
        required: true,
        phone: true
    },
    contact_email: {
        required: true,
        email: true
    },
    start_date: {
        required: true
    },
    end_date: {
        required: true
    },
    placements: {
        required: true,
        minSelected: 1
    },
    horizontal_image: {
        required: true
    },
    vertical_image: {
        required: true
    }
};

// DOM Elements cache
const elements = {
    // Form elements
    form: null,
    submitBtn: null,

    // Image elements
    horizontalUploadArea: null,
    horizontalPlaceholder: null,
    horizontalPreview: null,
    horizontalPreviewImage: null,
    horizontalFileName: null,
    horizontalFileSize: null,
    horizontalDimensions: null,
    horizontalErrorMessage: null,
    horizontalFileInput: null,

    verticalUploadArea: null,
    verticalPlaceholder: null,
    verticalPreview: null,
    verticalPreviewImage: null,
    verticalFileName: null,
    verticalFileSize: null,
    verticalDimensions: null,
    verticalErrorMessage: null,
    verticalFileInput: null
};

// Initialize DOM elements
function initializeElements() {
    // Form elements
    elements.form = document.getElementById('ad-form');
    elements.submitBtn = document.getElementById('submit-btn');

    // Horizontal elements
    elements.horizontalUploadArea = document.getElementById('upload-area-horizontal');
    elements.horizontalPlaceholder = document.getElementById('upload-placeholder-horizontal');
    elements.horizontalPreview = document.getElementById('image-preview-horizontal');
    elements.horizontalPreviewImage = document.getElementById('preview-image-horizontal');
    elements.horizontalFileName = document.getElementById('file-name-horizontal');
    elements.horizontalFileSize = document.getElementById('file-size-horizontal');
    elements.horizontalDimensions = document.getElementById('dimensions-horizontal');
    elements.horizontalErrorMessage = document.getElementById('error-message-horizontal');
    elements.horizontalFileInput = document.getElementById('horizontal-image');

    // Vertical elements
    elements.verticalUploadArea = document.getElementById('upload-area-vertical');
    elements.verticalPlaceholder = document.getElementById('upload-placeholder-vertical');
    elements.verticalPreview = document.getElementById('image-preview-vertical');
    elements.verticalPreviewImage = document.getElementById('preview-image-vertical');
    elements.verticalFileName = document.getElementById('file-name-vertical');
    elements.verticalFileSize = document.getElementById('file-size-vertical');
    elements.verticalDimensions = document.getElementById('dimensions-vertical');
    elements.verticalErrorMessage = document.getElementById('error-message-vertical');
    elements.verticalFileInput = document.getElementById('vertical-image');
}

// Validation helper functions
function validateRequired(value, fieldName) {
    if (!value || value.trim() === '') {
        return `${fieldName} is required`;
    }
    return null;
}

function validateMinLength(value, minLength, fieldName) {
    if (value && value.trim().length < minLength) {
        return `${fieldName} must be at least ${minLength} characters`;
    }
    return null;
}

function validateEmail(email) {
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    if (!emailRegex.test(email)) {
        return 'Please enter a valid email address';
    }
    return null;
}

function validatePhone(phone) {
    const phoneRegex = /^[6-9]\d{9}$/; // Indian mobile number format
    if (!phoneRegex.test(phone.replace(/\s+/g, ''))) {
        return 'Please enter a valid 10-digit mobile number';
    }
    return null;
}

function validateURL(url) {
    try {
        new URL(url);
        return null;
    } catch {
        return 'Please enter a valid URL';
    }
}

function validateDate(startDate, endDate) {
    const today = new Date().toISOString().split('T')[0];

    if (startDate < today) {
        return 'Start date cannot be in the past';
    }

    if (endDate < startDate) {
        return 'End date cannot be earlier than start date';
    }

    if (endDate === today) {
        return 'End date cannot be today';
    }

    return null;
}

function validatePlacements() {
    const appPlacements = document.querySelectorAll('input[name="app_ads_placement[]"]:checked');
    const webPlacements = document.querySelectorAll('input[name="web_ads_placement[]"]:checked');

    if (appPlacements.length === 0 && webPlacements.length === 0) {
        return 'Please select at least one ad placement';
    }
    return null;
}

// Show error message for a field
function showFieldError(fieldName, message) {
    // Remove existing error message
    clearFieldError(fieldName);

    const field = document.getElementById(fieldName) || document.querySelector(`[name="${fieldName}"]`);
    if (!field) return;

    // Create error message element
    const errorElement = document.createElement('span');
    errorElement.className = 'text-xs text-red-600 dark:text-red-400 error-message';
    errorElement.textContent = message;
    errorElement.setAttribute('data-field', fieldName);

    // Add error styling to field
    field.classList.add('border-red-500', 'focus:border-red-500');
    field.classList.remove('focus:border-purple-400');

    // Insert error message after the field
    if (field.parentNode) {
        field.parentNode.insertBefore(errorElement, field.nextSibling);
    }
}

// Clear error message for a field
function clearFieldError(fieldName) {
    const field = document.getElementById(fieldName) || document.querySelector(`[name="${fieldName}"]`);
    if (field) {
        // Remove error styling
        field.classList.remove('border-red-500', 'focus:border-red-500');
        field.classList.add('focus:border-purple-400');

        // Remove existing error messages
        const existingErrors = document.querySelectorAll(`[data-field="${fieldName}"]`);
        existingErrors.forEach(error => error.remove());
    }
}

// Show placement error
function showPlacementError(message) {
    clearPlacementError();

    const placementSection = document.querySelector('.mt-6.space-y-6.p-3');
    if (placementSection) {
        const errorElement = document.createElement('div');
        errorElement.className =
            'text-sm text-red-600 dark:text-red-400 mt-2 p-2 bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded placement-error';
        errorElement.textContent = message;

        placementSection.insertBefore(errorElement, placementSection.firstChild);
    }
}

// Clear placement error
function clearPlacementError() {
    const existingError = document.querySelector('.placement-error');
    if (existingError) {
        existingError.remove();
    }
}

// Comprehensive form validation
function validateForm() {
    let isValid = true;
    const errors = [];

    // Clear all existing errors
    document.querySelectorAll('.error-message').forEach(error => error.remove());
    clearPlacementError();

    // Get form values
    const formData = {
        name: document.getElementById('title')?.value?.trim() || '',
        body: document.querySelector('[name="body"]')?.value?.trim() || '',
        imageUrl: document.querySelector('[name="imageUrl"]')?.value?.trim() || '',
        imageAlt: document.querySelector('[name="imageAlt"]')?.value?.trim() || '',
        contact_name: document.getElementById('contact_name')?.value?.trim() || '',
        mobile_number: document.querySelector('[name="mobile_number"]')?.value?.trim() || '',
        contact_email: document.getElementById('contact_email')?.value?.trim() || '',
        start_date: document.getElementById('start_date')?.value || '',
        end_date: document.getElementById('end_date')?.value || ''
    };

    // Validate name
    let error = validateRequired(formData.name, 'Ad Name');
    if (error) {
        showFieldError('title', error);
        isValid = false;
    } else {
        error = validateMinLength(formData.name, 3, 'Ad Name');
        if (error) {
            showFieldError('title', error);
            isValid = false;
        }
    }

    // Validate body/description
    error = validateRequired(formData.body, 'Sponsor Ad Description');
    if (error) {
        showFieldError('body', error);
        isValid = false;
    } else {
        error = validateMinLength(formData.body, 10, 'Sponsor Ad Description');
        if (error) {
            showFieldError('body', error);
            isValid = false;
        }
    }

    // Validate Image URL
    error = validateRequired(formData.imageUrl, 'Image URL');
    if (error) {
        showFieldError('imageUrl', error);
        isValid = false;
    } else {
        error = validateURL(formData.imageUrl);
        if (error) {
            showFieldError('imageUrl', error);
            isValid = false;
        }
    }

    // Validate Contact Name
    error = validateRequired(formData.contact_name, 'Contact Name');
    if (error) {
        showFieldError('contact_name', error);
        isValid = false;
    } else {
        error = validateMinLength(formData.contact_name, 3, 'Contact Name');
        if (error) {
            showFieldError('contact_name', error);
            isValid = false;
        }
    }

    // Validate Mobile Number
    error = validateRequired(formData.mobile_number, 'Mobile Number');
    if (error) {
        showFieldError('mobile_number', error);
        isValid = false;
    } else {
        error = validatePhone(formData.mobile_number);
        if (error) {
            showFieldError('mobile_number', error);
            isValid = false;
        }
    }

    // Validate Contact Email
    error = validateRequired(formData.contact_email, 'Contact Email');
    if (error) {
        showFieldError('contact_email', error);
        isValid = false;
    } else {
        error = validateEmail(formData.contact_email);
        if (error) {
            showFieldError('contact_email', error);
            isValid = false;
        }
    }

    // Validate Start Date
    error = validateRequired(formData.start_date, 'Start Date');
    if (error) {
        showFieldError('start_date', error);
        isValid = false;
    }

    // Validate End Date
    error = validateRequired(formData.end_date, 'End Date');
    if (error) {
        showFieldError('end_date', error);
        isValid = false;
    }

    // Validate date range
    if (formData.start_date && formData.end_date) {
        error = validateDate(formData.start_date, formData.end_date);
        if (error) {
            showFieldError('end_date', error);
            isValid = false;
        }
    }

    // Validate placements
    error = validatePlacements();
    if (error) {
        showPlacementError(error);
        isValid = false;
    }

    // Validate images
    if (!uploadState.horizontal) {
        showError('Please upload a horizontal image (1920×753px)', 'horizontal');
        isValid = false;
    }

    if (!uploadState.vertical) {
        showError('Please upload a vertical image (740×500px)', 'vertical');
        isValid = false;
    }

    return isValid;
}

// Real-time validation for individual fields
function setupRealTimeValidation() {
    // Name validation
    const nameField = document.getElementById('title');
    if (nameField) {
        nameField.addEventListener('blur', function () {
            const value = this.value.trim();
            let error = validateRequired(value, 'Ad Name');
            if (!error) {
                error = validateMinLength(value, 3, 'Ad Name');
            }

            if (error) {
                showFieldError('title', error);
            } else {
                clearFieldError('title');
            }
        });
    }

    // Body validation
    const bodyField = document.querySelector('[name="body"]');
    if (bodyField) {
        bodyField.addEventListener('blur', function () {
            const value = this.value.trim();
            let error = validateRequired(value, 'Ad Body');
            if (!error) {
                error = validateMinLength(value, 10, 'Ad Body');
            }

            if (error) {
                showFieldError('body', error);
            } else {
                clearFieldError('body');
            }
        });
    }

    // Image URL validation
    const imageUrlField = document.querySelector('[name="imageUrl"]');
    if (imageUrlField) {
        imageUrlField.addEventListener('blur', function () {
            const value = this.value.trim();
            let error = validateRequired(value, 'Image URL');
            if (!error) {
                error = validateURL(value);
            }

            if (error) {
                showFieldError('imageUrl', error);
            } else {
                clearFieldError('imageUrl');
            }
        });
    }

    // Image Alt validation
    const imageAltField = document.querySelector('[name="imageAlt"]');
    if (imageAltField) {
        imageAltField.addEventListener('blur', function () {
            const value = this.value.trim();
            let error = validateRequired(value, 'Image Alt');
            if (!error) {
                error = validateMinLength(value, 3, 'Image Alt');
            }

            if (error) {
                showFieldError('imageAlt', error);
            } else {
                clearFieldError('imageAlt');
            }
        });
    }

    // Contact Name validation
    const contactNameField = document.getElementById('contact_name');
    if (contactNameField) {
        contactNameField.addEventListener('blur', function () {
            const value = this.value.trim();
            let error = validateRequired(value, 'Contact Name');
            if (!error) {
                error = validateMinLength(value, 3, 'Contact Name');
            }

            if (error) {
                showFieldError('contact_name', error);
            } else {
                clearFieldError('contact_name');
            }
        });
    }

    // Mobile Number validation
    const mobileField = document.querySelector('[name="mobile_number"]');
    if (mobileField) {
        mobileField.addEventListener('blur', function () {
            const value = this.value.trim();
            let error = validateRequired(value, 'Mobile Number');
            if (!error) {
                error = validatePhone(value);
            }

            if (error) {
                showFieldError('mobile_number', error);
            } else {
                clearFieldError('mobile_number');
            }
        });
    }

    // Email validation
    const emailField = document.getElementById('contact_email');
    if (emailField) {
        emailField.addEventListener('blur', function () {
            const value = this.value.trim();
            let error = validateRequired(value, 'Contact Email');
            if (!error) {
                error = validateEmail(value);
            }

            if (error) {
                showFieldError('contact_email', error);
            } else {
                clearFieldError('contact_email');
            }
        });
    }

    // Placement validation
    document.querySelectorAll('input[name="app_ads_placement[]"], input[name="web_ads_placement[]"]').forEach(
        checkbox => {
            checkbox.addEventListener('change', function () {
                const error = validatePlacements();
                if (error) {
                    showPlacementError(error);
                } else {
                    clearPlacementError();
                }
            });
        });
}


// File Selection Handler
function handleFileSelect(event, type) {
    const file = event.target.files[0];
    if (file) {
        processFile(file, type);
    }
}

// File Processing with comprehensive validation
function processFile(file, type) {

    // Clear previous errors and states
    hideError(type);
    uploadState[type] = false;

    // Validate file type
    if (!ALLOWED_TYPES.includes(file.type)) {
        showError('Please select a valid image file (JPG, PNG, GIF, WebP).', type);
        clearFileInput(type);
        return;
    }

    // Validate file size
    if (file.size > MAX_FILE_SIZE) {
        showError('File size must be less than 10MB.', type);
        clearFileInput(type);
        return;
    }

    // Validate image dimensions
    validateImageDimensions(file, type);
}

// Comprehensive image dimension validation
function validateImageDimensions(file, type) {
    const img = new Image();
    const url = URL.createObjectURL(file);

    img.onload = function () {
        URL.revokeObjectURL(url);

        const width = this.naturalWidth;
        const height = this.naturalHeight;
        const required = REQUIRED_DIMENSIONS[type];


        if (width !== required.width || height !== required.height) {
            showError(
                `Invalid dimensions (${width}×${height}px). Required: ${required.width}×${required.height}px`,
                type
            );
            clearFileInput(type);
            return;
        }

        showImagePreview(file, width, height, type);
        uploadState[type] = true;

    };

    img.onerror = function () {
        URL.revokeObjectURL(url);
        showError('Error reading image file. Please try another file.', type);
        clearFileInput(type);
    };

    img.src = url;
}

// Show Image Preview with enhanced feedback
function showImagePreview(file, width, height, type) {
    const reader = new FileReader();

    reader.onload = function (e) {
        if (elements[`${type}PreviewImage`]) {
            elements[`${type}PreviewImage`].src = e.target.result;
        }

        if (elements[`${type}FileName`]) {
            elements[`${type}FileName`].textContent = file.name;
        }

        if (elements[`${type}FileSize`]) {
            elements[`${type}FileSize`].textContent = formatFileSize(file.size);
        }

        if (elements[`${type}Dimensions`]) {
            elements[`${type}Dimensions`].textContent = `${width}×${height}px`;
        }

        if (elements[`${type}Placeholder`]) {
            elements[`${type}Placeholder`].classList.add('hidden');
        }

        if (elements[`${type}Preview`]) {
            elements[`${type}Preview`].classList.remove('hidden');
        }

        showSuccess(`${type.charAt(0).toUpperCase() + type.slice(1)} image uploaded successfully!`, type);
    };

    reader.onerror = function () {
        showError('Error reading file. Please try again.', type);
        clearFileInput(type);
    };

    reader.readAsDataURL(file);
}

// Remove Image with complete cleanup
function removeImage(event, type) {
    event.stopPropagation();

    clearFileInput(type);

    if (elements[`${type}PreviewImage`]) {
        elements[`${type}PreviewImage`].src = '';
    }

    if (elements[`${type}FileName`]) {
        elements[`${type}FileName`].textContent = '';
    }

    if (elements[`${type}FileSize`]) {
        elements[`${type}FileSize`].textContent = '';
    }

    if (elements[`${type}Dimensions`]) {
        elements[`${type}Dimensions`].textContent = '';
    }

    if (elements[`${type}Placeholder`]) {
        elements[`${type}Placeholder`].classList.remove('hidden');
    }

    if (elements[`${type}Preview`]) {
        elements[`${type}Preview`].classList.add('hidden');
    }

    hideError(type);
    hideSuccess(type);
    uploadState[type] = false;
}

// Clear file input
function clearFileInput(type) {
    if (elements[`${type}FileInput`]) {
        elements[`${type}FileInput`].value = '';
    }
}

// Utility Functions
function formatFileSize(bytes) {
    if (bytes === 0) return '0 Bytes';
    const k = 1024;
    const sizes = ['Bytes', 'KB', 'MB', 'GB'];
    const i = Math.floor(Math.log(bytes) / Math.log(k));
    return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
}

// Error handling
function showError(message, type) {
    const errorElement = elements[`${type}ErrorMessage`];
    if (errorElement) {
        errorElement.textContent = message;
        errorElement.classList.remove('hidden');
    }
}

function hideError(type) {
    const errorElement = elements[`${type}ErrorMessage`];
    if (errorElement) {
        errorElement.classList.add('hidden');
    }
}

function showSuccess(message, type) {
}

function hideSuccess(type) {
    // Hide success messages if implemented
}

// Price calculation function
function calculateTotal() {
    const checkboxes = document.querySelectorAll('.placement-checkbox:checked');
    const startDate = document.getElementById('start_date')?.value;
    const endDate = document.getElementById('end_date')?.value;
    const priceSummary = document.getElementById('price-summary');

    let dailyTotal = 0;
    let selectedPlacements = [];

    checkboxes.forEach(checkbox => {
        const price = parseFloat(checkbox.getAttribute('data-price')) || 0;
        const labelElement = checkbox.closest('label').querySelector('.block');
        const label = labelElement ? labelElement.textContent.trim() : '';
        dailyTotal += price;
        selectedPlacements.push(`${label} (₹${price}/day)`);
    });

    let totalDays = 0;
    let durationText = 'Not selected';

    if (startDate && endDate) {
        const start = new Date(startDate);
        const end = new Date(endDate);

        if (end >= start) {
            totalDays = Math.ceil((end - start) / (1000 * 60 * 60 * 24)) + 1;
            durationText = `${totalDays} day${totalDays > 1 ? 's' : ''} (${startDate} to ${endDate})`;
        } else {
            durationText = 'Invalid date range';
        }
    }

    const totalAmount = dailyTotal * totalDays;

    if (priceSummary) {
        if (checkboxes.length > 0 || (startDate && endDate)) {
            priceSummary.style.display = 'block';

            const placementsDiv = document.getElementById('selected-placements');
            if (placementsDiv) {
                if (selectedPlacements.length > 0) {
                    placementsDiv.innerHTML = selectedPlacements.map(placement =>
                        `<div class="text-gray-700 dark:text-gray-300">${placement}</div>`
                    ).join('');
                } else {
                    placementsDiv.innerHTML = '<span class="text-gray-500">None selected</span>';
                }
            }

            const durationDisplay = document.getElementById('duration-display');
            const dailyRate = document.getElementById('daily-rate');
            const totalAmountDisplay = document.getElementById('total-amount');
            const totalPriceInput = document.getElementById('total_price');
            const dailyPriceInput = document.getElementById('daily_price');
            const totalDaysInput = document.getElementById('total_days');

            if (durationDisplay) durationDisplay.textContent = durationText;
            if (dailyRate) dailyRate.textContent = `₹${dailyTotal}`;
            if (totalAmountDisplay) totalAmountDisplay.textContent = `₹${totalAmount}`;

            if (totalPriceInput) totalPriceInput.value = totalAmount;
            if (dailyPriceInput) dailyPriceInput.value = dailyTotal;
            if (totalDaysInput) totalDaysInput.value = totalDays;
        } else {
            priceSummary.style.display = 'none';
        }
    }
}

// Form submission handler with comprehensive validation
function handleFormSubmission(event) {

    // Run comprehensive validation
    if (!validateForm()) {
        event.preventDefault();

        // Scroll to first error
        const firstError = document.querySelector('.error-message, .placement-error');
        if (firstError) {
            firstError.scrollIntoView({
                behavior: 'smooth',
                block: 'center'
            });
        }

        return false;
    }

    // Show loading state
    if (elements.submitBtn) {
        const originalText = elements.submitBtn.textContent;
        elements.submitBtn.disabled = true;
        elements.submitBtn.textContent = 'Creating Advertisement...';
        elements.submitBtn.classList.add('bg-gray-400');

        setTimeout(() => {
            if (elements.submitBtn) {
                elements.submitBtn.disabled = false;
                elements.submitBtn.textContent = originalText;
                elements.submitBtn.classList.remove('bg-gray-400');
            }
        }, 30000);
    }

    return true;
}

// Prevent default drag behaviors on document
function preventDefaultDragBehaviors() {
    document.addEventListener('dragover', function (e) {
        e.preventDefault();
    });

    document.addEventListener('drop', function (e) {
        e.preventDefault();
    });
}

// Initialize everything when DOM is ready
document.addEventListener('DOMContentLoaded', function () {

    initializeElements();
    preventDefaultDragBehaviors();
    setupRealTimeValidation();

    if (elements.form) {
        elements.form.addEventListener('submit', handleFormSubmission);
    }

    // Initialize date inputs
    const today = new Date().toISOString().split('T')[0];
    const tomorrowDate = new Date();
    tomorrowDate.setDate(tomorrowDate.getDate() + 1);
    const tomorrow = tomorrowDate.toISOString().split('T')[0];

    const startDateInput = document.getElementById('start_date');
    const endDateInput = document.getElementById('end_date');

    if (startDateInput) {
        startDateInput.min = today;
        startDateInput.addEventListener('change', function () {
            const startDate = this.value;
            clearFieldError('start_date');

            if (startDate && endDateInput) {
                // End date min should be the later of tomorrow or start_date
                const minEndDate = startDate < tomorrow ? tomorrow : startDate;
                endDateInput.min = minEndDate;

                if (endDateInput.value && endDateInput.value < minEndDate) {
                    endDateInput.value = minEndDate;
                }
            }
            calculateTotal();
        });
    }

    if (endDateInput) {
        endDateInput.min = tomorrow; // Disable today for end date
        endDateInput.addEventListener('change', function () {
            const endDate = this.value;
            const startDate = startDateInput ? startDateInput.value : '';
            clearFieldError('end_date');

            if (endDate === today) {
                showFieldError('end_date', 'End date cannot be today');
                this.value = tomorrow;
            } else if (startDate && endDate && endDate < startDate) {
                showFieldError('end_date', 'End date cannot be earlier than start date');
                this.value = startDate;
            }
            calculateTotal();
        });
    }

    // Add event listeners to placement checkboxes
    document.querySelectorAll('.placement-checkbox').forEach(checkbox => {
        checkbox.addEventListener('change', calculateTotal);
    });

    calculateTotal();
});

// <><><><><><><> END JS OF CREATE SPONSOR ADS REQUEST FORM <><><><><><><>

// <><><><><><><> START JS FOR UPDATE PASSWORD ON SPONSOR ADS PAGE <><><><><><><>
// WEB URL -> /smart-ads/change-password
document.getElementById("validationEditPassAds").addEventListener("submit", function (e) {
    e.preventDefault();

    let form = this;
    let url = form.getAttribute("action");
    let formData = new FormData(form);

    // Clear previous errors
    document.querySelectorAll('.error-text').forEach(el => el.textContent = "");

    fetch(url, {
        method: "POST",
        headers: {
            "X-CSRF-TOKEN": document.querySelector('input[name="_token"]').value,
            "Accept": "application/json"
        },
        body: formData
    })
        .then(response => {
            console.log('Response status:', response.status);
            return response.json();
        })
        .then(data => {
            console.log('Response data:', data); // Debug log

            if (data.errors) {
                for (let field in data.errors) {
                    let errorSpan = document.querySelector(`.${field}_error`);
                    if (errorSpan) {
                        errorSpan.textContent = data.errors[field][0];
                    }
                }
            } else if (data.success) {
                console.log('Success block triggered'); // Debug log

                // Hide modal - check if using Bootstrap
                if (typeof $ !== 'undefined' && $.fn.modal) {
                    $('#validationEditPassAdsModel').modal('hide');
                }

                // Show SweetAlert2 confirmation
                if (typeof Swal !== 'undefined') {
                    Swal.fire({
                        title: 'Password Changed!',
                        text: 'Your password has been successfully changed.',
                        icon: 'success',
                        confirmButtonText: 'OK',
                        confirmButtonColor: '#3085d6',
                        allowOutsideClick: false,
                        allowEscapeKey: false
                    }).then((result) => {
                        if (result.isConfirmed) {
                            // Redirect to login page after confirmation
                            console.log('Redirecting to login page'); // Debug log
                            window.location.href = '/login'; // Adjust the login URL as needed
                        }
                    });
                } else {
                    console.error('SweetAlert2 is not loaded');
                    // Fallback redirect if SweetAlert2 is not available
                    window.location.href = '/login'; // Adjust the login URL as needed
                }

                form.reset();
            } else {
                console.log('Unexpected response format:', data);
            }
        })
        .catch(err => {
            console.error("Error:", err);

            // Show error message with iziToast
            iziToast.error({
                title: 'Error',
                message: 'Something went wrong. Please try again.',
                position: 'topRight',
                timeout: 5000
            });
        });
});
// <><><><><><><> END JS OF UPDATE PASSWORD ON SPONSOR ADS PAGE <><><><><><><>