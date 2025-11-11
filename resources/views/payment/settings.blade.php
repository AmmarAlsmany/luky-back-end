{{-- resources/views/admin/settings/payment/per_provider.blade.php --}}
@extends('layouts.vertical', ['title' => __('payment.payment_settings')])

@section('content')
    <div class="row">
        <div class="col-lg-12">
            <div class="card">

                <div class="card-header">
                    <h4 class="mb-0">{{ __('payment.payment_settings_per_provider') }}</h4>
                </div>

                <div class="card-body">
                    <div class="row g-4">
                        <!-- LEFT: Select + Fields -->
                        <div class="col-lg-8">
                            <form id="paymentSettingsForm" autocomplete="off">
                                {{-- Provider (searchable) --}}
                                <div class="mb-4">
                                    <label for="provider" class="form-label">{{ __('payment.select_provider') }}</label>
                                    <select id="provider" class="form-select">
                                        <option value="">{{ __('payment.choose_provider') }}</option>
                                        @if (isset($providers) && count($providers) > 0)
                                            @foreach ($providers as $provider)
                                                <option value="{{ $provider['id'] }}" data-name="{{ $provider['name'] }}"
                                                    data-tax="{{ $provider['tax'] }}"
                                                    data-commission="{{ $provider['commission'] }}"
                                                    data-currency="{{ $provider['currency'] }}">
                                                    {{ $provider['name'] }}
                                                </option>
                                            @endforeach
                                        @else
                                            {{-- Fallback dummy data if no providers found --}}
                                            <option value="1" data-name="Glow Salon" data-tax="15"
                                                data-commission="10" data-currency="SAR">Glow Salon</option>
                                            <option value="2" data-name="Elite Makeup" data-tax="5"
                                                data-commission="12" data-currency="SAR">Elite Makeup</option>
                                        @endif
                                    </select>
                                    <small class="text-muted">{{ __('payment.type_search_providers', ['count' => isset($providers) ? count($providers) : 0]) }}</small>
                                </div>

                                {{-- Fields (disabled by default) --}}
                                <div class="row g-3">
                                    <div class="col-md-4">
                                        <label for="tax" class="form-label">{{ __('payment.tax_percent') }}</label>
                                        <div class="input-group">
                                            <input id="tax" type="number" step="0.01" class="form-control"
                                                placeholder="e.g. 15" disabled>
                                            <span class="input-group-text">%</span>
                                        </div>
                                    </div>

                                    <div class="col-md-4">
                                        <label for="commission" class="form-label">{{ __('payment.commission_percent') }}</label>
                                        <div class="input-group">
                                            <input id="commission" type="number" step="0.01" class="form-control"
                                                placeholder="e.g. 10" disabled>
                                            <span class="input-group-text">%</span>
                                        </div>
                                    </div>

                                    <div class="col-md-4">
                                        <label for="currency" class="form-label">{{ __('payment.default_currency') }}</label>
                                        <select id="currency" class="form-select" disabled>
                                            <option value="">{{ __('payment.select_currency') }}</option>
                                            <option value="SAR">SAR (﷼)</option>
                                            <option value="AED">AED (د.إ)</option>
                                            <option value="USD">USD ($)</option>
                                        </select>
                                    </div>
                                </div>

                                {{-- Bottom action bar (hidden until Edit) --}}
                                <div id="actionBar" class="d-flex gap-2 mt-4 d-none">
                                    <button id="btnUpdate" type="button" class="btn btn-success btn-sm">
                                        <i class="bx bx-check me-1"></i> {{ __('payment.update') }}
                                    </button>
                                    <button id="btnCancel" type="button" class="btn btn-outline-secondary btn-sm">
                                        <i class="bx bx-x me-1"></i> {{ __('payment.cancel') }}
                                    </button>
                                </div>
                            </form>
                        </div>

                        <!-- RIGHT: Current Settings with EDIT ICON -->
                        <div class="col-lg-4">
                            <div class="border rounded p-3 bg-light position-relative">
                                <div class="d-flex align-items-center justify-content-between mb-3">
                                    <h6 class="mb-0">{{ __('payment.current_settings') }}</h6>
                                    <button id="btnEdit" type="button" class="btn btn-sm btn-link text-decoration-none">
                                        <i class="bx bx-edit-alt fs-5"></i>
                                    </button>
                                </div>

                                <dl class="row mb-0 small">
                                    <dt class="col-6 text-muted">{{ __('payment.provider') }}</dt>
                                    <dd class="col-6" id="summaryProvider">—</dd>

                                    <dt class="col-6 text-muted">{{ __('payment.tax_percent') }}</dt>
                                    <dd class="col-6" id="summaryTax">—</dd>

                                    <dt class="col-6 text-muted">{{ __('payment.commission_percent') }}</dt>
                                    <dd class="col-6" id="summaryCommission">—</dd>

                                    <dt class="col-6 text-muted">{{ __('payment.currency') }}</dt>
                                    <dd class="col-6" id="summaryCurrency">—</dd>
                                </dl>

                                <small class="text-muted d-block mt-2">{{ __('payment.click_edit_modify') }}</small>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card-footer">
                    <small class="text-muted">
                        {{ __('payment.settings_flow_hint') }}
                    </small>
                </div>

            </div>
        </div>
        <div class="col-lg-12">
            {{-- === Payment Gateways (Template with Enable/Disable toggles) === --}}
            <div class="card mt-4">
                <div class="card-header d-flex align-items-center justify-content-between">
                    <h5 class="mb-0">{{ __('payment.payment_gateways') }}</h5>
                    <small class="text-muted">{{ __('payment.myfatoorah') }} · {{ __('payment.tabby') }} · {{ __('payment.tamara') }}</small>
                </div>

                <div class="card-body">
                    <ul class="nav nav-tabs" role="tablist">
                        <li class="nav-item">
                            <a href="#pg-myfatoorah" data-bs-toggle="tab" class="nav-link active" role="tab"
                                aria-selected="true">
                                <span class="d-block d-sm-none"><i class="bx bx-credit-card"></i></span>
                                <span class="d-none d-sm-block">MyFatoorah</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="#pg-tabby" data-bs-toggle="tab" class="nav-link" role="tab"
                                aria-selected="false">
                                <span class="d-block d-sm-none"><i class="bx bx-purchase-tag"></i></span>
                                <span class="d-none d-sm-block">Tabby</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="#pg-tamara" data-bs-toggle="tab" class="nav-link" role="tab"
                                aria-selected="false">
                                <span class="d-block d-sm-none"><i class="bx bx-badge"></i></span>
                                <span class="d-none d-sm-block">Tamara</span>
                            </a>
                        </li>
                    </ul>

                    <div class="tab-content pt-3">
                        {{-- MyFatoorah --}}
                        <div class="tab-pane fade show active" id="pg-myfatoorah" role="tabpanel">
                            {{-- Connection Status Alert --}}
                            <div class="alert alert-info d-flex align-items-center mb-3" role="alert">
                                <i class="bx bx-info-circle fs-5 me-2"></i>
                                <div class="flex-grow-1">
                                    <strong>{{ __('payment.current_configuration') }}:</strong> {{ __('payment.connected_to') }}
                                    <strong>{{ config('services.myfatoorah.api_url') }}</strong>
                                </div>
                                <button type="button" class="btn btn-sm btn-light" onclick="testMyFatoorahConnection()">
                                    <i class="bx bx-refresh me-1"></i> {{ __('payment.test_now') }}
                                </button>
                            </div>

                            <div class="d-flex align-items-center justify-content-between mb-3">
                                <h6 class="mb-0">{{ __('payment.myfatoorah') }} — {{ __('payment.configuration') }}</h6>
                                <div class="d-flex gap-2 align-items-center">
                                    <button type="button" class="btn btn-sm btn-outline-primary"
                                        onclick="testMyFatoorahConnection()">
                                        <i class="bx bx-test-tube me-1"></i> {{ __('payment.test') }}
                                    </button>
                                    <div class="form-check form-switch m-0">
                                        <input class="form-check-input" type="checkbox" id="toggle-myfatoorah"
                                            name="myfatoorah[enabled]" checked>
                                        <label class="form-check-label" for="toggle-myfatoorah">{{ __('payment.enabled') }}</label>
                                    </div>
                                </div>
                            </div>

                            <form autocomplete="off">
                                <div class="row g-3">
                                    {{-- Country / Currency --}}
                                    <div class="col-12 col-md-6">
                                        <label class="form-label small mb-1">{{ __('payment.country') }}</label>
                                        <select class="form-select" name="myfatoorah[country]">
                                            <option value="SA" selected>{{ __('payment.saudi_arabia') }} (SA)</option>
                                            <option value="KW">{{ __('payment.kuwait') }} (KW)</option>
                                            <option value="BH">{{ __('payment.bahrain') }} (BH)</option>
                                            <option value="AE">{{ __('payment.united_arab_emirates') }} (AE)</option>
                                            <option value="QA">{{ __('payment.qatar') }} (QA)</option>
                                        </select>
                                        <small class="text-muted">Used to pick correct API base URL.</small>
                                    </div>
                                    <div class="col-12 col-md-6">
                                        <label class="form-label small mb-1">Default Currency</label>
                                        <select class="form-select" name="myfatoorah[currency_iso]">
                                            <option value="SAR" selected>SAR (﷼)</option>
                                            <option value="KWD">KWD (د.ك)</option>
                                            <option value="BHD">BHD (ب.د)</option>
                                            <option value="AED">AED (د.إ)</option>
                                            <option value="QAR">QAR (ر.ق)</option>
                                            <option value="USD">USD ($)</option>
                                        </select>
                                    </div>

                                    {{-- API Access --}}
                                    <div class="col-12">
                                        <label class="form-label small mb-1">API Base URL</label>
                                        <input type="text" class="form-control" name="myfatoorah[base_url]"
                                            value="{{ config('services.myfatoorah.api_url', 'https://apitest.myfatoorah.com') }}"
                                            placeholder="https://api.myfatoorah.com / https://apitest.myfatoorah.com">
                                        <small class="text-muted">Use production or sandbox endpoint based on
                                            Mode/Country.</small>
                                    </div>
                                    <div class="col-12 col-md-8">
                                        <label class="form-label small mb-1">API Key (Live/Test)</label>
                                        <div class="input-group">
                                            <input type="password" class="form-control" id="myfatoorah_api_key"
                                                name="myfatoorah[api_key]"
                                                value="{{ config('services.myfatoorah.api_key') }}"
                                                placeholder="Bearer token from MyFatoorah">
                                            <button class="btn btn-outline-secondary" type="button"
                                                onclick="toggleApiKeyVisibility()">
                                                <i class="bx bx-show" id="toggleIcon"></i>
                                            </button>
                                        </div>
                                        <small class="text-muted">Long bearer token from MyFatoorah portal.</small>
                                    </div>
                                    <div class="col-12 col-md-4">
                                        <label class="form-label small mb-1">Merchant Code / ID</label>
                                        <input type="text" class="form-control" name="myfatoorah[merchant_id]"
                                            value="{{ $myfatoorahSettings['myfatoorah_merchant_id'] ?? '' }}"
                                            placeholder="e.g. MF-123456">
                                    </div>

                                    {{-- URLs --}}
                                    <div class="col-12 col-md-6">
                                        <label class="form-label small mb-1">Success URL</label>
                                        <input type="url" class="form-control" name="myfatoorah[success_url]"
                                            value="{{ config('services.myfatoorah.success_url') }}"
                                            placeholder="https://yourapp.com/payments/myfatoorah/success">
                                    </div>
                                    <div class="col-12 col-md-6">
                                        <label class="form-label small mb-1">Failure URL</label>
                                        <input type="url" class="form-control" name="myfatoorah[failure_url]"
                                            value="{{ config('services.myfatoorah.error_url') }}"
                                            placeholder="https://yourapp.com/payments/myfatoorah/failure">
                                    </div>
                                    <div class="col-12">
                                        <label class="form-label small mb-1">Webhook URL (Payment Status)</label>
                                        <input type="url" class="form-control" name="myfatoorah[webhook_url]"
                                            value="{{ $myfatoorahSettings['myfatoorah_webhook_url'] ?? '' }}"
                                            placeholder="https://yourapp.com/payments/myfatoorah/webhook">
                                        <small class="text-muted">Optional: receive status updates
                                            server-to-server.</small>
                                    </div>

                                    {{-- Payment Options (common in KSA/GCC) --}}
                                    <div class="col-12">
                                        <label class="form-label small mb-1 d-block">Allowed Payment Methods</label>
                                        <div class="row g-2">
                                            <div class="col-6 col-md-4">
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox" id="mf_mada"
                                                        name="myfatoorah[methods][mada]"
                                                        {{ isset($myfatoorahSettings['myfatoorah_payment_methods']) && (json_decode($myfatoorahSettings['myfatoorah_payment_methods'], true)['mada'] ?? true) ? 'checked' : '' }}>
                                                    <label class="form-check-label" for="mf_mada">MADA</label>
                                                </div>
                                            </div>
                                            <div class="col-6 col-md-4">
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox" id="mf_visa"
                                                        name="myfatoorah[methods][visa_master]"
                                                        {{ isset($myfatoorahSettings['myfatoorah_payment_methods']) && (json_decode($myfatoorahSettings['myfatoorah_payment_methods'], true)['visa_master'] ?? true) ? 'checked' : '' }}>
                                                    <label class="form-check-label" for="mf_visa">Visa /
                                                        Mastercard</label>
                                                </div>
                                            </div>
                                            <div class="col-6 col-md-4">
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox" id="mf_applepay"
                                                        name="myfatoorah[methods][applepay]"
                                                        {{ isset($myfatoorahSettings['myfatoorah_payment_methods']) && (json_decode($myfatoorahSettings['myfatoorah_payment_methods'], true)['applepay'] ?? false) ? 'checked' : '' }}>
                                                    <label class="form-check-label" for="mf_applepay">Apple Pay</label>
                                                </div>
                                            </div>
                                            <div class="col-6 col-md-4">
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox" id="mf_stcpay"
                                                        name="myfatoorah[methods][stc_pay]"
                                                        {{ isset($myfatoorahSettings['myfatoorah_payment_methods']) && json_decode($myfatoorahSettings['myfatoorah_payment_methods'], true)['stc_pay'] ?? false ? 'checked' : '' }}>
                                                    <label class="form-check-label" for="mf_stcpay">STC Pay</label>
                                                </div>
                                            </div>
                                        </div>
                                        <small class="text-muted">Enable methods approved for your merchant &
                                            country.</small>
                                    </div>

                                    {{-- Order limits and operational controls --}}
                                    <div class="col-6 col-md-3">
                                        <label class="form-label small mb-1">Min Amount</label>
                                        <div class="input-group">
                                            <span
                                                class="input-group-text">{{ old('myfatoorah.currency_iso', 'SAR') }}</span>
                                            <input type="number" class="form-control" name="myfatoorah[min_amount]"
                                                value="{{ $myfatoorahSettings['myfatoorah_min_amount'] ?? '0' }}"
                                                placeholder="0">
                                        </div>
                                    </div>
                                    <div class="col-6 col-md-3">
                                        <label class="form-label small mb-1">Max Amount</label>
                                        <div class="input-group">
                                            <span
                                                class="input-group-text">{{ old('myfatoorah.currency_iso', 'SAR') }}</span>
                                            <input type="number" class="form-control" name="myfatoorah[max_amount]"
                                                value="{{ $myfatoorahSettings['myfatoorah_max_amount'] ?? '10000' }}"
                                                placeholder="10000">
                                        </div>
                                    </div>
                                    <div class="col-6 col-md-3">
                                        <label class="form-label small mb-1">Invoice Expiry (mins)</label>
                                        <input type="number" class="form-control" name="myfatoorah[invoice_expiry]"
                                            value="{{ $myfatoorahSettings['myfatoorah_invoice_expiry'] ?? '30' }}"
                                            placeholder="e.g. 30">
                                        <small class="text-muted">Unpaid invoices auto-cancel after this time.</small>
                                    </div>
                                    <div class="col-6 col-md-3">
                                        <label class="form-label small mb-1">Language</label>
                                        <select class="form-select" name="myfatoorah[language]">
                                            <option value="En" selected>English</option>
                                            <option value="Ar">Arabic</option>
                                        </select>
                                    </div>

                                    {{-- Optional descriptors / internal --}}
                                    <div class="col-12 col-md-6">
                                        <label class="form-label small mb-1">Statement Descriptor</label>
                                        <input type="text" class="form-control" name="myfatoorah[descriptor]"
                                            placeholder="Shown on customer statement (if supported)">
                                    </div>
                                    <div class="col-12 col-md-6">
                                        <label class="form-label small mb-1">Notes</label>
                                        <input type="text" class="form-control" name="myfatoorah[notes]"
                                            placeholder="Internal notes (optional)">
                                    </div>
                                </div>
                            </form>
                        </div>


                        {{-- Tabby --}}
                        <div class="tab-pane fade" id="pg-tabby" role="tabpanel">
                            <div class="d-flex align-items-center justify-content-between mb-3">
                                <h6 class="mb-0">Tabby — Configuration</h6>
                                <div class="form-check form-switch m-0">
                                    <input class="form-check-input" type="checkbox" id="toggle-tabby" checked>
                                    <label class="form-check-label" for="toggle-tabby">Enabled</label>
                                </div>
                            </div>

                            <form autocomplete="off">
                                <div class="row g-3">
                                    {{-- Environment / Region --}}
                                    <div class="col-12 col-md-4">
                                        <label class="form-label small mb-1">Mode</label>
                                        <select class="form-select">
                                            <option value="test" selected>Test (Sandbox)</option>
                                            <option value="live">Live (Production)</option>
                                        </select>
                                        <small class="text-muted">Use Test while integrating; switch to Live when
                                            approved.</small>
                                    </div>
                                    <div class="col-12 col-md-4">
                                        <label class="form-label small mb-1">Region / Country</label>
                                        <select class="form-select">
                                            <option value="SA" selected>Saudi Arabia (SA)</option>
                                            <option value="AE">United Arab Emirates (AE)</option>
                                            <option value="KW">Kuwait (KW)</option>
                                            <option value="BH">Bahrain (BH)</option>
                                            <option value="QA">Qatar (QA)</option>
                                        </select>
                                        <small class="text-muted">Determines currency/rules & Tabby endpoints.</small>
                                    </div>
                                    <div class="col-12 col-md-4">
                                        <label class="form-label small mb-1">Default Currency</label>
                                        <select class="form-select">
                                            <option value="SAR" selected>SAR (﷼)</option>
                                            <option value="AED">AED (د.إ)</option>
                                            <option value="KWD">KWD (د.ك)</option>
                                            <option value="BHD">BHD (ب.د)</option>
                                            <option value="QAR">QAR (ر.ق)</option>
                                            <option value="USD">USD ($)</option>
                                        </select>
                                    </div>

                                    {{-- Credentials --}}
                                    <div class="col-12 col-md-6">
                                        <label class="form-label small mb-1">Publishable Key (Public)</label>
                                        <input type="text" class="form-control"
                                            placeholder="pk_test_xxx / pk_live_xxx">
                                        <small class="text-muted">Used on the client side (frontend checkout
                                            widgets).</small>
                                    </div>
                                    <div class="col-12 col-md-6">
                                        <label class="form-label small mb-1">Secret Key (Server)</label>
                                        <input type="password" class="form-control"
                                            placeholder="sk_test_xxx / sk_live_xxx">
                                        <small class="text-muted">Keep secret; used on server for Orders/Payments
                                            API.</small>
                                    </div>

                                    {{-- Merchant / Store info --}}
                                    <div class="col-12 col-md-6">
                                        <label class="form-label small mb-1">Merchant ID / Code</label>
                                        <input type="text" class="form-control" placeholder="e.g. TABBY-MERCHANT-123">
                                    </div>
                                    <div class="col-12 col-md-6">
                                        <label class="form-label small mb-1">Store Name (Descriptor)</label>
                                        <input type="text" class="form-control"
                                            placeholder="Shown to customer in Tabby">
                                    </div>

                                    {{-- URLs --}}
                                    <div class="col-12 col-md-6">
                                        <label class="form-label small mb-1">Success URL</label>
                                        <input type="text" class="form-control"
                                            placeholder="https://yourapp.com/payments/tabby/success">
                                    </div>
                                    <div class="col-12 col-md-6">
                                        <label class="form-label small mb-1">Cancel URL</label>
                                        <input type="text" class="form-control"
                                            placeholder="https://yourapp.com/payments/tabby/cancel">
                                    </div>
                                    <div class="col-12">
                                        <label class="form-label small mb-1">Webhook URL (Server-to-Server)</label>
                                        <input type="text" class="form-control"
                                            placeholder="https://yourapp.com/payments/tabby/webhook">
                                        <small class="text-muted">Tabby sends payment/charge updates here.</small>
                                    </div>

                                    {{-- Payment method toggles --}}
                                    <div class="col-12">
                                        <label class="form-label small mb-1 d-block">Payment Methods</label>
                                        <div class="row g-2">
                                            <div class="col-12 col-md-4">
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox" id="tabby_pay_in_4"
                                                        checked>
                                                    <label class="form-check-label" for="tabby_pay_in_4">Pay in 4
                                                        (Installments)</label>
                                                </div>
                                            </div>
                                            <div class="col-12 col-md-4">
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox" id="tabby_pay_later">
                                                    <label class="form-check-label" for="tabby_pay_later">Pay
                                                        Later</label>
                                                </div>
                                            </div>
                                            <div class="col-12 col-md-4">
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox" id="tabby_split"
                                                        checked>
                                                    <label class="form-check-label" for="tabby_split">Split / BNPL</label>
                                                </div>
                                            </div>
                                        </div>
                                        <small class="text-muted">Enable only the flows approved for your merchant.</small>
                                    </div>

                                    {{-- Order limits & capture behavior --}}
                                    <div class="col-6 col-md-3">
                                        <label class="form-label small mb-1">Min Amount</label>
                                        <div class="input-group">
                                            <span class="input-group-text">Amount</span>
                                            <input type="number" class="form-control" placeholder="50">
                                        </div>
                                    </div>
                                    <div class="col-6 col-md-3">
                                        <label class="form-label small mb-1">Max Amount</label>
                                        <div class="input-group">
                                            <span class="input-group-text">Amount</span>
                                            <input type="number" class="form-control" placeholder="3000">
                                        </div>
                                    </div>
                                    <div class="col-12 col-md-6">
                                        <label class="form-label small mb-1">Capture Strategy</label>
                                        <select class="form-select">
                                            <option value="auto" selected>Auto-capture on approval</option>
                                            <option value="manual">Manual capture (via Dashboard/API)</option>
                                        </select>
                                        <small class="text-muted">Choose how charges are captured after
                                            authorization.</small>
                                    </div>

                                    {{-- Installments config --}}
                                    <div class="col-12 col-md-6">
                                        <label class="form-label small mb-1">Allowed Installments</label>
                                        <select class="form-select">
                                            <option value="2">2 payments</option>
                                            <option value="3" selected>3 payments</option>
                                            <option value="4">4 payments</option>
                                        </select>
                                    </div>
                                    <div class="col-12 col-md-6">
                                        <label class="form-label small mb-1">Customer Phone Format</label>
                                        <select class="form-select">
                                            <option value="e164" selected>E.164 (+9665xxxxxxx)</option>
                                            <option value="national">National (05xxxxxxx)</option>
                                        </select>
                                    </div>

                                    {{-- Risk / test options (optional placeholders) --}}
                                    <div class="col-12">
                                        <label class="form-label small mb-1">Notes</label>
                                        <textarea class="form-control" rows="2" placeholder="Internal notes for this gateway (optional)"></textarea>
                                    </div>
                                </div>
                            </form>
                        </div>

                        {{-- Tamara --}}
                        <div class="tab-pane fade" id="pg-tamara" role="tabpanel">
                            <div class="d-flex align-items-center justify-content-between mb-3">
                                <h6 class="mb-0">Tamara — Configuration</h6>
                                <div class="form-check form-switch m-0">
                                    <input class="form-check-input" type="checkbox" id="toggle-tamara"
                                        name="tamara[enabled]" checked>
                                    <label class="form-check-label" for="toggle-tamara">Enabled</label>
                                </div>
                            </div>

                            <form autocomplete="off">
                                <div class="row g-3">
                                    {{-- Environment / Region / Currency --}}
                                    <div class="col-12 col-md-4">
                                        <label class="form-label small mb-1">Mode</label>
                                        <select class="form-select" name="tamara[mode]">
                                            <option value="test" selected>Test (Sandbox)</option>
                                            <option value="live">Live (Production)</option>
                                        </select>
                                    </div>
                                    <div class="col-12 col-md-4">
                                        <label class="form-label small mb-1">Country</label>
                                        <select class="form-select" name="tamara[country]">
                                            <option value="SA" selected>Saudi Arabia (SA)</option>
                                            <option value="AE">United Arab Emirates (AE)</option>
                                            <option value="KW">Kuwait (KW)</option>
                                            <option value="BH">Bahrain (BH)</option>
                                        </select>
                                        <small class="text-muted">Determines Tamara endpoints & availability.</small>
                                    </div>
                                    <div class="col-12 col-md-4">
                                        <label class="form-label small mb-1">Default Currency</label>
                                        <select class="form-select" name="tamara[currency_iso]">
                                            <option value="SAR" selected>SAR (﷼)</option>
                                            <option value="AED">AED (د.إ)</option>
                                            <option value="KWD">KWD (د.ك)</option>
                                            <option value="BHD">BHD (ب.د)</option>
                                            <option value="USD">USD ($)</option>
                                        </select>
                                    </div>

                                    {{-- API Access --}}
                                    <div class="col-12">
                                        <label class="form-label small mb-1">API Base URL</label>
                                        <input type="url" class="form-control" name="tamara[base_url]"
                                            placeholder="https://api.tamara.co / https://sandbox.tamara.co">
                                        <small class="text-muted">Use production or sandbox endpoint based on
                                            Mode/Country.</small>
                                    </div>
                                    <div class="col-12 col-md-6">
                                        <label class="form-label small mb-1">Public Key (Publishable)</label>
                                        <input type="text" class="form-control" name="tamara[public_key]"
                                            placeholder="pk_test_xxx / pk_live_xxx">
                                    </div>
                                    <div class="col-12 col-md-6">
                                        <label class="form-label small mb-1">Secret Key</label>
                                        <input type="password" class="form-control" name="tamara[secret_key]"
                                            placeholder="sk_test_xxx / sk_live_xxx">
                                    </div>

                                    {{-- Merchant / Store info --}}
                                    <div class="col-12 col-md-6">
                                        <label class="form-label small mb-1">Merchant ID</label>
                                        <input type="text" class="form-control" name="tamara[merchant_id]"
                                            placeholder="e.g. TM-987654">
                                    </div>
                                    <div class="col-12 col-md-6">
                                        <label class="form-label small mb-1">Store Name (Descriptor)</label>
                                        <input type="text" class="form-control" name="tamara[store_name]"
                                            placeholder="Shown to the customer">
                                    </div>

                                    {{-- URLs --}}
                                    <div class="col-12 col-md-6">
                                        <label class="form-label small mb-1">Success URL</label>
                                        <input type="url" class="form-control" name="tamara[success_url]"
                                            placeholder="https://yourapp.com/payments/tamara/success">
                                    </div>
                                    <div class="col-12 col-md-6">
                                        <label class="form-label small mb-1">Cancel URL</label>
                                        <input type="url" class="form-control" name="tamara[cancel_url]"
                                            placeholder="https://yourapp.com/payments/tamara/cancel">
                                    </div>
                                    <div class="col-12">
                                        <label class="form-label small mb-1">Webhook URL</label>
                                        <input type="url" class="form-control" name="tamara[webhook_url]"
                                            placeholder="https://yourapp.com/payments/tamara/webhook">
                                        <small class="text-muted">Tamara sends order/charge updates here
                                            (server-to-server).</small>
                                    </div>

                                    {{-- Payment flows / BNPL options --}}
                                    <div class="col-12">
                                        <label class="form-label small mb-1 d-block">Payment Flows</label>
                                        <div class="row g-2">
                                            <div class="col-12 col-md-4">
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox" id="tm_pay_in_2"
                                                        name="tamara[flows][pay_in_2]">
                                                    <label class="form-check-label" for="tm_pay_in_2">Pay in 2</label>
                                                </div>
                                            </div>
                                            <div class="col-12 col-md-4">
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox" id="tm_pay_in_3"
                                                        name="tamara[flows][pay_in_3]" checked>
                                                    <label class="form-check-label" for="tm_pay_in_3">Pay in 3</label>
                                                </div>
                                            </div>
                                            <div class="col-12 col-md-4">
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox" id="tm_pay_in_4"
                                                        name="tamara[flows][pay_in_4]" checked>
                                                    <label class="form-check-label" for="tm_pay_in_4">Pay in 4</label>
                                                </div>
                                            </div>
                                        </div>
                                        <small class="text-muted">Enable only the plans approved for your merchant.</small>
                                    </div>

                                    {{-- Limits & capture behavior --}}
                                    <div class="col-6 col-md-3">
                                        <label class="form-label small mb-1">Min Amount</label>
                                        <div class="input-group">
                                            <span class="input-group-text">{{ old('tamara.currency_iso', 'SAR') }}</span>
                                            <input type="number" class="form-control" name="tamara[min_amount]"
                                                placeholder="50">
                                        </div>
                                    </div>
                                    <div class="col-6 col-md-3">
                                        <label class="form-label small mb-1">Max Amount</label>
                                        <div class="input-group">
                                            <span class="input-group-text">{{ old('tamara.currency_iso', 'SAR') }}</span>
                                            <input type="number" class="form-control" name="tamara[max_amount]"
                                                placeholder="3000">
                                        </div>
                                    </div>
                                    <div class="col-12 col-md-6">
                                        <label class="form-label small mb-1">Capture Strategy</label>
                                        <select class="form-select" name="tamara[capture_strategy]">
                                            <option value="auto" selected>Auto-capture on approval</option>
                                            <option value="manual">Manual capture (via Dashboard/API)</option>
                                        </select>
                                    </div>

                                    {{-- Customer info formatting (optional) --}}
                                    <div class="col-12 col-md-6">
                                        <label class="form-label small mb-1">Customer Phone Format</label>
                                        <select class="form-select" name="tamara[phone_format]">
                                            <option value="e164" selected>E.164 (+9665xxxxxxx)</option>
                                            <option value="national">National (05xxxxxxx)</option>
                                        </select>
                                    </div>

                                    {{-- Optional notes --}}
                                    <div class="col-12">
                                        <label class="form-label small mb-1">Notes</label>
                                        <textarea class="form-control" rows="2" name="tamara[notes]" placeholder="Internal notes (optional)"></textarea>
                                    </div>
                                </div>
                            </form>
                        </div>

                    </div>

                    <div class="d-flex justify-content-between align-items-center mt-4">
                        <button type="button" class="btn btn-outline-secondary"
                            onclick="window.location.href='{{ route('payments.methods') }}'">
                            <i class="bx bx-credit-card me-1"></i> View Payment Methods
                        </button>
                        <button type="button" class="btn btn-primary" onclick="saveGatewaySettings()">
                            <i class="bx bx-save me-1"></i> Save All Settings
                        </button>
                    </div>
                </div>
            </div>

        </div>
    </div>

    {{-- ====== Inline deps so there’s no stack/order problem ====== --}}
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <style>
        .select2-container .select2-selection--single {
            height: 38px;
        }

        .select2-selection__rendered {
            line-height: 38px !important;
        }

        .select2-selection__arrow {
            height: 38px !important;
        }
    </style>

    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

    <script>
        // everything scoped to this page
        jQuery(function($) {
            const $provider = $('#provider');
            const $tax = $('#tax');
            const $commission = $('#commission');
            const $currency = $('#currency');

            const $btnEdit = $('#btnEdit'); // icon on right card
            const $btnUpdate = $('#btnUpdate'); // bottom buttons
            const $btnCancel = $('#btnCancel');
            const $actionBar = $('#actionBar');

            const $sProv = $('#summaryProvider');
            const $sTax = $('#summaryTax');
            const $sCom = $('#summaryCommission');
            const $sCur = $('#summaryCurrency');

            let snapshot = {
                tax: '',
                commission: '',
                currency: ''
            };

            // turn on Select2 search
            $provider.select2({
                width: '100%',
                placeholder: '— Choose a provider —'
            });

            function lockFields(lock) {
                // when editing (lock=false): provider is disabled; other fields enabled
                $provider.prop('disabled', !lock);
                if (!lock) {
                    $provider.trigger('change.select2');
                } // refresh UI when disabling

                $tax.prop('disabled', lock);
                $commission.prop('disabled', lock);
                $currency.prop('disabled', lock);

                $actionBar.toggleClass('d-none', lock);
            }

            // load provider defaults into fields + summary
            function loadFromOption($opt) {
                const name = ($opt.data('name') || $opt.text() || '').trim();
                const tax = $opt.data('tax');
                const com = $opt.data('commission');
                const curr = $opt.data('currency');

                $tax.val(tax ?? '');
                $commission.val(com ?? '');
                $currency.val(curr ?? '');

                $sProv.text(name || '—');
                $sTax.text((tax !== undefined && tax !== '') ? `${tax}%` : '—');
                $sCom.text((com !== undefined && com !== '') ? `${com}%` : '—');
                $sCur.text(curr || '—');
            }

            // on provider change (not edit mode)
            $provider.on('change', function() {
                const $opt = $(this).find(':selected');
                if (!$opt.val()) { // empty choice
                    $tax.val('');
                    $commission.val('');
                    $currency.val('');
                    $sProv.text('—');
                    $sTax.text('—');
                    $sCom.text('—');
                    $sCur.text('—');
                    lockFields(true);
                    return;
                }
                loadFromOption($opt);
                lockFields(true); // stay locked until user clicks Edit
            });

            // Edit: require a provider selected
            $btnEdit.on('click', function(e) {
                e.preventDefault();
                if (!$provider.val()) {
                    $provider.select2('open');
                    return;
                }
                snapshot = {
                    tax: $tax.val(),
                    commission: $commission.val(),
                    currency: $currency.val()
                };
                lockFields(false); // enable fields, disable provider
            });

            // Cancel: restore snapshot and lock
            $btnCancel.on('click', function(e) {
                e.preventDefault();
                $tax.val(snapshot.tax);
                $commission.val(snapshot.commission);
                $currency.val(snapshot.currency);
                lockFields(true);
            });

            // Update: copy to summary and lock + SAVE TO DATABASE
            $btnUpdate.on('click', function(e) {
                e.preventDefault();

                const providerId = $provider.val();
                const name = $provider.find(':selected').data('name') || $provider.find(':selected').text()
                    .trim();
                const tax = $tax.val();
                const com = $commission.val();
                const curr = $currency.val();

                // Validation
                if (!providerId) {
                    alert('❌ Please select a provider');
                    return;
                }
                if (!tax || !com || !curr) {
                    alert('❌ Please fill in all fields');
                    return;
                }

                // Disable button and show loading
                const $btn = $(this);
                const originalText = $btn.html();
                $btn.prop('disabled', true).html('<i class="bx bx-loader bx-spin me-1"></i> Saving...');

                // Send AJAX request to save
                fetch('{{ route('payments.updateProviderSettings') }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                        },
                        body: JSON.stringify({
                            provider_id: providerId,
                            tax: parseFloat(tax),
                            commission: parseFloat(com),
                            currency: curr
                        })
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            // Update the dropdown option's data attributes with new values
                            const $selectedOption = $provider.find(':selected');
                            $selectedOption.attr('data-tax', tax);
                            $selectedOption.attr('data-commission', com);
                            $selectedOption.attr('data-currency', curr);

                            // Update summary display
                            $sProv.text(name || '—');
                            $sTax.text(tax ? `${tax}%` : '—');
                            $sCom.text(com ? `${com}%` : '—');
                            $sCur.text(curr || '—');

                            lockFields(true);

                            // Show success message with actual saved data
                            alert(`✅ Provider payment settings saved successfully!

Provider: ${data.data.business_name}
Commission: ${data.data.commission_rate}%
Tax: ${tax}%
Currency: ${curr}`);
                        } else {
                            alert('❌ Failed to save settings:\n\n' + (data.message || 'Unknown error'));
                        }
                    })
                    .catch(error => {
                        console.error('Error saving provider settings:', error);
                        alert('❌ Error saving settings:\n\n' + error.message);
                    })
                    .finally(() => {
                        $btn.prop('disabled', false).html(originalText);
                    });
            });

            // Optional: preload first provider (uncomment if you want an initial state)
            // $provider.val('1').trigger('change');
        });

        // Test MyFatoorah Connection
        function testMyFatoorahConnection() {
            const btn = event.target.closest('button');
            const originalHtml = btn.innerHTML;
            btn.disabled = true;
            btn.innerHTML = '<i class="bx bx-loader bx-spin me-1"></i> Testing...';

            fetch('{{ route('payments.testConnection') }}')
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert(
                            '✅ MyFatoorah Connection Successful!\n\nAPI is working correctly.\nYou can now process payments.');
                    } else {
                        alert('❌ Connection Failed!\n\n' + (data.message || 'Unknown error'));
                    }
                })
                .catch(error => {
                    alert('❌ Connection Test Failed!\n\n' + error.message);
                })
                .finally(() => {
                    btn.disabled = false;
                    btn.innerHTML = originalHtml;
                });
        }

        // Toggle API Key Visibility
        function toggleApiKeyVisibility() {
            const input = document.getElementById('myfatoorah_api_key');
            const icon = document.getElementById('toggleIcon');

            if (input.type === 'password') {
                input.type = 'text';
                icon.classList.remove('bx-show');
                icon.classList.add('bx-hide');
            } else {
                input.type = 'password';
                icon.classList.remove('bx-hide');
                icon.classList.add('bx-show');
            }
        }

        // Save Gateway Settings
        function saveGatewaySettings() {
            // Get all form data
            const myfatoorahData = {
                enabled: document.getElementById('toggle-myfatoorah').checked,
                mode: 'live', // Always use live mode
                country: document.querySelector('select[name="myfatoorah[country]"]').value,
                currency: document.querySelector('select[name="myfatoorah[currency_iso]"]').value,
                base_url: document.querySelector('input[name="myfatoorah[base_url]"]').value,
                api_key: document.querySelector('input[name="myfatoorah[api_key]"]').value,
                merchant_id: document.querySelector('input[name="myfatoorah[merchant_id]"]').value || '',
                success_url: document.querySelector('input[name="myfatoorah[success_url]"]').value,
                failure_url: document.querySelector('input[name="myfatoorah[failure_url]"]').value,
                webhook_url: document.querySelector('input[name="myfatoorah[webhook_url]"]').value || '',
                payment_methods: {
                    mada: document.getElementById('mf_mada').checked,
                    visa_master: document.getElementById('mf_visa').checked,
                    applepay: document.getElementById('mf_applepay').checked,
                    stc_pay: document.getElementById('mf_stcpay').checked,
                },
                min_amount: document.querySelector('input[name="myfatoorah[min_amount]"]').value || null,
                max_amount: document.querySelector('input[name="myfatoorah[max_amount]"]').value || null,
                invoice_expiry: document.querySelector('input[name="myfatoorah[invoice_expiry]"]').value || null,
                language: document.querySelector('select[name="myfatoorah[language]"]').value,
                descriptor: document.querySelector('input[name="myfatoorah[descriptor]"]').value || '',
                notes: document.querySelector('input[name="myfatoorah[notes]"]').value || '',
            };

            // Validation
            if (!myfatoorahData.base_url || !myfatoorahData.api_key || !myfatoorahData.success_url ||
                !myfatoorahData.failure_url) {
                alert('❌ Please fill in all required fields (API URL, API Key, Success URL, Failure URL)');
                return;
            }

            // Show loading state
            const btn = event.target;
            const originalText = btn.innerHTML;
            btn.disabled = true;
            btn.innerHTML = '<i class="bx bx-loader bx-spin me-1"></i> Saving...';

            // Send AJAX request
            fetch('{{ route('payments.updateGatewaySettings') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify(myfatoorahData)
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert('✅ MyFatoorah gateway settings saved successfully!\n\nSettings have been updated and config cache cleared.');
                    } else {
                        const errorMsg = data.errors
                            ? Object.values(data.errors).flat().join('\n')
                            : data.message;
                        alert('❌ Failed to save settings:\n\n' + errorMsg);
                    }
                })
                .catch(error => {
                    console.error('Error saving gateway settings:', error);
                    alert('❌ Error saving settings:\n\n' + error.message);
                })
                .finally(() => {
                    btn.disabled = false;
                    btn.innerHTML = originalText;
                });
        }
    </script>
@endsection
