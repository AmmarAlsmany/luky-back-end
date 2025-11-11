@extends('layouts.auth', ['title' => __('auth.reset_password')])

@section('content')
<div class="min-vh-100 d-flex align-items-center justify-content-center p-3 position-relative"
     style="background: url('/images/luky/background.png') center center / cover no-repeat;">
  
  <!-- Language Switcher -->
  <div class="position-absolute top-0 end-0 m-3">
    <div class="dropdown">
      <button class="btn btn-light btn-sm dropdown-toggle" type="button" id="languageDropdown" data-bs-toggle="dropdown" aria-expanded="false">
        <i class="bx bx-globe"></i>
        @if(app()->getLocale() === 'ar')
          العربية
        @else
          English
        @endif
      </button>
      <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="languageDropdown">
        <li>
          <a class="dropdown-item {{ app()->getLocale() === 'en' ? 'active' : '' }}" href="{{ route('language.switch', 'en') }}">
            <i class="bx bx-check me-1 {{ app()->getLocale() === 'en' ? '' : 'invisible' }}"></i>
            English
          </a>
        </li>
        <li>
          <a class="dropdown-item {{ app()->getLocale() === 'ar' ? 'active' : '' }}" href="{{ route('language.switch', 'ar') }}">
            <i class="bx bx-check me-1 {{ app()->getLocale() === 'ar' ? '' : 'invisible' }}"></i>
            العربية
          </a>
        </li>
      </ul>
    </div>
  </div>

  <div class="card shadow-lg border-0" style="max-width: 460px; width: 100%;">
    <div class="card-body p-4 p-lg-5">

      <!-- Logo (centered) -->
      <div class="text-center mb-4">
        <a href="{{ route('login') }}" class="d-inline-block">
          <img src="/images/luky/logo.png" alt="logo">
        </a>
      </div>

      <!-- Title + subtitle -->
      <h2 class="fw-bold fs-24 text-center mb-1 luky-text">{{ __('auth.reset_password') }}</h2>
      <p class="text-muted text-center mb-4">
        {{ __('auth.reset_password_description') }}
      </p>

      <!-- Form -->
      <form method="POST" action="{{ route('password.update') }}">
        @csrf
        <input type="hidden" name="token" value="{{ $token }}">

        <!-- Email -->
        <div class="mb-3">
          <label class="form-label" for="email">{{ __('auth.email') }}</label>
          <input type="email" id="email" name="email" class="form-control @error('email') is-invalid @enderror"
                 placeholder="{{ __('auth.enter_email') }}" value="{{ old('email', $email ?? '') }}" required autofocus>
          @error('email')
            <div class="invalid-feedback">
              {{ $message }}
            </div>
          @enderror
        </div>

        <!-- Password -->
        <div class="mb-3">
          <label class="form-label" for="password">{{ __('auth.new_password') }}</label>
          <input type="password" id="password" name="password" class="form-control @error('password') is-invalid @enderror"
                 placeholder="{{ __('auth.enter_password') }}" required>
          <small class="text-muted">
            Must be at least 8 characters with uppercase, lowercase, and number
          </small>
          @error('password')
            <div class="invalid-feedback d-block">
              {{ $message }}
            </div>
          @enderror
        </div>

        <!-- Confirm Password -->
        <div class="mb-3">
          <label class="form-label" for="password_confirmation">{{ __('auth.confirm_password') }}</label>
          <input type="password" id="password_confirmation" name="password_confirmation" class="form-control"
                 placeholder="{{ __('auth.confirm_password_placeholder') }}" required>
        </div>

        <!-- Submit Button -->
        <div class="d-grid mb-3">
          <button class="btn btn-primary" type="submit">{{ __('auth.reset_password_button') }}</button>
        </div>

        <!-- Back to Login -->
        <div class="text-center">
          <a href="{{ route('login') }}" class="text-muted text-decoration-underline">
            <i class="bx bx-arrow-back"></i> {{ __('auth.back_to_login') }}
          </a>
        </div>
      </form>

    </div>
  </div>
</div>
@endsection
