@extends('layouts.app')

@section('title', __('messages.results_for_exam') . ' - ÖSD Exams')

@section('content')
<div class="container py-5">
    <div class="row mb-4">
        <div class="col-12">
            <a href="{{ route('admin.exams.completed-results') }}" class="btn btn-secondary mb-3">
                <i class="fas fa-arrow-left"></i> {{ __('messages.back_button') }}
            </a>
            <h2><i class="fas fa-chart-bar"></i> {{ __('messages.results_for_exam') }} - {{ $exam->name }}</h2>
            <p class="text-muted">{{ $exam->date->format('d/m/Y H:i') }}</p>
            <div class="mt-3">
                <a href="{{ route('admin.exams.results.export-pdf', $exam) }}" class="btn btn-sm btn-outline-secondary me-2">
                    <i class="fas fa-file-pdf"></i> {{ __('messages.export_pdf') }}
                </a>
                <a href="{{ route('admin.exams.results.export-excel', $exam) }}" class="btn btn-sm btn-outline-secondary">
                    <i class="fas fa-file-excel"></i> {{ __('messages.export_excel') }}
                </a>
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
                        <th>{{ __('messages.level_achieved') }}</th>
                        <th>{{ __('messages.score') }}</th>
                        <th>{{ __('messages.status') }}</th>
                        <th>{{ __('messages.date_published') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($registrations as $registration)
                    <tr>
                        <td>
                            <strong>{{ $registration->user->first_name }} {{ $registration->user->last_name }}</strong>
                        </td>
                        <td>{{ $registration->user->email }}</td>
                        <td>
                            @if($registration->result)
                                <span class="badge bg-primary">{{ $registration->result->level_achieved ?? 'N/A' }}</span>
                            @else
                                <span class="text-muted">{{ __('messages.no_result') }}</span>
                            @endif
                        </td>
                        <td>
                            @if($registration->result && $registration->result->total_score !== null)
                                {{ $registration->result->total_score }}/100
                            @else
                                -
                            @endif
                        </td>
                        <td>
                            @if($registration->result)
                                <span class="badge {{ $registration->result->isPassed() ? 'bg-success' : 'bg-danger' }}">
                                    {{ ucfirst($registration->result->status) }}
                                </span>
                            @else
                                <span class="text-muted">{{ __('messages.no_result') }}</span>
                            @endif
                        </td>
                        <td>
                            @if($registration->result && $registration->result->published_at)
                                {{ $registration->result->published_at->format('d/m/Y H:i') }}
                            @else
                                {{ __('messages.pending') }}
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="text-center text-muted py-4">
                            {{ __('messages.no_registrations') }}
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
