@extends('layouts.vertical', ['title' => 'Ads & Banners'])

@section('css')
@vite(['node_modules/choices.js/public/assets/styles/choices.min.css'])
<style>
  .banner-option .banner-preview {
    transition: all 0.3s ease;
    cursor: pointer;
  }
  
  .banner-option .banner-preview:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 15px rgba(0,0,0,0.1);
  }
  
  .banner-option .banner-preview.border-primary {
    box-shadow: 0 0 0 3px rgba(13, 110, 253, 0.25);
  }
  
  .banner-preview-container {
    min-height: 350px;
  }
  
  #banner-preview {
    transition: all 0.3s ease;
  }
  
  .cursor-pointer {
    cursor: pointer;
  }
  
  .form-control:focus {
    border-color: #667eea;
    box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
  }
  
  .btn-success {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    border: none;
  }
  
  .btn-success:hover {
    background: linear-gradient(135deg, #5a6fd8 0%, #6a4190 100%);
    transform: translateY(-1px);
  }
  
  /* Existing ads section styles */
  .card.h-100 {
    transition: all 0.3s ease;
  }
  
  .card.h-100:hover {
    transform: translateY(-5px);
    box-shadow: 0 8px 25px rgba(0,0,0,0.15);
  }
  
  .badge {
    font-size: 0.75em;
    padding: 0.5em 0.75em;
  }
  
  .toast {
    min-width: 300px;
  }
  
  /* Status badge colors */
  .badge.bg-success {
    background: linear-gradient(135deg, #28a745 0%, #20c997 100%) !important;
  }
  
  .badge.bg-warning {
    background: linear-gradient(135deg, #ffc107 0%, #fd7e14 100%) !important;
    color: #000 !important;
  }
  
  .badge.bg-danger {
    background: linear-gradient(135deg, #dc3545 0%, #e83e8c 100%) !important;
  }
  
  /* Banner preview styles - fixed size, no drag/resize */
  #banner-preview {
    position: relative;
    overflow: hidden;
    cursor: default;
  }
  
  /* Preview text positioning */
  #banner-preview .position-absolute {
    pointer-events: none;
    user-select: none;
  }
  
  /* Instructions overlay */
  .preview-instructions {
    position: absolute;
    top: 10px;
    left: 10px;
    background: rgba(0, 0, 0, 0.7);
    color: white;
    padding: 8px 12px;
    border-radius: 5px;
    font-size: 0.8em;
    z-index: 100;
    opacity: 0;
    transition: opacity 0.3s ease;
  }
  
  #banner-preview:hover .preview-instructions {
    opacity: 1;
  }
  
  /* Color picker styles */
  .form-control-color {
    width: 50px;
    height: 38px;
    padding: 0;
    border: 1px solid #ced4da;
    border-radius: 0.375rem 0 0 0.375rem;
  }
  
  .form-control-color::-webkit-color-swatch-wrapper {
    padding: 0;
  }
  
  .form-control-color::-webkit-color-swatch {
    border: none;
    border-radius: 0.25rem;
  }
  
  #reset_title_color {
    border-radius: 0 0.375rem 0.375rem 0;
    border-left: 0;
  }
  
  #reset_title_color:hover {
    background-color: #e9ecef;
    border-color: #ced4da;
  }
  
  /* Draggable text elements */
  .text-resize-handle {
    transition: all 0.2s ease;
  }
  
  .text-resize-handle:hover {
    transform: scale(1.3);
    background: #28a745 !important;
  }
  
  /* Text element styling */
  #banner-preview > div[id^="preview-"] {
    display: flex;
    align-items: center;
    justify-content: center;
    text-align: center;
    word-wrap: break-word;
    overflow: hidden;
  }
  
  /* Ensure text elements stay within banner */
  #banner-preview {
    overflow: hidden;
  }
</style>
@endsection

@section('content')

