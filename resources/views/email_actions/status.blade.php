@extends('layouts.app')

@section('content')
    <div class="container">
        @include('partials.success')
        @include('partials.error')

        <a href="{{ route('home') }}" class="btn btn-primary">{{ __('messages.go-home') }}</a>
    </div>
@endsection
