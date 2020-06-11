@extends('layouts.app')

@section('content')
    <div class="row justify-content-center">
        @include('partials.success')
        @include('partials.error')
        <div class="col-12">
            <form method="POST" action="{{ route('user.assessment.store_result', $assessment_user->id) }}" class="submit-test">
                @csrf
                @php
                    $assessment = $assessment_user->assessment;
                    $schedule = $assessment_user->schedule;
                    $questions = $assessment->assessment_questions;
                    $user = \Auth::user();
                @endphp
                <h1>{{ __('messages.assessmentname', ['name' => $assessment->name]) }}</h1>
                <table class="table table-hover table-striped">
                    <tbody>
                    <tr>
                        <th>{{ __('messages.user') }}</th>
                        <td>{{ $user->name }}</td>
                    </tr>
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

                @if($questions->count() > 0)
                    @foreach($questions as $index => $question)
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
                                                @if($star_index == 5) checked @endif
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
                                                    value="{{ $option_value }}" @if($index == 0) checked @endif
                                                >
                                                <label class="form-check-label">{{ $option_value }}</label>
                                            </div>
                                        @endforeach
                                    @endif
                                    @break
                                    @default
                                    <textarea class="form-control" rows="7" name="question_{{ $question->id }}" required></textarea>
                                @endswitch
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
