<!doctype html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Informations de paiement</title>
</head>
<body style="margin:0;padding:0;background:#f6f7fb;font-family:Arial,Helvetica,sans-serif;color:#1f2937;">
    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="background:#f6f7fb;padding:24px;">
        <tr>
            <td align="center">
                <table role="presentation" width="640" cellpadding="0" cellspacing="0" style="background:#ffffff;border-radius:10px;overflow:hidden;box-shadow:0 2px 8px rgba(0,0,0,0.06);">
                    <tr>
                        <td style="padding:24px 32px;background:#0f172a;color:#ffffff;">
                            <p style="margin:0 0 6px 0;font-size:12px;letter-spacing:0.08em;text-transform:uppercase;opacity:0.8;">{{ config('app.name') }}</p>
                            <h1 style="margin:0;font-size:20px;font-weight:600;">Informations de paiement</h1>
                        </td>
                    </tr>
                    <tr>
                        <td style="padding:28px 32px;">
                            <p style="margin:0 0 12px 0;font-size:15px;">Bonjour {{ $user->first_name }} {{ $user->last_name }},</p>
                            <p style="margin:0 0 16px 0;font-size:15px;">Votre inscription est confirmée. Veuillez effectuer le paiement sur le compte ci-dessous.</p>

                            @if(!empty($exam))
                                <p style="margin:0 0 10px 0;font-size:15px;"><strong>Examen :</strong> {{ $exam->name }}</p>
                            @endif
                            @if(!empty($amount))
                                <p style="margin:0 0 16px 0;font-size:15px;"><strong>Montant à payer :</strong> {{ number_format($amount, 2, ',', ' ') }} €</p>
                            @endif

                            <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="margin:16px 0 20px 0;border:1px solid #e5e7eb;border-radius:8px;">
                                <tr>
                                    <td style="padding:12px 16px;font-size:14px;">
                                        <strong>Banque :</strong> {{ $bank['name'] ?? '—' }}
                                    </td>
                                </tr>
                                <tr>
                                    <td style="padding:12px 16px;font-size:14px;border-top:1px solid #e5e7eb;">
                                        <strong>Nom du compte :</strong> {{ $bank['account_name'] ?? '—' }}
                                    </td>
                                </tr>
                                <tr>
                                    <td style="padding:12px 16px;font-size:14px;border-top:1px solid #e5e7eb;">
                                        <strong>IBAN :</strong> {{ $bank['iban'] ?? '—' }}
                                    </td>
                                </tr>
                                <tr>
                                    <td style="padding:12px 16px;font-size:14px;border-top:1px solid #e5e7eb;">
                                        <strong>BIC / SWIFT :</strong> {{ $bank['bic'] ?? '—' }}
                                    </td>
                                </tr>
                                <tr>
                                    <td style="padding:12px 16px;font-size:14px;border-top:1px solid #e5e7eb;">
                                        <strong>Numéro de compte :</strong> {{ $bank['account_number'] ?? '—' }}
                                    </td>
                                </tr>
                                <tr>
                                    <td style="padding:12px 16px;font-size:14px;border-top:1px solid #e5e7eb;">
                                        <strong>Adresse :</strong> {{ $bank['address'] ?? '—' }}
                                    </td>
                                </tr>
                            </table>

                            @if(!empty($bank['note']))
                                <p style="margin:0 0 16px 0;font-size:14px;color:#6b7280;">{{ $bank['note'] }}</p>
                            @endif

                            <p style="margin:0;font-size:13px;color:#6b7280;">Merci de conserver la preuve de paiement.</p>
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
