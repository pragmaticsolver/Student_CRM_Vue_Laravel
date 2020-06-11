<div class="col-lg-12 col-md-12">
    <div class="row ibox calendar">
        <div class="ibox-content" style="position:relative;width:100%">
            <div id="calendar_preloader" class="text-center;" style="width: 100%;height: 100%;position: absolute;top:0;
            left: 0;z-index:10;background:#58608852;">
                <div class="fa fa-spinner fa-spin" style="font-size:30px;position: relative;display: inline-block;position: absolute;left: 50%;top: 50%;
                text-align:center;"></div>
            </div>
            <div id="attendance_calendar"></div>
        </div>
    </div>
    <div class="row" style="position:relative">
        <div id="class_details_preloader" class="text-center;" style="width: 100%;height: 100%;position: absolute;top:0;
        left: 0;z-index:10;background:#58608852;">
            <div class="fa fa-spinner fa-spin" style="font-size:30px;position: relative;display: inline-block;position: absolute;left: 50%;top: 50%;
            text-align:center;"></div>
        </div>
        <div id="class_usage_details" class="col-sm-12">
        </div>
    </div>
</div>

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
                    <button type="button" class="btn btn-light" data-dismiss="modal">{{ __('messages.close')}}</button>
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

    <script src="{{ asset('public'.mix('js/page/student/class_usage_tab.js')) }}"></script>
    <script>
        var classsUsageDetailsurl = "{{ route('student.class_usage_details') }}";
        var calDataUrl = "{{ route('student.att_cal_data') }}";
        var scheduleUrl  =   "{{url('schedule/details_student')}}";

        var student_id = "{{ $student->id }}";
        var lang = '{{ \Auth::user()->get_lang() }}';
        var app_timezone = '{{  \App\Helpers\CommonHelper::getSchoolTimezone() }}';
        var minTime = "{{isset($default_show_calendar[0]) ? $default_show_calendar[0] : '00:00'}}";
        var maxTime = "{{isset($default_show_calendar[1]) ? $default_show_calendar[1] : '24:00'}}";
        var calName = "student-attendance-page";
        var visibleDays = "{{ $visible_days }}";
        var weekStartDay = "{{ $week_start_day }}";
        var calendarView = 'month';
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
