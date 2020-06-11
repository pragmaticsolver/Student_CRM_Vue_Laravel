@extends('layouts.app')

@section('content')
    <div class="justify-content-center">
        @include('partials.success')
        @include('partials.error')
        <h1>{{ __('messages.papertestdetails') }}</h1>
        <table class="table table-hover">
            <thead>
            <tr>
                <th>{{ __('messages.name') }}</th>
                <th>{{ __('messages.course') }}</th>
                <th>{{ __('messages.unit') }}</th>
                <th>{{ __('messages.lesson') }}</th>
            </tr>
            </thead>
            <tbody>
            <tr>
                <td>{{ $paper_test->name }}</td>
                <td><a href="{{ url('/course/details/'.$paper_test->course->id) }}">{{ $paper_test->course->title }}</a></td>
                <td><a href="{{ url('/unit/details/'.$paper_test->unit->id) }}">{{ $paper_test->unit->name }}</a></td>
                <td><a href="{{ url('/lesson/details/'.$paper_test->lesson->id) }}">{{ $paper_test->lesson->title }}</td>
            </tr>
            </tbody>
        </table>
    </div>
@endsection
