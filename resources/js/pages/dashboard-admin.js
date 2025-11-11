/**
 * Admin Dashboard Page
 * Loads dashboard statistics and charts from backend API
 */

import ApexCharts from 'apexcharts';
import api, { showErrorToast } from '../services/api';
import authService from '../services/auth';

class AdminDashboard {
    constructor() {
        this.init();
    }

    async init() {
        // Require authentication
        authService.requireAuth();

        // Load dashboard data
        await this.loadDashboardOverview();
        await this.loadRevenueChart();
        await this.loadBookingsChart();
        await this.loadTopProviders();
        await this.loadRecentActivities();
    }

    /**
     * Load dashboard overview statistics
     */
    async loadDashboardOverview() {
        try {
            // Try API endpoint first, then fall back to web route
            let response;
            try {
                response = await api.dashboard.getOverview();
            } catch (apiError) {
                console.warn('API endpoint failed, trying web route:', apiError);
                response = await api.dashboard.getWebDashboard();
            }
            
            const data = response.data.data || response.data;
            const kpis = data.kpis || data.overview || data;

            // Update statistics on page
            this.updateStatistic('total-revenue', kpis.total_revenue);
            this.updateStatistic('total-bookings', kpis.total_bookings);
            this.updateStatistic('total-clients', kpis.total_clients);
            this.updateStatistic('total-providers', kpis.total_providers);
            this.updateStatistic('pending-bookings', kpis.pending_bookings || kpis.active_bookings);
            this.updateStatistic('active-providers', kpis.active_providers);

        } catch (error) {
            console.error('Failed to load dashboard overview:', error);
            showErrorToast('Failed to load dashboard statistics');
        }
    }

    /**
     * Load revenue chart data
     */
    async loadRevenueChart() {
        try {
            // Try API endpoint first, then fall back to web route
            let response;
            try {
                response = await api.dashboard.getRevenueChart({
                    period: 'month',
                    days: 30
                });
            } catch (apiError) {
                console.warn('API endpoint failed, trying web route:', apiError);
                response = await api.dashboard.getWebRevenueChart();
            }
            
            const data = response.data.data || response.data;

            // Initialize or update revenue chart
            this.renderRevenueChart(data);

        } catch (error) {
            console.error('Failed to load revenue chart:', error);
        }
    }

    /**
     * Load bookings chart data
     */
    async loadBookingsChart() {
        try {
            // Try API endpoint first, then fall back to web route
            let response;
            try {
                response = await api.dashboard.getBookingsChart({
                    days: 30
                });
            } catch (apiError) {
                console.warn('API endpoint failed, trying web route:', apiError);
                response = await api.dashboard.getWebBookingsChart();
            }
            
            const data = response.data.data || response.data;

            // Initialize or update bookings chart
            this.renderBookingsChart(data);

        } catch (error) {
            console.error('Failed to load bookings chart:', error);
        }
    }

    /**
     * Load top providers
     */
    async loadTopProviders() {
        try {
            // Try API endpoint first, then fall back to web route
            let response;
            try {
                response = await api.dashboard.getTopProviders({
                    limit: 5
                });
            } catch (apiError) {
                console.warn('API endpoint failed, trying web route:', apiError);
                response = await api.dashboard.getWebTopProviders();
            }
            
            const data = response.data.data || response.data;
            const providers = data.providers || data;

            // Render providers list
            this.renderTopProviders(providers);

        } catch (error) {
            console.error('Failed to load top providers:', error);
        }
    }

    /**
     * Load recent activities
     */
    async loadRecentActivities() {
        try {
            // Try API endpoint first, then fall back to web route
            let response;
            try {
                response = await api.dashboard.getRecentActivities();
            } catch (apiError) {
                console.warn('API endpoint failed, trying web route:', apiError);
                response = await api.dashboard.getWebRecentActivities();
            }
            
            const data = response.data.data || response.data;
            const activities = data.activities || data;

            // Render activities list
            this.renderRecentActivities(activities);

        } catch (error) {
            console.error('Failed to load recent activities:', error);
        }
    }

