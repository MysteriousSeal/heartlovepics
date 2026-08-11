<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $replySubject }}</title>
</head>
<body style="margin:0;padding:0;background:#f6f4f1;color:#2c2825;font-family:Georgia,'Times New Roman',serif;">
    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="background:#f6f4f1;padding:24px 12px;">
        <tr>
            <td align="center">
                <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="max-width:560px;background:#ffffff;border:1px solid #e4dfd8;">
                    <tr>
                        <td style="padding:20px 24px;border-bottom:1px solid #e4dfd8;">
                            <p style="margin:0;font-size:18px;font-weight:600;letter-spacing:-0.02em;">
                                HeartLove<span style="font-weight:400;color:#8b7e74;">Pics</span>
                            </p>
                        </td>
                    </tr>
                    <tr>
                        <td style="padding:24px;">
                            <p style="margin:0 0 16px;font-size:15px;line-height:1.65;white-space:pre-wrap;">{{ $replyBody }}</p>

                            <p style="margin:28px 0 8px;font-size:12px;text-transform:uppercase;letter-spacing:0.05em;color:#8b7e74;">
                                Your original message
                            </p>
                            <div style="padding:14px 16px;background:#f6f4f1;border:1px solid #e4dfd8;">
                                <p style="margin:0 0 6px;font-size:13px;font-weight:600;">{{ $contactMessage->subject }}</p>
                                <p style="margin:0;font-size:13px;line-height:1.55;color:#5c5650;white-space:pre-wrap;">{{ $contactMessage->message }}</p>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td style="padding:16px 24px;border-top:1px solid #e4dfd8;font-size:12px;line-height:1.5;color:#8b7e74;">
                            This email was sent by HeartLovePics in reply to your contact form message.
                            Please do not reply directly to this address if it is marked as noreply.
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>
