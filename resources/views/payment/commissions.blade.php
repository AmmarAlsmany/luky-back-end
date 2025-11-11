@extends('layouts.vertical', ['title' => __('payment.commission_tracking')])

@section('content')
<div class="row">
    {{-- Summary Cards --}}
    <div class="col-lg-3 col-md-6">
        <div class="card">
            <div class="card-body">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <p class="text-muted mb-1 small">{{ __('payment.total_commission') }}</p>
                        <h4 class="mb-0">{{ number_format($summary['total_commission'] ?? 0, 2) }} SAR</h4>
                        <small class="text-muted">{{ __('payment.all_time_earnings') }}</small>
                    </div>
                    <div class="avatar-sm">
                        <span class="avatar-title bg-primary-subtle text-primary rounded">
                            <i class="bx bx-wallet fs-4"></i>
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
                        <p class="text-muted mb-1 small">{{ __('payment.this_month') }}</p>
                        <h4 class="mb-0">{{ number_format($summary['this_month'] ?? 0, 2) }} SAR</h4>
                        <small class="text-muted">{{ __('payment.current_month_earnings') }}</small>
                    </div>
                    <div class="avatar-sm">
                        <span class="avatar-title bg-success-subtle text-success rounded">
                            <i class="bx bx-line-chart fs-4"></i>
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
                        <p class="text-muted mb-1 small">{{ __('payment.pending_payout') }}</p>
                        <h4 class="mb-0">{{ number_format($summary['pending_payout'] ?? 0, 2) }} SAR</h4>
                        <small class="text-warning">{{ __('payment.awaiting_payout') }}</small>
                    </div>
                    <div class="avatar-sm">
                        <span class="avatar-title bg-warning-subtle text-warning rounded">
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
                        <p class="text-muted mb-1 small">{{ __('payment.avg_rate') }}</p>
                        <h4 class="mb-0">{{ number_format($summary['avg_rate'] ?? 0, 1) }}%</h4>
                        <small class="text-muted">{{ __('payment.platform_commission') }}</small>
                    </div>
                    <div class="avatar-sm">
                        <span class="avatar-title bg-info-subtle text-info rounded">
                            <i class="bx bx-trending-up fs-4"></i>
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Quick Actions --}}
    <div class="col-lg-4">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">{{ __('payment.quick_actions') }}</h5>
            </div>
            <div class="card-body">
                <div class="d-grid gap-2">
                    <button type="button" class="btn btn-outline-secondary">
                        <i class="bx bx-download me-1"></i> {{ __('payment.export_report') }}
                    </button>
                </div>
            </div>
        </div>
    </div>

    {{-- Commission Breakdown & Provider List --}}
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header d-flex align-items-center justify-content-between">
                <h5 class="mb-0">{{ __('payment.provider_commission_breakdown') }}</h5>
                <div class="d-flex gap-2">
                    <select class="form-select form-select-sm" style="width: auto;">
                        <option>{{ __('payment.this_month') }}</option>
                        <option>{{ __('payment.last_month') }}</option>
                        <option>{{ __('payment.last_3_months') }}</option>
                        <option>{{ __('payment.this_year') }}</option>
                    </select>
                    <button class="btn btn-sm btn-outline-primary">
                        <i class="bx bx-filter-alt"></i>
                    </button>
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>{{ __('payment.provider') }}</th>
                                <th>{{ __('payment.rate') }}</th>
                                <th>{{ __('payment.transactions_count') }}</th>
                                <th>{{ __('payment.gross_revenue') }}</th>
                                <th>{{ __('payment.commission') }}</th>
                                <th>{{ __('payment.net_payout') }}</th>
                                <th>{{ __('payment.status') }}</th>
                                <th>{{ __('payment.actions') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($commissionData as $data)
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center gap-2">
                                        <div class="avatar-sm">
                                            <span class="avatar-title bg-primary-subtle text-primary rounded-circle">
                                                {{ strtoupper(substr($data->provider_name, 0, 2)) }}
                                            </span>
                                        </div>
                                        <div>
                                            <div class="fw-medium">{{ $data->provider_name }}</div>
                                            <small class="text-muted">ID: #PRV-{{ str_pad($data->provider_id, 3, '0', STR_PAD_LEFT) }}</small>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <span class="badge bg-info-subtle text-info">{{ number_format(($data->total_commission / $data->gross_revenue) * 100, 1) }}%</span>
                                </td>
                                <td>{{ $data->transaction_count }}</td>
                                <td class="fw-medium">{{ number_format($data->gross_revenue, 2) }} SAR</td>
                                <td class="text-success fw-medium">{{ number_format($data->total_commission, 2) }} SAR</td>
                                <td>{{ number_format($data->net_payout, 2) }} SAR</td>
                                <td>
                                    <span class="badge bg-success-subtle text-success">{{ __('payment.completed') }}</span>
                                </td>
                                <td>
                                    <div class="dropdown">
                                        <button class="btn btn-sm btn-light" data-bs-toggle="dropdown">
                                            <i class="bx bx-dots-vertical-rounded"></i>
                                        </button>
                                        <ul class="dropdown-menu">
                                            <li><a class="dropdown-item" href="#"><i class="bx bx-show me-2"></i>{{ __('payment.view_details_action') }}</a></li>
                                            <li><a class="dropdown-item" href="#"><i class="bx bx-edit-alt me-2"></i>{{ __('payment.edit_rate') }}</a></li>
                                            <li><a class="dropdown-item" href="#"><i class="bx bx-history me-2"></i>{{ __('payment.payment_history') }}</a></li>
                                            <li><hr class="dropdown-divider"></li>
                                            <li><a class="dropdown-item" href="#"><i class="bx bx-download me-2"></i>{{ __('payment.download_report') }}</a></li>
                                        </ul>
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="8" class="text-center py-4">
                                    <div class="text-muted">
                                        <i class="bx bx-info-circle fs-3 d-block mb-2"></i>
                                        <p>{{ __('payment.no_commission_data') }}</p>
                                        <small>{{ __('payment.commission_data_message') }}</small>
                                    </div>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                        <tfoot class="table-light">
                            <tr class="fw-bold">
                                <td colspan="3">{{ __('payment.totals') }}</td>
                                <td>{{ number_format($commissionData->sum('gross_revenue'), 2) }} SAR</td>
                                <td class="text-success">{{ number_format($commissionData->sum('total_commission'), 2) }} SAR</td>
                                <td>{{ number_format($commissionData->sum('net_payout'), 2) }} SAR</td>
                                <td colspan="2"></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Process Payout Modal --}}
