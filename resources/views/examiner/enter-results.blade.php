@extends('layouts.app')

@section('title', __('messages.enter_results') . ' - ÖSD Exams')

@section('content')
<div class="container py-5">
    <div class="row mb-4">
        <div class="col-12">
            <a href="{{ route('examiner.dashboard') }}" class="btn btn-secondary mb-3">
                <i class="fas fa-arrow-left"></i> {{ __('messages.back_button') }}
            </a>
            <h2><i class="fas fa-pencil-alt"></i> {{ __('messages.enter_results') }} - {{ $exam->name }}</h2>
            <p class="text-muted">{{ $exam->date->format('d/m/Y H:i') }}</p>
        </div>
    </div>

    <div class="card">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead>
                    <tr>
                        <th>{{ __('messages.student_name') }}</th>
                        <th>{{ __('messages.email_header') }}</th>
                        <th>{{ __('messages.status') }}</th>
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
                        <td>
                            @if($registration->result)
                                <span class="badge {{ $registration->result->isPassed() ? 'bg-success' : 'bg-danger' }}">
                                    {{ ucfirst($registration->result->status) }}
                                </span>
                            @else
                                <span class="badge bg-warning text-dark">{{ __('messages.pending') }}</span>
                            @endif
                        </td>
                        <td>
                            <button type="button" class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#resultModal{{ $registration->id }}">
                                <i class="fas fa-edit"></i> {{ __('messages.enter_result') }}
                            </button>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="text-center text-muted py-4">
                            {{ __('messages.no_registrations') }}
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($registrations->count() > 0)
        <div class="card-footer">
            <form action="{{ route('examiner.exams.publish-results', $exam) }}" method="POST">
                @csrf
                <button type="button" class="btn btn-success confirm-action" data-confirm-message="{{ __('messages.publish_all_results') }}">
                    <i class="fas fa-share"></i> {{ __('messages.publish_all_results') }}
                </button>
            </form>
        </div>
        @endif
    </div>

    @foreach($registrations as $registration)
    <!-- Result Modal -->
    <div class="modal" id="resultModal{{ $registration->id }}" tabindex="-1" aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false" data-bs-scroll="true">
        <div class="modal-dialog modal-lg modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">{{ __('messages.enter_result') }} - {{ $registration->user->first_name }} {{ $registration->user->last_name }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form action="{{ route('examiner.results.store', $registration) }}" method="POST">
                    @csrf
                    <div class="modal-body">
                        <div class="mb-3">
                            <p class="text-muted small"><strong>{{ __('messages.selected_modules') }}:</strong> 
                                @foreach($registration->selected_modules as $module)
                                    <span class="badge bg-info">{{ ucfirst($module) }}</span>
                                @endforeach
                            </p>
                        </div>
                        <div class="row">
                            @if(in_array('reading', $registration->selected_modules))
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="reading_{{ $registration->id }}" class="form-label">{{ __('messages.reading_score') }} (0-100)</label>
                                    <input type="number" class="form-control" id="reading_{{ $registration->id }}" name="reading_score" min="0" max="100" step="1" value="{{ $registration->result?->reading_score ?? 0 }}" required>
                                </div>
                            </div>
                            @else
                            <input type="hidden" name="reading_score" value="0">
                            @endif

                            @if(in_array('writing', $registration->selected_modules))
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="writing_{{ $registration->id }}" class="form-label">{{ __('messages.writing_score') }} (0-100)</label>
                                    <input type="number" class="form-control" id="writing_{{ $registration->id }}" name="writing_score" min="0" max="100" step="1" value="{{ $registration->result?->writing_score ?? 0 }}" required>
                                </div>
                            </div>
                            @else
                            <input type="hidden" name="writing_score" value="0">
                            @endif

                            @if(in_array('listening', $registration->selected_modules))
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="listening_{{ $registration->id }}" class="form-label">{{ __('messages.listening_score') }} (0-100)</label>
                                    <input type="number" class="form-control" id="listening_{{ $registration->id }}" name="listening_score" min="0" max="100" step="1" value="{{ $registration->result?->listening_score ?? 0 }}" required>
                                </div>
                            </div>
                            @else
                            <input type="hidden" name="listening_score" value="0">
                            @endif

                            @if(in_array('speaking', $registration->selected_modules))
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="speaking_{{ $registration->id }}" class="form-label">{{ __('messages.speaking_score') }} (0-100)</label>
                                    <input type="number" class="form-control" id="speaking_{{ $registration->id }}" name="speaking_score" min="0" max="100" step="1" value="{{ $registration->result?->speaking_score ?? 0 }}" required>
                                </div>
                            </div>
                            @else
                            <input type="hidden" name="speaking_score" value="0">
                            @endif
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="level_{{ $registration->id }}" class="form-label">{{ __('messages.level_achieved') }}</label>
                                    <select class="form-select" id="level_{{ $registration->id }}" name="level_achieved">
                                        <option value="">{{ __('messages.select_level') }}</option>
                                        <option value="A1" {{ $registration->result?->level_achieved === 'A1' ? 'selected' : '' }}>A1</option>
                                        <option value="A2" {{ $registration->result?->level_achieved === 'A2' ? 'selected' : '' }}>A2</option>
                                        <option value="B1" {{ $registration->result?->level_achieved === 'B1' ? 'selected' : '' }}>B1</option>
                                        <option value="B2" {{ $registration->result?->level_achieved === 'B2' ? 'selected' : '' }}>B2</option>
                                        <option value="C1" {{ $registration->result?->level_achieved === 'C1' ? 'selected' : '' }}>C1</option>
                                        <option value="C2" {{ $registration->result?->level_achieved === 'C2' ? 'selected' : '' }}>C2</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="status_{{ $registration->id }}" class="form-label">{{ __('messages.status') }}</label>
                                    <select class="form-select" id="status_{{ $registration->id }}" name="status" required>
                                        <option value="">{{ __('messages.select_status') }}</option>
                                        <option value="passed" {{ $registration->result?->status === 'passed' ? 'selected' : '' }}>{{ __('messages.passed') }}</option>
                                        <option value="failed" {{ $registration->result?->status === 'failed' ? 'selected' : '' }}>{{ __('messages.failed') }}</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="remarks_{{ $registration->id }}" class="form-label">{{ __('messages.remarks') }}</label>
                            <textarea class="form-control" id="remarks_{{ $registration->id }}" name="remarks" rows="3">{{ $registration->result?->remarks ?? '' }}</textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('messages.cancel_btn') }}</button>
                        <button type="submit" class="btn btn-primary">{{ __('messages.save_result') }}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    @endforeach
</div>
@endsection

@push('js')
<script>
document.addEventListener('DOMContentLoaded', function () {
    document.querySelectorAll('[id^="resultModal"]').forEach(function (el) {
        var instance = bootstrap.Modal.getOrCreateInstance(el, {
            backdrop: 'static',
            keyboard: false,
            focus: true,
            scrollBar: false
        });
    });
});
</script>
@endpush
