@extends('emails.layouts.email_layout')

@section('title') Welcome to @endsection

@section('heading') Confirm Your Email @endsection

@section('body')
    Please click on the button below to validate your email address and confirm that you own this account.
@endsection

@section('cta_url') {{ $verifyUrl }} @endsection
@section('cta_label') Confirm Email @endsection

@section('note')
    If you did not create an account, please disregard this email.
@endsection
