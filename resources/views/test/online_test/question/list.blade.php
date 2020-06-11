@extends('layouts.app')

@section('content')
    <div class="row justify-content-center">
        @include('partials.success')
        @include('partials.error')
        <div class="col-12">
            <h1>{{ __('messages.questionlist') }}</h1>
            <table class="table table-hover data-table order-column">
                @if($questions->count() > 0)
                    <thead>
                    <tr>
                        <th>{{ __('messages.question') }}</th>
                        <th>{{ __('messages.score') }}</th>
                        <th>{{ __('messages.test') }}</th>
                        <th>{{ __('messages.numberofanswers') }}</th>
                        <th>{{ __('messages.addanswer') }}</th>
                        <th>{{ __('messages.edit') }}</th>
                        <th>{{ __('messages.delete') }}</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($questions as $question)
                        <tr>
                            <td><a href="{{ url('/question/details/'.$question->id) }}">{{ $question->question }}</a></td>
                            <td>{{ $question->score }}</td>
                            <td><a href="{{ url('/test/details/'.$question->test_id) }}">{{ $question->test->name }}</a></td>
                            <td>{{ $question->answers->count() }}</td>
                            <td><a href="{{ url('/answer/add?question_id='.$question->id.'&test_id='.$question->test_id) }}" class="btn btn-success">{{ __('messages.addanswer') }}</a></td>
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
                @endif
            </table>
        </div>
    </div>
@endsection
