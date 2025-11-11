@extends('layouts.vertical', ['title' => 'Promos List'])

@section('content')
<!-- Trigger (example) -->



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
    <div class="card bg-success-subtle">
      <div class="card-body">
        <div class="row">
          <div class="col-6">
            <div class="avatar-md bg-success rounded">
              <iconify-icon icon="solar:check-circle-bold-duotone" class="fs-32 text-white avatar-title"></iconify-icon>
            </div>
          </div>
          <div class="col-6 text-end">
            <p class="text-muted mb-0 text-truncate">Active Promos</p>
            <h3 class="text-dark mt-1 mb-0">12</h3>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Scheduled Promos -->
  <div class="col-md-6 col-xl-3">
    <div class="card bg-info-subtle">
      <div class="card-body">
        <div class="row">
          <div class="col-6">
            <div class="avatar-md bg-info rounded">
              <iconify-icon icon="solar:calendar-bold-duotone" class="fs-32 text-white avatar-title"></iconify-icon>
            </div>
          </div>
          <div class="col-6 text-end">
            <p class="text-muted mb-0 text-truncate">Scheduled Promos</p>
            <h3 class="text-dark mt-1 mb-0">5</h3>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Expired Promos -->
  <div class="col-md-6 col-xl-3">
    <div class="card bg-danger-subtle">
      <div class="card-body">
        <div class="row">
          <div class="col-6">
            <div class="avatar-md bg-danger rounded">
              <iconify-icon icon="solar:close-circle-bold-duotone" class="fs-32 text-white avatar-title"></iconify-icon>
            </div>
          </div>
          <div class="col-6 text-end">
            <p class="text-muted mb-0 text-truncate">Expired Promos</p>
            <h3 class="text-dark mt-1 mb-0">21</h3>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Total Discount Given -->
  <div class="col-md-6 col-xl-3">
    <div class="card bg-warning-subtle">
      <div class="card-body">
        <div class="row">
          <div class="col-6">
            <div class="avatar-md bg-warning rounded">
              <iconify-icon icon="solar:wallet-money-bold-duotone" class="fs-32 text-white avatar-title"></iconify-icon>
            </div>
          </div>
          <div class="col-6 text-end">
            <p class="text-muted mb-0 text-truncate">Total Discount Given</p>
            <h3 class="text-dark mt-1 mb-0">
              <img src="/images/luky/sar.png" alt="" class="pb-1" height="24" style="filter: grayscale(1); opacity: .8;">
              <span>128,450</span>
            </h3>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>





