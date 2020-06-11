@extends('layouts.app')

@section('content')
    <div class="justify-content-center">
        @include('partials.success')
        @include('partials.error')
        <h1>{{ __('messages.testdetails') }}</h1>
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
                <td>{{ $test->name }}</td>
                <td><a href="{{ url('/course/details/'.$test->course->id) }}">{{ $test->course->title }}</a></td>
                <td><a href="{{ url('/unit/details/'.$test->unit->id) }}">{{ $test->unit->name }}</a></td>
                <td><a href="{{ url('/lesson/details/'.$test->lesson->id) }}">{{ $test->lesson->title }}</td>
            </tr>
            </tbody>
        </table>
        <br>
        <div class="clearfix">
            <h1 class="float-left">{{ __('messages.questions') }}</h1>
            <a href="{{ url('/question/add?test_id='.$test->id) }}" class="btn btn-success float-right">{{ __('messages.addquestion') }}</a>
        </div>
        @if($test->questions->count() > 0)
        <table class="table table-hover">
            <thead>
            <tr>
                <th>{{ __('messages.question') }}</th>
                <th>{{ __('messages.score') }}</th>
                <th>{{ __('messages.numberofanswers') }}</th>
                <th>{{ __('messages.addanswer') }}</th>
                <th>{{ __('messages.edit') }}</th>
                <th>{{ __('messages.delete') }}</th>
            </tr>
            </thead>
            <tbody>
            @foreach($test->questions as $question)
                <tr>
                    <td><a href="{{ url('/question/details/'.$question->id) }}">{{ $question->question }}</a></td>
                    <td>{{ $question->score }}</td>
                    <td>{{ $question->answers->count() }}</td>
                    <td><a href="{{ url('/answer/add?test_id='.$test->id.'&question_id='.$question->id) }}" class="btn btn-success">{{ __('messages.addanswer') }}</a></td>
                    <td><a href="{{ url('/question/edit/'.$question->id) }}" class="btn btn-success">{{ __('messages.edit') }}</a></td>
                    <td>
                        <form class="delete" method="POST" action="{{ route('question.destroy', $question->id) }}">
                            @csrf
                            @method('DELETE')
                            <button class="btn btn-danger" type="submit">{{ __('messages.delete') }}</button>
                        </form>
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
        @endif
    </div>
@endsection
