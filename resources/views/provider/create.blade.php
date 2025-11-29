@extends('layouts.vertical', ['title' => __('providers.add_provider')])

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

@if($errors->any())
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <i class="bx bx-error-circle me-2"></i>
        <ul class="mb-0">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <div class="d-flex align-items-center justify-content-between">
                    <h4 class="card-title">{{ __('providers.add_provider') }}</h4>
                    <a href="{{ route('providers.index') }}" class="btn btn-sm btn-secondary">
                        <i class="bx bx-arrow-back me-1"></i>{{ __('common.back_to_list') }}
                    </a>
                </div>
            </div>
            <div class="card-body">
                <form action="{{ route('providers.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf

                    <div class="row">
                        <!-- Basic Information -->
                        <div class="col-lg-6">
                            <h5 class="mb-3">{{ __('providers.basic_information') }}</h5>

                            <div class="mb-3">
                                <label for="name" class="form-label">{{ __('common.full_name') }} <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="name" name="name"
                                       value="{{ old('name') }}" required>
                            </div>

                            <div class="mb-3">
                                <label for="email" class="form-label">{{ __('common.email') }} <span class="text-danger">*</span></label>
                                <input type="email" class="form-control" id="email" name="email"
                                       value="{{ old('email') }}" required>
                            </div>

                            <div class="mb-3">
                                <label for="phone" class="form-label">{{ __('common.phone_number') }} <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="phone" name="phone"
                                       value="{{ old('phone') }}" required>
                                <small class="text-muted">{{ __('providers.phone_otp_hint') }}</small>
                            </div>

                            <!-- Contract Details -->
                            <h5 class="mb-3 mt-4">
                                <i class="bx bx-file-blank me-2"></i>{{ __('providers.contract_details') }}
                            </h5>

                            <div class="mb-3">
                                <label for="contract_start_date" class="form-label">{{ __('providers.contract_start_date') }} <span class="text-danger">*</span></label>
                                <input type="date" class="form-control" id="contract_start_date" name="contract_start_date"
                                       value="{{ old('contract_start_date') }}" required>
                            </div>

                            <div class="mb-3">
                                <label for="contract_end_date" class="form-label">{{ __('providers.contract_end_date') }}</label>
                                <input type="date" class="form-control" id="contract_end_date" name="contract_end_date"
                                       value="{{ old('contract_end_date') }}">
                                <small class="text-muted">{{ __('providers.contract_end_date_hint') }}</small>
                            </div>

                            <div class="mb-3">
                                <label for="payment_terms" class="form-label">{{ __('providers.payment_terms') }}</label>
                                <textarea class="form-control" id="payment_terms" name="payment_terms" rows="2"
                                          placeholder="{{ __('providers.payment_terms_placeholder') }}">{{ old('payment_terms') }}</textarea>
                            </div>

                            <div class="mb-3">
                                <label for="contract_notes" class="form-label">{{ __('providers.contract_notes') }}</label>
                                <textarea class="form-control" id="contract_notes" name="contract_notes" rows="2"
                                          placeholder="{{ __('providers.contract_notes_placeholder') }}">{{ old('contract_notes') }}</textarea>
                            </div>
                        </div>

                        <!-- Business Information -->
                        <div class="col-lg-6">
                            <h5 class="mb-3">{{ __('providers.business_info') }}</h5>

                            <div class="mb-3">
                                <label for="business_name" class="form-label">{{ __('providers.business_name') }} <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="business_name" name="business_name"
                                       value="{{ old('business_name') }}" required>
                            </div>

                            <!-- Logo Upload -->
                            <div class="mb-3">
                                <label for="logo" class="form-label">
                                    <i class="bx bx-image-alt me-1"></i>{{ __('providers.logo') }}
                                </label>
                                <input type="file" class="form-control" id="logo" name="logo" 
                                       accept="image/jpeg,image/png,image/jpg">
                                <small class="text-muted">{{ __('providers.logo_hint') }}</small>
                            </div>

                            <!-- Building Image Upload -->
                            <div class="mb-3">
                                <label for="building_image" class="form-label">
                                    <i class="bx bx-buildings me-1"></i>{{ __('providers.building_image') }}
                                </label>
                                <input type="file" class="form-control" id="building_image" name="building_image"
                                       accept="image/jpeg,image/png,image/jpg">
                                <small class="text-muted">{{ __('providers.building_image_hint') }}</small>
                            </div>

                            <div class="mb-3">
                                <label for="category_id" class="form-label">{{ __('providers.category') }} <span class="text-danger">*</span></label>
                                <select class="form-select" id="category_id" name="category_id" required>
                                    <option value="">{{ __('providers.select_category') }}</option>
                                    @foreach($categories as $category)
                                        <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>
                                            {{ app()->getLocale() === 'ar' ? $category->name_ar : $category->name_en }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="mb-3">
                                <label for="description" class="form-label">{{ __('common.description') }}</label>
                                <textarea class="form-control" id="description" name="description" rows="3">{{ old('description') }}</textarea>
                            </div>
                        </div>
                    </div>

                    <!-- Location & Working Hours Section (Side by Side) -->
                    <hr class="my-4">
                    <h5 class="mb-4"><i class="bx bx-map-alt me-2"></i>{{ __('providers.location_and_hours') }}</h5>

                    <div class="row">
                        <!-- LEFT: Google Maps Location Picker -->
                        <div class="col-lg-6">
                            <h6 class="mb-3">
                                <i class="bx bx-map-pin me-1"></i>{{ __('providers.select_location') }} <span class="text-danger">*</span>
                            </h6>
                            <p class="text-muted small mb-3">
                                <i class="bx bx-info-circle me-1"></i>Search for an address or click on the map to select the provider's exact location
                            </p>

                            <!-- Search Box -->
                            <div class="mb-3">
                                <input type="text" class="form-control" id="map-search"
                                       placeholder="Search for address, business name, or landmark...">
                            </div>

                            <!-- Map Container -->
                            <div class="card mb-3">
                                <div class="card-body p-0">
                                    <div id="map" style="height: 400px; width: 100%; border-radius: 0.25rem;"></div>
                                </div>
                            </div>

                            <!-- Selected Location Info (Auto-populated) -->
                            <div class="card bg-light">
                                <div class="card-body">
                                    <h6 class="mb-3"><i class="bx bx-current-location me-2"></i>Selected Location</h6>

                                    <div class="mb-2">
                                        <label class="form-label small text-muted mb-1">Full Address</label>
                                        <input type="text" class="form-control form-control-sm" id="address" name="address"
                                               value="{{ old('address') }}" readonly required>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-12 mb-2">
                                            <label class="form-label small text-muted mb-1">City</label>
                                            <input type="text" class="form-control form-control-sm" id="city_display"
                                                   readonly placeholder="Auto-detected">
                                            <input type="hidden" id="city_id" name="city_id" value="{{ old('city_id') }}" required>
                                        </div>
                                        <div class="col-md-6 mb-2">
                                            <label class="form-label small text-muted mb-1">Latitude</label>
                                            <input type="text" class="form-control form-control-sm" id="latitude" name="latitude"
                                                   value="{{ old('latitude', '24.7136') }}" readonly required>
                                        </div>
                                        <div class="col-md-6 mb-2">
                                            <label class="form-label small text-muted mb-1">Longitude</label>
                                            <input type="text" class="form-control form-control-sm" id="longitude" name="longitude"
                                                   value="{{ old('longitude', '46.6753') }}" readonly required>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- RIGHT: Working Hours -->
                        <div class="col-lg-6">
                            <h5 class="mb-3">{{ __('providers.working_hours') }}</h5>

                            <div class="mb-3">
                                <label class="form-label">{{ __('providers.working_days') }} <span class="text-danger">*</span></label>

                                @php
                                $days = ['sunday', 'monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday'];
                                $dayLabels = [
                                    __('providers.sunday'),
                                    __('providers.monday'),
                                    __('providers.tuesday'),
                                    __('providers.wednesday'),
                                    __('providers.thursday'),
                                    __('providers.friday'),
                                    __('providers.saturday')
                                ];
                                @endphp

                                @foreach($days as $index => $day)
                                <div class="card mb-2">
                                    <div class="card-body p-3">
                                        <div class="d-flex align-items-center justify-content-between mb-2">
                                            <div class="form-check">
                                                <input class="form-check-input working-day-checkbox" type="checkbox"
                                                       id="day_{{ $day }}" name="working_days[]" value="{{ $day }}"
                                                       {{ old('working_days') && in_array($day, old('working_days')) ? 'checked' : '' }}>
                                                <label class="form-check-label fw-semibold" for="day_{{ $day }}">
                                                    {{ $dayLabels[$index] }}
                                                </label>
                                            </div>
                                        </div>
                                        <div class="row g-2 working-hours-inputs" style="display: none;">
                                            <div class="col-6">
                                                <label class="form-label">{{ __('providers.open_time') }}</label>
                                                <input type="time" class="form-control form-control-sm time-input-{{ $day }}"
                                                       name="working_hours[{{ $day }}][open]"
                                                       value="{{ old('working_hours.'.$day.'.open', '09:00') }}"
                                                       disabled>
                                            </div>
                                            <div class="col-6">
                                                <label class="form-label">{{ __('providers.close_time') }}</label>
                                                <input type="time" class="form-control form-control-sm time-input-{{ $day }}"
                                                       name="working_hours[{{ $day }}][close]"
                                                       value="{{ old('working_hours.'.$day.'.close', '18:00') }}"
                                                       disabled>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                @endforeach
                                <small class="text-muted">{{ __('providers.working_days_hint') }}</small>
                            </div>

                            <!-- Off Days / Holidays -->
                            <h6 class="mb-3 mt-4">{{ __('providers.special_off_days') }}</h6>
                            <div class="mb-3">
                                <label class="form-label">{{ __('providers.add_holidays') }}</label>
                                <div id="off-days-container">
                                    <div class="input-group mb-2">
                                        <input type="date" class="form-control" name="off_days[]"
                                               placeholder="Select date">
                                        <button type="button" class="btn btn-outline-success btn-add-off-day">
                                            <i class="bx bx-plus"></i>
                                        </button>
                                    </div>
                                </div>
                                <small class="text-muted">{{ __('providers.off_days_hint') }}</small>
                            </div>
                        </div>
                    </div>

                    <!-- Provider Documents -->
                    <div class="row">
                        <div class="col-12">
                            <hr class="my-4">
                            <h5 class="mb-3">
                                <i class="bx bx-file me-2"></i>{{ __('providers.provider_documents') }}
                            </h5>
                            <p class="text-muted">{{ __('providers.upload_documents_hint') }}</p>

                            <div class="row">
                                <!-- Freelance License -->
                                <div class="col-md-6 mb-3">
                                    <label for="freelance_license" class="form-label">
                                        <i class="bx bx-file-blank me-1"></i>{{ __('providers.freelance_license') }}
                                    </label>
                                    <input type="file" class="form-control" id="freelance_license" 
                                           name="documents[freelance_license]" accept=".pdf,.jpg,.jpeg,.png">
                                    <small class="text-muted">{{ __('providers.file_format_hint') }}</small>
                                </div>

                                <!-- Commercial Register -->
                                <div class="col-md-6 mb-3">
                                    <label for="commercial_register" class="form-label">
                                        <i class="bx bx-file-blank me-1"></i>{{ __('providers.commercial_register') }}
                                    </label>
                                    <input type="file" class="form-control" id="commercial_register" 
                                           name="documents[commercial_register]" accept=".pdf,.jpg,.jpeg,.png">
                                    <small class="text-muted">{{ __('providers.file_format_hint') }}</small>
                                </div>

                                <!-- Municipal License -->
                                <div class="col-md-6 mb-3">
                                    <label for="municipal_license" class="form-label">
                                        <i class="bx bx-file-blank me-1"></i>{{ __('providers.municipal_license') }}
                                    </label>
                                    <input type="file" class="form-control" id="municipal_license" 
                                           name="documents[municipal_license]" accept=".pdf,.jpg,.jpeg,.png">
                                    <small class="text-muted">{{ __('providers.file_format_hint') }}</small>
                                </div>

                                <!-- National ID -->
                                <div class="col-md-6 mb-3">
                                    <label for="national_id" class="form-label">
                                        <i class="bx bx-file-blank me-1"></i>{{ __('providers.national_id') }}
                                    </label>
                                    <input type="file" class="form-control" id="national_id" 
                                           name="documents[national_id]" accept=".pdf,.jpg,.jpeg,.png">
                                    <small class="text-muted">{{ __('providers.file_format_hint') }}</small>
                                </div>

                                <!-- Agreement Contract -->
                                <div class="col-md-6 mb-3">
                                    <label for="agreement_contract" class="form-label">
                                        <i class="bx bx-file-blank me-1"></i>{{ __('providers.agreement_contract') }}
                                    </label>
                                    <input type="file" class="form-control" id="agreement_contract" 
                                           name="documents[agreement_contract]" accept=".pdf,.jpg,.jpeg,.png">
                                    <small class="text-muted">{{ __('providers.file_format_hint') }}</small>
                                </div>
                            </div>
                        </div>
                    </div>

                    <hr class="my-4">

                    <div class="row">
                        <div class="col-12">
                            <div class="d-flex justify-content-end gap-2">
                                <a href="{{ route('providers.index') }}" class="btn btn-secondary">
                                    <i class="bx bx-x me-1"></i>{{ __('common.cancel') }}
                                </a>
                                <button type="submit" class="btn btn-primary">
                                    <i class="bx bx-save me-1"></i>{{ __('providers.create_provider') }}
                                </button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@endsection

@section('script-bottom')
<!-- Google Maps API -->
<script src="https://maps.googleapis.com/maps/api/js?key={{ config('services.google_maps.api_key') }}&libraries=places&callback=initMap" async defer></script>

<script>
let map;
let marker;
let geocoder;

// Cities database from backend
const citiesDatabase = @json($cities ?? []);

// Initialize Google Maps
function initMap() {
    // Default location (Riyadh, Saudi Arabia)
    const defaultLocation = {
        lat: parseFloat(document.getElementById('latitude').value) || 24.7136,
        lng: parseFloat(document.getElementById('longitude').value) || 46.6753
    };

    // Initialize geocoder
    geocoder = new google.maps.Geocoder();

    // Create map
    map = new google.maps.Map(document.getElementById('map'), {
        center: defaultLocation,
        zoom: 13,
        mapTypeControl: true,
        streetViewControl: false,
        fullscreenControl: true
    });

    // Create marker
    marker = new google.maps.Marker({
        position: defaultLocation,
        map: map,
        draggable: true,
        title: 'Drag me to exact location',
        animation: google.maps.Animation.DROP
    });

    // Add click listener to map
    map.addListener('click', function(event) {
        placeMarkerAndGetAddress(event.latLng);
    });

    // Add drag listener to marker
    marker.addListener('dragend', function(event) {
        getAddressFromLatLng(event.latLng);
    });

    // Initialize search box
    const searchInput = document.getElementById('map-search');
    const searchBox = new google.maps.places.SearchBox(searchInput);

    // Bias the search to the map's viewport
    map.addListener('bounds_changed', function() {
        searchBox.setBounds(map.getBounds());
    });

    // Listen for place selection from search
    searchBox.addListener('places_changed', function() {
        const places = searchBox.getPlaces();

        if (places.length === 0) {
            return;
        }

        const place = places[0];

        if (!place.geometry || !place.geometry.location) {
            return;
        }

        // Move map and marker
        map.setCenter(place.geometry.location);
        map.setZoom(17);
        marker.setPosition(place.geometry.location);

        // Update all fields
        updateLocationFields(place.geometry.location, place);
    });
}

function placeMarkerAndGetAddress(location) {
    marker.setPosition(location);
    marker.setAnimation(google.maps.Animation.BOUNCE);
    setTimeout(() => marker.setAnimation(null), 750);

    getAddressFromLatLng(location);
}

function getAddressFromLatLng(latLng) {
    geocoder.geocode({ location: latLng }, function(results, status) {
        if (status === 'OK' && results[0]) {
            updateLocationFields(latLng, results[0]);
        } else {
            // Still update coordinates even if address lookup fails
            updateCoordinates(latLng.lat(), latLng.lng());
            alert('Could not fetch address for this location. Please try a different location.');
        }
    });
}

function updateLocationFields(latLng, placeResult) {
    const lat = latLng.lat();
    const lng = latLng.lng();

    // Update coordinates
    document.getElementById('latitude').value = lat.toFixed(8);
    document.getElementById('longitude').value = lng.toFixed(8);

    // Get address components
    let fullAddress = placeResult.formatted_address || '';
    let cityName = '';
    let countryName = '';

    if (placeResult.address_components) {
        placeResult.address_components.forEach(component => {
            // Get city name - try multiple address component types
            if (component.types.includes('locality')) {
                cityName = component.long_name;
            } else if (!cityName && component.types.includes('administrative_area_level_2')) {
                cityName = component.long_name;
            } else if (!cityName && component.types.includes('administrative_area_level_1')) {
                cityName = component.long_name;
            }
            // Get country
            if (component.types.includes('country')) {
                countryName = component.long_name;
            }
        });
    }

    // Clean up city name (remove "Principality", "Region", "Province" etc.)
    cityName = cleanCityName(cityName);

    // Update address field
    document.getElementById('address').value = fullAddress;

    // Match city from database
    const matchedCity = matchCityFromDatabase(cityName);

    if (matchedCity) {
        document.getElementById('city_id').value = matchedCity.id;
        document.getElementById('city_display').value = matchedCity.name_en || matchedCity.name_ar;
    } else {
        document.getElementById('city_id').value = '';
        document.getElementById('city_display').value = cityName || 'Unknown City';
    }

    // Visual feedback
    showSuccessFeedback();
}

function cleanCityName(cityName) {
    if (!cityName) return '';

    // Remove common administrative suffixes
    const suffixesToRemove = [
        ' Principality',
        ' Region',
        ' Province',
        ' Emirate',
        ' Municipality',
        ' Metropolitan',
        ' City',
        ' الإمارة',
        ' المنطقة',
        ' الإقليم',
        ' البلدية'
    ];

    let cleanName = cityName;
    suffixesToRemove.forEach(suffix => {
        cleanName = cleanName.replace(new RegExp(suffix + '$', 'i'), '');
    });

    return cleanName.trim();
}

function matchCityFromDatabase(cityName) {
    if (!cityName || citiesDatabase.length === 0) return null;

    const cityLower = cityName.toLowerCase();

    // Try exact match first
    let matched = citiesDatabase.find(city =>
        (city.name_en && city.name_en.toLowerCase() === cityLower) ||
        (city.name_ar && city.name_ar === cityName)
    );

    // Try partial match
    if (!matched) {
        matched = citiesDatabase.find(city =>
            (city.name_en && city.name_en.toLowerCase().includes(cityLower)) ||
            (city.name_ar && city.name_ar.includes(cityName))
        );
    }

    return matched;
}

function updateCoordinates(lat, lng) {
    document.getElementById('latitude').value = lat.toFixed(8);
    document.getElementById('longitude').value = lng.toFixed(8);
}

function showSuccessFeedback() {
    const fields = ['address', 'city_display', 'latitude', 'longitude'];

    fields.forEach(fieldId => {
        const field = document.getElementById(fieldId);
        if (field) {
            field.classList.add('is-valid');
            setTimeout(() => field.classList.remove('is-valid'), 2000);
        }
    });
}

document.addEventListener('DOMContentLoaded', function() {
    // Working hours toggle
    const workingDayCheckboxes = document.querySelectorAll('.working-day-checkbox');
    workingDayCheckboxes.forEach(checkbox => {
        // Show/hide hours on page load based on old input
        const hoursDiv = checkbox.closest('.card-body').querySelector('.working-hours-inputs');
        const timeInputs = hoursDiv.querySelectorAll('input[type="time"]');

        if (checkbox.checked) {
            hoursDiv.style.display = 'flex';
            timeInputs.forEach(input => input.disabled = false);
        }

        // Toggle hours on checkbox change
        checkbox.addEventListener('change', function() {
            const hoursDiv = this.closest('.card-body').querySelector('.working-hours-inputs');
            const timeInputs = hoursDiv.querySelectorAll('input[type="time"]');

            if (this.checked) {
                hoursDiv.style.display = 'flex';
                // Enable time inputs so they get submitted
                timeInputs.forEach(input => input.disabled = false);
            } else {
                hoursDiv.style.display = 'none';
                // Disable time inputs so they DON'T get submitted (day is off)
                timeInputs.forEach(input => input.disabled = true);
            }
        });
    });

    // Off days management
    const offDaysContainer = document.getElementById('off-days-container');

    // Add new off day field
    offDaysContainer.addEventListener('click', function(e) {
        if (e.target.closest('.btn-add-off-day')) {
            const newField = document.createElement('div');
            newField.className = 'input-group mb-2';
            newField.innerHTML = `
                <input type="date" class="form-control" name="off_days[]" placeholder="{{ __('providers.select_date') }}">
                <button type="button" class="btn btn-outline-danger btn-remove-off-day">
                    <i class="bx bx-trash"></i>
                </button>
            `;
            offDaysContainer.appendChild(newField);
        }
    });

    // Remove off day field
    offDaysContainer.addEventListener('click', function(e) {
        if (e.target.closest('.btn-remove-off-day')) {
            e.target.closest('.input-group').remove();
        }
    });
});
</script>
@endsection
