@extends('layouts.vertical', ['title' => 'Promo Code Details'])

@section('content')
<div class="row">
    <div class="col-12">
        <div class="page-title-box d-flex align-items-center justify-content-between">
            <div>
                <h4 class="mb-1 fw-bold">Promo Code Details</h4>
                <p class="text-muted mb-0">View promo code information and usage statistics</p>
            </div>
            <div class="d-flex gap-2">
                <a href="{{ route('promos.edit', $promoCode->id) }}" class="btn btn-primary">
                    <i class="mdi mdi-pencil me-1"></i> Edit
                </a>
                <a href="{{ route('promos.index') }}" class="btn btn-soft-secondary">
                    <i class="mdi mdi-arrow-left me-1"></i> Back
                </a>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <!-- Left Column - Main Info -->
    <div class="col-xl-8">
        <!-- Promo Code Info Card -->
        <div class="card">
            <div class="card-header border-bottom">
                <div class="d-flex align-items-center justify-content-between">
                    <div class="d-flex align-items-center gap-3">
                        <iconify-icon icon="solar:ticket-bold-duotone" class="fs-32 text-primary"></iconify-icon>
                        <div>
                            <h5 class="card-title mb-0 fw-bold">{{ $promoCode->code }}</h5>
                            <p class="text-muted mb-0 small">{{ $promoCode->description ?? 'No description' }}</p>
                        </div>
                    </div>
                    <div class="d-flex gap-2">
                        @if($promoCode->is_active)
                            <span class="badge bg-success-subtle text-success fs-13">
                                <i class="mdi mdi-check-circle me-1"></i> Active
                            </span>
                        @else
                            <span class="badge bg-danger-subtle text-danger fs-13">
                                <i class="mdi mdi-close-circle me-1"></i> Inactive
                            </span>
                        @endif
                        <span class="badge bg-primary-subtle text-primary fs-13">
                            {{ ucfirst($promoCode->discount_type) }}
                        </span>
                    </div>
                </div>
            </div>

            <div class="card-body">
                <!-- Discount Details -->
                <div class="row g-4 mb-4">
                    <div class="col-md-6">
                        <div class="p-3 bg-light rounded">
                            <small class="text-muted d-block mb-1">Discount Value</small>
                            <h4 class="mb-0 fw-bold text-primary">
                                @if($promoCode->discount_type === 'percentage')
                                    {{ $promoCode->discount_value }}%
                                @else
                                    {{ number_format($promoCode->discount_value, 2) }} SAR
                                @endif
                            </h4>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="p-3 bg-light rounded">
                            <small class="text-muted d-block mb-1">Usage Count</small>
                            <h4 class="mb-0 fw-bold">
                                {{ $promoCode->used_count }}
                                @if($promoCode->usage_limit)
                                    <span class="text-muted fs-14">/ {{ $promoCode->usage_limit }}</span>
                                @endif
                            </h4>
                        </div>
                    </div>
                </div>

                <!-- Additional Info -->
                <div class="table-responsive">
                    <table class="table table-borderless mb-0">
                        <tbody>
                            <tr>
                                <td class="text-muted" width="40%">Valid Period</td>
                                <td class="fw-semibold">
                                    {{ $promoCode->valid_from->format('d M Y') }} - {{ $promoCode->valid_until->format('d M Y') }}
                                </td>
                            </tr>
                            @if($promoCode->min_booking_amount)
                            <tr>
                                <td class="text-muted">Minimum Booking Amount</td>
                                <td class="fw-semibold">{{ number_format($promoCode->min_booking_amount, 2) }} SAR</td>
                            </tr>
                            @endif
                            @if($promoCode->max_discount_amount)
                            <tr>
                                <td class="text-muted">Maximum Discount Cap</td>
                                <td class="fw-semibold">{{ number_format($promoCode->max_discount_amount, 2) }} SAR</td>
                            </tr>
                            @endif
                            <tr>
                                <td class="text-muted">Usage Limit Per User</td>
                                <td class="fw-semibold">{{ $promoCode->usage_limit_per_user ?? 'Unlimited' }}</td>
                            </tr>
                            <tr>
                                <td class="text-muted">Applicable To</td>
                                <td class="fw-semibold">{{ ucfirst(str_replace('_', ' ', $promoCode->applicable_to)) }}</td>
                            </tr>
                            <tr>
                                <td class="text-muted">Created By</td>
                                <td class="fw-semibold">{{ $promoCode->createdBy->name ?? 'System' }}</td>
                            </tr>
                            <tr>
                                <td class="text-muted">Created At</td>
                                <td class="fw-semibold">{{ $promoCode->created_at->format('d M Y, h:i A') }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Usage Statistics -->
        <div class="card">
            <div class="card-header border-bottom">
                <h5 class="card-title mb-0 fw-semibold">
                    <i class="mdi mdi-chart-line me-2"></i>Usage Statistics
                </h5>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-4">
                        <div class="text-center p-3 bg-light rounded">
                            <h3 class="mb-1 fw-bold text-primary">{{ $usageStats['total_uses'] }}</h3>
                            <small class="text-muted">Total Uses</small>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="text-center p-3 bg-light rounded">
                            <h3 class="mb-1 fw-bold text-success">{{ number_format($usageStats['total_discount'], 2) }} SAR</h3>
                            <small class="text-muted">Total Discount Given</small>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="text-center p-3 bg-light rounded">
                            <h3 class="mb-1 fw-bold text-info">{{ $usageStats['unique_users'] }}</h3>
                            <small class="text-muted">Unique Users</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent Usage -->
        @if($usageStats['recent_uses']->count() > 0)
        <div class="card">
            <div class="card-header border-bottom">
                <h5 class="card-title mb-0 fw-semibold">
                    <i class="mdi mdi-history me-2"></i>Recent Usage
                </h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>User</th>
                                <th>Booking</th>
                                <th>Discount</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($usageStats['recent_uses'] as $usage)
                            <tr>
                                <td>{{ $usage->created_at->format('d M Y, h:i A') }}</td>
                                <td>{{ $usage->user->name ?? 'N/A' }}</td>
                                <td>
                                    <a href="{{ route('bookings.show', $usage->booking_id) }}" class="text-primary">
                                        #{{ $usage->booking_id }}
                                    </a>
                                </td>
                                <td class="fw-semibold text-success">{{ number_format($usage->discount_amount, 2) }} SAR</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        @endif
    </div>

    <!-- Right Column - Quick Actions -->
    <div class="col-xl-4">
        <!-- Actions Card -->
        <div class="card">
            <div class="card-header border-bottom">
                <h5 class="card-title mb-0 fw-semibold">Quick Actions</h5>
            </div>
            <div class="card-body">
                <div class="d-grid gap-2">
                    <a href="{{ route('promos.edit', $promoCode->id) }}" class="btn btn-primary">
                        <i class="mdi mdi-pencil me-1"></i> Edit Promo Code
                    </a>
                    <button onclick="toggleStatus({{ $promoCode->id }}, {{ $promoCode->is_active ? 'true' : 'false' }})" 
                            class="btn btn-outline-warning">
                        <i class="mdi mdi-power me-1"></i> 
                        {{ $promoCode->is_active ? 'Deactivate' : 'Activate' }}
                    </button>
                    <button onclick="deletePromo({{ $promoCode->id }}, '{{ $promoCode->code }}')" 
                            class="btn btn-outline-danger">
                        <i class="mdi mdi-delete me-1"></i> Delete
                    </button>
                </div>
            </div>
        </div>

        <!-- Info Card -->
        <div class="card">
            <div class="card-header border-bottom">
                <h5 class="card-title mb-0 fw-semibold">Information</h5>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <small class="text-muted d-block mb-1">Status</small>
                    @if($promoCode->is_active)
                        @if(now()->lt($promoCode->valid_from))
                            <span class="badge bg-info">Scheduled</span>
                        @elseif(now()->gt($promoCode->valid_until))
                            <span class="badge bg-danger">Expired</span>
                        @elseif($promoCode->usage_limit && $promoCode->used_count >= $promoCode->usage_limit)
                            <span class="badge bg-warning">Limit Reached</span>
                        @else
                            <span class="badge bg-success">Active</span>
                        @endif
                    @else
                        <span class="badge bg-secondary">Disabled</span>
                    @endif
                </div>
                
                <div class="mb-3">
                    <small class="text-muted d-block mb-1">Days Remaining</small>
                    <p class="mb-0 fw-semibold">
                        @if(now()->gt($promoCode->valid_until))
                            Expired
                        @else
                            {{ now()->diffInDays($promoCode->valid_until) }} days
                        @endif
                    </p>
                </div>

                @if($promoCode->usage_limit)
                <div>
                    <small class="text-muted d-block mb-1">Usage Progress</small>
                    <div class="progress" style="height: 20px;">
                        <div class="progress-bar" role="progressbar" 
                             style="width: {{ ($promoCode->used_count / $promoCode->usage_limit) * 100 }}%">
                            {{ number_format(($promoCode->used_count / $promoCode->usage_limit) * 100, 1) }}%
                        </div>
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection

@section('script')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
function toggleStatus(promoId, isActive) {
    const action = isActive ? 'deactivate' : 'activate';
    Swal.fire({
        title: `${action.charAt(0).toUpperCase() + action.slice(1)} Promo Code?`,
        text: `Are you sure you want to ${action} this promo code?`,
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#6c757d',
        confirmButtonText: `Yes, ${action} it!`
    }).then((result) => {
        if (result.isConfirmed) {
            fetch(`/promos/${promoId}/toggle-status`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    Swal.fire('Success!', data.message, 'success').then(() => location.reload());
                } else {
                    Swal.fire('Error!', data.message, 'error');
                }
            });
        }
    });
}

function deletePromo(promoId, promoCode) {
    Swal.fire({
        title: 'Delete Promo Code?',
        html: `Are you sure you want to delete <strong>${promoCode}</strong>?<br><small class="text-muted">This action cannot be undone.</small>`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Yes, delete it!'
    }).then((result) => {
        if (result.isConfirmed) {
            fetch(`/promos/${promoId}`, {
                method: 'DELETE',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    Swal.fire('Deleted!', data.message, 'success').then(() => {
                        window.location.href = '{{ route("promos.index") }}';
                    });
                } else {
                    Swal.fire('Error!', data.message, 'error');
                }
            });
        }
    });
}
</script>
@endsection
