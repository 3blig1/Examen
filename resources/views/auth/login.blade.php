@extends('layouts.app')

@section('title', __('messages.login_page_title'))

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-6 col-lg-5">
            <div class="card shadow-lg">
                <div class="card-header text-center py-4">
                    <h3 class="mb-0">
                        <i class="fas fa-sign-in-alt"></i> {{ __('messages.login') }}
                    </h3>
                </div>
                <div class="card-body p-5">
                    @if(session('success'))
                        <div class="alert alert-success">{{ session('success') }}</div>
                    @endif
                    @if(session('error'))
                        <div class="alert alert-danger">{{ session('error') }}</div>
                    @endif

                    <form method="POST" action="{{ route('login.store') }}">
                        @csrf

                        <div class="mb-4">
                            <label for="email" class="form-label">{{ __('messages.email') }}</label>
                            <input type="email" class="form-control form-control-lg @error('email') is-invalid @enderror" 
                                   id="email" name="email" value="{{ old('email') }}" required autofocus>
                            @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <label for="password" class="form-label">{{ __('messages.password') }}</label>
                            <div class="input-group">
                                <input type="password" class="form-control form-control-lg @error('password') is-invalid @enderror" 
                                       id="password" name="password" required>
                                <button class="btn btn-outline-secondary" type="button" data-toggle-password data-target="password" aria-label="{{ __('messages.show_password') }}">
                                    <i class="fa-solid fa-eye"></i>
                                </button>
                                @error('password')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="mb-4 form-check">
                            <input type="checkbox" class="form-check-input" id="remember" name="remember">
                            <label class="form-check-label" for="remember">
                                {{ __('messages.remember_me') }}
                            </label>
                        </div>

                        <div class="mb-4 text-end">
                            <a href="{{ route('password.request') }}" class="text-decoration-none">{{ __('messages.forgot_password') }}</a>
                        </div>

                        <button type="submit" class="btn btn-primary btn-lg w-100 mb-3">
                            <i class="fas fa-sign-in-alt"></i> {{ __('messages.login') }}
                        </button>

                        <p class="text-center text-muted mb-0">
                            {{ __('messages.no_account') }} 
                            <a href="{{ route('register') }}" class="text-primary fw-bold">{{ __('messages.register') }}</a>
                        </p>
                    </form>

                    @if(env('REQUIRE_EMAIL_VERIFICATION', true))
                        <hr class="my-4">
                        <form method="POST" action="{{ route('verification.resend') }}">
                            @csrf
                            <div class="mb-3">
                                <label for="resend_email" class="form-label">{{ __('messages.email') }}</label>
                                <input type="email" class="form-control" id="resend_email" name="email" value="{{ old('email') }}" required>
                                @error('email')
                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror
                            </div>
                            <button type="submit" class="btn btn-outline-primary w-100">
                                {{ __('messages.resend_verification_email') }}
                            </button>
                        </form>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
