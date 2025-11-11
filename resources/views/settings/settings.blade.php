@extends('layouts.vertical', ['title' => __('settings.settings')])

@section('css')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/choices.js/public/assets/styles/choices.min.css">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
@endsection

@section('content')

<!-- General Settings -->
<div class="row" id="general-settings">
  <div class="col-lg-12">
    <div class="card">
      <div class="card-header">
        <h4 class="card-title d-flex align-items-center gap-1">
          <iconify-icon icon="solar:settings-bold-duotone" class="text-primary fs-20"></iconify-icon>
          {{ __('settings.general_settings') }}
        </h4>
      </div>

      <div class="card-body">
        <div class="row g-4 align-items-start">
          <!-- LEFT: Fields -->
          <div class="col-lg-12">
            <div class="row g-4">

              <!-- Contact Email -->
              <div class="col-md-6">
                <label for="contact_email" class="form-label">{{ __('settings.contact_email') }}</label>
                <input type="email" id="contact_email" name="contact_email" class="form-control" value="{{ $settings['contact_email'] ?? 'support@luky.app' }}">
              </div>

              <!-- Contact Phone -->
              <div class="col-md-6">
                <label for="contact_phone" class="form-label">{{ __('settings.contact_phone') }}</label>
                <input type="tel" id="contact_phone" name="contact_phone" class="form-control" value="{{ $settings['contact_phone'] ?? '+966 5X XXX XXXX' }}">
              </div>

              <!-- Address -->
              <div class="col-md-6">
                <label for="contact_address" class="form-label">{{ __('settings.address') }}</label>
                <input type="text" id="contact_address" name="contact_address" class="form-control" value="{{ $settings['contact_address'] ?? 'Riyadh, Saudi Arabia' }}">
              </div>
            </div>
          </div>


          <!-- Divider -->
          <div class="col-12"><hr class="my-2"></div>

          <!-- Admin Password -->
          <div class="col-12">
            <h6 class="mb-3">{{ __('settings.admin_password') }}</h6>
            <div class="row g-3">
              <div class="col-md-4">
                <label for="current_password" class="form-label">{{ __('settings.current_password') }}</label>
                <input type="password" id="current_password" name="current_password" class="form-control" placeholder="{{ __('settings.current_password_placeholder') }}">
              </div>
              <div class="col-md-4">
                <label for="new_password" class="form-label">{{ __('settings.new_password') }}</label>
                <input type="password" id="new_password" name="new_password" class="form-control" placeholder="{{ __('settings.new_password_placeholder') }}">
              </div>
              <div class="col-md-4">
                <label for="new_password_confirmation" class="form-label">{{ __('settings.confirm_new_password') }}</label>
                <input type="password" id="new_password_confirmation" name="new_password_confirmation" class="form-control" placeholder="{{ __('settings.repeat_password_placeholder') }}">
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Action buttons -->
      <div class="card-footer d-flex justify-content-end gap-2">
        <button type="button" id="btn_reset_settings" class="btn btn-light">{{ __('settings.reset') }}</button>
        <button type="button" id="btn_save_settings" class="btn btn-success">{{ __('settings.save_changes') }}</button>
        <button type="button" id="btn_update_password" class="btn btn-primary">{{ __('settings.update_password') }}</button>
      </div>
    </div>
  </div>
</div>

@endsection

@section('script-bottom')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
  document.addEventListener('DOMContentLoaded', function() {
    console.log('Settings page loaded');
    console.log('SweetAlert loaded:', typeof Swal !== 'undefined');
    
    // Check if button exists
    const saveBtn = document.getElementById('btn_save_settings');
    if (!saveBtn) {
      console.error('Save button not found!');
      return;
    }
    console.log('Save button found:', saveBtn);
    
    // Save general settings
    saveBtn.addEventListener('click', function(e) {
      e.preventDefault(); // Prevent any default action
      console.log('Save button clicked!');
      
      const formData = {
        contact_email: document.getElementById('contact_email').value,
        contact_phone: document.getElementById('contact_phone').value,
        contact_address: document.getElementById('contact_address').value,
      };
      
      console.log('Form data:', formData);

      // Send AJAX request
      fetch('{{ route("settings.update") }}', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        },
        body: JSON.stringify(formData)
      })
      .then(response => {
        console.log('Response status:', response.status);
        return response.json();
      })
      .then(data => {
        console.log('Response data:', data);
        
        if (data.success) {
          // Show success message
          Swal.fire({
            title: '{{ __('settings.success_title') }}',
            text: data.message,
            icon: 'success',
            confirmButtonText: '{{ __('settings.ok_button') }}'
          });

          // Update form values if returned
          if (data.settings) {
            document.getElementById('contact_email').value = data.settings.contact_email || '';
            document.getElementById('contact_phone').value = data.settings.contact_phone || '';
            document.getElementById('contact_address').value = data.settings.contact_address || '';
          }
        } else {
          // Show error message
          Swal.fire({
            title: '{{ __('settings.error_title') }}',
            text: data.message || '{{ __('settings.settings_update_failed') }}',
            icon: 'error',
            confirmButtonText: '{{ __('settings.ok_button') }}'
          });
        }
      })
      .catch(error => {
        console.error('Fetch error:', error);
        Swal.fire({
          title: '{{ __('settings.error_title') }}',
          text: '{{ __('settings.unexpected_error') }}' + ': ' + error.message,
          icon: 'error',
          confirmButtonText: '{{ __('settings.ok_button') }}'
        });
      });
    });

    // Update password
    document.getElementById('btn_update_password').addEventListener('click', function() {
      const formData = {
        current_password: document.getElementById('current_password').value,
        new_password: document.getElementById('new_password').value,
        new_password_confirmation: document.getElementById('new_password_confirmation').value,
      };

      // Send AJAX request
      fetch('{{ route("settings.password") }}', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        },
        body: JSON.stringify(formData)
      })
      .then(response => response.json())
      .then(data => {
        if (data.success) {
          // Show success message
          Swal.fire({
            title: '{{ __('settings.success_title') }}',
            text: data.message,
            icon: 'success',
            confirmButtonText: '{{ __('settings.ok_button') }}'
          });

          // Clear password fields
          document.getElementById('current_password').value = '';
          document.getElementById('new_password').value = '';
          document.getElementById('new_password_confirmation').value = '';
        } else {
          // Show error message
          Swal.fire({
            title: '{{ __('settings.error_title') }}',
            text: data.message || '{{ __('settings.password_update_failed') }}',
            icon: 'error',
            confirmButtonText: '{{ __('settings.ok_button') }}'
          });
        }
      })
      .catch(error => {
        console.error('Error:', error);
        Swal.fire({
          title: '{{ __('settings.error_title') }}',
          text: '{{ __('settings.unexpected_error') }}',
          icon: 'error',
          confirmButtonText: '{{ __('settings.ok_button') }}'
        });
      });
    });

    // Reset form
    document.getElementById('btn_reset_settings').addEventListener('click', function() {
      // Reset to original values or reload the page
      window.location.reload();
    });
  });
</script>
@endsection