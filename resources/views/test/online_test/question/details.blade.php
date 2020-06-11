@extends('layouts.app')

@section('content')
    <div class="justify-content-center">
        @include('partials.success')
        @include('partials.error')
        <h1>{{ __('messages.questiondetails') }}</h1>
        <table class="table table-hover">
            <thead>
            <tr>
                <th>{{ __('messages.question') }}</th>
                <th>{{ __('messages.test') }}</th>
                <th>{{ __('messages.score') }}</th>
                <th>{{ __('messages.numberofanswers') }}</th>
            </tr>
            </thead>
            <tbody>
            <tr>
                <td>{{ $question->question }}</td>
                <td><a href="{{ route('test.show', $question->test->id) }}">{{ $question->test->name }}</a></td>
                <td>{{ $question->score }}</td>
                <td>{{ $question->answers->count() }}</td>
            </tr>
            </tbody>
        </table>
        <br>
        <div class="clearfix">
            <h1 class="float-left">{{ __('messages.answers') }}</h1>
            <a href="{{ url('/answer/add?question_id='.$question->id.'&test_id='.$question->test->id) }}" class="btn btn-success float-right">
                {{ __('messages.addanswer') }}
            </a>
        </div>
        @if($question->answers->count() > 0)
            <table class="table table-hover">
                <thead>
                <tr>
                    <th>{{ __('messages.answer') }}</th>
                    <th>{{ __('messages.question') }}</th>
                    <th>{{ __('messages.order') }}</th>
                    <th>{{ __('messages.correct') }}</th>
                    <th>{{ __('messages.edit') }}</th>
                    <th>{{ __('messages.delete') }}</th>
                </tr>
                </thead>
                <tbody>
                @foreach($question->answers as $answer)
                    <tr>
                        <td>{{ $answer->answer }}</td>
                        <td>{{ $answer->question->question }}</a></td>
                        <td>{{ $answer->order }}</td>
                        <td>{!! $answer->correct ? '<i class="fa fa-check-circle"></i>' : '<i class="fa fa-times-circle"></i>' !!}</td>
                        <td><a href="{{ url('/answer/edit/'.$answer->id) }}" class="btn btn-success">{{ __('messages.edit') }}</a></td>
                        <td>
                            <form class="delete" method="POST" action="{{ route('answer.destroy', $answer->id) }}">
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
