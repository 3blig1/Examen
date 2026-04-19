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
							@if($result->exam?->date)
								<div class="text-muted small">
									{{ __('messages.exam_session') }}: {{ $result->exam->date->format('m/Y') }}
								</div>
							@endif
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
							@php
								$selectedModules = $result->registration?->selected_modules ?? [];
								$showAllModules = empty($selectedModules);
							@endphp
							<div class="mb-3">
								<h5 class="mb-0">{{ __('messages.module_scores') }}</h5>
							</div>

							@if($showAllModules || in_array('reading', $selectedModules, true))
								<div class="mb-3">
									<label class="form-label d-flex justify-content-between align-items-center">
										<span>{{ __('messages.reading_score_label') }}</span>
										@if($result->reading_score !== null)
											<span class="badge {{ $result->reading_score >= 60 ? 'bg-success' : 'bg-danger' }}">
												{{ $result->reading_score >= 60 ? __('messages.passed') : __('messages.failed') }}
											</span>
										@endif
									</label>
									<div class="progress" style="height:18px">
										<div class="progress-bar bg-info" role="progressbar" style="width: {{ $result->reading_score ?? 0 }}%">{{ $result->reading_score ?? '-' }}</div>
									</div>
								</div>
							@endif

							@if($showAllModules || in_array('listening', $selectedModules, true))
								<div class="mb-3">
									<label class="form-label d-flex justify-content-between align-items-center">
										<span>{{ __('messages.listening_score_label') }}</span>
										@if($result->listening_score !== null)
											<span class="badge {{ $result->listening_score >= 60 ? 'bg-success' : 'bg-danger' }}">
												{{ $result->listening_score >= 60 ? __('messages.passed') : __('messages.failed') }}
											</span>
										@endif
									</label>
									<div class="progress" style="height:18px">
										<div class="progress-bar bg-primary" role="progressbar" style="width: {{ $result->listening_score ?? 0 }}%">{{ $result->listening_score ?? '-' }}</div>
									</div>
								</div>
							@endif

							@if($showAllModules || in_array('writing', $selectedModules, true))
								<div class="mb-3">
									<label class="form-label d-flex justify-content-between align-items-center">
										<span>{{ __('messages.writing_score_label') }}</span>
										@if($result->writing_score !== null)
											<span class="badge {{ $result->writing_score >= 60 ? 'bg-success' : 'bg-danger' }}">
												{{ $result->writing_score >= 60 ? __('messages.passed') : __('messages.failed') }}
											</span>
										@endif
									</label>
									<div class="progress" style="height:18px">
										<div class="progress-bar bg-warning" role="progressbar" style="width: {{ $result->writing_score ?? 0 }}%">{{ $result->writing_score ?? '-' }}</div>
									</div>
								</div>
							@endif

							@if($showAllModules || in_array('speaking', $selectedModules, true))
								<div class="mb-3">
									<label class="form-label d-flex justify-content-between align-items-center">
										<span>{{ __('messages.speaking_score_label') }}</span>
										@if($result->speaking_score !== null)
											<span class="badge {{ $result->speaking_score >= 60 ? 'bg-success' : 'bg-danger' }}">
												{{ $result->speaking_score >= 60 ? __('messages.passed') : __('messages.failed') }}
											</span>
										@endif
									</label>
									<div class="progress" style="height:18px">
										<div class="progress-bar bg-secondary" role="progressbar" style="width: {{ $result->speaking_score ?? 0 }}%">{{ $result->speaking_score ?? '-' }}</div>
									</div>
								</div>
							@endif

							@if($result->remarks)
							<div class="mt-4">
								<h6>{{ __('messages.remarks') }}</h6>
								<p class="small text-muted">{{ $result->remarks }}</p>
							</div>
							@endif

							<div class="mt-4">
								<small class="text-muted">{{ __('messages.date_published') }}: <strong>{{ $result->published_at ? $result->published_at->format('d/m/Y') : __('messages.pending') }}</strong></small>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
@endsection

