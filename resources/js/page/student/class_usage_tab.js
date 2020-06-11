window.calendarInstance = null;
window.lastClickedEvent = null;

window.addEventListener('DOMContentLoaded', function() {

    $('a[data-toggle="tab"]').on('shown.bs.tab', function (e) {
        var target = $(e.target).attr("href");
        if(target == "#classusage")
        {
            calendarInstance.updateSize();
        }
    });

    fetchClassUsageDetails(''); // Fetch data for current month initially
    $(document).on('click','.btn_fetch_class_usage', function(){
        fetchClassUsageDetails($(this).data('month_year'));
    });

    calView = isMobile() ? fcv3NameMapings.mobile[calendarView] : fcv3NameMapings.desktop[calendarView];
    if (isMobile()){
        header = {
            left: 'prev,next today,gotoDateButton',
            center: 'title',
            right: 'listMonth,listWeek,listDay'
        }
    } else {
        header = {
            left: 'prev,next today,gotoDateButton',
            center: 'title',
            right: 'dayGridMonth,timeGridWeek,timeGridDay,listMonth,listWeek,listDay'
        }
    }

    var calendarEl = document.getElementById('attendance_calendar');
    calendarInstance = new FullCalendar.Calendar(calendarEl, {
        plugins: ['dayGrid','timeGrid','list', 'bootstrap'],
        themeSystem: 'bootstrap',
        timeZone: app_timezone,
        defaultView: calView,
        defaultDate: moment.utc().format('YYYY-MM-DD'),
        hiddenDays: constructHiddenDays(visibleDays),
        firstDay: constructFirstDay(weekStartDay),
        minTime: minTime,
        maxTime: maxTime,
        eventTimeFormat: {
            hour: '2-digit',
            minute: '2-digit',
            meridiem: false,
            hour12: false
        },
        contentHeight: 'auto',
        header: header,
        locale: lang,
        views: {
            listDay: {
                buttonText: trans('messages.list-day')
            },
            listWeek: {
                buttonText: trans('messages.list-week')
            },
            listMonth: {
                buttonText: trans('messages.list-month')
            }
        },
        noEventsMessage: trans('messages.nothing-to-display'),
        customButtons: {
            gotoDateButton: {
                text: trans('messages.goto-date'),
                click: function()
                {
                    $('#GotoDateModal').modal('show');
                    $('#GotoDateModal #datepicker').focus();
                }
            }
        },
        eventClick: function(info) {
            event = info.event;
            jsEvent = info.jsEvent;

            var title = event.title + ' - ' + event.extendedProps.attendance_status;
            var start = moment.utc(event.start.getTime()).format("YYYY-MM-DD");
            var modal = $('#EventModal').modal('show');

            modal.find('.modal-body').load(scheduleUrl+'?schedule_id='+event.extendedProps.ID+'&date='+start+'&view=body&yoyaku_id='+event.extendedProps.yoyaku_id);
            modal.find('.modal-title').text(title);

            $('#EventModal').on('show.bs.modal', function (e) {
                $('#reservation_alert').text('');
                $('#reservation_alert').hide();
            });

            $('#EventModal').on('hidden.bs.modal', function(){
                $('#EventModal .modal-body').html('<div class="text-center"><div class="fa fa-spinner fa-spin" style="font-size:100px"></div></div>');
                $('#EventModal .modal-footer .cancel-class').html('');
            });
        },
        events: function(fetchInfo, successCallback, failureCallback) {
            $.ajax({
                url: calDataUrl,
                dataType: 'json',
                data: {
                    start: fetchInfo.start.getTime() / 1000,
                    end: fetchInfo.end.getTime() / 1000,
                    student_id: student_id
                },
                success: function success(response) {
                    successCallback(response.events);
                },
                error: function (e){
                    failureCallback(e);
                }
            });
        },
        loading: function(isLoading, view) {
            if (isLoading) {
                $('#calendar_preloader').show();
            } else {
                $('#calendar_preloader').hide();
            }
        },
        eventRender: function (info) {
            var event = info.event;
            var element = $(info.el);
            element.addClass(event.extendedProps.status_class);
        }
    });

    calendarInstance.render();
    // Hack for sometimes events are not being rendered properly for week view on first render.
    setTimeout(() => {
        calendarInstance.updateSize();
    }, 1000);

    $('#datepicker').datepicker({
        changeMonth: true,
        changeYear: true,
        onSelect: function(dateText, inst) {
            $('#GotoDateModal').modal('hide');
            var date = $(this).datepicker('getDate');
            calendarInstance.gotoDate(moment(date.getTime()).format('YYYY-MM-DD'));
        }
    });

    // navigate to particular month on click of class usage blog.
    $(document).on('click','.class-usage-info', function(){
        $('.class-usage-info').removeClass('active');
        $(this).addClass('active');
        calendarInstance.gotoDate($(this).data('month_year'));
    });
});

function fetchClassUsageDetails(from_date)
{
    $('#class_details_preloader').show();
    $.ajax({
        url: classsUsageDetailsurl,
        dataType: 'json',
        data: {
            customer_id: student_id,
            from_date: from_date,
        },
        success: function success(response) {
            $('#class_usage_details').html(response.html);
            $('#class_details_preloader').hide();
        }
    });
}

function isMobile()
{
    return $(window).width() < 514;
}
