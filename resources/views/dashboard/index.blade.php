@extends('layouts.app')

@section('title', __('messages.student_dashboard_title'))

@section('content')
<div class="container py-5">
    <div class="row mb-5">
        <div class="col-md-3 mb-3">
            <div class="stat-card">
                <h3>{{ $statistics['totalRegistrations'] }}</h3>
                <p><i class="fas fa-clipboard-list"></i> {{ __('messages.total_registrations') }}</p>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="stat-card">
                <h3>{{ $statistics['confirmedRegistrations'] }}</h3>
                <p><i class="fas fa-check-circle"></i> {{ __('messages.confirmed') }}</p>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="stat-card">
                <h3>{{ $statistics['totalExamsTaken'] }}</h3>
                <p><i class="fas fa-book"></i> {{ __('messages.exams_taken') }}</p>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="stat-card">
                <h3>{{ $statistics['passedExams'] }}</h3>
                <p><i class="fas fa-trophy"></i> {{ __('messages.passed_exams') }}</p>
            </div>
        </div>
    </div>

    <!-- Upcoming Exams -->
    @if($upcomingExams->count() > 0)
    <div class="card mb-5">
        <div class="card-header">
            <h5 class="mb-0"><i class="fas fa-calendar-alt"></i> {{ __('messages.upcoming_exams') }}</h5>
        </div>
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead>
                    <tr>
                        <th>{{ __('messages.level') }}</th>
                        <th>{{ __('messages.date') }}</th>
                        <th>{{ __('messages.location') }}</th>
                        <th>{{ __('messages.action') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($upcomingExams as $exam)
                    <tr>
                        <td><strong>{{ $exam->name }}</strong></td>
                        <td>{{ $exam->date->format('d/m/Y H:i') }}</td>
                        <td>{{ $exam->location ?? __('messages.not_available_short') }}</td>
                        <td>
                            <a href="{{ route('exams.show', $exam) }}" class="btn btn-sm btn-primary">
                                <i class="fas fa-eye"></i> {{ __('messages.see_details') }}
                            </a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @endif

    <!-- My Registrations -->
    @if(auth()->user()->isStudent())
    @if(!empty($bankInfo['name']) || !empty($bankInfo['iban']) || !empty($bankInfo['account_number']))
    <div class="card mb-5">
        <div class="card-header">
            <h5 class="mb-0"><i class="fas fa-university"></i> {{ __('messages.bank_info') }}</h5>
        </div>
        <div class="card-body">
            <ul class="list-unstyled mb-0">
                <li><strong>{{ __('messages.bank_name') }}:</strong> {{ $bankInfo['name'] ?? __('messages.not_available_short') }}</li>
                <li><strong>{{ __('messages.bank_account_name') }}:</strong> {{ $bankInfo['account_name'] ?? __('messages.not_available_short') }}</li>
                <li><strong>{{ __('messages.bank_iban') }}:</strong> {{ $bankInfo['iban'] ?? __('messages.not_available_short') }}</li>
                <li><strong>{{ __('messages.bank_bic') }}:</strong> {{ $bankInfo['bic'] ?? __('messages.not_available_short') }}</li>
                <li><strong>{{ __('messages.bank_account_number') }}:</strong> {{ $bankInfo['account_number'] ?? __('messages.not_available_short') }}</li>
                <li><strong>{{ __('messages.bank_address') }}:</strong> {{ $bankInfo['address'] ?? __('messages.not_available_short') }}</li>
            </ul>
            @if(!empty($bankInfo['note']))
                <div class="mt-3 text-muted">{{ $bankInfo['note'] }}</div>
            @endif
        </div>
    </div>
    @endif
    @if($registrations->count() > 0)
    <div class="card mb-5">
        <div class="card-header">
            <h5 class="mb-0"><i class="fas fa-clipboard-list"></i> {{ __('messages.my_registrations') }}</h5>
        </div>
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead>
                    <tr>
                        <th>{{ __('messages.exam') }}</th>
                        <th>{{ __('messages.date') }}</th>
                        <th>{{ __('messages.status') }}</th>
                        <th>{{ __('messages.payment') }}</th>
                        <th>{{ __('messages.result') }}</th>
                        @if(auth()->user()->isAdmin())
                            <th>{{ __('messages.action') }}</th>
                        @endif
                    </tr>
                </thead>
                <tbody>
                    @foreach($registrations as $registration)
                    <tr>
                        <td>
                            <strong>{{ $registration->exam->name }}</strong><br>
                            <small class="text-muted">{{ $registration->exam->location ?? __('messages.not_available_short') }}</small>
                        </td>
                        <td>{{ $registration->exam->date->format('d/m/Y H:i') }}</td>
                        <td>
                            <span class="badge badge-status-{{ $registration->status }}">
                                {{ ucfirst($registration->status) }}
                            </span>
                        </td>
                        <td>
                            @if($registration->payment_status === 'paid')
                                <span class="badge bg-success">
                                    <i class="fas fa-check"></i> {{ __('messages.paid') }}
                                </span>
                            @else
                                <span class="badge bg-warning text-dark">
                                    <i class="fas fa-hourglass-end"></i> {{ __('messages.pending') }}
                                </span>
                            @endif
                        </td>
                        <td>
                            @if($registration->payment_status !== 'paid')
                                <span class="text-muted">{{ __('messages.payment_required') }}</span>
                            @elseif($registration->result && $registration->result->published_at)
                                <a href="{{ route('results.show', $registration->result) }}" class="btn btn-sm btn-primary">
                                    <i class="fas fa-eye"></i> {{ __('messages.view') }}
                                </a>
                            @elseif($registration->result && !$registration->result->published_at)
                                <span class="text-muted">{{ __('messages.results_not_published') }}</span>
                            @else
                                <span class="text-muted">{{ __('messages.no_result') }}</span>
                            @endif
                        </td>
                        @if(auth()->user()->isAdmin())
                        <td>
                            @if(in_array($registration->status, ['pending', 'confirmed'], true) && $registration->payment_status === 'unpaid')
                                <form action="{{ route('admin.registrations.confirm', $registration) }}" method="POST" style="display:inline;">
                                    @csrf
                                    <button type="button" class="btn btn-sm btn-warning confirm-action" data-confirm-message="{{ __('messages.confirm_payment') }}">
                                        <i class="fas fa-credit-card"></i> {{ __('messages.mark_paid') }}
                                    </button>
                                </form>
                            @endif

                            @if($registration->status !== 'cancelled' && $registration->status !== 'completed')
                                <form action="{{ route('admin.registrations.reject', $registration) }}" method="POST" style="display:inline;">
                                    @csrf
                                    <button type="button" class="btn btn-sm btn-danger confirm-action" data-confirm-message="{{ __('messages.are_you_sure') }}">
                                        <i class="fas fa-times"></i> {{ __('messages.cancel_btn') }}
                                    </button>
                                </form>
                            @endif
                        </td>
                        @endif
                    </tr>

                    
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @endif
    @endif

    <!-- My Results -->
    <div id="my-results">
    @if($results->count() > 0)
    <div class="card mb-5">
        <div class="card-header">
            <h5 class="mb-0"><i class="fas fa-trophy"></i> {{ __('messages.my_results') }}</h5>
        </div>
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead>
                    <tr>
                        <th>{{ __('messages.exam') }}</th>
                        <th>{{ __('messages.session') }}</th>
                        <th>{{ __('messages.modules') }}</th>
                        <th>{{ __('messages.date_published') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($results as $result)
                    <tr>
                        <td><strong><a href="{{ route('results.show', $result) }}">{{ $result->exam->name }}</a></strong></td>
                        <td>
                            {{ $result->exam?->date ? $result->exam->date->format('m/Y') : '-' }}
                        </td>
                        <td>
                            @php
                                $selectedModules = $result->registration?->selected_modules ?? [];
                                $labels = [
                                    'reading' => __('messages.module_reading'),
                                    'listening' => __('messages.module_listening'),
                                    'writing' => __('messages.module_writing'),
                                    'speaking' => __('messages.module_speaking'),
                                ];
                            @endphp
                            @if(empty($selectedModules))
                                <span class="badge bg-light text-dark">{{ __('messages.all_four_modules_included') }}</span>
                            @else
                                @foreach($selectedModules as $module)
                                    <span class="badge bg-light text-dark me-1">{{ $labels[$module] ?? $module }}</span>
                                @endforeach
                            @endif
                        </td>
                        <td>{{ $result->published_at ? $result->published_at->format('d/m/Y') : __('messages.pending') }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @endif
    </div>

    @if($registrations->count() === 0 && $results->count() === 0)
    <div class="alert alert-info text-center py-5">
        <i class="fas fa-info-circle fa-2x mb-3"></i>
        <h5>{{ __('messages.no_registrations') }}</h5>
        <p>{{ __('messages.not_registered_yet') }} <a href="{{ route('exams.index') }}">{{ __('messages.check_available_exams') }}</a></p>
    </div>
    @endif
</div>
@endsection
