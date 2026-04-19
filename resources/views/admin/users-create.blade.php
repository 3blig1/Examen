@extends('layouts.app')

@section('content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="mb-0">{{ __('messages.create_admin_examiner') }}</h1>
        <a href="{{ route('admin.users') }}" class="btn btn-outline-secondary">{{ __('messages.back_button') }}</a>
    </div>

    @if($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="card">
        <div class="card-body">
            <form method="POST" action="{{ route('admin.users.store') }}">
                @csrf
                <div class="row g-3">
                    <div class="col-md-2">
                        <label for="title" class="form-label">{{ __('messages.title') }}</label>
                        <select id="title" name="title" class="form-select" required>
                            <option value="">{{ __('messages.select_option') }}</option>
                            <option value="mr" {{ old('title') === 'mr' ? 'selected' : '' }}>{{ __('messages.title_mr') }}</option>
                            <option value="mme" {{ old('title') === 'mme' ? 'selected' : '' }}>{{ __('messages.title_mme') }}</option>
                            <option value="mlle" {{ old('title') === 'mlle' ? 'selected' : '' }}>{{ __('messages.title_mlle') }}</option>
                            <option value="dr" {{ old('title') === 'dr' ? 'selected' : '' }}>{{ __('messages.title_dr') }}</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label for="first_name" class="form-label">{{ __('messages.first_name') }}</label>
                        <input type="text" id="first_name" name="first_name" class="form-control" value="{{ old('first_name') }}" required>
                    </div>
                    <div class="col-md-3">
                        <label for="last_name" class="form-label">{{ __('messages.last_name') }}</label>
                        <input type="text" id="last_name" name="last_name" class="form-control" value="{{ old('last_name') }}" required>
                    </div>
                    <div class="col-md-4">
                        <label for="email" class="form-label">{{ __('messages.email') }}</label>
                        <input type="email" id="email" name="email" class="form-control" value="{{ old('email') }}" required>
                    </div>
                    <div class="col-md-4">
                        <label for="password" class="form-label">{{ __('messages.password') }}</label>
                        <input type="password" id="password" name="password" class="form-control" required minlength="8">
                    </div>
                    <div class="col-md-4">
                        <label for="password_confirmation" class="form-label">{{ __('messages.password_confirm') }}</label>
                        <input type="password" id="password_confirmation" name="password_confirmation" class="form-control" required minlength="8">
                    </div>
                    <div class="col-md-4">
                        <label for="role" class="form-label">{{ __('messages.role') }}</label>
                        <select id="role" name="role" class="form-select" required>
                            <option value="examiner" {{ old('role') === 'examiner' ? 'selected' : '' }}>{{ __('messages.examiner_role') }}</option>
                            <option value="admin" {{ old('role') === 'admin' ? 'selected' : '' }}>{{ __('messages.admin_role') }}</option>
                        </select>
                    </div>
                </div>
                <div class="form-check form-switch mt-3">
                    <input class="form-check-input" type="checkbox" id="send_credentials" name="send_credentials" value="1" {{ old('send_credentials', '1') ? 'checked' : '' }}>
                    <label class="form-check-label" for="send_credentials">{{ __('messages.send_credentials_label') }}</label>
                    <div class="form-text">{{ __('messages.send_credentials_help') }}</div>
                </div>
                <div class="mt-3">
                    <button type="submit" class="btn btn-primary">{{ __('messages.create_user_button') }}</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
