@extends('layouts.vertical', ['title' => __('reports.reports_revenue')])
@section('css')
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/choices.js/public/assets/styles/choices.min.css">
  <style>
    .report-card {
      transition: all 0.3s ease;
      border-left: 4px solid transparent;
    }
    .report-card:hover {
      transform: translateY(-2px);
      box-shadow: 0 8px 25px rgba(0,0,0,0.1);
    }
    .report-card.revenue { border-left-color: #28a745; }
    .report-card.orders { border-left-color: #007bff; }
    .report-card.users { border-left-color: #ffc107; }
    .report-card.providers { border-left-color: #dc3545; }
    
    .chart-container {
      height: 300px;
      position: relative;
    }
    
    .filter-section {
      background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
      border-radius: 15px;
      padding: 2rem;
      margin-bottom: 2rem;
    }
    
    .report-section {
      background: #fff;
      border-radius: 15px;
      box-shadow: 0 4px 20px rgba(0,0,0,0.08);
      padding: 2rem;
    }
    
    .metric-card {
      background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
      border-radius: 12px;
      padding: 1.5rem;
      text-align: center;
      transition: all 0.3s ease;
    }
    
    .metric-card:hover {
      transform: translateY(-3px);
      box-shadow: 0 8px 25px rgba(0,0,0,0.15);
    }
    
    .metric-value {
      font-size: 2.5rem;
      font-weight: 700;
      margin-bottom: 0.5rem;
    }
    
    .metric-label {
      color: #6c757d;
      font-size: 0.9rem;
      text-transform: uppercase;
      letter-spacing: 0.5px;
    }
    
    .trend-up { color: #28a745; }
    .trend-down { color: #dc3545; }
    .trend-neutral { color: #6c757d; }
  </style>
@endsection

@section('content')

{{-- ===== Section 1: Report Filters ===== --}}
<div class="filter-section">
  <div class="row align-items-center">
    <div class="col-lg-12">
      <h3 class="text-white mb-3">
        <iconify-icon icon="solar:chart-bold-duotone" class="me-2"></iconify-icon>
        {{ __('reports.generate_reports_analysis') }}
      </h3>
      <p class="text-white-50 mb-0">{{ __('reports.select_criteria') }}</p>
    </div>
      </div>

  <div class="row g-4 mt-3">
    <!-- Report Type -->
    <div class="col-lg-3 col-md-6">
      <label class="form-label text-white">{{ __('reports.report_type') }}</label>
      <select id="reportType" class="form-select" data-choices>
        <option value="revenue">{{ __('reports.revenue_analysis') }}</option>
        <option value="orders">{{ __('reports.orders_report') }}</option>
        <option value="users">{{ __('reports.users_report') }}</option>
        <option value="providers">{{ __('reports.providers_report') }}</option>
        <option value="commission">{{ __('reports.commission_report') }}</option>
        <option value="activities">{{ __('reports.activities_report') }}</option>
      </select>
          </div>

    <!-- Time Period -->
    <div class="col-lg-3 col-md-6">
      <label class="form-label text-white">{{ __('reports.time_period') }}</label>
      <select id="timePeriod" class="form-select" data-choices>
        <option value="daily">{{ __('reports.daily') }}</option>
        <option value="weekly">{{ __('reports.weekly') }}</option>
        <option value="monthly" selected>{{ __('reports.monthly') }}</option>
        <option value="yearly">{{ __('reports.yearly') }}</option>
        <option value="custom">{{ __('reports.custom_range') }}</option>
            </select>
          </div>

    <!-- Date Range (for custom) -->
    <div class="col-lg-2 col-md-6" id="customDateRange" style="display: none;">
      <label class="form-label text-white">{{ __('reports.start_date') }}</label>
      <input type="date" id="fromDate" class="form-control" value="{{ \Carbon\Carbon::now()->startOfMonth()->format('Y-m-d') }}">
    </div>
    
    <div class="col-lg-2 col-md-6" id="customDateRangeTo" style="display: none;">
      <label class="form-label text-white">{{ __('reports.end_date') }}</label>
      <input type="date" id="toDate" class="form-control" value="{{ \Carbon\Carbon::now()->format('Y-m-d') }}">
    </div>
    
    <!-- Region Filter -->
    <div class="col-lg-3 col-md-6">
      <label class="form-label text-white">{{ __('reports.region') }}</label>
      <select id="regionFilter" class="form-select" data-choices>
        <option value="">{{ __('reports.all_regions') }}</option>
        @if(isset($cities) && count($cities) > 0)
          @foreach($cities as $city)
            <option value="{{ $city['id'] }}">
              {{ app()->getLocale() === 'ar' ? $city['name_ar'] : $city['name_en'] }}
            </option>
          @endforeach
        @endif
            </select>
          </div>

    <!-- Activity Type -->
    <div class="col-lg-3 col-md-6">
      <label class="form-label text-white">{{ __('reports.activity_type') }}</label>
      <select id="activityFilter" class="form-select" data-choices>
        <option value="">{{ __('reports.all_activities') }}</option>
        <option value="hair_beauty">{{ __('reports.hair_beauty') }}</option>
        <option value="nails_makeup">{{ __('reports.nails_makeup') }}</option>
        <option value="spa">{{ __('reports.spa_services') }}</option>
        <option value="clinics">{{ __('reports.medical_clinics') }}</option>
        <option value="fitness">{{ __('reports.fitness_wellness') }}</option>
            </select>
          </div>
    
    <!-- Metric Type -->
    <div class="col-lg-3 col-md-6">
      <label class="form-label text-white">{{ __('reports.metric_type') }}</label>
      <select id="metricType" class="form-select" data-choices>
        <option value="counts">{{ __('reports.counts_only') }}</option>
        <option value="totals">{{ __('reports.totals_only') }}</option>
        <option value="both" selected>{{ __('reports.counts_totals') }}</option>
      </select>
        </div>
    
    <!-- Export Format -->
    <div class="col-lg-3 col-md-6">
      <label class="form-label text-white">{{ __('reports.export_format') }}</label>
      <select id="exportFormat" class="form-select" data-choices>
        <option value="csv" selected>{{ __('reports.csv_available') }}</option>
        <option value="excel">{{ __('reports.excel_coming') }}</option>
        <option value="pdf">{{ __('reports.pdf_coming') }}</option>
      </select>
    </div>
  </div>
  
  <!-- Generate Report Button -->
  <div class="row mt-4">
    <div class="col-12 text-end">
      <button type="button" class="btn btn-light btn-lg px-4" id="generateReport">
        <iconify-icon icon="solar:chart-2-bold-duotone" class="me-2"></iconify-icon>
        {{ __('reports.generate_report') }}
      </button>
    </div>
  </div>
</div>

{{-- ===== Section 2: Report Display ===== --}}
<div class="report-section">
  <div class="row align-items-center mb-4">
    <div class="col-lg-8">
      <h4 class="mb-2">
        <iconify-icon icon="solar:document-text-bold-duotone" class="me-2"></iconify-icon>
        {{ __('reports.generated_report') }}
      </h4>
      <p class="text-muted mb-0">{{ __('reports.comprehensive_analysis') }}</p>
    </div>
    <div class="col-lg-4 text-end">
      <button type="button" class="btn btn-outline-primary" id="exportReport">
        <iconify-icon icon="solar:export-bold-duotone" class="me-1"></iconify-icon>
        {{ __('reports.export_report') }}
      </button>
    </div>
  </div>
  
  <!-- Report Content -->
  <div id="reportContent">
    <!-- No Report State -->
    <div id="noReportState" class="text-center py-5" style="display: none;">
      <div class="mb-4">
        <iconify-icon icon="solar:chart-2-bold-duotone" class="text-muted" style="font-size: 4rem;"></iconify-icon>
      </div>
      <h4 class="text-muted mb-3">{{ __('reports.no_report_generated') }}</h4>
      <p class="text-muted mb-0">{{ __('reports.select_criteria_instruction') }}</p>
    </div>
    
    <!-- Loading State -->
    <div id="loadingState" class="text-center py-5" style="display: none;">
      <div class="spinner-border text-primary mb-3" role="status">
        <span class="visually-hidden">{{ __('reports.loading') }}</span>
      </div>
      <h5 class="text-muted">{{ __('reports.generating_report') }}</h5>
      <p class="text-muted">{{ __('reports.please_wait') }}</p>
                  </div>
    
    <!-- Report Results -->
    <div id="reportResults">
      <!-- Key Metrics Cards -->
      <div class="row g-4 mb-4">
        <div class="col-lg-3 col-md-6">
          <div class="metric-card revenue">
            <div class="metric-value trend-up" id="totalRevenue">{{ number_format($stats['total_revenue'] ?? 0, 2, '.', ',') }} SAR</div>
            <div class="metric-label">{{ __('reports.total_revenue') }}</div>
                    </div>
                  </div>
        
        <div class="col-lg-3 col-md-6">
          <div class="metric-card orders">
            <div class="metric-value trend-up" id="totalOrders">{{ number_format($stats['total_bookings'] ?? 0) }}</div>
            <div class="metric-label">{{ __('reports.total_orders') }}</div>
          </div>
                  </div>
        
        <div class="col-lg-3 col-md-6">
          <div class="metric-card users">
            <div class="metric-value trend-up" id="totalUsers">{{ number_format($stats['total_clients'] ?? 0) }}</div>
            <div class="metric-label">{{ __('reports.active_users') }}</div>
          </div>
                  </div>
        
        <div class="col-lg-3 col-md-6">
          <div class="metric-card providers">
            <div class="metric-value trend-neutral" id="totalProviders">{{ number_format($stats['active_providers'] ?? 0) }}</div>
            <div class="metric-label">{{ __('reports.active_providers') }}</div>
                    </div>
                  </div>
                  </div>
      
      <!-- Charts Section -->
      <div class="row g-4 mb-4">
        <div class="col-lg-8">
          <div class="card">
            <div class="card-header">
              <h5 class="card-title mb-0">
                <iconify-icon icon="solar:chart-bold-duotone" class="me-2"></iconify-icon>
                {{ __('reports.revenue_trend') }}
              </h5>
            </div>
            <div class="card-body">
              <div class="chart-container">
                <canvas id="revenueChart"></canvas>
              </div>
            </div>
          </div>
                  </div>
        
        <div class="col-lg-4">
          <div class="card">
            <div class="card-header">
              <h5 class="card-title mb-0">
                <iconify-icon icon="solar:pie-chart-bold-duotone" class="me-2"></iconify-icon>
                {{ __('reports.revenue_by_payment_method') }}
              </h5>
            </div>
            <div class="card-body">
              <div class="chart-container">
                <canvas id="methodChart"></canvas>
              </div>
            </div>
                    </div>
                  </div>
                  </div>
      
      <!-- Detailed Table -->
      <div class="card">
        <div class="card-header">
          <h5 class="card-title mb-0">
            <iconify-icon icon="solar:table-bold-duotone" class="me-2"></iconify-icon>
            {{ __('reports.detailed_breakdown') }}
          </h5>
                  </div>
        <div class="card-body p-0">
          <div class="table-responsive">
            <table class="table table-hover mb-0">
              <thead class="bg-light">
                <tr>
                  <th>{{ __('reports.period') }}</th>
                  <th>{{ __('reports.revenue') }}</th>
                  <th>{{ __('reports.orders') }}</th>
                  <th>{{ __('reports.users') }}</th>
                  <th>{{ __('reports.providers') }}</th>
                  <th>{{ __('reports.avg_order_value') }}</th>
                  <th>{{ __('reports.growth_percent') }}</th>
              </tr>
              </thead>
              <tbody id="reportTableBody">

            </tbody>
          </table>
        </div>
      </div>
      </div>
    </div>
  </div>
</div>

@endsection

@section('script-bottom')
  <script src="https://cdn.jsdelivr.net/npm/choices.js/public/assets/scripts/choices.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
  <script>
    // Wait for Choices.js to load
    document.addEventListener('DOMContentLoaded', function() {
      // Initialize Choices
      if (typeof Choices !== 'undefined') {
        document.querySelectorAll('select[data-choices]').forEach(function (el) {
          new Choices(el, {
            shouldSort: false,
            searchEnabled: false
          });
        });
        console.log('Choices.js initialized successfully');
      } else {
        console.error('Choices.js library not loaded');
      }
    });

    // Time period change handler (works with Choices.js)
    const timePeriodElement = document.getElementById('timePeriod');
    if (timePeriodElement) {
      timePeriodElement.addEventListener('change', function(e) {
        const customRange = document.getElementById('customDateRange');
        const customRangeTo = document.getElementById('customDateRangeTo');
        const value = e.target.value || this.value;
        
        console.log('Time period changed to:', value);
        
        if (value === 'custom') {
          customRange.style.display = 'block';
          customRangeTo.style.display = 'block';
          console.log('Showing custom date range fields');
        } else {
          customRange.style.display = 'none';
          customRangeTo.style.display = 'none';
          console.log('Hiding custom date range fields');
        }
      });
      
      // Also listen for Choices.js specific event
      timePeriodElement.addEventListener('addItem', function(event) {
        const customRange = document.getElementById('customDateRange');
        const customRangeTo = document.getElementById('customDateRangeTo');
        const value = event.detail.value;
        
        console.log('Choices.js item selected:', value);
        
        if (value === 'custom') {
          customRange.style.display = 'block';
          customRangeTo.style.display = 'block';
        } else {
          customRange.style.display = 'none';
          customRangeTo.style.display = 'none';
        }
      });
    }

    // Generate Report Button
    document.getElementById('generateReport').addEventListener('click', function() {
      generateReport();
    });

    // Export Report Button
    document.getElementById('exportReport').addEventListener('click', function() {
      exportReport();
    });

    function generateReport() {
      const noReportState = document.getElementById('noReportState');
      const loadingState = document.getElementById('loadingState');
      const reportResults = document.getElementById('reportResults');
      const reportType = document.getElementById('reportType').value;
      const timePeriod = document.getElementById('timePeriod').value;
      const fromDate = document.getElementById('fromDate').value;
      const toDate = document.getElementById('toDate').value;
      
      // Hide no report state, show loading
      noReportState.style.display = 'none';
      loadingState.style.display = 'block';
      reportResults.style.display = 'none';
      
      let url = '';
      if (reportType === 'revenue') url = '{{ route('reports.revenue') }}';
      else if (reportType === 'orders') url = '{{ route('reports.bookings') }}';
      else if (reportType === 'providers') url = '{{ route('reports.providers') }}';
      else if (reportType === 'users') url = '{{ route('reports.clients') }}';
      else if (reportType === 'commission') url = '{{ route('reports.commission') }}';
      else url = '{{ route('reports.revenue') }}';
      
      const params = new URLSearchParams();
      params.set('period', timePeriod);
      if (timePeriod === 'custom' && fromDate && toDate) {
        params.set('start_date', fromDate);
        params.set('end_date', toDate);
      }
      
      fetch(`${url}?${params.toString()}`, { headers: { 'Accept': 'application/json' }})
        .then(resp => resp.json())
        .then(data => {
          loadingState.style.display = 'none';
          reportResults.style.display = 'block';
          if (!data.success) throw new Error('Failed to load report');
          
          const d = data.data || {};
          // Update metrics and table according to report type
          if (reportType === 'revenue') {
            const revenueEl = document.getElementById('totalRevenue');
            const ordersEl = document.getElementById('totalOrders');
            if (revenueEl) revenueEl.textContent = `${Number(d.total_revenue || 0).toLocaleString(undefined, {minimumFractionDigits:2, maximumFractionDigits:2})} SAR`;
            if (ordersEl) ordersEl.textContent = Number(d.total_transactions || 0).toLocaleString();
            const tbody = document.getElementById('reportTableBody');
            if (tbody && Array.isArray(d.revenue_by_day)) {
              tbody.innerHTML = d.revenue_by_day.map(row => `
                <tr>
                  <td>${row.date}</td>
                  <td><span class="fw-semibold text-success">${Number(row.revenue || 0).toLocaleString(undefined, {minimumFractionDigits:2, maximumFractionDigits:2})} SAR</span></td>
                  <td>${Number(row.transactions || 0).toLocaleString()}</td>
                  <td>—</td>
                  <td>—</td>
                  <td>${row.transactions ? (Number(row.revenue)/Number(row.transactions)).toFixed(2) : '—'}</td>
                  <td><span class="badge bg-success-subtle text-success">—</span></td>
                </tr>
              `).join('');
            }
            // Build charts from live data
            const revCanvas = document.getElementById('revenueChart');
            if (revCanvas && Array.isArray(d.revenue_by_day)) {
              const labels = d.revenue_by_day.map(r => r.date);
              const values = d.revenue_by_day.map(r => Number(r.revenue || 0));
              revCanvas.style.display = 'block';
              if (window.revenueChart && typeof window.revenueChart.destroy === 'function') {
                window.revenueChart.destroy();
              }
              window.revenueChart = new Chart(revCanvas.getContext('2d'), {
                type: 'line',
                data: {
                  labels,
                  datasets: [{
                    label: '{{ __('reports.revenue_label') }}',
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
                  plugins: { legend: { display: false } },
                  scales: { y: { beginAtZero: true } }
                }
              });
            }
            const methodCanvas = document.getElementById('methodChart');
            if (methodCanvas && Array.isArray(d.revenue_by_method)) {
              const labels = d.revenue_by_method.map(m => m.method || '{{ __('reports.unknown') }}');
              const values = d.revenue_by_method.map(m => Number(m.revenue || 0));
              methodCanvas.style.display = 'block';
              if (window.methodChart && typeof window.methodChart.destroy === 'function') {
                window.methodChart.destroy();
              }
              window.methodChart = new Chart(methodCanvas.getContext('2d'), {
                type: 'doughnut',
                data: {
                  labels,
                  datasets: [{
                    data: values,
                    backgroundColor: ['#667eea','#764ba2','#f093fb','#f5576c','#4facfe','#00d4aa']
                  }]
                },
                options: { responsive: true, maintainAspectRatio: false, plugins: { legend: { position: 'bottom' } } }
              });
            }
          } else if (reportType === 'orders') {
            const ordersEl = document.getElementById('totalOrders');
            if (ordersEl) ordersEl.textContent = Number(d.total_bookings || 0).toLocaleString();
            const tbody = document.getElementById('reportTableBody');
            if (tbody && Array.isArray(d.bookings_by_day)) {
              tbody.innerHTML = d.bookings_by_day.map(row => `
                <tr>
                  <td>${row.date}</td>
                  <td>—</td>
                  <td>${Number(row.count || 0).toLocaleString()}</td>
                  <td>—</td>
                  <td>—</td>
                  <td>—</td>
                  <td><span class="badge bg-success-subtle text-success">—</span></td>
                </tr>
              `).join('');
            }
            const revCanvas = document.getElementById('revenueChart');
            const methodCanvas = document.getElementById('methodChart');
            if (revCanvas) revCanvas.style.display = 'none';
            if (methodCanvas) methodCanvas.style.display = 'none';
          } else if (reportType === 'providers') {
            const tbody = document.getElementById('reportTableBody');
            if (tbody && d.top_providers) {
              tbody.innerHTML = d.top_providers.map(row => `
                <tr>
                  <td>${row.business_name}</td>
                  <td><span class="fw-semibold text-success">${Number(row.total_revenue || 0).toLocaleString(undefined, {minimumFractionDigits:2, maximumFractionDigits:2})} SAR</span></td>
                  <td>${Number(row.total_bookings || 0).toLocaleString()}</td>
                  <td>—</td>
                  <td>—</td>
                  <td>${Number(row.avg_booking_value || 0).toFixed(2)}</td>
                  <td><span class="badge bg-success-subtle text-success">—</span></td>
                </tr>
              `).join('');
            }
            const revCanvas = document.getElementById('revenueChart');
            const methodCanvas = document.getElementById('methodChart');
            if (revCanvas) revCanvas.style.display = 'none';
            if (methodCanvas) methodCanvas.style.display = 'none';
          } else if (reportType === 'users') {
            const usersEl = document.getElementById('totalUsers');
            if (usersEl) usersEl.textContent = Number((d.new_clients || 0) + (d.returning_clients || 0)).toLocaleString();
            const tbody = document.getElementById('reportTableBody');
            if (tbody && d.top_clients) {
              tbody.innerHTML = d.top_clients.map(row => `
                <tr>
                  <td>${row.name}</td>
                  <td><span class="fw-semibold text-success">${Number(row.total_spent || 0).toLocaleString(undefined, {minimumFractionDigits:2, maximumFractionDigits:2})} SAR</span></td>
                  <td>${Number(row.total_bookings || 0).toLocaleString()}</td>
                  <td>${row.email || ''}</td>
                  <td>${row.phone || ''}</td>
                  <td>${Number(row.avg_spent_per_booking || 0).toFixed(2)}</td>
                  <td><span class="badge bg-success-subtle text-success">—</span></td>
                </tr>
              `).join('');
            }
            const revCanvas = document.getElementById('revenueChart');
            const methodCanvas = document.getElementById('methodChart');
            if (revCanvas) revCanvas.style.display = 'none';
            if (methodCanvas) methodCanvas.style.display = 'none';
          } else if (reportType === 'commission') {
            const revenueEl = document.getElementById('totalRevenue');
            if (revenueEl) revenueEl.textContent = `${Number(d.total_commission || 0).toLocaleString(undefined, {minimumFractionDigits:2, maximumFractionDigits:2})} SAR`;
            const tbody = document.getElementById('reportTableBody');
            if (tbody && d.commission_by_provider) {
              tbody.innerHTML = d.commission_by_provider.map(row => `
                <tr>
                  <td>${row.business_name}</td>
                  <td><span class="fw-semibold text-success">${Number(row.total_revenue || 0).toLocaleString(undefined, {minimumFractionDigits:2, maximumFractionDigits:2})} SAR</span></td>
                  <td><span class="fw-semibold text-primary">${Number(row.total_commission || 0).toLocaleString(undefined, {minimumFractionDigits:2, maximumFractionDigits:2})} SAR</span></td>
                  <td>${Number(row.booking_count || 0).toLocaleString()}</td>
                  <td>—</td>
                  <td>${row.total_revenue > 0 ? ((row.total_commission / row.total_revenue) * 100).toFixed(2) + '%' : '—'}</td>
                  <td><span class="badge bg-success-subtle text-success">—</span></td>
                </tr>
              `).join('');
            }
            // Build commission chart
            const revCanvas = document.getElementById('revenueChart');
            if (revCanvas && Array.isArray(d.commission_by_day)) {
              const labels = d.commission_by_day.map(r => r.date);
              const values = d.commission_by_day.map(r => Number(r.commission || 0));
              revCanvas.style.display = 'block';
              if (window.revenueChart && typeof window.revenueChart.destroy === 'function') {
                window.revenueChart.destroy();
              }
              window.revenueChart = new Chart(revCanvas.getContext('2d'), {
                type: 'line',
                data: {
                  labels,
                  datasets: [{
                    label: '{{ __('reports.commission_label') }}',
                    data: values,
                    borderColor: '#28a745',
                    backgroundColor: 'rgba(40, 167, 69, 0.1)',
                    tension: 0.4,
                    fill: true
                  }]
                },
                options: {
                  responsive: true,
                  maintainAspectRatio: false,
                  plugins: { legend: { display: false } },
                  scales: { y: { beginAtZero: true } }
                }
              });
            }
            const methodCanvas = document.getElementById('methodChart');
            if (methodCanvas) methodCanvas.style.display = 'none';
          }
          
          showToast('{{ __('reports.report_generated_success') }}', 'success');
        })
        .catch(err => {
          console.error(err);
          loadingState.style.display = 'none';
          reportResults.style.display = 'none';
          noReportState.style.display = 'block';
          showToast('{{ __('reports.failed_generate_report') }}', 'error');
        });
    }

    // Removed dummy updateMetrics function - using real data from API

    // Removed dummy generateCharts and populateTable functions - charts are generated from real API data in generateReport()
    
    function exportReport() {
      const format = document.getElementById('exportFormat').value;
      const reportType = document.getElementById('reportType').value;
      const timePeriod = document.getElementById('timePeriod').value;
      const fromDate = document.getElementById('fromDate').value;
      const toDate = document.getElementById('toDate').value;
      
      showToast(`{{ __('reports.exporting_report_as', ['format' => '']) }}${format.toUpperCase()}...`, 'info');
      
      // Build export URL
      let exportUrl = '{{ route('reports.export') }}';
      const params = new URLSearchParams();
      params.set('type', reportType);
      params.set('format', format);
      params.set('period', timePeriod);
      
      if (timePeriod === 'custom' && fromDate && toDate) {
        params.set('start_date', fromDate);
        params.set('end_date', toDate);
      }
      
      // Open export URL in new window to download file
      const fullUrl = `${exportUrl}?${params.toString()}`;
      window.open(fullUrl, '_blank');
      
      setTimeout(() => {
        showToast('{{ __('reports.export_started') }}', 'success');
      }, 500);
    }

    function importData() {
      showToast('{{ __('reports.opening_import_dialog') }}', 'info');
      
      // Create file input
      const input = document.createElement('input');
      input.type = 'file';
      input.accept = '.csv,.xlsx,.xls';
      input.onchange = function(e) {
        const file = e.target.files[0];
        if (file) {
          showToast(`{{ __('reports.importing_file', ['file' => '']) }}${file.name}...`, 'info');
          setTimeout(() => {
            showToast('{{ __('reports.data_imported_success') }}', 'success');
          }, 2000);
        }
      };
      input.click();
    }

    function showToast(message, type) {
      // Simple toast notification
      const toast = document.createElement('div');
      toast.className = `alert alert-${type === 'success' ? 'success' : type === 'error' ? 'danger' : 'info'} alert-dismissible fade show position-fixed`;
      toast.style.cssText = 'top: 20px; right: 20px; z-index: 9999; min-width: 300px;';
      toast.innerHTML = `
        ${message}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
      `;
      
      document.body.appendChild(toast);
      
      // Auto remove after 3 seconds
      setTimeout(() => {
        if (toast.parentNode) {
          toast.parentNode.removeChild(toast);
        }
      }, 3000);
    }

    // Page is ready - show "No Report" state initially
    document.addEventListener('DOMContentLoaded', function() {
      const noReportState = document.getElementById('noReportState');
      const reportResults = document.getElementById('reportResults');
      if (noReportState) noReportState.style.display = 'block';
      if (reportResults) reportResults.style.display = 'none';
      
      console.log('Reports page ready. Click "Generate Report" to fetch real data from API.');
    });
  </script>
@endsection
