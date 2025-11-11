@extends('layouts.vertical', ['title' => __('users.dashboard_users')])

@section('css')
  @vite(['node_modules/choices.js/public/assets/styles/choices.min.css'])
@endsection

@section('content')

{{-- Flash Messages --}}
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

<!-- View User Modal (Dummy Data) -->
<div class="modal fade" id="userViewModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-scrollable">
    <div class="modal-content">

      <div class="modal-header">
        <h5 class="modal-title d-flex align-items-center gap-2">
          <iconify-icon icon="solar:user-bold-duotone" class="text-primary"></iconify-icon>
          {{ __('users.user_details') }}
        </h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>

      <div class="modal-body">
        <div class="row g-4">
          <!-- Left: Avatar + Summary -->
          <div class="col-md-4">
            <div class="card border-0 shadow-sm">
              <div class="card-body text-center">
                <div class="position-relative mx-auto mb-3 d-flex align-items-center justify-content-center rounded-circle bg-light"
                     style="width:112px;height:112px;">
                  <img src="/images/users/avatar-6.jpg" alt="Profile"
                       class="rounded-circle" style="width:104px;height:104px;object-fit:cover;">
                </div>
                <h5 class="mb-1">Lama Al Saud</h5>
                <div class="d-flex justify-content-center gap-2">
                  <span class="badge bg-info-subtle text-info">Admin</span>
                  <span class="badge bg-success-subtle text-success">{{ __('users.active') }}</span>
                </div>
                <small class="text-muted d-block mt-2">{{ __('users.user_id') }}: U-1001</small>
                <small class="text-muted d-block">{{ __('users.created_time', ['time' => '05 Oct 2025, 09:20 am']) }}</small>
              </div>
            </div>
          </div>

          <!-- Right: Details -->
          <div class="col-md-8">
            <div class="card border-0">
              <div class="card-body">
                <div class="row g-3">
                  <div class="col-12">
                    <label class="text-muted small mb-1">{{ __('users.email') }}</label>
                    <div class="fw-semibold">lama@example.com</div>
                  </div>

                  <div class="col-6">
                    <label class="text-muted small mb-1">{{ __('users.phone') }}</label>
                    <div class="fw-semibold">+966 55 123 4567</div>
                  </div>

                  <div class="col-6">
                    <label class="text-muted small mb-1">{{ __('users.city') }}</label>
                    <div class="fw-semibold">Riyadh</div>
                  </div>

                  <div class="col-6">
                    <label class="text-muted small mb-1">{{ __('users.role') }}</label>
                    <div class="fw-semibold">Admin</div>
                  </div>

                  <div class="col-6">
                    <label class="text-muted small mb-1">{{ __('users.status') }}</label>
                    <div class="fw-semibold">{{ __('users.active') }}</div>
                  </div>

                  <div class="col-12">
                    <label class="text-muted small mb-1">{{ __('users.notes') }}</label>
                    <div class="fw-semibold">
                      Trusted internal admin. Handles provider approvals and escalations.
                    </div>
                  </div>
                </div>

                <hr class="my-3">

                <div class="d-flex flex-wrap gap-2">
                  <button type="button" class="btn btn-outline-secondary btn-sm" data-bs-dismiss="modal">
                    {{ __('users.close') }}
                  </button>
                  <button type="button" class="btn btn-primary btn-sm" data-bs-target="#userEditModal" data-bs-toggle="modal">
                    {{ __('users.edit_user') }}
                  </button>
                </div>
              </div>
            </div>
          </div>
          <!-- /Right -->
        </div>
      </div>

    </div>
  </div>
</div>

