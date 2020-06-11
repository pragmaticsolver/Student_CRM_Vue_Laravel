import DailyStats from '../../js/components/stats/DailyStats.vue';

var signed_in_status = [];
var selected_teachers = [];

window.addEventListener('DOMContentLoaded', function() {

    // keep all filters selected when login
    if(localStorage.getItem('homepage_selected_teachers') == null)
    {
        selected_teachers = [];
        $('input[name="teachers_filter"]').each(function(){
            selected_teachers.push($(this).val());
        });
    }
    else
    {
        var local_stored_teachers = localStorage.getItem('homepage_selected_teachers').split(',');

        var all_teachers = [];
        $('input[name="teachers_filter"]').each(function(){
            all_teachers.push($(this).val());
        });

        selected_teachers = [];
        $.each(local_stored_teachers , function(key, teacher_id){
            if(all_teachers.includes(teacher_id))
            {
                selected_teachers.push(teacher_id);
            }
        });
    }

    if(localStorage.getItem('homepage_signed_in_status') == null)
    {
        signed_in_status = [];
        $('input[name="signed_in"]').each(function(){
            signed_in_status.push($(this).val());
        });
    }
    else
    {
        signed_in_status = localStorage.getItem('homepage_signed_in_status').split(',');
    }

    const vm = new Vue({
        el: '#vue-app',
        components: {
            'app-daily-stats' : DailyStats
        },
        data: {
            selected_teachers: selected_teachers
        }
    });

    (function($) {
        show_hide_rows();
        row_inserted();
        $('#main_section').fadeIn();

        // Form submit handlers for sign in and cancel buttons
        $(document).on('click','.btn_submit_form',function(){
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
                if(form.hasClass('delete-reservation-form'))
                {
                    var delete_future_reservations = button.hasClass('delete_future_reservations') ? 1 : 0;
                    if(delete_future_reservations == 1)
                    {
                        button = $(this).closest('td').find('.btn_delete_main'); // set button to main button element to show preloader on main button
                        swal_text = trans('messages.are-you-sure-you-wants-to-delete-all-future-resrvations-includding-current-reservation-of-this-student-and-this-class');
                        swal_cancel_button_text = trans('messages.no');
                    }
                    form.find('input[name="delete_future_reservations"]').val(delete_future_reservations);
                }
                else if(form.hasClass('cancel-reservation-form'))
                {
                    var cancel_future_reservations = button.hasClass('cancel_future_reservations') ? 1 : 0;
                    if(cancel_future_reservations == 1)
                    {
                        button = $(this).closest('td').find('.btn_cancel_main'); // set button to main button element to show preloader on main button
                        swal_text = trans('messages.are-you-sure-you-wants-to-cancel-all-future-resrvations-includding-current-reservation-of-this-student-and-this-class');
                        swal_cancel_button_text = trans('messages.no');
                    }
                    form.find('input[name="cancel_future_reservations"]').val(cancel_future_reservations);
                }

                Swal.fire({
                title: trans('messages.are-you-sure'),
                text: swal_text,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: trans('messages.yes-i-sure'),
                cancelButtonText: swal_cancel_button_text
                }).then(function (result) {
                    if (result.value) {
                        submit_form(button);
                    }
                });
            }
        });

        // Add Contact Functionality
        $(document).on('click','.add_contact_btn',function(){
            var element = $(this);
            $("#add_contact_form").trigger("reset");
            $('#cm_student_name').text(element.data('student_name'));
            $('#cm_customer_id').val(element.data('customer_id'));
            $('#submit_contact_btn').removeAttr('disabled');
            $('#form_spinner').hide();
            $('#AddContactModal').modal('show');
        });

        $('#add_contact_form').submit(function(e){

            e.stopPropagation();
            e.preventDefault();

            if(!$('#add_contact_form')[0].checkValidity())
            {
                return;
            }
            $('#submit_contact_btn').attr('disabled',true);
            $('#form_spinner').show();

            var data = {
                _token: csrf_token,
                customer_id: $('#AddContactModal #cm_customer_id').val(),
                type: $('#AddContactModal input[name="type"]:checked').val(),
                message: $('#AddContactModal #message').val()
            }

            $.ajax({
                url: route('contact.store'),
                type: 'POST',
                data: data,
                success: function(response){
                    if(response.status == 1)
                    {
                        $('#AddContactModal').modal('hide');
                        toastr.success(response.message);
                    }
                    else
                    {
                        $('#submit_contact_btn').removeAttr('disabled');
                        $('#form_spinner').hide();
                        Swal.fire({
                            text: trans('messages.something-went-wrong'),
                            icon: 'warning',
                            showCancelButton: false,
                            confirmButtonColor: '#3085d6',
                            cancelButtonColor: '#d33',
                        });
                    }
                },
                error: function(e){
                    $('#submit_contact_btn').removeAttr('disabled');
                    $('#form_spinner').hide();
                    Swal.fire({
                        text: trans('messages.something-went-wrong'),
                        icon: 'warning',
                        showCancelButton: false,
                        confirmButtonColor: '#3085d6',
                        cancelButtonColor: '#d33',
                    });
                }
            });
        });

        // Check in Funcationality
        $(document).on('click','.checkin_btn',function(){
            var element = $(this);
            $("#checkin_form").trigger("reset");
            $('#ckm_student_name').text(element.data('student_name'));
            $('#ckm_student_id').val(element.data('student_id'));
            $('#CheckinModal .submit_form_btn').removeAttr('disabled').find('.preloader').hide();
            $('#CheckinModal').modal('show');
            $('#ckm_barcode').focus();
        });

        // Checkout Functionality
        $(document).on('click','.checkout_btn',function(){
            var element = $(this);
            $("#checkout_form").trigger("reset");
            $('#ckom_student_name').text(element.data('student_name'));
            $('#ckom_student_id').val(element.data('student_id'));
            $('#CheckoutModal .submit_form_btn').removeAttr('disabled').find('.preloader').hide();
            $('#CheckoutModal').modal('show');
            $('#ckom_barcode').focus();
        });

        // Form submission handler for check in and checkout form (in modal)
        $('.modal_form').submit(function(e){
            e.stopPropagation();
            e.preventDefault();

            var form = $(this);
            var modal = form.closest('.modal');
            var submit_btn = modal.find('.submit_form_btn');
            var preloader = submit_btn.find('.preloader');
            if(!form[0].checkValidity())
            {
                return;
            }

            submit_btn.attr('disabled',true);
            preloader.show();

            $.ajax({
                url: form.attr('action'),
                type: form.attr('method'),
                data: form.serialize(),
                success: function(response){
                    if(response.status == 1)
                    {
                        modal.modal('hide');
                        toastr.success(response.message);
                    }
                    else
                    {
                        var message = response.message || trans('messages.something-went-wrong');
                        Swal.fire({
                            text: message,
                            icon: 'warning',
                            showCancelButton: false,
                            confirmButtonColor: '#3085d6',
                            cancelButtonColor: '#d33',
                        });

                        submit_btn.removeAttr('disabled');
                        preloader.hide();
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
                    submit_btn.removeAttr('disabled');
                    preloader.hide();
                }
            });
        });


        $('input[name="teachers_filter"]').each(function(){
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

        $('input[name="teachers_filter"]').on('change',function(){
            selected_teachers = [];
            $('input[name="teachers_filter"]:checked').each(function(){
                selected_teachers.push($(this).val());
            });
            localStorage.setItem('homepage_selected_teachers', selected_teachers);
            update_custom_toggle_button_ui($(this));
            show_hide_rows();
            vm.selected_teachers = selected_teachers;
        });


        $('input[name="signed_in"]').each(function(){
            if(signed_in_status.includes($(this).val()))
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

        $('input[name="signed_in"]').on('change',function(){
            signed_in_status = [];
            $('input[name="signed_in"]:checked').each(function(){
                signed_in_status.push($(this).val());
            });
            localStorage.setItem('homepage_signed_in_status', signed_in_status);
            update_custom_toggle_button_ui($(this));
            show_hide_rows();
        });

        $('input[name="send_email_for_actions"]').each(function(){
            update_custom_toggle_button_ui($(this));
        });
        $('input[name="send_email_for_actions"]').on('change',function(){
            update_custom_toggle_button_ui($(this));
        });

    })(jQuery);
});

function show_hide_rows()
{
    $("#main_table tr").not(':first').not('.time-row').each(function(){
        var techer_pass = false;
        var status_pass = false;

        if($(this).data('teacher_id') == undefined)
        {
            techer_pass = true;
        }
        else if(selected_teachers.includes($(this).data('teacher_id').toString())){
            techer_pass = true;
        }

        if($(this).data('sigend_id') == undefined)
        {
            status_pass = true;
        }
        else if(signed_in_status.includes($(this).data('sigend_id').toString()))
        {
            status_pass = true;
        }

        if(techer_pass && status_pass)
        {
            $(this).show();
        }
        else
        {
            $(this).hide();
        }
    });


    $('#main_table .schedule-row').each(function(){
        var rows = $("#main_table tr[data-schedule_id='"+ $(this).data('schedule_id') +"']").not('.schedule-row');
        var visible = false;
        rows.each(function(i, element){
            if($(element).css('display') != 'none')
            {
                visible = true;
                return false;
            }
        });
        if(visible)
        {
            $(this).show();
        }
        else
        {
            $(this).hide();
        }
    });

    $("#main_table .time-row").each(function(){
        var rows = $("#main_table tr[data-time='"+ $(this).data('time') +"']").not('.time-row');
        var visible = false;
        rows.each(function(i, element){
            if($(element).css('display') != 'none')
            {
                visible = true;
                return false;
            }
        });
        if(visible)
        {
            $(this).show();
        }
        else
        {
            $(this).hide();
        }
    });
}

function row_inserted()
{
    reInitializeToolitip();
    $('.attach-vue').each(function(i, element){
        $(element).removeClass('attach-vue');
        new Vue({
            'el': element
        });
    });
}

function reInitializeToolitip()
{
    $('[data-toggle="popover"]').popover({
        html: true,
        trigger: 'hover',
        content: function () {
            return '<img src="'+$(this).data('img') + '" style="max-width:300px;"/>';
        }
    });
}

function submit_form(button)
{
    button.find('.preloader').show();
    button.attr('disabled', true);
    var form = button.closest('form');
    var form_data = form.serialize();
    
    var send_email = $('#send_email_for_actions').is(':checked') ? 1 : 0;
    form_data  = form_data + '&' + $.param({ 'send_email': send_email });

    $.ajax({
        url: form.attr('action'),
        type: form.attr('method'),
        data: form_data,
        success: function(response){
            if(form.attr('id') == 'reservation_form' || form.attr('id') == 'cancel_waitlist_form')
            {
                response.status = response.success == true ? 1 : 0;
                response.message = response.success == true ? response.message : response.error;
            }
            if(response.status == 1)
            {
                if(form.attr('id') == 'reservation_form')
                {
                    $.ajax({
                        url: route("student.row"),
                        method: "GET",
                        data: { yoyaku_id: response.yoyaku.yoyaku_id },
                        success: function(response_1){
                            button.find('.preloader').hide();
                            button.removeAttr('disabled');

                            button.closest('tr').after(response_1.row_html);
                            button.closest('tr').remove();
                            show_hide_rows();
                            row_inserted();
                            toastr.success(response.message);
                            if(response.warning != undefined)
                            {
                                toastr.warning(response.warning);
                            }
                        },
                        error: function(){
                            toastr.success(response.message);
                            if(response.warning != undefined)
                            {
                                toastr.warning(response.warning);
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
                    button.find('.preloader').hide();
                    button.removeAttr('disabled');

                    if(response.row_html != undefined)
                    {
                        button.closest('tr').after(response.row_html);
                        row_inserted();
                    }
                    button.closest('tr').remove();
                    show_hide_rows();
                    toastr.success(response.message);
                }
            }
            else
            {
                var message = response.message || trans('messages.something-went-wrong');
                Swal.fire({
                    text: message,
                    icon: 'warning',
                    showCancelButton: false,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                });

                button.find('.preloader').hide();
                button.removeAttr('disabled');
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

            button.find('.preloader').hide();
            button.removeAttr('disabled');
        }
    });
}

window.onTodoHTMlUpdated = function()
{
    reInitializeToolitip();
}
