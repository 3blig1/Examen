@extends('layouts.app')

@section('content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="mb-0">{{ __('messages.user_management') }}</h1>
        <div class="d-flex gap-2">
            <a href="{{ route('admin.users.inactive') }}" class="btn btn-outline-secondary">
                {{ __('messages.inactive_users') }}
            </a>
            <a href="{{ route('admin.users.create') }}" class="btn btn-primary">
                {{ __('messages.create_admin_examiner') }}
            </a>
        </div>
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
        <div class="alert alert-success">{{ \App\Support\FlashMessage::render(session('success')) }}</div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger">{{ \App\Support\FlashMessage::render(session('error')) }}</div>
    @endif

    <div class="table-responsive">
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>#</th>
                    <th>{{ __('messages.name') }}</th>
                    <th>{{ __('messages.email_header') }}</th>
                    <th>{{ __('messages.role') }}</th>
                    <th>{{ __('messages.status') }}</th>
                    @if(auth()->user()->isSuperAdmin())
                        <th>{{ __('messages.user_manage_permission') }}</th>
                    @endif
                    <th>{{ __('messages.action') }}</th>
                </tr>
            </thead>
            <tbody>
                @foreach($users as $user)
                <tr>
                    <td>{{ $user->id }}</td>
                    <td>{{ $user->first_name }} {{ $user->last_name }}</td>
                    <td>{{ $user->email }}</td>
                    <td>
                        @if($user->role === 'student')
                            {{ __('messages.student_role') }}
                        @elseif($user->role === 'examiner')
                            {{ __('messages.examiner_role') }}
                        @else
                            {{ __('messages.admin_role') }}
                        @endif
                    </td>
                    <td>
                        <span class="badge {{ $user->is_active ? 'bg-success' : 'bg-secondary' }}">
                            {{ $user->is_active ? __('messages.status_active') : __('messages.status_inactive') }}
                        </span>
                    </td>
                    @if(auth()->user()->isSuperAdmin())
                        <td>
                            @if($user->role === 'admin' && !$user->isSuperAdmin())
                                <form method="POST" action="{{ route('admin.users.manage-permission', $user) }}" class="d-inline-block">
                                    @csrf
                                    <input type="hidden" name="admin_can_manage_users" value="{{ $user->admin_can_manage_users ? 0 : 1 }}">
                                    <button type="button" class="btn btn-sm {{ $user->admin_can_manage_users ? 'btn-outline-danger' : 'btn-outline-success' }} confirm-action" data-confirm-message="{{ $user->admin_can_manage_users ? __('messages.confirm_revoke_permission') : __('messages.confirm_grant_permission') }}">
                                        {{ $user->admin_can_manage_users ? __('messages.revoke_permission') : __('messages.grant_permission') }}
                                    </button>
                                </form>
                            @else
                                <span class="text-muted">—</span>
                            @endif
                        </td>
                    @endif
                    <td>
                        <form method="POST" action="{{ route('admin.users.update-role', $user) }}" class="d-inline-block">
                            @csrf
                            <div class="input-group">
                                <select name="role" class="form-select form-select-sm">
                                    <option value="student" {{ $user->role === 'student' ? 'selected' : '' }}>{{ __('messages.student_role') }}</option>
                                    <option value="examiner" {{ $user->role === 'examiner' ? 'selected' : '' }}>{{ __('messages.examiner_role') }}</option>
                                    <option value="admin" {{ $user->role === 'admin' ? 'selected' : '' }}>{{ __('messages.admin_role') }}</option>
                                </select>
                                <button class="btn btn-sm btn-primary" type="submit">{{ __('messages.update_button') }}</button>
                            </div>
                        </form>
                        @if(auth()->user()->canManageUsers() && !$user->isSuperAdmin() && auth()->id() !== $user->id)
                            <form method="POST" action="{{ route('admin.users.deactivate', $user) }}" class="d-inline-block ms-2">
                                @csrf
                                <button type="button" class="btn btn-sm btn-warning confirm-action" data-confirm-message="{{ __('messages.confirm_deactivate') }}">
                                    {{ __('messages.deactivate') }}
                                </button>
                            </form>
                            <form method="POST" action="{{ route('admin.users.delete', $user) }}" class="d-inline-block ms-2">
                                @csrf
                                @method('DELETE')
                                <button type="button" class="btn btn-sm btn-danger confirm-action" data-confirm-message="{{ __('messages.confirm_delete_user') }}">
                                    {{ __('messages.delete') }}
                                </button>
                            </form>
                        @endif
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
                <h5 class="modal-title" id="confirmAdminModalLabel">{{ __('messages.confirm_promotion') }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                {{ __('messages.confirm_admin_promotion') }}
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('messages.cancel_btn') }}</button>
                <button type="button" class="btn btn-danger" id="confirmAdminBtn">{{ __('messages.confirm') }}</button>
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
