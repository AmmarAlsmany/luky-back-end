/**
 * API Service - Handles all HTTP requests to the backend API
 * Uses axios with interceptors for authentication and error handling
 */

import axios from 'axios';
import Toastify from 'toastify-js';

// API Base URL - loaded from environment variable
const API_BASE_URL = import.meta.env.VITE_API_BASE_URL || 'http://localhost:8000/api';

// Create axios instance
const apiClient = axios.create({
    baseURL: API_BASE_URL,
    headers: {
        'Content-Type': 'application/json',
        'Accept': 'application/json',
    },
    withCredentials: true, // Important for Sanctum cookie-based auth
});

// Request interceptor - Add auth token to requests
apiClient.interceptors.request.use(
    (config) => {
        // Try to get token from window (session-based login) first, then localStorage (API login)
        const token = window.apiToken || localStorage.getItem('admin_token');
        if (token) {
            config.headers.Authorization = `Bearer ${token}`;
        }
        return config;
    },
    (error) => {
        return Promise.reject(error);
    }
);

// Response interceptor - Handle errors globally
apiClient.interceptors.response.use(
    (response) => {
        return response;
    },
    (error) => {
        // Handle different error types
        if (error.response) {
            const { status, data } = error.response;

            switch (status) {
                case 401:
                    // Unauthorized - Clear token and redirect to login
                    localStorage.removeItem('admin_token');
                    localStorage.removeItem('admin_user');

                    if (window.location.pathname !== '/auth-login' && window.location.pathname !== '/login') {
                        Toastify({
                            text: "Session expired. Please login again.",
                            gravity: "top",
                            position: "right",
                            className: "bg-danger",
                            duration: 3000
                        }).showToast();

                        setTimeout(() => {
                            window.location.href = '/login';
                        }, 1000);
                    }
                    break;

                case 403:
                    Toastify({
                        text: data.message || "You don't have permission to perform this action.",
                        gravity: "top",
                        position: "right",
                        className: "bg-warning",
                        duration: 3000
                    }).showToast();
                    break;

                case 404:
                    Toastify({
                        text: data.message || "Resource not found.",
                        gravity: "top",
                        position: "right",
                        className: "bg-warning",
                        duration: 3000
                    }).showToast();
                    break;

                case 422:
                    // Validation errors
                    const validationErrors = data.errors || {};
                    const errorMessages = Object.values(validationErrors).flat();

                    Toastify({
                        text: errorMessages[0] || "Validation error occurred.",
                        gravity: "top",
                        position: "right",
                        className: "bg-danger",
                        duration: 3000
                    }).showToast();
                    break;

                case 500:
                    Toastify({
                        text: "Server error. Please try again later.",
                        gravity: "top",
                        position: "right",
                        className: "bg-danger",
                        duration: 3000
                    }).showToast();
                    break;

                default:
                    Toastify({
                        text: data.message || "An error occurred.",
                        gravity: "top",
                        position: "right",
                        className: "bg-danger",
                        duration: 3000
                    }).showToast();
            }
        } else if (error.request) {
            // Network error
            Toastify({
                text: "Network error. Please check your connection.",
                gravity: "top",
                position: "right",
                className: "bg-danger",
                duration: 3000
            }).showToast();
        }

        return Promise.reject(error);
    }
);

