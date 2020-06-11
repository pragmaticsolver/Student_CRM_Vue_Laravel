@extends('layouts.app')

@section('content')
    <div class="container">
        <form action="{{ route('cancel_reservation_submit') }}" method="POST">
            {{ csrf_field() }}
            <input type="hidden" name="id" value="{{ $id }}">
            <h2>{{ @__('messages.are-you-sure-you-want-to-cancel-the-reservation-for').' '.$yoyaku->schedule->class->title }}?</h2>
            <button class="btn btn-danger" type="submit">{{ __('messages.yes-cancel-reservation') }}</button>
            <a class="btn btn-secondary" href="{{ route('login') }}">{{ __('messages.no') }}</a>
        </form>
    </div>
@endsection
