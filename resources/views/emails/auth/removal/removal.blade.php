@extends('emails.layouts.email_layout')

@section('title') We are sad to see you go :( @endsection
@section('heading') You requested the account removal @endsection

@section('body')
    Your {{ $suite }} account and all related data will be removed in the next {{ $hours }} hours.<br />
    If you changed your mind, you can cancel below.
@endsection

@section('cta_url') {{ $cancelUrl }} @endsection
@section('cta_label') Cancel Account Deletion @endsection

@section('note')
    This link expires in {{ $hours }} hours. If you did not request this, no action is required.
@endsection
