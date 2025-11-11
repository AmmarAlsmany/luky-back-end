@extends('layouts.vertical', ['title' => __('promos.promo_codes')])

@section('content')
<!-- Flash Messages -->
@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="bx bx-check-circle me-2"></i>{{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

@if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <i class="bx bx-error-circle me-2"></i>{{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif



<!-- Promo Detail Modal -->
<div class="modal fade" id="promoDetailModal" tabindex="-1" aria-labelledby="promoDetailLabel" aria-hidden="true">
  <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
    <div class="modal-content">

      <!-- Header -->
      <div class="modal-header border-0 pb-0">
        <div class="d-flex flex-column">
          <div class="d-flex align-items-center gap-2">
            <h5 class="modal-title mb-0" id="promoDetailLabel">Autumn Sale</h5>
            <span class="badge bg-primary-subtle text-primary">Percentage</span>
            <span class="badge bg-success-subtle text-success">Active</span>
          </div>
          <div class="d-flex align-items-center gap-3 mt-1">
            <small class="text-muted">Code:</small>
            <span class="fw-semibold">AUTUMN20</span>
            <span class="text-muted">•</span>
            <small class="text-muted">Validity:</small>
            <span class="fw-semibold">10 Oct 2025 – 31 Dec 2025</span>
          </div>
        </div>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>

      <div class="modal-body pt-3">
        <div class="row g-4">

          <!-- LEFT: Main Info -->
          <div class="col-lg-8">

            <!-- Summary (clear key–value) -->
            <div class="card border-0 bg-light-subtle">
              <div class="card-body p-3">
                <h6 class="text-uppercase text-muted mb-3">Summary</h6>
                <div class="row g-3">
                  <div class="col-md-6">
                    <dl class="row mb-0">
                      <dt class="col-6 text-muted">Discount</dt>
                      <dd class="col-6 fw-semibold text-end">20% <span class="text-muted">(Cap: SAR 100)</span></dd>
                    </dl>
                  </div>
                  <div class="col-md-6">
                    <dl class="row mb-0">
                      <dt class="col-6 text-muted">Min. Order</dt>
                      <dd class="col-6 fw-semibold text-end">SAR 250</dd>
                    </dl>
                  </div>
                  <div class="col-md-6">
                    <dl class="row mb-0">
                      <dt class="col-6 text-muted">Uses / Customer</dt>
                      <dd class="col-6 fw-semibold text-end">1</dd>
                    </dl>
                  </div>
                  <div class="col-md-6">
                    <dl class="row mb-0">
                      <dt class="col-6 text-muted">Global Usage</dt>
                      <dd class="col-6 fw-semibold text-end">134 / 1000</dd>
                    </dl>
                  </div>
                </div>
              </div>
            </div>

            <!-- Quick Stats (at-a-glance) -->
            <div class="row g-3 mt-1">
              <div class="col-6 col-md-3">
                <div class="card border-0 h-100">
                  <div class="card-body p-3">
                    <small class="text-muted d-block">Orders</small>
                    <div class="fw-semibold fs-5">134</div>
                  </div>
                </div>
              </div>
              <div class="col-6 col-md-3">
                <div class="card border-0 h-100">
                  <div class="card-body p-3">
                    <small class="text-muted d-block">Discount Given</small>
                    <div class="fw-semibold fs-5">SAR 7,980</div>
                  </div>
                </div>
              </div>
              <div class="col-6 col-md-3">
                <div class="card border-0 h-100">
                  <div class="card-body p-3">
                    <small class="text-muted d-block">AOV Lift</small>
                    <div class="fw-semibold fs-5">+8.4%</div>
                  </div>
                </div>
              </div>
              <div class="col-6 col-md-3">
                <div class="card border-0 h-100">
                  <div class="card-body p-3">
                    <small class="text-muted d-block">Refund Impact</small>
                    <div class="fw-semibold fs-5">Low</div>
                  </div>
                </div>
              </div>
            </div>

            <!-- Scope (what it applies to) -->
            <div class="card border-0 mt-3">
              <div class="card-body p-3">
                <h6 class="text-uppercase text-muted mb-2">Scope</h6>

                <small class="text-muted d-block mb-1">Categories</small>
                <div class="d-flex flex-wrap gap-2 mb-3">
                  <span class="badge bg-light text-dark">Mapping</span>
                  <span class="badge bg-light text-dark">Survey</span>
                </div>

                <small class="text-muted d-block mb-1">Services</small>
                <div class="d-flex flex-wrap gap-2 mb-3">
                  <span class="badge bg-light text-dark">Construction Mapping</span>
                  <span class="badge bg-light text-dark">Drone Roof Inspection</span>
                </div>

                <small class="text-muted d-block mb-1">Countries</small>
                <div class="d-flex flex-wrap gap-2 mb-3">
                  <span class="badge bg-light text-dark">U.A.E</span>
                  <span class="badge bg-light text-dark">Saudi Arabia</span>
                  <span class="badge bg-light text-dark">India</span>
                </div>

                <small class="text-muted d-block mb-1">Channels</small>
                <div class="d-flex flex-wrap gap-2">
                  <span class="badge bg-light text-dark">Web</span>
                  <span class="badge bg-light text-dark">App</span>
                  <span class="badge bg-light text-dark">In-store</span>
                </div>
              </div>
            </div>

            <!-- Terms (plain, readable) -->
            <div class="card border-0 mt-3">
              <div class="card-body p-3">
                <h6 class="text-uppercase text-muted mb-2">Terms</h6>
                <ul class="mb-0 ps-3">
                  <li>Cannot be combined with other percentage promos.</li>
                  <li>Excludes custom enterprise quotes.</li>
                  <li>Cap applies per order, not per item.</li>
                </ul>
              </div>
            </div>
          </div>

          <!-- RIGHT: Controls & Metadata -->
          <div class="col-lg-4">

            <!-- Actions -->
            <div class="card border-0">
              <div class="card-body p-3">
                <h6 class="text-uppercase text-muted mb-3">Actions</h6>
                <div class="d-grid gap-2">
                  <a href="#!" class="btn btn-primary">Edit Promo</a>
                  <a href="#!" class="btn btn-outline-secondary">Duplicate</a>
                  <a href="#!" class="btn btn-outline-warning">Disable</a>
                  <a href="#!" class="btn btn-outline-danger">Delete</a>
                </div>
              </div>
            </div>

            <!-- Metadata -->
            <div class="card border-0 mt-3">
              <div class="card-body p-3">
                <h6 class="text-uppercase text-muted mb-3">Metadata</h6>
                <div class="list-group list-group-flush">
                  <div class="list-group-item px-0 d-flex justify-content-between">
                    <span class="text-muted">Created By</span>
                    <span class="fw-semibold">Admin Team</span>
                  </div>
                  <div class="list-group-item px-0 d-flex justify-content-between">
                    <span class="text-muted">Created On</span>
                    <span class="fw-semibold">05 Oct 2025</span>
                  </div>
                  <div class="list-group-item px-0 d-flex justify-content-between">
                    <span class="text-muted">Last Updated</span>
                    <span class="fw-semibold">28 Oct 2025</span>
                  </div>
                  <div class="list-group-item px-0 d-flex justify-content-between">
                    <span class="text-muted">Priority</span>
                    <span class="fw-semibold">Medium</span>
                  </div>
                  <div class="list-group-item px-0 d-flex justify-content-between">
                    <span class="text-muted">Stacking</span>
                    <span class="fw-semibold">Not Allowed</span>
                  </div>
                </div>
              </div>
            </div>

            <!-- Code (copy friendly) -->
            <div class="card border-0 mt-3 bg-light-subtle">
              <div class="card-body p-3">
                <div class="d-flex align-items-center justify-content-between">
                  <div>
                    <small class="text-muted d-block">Promo Code</small>
                    <div class="fw-semibold fs-5">AUTUMN20</div>
                  </div>
                  <a href="#!" class="btn btn-light btn-sm">Copy</a>
                </div>
              </div>
            </div>

          </div>
        </div><!-- /row -->
      </div>

      <!-- Footer -->
      <div class="modal-footer">
        <a href="#!" class="btn btn-outline-secondary"  data-bs-dismiss="modal" aria-label="Close">Close</a>
        <a href="#!" class="btn btn-primary">Edit Promo</a>
      </div>
    </div>
  </div>
</div>

<!-- Optional hero/cards (kept as-is, tweak or remove later) -->
<div class="row d-none">
    <div class="col-lg-12">
        <div class="card bg-light-subtle">
            <div class="card-header border-0">
                <div class="row justify-content-between">
                    <div class="col-lg-6">
                        <div class="row align-items-center">
                            <div class="col-lg-6">
                                <form class="app-search d-none d-md-block me-auto">
                                    <div class="position-relative">
                                        <input type="search" class="form-control" placeholder="Search promos or codes" autocomplete="off" value="">
                                        <iconify-icon icon="solar:magnifer-broken" class="search-widget-icon"></iconify-icon>
                                    </div>
                                </form>
                            </div>
                            <div class="col-lg-4">
                                <h5 class="text-dark fw-medium mb-0">48 <span class="text-muted">Promos</span></h5>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <div class="text-md-end mt-3 mt-md-0">
                            <button type="button" class="btn btn-outline-secondary me-1"><i class="bx bx-cog me-1"></i>Settings</button>
                            <button type="button" class="btn btn-outline-secondary me-1"><i class="bx bx-filter-alt me-1"></i> Filters</button>
                            <button type="button" class="btn btn-success me-1"><i class="bx bx-plus"></i> New Promo</button>
                        </div>
                    </div><!-- end col-->
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Promo spotlight cards -->
<div class="row">
  <!-- Active Promos -->
  <div class="col-md-6 col-xl-3">
    <div class="card card-animate overflow-hidden bg-success-subtle">
      <div class="position-absolute end-0 top-0 p-3">
        <div class="avatar-md bg-success rounded-circle">
          <iconify-icon icon="solar:check-circle-bold-duotone" class="fs-32 text-white avatar-title"></iconify-icon>
        </div>
      </div>
      <div class="card-body" style="z-index: 1">
        <p class="text-muted text-uppercase fw-semibold fs-13 mb-2">{{ __('promos.active_promos') }}</p>
        <h3 class="mb-3 fw-bold">{{ number_format($stats['active'] ?? 0) }}</h3>
        <div class="d-flex align-items-center gap-2">
          <span class="badge bg-success-subtle text-success">
            <i class="mdi mdi-trending-up"></i> {{ __('promos.currently_running') }}
          </span>
        </div>
      </div>
    </div>
  </div>

  <!-- Scheduled Promos -->
  <div class="col-md-6 col-xl-3">
    <div class="card card-animate overflow-hidden bg-info-subtle">
      <div class="position-absolute end-0 top-0 p-3">
        <div class="avatar-md bg-info rounded-circle">
          <iconify-icon icon="solar:calendar-bold-duotone" class="fs-32 text-white avatar-title"></iconify-icon>
        </div>
      </div>
      <div class="card-body" style="z-index: 1">
        <p class="text-muted text-uppercase fw-semibold fs-13 mb-2">{{ __('promos.scheduled_promos') }}</p>
        <h3 class="mb-3 fw-bold">{{ number_format($stats['scheduled'] ?? 0) }}</h3>
        <div class="d-flex align-items-center gap-2">
          <span class="badge bg-info-subtle text-info">
            <i class="mdi mdi-clock-outline"></i> {{ __('promos.upcoming') }}
          </span>
        </div>
      </div>
    </div>
  </div>

  <!-- Expired Promos -->
  <div class="col-md-6 col-xl-3">
    <div class="card card-animate overflow-hidden bg-danger-subtle">
      <div class="position-absolute end-0 top-0 p-3">
        <div class="avatar-md bg-danger rounded-circle">
          <iconify-icon icon="solar:close-circle-bold-duotone" class="fs-32 text-white avatar-title"></iconify-icon>
        </div>
      </div>
      <div class="card-body" style="z-index: 1">
        <p class="text-muted text-uppercase fw-semibold fs-13 mb-2">{{ __('promos.expired_promos') }}</p>
        <h3 class="mb-3 fw-bold">{{ number_format($stats['expired'] ?? 0) }}</h3>
        <div class="d-flex align-items-center gap-2">
          <span class="badge bg-danger-subtle text-danger">
            <i class="mdi mdi-archive"></i> {{ __('promos.past_due') }}
          </span>
        </div>
      </div>
    </div>
  </div>

  <!-- Total Discount Given -->
  <div class="col-md-6 col-xl-3">
    <div class="card card-animate overflow-hidden bg-warning-subtle">
      <div class="position-absolute end-0 top-0 p-3">
        <div class="avatar-md bg-warning rounded-circle">
          <iconify-icon icon="solar:wallet-money-bold-duotone" class="fs-32 text-white avatar-title"></iconify-icon>
        </div>
      </div>
      <div class="card-body" style="z-index: 1">
        <p class="text-muted text-uppercase fw-semibold fs-13 mb-2">{{ __('promos.total_discount_given') }}</p>
        <h3 class="mb-3 fw-bold">SAR {{ number_format($stats['total_discount_given'] ?? 0, 2) }}</h3>
        <div class="d-flex align-items-center gap-2">
          <span class="badge bg-warning-subtle text-warning">
            <i class="mdi mdi-cash-multiple"></i> {{ __('promos.lifetime') }}
          </span>
        </div>
      </div>
    </div>
  </div>
</div>





<!-- Promo List -->
<div class="row">
    <div class="col-xl-12">
        <div class="card">
            <div class="d-flex card-header justify-content-between align-items-center border-bottom">
                <div>
                    <h4 class="card-title mb-0 fw-semibold">{{ __('promos.promo_codes_list') }}</h4>
                    <p class="text-muted mb-0 small">{{ __('promos.manage_promo_codes') }}</p>
                </div>
                <div class="d-flex align-items-center gap-2">
                    <form method="GET" action="{{ route('promos.index') }}" class="d-flex gap-2">
                        <input type="search" name="search" class="form-control" 
                               placeholder="{{ __('promos.search_codes') }}" 
                               value="{{ $filters['search'] ?? '' }}" 
                               style="min-width: 200px;">
                        <select name="status" class="form-select" style="min-width: 150px;">
                            <option value="">{{ __('promos.all_status') }}</option>
                            <option value="active" {{ ($filters['status'] ?? '') == 'active' ? 'selected' : '' }}>{{ __('promos.active') }}</option>
                            <option value="scheduled" {{ ($filters['status'] ?? '') == 'scheduled' ? 'selected' : '' }}>{{ __('promos.scheduled') }}</option>
                            <option value="expired" {{ ($filters['status'] ?? '') == 'expired' ? 'selected' : '' }}>{{ __('promos.expired') }}</option>
                            <option value="disabled" {{ ($filters['status'] ?? '') == 'disabled' ? 'selected' : '' }}>{{ __('promos.disabled') }}</option>
                        </select>
                        <button type="submit" class="btn btn-primary">
                            <i class="mdi mdi-filter-variant"></i>
                        </button>
                        <a href="{{ route('promos.index') }}" class="btn btn-soft-secondary">
                            <i class="mdi mdi-refresh"></i>
                        </a>
                    </form>
                    <a href="{{ route('promos.create') }}" class="btn btn-success">
                        <i class="bx bx-plus me-1"></i> {{ __('promos.new_promo') }}
                    </a>
                </div>
            </div>

            <div>
                <div class="table-responsive">
                    <table class="table align-middle mb-0 table-hover table-centered">
                        <thead class="bg-light-subtle">
                            <tr>
                                <th style="width: 20px;">
                                    <div class="form-check">
                                        <input type="checkbox" class="form-check-input" id="checkAll">
                                        <label class="form-check-label" for="checkAll"></label>
                                    </div>
                                </th>
                                <th>{{ __('promos.promo_and_code') }}</th>
                                <th>{{ __('promos.type') }}</th>
                                <th>{{ __('promos.discount') }}</th>
                                <th>{{ __('promos.scope') }}</th>
                                <th>{{ __('promos.start_date') }}</th>
                                <th>{{ __('promos.end_date') }}</th>
                                <th>{{ __('promos.status') }}</th>
                                <th>{{ __('promos.uses') }}</th>
                                <th>{{ __('promos.action') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($promoCodes as $promo)
                            <tr>
                                <td>
                                    <div class="form-check">
                                        <input type="checkbox" class="form-check-input" id="row{{ $promo['id'] }}">
                                        <label class="form-check-label" for="row{{ $promo['id'] }}">&nbsp;</label>
                                    </div>
                                </td>
                                <td>
                                    <div class="d-flex flex-column">
                                        <a href="{{ route('promos.show', $promo['id']) }}" class="text-dark fw-medium fs-15">
                                            {{ $promo['description'] ?? 'Promo Code' }}
                                        </a>
                                        <span class="text-muted fs-12">{{ __('promos.code') }}: <span class="text-dark fw-semibold">{{ $promo['code'] }}</span></span>
                                    </div>
                                </td>
                                <td>
                                    <span class="badge {{ $promo['discount_type'] == 'percentage' ? 'bg-primary-subtle text-primary' : 'bg-secondary-subtle text-secondary' }}">
                                        {{ ucfirst($promo['discount_type']) }}
                                    </span>
                                </td>
                                <td>
                                    @if($promo['discount_type'] == 'percentage')
                                        {{ $promo['discount_value'] }}%
                                        @if($promo['max_discount_amount'])
                                            <span class="text-muted fs-12">({{ __('promos.cap') }}: SAR {{ number_format($promo['max_discount_amount'], 2) }})</span>
                                        @endif
                                    @else
                                        SAR {{ number_format($promo['discount_value'], 2) }}
                                    @endif
                                </td>
                                <td>
                                    @if(!empty($promo['applicable_categories']))
                                        <span class="badge bg-light text-dark">{{ count($promo['applicable_categories']) }} {{ __('promos.categories') }}</span>
                                    @elseif(!empty($promo['applicable_services']))
                                        <span class="badge bg-light text-dark">{{ count($promo['applicable_services']) }} {{ __('promos.services') }}</span>
                                    @else
                                        <span class="badge bg-light text-dark">{{ __('promos.all_services') }}</span>
                                    @endif
                                </td>
                                <td>{{ $promo['valid_from']->format('d M Y') }}</td>
                                <td>{{ $promo['valid_until']->format('d M Y') }}</td>
                                <td>
                                    <span class="badge text-{{ $promo['status_class'] }} bg-{{ $promo['status_class'] }}-subtle fs-12">
                                        @if($promo['status_label'] == 'Active')
                                            <i class="bx bx-check-double"></i>
                                        @elseif($promo['status_label'] == 'Scheduled')
                                            <i class="bx bx-time"></i>
                                        @elseif($promo['status_label'] == 'Expired')
                                            <i class="bx bx-x"></i>
                                        @else
                                            <i class="bx bx-pause"></i>
                                        @endif
                                        {{ $promo['status_label'] }}
                                    </span>
                                </td>
                                <td>
                                    <span class="text-dark">{{ number_format($promo['used_count']) }}</span> / 
                                    {{ $promo['usage_limit'] ? number_format($promo['usage_limit']) : '∞' }}
                                </td>
                                <td>
                                    <div class="d-flex gap-2">
                                        <a href="{{ route('promos.show', $promo['id']) }}" 
                                           class="btn btn-light btn-sm" 
                                           title="{{ __('promos.view_details') }}">
                                            <iconify-icon icon="solar:eye-broken" class="align-middle fs-18"></iconify-icon>
                                        </a>
                                        <a href="{{ route('promos.edit', $promo['id']) }}" 
                                           class="btn btn-soft-primary btn-sm" 
                                           title="{{ __('promos.edit') }}">
                                            <iconify-icon icon="solar:pen-2-broken" class="align-middle fs-18"></iconify-icon>
                                        </a>
                                        <button onclick="toggleStatus({{ $promo['id'] }}, {{ $promo['is_active'] ? 'true' : 'false' }})" 
                                                class="btn btn-soft-{{ $promo['is_active'] ? 'warning' : 'success' }} btn-sm" 
                                                title="{{ $promo['is_active'] ? __('promos.disable') : __('promos.enable') }}">
                                            <iconify-icon icon="{{ $promo['is_active'] ? 'solar:pause-circle-broken' : 'solar:play-circle-broken' }}" 
                                                          class="align-middle fs-18"></iconify-icon>
                                        </button>
                                        <button onclick="deletePromo({{ $promo['id'] }}, '{{ $promo['code'] }}', {{ $promo['used_count'] }})" 
                                                class="btn btn-soft-danger btn-sm" 
                                                title="{{ __('promos.delete') }}">
                                            <iconify-icon icon="solar:trash-bin-minimalistic-2-broken" class="align-middle fs-18"></iconify-icon>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="10" class="text-center py-5">
                                    <div class="py-5">
                                        <iconify-icon icon="solar:ticket-broken" class="fs-48 text-muted mb-3"></iconify-icon>
                                        <h5 class="text-muted">{{ __('promos.no_promo_codes_found') }}</h5>
                                        <p class="text-muted">{{ __('promos.create_first_promo') }}</p>
                                        <a href="{{ route('promos.create') }}" class="btn btn-primary mt-3">
                                            <i class="bx bx-plus me-1"></i> {{ __('promos.create_promo') }}
                                        </a>
                                    </div>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <!-- end table-responsive -->
            </div>

            <div class="card-footer border-top">
                @if($pagination['last_page'] > 1)
                <nav aria-label="Page navigation">
                    <ul class="pagination justify-content-end mb-0">
                        {{-- Previous Button --}}
                        <li class="page-item {{ $pagination['current_page'] == 1 ? 'disabled' : '' }}">
                            <a class="page-link" href="{{ route('promos.index', array_merge($filters, ['page' => $pagination['current_page'] - 1])) }}">
                                {{ __('promos.previous') }}
                            </a>
                        </li>

                        {{-- Page Numbers --}}
                        @for($i = 1; $i <= $pagination['last_page']; $i++)
                            @if($i == 1 || $i == $pagination['last_page'] || abs($i - $pagination['current_page']) <= 2)
                                <li class="page-item {{ $i == $pagination['current_page'] ? 'active' : '' }}">
                                    <a class="page-link" href="{{ route('promos.index', array_merge($filters, ['page' => $i])) }}">
                                        {{ $i }}
                                    </a>
                                </li>
                            @elseif(abs($i - $pagination['current_page']) == 3)
                                <li class="page-item disabled">
                                    <span class="page-link">...</span>
                                </li>
                            @endif
                        @endfor

                        {{-- Next Button --}}
                        <li class="page-item {{ $pagination['current_page'] == $pagination['last_page'] ? 'disabled' : '' }}">
                            <a class="page-link" href="{{ route('promos.index', array_merge($filters, ['page' => $pagination['current_page'] + 1])) }}">
                                {{ __('promos.next') }}
                            </a>
                        </li>
                    </ul>
                </nav>
                @endif

                <div class="text-muted text-center mt-2 small">
                    {{ __('promos.showing_promo_codes', ['from' => $pagination['from'] ?? 0, 'to' => $pagination['to'] ?? 0, 'total' => $pagination['total']]) }}
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@section('script')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
// Toggle promo code status
function toggleStatus(promoId, currentStatus) {
    const action = currentStatus ? '{{ __('promos.disable') }}' : '{{ __('promos.enable') }}';
    const actionLower = action.toLowerCase();
    const title = currentStatus ? '{{ __('promos.disable_promo_title') }}' : '{{ __('promos.enable_promo_title') }}';
    const text = currentStatus ? '{{ __('promos.disable_promo_text') }}' : '{{ __('promos.enable_promo_text') }}';
    const subText = currentStatus ? '{{ __('promos.prevent_customers_using') }}' : '{{ __('promos.allow_customers_using') }}';
    const confirmText = currentStatus ? '{{ __('promos.yes_disable') }}' : '{{ __('promos.yes_enable') }}';
    
    Swal.fire({
        title: title,
        html: `<p class="mb-2">${text}</p>
               <p class="text-muted small">${subText}</p>`,
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: currentStatus ? '#f1b44c' : '#34c38f',
        cancelButtonColor: '#6c757d',
        confirmButtonText: confirmText,
        cancelButtonText: '{{ __('promos.cancel') }}'
    }).then((result) => {
        if (result.isConfirmed) {
            // Show loading
            Swal.fire({
                title: '{{ __('promos.processing') }}',
                allowOutsideClick: false,
                allowEscapeKey: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });

            // Make AJAX request
            fetch(`/promos/${promoId}/toggle-status`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    Swal.fire({
                        icon: 'success',
                        title: '{{ __('promos.success') }}',
                        text: data.message,
                        timer: 2000,
                        showConfirmButton: false
                    }).then(() => {
                        location.reload();
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: '{{ __('promos.error') }}',
                        text: data.message || '{{ __('promos.failed_update_status') }}'
                    });
                }
            })
            .catch(error => {
                console.error('Error:', error);
                Swal.fire({
                    icon: 'error',
                    title: '{{ __('promos.error') }}',
                    text: '{{ __('promos.error_updating_status') }}'
                });
            });
        }
    });
}

// Delete promo code
function deletePromo(promoId, promoCode, usedCount) {
    const deleteText = '{{ __('promos.delete_promo_text', ['code' => ':CODE:']) }}'.replace(':CODE:', promoCode);
    let warningHtml = `<p class="mb-2">${deleteText}</p>`;
    
    if (usedCount > 0) {
        const usedTimesMsg = '{{ __('promos.used_times', ['count' => ':COUNT:']) }}'.replace(':COUNT:', usedCount);
        warningHtml += `
            <div class="alert alert-danger mt-3 mb-2">
                <i class="mdi mdi-alert-circle-outline me-1"></i>
                <strong>{{ __('promos.warning') }}:</strong> ${usedTimesMsg}
            </div>
            <p class="text-muted small">{{ __('promos.cannot_delete_used') }}</p>
        `;
    } else {
        warningHtml += `<p class="text-muted small">{{ __('promos.action_cannot_undone') }}</p>`;
    }
    
    Swal.fire({
        title: '{{ __('promos.delete_promo_title') }}',
        html: warningHtml,
        icon: usedCount > 0 ? 'error' : 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#6c757d',
        confirmButtonText: '{{ __('promos.yes_delete_it') }}',
        cancelButtonText: '{{ __('promos.cancel') }}',
        allowOutsideClick: false
    }).then((result) => {
        if (result.isConfirmed) {
            // Show loading
            Swal.fire({
                title: '{{ __('promos.deleting') }}',
                allowOutsideClick: false,
                allowEscapeKey: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });

            // Make AJAX request
            fetch(`/promos/${promoId}`, {
                method: 'DELETE',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    Swal.fire({
                        icon: 'success',
                        title: '{{ __('promos.deleted') }}',
                        text: data.message,
                        timer: 2000,
                        showConfirmButton: false
                    }).then(() => {
                        location.reload();
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: '{{ __('promos.cannot_delete') }}',
                        html: data.message,
                        confirmButtonColor: '#3085d6'
                    });
                }
            })
            .catch(error => {
                console.error('Error:', error);
                Swal.fire({
                    icon: 'error',
                    title: '{{ __('promos.error') }}',
                    text: '{{ __('promos.error_deleting') }}'
                });
            });
        }
    });
}

// Select all checkboxes
document.getElementById('checkAll')?.addEventListener('change', function() {
    const checkboxes = document.querySelectorAll('tbody input[type="checkbox"]');
    checkboxes.forEach(checkbox => {
        checkbox.checked = this.checked;
    });
});
</script>
@endsection
