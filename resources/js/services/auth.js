/**
 * Authentication Service
 * Handles user authentication, token management, and session persistence
 */

import api, { showSuccessToast, showErrorToast } from './api';

class AuthService {
    constructor() {
        this.TOKEN_KEY = 'admin_token';
        this.USER_KEY = 'admin_user';
    }

    /**
     * Check if user is authenticated
     * @returns {boolean}
     */
    isAuthenticated() {
        return !!this.getToken();
    }

    /**
     * Get stored authentication token
     * @returns {string|null}
     */
    getToken() {
        // Check window.apiToken first (session-based auth), then localStorage (API auth)
        return window.apiToken || localStorage.getItem(this.TOKEN_KEY);
    }

    /**
     * Get stored user data
     * @returns {object|null}
     */
    getUser() {
        const userData = localStorage.getItem(this.USER_KEY);
        return userData ? JSON.parse(userData) : null;
    }

    /**
     * Store authentication token
     * @param {string} token
     */
    setToken(token) {
        localStorage.setItem(this.TOKEN_KEY, token);
    }

    /**
     * Store user data
     * @param {object} user
     */
    setUser(user) {
        localStorage.setItem(this.USER_KEY, JSON.stringify(user));
    }

    /**
     * Clear authentication data
     */
    clearAuth() {
        localStorage.removeItem(this.TOKEN_KEY);
        localStorage.removeItem(this.USER_KEY);
    }

    /**
     * Login with email and password
     * @param {string} email
     * @param {string} password
     * @param {boolean} remember
     * @returns {Promise}
     */
    async login(email, password, remember = false) {
        try {
            const response = await api.auth.login({
                email,
                password,
                remember
            });

            const { data } = response;

            // Store token and user data
            if (data.token) {
                this.setToken(data.token);
            }

            if (data.user || data.data?.user) {
                this.setUser(data.user || data.data.user);
            }

            showSuccessToast('Login successful! Redirecting...');

            return response;
        } catch (error) {
            // Error is already handled by API interceptor
            throw error;
        }
    }

    /**
     * Logout current user
     * @returns {Promise}
     */
    async logout() {
        try {
            await api.auth.logout();
        } catch (error) {
            console.error('Logout error:', error);
        } finally {
            this.clearAuth();
            showSuccessToast('Logged out successfully');
            window.location.href = '/auth-login';
        }
    }

    /**
     * Logout from all devices
     * @returns {Promise}
     */
    async logoutAll() {
        try {
            await api.auth.logoutAll();
        } catch (error) {
            console.error('Logout all error:', error);
        } finally {
            this.clearAuth();
            showSuccessToast('Logged out from all devices');
            window.location.href = '/auth-login';
        }
    }

    /**
     * Fetch current user profile
     * @returns {Promise}
     */
    async fetchProfile() {
        try {
            const response = await api.auth.getProfile();
            const { data } = response;

            // Update stored user data
            if (data.user || data.data?.user) {
                this.setUser(data.user || data.data.user);
            }

            return response;
        } catch (error) {
            throw error;
        }
    }

    /**
     * Update user profile
     * @param {object} profileData
     * @returns {Promise}
     */
    async updateProfile(profileData) {
        try {
            const response = await api.auth.updateProfile(profileData);
            const { data } = response;

            // Update stored user data
            if (data.user || data.data?.user) {
                this.setUser(data.user || data.data.user);
            }

            showSuccessToast('Profile updated successfully');
            return response;
        } catch (error) {
            throw error;
        }
    }

    /**
     * Change password
     * @param {string} currentPassword
     * @param {string} newPassword
     * @param {string} newPasswordConfirmation
     * @returns {Promise}
     */
    async changePassword(currentPassword, newPassword, newPasswordConfirmation) {
        try {
            const response = await api.auth.changePassword({
                current_password: currentPassword,
                new_password: newPassword,
                new_password_confirmation: newPasswordConfirmation
            });

            showSuccessToast('Password changed successfully');
            return response;
        } catch (error) {
            throw error;
        }
    }

    /**
     * Redirect to login if not authenticated
     */
    requireAuth() {
        // For session-based auth, don't redirect if we're already on a protected page
        // The server-side middleware will handle the redirect
        if (!this.isAuthenticated() && !window.location.pathname.startsWith('/dashboards')) {
            console.warn('Not authenticated, redirecting to login...');
            window.location.href = '/login';
        }
    }

    /**
     * Redirect to dashboard if already authenticated
     */
    redirectIfAuthenticated() {
        if (this.isAuthenticated()) {
            window.location.href = '/';
        }
    }
}

// Create singleton instance
const authService = new AuthService();

// Make auth service globally available
window.authService = authService;

export default authService;
