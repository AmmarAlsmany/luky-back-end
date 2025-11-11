@extends('layouts.auth', ['title' => __('auth.forgot_password_title')])

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
      <h2 class="fw-bold fs-24 text-center mb-1 luky-text">{{ __('auth.forgot_password_title') }}</h2>
      <p class="text-muted text-center mb-4">
        {{ __('auth.forgot_password_description') }}
      </p>

      <!-- Success Message -->
      @if (session('status'))
        <div class="alert alert-success mb-3">
          {{ session('status') }}
        </div>
      @endif

      <!-- Form -->
      <form method="POST" action="{{ route('password.email') }}">
        @csrf

        <!-- Email -->
        <div class="mb-3">
          <label class="form-label" for="email">{{ __('auth.email') }}</label>
          <input type="email" id="email" name="email" class="form-control @error('email') is-invalid @enderror"
                 placeholder="{{ __('auth.enter_email') }}" value="{{ old('email') }}" required autofocus>
          @error('email')
            <div class="invalid-feedback">
              {{ $message }}
            </div>
          @enderror
        </div>

        <!-- Submit Button -->
        <div class="d-grid mb-3">
          <button class="btn btn-primary" type="submit">{{ __('auth.send_reset_link') }}</button>
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
