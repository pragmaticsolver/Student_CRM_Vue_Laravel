@extends('layouts.app')

@section('content')
    <div class="col-lg-12 col-md-12">
        @include('partials.success')
        @include('partials.error')
        <div class="ibox ">

            <div class="ibox-content" style="position:relative;width:100%;">
                <div id="calendar_preloader" class="text-center;" style="width: 100%;height: 100%;position: absolute;top:0;
                left: 0;z-index:10;background:#58608852;">
                    <div class="fa fa-spinner fa-spin" style="font-size:30px;position: relative;display: inline-block;position: absolute;left: 50%;top: 50%;
                    text-align:center;"></div>
                </div>

                <div id="calendar"></div>
                <div class="form-group row mt-1">
                    <div class="col-lg-6">
                        <label>{{ __('messages.teachers') }}</label>
                        <br>
                        <div class="btn-group-toggle" data-toggle="buttons">
                            @foreach($teachers as $teacher)
                                <label class="btn active mb-1" data-color="{{ $teacher->get_color_coding() }}">
                                    <input type="checkbox" name="calendar_teachers" value="{{ $teacher->id }}" checked autocomplete="off">{{ $teacher->nickname }}
                                </label>
                            @endforeach
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <label>{{ __('messages.levels') }}</label>
                        <br>
                        <button id="select_all_levels" class="btn btn-sm btn-secondary mb-1">{{ __('messages.select-all') }}</button>
                        <button id="clear_selection_levels" class="btn btn-sm btn-secondary mb-1">{{ __('messages.clear-selection') }}</button>
                        <select id="student_levels" name="calendar_class_student_levels" class="form-control col-sm-3" multiple>
                            @foreach($class_student_levels as $level)
                                <option value="{{ $level }}">{{ $level }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('modals')
    <div class="modal inmodal" id="EventModal" tabindex="-1" role="dialog"  aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title"></h4>
                </div>
                <div class="modal-body">
                    <div class="text-center"><div class="fa fa-spinner fa-spin" style="font-size:100px"></div></div>
                </div>
                <div class="modal-footer facing-calendar">

                </div>
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
    <script src='https://unpkg.com/@fullcalendar/bootstrap@4.3.0/main.min.js'></script>
    <script src="{{ asset('public'.mix('js/page/schedule/calendar.js')) }}"></script>

    <script>
        window.reservationSimpleCancelUrl = "{{ url('yoyaku/simple-cancel') }}";
        window.eventDataUrl = "{{ route('schedule.event_data_1') }}";
        var reservationUrl  =   "{{url('schedule/reservation')}}";
        var studentUrl = "{{url('schedule/details')}}";
        var scheduleUrl  =   "{{url('schedule/details')}}";
        var waitlistUrl  =   "{{url('schedule/waitlist')}}";
        var calDataUrl = "{{ route('schedule.cal_data_1') }}";

        var selected_teachers = [];
        var selected_levels = {!! $student_levels !!};
        var lang = '{{ \Auth::user()->get_lang() }}';
        var app_timezone = '{{  \App\Helpers\CommonHelper::getSchoolTimezone() }}';

        var minTime = "{{isset($default_show_calendar[0]) ? $default_show_calendar[0] : '00:00'}}";
        var maxTime = "{{isset($default_show_calendar[1]) ? $default_show_calendar[1] : '24:00'}}";
        var calName = "calendar-page";
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
