<div class="overload-content">
    <div class="preload">
        <div class="fa fa-spinner fa-spin" style="font-size:100px"></div>
    </div>
    <div class="alert alert-success" style="display:none;" id="reservation_alert"></div>
    <div class="alert alert-danger" style="display:none;" id="reservation_alert_danger"></div>
    <div class="alert alert-warning" style="display:none;" id="reservation_alert_warning"></div>
    <table class="table table-striped table-bordered table-hover ">
        <tr>
            <th>{{ __('messages.class')}}</th>
            <td>{{$schedule->class->title}}</td>
        </tr>
        <tr>
            <th>{{ __('messages.date')}}</th>
            <td>{{$date}}</td>
        </tr>
        <tr>
            <th>{{ __('messages.classtime')}}</th>
            <td>
                {{$schedule->start_time}} - {{$schedule->end_time}}
            </td>
        </tr>
        <tr>
            <th>{{ __('messages.classteacher')}}</th>
            <td>{{$schedule->teacher->nickname}}</td>
        </tr>
        @if($schedule->course_schedule)
            <tr>
                <th>{{ __('messages.course')}}</th>
                <td>{{ $schedule->course_schedule->course->title }}</td>
            </tr>
        @endif
    </table>

    @can('schedule-details')
        <a class="btn btn-primary" href="{{ route('schedule.show', $schedule->id) }}">{{ __('messages.scheduledetails') }}</a>
    @endcan

    @can('edit-schedule')
        <button id="edit_schedule" class="btn btn-warning" data-id="{{ $schedule->id }}" data-date="{{ $date }}">{{ __('messages.edit-schedule') }}</button>
    @endcan

    @if($use_zoom && $permission_manage_zoom_meetings)
        <div id="schedule-zoom-meeting-container">
            <app-schedule-zoom-meeting
                schedule_id="{{ $schedule->id }}"
                date="{{ $date }}"
                :zoom_meeting="{{ $zoomMeeting ? json_encode($zoomMeeting->toArray()) : '{}' }}"
            ></app-schedule-zoom-meeting>
        </div>
    @endif

    <div class="col-12 bg-success text-white my-2">
        <label class="my-1"><input type="checkbox" id="send_email_for_actions">{{ __('messages.notify-student-&-teacher-via-appropriate-email-when-action-performed-from-this-modal') }}</label>
    </div>

    @if(!$yoyakus->isEmpty())
        <h3 id="registered-students-title">{{ __('messages.registeredstudents')}}</h3>
    @else
        <h3 id="registered-students-title" style="display: none;">{{ __('messages.registeredstudents')}}</h3>
    @endif
    <table class="table table-striped table-bordered table-hover registered-students">
        @foreach ($yoyakus as $yoyaku)
            @include('schedule.student_row')
        @endforeach
    </table>

    @if(!$waitlisted_yoyakus->isEmpty())
        <h3 id="waitlisted-students-title">{{ __('messages.waitliststudents')}}</h3>
    @else
        <h3 id="waitlisted-students-title" style="display: none;">{{ __('messages.waitliststudents')}}</h3>
    @endif
    <table class="table table-striped table-bordered table-hover waitlisted-students">
        @foreach ($waitlisted_yoyakus as $yoyaku)
            @include('schedule.waitlisted_student_row')
        @endforeach
    </table>

    <form id="reservation_form" class="waitlisting reserve" method="GET">
        <label class="col-12 col-form-label pl-0">{{__('messages.select-student')}}</label>
        <div class="form-group" style="margin-bottom:5px;">
            <input type="hidden" name="customer_id" id="selected_student_id">
            <div class="custom-dropdown">
                <div class="selected_value_text"></div>
                <input type="text" placeholder="{{ __('messages.search-student') }}" class="form-control search_input">
                <div class="options-section" style="max-height:100px;overflow-y:auto;overflow-x:none;">
                    @foreach ($students as $student)
                        @if(!$student->isArchived())
                            <div class="option-item visible" data-id="{{ $student->id }}">{{ $student->getFullNameAttribute() }}</div>
                        @endif
                    @endforeach
                </div>
            </div>
        </div>
        <div class="form-group" style="margin-bottom:5px;">
            <label><input type="checkbox" value="1" name="taiken">{{ __('messages.do-not-include-in-class-total') }}</label>
        </div>
        <div class="form-group">
            <button class="btn btn-primary" data-toggle="collapse" data-target="#regularStudent">Register regular student</button>
        </div>

        <div id="regularStudent" class="collapse">
            <div class="form-group row">
                <label class="col-lg-2 col-form-label">{{ __('messages.startdate') }}</label>
                <div class="col-lg-10">
                    <input class="form-control" type="date" name="start_date" required="">
                </div>
            </div>
            <div class="form-group row">
                <label class="col-lg-2 col-form-label">{{ __('messages.enddate') }}</label>
                <div class="col-lg-10">
                    <input class="form-control" type="date" name="end_date" required="">
                </div>
            </div>
        </div>

        <input type="hidden" name="schedule_id" value="{{$id}}"/>
        <input type="hidden" name="date" value="{{$date}}"/>
    </form>
</div>
