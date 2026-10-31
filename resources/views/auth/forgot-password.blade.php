@extends('layouts.app')

@section('title', __('messages.forgot_password_page_title'))

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-6 col-lg-5">
            <div class="card shadow-lg">
                <div class="card-header text-center py-4">
                    <h3 class="mb-0">
                        <i class="fas fa-key"></i> {{ __('messages.forgot_password') }}
                    </h3>
                </div>
                <div class="card-body p-5">
                    @if(session('success'))
                        <div class="alert alert-success">{{ \App\Support\FlashMessage::render(session('success')) }}</div>
                    @endif
                    @if(session('error'))
                        <div class="alert alert-danger">{{ \App\Support\FlashMessage::render(session('error')) }}</div>
                    @endif

                    <form method="POST" action="{{ route('password.email') }}">
                        @csrf
                        <div class="mb-4">
                            <label for="email" class="form-label">{{ __('messages.email') }}</label>
                            <input type="email" class="form-control form-control-lg @error('email') is-invalid @enderror"
                                   id="email" name="email" value="{{ old('email') }}" required autofocus>
                            @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <button type="submit" class="btn btn-primary btn-lg w-100 mb-3">
                            {{ __('messages.send_reset_link') }}
                        </button>

                        <p class="text-center text-muted mb-0">
                            <a href="{{ route('login') }}" class="text-primary fw-bold">{{ __('messages.back_to_login') }}</a>
                        </p>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
