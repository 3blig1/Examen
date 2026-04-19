@extends('layouts.app')

@section('title', __('messages.home_page_title'))

@section('content')
<!-- Hero Section -->
<div class="hero">
    <div class="container">
        <h1 style="font-size: 3.5rem; margin-bottom: 30px;">
            <i class="fas fa-graduation-cap"></i> {{ __('messages.welcome_title') }}
        </h1>
        <p style="font-size: 1.3rem; margin-bottom: 30px; opacity: 0.95;">
            {{ __('messages.welcome_subtitle') }}
        </p>
        <div> 
            @auth
                @if(auth()->user()->isAdmin())
                    <a href="{{ route('admin.dashboard') }}" class="btn btn-secondary btn-lg me-3">
                        <i class="fas fa-tachometer-alt"></i> {{ __('messages.dashboard') }}
                    </a>
                @elseif(auth()->user()->isExaminer())
                    <a href="{{ route('examiner.dashboard') }}" class="btn btn-secondary btn-lg me-3">
                        <i class="fas fa-tachometer-alt"></i> {{ __('messages.dashboard') }}
                    </a>
                @else
                    <a href="{{ route('dashboard') }}" class="btn btn-secondary btn-lg me-3">
                        <i class="fas fa-tachometer-alt"></i> {{ __('messages.dashboard') }}
                    </a>
                @endif
            @else
                <a href="{{ route('exams.index') }}" class="btn btn-secondary btn-lg me-3">
                    <i class="fas fa-book"></i> {{ __('messages.cta_explore') }}
                </a>
                <a href="{{ route('register') }}" class="btn btn-light btn-lg">
                    <i class="fas fa-user-plus"></i> {{ __('messages.cta_register') }}
                </a>
            @endauth
        </div>
    </div>
</div>

<!-- Features Section -->
 
 @if(auth()->check() && auth()->user()->isAdmin())
<div class="container py-5">
    <div class="row mb-5 text-center">
        <div class="col-12">
            <h2 class="mb-4"><i class="fas fa-star"></i> {{ __('messages.features') }}</h2>
        </div>
    </div>
@endif

    <div class="row">
        <div class="col-md-6 col-lg-3 mb-4">
            <div class="card h-100 text-center">
                <div class="card-body p-5">
                    <i class="fas fa-users fa-3x text-primary mb-3"></i>
                    <h5 class="card-title">{{ __('messages.feature_registration') }}</h5>
                    <p class="card-text">{{ __('messages.feature_easy_registration') }}</p>
                </div>
            </div>
        </div>

        <div class="col-md-6 col-lg-3 mb-4">
            <div class="card h-100 text-center">
                <div class="card-body p-5">
                    <i class="fas fa-envelope fa-3x text-primary mb-3"></i>
                    <h5 class="card-title">{{ __('messages.feature_exam') }}</h5>
                    <p class="card-text">{{ __('messages.feature_email_verify') }}</p>
                </div>
            </div>
        </div>

        <div class="col-md-6 col-lg-3 mb-4">
            <div class="card h-100 text-center">
                <div class="card-body p-5">
                    <i class="fas fa-credit-card fa-3x text-primary mb-3"></i>
                    <h5 class="card-title">{{ __('messages.feature_results') }}</h5>
                    <p class="card-text">{{ __('messages.feature_secure_payment') }}</p>
                </div>
            </div>
        </div>

        <div class="col-md-6 col-lg-3 mb-4">
            <div class="card h-100 text-center">
                <div class="card-body p-5">
                    <i class="fas fa-chart-bar fa-3x text-primary mb-3"></i>
                    <h5 class="card-title">{{ __('messages.feature_admin') }}</h5>
                    <p class="card-text">{{ __('messages.feature_dashboard') }}</p>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Exams Section -->
<div class="bg-light py-5">
    <div class="container">
        <div class="row mb-5">
            <div class="col-12">
                <h2 class="text-center mb-4"><i class="fas fa-book"></i> {{ __('messages.exam_levels') }}</h2>
                <p class="text-center text-muted lead">{{ __('messages.welcome_subtitle') }}</p>
            </div>
        </div>

        <div class="row">
            <div class="col-md-4 mb-4">
                <div class="card h-100">
                    <div class="card-body text-center">
                        <h5 class="card-title">{{ __('messages.level_a1') }}</h5>
                        <p class="text-muted mb-3">A1 • A2</p>
                        <p class="card-text">{{ __('messages.beginner_skills') }}</p>
                    </div>
                </div>
            </div>

            <div class="col-md-4 mb-4">
                <div class="card h-100">
                    <div class="card-body text-center">
                        <h5 class="card-title">{{ __('messages.level_b1') }}</h5>
                        <p class="text-muted mb-3">B1 • B2</p>
                        <p class="card-text">{{ __('messages.intermediate_skills') }}</p>
                    </div>
                </div>
            </div>

            <div class="col-md-4 mb-4">
                <div class="card h-100">
                    <div class="card-body text-center">
                        <h5 class="card-title">{{ __('messages.level_c1') }}</h5>
                        <p class="text-muted mb-3">C1 • C2</p>
                        <p class="card-text">{{ __('messages.advanced_skills') }}</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="text-center mt-4">
            <a href="{{ route('exams.index') }}" class="btn btn-primary btn-lg">
                <i class="fas fa-eye"></i> {{ __('messages.all_exams') }}
            </a>
        </div>
    </div>
</div>

<!-- CTA Section -->
<div class="container py-5">
    <div class="row">
        <div class="col-lg-8 mx-auto text-center">
            <h2 class="mb-4">{{ __('messages.start_now') }}</h2>
            <p class="lead text-muted mb-4">
                {{ __('messages.welcome_subtitle') }}
            </p>
            @guest
                <a href="{{ route('register') }}" class="btn btn-primary btn-lg me-3">
                    <i class="fas fa-user-plus"></i> {{ __('messages.cta_register') }}
                </a>
                <a href="{{ route('login') }}" class="btn btn-secondary btn-lg">
                    <i class="fas fa-sign-in-alt"></i> {{ __('messages.login') }}
                </a>
            @endguest
        </div>
    </div>
</div>
@endsection
