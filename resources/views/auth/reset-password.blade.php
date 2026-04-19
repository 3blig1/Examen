@extends('layouts.app')

@section('title', __('messages.reset_password_page_title'))

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-6 col-lg-5">
            <div class="card shadow-lg">
                <div class="card-header text-center py-4">
                    <h3 class="mb-0">
                        <i class="fas fa-lock"></i> {{ __('messages.reset_password_heading') }}
                    </h3>
                </div>
                <div class="card-body p-5">
                    @if(session('error'))
                        <div class="alert alert-danger">{{ session('error') }}</div>
                    @endif

                    <form method="POST" action="{{ route('password.update') }}">
                        @csrf
                        <input type="hidden" name="token" value="{{ $token }}">

                        <div class="mb-4">
                            <label for="email" class="form-label">{{ __('messages.email') }}</label>
                            <input type="email" class="form-control form-control-lg @error('email') is-invalid @enderror"
                                   id="email" name="email" value="{{ old('email', $email) }}" required autofocus>
                            @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <label for="password" class="form-label">{{ __('messages.password') }}</label>
                            <div class="input-group">
                                <input type="password" class="form-control form-control-lg @error('password') is-invalid @enderror"
                                       id="password" name="password" required minlength="8">
                                <button class="btn btn-outline-secondary" type="button" data-toggle-password data-target="password" aria-label="{{ __('messages.show_password') }}">
                                    <i class="fa-solid fa-eye"></i>
                                </button>
                                @error('password')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="mb-4">
                            <label for="password_confirmation" class="form-label">{{ __('messages.password_confirm') }}</label>
                            <div class="input-group">
                                <input type="password" class="form-control form-control-lg"
                                       id="password_confirmation" name="password_confirmation" required minlength="8">
                                <button class="btn btn-outline-secondary" type="button" data-toggle-password data-target="password_confirmation" aria-label="{{ __('messages.show_password') }}">
                                    <i class="fa-solid fa-eye"></i>
                                </button>
                            </div>
                        </div>

                        <button type="submit" class="btn btn-primary btn-lg w-100">
                            {{ __('messages.reset_password_button') }}
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
