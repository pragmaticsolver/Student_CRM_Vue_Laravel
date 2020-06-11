import EditSchedule from '../../components/EditSchedule.vue';
import AddContact from '../../components/AddContact.vue';
import ScheduleZoomMeeting from '../../components/zoom/ScheduleZoomMeeting.vue';

window.calendarInstance = null;
window.lastClickedEvent = null;
var offDays = [];
window.addEventListener('DOMContentLoaded', function() {

    var vm1 = new Vue({
        el: '#vue-app-1',
        components: {
            'app-edit-schedule' : EditSchedule
        },
        data: {
            edit_schedule: false,
            schedule_id: '',
            date: ''
        },
        methods: {
            modalClosed: function(){
                this.edit_schedule = false;
            },
            scheduleUpdated: function(){
                this.showMessage('success',trans('messages.schedule-updated-successfully'));
                calendarInstance.refetchEvents();
            }
        }
    });

    var vm2 = new Vue({
        el: '#vue-app-2',
        components: {
            'app-add-contact': AddContact
        },
        data: {
            add_contact: false,
        },
        methods: {
            modalClosed: function(){
                this.add_contact = false;
            },
            contactCreated: function(message){
                this.showMessage('success',message);
            }
        }
    });
    
    $('[data-toggle="tooltip"]').tooltip();
    $('#student_levels').select2({
        width: '100%',
        placeholder: trans('messages.select-level-s')
    });

    $(document).on('keyup','.search_input', function(){
        filterFunction();
    });

    // Custom Keyboard shortcut
    Mousetrap.bind(['ctrl+enter', 'command+enter'], function(e) {
        if (e.preventDefault) {
            e.preventDefault();
        } else {
            // internet explorer
            e.returnValue = false;
        }
        if($('#EventModal').hasClass('show'))
        {
            if($('#EventModal .modal-footer #reserve_now').is(':visible'))
            {
                $('#EventModal .modal-footer #reserve_now').click();
            }
            else
            {
                $('#EventModal .modal-footer #waitlist_now').click();
            }
            makeDropDownActive();
        }
    });

    var Draggable = FullCalendarInteraction.Draggable;
    var externalEventsConatiner = document.getElementById('external-events');
    if(externalEventsConatiner)
    {
        new Draggable(externalEventsConatiner, {
            itemSelector: '.fc-event-dragable',
            eventData: function(eventEl) {
                return {
                    title: eventEl.innerText,
                    create: false
                };
            },
            zIndex: 999,
            revert: true,      // will cause the event to go back to its
            revertDuration: 0,
        });
    }

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
    if(getCalendarStorageData('selected_levels') == null)
    {
        selected_levels = [];
        $('#student_levels').find('option').each(function(){
            selected_levels.push($(this).val());
        });
    }
    else
    {
        selected_levels = getCalendarStorageData('selected_levels').split(',');
    }
    if(getCalendarStorageData('show_empty_class') != null)
    {
        show_empty_class = getCalendarStorageData('show_empty_class');
    }
    if(getCalendarStorageData('show_birthday_events') == 1)
    {
        show_birthday_events = 1;
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
    var btn = $('input[name="show_empty_class"]');
    if(show_empty_class == 1)
    {
        btn.attr('checked',true);
        btn.closest('.btn').addClass('active');
        $('.empty-class-filter-warning').show();
    }
    else
    {
        btn.removeAttr('checked');
        btn.closest('.btn').removeClass('active')
    }
    update_custom_toggle_button_ui(btn);
    var btn = $('input[name="show_birthday_events"]');
    if(show_birthday_events == 1)
    {
        btn.attr('checked',true);
        btn.closest('.btn').addClass('active');
    }
    else
    {
        btn.removeAttr('checked');
        btn.closest('.btn').removeClass('active')
    }
    update_custom_toggle_button_ui(btn);

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
    $('input[name="show_empty_class"]').on('change', function () {
        show_empty_class = $('input[name="show_empty_class"]').is(':checked') ? 1 : 0;
        if(show_empty_class)
        {
            $('.empty-class-filter-warning').show();
        }
        else
        {
            $('.empty-class-filter-warning').hide();
        }
        setCalendarStorageData('show_empty_class', show_empty_class);
        calendarInstance.render();
        calendarInstance.updateSize();
        update_custom_toggle_button_ui($(this));
    });
    $('input[name="show_birthday_events"]').on('change', function () {
        show_birthday_events = $('input[name="show_birthday_events"]').is(':checked') ? 1 : 0;
        setCalendarStorageData('show_birthday_events', show_birthday_events);
        calendarInstance.render();
        calendarInstance.updateSize();
        update_custom_toggle_button_ui($(this));
    });


    $('#select_all_levels').click(function(){
        var _selection = [];
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

    var defaultCalendarView = isMobile() ? fcv3NameMapings.mobile[calendarView] : fcv3NameMapings.desktop[calendarView];
    var calView = getCalendarStorageData('defaultView') ? getCalendarStorageData('defaultView') : defaultCalendarView;

    if (isMobile()){
        var header = {
            left: 'prev,next today,gotoDateButton',
            center: 'title',
            right: 'listMonth,listWeek,listDay'
        }
    } else {
        var header = {
            left: 'prev,next today,gotoDateButton',
            center: 'title',
            right: 'dayGridMonth,timeGridWeek,timeGridDay,listMonth,listWeek,listDay'
        }
    }

    var calendarEl = document.getElementById('calendar');
    calendarInstance = new FullCalendar.Calendar(calendarEl, {
        plugins: [ 'interaction','dayGrid','timeGrid','list', 'bootstrap'],
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
        droppable: true,
        selectable: manage_school_off_days == 1 ? true : false,
        select: function(selectionInfo){

            var start = moment.utc(selectionInfo.start.getTime());
            var check = moment.utc(selectionInfo.start.getTime());
            if(selectionInfo.allDay)
            {
                var end = moment.utc(selectionInfo.end.getTime()).subtract(1,"days");
            }
            else
            {
                var end = moment.utc(selectionInfo.end.getTime());
            }

            var new_dates_selected = false;
            while(end.isSameOrAfter(check)){
                let check_date = check.format("YYYY-MM-DD");
                if(!offDays.includes(check_date))
                {
                    new_dates_selected = true;
                }
                check.add(1,"days");
            }
            if(!new_dates_selected)
            {
                return false;
            }

            var start_date = start.format("YYYY-MM-DD");
            var end_date = end.format("YYYY-MM-DD");
            if(start_date == end_date)
            {
                var message = __('messages.are-you-sure-you-want-to-add-school-off-day-on') + " <br/> " + start_date + " ?";
            }
            else
            {
                var message = __('messages.are-you-sure-you-want-to-add-school-off-day-from-') + " <br/>" + start_date + " " + __('messages.to') +" " + end_date +" ?";
            }

            message += "<br/><span class='fa fa-exclamation-triangle text-danger'></span> " + __('messages.all-the-classes-and-reservations-on-the-selected-date(s)-will-be-cancelled')
            
            Swal.fire({
                title: trans('messages.are-you-sure'),
                html: message,
                input: 'checkbox',
                inputValue: 0,
                inputPlaceholder: __('messages.send-cancel-reservation-email-to-registered-students'),
                confirmButtonText: trans('messages.yes-i-sure'),
                cancelButtonText: trans('messages.cancel'),
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
            }).then(function (result) {
                if(!result.dismiss)
                {
                    var send_email  = result.value;

                    $('#calendar_preloader').show();
                    let data = {
                        from_date: start_date,
                        to_date: end_date,
                        send_email: send_email
                    };
                    axios.post(route('offday.add'), data)
                        .then(res => {
                            let data = res.data;
                            calendarInstance.refetchEvents();
                            toastr.success(data.message);
                            $('#calendar_preloader').hide();
                        })
                        .catch(error => {
                            toastr.error(error.response.data.message || trans('messages.something-went-wrong'));
                            $('#calendar_preloader').hide();
                        });
                }
            });
        },
        drop: function(dropInfo) {
            var momentDate = moment(dropInfo.dateStr, 'YYYY-MM-DDTHH:mm:ss');
            var date = momentDate.format('YYYY-MM-DD');
            var start_time = momentDate.format('HH:mm');
            if(!offDays.includes(date))
            {
                var class_id = $(dropInfo.draggedEl).attr('class-id');
                var default_course_id = $(dropInfo.draggedEl).attr('class-default_course_id');

                // calculate end_time from class length
                var class_length = $(dropInfo.draggedEl).attr('class-length');
                var temp = class_length.split(':');
                var class_length_in_min = parseInt(temp[0]) * 60 + parseInt(temp[1]);
                var end_time = momentDate.add(class_length_in_min,'minutes').format("HH:mm");

                $('#DropEventModal').modal('show');
                $('#DropEventModal input[name="class_id"]').val(class_id);
                $('#DropEventModal input[name="date"]').val(date);
                $('#DropEventModal input[name="start_time"]').val(start_time);
                $('#DropEventModal input[name="end_time"]').val(end_time);
                $('#DropEventModal select[name="course_id"]').val(default_course_id);

                // Make one off class selected bydefault and trigger change to show / hide date fields.
                $('#DropEventModal input[name="type"]').filter('[value=1]').prop('checked', true).trigger("change");

                if($('input[name="calendar_teachers"]:checked').not('.hidden').length > 0)
                {
                    $('#DropEventModal select[name="teacher_id"]').val($('input[name="calendar_teachers"]:not(".hidden"):checked:first').val());
                }
            }
        },
        eventClick: function(info) {
            var event = info.event;
            var jsEvent = info.jsEvent;

            if(event.extendedProps.isBirthdayEvent == 1)
            {
                jsEvent.preventDefault();
                return;
            }
            else if(event.extendedProps.isSchoolOffDayEvent)
            {
                if(manage_school_off_days == 1)
                {
                    lastClickedEvent = event;
                    var start = moment.utc(event.start.getTime()).format("YYYY-MM-DD");

                    var message = __('messages.are-you-sure-you-want-to-remove-school-off-day-on') + " " + start + " ?";
                    Swal.fire({
                        title: trans('messages.are-you-sure'),
                        text: message,
                        confirmButtonText: trans('messages.yes-i-sure'),
                        cancelButtonText: trans('messages.cancel'),
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#3085d6',
                        cancelButtonColor: '#d33',
                    }).then(function (result) {
                        if (result.value) {
                            $('#calendar_preloader').show();
                            let data = {
                                date: start
                            };
                            axios.post(route('offday.delete'), data)
                                .then(res => {
                                    let data = res.data;
                                    calendarInstance.refetchEvents();
                                    calendarInstance.render();
                                    toastr.success(data.message);
                                    $('#calendar_preloader').hide();
                                })
                                .catch(error => {
                                    toastr.error(error.response.data.message || trans('messages.something-went-wrong'));
                                    $('#calendar_preloader').hide();
                                });
                        }
                    });
                }
            }
            else
            {
                lastClickedEvent = event;

                var title = event.title;
                var start = moment.utc(event.start.getTime()).format("YYYY-MM-DD");
                var modal = $('#EventModal').modal('show');

                modal.find('.modal-body').load(scheduleUrl+'?schedule_id='+event.extendedProps.ID+'&date='+start+'&view=body', function(){
                    attachVueApp();

                    if($('#schedule-zoom-meeting-container').length > 0)
                    {
                        new Vue({
                            'el': '#schedule-zoom-meeting-container',
                            components: {
                                'app-schedule-zoom-meeting': ScheduleZoomMeeting
                            }
                        });
                    }

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
                    offDays = [];
                    response.events.forEach(function(event){
                        if(event.isSchoolOffDayEvent){
                            offDays.push(event.start);
                        }
                    });
                    $('.school-off-day-all-day').removeClass('school-off-day-all-day');

                    $('input[name="calendar_teachers"]').each(function(){
                        if(!response.display_teachers.includes(parseInt($(this).val())))
                        {
                            $(this).addClass('hidden');
                        }
                        else
                        {
                            $(this).removeClass('hidden');
                        }
                        update_custom_toggle_button_ui($(this));
                    });
                    
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
        loading: function(isLoading) {
            if (isLoading) {
                $('#calendar_preloader').show();
            } else {
                $('#calendar_preloader').hide();
            }
        },
        eventRender: function (info) {
            var event = info.event;
            var element = $(info.el);

            const teachers = $('input[name="calendar_teachers"]:checked').map(function() {
                return parseInt($(this).val());
            }).get();
            const selectedClasses = $('#student_levels').val();
            const showEmptyClass = $('input[name="show_empty_class"]').is(':checked');
            const showBirthdayEvents = $('input[name="show_birthday_events"]').is(':checked');

            var event_date = moment.utc(event.start.getTime()).format('YYYY-MM-DD');

            // Add classes to event
            if(event.extendedProps.isEvent){ element.addClass("is-event");}
            if(event.extendedProps.isBirthdayEvent){ element.addClass("birthday-event"); }
            if(event.extendedProps.isStudentRegistered){ element.addClass("student-registered"); }
            if(event.extendedProps.isEmpty){ element.addClass("empty-class"); }
            if(event.extendedProps.isWaitlisted){ element.addClass("waitlisted-class"); }
            if(event.extendedProps.greyedPastClass) { element.addClass("greyedPastClass"); }
            if(event.extendedProps.isSchoolOffDayEvent){ element.addClass("school-off-day-event"); }

            if(event.allDay && !event.extendedProps.isBirthdayEvent && !event.extendedProps.isSchoolOffDayEvent)
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

            // show hide events based on condtions
            if(event.extendedProps.isBirthdayEvent == 1)
            {
                if(showBirthdayEvents)
                {
                    return true;
                }
                else
                {
                    return false;
                }
            }

            if(showEmptyClass)
            {
                // Show only empty classes
                // igonre teacher and level filters
                if(event.extendedProps.isEmpty)
                {
                    return true;
                }
                else
                {
                    return false;
                }
            }
            else
            {
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

    $(document).on('click','.btn_submit_form',function(){
        $('#reservation_alert').hide();
        $('#reservation_alert_danger').hide();
        $('#reservation_alert_warning').hide();
        var button = $(this);
        if($(button).attr('name') == 'signin_btn')
        {
            submit_form(button);
        }
        else
        {
            var swal_text = trans('messages.you-wont-be-able-to-revert-this');
            var swal_cancel_button_text = trans('messages.cancel');
            var form = button.closest('form');
            if(form.hasClass('cancel-reservation-form'))
            {
                var cancel_future_reservations = button.hasClass('cancel_future_reservations') ? 1 : 0;
                if(cancel_future_reservations == 1)
                {
                    swal_text = trans('messages.are-you-sure-you-wants-to-cancel-all-future-resrvations-includding-current-reservation-of-this-student-and-this-class');
                    swal_cancel_button_text = trans('messages.no');
                }
                form.find('input[name="cancel_future_reservations"]').val(cancel_future_reservations);
            }

            var send_email = 0;
            if($('#send_email_for_actions').is(':checked'))
            {
                send_email = 1;
            }
            form.find('input[name="send_email"]').val(send_email);

            Swal.fire({
                title: trans('messages.are-you-sure'),
                text: swal_text,
                confirmButtonText: trans('messages.yes-i-sure'),
                cancelButtonText: swal_cancel_button_text,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
            }).then(function (result) {
                if (result.value) {
                    submit_form(button);
                }
            });
        }
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

        var send_email = $('#send_email_for_actions').is(':checked') ? 1 : 0;
        form  = form + '&' + $.param({ 'send_email': send_email });

        $.ajax({
            url:reservationUrl,
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

                    if(data.full) {
                        $(".modal-footer.monthly-calendar #reserve_now").hide();
                        $(".modal-footer.monthly-calendar #waitlist_now").show();
                    }

                    refetchEvent();

                    if(data.yoyaku !== null) {
                        var yoyaku = data.yoyaku;
                        var yoyaku_id = yoyaku.yoyaku_id || yoyaku.id;

                        $.ajax({
                            url: studentRawUrl,
                            method: "GET",
                            data: { yoyaku_id: yoyaku_id },
                            success: function(response_1){
                                $('table.registered-students').append(response_1.row_html);
                                attachVueApp();
                            },
                            error: function(){
                                Swal.fire({
                                    text: trans('messages.could-not-update-ui-refresh-page'),
                                    icon: 'warning',
                                    showCancelButton: false,
                                    confirmButtonColor: '#3085d6',
                                    cancelButtonColor: '#d33',
                                });
                            }
                        });
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

    $(document).delegate("#cancel_class","click", function() {
        var form = $(this).closest('form');
        Swal.fire({
            title: __('messages.are-you-sure-you-want-to-cancel-class?'),
            text: __('messages.all-reservations-for-this-class-will-also-be-cancelled-and-it-cant-be-reverted!'),
            input: 'checkbox',
            inputValue: 0,
            inputPlaceholder: __('messages.send-cancel-reservation-email-to-registered-students'),
            confirmButtonText: trans('messages.yes-i-sure'),
            cancelButtonText: trans('messages.cancel'),
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
        }).then(function (result) {
            if(!result.dismiss)
            {
                var send_email  = result.value;
                form.find('input[name="send_email"]').val(send_email);
                form.submit();
            }
        });
    });

    // Cancel Multiple Functionality
    $(document).delegate("#cancel_multiple","click", function() {
        var scheduleId = $(this).closest('form').find("input[name='schedule_id']").val();
        var eventmodal = $('#EventModal').modal('hide');
        var modal = $('#CancelMultipleModal').modal('show');
        modal.find('.modal-body').load(cancelMultipleModalUrl+'?schedule_id='+scheduleId);
    });

    $(document).delegate("#cancel_selected","click", function() {
        $('#reservation_alert').hide();
        $('#reservation_alert_danger').hide();
        $('#reservation_alert_warning').hide();

        if($('.cancel_multiple_checkbox:checked').length == 0)
        {
            $('#reservation_alert_danger').text(trans('messages.select-atleast-one-date')).show();
            return false;
        }

        var button = $(this);

        Swal.fire({
            title: __('messages.are-you-sure-you-want-to-cancel-classes-on-selected-dates?'),
            text: __('messages.all-reservations-for-selected-class-and-dates-will-also-be-cancelled-and-it-cant-be-reverted!'),
            input: 'checkbox',
            inputValue: 0,
            inputPlaceholder: __('messages.send-cancel-reservation-email-to-registered-students'),
            confirmButtonText: trans('messages.yes-i-sure'),
            cancelButtonText: trans('messages.cancel'),
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
        }).then(function (result) {
            if(!result.dismiss)
            {
                button.attr('disabled',true);

                var send_email  = result.value;
                var data = button.closest('form').serialize();
                data  = data + '&' + $.param({ 'send_email': send_email });
                $.ajax({
                    url: cancelMultipleUrl,
                    method: 'POST',
                    data: data,
                    beforeSend: function(){
                        $('.overload-content .preload').css({"display" : "flex"});
                    },
                    success:function(data){
                        if(data.success)
                        {
                            toastr.success(data.message);
                            $('#CancelMultipleModal').modal('hide');
                            calendarInstance.refetchEvents();
                        }
                        else
                        {
                            $('#reservation_alert_danger').text(data.error || trans('messages.something-went-wrong') );
                            $('#reservation_alert_danger').show();
                        }
                        button.removeAttr('disabled');
                        $('.overload-content .preload').css({"display" : "none"});
                    },
                    error:function(e){
                        var message = trans('messages.something-went-wrong');
                        Swal.fire({
                            text: message,
                            icon: 'warning',
                            showCancelButton: false,
                            confirmButtonColor: '#3085d6',
                            cancelButtonColor: '#d33',
                        });
                        button.removeAttr('disabled');
                    }
                });
            }
        });
    });

    $(document).on('click','#cancel_multiple_select_all', function(){
        $('.cancel_multiple_checkbox').attr('checked', $(this).is(':checked'));
    });

    $('#CancelMultipleModal').on('hidden.bs.modal', function(){
        $('#CancelMultipleModal .modal-body').html('<div class="text-center"><div class="fa fa-spinner fa-spin" style="font-size:100px"></div></div>');
    });

    // For Custom Dropdown
    $(document).on('click','.custom-dropdown .option-item', function(){
        makeOptionSelected($(this));
    });
    $(document).on('click','.custom-dropdown .selected_value_text', function(){
        if(!isDropdownActive)
        {
            makeDropDownActive();
        }
        else
        {
            makeDropDownInvactive();
        }
    });
    $(document).on('keypress','.custom-dropdown .search_input', function(e){
        if (e.which == 13) {
            var element = $('.custom-dropdown .option-item.visible:first');
            if(element.length > 0)
            {
                makeOptionSelected(element);
            }
            return false;
        }
    });
    // Stoping auto close on blur as behaviour is conflicting with keyboard shortcut.
    // $(document).on('blur','.custom-dropdown .search_input', function(e){
    //     // timeout is required here to allow clicked item being selected.
    //     setTimeout(() => {
    //         makeDropDownInvactive();
    //     }, 500);
    // });

    $('#DropEventModal input[name="type"]').change(function() {
        var inputValue = $('#DropEventModal input[name="type"]:checked').val();
        if(inputValue == 0) {
            $('#schedule-date').show();
            $('#schedule-date input[type="date"]').attr('required', true);
        } else {
            $('#schedule-date').hide();
            $('#schedule-date input[type="date"]').removeAttr('required');
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

        var send_email = $('#send_email_for_actions').is(':checked') ? 1 : 0;
        form  = form + '&' + $.param({ 'send_email': send_email });
        
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

                    if(data.yoyaku !== null) {
                        var yoyaku = data.yoyaku;
                        $.ajax({
                            url: studentWatilistRawUrl,
                            method: "GET",
                            data: { yoyaku_id: yoyaku.yoyaku_id },
                            success: function(response_1){
                                $('table.waitlisted-students').append(response_1.row_html);
                                attachVueApp();
                            },
                            error: function(){
                                Swal.fire({
                                    text: trans('messages.could-not-update-ui-refresh-page'),
                                    icon: 'warning',
                                    showCancelButton: false,
                                    confirmButtonColor: '#3085d6',
                                    cancelButtonColor: '#d33',
                                });
                            }
                        });
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

    // For Edit schedule functionality
    $(document).delegate("#edit_schedule","click", function() {
        $('#EventModal').modal('hide');
        vm1.schedule_id = $(this).data('id');
        vm1.date = $(this).data('date');
        vm1.edit_schedule = true;
    });

    $(document).delegate('#btn_add_contact','click', function(){
        vm2.add_contact = true;
    });
});

function submit_form(button)
{
    var form = button.closest('form');
    var yoyaku_id = form.find("input[name='yoyaku_id']").val();

    $.ajax({
        url: form.attr('action'),
        type: form.attr('method'),
        data: form.serialize(),
        beforeSend: function(){
            $('.overload-content .preload').css({"display" : "flex"});
        },
        success: function(response){
            if(form.hasClass('cancel-reservation-form'))
            {
                response.status = response.success == true ? 1 : 0;
                response.message = response.success == true ? response.message : response.error;
            }
            if(response.status == 1)
            {
                refetchEvent();

                $('#reservation_alert').text(response.message);
                $('#reservation_alert').show();

                if(response.warning != undefined)
                {
                    $('#reservation_alert_warning').text(response.warning);
                    $('#reservation_alert_warning').show();
                }

                if(form.hasClass('cancel-reservation-form'))
                {
                    $(".modal-footer.monthly-calendar #reserve_now").show();
                    $(".modal-footer.monthly-calendar #waitlist_now").hide();
                }

                $.ajax({
                    url: studentRawUrl,
                    method: "GET",
                    data: { yoyaku_id: yoyaku_id },
                    success: function(response_1){

                        button.closest('tr').after(response_1.row_html);
                        button.closest('tr').remove();

                        $('#reservation_alert').text(response.message);
                        $('#reservation_alert').show();

                        if(response.warning != undefined)
                        {
                            $('#reservation_alert_warning').text(response.warning);
                            $('#reservation_alert_warning').show();
                        }

                        if(form.hasClass('cancel-reservation-form'))
                        {
                            $(".modal-footer.monthly-calendar #reserve_now").show();
                            $(".modal-footer.monthly-calendar #waitlist_now").hide();
                        }
                        $('.overload-content .preload').css({"display" : "none"});

                        attachVueApp();
                    },
                    error: function(){
                        $('#reservation_alert').text(response.message);
                        $('#reservation_alert').show();
                        if(response.warning != undefined)
                        {
                            $('#reservation_alert_warning').text(response.warning);
                            $('#reservation_alert_warning').show();
                        }
                        Swal.fire({
                            text: trans('messages.could-not-update-ui-refresh-page'),
                            icon: 'warning',
                            showCancelButton: false,
                            confirmButtonColor: '#3085d6',
                            cancelButtonColor: '#d33',
                        });
                    }
                });
            }
            else
            {
                message = response.message || trans('messages.something-went-wrong');
                Swal.fire({
                    text: message,
                    icon: 'warning',
                    showCancelButton: false,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                });

                $('.overload-content .preload').css({"display" : "none"});
            }
        },
        error: function(e){
            Swal.fire({
                text: trans('messages.something-went-wrong'),
                icon: 'warning',
                showCancelButton: false,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
            });
        }
    });
}

// For custom dropdown
function initializeCustomDropdown()
{
    var element = $('.custom-dropdown .option-item.visible:first');
    makeOptionSelected(element);
}
function filterFunction()
{
    var filter = $('.custom-dropdown .search_input').val().toUpperCase();
    $('.custom-dropdown .option-item').each(function(){
        if($(this).text().toUpperCase().indexOf(filter) > -1)
        {
            $(this).addClass('visible');
        }
        else
        {
            $(this).removeClass('visible');
        }
    });
}
function makeOptionSelected(element)
{
    $('.custom-dropdown .option-item').removeClass('active');
    element.addClass('active');
    $('#selected_student_id').val(element.data('id'));
    $('.custom-dropdown .selected_value_text').text(element.text());
    makeDropDownInvactive();
}

function makeDropDownInvactive()
{
    $('.custom-dropdown .search_input').hide();
    $('.custom-dropdown .option-item').removeClass('visible');
    $('.options-section').hide();
    isDropdownActive = 0;
}

function makeDropDownActive()
{
    $('.custom-dropdown .search_input').val("").show().focus();
    $('.custom-dropdown .option-item').addClass('visible');
    $('.options-section').show();
    isDropdownActive = 1;
}

function isMobile()
{
    return $(window).width() < 826;
}

function attachVueApp()
{
    $('.attach-vue').each(function(i, element){
        $(element).removeClass('attach-vue');
        new Vue({
            'el': element
        });
    });
}
