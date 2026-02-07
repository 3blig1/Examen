<!doctype html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <title>{{ __('messages.registrations_for_exam') }}</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 12px; }
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid #ddd; padding: 6px; }
        th { background: #f2f2f2; }
    </style>
</head>
<body>
    <h2>{{ __('messages.registrations_for_exam') }} - {{ $exam->name }}</h2>
    <p>{{ $exam->date->format('d/m/Y H:i') }}</p>

    <table>
        <thead>
            <tr>
                <th>{{ __('messages.student_header') }}</th>
                <th>{{ __('messages.email_header') }}</th>
                <th>{{ __('messages.country_header') }}</th>
                <th>{{ __('messages.status') }}</th>
                <th>{{ __('messages.payment') }}</th>
                <th>{{ __('messages.registration_date') }}</th>
            </tr>
        </thead>
        <tbody>
            @foreach($registrations as $registration)
            <tr>
                <td>{{ $registration->user->first_name }} {{ $registration->user->last_name }}</td>
                <td>{{ $registration->user->email }}</td>
                <td>{{ $registration->user->country }}</td>
                <td>{{ $registration->status }}</td>
                <td>{{ $registration->payment_status }}</td>
                <td>{{ $registration->created_at?->format('d/m/Y H:i') }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
