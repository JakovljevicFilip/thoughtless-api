@extends('emails.layouts.email_layout')

@section('title') We are sad to see you go :( @endsection
@section('heading') You requested the account removal @endsection

@section('body')
    Your {{ $suite }} account and all related data will be removed in the next {{ $hours }} hours. <br />
    To cancel this action before the action completes.
@endsection

@section('note')
    Thank you for giving us a chance!
@endsection
