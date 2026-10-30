@extends('layouts.app')

@section('title', __('messages.exams_index_title'))

@section('content')
<div class="hero">
    <div class="container">
        <h1><i class="fas fa-book"></i> {{ __('messages.osd_exams') }}</h1>
        <p>{{ __('messages.discover_exams') }}</p>
    </div>
</div>

<div class="container py-5">
    <div class="row">
        @forelse($exams as $exam)
        <div class="col-md-6 col-lg-4 mb-4">
            <div class="card h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start mb-3">
                        <h5 class="card-title mb-0">{{ __('messages.level_label') }} {{ $exam->name }}</h5>
                    </div>
                    
                    <p class="card-text text-muted mb-3">{{ Str::limit($exam->description, 100) }}</p>
                    
                    <div class="mb-3">
                        <p class="mb-2">
                            <i class="fas fa-calendar-alt text-primary"></i>
                            <strong>{{ $exam->date->format('d/m/Y H:i') }}</strong>
                        </p>
                        <p class="mb-2">
                            <i class="fas fa-map-marker-alt text-primary"></i>
                            {{ $exam->location ?? __('messages.location_to_be_defined') }}
                        </p>
                        <p class="mb-0">
                            <i class="fas fa-euro-sign text-success"></i>
                            <strong class="text-success">{{ number_format($exam->price, 2) }}€</strong>
                        </p>
                    </div>

                    <a href="{{ route('exams.show', $exam) }}" class="btn btn-primary w-100">
                        <i class="fas fa-info-circle"></i> {{ __('messages.see_details') }}
                    </a>
                </div>
            </div>
        </div>
        @empty
        <div class="col-12">
            <div class="alert alert-warning text-center py-5">
                <i class="fas fa-exclamation-triangle fa-2x mb-3"></i>
              <h5>{{ __('messages.no_exams') }}</h5>
                <p>{{ __('messages.recheck_later') }}</p>
            </div>
        </div>
        @endforelse
    </div>

    <!-- Pagination -->
    @if($exams->hasPages())
    <div class="d-flex justify-content-center mt-5">
        {{ $exams->links() }}
    </div>
    @endif
</div>
@endsection
