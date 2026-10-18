@extends('layouts.app')

@section('content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="mb-0">{{ __('messages.inactive_users') }}</h1>
        <a href="{{ route('admin.users') }}" class="btn btn-outline-secondary">
            {{ __('messages.back_to_active_users') }}
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
            <a href="{{ route('admin.users.inactive') }}" class="btn btn-outline-secondary btn-sm">{{ __('messages.reset_filters') }}</a>
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
                        <span class="badge bg-secondary">{{ __('messages.status_inactive') }}</span>
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
                        @if(auth()->user()->canManageUsers() && !$user->isSuperAdmin())
                            <form method="POST" action="{{ route('admin.users.activate', $user) }}" class="d-inline-block">
                                @csrf
                                <button type="button" class="btn btn-sm btn-success confirm-action" data-confirm-message="{{ __('messages.confirm_activate') }}">
                                    {{ __('messages.activate') }}
                                </button>
                            </form>
                            @if(auth()->id() !== $user->id)
                                <form method="POST" action="{{ route('admin.users.delete', $user) }}" class="d-inline-block ms-2">
                                    @csrf
                                    @method('DELETE')
                                    <button type="button" class="btn btn-sm btn-danger confirm-action" data-confirm-message="{{ __('messages.confirm_delete_user') }}">
                                        {{ __('messages.delete') }}
                                    </button>
                                </form>
                            @endif
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
