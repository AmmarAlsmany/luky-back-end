@extends('layouts.vertical', ['title' => __('common.dashboard')])
@section('css')
    @vite(['node_modules/flatpickr/dist/flatpickr.min.css'])
@endsection
@section('content')
    {{-- Page Header --}}
    <div class="row mb-3">
        <div class="col-12">
            <div class="page-title-box">
                <h4 class="page-title">{{ __('dashboard.title') }}</h4>
                <p class="text-muted">{{ __('dashboard.welcome') }}</p>
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
                        <p class="text-muted mb-0">{{ __('dashboard.revenue') }}</p>
                        <h3 class="text-dark mt-1 mb-0">
                            {{ number_format($overview['total_revenue'] ?? 0, 2) }}
                        </h3>
                        <small class="text-muted">{{ __('dashboard.sar') }}</small>
                        </div>
                    </div>
                    </div>
                    <div class="card-footer py-2 bg-light bg-opacity-50">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                        @if(isset($comparisons['revenue_change']))
                        <span class="text-{{ str_contains($comparisons['revenue_change'], '+') ? 'success' : 'danger' }}">
                            <i class="bx bxs-{{ str_contains($comparisons['revenue_change'], '+') ? 'up' : 'down' }}-arrow fs-12"></i>
                            {{ $comparisons['revenue_change'] }}
                        </span>
                        @endif
                        </div>
                    </div>
                    </div>
                </div>
                </div>

                {{-- Commission Earned Card --}}
                <div class="col-md-6">
                <div class="card overflow-hidden">
                    <div class="card-body">
                    <div class="row">
                        <div class="col-6">
                        <div class="avatar-md bg-soft-primary rounded d-flex align-items-center justify-content-center">
                            <iconify-icon icon="solar:wallet-money-bold-duotone" class="avatar-title fs-32 text-primary"></iconify-icon>
                        </div>
                        </div>
                        <div class="col-6 text-end">
                        <p class="text-muted mb-0">{{ __('dashboard.commission') }}</p>
                        <h3 class="text-dark mt-1 mb-0">
                            {{ number_format($overview['commission_earned'] ?? 0, 2) }}
                        </h3>
                        <small class="text-muted">{{ __('dashboard.sar') }}</small>
                        </div>
                    </div>
                    </div>
                    <div class="card-footer py-2 bg-light bg-opacity-50">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                        @if(isset($comparisons['commission_change']))
                        <span class="text-{{ str_contains($comparisons['commission_change'], '+') ? 'success' : 'danger' }}">
                            <i class="bx bxs-{{ str_contains($comparisons['commission_change'], '+') ? 'up' : 'down' }}-arrow fs-12"></i>
                            {{ $comparisons['commission_change'] }}
                        </span>
                        @endif
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
                        <p class="text-muted mb-0">{{ __('dashboard.bookings') }}</p>
                        <h3 class="text-dark mt-1 mb-0">
                            {{ number_format($overview['total_bookings'] ?? 0) }}
                        </h3>
                        <small class="text-muted">{{ __('dashboard.total') }}</small>
                        </div>
                    </div>
                    </div>
                    <div class="card-footer py-2 bg-light bg-opacity-50">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                        @if(isset($comparisons['bookings_change']))
                        <span class="text-{{ str_contains($comparisons['bookings_change'], '+') ? 'success' : 'danger' }}">
                            <i class="bx bxs-{{ str_contains($comparisons['bookings_change'], '+') ? 'up' : 'down' }}-arrow fs-12"></i>
                            {{ $comparisons['bookings_change'] }}
                        </span>
                        @endif
                        </div>
                        <a href="{{ route('bookings.index') }}" class="text-reset fw-semibold fs-12">{{ __('dashboard.view_all') }}</a>
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
                        <p class="text-muted mb-0">{{ __('dashboard.clients') }}</p>
                        <h3 class="text-dark mt-1 mb-0">
                            {{ number_format($overview['total_clients'] ?? 0) }}
                        </h3>
                        <small class="text-muted">{{ __('dashboard.total') }}</small>
                        </div>
                    </div>
                    </div>
                    <div class="card-footer py-2 bg-light bg-opacity-50">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                        @if(isset($comparisons['clients_change']))
                        <span class="text-{{ str_contains($comparisons['clients_change'], '+') ? 'success' : 'danger' }}">
                            <i class="bx bxs-{{ str_contains($comparisons['clients_change'], '+') ? 'up' : 'down' }}-arrow fs-12"></i>
                            {{ $comparisons['clients_change'] }}
                        </span>
                        @endif
                        </div>
                        <a href="/clients/list" class="text-reset fw-semibold fs-12">{{ __('dashboard.view_all') }}</a>
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
                        <p class="text-muted mb-0">{{ __('dashboard.providers') }}</p>
                        <h3 class="text-dark mt-1 mb-0">
                            {{ number_format($overview['total_providers'] ?? 0) }}
                        </h3>
                        <small class="text-muted">{{ __('dashboard.total') }}</small>
                        </div>
                    </div>
                    </div>
                    <div class="card-footer py-2 bg-light bg-opacity-50">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                        @if(isset($comparisons['providers_change']))
                        <span class="text-{{ str_contains($comparisons['providers_change'], '+') ? 'success' : 'danger' }}">
                            <i class="bx bxs-{{ str_contains($comparisons['providers_change'], '+') ? 'up' : 'down' }}-arrow fs-12"></i>
                            {{ $comparisons['providers_change'] }}
                        </span>
                        @endif
                        </div>
                        <a href="/provider/list" class="text-reset fw-semibold fs-12">{{ __('dashboard.view_all') }}</a>
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
                        <div class="avatar-md bg-soft-warning rounded d-flex align-items-center justify-content-center">
                            <iconify-icon icon="solar:graph-up-bold-duotone" class="avatar-title fs-32 text-warning"></iconify-icon>
                        </div>
                        </div>
                        <div class="col-6 text-end">
                        <p class="text-muted mb-0">{{ __('dashboard.active') }}</p>
                        <h3 class="text-dark mt-1 mb-0">
                            {{ number_format($overview['active_providers'] ?? 0) }}
                        </h3>
                        <small class="text-muted">{{ __('dashboard.providers') }}</small>
                        </div>
                    </div>
                    </div>
                    <div class="card-footer py-2 bg-light bg-opacity-50">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                        @php
                            $total = $overview['total_providers'] ?? 1;
                            $active = $overview['active_providers'] ?? 0;
                            $percentage = $total > 0 ? round(($active / $total) * 100) : 0;
                        @endphp
                        <span class="text-success">
                            <i class="bx bxs-up-arrow fs-12"></i>
                            {{ $percentage }}%
                        </span>
                        </div>
                        <a href="/provider/list" class="text-reset fw-semibold fs-12">{{ __('dashboard.view_all') }}</a>
                    </div>
                    </div>
                </div>
                </div>

                    <div class="col-12">
                        {{-- Top Providers Section --}}
                        <div class="card">
                            <div class="card-header d-flex justify-content-between align-items-center">
                                <h4 class="card-title mb-0">🏆 {{ __('dashboard.top_providers') }}</h4>
                                <a href="/provider/list" class="btn btn-sm btn-link">{{ __('dashboard.view_all_providers') }} →</a>
                            </div>
                            <div class="card-body">
                                @if(!empty($topProviders) && count($topProviders) > 0)
                                <div class="table-responsive">
                                    <table class="table table-hover align-middle mb-0">
                                        <thead class="bg-light-subtle">
                                            <tr>
                                                <th class="border-0">{{ __('dashboard.rank') }}</th>
                                                <th class="border-0">{{ __('dashboard.provider') }}</th>
                                                <th class="border-0">{{ __('dashboard.business_type') }}</th>
                                                <th class="border-0">{{ __('dashboard.city') }}</th>
                                                <th class="border-0 text-end">{{ __('dashboard.revenue_amount') }}</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach(array_slice($topProviders, 0, 5) as $index => $provider)
                                            <tr>
                                                <td>
                                                    <div class="avatar-xs bg-primary bg-opacity-10 d-flex align-items-center justify-content-center rounded">
                                                        <span class="fs-14 fw-bold text-primary">#{{ $index + 1 }}</span>
                                                    </div>
                                                </td>
                                                <td>
                                                    <h6 class="mb-0">{{ $provider['name'] ?? 'N/A' }}</h6>
                                                </td>
                                                <td>
                                                    <span class="badge bg-light text-dark">
                                                        {{ ucfirst(str_replace('_', ' ', $provider['business_type'] ?? 'N/A')) }}
                                                    </span>
                                                </td>
                                                <td>
                                                    @php
                                                        $cityNames = [
                                                            1 => 'Riyadh',
                                                            2 => 'Jeddah',
                                                            3 => 'Makkah',
                                                            4 => 'Madinah',
                                                            5 => 'Dammam',
                                                        ];
                                                    @endphp
                                                    <span class="text-muted">{{ $cityNames[$provider['city_id'] ?? 0] ?? 'Unknown' }}</span>
                                                </td>
                                                <td class="text-end">
                                                    <h6 class="mb-0 text-success">{{ __('dashboard.sar') }} {{ number_format($provider['revenue'] ?? 0, 2) }}</h6>
                                                </td>
                                            </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                                @else
                                <div class="text-center py-5">
                                    <iconify-icon icon="solar:database-bold-duotone" class="fs-48 text-muted mb-3"></iconify-icon>
                                    <p class="text-muted mb-0">{{ __('dashboard.no_provider_data') }}</p>
                                    <small class="text-muted">{{ __('dashboard.providers_appear') }}</small>
                                </div>
                                @endif
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
                            <h4 class="card-title mb-1">📊 {{ __('dashboard.provider_analytics') }}</h4>
                            <p class="text-muted small mb-0">{{ __('dashboard.compare_performance') }}</p>
                        </div>
                        <div>
                            <input type="text" id="humanfd-datepicker" class="form-control form-control-sm" placeholder="{{ __('dashboard.filter_by_date') }}" style="width: 150px;">
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div dir="ltr">
                        <div id="provider-performance-chart" class="apex-charts"></div>
                    </div>
                </div>
            </div>
        </div>
    </div> <!-- end row -->

    <div class="row mt-4">
        <div class="col-lg-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">📅 {{ __('dashboard.bookings_overview') }}</h5>
                </div>
                <div class="card-body">
                    <div id="conversions" class="apex-charts mb-2 mt-n2"></div>
                    <div class="row text-center mt-3">
                        <div class="col-6">
                            <div class="border-end">
                                <p class="text-muted mb-1 small">{{ __('dashboard.completed') }}</p>
                                <h4 class="text-success mb-0">
                                    {{ number_format($overview['completed_bookings'] ?? 0) }}
                                </h4>
                            </div>
                        </div>
                        <div class="col-6">
                            <p class="text-muted mb-1 small">{{ __('dashboard.pending') }}</p>
                            <h4 class="text-warning mb-0">
                                {{ number_format($overview['pending_bookings'] ?? 0) }}
                            </h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">🗺️ {{ __('dashboard.providers_by_region') }}</h5>
                </div>
                <div class="card-body">
                    <div id="ksa-map-markers" style="height: 260px"></div>
                    <div class="row text-center mt-3">
                        <div class="col-6">
                            <div class="border-end">
                                <p class="text-muted mb-1 small">{{ __('dashboard.total_providers') }}</p>
                                <h4 class="text-dark mb-0">
                                    {{ number_format($overview['total_providers'] ?? 0) }}
                                </h4>
                            </div>
                        </div>
                        <div class="col-6">
                            <p class="text-muted mb-1 small">{{ __('dashboard.active_now') }}</p>
                            <h4 class="text-success mb-0">
                                {{ number_format($overview['active_providers'] ?? 0) }}
                            </h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">⭐ {{ __('dashboard.top_client') }}</h5>
                </div>
                <div class="card-body">
                    <div class="text-center mb-3">
                        @if(!empty($topClient) && isset($topClient['name']))
                        <img src="{{ $topClient['avatar_url'] ?? asset('images/default-avatar-male.svg') }}"
                             alt="avatar" class="avatar-lg rounded-circle mb-2" loading="lazy">
                        <h5 class="mb-1">{{ $topClient['name'] }}</h5>
                        <p class="text-muted small mb-0">{{ __('dashboard.most_valuable_client') }}</p>
                        @else
                        <div class="avatar-lg bg-light d-flex align-items-center justify-content-center rounded-circle mx-auto mb-2">
                            <iconify-icon icon="solar:user-bold-duotone" class="fs-32 text-muted"></iconify-icon>
                        </div>
                        <h6 class="text-muted mb-0">{{ __('dashboard.no_client_data') }}</h6>
                        <p class="text-muted small">{{ __('dashboard.top_client_appears') }}</p>
                        @endif
                    </div>
                    
                    @if(!empty($topClient) && isset($topClient['name']))
                    <div class="text-center mb-3">
                        <h3 class="text-success mb-0">
                            {{ number_format($topClient['total_spent'] ?? 0, 2) }}
                        </h3>
                        <small class="text-muted">{{ __('dashboard.sar') }} - {{ __('dashboard.total_spending') }}</small>
                    </div>
                    <div id="clientofmonth" class="apex-charts"></div>
                    @endif
                </div>
            </div>
        </div>
    </div> <!-- end row -->

@endsection

@section('script')
    <script>
        // Pass PHP data to JavaScript
        window.dashboardData = {
            overview: @json($overview ?? []),
            comparisons: @json($comparisons ?? []),
            topProviders: @json($topProviders ?? []),
            topClient: @json($topClient ?? []),
            providersByRegion: @json($providersByRegion ?? [])
        };
    </script>
    @vite(['resources/js/pages/dashboard.js'])
@endsection
@section('script-bottom')
    @vite([
    'resources/js/components/form-flatepicker.js',
    'resources/js/pages/humanfd.js'
  ])
@endsection
