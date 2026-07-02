//  <><><><><><> START MEMBERSHIP PLAN JS <><><><><><>
//  <><><><><><> MEMBERSHIP PLAN -> INDEX FILE JS HERE !!<><><><><><>

$(document).ready(function () {
    $("#addPricingPlanForm").on("submit", function (e) {
        e.preventDefault();

        let form = $(this);
        let formData = new FormData(this);

        // Clear old errors
        $(".error-text").text("");

        $.ajax({
            url: form.attr("action"),
            method: "POST",
            data: formData,
            processData: false,
            contentType: false,
            success: function (response) {
                if (response.status === 'success') {
                    showSuccessToast(response.message);
                } else if (response.status === 'error') {
                    showErrorToast(response.message);
                }
                window.location.href = response.redirect;
            },
            error: function (xhr) {
                if (xhr.status === 422) {
                    let errors = xhr.responseJSON.errors;

                    $.each(errors, function (key, value) {
                        // Handle array field errors (duration.0, price.0 etc.)
                        let inputName = key.replace(/\.\d+$/, "");
                        let errorClass = inputName + "_error";

                        $("." + errorClass).text(value[0]);
                    });
                }
            }
        });
    });
});


document.querySelectorAll('#addPricingPlanForm input[type="number"]').forEach(input => {
    input.addEventListener('input', function () {
        if (this.value < 0) {
            this.value = 0; // reset to 0 if user types -1 or any negative
        }
    });
});

document.querySelectorAll('.editPricingPlanFormValidation input[type="number"]').forEach(input => {
    input.addEventListener('input', function () {
        if (this.value < 0) {
            this.value = 0; // reset to 0 if user types -1 or any negative
        }
    });
});

$(document).ready(function () {
    // Handle all edit pricing plan forms
    $("[id^=editPricingPlanForm_]").on("submit", function (e) {
        e.preventDefault();

        let form = $(this);
        let formData = new FormData(this);

        // Clear old errors
        form.find(".error-text").text("");

        $.ajax({
            url: form.attr("action"),
            method: "POST", // method is PUT but via _method hidden input
            data: formData,
            processData: false,
            contentType: false,
            success: function (response) {
                if (response.status === "success") {
                    showSuccessToast(response.message);
                    if (response.redirect) {
                        window.location.href = response.redirect;
                    }
                } else {
                    showErrorToast(response.message);
                }
            },
            error: function (xhr) {
                if (xhr.status === 422) {
                    let errors = xhr.responseJSON.errors;

                    $.each(errors, function (key, value) {
                        // Handle array field errors (duration.0, price.0 etc.)
                        let inputName = key.replace(/\.\d+$/, "");
                        let errorClass = inputName + "_error";

                        form.find("." + errorClass).text(value[0]);
                    });
                }
            },
        });
    });
});

