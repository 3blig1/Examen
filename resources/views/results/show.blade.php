@extends('layouts.app')

@section('title', __('messages.results') . ' - ' . ($result->exam->name ?? ''))

@section('content')
<div class="container py-5">
	<div class="row justify-content-center">
		<div class="col-lg-10">
			<div class="card shadow-sm">
				<div class="card-header bg-white border-0 d-flex justify-content-between align-items-center">
					<div>
						<h4 class="mb-0">{{ $result->exam->name ?? __('messages.exam_details_title') }}</h4>
						<small class="text-muted">{{ $result->exam->date ? $result->exam->date->format('d/m/Y') : '' }}</small>
					</div>
					<div>
						<a href="{{ route('dashboard') }}" class="btn btn-outline-secondary btn-sm">{{ __('messages.back_button') }}</a>
					</div>
				</div>

				<div class="card-body">
					<div class="row">
						<div class="col-md-4 text-center border-end">
							<div class="mb-3">
								<h5 class="mb-1">{{ $result->user->first_name ?? $result->user->name }} {{ $result->user->last_name ?? '' }}</h5>
								<p class="text-muted mb-1">{{ $result->user->email }}</p>
								<p class="text-muted small mb-2">{{ __('messages.country') }}: {{ $result->user->country ?? '-' }}</p>
								<p class="text-muted small">{{ __('messages.dob_label') }}: {{ $result->user->date_of_birth ? $result->user->date_of_birth->format('d/m/Y') : '-' }}</p>
								<p class="mt-2">
									
							</div>
						</div>

						<div class="col-md-8">
							<div class="mb-3 d-flex justify-content-between align-items-center">
								<div>
									<h5 class="mb-0">{{ __('messages.module_scores') ?? 'Module Scores' }}</h5>
									<small class="text-muted">{{ __('messages.total_score') }}: <strong>{{ $result->total_score ?? $result->calculateTotalScore() }}</strong></small>
								</div>
								<div class="text-end">
									<span class="badge {{ $result->isPassed() ? 'bg-success' : 'bg-danger' }}">{{ ucfirst($result->status) }}</span>
								</div>
							</div>

							<div class="mb-3">
								<label class="form-label">{{ __('messages.reading_score_label') }}</label>
								<div class="progress" style="height:18px">
									<div class="progress-bar bg-info" role="progressbar" style="width: {{ $result->reading_score ?? 0 }}%">{{ $result->reading_score ?? '-' }}</div>
								</div>
							</div>

							<div class="mb-3">
								<label class="form-label">{{ __('messages.listening_score_label') }}</label>
								<div class="progress" style="height:18px">
									<div class="progress-bar bg-primary" role="progressbar" style="width: {{ $result->listening_score ?? 0 }}%">{{ $result->listening_score ?? '-' }}</div>
								</div>
							</div>

							<div class="mb-3">
								<label class="form-label">{{ __('messages.writing_score_label') }}</label>
								<div class="progress" style="height:18px">
									<div class="progress-bar bg-warning" role="progressbar" style="width: {{ $result->writing_score ?? 0 }}%">{{ $result->writing_score ?? '-' }}</div>
								</div>
							</div>

							<div class="mb-3">
								<label class="form-label">{{ __('messages.speaking_score_label') }}</label>
								<div class="progress" style="height:18px">
									<div class="progress-bar bg-secondary" role="progressbar" style="width: {{ $result->speaking_score ?? 0 }}%">{{ $result->speaking_score ?? '-' }}</div>
								</div>
							</div>

							@if($result->remarks)
							<div class="mt-4">
								<h6>{{ __('messages.remarks') }}</h6>
								<p class="small text-muted">{{ $result->remarks }}</p>
							</div>
							@endif

							<div class="mt-4 d-flex justify-content-between align-items-center">
								<div>
									<small class="text-muted">{{ __('messages.level_achieved') }}: <strong>{{ $result->level_achieved ?? '-' }}</strong></small>
								</div>
								<div>
									<small class="text-muted">{{ __('messages.date_published') }}: <strong>{{ $result->published_at ? $result->published_at->format('d/m/Y') : __('messages.pending') }}</strong></small>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
@endsection

