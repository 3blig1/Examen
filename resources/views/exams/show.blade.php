@extends('layouts.app')

@section('title', __('messages.exam_details_title') . ' - ÖSD Exams')

@section('content')
<div class="container py-5">
    <div class="row">
        <div class="col-lg-8">
            <div class="card mb-5">
                <div class="card-body p-5">
                    <h1 class="mb-4">{{ __('messages.exam_osd_level') }} <strong>{{ $exam->name }}</strong></h1>

                    <div class="row mb-5">
                        <div class="col-md-6">
                            <h6 class="text-muted">{{ __('messages.date_and_time') }}</h6>
                            <p class="fs-5">
                                <i class="fas fa-calendar-alt text-primary"></i>
                                {{ $exam->date->format('d/m/Y H:i') }}
                            </p>
                        </div>
                        <div class="col-md-6">
                            <h6 class="text-muted">{{ __('messages.location_label') }}</h6>
                            <p class="fs-5">
                                <i class="fas fa-map-marker-alt text-primary"></i>
                                {{ $exam->location ?? __('messages.location_to_be_defined') }}
                            </p>
                        </div>
                    </div>

                    @if($exam->registration_deadline)
                    <div class="row mb-5">
                        <div class="col-md-6">
                            <h6 class="text-muted">{{ __('messages.registration_deadline') }}</h6>
                            <p class="fs-5">
                                <i class="fas fa-hourglass-end text-primary"></i>
                                {{ $exam->registration_deadline->format('d/m/Y H:i') }}
                            </p>
                        </div>
                    </div>
                    @endif

                    <div class="row mb-5">
                        <div class="col-md-6">
                            <h6 class="text-muted">{{ __('messages.price') }}</h6>
                            <p class="fs-4 text-success">
                                <strong>{{ number_format($exam->price, 0, ',', ' ') }} FCFA</strong>
                            </p>
                            <span class="badge {{ $exam->isAvailable() ? 'bg-success' : 'bg-danger' }}">
                                {{ $exam->isAvailable() ? __('messages.registration_open') : __('messages.registration_closed') }}
                            </span>
                        </div>
                    </div>

                    <hr>

                    <h5 class="mb-3">{{ __('messages.description') }}</h5>
                    <p class="lead">{{ $exam->description ?: __('messages.no_description') }}</p>

                    <hr>

                    <h5 class="mb-3">{{ __('messages.about_exam') }}</h5>
                    <ul class="list-unstyled">
                        <li class="mb-2">
                            <i class="fas fa-check text-success"></i>
                            {{ __('messages.internationally_recognized') }}
                        </li>
                        <li class="mb-2">
                            <i class="fas fa-check text-success"></i>
                            {{ __('messages.four_skills') }}
                        </li>
                        <li class="mb-2">
                            <i class="fas fa-check text-success"></i>
                            {{ __('messages.certificate_issued') }}
                        </li>
                        <li class="mb-2">
                            <i class="fas fa-check text-success"></i>
                            {{ __('messages.results_published') }}
                        </li>
                    </ul>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <!-- Registration Card -->
            <div class="card sticky-top" style="top: 20px;">
                <div class="card-header text-center">
                    <h5 class="mb-0">{{ __('messages.registration') }}</h5>
                </div>
                <div class="card-body">
                    @auth
                        @if($isRegistered)
                            <div class="alert alert-info">
                                <i class="fas fa-check-circle"></i>
                                {{ __('messages.already_registered') }}
                            </div>
                            <a href="{{ route('dashboard') }}" class="btn btn-secondary btn-lg w-100">
                                <i class="fas fa-arrow-right"></i> {{ __('messages.go_dashboard') }}
                            </a>
                        @else
                            @if(!$exam->isAvailable())
                                <div class="alert alert-danger">
                                    <i class="fas fa-times-circle"></i>
                                    {{ __('messages.not_available') }}
                                </div>
                                <a href="{{ route('exams.index') }}" class="btn btn-secondary btn-lg w-100">
                                    <i class="fas fa-arrow-left"></i> {{ __('messages.view_other_exams') }}
                                </a>
                            @else
                                <form action="{{ route('exams.register', $exam) }}" method="POST" id="registrationForm">
                                    @csrf
                                    <div class="alert alert-info mb-4">
                                        <i class="fas fa-info-circle"></i>
                                        {{ __('messages.confirm_registration') }}
                                    </div>
                                    
                                    @if($exam->exam_type === 'module')
                                    <div class="mb-4">
                                        <label class="form-label"><strong>{{ __('messages.select_modules') }}</strong></label>
                                        <div class="card bg-light">
                                            <div class="card-body">
                                                @foreach(['reading', 'writing', 'listening', 'speaking'] as $module)
                                                    @if(in_array($module, (array)$exam->available_modules))
                                                    <div class="form-check mb-2">
                                                        <input class="form-check-input module-checkbox" type="checkbox" name="selected_modules[]" 
                                                               value="{{ $module }}" id="module-{{ $module }}" data-price="{{ $exam->price_per_module }}">
                                                        <label class="form-check-label" for="module-{{ $module }}">
                                                            @switch($module)
                                                                @case('reading')
                                                                    {{ __('messages.reading_score') }}
                                                                    @break
                                                                @case('writing')
                                                                    {{ __('messages.writing_score') }}
                                                                    @break
                                                                @case('listening')
                                                                    {{ __('messages.listening_score') }}
                                                                    @break
                                                                @case('speaking')
                                                                    {{ __('messages.speaking_score') }}
                                                                    @break
                                                            @endswitch
                                                            <span class="text-muted">({{ number_format($exam->price_per_module, 0, ',', ' ') }} FCFA)</span>
                                                        </label>
                                                    </div>
                                                    @endif
                                                @endforeach
                                            </div>
                                        </div>
                                        @error('selected_modules')
                                            <div class="invalid-feedback d-block">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    @else
                                    <!-- Complete exam - include all modules automatically -->
                                    <input type="hidden" name="selected_modules[]" value="reading">
                                    <input type="hidden" name="selected_modules[]" value="writing">
                                    <input type="hidden" name="selected_modules[]" value="listening">
                                    <input type="hidden" name="selected_modules[]" value="speaking">
                                    <div class="alert alert-light mb-4">
                                        <i class="fas fa-check-circle text-success"></i>
                                        {{ __('messages.all_four_modules_included') }}
                                    </div>
                                    @endif
                                    
                                    <div class="mb-4">
                                        <h6 class="text-muted">{{ __('messages.amount_to_pay') }}</h6>
                                        <p class="fs-4 text-success">
                                            <strong id="totalPrice">{{ number_format($exam->exam_type === 'complete' ? $exam->price : 0, 0, ',', ' ') }} FCFA</strong>
                                        </p>
                                    </div>

                                    <button type="submit" class="btn btn-primary btn-lg w-100 mb-2" id="registerBtn" {{ $exam->exam_type === 'module' ? 'disabled' : '' }}>
                                        <i class="fas fa-user-check"></i> {{ __('messages.register_button') }}
                                    </button>
                                </form>
                            @endif
                        @endif
                    @else
                        <p class="text-muted mb-4">{{ __('messages.please_login') }}</p>
                        <a href="{{ route('login') }}" class="btn btn-primary btn-lg w-100 mb-2">
                            <i class="fas fa-sign-in-alt"></i> {{ __('messages.sign_in') }}
                        </a>
                        <a href="{{ route('register') }}" class="btn btn-secondary btn-lg w-100">
                            <i class="fas fa-user-plus"></i> {{ __('messages.create_account') }}
                        </a>
                    @endauth
                </div>
            </div>
        </div>
    </div>
</div>

@if($exam->exam_type === 'module')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const checkboxes = document.querySelectorAll('.module-checkbox');
        const totalPriceElement = document.getElementById('totalPrice');
        const registerBtn = document.getElementById('registerBtn');
        const pricePerModule = {{ $exam->price_per_module ?? 0 }};
        const fullExamPrice = {{ $exam->price ?? 0 }};
        const totalModules = {{ count((array)$exam->available_modules) }};

        function updateTotalPrice() {
            let count = 0;
            checkboxes.forEach(checkbox => {
                if (checkbox.checked) {
                    count++;
                }
            });

            const total = (count === totalModules && totalModules === 4)
                ? fullExamPrice
                : (count * pricePerModule);
            totalPriceElement.textContent = Math.round(total).toLocaleString('fr-FR') + ' FCFA';

            // Enable/disable register button
            if (count > 0) {
                registerBtn.disabled = false;
            } else {
                registerBtn.disabled = true;
            }
        }

        checkboxes.forEach(checkbox => {
            checkbox.addEventListener('change', updateTotalPrice);
        });
    });
</script>
@endif
@endsection