// API Service object with all endpoints
const api = {
    // ===================================
    // AUTHENTICATION
    // ===================================
    auth: {
        login: (credentials) => apiClient.post('/admin/auth/login', credentials),
        logout: () => apiClient.post('/admin/auth/logout'),
        logoutAll: () => apiClient.post('/admin/auth/logout-all'),
        getProfile: () => apiClient.get('/admin/auth/profile'),
        updateProfile: (data) => apiClient.put('/admin/auth/profile', data),
        changePassword: (data) => apiClient.put('/admin/auth/change-password', data),
    },

    // ===================================
    // DASHBOARD
    // ===================================
    dashboard: {
        getOverview: () => apiClient.get('/admin/dashboard'),
        getRevenueChart: (params) => apiClient.get('/admin/dashboard/charts/revenue', { params }),
        getBookingsChart: (params) => apiClient.get('/admin/dashboard/charts/bookings', { params }),
        getUsersGrowthChart: (params) => apiClient.get('/admin/dashboard/charts/users-growth', { params }),
        getTopProviders: (params) => apiClient.get('/admin/dashboard/top-providers', { params }),
        getRecentActivities: () => apiClient.get('/admin/dashboard/recent-activities'),
        getBookingStatusDistribution: () => apiClient.get('/admin/dashboard/booking-status-distribution'),
        getRatingDistribution: () => apiClient.get('/admin/dashboard/rating-distribution'),
        // Add web routes for backward compatibility
        getWebDashboard: () => axios.get('/dashboards/index'),
        getWebRevenueChart: () => axios.get('/dashboards/revenue-chart'),
        getWebBookingsChart: () => axios.get('/dashboards/bookings-chart'),
        getWebTopProviders: () => axios.get('/dashboards/top-providers'),
        getWebRecentActivities: () => axios.get('/dashboards/recent-activities'),
    },

    // ===================================
    // CLIENTS
    // ===================================
    clients: {
        getAll: (params) => apiClient.get('/admin/clients', { params }),
        getStats: () => apiClient.get('/admin/clients/stats'),
        getById: (id) => apiClient.get(`/admin/clients/${id}`),
        update: (id, data) => apiClient.put(`/admin/clients/${id}`, data),
        delete: (id) => apiClient.delete(`/admin/clients/${id}`),
        updateStatus: (id, status) => apiClient.put(`/admin/clients/${id}/status`, { status }),
        getBookings: (id, params) => apiClient.get(`/admin/clients/${id}/bookings`, { params }),
        getTransactions: (id, params) => apiClient.get(`/admin/clients/${id}/transactions`, { params }),
        export: (params) => apiClient.get('/admin/clients/export', { params, responseType: 'blob' }),
    },

    // ===================================
    // PROVIDERS
    // ===================================
    providers: {
        getAll: (params) => apiClient.get('/admin/providers', { params }),
        getStats: () => apiClient.get('/admin/providers/stats'),
        getPendingApproval: (params) => apiClient.get('/admin/providers/pending-approval', { params }),
        getById: (id) => apiClient.get(`/admin/providers/${id}`),
        update: (id, data) => apiClient.put(`/admin/providers/${id}`, data),
        delete: (id) => apiClient.delete(`/admin/providers/${id}`),
        verify: (id, data) => apiClient.put(`/admin/providers/${id}/verify`, data),
        updateStatus: (id, status) => apiClient.put(`/admin/providers/${id}/status`, { status }),
        getServices: (id, params) => apiClient.get(`/admin/providers/${id}/services`, { params }),
        getBookings: (id, params) => apiClient.get(`/admin/providers/${id}/bookings`, { params }),
        getRevenue: (id, params) => apiClient.get(`/admin/providers/${id}/revenue`, { params }),
        getReviews: (id, params) => apiClient.get(`/admin/providers/${id}/reviews`, { params }),
        getActivityLogs: (id, params) => apiClient.get(`/admin/providers/${id}/activity-logs`, { params }),
        export: (params) => apiClient.get('/admin/providers/export', { params, responseType: 'blob' }),
    },

    // ===================================
    // EMPLOYEES
    // ===================================
    employees: {
        getAll: (params) => apiClient.get('/admin/employees', { params }),
        getStats: () => apiClient.get('/admin/employees/stats'),
        getById: (id) => apiClient.get(`/admin/employees/${id}`),
        create: (data) => apiClient.post('/admin/employees', data),
        update: (id, data) => apiClient.put(`/admin/employees/${id}`, data),
        delete: (id) => apiClient.delete(`/admin/employees/${id}`),
        updateStatus: (id, status) => apiClient.put(`/admin/employees/${id}/status`, { status }),
        assignRole: (id, role) => apiClient.put(`/admin/employees/${id}/role`, { role }),
        updatePermissions: (id, permissions) => apiClient.put(`/admin/employees/${id}/permissions`, { permissions }),
        resetPassword: (id) => apiClient.post(`/admin/employees/${id}/reset-password`),
        export: (params) => apiClient.get('/admin/employees/export', { params, responseType: 'blob' }),
    },

    // ===================================
    // ROLES & PERMISSIONS
    // ===================================
    roles: {
        getAll: () => apiClient.get('/admin/roles'),
        getPermissions: () => apiClient.get('/admin/permissions'),
    },

    // ===================================
    // PROMO CODES
    // ===================================
    promoCodes: {
        getAll: (params) => apiClient.get('/admin/promo-codes', { params }),
        getStats: () => apiClient.get('/admin/promo-codes/stats'),
        getById: (id) => apiClient.get(`/admin/promo-codes/${id}`),
        create: (data) => apiClient.post('/admin/promo-codes', data),
        update: (id, data) => apiClient.put(`/admin/promo-codes/${id}`, data),
        delete: (id) => apiClient.delete(`/admin/promo-codes/${id}`),
        toggleStatus: (id) => apiClient.post(`/admin/promo-codes/${id}/toggle`),
        generateCode: () => apiClient.get('/admin/promo-codes/generate'),
        validate: (code) => apiClient.post('/admin/promo-codes/validate', { code }),
        getUsageHistory: (id, params) => apiClient.get(`/admin/promo-codes/${id}/usage`, { params }),
    },

    // ===================================
    // BANNERS
    // ===================================
    banners: {
        getAll: (params) => apiClient.get('/admin/banners', { params }),
        getStats: () => apiClient.get('/admin/banners/stats'),
        getById: (id) => apiClient.get(`/admin/banners/${id}`),
        create: (data) => apiClient.post('/admin/banners', data),
        update: (id, data) => apiClient.put(`/admin/banners/${id}`, data),
        delete: (id) => apiClient.delete(`/admin/banners/${id}`),
        toggleStatus: (id) => apiClient.post(`/admin/banners/${id}/toggle`),
        updateOrder: (data) => apiClient.put('/admin/banners/order', data),
        getAnalytics: (id, params) => apiClient.get(`/admin/banners/${id}/analytics`, { params }),
    },

    // ===================================
    // STATIC PAGES
    // ===================================
    pages: {
        getAll: (params) => apiClient.get('/admin/pages', { params }),
        getStats: () => apiClient.get('/admin/pages/stats'),
        getById: (id) => apiClient.get(`/admin/pages/${id}`),
        create: (data) => apiClient.post('/admin/pages', data),
        update: (id, data) => apiClient.put(`/admin/pages/${id}`, data),
        delete: (id) => apiClient.delete(`/admin/pages/${id}`),
        toggleStatus: (id) => apiClient.post(`/admin/pages/${id}/toggle`),
        duplicate: (id) => apiClient.post(`/admin/pages/${id}/duplicate`),
    },

    // ===================================
    // NOTIFICATIONS
    // ===================================
    notifications: {
        getAll: (params) => apiClient.get('/admin/notifications', { params }),
        getStats: () => apiClient.get('/admin/notifications/stats'),
        getScheduled: (params) => apiClient.get('/admin/notifications/scheduled', { params }),
        getTemplates: () => apiClient.get('/admin/notifications/templates'),
        getUserCounts: () => apiClient.get('/admin/notifications/user-counts'),
        getById: (id) => apiClient.get(`/admin/notifications/${id}`),
        send: (data) => apiClient.post('/admin/notifications/send', data),
        sendTest: (data) => apiClient.post('/admin/notifications/send-test', data),
        delete: (id) => apiClient.delete(`/admin/notifications/${id}`),
        cancelScheduled: (id) => apiClient.delete(`/admin/notifications/${id}/cancel`),
    },

    // ===================================
    // REVIEWS
    // ===================================
    reviews: {
        getAll: (params) => apiClient.get('/admin/reviews', { params }),
        getStats: () => apiClient.get('/admin/reviews/stats'),
        getFlagged: (params) => apiClient.get('/admin/reviews/flagged', { params }),
        getById: (id) => apiClient.get(`/admin/reviews/${id}`),
        getByProvider: (providerId, params) => apiClient.get(`/admin/reviews/provider/${providerId}`, { params }),
        getByUser: (userId, params) => apiClient.get(`/admin/reviews/user/${userId}`, { params }),
        flag: (id) => apiClient.post(`/admin/reviews/${id}/flag`),
        unflag: (id) => apiClient.post(`/admin/reviews/${id}/unflag`),
        toggleVisibility: (id) => apiClient.post(`/admin/reviews/${id}/toggle-visibility`),
        respond: (id, response) => apiClient.post(`/admin/reviews/${id}/respond`, { response }),
        deleteResponse: (id) => apiClient.delete(`/admin/reviews/${id}/response`),
        delete: (id) => apiClient.delete(`/admin/reviews/${id}`),
        bulkAction: (data) => apiClient.post('/admin/reviews/bulk-action', data),
    },

    // ===================================
    // SUPPORT TICKETS
    // ===================================
    support: {
        getTickets: (params) => apiClient.get('/admin/support/tickets', { params }),
        getStats: () => apiClient.get('/admin/support/tickets/stats'),
        getTicketById: (id) => apiClient.get(`/admin/support/tickets/${id}`),
        updateTicket: (id, data) => apiClient.put(`/admin/support/tickets/${id}`, data),
        assignTicket: (id, agentId) => apiClient.post(`/admin/support/tickets/${id}/assign`, { agent_id: agentId }),
        addMessage: (id, message) => apiClient.post(`/admin/support/tickets/${id}/messages`, message),
        deleteTicket: (id) => apiClient.delete(`/admin/support/tickets/${id}`),
        getAgents: () => apiClient.get('/admin/support/agents'),
        getCannedResponses: () => apiClient.get('/admin/support/canned-responses'),
        createCannedResponse: (data) => apiClient.post('/admin/support/canned-responses', data),
        updateCannedResponse: (id, data) => apiClient.put(`/admin/support/canned-responses/${id}`, data),
        deleteCannedResponse: (id) => apiClient.delete(`/admin/support/canned-responses/${id}`),
    },

    // ===================================
    // REPORTS
    // ===================================
    reports: {
        getRevenueOverview: (params) => apiClient.get('/admin/reports/revenue/overview', { params }),
        getRevenueByPeriod: (params) => apiClient.get('/admin/reports/revenue/by-period', { params }),
        getBookingStatistics: (params) => apiClient.get('/admin/reports/bookings/statistics', { params }),
        getProviderRevenue: (params) => apiClient.get('/admin/reports/providers/revenue', { params }),
        getClientSpending: (params) => apiClient.get('/admin/reports/clients/spending', { params }),
        getCommissionReport: (params) => apiClient.get('/admin/reports/commission', { params }),
        getPaymentMethodsStats: (params) => apiClient.get('/admin/reports/payment-methods', { params }),
        exportRevenue: (params) => apiClient.get('/admin/reports/revenue/export', { params, responseType: 'blob' }),
        exportBookings: (params) => apiClient.get('/admin/reports/bookings/export', { params, responseType: 'blob' }),
    },

    // ===================================
    // PAYMENT SETTINGS
    // ===================================
    payments: {
        getGateways: () => apiClient.get('/admin/payment-gateways'),
        getGateway: (id) => apiClient.get(`/admin/payment-gateways/${id}`),
        createGateway: (data) => apiClient.post('/admin/payment-gateways', data),
        updateGateway: (id, data) => apiClient.put(`/admin/payment-gateways/${id}`, data),
        deleteGateway: (id) => apiClient.delete(`/admin/payment-gateways/${id}`),
        toggleGateway: (id) => apiClient.post(`/admin/payment-gateways/${id}/toggle`),
        testGateway: (id) => apiClient.post(`/admin/payment-gateways/${id}/test`),
        getSettings: () => apiClient.get('/admin/payment-settings'),
        updateSettings: (data) => apiClient.put('/admin/payment-settings', data),
        getTaxSettings: () => apiClient.get('/admin/payment-settings/tax'),
        updateTaxSettings: (data) => apiClient.put('/admin/payment-settings/tax', data),
        getCommissionSettings: () => apiClient.get('/admin/payment-settings/commission'),
        updateCommissionSettings: (data) => apiClient.put('/admin/payment-settings/commission', data),
    },

    // ===================================
    // GENERAL SETTINGS
    // ===================================
    settings: {
        getAll: () => apiClient.get('/admin/settings'),
        getByKey: (key) => apiClient.get(`/admin/settings/${key}`),
        update: (key, data) => apiClient.put(`/admin/settings/${key}`, data),
        bulkUpdate: (data) => apiClient.post('/admin/settings/bulk-update', data),
        getAppSettings: () => apiClient.get('/admin/settings/app/general'),
        updateAppSettings: (data) => apiClient.put('/admin/settings/app/general', data),
        getBookingSettings: () => apiClient.get('/admin/settings/booking'),
        updateBookingSettings: (data) => apiClient.put('/admin/settings/booking', data),
        getNotificationSettings: () => apiClient.get('/admin/settings/notifications'),
        updateNotificationSettings: (data) => apiClient.put('/admin/settings/notifications', data),
        getMaintenanceMode: () => apiClient.get('/admin/settings/maintenance'),
        toggleMaintenanceMode: () => apiClient.post('/admin/settings/maintenance/toggle'),
        clearCache: () => apiClient.post('/admin/settings/cache/clear'),
    },
};

// Helper function to show success toast
export const showSuccessToast = (message) => {
    Toastify({
        text: message,
        gravity: "top",
        position: "right",
        className: "bg-success",
        duration: 3000
    }).showToast();
};

// Helper function to show error toast
export const showErrorToast = (message) => {
    Toastify({
        text: message,
        gravity: "top",
        position: "right",
        className: "bg-danger",
        duration: 3000
    }).showToast();
};

// Export the API service
export default api;
export { apiClient };
