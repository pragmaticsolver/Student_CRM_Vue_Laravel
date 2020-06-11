@extends('layouts.app')

@section('content')
    <div class="row justify-content-center">
        @include('partials.success')
        @include('partials.error')
        <div class="col-12" id="vue-app">
            <form method="POST" class="submit-test" onsubmit="return false;" autocomplete="off">
                @csrf
                <h1>{{ __('messages.assessment-preview') }}: {{  $assessment->name }}</h1>
                
                <table class="table table-hover table-striped">
                    <tbody>
                        @if($assessment->description)
                            <tr>
                                <th width="16%">{{ __('messages.description') }}</th>
                                <td>{{ $assessment->description }}</td>
                            </tr>
                        @endif
                    </tbody>
                </table>

                @foreach($assessment->assessment_questions as $index => $question)
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
                                                @if($star_index == 5) checked @endif
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
                                                    value="{{ $option_value }}" @if($index == 0) checked @endif
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
                                        :disabled="false"
                                    ></app-time-slot-picker>
                                @break
                                @case('textfield')
                                    <input type="text" name="question_{{ $question->id }}" class="form-control" />
                                @break
                                @default
                                    <textarea class="form-control" rows="7" name="question_{{ $question->id }}"></textarea>
                            @endswitch
                        </div>
                    </div>
                @endforeach
            </form>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="{{ asset('public'.mix('js/page/user/take.js')) }}"></script>
@endpush