$(document).ready(function () {
    // Generate slug from name field on create form
    $('#name').on('keyup', function () {
        let name = $(this).val();
        let slug = name.toLowerCase()
            .replace(/\s+/g, '-')                // spaces → dash
            .replace(/[^\p{L}\p{N}-]+/gu, '');  // allow letters, numbers, dash

        // If slug is empty or just '-', create a unique temp slug
        if (!slug || slug === '-') {
            slug = 'temp-' + Date.now() + '-' + Math.floor(Math.random() * 1000);
        }

        $('#slug').val(slug);
    });

    // Handle tenure selection for pricing display
    $('.tenure-selector').on('change', function () {
        const planId = $(this).data('plan');
        const selectedOption = $(this).find('option:selected');
        const price = selectedOption.data('price');
        const duration = selectedOption.data('duration');
        const tenureId = selectedOption.val();

        // Update form values
        $(`.payment-form-${planId} input[name="tenure_id"]`).val(tenureId);
        $(`.payment-form-${planId} input[name="amount"]`).val(price);
        $(`.payment-form-${planId} input[name="duration"]`).val(duration);

        // Update the price display in the card
        const priceDisplay = $(this).closest('.card-body').find('h2.display-8');
        if (priceDisplay.length) {
            const currencySymbol = priceDisplay.find('span.fs-10').text();
            const formattedPrice = parseFloat(price).toFixed(2);
            const monthText = parseInt(duration) > 1 ? 'months' : 'month';

            priceDisplay.html(`
                <span class="fs-10">${currencySymbol}</span> ${formattedPrice}
                <span class="fs-6 text-muted">/ ${duration} ${monthText}</span>
            `);
        }
    });

    // Handle active plan edit click
    $(document).on('click', '.plan-edit-active', function (e) {
        e.preventDefault();
        Swal.fire({
            icon: 'info',
            title: 'Action Not Allowed',
            text: 'You cannot edit this plan because it has active subscriptions or transactions.',
            confirmButtonColor: '#3085d6',
            confirmButtonText: 'OK',
            customClass: {
                popup: 'dark:bg-black dark:text-white',
            },
        });
    });

    // For each edit plan modal, generate slug from name field
    $('[id^="edit_name_"]').each(function () {
        const planId = $(this).attr('id').split('_').pop();
        $(this).on('keyup', function () {
            let name = $(this).val();
            let slug = name.toLowerCase()
                .replace(/\s+/g, '-')                // spaces → dash
                .replace(/[^\p{L}\p{N}-]+/gu, '');  // allow letters, numbers, dash

            // If slug is empty or just '-', create a unique temp slug
            if (!slug || slug === '-') {
                slug = 'temp-' + Date.now() + '-' + Math.floor(Math.random() * 1000);
            }

            $(`#edit_slug_${planId}`).val(slug);
        });
    });

    // CREATE FORM - Add new tenure row
    $('#add-tenure-btn').on('click', function () {
        addTenureRow('#tenure-container');
        toggleRemoveButtons();
    });

    // EDIT FORMS - Add new tenure row for each plan
    $('[id^="add-tenure-btn-"]').each(function () {
        const planId = $(this).attr('id').split('-').pop();
        $(this).on('click', function () {
            addTenureRow(`#tenure-container-${planId}`, planId);
            toggleRemoveButtons(planId);
        });
    });

    // Handle delete tenure row for both create and edit forms
    $(document).on('click', '.remove-tenure-btn', function () {
        const formContainer = $(this).closest('.modal-content');
        $(this).closest('.tenure-row').remove();

        // Check if this is in an edit modal
        const planId = formContainer.find('input[name="id"]').val();
        if (planId) {
            toggleRemoveButtons(planId);
        } else {
            toggleRemoveButtons();
        }
    });

    // Handle plan deletion
    $(document).on('click', '.plan-delete-btn', function () {
        const planId = $(this).data('id');
        const isActive = $(this).data('is-active');

        if (isActive === true || isActive === 'true') {
            Swal.fire({
                icon: 'info',
                title: 'Action Not Allowed',
                text: 'You cannot delete this plan because it has active subscriptions or transactions.',
                confirmButtonColor: '#3085d6',
                confirmButtonText: 'OK',
                customClass: {
                    popup: 'dark:bg-black dark:text-white',
                },
            });
            return;
        }

        Swal.fire({
            title: "Are you sure?",
            text: "You won't be able to revert this",
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: "#d33",
            cancelButtonColor: "#3085d6",
            confirmButtonText: "Delete",
            customClass: {
                popup: "dark:bg-black dark:text-white",
            },
        }).then((result) => {
            if (result.isConfirmed) {
                $(`#delete-plan-form-${planId}`).submit();
            }
        });
    });

    // Functions to manage tenure rows
    function addTenureRow(containerId, planId = '') {
        const timestamp = new Date().getTime();
        const index = planId ?
            $(`${containerId} .tenure-row`).length + $('.tenure-row').length :
            $(`${containerId} .tenure-row`).length + 1;

        const idSuffix = planId ? `_${planId}_${timestamp}` : `_${timestamp}`;

        const newRow = `
            <div class="row tenure-row mb-3">
                <div class="col-md-2">
                    <div class="form-group">
                        <label for="tenure_name${idSuffix}" class="form-label">Tenure Name</label>
                        <input type="text" class="form-control" name="tenure_name[]" id="tenure_name${idSuffix}" placeholder="e.g. Months">
                        <input type="hidden" name="tenure_id[]" value="">
                        <span class="parsley-required error-text tenure_name_error"></span>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label for="duration${idSuffix}" class="form-label">Duration (months) <span class="text-danger">*</span></label>
                        <input type="number" class="form-control" name="duration[]" id="duration${idSuffix}" min="1" >
                                                            <span class="parsley-required error-text duration_error"></span>

                    </div>
                </div>

            

                <div class="col-md-3">
                    <div class="form-group">
                        <label for="price${idSuffix}" class="form-label">Price <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text">$</span>
                            <input type="number" class="form-control" name="price[]" id="price${idSuffix}" min="0" step="0.01" >
                            
                        </div>
                                                            <span class="parsley-required error-text price_error"></span>

                    </div>
                </div>

                  <div class="col-md-3 mt-2">
                                <div class="form-group">
                                    <label for="product_id${idSuffix}" class="form-label">Product Id</label>
                                    <input type="text" class="form-control"  name="product_id[]" id="product_id${idSuffix}"
                                    placeholder="e.g. premium_one_month">
                                    <small class="text-danger fw-bolder">
                                        Product ID is required for Apple Pay integration. Leave this blank if Apple Pay is disabled or your using another gateway.
                                    </small>
                                </div>
                            </div>

                <div class="col-md-1">
                                <div class="form-group">
                                    <label class="form-label mt-3 p-1"></label> <!-- Empty label for alignment -->
                                    <div class="d-flex align-items-end h-100">
                                        <button type="button" class="btn btn-danger remove-tenure-btn display-none">
                                            <i class="fas fa-times"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
            </div>
        `;

        $(containerId).append(newRow);
    }

    function toggleRemoveButtons(planId = '') {
        const formSelector = planId ?
            `#editPricingPlanForm_${planId}` :
            '#addPricingPlanForm';

        const rows = $(`${formSelector} .tenure-row`);
        // Allow remove button for at least one row
        if (rows.length === 1) {
            $(`${formSelector} .remove-tenure-btn`).show();
        } else {
            $(`${formSelector} .remove-tenure-btn`).show();
        }
    }

});
//  <><><><><><> END MEMBERSHIP PLAN JS <><><><><><>  


