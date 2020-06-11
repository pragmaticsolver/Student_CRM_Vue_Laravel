@extends('layouts.app')

@section('content')
    <div class="justify-content-center">
        @include('partials.success')
        @include('partials.error')
        <div class="clearfix mb-2 mt-2">
            <h1 class="float-left">{{ __('messages.unitdetails') }}</h1>
            <a href="{{ url('/lesson/add?unit_id='.$unit->id.'&course_id='.$unit->course->id) }}" class="btn btn-success float-right">{{ __('messages.lessonadd') }}</a>
        </div>
        <table class="table table-striped table-bordered table-hover">
            <tbody>
                <tr>
                    <td>{{ __('messages.name') }}</td>
                    <td>{{ $unit->name }}</td>
                </tr>
                <tr>
                    <td>{{ __('messages.course') }}</td>
                    <td>{{ $unit->course->title }}</td>
                </tr>
                <tr>
                    <td>{{ __('messages.objectives') }}</td>
                    <td>{{ $unit->objectives }}</td>
                </tr>
            </tbody>
        </table>
    </div>
@endsection
