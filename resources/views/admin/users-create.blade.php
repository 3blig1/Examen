@extends('layouts.app')

@section('content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="mb-0">Creer un utilisateur admin/examinateur</h1>
        <a href="{{ route('admin.users') }}" class="btn btn-outline-secondary">Retour</a>
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
                        <label for="title" class="form-label">Titre</label>
                        <select id="title" name="title" class="form-select" required>
                            <option value="">Selectionner</option>
                            <option value="mr" {{ old('title') === 'mr' ? 'selected' : '' }}>M.</option>
                            <option value="mme" {{ old('title') === 'mme' ? 'selected' : '' }}>Mme</option>
                            <option value="mlle" {{ old('title') === 'mlle' ? 'selected' : '' }}>Mlle</option>
                            <option value="dr" {{ old('title') === 'dr' ? 'selected' : '' }}>Dr</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label for="first_name" class="form-label">Prenom</label>
                        <input type="text" id="first_name" name="first_name" class="form-control" value="{{ old('first_name') }}" required>
                    </div>
                    <div class="col-md-3">
                        <label for="last_name" class="form-label">Nom</label>
                        <input type="text" id="last_name" name="last_name" class="form-control" value="{{ old('last_name') }}" required>
                    </div>
                    <div class="col-md-4">
                        <label for="email" class="form-label">Email</label>
                        <input type="email" id="email" name="email" class="form-control" value="{{ old('email') }}" required>
                    </div>
                    <div class="col-md-4">
                        <label for="password" class="form-label">Mot de passe</label>
                        <input type="password" id="password" name="password" class="form-control" required minlength="8">
                    </div>
                    <div class="col-md-4">
                        <label for="password_confirmation" class="form-label">Confirmation mot de passe</label>
                        <input type="password" id="password_confirmation" name="password_confirmation" class="form-control" required minlength="8">
                    </div>
                    <div class="col-md-4">
                        <label for="role" class="form-label">Role</label>
                        <select id="role" name="role" class="form-select" required>
                            <option value="examiner" {{ old('role') === 'examiner' ? 'selected' : '' }}>Examinateur</option>
                            <option value="admin" {{ old('role') === 'admin' ? 'selected' : '' }}>Admin</option>
                        </select>
                    </div>
                </div>
                <div class="mt-3">
                    <button type="submit" class="btn btn-primary">Creer l'utilisateur</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