<div class="modal fade" id="processPayoutModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Process Provider Payouts</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="alert alert-info">
                    <i class="bx bx-info-circle me-2"></i>
                    <strong>2 providers</strong> have pending payouts totaling <strong>2,120.00 SAR</strong>
                </div>
                
                <form>
                    <div class="mb-3">
                        <label class="form-label">Select Providers</label>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="provider1" checked>
                            <label class="form-check-label" for="provider1">
                                Glow Salon - 1,540.00 SAR
                            </label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="provider2" checked>
                            <label class="form-check-label" for="provider2">
                                Urban Hairstyle - 580.00 SAR
                            </label>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Payment Method</label>
                        <select class="form-select">
                            <option>Bank Transfer</option>
                            <option>Wallet</option>
                            <option>Check</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Payment Date</label>
                        <input type="date" class="form-control" value="{{ date('Y-m-d') }}">
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Notes (Optional)</label>
                        <textarea class="form-control" rows="2" placeholder="Add any notes about this payout..."></textarea>
                    </div>

                    <div class="border rounded p-3 bg-light">
                        <div class="d-flex justify-content-between mb-2">
                            <span class="text-muted">Selected Providers:</span>
                            <span class="fw-medium">2</span>
                        </div>
                        <div class="d-flex justify-content-between">
                            <span class="text-muted">Total Payout Amount:</span>
                            <span class="fw-bold text-success fs-5">2,120.00 SAR</span>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-success">
                    <i class="bx bx-check me-1"></i> Process Payout
                </button>
            </div>
        </div>
    </div>
</div>
@endsection
