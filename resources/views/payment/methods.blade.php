@extends('layouts.vertical', ['title' => __('payment.payment_methods')])

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header d-flex align-items-center justify-content-between">
                <h4 class="mb-0">{{ __('payment.payment_method_management') }}</h4>
                <div class="d-flex gap-2">
                    <button type="button" class="btn btn-outline-secondary btn-sm" onclick="testConnection()">
                        <i class="bx bx-test-tube me-1"></i> {{ __('payment.test_connection') }}
                    </button>
                    <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#addMethodModal">
                        <i class="bx bx-plus me-1"></i> {{ __('payment.add_payment_method') }}
                    </button>
                </div>
            </div>

            <div class="card-body">
                @if(isset($paymentMethods) && count($paymentMethods) > 0)
                    <div class="alert alert-success">
                        <i class="bx bx-check-circle me-2"></i>
                        {{ __('payment.connected_successfully', ['count' => count($paymentMethods)]) }}
                    </div>

                    <div class="row g-4">
                        @foreach($paymentMethods as $method)
                        <div class="col-md-6 col-lg-4">
                            <div class="card border h-100">
                                <div class="card-body">
                                    <div class="d-flex align-items-start justify-content-between mb-3">
                                        <div class="d-flex align-items-center gap-3">
                                            @if(!empty($method['ImageUrl']))
                                                <img src="{{ $method['ImageUrl'] }}" alt="{{ $method['PaymentMethodEn'] }}" style="height: 40px; width: auto;">
                                            @else
                                                <div class="avatar-sm">
                                                    <span class="avatar-title bg-primary-subtle text-primary rounded">
                                                        <i class="bx bx-credit-card fs-4"></i>
                                                    </span>
                                                </div>
                                            @endif
                                            <div>
                                                <h5 class="mb-0">{{ $method['PaymentMethodEn'] }}</h5>
                                                <small class="text-muted">{{ $method['PaymentMethodAr'] }}</small>
                                            </div>
                                        </div>
                                        <div class="form-check form-switch">
                                            <input class="form-check-input" type="checkbox" 
                                                   id="method_{{ $method['PaymentMethodId'] }}" 
                                                   data-method-id="{{ $method['PaymentMethodId'] }}"
                                                   {{ ($method['is_enabled'] ?? true) ? 'checked' : '' }}
                                                   onchange="toggleMethod(this)">
                                        </div>
                                    </div>

                                    <div class="mb-3">
                                        <div class="d-flex justify-content-between mb-2">
                                            <span class="text-muted small">{{ __('payment.method_id') }}</span>
                                            <span><code>{{ $method['PaymentMethodId'] }}</code></span>
                                        </div>
                                        <div class="d-flex justify-content-between mb-2">
                                            <span class="text-muted small">{{ __('payment.type') }}</span>
                                            <span class="badge bg-{{ $method['IsDirectPayment'] ? 'success' : 'info' }}-subtle text-{{ $method['IsDirectPayment'] ? 'success' : 'info' }}">
                                                {{ $method['IsDirectPayment'] ? __('payment.direct_payment') : __('payment.redirect') }}
                                            </span>
                                        </div>
                                        @if(!empty($method['ServiceCharge']))
                                        <div class="d-flex justify-content-between mb-2">
                                            <span class="text-muted small">{{ __('payment.service_charge') }}</span>
                                            <span>{{ $method['ServiceCharge'] }} {{ $method['CurrencyIso'] ?? 'SAR' }}</span>
                                        </div>
                                        @endif
                                        @if(!empty($method['TotalAmount']))
                                        <div class="d-flex justify-content-between mb-2">
                                            <span class="text-muted small">{{ __('payment.total_amount') }}</span>
                                            <span class="fw-medium">{{ $method['TotalAmount'] }} {{ $method['CurrencyIso'] ?? 'SAR' }}</span>
                                        </div>
                                        @endif
                                    </div>

                                    <div class="d-flex gap-2">
                                        <button class="btn btn-sm btn-outline-primary flex-fill" 
                                                onclick="selectPaymentMethod({{ $method['PaymentMethodId'] }}, '{{ $method['PaymentMethodEn'] }}')">
                                            <i class="bx bx-check"></i> {{ __('payment.select') }}
                                        </button>
                                        <button class="btn btn-sm btn-outline-secondary" 
                                                data-bs-toggle="tooltip" 
                                                title="{{ json_encode($method) }}">
                                            <i class="bx bx-info-circle"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                @elseif(isset($error))
                    <div class="alert alert-warning">
                        <i class="bx bx-error-circle me-2"></i>
                        <strong>{{ __('payment.unable_load_methods') }}</strong>
                        <br>
                        <small>{{ $error }}</small>
                        <br><br>
                        <small>{{ __('payment.check_api_config') }} <a href="{{ route('second', ['payment', 'settings']) }}" class="alert-link">{{ __('payment.gateway_settings') }}</a></small>
                    </div>

                    <div class="text-center py-4">
                        <button class="btn btn-primary" onclick="window.location.reload()">
                            <i class="bx bx-refresh me-1"></i> {{ __('payment.retry_loading') }}
                        </button>
                        <a href="{{ route('second', ['payment', 'settings']) }}" class="btn btn-outline-secondary">
                            <i class="bx bx-cog me-1"></i> {{ __('payment.configure_api') }}
                        </a>
                    </div>

                    <hr class="my-4">
                    <h5 class="mb-3">{{ __('payment.available_gateways') }}</h5>
                <div class="row g-4">
                    {{-- MyFatoorah Card --}}
                    <div class="col-md-6 col-lg-4">
                        <div class="card border h-100">
                            <div class="card-body">
                                <div class="d-flex align-items-start justify-content-between mb-3">
                                    <div class="d-flex align-items-center gap-3">
                                        <div class="avatar-sm">
                                            <span class="avatar-title bg-info-subtle text-info rounded">
                                                <i class="bx bx-credit-card fs-4"></i>
                                            </span>
                                        </div>
                                        <div>
                                            <h5 class="mb-0">{{ __('payment.myfatoorah') }}</h5>
                                            <small class="text-muted">{{ __('payment.payment_gateway') }}</small>
                                        </div>
                                    </div>
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" id="myfatoorahStatus" checked>
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <div class="d-flex justify-content-between mb-2">
                                        <span class="text-muted small">{{ __('payment.status') }}</span>
                                        <span class="badge bg-success-subtle text-success">{{ __('payment.active') }}</span>
                                    </div>
                                    <div class="d-flex justify-content-between mb-2">
                                        <span class="text-muted small">{{ __('payment.mode') }}</span>
                                        <span class="badge bg-warning-subtle text-warning">{{ __('payment.test') }}</span>
                                    </div>
                                    <div class="d-flex justify-content-between mb-2">
                                        <span class="text-muted small">{{ __('payment.country') }}</span>
                                        <span>{{ __('payment.saudi_arabia') }}</span>
                                    </div>
                                    <div class="d-flex justify-content-between mb-2">
                                        <span class="text-muted small">{{ __('payment.currency') }}</span>
                                        <span>SAR</span>
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <p class="small text-muted mb-2">{{ __('payment.supported_methods') }}</p>
                                    <div class="d-flex flex-wrap gap-1">
                                        <span class="badge bg-light text-dark border">MADA</span>
                                        <span class="badge bg-light text-dark border">Visa</span>
                                        <span class="badge bg-light text-dark border">Mastercard</span>
                                        <span class="badge bg-light text-dark border">Apple Pay</span>
                                        <span class="badge bg-light text-dark border">STC Pay</span>
                                    </div>
                                </div>

                                <div class="d-flex gap-2">
                                    <button class="btn btn-sm btn-outline-primary flex-fill">
                                        <i class="bx bx-edit-alt"></i> Configure
                                    </button>
                                    <button class="btn btn-sm btn-outline-secondary">
                                        <i class="bx bx-test-tube"></i> Test
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Tabby Card --}}
                    <div class="col-md-6 col-lg-4">
                        <div class="card border h-100">
                            <div class="card-body">
                                <div class="d-flex align-items-start justify-content-between mb-3">
                                    <div class="d-flex align-items-center gap-3">
                                        <div class="avatar-sm">
                                            <span class="avatar-title bg-warning-subtle text-warning rounded">
                                                <i class="bx bx-purchase-tag fs-4"></i>
                                            </span>
                                        </div>
                                        <div>
                                            <h5 class="mb-0">{{ __('payment.tabby') }}</h5>
                                            <small class="text-muted">{{ __('payment.buy_now_pay_later') }}</small>
                                        </div>
                                    </div>
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" id="tabbyStatus" checked>
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <div class="d-flex justify-content-between mb-2">
                                        <span class="text-muted small">Status</span>
                                        <span class="badge bg-success-subtle text-success">Active</span>
                                    </div>
                                    <div class="d-flex justify-content-between mb-2">
                                        <span class="text-muted small">Mode</span>
                                        <span class="badge bg-warning-subtle text-warning">Test</span>
                                    </div>
                                    <div class="d-flex justify-content-between mb-2">
                                        <span class="text-muted small">Country</span>
                                        <span>Saudi Arabia</span>
                                    </div>
                                    <div class="d-flex justify-content-between mb-2">
                                        <span class="text-muted small">Currency</span>
                                        <span>SAR</span>
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <p class="small text-muted mb-2">Payment Plans:</p>
                                    <div class="d-flex flex-wrap gap-1">
                                        <span class="badge bg-light text-dark border">Pay in 4</span>
                                        <span class="badge bg-light text-dark border">Pay Later</span>
                                        <span class="badge bg-light text-dark border">Split</span>
                                    </div>
                                </div>

                                <div class="d-flex gap-2">
                                    <button class="btn btn-sm btn-outline-primary flex-fill">
                                        <i class="bx bx-edit-alt"></i> Configure
                                    </button>
                                    <button class="btn btn-sm btn-outline-secondary">
                                        <i class="bx bx-test-tube"></i> Test
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Tamara Card --}}
                    <div class="col-md-6 col-lg-4">
                        <div class="card border h-100">
                            <div class="card-body">
                                <div class="d-flex align-items-start justify-content-between mb-3">
                                    <div class="d-flex align-items-center gap-3">
                                        <div class="avatar-sm">
                                            <span class="avatar-title bg-danger-subtle text-danger rounded">
                                                <i class="bx bx-badge fs-4"></i>
                                            </span>
                                        </div>
                                        <div>
                                            <h5 class="mb-0">Tamara</h5>
                                            <small class="text-muted">Buy Now Pay Later</small>
                                        </div>
                                    </div>
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" id="tamaraStatus" checked>
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <div class="d-flex justify-content-between mb-2">
                                        <span class="text-muted small">Status</span>
                                        <span class="badge bg-success-subtle text-success">Active</span>
                                    </div>
                                    <div class="d-flex justify-content-between mb-2">
                                        <span class="text-muted small">Mode</span>
                                        <span class="badge bg-warning-subtle text-warning">Test</span>
                                    </div>
                                    <div class="d-flex justify-content-between mb-2">
                                        <span class="text-muted small">Country</span>
                                        <span>Saudi Arabia</span>
                                    </div>
                                    <div class="d-flex justify-content-between mb-2">
                                        <span class="text-muted small">Currency</span>
                                        <span>SAR</span>
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <p class="small text-muted mb-2">Payment Plans:</p>
                                    <div class="d-flex flex-wrap gap-1">
                                        <span class="badge bg-light text-dark border">Pay in 2</span>
                                        <span class="badge bg-light text-dark border">Pay in 3</span>
                                        <span class="badge bg-light text-dark border">Pay in 4</span>
                                    </div>
                                </div>

                                <div class="d-flex gap-2">
                                    <button class="btn btn-sm btn-outline-primary flex-fill">
                                        <i class="bx bx-edit-alt"></i> Configure
                                    </button>
                                    <button class="btn btn-sm btn-outline-secondary">
                                        <i class="bx bx-test-tube"></i> Test
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Stripe (Inactive Example) --}}
                    <div class="col-md-6 col-lg-4">
                        <div class="card border h-100 opacity-75">
                            <div class="card-body">
                                <div class="d-flex align-items-start justify-content-between mb-3">
                                    <div class="d-flex align-items-center gap-3">
                                        <div class="avatar-sm">
                                            <span class="avatar-title bg-secondary-subtle text-secondary rounded">
                                                <i class="bx bx-credit-card-alt fs-4"></i>
                                            </span>
                                        </div>
                                        <div>
                                            <h5 class="mb-0">Stripe</h5>
                                            <small class="text-muted">Payment Gateway</small>
                                        </div>
                                    </div>
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" id="stripeStatus">
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <div class="d-flex justify-content-between mb-2">
                                        <span class="text-muted small">Status</span>
                                        <span class="badge bg-secondary-subtle text-secondary">Inactive</span>
                                    </div>
                                    <div class="d-flex justify-content-between mb-2">
                                        <span class="text-muted small">Mode</span>
                                        <span>—</span>
                                    </div>
                                    <div class="d-flex justify-content-between mb-2">
                                        <span class="text-muted small">Country</span>
                                        <span>—</span>
                                    </div>
                                    <div class="d-flex justify-content-between mb-2">
                                        <span class="text-muted small">Currency</span>
                                        <span>—</span>
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <p class="small text-muted mb-2">Supported Methods:</p>
                                    <div class="d-flex flex-wrap gap-1">
                                        <span class="badge bg-light text-muted border">Not Configured</span>
                                    </div>
                                </div>

                                <div class="d-flex gap-2">
                                    <button class="btn btn-sm btn-primary flex-fill">
                                        <i class="bx bx-cog"></i> Setup
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>