// <><><><><><> START SUBSCRIPTION JS <><><><><><>

document.getElementById('plan_id').addEventListener('change', function () {
    const selectedOption = this.options[this.selectedIndex];
    const selectedPlanId = selectedOption.value;
    const selectedFeatureId = selectedOption.dataset.featureId;

    const tenureGroup = document.getElementById('tenure-group');
    const tenureSelect = document.getElementById('plan_tenure_id');
    const featureInput = document.getElementById('feature_id');
    const featureDisplay = document.getElementById('feature_id_display');

    // Show/hide tenure dropdown
    tenureGroup.style.display = selectedPlanId ? 'block' : 'none';

    // Reset and filter tenures
    tenureSelect.value = '';
    document.getElementById('duration').value = '';
    for (const option of tenureSelect.options) {
        if (option.value === '') continue; // Skip the default option
        option.style.display = option.dataset.planId == selectedPlanId ? 'block' : 'none';
    }

    // Set feature_id from the selected plan and display it
    if (selectedFeatureId) {
        featureInput.value = selectedFeatureId;
        featureDisplay.value = selectedFeatureId;
    } else {
        featureInput.value = '';
        featureDisplay.value = 'No feature associated with this plan';
    }

    // Clear end date when plan changes
    document.getElementById('end_date').value = '';
});

document.getElementById('plan_tenure_id').addEventListener('change', function () {
    if (!this.value) return;

    const selectedOption = this.options[this.selectedIndex];
    const duration = selectedOption.dataset.duration;

    // Set duration
    document.getElementById('duration').value = duration || '';

    // Update end date based on start date and duration
    updateEndDate();
});

// Manual duration input should also update end date
document.getElementById('duration').addEventListener('input', function () {
    updateEndDate();
});

