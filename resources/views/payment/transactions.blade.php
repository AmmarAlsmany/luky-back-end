@extends('layouts.vertical', ['title' => __('payment.transaction_tracking')])

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
                <div class="card-header d-flex align-items-center justify-content-between">
                    <h4 class="mb-0">{{ __('payment.payment_transactions') }}</h4>
                    <div class="d-flex gap-2">
                        <button type="button" class="btn btn-sm btn-outline-primary" id="filterBtn">
                            <i class="bx bx-filter-alt me-1"></i> {{ __('payment.filters') }}
                        </button>
                        <button type="button" class="btn btn-sm btn-success" id="exportBtn">
                            <i class="bx bx-download me-1"></i> {{ __('payment.export') }}
                        </button>
                    </div>
                </div>

                <div class="card-body">
                    {{-- Filter Panel (Hidden by Default) --}}
                    <div id="filterPanel" class="border rounded p-3 mb-3 d-none">
                        <div class="row g-3">
                            <div class="col-md-3">
                                <label class="form-label small">{{ __('payment.date_range') }}</label>
                                <input type="text" class="form-control" id="dateRange" placeholder="{{ __('payment.select_date_range') }}">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label small">{{ __('payment.status') }}</label>
                                <select class="form-select" id="filterStatus">
                                    <option value="">{{ __('payment.all_statuses') }}</option>
                                    <option value="pending">{{ __('payment.pending') }}</option>
                                    <option value="completed">{{ __('payment.completed') }}</option>
                                    <option value="failed">{{ __('payment.failed') }}</option>
                                    <option value="refunded">{{ __('payment.refunded') }}</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label small">{{ __('payment.gateway') }}</label>
                                <select class="form-select" id="filterGateway">
                                    <option value="">{{ __('payment.all_gateways') }}</option>
                                    <option value="myfatoorah">{{ __('payment.myfatoorah') }}</option>
                                    <option value="tabby">{{ __('payment.tabby') }}</option>
                                    <option value="tamara">{{ __('payment.tamara') }}</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label small">&nbsp;</label>
                                <div class="d-flex gap-2">
                                    <button type="button" class="btn btn-primary w-100" id="applyFilter">{{ __('payment.apply') }}</button>
                                    <button type="button" class="btn btn-outline-secondary" id="clearFilter">{{ __('payment.clear') }}</button>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Transactions Table --}}
                    <div class="table-responsive">
                        <table class="table table-hover table-striped align-middle mb-0" id="transactionsTable">
                            <thead class="table-light">
                                <tr>
                                    <th>{{ __('payment.transaction_id') }}</th>
                                    <th>{{ __('payment.booking_id') }}</th>
                                    <th>{{ __('payment.customer') }}</th>
                                    <th>{{ __('payment.provider') }}</th>
                                    <th>{{ __('payment.amount') }}</th>
                                    <th>{{ __('payment.gateway') }}</th>
                                    <th>{{ __('payment.status') }}</th>
                                    <th>{{ __('payment.date') }}</th>
                                    <th>{{ __('payment.actions') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($payments as $payment)
                                    <tr>
                                        <td><code>#{{ $payment->transaction_id ?? ($payment->payment_id ?? 'TXN-' . $payment->id) }}</code>
                                        </td>
                                        <td>
                                            @if ($payment->booking)
                                                <a
                                                    href="{{ route('bookings.show', $payment->booking_id) }}">#BK-{{ $payment->booking_id }}</a>
                                            @else
                                                <span class="text-muted">—</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if ($payment->booking && $payment->booking->client)
                                                <div class="d-flex align-items-center gap-2">
                                                    <div class="avatar-sm">
                                                        <span
                                                            class="avatar-title bg-primary-subtle text-primary rounded-circle">
                                                            {{ strtoupper(substr($payment->booking->client->name, 0, 2)) }}
                                                        </span>
                                                    </div>
                                                    <div>
                                                        <div class="fw-medium">{{ $payment->booking->client->name }}</div>
                                                        <small
                                                            class="text-muted">{{ $payment->booking->client->phone ?? __('payment.no_phone') }}</small>
                                                    </div>
                                                </div>
                                            @else
                                                <span class="text-muted">{{ __('payment.unknown_customer') }}</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if ($payment->booking && $payment->booking->provider)
                                                <div>{{ $payment->booking->provider->business_name }}</div>
                                                <small
                                                    class="text-muted">{{ ucfirst($payment->booking->provider->business_type) }}</small>
                                            @else
                                                <span class="text-muted">—</span>
                                            @endif
                                        </td>
                                        <td class="fw-semibold">{{ number_format($payment->amount, 2) }}
                                            {{ $payment->currency }}</td>
                                        <td>
                                            @php
                                                $gatewayColors = [
                                                    'myfatoorah' => 'info',
                                                    'stripe' => 'primary',
                                                    'tabby' => 'warning',
                                                    'tamara' => 'danger',
                                                ];
                                                $color =
                                                    $gatewayColors[$payment->payment_gateway ?? $payment->gateway] ??
                                                    'secondary';
                                            @endphp
                                            <span
                                                class="badge bg-{{ $color }}-subtle text-{{ $color }}">{{ ucfirst($payment->payment_gateway ?? ($payment->gateway ?? __('payment.unknown'))) }}</span>
                                        </td>
                                        <td>
                                            @php
                                                $statusColors = [
                                                    'completed' => 'success',
                                                    'pending' => 'warning',
                                                    'failed' => 'danger',
                                                    'refunded' => 'secondary',
                                                ];
                                                $statusColor = $statusColors[$payment->status] ?? 'secondary';
                                            @endphp
                                            <span
                                                class="badge bg-{{ $statusColor }}">{{ ucfirst($payment->status) }}</span>
                                        </td>
                                        <td>
                                            <div>{{ $payment->created_at->format('Y-m-d') }}</div>
                                            <small class="text-muted">{{ $payment->created_at->format('h:i A') }}</small>
                                        </td>
                                        <td>
                                            <div class="d-flex gap-1">
                                                <button class="btn btn-sm btn-light"
                                                    onclick="viewTransactionDetails({{ $payment->id }})"
                                                    data-bs-toggle="tooltip" title="{{ __('payment.view_details') }}">
                                                    <i class="bx bx-show"></i>
                                                </button>
                                                <button class="btn btn-sm btn-light"
                                                    onclick="downloadReceipt({{ $payment->id }})" data-bs-toggle="tooltip"
                                                    title="{{ __('payment.download_receipt') }}">
                                                    <i class="bx bx-download"></i>
                                                </button>
                                                @if ($payment->status === 'failed')
                                                    <button class="btn btn-sm btn-light"
                                                        onclick="retryPayment({{ $payment->id }})"
                                                        data-bs-toggle="tooltip" title="{{ __('payment.retry_payment') }}">
                                                        <i class="bx bx-refresh"></i>
                                                    </button>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="9" class="text-center py-4">
                                            <div class="text-muted">
                                                <i class="bx bx-info-circle fs-3 d-block mb-2"></i>
                                                <p>{{ __('payment.no_transactions_found') }}</p>
                                                <small>{{ __('payment.transactions_appear_message') }}</small>
                                            </div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    {{-- Pagination --}}
                    <div class="d-flex justify-content-between align-items-center mt-3">
                        <div class="text-muted small">
                            {{ __('payment.showing') }} {{ $payments->firstItem() ?? 0 }} {{ __('payment.to') }} {{ $payments->lastItem() ?? 0 }} {{ __('payment.of') }} {{ $payments->total() }} {{ __('payment.transactions') }}
                        </div>
                        {{ $payments->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Summary Cards --}}
    <div class="col-lg-3 col-md-6">
        <div class="card">
            <div class="card-body">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <p class="text-muted mb-1">{{ __('payment.total_transactions') }}</p>
                        <h3 class="mb-0">{{ $stats['total'] ?? 0 }}</h3>
                    </div>
                    <div class="avatar-sm">
                        <span class="avatar-title bg-primary-subtle text-primary rounded-circle">
                            <i class="bx bx-receipt fs-4"></i>
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-3 col-md-6">
        <div class="card">
            <div class="card-body">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <p class="text-muted mb-1">{{ __('payment.completed_transactions') }}</p>
                        <h3 class="mb-0 text-success">{{ $stats['completed'] ?? 0 }}</h3>
                    </div>
                    <div class="avatar-sm">
                        <span class="avatar-title bg-success-subtle text-success rounded-circle">
                            <i class="bx bx-check-circle fs-4"></i>
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-3 col-md-6">
        <div class="card">
            <div class="card-body">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <p class="text-muted mb-1">{{ __('payment.pending_transactions') }}</p>
                        <h3 class="mb-0 text-warning">{{ $stats['pending'] ?? 0 }}</h3>
                    </div>
                    <div class="avatar-sm">
                        <span class="avatar-title bg-warning-subtle text-warning rounded-circle">
                            <i class="bx bx-time-five fs-4"></i>
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-3 col-md-6">
        <div class="card">
            <div class="card-body">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <p class="text-muted mb-1">{{ __('payment.failed_transactions') }}</p>
                        <h3 class="mb-0 text-danger">{{ $stats['failed'] ?? 0 }}</h3>
                    </div>
                    <div class="avatar-sm">
                        <span class="avatar-title bg-danger-subtle text-danger rounded-circle">
                            <i class="bx bx-x-circle fs-4"></i>
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Toggle filter panel
            document.getElementById('filterBtn')?.addEventListener('click', function() {
                const filterPanel = document.getElementById('filterPanel');
                filterPanel.classList.toggle('d-none');
            });

            // Apply filters
            document.getElementById('applyFilter')?.addEventListener('click', function() {
                const status = document.getElementById('filterStatus').value;
                const gateway = document.getElementById('filterGateway').value;
                const dateRange = document.getElementById('dateRange').value;

                let url = new URL(window.location.href);
                if (status) url.searchParams.set('status', status);
                if (gateway) url.searchParams.set('gateway', gateway);
                // TODO: Parse and add date range when date picker is implemented

                window.location.href = url.toString();
            });

            // Clear filters
            document.getElementById('clearFilter')?.addEventListener('click', function() {
                window.location.href = '{{ route('payments.transactions') }}';
            });

            // Export transactions
            document.getElementById('exportBtn')?.addEventListener('click', function() {
                const params = new URLSearchParams(window.location.search);
                window.location.href = '{{ route('payments.export') }}?' + params.toString();
            });

            // Initialize tooltips
            const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
            tooltipTriggerList.map(function(tooltipTriggerEl) {
                return new bootstrap.Tooltip(tooltipTriggerEl);
            });
        });

        // View transaction details
        function viewTransactionDetails(paymentId) {
            fetch(`{{ url('/payments') }}/${paymentId}/details`)
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // Create a nice formatted modal/alert with transaction details
                        const details = data.data;
                        let detailsHtml = `
Transaction Details:

Payment ID: ${details.InvoiceId || 'N/A'}
Amount: ${details.InvoiceValue || 'N/A'} ${details.Currency || 'SAR'}
Status: ${details.InvoiceStatus || 'N/A'}
Customer: ${details.CustomerName || 'N/A'}
Reference: ${details.CustomerReference || 'N/A'}
Created: ${details.CreatedDate || 'N/A'}
                `;
                        alert(detailsHtml);
                    } else {
                        alert('❌ Failed to load transaction details');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('❌ Error loading transaction details');
                });
        }

        // Download receipt
        function downloadReceipt(paymentId) {
            window.open('/payments/' + paymentId + '/receipt/download', '_blank');
        }
        
        // View receipt
        function viewReceipt(paymentId) {
            window.location.href = '/payments/' + paymentId + '/receipt';
        }

        // Retry failed payment
        function retryPayment(paymentId) {
            if (confirm('{{ __('payment.retry_payment_confirm') }}')) {
                // TODO: Implement payment retry logic
                alert('{{ __('payment.payment_retry_coming_soon') }}\n\n{{ __('payment.payment_id') }}: ' + paymentId);
            }
        }
    </script>
@endsection
