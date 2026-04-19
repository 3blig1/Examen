@extends('layouts.app')

@section('content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="mb-0">Gestion des utilisateurs</h1>
        <a href="{{ route('admin.users.create') }}" class="btn btn-primary">
            Creer un utilisateur admin/examinateur
        </a>
    </div>

    <form method="GET" class="mb-4">
        <div class="row g-3">
            <div class="col-md-4">
                <label class="form-label">{{ __('messages.name') }}</label>
                <input type="text" name="name" class="form-control" value="{{ request('name') }}">
            </div>
            <div class="col-md-4">
                <label class="form-label">{{ __('messages.email_header') }}</label>
                <input type="text" name="email" class="form-control" value="{{ request('email') }}">
            </div>
            <div class="col-md-3">
                <label class="form-label">{{ __('messages.role') }}</label>
                <select name="role" class="form-select">
                    <option value="">{{ __('messages.all') }}</option>
                    <option value="student" {{ request('role') === 'student' ? 'selected' : '' }}>{{ __('messages.student_role') }}</option>
                    <option value="examiner" {{ request('role') === 'examiner' ? 'selected' : '' }}>{{ __('messages.examiner_role') }}</option>
                    <option value="admin" {{ request('role') === 'admin' ? 'selected' : '' }}>{{ __('messages.admin_role') }}</option>
                </select>
            </div>
            <div class="col-md-1 d-flex align-items-end">
                <button class="btn btn-primary w-100" type="submit">{{ __('messages.filter') }}</button>
            </div>
        </div>
        <div class="mt-3">
            <a href="{{ route('admin.users') }}" class="btn btn-outline-secondary btn-sm">{{ __('messages.reset_filters') }}</a>
        </div>
    </form>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    <div class="table-responsive">
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Nom</th>
                    <th>Email</th>
                    <th>Rôle</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($users as $user)
                <tr>
                    <td>{{ $user->id }}</td>
                    <td>{{ $user->first_name }} {{ $user->last_name }}</td>
                    <td>{{ $user->email }}</td>
                    <td>{{ ucfirst($user->role) }}</td>
                    <td>
                        <form method="POST" action="{{ route('admin.users.update-role', $user) }}" class="d-inline-block">
                            @csrf
                            <div class="input-group">
                                <select name="role" class="form-select form-select-sm">
                                    <option value="student" {{ $user->role === 'student' ? 'selected' : '' }}>Student</option>
                                    <option value="examiner" {{ $user->role === 'examiner' ? 'selected' : '' }}>Examiner</option>
                                    <option value="admin" {{ $user->role === 'admin' ? 'selected' : '' }}>Admin</option>
                                </select>
                                <button class="btn btn-sm btn-primary" type="submit">Mettre à jour</button>
                            </div>
                        </form>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    {{ $users->links() }}
</div>
@endsection

<!-- Confirmation modal for promoting to admin -->
<div class="modal fade" id="confirmAdminModal" tabindex="-1" aria-labelledby="confirmAdminModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="confirmAdminModalLabel">Confirmer la promotion</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                Vous êtes sur le point de promouvoir cet utilisateur au rôle <strong>admin</strong>. Cette action donnera des privilèges d'administration complets. Voulez-vous continuer ?
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                <button type="button" class="btn btn-danger" id="confirmAdminBtn">Confirmer</button>
            </div>
        </div>
    </div>
</div>

@push('js')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        try {
            const modalEl = document.getElementById('confirmAdminModal');
            const confirmBtn = document.getElementById('confirmAdminBtn');
            if (!modalEl || !confirmBtn) {
                console.warn('Confirm admin modal elements not found');
                return;
            }

            const bs = window.bootstrap;
            if (!bs || !bs.Modal) {
                console.error('Bootstrap Modal not available on window.bootstrap');
                return;
            }

            const confirmModal = new bs.Modal(modalEl);
            let pendingForm = null;

            // Intercept button clicks to show modal when selecting admin
            document.querySelectorAll('.role-form').forEach(form => {
                const btn = form.querySelector('.role-submit');
                const select = form.querySelector('.role-select');
                if (!btn || !select) return;

                btn.addEventListener('click', function(e){
                    // If selecting admin, show confirmation modal
                    if (select.value === 'admin') {
                        e.preventDefault();
                        pendingForm = form;
                        confirmModal.show();
                    } else {
                        // allow normal submit when not promoting to admin
                        // ensure any default action is prevented then submit
                        e.preventDefault();
                        form.submit();
                    }
                });
            });

            confirmBtn.addEventListener('click', function(){
                if (pendingForm) {
                    pendingForm.submit();
                }
            });
        } catch (err) {
            console.error('Error initializing admin promotion modal:', err);
        }
    });
</script>
@endpush
