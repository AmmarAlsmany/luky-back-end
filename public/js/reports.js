/**
 * Reports & Analytics JavaScript
 */

// Initialize charts
let revenueChart = null;
let paymentMethodChart = null;
let bookingsChart = null;
let bookingStatusChart = null;

// Update reports when dates change
function updateReports() {
    const startDate = document.getElementById('start_date').value;
    const endDate = document.getElementById('end_date').value;

    if (!startDate || !endDate) {
        showToast('Please select both start and end dates', 'warning');
        return;
    }

    // Load all reports
    loadRevenueReport(startDate, endDate);
    loadBookingStats(startDate, endDate);
    loadProviderPerformance(startDate, endDate);
    loadClientSpending(startDate, endDate);
}

// Quick date selection
document.getElementById('quick_select').addEventListener('change', function() {
    const value = this.value;
    const today = new Date();
    let startDate, endDate;

    switch(value) {
        case 'today':
            startDate = endDate = formatDate(today);
            break;
        case 'yesterday':
            const yesterday = new Date(today);
            yesterday.setDate(yesterday.getDate() - 1);
            startDate = endDate = formatDate(yesterday);
            break;
        case 'this_week':
            startDate = formatDate(getMonday(today));
            endDate = formatDate(today);
            break;
        case 'last_week':
            const lastWeek = new Date(today);
            lastWeek.setDate(lastWeek.getDate() - 7);
            startDate = formatDate(getMonday(lastWeek));
            const lastWeekEnd = new Date(startDate);
            lastWeekEnd.setDate(lastWeekEnd.getDate() + 6);
            endDate = formatDate(lastWeekEnd);
            break;
        case 'this_month':
            startDate = formatDate(new Date(today.getFullYear(), today.getMonth(), 1));
            endDate = formatDate(today);
            break;
        case 'last_month':
            const lastMonth = new Date(today.getFullYear(), today.getMonth() - 1, 1);
            startDate = formatDate(lastMonth);
            endDate = formatDate(new Date(today.getFullYear(), today.getMonth(), 0));
            break;
        case 'this_year':
            startDate = formatDate(new Date(today.getFullYear(), 0, 1));
            endDate = formatDate(today);
            break;
    }

    document.getElementById('start_date').value = startDate;
    document.getElementById('end_date').value = endDate;
    updateReports();
});

// Load revenue report
async function loadRevenueReport(startDate, endDate) {
    try {
        const response = await fetch(`/reports/revenue?start_date=${startDate}&end_date=${endDate}`);
        const result = await response.json();

        if (result.success) {
            const data = result.data;

            // Update summary cards
            document.getElementById('total_revenue').textContent = formatCurrency(data.total_revenue);
            document.getElementById('total_transactions').textContent = data.total_transactions;
            document.getElementById('total_commission').textContent = formatCurrency(data.total_commission);
            document.getElementById('total_discounts').textContent = formatCurrency(data.total_discounts);

            // Update revenue chart
            updateRevenueChart(data.revenue_by_day);

            // Update payment method chart
            updatePaymentMethodChart(data.revenue_by_method);
        }
    } catch (error) {
        console.error('Error loading revenue report:', error);
        showToast('Failed to load revenue report', 'error');
    }
}

// Load booking statistics
async function loadBookingStats(startDate, endDate) {
    try {
        const response = await fetch(`/reports/bookings?start_date=${startDate}&end_date=${endDate}`);
        const result = await response.json();

        if (result.success) {
            const data = result.data;

            // Update summary cards
            document.getElementById('avg_booking_value').textContent = formatCurrency(data.avg_booking_value);
            document.getElementById('completion_rate').textContent = data.completion_rate + '%';
            document.getElementById('cancellation_rate').textContent = data.cancellation_rate + '%';
            document.getElementById('period_total_bookings').textContent = data.total_bookings;

            // Update bookings chart
            updateBookingsChart(data.bookings_by_day);

            // Update booking status chart
            updateBookingStatusChart(data.bookings_by_status);
        }
    } catch (error) {
        console.error('Error loading booking stats:', error);
        showToast('Failed to load booking statistics', 'error');
    }
}