{{-- Stat Cards --}}
<div class="row">
  <div class="col-lg-4">
    <div class="card">
      <div class="card-body d-flex align-items-center justify-content-between">
        <div>
          <h4 class="card-title mb-2">{{ __('users.total_users') }}</h4>
          <p class="text-muted fw-medium fs-22 mb-0">{{ number_format($stats['total'] ?? 0) }}</p>
        </div>
        <div class="avatar-md bg-primary bg-opacity-10 rounded d-flex align-items-center justify-content-center">
          <iconify-icon icon="solar:users-group-two-rounded-bold-duotone" class="fs-32 text-primary"></iconify-icon>
        </div>
      </div>
    </div>
  </div>
  <div class="col-lg-4">
    <div class="card">
      <div class="card-body d-flex align-items-center justify-content-between">
        <div>
          <h4 class="card-title mb-2">{{ __('users.active_users') }}</h4>
          <p class="text-muted fw-medium fs-22 mb-0">{{ number_format($stats['active'] ?? 0) }}</p>
        </div>
        <div class="avatar-md bg-success bg-opacity-10 rounded d-flex align-items-center justify-content-center">
          <iconify-icon icon="solar:user-check-bold-duotone" class="fs-32 text-success"></iconify-icon>
        </div>
      </div>
    </div>
  </div>
  <div class="col-lg-4">
    <div class="card">
      <div class="card-body d-flex align-items-center justify-content-between">
        <div>
          <h4 class="card-title mb-2">{{ __('users.inactive_users') }}</h4>
          <p class="text-muted fw-medium fs-22 mb-0">{{ number_format($stats['inactive'] ?? 0) }}</p>
        </div>
        <div class="avatar-md bg-danger bg-opacity-10 rounded d-flex align-items-center justify-content-center">
          <iconify-icon icon="solar:user-block-bold-duotone" class="fs-32 text-danger"></iconify-icon>
        </div>
      </div>
    </div>
  </div>
</div>

