@extends('layouts.vertical', ['title' => __('banners.ads_banners')])

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
    width: 600px;
    max-width: 100%;
    margin: 0 auto;
    transition: all 0.3s ease;
  }
  
  .cursor-pointer {
    cursor: pointer;
  }
  
  .form-control:focus {
    border-color: #667eea;
    box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
  }
  
  /* Choices.js dropdown fix for long provider names */
  .choices__list--dropdown {
    max-width: 100% !important;
    width: auto !important;
    min-width: 300px !important;
  }
  
  .choices__list--dropdown .choices__item {
    white-space: normal !important;
    word-wrap: break-word !important;
    padding: 10px 12px !important;
  }
  
  .choices__inner {
    min-height: 38px !important;
  }
  
  .choices[data-type*="select-one"] .choices__inner {
    padding-bottom: 7px !important;
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
          {{ __('banners.create_ad_banner') }}
        </h4>
      </div>

      <div class="card-body">
        <form id="ad-banner-form">
          <div class="col-12">
            <label class="form-label">{{ __('banners.choose_banner_background') }}</label>
            <div class="row g-3">
              <div class="col-md-3">
                <div class="banner-option" data-banner="b1.png">
                  <div class="banner-preview border rounded p-2 text-center cursor-pointer">
                    <img src="/images/banners/b1.png" alt="Banner 1" class="img-fluid rounded" style="height: 120px; object-fit: cover;">
                  </div>
                  <small class="text-muted d-block text-center mt-2">{{ __('banners.banner_1') }}</small>
                </div>
              </div>
              <div class="col-md-3">
                <div class="banner-option" data-banner="b2.png">
                  <div class="banner-preview border rounded p-2 text-center cursor-pointer">
                    <img src="/images/banners/b2.png" alt="Banner 2" class="img-fluid rounded" style="height: 120px; object-fit: cover;">
                  </div>
                  <small class="text-muted d-block text-center mt-2">{{ __('banners.banner_2') }}</small>
                </div>
              </div>
              <div class="col-md-3">
                <div class="banner-option" data-banner="b3.png">
                  <div class="banner-preview border rounded p-2 text-center cursor-pointer">
                    <img src="/images/banners/b3.png" alt="Banner 3" class="img-fluid rounded" style="height: 120px; object-fit: cover;">
                  </div>
                  <small class="text-muted d-block text-center mt-2">{{ __('banners.banner_3') }}</small>
                </div>
              </div>
              <div class="col-md-3">
                <div class="banner-option" data-banner="b4.png">
                  <div class="banner-preview border rounded p-2 text-center cursor-pointer">
                    <img src="/images/banners/b4.png" alt="Banner 4" class="img-fluid rounded" style="height: 120px; object-fit: cover;">
                  </div>
                  <small class="text-muted d-block text-center mt-2">{{ __('banners.banner_4') }}</small>
                </div>
              </div>
            </div>
            <input type="hidden" id="selected_banner" name="selected_banner" value="">
          </div>

          <div class="row g-4  mt-4">
            <!-- LEFT: Form Fields -->
            <div class="col-lg-8 ">
            <div class="row g-4">
                 <!-- Main Title -->
                 <div class="col-md-3">
                   <label for="main_title" class="form-label">{{ __('banners.main_title') }}</label>
                   <input type="text" id="main_title" name="main_title" class="form-control" 
                          placeholder="{{ __('banners.main_title_placeholder') }}" required>
                 </div>
                 
                 <!-- Title Color & Font -->
                 <div class="col-md-3">
                   <label for="title_color" class="form-label">{{ __('banners.title_color') }}</label>
                   <div class="input-group">
                     <input type="color" id="title_color" name="title_color" class="form-control form-control-color" 
                            value="#ffffff" title="{{ __('banners.choose_title_color') }}">
                     <button type="button" class="btn btn-outline-secondary" id="reset_title_color" title="{{ __('banners.reset_to_white') }}">
                       <iconify-icon icon="solar:refresh-bold-duotone"></iconify-icon>
                     </button>
                   </div>
                 </div>
                 
                 <div class="col-md-3">
                   <label for="title_font" class="form-label">{{ __('banners.title_font') }}</label>
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
                   <label for="title_size" class="form-label">{{ __('banners.title_size') }}</label>
                   <select id="title_size" name="title_size" class="form-select">
                     <option value="1rem">{{ __('banners.small') }}</option>
                     <option value="1.5rem">{{ __('banners.medium') }}</option>
                     <option value="2rem" selected>{{ __('banners.large') }}</option>
                     <option value="2.5rem">{{ __('banners.extra_large') }}</option>
                     <option value="3rem">{{ __('banners.huge') }}</option>
                   </select>
                 </div>

                <!-- Provider Name -->
                <div class="col-md-6">
                  <label for="provider_name" class="form-label">{{ __('banners.provider_name') }}</label>
                  <select id="provider_name" name="provider_name" class="form-select" data-choices>
                    <option value="">{{ __('banners.select_provider_optional') }}</option>
                    @if(isset($providers) && count($providers) > 0)
                      @foreach($providers as $provider)
                        <option value="{{ $provider->business_name }}" 
                                data-custom-properties='{"owner": "{{ $provider->owner_name }}"}'>
                          {{ $provider->business_name }}
                        </option>
                      @endforeach
                    @else
                      <option value="" disabled>{{ __('banners.no_providers_available') }}</option>
                    @endif
                  </select>
                  <small class="text-muted">{{ __('banners.or_type_custom_name') }}</small>
                </div>
                
                <!-- Provider Color & Font -->
                <div class="col-md-3">
                  <label for="provider_color" class="form-label">{{ __('banners.provider_color') }}</label>
                  <div class="input-group">
                    <input type="color" id="provider_color" name="provider_color" class="form-control form-control-color" 
                           value="#ffffff" title="{{ __('banners.choose_provider_color') }}">
                    <button type="button" class="btn btn-outline-secondary" id="reset_provider_color" title="{{ __('banners.reset_to_white') }}">
                      <iconify-icon icon="solar:refresh-bold-duotone"></iconify-icon>
                    </button>
                  </div>
                </div>
                
                <div class="col-md-3">
                  <label for="provider_font" class="form-label">{{ __('banners.provider_font') }}</label>
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
                  <label for="provider_size" class="form-label">{{ __('banners.provider_size') }}</label>
                  <select id="provider_size" name="provider_size" class="form-select">
                    <option value="0.8rem">{{ __('banners.small') }}</option>
                    <option value="1rem">{{ __('banners.medium') }}</option>
                    <option value="1.2rem" selected>{{ __('banners.large') }}</option>
                    <option value="1.5rem">{{ __('banners.extra_large') }}</option>
                    <option value="1.8rem">{{ __('banners.huge') }}</option>
                  </select>
                </div>

                <!-- Offer Text -->
                <div class="col-md-3">
                  <label for="offer_text" class="form-label">{{ __('banners.offer_text') }}</label>
                  <input type="text" id="offer_text" name="offer_text" class="form-control" 
                         placeholder="{{ __('banners.offer_placeholder') }}" required>
                </div>

                <div class="col-md-3">
                  <label for="offer_font" class="form-label">{{ __('banners.offer_font') }}</label>
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
                        
                <div class="col-md-2">
                  <label for="offer_size" class="form-label">{{ __('banners.offer_size') }}</label>
                  <select id="offer_size" name="offer_size" class="form-select">
                    <option value="0.7rem">{{ __('banners.small') }}</option>
                    <option value="0.9rem">{{ __('banners.medium') }}</option>
                    <option value="1.1rem" selected>{{ __('banners.large') }}</option>
                    <option value="1.3rem">{{ __('banners.extra_large') }}</option>
                    <option value="1.5rem">{{ __('banners.huge') }}</option>
                  </select>
                </div>         
                <!-- Offer Text Color & Font -->
                <div class="col-md-2">
                  <label for="offer_text_color" class="form-label">{{ __('banners.offer_text_color') }}</label>
                  <div class="input-group">
                    <input type="color" id="offer_text_color" name="offer_text_color" class="form-control form-control-color" 
                           value="#000000" title="{{ __('banners.choose_offer_text_color') }}">
                    
                  </div>
                </div>
                <!-- Offer Background Color -->
                <div class="col-md-2">
                  <label for="offer_bg_color" class="form-label">{{ __('banners.offer_background') }}</label>
                  <div class="input-group">
                    <input type="color" id="offer_bg_color" name="offer_bg_color" class="form-control form-control-color" 
                           value="#ffc107" title="{{ __('banners.choose_offer_bg_color') }}">
                  
                  </div>
                </div>

                
                
               


                 <!-- Date Range -->
                 <div class="col-md-6">
                   <label for="start_date" class="form-label">{{ __('banners.start_date') }}</label>
                   <input type="date" id="start_date" name="start_date" class="form-control" required>
                 </div>

              <div class="col-md-6">
                   <label for="end_date" class="form-label">{{ __('banners.end_date') }}</label>
                   <input type="date" id="end_date" name="end_date" class="form-control" required>
                 </div>

                 
              </div>
            </div>

            <!-- RIGHT: Banner Preview -->
            <div class="col-lg-4">
              <label class="form-label">{{ __('banners.interactive_banner_preview') }}</label>
              <div class="banner-preview-container border rounded p-3 bg-light">
                <div id="banner-preview" class="position-relative rounded overflow-hidden" style="height: 300px;">
                  <!-- Instructions overlay -->
                  <div class="preview-instructions">
                    <iconify-icon icon="solar:hand-stars-bold-duotone" class="me-1"></iconify-icon>
                    {{ __('banners.drag_text_instruction') }}
                  </div>
                  
                  <!-- Preview content -->
                  <div class="position-absolute top-0 start-0 w-100 h-100 d-flex flex-column justify-content-center align-items-center text-white p-3">
                    
                  </div>
                </div>
                <div class="mt-3 text-center">
                  <small class="text-muted">
                    <iconify-icon icon="solar:magic-stick-bold-duotone" class="me-1"></iconify-icon>
                    {{ __('banners.interactive_live_preview') }}
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
          {{ __('banners.preview') }}
        </button>
        <button type="button" id="btn_reset_ad" class="btn btn-light">{{ __('banners.reset') }}</button>
        <button type="button" id="btn_create_ad" class="btn btn-success">
          <iconify-icon icon="solar:add-circle-bold-duotone" class="me-1"></iconify-icon>
          {{ __('banners.create_ad') }}
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
          {{ __('banners.existing_ads_banners') }}
        </h4>
              </div>

      <div class="card-body">
        <div class="row g-4" id="ads-list">
          @forelse($banners as $banner)
          <div class="col-lg-6 col-xl-4" data-ad-id="{{ $banner->id }}">
            <div class="card h-100 border">
              <div class="position-relative">
                @if($banner->image_full_url)
                  <img src="{{ $banner->image_full_url }}" class="card-img-top" alt="{{ $banner->title }}" style="height: 200px; object-fit: cover;">
                @else
                  <div class="bg-light d-flex align-items-center justify-content-center" style="height: 200px;">
                    <iconify-icon icon="solar:image-bold-duotone" class="fs-1 text-muted"></iconify-icon>
                  </div>
                @endif
                <div class="position-absolute top-0 end-0 m-2">
                  <span class="badge {{ $banner->status_class }}">{{ $banner->status_badge }}</span>
                </div>
              </div>
              <div class="card-body d-flex flex-column">
                <h5 class="card-title">{{ $banner->title }}</h5>
                @if($banner->provider_name)
                <p class="card-text text-muted mb-2">
                  <iconify-icon icon="solar:buildings-bold-duotone" class="me-1"></iconify-icon>
                  <strong>{{ __('banners.provider') }}:</strong> {{ $banner->provider_name }}
                </p>
                @endif
                <p class="card-text text-muted mb-2">
                  <iconify-icon icon="solar:tag-bold-duotone" class="me-1"></iconify-icon>
                  <strong>{{ __('banners.offer') }}:</strong> {{ $banner->offer_text }}
                </p>
                <p class="card-text text-muted mb-2">
                  <iconify-icon icon="solar:calendar-bold-duotone" class="me-1"></iconify-icon>
                  <strong>{{ __('banners.start') }}:</strong> {{ $banner->start_date }}
                </p>
                <p class="card-text text-muted mb-2">
                  <iconify-icon icon="solar:calendar-mark-bold-duotone" class="me-1"></iconify-icon>
                  <strong>{{ __('banners.end') }}:</strong> {{ $banner->end_date }}
                </p>
                <p class="card-text text-muted mb-3">
                  <iconify-icon icon="solar:eye-bold-duotone" class="me-1"></iconify-icon>
                  <strong>{{ __('banners.views') }}:</strong> {{ $banner->impression_count }} |
                  <strong>{{ __('banners.clicks') }}:</strong> {{ $banner->click_count }}
                </p>
                <div class="mt-auto">
                  <div class="d-flex gap-2">
                    <button class="btn btn-sm btn-outline-primary flex-fill" onclick="editAd({{ $banner->id }})">
                      <iconify-icon icon="solar:pen-bold-duotone" class="me-1"></iconify-icon>
                      {{ __('banners.edit') }}
                    </button>
                    <button class="btn btn-sm btn-outline-danger flex-fill" onclick="deleteAd({{ $banner->id }})">
                      <iconify-icon icon="solar:trash-bin-trash-bold-duotone" class="me-1"></iconify-icon>
                      {{ __('banners.delete') }}
                    </button>
                  </div>
                </div>
              </div>
            </div>
          </div>
          @empty
          <div class="col-12">
            <div class="alert alert-info text-center">
              <iconify-icon icon="solar:info-circle-bold-duotone" class="fs-3 mb-2"></iconify-icon>
              <p class="mb-0">{{ __('banners.no_banners_created') }}</p>
            </div>
          </div>
          @endforelse
        </div>
      </div>
    </div>
  </div>
</div>



@endsection

@section('script-bottom')
@vite(['resources/js/pages/app-ecommerce-product.js', 'node_modules/choices.js/public/assets/scripts/choices.min.js'])
<script>
  // Logo upload functionality (only if elements exist)
  (function () {
    const input = document.getElementById('logo_input');
    const btn = document.getElementById('change_logo_btn');
    const preview = document.getElementById('logo_preview');
    
    // Check if elements exist before adding listeners
    if (!input || !btn || !preview) {
      console.log('Logo upload elements not found on this page, skipping initialization');
      return;
    }
    
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
<script>
  // Pass Laravel translations to JavaScript
  window.bannerTranslations = {
    provider_color_reset: "{{ __('banners.provider_color_reset') }}",
    offer_bg_color_reset: "{{ __('banners.offer_bg_color_reset') }}",
    offer_text_color_reset: "{{ __('banners.offer_text_color_reset') }}",
    fill_required_fields: "{{ __('banners.fill_required_fields') }}",
    end_date_must_be_after: "{{ __('banners.end_date_must_be_after') }}",
    select_banner_first: "{{ __('banners.select_banner_first') }}",
    preview_banner_first: "{{ __('banners.preview_banner_first') }}",
    banner_downloaded: "{{ __('banners.banner_downloaded') }}",
    download_failed: "{{ __('banners.download_failed') }}",
    confirm_delete: "{{ __('banners.confirm_delete') }}",
    ad_deleted: "{{ __('banners.ad_deleted') }}",
    delete_failed: "{{ __('banners.delete_failed') }}",
    failed_load_banner: "{{ __('banners.failed_load_banner') }}",
    editing_banner: "{{ __('banners.editing_banner') }}"
  };
</script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
<script src="/js/adbanners.js?v={{ time() }}"></script>

<!-- Preview Modal -->
<div class="modal fade" id="previewModal" tabindex="-1" aria-labelledby="previewModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="previewModalLabel">
          <iconify-icon icon="solar:eye-bold-duotone" class="me-2"></iconify-icon>
          {{ __('banners.banner_preview') }}
        </h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body text-center">
        <div id="modal-preview-container" class="d-inline-block">
          <!-- Preview content will be inserted here -->
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('banners.close') }}</button>
        <button type="button" class="btn btn-primary" id="download-preview">
          <iconify-icon icon="solar:download-bold-duotone" class="me-1"></iconify-icon>
          {{ __('banners.download') }}
        </button>
      </div>
    </div>
  </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    console.log('Page loaded, initializing AdBannerManager...');
    
    // Initialize Choices.js for provider dropdown (only once)
    const providerSelect = document.getElementById('provider_name');
    if (providerSelect && !providerSelect.classList.contains('choices__input')) {
        const choices = new Choices(providerSelect, {
            searchEnabled: true,
            searchPlaceholderValue: '{{ __('banners.search_providers') }}',
            itemSelectText: '{{ __('banners.click_to_select') }}',
            allowHTML: true,
            shouldSort: false,
            placeholder: true,
            placeholderValue: '{{ __('banners.select_provider_optional') }}',
            removeItemButton: true,
            // Allow custom values (user can type their own)
            addItems: true,
            addItemText: (value) => {
                return `{{ __('banners.press_enter_to_add') }} <b>"${value}"</b>`;
            },
        });
        
        // Allow user to add custom provider name
        providerSelect.addEventListener('addItem', function(event) {
            console.log('Provider selected:', event.detail.value);
        });
        
        console.log('Choices.js initialized for provider dropdown');
    } else if (providerSelect && providerSelect.classList.contains('choices__input')) {
        console.log('Choices.js already initialized for provider dropdown, skipping');
    }
    
    try {
        // Create single instance and store globally
        if (!window.adBannerManager) {
            window.adBannerManager = new AdBannerManager();
            console.log('AdBannerManager created successfully');
            
            // Manually select first banner
            setTimeout(() => {
                const firstBanner = document.querySelector('.banner-option');
                if (firstBanner) {
                    console.log('Manually selecting first banner');
                    firstBanner.click();
                }
            }, 100);
        } else {
            console.log('AdBannerManager already exists, skipping initialization');
        }
        
    } catch (error) {
        console.error('Error creating AdBannerManager:', error);
    }
});
</script>

@endsection