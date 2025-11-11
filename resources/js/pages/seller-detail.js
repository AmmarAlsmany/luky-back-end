import ApexCharts from "apexcharts";

// Provider Details page charts
// Expects window.providerPageData injected by Blade with shape:
// { providerId: number|null, revenue: { total_revenue, monthly_revenue, yearly_revenue } }

(function () {
  const el = document.querySelector('#provider-revenue-chart');
  if (!el || typeof window === 'undefined') return;

  const pageData = window.providerPageData || {};
  const revenue = pageData.revenue || {};

  const monthVal = Number(revenue.monthly_revenue || 0);
  const yearVal = Number(revenue.yearly_revenue || 0);

  const hasData = monthVal > 0 || yearVal > 0;

  if (!hasData) {
    el.innerHTML = '<div class="text-center text-muted py-4"><iconify-icon icon="solar:chart-bold-duotone" class="fs-36 mb-2 d-block"></iconify-icon><p class="mb-0">No revenue data yet</p><small>Revenue will appear as bookings complete</small></div>';
    return;
  }

  const options = {
    chart: {
      height: 328,
      type: 'bar',
      toolbar: { show: false },
    },
    series: [{
      name: 'Revenue (SAR)',
      data: [monthVal, yearVal]
    }],
    colors: ['#47ad94', '#2563eb'],
    plotOptions: {
      bar: {
        horizontal: false,
        columnWidth: '45%',
        borderRadius: 6,
        dataLabels: { position: 'top' }
      }
    },
    dataLabels: {
      enabled: true,
      formatter: (val) => {
        if (val >= 1_000_000) return 'SAR ' + (val / 1_000_000).toFixed(1) + 'M';
        if (val >= 1_000) return 'SAR ' + (val / 1_000).toFixed(1) + 'k';
        return 'SAR ' + val.toFixed(0);
      },
      offsetY: -12,
      style: { fontSize: '12px' }
    },
    xaxis: {
      categories: ['This Month', 'This Year'],
      axisBorder: { show: false },
      axisTicks: { show: false }
    },
    yaxis: {
      labels: {
        formatter: (val) => {
          if (val >= 1_000_000) return (val / 1_000_000).toFixed(1) + 'M';
          if (val >= 1_000) return (val / 1_000).toFixed(0) + 'k';
          return val.toFixed(0);
        }
      }
    },
    grid: { strokeDashArray: 4 },
    legend: { show: false }
  };

  const chart = new ApexCharts(el, options);
  chart.render();
})();

// Provider Chat Message Form Handling
(function() {
  const form = document.getElementById('providerChatMessageForm');
  if (!form) return;

  const pageData = window.providerPageData || {};
  const providerId = pageData.providerId;

  if (!providerId) {
    console.error('Provider ID not found');
    return;
  }

  form.addEventListener('submit', function(e) {
    e.preventDefault();

    const messageText = document.getElementById('chatMessageText');
    const submitBtn = document.getElementById('chatSendBtn');
    const spinner = document.getElementById('msgSpinner');

    if (!messageText || !messageText.value.trim()) {
      if (window.Swal) {
        Swal.fire('Error', 'Please enter a message', 'error');
      } else {
        alert('Please enter a message');
      }
      return;
    }

    // Disable button and show spinner
    submitBtn.disabled = true;
    if (spinner) spinner.classList.remove('d-none');

    // Get CSRF token
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content ||
                      document.querySelector('input[name="_token"]')?.value || '';

    fetch(`/provider/${providerId}/send-message`, {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': csrfToken,
        'Accept': 'application/json'
      },
      body: JSON.stringify({
        message: messageText.value
      })
    })
    .then(response => response.json())
    .then(data => {
      if (data.success) {
        // Close modal
        const modal = bootstrap.Modal.getInstance(document.getElementById('providerChatMessage'));
        if (modal) modal.hide();

        // Clear form
        form.reset();

        // Show success
        if (window.Swal) {
          Swal.fire({
            icon: 'success',
            title: 'Message Sent!',
            text: data.message || 'Your message has been sent successfully',
            timer: 3000,
            showConfirmButton: false
          });
        } else {
          alert('Message sent successfully!');
        }
      } else {
        throw new Error(data.message || 'Failed to send message');
      }
    })
    .catch(error => {
      console.error('Error:', error);
      if (window.Swal) {
        Swal.fire('Error', error.message || 'Failed to send message', 'error');
      } else {
        alert('Error: ' + (error.message || 'Failed to send message'));
      }
    })
    .finally(() => {
      submitBtn.disabled = false;
      if (spinner) spinner.classList.add('d-none');
    });
  });

  // Character count
  const messageText = document.getElementById('chatMessageText');
  const charCount = document.getElementById('msgCharCount');
  if (messageText && charCount) {
    messageText.addEventListener('input', function() {
      charCount.textContent = this.value.length;
    });
  }
})();

// Provider Notification Form Handling
(function() {
  const form = document.getElementById('providerNotificationForm');
  if (!form) return;

  const pageData = window.providerPageData || {};
  const providerId = pageData.providerId;

  if (!providerId) {
    console.error('Provider ID not found');
    return;
  }

  form.addEventListener('submit', function(e) {
    e.preventDefault();

    const title = document.getElementById('notifTitle');
    const message = document.getElementById('notifMessage');
    const type = document.getElementById('notifType');
    const submitBtn = document.getElementById('notifSendBtn');
    const spinner = document.getElementById('notifSpinner');

    if (!title || !title.value.trim() || !message || !message.value.trim()) {
      if (window.Swal) {
        Swal.fire('Error', 'Please fill in all required fields', 'error');
      } else {
        alert('Please fill in all required fields');
      }
      return;
    }

    // Disable button and show spinner
    submitBtn.disabled = true;
    if (spinner) spinner.classList.remove('d-none');

    // Get CSRF token
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content ||
                      document.querySelector('input[name="_token"]')?.value || '';

    fetch(`/provider/${providerId}/send-notification`, {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': csrfToken,
        'Accept': 'application/json'
      },
      body: JSON.stringify({
        title: title.value,
        message: message.value,
        notification_type: type ? type.value : 'general'
      })
    })
    .then(response => response.json())
    .then(data => {
      if (data.success) {
        // Close modal
        const modal = bootstrap.Modal.getInstance(document.getElementById('providerNotificationModal'));
        if (modal) modal.hide();

        // Clear form
        form.reset();

        // Show success
        if (window.Swal) {
          Swal.fire({
            icon: 'success',
            title: 'Notification Sent!',
            text: data.message || 'Notification has been sent successfully',
            timer: 3000,
            showConfirmButton: false
          });
        } else {
          alert('Notification sent successfully!');
        }
      } else {
        throw new Error(data.message || 'Failed to send notification');
      }
    })
    .catch(error => {
      console.error('Error:', error);
      if (window.Swal) {
        Swal.fire('Error', error.message || 'Failed to send notification', 'error');
      } else {
        alert('Error: ' + (error.message || 'Failed to send notification'));
      }
    })
    .finally(() => {
      submitBtn.disabled = false;
      if (spinner) spinner.classList.add('d-none');
    });
  });
})();

