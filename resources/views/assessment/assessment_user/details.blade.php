@extends('layouts.app')

@section('content')
    <div class="row justify-content-center">
        @include('partials.success')
        @include('partials.error')
        <div class="col-12" id="vue-app">
            @php
                $assessment = $assessment_user->assessment;
                $assessment_questions = $assessment->assessment_questions;
                $schedule = $assessment_user->schedule;
                $user = $assessment_user->user;
            @endphp
            <h1>{{ __('messages.assessmentname', ['name' => $assessment->name]) }}</h1>
            <table class="table table-hover table-striped">
                <tbody>
                @if($assessment->description)
                    <tr>
                        <th width="16%">{{ __('messages.description') }}</th>
                        <td>{{ $assessment->description }}</td>
                    </tr>
                @endif
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
                @if($schedule)
                    <tr>
                        <th>{{ __('messages.class') }}</th>
                        <td>{{ $schedule->class->title }}</td>
                    </tr>
                    <tr>
                        <th>{{ __('messages.classdate') }}</th>
                        <td>{{ $schedule->get_date() }}</td>
                    </tr>
                @endif
                </tbody>
            </table>

            @foreach($assessment_questions as $index => $question)
                @php
                    $user_answer = $question->getUserAnswer($assessment_user->id);
                @endphp
                <div class="form-group row">
                    <label class="col-lg-2 col-form-label">{{ $question->name }}: {{ $question->is_required ? '*' : ''}}</label>
                    <div class="col-lg-10">
                        @switch($question->type)
                            @case('rating')
                                <fieldset class="rating">
                                    @for($star_index = 5; $star_index >= 1; $star_index--)
                                        <input
                                            type="radio" id="star{{ $star_index }}" name="question_{{ $question->id }}"
                                            value="{{ $star_index }}"
                                            @if($user_answer == $star_index) checked @endif
                                            disabled
                                        />
                                        <label class="full" for="star{{ $star_index }}"></label>
                                    @endfor
                                </fieldset>
                                <div class="clearfix"></div>
                            @break
                            @case('option')
                                @php($option_values = json_decode($question->option_values))
                                @if($option_values)
                                    @foreach($option_values as $index => $option_value)
                                        <div class="form-check form-check-inline">
                                            <input
                                                name="question_{{ $question->id }}" class="form-check-input" type="radio"
                                                value="{{ $option_value }}" @if($user_answer == $option_value) checked @endif
                                                disabled
                                            >
                                            <label class="form-check-label">{{ $option_value }}</label>
                                        </div>
                                    @endforeach
                                @endif
                            @break
                            @case('availability-selection-calender')
                                <app-time-slot-picker
                                    assessment_question_id="{{ $question->id }}"
                                    timeslots_field_name="timeslots_{{ $question->id }}[]"
                                    selected_timeslots="{{ $user_answer }}"
                                    :disabled="true"
                                ></app-time-slot-picker>
                            @break
                            @case('textfield')
                                <input type="text" name="question_{{ $question->id }}" class="form-control" value="{{ $user_answer }}" disabled />
                            @break
                            @default
                                <textarea class="form-control" rows="7" name="question_{{ $question->id }}" readonly>{{ $user_answer }}</textarea>
                        @endswitch
                    </div>
                </div>
            @endforeach
        </div>
    </div>
@endsection

<script src="{{ asset('public'.mix('js/page/assessment/assessment_user/details.js')) }}"></script>