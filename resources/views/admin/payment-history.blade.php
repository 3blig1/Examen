@extends('layouts.app')

@section('title', __('messages.payment_history') . ' - ÖSD Exams')

@section('content')
<div class="container py-5">
    <div class="row mb-4">
        <div class="col-12">
            <a href="{{ route('admin.dashboard') }}" class="btn btn-secondary mb-3">
                <i class="fas fa-arrow-left"></i> {{ __('messages.back_button') }}
            </a>
            <h2><i class="fas fa-receipt"></i> {{ __('messages.payment_history') }}</h2>
        </div>
    </div>

    <form method="GET" class="mb-4">
        <div class="row g-3">
            <div class="col-md-5">
                <label class="form-label">{{ __('messages.student_header') }}</label>
                <select name="user_id" class="form-select">
                    <option value="">{{ __('messages.all') }}</option>
                    @foreach($users as $user)
                        <option value="{{ $user->id }}" {{ request('user_id') == $user->id ? 'selected' : '' }}>
                            {{ $user->first_name }} {{ $user->last_name }} ({{ $user->email }})
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-5">
                <label class="form-label">{{ __('messages.exam') }}</label>
                <select name="exam_id" class="form-select">
                    <option value="">{{ __('messages.all') }}</option>
                    @foreach($exams as $exam)
                        <option value="{{ $exam->id }}" {{ request('exam_id') == $exam->id ? 'selected' : '' }}>
                            {{ $exam->name }} - {{ $exam->date->format('d/m/Y') }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2 d-flex align-items-end">
                <button class="btn btn-primary w-100" type="submit">{{ __('messages.filter') }}</button>
            </div>
        </div>
        <div class="mt-3">
            <a href="{{ route('admin.payments.history') }}" class="btn btn-outline-secondary btn-sm">{{ __('messages.reset_filters') }}</a>
        </div>
    </form>

    @if($totalsByExam->isNotEmpty())
    <div class="card mb-4">
        <div class="card-header">
            <strong>{{ __('messages.totals_by_exam') }}</strong>
        </div>
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead>
                    <tr>
                        <th>{{ __('messages.exam') }}</th>
                        <th>{{ __('messages.date') }}</th>
                        <th>{{ __('messages.total') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($totalsByExam as $total)
                    <tr>
                        <td>{{ $total->name }}</td>
                        <td>{{ \Carbon\Carbon::parse($total->date)->format('d/m/Y') }}</td>
                        <td>{{ number_format($total->total_amount, 0, ',', ' ') }} FCFA</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @endif

    <div class="card">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead>
                    <tr>
                        <th>{{ __('messages.student_header') }}</th>
                        <th>{{ __('messages.exam') }}</th>
                        <th>{{ __('messages.amount') }}</th>
                        <th>{{ __('messages.payment') }}</th>
                        <th>{{ __('messages.date') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($payments as $payment)
                    <tr>
                        <td>{{ $payment->user?->first_name }} {{ $payment->user?->last_name }}</td>
                        <td>{{ $payment->registration?->exam?->name ?? __('messages.not_available_short') }}</td>
                        <td>{{ number_format($payment->amount, 0, ',', ' ') }} FCFA</td>
                        <td>
                            @if($payment->status === 'confirmed')
                                {{ __('messages.confirmed') }}
                            @elseif($payment->status === 'reversed')
                                {{ __('messages.payment_reversed') }}
                            @else
                                {{ __('messages.pending') }}
                            @endif
                        </td>
                        <td>{{ $payment->paid_at?->format('d/m/Y H:i') ?? __('messages.not_available_short') }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="text-center text-muted py-4">
                            {{ __('messages.no_payments') }}
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    @if($payments->hasPages())
    <div class="d-flex justify-content-center mt-5">
        {{ $payments->links() }}
    </div>
    @endif
</div>
@endsection
