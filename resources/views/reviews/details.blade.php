@extends('layouts.vertical', ['title' => __('reviews.provider_reviews')])

@section('content')
<div class="container-fluid">
    
    <!-- Page Header -->
    <div class="row">
        <div class="col-12">
            <div class="page-title-box">
                <div class="page-title-right">
                    <a href="{{ route('reviews.index') }}" class="btn btn-outline-primary">
                        <i class="mdi mdi-arrow-left me-1"></i> {{ __('reviews.back_to_reviews') }}
                    </a>
                </div>
                <h4 class="page-title">{{ __('reviews.provider_reviews') }}</h4>
            </div>
        </div>
    </div>

    <!-- Provider Info Card -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col-md-8">
                            <h4 class="mb-1">{{ $provider->business_name }}</h4>
                            <p class="text-muted mb-2">
                                <i class="mdi mdi-map-marker me-1"></i>
                                {{ $provider->address ?? __('reviews.n_a') }}
                            </p>
                            <div class="d-flex gap-3">
                                <span class="badge bg-info-subtle text-info">
                                    {{ ucfirst(str_replace('_', ' ', $provider->business_type)) }}
                                </span>
                                <span class="badge bg-success-subtle text-success">
                                    {{ ucfirst($provider->verification_status) }}
                                </span>
                            </div>
                        </div>
                        <div class="col-md-4 text-end">
                            <div class="mb-2">
                                <h2 class="mb-0">{{ $stats['avg_rating'] ?? 0 }} <small class="text-muted">/ 5.0</small></h2>
                                <p class="text-muted mb-0">{{ __('reviews.avg_rating') }}</p>
                            </div>
                            <p class="text-muted mb-0">{{ $stats['total_reviews'] }} {{ __('reviews.total_reviews_count') }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="row">
        <div class="col-md-2">
            <div class="card">
                <div class="card-body text-center">
                    <i class="mdi mdi-star text-warning fs-2"></i>
                    <h3 class="mt-2 mb-0">{{ $stats['rating_breakdown'][5] ?? 0 }}</h3>
                    <p class="text-muted mb-0">{{ __('reviews.5_stars') }}</p>
                </div>
            </div>
        </div>
        <div class="col-md-2">
            <div class="card">
                <div class="card-body text-center">
                    <i class="mdi mdi-star text-warning fs-2"></i>
                    <h3 class="mt-2 mb-0">{{ $stats['rating_breakdown'][4] ?? 0 }}</h3>
                    <p class="text-muted mb-0">{{ __('reviews.4_stars') }}</p>
                </div>
            </div>
        </div>
        <div class="col-md-2">
            <div class="card">
                <div class="card-body text-center">
                    <i class="mdi mdi-star text-warning fs-2"></i>
                    <h3 class="mt-2 mb-0">{{ $stats['rating_breakdown'][3] ?? 0 }}</h3>
                    <p class="text-muted mb-0">{{ __('reviews.3_stars') }}</p>
                </div>
            </div>
        </div>
        <div class="col-md-2">
            <div class="card">
                <div class="card-body text-center">
                    <i class="mdi mdi-star text-warning fs-2"></i>
                    <h3 class="mt-2 mb-0">{{ $stats['rating_breakdown'][2] ?? 0 }}</h3>
                    <p class="text-muted mb-0">{{ __('reviews.2_stars') }}</p>
                </div>
            </div>
        </div>
        <div class="col-md-2">
            <div class="card">
                <div class="card-body text-center">
                    <i class="mdi mdi-star text-warning fs-2"></i>
                    <h3 class="mt-2 mb-0">{{ $stats['rating_breakdown'][1] ?? 0 }}</h3>
                    <p class="text-muted mb-0">{{ __('reviews.1_star') }}</p>
                </div>
            </div>
        </div>
        <div class="col-md-2">
            <div class="card">
                <div class="card-body text-center">
                    <i class="mdi mdi-flag text-danger fs-2"></i>
                    <h3 class="mt-2 mb-0">{{ $stats['flagged'] ?? 0 }}</h3>
                    <p class="text-muted mb-0">{{ __('reviews.flagged') }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Reviews List -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title mb-0">{{ __('reviews.customer_reviews') }}</h4>
                </div>
                <div class="card-body">
                    @forelse($reviews as $review)
                    <div class="review-item border-bottom pb-3 mb-3">
                        <div class="d-flex justify-content-between align-items-start">
                            <div class="d-flex gap-3">
                                <div class="avatar-sm bg-primary-subtle text-primary rounded-circle d-flex align-items-center justify-content-center">
                                    <span class="fw-bold">{{ strtoupper(substr($review->client->name ?? 'U', 0, 1)) }}</span>
                                </div>
                                <div>
                                    <h5 class="mb-1">{{ $review->client->name ?? __('reviews.unknown') }}</h5>
                                    <div class="mb-2">
                                        @for($i = 1; $i <= 5; $i++)
                                            @if($i <= $review->rating)
                                                <i class="mdi mdi-star text-warning"></i>
                                            @else
                                                <i class="mdi mdi-star-outline text-muted"></i>
                                            @endif
                                        @endfor
                                        <span class="text-muted ms-2">{{ $review->created_at->diffForHumans() }}</span>
                                    </div>
                                    @if($review->comment)
                                        <p class="text-muted mb-2">{{ $review->comment }}</p>
                                    @else
                                        <p class="text-muted fst-italic mb-2">{{ __('reviews.no_comment_provided') }}</p>
                                    @endif

                                    <div class="d-flex align-items-center gap-3">
                                        @if($review->booking)
                                            <small class="text-muted">
                                                <i class="mdi mdi-calendar me-1"></i>
                                                {{ __('reviews.booking') }}: #{{ $review->booking->booking_number }}
                                            </small>
                                        @endif

                                        {{-- Approval Status Badge --}}
                                        @if($review->approval_status === 'approved')
                                            <span class="badge bg-success-subtle text-success">
                                                <i class="mdi mdi-check-circle me-1"></i>{{ __('reviews.approved') }}
                                            </span>
                                        @elseif($review->approval_status === 'rejected')
                                            <span class="badge bg-danger-subtle text-danger">
                                                <i class="mdi mdi-close-circle me-1"></i>{{ __('reviews.rejected') }}
                                            </span>
                                        @else
                                            <span class="badge bg-warning-subtle text-warning">
                                                <i class="mdi mdi-clock-outline me-1"></i>{{ __('reviews.pending_approval') }}
                                            </span>
                                        @endif
                                    </div>

                                    {{-- Rejection Reason --}}
                                    @if($review->rejection_reason)
                                        <div class="mt-2 p-2 bg-danger-subtle rounded">
                                            <small class="text-danger">
                                                <strong>{{ __('reviews.rejection_reason') }}:</strong> {{ $review->rejection_reason }}
                                            </small>
                                        </div>
                                    @endif

                                    @if($review->admin_response)
                                        <div class="mt-3 p-3 bg-light rounded">
                                            <strong class="text-primary">{{ __('reviews.admin_response') }}:</strong>
                                            <p class="mb-0 mt-1">{{ $review->admin_response }}</p>
                                            <small class="text-muted">{{ $review->responded_at->diffForHumans() }}</small>
                                        </div>
                                    @endif
                                </div>
                            </div>
                            <div class="d-flex gap-2 flex-wrap">
                                @if($review->is_flagged)
                                    <span class="badge bg-danger">{{ __('reviews.flagged_badge') }}</span>
                                @endif

                                {{-- Approval Buttons --}}
                                @if($review->approval_status === 'pending')
                                    <button class="btn btn-sm btn-success approve-review" data-id="{{ $review->id }}" title="{{ __('reviews.approve_review') }}">
                                        <i class="mdi mdi-check"></i> {{ __('reviews.approve') }}
                                    </button>
                                    <button class="btn btn-sm btn-danger reject-review" data-id="{{ $review->id }}" title="{{ __('reviews.reject_review') }}">
                                        <i class="mdi mdi-close"></i> {{ __('reviews.reject') }}
                                    </button>
                                @elseif($review->approval_status === 'approved')
                                    <button class="btn btn-sm btn-warning reject-review" data-id="{{ $review->id }}" title="{{ __('reviews.reject_review') }}">
                                        <i class="mdi mdi-close"></i> {{ __('reviews.reject') }}
                                    </button>
                                @elseif($review->approval_status === 'rejected')
                                    <button class="btn btn-sm btn-success approve-review" data-id="{{ $review->id }}" title="{{ __('reviews.approve_review') }}">
                                        <i class="mdi mdi-check"></i> {{ __('reviews.approve') }}
                                    </button>
                                @endif

                                <button class="btn btn-sm btn-outline-warning toggle-flag" data-id="{{ $review->id }}" data-flagged="{{ $review->is_flagged ? 1 : 0 }}" title="{{ __('reviews.toggle_flag') }}">
                                    <i class="mdi mdi-flag"></i> {{ __('reviews.flag') }}
                                </button>
                                <button class="btn btn-sm btn-outline-primary add-response" data-id="{{ $review->id }}" data-response="{{ $review->admin_response ?? '' }}" title="{{ __('reviews.add_response') }}">
                                    <i class="mdi mdi-reply"></i> {{ __('reviews.respond') }}
                                </button>
                                <button class="btn btn-sm btn-outline-danger delete-review" data-id="{{ $review->id }}" title="{{ __('reviews.delete_review') }}">
                                    <i class="mdi mdi-delete"></i> {{ __('reviews.delete') }}
                                </button>
                            </div>
                        </div>
                    </div>
                    @empty
                    <div class="text-center py-5">
                        <i class="mdi mdi-comment-remove-outline fs-1 text-muted"></i>
                        <p class="text-muted mt-2">{{ __('reviews.no_reviews_yet') }}</p>
                    </div>
                    @endforelse

                    <!-- Pagination -->
                    @if($reviews->hasPages())
                    <div class="mt-3">
                        {{ $reviews->links() }}
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

</div>

<!-- Response Modal -->
<div class="modal fade" id="responseModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">{{ __('reviews.add_admin_response') }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <textarea class="form-control" id="adminResponse" rows="4" placeholder="{{ __('reviews.enter_your_response') }}"></textarea>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('reviews.cancel') }}</button>
                <button type="button" class="btn btn-primary" id="saveResponse">{{ __('reviews.save_response') }}</button>
            </div>
        </div>
    </div>
</div>

<!-- Rejection Modal -->
<div class="modal fade" id="rejectModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-danger-subtle">
                <h5 class="modal-title text-danger">
                    <i class="mdi mdi-close-circle me-2"></i>{{ __('reviews.reject_review') }}
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p class="text-muted">{{ __('reviews.rejection_reason_prompt') }}</p>
                <textarea class="form-control" id="rejectionReason" rows="3" placeholder="{{ __('reviews.enter_rejection_reason') }}"></textarea>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('reviews.cancel') }}</button>
                <button type="button" class="btn btn-danger" id="confirmReject">
                    <i class="mdi mdi-close me-1"></i>{{ __('reviews.confirm_reject') }}
                </button>
            </div>
        </div>
    </div>
