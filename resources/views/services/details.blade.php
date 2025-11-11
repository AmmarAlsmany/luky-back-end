@extends('layouts.vertical', ['title' => __('services.service_details')])

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="d-flex align-items-center justify-content-between mb-3">
                <div>
                    <h4 class="mb-0">{{ __('services.service_details') }}</h4>
                    <p class="text-muted mb-0">{{ __('services.view_service_info') }}</p>
                </div>
                <div class="d-flex gap-2">
                    <a href="{{ route('services.edit', $service->id) }}" class="btn btn-primary">
                        <i class="mdi mdi-pencil me-1"></i> {{ __('services.edit_service') }}
                    </a>
                    <a href="{{ route('services.index') }}" class="btn btn-secondary">
                        <i class="mdi mdi-arrow-left me-1"></i> {{ __('services.back_to_services') }}
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        {{-- Left Column --}}
        <div class="col-xl-8">
            {{-- Service Information --}}
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">{{ __('services.service_information') }}</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-borderless mb-0">
                            <tbody>
                                <tr>
                                    <th style="width: 200px;">{{ __('services.service_name_label_en') }}</th>
                                    <td>{{ $service->name_en ?? __('services.n_a') }}</td>
                                </tr>
                                <tr>
                                    <th>{{ __('services.service_name_label_ar') }}</th>
                                    <td dir="rtl">{{ $service->name_ar ?? __('services.n_a') }}</td>
                                </tr>
                                <tr>
                                    <th>{{ __('services.category_label') }}</th>
                                    <td>
                                        @if($service->category)
                                            <span class="badge bg-info">
                                                {{ $service->category->name_en }} / {{ $service->category->name_ar }}
                                            </span>
                                        @else
                                            <span class="text-muted">{{ __('services.n_a') }}</span>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <th>{{ __('services.provider_label') }}</th>
                                    <td>
                                        @if($service->provider)
                                            <a href="{{ route('providers.show', $service->provider->id) }}">
                                                {{ $service->provider->business_name ?? $service->provider->user->name ?? 'N/A' }}
                                            </a>
                                        @else
                                            <span class="text-muted">{{ __('services.n_a') }}</span>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <th>{{ __('services.price_label') }}</th>
                                    <td><strong class="text-primary">{{ number_format($service->price, 2) }} SAR</strong></td>
                                </tr>
                                <tr>
                                    <th>{{ __('services.duration_label') }}</th>
                                    <td>{{ $service->duration_minutes }} {{ __('services.minutes') }}</td>
                                </tr>
                                <tr>
                                    <th>{{ __('services.available_at_home_label') }}</th>
                                    <td>
                                        @if($service->available_at_home)
                                            <span class="badge bg-success">{{ __('services.yes') }}</span>
                                            @if($service->home_service_price)
                                                <span class="ms-2">{{ number_format($service->home_service_price, 2) }} SAR</span>
                                            @endif
                                        @else
                                            <span class="badge bg-secondary">{{ __('services.no') }}</span>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <th>{{ __('services.status_label') }}</th>
                                    <td>
                                        @if($service->is_active)
                                            <span class="badge bg-success">{{ __('services.active') }}</span>
                                        @else
                                            <span class="badge bg-danger">{{ __('services.inactive') }}</span>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <th>{{ __('services.featured_label') }}</th>
                                    <td>
                                        @if($service->is_featured)
                                            <span class="badge bg-warning">{{ __('services.featured') }}</span>
                                        @else
                                            <span class="text-muted">{{ __('services.no') }}</span>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <th>{{ __('services.sort_order') }}</th>
                                    <td>{{ $service->sort_order ?? 0 }}</td>
                                </tr>
                                <tr>
                                    <th>{{ __('services.created_at') }}</th>
                                    <td>{{ $service->created_at?->format('M d, Y h:i A') ?? __('services.n_a') }}</td>
                                </tr>
                                <tr>
                                    <th>{{ __('services.updated_at') }}</th>
                                    <td>{{ $service->updated_at?->format('M d, Y h:i A') ?? __('services.n_a') }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            {{-- Description --}}
            @if($service->description_en || $service->description_ar)
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">{{ __('services.description') }}</h5>
                </div>
                <div class="card-body">
                    @if($service->description_en)
                        <h6>{{ __('services.english') }}</h6>
                        <p>{{ $service->description_en }}</p>
                    @endif

                    @if($service->description_ar)
                        <h6>{{ __('services.arabic') }}</h6>
                        <p dir="rtl">{{ $service->description_ar }}</p>
                    @endif
                </div>
            </div>
            @endif
        </div>

        {{-- Right Column --}}
        <div class="col-xl-4">
            {{-- Statistics --}}
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">{{ __('services.statistics') }}</h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <small class="text-muted">{{ __('services.total_bookings') }}</small>
                        <h4 class="mb-0">{{ $stats['total_bookings'] ?? 0 }}</h4>
                    </div>

                    <div class="mb-3">
                        <small class="text-muted">{{ __('services.total_revenue') }}</small>
                        <h4 class="mb-0 text-success">{{ number_format($stats['total_revenue'] ?? 0, 2) }} SAR</h4>
                    </div>

                    @if($service->average_rating)
                    <div class="mb-3">
                        <small class="text-muted">{{ __('services.average_rating') }}</small>
                        <h4 class="mb-0">
                            <i class="mdi mdi-star text-warning"></i> {{ number_format($service->average_rating, 1) }}
                        </h4>
                    </div>
                    @endif

                    @if($service->total_bookings)
                    <div class="mb-0">
                        <small class="text-muted">{{ __('services.total_bookings_lifetime') }}</small>
                        <h4 class="mb-0">{{ $service->total_bookings }}</h4>
                    </div>
                    @endif
                </div>
            </div>

            {{-- Actions --}}
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">{{ __('services.actions') }}</h5>
                </div>
                <div class="card-body">
                    <a href="{{ route('services.edit', $service->id) }}" class="btn btn-primary w-100 mb-2">
                        <i class="mdi mdi-pencil me-1"></i> {{ __('services.edit_service') }}
                    </a>

                    <button type="button" class="btn btn-danger w-100" onclick="deleteService({{ $service->id }})">
                        <i class="mdi mdi-delete me-1"></i> {{ __('services.delete_service') }}
                    </button>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('script')
<script>
function deleteService(serviceId) {
    if (!confirm('{{ __('services.confirm_delete') }}')) {
        return;
    }

    fetch(`/services/${serviceId}`, {
        method: 'DELETE',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'Accept': 'application/json',
            'Content-Type': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('{{ __('services.service_deleted_success') }}');
            window.location.href = '{{ route("services.index") }}';
        } else {
            alert(data.message || '{{ __('services.failed_delete_service') }}');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('{{ __('services.failed_delete_service') }}');
    });
}
</script>
@endsection
