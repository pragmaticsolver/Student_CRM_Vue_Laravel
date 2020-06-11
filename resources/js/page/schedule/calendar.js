window.calendarInstance = null;
window.lastClickedEvent = null;

window.addEventListener('DOMContentLoaded', function() {

    $('#student_levels').select2({
        width: '100%',
        placeholder: trans('messages.select-level-s')
    });

    // Set initial values for filters
    // keep all filters selected when login
    if(getCalendarStorageData('selected_teachers') == null)
    {
        selected_teachers = [];
        $('input[name="calendar_teachers"]').each(function(){
            selected_teachers.push($(this).val());
        });
    }
    else
    {
        selected_teachers = getCalendarStorageData('selected_teachers').split(',');
    }
    if(getCalendarStorageData('selected_levels') != null)
    {
        selected_levels = getCalendarStorageData('selected_levels').split(',');
    }

    $('input[name="calendar_teachers"]').each(function(){
        if(selected_teachers.includes($(this).val()))
        {
            $(this).attr('checked',true);
            $(this).closest('.btn').addClass('active');
        }
        else
        {
            $(this).removeAttr('checked');
            $(this).closest('.btn').removeClass('active')
        }
        update_custom_toggle_button_ui($(this));
    });
    $('#student_levels').val(selected_levels);
    $('#student_levels').trigger('change'); // on change is not binded yet so it will not save it to local storage yet.

    // On change of filter values save value to local storage
    $('input[name="calendar_teachers"]').on('change',function(){
        selected_teachers = [];
        $('input[name="calendar_teachers"]:checked').each(function(){
            selected_teachers.push($(this).val());
        });
        setCalendarStorageData('selected_teachers', selected_teachers);
        calendarInstance.render();
        calendarInstance.updateSize();
        update_custom_toggle_button_ui($(this));
    });
    $('#student_levels').on('change', function () {
        selected_levels = $(this).val();
        setCalendarStorageData('selected_levels', selected_levels);
        calendarInstance.render();
        calendarInstance.updateSize();
    });

    $('#select_all_levels').click(function(){
        _selection = [];
        $('#student_levels').find('option').each(function(){
            _selection.push($(this).val());
        });
        $('#student_levels').val(_selection);
        $('#student_levels').trigger('change');
    });

    $('#clear_selection_levels').click(function(){
        $('#student_levels').val([]);
        $('#student_levels').trigger('change');
    });

    $(document).delegate("#reserve_now","click", function() {
        $('#reservation_alert').hide();
        $('#reservation_alert_danger').hide();
        $('#reservation_alert_warning').hide();
        if($(this).parent().hasClass("facing-calendar")) {
            $(this).attr('disabled',true);
        }
        if($(this).parent().attr('id') == 'reservation_form') {
            var form = $(this).parent().serialize();
            var reserve_from_waitlist = $(this).parent().parent().parent();
        } else {
            var form = $('#reservation_form.reserve').serialize();
        }

        $.ajax({
            url:reservationUrl,
            data:form,
            beforeSend: function(){
                $('.overload-content .preload').css({"display" : "flex"});
            },
            success:function(data){
                if(data.success){
                    refetchEvent();

                    $('#reservation_alert').text(data.message);
                    $('#reservation_alert').show();
                    $('#reservation_h3').hide();

                    if(data.warning !== undefined) {
                        $('#reservation_alert_warning').text(data.warning);
                        $('#reservation_alert_warning').show();
                    }

                    if(data.full) {
                        $(".modal-footer.monthly-calendar #reserve_now").hide();
                        $(".modal-footer.monthly-calendar #waitlist_now").show();
                    }

                    if(reserve_from_waitlist !== undefined) {
                        reserve_from_waitlist.remove();
                    }

                    $('h3#registered-students-title').show();

                    if(!$('table.waitlisted-students tr').length) {
                        $('h3#waitlisted-students-title').hide();
                    }
                }else{
                    $('#reservation_alert_danger').text(data.error);
                    $('#reservation_alert_danger').show();
                }

                $('.overload-content .preload').css({"display" : "none"});
            },
            error:function(e){
                //alert(e);
                Swal.fire(e);
            },

        });

    });

    defaultCalendarView = isMobile() ? fcv3NameMapings.mobile[calendarView] : fcv3NameMapings.desktop[calendarView];
    calView = getCalendarStorageData('defaultView') ? getCalendarStorageData('defaultView') : defaultCalendarView;

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

    var calendarEl = document.getElementById('calendar');
    calendarInstance = new FullCalendar.Calendar(calendarEl, {
        plugins: ['dayGrid','timeGrid','list', 'bootstrap'],
        themeSystem: 'bootstrap',
        timeZone: app_timezone,
        defaultView: calView,
        defaultDate: getCalendarStorageData('defaultDate') ?  getCalendarStorageData('defaultDate') : moment.utc().format('YYYY-MM-DD'),
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
        editable: false,
        eventClick: function(info) {
            event = info.event;
            jsEvent = info.jsEvent;
            
            if(!event.extendedProps.isSchoolOffDayEvent)
            {
                lastClickedEvent = event;

                var title = event.title;
                var start = moment.utc(event.start.getTime()).format("YYYY-MM-DD");
                var modal = $('#EventModal').modal('show');

                modal.find('.modal-body').load(scheduleUrl+'?schedule_id='+event.extendedProps.ID+'&date='+start+'&view=body', function(){
                    if (typeof initializeCustomDropdown == "function") {
                        initializeCustomDropdown();
                    }
                });
                modal.find('.modal-footer.facing-calendar').load(scheduleUrl+'?schedule_id='+event.extendedProps.ID+'&date='+start+'&view=facing-footer');
                modal.find('.modal-footer.monthly-calendar').load(studentUrl+'?schedule_id='+event.extendedProps.ID+'&date='+start+'&view=monthly-footer');

                modal.find('.modal-title').text(title);

                $('#EventModal').on('show.bs.modal', function (e) {
                    $('#reservation_alert').text('');
                    $('#reservation_alert').hide();

                });

                $('#EventModal').on('hidden.bs.modal', function(){
                    $('#EventModal .modal-body').html('<div class="text-center"><div class="fa fa-spinner fa-spin" style="font-size:100px"></div></div>');
                    $('#EventModal .modal-footer .cancel-class').html('');
                });
            }
        },
        events: function(fetchInfo, successCallback, failureCallback) {
            $.ajax({
                url: calDataUrl,
                dataType: 'json',
                data: {
                    start: fetchInfo.start.getTime() / 1000,
                    end: fetchInfo.end.getTime() / 1000,
                },
                success: function success(response) {
                    successCallback(response.events);
                },
                error: function (e){
                    failureCallback(e);
                }
            });
        },
        viewSkeletonRender: function (info) {
            setCalendarStorageData('defaultView', info.view.type);
        },
        datesRender: function(info){
            setCalendarStorageData('defaultDate', moment.utc(info.view.activeStart.getTime()).format('YYYY-MM-DD'));
        },
        loading: function(isLoading, view) {
            if (isLoading) {
                $('#calendar_preloader').show();
            } else {
                $('#calendar_preloader').hide();
            }
        },
        eventRender: function (info) {
            event = info.event;
            element = $(info.el);

            const teachers = $('input[name="calendar_teachers"]:checked').map(function() {
                return parseInt($(this).val());
            }).get();
            const selectedClasses = $('#student_levels').val();
            var event_date = moment.utc(event.start.getTime()).format('YYYY-MM-DD');

            // Add classes to event
            if(event.extendedProps.isEvent){ element.addClass("is-event");}
            if(event.extendedProps.isStudentRegistered){ element.addClass("student-registered");}
            if(event.extendedProps.greyedPastClass){ element.addClass("greyedPastClass");}
            if(event.extendedProps.isSchoolOffDayEvent){ element.addClass("school-off-day-event"); }

            if(event.extendedProps.allDay && !event.extendedProps.isBirthdayEvent && !event.extendedProps.isSchoolOffDayEvent)
            {
                $('.fc-day').each(function(){
                    if($(this).data('date') == event_date) {
                        $(this).addClass("all-day");
                    }
                });
            }

            if(event.extendedProps.isSchoolOffDayEvent)
            {
                $('.fc-day').each(function(){
                    if($(this).data('date') == event_date) {
                        $(this).addClass("school-off-day-all-day");
                    }
                });
                return true;
            }

            if(
                (teachers.indexOf(event.extendedProps.teacher_id) >= 0 || event.extendedProps.isEvent) &&
                selectedClasses.includes(event.extendedProps.classLevel) &&
                event.extendedProps.isVisible
            ) {
                // Check if the current class is full
                if(typeof event.extendedProps.fullDates != 'undefined' && event.extendedProps.fullDates.indexOf(event_date) != -1)
                {
                    if((!event.extendedProps.hideFull || event.extendedProps.isReserved))
                    {
                        element.addClass("full-class");
                        return true;
                    }
                    else
                    {
                        return false;
                    }
                }
            } else {
                return false;
            }
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

    $(document).delegate("form#waitlist-delete button", "click", function() {
        var _this = this;

        Swal.fire({
            title: trans('messages.are-you-sure'),
            text: trans('messages.you-wont-be-able-to-revert-this'),
            confirmButtonText: trans('messages.yes-i-sure'),
            cancelButtonText: trans('messages.cancel'),
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
        }).then(function (result) {
            if (result.value) {
              // if(confirm("Are you sure?")) {
              $('#reservation_alert').hide();
              $('#reservation_alert_danger').hide();
              $('#reservation_alert_warning').hide();
              var form = $(_this).parent().serialize();
              var row = $(_this).parent().parent().parent();
              $.ajax({
                url: waitlistDeleteUrl,
                data: form,
                beforeSend: function beforeSend() {
                  $('.overload-content .preload').css({
                    "display": "flex"
                  });
                },
                success: function success(data) {
                  if (data.success) {
                    $('#reservation_alert').text(data.message);
                    $('#reservation_alert').show();
                    $('#reservation_h3').hide();
                    row.remove();
                    refetchEvent();
                  } else {
                    $('#reservation_alert_danger').text(data.error);
                    $('#reservation_alert_danger').show();
                  }

                  $('.overload-content .preload').css({
                    "display": "none"
                  });

                  if (!$('table.waitlisted-students tr').length) {
                    $('h3#waitlisted-students-title').hide();
                  }
                },
                error: function error(e) {
                  // alert(e);
                  Swal.fire(e);
                }
              }); // }
            }
        });
    });

    $(document).delegate("#waitlist_now","click", function() {
        $('#reservation_alert').hide();
        $('#reservation_alert_danger').hide();
        $('#reservation_alert_warning').hide();
        if($(this).parent().hasClass("facing-calendar")) {
            $(this).attr('disabled',true);
        }
        var form = $('#reservation_form.waitlisting').serialize();
        $.ajax({
            url:waitlistUrl,
            data:form,
            beforeSend: function(){
                $('.overload-content .preload').css({"display" : "flex"});
            },
            success:function(data){
                if(data.success){
                    $('#reservation_alert').text(data.message);
                    $('#reservation_alert').show();
                    $('#reservation_h3').hide();

                    if(data.warning !== undefined) {
                        $('#reservation_alert_warning').text(data.warning);
                        $('#reservation_alert_warning').show();
                    }

                    $('h3#waitlisted-students-title').show();
                    refetchEvent();
                }else{
                    $('#reservation_alert_danger').text(data.error);
                    $('#reservation_alert_danger').show();
                }

                $('.overload-content .preload').css({"display" : "none"});
            },
            error:function(e){
                //alert(e);
                Swal.fire(e);
            },

        });

    });
});

function isMobile()
{
    return $(window).width() < 514;
}
