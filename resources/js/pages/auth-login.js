/**
 * Login Page
 * Handles admin authentication
 */

import authService from '../services/auth';
import { showErrorToast } from '../services/api';

class LoginPage {
    constructor() {
        this.form = document.getElementById('loginForm');
        this.emailInput = document.getElementById('email');
        this.passwordInput = document.getElementById('password');
        this.rememberCheckbox = document.getElementById('remember');
        this.submitButton = document.querySelector('button[type="submit"]');
        this.init();
    }

    init() {
        // Redirect if already logged in
        authService.redirectIfAuthenticated();

        // Handle form submission
        if (this.form) {
            this.form.addEventListener('submit', (e) => this.handleLogin(e));
        }
    }

    async handleLogin(event) {
        event.preventDefault();

        // Get form values
        const email = this.emailInput?.value;
        const password = this.passwordInput?.value;
        const remember = this.rememberCheckbox?.checked || false;

        // Validate
        if (!email || !password) {
            showErrorToast('Please enter email and password');
            return;
        }

        // Disable submit button
        this.setLoading(true);

        try {
            // Call login API
            await authService.login(email, password, remember);

            // Redirect to dashboard after successful login
            setTimeout(() => {
                window.location.href = '/';
            }, 1000);

        } catch (error) {
            console.error('Login error:', error);
            // Error toast is already shown by API interceptor
        } finally {
            this.setLoading(false);
        }
    }

    setLoading(isLoading) {
        if (this.submitButton) {
            if (isLoading) {
                this.submitButton.disabled = true;
                this.submitButton.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Logging in...';
            } else {
                this.submitButton.disabled = false;
                this.submitButton.innerHTML = 'Log In';
            }
        }
    }
}

// Initialize when DOM is ready
document.addEventListener('DOMContentLoaded', function () {
    new LoginPage();
});

export default LoginPage;
