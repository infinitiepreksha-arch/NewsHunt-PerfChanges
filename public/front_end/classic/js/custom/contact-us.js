// <><><><><><><> START JS FOR CONTACT US PAGE ON WEB <><><><><><><>
// WEB URL -> /contact-us
document.addEventListener('DOMContentLoaded', function () {
    var input = document.querySelector("#phone");
    var iti = window.intlTelInput(input, {
        utilsScript: "https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.8/js/utils.js",
        initialCountry: 'auto',
        geoIpLookup: function (success, failure) {
            success('us');
        },
        preferredCountries: ['us', 'gb', 'in'],
        separateDialCode: true,
    });

    document.querySelector("#contact-form").addEventListener('submit', function (e) {
        e.preventDefault();
        clearErrors();

        var isValid = true;

        var fullPhoneNumber = iti.getNumber();
        input.value = fullPhoneNumber;

        // phone validation
        if (!iti.isValidNumber()) {
            var phoneError = document.querySelector('#phone').closest('.mb-2').querySelector('.help-block')
                .getAttribute('data-phone-invalid');
            displayError('phone', phoneError);
            isValid = false;
        }

        // email validation
        var email = document.querySelector("#email").value.trim();
        var emailErrorContainer = document.querySelector('#email').closest('.mb-2').querySelector('.help-block');
        if (email === '') {
            displayError('email', emailErrorContainer.getAttribute('data-email-required'));
            isValid = false;
        } else if (!validateEmail(email)) {
            displayError('email', emailErrorContainer.getAttribute('data-email-invalid'));
            isValid = false;
        }

        // message validation
        var message = document.querySelector("#message").value.trim();
        var messageErrorContainer = document.querySelector('#message').closest('.mb-2').querySelector('.help-block');
        if (message === '') {
            displayError('message', messageErrorContainer.getAttribute('data-message-required'));
            isValid = false;
        }

        if (!isValid) {
            return;
        }

        // Prepare form data
        var formData = new FormData(this);

        // Submit via fetch (AJAX)
        fetch(this.action, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value,
                'Accept': 'application/json'
            },
            body: formData
        })
            .then(response => {
                if (!response.ok) {
                    return response.json().then(data => { throw data });
                }
                return response.json();
            })
            .then(data => {
                if (data.success) {
                    iziToast.success({
                        title: data.message,
                        position: 'topCenter',
                    });
                    document.querySelector("#contact-form").reset();
                    iti.setNumber('');
                }
            })
            .catch(errors => {
                if (errors.errors) {
                    displayServerErrors(errors.errors);
                }
            });
    });

    // Utility functions
    function displayError(field, message) {
        var errorContainer = document.querySelector(`#${field}`).closest('.mb-2').querySelector('.help-block');
        if (errorContainer) {
            errorContainer.innerHTML = `<strong>${message}</strong>`;
            errorContainer.style.display = 'block';
        }
    }

    function clearErrors() {
        document.querySelectorAll('.help-block').forEach(el => {
            el.innerHTML = '';
            el.style.display = 'none';
        });
    }

    function validateEmail(email) {
        var re = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        return re.test(email);
    }

    function displayServerErrors(errors) {
        Object.keys(errors).forEach(field => {
            displayError(field, errors[field][0]);
        });
    }
});
// <><><><><><><> END JS OF CONTACT US PAGE ON WEB  <><><><><><><>