    /**
     * Update statistic value on page
     */
    updateStatistic(id, value) {
        const element = document.getElementById(id);
        if (element) {
            // Check if it's a counter element
            if (element.classList.contains('counter-value')) {
                element.setAttribute('data-target', value);
                element.innerText = '0'; // Reset before counting
            } else {
                element.innerText = this.formatNumber(value);
            }
        }
    }

    /**
     * Format number with commas
     */
    formatNumber(num) {
        if (typeof num !== 'number') return num;
        return num.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ',');
    }

    /**
     * Render revenue chart (integrate with your chart library)
     */
    renderRevenueChart(data) {
        console.log('Revenue chart data:', data);
        
        // Get chart element
        const chartElement = document.querySelector('#revenue-chart');
        if (!chartElement) {
            console.warn('Chart element #revenue-chart not found');
            return;
        }
        
        // Normalize data format from different API endpoints
        let chartData = [];
        let labels = [];
        
        if (data.chart_data) {
            // API format
            chartData = data.chart_data.map(item => parseFloat(item.revenue || 0));
            labels = data.chart_data.map(item => item.date);
        } else if (data.labels && data.data) {
            // Web route format
            chartData = data.data;
            labels = data.labels;
        } else {
            console.warn('Unexpected data format for revenue chart');
            return;
        }
        
        // Create chart
        const chart = new ApexCharts(chartElement, {
            series: [{
                name: 'Revenue',
                data: chartData
            }],
            chart: {
                type: 'area',
                height: 350,
                toolbar: {
                    show: false
                }
            },
            dataLabels: {
                enabled: false
            },
            stroke: {
                curve: 'smooth',
                width: 2
            },
            fill: {
                type: 'gradient',
                gradient: {
                    shadeIntensity: 1,
                    opacityFrom: 0.7,
                    opacityTo: 0.3,
                    stops: [0, 90, 100]
                }
            },
            xaxis: {
                categories: labels,
                labels: {
                    rotate: -45,
                    style: {
                        fontSize: '12px'
                    }
                }
            },
            yaxis: {
                labels: {
                    formatter: (val) => {
                        if (val >= 1000) return `${(val/1000).toFixed(1)}k`;
                        return val;
                    }
                }
            },
            colors: ['#7f56da'],
            tooltip: {
                y: {
                    formatter: (val) => `SAR ${val.toLocaleString()}`
                }
            }
        });
        
        chart.render();
    }

    /**
     * Render bookings chart
     */
    renderBookingsChart(data) {
        console.log('Bookings chart data:', data);
        
        // Get chart element
        const chartElement = document.querySelector('#bookings-chart');
        if (!chartElement) {
            console.warn('Chart element #bookings-chart not found');
            return;
        }
        
        // Normalize data format from different API endpoints
        let chartData = [];
        let labels = [];
        let datasets = [];
        
        if (data.chart_data) {
            // API format
            labels = data.chart_data.map(item => item.date);
            
            // Create datasets for different booking statuses
            const statuses = ['pending', 'confirmed', 'completed', 'cancelled'];
            datasets = statuses.map(status => ({
                name: status.charAt(0).toUpperCase() + status.slice(1),
                data: data.chart_data.map(item => item[status] || 0)
            }));
            
        } else if (data.labels && data.datasets) {
            // Web route format
            labels = data.labels;
            datasets = data.datasets.map(ds => ({
                name: ds.label,
                data: ds.data
            }));
        } else {
            console.warn('Unexpected data format for bookings chart');
            return;
        }
        
        // Define colors for different booking statuses
        const colors = ['#ff9f43', '#7f56da', '#22c55e', '#ff5c75'];
        
        // Create chart
        const chart = new ApexCharts(chartElement, {
            series: datasets,
            chart: {
                type: 'bar',
                height: 350,
                stacked: true,
                toolbar: {
                    show: false
                }
            },
            plotOptions: {
                bar: {
                    horizontal: false,
                    columnWidth: '55%',
                    borderRadius: 2
                },
            },
            dataLabels: {
                enabled: false
            },
            xaxis: {
                categories: labels,
                labels: {
                    rotate: -45,
                    style: {
                        fontSize: '12px'
                    }
                }
            },
            yaxis: {
                title: {
                    text: 'Number of Bookings'
                }
            },
            legend: {
                position: 'top'
            },
            fill: {
                opacity: 1
            },
            colors: colors
        });
        
        chart.render();
    }

    /**
     * Render top providers list
     */
    renderTopProviders(providers) {
        const container = document.getElementById('top-providers-list');
        if (!container || !Array.isArray(providers)) return;

        let html = '';
        providers.forEach((provider, index) => {
            // Normalize provider data from different API endpoints
            const name = provider.name || provider.business_name || 'N/A';
            const bookingsCount = provider.completed_bookings_count || provider.total_bookings || provider.bookings_count || 0;
            const rating = provider.rating || provider.average_rating || 0;
            const revenue = provider.total_revenue || provider.revenue || 0;
            
            html += `
                <div class="d-flex align-items-center mb-3">
                    <div class="flex-shrink-0">
                        <span class="badge bg-primary rounded-circle" style="width: 40px; height: 40px; line-height: 40px;">
                            ${index + 1}
                        </span>
                    </div>
                    <div class="flex-grow-1 ms-3">
                        <h6 class="mb-0">${name}</h6>
                        <small class="text-muted">${bookingsCount} bookings · SAR ${this.formatNumber(revenue)}</small>
                    </div>
                    <div class="flex-shrink-0">
                        <span class="badge bg-success">${parseFloat(rating).toFixed(1)} ⭐</span>
                    </div>
                </div>
            `;
        });

        container.innerHTML = html || '<p class="text-muted">No data available</p>';
    }

    /**
     * Render recent activities list
     */
    renderRecentActivities(activities) {
        const container = document.getElementById('recent-activities-list');
        if (!container || !Array.isArray(activities)) return;

        let html = '';
        activities.forEach((activity) => {
            // Normalize activity data from different API endpoints
            const type = activity.type || 'default';
            const icon = this.getActivityIcon(type);
            const timestamp = activity.created_at || activity.timestamp || new Date();
            const timeAgo = this.formatTimeAgo(timestamp);
            
            // Get description based on available data
            let description = activity.description || '';
            if (!description && activity.title) {
                description = activity.title;
                if (activity.user) {
                    description += ` by ${activity.user}`;
                }
                if (activity.provider) {
                    description += ` with ${activity.provider}`;
                }
            }

            html += `
                <div class="d-flex align-items-start mb-3">
                    <div class="flex-shrink-0">
                        <span class="avatar avatar-sm rounded-circle bg-light text-primary">
                            <i class="${icon}"></i>
                        </span>
                    </div>
                    <div class="flex-grow-1 ms-3">
                        <p class="mb-1">${description}</p>
                        <small class="text-muted">${timeAgo}</small>
                    </div>
                </div>
            `;
        });

        container.innerHTML = html || '<p class="text-muted">No recent activities</p>';
    }

    /**
     * Get icon based on activity type
     */
    getActivityIcon(type) {
        const icons = {
            'booking': 'ri-calendar-check-line',
            'payment': 'ri-money-dollar-circle-line',
            'user': 'ri-user-add-line',
            'provider': 'ri-store-line',
            'review': 'ri-star-line',
            'default': 'ri-notification-line'
        };
        return icons[type] || icons.default;
    }

    /**
     * Format timestamp to relative time
     */
    formatTimeAgo(timestamp) {
        // Simple implementation - you can use moment.js or dayjs for better formatting
        const date = new Date(timestamp);
        const now = new Date();
        const seconds = Math.floor((now - date) / 1000);

        if (seconds < 60) return 'Just now';
        if (seconds < 3600) return `${Math.floor(seconds / 60)} minutes ago`;
        if (seconds < 86400) return `${Math.floor(seconds / 3600)} hours ago`;
        return `${Math.floor(seconds / 86400)} days ago`;
    }
}

// Initialize when DOM is ready
document.addEventListener('DOMContentLoaded', function () {
    new AdminDashboard();
});

export default AdminDashboard;