// Load provider performance
async function loadProviderPerformance(startDate, endDate) {
    try {
        const response = await fetch(`/reports/providers?start_date=${startDate}&end_date=${endDate}&limit=10`);
        const result = await response.json();

        if (result.success) {
            const data = result.data;
            updateProvidersTable(data.top_providers);
        }
    } catch (error) {
        console.error('Error loading provider performance:', error);
        showToast('Failed to load provider performance', 'error');
    }
}

// Load client spending
async function loadClientSpending(startDate, endDate) {
    try {
        const response = await fetch(`/reports/clients?start_date=${startDate}&end_date=${endDate}&limit=10`);
        const result = await response.json();

        if (result.success) {
            const data = result.data;
            updateClientsTable(data.top_clients);
        }
    } catch (error) {
        console.error('Error loading client spending:', error);
        showToast('Failed to load client spending', 'error');
    }
}

// Update revenue chart
function updateRevenueChart(revenueByDay) {
    const ctx = document.getElementById('revenueChart');
    if (!ctx) return;

    const labels = revenueByDay.map(item => item.date);
    const values = revenueByDay.map(item => parseFloat(item.revenue));

    if (revenueChart) {
        revenueChart.destroy();
    }

    revenueChart = new Chart(ctx, {
        type: 'line',
        data: {
            labels: labels,
            datasets: [{
                label: 'Revenue',
                data: values,
                borderColor: '#667eea',
                backgroundColor: 'rgba(102, 126, 234, 0.1)',
                tension: 0.4,
                fill: true
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: false
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        callback: function(value) {
                            return formatCurrency(value);
                        }
                    }
                }
            }
        }
    });
}

// Update payment method chart
function updatePaymentMethodChart(revenueByMethod) {
    const ctx = document.getElementById('paymentMethodChart');
    if (!ctx) return;

    const labels = revenueByMethod.map(item => item.method || 'Unknown');
    const values = revenueByMethod.map(item => parseFloat(item.revenue));
    const colors = ['#667eea', '#764ba2', '#f093fb', '#4facfe', '#00d4aa'];

    if (paymentMethodChart) {
        paymentMethodChart.destroy();
    }

    paymentMethodChart = new Chart(ctx, {
        type: 'doughnut',
        data: {
            labels: labels,
            datasets: [{
                data: values,
                backgroundColor: colors.slice(0, labels.length)
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'bottom'
                }
            }
        }
    });
}

// Update bookings chart
function updateBookingsChart(bookingsByDay) {
    const ctx = document.getElementById('bookingsChart');
    if (!ctx) return;

    // Group by date and status
    const dates = [...new Set(bookingsByDay.map(item => item.date))];
    const statuses = [...new Set(bookingsByDay.map(item => item.status))];

    const datasets = statuses.map((status, index) => {
        const colors = {
            'completed': '#28a745',
            'confirmed': '#17a2b8',
            'pending': '#ffc107',
            'cancelled': '#dc3545'
        };

        return {
            label: status.charAt(0).toUpperCase() + status.slice(1),
            data: dates.map(date => {
                const item = bookingsByDay.find(b => b.date === date && b.status === status);
                return item ? item.count : 0;
            }),
            backgroundColor: colors[status] || '#6c757d'
        };
    });

    if (bookingsChart) {
        bookingsChart.destroy();
    }

    bookingsChart = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: dates,
            datasets: datasets
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                x: {
                    stacked: true
                },
                y: {
                    stacked: true,
                    beginAtZero: true
                }
            }
        }
    });
}

