@extends('emails.layouts.email_layout')

@section('title') Password Reset Request @endsection

@section('heading') Did you forget your password? @endsection

@section('body')
    We've received a request to reset the password for your {{ $suite }} account.
    Click the button below to choose a new password.
@endsection

@section('cta_url') {{ $resetUrl }} @endsection
@section('cta_label') Reset Password @endsection

@section('note')
    This link will expire in {{ config('auth.passwords.users.expire') }} minutes.
    If you didn’t request a password reset, you can safely ignore this email.
@endsection
