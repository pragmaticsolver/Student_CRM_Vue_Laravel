@extends('layouts.app')

@section('content')
    <div class="row justify-content-center">
        @include('partials.success')
        @include('partials.error')
        <div class="col-12">
            @if($errors)
                <div class="alert alert-danger" role="alert">
                    {{ __('messages.please-fill-all-the-required-fields-and-resbumit-the-form') }}.
                </div>
            @endif
        </div>
        <div class="col-12" id="vue-app">
        <form method="POST" action="{{ route('user.assessment.store_result', $assessment_user->id) }}" class="{{ $show_warning_on_submit ? 'submit-test' : '' }}" autocomplete="off">
                @csrf
                <input type="hidden" name="return_url" value="{{ $return_url }}">
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

                @if($questions->count() > 0)
                    @foreach($questions as $index => $question)
                        @php
                            if(isset($inputs[$question->id]))
                            {
                                $user_answer = $inputs[$question->id];
                            }
                            else 
                            {
                                $user_answer = $question->getUserAnswer($assessment_user->id);
                            }
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
                                                    @if(($user_answer ? $user_answer : 5) == $star_index) checked @endif
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
                                            :disabled="false"
                                        ></app-time-slot-picker>
                                    @break
                                    @case('textfield')
                                        <input type="text" name="question_{{ $question->id }}" class="form-control" value="{{ $user_answer }}" />
                                    @break
                                    @default
                                        <textarea class="form-control" rows="7" name="question_{{ $question->id }}">{{ $user_answer }}</textarea>
                                @endswitch
                                @if(isset($errors[$question->id]))
                                    <div class="form_error my-1 text-danger">{{ $errors[$question->id] }}</div>
                                @endif
                            </div>
                        </div>
                    @endforeach
                @endif

                <div class="form-group row">
                    <div class="col-lg-12 m-auto">
                        <input name="add" type="submit" value="{{ __('messages.submit') }}" class="form-control btn-success">
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="{{ asset('public'.mix('js/page/user/take.js')) }}"></script>
@endpush