<!-- Create Ad & Banner -->
<div class="row" id="create-ad-banner">
  <div class="col-lg-12">
    <div class="card">
      <div class="card-header">
        <h4 class="card-title d-flex align-items-center gap-1">
          <iconify-icon icon="solar:advertising-bold-duotone" class="text-primary fs-20"></iconify-icon>
          Create Ad & Banner
        </h4>
      </div>

      <div class="card-body">
        <form id="ad-banner-form">
          <div class="row g-4">
            <!-- LEFT: Form Fields -->
            <div class="col-lg-8">
            <div class="row g-4">
                <!-- Banner Selection -->
                <div class="col-12">
                  <label class="form-label">Choose Banner Background</label>
                  <div class="row g-3">
                    <div class="col-md-3">
                      <div class="banner-option" data-banner="b1.png">
                        <div class="banner-preview border rounded p-2 text-center cursor-pointer">
                          <img src="/images/banners/b1.png" alt="Banner 1" class="img-fluid rounded mb-2" style="height: 120px; object-fit: cover;">
                          <small class="text-muted">Banner 1</small>
                        </div>
                      </div>
                    </div>
                    <div class="col-md-3">
                      <div class="banner-option" data-banner="b2.png">
                        <div class="banner-preview border rounded p-2 text-center cursor-pointer">
                          <img src="/images/banners/b2.png" alt="Banner 2" class="img-fluid rounded mb-2" style="height: 120px; object-fit: cover;">
                          <small class="text-muted">Banner 2</small>
                        </div>
                      </div>
                    </div>
                    <div class="col-md-3">
                      <div class="banner-option" data-banner="b3.png">
                        <div class="banner-preview border rounded p-2 text-center cursor-pointer">
                          <img src="/images/banners/b3.png" alt="Banner 3" class="img-fluid rounded mb-2" style="height: 120px; object-fit: cover;">
                          <small class="text-muted">Banner 3</small>
                        </div>
                      </div>
                    </div>
                    <div class="col-md-3">
                      <div class="banner-option" data-banner="b4.png">
                        <div class="banner-preview border rounded p-2 text-center cursor-pointer">
                          <img src="/images/banners/b4.png" alt="Banner 4" class="img-fluid rounded mb-2" style="height: 120px; object-fit: cover;">
                          <small class="text-muted">Banner 4</small>
                        </div>
                      </div>
                    </div>
                  </div>
                  <input type="hidden" id="selected_banner" name="selected_banner" value="">
                </div>
                 <!-- Main Title -->
                 <div class="col-md-3">
                   <label for="main_title" class="form-label">Main Title</label>
                   <input type="text" id="main_title" name="main_title" class="form-control" 
                          placeholder="Unlock Your Beauty Potential" required>
                 </div>
                 
                 <!-- Title Color & Font -->
                 <div class="col-md-3">
                   <label for="title_color" class="form-label">Title Color</label>
                   <div class="input-group">
                     <input type="color" id="title_color" name="title_color" class="form-control form-control-color" 
                            value="#ffffff" title="Choose title color">
                     <button type="button" class="btn btn-outline-secondary" id="reset_title_color" title="Reset to white">
                       <iconify-icon icon="solar:refresh-bold-duotone"></iconify-icon>
                     </button>
                   </div>
                 </div>
                 
                 <div class="col-md-3">
                   <label for="title_font" class="form-label">Title Font</label>
                   <select id="title_font" name="title_font" class="form-select">
                     <option value="Arial, sans-serif">Arial</option>
                     <option value="Helvetica, sans-serif">Helvetica</option>
                     <option value="Georgia, serif">Georgia</option>
                     <option value="Times New Roman, serif">Times New Roman</option>
                     <option value="Courier New, monospace">Courier New</option>
                     <option value="Verdana, sans-serif">Verdana</option>
                     <option value="Impact, sans-serif">Impact</option>
                     <option value="Comic Sans MS, cursive">Comic Sans MS</option>
                     <option value="Trebuchet MS, sans-serif">Trebuchet MS</option>
                     <option value="Palatino, serif">Palatino</option>
                   </select>
                 </div>
                 
                 <div class="col-md-3">
                   <label for="title_size" class="form-label">Title Size</label>
                   <select id="title_size" name="title_size" class="form-select">
                     <option value="1rem">Small</option>
                     <option value="1.5rem">Medium</option>
                     <option value="2rem" selected>Large</option>
                     <option value="2.5rem">Extra Large</option>
                     <option value="3rem">Huge</option>
                   </select>
                 </div>

                <!-- Provider Name -->
                <div class="col-md-3">
                  <label for="provider_name" class="form-label">Provider Name</label>
                  <input type="text" id="provider_name" name="provider_name" class="form-control" 
                         placeholder="Beauty Salon Name" required>
                </div>
                
                <!-- Provider Color & Font -->
                <div class="col-md-3">
                  <label for="provider_color" class="form-label">Provider Color</label>
                  <div class="input-group">
                    <input type="color" id="provider_color" name="provider_color" class="form-control form-control-color" 
                           value="#ffffff" title="Choose provider color">
                    <button type="button" class="btn btn-outline-secondary" id="reset_provider_color" title="Reset to white">
                      <iconify-icon icon="solar:refresh-bold-duotone"></iconify-icon>
                    </button>
                  </div>
                </div>
                
                <div class="col-md-3">
                  <label for="provider_font" class="form-label">Provider Font</label>
                  <select id="provider_font" name="provider_font" class="form-select">
                    <option value="Arial, sans-serif">Arial</option>
                    <option value="Helvetica, sans-serif">Helvetica</option>
                    <option value="Georgia, serif">Georgia</option>
                    <option value="Times New Roman, serif">Times New Roman</option>
                    <option value="Courier New, monospace">Courier New</option>
                    <option value="Verdana, sans-serif">Verdana</option>
                    <option value="Impact, sans-serif">Impact</option>
                    <option value="Comic Sans MS, cursive">Comic Sans MS</option>
                    <option value="Trebuchet MS, sans-serif">Trebuchet MS</option>
                    <option value="Palatino, serif">Palatino</option>
                  </select>
                </div>
                
                <div class="col-md-3">
                  <label for="provider_size" class="form-label">Provider Size</label>
                  <select id="provider_size" name="provider_size" class="form-select">
                    <option value="0.8rem">Small</option>
                    <option value="1rem">Medium</option>
                    <option value="1.2rem" selected>Large</option>
                    <option value="1.5rem">Extra Large</option>
                    <option value="1.8rem">Huge</option>
                  </select>
                </div>

                <!-- Offer Text -->
                <div class="col-md-2">
                  <label for="offer_text" class="form-label">Offer Text</label>
                  <input type="text" id="offer_text" name="offer_text" class="form-control" 
                         placeholder="50% Off" required>
                </div>

                <div class="col-md-2">
                  <label for="offer_font" class="form-label">Offer Font</label>
                  <select id="offer_font" name="offer_font" class="form-select">
                    <option value="Arial, sans-serif">Arial</option>
                    <option value="Helvetica, sans-serif">Helvetica</option>
                    <option value="Georgia, serif">Georgia</option>
                    <option value="Times New Roman, serif">Times New Roman</option>
                    <option value="Courier New, monospace">Courier New</option>
                    <option value="Verdana, sans-serif">Verdana</option>
                    <option value="Impact, sans-serif">Impact</option>
                    <option value="Comic Sans MS, cursive">Comic Sans MS</option>
                    <option value="Trebuchet MS, sans-serif">Trebuchet MS</option>
                    <option value="Palatino, serif">Palatino</option>
                  </select>
                </div>
                <!-- Offer Background Color -->
                <div class="col-md-2">
                  <label for="offer_bg_color" class="form-label">Offer Background</label>
                  <div class="input-group">
                    <input type="color" id="offer_bg_color" name="offer_bg_color" class="form-control form-control-color" 
                           value="#ffc107" title="Choose offer background color">
                    <button type="button" class="btn btn-outline-secondary" id="reset_offer_bg_color" title="Reset to yellow">
                      <iconify-icon icon="solar:refresh-bold-duotone"></iconify-icon>
                    </button>
                  </div>
                </div>
                
                <!-- Offer Text Color & Font -->
                <div class="col-md-2">
                  <label for="offer_text_color" class="form-label">Offer Text Color</label>
                  <div class="input-group">
                    <input type="color" id="offer_text_color" name="offer_text_color" class="form-control form-control-color" 
                           value="#000000" title="Choose offer text color">
                    <button type="button" class="btn btn-outline-secondary" id="reset_offer_text_color" title="Reset to black">
                      <iconify-icon icon="solar:refresh-bold-duotone"></iconify-icon>
                    </button>
                  </div>
                </div>
                
                
                
                <div class="col-md-2">
                  <label for="offer_size" class="form-label">Offer Size</label>
                  <select id="offer_size" name="offer_size" class="form-select">
                    <option value="0.7rem">Small</option>
                    <option value="0.9rem">Medium</option>
                    <option value="1.1rem" selected>Large</option>
                    <option value="1.3rem">Extra Large</option>
                    <option value="1.5rem">Huge</option>
                  </select>
                </div>


                 <!-- Date Range -->
                 <div class="col-md-6">
                   <label for="start_date" class="form-label">Start Date</label>
                   <input type="date" id="start_date" name="start_date" class="form-control" required>
                 </div>

              <div class="col-md-6">
                   <label for="end_date" class="form-label">End Date</label>
                   <input type="date" id="end_date" name="end_date" class="form-control" required>
                 </div>

                 
              </div>
            </div>

            <!-- RIGHT: Banner Preview -->
            <div class="col-lg-4">
              <label class="form-label">Interactive Banner Preview</label>
              <div class="banner-preview-container border rounded p-3 bg-light">
                <div id="banner-preview" class="position-relative rounded overflow-hidden" style="height: 300px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                  <!-- Instructions overlay -->
                  <div class="preview-instructions">
                    <iconify-icon icon="solar:hand-stars-bold-duotone" class="me-1"></iconify-icon>
                    Drag text to move • Corner handles to resize
                  </div>
                  
                  <!-- Preview content -->
                  <div class="position-absolute top-0 start-0 w-100 h-100 d-flex flex-column justify-content-center align-items-center text-white p-3">
                    
                  </div>
                </div>
                <div class="mt-3 text-center">
                  <small class="text-muted">
                    <iconify-icon icon="solar:magic-stick-bold-duotone" class="me-1"></iconify-icon>
                    Interactive Live Preview
                  </small>
                </div>
              </div>
            </div>
          </div>
        </form>
      </div>

      <!-- Action buttons -->
      <div class="card-footer d-flex justify-content-end gap-2">
        <button type="button" id="btn_preview_ad" class="btn btn-info">
          <iconify-icon icon="solar:eye-bold-duotone" class="me-1"></iconify-icon>
          Preview
        </button>
        <button type="button" id="btn_reset_ad" class="btn btn-light">Reset</button>
        <button type="button" id="btn_create_ad" class="btn btn-success">
          <iconify-icon icon="solar:add-circle-bold-duotone" class="me-1"></iconify-icon>
          Create Ad
        </button>
      </div>
    </div>
  </div>
              </div>

