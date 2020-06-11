@extends('layouts.app')

@section('content')
    <div class="container">
        <form action="{{ route('waitlist.cancel.submit', $id) }}" method="POST">
            {{ csrf_field() }}
            <h2>{{ @__('messages.are-you-sure-you-want-to-leave-waitlist-for').' '.$yoyaku->schedule->class->title }}?</h2>
            <button class="btn btn-danger" type="submit">{{ __('messages.yes-leave-waitlist') }}</button>
            <a class="btn btn-secondary" href="{{ route('login') }}">{{ __('messages.no') }}</a>
        </form>
    </div>
@endsection
