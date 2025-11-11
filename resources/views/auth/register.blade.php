@extends('layouts.auth', ['title' => __('auth.create_account')])

@section('content')
    <div class="d-flex flex-column h-100 p-3 position-relative">
        
        <!-- Language Switcher -->
        <div class="position-absolute top-0 end-0 m-3" style="z-index: 10;">
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

        <div class="d-flex flex-column flex-grow-1">
            <div class="row h-100">
                <div class="col-xxl-7">
                    <div class="row justify-content-center h-100">
                        <div class="col-lg-6 py-lg-5">
                            <div class="d-flex flex-column h-100 justify-content-center">
                                <div class="auth-logo mb-4">
                                    <a href="{{ route('second', [ 'dashboards' , 'index']) }}" class="logo-dark">
                                        <img src="/images/logo-dark.png" height="24" alt="logo dark">
                                    </a>

                                    <a href="{{ route('second', [ 'dashboards' , 'index']) }}" class="logo-light">
                                        <img src="/images/logo-light.png" height="24" alt="logo light">
                                    </a>
                                </div>

                                <h2 class="fw-bold fs-24">{{ __('auth.create_account') }}</h2>

                                <p class="text-muted mt-1 mb-4">{{ __('auth.register_description') }}</p>

                                <div>
                                    <form class="authentication-form">
                                        <div class="mb-3">
                                            <label class="form-label" for="example-name">{{ __('auth.full_name') }}</label>
                                            <input type="text" id="example-name" name="example-name"
                                                   class="form-control" placeholder="{{ __('auth.enter_name') }}">
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label" for="example-email">{{ __('auth.email') }}</label>
                                            <input type="email" id="example-email" name="example-email"
                                                   class="form-control bg-" placeholder="{{ __('auth.enter_email') }}">
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label" for="example-password">{{ __('auth.password') }}</label>
                                            <input type="text" id="example-password" class="form-control"
                                                   placeholder="{{ __('auth.enter_password') }}">
                                        </div>
                                        <div class="mb-3">
                                            <div class="form-check">
                                                <input type="checkbox" class="form-check-input" id="checkbox-signin">
                                                <label class="form-check-label" for="checkbox-signin">I accept Terms and
                                                    Condition</label>
                                            </div>
                                        </div>

                                        <div class="mb-1 text-center d-grid">
                                            <button class="btn btn-soft-primary" type="submit">{{ __('auth.sign_up') }}</button>
                                        </div>
                                    </form>

                                    <p class="mt-3 fw-semibold no-span">OR sign with</p>

                                    <div class="d-grid gap-2">
                                        <a href="javascript:void(0);" class="btn btn-soft-dark"><i
                                                class="bx bxl-google fs-20 me-1"></i> Sign Up with Google</a>
                                        <a href="javascript:void(0);" class="btn btn-soft-primary"><i
                                                class="bx bxl-facebook fs-20 me-1"></i> Sign Up with Facebook</a>
                                    </div>
                                </div>

                                <p class="mt-auto text-danger text-center">{{ __('auth.already_have_account') }} <a
                                        href="{{ route('second', [ 'auth' , 'login']) }}" class="text-dark fw-bold ms-1">{{ __('auth.sign_in_here') }}</a></p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-xxl-5 d-none d-xxl-flex">
                    <div class="card h-100 mb-0 overflow-hidden">
                        <div class="d-flex flex-column h-100">
                            <img src="/images/small/img-10.jpg" alt="" class="w-100 h-100">
                        </div>
                    </div> <!-- end card -->
                </div>
            </div>
        </div>
    </div>

@endsection