<!-- Existing Ads & Banners -->
<div class="row" id="existing-ads-banners">
  <div class="col-lg-12">
    <div class="card">
      <div class="card-header">
        <h4 class="card-title d-flex align-items-center gap-1">
          <iconify-icon icon="solar:gallery-bold-duotone" class="text-primary fs-20"></iconify-icon>
          Existing Ads & Banners
        </h4>
              </div>

      <div class="card-body">
        <div class="row g-4" id="ads-list">
          <!-- Ad 1 -->
          <div class="col-lg-6 col-xl-4" data-ad-id="1">
            <div class="card h-100 border">
              <div class="position-relative">
                <img src="/images/banners/b1.png" class="card-img-top" alt="Banner" style="height: 200px; object-fit: cover;">
                <div class="position-absolute top-0 end-0 m-2">
                  <span class="badge bg-success">Active</span>
                </div>
              </div>
              <div class="card-body d-flex flex-column">
                <h5 class="card-title">Unlock Your Beauty Potential</h5>
                <p class="card-text text-muted mb-2">
                  <iconify-icon icon="solar:buildings-bold-duotone" class="me-1"></iconify-icon>
                  <strong>Provider:</strong> Beauty Palace Salon
                </p>
                <p class="card-text text-muted mb-2">
                  <iconify-icon icon="solar:tag-bold-duotone" class="me-1"></iconify-icon>
                  <strong>Offer:</strong> 50% Off All Services
                </p>
                <p class="card-text text-muted mb-2">
                  <iconify-icon icon="solar:calendar-bold-duotone" class="me-1"></iconify-icon>
                  <strong>Start:</strong> 2024-01-15
                </p>
                <p class="card-text text-muted mb-3">
                  <iconify-icon icon="solar:calendar-mark-bold-duotone" class="me-1"></iconify-icon>
                  <strong>End:</strong> 2024-02-15
                </p>
                <div class="mt-auto">
                  <div class="d-flex gap-2">
                    <button class="btn btn-sm btn-outline-primary flex-fill" onclick="editAd(1)">
                      <iconify-icon icon="solar:pen-bold-duotone" class="me-1"></iconify-icon>
                      Edit
                    </button>
                    <button class="btn btn-sm btn-outline-danger flex-fill" onclick="deleteAd(1)">
                      <iconify-icon icon="solar:trash-bin-trash-bold-duotone" class="me-1"></iconify-icon>
                      Delete
                    </button>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <!-- Ad 2 -->
          <div class="col-lg-6 col-xl-4" data-ad-id="2">
            <div class="card h-100 border">
              <div class="position-relative">
                <img src="/images/banners/b2.png" class="card-img-top" alt="Banner" style="height: 200px; object-fit: cover;">
                <div class="position-absolute top-0 end-0 m-2">
                  <span class="badge bg-warning">Upcoming</span>
                </div>
              </div>
              <div class="card-body d-flex flex-column">
                <h5 class="card-title">Summer Glow Special</h5>
                <p class="card-text text-muted mb-2">
                  <iconify-icon icon="solar:buildings-bold-duotone" class="me-1"></iconify-icon>
                  <strong>Provider:</strong> Glamour Studio
                </p>
                <p class="card-text text-muted mb-2">
                  <iconify-icon icon="solar:tag-bold-duotone" class="me-1"></iconify-icon>
                  <strong>Offer:</strong> 30% Off Facials
                </p>
                <p class="card-text text-muted mb-2">
                  <iconify-icon icon="solar:calendar-bold-duotone" class="me-1"></iconify-icon>
                  <strong>Start:</strong> 2024-03-01
                </p>
                <p class="card-text text-muted mb-3">
                  <iconify-icon icon="solar:calendar-mark-bold-duotone" class="me-1"></iconify-icon>
                  <strong>End:</strong> 2024-03-31
                </p>
                <div class="mt-auto">
                  <div class="d-flex gap-2">
                    <button class="btn btn-sm btn-outline-primary flex-fill" onclick="editAd(2)">
                      <iconify-icon icon="solar:pen-bold-duotone" class="me-1"></iconify-icon>
                      Edit
                    </button>
                    <button class="btn btn-sm btn-outline-danger flex-fill" onclick="deleteAd(2)">
                      <iconify-icon icon="solar:trash-bin-trash-bold-duotone" class="me-1"></iconify-icon>
                      Delete
              </button>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <!-- Ad 3 -->
          <div class="col-lg-6 col-xl-4" data-ad-id="3">
            <div class="card h-100 border">
              <div class="position-relative">
                <img src="/images/banners/b3.png" class="card-img-top" alt="Banner" style="height: 200px; object-fit: cover;">
                <div class="position-absolute top-0 end-0 m-2">
                  <span class="badge bg-danger">Expired</span>
                </div>
              </div>
              <div class="card-body d-flex flex-column">
                <h5 class="card-title">New Year Beauty Package</h5>
                <p class="card-text text-muted mb-2">
                  <iconify-icon icon="solar:buildings-bold-duotone" class="me-1"></iconify-icon>
                  <strong>Provider:</strong> Elite Beauty Center
                </p>
                <p class="card-text text-muted mb-2">
                  <iconify-icon icon="solar:tag-bold-duotone" class="me-1"></iconify-icon>
                  <strong>Offer:</strong> Buy 2 Get 1 Free
                </p>
                <p class="card-text text-muted mb-2">
                  <iconify-icon icon="solar:calendar-bold-duotone" class="me-1"></iconify-icon>
                  <strong>Start:</strong> 2023-12-01
                </p>
                <p class="card-text text-muted mb-3">
                  <iconify-icon icon="solar:calendar-mark-bold-duotone" class="me-1"></iconify-icon>
                  <strong>End:</strong> 2023-12-31
                </p>
                <div class="mt-auto">
                  <div class="d-flex gap-2">
                    <button class="btn btn-sm btn-outline-primary flex-fill" onclick="editAd(3)">
                      <iconify-icon icon="solar:pen-bold-duotone" class="me-1"></iconify-icon>
                      Edit
                    </button>
                    <button class="btn btn-sm btn-outline-danger flex-fill" onclick="deleteAd(3)">
                      <iconify-icon icon="solar:trash-bin-trash-bold-duotone" class="me-1"></iconify-icon>
                      Delete
                    </button>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <!-- Ad 4 -->
          <div class="col-lg-6 col-xl-4" data-ad-id="4">
            <div class="card h-100 border">
              <div class="position-relative">
                <img src="/images/banners/b4.png" class="card-img-top" alt="Banner" style="height: 200px; object-fit: cover;">
                <div class="position-absolute top-0 end-0 m-2">
                  <span class="badge bg-success">Active</span>
                </div>
              </div>
              <div class="card-body d-flex flex-column">
                <h5 class="card-title">Valentine's Day Special</h5>
                <p class="card-text text-muted mb-2">
                  <iconify-icon icon="solar:buildings-bold-duotone" class="me-1"></iconify-icon>
                  <strong>Provider:</strong> Rose Beauty Spa
                </p>
                <p class="card-text text-muted mb-2">
                  <iconify-icon icon="solar:tag-bold-duotone" class="me-1"></iconify-icon>
                  <strong>Offer:</strong> Couples Package 40% Off
                </p>
                <p class="card-text text-muted mb-2">
                  <iconify-icon icon="solar:calendar-bold-duotone" class="me-1"></iconify-icon>
                  <strong>Start:</strong> 2024-02-01
                </p>
                <p class="card-text text-muted mb-3">
                  <iconify-icon icon="solar:calendar-mark-bold-duotone" class="me-1"></iconify-icon>
                  <strong>End:</strong> 2024-02-29
                </p>
                <div class="mt-auto">
                  <div class="d-flex gap-2">
                    <button class="btn btn-sm btn-outline-primary flex-fill" onclick="editAd(4)">
                      <iconify-icon icon="solar:pen-bold-duotone" class="me-1"></iconify-icon>
                      Edit
                    </button>
                    <button class="btn btn-sm btn-outline-danger flex-fill" onclick="deleteAd(4)">
                      <iconify-icon icon="solar:trash-bin-trash-bold-duotone" class="me-1"></iconify-icon>
                      Delete
                    </button>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>



