@extends('layouts.app')

@section('title', __('messages.examiner_tasks') . ' - ÖSD Exams')

@section('content')
<div class="hero" style="padding: 40px 0;">
    <div class="container">
        <h2><i class="fas fa-user-check"></i> {{ __('messages.examiner_tasks') }}</h2>
        <p>{{ __('messages.examiner_tasks_overview') }}</p>
    </div>
</div>

<div class="container py-5">
    <div class="row mb-4">
        <div class="col-md-4 mb-3">
            <div class="card h-100">
                <div class="card-body">
                    <div class="text-muted text-uppercase" style="font-size: 0.8rem; letter-spacing: 0.5px;">{{ __('messages.all') }}</div>
                    <div class="fs-3 fw-semibold">{{ $ongoingCount + $upcomingCount }}</div>
                    <div class="text-muted">{{ __('messages.tasks_assigned') }}</div>
                </div>
            </div>
        </div>
        <div class="col-md-4 mb-3">
            <div class="card h-100">
                <div class="card-body">
                    <div class="text-muted text-uppercase" style="font-size: 0.8rem; letter-spacing: 0.5px;">{{ __('messages.status_ongoing') }}</div>
                    <div class="fs-3 fw-semibold">{{ $ongoingCount }}</div>
                    <div class="text-muted">{{ __('messages.tasks_assigned') }}</div>
                </div>
            </div>
        </div>
        <div class="col-md-4 mb-3">
            <div class="card h-100">
                <div class="card-body">
                    <div class="text-muted text-uppercase" style="font-size: 0.8rem; letter-spacing: 0.5px;">{{ __('messages.status_upcoming') }}</div>
                    <div class="fs-3 fw-semibold">{{ $upcomingCount }}</div>
                    <div class="text-muted">{{ __('messages.tasks_assigned') }}</div>
                </div>
            </div>
        </div>
    </div>

    <div class="card mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('admin.examiners.tasks') }}" class="row g-3 align-items-end">
                <div class="col-md-3">
                    <label class="form-label">{{ __('messages.role_examiner') }}</label>
                    <select name="examiner" class="form-select">
                        <option value="">{{ __('messages.all') }}</option>
                        @foreach($examiners as $examiner)
                            <option value="{{ $examiner->id }}" {{ (string) $examinerId === (string) $examiner->id ? 'selected' : '' }}>
                                {{ $examiner->first_name }} {{ $examiner->last_name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label">{{ __('messages.filter_date_from') }}</label>
                    <input type="date" name="date_from" class="form-control" value="{{ $dateFrom }}">
                </div>
                <div class="col-md-2">
                    <label class="form-label">{{ __('messages.filter_date_to') }}</label>
                    <input type="date" name="date_to" class="form-control" value="{{ $dateTo }}">
                </div>
                <div class="col-md-5">
                    <label class="form-label">{{ __('messages.filter_status') }}</label>
                    <div class="d-flex flex-wrap gap-2">
                        <input type="hidden" name="status" value="{{ $statusFilter }}">
                        <button type="button" class="btn btn-sm {{ $statusFilter === 'all' ? 'btn-primary' : 'btn-outline-primary' }}" onclick="this.form.status.value='all'; this.form.submit();">
                            {{ __('messages.all') }}
                        </button>
                        <button type="button" class="btn btn-sm {{ $statusFilter === 'ongoing' ? 'btn-primary' : 'btn-outline-primary' }}" onclick="this.form.status.value='ongoing'; this.form.submit();">
                            {{ __('messages.status_ongoing') }}
                        </button>
                        <button type="button" class="btn btn-sm {{ $statusFilter === 'upcoming' ? 'btn-primary' : 'btn-outline-primary' }}" onclick="this.form.status.value='upcoming'; this.form.submit();">
                            {{ __('messages.status_upcoming') }}
                        </button>
                    </div>
                </div>
                <div class="col-12 d-flex gap-2">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-filter"></i> {{ __('messages.filter') }}
                    </button>
                    <a class="btn btn-outline-secondary" href="{{ route('admin.examiners.tasks') }}">
                        {{ __('messages.reset_filters') }}
                    </a>
                </div>
            </form>
        </div>
    </div>

    @if($assignmentsByExaminer->count() > 0)
        @foreach($assignmentsByExaminer as $examinerId => $assignments)
            @php
                $examiner = $assignments->first()->examiner;
                $labels = [
                    'reading' => __('messages.module_reading'),
                    'listening' => __('messages.module_listening'),
                    'writing' => __('messages.module_writing'),
                    'speaking' => __('messages.module_speaking'),
                ];
            @endphp
            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <div>
                        <h5 class="mb-0"><i class="fas fa-user"></i> {{ $examiner?->first_name }} {{ $examiner?->last_name }}</h5>
                        <small class="text-muted">{{ $examiner?->email }}</small>
                    </div>
                    <span class="badge bg-primary">{{ $assignments->count() }} {{ __('messages.tasks_assigned') }}</span>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead>
                            <tr>
                                <th>{{ __('messages.exam') }}</th>
                                <th>{{ __('messages.date') }}</th>
                                <th>{{ __('messages.status') }}</th>
                                <th>{{ __('messages.modules') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($assignments as $assignment)
                                @php
                                    $modules = collect($assignment->modules ?? [])
                                        ->map(fn($m) => $labels[$m] ?? $m)
                                        ->values();
                                @endphp
                                <tr>
                                    <td>{{ $assignment->exam?->name }}</td>
                                    <td>{{ $assignment->exam?->date?->format('d/m/Y H:i') }}</td>
                                    <td>
                                        @if($assignment->exam?->status === 'ongoing')
                                            <span class="badge bg-warning text-dark">{{ __('messages.status_ongoing') }}</span>
                                        @else
                                            <span class="badge bg-info text-dark">{{ __('messages.status_upcoming') }}</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($modules->count() > 0)
                                            @foreach($modules as $module)
                                                <span class="badge bg-light text-dark me-1">{{ $module }}</span>
                                            @endforeach
                                        @else
                                            <span class="text-muted">{{ __('messages.not_available_short') }}</span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        @endforeach
    @else
        <div class="alert alert-info">
            {{ __('messages.no_examiner_tasks') }}
        </div>
    @endif
</div>
@endsection
