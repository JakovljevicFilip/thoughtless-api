@component('mail::message')
    # Welcome, {{ $user->first_name }}!

    Thanks for registering at **{{ config('app.suite_name') }}** 🎉
    Please confirm your email address by clicking the button below.

    @component('mail::button', ['url' => url('/verify?email=' . urlencode($user->email))])
        Confirm Email
    @endcomponent

    If you did not create an account, no further action is required.

    Thanks,<br>
    {{ config('app.suite_name') }}
@endcomponent