@endsection

@section('script-bottom')
@vite(['resources/js/pages/app-ecommerce-product.js'])
<script>
  // Logo upload functionality
  (function () {
    const input = document.getElementById('logo_input');
    const btn = document.getElementById('change_logo_btn');
    const preview = document.getElementById('logo_preview');
    let lastUrl = null;

    btn.addEventListener('click', () => input.click());
    input.addEventListener('change', function () {
      const file = this.files && this.files[0];
      if (!file) return;
      if (lastUrl) URL.revokeObjectURL(lastUrl);
      lastUrl = URL.createObjectURL(file);
      preview.src = lastUrl;

      // TODO: jQuery AJAX upload if needed:
      // const fd = new FormData(); fd.append('logo', file);
      // $.ajax({ url:'/api/settings/logo', method:'POST', data:fd, processData:false, contentType:false });
    });
  })();
</script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
<script src="/js/adbanners.js"></script>

<!-- Preview Modal -->
<div class="modal fade" id="previewModal" tabindex="-1" aria-labelledby="previewModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="previewModalLabel">
          <iconify-icon icon="solar:eye-bold-duotone" class="me-2"></iconify-icon>
          Banner Preview
        </h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body text-center">
        <div id="modal-preview-container" class="d-inline-block">
          <!-- Preview content will be inserted here -->
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
        <button type="button" class="btn btn-primary" id="download-preview">
          <iconify-icon icon="solar:download-bold-duotone" class="me-1"></iconify-icon>
          Download
        </button>
      </div>
    </div>
  </div>
</div>

@endsection