</div>

@endsection

@section('script-bottom')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        console.log('Review actions initialized');

        let currentReviewId = null;
        let responseModal = null;
        let rejectModal = null;

        // Initialize Bootstrap modals
        const responseModalEl = document.getElementById('responseModal');
        const rejectModalEl = document.getElementById('rejectModal');

        if (responseModalEl) {
            responseModal = new bootstrap.Modal(responseModalEl);
            console.log('Response modal initialized');
        }
        if (rejectModalEl) {
            rejectModal = new bootstrap.Modal(rejectModalEl);
            console.log('Reject modal initialized');
        }

        // Helper function for AJAX requests
        function makeAjaxRequest(url, method, data, successCallback, errorCallback) {
            fetch(url, {
                method: method,
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json',
                },
                body: method !== 'GET' ? JSON.stringify(data) : null
            })
            .then(response => response.json())
            .then(data => {
                if (successCallback) successCallback(data);
            })
            .catch(error => {
                if (errorCallback) errorCallback(error);
            });
        }

        // Toggle Flag
        document.querySelectorAll('.toggle-flag').forEach(button => {
            button.addEventListener('click', function() {
                console.log('Toggle flag clicked');
                const reviewId = this.getAttribute('data-id');
                console.log('Review ID:', reviewId);

                makeAjaxRequest(
                    `/reviews/${reviewId}/toggle-flag`,
                    'POST',
                    {},
                    (response) => {
                        if(response.success) {
                            location.reload();
                        }
                    },
                    (error) => {
                        alert('{{ __('reviews.error') }}: Failed to toggle flag');
                    }
                );
            });
        });

        // Add Response
        document.querySelectorAll('.add-response').forEach(button => {
            button.addEventListener('click', function() {
                currentReviewId = this.getAttribute('data-id');
                const currentResponse = this.getAttribute('data-response');
                document.getElementById('adminResponse').value = currentResponse || '';
                if (responseModal) {
                    responseModal.show();
                }
            });
        });

        const saveResponseBtn = document.getElementById('saveResponse');
        if (saveResponseBtn) {
            saveResponseBtn.addEventListener('click', function() {
                const response = document.getElementById('adminResponse').value;

                makeAjaxRequest(
                    `/reviews/${currentReviewId}/response`,
                    'POST',
                    { admin_response: response },
                    (res) => {
                        if(res.success) {
                            if (responseModal) {
                                responseModal.hide();
                            }
                            location.reload();
                        }
                    },
                    (error) => {
                        alert('{{ __('reviews.error') }}: Failed to save response');
                    }
                );
            });
        }

        // Delete Review
        document.querySelectorAll('.delete-review').forEach(button => {
            button.addEventListener('click', function() {
                if(!confirm('{{ __('reviews.delete_review_confirm') }}')) return;

                const reviewId = this.getAttribute('data-id');

                makeAjaxRequest(
                    `/reviews/${reviewId}`,
                    'DELETE',
                    {},
                    (response) => {
                        if(response.success) {
                            location.reload();
                        }
                    },
                    (error) => {
                        alert('{{ __('reviews.error') }}: Failed to delete review');
                    }
                );
            });
        });

        // Approve Review
        document.querySelectorAll('.approve-review').forEach(button => {
            button.addEventListener('click', function() {
                if(!confirm('{{ __('reviews.approve_review_confirm') }}')) return;

                const reviewId = this.getAttribute('data-id');
                const buttonElement = this;

                makeAjaxRequest(
                    `/reviews/${reviewId}/approve`,
                    'POST',
                    {},
                    (response) => {
                        if(response.success) {
                            // Force reload with cache bypass
                            location.reload(true);
                        }
                    },
                    (error) => {
                        alert('{{ __('reviews.error_approving') }}');
                    }
                );
            });
        });

        // Reject Review
        document.querySelectorAll('.reject-review').forEach(button => {
            button.addEventListener('click', function() {
                currentReviewId = this.getAttribute('data-id');
                document.getElementById('rejectionReason').value = '';
                if (rejectModal) {
                    rejectModal.show();
                }
            });
        });

        const confirmRejectBtn = document.getElementById('confirmReject');
        if (confirmRejectBtn) {
            confirmRejectBtn.addEventListener('click', function() {
                const reason = document.getElementById('rejectionReason').value;

                if(!reason.trim()) {
                    alert('{{ __('reviews.rejection_reason_required') }}');
                    return;
                }

                makeAjaxRequest(
                    `/reviews/${currentReviewId}/reject`,
                    'POST',
                    { reason: reason },
                    (response) => {
                        if(response.success) {
                            if (rejectModal) {
                                rejectModal.hide();
                            }
                            // Force reload with cache bypass
                            location.reload(true);
                        }
                    },
                    (error) => {
                        alert('{{ __('reviews.error_rejecting') }}');
                    }
                );
            });
        }

        console.log('All event listeners attached');
    });
</script>
@endsection