{{-- Add Method Modal --}}
<div class="modal fade" id="addMethodModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Add Payment Method</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form>
                    <div class="mb-3">
                        <label class="form-label">Payment Gateway</label>
                        <select class="form-select">
                            <option value="">Select Gateway</option>
                            <option value="stripe">Stripe</option>
                            <option value="paypal">PayPal</option>
                            <option value="hyperpay">HyperPay</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Gateway Name</label>
                        <input type="text" class="form-control" placeholder="Enter gateway name">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Country</label>
                        <select class="form-select">
                            <option value="SA">Saudi Arabia</option>
                            <option value="AE">United Arab Emirates</option>
                            <option value="KW">Kuwait</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Currency</label>
                        <select class="form-select">
                            <option value="SAR">SAR</option>
                            <option value="AED">AED</option>
                            <option value="KWD">KWD</option>
                        </select>
                    </div>
                    <div class="form-check form-switch mb-3">
                        <input class="form-check-input" type="checkbox" id="enableTestMode">
                        <label class="form-check-label" for="enableTestMode">Enable Test Mode</label>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary">Add Method</button>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Initialize tooltips
    const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });
});

// Toggle payment method enabled/disabled
function toggleMethod(checkbox) {
    const methodId = checkbox.dataset.methodId;
    const isEnabled = checkbox.checked;
    
    fetch('{{ route("payments.methods.toggle") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        },
        body: JSON.stringify({
            method_id: methodId,
            is_enabled: isEnabled
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Show success message
            const toast = document.createElement('div');
            toast.className = 'alert alert-success alert-dismissible fade show position-fixed top-0 end-0 m-3';
            toast.style.zIndex = '9999';
            toast.innerHTML = `
                <strong>Success!</strong> ${data.message}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            `;
            document.body.appendChild(toast);
            setTimeout(() => toast.remove(), 3000);
        } else {
            alert('❌ Error: ' + data.message);
            checkbox.checked = !isEnabled; // Revert
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('❌ Error updating payment method');
        checkbox.checked = !isEnabled; // Revert
    });
}

// Test MyFatoorah connection
function testConnection() {
    const btn = event.target.closest('button');
    const originalText = btn.innerHTML;
    btn.disabled = true;
    btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span> Testing...';

    fetch('{{ route("payments.testConnection") }}')
        .then(response => response.json())
        .then(data => {
            btn.disabled = false;
            btn.innerHTML = originalText;
            
            if (data.success) {
                alert('✅ Connection successful!\n\nMyFatoorah API is working correctly.');
            } else {
                alert('❌ Connection failed!\n\n' + (data.message || 'Unknown error'));
            }
        })
        .catch(error => {
            btn.disabled = false;
            btn.innerHTML = originalText;
            alert('❌ Error testing connection: ' + error.message);
        });
}

// Select payment method as default
function selectPaymentMethod(methodId, methodName) {
    if (!confirm(`Set ${methodName} as the default payment method?`)) {
        return;
    }
    
    fetch('{{ route("payments.methods.select") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        },
        body: JSON.stringify({
            method_id: methodId
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('✅ ' + data.message);
            location.reload(); // Reload to show updated default
        } else {
            alert('❌ Error: ' + data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('❌ Error selecting payment method');
    });
}
</script>
@endsection
