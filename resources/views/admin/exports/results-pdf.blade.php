<!doctype html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="utf-8">
    <title>{{ __('messages.results_for_exam') }}</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 12px; }
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid #ddd; padding: 6px; }
        th { background: #f2f2f2; }
    </style>
</head>
<body>
    <h2>{{ __('messages.results_for_exam') }} - {{ $exam->name }}</h2>
    <p>{{ $exam->date->format('d/m/Y H:i') }}</p>

    <table>
        <thead>
            <tr>
                <th>{{ __('messages.student_header') }}</th>
                <th>{{ __('messages.email_header') }}</th>
                <th>{{ __('messages.level_achieved') }}</th>
                <th>{{ __('messages.score') }}</th>
                <th>{{ __('messages.status') }}</th>
                <th>{{ __('messages.date_published') }}</th>
            </tr>
        </thead>
        <tbody>
            @foreach($registrations as $registration)
            <tr>
                <td>{{ $registration->user->first_name }} {{ $registration->user->last_name }}</td>
                <td>{{ $registration->user->email }}</td>
                <td>{{ $registration->result?->level_achieved ?? __('messages.not_available_short') }}</td>
                <td>{{ $registration->result?->total_score ?? __('messages.not_available_short') }}</td>
                <td>
                    @if($registration->result)
                        {{ $registration->result->isPassed() ? __('messages.passed') : __('messages.failed') }}
                    @else
                        {{ __('messages.pending') }}
                    @endif
                </td>
                <td>{{ $registration->result?->published_at?->format('d/m/Y H:i') ?? '' }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
