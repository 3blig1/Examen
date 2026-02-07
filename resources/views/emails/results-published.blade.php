<!doctype html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Résultats publiés</title>
</head>
<body style="margin:0;padding:0;background:#f6f7fb;font-family:Arial,Helvetica,sans-serif;color:#1f2937;">
    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="background:#f6f7fb;padding:24px;">
        <tr>
            <td align="center">
                <table role="presentation" width="640" cellpadding="0" cellspacing="0" style="background:#ffffff;border-radius:10px;overflow:hidden;box-shadow:0 2px 8px rgba(0,0,0,0.06);">
                    <tr>
                        <td style="padding:24px 32px;background:#0f172a;color:#ffffff;">
                            <p style="margin:0 0 6px 0;font-size:12px;letter-spacing:0.08em;text-transform:uppercase;opacity:0.8;">{{ config('app.name') }}</p>
                            <h1 style="margin:0;font-size:20px;font-weight:600;">Résultats publiés</h1>
                        </td>
                    </tr>
                    <tr>
                        <td style="padding:28px 32px;">
                            <p style="margin:0 0 12px 0;font-size:15px;">Bonjour {{ $user->first_name }} {{ $user->last_name }},</p>
                            <p style="margin:0 0 16px 0;font-size:15px;">Les résultats de l'examen <strong>{{ $exam->name }}</strong> sont maintenant disponibles.</p>
                            <p style="margin:0 0 16px 0;font-size:15px;">Connectez-vous à votre espace étudiant pour consulter votre note et votre niveau.</p>
                            <table role="presentation" cellpadding="0" cellspacing="0" style="margin:8px 0 20px 0;">
                                <tr>
                                    <td bgcolor="#2563eb" style="border-radius:6px;">
                                        <a href="{{ rtrim(config('app.url'), '/') . '/login' }}" style="display:inline-block;padding:10px 16px;font-size:14px;color:#ffffff;text-decoration:none;font-weight:600;">Voir mes résultats</a>
                                    </td>
                                </tr>
                            </table>
                            <p style="margin:0;font-size:13px;color:#6b7280;">Si vous avez des questions, contactez-nous en répondant à cet email.</p>
                        </td>
                    </tr>
                    <tr>
                        <td style="padding:16px 32px;background:#f8fafc;font-size:12px;color:#6b7280;">
                            Merci, <br>Équipe examens
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>
