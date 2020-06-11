window.addEventListener('DOMContentLoaded', function() {

    $('[data-toggle="tooltip"]').tooltip();
    $('.drr_student_id').select2({ width: '100%'  });
    $('.drr_view_email_preview_btn').click(function(){
        var section = $(this).closest('.preview_email_section');
        var student_id = section.find('.drr_student_id').val();
        var date = section.find('.drr_date').val();
        var lang = $(this).data('lang');
        var class_reminder = section.find('input[name=class_reminder]').prop('checked');
        var event_reminder = section.find('input[name=event_reminder]').prop('checked');
        var url = route('preview_drr_email') + "?student_id=" + student_id + "&date=" + date + "&lang=" + lang + "&class_reminder=" + class_reminder + "&event_reminder=" + event_reminder;
        window.open(url, '_blank');
    });

    $('#notification-status-form').submit(function(e) {
        e.preventDefault()

        const form = $(this)
        const submitButton = form.find('input[type="submit"]')
        submitButton.attr('disabled', true)
        
        data = form.serialize()
        axios.post(route('notification-status.save'),data) .then(res => {
            let data = res.data;
            if(data.status == 1) {
                toastr.success(data.message)
            } else {
                toastr.error(data.message || trans('messages.something-went-wrong'))
            }
            submitButton.removeAttr('disabled')
        })
        .catch(error => {
            toastr.error(error.response.data.message || trans('messages.something-went-wrong'))
            submitButton.removeAttr('disabled')
        });
    })

    $('.notification-text-form').submit(function(e) {
        e.preventDefault()

        const form = $(this)
        const submitButton = form.find('input[type="submit"]')
        submitButton.attr('disabled', true)

        data = form.serialize()
        axios.post(route('notification-text.save'),data) .then(res => {
            let data = res.data;
            if(data.status == 1) {
                toastr.success(data.message)
            } else {
                toastr.error(data.message || trans('messages.something-went-wrong'))
            }
            submitButton.removeAttr('disabled')
        })
        .catch(error => {
            if(error.response.status == 422)
            {
                const data = error.response.data;
                var errorMessage = '';
                const form_errors = data.errors;

                $.each(form_errors, function(key ,errors) {
                    errors.forEach((fieldError) => {
                        errorMessage += fieldError + '<br>';
                    })
                })
                
                Swal.fire({
                    title: data.message,
                    html: errorMessage,
                    icon: 'error',
                    showCancelButton: false,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                });
            }
            else
            {
                toastr.error(error.response.data.message || trans('messages.something-went-wrong'))
            }
            submitButton.removeAttr('disabled')
        });
    });
    $('.toggle-group').click( function() {
        if (!$(this).parent().find('input').prop('checked')) {
            $('.' + ($(this).parent().find('input').attr('name')=='class_reminder'?'btn_cancel-class-reservation':'btn_cancel-event-reservation')).removeClass('hide');
        } else { 
            $('.' + ($(this).parent().find('input').attr('name')=='class_reminder'?'btn_cancel-class-reservation':'btn_cancel-event-reservation')).addClass('hide');
        }
    });
});