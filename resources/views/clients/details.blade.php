@extends('layouts.vertical', ['title' => __('clients.details_title')])

@section('css')
<style>
/* Timeline Styles */
.timeline {
    position: relative;
    padding: 20px 0 20px 40px;
}

.timeline::before {
    content: '';
    position: absolute;
    left: 19px;
    top: 0;
    bottom: 0;
    width: 3px;
    background: linear-gradient(to bottom, #e9ecef 0%, #dee2e6 100%);
}

.timeline-item {
    position: relative;
    margin-bottom: 25px;
    padding-left: 30px;
}

.timeline-item:last-child {
    margin-bottom: 0;
}

.timeline-marker {
    position: absolute;
    left: -29px;
    top: 8px;
    width: 16px;
    height: 16px;
    border-radius: 50%;
    border: 3px solid #fff;
    z-index: 2;
    box-shadow: 0 0 0 1px rgba(0,0,0,0.1);
}

.timeline-marker.bg-success {
    background-color: #28a745 !important;
}

.timeline-marker.bg-info {
    background-color: #17a2b8 !important;
}

.timeline-marker.bg-primary {
    background-color: #0d6efd !important;
}

.timeline-marker.bg-secondary {
    background-color: #6c757d !important;
}

.timeline-content {
    background: #ffffff;
    padding: 16px 20px;
    border-radius: 8px;
    border: 1px solid #e9ecef;
    box-shadow: 0 1px 3px rgba(0,0,0,0.05);
}

.timeline-content:hover {
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    transition: box-shadow 0.3s ease;
}

.timeline-content h6 {
    font-size: 15px;
    font-weight: 600;
    margin-bottom: 4px;
    color: #212529;
}

.timeline-content p {
    font-size: 14px;
    color: #6c757d;
    margin-bottom: 0;
}

.timeline-content small {
    font-size: 12px;
    color: #adb5bd;
}
</style>
@endsection

@section('content')
    @if(empty($client) || !isset($client['id']))
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body text-center py-5">
                        <iconify-icon icon="solar:user-broken" class="fs-48 text-muted mb-3"></iconify-icon>
                        <h4>{{ __('common.client_not_found') }}</h4>
                        <p class="text-muted">{{ __('common.client_not_found_text') }}</p>
                        <a href="{{ route('clients.index') }}" class="btn btn-primary mt-3">
                            <i class="mdi mdi-arrow-left me-1"></i> {{ __('common.back_to_clients') }}
                        </a>
                    </div>
                </div>
            </div>
        </div>
    @else
    <div class="row">
        <div class="col-12">
            <div class="d-flex align-items-center justify-content-between mb-3">
                <div>
                    <h4 class="mb-0">{{ __('common.client_profile') }}</h4>
                    <p class="text-muted mb-0">{{ __('common.view_manage_client') }}</p>
                </div>
                <a href="{{ route('clients.index') }}" class="btn btn-secondary">
                    <i class="mdi mdi-arrow-left me-1"></i> {{ __('common.back_to_clients') }}
                </a>
            </div>
        </div>
    </div>

    <div class="row">
        {{-- Client Info Sidebar --}}
        <div class="col-xl-4">
            {{-- Profile Card --}}
            <div class="card">
                <div class="card-body text-center">
                    <div class="position-relative d-inline-block mb-3">
                        @if(!empty($client['avatar_url']))
                            <img src="{{ $client['avatar_url'] }}" class="rounded-circle avatar-xl" alt="">
                        @else
                            <div class="avatar-xl bg-primary-subtle rounded-circle d-inline-flex align-items-center justify-content-center">
                                <span class="text-primary fw-bold fs-1">
                                    {{ strtoupper(substr($client['name'] ?? 'C', 0, 1)) }}
                                </span>
                            </div>
                        @endif
                        @if(!empty($client['phone_verified_at']))
                            <span class="position-absolute bottom-0 end-0 badge rounded-pill bg-success" title="{{ __('common.verified') }}">
                                <i class="bx bx-check"></i>
                            </span>
                        @endif
                    </div>

                    <h4 class="mb-1">{{ $client['name'] ?? 'N/A' }}</h4>
                    <p class="text-muted mb-2">{{ $client['email'] ?? 'N/A' }}</p>

                    {{-- Verification Badges --}}
                    <div class="mb-3">
                        @if(!empty($client['phone_verified_at']))
                            <span class="badge bg-success-subtle text-success me-1" title="{{ __('common.phone_verified_on') }} {{ date('d M Y', strtotime($client['phone_verified_at'])) }}">
                                <i class="bx bx-phone"></i> {{ __('common.phone_verified') }}
                            </span>
                        @else
                            <span class="badge bg-warning-subtle text-warning me-1">
                                <i class="bx bx-phone"></i> {{ __('common.phone_not_verified') }}
                            </span>
                        @endif
                    </div>

                    @php
                        $statusColors = [
                            'active' => 'success',
                            'inactive' => 'warning',
                            'suspended' => 'danger',
                        ];
                        $status = $client['status'] ?? 'active';
                        $color = $statusColors[$status] ?? 'secondary';
                    @endphp
                    <span class="badge bg-{{ $color }}-subtle text-{{ $color }} px-3 py-2 fs-13">
                        {{ ucfirst($status) }}
                    </span>

                    {{-- Quick Actions --}}
                    <div class="mt-3 d-flex gap-2 justify-content-center">
                        @if(!empty($client['phone']))
                            <a href="tel:{{ $client['phone'] }}" class="btn btn-sm btn-soft-success" title="{{ __('common.call_client') }}">
                                <i class="bx bx-phone"></i>
                            </a>
                        @endif
                        @if(!empty($client['email']))
                            <a href="mailto:{{ $client['email'] }}" class="btn btn-sm btn-soft-primary" title="{{ __('common.email_client') }}">
                                <i class="bx bx-envelope"></i>
                            </a>
                        @endif
                        <button class="btn btn-sm btn-soft-info" title="{{ __('common.send_message_app') }}" data-bs-toggle="modal" data-bs-target="#sendMessageModal">
                            <i class="bx bx-message-dots"></i>
                        </button>
                        <button class="btn btn-sm btn-soft-warning" title="{{ __('common.send_notification_app') }}" data-bs-toggle="modal" data-bs-target="#sendNotificationModal">
                            <i class="bx bx-bell"></i>
                        </button>
                    </div>

                    <div class="mt-4 pt-3 border-top">
                        <div class="row text-center g-3">
                            <div class="col-6">
                                <p class="text-muted mb-1 fs-12">{{ __('common.total_bookings') }}</p>
                                <h4 class="mb-0 text-primary">{{ number_format($client['total_bookings'] ?? 0) }}</h4>
                            </div>
                            <div class="col-6">
                                <p class="text-muted mb-1 fs-12">{{ __('clients.total_spent') }}</p>
                                <h4 class="mb-0 text-success">{{ number_format($client['total_spent'] ?? 0, 2) }} <small class="fs-14">{{ __('common.sar') }}</small></h4>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Contact Information --}}
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">{{ __('common.contact_information') }}</h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <p class="text-muted mb-1">
                            <i class="mdi mdi-phone me-1"></i> {{ __('common.phone') }}
                        </p>
                        <h6 class="mb-0">{{ $client['phone'] ?? 'N/A' }}</h6>
                    </div>
                    <div class="mb-3">
                        <p class="text-muted mb-1">
                            <i class="mdi mdi-email me-1"></i> {{ __('common.email') }}
                        </p>
                        <h6 class="mb-0">{{ $client['email'] ?? 'N/A' }}</h6>
                    </div>
                    @if(!empty($client['date_of_birth']))
                        <div class="mb-3">
                            <p class="text-muted mb-1">
                                <i class="mdi mdi-cake me-1"></i> {{ __('common.date_of_birth') }}
                            </p>
                            <h6 class="mb-0">{{ date('d M Y', strtotime($client['date_of_birth'])) }}</h6>
                        </div>
                    @endif
                    @if(!empty($client['gender']))
                        <div>
                            <p class="text-muted mb-1">
                                <i class="mdi mdi-gender-male-female me-1"></i> {{ __('common.gender') }}
                            </p>
                            <h6 class="mb-0">{{ ucfirst($client['gender']) }}</h6>
                        </div>
                    @endif
                </div>
            </div>

            {{-- Account Info --}}
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">{{ __('common.account_details') }}</h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <p class="text-muted mb-1">{{ __('common.client_id') }}</p>
                        <h6 class="mb-0">#{{ $client['id'] ?? 'N/A' }}</h6>
                    </div>
                    <div class="mb-3">
                        <p class="text-muted mb-1">{{ __('common.member_since') }}</p>
                        <h6 class="mb-0">{{ date('d M Y', strtotime($client['created_at'] ?? now())) }}</h6>
                    </div>
                    <div>
                        <p class="text-muted mb-1">{{ __('common.last_activity') }}</p>
                        <h6 class="mb-0">
                            {{ !empty($client['last_login_at']) ? date('d M Y, h:i A', strtotime($client['last_login_at'])) : __('common.never') }}
                        </h6>
                    </div>
                </div>
            </div>

            {{-- Actions --}}
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">{{ __('common.quick_actions') }}</h5>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        {{-- Status Change Dropdown --}}
                        <div class="btn-group w-100" role="group">
                            <button type="button" class="btn btn-outline-secondary dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="bx bx-edit-alt me-1"></i> {{ __('common.change_status') }}
                            </button>
                            <ul class="dropdown-menu w-100">
                                @if($status !== 'active')
                                    <li>
                                        <a class="dropdown-item text-success" href="javascript:void(0);" onclick="updateStatus('active')">
                                            <i class="bx bx-check-circle me-1"></i> {{ __('common.activate') }}
                                        </a>
                                    </li>
                                @endif
                                @if($status !== 'inactive')
                                    <li>
                                        <a class="dropdown-item text-warning" href="javascript:void(0);" onclick="updateStatus('inactive')">
                                            <i class="bx bx-pause-circle me-1"></i> {{ __('common.deactivate') }}
                                        </a>
                                    </li>
                                @endif
                                @if($status !== 'suspended')
                                    <li>
                                        <a class="dropdown-item text-danger" href="javascript:void(0);" onclick="updateStatus('suspended')">
                                            <i class="bx bx-block me-1"></i> {{ __('common.suspend') }}
                                        </a>
                                    </li>
                                @endif
                            </ul>
                        </div>

                        {{-- Edit Client --}}
                        <button type="button" class="btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#editClientModal">
                            <i class="bx bx-edit me-1"></i> {{ __('common.edit_client_info') }}
                        </button>

                        {{-- Send Notification --}}
                        <button type="button" class="btn btn-outline-info" data-bs-toggle="modal" data-bs-target="#sendNotificationModal">
                            <i class="bx bx-bell me-1"></i> {{ __('common.send_notification') }}
                        </button>

                        {{-- Export Client Data --}}
                        <button type="button" class="btn btn-outline-secondary" onclick="alert('Export feature coming soon!')">
                            <i class="bx bx-download me-1"></i> {{ __('common.export_data') }}
                        </button>

                        {{-- Delete Client --}}
                        <button type="button" class="btn btn-outline-danger" onclick="deleteClient()">
                            <i class="bx bx-trash me-1"></i> {{ __('common.delete_client') }}
                        </button>
                    </div>
                </div>
            </div>
        </div>

        {{-- Main Content --}}
        <div class="col-xl-8">
            {{-- Statistics Overview --}}
            <div class="row">
                <div class="col-md-4">
                    <div class="card card-animate">
                        <div class="card-body">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <p class="fw-medium text-muted mb-0">{{ __('common.pending_bookings') }}</p>
                                    <h2 class="mt-2 ff-secondary fw-semibold text-warning" id="pendingBookings">
                                        <span class="spinner-border spinner-border-sm" role="status"></span>
                                    </h2>
                                </div>
                                <div>
                                    <div class="avatar-sm flex-shrink-0">
                                        <span class="avatar-title bg-warning-subtle rounded-circle fs-2">
                                            <i class="bx bx-time-five text-warning"></i>
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="card card-animate">
                        <div class="card-body">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <p class="fw-medium text-muted mb-0">{{ __('common.completed') }}</p>
                                    <h2 class="mt-2 ff-secondary fw-semibold text-success" id="completedBookings">
                                        <span class="spinner-border spinner-border-sm" role="status"></span>
                                    </h2>
                                </div>
                                <div>
                                    <div class="avatar-sm flex-shrink-0">
                                        <span class="avatar-title bg-success-subtle rounded-circle fs-2">
                                            <i class="bx bx-check-circle text-success"></i>
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="card card-animate">
                        <div class="card-body">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <p class="fw-medium text-muted mb-0">{{ __('common.cancelled') }}</p>
                                    <h2 class="mt-2 ff-secondary fw-semibold text-danger" id="cancelledBookings">
                                        <span class="spinner-border spinner-border-sm" role="status"></span>
                                    </h2>
                                </div>
                                <div>
                                    <div class="avatar-sm flex-shrink-0">
                                        <span class="avatar-title bg-danger-subtle rounded-circle fs-2">
                                            <i class="bx bx-x-circle text-danger"></i>
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Tabs --}}
            <div class="card">
                <div class="card-header">
                    <ul class="nav nav-tabs-custom card-header-tabs border-bottom-0" role="tablist">
                        <li class="nav-item">
                            <a class="nav-link active" data-bs-toggle="tab" href="#bookings" role="tab">
                                <i class="mdi mdi-calendar-check me-1"></i> {{ __('common.bookings') }}
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" data-bs-toggle="tab" href="#transactions" role="tab">
                                <i class="mdi mdi-cash me-1"></i> {{ __('common.transactions') }}
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" data-bs-toggle="tab" href="#addresses" role="tab">
                                <i class="mdi mdi-map-marker me-1"></i> {{ __('common.addresses') }}
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" data-bs-toggle="tab" href="#activity" role="tab">
                                <i class="bx bx-history me-1"></i> {{ __('common.activity_log') }}
                            </a>
                        </li>
                    </ul>
                </div>
                <div class="card-body">
                    <div class="tab-content">
                        {{-- Bookings Tab --}}
                        <div class="tab-pane active" id="bookings" role="tabpanel">
                            <div id="bookings-list">
                                <div class="text-center py-4">
                                    <div class="spinner-border text-primary" role="status">
                                        <span class="visually-hidden">{{ __('common.loading') }}...</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Transactions Tab --}}
                        <div class="tab-pane" id="transactions" role="tabpanel">
                            <div id="transactions-list">
                                <div class="text-center py-4">
                                    <div class="spinner-border text-primary" role="status">
                                        <span class="visually-hidden">{{ __('common.loading') }}...</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Addresses Tab --}}
                        <div class="tab-pane" id="addresses" role="tabpanel">
                            @if(!empty($client['addresses']) && count($client['addresses']) > 0)
                                <div class="row g-3">
                                    @foreach($client['addresses'] as $address)
                                        <div class="col-md-6">
                                            <div class="card border mb-0">
                                                <div class="card-body">
                                                    @if($address['is_default'] ?? false)
                                                        <span class="badge bg-primary-subtle text-primary float-end">{{ __('common.default') }}</span>
                                                    @endif
                                                    <h6 class="mb-2">{{ $address['label'] ?? __('common.home') }}</h6>
                                                    <p class="text-muted mb-0">{{ $address['full_address'] ?? 'N/A' }}</p>
                                                    @if(!empty($address['city']))
                                                        <p class="text-muted mb-0">{{ $address['city'] }}</p>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                <div class="text-center py-5">
                                    <iconify-icon icon="solar:map-bold-duotone" class="fs-48 text-muted mb-3"></iconify-icon>
                                    <p class="text-muted mb-0">{{ __('common.no_addresses_added') }}</p>
                                </div>
                            @endif
                        </div>

                        {{-- Activity Log Tab --}}
                        <div class="tab-pane" id="activity" role="tabpanel">
                            <div class="timeline">
                                {{-- Sample activity items - This will be populated from backend later --}}
                                <div class="timeline-item">
                                    <div class="timeline-marker bg-success"></div>
                                    <div class="timeline-content">
                                        <div class="d-flex justify-content-between mb-1">
                                            <h6 class="mb-0">{{ __('common.account_created') }}</h6>
                                            <small class="text-muted">{{ date('d M Y', strtotime($client['created_at'] ?? now())) }}</small>
                                        </div>
                                        <p class="text-muted mb-0">{{ __('common.client_registered_platform') }}</p>
                                    </div>
                                </div>

                                @if(!empty($client['phone_verified_at']))
                                <div class="timeline-item">
                                    <div class="timeline-marker bg-info"></div>
                                    <div class="timeline-content">
                                        <div class="d-flex justify-content-between mb-1">
                                            <h6 class="mb-0">{{ __('common.phone_verified') }}</h6>
                                            <small class="text-muted">{{ date('d M Y', strtotime($client['phone_verified_at'])) }}</small>
                                        </div>
                                        <p class="text-muted mb-0">{{ __('common.phone_number_verified') }}</p>
                                    </div>
                                </div>
                                @endif

                                @if(!empty($client['last_login_at']))
                                <div class="timeline-item">
                                    <div class="timeline-marker bg-primary"></div>
                                    <div class="timeline-content">
                                        <div class="d-flex justify-content-between mb-1">
                                            <h6 class="mb-0">{{ __('common.last_login') }}</h6>
                                            <small class="text-muted">{{ date('d M Y, h:i A', strtotime($client['last_login_at'])) }}</small>
                                        </div>
                                        <p class="text-muted mb-0">{{ __('common.client_logged_in') }}</p>
                                    </div>
                                </div>
                                @endif

                                <div class="timeline-item">
                                    <div class="timeline-marker bg-secondary"></div>
                                    <div class="timeline-content">
                                        <p class="text-muted mb-0"><i>{{ __('common.more_activity_text') }}</i></p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Edit Client Modal --}}
    <div class="modal fade" id="editClientModal" tabindex="-1" aria-labelledby="editClientModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editClientModalLabel">
                        <i class="bx bx-edit me-2"></i>{{ __('common.edit_client_information') }}
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="editClientForm">
                    @csrf
                    <div class="modal-body">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">{{ __('common.full_name') }} <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" name="name" id="edit_name" required value="{{ $client['name'] ?? '' }}">
                                <div class="invalid-feedback"></div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">{{ __('common.email') }} <span class="text-danger">*</span></label>
                                <input type="email" class="form-control" name="email" id="edit_email" required value="{{ $client['email'] ?? '' }}">
                                <div class="invalid-feedback"></div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">{{ __('common.phone') }} <span class="text-danger">*</span></label>
                                <input type="tel" class="form-control" name="phone" id="edit_phone" required value="{{ $client['phone'] ?? '' }}">
                                <div class="invalid-feedback"></div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">{{ __('common.city') }}</label>
                                <select class="form-select" name="city_id" id="edit_city_id">
                                    <option value="">{{ __('common.select_city') }}</option>
                                    @php
                                        $cities = \App\Models\City::select('id', 'name_en', 'name_ar')->get();
                                    @endphp
                                    @foreach($cities as $city)
                                        <option value="{{ $city->id }}" {{ ($client['city_id'] ?? '') == $city->id ? 'selected' : '' }}>
                                            {{ app()->getLocale() === 'ar' ? $city->name_ar : $city->name_en }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">{{ __('common.gender') }}</label>
                                <select class="form-select" name="gender" id="edit_gender">
                                    <option value="">{{ __('common.select_gender') }}</option>
                                    <option value="male" {{ ($client['gender'] ?? '') == 'male' ? 'selected' : '' }}>{{ __('common.male') }}</option>
                                    <option value="female" {{ ($client['gender'] ?? '') == 'female' ? 'selected' : '' }}>{{ __('common.female') }}</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">{{ __('common.date_of_birth') }}</label>
                                <input type="date" class="form-control" name="date_of_birth" id="edit_date_of_birth" value="{{ $client['date_of_birth'] ?? '' }}">
                                <div class="invalid-feedback"></div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('common.cancel') }}</button>
                        <button type="submit" class="btn btn-primary">
                            <i class="bx bx-save me-1"></i>{{ __('common.save_changes') }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- Send Notification Modal --}}
    <div class="modal fade" id="sendNotificationModal" tabindex="-1" aria-labelledby="sendNotificationModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="sendNotificationModalLabel">
                        <i class="bx bx-bell me-2"></i>{{ __('common.send_notification_to') }} {{ $client['name'] ?? __('common.client') }}
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="{{ __('common.close') }}"></button>
                </div>
                <div class="modal-body">
                    <form id="sendNotificationForm">
                        <div class="mb-3">
                            <label for="notificationTitle" class="form-label">{{ __('common.title') }} <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="notificationTitle" required placeholder="{{ __('common.eg_special_offer') }}">
                        </div>

                        <div class="mb-3">
                            <label for="notificationMessage" class="form-label">{{ __('common.message') }} <span class="text-danger">*</span></label>
                            <textarea class="form-control" id="notificationMessage" rows="4" required placeholder="{{ __('common.enter_message_here') }}"></textarea>
                            <small class="text-muted">{{ __('common.message_sent_device') }}</small>
                        </div>

                        <div class="mb-3">
                            <label for="notificationType" class="form-label">{{ __('common.notification_type') }}</label>
                            <select class="form-select" id="notificationType">
                                <option value="general">{{ __('common.general') }}</option>
                                <option value="promotional">{{ __('common.promotional') }}</option>
                                <option value="informational">{{ __('common.informational') }}</option>
                                <option value="alert">{{ __('common.alert') }}</option>
                            </select>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('common.cancel') }}</button>
                    <button type="button" class="btn btn-primary" onclick="sendNotification()">
                        <i class="bx bx-send me-1"></i>{{ __('common.send_notification') }}
                    </button>
                </div>
            </div>
        </div>
    </div>

    {{-- Send Message Modal --}}
    <div class="modal fade" id="sendMessageModal" tabindex="-1" aria-labelledby="sendMessageModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="sendMessageModalLabel">
                        <i class="bx bx-message-dots me-2"></i>{{ __('common.send_message_to') }} {{ $client['name'] ?? __('common.client') }}
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="{{ __('common.close') }}"></button>
                </div>
                <div class="modal-body">
                    <form id="sendMessageForm">
                        <div class="mb-3">
                            <label for="messageContent" class="form-label">{{ __('common.message') }} <span class="text-danger">*</span></label>
                            <textarea class="form-control" id="messageContent" rows="5" required placeholder="{{ __('common.type_message_here') }}"></textarea>
                            <small class="text-muted">{{ __('common.sent_as_chat_message') }}</small>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('common.cancel') }}</button>
                    <button type="button" class="btn btn-primary" onclick="sendMessage()">
                        <i class="bx bx-send me-1"></i>{{ __('common.send_message') }}
                    </button>
                </div>
            </div>
        </div>
    </div>
    @endif