// Update booking status chart
function updateBookingStatusChart(bookingsByStatus) {
    const ctx = document.getElementById('bookingStatusChart');
    if (!ctx) return;

    const labels = bookingsByStatus.map(item => item.status.charAt(0).toUpperCase() + item.status.slice(1));
    const values = bookingsByStatus.map(item => item.count);
    const colors = {
        'Completed': '#28a745',
        'Confirmed': '#17a2b8',
        'Pending': '#ffc107',
        'Cancelled': '#dc3545'
    };

    const backgroundColors = labels.map(label => colors[label] || '#6c757d');

    if (bookingStatusChart) {
        bookingStatusChart.destroy();
    }

    bookingStatusChart = new Chart(ctx, {
        type: 'pie',
        data: {
            labels: labels,
            datasets: [{
                data: values,
                backgroundColor: backgroundColors
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'bottom'
                }
            }
        }
    });
}

// Update providers table
function updateProvidersTable(providers) {
    const tbody = document.querySelector('#providersTable tbody');
    if (!tbody) return;

    if (providers.length === 0) {
        tbody.innerHTML = '<tr><td colspan="6" class="text-center text-muted">No data available</td></tr>';
        return;
    }

    tbody.innerHTML = providers.map(provider => `
        <tr>
            <td>${provider.business_name}</td>
            <td><span class="badge bg-info">${provider.business_type}</span></td>
            <td>${provider.total_bookings}</td>
            <td>${formatCurrency(provider.total_revenue)}</td>
            <td>${formatCurrency(provider.total_commission)}</td>
            <td>${formatCurrency(provider.avg_booking_value)}</td>
        </tr>
    `).join('');
}

// Update clients table
function updateClientsTable(clients) {
    const tbody = document.querySelector('#clientsTable tbody');
    if (!tbody) return;

    if (clients.length === 0) {
        tbody.innerHTML = '<tr><td colspan="6" class="text-center text-muted">No data available</td></tr>';
        return;
    }

    tbody.innerHTML = clients.map(client => `
        <tr>
            <td>${client.name}</td>
            <td>${client.email}</td>
            <td>${client.phone || 'N/A'}</td>
            <td>${client.total_bookings}</td>
            <td>${formatCurrency(client.total_spent)}</td>
            <td>${formatCurrency(client.avg_spent_per_booking)}</td>
        </tr>
    `).join('');
}

// Utility functions
function formatCurrency(amount) {
    return new Intl.NumberFormat('en-SA', {
        style: 'currency',
        currency: 'SAR',
        minimumFractionDigits: 2
    }).format(amount || 0);
}

function formatDate(date) {
    const d = new Date(date);
    const month = String(d.getMonth() + 1).padStart(2, '0');
    const day = String(d.getDate()).padStart(2, '0');
    const year = d.getFullYear();
    return `${year}-${month}-${day}`;
}

function getMonday(date) {
    const d = new Date(date);
    const day = d.getDay();
    const diff = d.getDate() - day + (day === 0 ? -6 : 1);
    return new Date(d.setDate(diff));
}

function showToast(message, type = 'info') {
    const bgColors = {
        'success': 'bg-success',
        'error': 'bg-danger',
        'warning': 'bg-warning',
        'info': 'bg-info'
    };

    const toast = document.createElement('div');
    toast.className = `toast align-items-center text-white ${bgColors[type]} border-0 position-fixed top-0 end-0 m-3`;
    toast.style.zIndex = '9999';
    toast.innerHTML = `
        <div class="d-flex">
            <div class="toast-body">
                ${message}
            </div>
            <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
        </div>
    `;

    document.body.appendChild(toast);

    const bsToast = new bootstrap.Toast(toast);
    bsToast.show();

    setTimeout(() => {
        toast.remove();
    }, 3000);
}

// Initialize on page load
document.addEventListener('DOMContentLoaded', function() {
    // Set default dates (this month)
    const today = new Date();
    const firstDay = new Date(today.getFullYear(), today.getMonth(), 1);

    document.getElementById('start_date').value = formatDate(firstDay);
    document.getElementById('end_date').value = formatDate(today);

    // Load initial reports
    updateReports();
});
