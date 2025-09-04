Hey {{ $firstName }}, did you forget your password? - {{ $suite }}

We've received a request to reset the password for your {{ $suite }} account.
If this was you, please use the link below to choose a new password:

{{ $resetUrl }}

This link will expire in {{ config('auth.passwords.users.expire') }} minutes.

If you didn’t request a password reset, you can safely ignore this message.
