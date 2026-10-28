<!doctype html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ __('messages.email_payment_instructions_title') }}</title>
</head>
<body style="margin:0;padding:0;background:#f6f7fb;font-family:Arial,Helvetica,sans-serif;color:#1f2937;">
    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="background:#f6f7fb;padding:24px;">
        <tr>
            <td align="center">
                <table role="presentation" width="640" cellpadding="0" cellspacing="0" style="background:#ffffff;border-radius:10px;overflow:hidden;box-shadow:0 2px 8px rgba(0,0,0,0.06);">
                    <tr>
                        <td style="padding:24px 32px;background:#0f172a;color:#ffffff;">
                            <p style="margin:0 0 6px 0;font-size:12px;letter-spacing:0.08em;text-transform:uppercase;opacity:0.8;">{{ config('app.name') }}</p>
                            <h1 style="margin:0;font-size:20px;font-weight:600;">{{ __('messages.email_payment_instructions_heading') }}</h1>
                        </td>
                    </tr>
                    <tr>
                        <td style="padding:28px 32px;">
                            <p style="margin:0 0 12px 0;font-size:15px;">{{ __('messages.email_greeting', ['first' => $user->first_name, 'last' => $user->last_name]) }}</p>
                            <p style="margin:0 0 16px 0;font-size:15px;">{{ __('messages.email_payment_instructions_body') }}</p>

                            @if(!empty($exam))
                                <p style="margin:0 0 10px 0;font-size:15px;"><strong>{{ __('messages.email_exam_label') }}:</strong> {{ $exam->name }}</p>
                            @endif
                            @if(!empty($amount))
                                <p style="margin:0 0 16px 0;font-size:15px;"><strong>{{ __('messages.email_amount_due_label') }}:</strong> {{ number_format($amount, 2, ',', ' ') }} €</p>
                            @endif

                            @if(count($selectedModules) > 0)
                                @php
                                    $moduleLabels = [
                                        'reading' => __('messages.module_reading'),
                                        'listening' => __('messages.module_listening'),
                                        'writing' => __('messages.module_writing'),
                                        'speaking' => __('messages.module_speaking'),
                                    ];
                                @endphp
                                <p style="margin:0 0 8px 0;font-size:15px;"><strong>{{ __('messages.email_selected_modules_label') }}:</strong></p>
                                <ul style="margin:0 0 16px 20px;padding:0;font-size:14px;">
                                    @foreach($selectedModules as $module)
                                        <li>{{ $moduleLabels[$module] ?? $module }}</li>
                                    @endforeach
                                </ul>
                            @endif

                            <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="margin:16px 0 20px 0;border:1px solid #e5e7eb;border-radius:8px;">
                                <tr>
                                    <td style="padding:12px 16px;font-size:14px;">
                                        <strong>{{ __('messages.bank_name') }}:</strong> {{ $bank['name'] ?? __('messages.not_available_short') }}
                                    </td>
                                </tr>
                                <tr>
                                    <td style="padding:12px 16px;font-size:14px;border-top:1px solid #e5e7eb;">
                                        <strong>{{ __('messages.bank_account_name') }}:</strong> {{ $bank['account_name'] ?? __('messages.not_available_short') }}
                                    </td>
                                </tr>
                                <tr>
                                    <td style="padding:12px 16px;font-size:14px;border-top:1px solid #e5e7eb;">
                                        <strong>{{ __('messages.bank_iban') }}:</strong> {{ $bank['iban'] ?? __('messages.not_available_short') }}
                                    </td>
                                </tr>
                                @if(!empty($bank['bic']))
                                    <tr>
                                        <td style="padding:12px 16px;font-size:14px;border-top:1px solid #e5e7eb;">
                                            <strong>{{ __('messages.bank_bic') }}:</strong> {{ $bank['bic'] }}
                                        </td>
                                    </tr>
                                @endif
                                <tr>
                                    <td style="padding:12px 16px;font-size:14px;border-top:1px solid #e5e7eb;">
                                        <strong>{{ __('messages.bank_account_number') }}:</strong> {{ $bank['account_number'] ?? __('messages.not_available_short') }}
                                    </td>
                                </tr>
                                @if(!empty($bank['address']))
                                    <tr>
                                        <td style="padding:12px 16px;font-size:14px;border-top:1px solid #e5e7eb;">
                                            <strong>{{ __('messages.bank_address') }}:</strong> {{ $bank['address'] }}
                                        </td>
                                    </tr>
                                @endif
                            </table>

                            @if(!empty($bank['note']))
                                <p style="margin:0 0 16px 0;font-size:14px;color:#6b7280;">{{ $bank['note'] }}</p>
                            @endif

                            <p style="margin:0 0 6px 0;font-size:13px;color:#6b7280;">{{ __('messages.email_payment_proof_note') }}</p>
                            <p style="margin:0;font-size:13px;color:#6b7280;">
                                <a href="mailto:info@examenosd.cial-de.com" style="color:#2563eb;">info@examenosd.cial-de.com</a>
                            </p>
                        </td>
                    </tr>
                    <tr>
                        <td style="padding:16px 32px;background:#f8fafc;font-size:12px;color:#6b7280;">
                            {!! __('messages.email_signature') !!}
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>