{{-- Users Table --}}
<div class="row">
  <div class="col-xl-12">
    <div class="card">
      <div class="d-flex card-header justify-content-between align-items-center border-bottom">
        <div>
          <h4 class="card-title mb-0 fw-semibold">{{ __('users.dashboard_users') }}</h4>
          <p class="text-muted mb-0 small">{{ __('users.manage_admin_managers_support') }}</p>
        </div>

        <div class="d-flex align-items-center gap-2">
          <form method="GET" action="{{ route('adminrole.users') }}" class="d-flex gap-2">
            <input type="search" name="search" class="form-control" 
                   placeholder="{{ __('users.search_users') }}" 
                   value="{{ $filters['search'] ?? '' }}" 
                   style="min-width: 200px;">
            <select name="status" class="form-select" style="min-width: 120px;">
              <option value="">{{ __('users.all_status') }}</option>
              <option value="active" {{ ($filters['status'] ?? '') == 'active' ? 'selected' : '' }}>{{ __('users.active') }}</option>
              <option value="inactive" {{ ($filters['status'] ?? '') == 'inactive' ? 'selected' : '' }}>{{ __('users.inactive') }}</option>
            </select>
            <select name="role" class="form-select" style="min-width: 150px;">
              <option value="">{{ __('users.all_roles') }}</option>
              @foreach($roles as $role)
                <option value="{{ $role->name }}" {{ ($filters['role'] ?? '') == $role->name ? 'selected' : '' }}>
                  {{ ucwords(str_replace('_', ' ', $role->name)) }}
                </option>
              @endforeach
            </select>
            <button type="submit" class="btn btn-primary">
              <i class="mdi mdi-filter-variant"></i>
            </button>
            <a href="{{ route('adminrole.users') }}" class="btn btn-soft-secondary">
              <i class="mdi mdi-refresh"></i>
            </a>
          </form>
          <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#userCreateModal">
            <i class="bx bx-plus me-1"></i> {{ __('users.create_user') }}
          </button>
        </div>
      </div>

      <div class="table-responsive">
        <table class="table align-middle mb-0 table-hover table-centered">
          <thead class="bg-light-subtle">
            <tr>
              <th style="width:20px;">
                <div class="form-check">
                  <input type="checkbox" class="form-check-input" id="checkAll">
                </div>
              </th>
              <th>{{ __('users.user') }}</th>
              <th>{{ __('users.role') }}</th>
              <th>{{ __('users.status') }}</th>
              <th>{{ __('users.email') }}</th>
              <th>{{ __('users.phone') }}</th>
              <th>{{ __('users.city') }}</th>
              <th>{{ __('users.created') }}</th>
              <th>{{ __('users.action') }}</th>
            </tr>
          </thead>

          <tbody>
            @forelse($users as $user)
            <tr>
              <td>
                <div class="form-check">
                  <input type="checkbox" class="form-check-input" id="row{{ $user['id'] }}">
                  <label class="form-check-label" for="row{{ $user['id'] }}">&nbsp;</label>
                </div>
              </td>
              <td>
                <div class="d-flex align-items-center">
                  @if($user['avatar'])
                    <img src="{{ $user['avatar'] }}" class="avatar-sm rounded-circle me-2" alt="{{ $user['name'] }}">
                  @else
                    <div class="avatar-sm bg-primary bg-opacity-10 rounded-circle me-2 d-flex align-items-center justify-content-center">
                      <span class="text-primary fw-bold">{{ strtoupper(substr($user['name'], 0, 1)) }}</span>
                    </div>
                  @endif
                  <div class="d-flex flex-column">
                    <span class="fw-semibold">{{ $user['name'] }}</span>
                    <small class="text-muted">ID: {{ $user['id'] }}</small>
                  </div>
                </div>
              </td>
              <td>
                @if(!empty($user['roles']))
                  @foreach($user['roles'] as $role)
                    <span class="badge bg-{{ $role === 'super_admin' ? 'danger' : ($role === 'admin' ? 'info' : 'primary') }}-subtle text-{{ $role === 'super_admin' ? 'danger' : ($role === 'admin' ? 'info' : 'primary') }}">
                      {{ ucwords(str_replace('_', ' ', $role)) }}
                    </span>
                  @endforeach
                @else
                  <span class="badge bg-secondary-subtle text-secondary">{{ __('users.no_role') }}</span>
                @endif
              </td>
              <td>
                <span class="badge bg-{{ $user['is_active'] ? 'success' : 'danger' }}-subtle text-{{ $user['is_active'] ? 'success' : 'danger' }}">
                  {{ $user['is_active'] ? __('users.active') : __('users.inactive') }}
                </span>
              </td>
              <td>{{ $user['email'] ?? __('users.n_a') }}</td>
              <td>{{ $user['phone'] ?? __('users.n_a') }}</td>
              <td>{{ $user['city'] ?? __('users.n_a') }}</td>
              <td>{{ $user['created_at']->format('d M Y') }}</td>
              <td>
                <div class="d-flex gap-2">
                  <button onclick="toggleStatus({{ $user['id'] }}, {{ $user['is_active'] ? 'true' : 'false' }})" 
                          class="btn btn-soft-{{ $user['is_active'] ? 'warning' : 'success' }} btn-sm" 
                          title="{{ $user['is_active'] ? __('users.deactivate') : __('users.activate') }}">
                    <iconify-icon icon="{{ $user['is_active'] ? 'solar:pause-circle-broken' : 'solar:play-circle-broken' }}" 
                                  class="fs-18"></iconify-icon>
                  </button>
                  <button onclick="resetPassword({{ $user['id'] }}, '{{ $user['name'] }}')" 
                          class="btn btn-soft-secondary btn-sm" 
                          title="{{ __('users.reset_password') }}">
                    <iconify-icon icon="solar:lock-password-broken" class="fs-18"></iconify-icon>
                  </button>
                  <button onclick="deleteUser({{ $user['id'] }}, '{{ $user['name'] }}')" 
                          class="btn btn-soft-danger btn-sm" 
                          title="{{ __('users.delete') }}">
                    <iconify-icon icon="solar:trash-bin-minimalistic-2-broken" class="fs-18"></iconify-icon>
                  </button>
                </div>
              </td>
            </tr>
            @empty
            <tr>
              <td colspan="9" class="text-center py-5">
                <div class="py-5">
                  <iconify-icon icon="solar:user-broken" class="fs-48 text-muted mb-3"></iconify-icon>
                  <h5 class="text-muted">{{ __('users.no_users_found') }}</h5>
                  <p class="text-muted">{{ __('users.create_first_user') }}</p>
                  <button class="btn btn-primary mt-3" data-bs-toggle="modal" data-bs-target="#userCreateModal">
                    <i class="bx bx-plus me-1"></i> {{ __('users.create_user') }}
                  </button>
                </div>
              </td>
            </tr>
            @endforelse
          </tbody>
        </table>
      </div>

      <div class="card-footer border-top">
        @if($pagination['last_page'] > 1)
        <nav aria-label="Page navigation">
          <ul class="pagination justify-content-end mb-0">
            {{-- Previous Button --}}
            <li class="page-item {{ $pagination['current_page'] == 1 ? 'disabled' : '' }}">
              <a class="page-link" href="{{ route('adminrole.users', array_merge($filters, ['page' => $pagination['current_page'] - 1])) }}">
                {{ __('users.previous') }}
              </a>
            </li>

            {{-- Page Numbers --}}
            @for($i = 1; $i <= $pagination['last_page']; $i++)
              @if($i == 1 || $i == $pagination['last_page'] || abs($i - $pagination['current_page']) <= 2)
                <li class="page-item {{ $i == $pagination['current_page'] ? 'active' : '' }}">
                  <a class="page-link" href="{{ route('adminrole.users', array_merge($filters, ['page' => $i])) }}">
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
              <a class="page-link" href="{{ route('adminrole.users', array_merge($filters, ['page' => $pagination['current_page'] + 1])) }}">
                {{ __('users.next') }}
              </a>
            </li>
          </ul>
        </nav>
        @endif

        <div class="text-muted text-center mt-2 small">
          {!! __('users.showing_to_of_users', ['from' => $pagination['from'] ?? 0, 'to' => $pagination['to'] ?? 0, 'total' => $pagination['total']]) !!}
        </div>
      </div>
    </div>
  </div>
