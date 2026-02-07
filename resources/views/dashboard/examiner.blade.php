@extends('layouts.app')

@section('title', 'Examiner Dashboard - ÖSD Exams')

@section('content')
<div class="hero" style="padding: 40px 0;">
    <div class="container">
        <h2><i class="fas fa-tasks"></i> {{ __('messages.examiner_dashboard') }}</h2>
    </div>
</div>

<div class="container py-5">
    <div class="row mb-5">
        <div class="col-md-6">
            <div class="stat-card">
                <h3>{{ $statistics['pendingResults'] }}</h3>
                <p><i class="fas fa-hourglass-end"></i> {{ __('messages.pending_results') }}</p>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-6">
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0"><i class="fas fa-calendar-alt"></i> {{ __('messages.upcoming_exams') }}</h5>
                </div>
                @if($upcomingExams->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead>
                                <tr>
                                    <th>{{ __('messages.exam') }}</th>
                                    <th>{{ __('messages.date') }}</th>
                                    <th>{{ __('messages.registered') }}</th>
                                    <th>{{ __('messages.status') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($upcomingExams as $exam)
                                <tr>
                                    <td>{{ $exam->name }}</td>
                                    <td>{{ $exam->date->format('d/m/Y H:i') }}</td>
                                    <td>
                                        <span class="badge bg-info">{{ $exam->registered_count }} / {{ $exam->max_participants }}</span>
                                    </td>
                                    <td>
                                        <span class="badge badge-status-{{ $exam->status }}">
                                            {{ __('messages.status_' . $exam->status) }}
                                        </span>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="alert alert-info m-3">{{ __('messages.no_upcoming_exams') }}</div>
                @endif
            </div>
        </div>

        <div class="col-lg-6">
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0"><i class="fas fa-play-circle"></i> {{ __('messages.ongoing_exams') }}</h5>
                </div>
                @if($ongoingExams->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead>
                                <tr>
                                    <th>{{ __('messages.exam') }}</th>
                                    <th>{{ __('messages.date') }}</th>
                                    <th>{{ __('messages.registered') }}</th>
                                    <th>{{ __('messages.status') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($ongoingExams as $exam)
                                <tr>
                                    <td>{{ $exam->name }}</td>
                                    <td>{{ $exam->date->format('d/m/Y H:i') }}</td>
                                    <td>
                                        <span class="badge bg-info">{{ $exam->registered_count }} / {{ $exam->max_participants }}</span>
                                    </td>
                                    <td>
                                        <span class="badge badge-status-{{ $exam->status }}">
                                            {{ __('messages.status_' . $exam->status) }}
                                        </span>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="alert alert-info m-3">{{ __('messages.no_ongoing_exams') }}</div>
                @endif
            </div>
        </div>

        <div class="col-lg-6">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0"><i class="fas fa-check-circle"></i> {{ __('messages.completed_exams') }}</h5>
                </div>
                @if($completedExams->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead>
                                <tr>
                                    <th>{{ __('messages.exam') }}</th>
                                    <th>{{ __('messages.date') }}</th>
                                    <th>{{ __('messages.action') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($completedExams->take(5) as $exam)
                                <tr>
                                    <td>{{ $exam->name }}</td>
                                    <td>{{ $exam->date->format('d/m/Y') }}</td>
                                    <td>
                                        <a href="{{ route('examiner.exams.results', $exam) }}" class="btn btn-sm btn-primary">
                                            <i class="fas fa-pencil-alt"></i> {{ __('messages.enter_results') }}
                                        </a>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="alert alert-info m-3">{{ __('messages.no_completed_exams') }}</div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
