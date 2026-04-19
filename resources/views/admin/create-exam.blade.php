@extends('layouts.app')

@section('title', __('messages.new_exam') . ' - ÖSD Exams')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <a href="{{ route('admin.exams') }}" class="btn btn-secondary mb-3">
                <i class="fas fa-arrow-left"></i> {{ __('messages.back_button') }}
            </a>

            <div class="card">
                <div class="card-header">
                    <h4 class="mb-0"><i class="fas fa-plus"></i> {{ __('messages.create_exam') }}</h4>
                </div>
                <div class="card-body p-5">
                    <form method="POST" action="{{ route('admin.exams.store') }}">
                        @csrf

                        <div class="mb-4">
                            <label for="name" class="form-label">{{ __('messages.level') }}</label>
                            <select class="form-select form-select-lg @error('name') is-invalid @enderror" 
                                    id="name" name="name" required>
                                <option value="">{{ __('messages.select_level') }}</option>
                                <option value="A1">A1</option>
                                <option value="A2">A2</option>
                                <option value="B1">B1</option>
                                <option value="B2">B2</option>
                                <option value="C1">C1</option>
                                <option value="C2">C2</option>
                            </select>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <label for="description" class="form-label">{{ __('messages.description') }}</label>
                            <textarea class="form-control @error('description') is-invalid @enderror" 
                                      id="description" name="description" rows="4">{{ old('description') }}</textarea>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <label for="exam_type" class="form-label">{{ __('messages.exam_type') }}</label>
                            <select class="form-select @error('exam_type') is-invalid @enderror" 
                                    id="exam_type" name="exam_type" required onchange="updateModuleSelection()">
                                <option value="">{{ __('messages.select_exam_type') }}</option>
                                <option value="complete" {{ old('exam_type') === 'complete' ? 'selected' : '' }}>{{ __('messages.complete_exam') }}</option>
                                <option value="module" {{ old('exam_type') === 'module' ? 'selected' : '' }}>{{ __('messages.module_exam') }}</option>
                            </select>
                            @error('exam_type')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-4" id="modules-section" style="display: none;">
                            <label class="form-label">{{ __('messages.available_modules') }}</label>
                            <div class="card">
                                <div class="card-body">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="available_modules[]" value="reading" 
                                               id="module-reading" {{ in_array('reading', (array)old('available_modules', [])) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="module-reading">
                                            {{ __('messages.reading_score') }}
                                        </label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="available_modules[]" value="writing" 
                                               id="module-writing" {{ in_array('writing', (array)old('available_modules', [])) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="module-writing">
                                            {{ __('messages.writing_score') }}
                                        </label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="available_modules[]" value="listening" 
                                               id="module-listening" {{ in_array('listening', (array)old('available_modules', [])) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="module-listening">
                                            {{ __('messages.listening_score') }}
                                        </label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="available_modules[]" value="speaking" 
                                               id="module-speaking" {{ in_array('speaking', (array)old('available_modules', [])) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="module-speaking">
                                            {{ __('messages.speaking_score') }}
                                        </label>
                                    </div>
                                </div>
                            </div>
                            @error('available_modules')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-4">
                                <label for="date" class="form-label">{{ __('messages.date_and_time') }}</label>
                                <input type="datetime-local" class="form-control @error('date') is-invalid @enderror" 
                                       id="date" name="date" value="{{ old('date') }}" required>
                                @error('date')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-4">
                                <label for="registration_deadline" class="form-label">{{ __('messages.registration_deadline') }}</label>
                                <input type="datetime-local" class="form-control @error('registration_deadline') is-invalid @enderror" 
                                       id="registration_deadline" name="registration_deadline" value="{{ old('registration_deadline') }}">
                                <small class="text-muted">{{ __('messages.registration_deadline_hint') }}</small>
                                @error('registration_deadline')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-4">
                                <label for="location" class="form-label">{{ __('messages.location_label') }}</label>
                                <input type="text" class="form-control @error('location') is-invalid @enderror" 
                                       id="location" name="location" value="{{ old('location') }}" placeholder="{{ __('messages.location_placeholder') }}">
                                @error('location')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-4">
                                <label for="max_participants" class="form-label">{{ __('messages.max_participants') }}</label>
                                <input type="number" class="form-control @error('max_participants') is-invalid @enderror" 
                                       id="max_participants" name="max_participants" value="{{ old('max_participants', 30) }}" min="1" required>
                                @error('max_participants')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-4">
                                <label for="price" class="form-label">{{ __('messages.price') }} (€)</label>
                                <input type="number" class="form-control @error('price') is-invalid @enderror" 
                                       id="price" name="price" value="{{ old('price', 0) }}" min="0" step="0.01" required>
                                <small class="text-muted">{{ __('messages.price_complete_exam') }}</small>
                                @error('price')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-4">
                                <label for="price_per_module" class="form-label">{{ __('messages.price_per_module') }} (€)</label>
                                <input type="number" class="form-control @error('price_per_module') is-invalid @enderror" 
                                       id="price_per_module" name="price_per_module" value="{{ old('price_per_module', 0) }}" min="0" step="0.01">
                                <small class="text-muted">{{ __('messages.price_per_module_hint') }}</small>
                                @error('price_per_module')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary btn-lg">
                                <i class="fas fa-check"></i> {{ __('messages.create_exam_button') }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    function updateModuleSelection() {
        const examType = document.getElementById('exam_type').value;
        const modulesSection = document.getElementById('modules-section');
        const checkboxes = document.querySelectorAll('input[name="available_modules[]"]');
        
        if (examType === 'module') {
            modulesSection.style.display = 'block';
        } else {
            modulesSection.style.display = 'none';
            // Check all checkboxes if complete exam
            checkboxes.forEach(checkbox => {
                checkbox.checked = false;
            });
        }
    }

    // Initialize on page load
    document.addEventListener('DOMContentLoaded', function() {
        updateModuleSelection();
    });
</script>
@endsection
