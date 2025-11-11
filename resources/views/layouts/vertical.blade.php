<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">

<head>
    @include('layouts.partials/title-meta', ['title' => $title])
    @yield('css')
    @include('layouts.partials/head-css')
</head>

<body data-layout-mode="detached" data-sidebar-user="true">

<!-- Pass API token to JavaScript -->
@auth
<script>
    window.apiToken = @json(session('api_token'));
    
    if (window.apiToken) {
        console.log('✓ API Token loaded from session');
    } else {
        console.error('❌ No API token - Please logout and login again');
    }
</script>
@endauth

<div class="wrapper">

    @include("layouts.partials/topbar", ['title' => $title])
    @include('layouts.partials/main-nav')

    <!-- Change Password Modal -->
    <div class="modal fade" id="changePasswordModal" tabindex="-1" aria-labelledby="changePasswordModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="changePasswordModalLabel">
                        <i class="bx bx-key me-2"></i>{{ __('common.change_password') }}
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="changePasswordForm">
                    @csrf
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="current_password" class="form-label">{{ __('settings.current_password') }}</label>
                            <input type="password" class="form-control" id="current_password" name="current_password" required>
                            <div class="invalid-feedback"></div>
                        </div>
                        <div class="mb-3">
                            <label for="new_password" class="form-label">{{ __('settings.new_password') }}</label>
                            <input type="password" class="form-control" id="new_password" name="new_password" required>
                            <small class="text-muted">{{ __('settings.password_requirements') }}</small>
                            <div class="invalid-feedback"></div>
                        </div>
                        <div class="mb-3">
                            <label for="new_password_confirmation" class="form-label">{{ __('settings.confirm_new_password') }}</label>
                            <input type="password" class="form-control" id="new_password_confirmation" name="new_password_confirmation" required>
                            <div class="invalid-feedback"></div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('common.cancel') }}</button>
                        <button type="submit" class="btn btn-primary" id="changePasswordBtn">
                            <i class="bx bx-save me-1"></i>{{ __('common.update') }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const changePasswordForm = document.getElementById('changePasswordForm');
        const changePasswordBtn = document.getElementById('changePasswordBtn');
        const changePasswordModal = document.getElementById('changePasswordModal');
        
        if (changePasswordForm) {
            changePasswordForm.addEventListener('submit', function(e) {
                e.preventDefault();
                
                // Clear previous errors
                document.querySelectorAll('.is-invalid').forEach(el => el.classList.remove('is-invalid'));
                
                // Get form data
                const formData = new FormData(this);
                
                // Disable button and show loading
                changePasswordBtn.disabled = true;
                changePasswordBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span>{{ __('common.updating') }}';
                
                // Send request
                fetch('{{ route('settings.password') }}', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // Success
                        Swal.fire({
                            icon: 'success',
                            title: '{{ __('settings.success_title') }}',
                            text: '{{ __('settings.password_updated_success') }}',
                            confirmButtonText: '{{ __('common.close') }}'
                        });
                        
                        // Close modal and reset form
                        bootstrap.Modal.getInstance(changePasswordModal).hide();
                        changePasswordForm.reset();
                    } else {
                        // Validation errors
                        if (data.errors) {
                            Object.keys(data.errors).forEach(key => {
                                const input = document.getElementById(key);
                                if (input) {
                                    input.classList.add('is-invalid');
                                    const feedback = input.nextElementSibling;
                                    if (feedback && feedback.classList.contains('invalid-feedback')) {
                                        feedback.textContent = data.errors[key][0];
                                    }
                                }
                            });
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: '{{ __('settings.error_title') }}',
                                text: data.message || '{{ __('settings.password_update_failed') }}',
                                confirmButtonText: '{{ __('common.close') }}'
                            });
                        }
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    Swal.fire({
                        icon: 'error',
                        title: '{{ __('settings.error_title') }}',
                        text: '{{ __('common.unexpected_error') }}',
                        confirmButtonText: '{{ __('common.close') }}'
                    });
                })
                .finally(() => {
                    // Re-enable button
                    changePasswordBtn.disabled = false;
                    changePasswordBtn.innerHTML = '<i class="bx bx-save me-1"></i>{{ __('common.update') }}';
                });
            });
        }
        
        // Reset form when modal is closed
        if (changePasswordModal) {
            changePasswordModal.addEventListener('hidden.bs.modal', function () {
                changePasswordForm.reset();
                document.querySelectorAll('.is-invalid').forEach(el => el.classList.remove('is-invalid'));
            });
        }
    });
    </script>

    <div class="page-content">

        <div class="container-fluid">
            @yield('content')
        </div>

        @include("layouts.partials/footer")

    </div>

</div>

@include("layouts.partials/right-sidebar")
@include("layouts.partials/footer-scripts")
@vite(['resources/js/app.js','resources/js/layout.js'])

</body>

</html>
