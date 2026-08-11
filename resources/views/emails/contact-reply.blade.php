<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>{{ $replySubject }}</title>
    <!--[if !mso]><!-->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:ital,opsz,wght@0,9..40,400;0,9..40,500;0,9..40,600;0,9..40,700;1,9..40,400&display=swap" rel="stylesheet">
    <!--<![endif]-->
    <style type="text/css">
        @media only screen and (max-width: 620px) {
            .email-shell { padding: 16px 10px !important; }
            .email-card { width: 100% !important; }
            .email-pad { padding-left: 20px !important; padding-right: 20px !important; }
            .email-body-pad { padding: 22px 20px !important; }
        }
    </style>
</head>
@php
    $font = "'DM Sans', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif";
@endphp
<body style="margin:0;padding:0;background:#f3f0eb;color:#2a2623;-webkit-text-size-adjust:100%;-ms-text-size-adjust:100%;">
    <div style="display:none;max-height:0;overflow:hidden;mso-hide:all;">
        A reply from HeartLovePics
    </div>
    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0" class="email-shell" style="background:#f3f0eb;padding:40px 16px;font-family:{{ $font }};">
        <tr>
            <td align="center">
                <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0" class="email-card" style="max-width:580px;background:#ffffff;border:1px solid #e6e0d7;border-radius:12px;overflow:hidden;box-shadow:0 8px 28px rgba(60, 48, 36, 0.06);">
                    {{-- Brand header --}}
                    <tr>
                        <td class="email-pad" style="padding:28px 32px 22px;background:linear-gradient(180deg, #fbf9f6 0%, #ffffff 100%);border-bottom:1px solid #efeae3;">
                            <p style="margin:0;font-family:{{ $font }};font-size:11px;font-weight:600;letter-spacing:0.12em;text-transform:uppercase;color:#a89888;">
                                HeartLovePics
                            </p>
                            <p style="margin:10px 0 0;font-family:{{ $font }};font-size:26px;font-weight:700;letter-spacing:-0.03em;line-height:1.2;color:#2a2623;">
                                HeartLove<span style="font-weight:500;color:#9a8b7c;">Pics</span>
                            </p>
                            <p style="margin:8px 0 0;font-family:{{ $font }};font-size:14px;font-weight:400;line-height:1.45;color:#8a7d72;">
                                A quiet place for photo stories
                            </p>
                        </td>
                    </tr>

                    {{-- Reply body --}}
                    <tr>
                        <td class="email-body-pad" style="padding:30px 32px 8px;font-family:{{ $font }};">
                            <p style="margin:0 0 10px;font-family:{{ $font }};font-size:12px;font-weight:600;letter-spacing:0.08em;text-transform:uppercase;color:#a89888;">
                                Message
                            </p>
                            <div style="font-family:{{ $font }};font-size:16px;font-weight:400;line-height:1.7;color:#2a2623;white-space:pre-wrap;">{{ $replyBody }}</div>
                        </td>
                    </tr>

                    {{-- Original message --}}
                    <tr>
                        <td class="email-body-pad" style="padding:24px 32px 30px;font-family:{{ $font }};">
                            <p style="margin:0 0 12px;font-family:{{ $font }};font-size:12px;font-weight:600;letter-spacing:0.08em;text-transform:uppercase;color:#a89888;">
                                Your original message
                            </p>
                            <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0" style="background:#f8f5f1;border:1px solid #ebe4db;border-radius:10px;">
                                <tr>
                                    <td style="padding:18px 20px;font-family:{{ $font }};">
                                        <p style="margin:0 0 8px;font-family:{{ $font }};font-size:15px;font-weight:600;letter-spacing:-0.01em;line-height:1.35;color:#2a2623;">
                                            {{ $contactMessage->subject }}
                                        </p>
                                        <p style="margin:0;font-family:{{ $font }};font-size:14px;font-weight:400;line-height:1.65;color:#5f564e;white-space:pre-wrap;">{{ $contactMessage->message }}</p>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>

                    {{-- Footer --}}
                    <tr>
                        <td class="email-pad" style="padding:18px 32px 24px;background:#fbf9f6;border-top:1px solid #efeae3;font-family:{{ $font }};">
                            <p style="margin:0;font-family:{{ $font }};font-size:12px;font-weight:400;line-height:1.6;color:#9a8b7c;">
                                Sent by <strong style="font-weight:600;color:#6e635a;">HeartLovePics</strong> in reply to your contact form message.
                                This address may not accept replies.
                            </p>
                            <p style="margin:10px 0 0;font-family:{{ $font }};font-size:12px;font-weight:500;color:#b0a396;">
                                <a href="{{ config('app.url') }}" style="color:#8b7e74;text-decoration:none;">{{ parse_url((string) config('app.url'), PHP_URL_HOST) ?: 'heartlovepics.com' }}</a>
                            </p>
                        </td>
                    </tr>
                </table>

                <p style="margin:20px 0 0;font-family:{{ $font }};font-size:11px;line-height:1.5;color:#b5a99c;text-align:center;">
                    © {{ date('Y') }} HeartLovePics
                </p>
            </td>
        </tr>
    </table>
</body>
</html>
