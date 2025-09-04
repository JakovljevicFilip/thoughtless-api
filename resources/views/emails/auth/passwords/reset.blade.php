<!doctype html>
<html>
<head>
    <style type="text/css">
        @media only screen and (max-width: 600px) {
            .email-card { width: 100% !important; border-radius: 12px !important; }
            .email-padding { padding: 20px 16px !important; }
            .email-logo { max-width: 100px !important; width: 35% !important; }
        }
    </style>
</head>
<body style="margin:0;padding:0;background:#e9ecef;">
<table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0" style="min-height:100vh;">
    <tr>
        <td align="center" valign="top" class="email-padding" style="padding:32px 16px;">
            <table role="presentation" cellspacing="0" cellpadding="0" border="0"
                   class="email-card"
                   style="width:100%;max-width:560px;background:#ffffff;border-radius:16px;">
                <tr>
                    <td align="center" style="padding:32px 32px 16px 32px;">
                        <img src="{{ $logo }}" alt="{{ $suite }} Logo"
                             class="email-logo"
                             style="display:block;border-radius:8px;max-width:180px;width:100%;height:auto;">
                    </td>
                </tr>

                <tr>
                    <td align="left" style="padding:16px 32px 0;">
                        <p style="font-family:Roboto, Arial, sans-serif;
                                  font-size:16px;line-height:24px;color:#374151;">
                            We've received a request to reset the password for your {{ $suite }} account.
                            If this was you, please click the button below to set a new password.
                        </p>
                    </td>
                </tr>

                <tr>
                    <td align="center" style="padding:24px 32px 28px;">
                        <a href="{{ $resetUrl }}"
                           style="background:#2563eb;color:#fff;text-decoration:none;
                           padding:14px 28px;border-radius:999px;display:inline-block;
                           font-family:Roboto, Arial, sans-serif;font-size:16px;">
                            Reset Password
                        </a>
                    </td>
                </tr>

                <tr>
                    <td align="left" style="padding:0 32px 28px;">
                        <p style="font-family:Roboto, Arial, sans-serif;
                                  font-size:16px;line-height:24px;color:#374151;">
                            This link will expire in {{ config('auth.passwords.users.expire') }} minutes.
                            If you didn’t request a password reset, you can safely ignore this message.
                        </p>
                    </td>
                </tr>
            </table>

            <div style="font-family:Roboto, Arial, sans-serif;
                        font-size:12px;color:#9ca3af;padding-top:16px;">
                © {{ date('Y') }} {{ $suite }} · Please do not reply to this email.
            </div>
        </td>
    </tr>
</table>
</body>
</html>
