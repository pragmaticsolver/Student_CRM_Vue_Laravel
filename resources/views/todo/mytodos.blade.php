@extends('layouts.app')

@section('content')
    <div class="row justify-content-center">
        <div class="col-12 mt-2">
            <div class="row">
                <div class="col-6">
                    <h1>{{ __('messages.mytodos') }}</h1>
                </div>
            </div>
        </div>
        <div class="col-12">
            @include('partials.success')
            @include('partials.error')
            @include('todo.list-todo')
        </div>
    </div>
@endsection
