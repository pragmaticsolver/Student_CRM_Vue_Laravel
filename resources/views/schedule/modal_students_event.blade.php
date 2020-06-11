<div class="overload-content">
    <div class="preload">
        <div class="fa fa-spinner fa-spin" style="font-size:100px"></div>
    </div>
    <div class="alert alert-success" style="display:none;" id="reservation_alert"></div>
    <div class="alert alert-danger" style="display:none;" id="reservation_alert_danger"></div>
    <div class="alert alert-warning" style="display:none;" id="reservation_alert_warning"></div>
    <table class="table table-striped table-bordered table-hover ">
        <tr>
            <th>{{ __('messages.event')}}</th>
            <td>{{$schedule->class->title}}</td>
        </tr>
        <tr>
            <th>{{ __('messages.date')}}</th>
            <td>{{$date}}</td>
        </tr>
        <tr>
            <th>{{ __('messages.description')}}</th>
            <td>{{ $schedule->class->description  }}</td>
        </tr>
        <tr>
            <th>{{ __('messages.eventtime')}}</th>
            <td>
                @if($schedule->type != '2')
                    {{$schedule->start_time}} - {{$schedule->end_time}}
                @else
                    {{ __('messages.allday') }}
                @endif
            </td>
        </tr>
        <tr>
            <th>{{ __('messages.cost') }}</th>
            <td>{{ $schedule->event()->first()->cost }}</td>
        </tr>
        <tr>
            <th>{{ __('messages.size') }}</th>
            <td>{{ $schedule->event()->first()->size }}</td>
        </tr>
    </table>
    <h3 id="students_h3">{{ __('messages.registeredstudents')}}</h3>

    <table class="table table-striped table-bordered table-hover registered-students">
        @foreach ($yoyakus as $yoyaku)
            @include('schedule.student_row_event')
        @endforeach
    </table>
    <form id="reservation_form" class="reserve" method="GET">
        <label class="col-12 col-form-label pl-0">{{__('messages.select-student')}}</label>
        <div class="form-group">
            <input type="hidden" name="customer_id" id="selected_student_id">
            <div class="custom-dropdown">
                <div class="selected_value_text"></div>
                <input type="text" placeholder="{{ __('messages.search-student') }}" class="form-control search_input">
                <div class="options-section" style="max-height:100px;overflow-y:auto;overflow-x:none;">
                    @foreach ($students as $student)
                        <div class="option-item visible" data-id="{{ $student->id }}">{{ $student->getFullNameAttribute() }}</div>
                    @endforeach
                </div>
            </div>
        </div>
        <input type="hidden" name="schedule_id" value="{{$id}}"/>
        <input type="hidden" name="date" value="{{$date}}"/>
    </form>
</div>
