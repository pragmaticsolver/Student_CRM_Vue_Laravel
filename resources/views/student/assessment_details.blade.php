@extends('layouts.app')

@section('content')
    <div class="row justify-content-center">
        @include('partials.success')
        @include('partials.error')
        <div class="col-12">
            @php
                $assessment = $assessment_user->assessment;
                $schedule = $assessment_user->schedule;
                $user_questions = $assessment_user->assessment_user_questions;
                $user = $user = $assessment_user->user;
            @endphp
            <h1>{{ __('messages.assessmentname', ['name' => $assessment->name]) }}</h1>
            <table class="table table-hover table-striped">
                <tbody>
                <tr>
                    <th>{{ __('messages.assessment-by') }}</th>
                    <td>
                        @if($user->student)
                           {{ $user->student->get_kanji_name() }}
                        @elseif($user->teacher)
                            {{ $user->teacher->nickname }}
                        @endif
                    </td>
                </tr>
                @if($assessment_user->assessment_for_student)
                    <tr>
                        <th>{{ __('messages.assessment-for') }}</th>
                        <td>{{ $assessment_user->assessment_for_student->get_kanji_name() }}</td>
                    </tr>
                @endif
                <tr>
                    <th>{{ __('messages.class') }}</th>
                    <td>{{ $schedule->class->title }}</td>
                </tr>
                <tr>
                    <th>{{ __('messages.classdate') }}</th>
                    <td>{{ $schedule->get_date() }}</td>
                </tr>
                </tbody>
            </table>

            @if($user_questions->count() > 0)
                @foreach($user_questions as $index => $user_question)
                    @php($question = $user_question->assessment_question)
                    <div class="form-group row">
                        <label class="col-lg-2 col-form-label">{{ $question->name }}</label>
                        <div class="col-lg-10">
                            @switch($question->type)
                                @case('rating')
                                    <fieldset class="rating">
                                    @for($star_index = 5; $star_index >= 1; $star_index--)
                                        <input
                                            type="radio" id="star{{ $star_index }}" name="question_{{ $question->id }}"
                                            value="{{ $star_index }}"
                                            @if($user_question->value == $star_index) checked @endif
                                            disabled
                                        />
                                        <label class="full" for="star{{ $star_index }}"></label>
                                    @endfor
                                    </fieldset>
                                @break
                                @case('option')
                                @php($option_values = json_decode($question->option_values))
                                @if($option_values)
                                    @foreach($option_values as $index => $option_value)
                                        <div class="form-check form-check-inline">
                                            <input
                                                name="question_{{ $question->id }}" class="form-check-input" type="radio"
                                                value="{{ $option_value }}" @if($user_question->value == $option_value) checked @endif
                                                disabled
                                            >
                                            <label class="form-check-label">{{ $option_value }}</label>
                                        </div>
                                    @endforeach
                                @endif
                                @break
                                @default
                                <textarea class="form-control" rows="7" name="question_{{ $question->id }}" readonly>{{ $user_question->value }}</textarea>
                            @endswitch
                        </div>
                    </div>
                @endforeach
            @endif
        </div>
    </div>
@endsection
