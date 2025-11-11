@extends('layouts.vertical', ['title' => 'Services'])

@section('content')
    {{-- Page Header --}}
    <div class="row mb-3">
        <div class="col-12">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <h4 class="mb-1 fw-bold">Services Management</h4>
                    <p class="text-muted mb-0">Manage and monitor all services across your platform</p>
                </div>
                <div class="d-flex gap-2">
                    <a href="{{ route('services.create') }}" class="btn btn-primary">
                        <i class="mdi mdi-plus-circle me-1"></i> Add New Service
                    </a>
                </div>
            </div>
        </div>
    </div>

    {{-- Stats Cards --}}
    <div class="row">
        <div class="col-md-4">
            <div class="card card-animate overflow-hidden">
                <div class="position-absolute end-0 top-0 p-3">
                    <div class="avatar-md bg-primary-subtle rounded-circle">
                        <iconify-icon icon="solar:widget-5-bold-duotone" class="fs-32 text-primary avatar-title"></iconify-icon>
                    </div>
                </div>
                <div class="card-body" style="z-index: 1">
                    <p class="text-muted text-uppercase fw-semibold fs-13 mb-2">Total Services</p>
                    <h3 class="mb-3 fw-bold">{{ number_format($stats['total'] ?? 0) }}</h3>
                    <div class="d-flex align-items-center gap-2">
                        <span class="badge bg-light text-dark">
                            <i class="mdi mdi-trending-up"></i> Active Platform
                        </span>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card card-animate overflow-hidden">
                <div class="position-absolute end-0 top-0 p-3">
                    <div class="avatar-md bg-success-subtle rounded-circle">
                        <iconify-icon icon="solar:home-bold-duotone" class="fs-32 text-success avatar-title"></iconify-icon>
                    </div>
                </div>
                <div class="card-body" style="z-index: 1">
                    <p class="text-muted text-uppercase fw-semibold fs-13 mb-2">Home Services</p>
                    <h3 class="mb-3 fw-bold">{{ number_format($stats['home_service'] ?? 0) }}</h3>
                    <div class="d-flex align-items-center gap-2">
                        <span class="badge bg-success-subtle text-success">
                            <i class="mdi mdi-home-variant"></i> On-Site Available
                        </span>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card card-animate overflow-hidden">
                <div class="position-absolute end-0 top-0 p-3">
                    <div class="avatar-md bg-info-subtle rounded-circle">
                        <iconify-icon icon="solar:shop-2-bold-duotone" class="fs-32 text-info avatar-title"></iconify-icon>
                    </div>
                </div>
                <div class="card-body" style="z-index: 1">
                    <p class="text-muted text-uppercase fw-semibold fs-13 mb-2">Center Services</p>
                    <h3 class="mb-3 fw-bold">{{ number_format($stats['center_service'] ?? 0) }}</h3>
                    <div class="d-flex align-items-center gap-2">
                        <span class="badge bg-info-subtle text-info">
                            <i class="mdi mdi-store"></i> In-Center Available
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Services Table --}}
    <div class="row">
        <div class="col-xl-12">
            <div class="card">
                <div class="card-header border-bottom">
                    <div class="d-flex align-items-center justify-content-between">
                        <h5 class="card-title mb-0 fw-semibold">All Services</h5>
                        <div class="d-flex gap-2">
                            <button type="button" class="btn btn-soft-secondary btn-sm" onclick="toggleView()">
                                <i class="mdi mdi-view-grid" id="viewIcon"></i>
                            </button>
                        </div>
                    </div>
                </div>

                <div class="card-body">
                    {{-- Filters --}}
                    <form method="GET" action="{{ route('services.index') }}" class="mb-4">
                        <div class="row g-3 align-items-end">
                            <div class="col-md-4">
                                <label class="form-label fw-semibold">Search Services</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-end-0">
                                        <i class="mdi mdi-magnify"></i>
                                    </span>
                                    <input type="text" name="search" class="form-control border-start-0"
                                        placeholder="Search by name or provider..."
                                        value="{{ $filters['search'] ?? '' }}">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label fw-semibold">Category</label>
                                <select name="category_id" class="form-select">
                                    <option value="">All Categories</option>
                                    @foreach($categories as $category)
                                        <option value="{{ $category['id'] }}"
                                            {{ ($filters['category_id'] ?? '') == $category['id'] ? 'selected' : '' }}>
                                            {{ $category['name'] ?? 'N/A' }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-2">
                                <label class="form-label fw-semibold">Location Type</label>
                                <select name="service_location" class="form-select">
                                    <option value="">All Locations</option>
                                    <option value="home" {{ ($filters['service_location'] ?? '') == 'home' ? 'selected' : '' }}>
                                        <i class="mdi mdi-home"></i> Home Service
                                    </option>
                                    <option value="center" {{ ($filters['service_location'] ?? '') == 'center' ? 'selected' : '' }}>
                                        <i class="mdi mdi-store"></i> Center Service
                                    </option>
                                </select>
                            </div>
                            <div class="col-md-3 d-flex align-items-end gap-2">
                                <button type="submit" class="btn btn-primary flex-fill">
                                    <i class="mdi mdi-filter-variant me-1"></i> Apply Filters
                                </button>
                                <a href="{{ route('services.index') }}" class="btn btn-soft-secondary">
                                    <i class="mdi mdi-refresh"></i>
                                </a>
                            </div>
                        </div>
                    </form>

                    {{-- Services Grid --}}
                    <div class="row g-3" id="servicesGrid">
                        @forelse($services as $service)
                            <div class="col-md-6 col-xl-4 col-xxl-3">
                                <div class="card mb-0 service-card h-100 border shadow-sm hover-shadow-lg transition">
                                    <div class="position-relative overflow-hidden rounded-top">
                                        @if(!empty($service['image']))
                                            <img src="{{ $service['image'] }}" class="card-img-top service-image"
                                                alt="{{ $service['name'] ?? '' }}"
                                                style="height: 220px; object-fit: cover; transition: transform 0.3s ease;">
                                        @else
                                            <div class="bg-gradient bg-primary bg-opacity-10 d-flex align-items-center justify-content-center"
                                                style="height: 220px;">
                                                <iconify-icon icon="solar:widget-5-bold-duotone"
                                                    class="fs-48 text-primary opacity-50"></iconify-icon>
                                            </div>
                                        @endif

                                        <div class="position-absolute top-0 end-0 m-3">
                                            @if($service['home_service_available'] ?? false)
                                                <span class="badge bg-success shadow-sm me-1">
                                                    <i class="mdi mdi-home me-1"></i> Home
                                                </span>
                                            @endif
                                            @if($service['center_service_available'] ?? false)
                                                <span class="badge bg-info shadow-sm">
                                                    <i class="mdi mdi-store me-1"></i> Center
                                                </span>
                                            @endif
                                        </div>
                                    </div>

                                    <div class="card-body d-flex flex-column">
                                        <div class="mb-3">
                                            <h5 class="card-title mb-2 fw-bold text-dark">{{ $service['name'] ?? 'N/A' }}</h5>
                                            <p class="text-muted fs-13 mb-0">
                                                <i class="mdi mdi-tag-outline me-1"></i>
                                                {{ $service['category']['name'] ?? 'N/A' }}
                                            </p>
                                        </div>

                                        @if(!empty($service['description']))
                                            <p class="card-text text-muted fs-13 mb-3">
                                                {{ Str::limit($service['description'], 70) }}
                                            </p>
                                        @endif

                                        <div class="row g-3 mb-3">
                                            <div class="col-6">
                                                <div class="p-2 bg-light rounded text-center">
                                                    <small class="text-muted d-block mb-1">Price</small>
                                                    <span class="fw-bold text-primary fs-15">
                                                        {{ number_format($service['price'], 2) }} SAR
                                                    </span>
                                                </div>
                                            </div>
                                            <div class="col-6">
                                                <div class="p-2 bg-light rounded text-center">
                                                    <small class="text-muted d-block mb-1">Duration</small>
                                                    <span class="fw-bold text-dark fs-15">
                                                        <i class="mdi mdi-clock-outline me-1"></i>{{ $service['duration_minutes'] ?? $service['duration'] ?? 'N/A' }} min
                                                    </span>
                                                </div>
                                            </div>
                                        </div>

                                        @if(!empty($service['provider']))
                                            <div class="d-flex align-items-center gap-2 mb-3 pb-3 border-bottom">
                                                <div class="avatar-sm bg-primary-subtle rounded-circle d-flex align-items-center justify-content-center">
                                                    <span class="text-primary fw-bold fs-16">
                                                        {{ strtoupper(substr($service['provider']['business_name'] ?? 'P', 0, 1)) }}
                                                    </span>
                                                </div>
                                                <div class="flex-grow-1">
                                                    <p class="mb-0 fs-13 fw-semibold text-dark">
                                                        {{ $service['provider']['business_name'] ?? 'N/A' }}
                                                    </p>
                                                    <small class="text-muted"><i class="mdi mdi-account-outline"></i> Provider</small>
                                                </div>
                                            </div>
                                        @endif

                                        <div class="mt-auto d-flex gap-2">
                                            <a href="{{ route('services.edit', $service['id']) }}" class="btn btn-soft-primary btn-sm flex-fill">
                                                <i class="mdi mdi-pencil me-1"></i> Edit
                                            </a>
                                            <button onclick="deleteService({{ $service['id'] }}, '{{ addslashes($service['name'] ?? 'this service') }}')" 
                                                    class="btn btn-soft-danger btn-sm flex-fill">
                                                <i class="mdi mdi-delete me-1"></i> Delete
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="col-12">
                                <div class="text-center py-5">
                                    <div class="avatar-xl bg-light rounded-circle d-inline-flex align-items-center justify-content-center mb-3">
                                        <iconify-icon icon="solar:inbox-line-bold-duotone" class="fs-48 text-muted"></iconify-icon>
                                    </div>
                                    <h5 class="text-muted">No Services Found</h5>
                                    <p class="text-muted mb-3">There are no services matching your criteria</p>
                                    <a href="{{ route('services.create') }}" class="btn btn-primary">
                                        <i class="mdi mdi-plus-circle me-1"></i> Add New Service
                                    </a>
                                </div>
                            </div>
                        @endforelse
                    </div>

                    {{-- Pagination --}}
                    @if(!empty($pagination) && $pagination['last_page'] > 1)
                        <div class="d-flex justify-content-between align-items-center mt-4 pt-3 border-top">
                            <div class="text-muted">
                                Showing <span class="fw-semibold">{{ $pagination['from'] ?? 0 }}</span> to 
                                <span class="fw-semibold">{{ $pagination['to'] ?? 0 }}</span> of 
                                <span class="fw-semibold">{{ $pagination['total'] ?? 0 }}</span> entries
                            </div>
                            <nav>
                                <ul class="pagination mb-0">
                                    @if($pagination['current_page'] > 1)
                                        <li class="page-item">
                                            <a class="page-link" href="?page={{ $pagination['current_page'] - 1 }}">
                                                <i class="mdi mdi-chevron-left"></i>
                                            </a>
                                        </li>
                                    @endif

                                    @for($i = 1; $i <= $pagination['last_page']; $i++)
                                        <li class="page-item {{ $i == $pagination['current_page'] ? 'active' : '' }}">
                                            <a class="page-link" href="?page={{ $i }}">{{ $i }}</a>
                                        </li>
                                    @endfor

                                    @if($pagination['current_page'] < $pagination['last_page'])
                                        <li class="page-item">
                                            <a class="page-link" href="?page={{ $pagination['current_page'] + 1 }}">
                                                <i class="mdi mdi-chevron-right"></i>
                                            </a>
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
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<style>
    .service-card {
        transition: all 0.3s ease;
    }
    .service-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 25px rgba(0,0,0,0.1) !important;
    }
    .service-card:hover .service-image {
        transform: scale(1.05);
    }
    .hover-shadow-lg:hover {
        box-shadow: 0 1rem 3rem rgba(0,0,0,.175) !important;
    }
