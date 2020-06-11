<div class="form-group row">
    <label class="col-lg-2 col-form-label">{{ __('messages.name') }}</label>
    <div class="col-lg-10">
        <input name="name" type="text" class="form-control{{ $errors->has('name') ? ' is-invalid' : '' }}"
        value="{{ isset($assessment_question) ? old('name',$assessment_question->name) : old('name') }}" required="">
    </div>
</div>

<div class="form-group row">
    <label class="col-lg-2 col-form-label">{{ __('messages.assessment') }}</label>
    <div class="col-lg-10">
        <select name="assessment_id" class="form-control{{ $errors->has('assessment_id') ? ' is-invalid' : '' }}" required="">
            <option value="">{{ __('messages.selectassessment') }}</option>
            @if(!$assessments->isEmpty())
                @foreach($assessments as $assessment)
                    <option
                        value="{{$assessment->id}}"
                        @if($assessment->id == old('assessment_id',$assessment_id)) selected @endif
                    >{{ $assessment->name }}</option>
                @endforeach
            @endif
        </select>
    </div>
</div>

<div class="form-group row">
    <label class="col-lg-2 col-form-label">{{ __('messages.type') }}</label>
    <div class="col-lg-10">
        <select name="assessment_question_type" class="form-control{{ $errors->has('type') ? ' is-invalid' : '' }}" required="">
            <option value="">{{ __('messages.selecttype') }}</option>
            @php
                $val = isset($assessment_question) ? old('assessment_question_type',$assessment_question->type) : old('assessment_question_type');
            @endphp
            @foreach (\App\AssessmentQuestions::getQuestionTypes() as $key => $value)
                <option value="{{ $key }}" {{ $val == $key ? 'selected' : '' }} >{{ $value }}</option>
            @endforeach
        </select>
    </div>
</div>

<div class="form-group row assessment-question-type assessment-question-type-option">
    <label class="col-lg-2 col-form-label">{{ __('messages.options') }}</label>
    <div class="col-lg-10">
        <table id="assessmentQuestionOption" class="table table-bordered">
            <thead>
            <tr>
                <td width="80%">{{ __('messages.option') }}</td>
                <td><button class="btn btn-success" id="addRowOption">{{ __('messages.addoption') }}</button></td>
            </tr>
            </thead>
            <tbody>
                @if(isset($assessment_question))
                    @php
                        $option_values = json_decode($assessment_question->option_values);
                    @endphp
                    @if($option_values)
                        @foreach($option_values as $option_value)
                            <tr>
                                <td width="80%">
                                    <input type="text" name="options[]" class="form-control" value="{{ $option_value }}"/>
                                </td>
                                <td>
                                    <button class="btn btn-danger remove-row">Delete</button>
                                </td>
                            </tr>
                        @endforeach
                    @endif
                @else
                    <tr>
                        <td width="80%">
                            <input type="text" name="options[]" class="form-control" value=""/>
                        </td>
                        <td>
                            <button class="btn btn-danger remove-row">Delete</button>
                        </td>
                    </tr>
                @endif
            </tbody>
        </table>
    </div>
</div>

<div class="form-group row assessment-question-type assessment-question-type-availability-selection-calender">
    <label class="col-lg-2 col-form-label">{{ __('messages.calendar') }}</label>
    <div class="col-lg-10">
        @php
            $val = isset($assessment_question) ? old('availability_selection_calendar_id',$assessment_question->availability_selection_calendar_id) : old('availability_selection_calendar_id');
        @endphp
        <select name="availability_selection_calendar_id" class="form-control {{ $errors->has('availability_selection_calendar_id') ? ' is-invalid' : '' }} required">
            @foreach($availability_selection_calendars as $calendar)
                <option value="{{ $calendar->id }}" {{ $val == $calendar->id ? 'selected' : '' }} >{{ $calendar->name }}</option>
            @endforeach
        </select>
    </div>
</div>

<div class="form-group row">
    <label class="col-lg-2 col-form-label"></label>
    @php
        $val = isset($assessment_question) ? old('is_required',$assessment_question->is_required) : old('is_required');
    @endphp
    <div class="col-lg-10">
        <label><input type="checkbox" name="is_required" value="1" {{ $val == 1 ? 'checked' : ''}} >{{ __('messages.required') }}</label>
    </div>
</div>