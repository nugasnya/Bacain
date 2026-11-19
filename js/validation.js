/**
 * ============================================================================
 * BACAin - validation.js
 * Validasi form di sisi klien (Format email, panjang password, field wajib, dsb.)
 * Dikerjakan oleh: Syakhish (Front-end)
 * ============================================================================
 */

document.addEventListener('DOMContentLoaded', function () {
    'use strict';

    // Helper functions
    function isValidEmail(email) {
        const emailRegex = /^[a-zA-Z0-9.!#$%&'*+/=?^_`{|}~-]+@[a-zA-Z0-9-]+(?:\.[a-zA-Z0-9-]+)+$/;
        return emailRegex.test(String(email).toLowerCase());
    }

    function setFieldError(inputEl, errorEl, message) {
        if (!inputEl) return;
        inputEl.classList.add('is-invalid');
        if (errorEl) {
            errorEl.textContent = message;
        }
    }

    function clearFieldError(inputEl, errorEl) {
        if (!inputEl) return;
        inputEl.classList.remove('is-invalid');
        if (errorEl) {
            errorEl.textContent = '';
        }
    }

    // ========================================================================
    // 1. Validasi Form Login
    // ========================================================================
    const formLogin = document.getElementById('form-login');
    if (formLogin) {
        const emailInput = document.getElementById('login-email');
        const emailError = document.getElementById('login-email-error');
        const passwordInput = document.getElementById('login-password');
        const passwordError = document.getElementById('login-password-error');

        // Live validation on blur
        if (emailInput) {
            emailInput.addEventListener('blur', function () {
                const val = emailInput.value.trim();
                if (val === '') {
                    setFieldError(emailInput, emailError, 'Alamat email wajib diisi.');
                } else if (!isValidEmail(val)) {
                    setFieldError(emailInput, emailError, 'Format email tidak valid (contoh: nama@domain.com).');
                } else {
                    clearFieldError(emailInput, emailError);
                }
            });

            emailInput.addEventListener('input', function () {
                if (emailInput.classList.contains('is-invalid')) {
                    clearFieldError(emailInput, emailError);
                }
            });
        }

        if (passwordInput) {
            passwordInput.addEventListener('blur', function () {
                if (passwordInput.value.trim() === '') {
                    setFieldError(passwordInput, passwordError, 'Kata sandi wajib diisi.');
                } else {
                    clearFieldError(passwordInput, passwordError);
                }
            });

            passwordInput.addEventListener('input', function () {
                if (passwordInput.classList.contains('is-invalid')) {
                    clearFieldError(passwordInput, passwordError);
                }
            });
        }

        // Form submit check
        formLogin.addEventListener('submit', function (e) {
            let isValid = true;

            const emailVal = emailInput ? emailInput.value.trim() : '';
            if (emailVal === '') {
                setFieldError(emailInput, emailError, 'Alamat email wajib diisi.');
                isValid = false;
            } else if (!isValidEmail(emailVal)) {
                setFieldError(emailInput, emailError, 'Format email tidak valid.');
                isValid = false;
            } else {
                clearFieldError(emailInput, emailError);
            }

            const passVal = passwordInput ? passwordInput.value : '';
            if (passVal.trim() === '') {
                setFieldError(passwordInput, passwordError, 'Kata sandi wajib diisi.');
                isValid = false;
            } else {
                clearFieldError(passwordInput, passwordError);
            }

            if (!isValid) {
                e.preventDefault();
            }
        });
    }

    // ========================================================================
    // 2. Validasi Form Register
    // ========================================================================
    const formRegister = document.getElementById('form-register');
    if (formRegister) {
        const nameInput = document.getElementById('reg-name');
        const nameError = document.getElementById('reg-name-error');
        const emailInput = document.getElementById('reg-email');
        const emailError = document.getElementById('reg-email-error');
        const passwordInput = document.getElementById('reg-password');
        const passwordError = document.getElementById('reg-password-error');
        const confirmInput = document.getElementById('reg-confirm-password');
        const confirmError = document.getElementById('reg-confirm-password-error');

        // Blur validation
        if (nameInput) {
            nameInput.addEventListener('blur', function () {
                if (nameInput.value.trim().length < 3) {
                    setFieldError(nameInput, nameError, 'Nama lengkap minimal 3 karakter.');
                } else {
                    clearFieldError(nameInput, nameError);
                }
            });
        }

        if (emailInput) {
            emailInput.addEventListener('blur', function () {
                const val = emailInput.value.trim();
                if (val === '') {
                    setFieldError(emailInput, emailError, 'Alamat email wajib diisi.');
                } else if (!isValidEmail(val)) {
                    setFieldError(emailInput, emailError, 'Format email tidak valid.');
                } else {
                    clearFieldError(emailInput, emailError);
                }
            });
        }

        if (passwordInput) {
            passwordInput.addEventListener('blur', function () {
                if (passwordInput.value.length < 6) {
                    setFieldError(passwordInput, passwordError, 'Kata sandi minimal 6 karakter.');
                } else {
                    clearFieldError(passwordInput, passwordError);
                }
            });
        }

        if (confirmInput) {
            confirmInput.addEventListener('blur', function () {
                if (confirmInput.value !== passwordInput.value) {
                    setFieldError(confirmInput, confirmError, 'Konfirmasi kata sandi tidak cocok.');
                } else {
                    clearFieldError(confirmInput, confirmError);
                }
            });
        }

        // Submit check
        formRegister.addEventListener('submit', function (e) {
            let isValid = true;

            if (nameInput && nameInput.value.trim().length < 3) {
                setFieldError(nameInput, nameError, 'Nama lengkap minimal 3 karakter.');
                isValid = false;
            }

            const emailVal = emailInput ? emailInput.value.trim() : '';
            if (emailVal === '') {
                setFieldError(emailInput, emailError, 'Alamat email wajib diisi.');
                isValid = false;
            } else if (!isValidEmail(emailVal)) {
                setFieldError(emailInput, emailError, 'Format email tidak valid.');
                isValid = false;
            }

            if (passwordInput && passwordInput.value.length < 6) {
                setFieldError(passwordInput, passwordError, 'Kata sandi minimal 6 karakter.');
                isValid = false;
            }

            if (confirmInput && confirmInput.value !== passwordInput.value) {
                setFieldError(confirmInput, confirmError, 'Konfirmasi kata sandi tidak cocok.');
                isValid = false;
            }

            if (!isValid) {
                e.preventDefault();
            }
        });
    }

    // ========================================================================
    // 3. Toggle Show/Hide Password
    // ========================================================================
    function setupPasswordToggle(btnId, inputId) {
        const toggleBtn = document.getElementById(btnId);
        const passInput = document.getElementById(inputId);
        if (toggleBtn && passInput) {
            toggleBtn.addEventListener('click', function () {
                const isPassword = passInput.getAttribute('type') === 'password';
                passInput.setAttribute('type', isPassword ? 'text' : 'password');
                toggleBtn.textContent = isPassword ? '🙈' : '👁️';
            });
        }
    }

    setupPasswordToggle('toggle-password', 'login-password');
    setupPasswordToggle('toggle-reg-password', 'reg-password');
});
