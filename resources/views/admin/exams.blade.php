@extends('layouts.app')

@section('title', __('messages.exam_management') . ' - ÖSD Exams')

@section('content')
<div class="container py-5">
    <div class="row mb-4">
        <div class="col-12 d-flex justify-content-between align-items-center">
            <h2><i class="fas fa-book"></i> {{ __('messages.exam_management') }}</h2>
            <a href="{{ route('admin.exams.create') }}" class="btn btn-primary">
                <i class="fas fa-plus"></i> {{ __('messages.new_exam') }}
            </a>
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
            <div class="col-md-3">
                <label class="form-label">{{ __('messages.filter_location') }}</label>
                <input type="text" name="location" class="form-control" placeholder="{{ __('messages.location') }}" value="{{ request('location') }}">
            </div>
            <div class="col-md-2">
                <label class="form-label">{{ __('messages.filter_status') }}</label>
                <select name="status" class="form-select">
                    <option value="">{{ __('messages.all') }}</option>
                    <option value="upcoming" {{ request('status') === 'upcoming' ? 'selected' : '' }}>{{ __('messages.status_upcoming') }}</option>
                    <option value="ongoing" {{ request('status') === 'ongoing' ? 'selected' : '' }}>{{ __('messages.status_ongoing') }}</option>
                    <option value="completed" {{ request('status') === 'completed' ? 'selected' : '' }}>{{ __('messages.status_completed') }}</option>
                </select>
            </div>
            <div class="col-md-1 d-flex align-items-end">
                <button class="btn btn-primary w-100" type="submit">{{ __('messages.filter') }}</button>
            </div>
        </div>
        <div class="mt-3">
            <a href="{{ route('admin.exams') }}" class="btn btn-outline-secondary btn-sm">{{ __('messages.reset_filters') }}</a>
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
                        <th>{{ __('messages.capacity') }}</th>
                        <th>{{ __('messages.registrations') }}</th>
                        <th>{{ __('messages.price') }}</th>
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
                        <td>{{ $exam->max_participants }}</td>
                        <td>
                            <span class="badge bg-info">{{ $exam->registered_count }} / {{ $exam->max_participants }}</span>
                        </td>
                        <td>{{ number_format($exam->price, 0, ',', ' ') }} FCFA</td>
                        <td>
                            <span class="badge badge-status-{{ $exam->status }}">
                                {{ __('messages.status_' . $exam->status) }}
                            </span>
                        </td>
                        <td>
                            <a href="{{ route('admin.exams.registrations', $exam) }}" class="btn btn-sm btn-info" title="{{ __('messages.registrations_for_exam') }}">
                                <i class="fas fa-list"></i>
                            </a>
                            <a href="{{ route('admin.exams.examiners', $exam) }}" class="btn btn-sm btn-secondary" title="{{ __('messages.examiner_management') }}">
                                <i class="fas fa-user-check"></i>
                            </a>
                            <a href="{{ route('admin.exams.results', $exam) }}" class="btn btn-sm btn-warning" title="{{ __('messages.enter_results') }}">
                                <i class="fas fa-pencil-alt"></i>
                            </a>
                            <a href="{{ route('admin.exams.edit', $exam) }}" class="btn btn-sm btn-warning" title="{{ __('messages.edit_exam') }}">
                                <i class="fas fa-edit"></i>
                            </a>
                            <form action="{{ route('admin.exams.delete', $exam) }}" method="POST" style="display:inline;">
                                @csrf
                                @method('DELETE')
                                <button type="button" class="btn btn-sm btn-danger confirm-action" title="{{ __('messages.delete_exam') }}" data-confirm-message="{{ __('messages.are_you_sure') }}">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                            <td colspan="8" class="text-center text-muted py-4">
                            {{ __('messages.no_exams') }}
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Pagination -->
    @if($exams->hasPages())
    <div class="d-flex justify-content-center mt-5">
        {{ $exams->links() }}
    </div>
    @endif
</div>
@endsection
