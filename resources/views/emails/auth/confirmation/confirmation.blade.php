<!doctype html>
<html>
<head>
    <style type="text/css">
        /* Mobile styles */
        @media only screen and (max-width: 600px) {
            .email-card {
                width: 100% !important;
                border-radius: 12px !important;
            }
            .email-padding {
                padding: 20px 16px !important;
            }
            .email-logo {
                max-width: 100px !important;
                width: 35% !important;
            }
            .email-title {
                font-size: 24px !important;
                line-height: 32px !important;
            }
            .email-subtitle {
                font-size: 20px !important;
                line-height: 28px !important;
            }
        }
    </style>
</head>
<body style="margin:0;padding:0;background:#e9ecef;">
<table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0" style="min-height:100vh;">
    <tr>
        <td align="center" valign="top" class="email-padding" style="padding:32px 16px;">

            <!-- Card -->
            <table role="presentation" cellspacing="0" cellpadding="0" border="0"
                   class="email-card"
                   style="width:100%;max-width:560px;background:#ffffff;border-radius:16px;">
                <tr>
                    <td align="center" style="padding:32px 32px 16px 32px;">
                        <div class="email-title" style="font-family:Roboto, -apple-system, Helvetica, Arial, sans-serif;
                            font-size:28px;line-height:36px;font-weight:700;color:#111827;margin-bottom:8px;">
                            Welcome to
                        </div>
                        <div class="email-subtitle" style="font-family:Roboto, -apple-system, Helvetica, Arial, sans-serif;
                            font-size:24px;line-height:32px;font-weight:600;color:#111827;">
                            {{ $suite }}
                        </div>
                    </td>
                </tr>

                <tr>
                    <td align="center" style="padding:16px 32px;">
                        <img src="{{ $logo }}" alt="{{ $suite }} Logo"
                             class="email-logo"
                             style="display:block;border-radius:8px;max-width:180px;width:100%;height:auto;">
                    </td>
                </tr>

                <tr>
                    <td align="center" style="padding:16px 32px 0;">
                        <div style="font-family:Roboto, -apple-system, Helvetica, Arial, sans-serif;
                            font-size:24px;font-weight:700;color:#111827;">
                            Confirm Your Email
                        </div>
                    </td>
                </tr>

                <tr>
                    <td align="center" style="padding:12px 32px;">
                        <div style="font-family:Roboto, -apple-system, Helvetica, Arial, sans-serif;
                            font-size:16px;line-height:22px;color:#374151;">
                            Please click on the button below to validate your email address and confirm that you own this account.
                        </div>
                    </td>
                </tr>

                <tr>
                    <td align="center" style="padding:24px 32px 28px;">
                        <a href="{{ $verifyUrl }}"
                           style="background:#16a34a;color:#fff;text-decoration:none;
                           padding:14px 28px;border-radius:999px;display:inline-block;
                           font-family:Roboto, -apple-system, Helvetica, Arial, sans-serif;font-size:16px;">
                            Confirm Email
                        </a>
                    </td>
                </tr>

                <tr>
                    <td align="center" style="padding:0 32px 28px;">
                        <div style="font-family:Roboto, -apple-system, Helvetica, Arial, sans-serif;
                            font-size:14px;color:#6b7280;">
                            If you did not create an account, please disregard this email.
                        </div>
                    </td>
                </tr>
            </table>

            <!-- Footer -->
            <div style="font-family:Roboto, -apple-system, Helvetica, Arial, sans-serif;
                font-size:12px;color:#9ca3af;padding-top:16px;">
                © {{ date('Y') }} {{ $suite }} · Please do not reply to this email.
            </div>

        </td>
    </tr>
</table>
</body>
</html>