// Start date change handler
document.getElementById('start_date').addEventListener('change', function () {
    updateEndDate();
});

// Function to update the end date based on start date and duration
function updateEndDate() {
    const startDateInput = document.getElementById('start_date');
    const durationInput = document.getElementById('duration');
    const endDateInput = document.getElementById('end_date');

    if (startDateInput.value && durationInput.value) {
        const startDate = new Date(startDateInput.value);
        const durationMonths = parseInt(durationInput.value);

        if (!isNaN(durationMonths) && durationMonths > 0) {
            // Calculate end date by adding duration in months
            const endDate = new Date(startDate);
            endDate.setMonth(endDate.getMonth() + durationMonths);

            // Format date to YYYY-MM-DD for input
            const endDateStr = endDate.toISOString().split('T')[0];
            endDateInput.value = endDateStr;
        }
    }
}

// Set today's date as default start date
document.addEventListener('DOMContentLoaded', function () {
    const today = new Date().toISOString().split('T')[0];
    document.getElementById('start_date').value = today;

    // Initialize the form
    const planSelect = document.getElementById('plan_id');
    // If a plan is already selected, trigger the change event
    if (planSelect.value) {
        planSelect.dispatchEvent(new Event('change'));
    }
});

document.getElementById('plan_id').addEventListener('change', function () {
    const selectedOption = this.options[this.selectedIndex];
    const selectedPlanId = selectedOption.value;
    const selectedFeatureId = selectedOption.dataset.featureId;

    const tenureGroup = document.getElementById('tenure-group');
    const tenureSelect = document.getElementById('plan_tenure_id');
    const featureInput = document.getElementById('feature_id');
    const featureDisplay = document.getElementById('feature_id_display');

    // Show/hide tenure dropdown
    tenureGroup.style.display = selectedPlanId ? 'block' : 'none';

    // Reset and filter tenures
    tenureSelect.value = '';
    document.getElementById('duration').value = '';

    // Reset and show only tenures for the selected plan
    for (const option of tenureSelect.options) {
        if (option.value === '') continue; // Skip the default option
        option.style.display = option.dataset.planId == selectedPlanId ? 'block' : 'none';
    }

    // Set feature_id from the selected plan and display it
    if (selectedFeatureId) {
        featureInput.value = selectedFeatureId;
        featureDisplay.value = selectedFeatureId;
    } else {
        featureInput.value = '';
        featureDisplay.value = 'No feature associated with this plan';
    }

    // Clear end date when plan changes
    document.getElementById('end_date').value = '';
});

document.getElementById('plan_tenure_id').addEventListener('change', function () {
    if (!this.value) return;

    const selectedOption = this.options[this.selectedIndex];
    const duration = selectedOption.dataset.duration;

    // Set duration
    document.getElementById('duration').value = duration || '';

    // Update end date based on start date and duration
    updateEndDate();
});

// Manual duration input should also update end date
document.getElementById('duration').addEventListener('input', function () {
    updateEndDate();
});

// Start date change handler
document.getElementById('start_date').addEventListener('change', function () {
    updateEndDate();
});

// Function to update the end date based on start date and duration
function updateEndDate() {
    const startDateInput = document.getElementById('start_date');
    const durationInput = document.getElementById('duration');
    const endDateInput = document.getElementById('end_date');

    if (startDateInput.value && durationInput.value) {
        const startDate = new Date(startDateInput.value);
        const durationMonths = parseInt(durationInput.value);

        if (!isNaN(durationMonths) && durationMonths > 0) {
            // Calculate end date by adding duration in months
            const endDate = new Date(startDate);
            endDate.setMonth(endDate.getMonth() + durationMonths);

            // Format date to YYYY-MM-DD for input
            const endDateStr = endDate.toISOString().split('T')[0];
            endDateInput.value = endDateStr;
        }
    }
}

// Add a form submit handler to ensure the end date is properly calculated before submission
document.getElementById('addSubscriptionForm').addEventListener('submit', function (e) {
    // Ensure end date is calculated one final time before submission
    updateEndDate();
});
// <><><><><><> END SUBSCRIPTION JS <><><><><><>
