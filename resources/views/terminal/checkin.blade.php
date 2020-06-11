@extends('layouts.app')

@section('content')
    <h1>{{ __('messages.checkin')}}</h1>
    <div class="container text-center">
        @include('partials.error')

    </div>
@endsection