@endsection

@section('script')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        // Calculate and display booking statistics
        function loadStatistics() {
            fetch('/clients/{{ $client['id'] ?? '' }}/bookings')
                .then(response => response.json())
                .then(data => {
                    if (data.success && data.data && data.data.bookings) {
                        const bookings = data.data.bookings;
                        const pending = bookings.filter(b => b.status === 'pending').length;
                        const completed = bookings.filter(b => b.status === 'completed').length;
                        const cancelled = bookings.filter(b => b.status === 'cancelled').length;

                        document.getElementById('pendingBookings').textContent = pending;
                        document.getElementById('completedBookings').textContent = completed;
                        document.getElementById('cancelledBookings').textContent = cancelled;
                    } else {
                        document.getElementById('pendingBookings').textContent = '0';
                        document.getElementById('completedBookings').textContent = '0';
                        document.getElementById('cancelledBookings').textContent = '0';
                    }
                })
                .catch(error => {
                    console.error('Error loading statistics:', error);
                    document.getElementById('pendingBookings').textContent = '-';
                    document.getElementById('completedBookings').textContent = '-';
                    document.getElementById('cancelledBookings').textContent = '-';
                });
        }

        // Load bookings with pagination
        let bookingsCurrentPage = 1;
        function loadBookings(page = 1) {
            bookingsCurrentPage = page;
            console.log('Loading bookings for client ID:', '{{ $client['id'] ?? '' }}', 'Page:', page);
            fetch(`/clients/{{ $client['id'] ?? '' }}/bookings?page=${page}`)
                .then(response => {
                    console.log('Bookings response status:', response.status);
                    if (!response.ok) {
                        return response.json().then(err => {
                            console.error('Bookings error response:', err);
                            throw new Error(err.message || 'Failed to load bookings');
                        });
                    }
                    return response.json();
                })
                .then(data => {
                    console.log('Bookings data:', data);
                    if (data.success && data.data && data.data.bookings) {
                        renderBookings(data.data.bookings, data.data.pagination);
                    } else {
                        console.warn('No bookings in response');
                        document.getElementById('bookings-list').innerHTML =
                            '<div class="text-center py-5"><iconify-icon icon="solar:inbox-line-bold-duotone" class="fs-48 text-muted mb-3"></iconify-icon><p class="text-muted">{{ __('common.no_bookings_found') }}</p></div>';
                    }
                })
                .catch(error => {
                    console.error('Bookings error:', error);
                    document.getElementById('bookings-list').innerHTML =
                        '<div class="text-center py-5"><iconify-icon icon="solar:danger-circle-bold-duotone" class="fs-48 text-danger mb-3"></iconify-icon><p class="text-danger">{{ __('common.error_loading_bookings') }}</p><small class="text-muted d-block mt-2">' + error.message + '</small></div>';
                });
        }

        function renderBookings(bookings, pagination) {
            console.log('Rendering bookings, type:', typeof bookings, 'isArray:', Array.isArray(bookings), 'value:', bookings);

            // Check if bookings is an array
            if (!Array.isArray(bookings)) {
                console.error('Bookings is not an array:', bookings);
                document.getElementById('bookings-list').innerHTML =
                    '<div class="text-center py-5"><iconify-icon icon="solar:danger-circle-bold-duotone" class="fs-48 text-warning mb-3"></iconify-icon><p class="text-warning">{{ __('common.invalid_data_format') }}</p><small class="text-muted d-block mt-2">Expected array, received ' + typeof bookings + '</small></div>';
                return;
            }

            if (bookings.length === 0) {
                document.getElementById('bookings-list').innerHTML =
                    '<div class="text-center py-5"><iconify-icon icon="solar:inbox-line-bold-duotone" class="fs-48 text-muted mb-3"></iconify-icon><p class="text-muted">{{ __('common.no_bookings_yet') }}</p></div>';
                return;
            }

            let html = '<div class="table-responsive"><table class="table table-hover align-middle mb-0">';
            html += '<thead class="bg-light-subtle"><tr>';
            html += '<th>ID</th><th>Service</th><th>Provider</th><th>Date</th><th>Amount</th><th>Status</th><th>Action</th>';
            html += '</tr></thead><tbody>';

            bookings.forEach(booking => {
                const statusColors = {
                    'pending': 'warning',
                    'accepted': 'info',
                    'in_progress': 'primary',
                    'completed': 'success',
                    'cancelled': 'danger'
                };
                const color = statusColors[booking.status] || 'secondary';

                html += `<tr>
                    <td>#${booking.id}</td>
                    <td>${booking.service?.name || 'N/A'}</td>
                    <td>${booking.provider?.business_name || 'N/A'}</td>
                    <td>${new Date(booking.booking_date).toLocaleDateString()}</td>
                    <td>${parseFloat(booking.total_amount).toFixed(2)} SAR</td>
                    <td><span class="badge bg-${color}-subtle text-${color}">${booking.status}</span></td>
                    <td><a href="/bookings/${booking.id}" class="btn btn-sm btn-light">View</a></td>
                </tr>`;
            });

            html += '</tbody></table></div>';

            // Add pagination if available
            if (pagination && pagination.total > pagination.per_page) {
                html += '<div class="card-footer bg-light"><div class="row align-items-center">';
                html += '<div class="col-sm-6"><div class="text-muted">{{ __('common.showing') }} ' + (pagination.from || 1) + ' {{ __('common.to') }} ' + (pagination.to || 0) + ' {{ __('common.of') }} ' + pagination.total + ' {{ __('common.bookings') }}</div></div>';
                html += '<div class="col-sm-6"><nav><ul class="pagination justify-content-end mb-0">';

                // Previous button
                if (pagination.current_page > 1) {
                    html += `<li class="page-item"><a class="page-link" href="javascript:void(0);" onclick="loadBookings(${pagination.current_page - 1})">{{ __('common.previous') }}</a></li>`;
                }

                // Page numbers
                for (let i = 1; i <= pagination.last_page; i++) {
                    if (i === 1 || i === pagination.last_page || Math.abs(i - pagination.current_page) <= 2) {
                        html += `<li class="page-item ${i === pagination.current_page ? 'active' : ''}"><a class="page-link" href="javascript:void(0);" onclick="loadBookings(${i})">${i}</a></li>`;
                    } else if (Math.abs(i - pagination.current_page) === 3) {
                        html += '<li class="page-item disabled"><span class="page-link">...</span></li>';
                    }
                }

                // Next button
                if (pagination.current_page < pagination.last_page) {
                    html += `<li class="page-item"><a class="page-link" href="javascript:void(0);" onclick="loadBookings(${pagination.current_page + 1})">Next</a></li>`;
                }

                html += '</ul></nav></div></div></div>';
            }

            document.getElementById('bookings-list').innerHTML = html;
        }

        // Load transactions with pagination
        let transactionsCurrentPage = 1;
        function loadTransactions(page = 1) {
            transactionsCurrentPage = page;
            console.log('Loading transactions for client ID:', '{{ $client['id'] ?? '' }}', 'Page:', page);
            fetch(`/clients/{{ $client['id'] ?? '' }}/transactions?page=${page}`)
                .then(response => {
                    console.log('Transactions response status:', response.status);
                    if (!response.ok) {
                        return response.json().then(err => {
                            console.error('Transactions error response:', err);
                            throw new Error(err.message || 'Failed to load transactions');
                        });
                    }
                    return response.json();
                })
                .then(data => {
                    console.log('Transactions data:', data);
                    if (data.success && data.data && data.data.transactions) {
                        renderTransactions(data.data.transactions, data.data.pagination);
                    } else {
                        console.warn('No transactions in response');
                        document.getElementById('transactions-list').innerHTML =
                            '<div class="text-center py-5"><iconify-icon icon="solar:inbox-line-bold-duotone" class="fs-48 text-muted mb-3"></iconify-icon><p class="text-muted">No transactions found</p></div>';
                    }
                })
                .catch(error => {
                    console.error('Transactions error:', error);
                    document.getElementById('transactions-list').innerHTML =
                        '<div class="text-center py-5"><iconify-icon icon="solar:danger-circle-bold-duotone" class="fs-48 text-danger mb-3"></iconify-icon><p class="text-danger">Error loading transactions</p><small class="text-muted d-block mt-2">' + error.message + '</small></div>';
                });
        }

        function renderTransactions(transactions, pagination) {
            console.log('Rendering transactions, type:', typeof transactions, 'isArray:', Array.isArray(transactions), 'value:', transactions);

            // Check if transactions is an array
            if (!Array.isArray(transactions)) {
                console.error('Transactions is not an array:', transactions);
                document.getElementById('transactions-list').innerHTML =
                    '<div class="text-center py-5"><iconify-icon icon="solar:danger-circle-bold-duotone" class="fs-48 text-warning mb-3"></iconify-icon><p class="text-warning">Invalid transactions data format</p><small class="text-muted d-block mt-2">Expected array, received ' + typeof transactions + '</small></div>';
                return;
            }

            if (transactions.length === 0) {
                document.getElementById('transactions-list').innerHTML =
                    '<div class="text-center py-5"><iconify-icon icon="solar:inbox-line-bold-duotone" class="fs-48 text-muted mb-3"></iconify-icon><p class="text-muted">No transactions yet</p></div>';
                return;
            }

            let html = '<div class="table-responsive"><table class="table table-hover align-middle mb-0">';
            html += '<thead class="bg-light-subtle"><tr>';
            html += '<th>Date</th><th>Description</th><th>Type</th><th>Amount</th><th>Status</th>';
            html += '</tr></thead><tbody>';

            transactions.forEach(transaction => {
                const typeColors = {
                    'payment': 'primary',
                    'refund': 'warning'
                };
                const statusColors = {
                    'completed': 'success',
                    'pending': 'warning',
                    'failed': 'danger'
                };

                html += `<tr>
                    <td>${new Date(transaction.created_at).toLocaleDateString()}</td>
                    <td>${transaction.description || 'N/A'}</td>
                    <td><span class="badge bg-${typeColors[transaction.type] || 'secondary'}-subtle text-${typeColors[transaction.type] || 'secondary'}">${transaction.type}</span></td>
                    <td class="fw-semibold">${parseFloat(transaction.amount).toFixed(2)} SAR</td>
                    <td><span class="badge bg-${statusColors[transaction.status] || 'secondary'}-subtle text-${statusColors[transaction.status] || 'secondary'}">${transaction.status}</span></td>
                </tr>`;
            });

            html += '</tbody></table></div>';

            // Add pagination if available
            if (pagination && pagination.total > pagination.per_page) {
                html += '<div class="card-footer bg-light"><div class="row align-items-center">';
                html += '<div class="col-sm-6"><div class="text-muted">Showing ' + (pagination.from || 1) + ' to ' + (pagination.to || 0) + ' of ' + pagination.total + ' transactions</div></div>';
                html += '<div class="col-sm-6"><nav><ul class="pagination justify-content-end mb-0">';

                // Previous button
                if (pagination.current_page > 1) {
                    html += `<li class="page-item"><a class="page-link" href="javascript:void(0);" onclick="loadTransactions(${pagination.current_page - 1})">Previous</a></li>`;
                }

                // Page numbers
                for (let i = 1; i <= pagination.last_page; i++) {
                    if (i === 1 || i === pagination.last_page || Math.abs(i - pagination.current_page) <= 2) {
                        html += `<li class="page-item ${i === pagination.current_page ? 'active' : ''}"><a class="page-link" href="javascript:void(0);" onclick="loadTransactions(${i})">${i}</a></li>`;
                    } else if (Math.abs(i - pagination.current_page) === 3) {
                        html += '<li class="page-item disabled"><span class="page-link">...</span></li>';
                    }
                }

                // Next button
                if (pagination.current_page < pagination.last_page) {
                    html += `<li class="page-item"><a class="page-link" href="javascript:void(0);" onclick="loadTransactions(${pagination.current_page + 1})">Next</a></li>`;
                }

                html += '</ul></nav></div></div></div>';
            }

            document.getElementById('transactions-list').innerHTML = html;
        }

        // Update client status with improved UX
        function updateStatus(status) {
            const statusMessages = {
                'active': {
                    title: 'Activate Client',
                    text: 'This client will be able to book services again.',
                    icon: 'info',
                    confirmButton: 'Yes, activate',
                    confirmColor: '#28a745'
                },
                'inactive': {
                    title: 'Deactivate Client',
                    text: 'This client will be temporarily deactivated.',
                    icon: 'warning',
                    confirmButton: 'Yes, deactivate',
                    confirmColor: '#ffc107'
                },
                'suspended': {
                    title: 'Suspend Client',
                    text: 'This client will be suspended and cannot book services.',
                    icon: 'warning',
                    confirmButton: 'Yes, suspend',
                    confirmColor: '#dc3545'
                }
            };

            const config = statusMessages[status] || statusMessages['inactive'];

            Swal.fire({
                title: config.title,
                text: config.text,
                icon: config.icon,
                showCancelButton: true,
                confirmButtonColor: config.confirmColor,
                cancelButtonColor: '#6c757d',
                confirmButtonText: config.confirmButton,
                cancelButtonText: 'Cancel',
                showLoaderOnConfirm: true,
                preConfirm: () => {
                    return fetch('/clients/{{ $client['id'] ?? '' }}/status', {
                        method: 'PUT',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'X-Requested-With': 'XMLHttpRequest',
                            'Accept': 'application/json'
                        },
                        body: JSON.stringify({ status: status })
                    })
                    .then(response => {
                        if (!response.ok) {
                            return response.json().then(err => Promise.reject(err));
                        }
                        return response.json();
                    })
                    .then(data => {
                        if (!data.success) {
                            throw new Error(data.message || 'Failed to update status');
                        }
                        return data;
                    })
                    .catch(error => {
                        Swal.showValidationMessage(`Request failed: ${error.message || 'Unknown error'}`);
                    });
                },
                allowOutsideClick: () => !Swal.isLoading()
            }).then((result) => {
                if (result.isConfirmed && result.value) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Status Updated!',
                        text: result.value.message || 'Client status has been updated successfully.',
                        timer: 2000,
                        showConfirmButton: false
                    }).then(() => {
                        location.reload();
                    });
                }
            });
        }

        // Delete client function
        function deleteClient() {
            Swal.fire({
                title: 'Delete Client?',
                text: "This action cannot be undone! All client data will be permanently deleted.",
                icon: 'error',
                showCancelButton: true,
                confirmButtonColor: '#dc3545',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Yes, delete permanently',
                cancelButtonText: 'Cancel',
                showLoaderOnConfirm: true,
                preConfirm: () => {
                    return fetch('/clients/{{ $client['id'] ?? '' }}', {
                        method: 'DELETE',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'X-Requested-With': 'XMLHttpRequest',
                            'Accept': 'application/json'
                        }
                    })
                    .then(response => {
                        if (!response.ok) {
                            return response.json().then(err => Promise.reject(err));
                        }
                        return response.json();
                    })
                    .then(data => {
                        if (!data.success) {
                            throw new Error(data.message || 'Failed to delete client');
                        }
                        return data;
                    })
                    .catch(error => {
                        Swal.showValidationMessage(`Request failed: ${error.message || 'Unknown error'}`);
                    });
                },
                allowOutsideClick: () => !Swal.isLoading()
            }).then((result) => {
                if (result.isConfirmed && result.value) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Deleted!',
                        text: 'Client has been deleted successfully.',
                        timer: 2000,
                        showConfirmButton: false
                    }).then(() => {
                        window.location.href = '{{ route('clients.index') }}';
                    });
                }
            });
        }

        // Load data on tab click
        document.querySelector('a[href="#bookings"]').addEventListener('shown.bs.tab', function() {
            loadBookings();
        });

        document.querySelector('a[href="#transactions"]').addEventListener('shown.bs.tab', function() {
            loadTransactions();
        });

        // Send notification function
        function sendNotification() {
            const title = document.getElementById('notificationTitle').value;
            const message = document.getElementById('notificationMessage').value;
            const type = document.getElementById('notificationType').value;

            if (!title || !message) {
                Swal.fire('Error', 'Please fill in all required fields', 'error');
                return;
            }

            const button = event.target;
            button.disabled = true;
            button.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span>Sending...';

            fetch('/clients/{{ $client['id'] ?? 0 }}/send-notification', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json'
                },
                body: JSON.stringify({
                    title: title,
                    message: message,
                    notification_type: type
                })
            })
            .then(response => {
                console.log('Notification response status:', response.status);
                if (!response.ok) {
                    return response.json().then(err => {
                        console.error('Notification error response:', err);
                        throw err;
                    });
                }
                return response.json();
            })
            .then(data => {
                console.log('Notification success:', data);
                button.disabled = false;
                button.innerHTML = '<i class="bx bx-send me-1"></i>Send Notification';

                if (data.success) {
                    // Close modal
                    const modal = bootstrap.Modal.getInstance(document.getElementById('sendNotificationModal'));
                    modal.hide();

                    // Reset form
                    document.getElementById('sendNotificationForm').reset();

                    // Show success message
                    Swal.fire({
                        icon: 'success',
                        title: 'Notification Sent!',
                        text: data.message || 'Notification has been sent successfully',
                        timer: 3000,
                        showConfirmButton: false
                    });
                } else {
                    Swal.fire('Error', data.message || 'Failed to send notification', 'error');
                }
            })
            .catch(error => {
                button.disabled = false;
                button.innerHTML = '<i class="bx bx-send me-1"></i>Send Notification';
                console.error('Notification error:', error);

                let errorMessage = error.message || 'An error occurred while sending notification';
                if (error.errors) {
                    errorMessage += '<br><small>' + Object.values(error.errors).flat().join('<br>') + '</small>';
                }

                Swal.fire({
                    icon: 'error',
                    title: 'Error!',
                    html: errorMessage
                });
            });
        }

        // Send message function
        function sendMessage() {
            const message = document.getElementById('messageContent').value;

            if (!message) {
                Swal.fire('Error', 'Please enter a message', 'error');
                return;
            }

            const button = event.target;
            button.disabled = true;
            button.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span>Sending...';

            // Send message through dashboard proxy
            fetch('/clients/{{ $client['id'] ?? 0 }}/send-message', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json'
                },
                body: JSON.stringify({
                    message: message
                })
            })
            .then(response => {
                console.log('Message response status:', response.status);
                if (!response.ok) {
                    return response.json().then(err => {
                        console.error('Message error response:', err);
                        throw err;
                    });
                }
                return response.json();
            })
            .then(data => {
                console.log('Message success:', data);
                button.disabled = false;
                button.innerHTML = '<i class="bx bx-send me-1"></i>Send Message';

                if (data.success) {
                    // Close modal
                    const modal = bootstrap.Modal.getInstance(document.getElementById('sendMessageModal'));
                    modal.hide();

                    // Reset form
                    document.getElementById('sendMessageForm').reset();

                    // Show success message
                    Swal.fire({
                        icon: 'success',
                        title: 'Message Sent!',
                        text: 'Your message has been sent successfully',
                        timer: 3000,
                        showConfirmButton: false
                    });
                } else {
                    Swal.fire('Error', data.message || 'Failed to send message', 'error');
                }
            })
            .catch(error => {
                button.disabled = false;
                button.innerHTML = '<i class="bx bx-send me-1"></i>Send Message';
                console.error('Message error:', error);

                let errorMessage = error.message || 'An error occurred while sending message';
                if (error.errors) {
                    errorMessage += '<br><small>' + Object.values(error.errors).flat().join('<br>') + '</small>';
                }

                Swal.fire({
                    icon: 'error',
                    title: 'Error!',
                    html: errorMessage
                });
            });
        }

        // Edit client form submission
        document.getElementById('editClientForm').addEventListener('submit', function(e) {
            e.preventDefault();

            const form = this;
            const submitBtn = form.querySelector('button[type="submit"]');
            const formData = new FormData(form);

            // Clear previous errors
            form.querySelectorAll('.is-invalid').forEach(el => el.classList.remove('is-invalid'));
            form.querySelectorAll('.invalid-feedback').forEach(el => el.textContent = '');

            // Disable submit button
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span>Saving...';

            // Convert FormData to JSON
            const data = {};
            formData.forEach((value, key) => {
                if (key !== '_token') {
                    data[key] = value;
                }
            });

            fetch('/clients/{{ $client['id'] ?? 0 }}', {
                method: 'PUT',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': formData.get('_token'),
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                },
                body: JSON.stringify(data)
            })
            .then(response => {
                if (!response.ok) {
                    return response.json().then(err => {
                        throw err;
                    });
                }
                return response.json();
            })
            .then(result => {
                if (result.success) {
                    // Close modal
                    const modal = bootstrap.Modal.getInstance(document.getElementById('editClientModal'));
                    modal.hide();

                    // Show success message and reload
                    Swal.fire({
                        icon: 'success',
                        title: 'Client Updated!',
                        text: result.message || 'Client information has been updated successfully.',
                        timer: 2000,
                        showConfirmButton: false
                    }).then(() => {
                        // Reload page to show updated data
                        location.reload();
                    });
                }
            })
            .catch(error => {
                // Handle validation errors
                if (error.errors) {
                    Object.keys(error.errors).forEach(field => {
                        const input = form.querySelector(`[name="${field}"]`);
                        if (input) {
                            input.classList.add('is-invalid');
                            const feedback = input.nextElementSibling;
                            if (feedback && feedback.classList.contains('invalid-feedback')) {
                                feedback.textContent = error.errors[field][0];
                                feedback.style.display = 'block';
                            }
                        }
                    });

                    // Show alert with specific error
                    const firstError = Object.values(error.errors)[0][0];
                    Swal.fire({
                        icon: 'error',
                        title: 'Validation Error',
                        text: firstError || error.message || 'Please check the form and try again.'
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Update Failed',
                        text: error.message || 'An unexpected error occurred. Please try again.'
                    });
                }
            })
            .finally(() => {
                // Re-enable submit button
                submitBtn.disabled = false;
                submitBtn.innerHTML = '<i class="bx bx-save me-1"></i>Save Changes';
            });
        });

        // Load data on page load
        loadStatistics();
        loadBookings();
    </script>
@endsection