</style>

<script>
    function toggleView() {
        const grid = document.getElementById('servicesGrid');
        const icon = document.getElementById('viewIcon');
        // Add view toggle functionality if needed
    }

    function deleteService(serviceId, serviceName) {
        Swal.fire({
            title: 'Delete Service?',
            html: `<p class="mb-2">Are you sure you want to delete <strong>"${serviceName}"</strong>?</p>
                   <p class="text-muted small">This action cannot be undone. All related bookings and data will be affected.</p>`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#6c757d',
            confirmButtonText: '<i class="mdi mdi-delete me-1"></i> Yes, Delete It!',
            cancelButtonText: '<i class="mdi mdi-close me-1"></i> Cancel',
            customClass: {
                confirmButton: 'btn btn-danger',
                cancelButton: 'btn btn-secondary'
            },
            buttonsStyling: false
        }).then((result) => {
            if (result.isConfirmed) {
                // Show loading state
                Swal.fire({
                    title: 'Deleting...',
                    text: 'Please wait while we delete the service',
                    allowOutsideClick: false,
                    allowEscapeKey: false,
                    showConfirmButton: false,
                    willOpen: () => {
                        Swal.showLoading();
                    }
                });

                // Send delete request
                fetch(`/services/${serviceId}`, {
                    method: 'DELETE',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Deleted!',
                            text: data.message || 'Service has been deleted successfully.',
                            showConfirmButton: false,
                            timer: 1500
                        }).then(() => {
                            window.location.reload();
                        });
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error!',
                            text: data.message || 'Failed to delete service.',
                            confirmButtonColor: '#3085d6'
                        });
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    Swal.fire({
                        icon: 'error',
                        title: 'Error!',
                        text: 'Something went wrong! Please try again.',
                        confirmButtonColor: '#3085d6'
                    });
                });
            }
        });
    }
</script>
@endsection