</div>

{{-- Create User Modal --}}
<div class="modal fade" id="userCreateModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-xl">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title d-flex align-items-center gap-2">
          <iconify-icon icon="solar:user-plus-bold-duotone" class="text-primary"></iconify-icon>
          {{ __('users.create_user') }}
        </h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>

      <div class="modal-body">
        <div class="row g-3">
          <div class="col-6">
            <label class="form-label">{{ __('users.full_name') }} <span class="text-danger">*</span></label>
            <input type="text" class="form-control" id="cu_name" placeholder="{{ __('users.full_name_placeholder') }}" required>
          </div>
          <div class="col-6">
            <label class="form-label">{{ __('users.email') }} <span class="text-danger">*</span></label>
            <input type="email" class="form-control" id="cu_email" placeholder="{{ __('users.email_placeholder') }}" required>
          </div>
          <div class="col-6">
            <label class="form-label">{{ __('users.phone') }} <span class="text-danger">*</span></label>
            <input type="tel" class="form-control" id="cu_phone" placeholder="{{ __('users.phone_placeholder') }}" required>
          </div>
 
            <div class="col-6">
                <label for="notify_recipient" class="form-label">{{ __('users.select_role') }} <span class="text-danger">*</span></label>
                <select id="notify_recipient"
                        class="form-select"
                        data-choices
                        data-choices-search-true>
                @foreach($roles as $role)
                    <option value="{{ $role->name }}">{{ ucwords(str_replace('_', ' ', $role->name)) }}</option>
                @endforeach
                </select>
                <small class="text-muted">{{ __('users.select_role_help') }}</small>

            </div>

            <div class="col-6">
                <label class="form-label">{{ __('users.password') }} <span class="text-danger">*</span></label>
                <input type="password" class="form-control" id="cu_password" placeholder="{{ __('users.password_placeholder') }}" required>
            </div>

            <div class="col-6">
                <label class="form-label">{{ __('users.confirm_password') }} <span class="text-danger">*</span></label>
                <input type="password" class="form-control" id="cu_password_confirm" placeholder="{{ __('users.confirm_password_placeholder') }}" required>
            </div>

            <div class="col-12">
                <label class="form-label d-block">{{ __('users.profile_image') }}</label>
                <div class="d-flex align-items-center gap-3">
                <div class="border rounded bg-light d-flex align-items-center justify-content-center" style="width:96px;height:96px;">
                    <img id="cu_preview" src="/images/users/avatar-1.jpg" alt="" class="rounded-circle" style="width:88px;height:88px;object-fit:cover;">
                </div>
                <div>
                    <input type="file" id="cu_image" accept=".png,.jpg,.jpeg,.webp" hidden>
                    <button type="button" class="btn btn-outline-primary btn-sm" id="cu_pick">{{ __('users.upload_image') }}</button>
                    <small class="text-muted d-block mt-1">{{ __('users.image_requirements') }}</small>
                </div>
                </div>
            </div>

            </div>
      </div>

      <div class="modal-footer">
        <button class="btn btn-light" data-bs-dismiss="modal">{{ __('users.cancel') }}</button>
        <button class="btn btn-success" id="cu_submit">{{ __('users.create') }}</button>
      </div>
    </div>
  </div>
