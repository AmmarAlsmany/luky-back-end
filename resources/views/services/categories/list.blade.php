@extends('layouts.vertical', ['title' => 'Service Categories'])

@section('content')
    {{-- Page Header --}}
    <div class="row mb-3">
        <div class="col-12">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <h4 class="mb-1 fw-bold">Service Categories</h4>
                    <p class="text-muted mb-0">Organize your services with categories</p>
                </div>
                <div class="d-flex gap-2">
                    <a href="{{ route('categories.create') }}" class="btn btn-primary">
                        <i class="mdi mdi-plus-circle me-1"></i> Add Category
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
                        <iconify-icon icon="solar:layer-bold-duotone" class="fs-32 text-primary avatar-title"></iconify-icon>
                    </div>
                </div>
                <div class="card-body" style="z-index: 1">
                    <p class="text-muted text-uppercase fw-semibold fs-13 mb-2">Total Categories</p>
                    <h3 class="mb-3 fw-bold">{{ number_format($stats['total'] ?? 0) }}</h3>
                    <div class="d-flex align-items-center gap-2">
                        <span class="badge bg-light text-dark">
                            <i class="mdi mdi-view-grid"></i> All Types
                        </span>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card card-animate overflow-hidden">
                <div class="position-absolute end-0 top-0 p-3">
                    <div class="avatar-md bg-success-subtle rounded-circle">
                        <iconify-icon icon="solar:check-circle-bold-duotone" class="fs-32 text-success avatar-title"></iconify-icon>
                    </div>
                </div>
                <div class="card-body" style="z-index: 1">
                    <p class="text-muted text-uppercase fw-semibold fs-13 mb-2">Active Categories</p>
                    <h3 class="mb-3 fw-bold">{{ number_format($stats['active'] ?? 0) }}</h3>
                    <div class="d-flex align-items-center gap-2">
                        <span class="badge bg-success-subtle text-success">
                            <i class="mdi mdi-eye"></i> Visible
                        </span>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card card-animate overflow-hidden">
                <div class="position-absolute end-0 top-0 p-3">
                    <div class="avatar-md bg-danger-subtle rounded-circle">
                        <iconify-icon icon="solar:close-circle-bold-duotone" class="fs-32 text-danger avatar-title"></iconify-icon>
                    </div>
                </div>
                <div class="card-body" style="z-index: 1">
                    <p class="text-muted text-uppercase fw-semibold fs-13 mb-2">Inactive Categories</p>
                    <h3 class="mb-3 fw-bold">{{ number_format($stats['inactive'] ?? 0) }}</h3>
                    <div class="d-flex align-items-center gap-2">
                        <span class="badge bg-danger-subtle text-danger">
                            <i class="mdi mdi-eye-off"></i> Hidden
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Categories Grid --}}
    <div class="row">
        <div class="col-xl-12">
            <div class="card">
                <div class="card-header border-bottom">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="card-title mb-0 fw-semibold">All Categories</h5>
                    </div>
                </div>

                <div class="card-body">
                    {{-- Filters --}}
                    <form method="GET" action="{{ route('categories.index') }}" class="mb-4">
                        <div class="row g-3 align-items-end">
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Search Categories</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-end-0">
                                        <i class="mdi mdi-magnify"></i>
                                    </span>
                                    <input type="text" name="search" class="form-control border-start-0"
                                        placeholder="Search by category name..."
                                        value="{{ $filters['search'] ?? '' }}">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label fw-semibold">Status Filter</label>
                                <select name="status" class="form-select">
                                    <option value="">All Status</option>
                                    <option value="active" {{ ($filters['status'] ?? '') == 'active' ? 'selected' : '' }}>Active Only</option>
                                    <option value="inactive" {{ ($filters['status'] ?? '') == 'inactive' ? 'selected' : '' }}>Inactive Only</option>
                                </select>
                            </div>
                            <div class="col-md-3 d-flex gap-2">
                                <button type="submit" class="btn btn-primary flex-fill">
                                    <i class="mdi mdi-filter-variant me-1"></i> Apply
                                </button>
                                <a href="{{ route('categories.index') }}" class="btn btn-soft-secondary">
                                    <i class="mdi mdi-refresh"></i>
                                </a>
                            </div>
                        </div>
                    </form>

                    {{-- Categories Grid --}}
                    <div class="row g-4">
                        @forelse($categories as $category)
                            <div class="col-md-6 col-xl-4 col-xxl-3">
                                <div class="card mb-0 category-card h-100 border shadow-sm hover-shadow-lg transition">
                                    <div class="card-body text-center">
                                        <div class="position-relative mb-3">
                                            @if(!empty($category['image']))
                                                <div class="rounded-3 bg-light d-flex align-items-center justify-content-center mx-auto overflow-hidden" 
                                                     style="height: 180px; width: 100%;">
                                                    <img src="{{ $category['image'] }}" alt="{{ $category['name'] ?? '' }}" 
                                                         class="img-fluid category-image" 
                                                         style="max-height: 100%; max-width: 100%; object-fit: cover; transition: transform 0.3s ease;">
                                                </div>
                                            @else
                                                <div class="rounded-3 bg-gradient bg-primary bg-opacity-10 d-flex align-items-center justify-content-center mx-auto" 
                                                     style="height: 180px;">
                                                    <iconify-icon icon="solar:layer-bold-duotone" class="fs-48 text-primary opacity-75"></iconify-icon>
                                                </div>
                                            @endif

                                            <div class="position-absolute top-0 end-0 m-2">
                                                @if($category['is_active'] ?? false)
                                                    <span class="badge bg-success shadow-sm">
                                                        <i class="mdi mdi-check-circle"></i> Active
                                                    </span>
                                                @else
                                                    <span class="badge bg-danger shadow-sm">
                                                        <i class="mdi mdi-close-circle"></i> Inactive
                                                    </span>
                                                @endif
                                            </div>
                                        </div>

                                        <h5 class="mt-3 mb-2 fw-bold text-dark">{{ $category['name'] ?? 'N/A' }}</h5>
                                        @if(!empty($category['description']))
                                            <p class="text-muted fs-13 mb-3">{{ Str::limit($category['description'], 70) }}</p>
                                        @else
                                            <p class="text-muted fs-13 mb-3">No description available</p>
                                        @endif

                                        {{-- Services Count Badge --}}
                                        <div class="mb-3">
                                            <span class="badge bg-primary-subtle text-primary">
                                                <i class="mdi mdi-apps me-1"></i>
                                                {{ $category['services_count'] ?? 0 }} Service(s)
                                            </span>
                                        </div>

                                        <div class="d-flex justify-content-center gap-2 mt-3 pt-3 border-top">
                                            <a href="{{ route('categories.edit', $category['id']) }}" 
                                               class="btn btn-soft-primary btn-sm" 
                                               data-bs-toggle="tooltip" 
                                               title="Edit Category">
                                                <iconify-icon icon="solar:pen-2-broken" class="align-middle fs-18"></iconify-icon>
                                            </a>
                                            <button onclick="toggleStatus({{ $category['id'] }})" 
                                                    class="btn btn-soft-warning btn-sm"
                                                    data-bs-toggle="tooltip" 
                                                    title="Toggle Status">
                                                <iconify-icon icon="solar:power-bold-duotone" class="align-middle fs-18"></iconify-icon>
                                            </button>
                                            <button onclick="deleteCategory({{ $category['id'] }}, '{{ addslashes($category['name'] ?? 'this category') }}', {{ $category['services_count'] ?? 0 }})" 
                                                    class="btn btn-soft-danger btn-sm"
                                                    data-bs-toggle="tooltip" 
                                                    title="Delete Category">
                                                <iconify-icon icon="solar:trash-bin-minimalistic-2-broken" class="align-middle fs-18"></iconify-icon>
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
                                    <h5 class="text-muted">No Categories Found</h5>
                                    <p class="text-muted mb-3">Start organizing your services by creating your first category</p>
                                    <a href="{{ route('categories.create') }}" class="btn btn-primary">
                                        <i class="mdi mdi-plus-circle me-1"></i> Create Your First Category
                                    </a>
                                </div>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('script')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    
    <style>
        .category-card {
            transition: all 0.3s ease;
        }
        .category-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 25px rgba(0,0,0,0.1) !important;
        }
        .category-card:hover .category-image {
            transform: scale(1.1);
        }
        .hover-shadow-lg:hover {
            box-shadow: 0 1rem 3rem rgba(0,0,0,.175) !important;
        }
    </style>
    
    <script>
        // Initialize tooltips
        document.addEventListener('DOMContentLoaded', function() {
            var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
            var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
                return new bootstrap.Tooltip(tooltipTriggerEl)
            })
        });

        function toggleStatus(categoryId) {
            // First attempt to toggle
            fetch(`/categories/${categoryId}/toggle-status`, {
                method: 'POST',
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
                        title: 'Updated!',
                        text: data.message,
                        showConfirmButton: false,
                        timer: 2000
                    }).then(() => location.reload());
                } else if (data.warning) {
                    // Show warning and ask for confirmation
                    Swal.fire({
                        title: 'Warning!',
                        html: `<p>${data.message}</p>
                               <div class="alert alert-warning mt-3">
                                   <i class="mdi mdi-alert me-1"></i>
                                   <strong>${data.services_count} service(s)</strong> will still be accessible but the category will be hidden.
                               </div>`,
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#f59e0b',
                        cancelButtonColor: '#6c757d',
                        confirmButtonText: 'Yes, Deactivate Anyway',
                        cancelButtonText: 'Cancel'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            // Force toggle by updating directly
                            fetch(`/categories/${categoryId}`, {
                                method: 'PUT',
                                headers: {
                                    'Content-Type': 'application/json',
                                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                                },
                                body: JSON.stringify({ force_toggle: true })
                            })
                            .then(response => response.json())
                            .then(data => {
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Deactivated!',
                                    text: 'Category has been deactivated.',
                                    showConfirmButton: false,
                                    timer: 1500
                                }).then(() => location.reload());
                            });
                        }
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Cannot Toggle!',
                        text: data.message
                    });
                }
            })
            .catch(error => {
                console.error('Error:', error);
                Swal.fire('Error!', 'Something went wrong!', 'error');
            });
        }

        function deleteCategory(categoryId, categoryName, servicesCount) {
            // Build warning message based on services count
            let warningHtml = `<p class="mb-2">Are you sure you want to delete the category <strong>"${categoryName}"</strong>?</p>`;
            
            if (servicesCount > 0) {
                warningHtml += `
                    <div class="alert alert-danger mt-3 mb-2">
                        <i class="mdi mdi-alert-circle-outline me-1"></i>
                        <strong>Warning:</strong> This category has <strong>${servicesCount} service(s)</strong> assigned to it.
                    </div>
                    <p class="text-muted small">Deleting this category may affect all associated services. Please reassign services to another category before deletion.</p>
                `;
            } else {
                warningHtml += `<p class="text-muted small">This action cannot be undone.</p>`;
            }

            Swal.fire({
                title: 'Delete Category?',
                html: warningHtml,
                icon: servicesCount > 0 ? 'error' : 'warning',
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
                        text: 'Please wait while we delete the category',
                        allowOutsideClick: false,
                        allowEscapeKey: false,
                        showConfirmButton: false,
                        willOpen: () => {
                            Swal.showLoading();
                        }
                    });

                    fetch(`/categories/${categoryId}`, {
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
                                text: data.message || 'Category has been deleted successfully.',
                                showConfirmButton: false,
                                timer: 1500
                            }).then(() => {
                                window.location.reload();
                            });
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Error!',
                                text: data.message || 'Failed to delete category.',
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
