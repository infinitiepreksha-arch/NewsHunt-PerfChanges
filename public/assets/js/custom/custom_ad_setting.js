

$(document).ready(function () {
    $(".customAdSettingForm").on("submit", function (e) {
        e.preventDefault();

        let form = $(this);
        let formData = new FormData(this);

        // clear old errors
        $("strong[id$='-error']").text("");

        $.ajax({
            url: form.attr("action"),
            method: "POST",
            data: formData,
            contentType: false,
            processData: false,
            headers: {
                "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
            },
            success: function (response) {
                if (response.status === true) {
                    showSuccessToast(response.message);

                    setTimeout(function () {
                        window.location.href = response.redirect; // ✅ redirect after success
                    }, 2000);
                } else {
                    showErrorToast(response.message || "Something went wrong.");
                }
            },
            error: function (xhr) {
                if (xhr.status === 422) {
                    let errors = xhr.responseJSON.errors;

                    $.each(errors, function (key, value) {
                        // Normalize keys
                        let fieldId = "custom_" + key.replaceAll("_", "_");

                        // Show error in corresponding <span>
                        $("#" + fieldId + "-error").text(value[0]);
                    });
                } else {
                    showErrorToast("Unexpected error occurred.");
                }
            }

        });
    });
});

(function () {
    const checkActiveAdsUrl = "/admin/settings/check-active-ads";
    const checkPendingPaymentsUrl = "/admin/settings/check-pending-payments";

    const $toggle = $('#switch_custom_ads_status_mode');
    const $hiddenToggle = $('#enable_custom_ads_status');
    const $hours = $('#payment_deadline_hours');
    const $minutes = $('#payment_deadline_minutes');

    let isProcessing = false;

    // ==========================
    // Toggle switch click
    // ==========================
    $toggle.on('click', function (e) {
        e.preventDefault();

        if (isProcessing) return;
        isProcessing = true;

        const newState = $(this).is(':checked');
        const originalState = !newState;

        // Reset to original state immediately
        $(this).prop('checked', originalState);

        // Only run AJAX when user clicks toggle
        $.get(checkActiveAdsUrl)
            .done(function (response) {
                if (response.has_active_ads) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Cannot Change Status',
                        text: `Cannot change Custom ads Feature disable mode. There are ${response.active_count} active ads that need to complete first.`,
                        confirmButtonText: 'OK',
                        allowOutsideClick: false,   // Disable outside click
                        allowEscapeKey: false,      // Disable ESC
                        allowEnterKey: false,       // Disable Enter key
                    });
                } else {
                    $toggle.prop('checked', newState);
                    $hiddenToggle.val(newState ? 1 : 0);
                }
            })
            .always(function () {
                isProcessing = false;
            });
    });

    // ==========================
    // Hours & minutes focus/click
    // ==========================
    function checkPending($field) {
        if (isProcessing) return;
        isProcessing = true;

        const originalValue = $field.val();

        $.get(checkPendingPaymentsUrl)
            .done(function (response) {
                if (response.has_pending_payments) {
                    $field.blur();
                    Swal.fire({
                        icon: 'warning',
                        title: 'Cannot Change Payment Deadline',
                        text: `Cannot modify payment deadline. There are ${response.pending_count} users with pending payments.`,
                        confirmButtonText: 'OK',
                        allowOutsideClick: false,   // Disable outside click
                        allowEscapeKey: false,      // Disable ESC
                        allowEnterKey: false,       // Disable Enter key
                    });
                } else {
                    $field.focus();
                }
            })
            .always(function () {
                isProcessing = false;
            });
    }

    // Attach event only to user interaction
    $hours.add($minutes).on('focus click', function (e) {
        e.preventDefault();
        checkPending($(this));
    });
})();


// ✅ Toasts
function showSuccessToast(message) {
    Toastify({
        text: message,
        duration: 4000,
        close: true,
        style: { background: "#28a745" }, // green
    }).showToast();
}

function showErrorToast(message) {
    Toastify({
        text: message,
        duration: 4000,
        close: true,
        style: { background: "#dc3545" }, // red
    }).showToast();
}