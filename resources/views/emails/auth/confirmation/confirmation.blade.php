@php
    $suite = config('app.suite_name');
    $verifyUrl = url('/verify?email=' . urlencode($user->email));
    $placeholder = 'https://via.placeholder.com/240x140?text=Image';
@endphp
    <!doctype html>
<html>
<body style="margin:0;padding:0;background:#e9ecef;">
<!-- Wrapper -->
<table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0" style="background:#e9ecef;">
    <tr>
        <td align="center">

            <!-- Top green band with suite name -->
            <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0" style="background:#16a34a;">
                <tr>
                    <td align="center" style="padding:28px 16px;">
                        <div style="font-family:Arial,Helvetica,sans-serif;font-size:24px;line-height:28px;color:#ffffff;font-weight:700;">
                            {{ $suite }}
                        </div>
                    </td>
                </tr>
            </table>

            <!-- Content area (light gray bg) -->
            <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0" style="background:#e9ecef;">
                <tr>
                    <td align="center" style="padding:32px 16px 48px 16px;">

                        <!-- Card -->
                        <table role="presentation" width="560" cellspacing="0" cellpadding="0" border="0" style="max-width:560px;background:#ffffff;border-radius:16px;">
                            <tr>
                                <td align="center" style="padding:32px 32px 8px 32px;">
                                    <img src="{{ $placeholder }}" width="180" height="auto" alt="Placeholder image"
                                         style="display:block;border:0;outline:none;text-decoration:none;border-radius:8px;">
                                </td>
                            </tr>

                            <tr>
                                <td align="center" style="padding:16px 32px 0 32px;">
                                    <div style="font-family:Arial,Helvetica,sans-serif;font-size:28px;line-height:34px;font-weight:700;color:#111827;">
                                        Confirm Your Email
                                    </div>
                                </td>
                            </tr>

                            <tr>
                                <td align="center" style="padding:12px 32px 0 32px;">
                                    <div style="font-family:Arial,Helvetica,sans-serif;font-size:16px;line-height:24px;color:#374151;">
                                        Please click on the button below to validate your email address and confirm that you own this account.
                                    </div>
                                </td>
                            </tr>

                            <!-- Button -->
                            <tr>
                                <td align="center" style="padding:24px 32px 28px 32px;">
                                    <table role="presentation" cellspacing="0" cellpadding="0" border="0">
                                        <tr>
                                            <td align="center" bgcolor="#16a34a" style="border-radius:999px;">
                                                <a href="{{ $verifyUrl }}"
                                                   style="display:inline-block;font-family:Arial,Helvetica,sans-serif;font-size:16px;line-height:20px;
                               color:#ffffff;text-decoration:none;padding:14px 28px;border-radius:999px;">
                                                    Confirm Email
                                                </a>
                                            </td>
                                        </tr>
                                    </table>
                                </td>
                            </tr>

                            <tr>
                                <td align="center" style="padding:0 32px 28px 32px;">
                                    <div style="font-family:Arial,Helvetica,sans-serif;font-size:14px;line-height:22px;color:#6b7280;">
                                        If you did not create an account, please disregard this email.
                                    </div>
                                </td>
                            </tr>
                        </table>
                        <!-- /Card -->

                        <!-- Footer -->
                        <table role="presentation" width="560" cellspacing="0" cellpadding="0" border="0" style="max-width:560px;">
                            <tr>
                                <td align="center" style="padding:24px 16px 0 16px;">
                                    <div style="font-family:Arial,Helvetica,sans-serif;font-size:12px;line-height:18px;color:#9ca3af;">
                                        © {{ date('Y') }} {{ $suite }} · Please do not reply to this email.
                                    </div>
                                </td>
                            </tr>
                        </table>
                        <!-- /Footer -->

                    </td>
                </tr>
            </table>

        </td>
    </tr>
</table>
</body>
</html>
