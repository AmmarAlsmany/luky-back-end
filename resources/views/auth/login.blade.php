@extends('layouts.auth', ['title' => 'Login'])

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
        <a href="{{ route('second', [ 'lukydashboards' , 'index']) }}" class="d-inline-block">
          <!-- show dark/light variants if you want with a theme toggle; using dark for example -->
          <img src="/images/luky/logo.png"  alt="logo">
        </a>
      </div>

      <!-- Title + subtitle -->
      <h2 class="fw-bold fs-24 text-center mb-1 luky-text">{{ __('auth.welcome_to_luky') }}</h2>
      <p class="luky-text text-center mb-4 fw-bold">
        {{ __('auth.innovative_beauty_idea') }}
      </p>

      <!-- Form -->
      <form method="POST" action="{{ route('login') }}" class="authentication-form">
        @csrf

        @if (sizeof($errors) > 0)
          @foreach ($errors->all() as $error)
            <p class="text-danger mb-2">{{ $error }}</p>
          @endforeach
        @endif
        
        @if (session('error'))
          <div class="alert alert-danger">
            {{ session('error') }}
          </div>
        @endif

        @if (session('status'))
          <div class="alert alert-success">
            {{ session('status') }}
          </div>
        @endif

        <div class="mb-3">
          <label class="form-label" for="example-email">{{ __('auth.email') }}</label>
          <input type="email" id="example-email" name="email" class="form-control"
                 placeholder="{{ __('auth.enter_email') }}" value="{{ old('email') }}" required>
        </div>

        <div class="mb-3">
          <div class="d-flex justify-content-between align-items-center">
            <label class="form-label mb-0" for="example-password">{{ __('auth.password') }}</label>
            <a href="{{ route('password.request') }}"
               class="text-muted text-decoration-underline small">{{ __('auth.forgot_password') }}</a>
          </div>
          <input type="password" id="example-password" class="form-control"
                 placeholder="{{ __('auth.enter_password') }}" name="password" required>
        </div>

        <div class="mb-3 form-check">
          <input type="checkbox" class="form-check-input" id="checkbox-signin" name="remember">
          <label class="form-check-label" for="checkbox-signin">{{ __('auth.remember_me') }}</label>
        </div>

        <div class="d-grid">
          <button class="btn btn-primary" type="submit">{{ __('auth.sign_in') }}</button>
        </div>
      </form>

    </div>
  </div>
</div>

@endsection
