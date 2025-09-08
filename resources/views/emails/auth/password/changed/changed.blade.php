@extends('emails.layouts.email_layout')

@section('title') Password Changed @endsection
@section('heading') Did you recently change your password? @endsection

@section('body')
    Your password for {{ $suite }} was just changed.<br />
    If this wasn’t you, please open the app and reset it immediately.
@endsection

@section('note')
    To reset your password, pick the <strong>Forgot Password?</strong> option on the sign-in screen.
@endsection
