@extends('layouts.app')

@section('title', 'Tableau de bord - ÖSD Exams')

@section('content')
<div class="container py-5">
    <div class="row mb-5">
        <div class="col-md-3 mb-3">
            <div class="stat-card">
                <h3>{{ $statistics['totalRegistrations'] }}</h3>
                <p><i class="fas fa-clipboard-list"></i> {{ __('messages.total_registrations') }}</p>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="stat-card">
                <h3>{{ $statistics['confirmedRegistrations'] }}</h3>
                <p><i class="fas fa-check-circle"></i> {{ __('messages.confirmed') }}</p>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="stat-card">
                <h3>{{ $statistics['totalExamsTaken'] }}</h3>
                <p><i class="fas fa-book"></i> {{ __('messages.exams_taken') }}</p>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="stat-card">
                <h3>{{ $statistics['passedExams'] }}</h3>
                <p><i class="fas fa-trophy"></i> {{ __('messages.passed_exams') }}</p>
            </div>
        </div>
    </div>

    <!-- Upcoming Exams -->
    @if($upcomingExams->count() > 0)
    <div class="card mb-5">
        <div class="card-header">
            <h5 class="mb-0"><i class="fas fa-calendar-alt"></i> {{ __('messages.upcoming_exams') }}</h5>
        </div>
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead>
                    <tr>
                        <th>{{ __('messages.level') }}</th>
                        <th>{{ __('messages.date') }}</th>
                        <th>{{ __('messages.location') }}</th>
                        <th>{{ __('messages.available_spots') }}</th>
                        <th>{{ __('messages.action') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($upcomingExams as $exam)
                    <tr>
                        <td><strong>{{ $exam->name }}</strong></td>
                        <td>{{ $exam->date->format('d/m/Y H:i') }}</td>
                        <td>{{ $exam->location ?? 'N/A' }}</td>
                        <td>
                            <span class="badge bg-info">{{ $exam->available_spots }} / {{ $exam->max_participants }}</span>
                        </td>
                        <td>
                            <a href="{{ route('exams.show', $exam) }}" class="btn btn-sm btn-primary">
                                <i class="fas fa-eye"></i> {{ __('messages.see_details') }}
                            </a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @endif

    <!-- My Registrations -->
    @if(auth()->user()->isStudent())
    @if($registrations->count() > 0)
    <div class="card mb-5">
        <div class="card-header">
            <h5 class="mb-0"><i class="fas fa-clipboard-list"></i> {{ __('messages.my_registrations') }}</h5>
        </div>
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead>
                    <tr>
                        <th>{{ __('messages.exam') }}</th>
                        <th>{{ __('messages.date') }}</th>
                        <th>{{ __('messages.status') }}</th>
                        <th>{{ __('messages.payment') }}</th>
                        <th>{{ __('messages.result') }}</th>
                        @if(auth()->user()->isAdmin())
                            <th>{{ __('messages.action') }}</th>
                        @endif
                    </tr>
                </thead>
                <tbody>
                    @foreach($registrations as $registration)
                    <tr>
                        <td>
                            <strong>{{ $registration->exam->name }}</strong><br>
                            <small class="text-muted">{{ $registration->exam->location ?? 'N/A' }}</small>
                        </td>
                        <td>{{ $registration->exam->date->format('d/m/Y H:i') }}</td>
                        <td>
                            <span class="badge badge-status-{{ $registration->status }}">
                                {{ ucfirst($registration->status) }}
                            </span>
                        </td>
                        <td>
                            @if($registration->payment_status === 'paid')
                                <span class="badge bg-success">
                                    <i class="fas fa-check"></i> {{ __('messages.paid') }}
                                </span>
                            @else
                                <span class="badge bg-warning text-dark">
                                    <i class="fas fa-hourglass-end"></i> {{ __('messages.pending') }}
                                </span>
                            @endif
                        </td>
                        <td>
                            @if($registration->payment_status !== 'paid')
                                <span class="text-muted">{{ __('messages.payment_required') }}</span>
                            @elseif($registration->result && $registration->result->published_at)
                                <a href="{{ route('results.show', $registration->result) }}" class="badge bg-primary text-decoration-none">
                                    {{ $registration->result->level_achieved ?? __('messages.result') }}
                                </a>
                            @elseif($registration->result && !$registration->result->published_at)
                                <span class="text-muted">{{ __('messages.results_not_published') }}</span>
                            @else
                                <span class="text-muted">{{ __('messages.no_result') }}</span>
                            @endif
                        </td>
                        @if(auth()->user()->isAdmin())
                        <td>
                            @if($registration->status === 'pending' && $registration->payment_status === 'unpaid')
                                <form action="{{ route('admin.registrations.confirm', $registration) }}" method="POST" style="display:inline;">
                                    @csrf
                                    <button type="button" class="btn btn-sm btn-warning confirm-action" data-confirm-message="{{ __('messages.confirm_payment') }}">
                                        <i class="fas fa-credit-card"></i> {{ __('messages.mark_paid') }}
                                    </button>
                                </form>
                            @endif

                            @if($registration->status !== 'cancelled' && $registration->status !== 'completed')
                                <form action="{{ route('admin.registrations.reject', $registration) }}" method="POST" style="display:inline;">
                                    @csrf
                                    <button type="button" class="btn btn-sm btn-danger confirm-action" data-confirm-message="{{ __('messages.are_you_sure') }}">
                                        <i class="fas fa-times"></i> {{ __('messages.cancel_btn') }}
                                    </button>
                                </form>
                            @endif
                        </td>
                        @endif
                    </tr>

                    
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @endif
    @endif

    <!-- My Results -->
    <div id="my-results">
    @if($results->count() > 0)
    <div class="card mb-5">
        <div class="card-header">
            <h5 class="mb-0"><i class="fas fa-trophy"></i> {{ __('messages.my_results') }}</h5>
        </div>
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead>
                    <tr>
                        <th>{{ __('messages.exam') }}</th>
                        <th>{{ __('messages.level_achieved') }}</th>
                        <th>{{ __('messages.score') }}</th>
                        <th>{{ __('messages.status') }}</th>
                        <th>{{ __('messages.date_published') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($results as $result)
                    <tr>
                        <td><strong><a href="{{ route('results.show', $result) }}">{{ $result->exam->name }}</a></strong></td>
                        <td>
                            <span class="badge bg-primary">{{ $result->level_achieved ?? 'N/A' }}</span>
                        </td>
                        <td>
                            @if($result->total_score)
                                {{ $result->total_score }}/100
                            @else
                                -
                            @endif
                        </td>
                        <td>
                            <span class="badge {{ $result->isPassed() ? 'bg-success' : 'bg-danger' }}">
                                {{ ucfirst($result->status) }}
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

    @if($registrations->count() === 0 && $results->count() === 0)
    <div class="alert alert-info text-center py-5">
        <i class="fas fa-info-circle fa-2x mb-3"></i>
        <h5>{{ __('messages.no_registrations') }}</h5>
        <p>{{ __('messages.not_registered_yet') }} <a href="{{ route('exams.index') }}">{{ __('messages.check_available_exams') }}</a></p>
    </div>
    @endif
</div>
@endsection