</div>

<!-- Edit User Modal -->
<div class="modal fade" id="userEditModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-xl">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title d-flex align-items-center gap-2">
          <iconify-icon icon="solar:pen-2-bold-duotone" class="text-primary"></iconify-icon>
          {{ __('users.edit_user') }}
        </h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>

      <div class="modal-body">
        <div class="row g-3">
          <input type="hidden" id="eu_id">

          <div class="col-6">
            <label class="form-label">{{ __('users.full_name') }}</label>
            <input type="text" class="form-control" id="eu_name" placeholder="{{ __('users.full_name_placeholder') }}">
          </div>

          <div class="col-6">
            <label class="form-label">{{ __('users.email') }}</label>
            <input type="email" class="form-control" id="eu_email" placeholder="{{ __('users.email_placeholder') }}">
          </div>

          <div class="col-6">
            <label class="form-label">{{ __('users.phone') }}</label>
            <input type="tel" class="form-control" id="eu_phone" placeholder="{{ __('users.phone_placeholder') }}">
          </div>

          <div class="col-6">
            <label class="form-label">{{ __('users.role') }}</label>
            <select id="eu_role" class="form-select" data-choices data-choices-search-true>
              @foreach($roles as $role)
                  <option value="{{ $role->name }}">{{ ucwords(str_replace('_', ' ', $role->name)) }}</option>
              @endforeach
            </select>
          </div>

          <!-- Profile image with tiny close button -->
          <div class="col-12">
            <label class="form-label d-block">{{ __('users.profile_image') }}</label>
            <div class="d-flex align-items-center gap-3">
              <div class="position-relative border rounded bg-light d-flex align-items-center justify-content-center"
                   style="width:96px;height:96px;">
                <img id="eu_preview"
                     src="/images/users/avatar-1.jpg"
                     alt=""
                     class="rounded-circle"
                     style="width:88px;height:88px;object-fit:cover;">
                <!-- tiny close button (clear image) -->
                <button type="button"
                        id="eu_clear_img"
                        class="btn btn-sm btn-danger position-absolute top-0 end-0 p-0 d-inline-flex align-items-center justify-content-center"
                        style="width:22px;height:22px;transform:translate(25%,-25%); border-radius:50%; box-shadow:0 0 0 1px rgba(0,0,0,.06);"
                        title="Remove image">
                  <i class="bx bx-x fs-18"></i>
                </button>
              </div>

              <div>
                <input type="file" id="eu_image" accept=".png,.jpg,.jpeg,.webp" hidden>
                <button type="button" class="btn btn-outline-primary btn-sm" id="eu_pick">{{ __('users.change_image') }}</button>
                <small class="text-muted d-block mt-1">{{ __('users.image_requirements') }}</small>

                <!-- flag to tell backend to remove image if true -->
                <input type="hidden" id="eu_remove_image" value="0">
              </div>
            </div>
          </div>

        </div>
      </div>

      <div class="modal-footer">
        <button class="btn btn-light" data-bs-dismiss="modal">{{ __('users.cancel') }}</button>
        <button class="btn btn-success" id="eu_save">{{ __('users.save_changes') }}</button>
      </div>
    </div>
  </div>
</div>


@endsection

@section('script')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
// Toggle user status
function toggleStatus(userId, currentStatus) {
    const action = currentStatus ? '{{ __('users.deactivate') }}' : '{{ __('users.activate') }}';
    const title = currentStatus ? '{{ __('users.deactivate_user_title') }}' : '{{ __('users.activate_user_title') }}';
    const text = currentStatus ? '{{ __('users.deactivate_user_text') }}' : '{{ __('users.activate_user_text') }}';
    const subtext = currentStatus ? '{{ __('users.prevent_dashboard_access') }}' : '{{ __('users.allow_dashboard_access') }}';
    const confirmText = currentStatus ? '{{ __('users.yes_deactivate') }}' : '{{ __('users.yes_activate') }}';
    
    Swal.fire({
        title: title,
        html: `<p class="mb-2">${text}</p>
               <p class="text-muted small">${subtext}.</p>`,
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: currentStatus ? '#f1b44c' : '#34c38f',
        confirmButtonText: confirmText,
        cancelButtonText: '{{ __('users.cancel') }}'
    }).then((result) => {
        if (result.isConfirmed) {
            Swal.fire({
                title: '{{ __('users.processing') }}',
                allowOutsideClick: false,
                didOpen: () => { Swal.showLoading(); }
            });

            fetch(`/adminrole/users/${userId}/toggle-status`, {
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
                        title: '{{ __('users.success') }}',
                        text: data.message,
                        timer: 2000,
                        showConfirmButton: false
                    }).then(() => {
                        location.reload();
                    });
                } else {
                    Swal.fire('{{ __('users.error') }}', data.message, 'error');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                Swal.fire('{{ __('users.error') }}', 'An error occurred', 'error');
            });
        }
    });
}