<!-- Promo List -->
<div class="row ">
    <div class="col-xl-12">
        <div class="card">
            <div class="d-flex card-header justify-content-between align-items-center">
                <div>
                    <h4 class="card-title">Promo List</h4>
                </div>
                <div class="d-flex align-items-center gap-2">
                    <div>
                        <input type="search" class="form-control" placeholder="Search promos or codes..." aria-label="Search">
                    </div>
                    <div class="dropdown">
                        <a href="#" class="dropdown-toggle btn btn-outline-light rounded" data-bs-toggle="dropdown" aria-expanded="false">
                            Filter
                        </a>
                        <div class="dropdown-menu dropdown-menu-end">
                            <a href="#!" class="dropdown-item">Active</a>
                            <a href="#!" class="dropdown-item">Scheduled</a>
                            <a href="#!" class="dropdown-item">Expired</a>
                            <a href="#!" class="dropdown-item">Disabled</a>
                        </div>
                    </div>
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
                                <th>Promo & Code</th>
                                <th>Type</th>
                                <th>Discount</th>
                                <th>Scope</th>
                                <th>Start Date</th>
                                <th>End Date</th>
                                <th>Status</th>
                                <th>Uses</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <!-- Row 1 -->
                            <tr>
                                <td>
                                    <div class="form-check">
                                        <input type="checkbox" class="form-check-input" id="row1">
                                        <label class="form-check-label" for="row1">&nbsp;</label>
                                    </div>
                                </td>
                                <td>
                                    <div class="d-flex flex-column">
                                        <a href="#!" class="text-dark fw-medium fs-15">Autumn Sale</a>
                                        <span class="text-muted fs-12">Code: <span class="text-dark">AUTUMN20</span></span>
                                    </div>
                                </td>
                                <td><span class="badge bg-primary-subtle text-primary">Percentage</span></td>
                                <td>20% <span class="text-muted fs-12">(Cap: SAR 100)</span></td>
                                <td>
                                    <span class="badge bg-light text-dark">Mapping</span>
                                    <span class="badge bg-light text-dark">Survey</span>
                                </td>
                                <td>10 Oct 2025</td>
                                <td>31 Dec 2025</td>
                                <td><span class="badge text-success bg-success-subtle fs-12"><i class="bx bx-check-double"></i> Active</span></td>
                                <td><span class="text-dark">134</span> / 1000</td>
                                <td>

                                    <div class="d-flex gap-2">
                                        <a href="#promoDetailModal" data-bs-toggle="modal" class="btn btn-light btn-sm" title="View"><iconify-icon icon="solar:eye-broken" class="align-middle fs-18"></iconify-icon></a>
                                        <a href="#!" class="btn btn-soft-primary btn-sm" title="Edit"><iconify-icon icon="solar:pen-2-broken" class="align-middle fs-18"></iconify-icon></a>
                                        <a href="#!" class="btn btn-soft-danger btn-sm" title="Delete"><iconify-icon icon="solar:trash-bin-minimalistic-2-broken" class="align-middle fs-18"></iconify-icon></a>
                                    </div>
                                </td>
                            </tr>

                            <!-- Row 2 -->
                            <tr>
                                <td>
                                    <div class="form-check">
                                        <input type="checkbox" class="form-check-input" id="row2">
                                        <label class="form-check-label" for="row2">&nbsp;</label>
                                    </div>
                                </td>
                                <td>
                                    <div class="d-flex flex-column">
                                        <a href="#!" class="text-dark fw-medium fs-15">Beauty Fest Weekend</a>
                                        <span class="text-muted fs-12">Code: <span class="text-dark">BEAUTY25</span></span>
                                    </div>
                                </td>
                                <td><span class="badge bg-primary-subtle text-primary">Percentage</span></td>
                                <td>25% <span class="text-muted fs-12">(Cap: SAR 75)</span></td>
                                <td>
                                    <span class="badge bg-light text-dark">Beauty Parlor</span>
                                    <span class="badge bg-light text-dark">Haircut &amp; Blow Dry</span>
                                </td>
                                <td>07 Nov 2025</td>
                                <td>10 Nov 2025</td>
                                <td><span class="badge text-info bg-info-subtle fs-12"><i class="bx bx-time"></i> Scheduled</span></td>
                                <td><span class="text-dark">0</span> / 500</td>
                                <td>
                                    <div class="d-flex gap-2">
                                        <a href="#promoDetailModal" data-bs-toggle="modal" class="btn btn-light btn-sm" title="View"><iconify-icon icon="solar:eye-broken" class="align-middle fs-18"></iconify-icon></a>
                                        <a href="#!" class="btn btn-soft-primary btn-sm" title="Edit"><iconify-icon icon="solar:pen-2-broken" class="align-middle fs-18"></iconify-icon></a>
                                        <a href="#!" class="btn btn-soft-danger btn-sm" title="Delete"><iconify-icon icon="solar:trash-bin-minimalistic-2-broken" class="align-middle fs-18"></iconify-icon></a>
                                    </div>
                                </td>
                            </tr>

                            <!-- Row 3 -->
                            <tr>
                                <td>
                                    <div class="form-check">
                                        <input type="checkbox" class="form-check-input" id="row3">
                                        <label class="form-check-label" for="row3">&nbsp;</label>
                                    </div>
                                </td>
                                <td>
                                    <div class="d-flex flex-column">
                                        <a href="#!" class="text-dark fw-medium fs-15">Fixed Saver</a>
                                        <span class="text-muted fs-12">Code: <span class="text-dark">SAVE50</span></span>
                                    </div>
                                </td>
                                <td><span class="badge bg-secondary-subtle text-secondary">Fixed</span></td>
                                <td>SAR 50</td>
                                <td>
                                    <span class="badge bg-light text-dark">Inspection</span>
                                    <span class="badge bg-light text-dark">Drone Roof Inspection</span>
                                </td>
                                <td>01 Sep 2025</td>
                                <td>30 Nov 2025</td>
                                <td><span class="badge text-success bg-success-subtle fs-12"><i class="bx bx-check-double"></i> Active</span></td>
                                <td><span class="text-dark">412</span> / 1000</td>
                                <td>
                                    <div class="d-flex gap-2">
                                        <a href="#promoDetailModal" data-bs-toggle="modal" class="btn btn-light btn-sm" title="View"><iconify-icon icon="solar:eye-broken" class="align-middle fs-18"></iconify-icon></a>
                                        <a href="#!" class="btn btn-soft-primary btn-sm" title="Edit"><iconify-icon icon="solar:pen-2-broken" class="align-middle fs-18"></iconify-icon></a>
                                        <a href="#!" class="btn btn-soft-danger btn-sm" title="Delete"><iconify-icon icon="solar:trash-bin-minimalistic-2-broken" class="align-middle fs-18"></iconify-icon></a>
                                    </div>
                                </td>
                            </tr>

                            <!-- Row 4 -->
                            <tr>
                                <td>
                                    <div class="form-check">
                                        <input type="checkbox" class="form-check-input" id="row4">
                                        <label class="form-check-label" for="row4">&nbsp;</label>
                                    </div>
                                </td>
                                <td>
                                    <div class="d-flex flex-column">
                                        <a href="#!" class="text-dark fw-medium fs-15">Free Ship Friday</a>
                                        <span class="text-muted fs-12">Code: <span class="text-dark">SHIPFREE</span></span>
                                    </div>
                                </td>
                                <td><span class="badge bg-success-subtle text-success">Free Shipping</span></td>
                                <td>—</td>
                                <td>
                                    <span class="badge bg-light text-dark">All Categories</span>
                                </td>
                                <td>05 Sep 2025</td>
                                <td>05 Sep 2025</td>
                                <td><span class="badge text-danger bg-danger-subtle fs-12"><i class="bx bx-x"></i> Expired</span></td>
                                <td><span class="text-dark">986</span> / ∞</td>
                                <td>
                                    <div class="d-flex gap-2">
                                        <a href="#promoDetailModal" data-bs-toggle="modal" class="btn btn-light btn-sm" title="View"><iconify-icon icon="solar:eye-broken" class="align-middle fs-18"></iconify-icon></a>
                                        <a href="#!" class="btn btn-soft-primary btn-sm" title="Edit"><iconify-icon icon="solar:pen-2-broken" class="align-middle fs-18"></iconify-icon></a>
                                        <a href="#!" class="btn btn-soft-danger btn-sm" title="Delete"><iconify-icon icon="solar:trash-bin-minimalistic-2-broken" class="align-middle fs-18"></iconify-icon></a>
                                    </div>
                                </td>
                            </tr>

                            <!-- Row 5 -->
                            <tr>
                                <td>
                                    <div class="form-check">
                                        <input type="checkbox" class="form-check-input" id="row5">
                                        <label class="form-check-label" for="row5">&nbsp;</label>
                                    </div>
                                </td>
                                <td>
                                    <div class="d-flex flex-column">
                                        <a href="#!" class="text-dark fw-medium fs-15">Training Starter</a>
                                        <span class="text-muted fs-12">Code: <span class="text-dark">TRAIN10</span></span>
                                    </div>
                                </td>
                                <td><span class="badge bg-primary-subtle text-primary">Percentage</span></td>
                                <td>10%</td>
                                <td>
                                    <span class="badge bg-light text-dark">Training</span>
                                </td>
                                <td>01 Aug 2025</td>
                                <td>01 Nov 2025</td>
                                <td><span class="badge text-secondary bg-secondary-subtle fs-12"><i class="bx bx-pause"></i> Disabled</span></td>
                                <td><span class="text-dark">59</span> / 200</td>
                                <td>
                                    <div class="d-flex gap-2">
                                       <a href="#promoDetailModal" data-bs-toggle="modal" class="btn btn-light btn-sm" title="View"><iconify-icon icon="solar:eye-broken" class="align-middle fs-18"></iconify-icon></a>
                                        <a href="#!" class="btn btn-soft-primary btn-sm" title="Edit"><iconify-icon icon="solar:pen-2-broken" class="align-middle fs-18"></iconify-icon></a>
                                        <a href="#!" class="btn btn-soft-danger btn-sm" title="Delete"><iconify-icon icon="solar:trash-bin-minimalistic-2-broken" class="align-middle fs-18"></iconify-icon></a>
                                    </div>
                                </td>
                            </tr>

                        </tbody>
                    </table>
                </div>
                <!-- end table-responsive -->
            </div>

            <div class="card-footer border-top">
                <nav aria-label="Page navigation">
                    <ul class="pagination justify-content-end mb-0">
                        <li class="page-item"><a class="page-link" href="javascript:void(0);">Previous</a></li>
                        <li class="page-item active"><a class="page-link" href="javascript:void(0);">1</a></li>
                        <li class="page-item"><a class="page-link" href="javascript:void(0);">2</a></li>
                        <li class="page-item"><a class="page-link" href="javascript:void(0);">3</a></li>
                        <li class="page-item"><a class="page-link" href="javascript:void(0);">Next</a></li>
                    </ul>
                </nav>
            </div>
        </div>
    </div>
</div>

@endsection
