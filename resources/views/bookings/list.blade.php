@extends('layouts.vertical', ['title' => __('bookings.bookings')])
@section('css')
    @vite(['node_modules/flatpickr/dist/flatpickr.min.css'])
@endsection
@section('content')
    {{-- Stats Cards --}}
    <div class="row">
        <div class="col-md-6 col-xl-2">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-center gap-2 mb-3">
                        <div class="avatar-md bg-primary bg-opacity-10 rounded">
                            <iconify-icon icon="solar:clipboard-list-bold-duotone"
                                class="fs-32 text-primary avatar-title"></iconify-icon>
                        </div>
                        <div>
                            <h4 class="mb-0">{{ __('bookings.total') }}</h4>
                        </div>
                    </div>
                    <div class="d-flex align-items-center justify-content-between">
                        <p class="text-muted fw-medium fs-22 mb-0">
                            {{ number_format($stats['total'] ?? 0) }}
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-6 col-xl-2">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-center gap-2 mb-3">
                        <div class="avatar-md bg-warning bg-opacity-10 rounded">
                            <iconify-icon icon="solar:clock-circle-bold-duotone"
                                class="fs-32 text-warning avatar-title"></iconify-icon>
                        </div>
                        <div>
                            <h4 class="mb-0">{{ __('bookings.pending') }}</h4>
                        </div>
                    </div>
                    <div class="d-flex align-items-center justify-content-between">
                        <p class="text-muted fw-medium fs-22 mb-0">
                            {{ number_format($stats['pending'] ?? 0) }}
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-6 col-xl-2">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-center gap-2 mb-3">
                        <div class="avatar-md bg-info bg-opacity-10 rounded">
                            <iconify-icon icon="solar:check-circle-bold-duotone"
                                class="fs-32 text-info avatar-title"></iconify-icon>
                        </div>
                        <div>
                            <h4 class="mb-0">{{ __('bookings.confirmed') }}</h4>
                        </div>
                    </div>
                    <div class="d-flex align-items-center justify-content-between">
                        <p class="text-muted fw-medium fs-22 mb-0">
                            {{ number_format($stats['confirmed'] ?? 0) }}
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-6 col-xl-2">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-center gap-2 mb-3">
                        <div class="avatar-md bg-success bg-opacity-10 rounded">
                            <iconify-icon icon="solar:check-circle-bold-duotone"
                                class="fs-32 text-success avatar-title"></iconify-icon>
                        </div>
                        <div>
                            <h4 class="mb-0">{{ __('bookings.completed') }}</h4>
                        </div>
                    </div>
                    <div class="d-flex align-items-center justify-content-between">
                        <p class="text-muted fw-medium fs-22 mb-0">
                            {{ number_format($stats['completed'] ?? 0) }}
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-6 col-xl-2">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-center gap-2 mb-3">
                        <div class="avatar-md bg-primary bg-opacity-10 rounded">
                            <iconify-icon icon="solar:dollar-minimalistic-bold-duotone"
                                class="fs-32 text-primary avatar-title"></iconify-icon>
                        </div>
                        <div>
                            <h4 class="mb-0">{{ __('bookings.revenue') }}</h4>
                        </div>
                    </div>
                    <div class="d-flex align-items-center justify-content-between">
                        <p class="text-muted fw-medium fs-22 mb-0">
                            {{ number_format($stats['total_revenue'] ?? 0, 2) }}
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-6 col-xl-2">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-center gap-2 mb-3">
                        <div class="avatar-md bg-success bg-opacity-10 rounded">
                            <iconify-icon icon="solar:wallet-money-bold-duotone"
                                class="fs-32 text-success avatar-title"></iconify-icon>
                        </div>
                        <div>
                            <h4 class="mb-0">{{ __('bookings.commission') }}</h4>
                        </div>
                    </div>
                    <div class="d-flex align-items-center justify-content-between">
                        <p class="text-muted fw-medium fs-22 mb-0">
                            {{ number_format($stats['total_commission'] ?? 0, 2) }}
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Bookings Table --}}
    <div class="row">
        <div class="col-xl-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="card-title">{{ __('bookings.bookings_list') }}</h4>
                    <div class="d-flex gap-2">
                        <button type="button" class="btn btn-sm btn-success" onclick="exportBookings()">
                            <i class="mdi mdi-download me-1"></i> {{ __('bookings.export') }}
                        </button>
                    </div>
                </div>

                <div class="card-body">
                    {{-- Filters --}}
                    <form method="GET" action="{{ route('bookings.index') }}" class="mb-3">
                        <div class="row g-3">
                            <div class="col-md-3">
                                <label class="form-label">{{ __('bookings.search') }}</label>
                                <input type="text" name="search" class="form-control"
                                    placeholder="{{ __('bookings.search_placeholder') }}"
                                    value="{{ $filters['search'] ?? '' }}">
                            </div>
                            <div class="col-md-2">
                                <label class="form-label">{{ __('bookings.status') }}</label>
                                <select name="status" class="form-select">
                                    <option value="">{{ __('bookings.all_status') }}</option>
                                    <option value="pending" {{ ($filters['status'] ?? '') == 'pending' ? 'selected' : '' }}>{{ __('bookings.pending') }}</option>
                                    <option value="confirmed" {{ ($filters['status'] ?? '') == 'confirmed' ? 'selected' : '' }}>{{ __('bookings.confirmed') }}</option>
                                    <option value="completed" {{ ($filters['status'] ?? '') == 'completed' ? 'selected' : '' }}>{{ __('bookings.completed') }}</option>
                                    <option value="cancelled" {{ ($filters['status'] ?? '') == 'cancelled' ? 'selected' : '' }}>{{ __('bookings.cancelled') }}</option>
                                    <option value="rejected" {{ ($filters['status'] ?? '') == 'rejected' ? 'selected' : '' }}>{{ __('bookings.rejected') }}</option>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <label class="form-label">{{ __('bookings.payment_status') }}</label>
                                <select name="payment_status" class="form-select">
                                    <option value="">{{ __('bookings.all') }}</option>
                                    <option value="pending" {{ ($filters['paymentStatus'] ?? '') == 'pending' ? 'selected' : '' }}>{{ __('bookings.pending') }}</option>
                                    <option value="paid" {{ ($filters['paymentStatus'] ?? '') == 'paid' ? 'selected' : '' }}>{{ __('bookings.paid') }}</option>
                                    <option value="refunded" {{ ($filters['paymentStatus'] ?? '') == 'refunded' ? 'selected' : '' }}>{{ __('bookings.refunded') }}</option>
                                    <option value="failed" {{ ($filters['paymentStatus'] ?? '') == 'failed' ? 'selected' : '' }}>{{ __('bookings.failed') }}</option>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <label class="form-label">{{ __('bookings.provider') }}</label>
                                <select name="provider_id" class="form-select">
                                    <option value="">{{ __('bookings.all_providers') }}</option>
                                    @foreach($providers as $provider)
                                        <option value="{{ $provider->id }}" {{ ($filters['providerId'] ?? '') == $provider->id ? 'selected' : '' }}>
                                            {{ $provider->business_name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-1-5">
                                <label class="form-label">{{ __('bookings.from_date') }}</label>
                                <input type="date" name="date_from" class="form-control"
                                    value="{{ $filters['dateFrom'] ?? '' }}">
                            </div>
                            <div class="col-md-1-5">
                                <label class="form-label">{{ __('bookings.to_date') }}</label>
                                <input type="date" name="date_to" class="form-control"
                                    value="{{ $filters['dateTo'] ?? '' }}">
                            </div>
                            <div class="col-md-12">
                                <button type="submit" class="btn btn-primary">
                                    <i class="mdi mdi-filter me-1"></i> {{ __('bookings.apply_filters') }}
                                </button>
                                <a href="{{ route('bookings.index') }}" class="btn btn-secondary">
                                    <i class="mdi mdi-refresh me-1"></i> {{ __('bookings.reset') }}
                                </a>
                            </div>
                        </div>
                    </form>

                    {{-- Table --}}
                    <div class="table-responsive">
                        <table class="table table-hover table-nowrap mb-0">
                            <thead class="bg-light-subtle">
                                <tr>
                                    <th>{{ __('bookings.booking_number') }}</th>
                                    <th>{{ __('bookings.client') }}</th>
                                    <th>{{ __('bookings.provider') }}</th>
                                    <th>{{ __('bookings.services') }}</th>
                                    <th>{{ __('bookings.date_time') }}</th>
                                    <th>{{ __('bookings.status') }}</th>
                                    <th>{{ __('bookings.payment') }}</th>
                                    <th>{{ __('bookings.total_amount') }}</th>
                                    <th>{{ __('bookings.commission') }}</th>
                                    <th>{{ __('bookings.actions') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($bookings as $booking)
                                    <tr>
                                        <td>
                                            <a href="{{ route('bookings.show', $booking['id']) }}"
                                                class="text-primary fw-semibold">
                                                #{{ $booking['booking_number'] }}
                                            </a>
                                        </td>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div class="avatar-sm flex-shrink-0 me-2">
                                                    @if(!empty($booking['client']['avatar']))
                                                        <img src="{{ $booking['client']['avatar'] }}"
                                                            class="rounded-circle" alt="">
                                                    @else
                                                        <div class="avatar-sm bg-primary-subtle rounded-circle d-flex align-items-center justify-content-center">
                                                            <span class="text-primary fw-bold">
                                                                {{ strtoupper(substr($booking['client']['name'] ?? 'U', 0, 1)) }}
                                                            </span>
                                                        </div>
                                                    @endif
                                                </div>
                                                <div>
                                                    <h5 class="fs-14 mb-0">{{ $booking['client']['name'] ?? __('providers.n_a') }}</h5>
                                                    <p class="text-muted fs-13 mb-0">{{ $booking['client']['phone'] ?? '' }}</p>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            @if(!empty($booking['provider']))
                                                <div>
                                                    <h5 class="fs-14 mb-0">{{ $booking['provider']['business_name'] ?? __('providers.n_a') }}</h5>
                                                    <p class="text-muted fs-13 mb-0">{{ $booking['provider']['phone'] ?? '' }}</p>
                                                </div>
                                            @else
                                                <span class="badge bg-warning-subtle text-warning">{{ __('bookings.unassigned') }}</span>
                                            @endif
                                        </td>
                                        <td>
                                            <div>
                                                <span class="text-truncate d-inline-block" style="max-width: 200px;"
                                                    title="{{ $booking['services'] }}">
                                                    {{ $booking['services'] }}
                                                </span>
                                                <p class="text-muted fs-13 mb-0">({{ __('bookings.items', ['count' => $booking['items_count']]) }})</p>
                                            </div>
                                        </td>
                                        <td>
                                            <div>
                                                <p class="mb-0">{{ date('d M Y', strtotime($booking['booking_date'])) }}</p>
                                                <p class="text-muted fs-13 mb-0">
                                                    {{ $booking['start_time'] }}
                                                </p>
                                            </div>
                                        </td>
                                        <td>
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
                                            <span class="badge bg-{{ $color }}-subtle text-{{ $color }}">
                                                {{ ucfirst(str_replace('_', ' ', $booking['status'])) }}
                                            </span>
                                        </td>
                                        <td>
                                            @php
                                                $paymentColors = [
                                                    'pending' => 'warning',
                                                    'paid' => 'success',
                                                    'refunded' => 'info',
                                                    'failed' => 'danger'
                                                ];
                                                $payColor = $paymentColors[$booking['payment_status']] ?? 'secondary';
                                            @endphp
                                            <span class="badge bg-{{ $payColor }}-subtle text-{{ $payColor }}">
                                                {{ ucfirst($booking['payment_status']) }}
                                            </span>
                                        </td>
                                        <td>
                                            <span class="fw-semibold">{{ number_format($booking['total_amount'], 2) }} SAR</span>
                                        </td>
                                        <td>
                                            <span class="text-success fw-semibold">{{ number_format($booking['commission_amount'], 2) }} SAR</span>
                                        </td>
                                        <td>
                                            <div class="d-flex gap-1">
                                                <a href="{{ route('bookings.show', $booking['id']) }}"
                                                    class="btn btn-info btn-sm" title="{{ __('bookings.view_details') }}">
                                                    <iconify-icon icon="solar:eye-line-duotone"></iconify-icon>
                                                </a>
                                                @if(in_array($booking['status'], ['pending', 'cancelled']))
                                                    <button type="button" class="btn btn-soft-danger btn-sm"
                                                        onclick="deleteBooking({{ $booking['id'] }})" title="{{ __('bookings.delete') }}">
                                                        <iconify-icon icon="solar:trash-bin-minimalistic-2-broken"></iconify-icon>
                                                    </button>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="10" class="text-center py-4">
                                            <iconify-icon icon="solar:inbox-line-bold-duotone" class="fs-48 text-muted"></iconify-icon>
                                            <p class="text-muted mt-2">{{ __('bookings.no_bookings_found') }}</p>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    {{-- Pagination --}}
                    @if(!empty($pagination) && $pagination['last_page'] > 1)
                        <div class="d-flex justify-content-between align-items-center mt-3">
                            <div>
                                {{ __('bookings.showing_entries', ['from' => $pagination['from'] ?? 0, 'to' => $pagination['to'] ?? 0, 'total' => $pagination['total'] ?? 0]) }}
                            </div>
                            <nav>
                                <ul class="pagination mb-0">
                                    @if($pagination['current_page'] > 1)
                                        <li class="page-item">
                                            <a class="page-link" href="?page={{ $pagination['current_page'] - 1 }}">{{ __('bookings.previous') }}</a>
                                        </li>
                                    @endif

                                    @for($i = 1; $i <= $pagination['last_page']; $i++)
                                        <li class="page-item {{ $i == $pagination['current_page'] ? 'active' : '' }}">
                                            <a class="page-link" href="?page={{ $i }}">{{ $i }}</a>
                                        </li>
                                    @endfor

                                    @if($pagination['current_page'] < $pagination['last_page'])
                                        <li class="page-item">
                                            <a class="page-link" href="?page={{ $pagination['current_page'] + 1 }}">{{ __('bookings.next') }}</a>
                                        </li>
                                    @endif
                                </ul>
                            </nav>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection

@section('script')
    @vite(['resources/js/pages/datatable.init.js'])
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        function deleteBooking(bookingId) {
            if (!confirm('{{ __('bookings.confirm_delete_booking') }}')) {
                return;
            }

            const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

            fetch(`/bookings/${bookingId}`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json',
                    'Content-Type': 'application/json'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    Swal.fire({
                        icon: 'success',
                        title: '{{ __('bookings.success') }}',
                        text: data.message,
                        timer: 2000,
                        showConfirmButton: false
                    }).then(() => {
                        window.location.reload();
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: '{{ __('bookings.error') }}',
                        text: data.message
                    });
                }
            })
            .catch(error => {
                Swal.fire({
                    icon: 'error',
                    title: '{{ __('bookings.error') }}',
                    text: '{{ __('bookings.something_went_wrong') }}'
                });
            });
        }

        function exportBookings() {
            const params = new URLSearchParams(window.location.search);
            window.location.href = '{{ route("bookings.export") }}?' + params.toString();
        }
    </script>
@endsection
