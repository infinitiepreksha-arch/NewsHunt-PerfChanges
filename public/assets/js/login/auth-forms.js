"use strict";

/**
 * Handle User Login Form Submission
 */
function handleLoginForm() {
    const loginForm = document.getElementById('loginForm');
    if (!loginForm) return;

    loginForm.addEventListener('submit', function (e) {
        e.preventDefault();
        clearLoginErrors();

        const loginBtn = document.getElementById('loginBtn');
        loginBtn.disabled = true;
        const originalBtnText = loginBtn.innerHTML;
        loginBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>Logging in...';

        const formData = new FormData(this);

        fetch(loginForm.action, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value,
                'Accept': 'application/json'
            },
            body: formData
        })
            .then(response => response.json())
            .then(data => {
                if (data.error) {
                    // Handle field-specific errors
                    if (data.errors) {
                        Object.keys(data.errors).forEach(field => {
                            const errorEl = document.querySelector('.' + field + '-error');
                            if (errorEl) {
                                errorEl.style.display = 'block';
                                errorEl.textContent = data.errors[field][0];
                            }
                        });
                    } else if (data.message) {
                        // Handle general error message
                        iziToast.error({
                            title: data.message,
                            position: 'topCenter'
                        });
                    }
                } else if (data.status === 'success') {
                    iziToast.success({
                        title: data.message,
                        position: 'topCenter',
                        timeout: 3000, // auto close after 3 sec
                        onClosing: function () {
                            window.location.href = '/'; // redirect to home
                        }
                    });
                }
            })
            .catch(error => {
                console.error('Error:', error);
                iziToast.error({
                    title: 'An error occurred. Please try again.',
                    position: 'topCenter'
                });
            })
            .finally(() => {
                loginBtn.disabled = false;
                loginBtn.innerHTML = originalBtnText;
            });
    });
}

/**
 * Handle User Register Form Submission
 */
function handleRegisterForm() {
    const registerForm = document.getElementById('registerForm');
    if (!registerForm) return;

    registerForm.addEventListener('submit', function (e) {
        e.preventDefault();
        clearRegisterErrors();

        const registerBtn = document.getElementById('registerBtn');
        registerBtn.disabled = true;
        const originalBtnText = registerBtn.innerHTML;
        registerBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>Registering...';

        const formData = new FormData(this);

        fetch(registerForm.action, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value,
                'Accept': 'application/json'
            },
            body: formData
        })
            .then(response => response.json())
            .then(data => {
                if (data.status === 'error') {
                    // Handle field-specific errors
                    if (data.errors) {
                        Object.keys(data.errors).forEach(field => {
                            const errorEl = document.querySelector('.' + field + '-error');
                            if (errorEl) {
                                errorEl.style.display = 'block';
                                errorEl.textContent = data.errors[field][0];
                            }
                        });
                    }
                } else if (data.status === 'success') {
                    iziToast.success({
                        title: data.message,
                        position: 'topCenter',
                        timeout: 3000, // auto close after 3 sec
                        onClosing: function () {
                            window.location.href = '/'; // redirect to home
                        }
                    });
                }
            })
            .catch(error => {
                console.error('Error:', error);
                iziToast.error({
                    title: 'An error occurred. Please try again.',
                    position: 'topCenter'
                });
            })
            .finally(() => {
                registerBtn.disabled = false;
                registerBtn.innerHTML = originalBtnText;
            });
    });
}

/**
 * Clear Login Form Errors
 */
function clearLoginErrors() {
    document.querySelectorAll('.email-error, .password-error').forEach(el => {
        el.style.display = 'none';
        el.textContent = '';
    });
}

/**
 * Clear Register Form Errors
 */
function clearRegisterErrors() {
    document.querySelectorAll('.name-error, .email-error, .password-error, .password_confirmation-error, .accept_terms-error').forEach(el => {
        el.style.display = 'none';
        el.textContent = '';
    });
}

/**
 * Redirect to Home Page
 */
function redirectToHome(redirectUrl) {
    if (redirectUrl) {
        window.location.href = redirectUrl;
    } else {
        window.location.href = '/';
    }
}

/**
 * Initialize Forms on Document Ready
 */
document.addEventListener('DOMContentLoaded', function () {
    handleLoginForm();
    handleRegisterForm();
});
