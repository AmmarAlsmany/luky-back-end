@extends('layouts.vertical', ['title' => __('providers.pending_providers')])

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

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <div class="row align-items-center">
                    <div class="col">
                        <h4 class="card-title">{{ __('providers.pending_provider_approvals') }}</h4>
                        <p class="text-muted mb-0">{{ __('providers.review_approve_applications') }}</p>
                    </div>
                    <div class="col-auto">
                        <span class="badge bg-warning fs-14">{{ __('providers.pending_count', ['count' => count($providers ?? [])]) }}</span>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover table-nowrap mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>{{ __('providers.provider') }}</th>
                                <th>{{ __('providers.business_type') }}</th>
                                <th>{{ __('providers.city') }}</th>
                                <th>{{ __('providers.phone') }}</th>
                                <th>{{ __('providers.submitted') }}</th>
                                <th>{{ __('providers.documents') }}</th>
                                <th class="text-center">{{ __('providers.actions') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                        @forelse($providers as $provider)
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <img src="{{ $provider['providerProfile']->logo_url ?? '/images/default-business.svg' }}"
                                             class="avatar-sm rounded-circle me-2 object-fit-cover"
                                             alt="{{ $provider['business_name'] ?? 'Provider' }}">
                                        <div>
                                            <span class="fw-semibold">{{ $provider['business_name'] ?? $provider['name'] ?? __('providers.n_a') }}</span>
                                            <br>
                                            <small class="text-muted">{{ $provider['email'] ?? __('providers.n_a') }}</small>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <span class="badge bg-info-subtle text-info">
                                        {{ ucfirst($provider['business_type'] ?? __('providers.general')) }}
                                    </span>
                                </td>
                                <td>{{ $provider['city']['name_en'] ?? $provider['city']['name_ar'] ?? __('providers.n_a') }}</td>
                                <td>{{ $provider['phone'] ?? __('providers.n_a') }}</td>
                                <td>{{ isset($provider['created_at']) ? \Carbon\Carbon::parse($provider['created_at'])->format('M d, Y') : __('providers.n_a') }}</td>
                                <td>
                                    @if(!empty($provider['documents']) && is_array($provider['documents']))
                                        <button type="button" class="btn btn-sm btn-soft-primary"
                                                onclick="viewDocuments({{ json_encode($provider['documents']) }}, '{{ $provider['business_name'] ?? __('providers.provider') }}')">
                                            <i class="bx bx-file me-1"></i>
                                            {{ __('providers.files', ['count' => count($provider['documents'])]) }}
                                        </button>
                                    @else
                                        <span class="text-muted">{{ __('providers.no_documents') }}</span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    <div class="d-flex gap-2 justify-content-center">
                                        <a href="{{ route('providers.show', ['id' => $provider['id']]) }}"
                                           class="btn btn-light btn-sm"
                                           title="{{ __('providers.view_details') }}">
                                            <iconify-icon icon="solar:eye-broken" class="align-middle fs-18"></iconify-icon>
                                        </a>
                                        <button type="button"
                                                class="btn btn-success btn-sm"
                                                onclick="showApprovalModal({{ $provider['id'] }}, '{{ $provider['business_name'] ?? __('providers.provider') }}', 'approve')"
                                                title="{{ __('providers.approve') }}">
                                            <iconify-icon icon="solar:check-circle-bold" class="align-middle fs-18"></iconify-icon>
                                        </button>
                                        <button type="button"
                                                class="btn btn-danger btn-sm"
                                                onclick="showApprovalModal({{ $provider['id'] }}, '{{ $provider['business_name'] ?? __('providers.provider') }}', 'reject')"
                                                title="{{ __('providers.reject') }}">
                                            <iconify-icon icon="solar:close-circle-bold" class="align-middle fs-18"></iconify-icon>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center py-5">
                                    <div class="text-muted">
                                        <iconify-icon icon="solar:inbox-line-broken" class="fs-48 mb-2"></iconify-icon>
                                        <p>{{ __('providers.no_pending_providers') }}</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Documents Viewer Modal -->
<div class="modal fade" id="documentsModal" tabindex="-1" aria-labelledby="documentsModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="documentsModalLabel">
                    <i class="bx bx-file me-2"></i>{{ __('providers.provider_documents') }}
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="{{ __('providers.close') }}"></button>
            </div>
            <div class="modal-body" id="documentsContent">
                <!-- Documents will be loaded here -->
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('providers.close') }}</button>
            </div>
        </div>
    </div>
