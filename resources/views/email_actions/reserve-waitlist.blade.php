@extends('layouts.app')

@section('content')
    <div class="container">
        <form action="{{ route('waitlist.reserve.submit', $id) }}" method="POST">
            {{ csrf_field() }}
            <h2>{{ @__('messages.are-you-sure-you-want-to-make-the-reservation-for').' '.$yoyaku->schedule->class->title }}?</h2>
            <button class="btn btn-primary" type="submit">{{ __('messages.yes-make-reservation') }}</button>
            <a class="btn btn-danger" href="{{ route('login') }}">{{ __('messages.no') }}</a>
        </form>
    </div>
@endsection
