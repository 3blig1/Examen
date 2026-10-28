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
                <a href="{{ route('admin.exams.registrations.export-pdf', array_filter(['exam' => $exam, 'filter' => $filter])) }}" class="btn btn-sm btn-outline-secondary me-2">
                    <i class="fas fa-file-pdf"></i> {{ __('messages.export_pdf') }}
                </a>
                <a href="{{ route('admin.exams.registrations.export-excel', array_filter(['exam' => $exam, 'filter' => $filter])) }}" class="btn btn-sm btn-outline-secondary">
                    <i class="fas fa-file-excel"></i> {{ __('messages.export_excel') }}
                </a>
            </div>
            <div class="mt-3" role="group" aria-label="Filtrer les inscriptions par paiement">
                     <a href="{{ route('admin.exams.registrations', $exam) }}"
                         class="btn {{ !$filter ? 'btn-primary' : 'btn-outline-primary' }} me-2">
                    <i class="fas fa-list"></i> {{ __('messages.view_all') }}
                </a>
                     <a href="{{ route('admin.exams.registrations', ['exam' => $exam, 'filter' => 'paid']) }}"
                         class="btn {{ $filter === 'paid' ? 'btn-success' : 'btn-outline-success' }} me-2">
                    <i class="fas fa-check-circle"></i> {{ __('messages.paid') }} ({{ $paymentCounts['paid'] }})
                </a>
                    <a href="{{ route('admin.exams.registrations', ['exam' => $exam, 'filter' => 'osd']) }}"
                       class="btn text-white me-2 {{ $filter === 'osd' ? 'active' : '' }}"
                       style="background-color:#6f42c1;border-color:#6f42c1;">
                        <i class="fas fa-graduation-cap"></i> ÖSD ({{ $paymentCounts['osd'] }})
                    </a>
                     <a href="{{ route('admin.exams.registrations', ['exam' => $exam, 'filter' => 'unpaid']) }}"
                         class="btn {{ $filter === 'unpaid' ? 'btn-warning' : 'btn-outline-warning' }} me-2">
                    <i class="fas fa-clock"></i> {{ __('messages.unpaid') }} ({{ $paymentCounts['unpaid'] }})
                </a>
                     <a href="{{ route('admin.exams.registrations', ['exam' => $exam, 'filter' => 'cancelled']) }}"
                         class="btn {{ $filter === 'cancelled' ? 'btn-danger' : 'btn-outline-danger' }}">
                    <i class="fas fa-ban"></i> {{ __('messages.status_cancelled') }} ({{ $paymentCounts['cancelled'] }})
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
                            <button type="button" class="btn btn-sm btn-info text-white mb-1" data-bs-toggle="modal" data-bs-target="#candidateDetails{{ $registration->id }}" title="{{ __('messages.view_details') }}">
                                <i class="fas fa-eye"></i>
                            </button>
                            @if(in_array($registration->status, ['pending', 'confirmed'], true) && $registration->payment_status !== 'paid')
                            <form action="{{ route('admin.registrations.confirm', $registration) }}" method="POST" style="display:inline;">
                                @csrf
                                <button type="button" class="btn btn-sm btn-success confirm-action mb-1" title="{{ __('messages.mark_paid') }}" data-confirm-message="{{ __('messages.confirm_payment') }}">
                                    <i class="fas fa-credit-card"></i> {{ __('messages.mark_paid') }}
                                </button>
                            </form>
                            <form action="{{ route('admin.registrations.reject', $registration) }}" method="POST" style="display:inline;">
                                @csrf
                                <button type="button" class="btn btn-sm btn-danger confirm-action" title="{{ __('messages.reject_registration') }}" data-confirm-message="{{ __('messages.are_you_sure') }}">
                                    <i class="fas fa-times"></i>
                                </button>
                            </form>
                            @endif
                            @if($registration->status === 'cancelled' && $registration->payment_status !== 'paid')
                            <form action="{{ route('admin.registrations.restore-confirmed-unpaid', $registration) }}" method="POST" style="display:inline;">
                                @csrf
                                <button type="button" class="btn btn-sm text-white confirm-action mb-1" style="background-color:#6f42c1;border-color:#6f42c1;" title="{{ __('messages.restore_confirmed_unpaid') }}" data-confirm-message="{{ __('messages.confirm_restore_confirmed_unpaid') }}">
                                    <i class="fas fa-user-check"></i> {{ __('messages.restore_confirmed_unpaid') }}
                                </button>
                            </form>
                            <form action="{{ route('admin.registrations.restore-paid', $registration) }}" method="POST" style="display:inline;">
                                @csrf
                                <button type="button" class="btn btn-sm btn-success confirm-action mb-1" title="{{ __('messages.restore_paid_registration') }}" data-confirm-message="{{ __('messages.confirm_restore_paid_registration') }}">
                                    <i class="fas fa-undo"></i> {{ __('messages.restore_paid_registration') }}
                                </button>
                            </form>
                            @endif
                            @if($registration->payment_status === 'paid' && $registration->status !== 'completed')
                            <form action="{{ route('admin.registrations.undo-payment-confirmation', $registration) }}" method="POST" style="display:inline;">
                                @csrf
                                <button type="button" class="btn btn-sm btn-warning confirm-action mb-1" title="{{ __('messages.undo_payment_confirmation') }}" data-confirm-message="{{ __('messages.confirm_undo_payment_confirmation') }}">
                                    <i class="fas fa-undo"></i>
                                </button>
                            </form>
                            @endif
                            <form action="{{ route('admin.registrations.delete', $registration) }}" method="POST" style="display:inline;">
                                @csrf
                                @method('DELETE')
                                <button type="button" class="btn btn-sm btn-danger confirm-action mb-1" title="{{ __('messages.delete') }}" data-confirm-message="{{ __('messages.confirm_delete_registration') }}">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </form>
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

    @foreach($registrations as $registration)
    <div class="modal fade" id="candidateDetails{{ $registration->id }}" tabindex="-1" aria-labelledby="candidateDetailsLabel{{ $registration->id }}" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="candidateDetailsLabel{{ $registration->id }}">
                        {{ $registration->user->first_name }} {{ $registration->user->last_name }}
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="{{ __('messages.cancel_btn') }}"></button>
                </div>
                <div class="modal-body">
                    <h6>{{ __('messages.user_details') }}</h6>
                    <dl class="row">
                        <dt class="col-sm-4">{{ __('messages.email') }}</dt>
                        <dd class="col-sm-8">{{ $registration->user->email }}</dd>
                        <dt class="col-sm-4">{{ __('messages.phone') }}</dt>
                        <dd class="col-sm-8">{{ $registration->user->phone ?: '—' }}</dd>
                        <dt class="col-sm-4">{{ __('messages.country') }}</dt>
                        <dd class="col-sm-8">{{ $registration->user->country ?: '—' }}</dd>
                        <dt class="col-sm-4">{{ __('messages.date_of_birth') }}</dt>
                        <dd class="col-sm-8">{{ $registration->user->date_of_birth?->format('d/m/Y') ?? '—' }}</dd>
                        <dt class="col-sm-4">{{ __('messages.birth_place') }}</dt>
                        <dd class="col-sm-8">{{ $registration->user->birth_place ?: '—' }}</dd>
                        <dt class="col-sm-4">{{ __('messages.birth_country') }}</dt>
                        <dd class="col-sm-8">{{ $registration->user->birth_country ?: '—' }}</dd>
                        <dt class="col-sm-4">{{ __('messages.nationality') }}</dt>
                        <dd class="col-sm-8">{{ $registration->user->nationality ?: '—' }}</dd>
                        <dt class="col-sm-4">{{ __('messages.address') }}</dt>
                        <dd class="col-sm-8">{{ $registration->user->address ?: '—' }}</dd>
                    </dl>

                    <h6 class="mt-4">{{ __('messages.selected_modules') }}</h6>
                    @php
                        $moduleLabels = [
                            'reading' => __('messages.module_reading'),
                            'listening' => __('messages.module_listening'),
                            'writing' => __('messages.module_writing'),
                            'speaking' => __('messages.module_speaking'),
                        ];
                        $selectedModules = $registration->selected_modules ?? [];
                    @endphp
                    @forelse($selectedModules as $module)
                        <span class="badge bg-primary me-1">{{ $moduleLabels[$module] ?? $module }}</span>
                    @empty
                        <span class="text-muted">—</span>
                    @endforelse
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('messages.cancel_btn') }}</button>
                </div>
            </div>
        </div>
    </div>
    @endforeach

    <!-- Pagination -->
    @if($registrations->hasPages())
    <div class="d-flex justify-content-center mt-5">
        {{ $registrations->links() }}
    </div>
    @endif
</div>
@endsection