</div>

<!-- Approval/Rejection Modal -->
<div class="modal fade" id="approvalModal" tabindex="-1" aria-labelledby="approvalModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="approvalModalLabel">{{ __('providers.confirm_action') }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="{{ __('providers.close') }}"></button>
            </div>
            <form id="approvalForm" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <input type="hidden" name="action" id="approvalAction">

                    <div class="alert alert-info" id="approvalMessage">
                        <!-- Message will be set dynamically -->
                    </div>

                    <div class="mb-3">
                        <label for="approvalNotes" class="form-label">{{ __('providers.notes_optional') }}</label>
                        <textarea class="form-control" id="approvalNotes" name="notes" rows="3"
                                  placeholder="{{ __('providers.add_notes_reasons') }}"></textarea>
                        <small class="text-muted">{{ __('providers.notes_record_keeping') }}</small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('providers.cancel') }}</button>
                    <button type="submit" class="btn" id="approvalSubmitBtn">{{ __('providers.confirm') }}</button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection

@section('script-bottom')
<script>
function viewDocuments(documents, providerName) {
    const modal = new bootstrap.Modal(document.getElementById('documentsModal'));
    const content = document.getElementById('documentsContent');

    let html = '<div class="list-group">';

    if (documents && documents.length > 0) {
        documents.forEach((doc, index) => {
            const fileName = doc.name || doc.file_name || `{{ __('providers.document_number', ['number' => '${index + 1}']) }}`;
            const fileUrl = doc.url || doc.file_url || '#';
            const fileType = doc.type || '{{ __('providers.unknown') }}';

            html += `
                <div class="list-group-item">
                    <div class="d-flex align-items-center">
                        <div class="avatar-sm bg-light rounded me-3">
                            <iconify-icon icon="solar:file-text-bold" class="fs-24 text-primary avatar-title"></iconify-icon>
                        </div>
                        <div class="flex-grow-1">
                            <h6 class="mb-1">${fileName}</h6>
                            <small class="text-muted">${fileType}</small>
                        </div>
                        <a href="${fileUrl}" target="_blank" class="btn btn-sm btn-primary">
                            <i class="bx bx-download me-1"></i>{{ __('providers.view_document') }}
                        </a>
                    </div>
                </div>
            `;
        });
    } else {
        html += '<p class="text-muted text-center py-3">{{ __('providers.no_documents_available') }}</p>';
    }

    html += '</div>';
    content.innerHTML = html;

    document.getElementById('documentsModalLabel').innerHTML =
        `<i class="bx bx-file me-2"></i>${providerName} - {{ __('providers.documents') }}`;

    modal.show();
}

function showApprovalModal(providerId, providerName, action) {
    const modal = new bootstrap.Modal(document.getElementById('approvalModal'));
    const form = document.getElementById('approvalForm');
    const submitBtn = document.getElementById('approvalSubmitBtn');
    const message = document.getElementById('approvalMessage');

    // Set form action URL
    form.action = `/provider/${providerId}/verify`;

    // Set action type
    document.getElementById('approvalAction').value = action;

    // Update modal content based on action
    if (action === 'approve') {
        document.getElementById('approvalModalLabel').textContent = '{{ __('providers.approve_provider') }}';
        message.className = 'alert alert-success';
        message.innerHTML = `<i class="bx bx-check-circle me-2"></i>{!! __('providers.approve_confirm_msg', ['name' => '${providerName}']) !!}`;
        submitBtn.className = 'btn btn-success';
        submitBtn.innerHTML = '<i class="bx bx-check me-1"></i>{{ __('providers.approve') }}';
    } else {
        document.getElementById('approvalModalLabel').textContent = '{{ __('providers.reject_provider') }}';
        message.className = 'alert alert-danger';
        message.innerHTML = `<i class="bx bx-error-circle me-2"></i>{!! __('providers.reject_confirm_msg', ['name' => '${providerName}']) !!}`;
        submitBtn.className = 'btn btn-danger';
        submitBtn.innerHTML = '<i class="bx bx-x me-1"></i>{{ __('providers.reject') }}';
    }

    // Clear notes
    document.getElementById('approvalNotes').value = '';

    modal.show();
}
</script>
@endsection
