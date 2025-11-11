@extends('layouts.vertical', ['title' => __('promos.create_promo_code')])

@section('css')
@vite(['node_modules/flatpickr/dist/flatpickr.min.css', 'node_modules/choices.js/public/assets/styles/choices.min.css'])
@endsection

@section('content')

<div class="row">
    <div class="col-xl-12">
        <div class="card">
            <div class="card-header border-bottom">
                <div class="d-flex align-items-center">
                    <iconify-icon icon="solar:ticket-bold-duotone" class="fs-32 text-primary me-3"></iconify-icon>
                    <div>
                        <h4 class="card-title mb-0 fw-semibold">{{ __('promos.create_new_promo_code') }}</h4>
                        <p class="text-muted mb-0 small">{{ __('promos.manage_promo_codes') }}</p>
                    </div>
                </div>
            </div>

            <form action="{{ route('promos.store') }}" method="POST">
                @csrf
                
                @if ($errors->any())
                    <div class="alert alert-danger alert-dismissible fade show m-3" role="alert">
                        <h5 class="alert-heading"><i class="mdi mdi-alert-circle-outline me-1"></i> {{ __('promos.validation_errors') }}</h5>
                        <ul class="mb-0">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif
                
                <div class="card-body">
                    
                    <!-- Basic Information -->
                    <div class="mb-4">
                        <h6 class="text-uppercase text-muted fw-semibold mb-3">
                            <i class="mdi mdi-information-outline me-1"></i> {{ __('promos.basic_information') }}
                        </h6>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">{{ __('promos.promo_code_label') }} <span class="text-danger">*</span></label>
                                <input type="text" name="code" class="form-control @error('code') is-invalid @enderror" 
                                       placeholder="{{ __('promos.promo_code_placeholder') }}" 
                                       value="{{ old('code') }}" 
                                       style="text-transform: uppercase;"
                                       required>
                                <div class="form-text">{{ __('promos.promo_code_help') }}</div>
                                @error('code')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">{{ __('promos.description') }}</label>
                                <input type="text" name="description" class="form-control @error('description') is-invalid @enderror" 
                                       placeholder="{{ __('promos.description_placeholder') }}" 
                                       value="{{ old('description') }}">
                                <div class="form-text">{{ __('promos.description_help') }}</div>
                                @error('description')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <hr class="my-4">

                    <!-- Discount Settings -->
                    <div class="mb-4">
                        <h6 class="text-uppercase text-muted fw-semibold mb-3">
                            <i class="mdi mdi-percent-outline me-1"></i> {{ __('promos.discount_settings') }}
                        </h6>
                        <div class="row g-3">
                            <div class="col-md-4">
                                <label class="form-label fw-semibold">{{ __('promos.discount_type') }} <span class="text-danger">*</span></label>
                                <select name="discount_type" id="discountType" class="form-select @error('discount_type') is-invalid @enderror" required>
                                    <option value="percentage" {{ old('discount_type') == 'percentage' ? 'selected' : '' }}>{{ __('promos.percentage_type') }}</option>
                                    <option value="fixed" {{ old('discount_type') == 'fixed' ? 'selected' : '' }}>{{ __('promos.fixed_type') }}</option>
                                </select>
                                @error('discount_type')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-semibold">{{ __('promos.discount_value') }} <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <input type="number" name="discount_value" id="discountValue" 
                                           class="form-control @error('discount_value') is-invalid @enderror" 
                                           placeholder="20" 
                                           value="{{ old('discount_value') }}" 
                                           step="0.01" 
                                           min="0"
                                           required>
                                    <span class="input-group-text" id="discountUnit">%</span>
                                </div>
                                @error('discount_value')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-4" id="maxDiscountGroup">
                                <label class="form-label fw-semibold">{{ __('promos.max_discount_cap') }}</label>
                                <input type="number" name="max_discount_amount" class="form-control @error('max_discount_amount') is-invalid @enderror" 
                                       placeholder="100" 
                                       value="{{ old('max_discount_amount') }}" 
                                       step="0.01" 
                                       min="0">
                                <div class="form-text">{{ __('promos.max_discount_help') }}</div>
                                @error('max_discount_amount')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">{{ __('promos.min_booking_amount') }}</label>
                                <input type="number" name="min_booking_amount" class="form-control @error('min_booking_amount') is-invalid @enderror" 
                                       placeholder="250" 
                                       value="{{ old('min_booking_amount') }}" 
                                       step="0.01" 
                                       min="0">
                                <div class="form-text">{{ __('promos.min_booking_help') }}</div>
                                @error('min_booking_amount')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <hr class="my-4">

                    <!-- Validity Period -->
                    <div class="mb-4">
                        <h6 class="text-uppercase text-muted fw-semibold mb-3">
                            <i class="mdi mdi-calendar-range me-1"></i> {{ __('promos.validity_period') }}
                        </h6>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">{{ __('promos.valid_from') }} <span class="text-danger">*</span></label>
                                <input type="date" name="valid_from" class="form-control @error('valid_from') is-invalid @enderror" 
                                       value="{{ old('valid_from') }}" 
                                       required>
                                @error('valid_from')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">{{ __('promos.valid_until') }} <span class="text-danger">*</span></label>
                                <input type="date" name="valid_until" class="form-control @error('valid_until') is-invalid @enderror" 
                                       value="{{ old('valid_until') }}" 
                                       required>
                                @error('valid_until')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <hr class="my-4">

                    <!-- Usage Limits -->
                    <div class="mb-4">
                        <h6 class="text-uppercase text-muted fw-semibold mb-3">
                            <i class="mdi mdi-lock-outline me-1"></i> {{ __('promos.usage_limits') }}
                        </h6>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">{{ __('promos.total_usage_limit') }}</label>
                                <input type="number" name="usage_limit" class="form-control @error('usage_limit') is-invalid @enderror" 
                                       placeholder="{{ __('promos.total_usage_placeholder') }}" 
                                       value="{{ old('usage_limit') }}" 
                                       min="1">
                                <div class="form-text">{{ __('promos.total_usage_help') }}</div>
                                @error('usage_limit')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">{{ __('promos.usage_limit_per_user') }}</label>
                                <input type="number" name="usage_limit_per_user" class="form-control @error('usage_limit_per_user') is-invalid @enderror" 
                                       placeholder="{{ __('promos.usage_per_user_placeholder') }}" 
                                       value="{{ old('usage_limit_per_user', 1) }}" 
                                       min="1">
                                <div class="form-text">{{ __('promos.usage_per_user_help') }}</div>
                                @error('usage_limit_per_user')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <hr class="my-4">

                    <!-- Applicable Scope -->
                    <div class="mb-4">
                        <h6 class="text-uppercase text-muted fw-semibold mb-3">
                            <i class="mdi mdi-target me-1"></i> {{ __('promos.applicable_scope') }}
                        </h6>
                        <div class="mb-3">
                            <label class="form-label fw-semibold">{{ __('promos.apply_to') }}</label>
                            <select name="applicable_to" id="applicableTo" class="form-select">
                                <option value="all" {{ old('applicable_to') == 'all' ? 'selected' : '' }}>{{ __('promos.all_services_option') }}</option>
                                <option value="specific_categories" {{ old('applicable_to') == 'specific_categories' ? 'selected' : '' }}>{{ __('promos.specific_categories') }}</option>
                                <option value="specific_services" {{ old('applicable_to') == 'specific_services' ? 'selected' : '' }}>{{ __('promos.specific_services') }}</option>
                            </select>
                        </div>

                        <div class="row g-3" id="scopeSelectors" style="display: none;">
                            <div class="col-md-6" id="categoriesGroup">
                                <label class="form-label fw-semibold">{{ __('promos.select_categories') }}</label>
                                <select name="applicable_categories[]" class="form-select" multiple size="8">
                                    @foreach($categories as $category)
                                        <option value="{{ $category->id }}" 
                                                {{ in_array($category->id, old('applicable_categories', [])) ? 'selected' : '' }}>
                                            {{ $category->name_en }} ({{ $category->name_ar }})
                                        </option>
                                    @endforeach
                                </select>
                                <div class="form-text">{{ __('promos.hold_ctrl_to_select') }}</div>
                            </div>
                            <div class="col-md-6" id="servicesGroup">
                                <label class="form-label fw-semibold">{{ __('promos.select_services') }}</label>
                                <select name="applicable_services[]" class="form-select" multiple size="8">
                                    @foreach($services as $service)
                                        <option value="{{ $service->id }}" 
                                                {{ in_array($service->id, old('applicable_services', [])) ? 'selected' : '' }}>
                                            {{ $service->name_en ?? $service->name_ar ?? 'Service #' . $service->id }}
                                        </option>
                                    @endforeach
                                </select>
                                <div class="form-text">{{ __('promos.hold_ctrl_to_select') }}</div>
                            </div>
                        </div>
                    </div>

                    <hr class="my-4">

                    <!-- Status -->
                    <div class="mb-4">
                        <h6 class="text-uppercase text-muted fw-semibold mb-3">
                            <i class="mdi mdi-toggle-switch-outline me-1"></i> {{ __('promos.status_section') }}
                        </h6>
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" name="is_active" id="isActive" value="1" checked>
                            <label class="form-check-label" for="isActive">
                                <span class="fw-semibold">{{ __('promos.active_status') }}</span>
                                <span class="text-muted d-block small">{{ __('promos.enable_promo_immediately') }}</span>
                            </label>
                        </div>
                    </div>

                </div>

                <div class="card-footer border-top d-flex justify-content-between">
                    <a href="{{ route('promos.index') }}" class="btn btn-light">
                        <i class="mdi mdi-arrow-left me-1"></i> {{ __('promos.cancel') }}
                    </a>
                    <button type="submit" class="btn btn-primary">
                        <i class="mdi mdi-check me-1"></i> {{ __('promos.create_promo_button') }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection

@section('script')
<script>
// Toggle discount unit based on type
document.getElementById('discountType').addEventListener('change', function() {
    const unit = document.getElementById('discountUnit');
    const maxDiscountGroup = document.getElementById('maxDiscountGroup');
    
    if (this.value === 'percentage') {
        unit.textContent = '%';
        maxDiscountGroup.style.display = 'block';
    } else {
        unit.textContent = 'SAR';
        maxDiscountGroup.style.display = 'none';
    }
});

// Toggle scope selectors based on applicable_to
document.getElementById('applicableTo').addEventListener('change', function() {
    const scopeSelectors = document.getElementById('scopeSelectors');
    const categoriesGroup = document.getElementById('categoriesGroup');
    const servicesGroup = document.getElementById('servicesGroup');
    
    if (this.value === 'all') {
        scopeSelectors.style.display = 'none';
        categoriesGroup.style.display = 'none';
        servicesGroup.style.display = 'none';
    } else if (this.value === 'specific_categories') {
        scopeSelectors.style.display = 'flex';
        categoriesGroup.style.display = 'block';
        servicesGroup.style.display = 'none';
    } else if (this.value === 'specific_services') {
        scopeSelectors.style.display = 'flex';
        categoriesGroup.style.display = 'none';
        servicesGroup.style.display = 'block';
    }
});

// Initialize on page load
document.addEventListener('DOMContentLoaded', function() {
    // Trigger change events to set initial state
    document.getElementById('discountType').dispatchEvent(new Event('change'));
    document.getElementById('applicableTo').dispatchEvent(new Event('change'));
});

// Auto-uppercase code input
document.querySelector('input[name="code"]').addEventListener('input', function() {
    this.value = this.value.toUpperCase();
});
</script>
@endsection
