@extends('layouts.vertical', ['title' => 'Admin Dashboard'])
@section('css')
    @vite(['node_modules/flatpickr/dist/flatpickr.min.css'])
@endsection
@section('content')
    {{-- Page Header --}}
    <div class="row mb-3">
        <div class="col-12">
            <div class="page-title-box">
                <h4 class="page-title">{{ __('dashboard.admin_title') ?? 'Admin Dashboard' }}</h4>
                <p class="text-muted">{{ __('dashboard.admin_welcome') ?? 'Welcome to the admin dashboard' }}</p>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-xxl-5">
            <div class="row">
                {{-- Gross Revenue Card --}}
                <div class="col-md-6">
                    <div class="card overflow-hidden">
                        <div class="card-body">
                            <div class="row">
                                <div class="col-6">
                                    <div class="avatar-md bg-soft-primary rounded d-flex align-items-center justify-content-center">
                                        <iconify-icon icon="solar:bag-4-bold-duotone" class="avatar-title fs-32 text-primary"></iconify-icon>
                                    </div>
                                </div>
                                <div class="col-6 text-end">
                                    <p class="text-muted mb-0">{{ __('dashboard.revenue') ?? 'Revenue' }}</p>
                                    <h3 class="text-dark mt-1 mb-0" id="total-revenue">0</h3>
                                    <small class="text-muted">{{ __('dashboard.sar') ?? 'SAR' }}</small>
                                </div>
                            </div>
                        </div>
                        <div class="card-footer py-2 bg-light bg-opacity-50">
                            <div class="d-flex align-items-center justify-content-between">
                                <div>
                                    <span class="text-success revenue-change">
                                        <i class="bx bxs-up-arrow fs-12"></i> 0%
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Total Bookings Card --}}
                <div class="col-md-6">
                    <div class="card overflow-hidden">
                        <div class="card-body">
                            <div class="row">
                                <div class="col-6">
                                    <div class="avatar-md bg-soft-primary rounded d-flex align-items-center justify-content-center">
                                        <iconify-icon icon="solar:calendar-minimalistic-bold-duotone" class="avatar-title fs-32 text-primary"></iconify-icon>
                                    </div>
                                </div>
                                <div class="col-6 text-end">
                                    <p class="text-muted mb-0">{{ __('dashboard.bookings') ?? 'Bookings' }}</p>
                                    <h3 class="text-dark mt-1 mb-0" id="total-bookings">0</h3>
                                    <small class="text-muted">{{ __('dashboard.total') ?? 'Total' }}</small>
                                </div>
                            </div>
                        </div>
                        <div class="card-footer py-2 bg-light bg-opacity-50">
                            <div class="d-flex align-items-center justify-content-between">
                                <div>
                                    <span class="text-success bookings-change">
                                        <i class="bx bxs-up-arrow fs-12"></i> 0%
                                    </span>
                                </div>
                                <a href="{{ route('bookings.index') }}" class="text-reset fw-semibold fs-12">{{ __('dashboard.view_all') ?? 'View All' }}</a>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Total Clients Card --}}
                <div class="col-md-6">
                    <div class="card overflow-hidden">
                        <div class="card-body">
                            <div class="row">
                                <div class="col-6">
                                    <div class="avatar-md bg-soft-success rounded d-flex align-items-center justify-content-center">
                                        <iconify-icon icon="solar:users-group-rounded-bold-duotone" class="avatar-title fs-32 text-success"></iconify-icon>
                                    </div>
                                </div>
                                <div class="col-6 text-end">
                                    <p class="text-muted mb-0">{{ __('dashboard.clients') ?? 'Clients' }}</p>
                                    <h3 class="text-dark mt-1 mb-0" id="total-clients">0</h3>
                                    <small class="text-muted">{{ __('dashboard.total') ?? 'Total' }}</small>
                                </div>
                            </div>
                        </div>
                        <div class="card-footer py-2 bg-light bg-opacity-50">
                            <div class="d-flex align-items-center justify-content-between">
                                <div>
                                    <span class="text-success clients-change">
                                        <i class="bx bxs-up-arrow fs-12"></i> 0%
                                    </span>
                                </div>
                                <a href="/clients/list" class="text-reset fw-semibold fs-12">{{ __('dashboard.view_all') ?? 'View All' }}</a>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Total Providers Card --}}
                <div class="col-md-6">
                    <div class="card overflow-hidden">
                        <div class="card-body">
                            <div class="row">
                                <div class="col-6">
                                    <div class="avatar-md bg-soft-info rounded d-flex align-items-center justify-content-center">
                                        <iconify-icon icon="solar:shop-bold-duotone" class="avatar-title fs-32 text-info"></iconify-icon>
                                    </div>
                                </div>
                                <div class="col-6 text-end">
                                    <p class="text-muted mb-0">{{ __('dashboard.providers') ?? 'Providers' }}</p>
                                    <h3 class="text-dark mt-1 mb-0" id="total-providers">0</h3>
                                    <small class="text-muted">{{ __('dashboard.total') ?? 'Total' }}</small>
                                </div>
                            </div>
                        </div>
                        <div class="card-footer py-2 bg-light bg-opacity-50">
                            <div class="d-flex align-items-center justify-content-between">
                                <div>
                                    <span class="text-success providers-change">
                                        <i class="bx bxs-up-arrow fs-12"></i> 0%
                                    </span>
                                </div>
                                <a href="/provider/list" class="text-reset fw-semibold fs-12">{{ __('dashboard.view_all') ?? 'View All' }}</a>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Pending Bookings Card --}}
                <div class="col-md-6">
                    <div class="card overflow-hidden">
                        <div class="card-body">
                            <div class="row">
                                <div class="col-6">
                                    <div class="avatar-md bg-soft-warning rounded d-flex align-items-center justify-content-center">
                                        <iconify-icon icon="solar:hourglass-bold-duotone" class="avatar-title fs-32 text-warning"></iconify-icon>
                                    </div>
                                </div>
                                <div class="col-6 text-end">
                                    <p class="text-muted mb-0">{{ __('dashboard.pending') ?? 'Pending' }}</p>
                                    <h3 class="text-dark mt-1 mb-0" id="pending-bookings">0</h3>
                                    <small class="text-muted">{{ __('dashboard.bookings') ?? 'Bookings' }}</small>
                                </div>
                            </div>
                        </div>
                        <div class="card-footer py-2 bg-light bg-opacity-50">
                            <div class="d-flex align-items-center justify-content-between">
                                <div>
                                    <span class="text-warning">
                                        <i class="bx bx-time fs-12"></i> {{ __('dashboard.needs_attention') ?? 'Needs Attention' }}
                                    </span>
                                </div>
                                <a href="/bookings/list" class="text-reset fw-semibold fs-12">{{ __('dashboard.view_all') ?? 'View All' }}</a>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Active Providers Card --}}
                <div class="col-md-6">
                    <div class="card overflow-hidden">
                        <div class="card-body">
                            <div class="row">
                                <div class="col-6">
                                    <div class="avatar-md bg-soft-success rounded d-flex align-items-center justify-content-center">
                                        <iconify-icon icon="solar:graph-up-bold-duotone" class="avatar-title fs-32 text-success"></iconify-icon>
                                    </div>
                                </div>
                                <div class="col-6 text-end">
                                    <p class="text-muted mb-0">{{ __('dashboard.active') ?? 'Active' }}</p>
                                    <h3 class="text-dark mt-1 mb-0" id="active-providers">0</h3>
                                    <small class="text-muted">{{ __('dashboard.providers') ?? 'Providers' }}</small>
                                </div>
                            </div>
                        </div>
                        <div class="card-footer py-2 bg-light bg-opacity-50">
                            <div class="d-flex align-items-center justify-content-between">
                                <div>
                                    <span class="text-success active-providers-percentage">
                                        <i class="bx bxs-up-arrow fs-12"></i> 0%
                                    </span>
                                </div>
                                <a href="/provider/list" class="text-reset fw-semibold fs-12">{{ __('dashboard.view_all') ?? 'View All' }}</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div> <!-- end row -->
        </div> <!-- end col -->

        <div class="col-xxl-7">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h4 class="card-title mb-1">📊 {{ __('dashboard.revenue_analytics') ?? 'Revenue Analytics' }}</h4>
                            <p class="text-muted small mb-0">{{ __('dashboard.revenue_description') ?? 'Track revenue trends over time' }}</p>
                        </div>
                        <div>
                            <select class="form-select form-select-sm" id="revenue-period">
                                <option value="week">{{ __('dashboard.this_week') ?? 'This Week' }}</option>
                                <option value="month" selected>{{ __('dashboard.this_month') ?? 'This Month' }}</option>
                                <option value="year">{{ __('dashboard.this_year') ?? 'This Year' }}</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div dir="ltr">
                        <div id="revenue-chart" class="apex-charts"></div>
                    </div>
                </div>
            </div>
        </div>
    </div> <!-- end row -->

    <div class="row mt-4">
        <div class="col-lg-6">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="card-title mb-0">📅 {{ __('dashboard.bookings_overview') ?? 'Bookings Overview' }}</h5>
                        <div>
                            <select class="form-select form-select-sm" id="bookings-period">
                                <option value="week">{{ __('dashboard.this_week') ?? 'This Week' }}</option>
                                <option value="month" selected>{{ __('dashboard.this_month') ?? 'This Month' }}</option>
                                <option value="year">{{ __('dashboard.this_year') ?? 'This Year' }}</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div id="bookings-chart" class="apex-charts"></div>
                </div>
            </div>
        </div>

        <div class="col-lg-6">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">🏆 {{ __('dashboard.top_providers') ?? 'Top Providers' }}</h5>
                </div>
                <div class="card-body">
                    <div id="top-providers-list">
                        <div class="text-center py-5">
                            <div class="spinner-border text-primary" role="status">
                                <span class="visually-hidden">Loading...</span>
                            </div>
                            <p class="mt-2 text-muted">{{ __('dashboard.loading_providers') ?? 'Loading top providers...' }}</p>
                        </div>
                    </div>
                </div>
                <div class="card-footer">
                    <a href="/provider/list" class="btn btn-sm btn-primary">{{ __('dashboard.view_all_providers') ?? 'View All Providers' }}</a>
                </div>
            </div>
        </div>
    </div> <!-- end row -->

    <div class="row mt-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">🔔 {{ __('dashboard.recent_activities') ?? 'Recent Activities' }}</h5>
                </div>
                <div class="card-body">
                    <div id="recent-activities-list">
                        <div class="text-center py-5">
                            <div class="spinner-border text-primary" role="status">
                                <span class="visually-hidden">Loading...</span>
                            </div>
                            <p class="mt-2 text-muted">{{ __('dashboard.loading_activities') ?? 'Loading recent activities...' }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div> <!-- end row -->

@endsection

@section('script')
    @vite(['resources/js/pages/dashboard-admin.js'])
@endsection

@section('script-bottom')
    @vite([
        'resources/js/components/form-flatepicker.js'
    ])
@endsection
