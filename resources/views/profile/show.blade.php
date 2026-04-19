@extends('layouts.app')

@section('title', __('messages.profile') . ' - ÖSD Exams')

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
    <div class="row mb-4">
        <div class="col-12">
            <h2><i class="fas fa-user"></i> {{ __('messages.profile') }}</h2>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-7 mb-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0"><i class="fas fa-id-card"></i> {{ __('messages.profile_details') }}</h5>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('profile.update') }}">
                        @csrf
                        @method('PUT')

                        <div class="mb-3">
                            <label for="title" class="form-label">{{ __('messages.title') }}</label>
                            <select class="form-select @error('title') is-invalid @enderror" id="title" name="title" required>
                                <option value="">{{ __('messages.select_option') }}</option>
                                <option value="mr" {{ old('title', $user->title) === 'mr' ? 'selected' : '' }}>{{ __('messages.title_mr') }}</option>
                                <option value="mme" {{ old('title', $user->title) === 'mme' ? 'selected' : '' }}>{{ __('messages.title_mme') }}</option>
                                <option value="mlle" {{ old('title', $user->title) === 'mlle' ? 'selected' : '' }}>{{ __('messages.title_mlle') }}</option>
                                <option value="dr" {{ old('title', $user->title) === 'dr' ? 'selected' : '' }}>{{ __('messages.title_dr') }}</option>
                            </select>
                            @error('title')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="last_name" class="form-label">{{ __('messages.last_name') }}</label>
                                <input type="text" class="form-control @error('last_name') is-invalid @enderror" id="last_name" name="last_name" value="{{ old('last_name', $user->last_name) }}" required>
                                @error('last_name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="first_name" class="form-label">{{ __('messages.first_name') }}</label>
                                <input type="text" class="form-control @error('first_name') is-invalid @enderror" id="first_name" name="first_name" value="{{ old('first_name', $user->first_name) }}" required>
                                @error('first_name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="sex" class="form-label">{{ __('messages.sex') }}</label>
                                <select class="form-select @error('sex') is-invalid @enderror" id="sex" name="sex" required>
                                    <option value="">{{ __('messages.select_option') }}</option>
                                    <option value="male" {{ old('sex', $user->sex) === 'male' ? 'selected' : '' }}>{{ __('messages.sex_male') }}</option>
                                    <option value="female" {{ old('sex', $user->sex) === 'female' ? 'selected' : '' }}>{{ __('messages.sex_female') }}</option>
                                    <option value="other" {{ old('sex', $user->sex) === 'other' ? 'selected' : '' }}>{{ __('messages.sex_other') }}</option>
                                </select>
                                @error('sex')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="date_of_birth" class="form-label">{{ __('messages.date_of_birth') }}</label>
                                <input type="date" class="form-control @error('date_of_birth') is-invalid @enderror" id="date_of_birth" name="date_of_birth" value="{{ old('date_of_birth', $user->date_of_birth?->format('Y-m-d')) }}" required>
                                @error('date_of_birth')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="birth_place" class="form-label">{{ __('messages.birth_place') }}</label>
                                <input type="text" class="form-control @error('birth_place') is-invalid @enderror" id="birth_place" name="birth_place" value="{{ old('birth_place', $user->birth_place) }}" required>
                                @error('birth_place')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="birth_country" class="form-label">{{ __('messages.birth_country') }}</label>
                                <input type="text" class="form-control @error('birth_country') is-invalid @enderror" id="birth_country" name="birth_country" value="{{ old('birth_country', $user->birth_country) }}" required>
                                @error('birth_country')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="nationality" class="form-label">{{ __('messages.nationality') }}</label>
                            <input type="text" class="form-control @error('nationality') is-invalid @enderror" id="nationality" name="nationality" value="{{ old('nationality', $user->nationality) }}" required>
                            @error('nationality')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="email" class="form-label">{{ __('messages.email') }}</label>
                            <input type="email" class="form-control @error('email') is-invalid @enderror" id="email" name="email" value="{{ old('email', $user->email) }}" required>
                            @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="phone_display" class="form-label">{{ __('messages.phone') }}</label>
                                <input type="tel" class="form-control @error('phone') is-invalid @enderror" id="phone_display" value="{{ old('phone', $user->phone) }}" required>
                                <input type="hidden" name="phone" id="phone">
                                @error('phone')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="country" class="form-label">{{ __('messages.country') }}</label>
                                <input type="text" class="form-control @error('country') is-invalid @enderror" id="country" name="country" value="{{ old('country', $user->country) }}" required>
                                @error('country')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="address" class="form-label">{{ __('messages.address') }}</label>
                            <input type="text" class="form-control @error('address') is-invalid @enderror" id="address" name="address" value="{{ old('address', $user->address) }}" required>
                            @error('address')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> {{ __('messages.update_profile') }}
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-lg-5 mb-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0"><i class="fas fa-lock"></i> {{ __('messages.change_password') }}</h5>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('profile.password') }}">
                        @csrf
                        @method('PUT')

                        <div class="mb-3">
                            <label for="current_password" class="form-label">{{ __('messages.current_password') }}</label>
                            <div class="input-group">
                                <input type="password" class="form-control @error('current_password') is-invalid @enderror" id="current_password" name="current_password" required>
                                <button class="btn btn-outline-secondary" type="button" data-toggle-password data-target="current_password" aria-label="{{ __('messages.show_password') }}">
                                    <i class="fa-solid fa-eye"></i>
                                </button>
                                @error('current_password')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="password" class="form-label">{{ __('messages.new_password') }}</label>
                            <div class="input-group">
                                <input type="password" class="form-control @error('password') is-invalid @enderror" id="password" name="password" required>
                                <button class="btn btn-outline-secondary" type="button" data-toggle-password data-target="password" aria-label="{{ __('messages.show_password') }}">
                                    <i class="fa-solid fa-eye"></i>
                                </button>
                                @error('password')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="password_confirmation" class="form-label">{{ __('messages.confirm_new_password') }}</label>
                            <div class="input-group">
                                <input type="password" class="form-control" id="password_confirmation" name="password_confirmation" required>
                                <button class="btn btn-outline-secondary" type="button" data-toggle-password data-target="password_confirmation" aria-label="{{ __('messages.show_password') }}">
                                    <i class="fa-solid fa-eye"></i>
                                </button>
                            </div>
                        </div>

                        <button type="submit" class="btn btn-secondary">
                            <i class="fas fa-key"></i> {{ __('messages.update_password') }}
                        </button>
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
