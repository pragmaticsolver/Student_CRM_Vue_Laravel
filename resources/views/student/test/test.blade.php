@extends('layouts.app')

@section('content')
    <div class="row justify-content-center">
        @include('partials.success')
        @include('partials.error')
        <div class="col-12">
            <form method="POST" action="{{ route('student.online_test.store_result', $student_test->id) }}" class="submit-test">
                @csrf

                <div class="text-center">
                    <h1>{{ __('messages.testname', ['name' => $test->name]) }}</h1>
                    <table class="table table-hover table-striped">
                        <tbody>
                            <tr>
                                <th>{{ __('messages.student') }}</th>
                                <td>{{ $student->get_kanji_name() }}</td>
                            </tr>
                            <tr>
                                <th>{{ __('messages.class') }}</th>
                                <td>{{ $schedule->class->title }}</td>
                            </tr>
                            <tr>
                                <th>{{ __('messages.numberofquestions') }}</th>
                                <td>{{ $test->questions->count() }}</td>
                            </tr>
                        </tbody>
                    </table>

                    @if($test->questions->count() > 0)
                        @foreach($test->questions as $index => $question)
                            <div class="question-wrapper">
                                <div class="question">
                                    {{ __('messages.questionnumber', ['number' => $index]) }}: {{ $question->question }}
                                </div>
                                <div class="answer-wrapper">
                                    @if($question->answers->count() > 0)
                                        @foreach($question->answers()->orderBy('order')->get() as $index => $answer)
                                            <div class="answer">
                                                <input
                                                    name="question_{{ $question->id }}" type="radio" value="{{ $answer->id }}"
                                                    @if($index == 0) checked @endif
                                                >{{ $answer->answer }}
                                            </div>
                                        @endforeach
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    @endif

                    <div class="form-group row">
                        <div class="col-lg-6 m-auto">
                            <input name="add" type="submit" value="{{ __('messages.submit') }}" class="form-control btn-success">
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection
