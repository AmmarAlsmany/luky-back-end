@extends('layouts.vertical', ['title' => 'Coupons Add'])

@section('css')
@vite(['node_modules/flatpickr/dist/flatpickr.min.css', 'node_modules/choices.js/public/assets/styles/choices.min.css'])
@endsection

@section('content')

<div class="row g-4">
<!-- Compact: Create Promotion / Coupon (Essentials) -->



  <div class="card">
    <div class="card-body">
      <!-- 1) Basics -->
      <h6 class="text-uppercase text-muted mb-3">Basics</h6>
      <div class="row g-3">
        <div class="col-md-6">
          <label class="form-label">Title<span class="text-danger">*</span></label>
          <input type="text" class="form-control" placeholder="Autumn Sale — 20% OFF" required>
        </div>
        <div class="col-md-6">
          <label class="form-label">Coupon Code<span class="text-danger">*</span></label>
          <input type="text" class="form-control text-uppercase" placeholder="AUTUMN20" maxlength="24" required>
        </div>
      </div>

      <hr class="my-4">

      <!-- 2) Status & Schedule -->
      <h6 class="text-uppercase text-muted mb-3">Status & Schedule</h6>
      <div class="row g-3">
        <div class="col-md-4">
          <label class="form-label d-block">Status</label>
          <div class="d-flex gap-3">
            <label class="form-check m-0">
              <input class="form-check-input" type="radio" name="status" checked>
              <span class="form-check-label">Active</span>
            </label>
            <label class="form-check m-0">
              <input class="form-check-input" type="radio" name="status">
              <span class="form-check-label">Inactive</span>
            </label>
          </div>
        </div>
        <div class="col-md-4">
          <label class="form-label">Start Date<span class="text-danger">*</span></label>
          <input type="date" class="form-control" required>
        </div>
        <div class="col-md-4">
          <label class="form-label">End Date<span class="text-danger">*</span></label>
          <input type="date" class="form-control" required>
        </div>
      </div>

      <hr class="my-4">

      <!-- 3) Discount -->
      <h6 class="text-uppercase text-muted mb-3">Discount</h6>
      <div class="row g-3">
        <div class="col-md-6">
          <label class="form-label">Type<span class="text-danger">*</span></label>
          <select class="form-select" required>
            <option selected>Percentage</option>
            <option>Fixed Amount</option>
            <option>Free Shipping</option>
          </select>
        </div>
        <div class="col-md-3">
          <label class="form-label">Percent (%)</label>
          <input type="number" class="form-control" min="1" max="100" placeholder="e.g., 20">
        </div>
        <div class="col-md-3">
          <label class="form-label">Fixed (SAR)</label>
          <input type="number" class="form-control" min="1" placeholder="e.g., 50">
        </div>
        <div class="col-md-6">
          <label class="form-label">Max Discount Cap (SAR)</label>
          <input type="number" class="form-control" placeholder="Optional — for % type">
        </div>
        <div class="col-md-6">
          <label class="form-label">Minimum Order (SAR)</label>
          <input type="number" class="form-control" placeholder="Optional">
        </div>
      </div>

      <hr class="my-4">

      <!-- 4) Scope (Categories & Services) -->
      <h6 class="text-uppercase text-muted mb-3">Scope</h6>
      <div class="row g-3">
        <div class="col-md-6">
        <label class="form-label">Categories<span class="text-danger">*</span></label>
        <select class="form-select" multiple size="6" required>
            <option>Inspection</option>
            <option>Mapping</option>
            <option>Survey</option>
            <option>Maintenance</option>
            <option>Training</option>
            <option>Beauty Parlor</option>
        </select>
        <div class="form-text">Hold Ctrl/Cmd to select multiple.</div>
        </div>

        <div class="col-md-6">
        <label class="form-label">Services<span class="text-danger">*</span></label>
        <select class="form-select" multiple size="6" required>

            
            <option>Drone Roof Inspection</option>
            <option>Thermal Leak Detection</option>
            <option>Construction Mapping</option>
            <option>Asset 3D Modeling</option>
            <option>Powerline Survey</option>


        
        </select>
        </div>
      </div>

      <hr class="my-4">



    <!-- Actions -->
    <div class="card-footer d-flex justify-content-end gap-2">
      <a href="#!" class="btn btn-light">Reset</a>
      <a href="#!" class="btn btn-outline-secondary">Save Draft</a>
      <a href="#!" class="btn btn-primary">Create Coupon</a>
    </div>
  </div>


</div>


@endsection

@section('script-bottom')
@vite(['resources/js/pages/coupons-add.js'])
@endsection