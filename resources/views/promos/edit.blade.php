@extends('layouts.vertical', ['title' => 'Edit Promo Code'])

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
                        <h4 class="card-title mb-0 fw-semibold">Edit Promo Code</h4>
                        <p class="text-muted mb-0 small">Update promotional discount code settings</p>
                    </div>
                </div>
            </div>

            <form action="{{ route('promos.update', $promoCode->id) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="card-body">
                    
                    <!-- Basic Information -->
                    <div class="mb-4">
                        <h6 class="text-uppercase text-muted fw-semibold mb-3">
                            <i class="mdi mdi-information-outline me-1"></i> Basic Information
                        </h6>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Promo Code <span class="text-danger">*</span></label>
                                <input type="text" name="code" class="form-control @error('code') is-invalid @enderror" 
                                       placeholder="e.g., SUMMER25" 
                                       value="{{ old('code', $promoCode->code) }}" 
                                       style="text-transform: uppercase;"
                                       required>
                                <div class="form-text">Unique code customers will enter. Auto-converted to uppercase.</div>
                                @error('code')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Description</label>
                                <input type="text" name="description" class="form-control @error('description') is-invalid @enderror" 
                                       placeholder="e.g., Summer Sale 2025" 
                                       value="{{ old('description', $promoCode->description) }}">
                                <div class="form-text">Internal description for your reference</div>
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
                            <i class="mdi mdi-percent-outline me-1"></i> Discount Settings
                        </h6>
                        <div class="row g-3">
                            <div class="col-md-4">
                                <label class="form-label fw-semibold">Discount Type <span class="text-danger">*</span></label>
                                <select name="discount_type" id="discountType" class="form-select @error('discount_type') is-invalid @enderror" required>
                                    <option value="percentage" {{ old('discount_type', $promoCode->discount_type) == 'percentage' ? 'selected' : '' }}>Percentage (%)</option>
                                    <option value="fixed" {{ old('discount_type', $promoCode->discount_type) == 'fixed' ? 'selected' : '' }}>Fixed Amount (SAR)</option>
                                </select>
                                @error('discount_type')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-semibold">Discount Value <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <input type="number" name="discount_value" id="discountValue" 
                                           class="form-control @error('discount_value') is-invalid @enderror" 
                                           placeholder="20" 
                                           value="{{ old('discount_value', $promoCode->discount_value) }}" 
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
                                <label class="form-label fw-semibold">Max Discount Cap (SAR)</label>
                                <input type="number" name="max_discount_amount" class="form-control @error('max_discount_amount') is-invalid @enderror" 
                                       placeholder="100" 
                                       value="{{ old('max_discount_amount', $promoCode->max_discount_amount) }}" 
                                       step="0.01" 
                                       min="0">
                                <div class="form-text">Maximum discount for percentage type</div>
                                @error('max_discount_amount')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Minimum Booking Amount (SAR)</label>
                                <input type="number" name="min_booking_amount" class="form-control @error('min_booking_amount') is-invalid @enderror" 
                                       placeholder="250" 
                                       value="{{ old('min_booking_amount', $promoCode->min_booking_amount) }}" 
                                       step="0.01" 
                                       min="0">
                                <div class="form-text">Minimum order value required to use this promo</div>
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
                            <i class="mdi mdi-calendar-range me-1"></i> Validity Period
                        </h6>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Valid From <span class="text-danger">*</span></label>
                                <input type="date" name="valid_from" class="form-control @error('valid_from') is-invalid @enderror" 
                                       value="{{ old('valid_from', $promoCode->valid_from?->format('Y-m-d')) }}" 
                                       required>
                                @error('valid_from')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Valid Until <span class="text-danger">*</span></label>
                                <input type="date" name="valid_until" class="form-control @error('valid_until') is-invalid @enderror" 
                                       value="{{ old('valid_until', $promoCode->valid_until?->format('Y-m-d')) }}" 
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
                            <i class="mdi mdi-lock-outline me-1"></i> Usage Limits
                        </h6>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Total Usage Limit</label>
                                <input type="number" name="usage_limit" class="form-control @error('usage_limit') is-invalid @enderror" 
                                       placeholder="1000 (leave empty for unlimited)" 
                                       value="{{ old('usage_limit', $promoCode->usage_limit) }}" 
                                       min="1">
                                <div class="form-text">Maximum total times this code can be used</div>
                                @error('usage_limit')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Usage Limit Per User</label>
                                <input type="number" name="usage_limit_per_user" class="form-control @error('usage_limit_per_user') is-invalid @enderror" 
                                       placeholder="1 (leave empty for unlimited)" 
                                       value="{{ old('usage_limit_per_user', $promoCode->usage_limit_per_user ?? 1) }}" 
                                       min="1">
                                <div class="form-text">How many times each user can use this code</div>
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
                            <i class="mdi mdi-target me-1"></i> Applicable Scope
                        </h6>
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Apply To</label>
                            <select name="applicable_to" id="applicableTo" class="form-select">
                                <option value="all" {{ old('applicable_to', $promoCode->applicable_to ?? 'all') == 'all' ? 'selected' : '' }}>All Services</option>
                                <option value="specific_categories" {{ old('applicable_to', $promoCode->applicable_to ?? 'all') == 'specific_categories' ? 'selected' : '' }}>Specific Categories</option>
                                <option value="specific_services" {{ old('applicable_to', $promoCode->applicable_to ?? 'all') == 'specific_services' ? 'selected' : '' }}>Specific Services</option>
                            </select>
                        </div>

                        <div class="row g-3" id="scopeSelectors" style="display: none;">
                            <div class="col-md-6" id="categoriesGroup">
                                <label class="form-label fw-semibold">Select Categories</label>
                                <select name="applicable_categories[]" class="form-select" multiple size="8">
                                    @foreach($categories as $category)
                                        <option value="{{ $category->id }}" 
                                                {{ in_array($category->id, old('applicable_categories', $promoCode->applicable_categories ?? [])) ? 'selected' : '' }}>
                                            {{ $category->name_en }} ({{ $category->name_ar }})
                                        </option>
                                    @endforeach
                                </select>
                                <div class="form-text">Hold Ctrl/Cmd to select multiple</div>
                            </div>
                            <div class="col-md-6" id="servicesGroup">
                                <label class="form-label fw-semibold">Select Services</label>
                                <select name="applicable_services[]" class="form-select" multiple size="8">
                                    @foreach($services as $service)
                                        <option value="{{ $service->id }}" 
                                                {{ in_array($service->id, old('applicable_services', $promoCode->applicable_services ?? [])) ? 'selected' : '' }}>
                                            {{ $service->name_en }}
                                        </option>
                                    @endforeach
                                </select>
                                <div class="form-text">Hold Ctrl/Cmd to select multiple</div>
                            </div>
                        </div>
                    </div>

                    <hr class="my-4">

                    <!-- Status -->
                    <div class="mb-4">
                        <h6 class="text-uppercase text-muted fw-semibold mb-3">
                            <i class="mdi mdi-toggle-switch-outline me-1"></i> Status
                        </h6>
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" name="is_active" id="isActive" value="1" {{ old('is_active', $promoCode->is_active) ? 'checked' : '' }}>
                            <label class="form-check-label" for="isActive">
                                <span class="fw-semibold">Active</span>
                                <span class="text-muted d-block small">Enable this promo code immediately</span>
                            </label>
                        </div>
                    </div>

                </div>

                <div class="card-footer border-top d-flex justify-content-between">
                    <a href="{{ route('promos.index') }}" class="btn btn-light">
                        <i class="mdi mdi-arrow-left me-1"></i> Cancel
                    </a>
                    <button type="submit" class="btn btn-primary">
                        <i class="mdi mdi-content-save me-1"></i> Update Promo Code
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection

@section('scripts')
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
    } else if (this.value === 'specific_categories') {
        scopeSelectors.style.display = 'block';
        categoriesGroup.style.display = 'block';
        servicesGroup.style.display = 'none';
    } else if (this.value === 'specific_services') {
        scopeSelectors.style.display = 'block';
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
