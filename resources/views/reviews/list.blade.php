@extends('layouts.vertical', ['title' => __('reviews.reviews_management')])
@section('css')
  @vite(['node_modules/choices.js/public/assets/styles/choices.min.css'])
@endsection

@section('content')

{{-- ===== Portion 1: Filters ===== --}}
<div class="row">
  <div class="col-12">
    <div class="card">
      <div class="card-header border-bottom-0 pb-0">
        <h4 class="card-title mb-2">{{ __('reviews.reviews_management') }}</h4>
        <p class="text-muted mb-0">{{ $stats['total_reviews'] }} {{ __('reviews.total_reviews') }} • {{ $stats['avg_rating'] }}★ {{ __('reviews.average_rating') }} • {{ $stats['flagged_reviews'] }} {{ __('reviews.flagged') }}</p>
      </div>

      <div class="card-body pt-2">
        <form method="GET" action="{{ route('reviews.index') }}" class="row g-3 align-items-end">
          <!-- Search Provider -->
          <div class="col-12 col-md-4">
            <label for="q" class="form-label small mb-1">{{ __('reviews.search_provider') }}</label>
            <input id="q" name="search" type="search" class="form-control" placeholder="{{ __('reviews.type_provider_name') }}" value="{{ $filters['search'] ?? '' }}">
          </div>

          <!-- City -->
          <div class="col-12 col-md-3">
            <label class="form-label small mb-1">{{ __('reviews.city') }}</label>
            <select name="city" class="form-select" data-choices data-choices-search-true>
              <option value="">{{ __('reviews.all_cities') }}</option>
              @foreach($cities as $city)
                <option value="{{ $city->id }}" {{ ($filters['city'] ?? '') == $city->id ? 'selected' : '' }}>
                  {{ app()->getLocale() == 'ar' ? $city->name_ar : $city->name_en }}
                </option>
              @endforeach
            </select>
          </div>

          <!-- Main Rating -->
          <div class="col-12 col-md-3">
            <label class="form-label small mb-1">{{ __('reviews.minimum_rating') }}</label>
            <select name="min_rating" class="form-select" data-choices data-choices-search-false>
              <option value="0" {{ ($filters['min_rating'] ?? 0) == 0 ? 'selected' : '' }}>{{ __('reviews.any_rating') }}</option>
              <option value="5" {{ ($filters['min_rating'] ?? 0) == 5 ? 'selected' : '' }}>5★</option>
              <option value="4" {{ ($filters['min_rating'] ?? 0) == 4 ? 'selected' : '' }}>4★+</option>
              <option value="3" {{ ($filters['min_rating'] ?? 0) == 3 ? 'selected' : '' }}>3★+</option>
              <option value="2" {{ ($filters['min_rating'] ?? 0) == 2 ? 'selected' : '' }}>2★+</option>
              <option value="1" {{ ($filters['min_rating'] ?? 0) == 1 ? 'selected' : '' }}>1★+</option>
            </select>
          </div>

          <div class="col-12 col-md-2">
            <button type="submit" class="btn btn-primary w-100"><i class="mdi mdi-filter-variant me-1"></i> {{ __('reviews.filter') }}</button>
          </div>
        </form>
      </div> {{-- /card-body --}}
    </div>
  </div>
</div>


