@extends('layouts.app')

@section('title', __('messages.student_details_title') . ' - ÖSD Exams')

@section('content')
<div class="container py-5">
    <div class="row mb-4">
        <div class="col-12">
            <a href="{{ route('admin.students') }}" class="btn btn-secondary mb-3">
                <i class="fas fa-arrow-left"></i> {{ __('messages.back_button') }}
            </a>
            <h2>{{ $student->first_name }} {{ $student->last_name }}</h2>
        </div>
    </div>

    <!-- Student Info Card -->
    <div class="row mb-4">
        <div class="col-lg-6">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0"><i class="fas fa-user"></i> {{ __('messages.personal_info') }}</h5>
                </div>
                <div class="card-body">
                    <p class="mb-2">
                        <strong>{{ __('messages.email_header') }}:</strong> {{ $student->email }}<br>
                        <strong>{{ __('messages.phone_header') }}:</strong> {{ $student->phone }}<br>
                        <strong>{{ __('messages.country_header') }}:</strong> {{ $student->country }}<br>
                        <strong>{{ __('messages.dob_label') }}:</strong> {{ $student->date_of_birth ? $student->date_of_birth->format('d/m/Y') : __('messages.not_available_short') }}<br>
                        <strong>{{ __('messages.email_verified') }}:</strong> 
                        @if($student->isEmailVerified())
                            <span class="badge bg-success">{{ __('messages.yes') }}</span>
                        @else
                            <span class="badge bg-warning">{{ __('messages.no') }}</span>
                        @endif
                    </p>
                </div>
            </div>
        </div>

        <div class="col-lg-6">
            <div class="row">
                <div class="col-6">
                    <div class="stat-card">
                                <h3>{{ $registrations->count() }}</h3>
                                <p><i class="fas fa-clipboard-list"></i> {{ __('messages.registrations_label') }}</p>
                    </div>
                </div>
                <div class="col-6">
                    <div class="stat-card">
                        <h3>{{ $results->count() }}</h3>
                        <p><i class="fas fa-trophy"></i> {{ __('messages.results_label') }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Registrations -->
    @if($registrations->count() > 0)
    <div class="card mb-4">
        <div class="card-header">
            <h5 class="mb-0"><i class="fas fa-clipboard-list"></i> {{ __('messages.registrations_to_exams') }}</h5>
        </div>
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead>
                        <tr>
                            <th>{{ __('messages.exam') }}</th>
                            <th>{{ __('messages.date') }}</th>
                            <th>{{ __('messages.status') }}</th>
                            <th>{{ __('messages.payment') }}</th>
                            <th>{{ __('messages.registration_date') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($registrations as $registration)
                    <tr>
                        <td><strong>{{ $registration->exam->name }}</strong></td>
                        <td>{{ $registration->exam->date->format('d/m/Y H:i') }}</td>
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
                        <td>{{ $registration->created_at->format('d/m/Y') }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @endif

    <!-- Payment History -->
    @if($payments->count() > 0)
    <div class="card mb-4">
        <div class="card-header">
            <h5 class="mb-0"><i class="fas fa-receipt"></i> {{ __('messages.payment_history') }}</h5>
        </div>
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead>
                    <tr>
                        <th>{{ __('messages.exam') }}</th>
                        <th>{{ __('messages.amount') }}</th>
                        <th>{{ __('messages.payment') }}</th>
                        <th>{{ __('messages.date') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($payments as $payment)
                    <tr>
                        <td>{{ $payment->registration?->exam?->name ?? __('messages.not_available_short') }}</td>
                        <td>{{ number_format($payment->amount, 2) }}€</td>
                        <td>{{ $payment->status === 'confirmed' ? __('messages.confirmed') : __('messages.pending') }}</td>
                        <td>{{ $payment->paid_at?->format('d/m/Y H:i') ?? __('messages.not_available_short') }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @endif

    <!-- Results -->
    @if($results->count() > 0)
    <div class="card">
        <div class="card-header">
            <h5 class="mb-0"><i class="fas fa-trophy"></i> {{ __('messages.results_for_exam') }}</h5>
        </div>
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead>
                    <tr>
                        <th>{{ __('messages.exam') }}</th>
                        <th>{{ __('messages.level') }}</th>
                        <th>{{ __('messages.score') }}</th>
                        <th>{{ __('messages.status') }}</th>
                        <th>{{ __('messages.date_published') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($results as $result)
                    <tr>
                        <td><strong>{{ $result->exam->name }}</strong></td>
                        <td>{{ $result->level_achieved ?? __('messages.not_available_short') }}</td>
                        <td>{{ $result->total_score ?? '-' }}/100</td>
                        <td>
                            <span class="badge {{ $result->isPassed() ? 'bg-success' : 'bg-danger' }}">
                                {{ $result->isPassed() ? __('messages.passed') : __('messages.failed') }}
                            </span>
                        </td>
                        <td>{{ $result->published_at ? $result->published_at->format('d/m/Y') : __('messages.pending') }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @endif
</div>
@endsection
