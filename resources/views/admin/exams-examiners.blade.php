@extends('layouts.app')

@section('title', __('messages.examiner_management'))

@section('content')
<div class="container py-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="mb-1">{{ __('messages.examiner_management') }}</h2>
            <div class="text-muted">{{ __('messages.exam') }}: {{ $exam->name }} - {{ $exam->date->format('d/m/Y H:i') }}</div>
        </div>
        <a href="{{ route('admin.exams') }}" class="btn btn-outline-secondary">{{ __('messages.back_button') }}</a>
    </div>

    @if(session('success'))
        <div class="alert alert-success">{{ \App\Support\FlashMessage::render(session('success')) }}</div>
    @endif
    @if($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('admin.exams.examiners.store', $exam) }}">
        @csrf
        <div class="card">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead>
                        <tr>
                            <th>{{ __('messages.examiner_role') }}</th>
                            <th>{{ __('messages.module_reading') }}</th>
                            <th>{{ __('messages.module_listening') }}</th>
                            <th>{{ __('messages.module_writing') }}</th>
                            <th>{{ __('messages.module_speaking') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($examiners as $examiner)
                            @php
                                $assignment = $assignments->get($examiner->id);
                                $modules = $assignment?->modules ?? [];
                            @endphp
                            <tr>
                                <td>
                                    <strong>{{ $examiner->first_name }} {{ $examiner->last_name }}</strong><br>
                                    <small class="text-muted">{{ $examiner->email }}</small>
                                </td>
                                <td class="text-center">
                                    <input type="checkbox" name="assignments[{{ $examiner->id }}][]" value="reading" {{ in_array('reading', $modules, true) ? 'checked' : '' }}>
                                </td>
                                <td class="text-center">
                                    <input type="checkbox" name="assignments[{{ $examiner->id }}][]" value="listening" {{ in_array('listening', $modules, true) ? 'checked' : '' }}>
                                </td>
                                <td class="text-center">
                                    <input type="checkbox" name="assignments[{{ $examiner->id }}][]" value="writing" {{ in_array('writing', $modules, true) ? 'checked' : '' }}>
                                </td>
                                <td class="text-center">
                                    <input type="checkbox" name="assignments[{{ $examiner->id }}][]" value="speaking" {{ in_array('speaking', $modules, true) ? 'checked' : '' }}>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center text-muted py-4">{{ __('messages.no_examiners') }}</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div class="mt-3">
            <button type="submit" class="btn btn-primary">{{ __('messages.save_assignments') }}</button>
        </div>
    </form>
</div>
@endsection