{{-- ===== Portion 2: Table ===== --}}
<div class="row">
  <div class="col-12">
    <div class="card">
      <div class="card-body p-0">
        <div class="table-responsive">
          <table class="table align-middle mb-0 table-hover table-centered">
            <thead class="bg-light-subtle">
              <tr>
                <th style="width:20px;">
                  <div class="form-check">
                    <input type="checkbox" class="form-check-input" id="checkAll">
                    <label class="form-check-label" for="checkAll"></label>
                  </div>
                </th>
                <th>{{ __('reviews.provider') }}</th>
                <th>{{ __('reviews.city') }}</th>
                <th>{{ __('reviews.category') }}</th>
                <th>{{ __('reviews.avg_rating_short') }}</th>
                <th>{{ __('reviews.reviews') }}</th>
                <th>{{ __('reviews.last_review') }}</th>
                <th>{{ __('reviews.status') }}</th>
                <th>{{ __('reviews.action') }}</th>
              </tr>
            </thead>

            <tbody id="providers_tbody">
              @forelse($providers as $provider)
              <tr>
                <td>
                  <div class="form-check"><input type="checkbox" class="form-check-input"><label class="form-check-label">&nbsp;</label></div>
                </td>
                <td>
                  <div class="d-flex align-items-center">
                    @if($provider->profile_image)
                      <img src="{{ $provider->profile_image }}" class="avatar-sm rounded-circle me-2" alt="{{ $provider->name }}">
                    @else
                      <div class="avatar-sm bg-primary bg-opacity-10 rounded-circle me-2 d-flex align-items-center justify-content-center">
                        <span class="text-primary fw-bold">{{ strtoupper(substr($provider->name, 0, 1)) }}</span>
                      </div>
                    @endif
                    <div>
                      <div class="fw-semibold">{{ $provider->name }}</div>
                      <small class="text-muted">{{ $provider->business_name ?? __('reviews.provider') }}</small>
                    </div>
                  </div>
                </td>
                <td>{{ $provider->city->name_en ?? __('reviews.n_a') }}</td>
                <td>{{ $provider->providerProfile->business_type ?? __('reviews.n_a') }}</td>
                <td>
                  @php
                    $rating = round($provider->avg_rating ?? 0, 1);
                    $fullStars = floor($rating);
                    $halfStar = ($rating - $fullStars) >= 0.5;
                    $emptyStars = 5 - $fullStars - ($halfStar ? 1 : 0);
                  @endphp
                  <div class="d-inline-flex align-items-center gap-1">
                    @for($i = 0; $i < $fullStars; $i++)
                      <iconify-icon icon="mdi:star" class="text-warning fs-5"></iconify-icon>
                    @endfor
                    @if($halfStar)
                      <iconify-icon icon="mdi:star-half-full" class="text-warning fs-5"></iconify-icon>
                    @endif
                    @for($i = 0; $i < $emptyStars; $i++)
                      <iconify-icon icon="mdi:star-outline" class="text-warning fs-5"></iconify-icon>
                    @endfor
                    <small class="text-muted ms-1">{{ $rating }}</small>
                  </div>
                </td>
                <td>{{ $provider->reviews_count }}</td>
                <td>
                  @if($provider->latest_review)
                    {{ $provider->latest_review->created_at->format('d M Y, h:i A') }}
                  @else
                    <span class="text-muted">{{ __('reviews.no_reviews_yet') }}</span>
                  @endif
                </td>
                <td>
                  @if($provider->is_active)
                    <span class="badge bg-success-subtle text-success">{{ __('reviews.active') }}</span>
                  @else
                    <span class="badge bg-danger-subtle text-danger">{{ __('reviews.inactive') }}</span>
                  @endif
                </td>
                <td>
                  <div class="d-flex gap-2">
                    <a href="{{ route('reviews.show', $provider->id) }}" class="btn btn-soft-primary btn-sm" title="{{ __('reviews.view_reviews') }}">
                      <iconify-icon icon="solar:eye-bold" class="fs-18"></iconify-icon>
                    </a>
                  </div>
                </td>
              </tr>
              @empty
              <tr>
                <td colspan="9" class="text-center py-4">
                  <div class="text-muted">
                    <iconify-icon icon="solar:inbox-line-broken" class="fs-1 mb-2"></iconify-icon>
                    <p class="mb-0">{{ __('reviews.no_providers_found') }}</p>
                  </div>
                </td>
              </tr>
              @endforelse
            </tbody>
          </table>
        </div>
      </div>

      <div class="card-footer border-top">
        @if($providers->hasPages())
        <nav aria-label="Page navigation">
          {{ $providers->links('pagination::bootstrap-5') }}
        </nav>
        @else
        <div class="text-muted text-center py-2">
          {{ __('reviews.showing_providers', ['count' => $providers->total()]) }}
        </div>
        @endif
      </div>

    </div>
  </div>
</div>

@endsection

@section('script-bottom')
  @vite(['node_modules/choices.js/public/assets/scripts/choices.min.js'])
  <script>
    // Initialize Choices for select dropdowns
    document.querySelectorAll('select[data-choices]').forEach(function (el) {
      new Choices(el, {
        allowHTML: false,
        shouldSort: false,
        searchEnabled: el.getAttribute('data-choices-search-true') !== null
      });
    });

    // Select all checkboxes
    document.getElementById('checkAll')?.addEventListener('change', function() {
      const checkboxes = document.querySelectorAll('tbody input[type="checkbox"]');
      checkboxes.forEach(checkbox => {
        checkbox.checked = this.checked;
      });
    });
  </script>
@endsection
