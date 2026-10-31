@extends('layouts.app')

@section('title', __('messages.enter_results_title') . ' - ÖSD Exams')

@section('content')
<div class="container py-5">
    <div class="row mb-4">
        <div class="col-12">
            <a href="{{ route('admin.exams') }}" class="btn btn-secondary mb-3">
                <i class="fas fa-arrow-left"></i> {{ __('messages.back_button') }}
            </a>
            <h2><i class="fas fa-pencil-alt"></i> {{ __('messages.results_for_exam') }} - {{ $exam->name }}</h2>
        </div>
    </div>

    @if(auth()->user()->isSuperAdmin())
    <div class="card mb-4">
        <div class="card-header">
            <h5 class="mb-0"><i class="fas fa-file-import"></i> {{ __('messages.results_import_title') }}</h5>
        </div>
        <div class="card-body">
            <p>{{ __('messages.results_import_help') }}</p>
            <p class="small text-muted">{{ __('messages.results_import_columns') }}</p>
            <a href="{{ route('admin.exams.results.template', $exam) }}" class="btn btn-outline-primary mb-3">
                <i class="fas fa-download"></i> {{ __('messages.results_download_template') }}
            </a>
            <form action="{{ route('admin.exams.results.import', $exam) }}" method="POST" enctype="multipart/form-data" class="row g-2 align-items-end">
                @csrf
                <div class="col-md-8">
                    <label for="results_file" class="form-label">{{ __('messages.results_choose_file') }}</label>
                    <input type="file" class="form-control" id="results_file" name="results_file" accept=".xlsx,.xls,.csv,.txt" required>
                </div>
                <div class="col-md-4">
                    <button type="submit" class="btn btn-success">
                        <i class="fas fa-upload"></i> {{ __('messages.results_import_button') }}
                    </button>
                </div>
            </form>
        </div>
    </div>
    @endif

    <div class="card">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead>
                    <tr>
                        <th>{{ __('messages.student_header') }}</th>
                        <th>{{ __('messages.reading_score') }}</th>
                        <th>{{ __('messages.listening_score') }}</th>
                        <th>{{ __('messages.writing_score') }}</th>
                        <th>{{ __('messages.speaking_score') }}</th>
                        <th>{{ __('messages.level_header') }}</th>
                        <th>{{ __('messages.status_header') }}</th>
                        <th>{{ __('messages.action') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($registrations as $registration)
                    @php
                        $selectedModules = $registration->selected_modules ?: ['reading', 'listening', 'writing', 'speaking'];
                        $moduleLabels = [
                            'reading' => __('messages.reading_score_label'),
                            'listening' => __('messages.listening_score_label'),
                            'writing' => __('messages.writing_score_label'),
                            'speaking' => __('messages.speaking_score_label'),
                        ];
                    @endphp
                    <tr>
                        <td>
                            <strong>{{ $registration->user->first_name }} {{ $registration->user->last_name }}</strong><br>
                            <small class="text-muted">{{ $registration->user->email }}</small>
                        </td>
                        <td>{{ in_array('reading', $selectedModules, true) ? ($registration->result?->reading_score ?? '-') : '-' }}</td>
                        <td>{{ in_array('listening', $selectedModules, true) ? ($registration->result?->listening_score ?? '-') : '-' }}</td>
                        <td>{{ in_array('writing', $selectedModules, true) ? ($registration->result?->writing_score ?? '-') : '-' }}</td>
                        <td>{{ in_array('speaking', $selectedModules, true) ? ($registration->result?->speaking_score ?? '-') : '-' }}</td>
                        <td>{{ $registration->result?->level_achieved ?? '-' }}</td>
                        <td>
                            @if($registration->result)
                                <span class="badge {{ $registration->result->isPassed() ? 'bg-success' : 'bg-danger' }}">
                                    {{ $registration->result->isPassed() ? __('messages.passed') : __('messages.failed') }}
                                </span>
                            @else
                                <span class="badge bg-secondary">{{ __('messages.not_entered') }}</span>
                            @endif
                        </td>
                        <td>
                            <button class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#resultModal{{ $registration->id }}">
                                <i class="fas fa-edit"></i> {{ __('messages.enter_button') }}
                            </button>
                        </td>
                    </tr>

                    <!-- Result Modal -->
                    <div class="modal fade" id="resultModal{{ $registration->id }}" tabindex="-1">
                        <div class="modal-dialog modal-lg">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title">{{ __('messages.results_modal_title') }} - {{ $registration->user->first_name }} {{ $registration->user->last_name }}</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                </div>
                                <form action="{{ route('admin.results.store', $registration) }}" method="POST">
                                    @csrf
                                    <div class="modal-body">
                                        <div class="row">
                                            @foreach($selectedModules as $module)
                                                <div class="col-md-6">
                                                    <div class="mb-3">
                                                        <label for="{{ $module }}_{{ $registration->id }}" class="form-label">{{ $moduleLabels[$module] }}</label>
                                                        <input type="number" class="form-control" id="{{ $module }}_{{ $registration->id }}"
                                                               name="{{ $module }}_score" min="0" max="100" step="1"
                                                               value="{{ $registration->result?->{$module . '_score'} ?? '' }}" required>
                                                    </div>
                                                </div>
                                            @endforeach
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
                                        <button type="submit" class="btn btn-primary">{{ __('messages.save_results') }}</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                    @empty
                    <tr>
                        <td colspan="8" class="text-center text-muted py-4">
                            {{ __('messages.no_registrations_for_exam') }}
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($registrations->count() > 0)
        <div class="card-footer">
            <form action="{{ route('admin.exams.publish-results', $exam) }}" method="POST">
                @csrf
                <button type="button" class="btn btn-success confirm-action" data-confirm-message="{{ __('messages.publish_all_results') }}">
                    <i class="fas fa-share"></i> {{ __('messages.publish_all_results') }}
                </button>
            </form>
        </div>
        @endif
    </div>
</div>
@endsection
