@extends('layouts.app')

@section('title', __('messages.examiner_dashboard_title'))

@push('styles')
    <link href="https://fonts.googleapis.com/css2?family=IBM+Plex+Sans:wght@400;500;600&family=Space+Mono:wght@400;600&display=swap" rel="stylesheet">
    <style>
        :root {
            --ops-ink: #1f2933;
            --ops-muted: #6b7280;
            --ops-panel: #ffffff;
            --ops-line: #e6e7eb;
            --ops-accent: #2563eb;
            --ops-warn: #f59e0b;
            --ops-bg: #f6f7f9;
        }

        .ops-shell {
            background: var(--ops-bg);
        }

        .ops-hero {
            background: linear-gradient(135deg, #fdfbfb 0%, #f3f4f7 100%);
            border-bottom: 1px solid var(--ops-line);
        }

        .ops-hero h2 {
            font-family: 'IBM Plex Sans', sans-serif;
            color: var(--ops-ink);
        }

        .ops-hero p {
            color: var(--ops-muted);
        }

        .ops-kpis {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
            gap: 14px;
            margin-bottom: 24px;
        }

        .ops-kpi {
            background: var(--ops-panel);
            border: 1px solid var(--ops-line);
            border-radius: 10px;
            padding: 14px 16px;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .ops-kpi .label {
            font-size: 0.85rem;
            color: var(--ops-muted);
            text-transform: uppercase;
            letter-spacing: 0.4px;
        }

        .ops-kpi .value {
            font-family: 'Space Mono', monospace;
            font-size: 1.8rem;
            font-weight: 600;
            color: var(--ops-ink);
        }

        .ops-grid {
            display: grid;
            grid-template-columns: 1.25fr 1fr;
            gap: 18px;
        }

        .ops-stack {
            display: grid;
            gap: 18px;
        }

        .ops-panel {
            background: var(--ops-panel);
            border: 1px solid var(--ops-line);
            border-radius: 12px;
            overflow: hidden;
        }

        .ops-panel-header {
            padding: 12px 16px;
            border-bottom: 1px solid var(--ops-line);
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .ops-panel-title {
            font-size: 1rem;
            font-weight: 600;
            color: var(--ops-ink);
            margin: 0;
        }

        .ops-table {
            width: 100%;
            border-collapse: collapse;
        }

        .ops-table th,
        .ops-table td {
            padding: 8px 12px;
            border-bottom: 1px solid var(--ops-line);
            vertical-align: middle;
        }

        .ops-table th {
            background: #f2f4f7;
            color: var(--ops-ink);
            font-size: 0.78rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .ops-table tbody tr:hover {
            background: #f9fafb;
        }

        .chip-row {
            display: flex;
            flex-wrap: wrap;
            gap: 6px;
        }

        .chip {
            background: #eef2ff;
            color: #1e3a8a;
            padding: 2px 8px;
            border-radius: 999px;
            font-size: 0.75rem;
            font-weight: 600;
        }

        .status-dot {
            width: 8px;
            height: 8px;
            border-radius: 50%;
            display: inline-block;
            margin-right: 6px;
        }

        .dot-upcoming { background: #3b82f6; }
        .dot-ongoing { background: #f59e0b; }
        .dot-completed { background: #10b981; }

        .ops-empty {
            padding: 16px;
            color: var(--ops-muted);
        }

        @media (max-width: 992px) {
            .ops-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
@endpush

@section('content')
<div class="ops-hero" style="padding: 32px 0;">
    <div class="container">
        <h2><i class="fas fa-tasks"></i> {{ __('messages.examiner_dashboard') }}</h2>
        <p class="mb-0">{{ __('messages.examiner_overview') }}</p>
    </div>
</div>

<div class="container py-4 ops-shell">
    <div class="ops-kpis">
        <div class="ops-kpi">
            <div>
                <div class="label">{{ __('messages.pending_results') }}</div>
                <div class="value">{{ $statistics['pendingResults'] }}</div>
            </div>
            <i class="fas fa-hourglass-end text-warning"></i>
        </div>
        <div class="ops-kpi">
            <div>
                <div class="label">{{ __('messages.tasks_todo') }}</div>
                <div class="value">{{ $pendingAssignmentsCount }}</div>
            </div>
            <i class="fas fa-list-check text-primary"></i>
        </div>
        <div class="ops-kpi">
            <div>
                <div class="label">{{ __('messages.tasks_done') }}</div>
                <div class="value">{{ $completedAssignmentsCount }}</div>
            </div>
            <i class="fas fa-check-circle text-success"></i>
        </div>
    </div>

    <div class="ops-grid">
        <div class="ops-stack">
            <div class="ops-panel">
                <div class="ops-panel-header">
                    <h5 class="ops-panel-title">{{ __('messages.tasks_todo') }}</h5>
                    <span class="text-muted">{{ $pendingAssignmentsCount }}</span>
                </div>
                @if($pendingAssignments->count() > 0)
                    <div class="table-responsive">
                        <table class="ops-table">
                            <thead>
                                <tr>
                                    <th>{{ __('messages.exam') }}</th>
                                    <th>{{ __('messages.date') }}</th>
                                    <th>{{ __('messages.modules') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($pendingAssignments as $assignment)
                                    @php
                                        $labels = [
                                            'reading' => __('messages.module_reading'),
                                            'listening' => __('messages.module_listening'),
                                            'writing' => __('messages.module_writing'),
                                            'speaking' => __('messages.module_speaking'),
                                        ];
                                        $modules = collect($assignment->modules ?? [])
                                            ->map(fn($m) => $labels[$m] ?? $m)
                                            ->values();
                                    @endphp
                                    <tr>
                                        <td>{{ $assignment->exam?->name }}</td>
                                        <td>{{ $assignment->exam?->date?->format('d/m/Y H:i') }}</td>
                                        <td>
                                            @if($modules->count() > 0)
                                                <div class="chip-row">
                                                    @foreach($modules as $module)
                                                        <span class="chip">{{ $module }}</span>
                                                    @endforeach
                                                </div>
                                            @else
                                                <span class="text-muted">{{ __('messages.not_available_short') }}</span>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="ops-empty">{{ __('messages.no_tasks_todo') }}</div>
                @endif
            </div>

            <div class="ops-panel">
                <div class="ops-panel-header">
                    <h5 class="ops-panel-title">{{ __('messages.tasks_done') }}</h5>
                    <span class="text-muted">{{ $completedAssignmentsCount }}</span>
                </div>
                @if($completedAssignments->count() > 0)
                    <div class="table-responsive">
                        <table class="ops-table">
                            <thead>
                                <tr>
                                    <th>{{ __('messages.exam') }}</th>
                                    <th>{{ __('messages.date') }}</th>
                                    <th>{{ __('messages.modules') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($completedAssignments as $assignment)
                                    @php
                                        $labels = [
                                            'reading' => __('messages.module_reading'),
                                            'listening' => __('messages.module_listening'),
                                            'writing' => __('messages.module_writing'),
                                            'speaking' => __('messages.module_speaking'),
                                        ];
                                        $modules = collect($assignment->modules ?? [])
                                            ->map(fn($m) => $labels[$m] ?? $m)
                                            ->values();
                                    @endphp
                                    <tr>
                                        <td>{{ $assignment->exam?->name }}</td>
                                        <td>{{ $assignment->exam?->date?->format('d/m/Y H:i') }}</td>
                                        <td>
                                            @if($modules->count() > 0)
                                                <div class="chip-row">
                                                    @foreach($modules as $module)
                                                        <span class="chip">{{ $module }}</span>
                                                    @endforeach
                                                </div>
                                            @else
                                                <span class="text-muted">-</span>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="ops-empty">{{ __('messages.no_tasks_done') }}</div>
                @endif
            </div>
        </div>

        <div class="ops-stack">
            <div class="ops-panel">
                <div class="ops-panel-header">
                    <h5 class="ops-panel-title">{{ __('messages.upcoming_exams') }}</h5>
                </div>
                @if($upcomingExams->count() > 0)
                    <div class="table-responsive">
                        <table class="ops-table">
                            <thead>
                                <tr>
                                    <th>{{ __('messages.exam') }}</th>
                                    <th>{{ __('messages.date') }}</th>
                                    <th>{{ __('messages.registered') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($upcomingExams as $exam)
                                <tr>
                                    <td><span class="status-dot dot-upcoming"></span>{{ $exam->name }}</td>
                                    <td>{{ $exam->date->format('d/m/Y H:i') }}</td>
                                    <td>{{ $exam->registered_count }} / {{ $exam->max_participants }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="ops-empty">{{ __('messages.no_upcoming_exams') }}</div>
                @endif
            </div>

            <div class="ops-panel">
                <div class="ops-panel-header">
                    <h5 class="ops-panel-title">{{ __('messages.ongoing_exams') }}</h5>
                </div>
                @if($ongoingExams->count() > 0)
                    <div class="table-responsive">
                        <table class="ops-table">
                            <thead>
                                <tr>
                                    <th>{{ __('messages.exam') }}</th>
                                    <th>{{ __('messages.date') }}</th>
                                    <th>{{ __('messages.registered') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($ongoingExams as $exam)
                                <tr>
                                    <td><span class="status-dot dot-ongoing"></span>{{ $exam->name }}</td>
                                    <td>{{ $exam->date->format('d/m/Y H:i') }}</td>
                                    <td>{{ $exam->registered_count }} / {{ $exam->max_participants }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="ops-empty">{{ __('messages.no_ongoing_exams') }}</div>
                @endif
            </div>

            <div class="ops-panel">
                <div class="ops-panel-header">
                    <h5 class="ops-panel-title">{{ __('messages.completed_exams') }}</h5>
                </div>
                @if($completedExams->count() > 0)
                    <div class="table-responsive">
                        <table class="ops-table">
                            <thead>
                                <tr>
                                    <th>{{ __('messages.exam') }}</th>
                                    <th>{{ __('messages.date') }}</th>
                                    <th>{{ __('messages.action') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($completedExams as $exam)
                                <tr>
                                    <td><span class="status-dot dot-completed"></span>{{ $exam->name }}</td>
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
                    <div class="ops-empty">{{ __('messages.no_completed_exams') }}</div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
