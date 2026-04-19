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
                <th>Prüfungsstufe</th>
                <th>Titel Vor</th>
                <th>Vorname</th>
                <th>Familienname</th>
                <th>Titel Nach</th>
                <th>Geschlecht</th>
                <th>Geburts-Ort</th>
                <th>Geburts-Land</th>
                <th>Nationalität</th>
                <th>Geburts-Datum</th>
                <th>Prüfer/In Nr 1 schriftlich</th>
                <th>Prüfer/In Nr 2 schriftlich</th>
                <th>Prüfer/In Nr 1 mündlich</th>
                <th>Prüfer/In Nr 2 mündlich</th>
                <th>Modul Lesen</th>
                <th>Modul Hören</th>
                <th>Modul Schreiben</th>
                <th>Modul Sprechen</th>
            </tr>
        </thead>
        <tbody>
            @foreach($registrations as $registration)
            <tr>
                @php
                    $selectedModules = $registration->selected_modules ?? [];
                    $writtenExaminers = $examinerAssignments['written'] ?? [];
                    $oralExaminers = $examinerAssignments['oral'] ?? [];
                @endphp
                <td>{{ $registration->exam?->name }}</td>
                <td>{{ $registration->user?->title }}</td>
                <td>{{ $registration->user?->first_name }}</td>
                <td>{{ $registration->user?->last_name }}</td>
                <td></td>
                <td>{{ $registration->user?->sex }}</td>
                <td>{{ $registration->user?->birth_place }}</td>
                <td>{{ $registration->user?->birth_country }}</td>
                <td>{{ $registration->user?->nationality }}</td>
                <td>{{ $registration->user?->date_of_birth?->format('d/m/Y') }}</td>
                <td>{{ $writtenExaminers[0] ?? '' }}</td>
                <td>{{ $writtenExaminers[1] ?? '' }}</td>
                <td>{{ $oralExaminers[0] ?? '' }}</td>
                <td>{{ $oralExaminers[1] ?? '' }}</td>
                <td>{{ in_array('reading', $selectedModules, true) ? 'J' : '' }}</td>
                <td>{{ in_array('listening', $selectedModules, true) ? 'J' : '' }}</td>
                <td>{{ in_array('writing', $selectedModules, true) ? 'J' : '' }}</td>
                <td>{{ in_array('speaking', $selectedModules, true) ? 'J' : '' }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