// Delete user
function deleteUser(userId, userName) {
    Swal.fire({
        title: '{{ __('users.delete_user_title') }}',
        html: `<p class="mb-2">{{ __('users.delete_user_text') }}</p>
               <p class="text-danger small"><i class="mdi mdi-alert"></i> {{ __('users.action_cannot_undone') }}</p>`
               .replace(':name', `<strong>"${userName}"</strong>`),
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        confirmButtonText: '{{ __('users.yes_delete') }}',
        cancelButtonText: '{{ __('users.cancel') }}'
    }).then((result) => {
        if (result.isConfirmed) {
            Swal.fire({
                title: '{{ __('users.deleting') }}',
                allowOutsideClick: false,
                didOpen: () => { Swal.showLoading(); }
            });

            fetch(`/adminrole/users/${userId}`, {
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
                        title: '{{ __('users.deleted') }}',
                        text: data.message,
                        timer: 2000,
                        showConfirmButton: false
                    }).then(() => {
                        location.reload();
                    });
                } else {
                    Swal.fire('{{ __('users.error') }}', data.message, 'error');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                Swal.fire('{{ __('users.error') }}', 'An error occurred', 'error');
            });
        }
    });
}

// Reset password
function resetPassword(userId, userName) {
    Swal.fire({
        title: '{{ __('users.reset_password_title') }}',
        html: `
            <p class="mb-3">{{ __('users.reset_password_for') }}</p>
            <input type="password" id="swal-password" class="form-control mb-2" placeholder="{{ __('users.new_password') }}" minlength="8">
            <input type="password" id="swal-password-confirm" class="form-control" placeholder="{{ __('users.confirm_password_label') }}" minlength="8">
        `.replace(':name', `<strong>${userName}</strong>`),
        showCancelButton: true,
        confirmButtonText: '{{ __('users.reset_password_button') }}',
        confirmButtonColor: '#3085d6',
        preConfirm: () => {
            const password = document.getElementById('swal-password').value;
            const confirmPassword = document.getElementById('swal-password-confirm').value;
            
            if (!password || password.length < 8) {
                Swal.showValidationMessage('{{ __('users.password_min_8') }}');
                return false;
            }
            if (password !== confirmPassword) {
                Swal.showValidationMessage('{{ __('users.passwords_not_match') }}');
                return false;
            }
            return { password, password_confirmation: confirmPassword };
        }
    }).then((result) => {
        if (result.isConfirmed) {
            Swal.fire({
                title: '{{ __('users.resetting') }}',
                allowOutsideClick: false,
                didOpen: () => { Swal.showLoading(); }
            });

            fetch(`/adminrole/users/${userId}/reset-password`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify(result.value)
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    Swal.fire('{{ __('users.success') }}', data.message, 'success');
                } else {
                    Swal.fire('{{ __('users.error') }}', data.message, 'error');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                Swal.fire('{{ __('users.error') }}', 'An error occurred', 'error');
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

// === CREATE USER MODAL ===
// Image upload for create user
document.getElementById('cu_pick')?.addEventListener('click', function() {
    document.getElementById('cu_image').click();
});

document.getElementById('cu_image')?.addEventListener('change', function(e) {
    const file = e.target.files[0];
    if (file) {
        // Validate file size (2MB max)
        if (file.size > 2 * 1024 * 1024) {
            Swal.fire('{{ __('users.error') }}', '{{ __('users.image_size_error') }}', 'error');
            this.value = '';
            return;
        }
        
        // Validate file type
        if (!['image/png', 'image/jpeg', 'image/jpg', 'image/webp'].includes(file.type)) {
            Swal.fire('{{ __('users.error') }}', '{{ __('users.image_type_error') }}', 'error');
            this.value = '';
            return;
        }
        
        // Preview image
        const reader = new FileReader();
        reader.onload = function(e) {
            document.getElementById('cu_preview').src = e.target.result;
        };
        reader.readAsDataURL(file);
    }
});

// Create user submit
document.getElementById('cu_submit')?.addEventListener('click', function() {
    const name = document.getElementById('cu_name').value.trim();
    const email = document.getElementById('cu_email').value.trim();
    const phone = document.getElementById('cu_phone').value.trim();
    const role = document.getElementById('notify_recipient').value;
    const password = document.getElementById('cu_password').value;
    const passwordConfirm = document.getElementById('cu_password_confirm').value;
    
    // Basic validation
    if (!name || !email || !phone || !role) {
        Swal.fire('{{ __('users.error') }}', '{{ __('users.fill_required_fields') }}', 'error');
        return;
    }
    
    // Email validation
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    if (!emailRegex.test(email)) {
        Swal.fire('{{ __('users.error') }}', '{{ __('users.valid_email') }}', 'error');
        return;
    }
    
    // Password validation
    if (!password || password.length < 8) {
        Swal.fire('{{ __('users.error') }}', '{{ __('users.password_min_8') }}', 'error');
        return;
    }
    
    if (password !== passwordConfirm) {
        Swal.fire('{{ __('users.error') }}', '{{ __('users.passwords_not_match') }}', 'error');
        return;
    }
    
    // Show loading
    Swal.fire({
        title: '{{ __('users.creating') }}',
        allowOutsideClick: false,
        didOpen: () => { Swal.showLoading(); }
    });
    
    // Role value is already the database name (e.g., 'admin', 'super_admin', etc.)
    // No mapping needed since dropdown now uses actual role names
    
    fetch('/adminrole/users', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        },
        body: JSON.stringify({
            name: name,
            email: email,
            phone: phone,
            password: password,
            role: role  // Direct use of role value from database
        })
    })
    .then(response => {
        if (!response.ok) {
            return response.json().then(err => { throw err; });
        }
        return response.json();
    })
    .then(data => {
        if (data.success) {
            Swal.fire({
                icon: 'success',
                title: '{{ __('users.success') }}',
                text: data.message,
                timer: 2000,
                showConfirmButton: false
            }).then(() => {
                location.reload();
            });
        } else {
            Swal.fire('{{ __('users.error') }}', data.message || 'Failed to create user', 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        let errorMsg = 'An error occurred while creating user';
        
        // Handle validation errors
        if (error.errors) {
            errorMsg = Object.values(error.errors).flat().join('<br>');
        } else if (error.message) {
            errorMsg = error.message;
        }
        
        Swal.fire({
            icon: 'error',
            title: '{{ __('users.error') }}',
            html: errorMsg
        });
    });
});

// === EDIT USER MODAL ===
// Image upload for edit user
document.getElementById('eu_pick')?.addEventListener('click', function() {
    document.getElementById('eu_image').click();
});

document.getElementById('eu_image')?.addEventListener('change', function(e) {
    const file = e.target.files[0];
    if (file) {
        // Validate file size (2MB max)
        if (file.size > 2 * 1024 * 1024) {
            Swal.fire('{{ __('users.error') }}', '{{ __('users.image_size_error') }}', 'error');
            this.value = '';
            return;
        }
        
        // Validate file type
        if (!['image/png', 'image/jpeg', 'image/jpg', 'image/webp'].includes(file.type)) {
            Swal.fire('{{ __('users.error') }}', '{{ __('users.image_type_error') }}', 'error');
            this.value = '';
            return;
        }
        
        // Preview image
        const reader = new FileReader();
        reader.onload = function(e) {
            document.getElementById('eu_preview').src = e.target.result;
            document.getElementById('eu_remove_image').value = '0';
        };
        reader.readAsDataURL(file);
    }
});

// Clear image for edit user
document.getElementById('eu_clear_img')?.addEventListener('click', function() {
    document.getElementById('eu_preview').src = '/images/users/avatar-1.jpg';
    document.getElementById('eu_image').value = '';
    document.getElementById('eu_remove_image').value = '1';
});
</script>
@endsection

@section('script-bottom')
  @vite(['node_modules/choices.js/public/assets/scripts/choices.min.js'])
@endsection