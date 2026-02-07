@extends('layouts.app')

@section('title', __('messages.students_management') . ' - ÖSD Exams')

@section('content')
<div class="container py-5">
    <div class="row mb-4">
        <div class="col-12">
            <h2><i class="fas fa-users"></i> {{ __('messages.students_management') }}</h2>
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
                <label class="form-label">{{ __('messages.country_header') }}</label>
                <input type="text" name="country" class="form-control" value="{{ request('country') }}">
            </div>
            <div class="col-md-1 d-flex align-items-end">
                <button class="btn btn-primary w-100" type="submit">{{ __('messages.filter') }}</button>
            </div>
        </div>
        <div class="mt-3">
            <a href="{{ route('admin.students') }}" class="btn btn-outline-secondary btn-sm">{{ __('messages.reset_filters') }}</a>
        </div>
    </form>

    <div class="card">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead>
                    <tr>
                        <th>{{ __('messages.name') }}</th>
                        <th>{{ __('messages.email_header') }}</th>
                        <th>{{ __('messages.phone_header') }}</th>
                        <th>{{ __('messages.country_header') }}</th>
                        <th>{{ __('messages.registrations_header') }}</th>
                        <th>{{ __('messages.status_header') }}</th>
                        <th>{{ __('messages.action') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($students as $student)
                    <tr>
                        <td>
                            <strong>{{ $student->first_name }} {{ $student->last_name }}</strong>
                        </td>
                        <td>{{ $student->email }}</td>
                        <td>{{ $student->phone }}</td>
                        <td>{{ $student->country }}</td>
                        <td>
                            <span class="badge bg-info">
                                {{ $student->examRegistrations()->count() }}
                            </span>
                        </td>
                        <td>
                            @if($student->is_active)
                                <span class="badge bg-success">{{ __('messages.active') }}</span>
                            @else
                                <span class="badge bg-danger">{{ __('messages.disabled') }}</span>
                            @endif
                        </td>
                        <td>
                            <a href="{{ route('admin.student-details', $student) }}" class="btn btn-sm btn-info">
                                <i class="fas fa-eye"></i> {{ __('messages.see_details') }}
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center text-muted py-4">
                            {{ __('messages.no_students') }}
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Pagination -->
    @if($students->hasPages())
    <div class="d-flex justify-content-center mt-5">
        {{ $students->links() }}
    </div>
    @endif
</div>
@endsection
