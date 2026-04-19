@extends('layouts.app')

@section('title', 'Inscription - ÖSD Exams')

@push('styles')
    <link href="https://cdn.jsdelivr.net/npm/intl-tel-input@23.8.0/build/css/intlTelInput.css" rel="stylesheet">
    <style>
        .iti {
            width: 100%;
        }
        .iti input {
            width: 100%;
            padding-left: 52px;
        }
        .iti__country-list {
            z-index: 2000;
        }
    </style>
@endpush

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-8 col-lg-6">
            <div class="card shadow-lg">
                <div class="card-header text-center py-4">
                    <h3 class="mb-0">
                        <i class="fas fa-user-plus"></i> {{ __('messages.register') }}
                    </h3>
                </div>
                <div class="card-body p-5">
                    <form method="POST" action="{{ route('register.store') }}">
                        @csrf

                        <div class="mb-3">
                            <label for="title" class="form-label">Titre</label>
                            <select class="form-select @error('title') is-invalid @enderror" id="title" name="title" required>
                                <option value="">Selectionner</option>
                                <option value="mr" {{ old('title') === 'mr' ? 'selected' : '' }}>M.</option>
                                <option value="mme" {{ old('title') === 'mme' ? 'selected' : '' }}>Mme</option>
                                <option value="mlle" {{ old('title') === 'mlle' ? 'selected' : '' }}>Mlle</option>
                                <option value="dr" {{ old('title') === 'dr' ? 'selected' : '' }}>Dr</option>
                            </select>
                            @error('title')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="last_name" class="form-label">{{ __('messages.last_name') }}</label>
                                <input type="text" class="form-control @error('last_name') is-invalid @enderror" 
                                       id="last_name" name="last_name" value="{{ old('last_name') }}" required>
                                @error('last_name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="first_name" class="form-label">{{ __('messages.first_name') }}</label>
                                <input type="text" class="form-control @error('first_name') is-invalid @enderror" 
                                       id="first_name" name="first_name" value="{{ old('first_name') }}" required>
                                @error('first_name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="sex" class="form-label">Sexe</label>
                                <select class="form-select @error('sex') is-invalid @enderror" id="sex" name="sex" required>
                                    <option value="">Selectionner</option>
                                    <option value="male" {{ old('sex') === 'male' ? 'selected' : '' }}>Homme</option>
                                    <option value="female" {{ old('sex') === 'female' ? 'selected' : '' }}>Femme</option>
                                    <option value="other" {{ old('sex') === 'other' ? 'selected' : '' }}>Autre</option>
                                </select>
                                @error('sex')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="date_of_birth" class="form-label">{{ __('messages.date_of_birth') }}</label>
                                <input type="date" class="form-control @error('date_of_birth') is-invalid @enderror" 
                                       id="date_of_birth" name="date_of_birth" value="{{ old('date_of_birth') }}" required>
                                @error('date_of_birth')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="birth_place" class="form-label">Lieu de naissance</label>
                                <input type="text" class="form-control @error('birth_place') is-invalid @enderror"
                                       id="birth_place" name="birth_place" value="{{ old('birth_place') }}" required>
                                @error('birth_place')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="birth_country" class="form-label">Pays de naissance</label>
                                <input type="text" class="form-control @error('birth_country') is-invalid @enderror"
                                       id="birth_country" name="birth_country" value="{{ old('birth_country') }}" required>
                                @error('birth_country')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="nationality" class="form-label">Nationalite</label>
                            <input type="text" class="form-control @error('nationality') is-invalid @enderror"
                                   id="nationality" name="nationality" value="{{ old('nationality') }}" required>
                            @error('nationality')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="email" class="form-label">{{ __('messages.email') }}</label>
                            <input type="email" class="form-control @error('email') is-invalid @enderror" 
                                   id="email" name="email" value="{{ old('email') }}" required>
                            @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="phone_display" class="form-label">{{ __('messages.phone') }}</label>
                                <input type="tel" class="form-control @error('phone') is-invalid @enderror" 
                                       id="phone_display" value="{{ old('phone') }}" required>
                                <input type="hidden" name="phone" id="phone">
                                @error('phone')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="country" class="form-label">{{ __('messages.country') }}</label>
                                <input type="text" class="form-control @error('country') is-invalid @enderror" 
                                       id="country" name="country" value="{{ old('country') }}" required>
                                @error('country')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="address" class="form-label">Adresse du domicile ou du lieu de sejour</label>
                            <input type="text" class="form-control @error('address') is-invalid @enderror"
                                   id="address" name="address" value="{{ old('address') }}" required>
                            @error('address')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="password" class="form-label">{{ __('messages.password') }}</label>
                            <div class="input-group">
                                <input type="password" class="form-control @error('password') is-invalid @enderror" 
                                       id="password" name="password" required minlength="8">
                                <button class="btn btn-outline-secondary" type="button" data-toggle-password data-target="password" aria-label="Afficher le mot de passe">
                                    <i class="fa-solid fa-eye"></i>
                                </button>
                                @error('password')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <small class="form-text text-muted">Au minimum 8 caractères</small>
                        </div>

                        <div class="mb-4">
                            <label for="password_confirmation" class="form-label">{{ __('messages.password_confirm') }}</label>
                            <div class="input-group">
                                <input type="password" class="form-control" id="password_confirmation" 
                                       name="password_confirmation" required minlength="8">
                                <button class="btn btn-outline-secondary" type="button" data-toggle-password data-target="password_confirmation" aria-label="Afficher le mot de passe">
                                    <i class="fa-solid fa-eye"></i>
                                </button>
                            </div>
                        </div>

                        <button type="submit" class="btn btn-primary btn-lg w-100 mb-3">
                            <i class="fas fa-user-plus"></i> {{ __('messages.register') }}
                        </button>

                        <p class="text-center text-muted">
                            {{ __('messages.have_account') }} 
                            <a href="{{ route('login') }}" class="text-primary fw-bold">{{ __('messages.login') }}</a>
                        </p>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('js')
<script src="https://cdn.jsdelivr.net/npm/intl-tel-input@23.8.0/build/js/intlTelInput.min.js"></script>
<script>
    (function(){
        var input = document.getElementById('phone_display');
        var hiddenInput = document.getElementById('phone');
        if (!input || !hiddenInput) return;

        var iti = window.intlTelInput(input, {
            initialCountry: 'auto',
            utilsScript: 'https://cdn.jsdelivr.net/npm/intl-tel-input@23.8.0/build/js/utils.js',
            geoIpLookup: function(callback) {
                fetch('https://ipapi.co/json/')
                    .then(function(resp){ return resp.json(); })
                    .then(function(data){ callback(data && data.country_code ? data.country_code : 'fr'); })
                    .catch(function(){ callback('fr'); });
            }
        });

        var form = input.closest('form');
        if (form) {
            form.addEventListener('submit', function(){
                hiddenInput.value = iti.getNumber();
            });
        }
    })();
</script>
@endpush
