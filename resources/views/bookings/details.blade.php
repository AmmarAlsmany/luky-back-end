@extends('layouts.vertical', ['title' => __('bookings.booking_details')])
@section('css')
    @vite(['node_modules/flatpickr/dist/flatpickr.min.css'])
@endsection
@section('content')
    <div class="row">
        <div class="col-12">
            <div class="d-flex align-items-center justify-content-between mb-3">
                <div>
                    <h4 class="mb-0">{{ __('bookings.booking_number') }} {{ $booking['booking_number'] ?? __('bookings.n_a') }}</h4>
                    <p class="text-muted mb-0">{{ __('bookings.created') }}: {{ date('d M Y, h:i A', strtotime($booking['created_at'] ?? now())) }}</p>
                </div>
                <div class="d-flex gap-2">
                    <a href="{{ route('bookings.index') }}" class="btn btn-secondary">
                        <i class="mdi mdi-arrow-left me-1"></i> {{ __('bookings.back_to_list') }}
                    </a>
                    <button class="btn btn-primary" onclick="window.print()">
                        <i class="mdi mdi-printer me-1"></i> {{ __('bookings.print') }}
                    </button>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        {{-- Booking Status & Timeline --}}
        <div class="col-xl-8">
            {{-- Booking Info Card --}}
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">{{ __('bookings.booking_information') }}</h5>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="text-muted fw-medium mb-1">{{ __('bookings.booking_number') }}</label>
                            <p class="mb-0 fw-semibold">#{{ $booking['booking_number'] }}</p>
                        </div>
                        <div class="col-md-6">
                            <label class="text-muted fw-medium mb-1">{{ __('bookings.status') }}</label>
                            @php
                                $statusColors = [
                                    'pending' => 'warning',
                                    'confirmed' => 'info',
                                    'completed' => 'success',
                                    'cancelled' => 'danger',
                                    'rejected' => 'danger',
                                ];
                                $color = $statusColors[$booking['status']] ?? 'secondary';
                            @endphp
                            <p class="mb-0">
                                <span class="badge bg-{{ $color }}-subtle text-{{ $color }} px-3 py-2">
                                    {{ ucfirst(str_replace('_', ' ', $booking['status'])) }}
                                </span>
                            </p>
                        </div>
                        <div class="col-md-6">
                            <label class="text-muted fw-medium mb-1">{{ __('bookings.booking_date') }}</label>
                            <p class="mb-0 fw-semibold">{{ $booking['booking_date'] }}</p>
                        </div>
                        <div class="col-md-6">
                            <label class="text-muted fw-medium mb-1">{{ __('bookings.time_slot') }}</label>
                            <p class="mb-0 fw-semibold">{{ $booking['start_time_formatted'] }} - {{ $booking['end_time_formatted'] }}</p>
                            <small class="text-muted">({{ __('bookings.minutes', ['count' => $booking['duration_minutes']]) }})</small>
                        </div>
                        <div class="col-md-6">
                            <label class="text-muted fw-medium mb-1">{{ __('bookings.payment_method') }}</label>
                            <p class="mb-0 fw-semibold">{{ ucfirst($booking['payment_method']) }}</p>
                        </div>
                        <div class="col-md-6">
                            <label class="text-muted fw-medium mb-1">{{ __('bookings.payment_status') }}</label>
                            @php
                                $paymentColors = [
                                    'pending' => 'warning',
                                    'paid' => 'success',
                                    'refunded' => 'info',
                                    'failed' => 'danger'
                                ];
                                $payColor = $paymentColors[$booking['payment_status']] ?? 'secondary';
                            @endphp
                            <p class="mb-0">
                                <span class="badge bg-{{ $payColor }}-subtle text-{{ $payColor }} px-3 py-2">
                                    {{ ucfirst($booking['payment_status']) }}
                                </span>
                            </p>
                        </div>
                        @if(!empty($booking['client_address']))
                            <div class="col-12">
                                <label class="text-muted fw-medium mb-1">{{ __('bookings.service_address') }}</label>
                                <p class="mb-0">{{ $booking['client_address'] }}</p>
                                @if($booking['client_latitude'] && $booking['client_longitude'])
                                    <small class="text-muted">
                                        <i class="mdi mdi-map-marker"></i>
                                        {{ number_format($booking['client_latitude'], 6) }}, {{ number_format($booking['client_longitude'], 6) }}
                                    </small>
                                @endif
                            </div>
                        @endif
                        @if(!empty($booking['notes']))
                            <div class="col-12">
                                <label class="text-muted fw-medium mb-1">{{ __('bookings.notes') }}</label>
                                <p class="mb-0">{{ $booking['notes'] }}</p>
                            </div>
                        @endif
                        @if(!empty($booking['cancellation_reason']))
                            <div class="col-12">
                                <div class="alert alert-danger mb-0">
                                    <strong>{{ __('bookings.cancellation_reason') }}:</strong><br>
                                    {{ $booking['cancellation_reason'] }}
                                    <br><small>{{ __('bookings.cancelled_by') }}: {{ ucfirst($booking['cancelled_by']) }}</small>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            {{-- Client & Provider Info --}}
            <div class="card">
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <h5 class="card-title mb-3">{{ __('bookings.client_information') }}</h5>
                            @if(!empty($booking['client']))
                                <div class="d-flex align-items-start gap-3">
                                    @if(!empty($booking['client']['avatar']))
                                        <img src="{{ $booking['client']['avatar'] }}" class="rounded-circle avatar-lg" alt="">
                                    @else
                                        <div class="avatar-lg bg-primary-subtle rounded-circle d-flex align-items-center justify-content-center">
                                            <span class="text-primary fw-bold fs-3">
                                                {{ strtoupper(substr($booking['client']['name'] ?? 'U', 0, 1)) }}
                                            </span>
                                        </div>
                                    @endif
                                    <div>
                                        <h4 class="mb-1">{{ $booking['client']['name'] ?? __('bookings.n_a') }}</h4>
                                        <p class="text-muted mb-1">
                                            <i class="mdi mdi-phone me-1"></i> {{ $booking['client']['phone'] ?? __('bookings.n_a') }}
                                        </p>
                                        <p class="text-muted mb-0">
                                            <i class="mdi mdi-email me-1"></i> {{ $booking['client']['email'] ?? __('bookings.n_a') }}
                                        </p>
                                        <a href="{{ route('clients.show', $booking['client']['id']) }}" class="btn btn-sm btn-outline-primary mt-2">
                                            {{ __('bookings.view_profile') }}
                                        </a>
                                    </div>
                                </div>
                            @endif
                        </div>
                        <div class="col-md-6 border-start">
                            <h5 class="card-title mb-3">{{ __('bookings.provider_information') }}</h5>
                            @if(!empty($booking['provider']))
                                <div class="d-flex align-items-start gap-3">
                                    @if(!empty($booking['provider']['logo']))
                                        <img src="{{ $booking['provider']['logo'] }}" class="rounded avatar-lg" alt="">
                                    @else
                                        <div class="avatar-lg bg-success-subtle rounded d-flex align-items-center justify-content-center">
                                            <span class="text-success fw-bold fs-3">
                                                {{ strtoupper(substr($booking['provider']['business_name'] ?? 'P', 0, 1)) }}
                                            </span>
                                        </div>
                                    @endif
                                    <div>
                                        <h4 class="mb-1">{{ $booking['provider']['business_name'] ?? __('bookings.n_a') }}</h4>
                                        <p class="text-muted mb-1">
                                            <i class="mdi mdi-phone me-1"></i> {{ $booking['provider']['phone'] ?? __('bookings.n_a') }}
                                        </p>
                                        <p class="text-muted mb-0">
                                            <i class="mdi mdi-email me-1"></i> {{ $booking['provider']['email'] ?? __('bookings.n_a') }}
                                        </p>
                                        <a href="{{ route('providers.show', $booking['provider']['id']) }}" class="btn btn-sm btn-outline-success mt-2">
                                            {{ __('bookings.view_profile') }}
                                        </a>
                                    </div>
                                </div>
                            @else
                                <div class="alert alert-warning mb-3">
                                    <i class="mdi mdi-alert me-1"></i> {{ __('bookings.no_provider_assigned') }}
                                </div>
                                <button type="button" class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#assignProviderModal">
                                    <i class="mdi mdi-account-plus me-1"></i> {{ __('bookings.assign_provider') }}
                                </button>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            {{-- Service Details --}}
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">{{ __('bookings.service_details') }}</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-borderless mb-0">
                            <thead class="bg-light-subtle">
                                <tr>
                                    <th>{{ __('bookings.service') }}</th>
                                    <th>{{ __('bookings.category') }}</th>
                                    <th>{{ __('bookings.duration') }}</th>
                                    <th>{{ __('bookings.price') }}</th>
                                    <th class="text-end">{{ __('bookings.total_amount') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @if(!empty($booking['items']))
                                    @foreach($booking['items'] as $item)
                                        <tr>
                                            <td>
                                                <div class="d-flex align-items-center gap-2">
                                                    @if(!empty($item['service']['image']))
                                                        <img src="{{ $item['service']['image'] }}" class="avatar-sm rounded" alt="">
                                                    @endif
                                                    <div>
                                                        <h5 class="mb-0">{{ $item['service']['name_en'] ?? $item['service']['name_ar'] ?? __('bookings.n_a') }}</h5>
                                                        @if(!empty($item['service']['description']))
                                                            <p class="text-muted fs-13 mb-0">{{ Str::limit($item['service']['description'], 50) }}</p>
                                                        @endif
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                @if(!empty($item['service']['category']))
                                                    {{ $item['service']['category']['name'] ?? __('bookings.n_a') }}
                                                @else
                                                    {{ __('bookings.n_a') }}
                                                @endif
                                            </td>
                                            <td>
                                                @if(!empty($item['service']['duration']))
                                                    {{ $item['service']['duration'] }} {{ __('bookings.mins') }}
                                                @else
                                                    {{ __('bookings.n_a') }}
                                                @endif
                                            </td>
                                            <td>
                                                {{ number_format($item['unit_price'], 2) }} SAR
                                                @if($item['quantity'] > 1)
                                                    <br><small class="text-muted">x{{ $item['quantity'] }}</small>
                                                @endif
                                            </td>
                                            <td class="text-end fw-semibold">{{ number_format($item['total_price'], 2) }} SAR</td>
                                        </tr>
                                    @endforeach
                                @else
                                    <tr>
                                        <td colspan="5" class="text-center py-3">
                                            <p class="text-muted mb-0">{{ __('bookings.no_services') }}</p>
                                        </td>
                                    </tr>
                                @endif
                            </tbody>
                        </table>
                    </div>

                    <div class="border-top mt-3 pt-3">
                        <div class="row">
                            <div class="col-sm-6 offset-sm-6">
                                <div class="d-flex justify-content-between mb-2">
                                    <span>{{ __('bookings.subtotal') }}:</span>
                                    <span class="fw-semibold">{{ number_format($booking['service_amount'], 2) }} SAR</span>
                                </div>
                                @if(!empty($booking['discount_amount']) && $booking['discount_amount'] > 0)
                                    <div class="d-flex justify-content-between mb-2 text-success">
                                        <span>{{ __('bookings.discount') }}:</span>
                                        <span class="fw-semibold">-{{ number_format($booking['discount_amount'], 2) }} SAR</span>
                                    </div>
                                @endif
                                @if(!empty($booking['tax_amount']) && $booking['tax_amount'] > 0)
                                    <div class="d-flex justify-content-between mb-2">
                                        <span>{{ __('bookings.tax_percentage', ['percentage' => $booking['tax_percentage'] ?? 15]) }}:</span>
                                        <span class="fw-semibold">{{ number_format($booking['tax_amount'], 2) }} SAR</span>
                                    </div>
                                @endif
                                <div class="d-flex justify-content-between border-top pt-2 fs-5">
                                    <span class="fw-bold">{{ __('bookings.total_amount') }}:</span>
                                    <span class="fw-bold text-primary">{{ number_format($booking['total_amount'], 2) }} SAR</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Sidebar Actions --}}
        <div class="col-xl-4">
            {{-- Status Actions --}}
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">{{ __('bookings.actions') }}</h5>
                </div>
                <div class="card-body">
                    @if($booking['status'] == 'pending')
                        <button class="btn btn-success w-100 mb-2" onclick="updateStatus('confirmed')">
                            <i class="mdi mdi-check me-1"></i> {{ __('bookings.confirm_booking') }}
                        </button>
                        <button class="btn btn-danger w-100 mb-2" onclick="updateStatus('rejected')">
                            <i class="mdi mdi-close me-1"></i> {{ __('bookings.reject_booking') }}
                        </button>
                        <button class="btn btn-outline-danger w-100" onclick="cancelBooking()">
                            <i class="mdi mdi-close-circle me-1"></i> {{ __('bookings.cancel_booking') }}
                        </button>
                    @elseif($booking['status'] == 'confirmed')
                        <button class="btn btn-success w-100 mb-2" onclick="updateStatus('completed')">
                            <i class="mdi mdi-check-all me-1"></i> {{ __('bookings.mark_completed') }}
                        </button>
                        <button class="btn btn-outline-danger w-100" onclick="cancelBooking()">
                            <i class="mdi mdi-close-circle me-1"></i> {{ __('bookings.cancel_booking') }}
                        </button>
                    @elseif($booking['status'] == 'completed')
                        <div class="alert alert-success mb-0">
                            <i class="mdi mdi-check-circle me-1"></i> {{ __('bookings.booking_completed_success') }}
                        </div>
                    @else
                        <div class="alert alert-danger mb-0">
                            <i class="mdi mdi-close-circle me-1"></i> {{ __('bookings.booking_was', ['status' => $booking['status']]) }}
                        </div>
                    @endif

                    @if(in_array($booking['status'], ['pending', 'cancelled']))
                        <hr>
                        <button type="button" class="btn btn-outline-danger btn-sm w-100" onclick="deleteBooking()">
                            <i class="mdi mdi-delete me-1"></i> {{ __('bookings.delete_booking') }}
                        </button>
                    @endif
                </div>
            </div>

            {{-- Payment Info --}}
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">{{ __('bookings.payment_information') }}</h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="text-muted fw-medium mb-1">{{ __('bookings.payment_status') }}</label>
                        @php
                            $paymentStatus = $booking['payment_status'] ?? 'pending';
                            $paymentColors = [
                                'paid' => 'success',
                                'pending' => 'warning',
                                'failed' => 'danger',
                                'refunded' => 'info',
                            ];
                            $paymentColor = $paymentColors[$paymentStatus] ?? 'secondary';
                        @endphp
                        <p class="mb-0">
                            <span class="badge bg-{{ $paymentColor }}-subtle text-{{ $paymentColor }}">
                                {{ ucfirst($paymentStatus) }}
                            </span>
                        </p>
                    </div>
                    <div class="mb-3">
                        <label class="text-muted fw-medium mb-1">{{ __('bookings.payment_method') }}</label>
                        <p class="mb-0 fw-semibold">{{ ucfirst($booking['payment_method'] ?? __('bookings.n_a')) }}</p>
                    </div>
                    @if(!empty($booking['payment_reference']))
                        <div class="mb-3">
                            <label class="text-muted fw-medium mb-1">{{ __('bookings.payment_reference') }}</label>
                            <p class="mb-0 fw-semibold">{{ $booking['payment_reference'] }}</p>
                        </div>
                    @endif
                    <hr>
                    <div class="mb-3">
                        <label class="text-muted fw-medium mb-1">{{ __('bookings.subtotal') }}</label>
                        <p class="mb-0 fw-semibold">{{ number_format($booking['subtotal'], 2) }} SAR</p>
                    </div>
                    @if($booking['discount_amount'] > 0)
                        <div class="mb-3">
                            <label class="text-muted fw-medium mb-1">{{ __('bookings.discount') }}</label>
                            <p class="mb-0 text-success fw-semibold">-{{ number_format($booking['discount_amount'], 2) }} SAR</p>
                        </div>
                    @endif
                    <div class="mb-3">
                        <label class="text-muted fw-medium mb-1">{{ __('bookings.tax') }}</label>
                        <p class="mb-0 fw-semibold">{{ number_format($booking['tax_amount'], 2) }} SAR</p>
                    </div>
                    <div class="mb-3">
                        <label class="text-muted fw-medium mb-1">{{ __('bookings.total_amount') }}</label>
                        <p class="mb-0 fs-4 fw-bold text-primary">{{ number_format($booking['total_amount'], 2) }} SAR</p>
                    </div>
                    <hr>
                    <div>
                        <label class="text-muted fw-medium mb-1">{{ __('bookings.commission') }}</label>
                        <p class="mb-0 fs-5 fw-bold text-success">{{ number_format($booking['commission_amount'], 2) }} SAR</p>
                        <small class="text-muted">{{ __('bookings.platform_commission') }}</small>
                    </div>
                </div>
            </div>

            {{-- Timeline/Activity Log --}}
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">{{ __('bookings.activity_timeline') }}</h5>
                </div>
                <div class="card-body">
                    <div class="timeline">
                        @if(!empty($booking['timeline']))
                            @foreach($booking['timeline'] as $activity)
                                <div class="timeline-item">
                                    <div class="timeline-marker bg-{{ $activity['color'] ?? 'primary' }}"></div>
                                    <div class="timeline-content">
                                        <h6 class="mb-1">{{ $activity['title'] ?? '' }}</h6>
                                        <p class="text-muted fs-13 mb-1">{{ $activity['description'] ?? '' }}</p>
                                        <small class="text-muted">
                                            <i class="mdi mdi-clock-outline me-1"></i>
                                            {{ $activity['timestamp']->format('d M Y, h:i A') }}
                                        </small>
                                    </div>
                                </div>
                            @endforeach
                        @else
                            <p class="text-muted mb-0">{{ __('bookings.no_activity_history') }}</p>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Assign Provider Modal --}}
    <div class="modal fade" id="assignProviderModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">{{ __('bookings.assign_provider') }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form id="assignProviderForm">
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">{{ __('bookings.select_provider') }} <span class="text-danger">*</span></label>
                            <select class="form-select" id="provider_id" name="provider_id" required>
                                <option value="">{{ __('bookings.choose_provider') }}</option>
                                @foreach($availableProviders as $provider)
                                    <option value="{{ $provider->id }}">
                                        {{ $provider->business_name }} - {{ $provider->phone }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('bookings.cancel') }}</button>
                        <button type="submit" class="btn btn-primary">{{ __('bookings.assign_provider') }}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@section('script')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        function updateStatus(status) {
            Swal.fire({
                title: '{{ __('bookings.confirm_status_update') }}',
                text: `{{ __('bookings.confirm_status_text', ['status' => '']) }}`.replace(':status', status.replace('_', ' ')),
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: '{{ __('bookings.yes_proceed') }}'
            }).then((result) => {
                if (result.isConfirmed) {
                    fetch(`/bookings/{{ $booking['id'] }}/status`, {
                        method: 'PUT',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        body: JSON.stringify({ status: status })
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            Swal.fire('{{ __('bookings.updated') }}', data.message, 'success')
                                .then(() => location.reload());
                        } else {
                            Swal.fire('{{ __('bookings.error') }}', data.message, 'error');
                        }
                    })
                    .catch(error => {
                        Swal.fire('{{ __('bookings.error') }}', '{{ __('bookings.something_went_wrong') }}', 'error');
                    });
                }
            });
        }

        // Cancel Booking with Reason
        function cancelBooking() {
            Swal.fire({
                title: '{{ __('bookings.cancel_booking_title') }}',
                text: '{{ __('bookings.provide_cancellation_reason') }}',
                input: 'textarea',
                inputPlaceholder: '{{ __('bookings.enter_cancellation_reason') }}',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#6c757d',
                confirmButtonText: '{{ __('bookings.cancel_booking') }}',
                inputValidator: (value) => {
                    if (!value) {
                        return '{{ __('bookings.need_provide_reason') }}';
                    }
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    fetch(`/bookings/{{ $booking['id'] }}/status`, {
                        method: 'PUT',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        body: JSON.stringify({
                            status: 'cancelled',
                            reason: result.value,
                            cancelled_by: 'admin'
                        })
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            Swal.fire('{{ __('bookings.cancelled_success') }}', data.message, 'success')
                                .then(() => location.reload());
                        } else {
                            Swal.fire('{{ __('bookings.error') }}', data.message, 'error');
                        }
                    })
                    .catch(error => {
                        Swal.fire('{{ __('bookings.error') }}', '{{ __('bookings.something_went_wrong') }}', 'error');
                    });
                }
            });
        }

        // Delete Booking
        function deleteBooking() {
            Swal.fire({
                title: '{{ __('bookings.are_you_sure') }}',
                text: '{{ __('bookings.action_cannot_undone') }}',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#6c757d',
                confirmButtonText: '{{ __('bookings.yes_delete_it') }}'
            }).then((result) => {
                if (result.isConfirmed) {
                    fetch(`/bookings/{{ $booking['id'] }}`, {
                        method: 'DELETE',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        }
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            Swal.fire('{{ __('bookings.deleted') }}', data.message, 'success')
                                .then(() => window.location.href = '{{ route("bookings.index") }}');
                        } else {
                            Swal.fire('{{ __('bookings.error') }}', data.message, 'error');
                        }
                    })
                    .catch(error => {
                        Swal.fire('{{ __('bookings.error') }}', '{{ __('bookings.something_went_wrong') }}', 'error');
                    });
                }
            });
        }

        // Assign Provider Form Handler
        document.getElementById('assignProviderForm').addEventListener('submit', function(e) {
            e.preventDefault();

            const providerId = document.getElementById('provider_id').value;

            if (!providerId) {
                Swal.fire('{{ __('bookings.error') }}', '{{ __('bookings.please_select_provider') }}', 'error');
                return;
            }

            fetch(`/bookings/{{ $booking['id'] }}/assign`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({ provider_id: providerId })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Close modal
                    const modal = bootstrap.Modal.getInstance(document.getElementById('assignProviderModal'));
                    modal.hide();

                    Swal.fire('{{ __('bookings.success') }}', data.message, 'success')
                        .then(() => location.reload());
                } else {
                    Swal.fire('{{ __('bookings.error') }}', data.message, 'error');
                }
            })
            .catch(error => {
                Swal.fire('{{ __('bookings.error') }}', '{{ __('bookings.something_went_wrong') }}', 'error');
            });
        });
    </script>

    <style>
        .timeline {
            position: relative;
            padding-left: 30px;
        }
        .timeline-item {
            position: relative;
            padding-bottom: 20px;
        }
        .timeline-item:not(:last-child):before {
            content: '';
            position: absolute;
            left: -23px;
            top: 8px;
            height: 100%;
            width: 2px;
            background: #e9ecef;
        }
        .timeline-marker {
            position: absolute;
            left: -28px;
            top: 0;
            width: 12px;
            height: 12px;
            border-radius: 50%;
            background: #0d6efd;
            border: 2px solid #fff;
            box-shadow: 0 0 0 2px #0d6efd;
        }
        .timeline-content {
            position: relative;
        }
    </style>
@endsection
