@extends('layouts.app')

@section('title', __('messages.registrations_for_exam') . ' - ÖSD Exams')

@section('content')
<div class="container py-5">
    <div class="row mb-4">
        <div class="col-12">
            <a href="{{ route('admin.exams') }}" class="btn btn-secondary mb-3">
                <i class="fas fa-arrow-left"></i> {{ __('messages.back_button') }}
            </a>
            <h2><i class="fas fa-clipboard-list"></i> {{ __('messages.registrations') }} - {{ $exam->name }} ({{ $exam->date->format('d/m/Y') }})</h2>
            <div class="mt-3">
                <a href="{{ route('admin.exams.registrations.export-pdf', $exam) }}" class="btn btn-sm btn-outline-secondary me-2">
                    <i class="fas fa-file-pdf"></i> {{ __('messages.export_pdf') }}
                </a>
                <a href="{{ route('admin.exams.registrations.export-excel', $exam) }}" class="btn btn-sm btn-outline-secondary">
                    <i class="fas fa-file-excel"></i> {{ __('messages.export_excel') }}
                </a>
            </div>
        </div>
    </div>

    <div class="row mb-4">
        <div class="col-md-4">
            <div class="stat-card">
                <h3>{{ $registrations->count() }}</h3>
                <p><i class="fas fa-users"></i> {{ __('messages.total_registrations') }}</p>
            </div>
        </div>
        <div class="col-md-4">
            <div class="stat-card">
                <h3>{{ $registrations->where('status', 'confirmed')->count() }}</h3>
                <p><i class="fas fa-check-circle"></i> {{ __('messages.confirmed') }}</p>
            </div>
        </div>
        <div class="col-md-4">
            <div class="stat-card">
                <h3>{{ $registrations->where('status', 'pending')->count() }}</h3>
                <p><i class="fas fa-hourglass-end"></i> {{ __('messages.pending') }}</p>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead>
                    <tr>
                        <th>{{ __('messages.student_header') }}</th>
                        <th>{{ __('messages.email_header') }}</th>
                        <th>{{ __('messages.country_header') }}</th>
                        <th>{{ __('messages.status') }}</th>
                        <th>{{ __('messages.payment') }}</th>
                        <th>{{ __('messages.registration_date') }}</th>
                        <th>{{ __('messages.action') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($registrations as $registration)
                    <tr>
                        <td>
                            <strong>{{ $registration->user->first_name }} {{ $registration->user->last_name }}</strong>
                        </td>
                        <td>{{ $registration->user->email }}</td>
                        <td>{{ $registration->user->country }}</td>
                        <td>
                            <span class="badge badge-status-{{ $registration->status }}">
                                {{ __('messages.status_' . $registration->status) }}
                            </span>
                        </td>
                        <td>
                            <span class="badge {{ $registration->payment_status === 'paid' ? 'bg-success' : 'bg-warning text-dark' }}">
                                {{ $registration->payment_status === 'paid' ? __('messages.paid') : __('messages.pending') }}
                            </span>
                        </td>
                        <td>{{ $registration->created_at->format('d/m/Y H:i') }}</td>
                        <td>
                            @if($registration->status === 'pending')
                            <form action="{{ route('admin.registrations.confirm', $registration) }}" method="POST" style="display:inline;">
                                @csrf
                                <button type="button" class="btn btn-sm btn-success confirm-action" title="{{ __('messages.confirm') }}" data-confirm-message="{{ __('messages.confirm_payment') }}">
                                    <i class="fas fa-check"></i>
                                </button>
                            </form>
                            <form action="{{ route('admin.registrations.reject', $registration) }}" method="POST" style="display:inline;">
                                @csrf
                                <button type="button" class="btn btn-sm btn-danger confirm-action" title="{{ __('messages.reject_registration') }}" data-confirm-message="{{ __('messages.are_you_sure') }}">
                                    <i class="fas fa-times"></i>
                                </button>
                            </form>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                            <td colspan="7" class="text-center text-muted py-4">
                            {{ __('messages.no_registrations') }}
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Pagination -->
    @if($registrations->hasPages())
    <div class="d-flex justify-content-center mt-5">
        {{ $registrations->links() }}
    </div>
    @endif
</div>
@endsection
