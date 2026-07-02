document.addEventListener('DOMContentLoaded', function () {
    const email = document.getElementById('email');
    const password = document.getElementById('password');
    const passwordConfirm = document.getElementById('password_confirmation');
    const passwordError = document.getElementById('password-error');
    const confirmError = document.getElementById('password-confirmation-error');
    const submitBtn = document.getElementById('submitBtn');
    const togglePassword = document.getElementById('togglePassword');
    const togglePasswordConfirm = document.getElementById('togglePasswordConfirm');


    if (email) {
        const originalEmail = email.value;
        const eyeIcon = `<svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"></path><path d="M10 12a2 2 0 1 0 4 0a2 2 0 0 0 -4 0"></path><path d="M21 12c-2.4 4 -5.4 6 -9 6c-3.6 0 -6.6 -2 -9 -6c2.4 -4 5.4 -6 9 -6c3.6 0 6.6 2 9 6"></path></svg>`;
        const eyeOffIcon = `<svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"></path><path d="M10.585 10.587a2 2 0 0 0 2.829 2.828"></path><path d="M16.681 16.673a8.717 8.717 0 0 1 -4.681 1.327c-3.6 0 -6.6 -2 -9 -6c1.272 -2.12 2.712 -3.678 4.32 -4.674m2.86 -1.146a9.055 9.055 0 0 1 1.82 -.18c3.6 0 6.6 2 9 6c-.666 1.11 -1.379 2.067 -2.138 2.87"></path><path d="M3 3l18 18"></path></svg>`;

        togglePassword.addEventListener('click', function () {
            const type = password.type === 'password' ? 'text' : 'password';
            password.type = type;
            this.innerHTML = type === 'password' ? eyeIcon : eyeOffIcon;
        });

        togglePasswordConfirm.addEventListener('click', function () {
            const type = passwordConfirm.type === 'password' ? 'text' : 'password';
            passwordConfirm.type = type;
            this.innerHTML = type === 'password' ? eyeIcon : eyeOffIcon;
        });

        function validatePassword() {
            const val = password.value;
            const isValid = val.length >= 8 && /[a-z]/.test(val) && /[A-Z]/.test(val) && /[0-9]/.test(val) && /[@$!%*?&]/.test(val);

            password.classList.toggle('is-invalid', !isValid && val);
            password.classList.toggle('is-valid', isValid);
            passwordError.textContent = !isValid && val ? 'The password must be at least 8 characters long and contain at least one uppercase letter, one lowercase letter, one number, and one special character.' : '';
            passwordError.style.display = !isValid && val ? 'block' : 'none';

            return isValid;
        }

        function validateConfirm() {
            const match = passwordConfirm.value === password.value && passwordConfirm.value;

            passwordConfirm.classList.toggle('is-invalid', !match && passwordConfirm.value);
            passwordConfirm.classList.toggle('is-valid', match);
            confirmError.textContent = !match && passwordConfirm.value ? 'The password confirmation does not match.' : '';
            confirmError.style.display = !match && passwordConfirm.value ? 'block' : 'none';

            return match;
        }

        function updateButton() {
            submitBtn.disabled = !(validatePassword() && validateConfirm() && password.value && passwordConfirm.value);
        }

        email.addEventListener('keydown', (e) => e.preventDefault());
        email.addEventListener('input', () => { email.value = originalEmail; });
        email.addEventListener('paste', (e) => e.preventDefault());

        password.addEventListener('input', () => { validatePassword(); validateConfirm(); updateButton(); });
        passwordConfirm.addEventListener('input', () => { validateConfirm(); updateButton(); });
    }
});