<div class="card card-body course-progress">
    @if($assessment->assessment_questions->count() > 0)
        @foreach($assessment->assessment_questions as $key => $question)
           <div class="assessment-question-row">
                <table class="table table-bordered table-hover">
                    <tbody>
                    <tr>
                        <th style="width:15%">{{ __('messages.question') }} {{ $key + 1  }}: </th>
                        <td>{{ $question->name }}</td>
                    </tr>
                    <tr>
                        <th>{{ __('messages.type') }}: </th>
                        <td>{{ $question->getDislayType() }}</td>
                    </tr>
                    @if($question->type == 'option')
                        <tr>
                            <th>{{ __('messages.options') }}: </th>
                            <td>
                                @foreach((array)json_decode($question->option_values) as $option_value)
                                    <li>{{ $option_value }}</li>
                                @endforeach
                            </td>
                        </tr>
                    @elseif($question->type == 'availability-selection-calender')
                        <tr>
                            <th>{{ __('messages.calendar') }}: </th>
                            <td>
                                {{ $question->availabilitySelectionCalendar->name }}
                            </td>
                        </tr>
                    @endif
                    <tr>
                        <th>{{ __('messages.required') }}: </th>
                        <td>{{ $question->is_required ? __('messages.yes') : __('messages.no') }}</td>
                    </tr>
                    <tr>
                        <th>{{ __('messages.actions') }}: </th>
                        <td>
                            <button type="button" class="btn_edit_question btn btn-sm btn-success" data-id="{{ $question->id}}">{{ __('messages.edit') }}</button>

                            <button type="button" class="btn_delete_question btn btn-sm btn-danger" data-id="{{ $question->id}}">{{ __('messages.delete') }}</button>
                        </td>
                    </tr>
                    </tbody>
                </table>
           </div>
        @endforeach
    @else
        <p class="text-center">{{ __('messages.no-questions-added-yet') }} </p>
    @endif
</div>
