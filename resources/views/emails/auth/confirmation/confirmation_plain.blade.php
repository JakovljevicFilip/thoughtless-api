Confirm Your Email - {{ config('app.suite_name') }}

Please open the link to confirm your email:
{{ url('/verify?email=' . urlencode($user->email)) }}

If you did not create an account, please disregard this email.
