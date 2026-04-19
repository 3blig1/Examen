@extends('layouts.app')

@section('title', __('messages.completed_exam_results') . ' - ÖSD Exams')

@section('content')
<div class="container py-5">
    <div class="row mb-4">
        <div class="col-12">
            <a href="{{ route('admin.dashboard') }}" class="btn btn-secondary mb-3">
                <i class="fas fa-arrow-left"></i> {{ __('messages.back_button') }}
            </a>
            <h2><i class="fas fa-clipboard-check"></i> {{ __('messages.completed_exam_results') }}</h2>
        </div>
    </div>

    <form method="GET" class="mb-4">
        <div class="row g-3">
            <div class="col-md-2">
                <label class="form-label">{{ __('messages.filter_level') }}</label>
                <select name="level" class="form-select">
                    <option value="">{{ __('messages.all') }}</option>
                    <option value="A1" {{ request('level') === 'A1' ? 'selected' : '' }}>A1</option>
                    <option value="A2" {{ request('level') === 'A2' ? 'selected' : '' }}>A2</option>
                    <option value="B1" {{ request('level') === 'B1' ? 'selected' : '' }}>B1</option>
                    <option value="B2" {{ request('level') === 'B2' ? 'selected' : '' }}>B2</option>
                    <option value="C1" {{ request('level') === 'C1' ? 'selected' : '' }}>C1</option>
                    <option value="C2" {{ request('level') === 'C2' ? 'selected' : '' }}>C2</option>
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label">{{ __('messages.filter_date_from') }}</label>
                <input type="date" name="date_from" class="form-control" value="{{ request('date_from') }}">
            </div>
            <div class="col-md-2">
                <label class="form-label">{{ __('messages.filter_date_to') }}</label>
                <input type="date" name="date_to" class="form-control" value="{{ request('date_to') }}">
            </div>
            <div class="col-md-4">
                <label class="form-label">{{ __('messages.filter_location') }}</label>
                <input type="text" name="location" class="form-control" placeholder="{{ __('messages.location') }}" value="{{ request('location') }}">
            </div>
            <div class="col-md-2 d-flex align-items-end">
                <button class="btn btn-primary w-100" type="submit">{{ __('messages.filter') }}</button>
            </div>
        </div>
        <div class="mt-3">
            <a href="{{ route('admin.exams.completed-results') }}" class="btn btn-outline-secondary btn-sm">{{ __('messages.reset_filters') }}</a>
        </div>
    </form>

    <div class="card">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead>
                    <tr>
                        <th>{{ __('messages.level') }}</th>
                        <th>{{ __('messages.date') }}</th>
                        <th>{{ __('messages.location') }}</th>
                        <th>{{ __('messages.status') }}</th>
                        <th>{{ __('messages.action') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($exams as $exam)
                    <tr>
                        <td><strong>{{ $exam->name }}</strong></td>
                        <td>{{ $exam->date->format('d/m/Y H:i') }}</td>
                        <td>{{ $exam->location ?? __('messages.not_available_short') }}</td>
                        <td>
                            <span class="badge badge-status-{{ $exam->status }}">
                                {{ __('messages.status_' . $exam->status) }}
                            </span>
                        </td>
                        <td>
                            <a href="{{ route('admin.exams.results-view', $exam) }}" class="btn btn-sm btn-success" title="{{ __('messages.view_results') }}">
                                <i class="fas fa-eye"></i> {{ __('messages.view_results') }}
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="text-center text-muted py-4">
                            {{ __('messages.no_completed_exams') }}
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    @if($exams->hasPages())
    <div class="d-flex justify-content-center mt-5">
        {{ $exams->links() }}
    </div>
    @endif
</div>
@endsection
