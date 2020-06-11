@extends('layouts.app')

@section('content')
    @if(session()->has('success'))
        <div class="alert alert-success">
            {{ session()->get('success') }}
        </div><br/>
    @endif
    @if(session()->has('error'))
        <div class="alert alert-danger">
            {{ session()->get('error') }}
        </div><br/>
    @endif
	<div class="col-lg-12 col-md-12">
	    <div class="ibox calendar">
            <div id='external-events' class="d-none d-md-block">
                <div>
                    <button class="btn btn-success btn-block" id="btn_add_contact">{{ __('messages.addcontact') }}</button>
                </div>
                @can('schedule-add')
                    <h4>{{ __('messages.classes')}}</h4>
                    <div style="height:50%;overflow-y:auto;">
                        @foreach($classes as $class)
                            <div class='fc-event fc-event-dragable'
                            class-id="{{$class->id}}"
                            class-length="{{ $class->length ? $class->length: $default_class_length }}"
                            class-default_course_id="{{ $class->default_course_id  }}"
                            >{{$class->title}}</div>
                        @endforeach
                    </div>
                @endcan
            </div>
	       
	        <div class="ibox-content" style="position:relative;width:100%;">
                <div id="calendar_preloader" class="text-center;" style="width: 100%;height: 100%;position: absolute;top:0;
                left: 0;z-index:10;background:#58608852;">
                    <div class="fa fa-spinner fa-spin" style="font-size:30px;position: relative;display: inline-block;position: absolute;left: 50%;top: 50%;
                    text-align:center;"></div>
                </div>

                <div id="calendar"></div>
                <div class="form-group row mt-2">
                    <div class="col-lg-6">
                        <div class="col">
                            <label>{{ __('messages.teachers') }}
                                <span class="empty-class-filter-warning" style="display:none;"><i class="fa fa-info-circle" data-toggle="tooltip" title="{{ __('messages.filter-not-applicable-when-empty-class-is-on') }}" data-placement="right"></i></span>
                            </label>
                            <br>
                            <div class="btn-group-toggle" data-toggle="buttons">
                                @foreach($teachers as $teacher)
                                    <label class="btn active mb-1" data-color="{{ $teacher->get_color_coding() }}">
                                        <input type="checkbox" name="calendar_teachers" value="{{ $teacher->id }}" checked autocomplete="off">{{ $teacher->nickname }}
                                    </label>
                                @endforeach
                            </div>
                        </div>
                        <div class="col">
                            <label>{{ __('messages.filters') }}</label>
                            <br>
                            <div class="btn-group-toggle" data-toggle="buttons">
                                <label class="btn active mb-1" data-color="blue">
                                    <input type="checkbox" name="show_empty_class" value="show_empty_class" checked autocomplete="off">{{ __('messages.empty-class') }}
                                </label>
                                <label class="btn active mb-1" data-color="#1a97b3">
                                    <input type="checkbox" name="show_birthday_events" value="show_birthday_events" checked autocomplete="off">{{ __('messages.birthdays') }}
                                </label>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <div class="col">
                            <label>{{ __('messages.levels') }}
                                <span class="empty-class-filter-warning" style="display:none;"><i class="fa fa-info-circle" data-toggle="tooltip" title="{{ __('messages.filter-not-applicable-when-empty-class-is-on') }}" data-placement="right"></i></span>
                            </label>

                            <br>
                            <button id="select_all_levels" class="btn btn-sm btn-secondary mb-1">{{ __('messages.select-all') }}</button>
                            <button id="clear_selection_levels" class="btn btn-sm btn-secondary mb-1">{{ __('messages.clear-selection') }}</button>
                            <select id="student_levels" name="calendar_class_student_levels" class="form-control col-sm-4" multiple >
                                @foreach($class_student_levels as $level)
                                    <option value="{{ $level }}">{{ $level }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>
	        </div>
	    </div>
    </div>
@endsection

@push('modals')
    <div class="modal inmodal" id="EventModal" tabindex="-1" role="dialog"  aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content animated fadeIn">
                <div class="modal-header">
                    <h4 class="modal-title"></h4>
                </div>
                <div class="modal-body">
                    <div class="text-center"><div class="fa fa-spinner fa-spin" style="font-size:100px"></div></div>
                </div>
                <div class="modal-footer monthly-calendar">

                </div>
            </div>
        </div>
    </div>

    <div class="modal inmodal" id="DropEventModal" tabindex="-1" role="dialog"  aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content animated fadeIn">
                <div class="modal-header">
                    <h4 class="modal-title">{{ __('messages.addclass')}}</h4>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body">
                    <form method="POST" action="{{ route('schedule.save') }}" class="form-add-schedule">
                        @csrf
                        <div class="form-group row" style="display: none">
                            <label class="col-lg-2 col-form-label">{{ __('messages.description') }}</label>
                            <div class="col-lg-10">
                                <input name="description" type="text" class="form-control{{ $errors->has('description') ? ' is-invalid' : '' }}" value="{{ old('description') }}">
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-lg-2 col-form-label">{{ __('messages.starttime') }}</label>
                            <div class="col-lg-10">
                                <input name="start_time" type="time" class="form-control{{ $errors->has('start_time') ? ' is-invalid' : '' }}" value="{{ old('start_time') }}" required="">
                            </div>
                        </div>
                        <div class="form-group row allday-off">
                            <label class="col-lg-2 col-form-label">{{ __('messages.endtime') }}</label>
                            <div class="col-lg-10">
                                <input name="end_time" type="time" class="form-control{{ $errors->has('end_time') ? ' is-invalid' : '' }}" value="{{ old('end_time') }}" required="">
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-lg-2 col-form-label">{{ __('messages.classteacher') }}</label>
                            <div class="col-lg-10">
                                <select name="teacher_id" class="form-control" required="">
                                    <option value="">{{ __('messages.selectteacher') }}</option>
                                    @if(!$teachers->isEmpty())
                                        @foreach($teachers as $teacher)
                                            @if($teacher->status == 0)
                                                <option value="{{$teacher->id}}" <?php if($teacher->id == old('teacher_id')) echo 'selected'; ?>>{{$teacher->nickname}}</option>
                                            @endif
                                        @endforeach
                                    @endif
                                </select>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-lg-2 col-form-label">{{ __('messages.course') }}</label>
                            <div class="col-lg-10">
                                <select name="course_id" class="form-control">
                                    <option value="">{{ __('messages.selectcourse') }}</option>
                                    @if(!$courses->isEmpty())
                                        @foreach($courses as $course)
                                            <option value="{{$course->id}}" <?php if($course->id == old('course_id')) echo 'selected'; ?>>{{$course->title}}</option>
                                        @endforeach
                                    @endif
                                </select>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-lg-2 col-form-label"></label>
                            <div class="col-lg-10">
                                <label><input type="radio" name="type" value="0" checked="">{{ __('messages.addrepeatschedule') }}</label>
                                <label><input type="radio" name="type" value="1">{{ __('messages.addoneoffschedule') }}</label>
                            </div>
                        </div>
                        <div id="schedule-date">
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
                        <input type="hidden" name="class_id" value="">
                        <input type="hidden" name="date" value="">
                        <button type="submit" class="btn btn-success btn-block">{{ __('messages.schedule') }}</button>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-danger pull-left" data-dismiss="modal" aria-label="Close">{{ __('messages.cancel')}}</button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal inmodal" id="CancelMultipleModal" tabindex="-1" role="dialog"  aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content animated fadeIn">
                <div class="modal-header">
                    <h4 class="modal-title">{{ __('messages.cancel-mutliple')}}</h4>
                </div>
                <form action="">
                    <div class="modal-body">
                        <div class="text-center"><div class="fa fa-spinner fa-spin" style="font-size:100px"></div></div>
                    </div>
                    <div class="modal-footer monthly-calendar">
                        <button type="button" id="cancel_selected" class="btn btn-danger">{{ __('messages.cancel-on-selected-dates')}}</button>
                        <button type="button" class="btn btn-light" data-dismiss="modal">{{ __('messages.close')}}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="modal inmodal" id="GotoDateModal" tabindex="-1" role="dialog"  aria-hidden="true">
        <div class="modal-dialog" style="width:276px;margin:30px auto;">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">{{ __('messages.select-date') }}</h4>
                </div>
                <div class="modal-body">
                    <div id="datepicker"></div>
                </div>
                <div class="modal-footer facing-calendar">
                    <button type="button" class="btn btn-light" data-dismiss="modal">{{ __('messages.close')}}</button>
                </div>
            </div>
        </div>
    </div>

    <div id="vue-app-1">
        <app-edit-schedule v-if="edit_schedule"
            :schedule_id="schedule_id"
            :date="date"
            @modal-close="modalClosed"
            @schedule-updated="scheduleUpdated"
        ></app-edit-schedule>
    </div>

    <div id="vue-app-2">
        <app-add-contact v-if="add_contact"
            @modal-close="modalClosed"
            @contact-created="contactCreated"
        ></app-add-contact>
    </div>
@endpush

@push('styles')
    <link href='https://unpkg.com/@fullcalendar/core@4.3.0/main.css' rel='stylesheet' />
    <link href='https://unpkg.com/@fullcalendar/daygrid@4.3.0/main.css' rel='stylesheet' />
    <link href='https://unpkg.com/@fullcalendar/timegrid@4.3.0/main.css' rel='stylesheet' />
    <link href='https://unpkg.com/@fullcalendar/list@4.3.0/main.css' rel='stylesheet' />
    <link href='https://unpkg.com/@fullcalendar/bootstrap@4.3.0/main.css' rel='stylesheet' />
@endpush

@push('scripts')
    <script src='https://unpkg.com/@fullcalendar/core@4.3.0/main.min.js'></script>
    <script src='https://unpkg.com/@fullcalendar/daygrid@4.3.0/main.min.js'></script>
    <script src='https://unpkg.com/@fullcalendar/timegrid@4.3.0/main.min.js'></script>
    <script src='https://unpkg.com/@fullcalendar/list@4.3.0/main.min.js'></script>
    <script src='https://unpkg.com/@fullcalendar/core@4.3.0/locales/ja.js'></script>
    <script src='https://unpkg.com/@fullcalendar/interaction@4.3.0/main.min.js'></script>
    <script src='https://unpkg.com/@fullcalendar/bootstrap@4.3.0/main.min.js'></script>

    <script defer src="https://cdnjs.cloudflare.com/ajax/libs/mousetrap/1.6.3/mousetrap.min.js"></script>
    <script src="{{ asset('public'.mix('js/page/schedule/monthly.js')) }}"></script>
    <script>
        window.reservationCancelUrl  =   "{{url('attendance/cancel')}}";
        window.reservationSimpleCancelUrl = "{{ url('yoyaku/simple-cancel') }}";
        window.eventDataUrl = "{{ route('schedule.event_data') }}"

        var waitlistUrl  =   "{{url('schedule/waitlist_by_teacher')}}";
        var waitlistDeleteUrl  =   "{{url('schedule/waitlist_delete')}}";
        var scheduleUrl = "{{url('schedule/details_students')}}";
        var studentUrl = "{{url('schedule/details')}}";
        var reservationUrl  =   "{{url('schedule/reservation_by_teacher')}}";
        var calDataUrl = "{{ route('schedule.cal_data') }}";
        var cancelMultipleModalUrl = "{{ route('schedule.cancel_multiple_modal') }}";
        var cancelMultipleUrl = "{{ route('schedule.cancel_multiple') }}";
        var studentRawUrl = "{{ route('schedule.student.row') }}";
        var studentWatilistRawUrl = "{{ route('schedule.waitlist_student.row') }}";

        var selected_teachers = [];
        var selected_levels = [];
        var show_empty_class = 0;
        var show_birthday_events = 0;
        var isDropdownActive = 0;
        var lang = '{{ \Auth::user()->get_lang() }}';
        var app_timezone = '{{  \App\Helpers\CommonHelper::getSchoolTimezone() }}';
        var manage_school_off_days = "{{ $manage_school_off_days  }}";

        var minTime = "{{isset($default_show_calendar[0]) ? $default_show_calendar[0] : '00:00'}}";
        var maxTime = "{{isset($default_show_calendar[1]) ? $default_show_calendar[1] : '24:00'}}";
        var calName = "monthly-page";
        var visibleDays = "{{ $visible_days }}";
        var weekStartDay = "{{ $week_start_day }}";
        var calendarView = '{{ Auth::user()->get_calendar_view() }}';
        var fcv3NameMapings = {
            mobile: {
                month: 'listMonth',
                agendaWeek: 'listWeek',
                agendaDay: 'listDay'
            },
            desktop:{
                month: 'dayGridMonth',
                agendaWeek: 'timeGridWeek',
                agendaDay: 'timeGridDay'
            },
        };
    </script>
@endpush
