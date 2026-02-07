@extends('layouts.app')

@section('title', 'Tableau de bord Admin - ÖSD Exams')

@section('content')
<div class="hero" style="padding: 40px 0;">
    <div class="container">
        <h2><i class="fas fa-cog"></i> {{ __('messages.admin_panel') }}</h2>
        <p>{{ __('messages.complete_exam_management') }}</p>
    </div>
</div>

<div class="container py-5">
    <!-- Statistics -->
    <div class="row mb-5">
        <div class="col-md-3 mb-3">
            <div class="stat-card">
                <h3>{{ $statistics['totalStudents'] }}</h3>
                <p><i class="fas fa-users"></i> {{ __('messages.total_students') }}</p>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="stat-card">
                <h3>{{ $statistics['totalRegistrations'] }}</h3>
                <p><i class="fas fa-clipboard-list"></i> {{ __('messages.total_registrations') }}</p>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="stat-card">
                <h3>{{ $statistics['pendingRegistrations'] }}</h3>
                <p><i class="fas fa-hourglass-end"></i> {{ __('messages.pending_registrations') }}</p>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="stat-card">
                <h3>{{ $statistics['totalExams'] }}</h3>
                <p><i class="fas fa-book"></i> {{ __('messages.total_exams') }}</p>
            </div>
        </div>
    </div>

    <!-- Navigation Tabs -->
    <ul class="nav nav-tabs mb-5" role="tablist">
        <li class="nav-item" role="presentation">
            <button class="nav-link active" id="dashboard-tab" data-bs-toggle="tab" data-bs-target="#dashboard-content" type="button">
                <i class="fas fa-home"></i> {{ __('messages.dashboard_tab') }}
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="students-tab" data-bs-toggle="tab" data-bs-target="#students-content" type="button">
                <i class="fas fa-users"></i> {{ __('messages.students_tab') }}
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="exams-tab" data-bs-toggle="tab" data-bs-target="#exams-content" type="button">
                <i class="fas fa-book"></i> {{ __('messages.exams_tab') }}
            </button>
        </li>
    </ul>

    <!-- Tab Content -->
    <div class="tab-content">
        <!-- Dashboard Content -->
        <div class="tab-pane fade show active" id="dashboard-content">
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0"><i class="fas fa-list"></i> {{ __('messages.recent_registrations') }}</h5>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead>
                            <tr>
                                <th>{{ __('messages.student_name') }}</th>
                                <th>{{ __('messages.exam') }}</th>
                                <th>{{ __('messages.status') }}</th>
                                <th>{{ __('messages.payment') }}</th>
                                <th>{{ __('messages.date') }}</th>
                                <th>{{ __('messages.action') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($recentRegistrations as $registration)
                            <tr>
                                <td>
                                    <strong>{{ $registration->user->first_name }} {{ $registration->user->last_name }}</strong><br>
                                    <small class="text-muted">{{ $registration->user->email }}</small>
                                </td>
                                <td>{{ $registration->exam->name }}</td>
                                <td>
                                    <span class="badge badge-status-{{ $registration->status }}">
                                        {{ ucfirst($registration->status) }}
                                    </span>
                                </td>
                                <td>
                                    <span class="badge {{ $registration->payment_status === 'paid' ? 'bg-success' : 'bg-warning text-dark' }}">
                                        {{ ucfirst($registration->payment_status) }}
                                    </span>
                                </td>
                                <td>{{ $registration->created_at->format('d/m/Y') }}</td>
                                <td>
                                    @if($registration->status === 'pending')
                                    <form action="{{ route('admin.registrations.confirm', $registration) }}" method="POST" style="display:inline;">
                                        @csrf
                                        <button type="button" class="btn btn-sm btn-success confirm-action" onclick="return false;" data-confirm-message="{{ __('messages.confirm_payment') }}">
                                            <i class="fas fa-check"></i> {{ __('messages.confirm') }}
                                        </button>
                                    </form>
                                    @endif
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="6" class="text-center text-muted py-4">
                                    {{ __('messages.no_recent_registrations') }}
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Students Content -->
        <div class="tab-pane fade" id="students-content">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0"><i class="fas fa-users"></i> {{ __('messages.students_list') }}</h5>
                    <a href="{{ route('admin.students') }}" class="btn btn-sm btn-primary">
                        <i class="fas fa-eye"></i> {{ __('messages.view_all') }}
                    </a>
                </div>
            </div>
        </div>

        <!-- Exams Content -->
        <div class="tab-pane fade" id="exams-content">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0"><i class="fas fa-book"></i> {{ __('messages.exam_management') }}</h5>
                    <a href="{{ route('admin.exams.create') }}" class="btn btn-sm btn-primary">
                        <i class="fas fa-plus"></i> {{ __('messages.new_exam') }}
                    </a>
                </div>
                <div class="card-body">
                    <a href="{{ route('admin.exams') }}" class="btn btn-primary">
                        <i class="fas fa-list"></i> {{ __('messages.manage_all_exams') }}
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
