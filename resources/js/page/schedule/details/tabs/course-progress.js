window.addEventListener('DOMContentLoaded', function() {

    $(document).on('click','.lesson_exercise_cb',function(){
        var element = $(this);
        var is_complete = element.prop('checked') ? 1 : 0;
        if(!is_complete)
        {
            Swal.fire({
                text: trans('messages.are-you-sure-you-wants-to-mark-exercise-as-incomplete?'),
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                showCancelButton: true,
                confirmButtonText: trans('messages.yes-mark-as-incomplete'),
                cancelButtonText: trans('messages.no')
            }).then(function(result){
                if(result.value)
                {
                    update_exercise_status(element, is_complete);
                }
            });
        }
        else
        {
            update_exercise_status(element, is_complete);
        }
        return false;
    });
    function update_exercise_status(element, is_complete)
    {
        var data = {
            lesson_exercise_id: element.data('lesson_exercise_id'),
            schedule_id:  element.data('schedule_id'),
            _token: csrf_token,
            is_complete: is_complete
        };

        $.ajax({
            url: route('lesson.update_exercise_status'),
            type : 'POST',
            data: data,
            success: function(response){
                if(response.status != 1)
                {
                    var message = response.message || trans('messages.something-went-wrong')
                    Swal.fire({
                        text: message,
                        icon: 'warning',
                        showCancelButton: false,
                        confirmButtonColor: '#3085d6',
                        cancelButtonColor: '#d33',
                    });
                }
                else
                {
                    element.prop('checked', is_complete);
                    element.parents('.lesson_exercise_row').find('.status_line_text').text(response.status_line);
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

    $(document).on('click','.lesson_homework_cb',function(){
        var element = $(this);
        var is_complete = element.prop('checked') ? 1 : 0;
        if(!is_complete)
        {
            Swal.fire({
                text: trans('messages.are-you-sure-you-wants-to-mark-homework-as-incomplete?'),
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                showCancelButton: true,
                confirmButtonText: trans('messages.yes-mark-as-incomplete'),
                cancelButtonText: trans('messages.no')
            }).then(function(result){
                if(result.value)
                {
                    update_homework_status(element, is_complete);
                }
            });
        }
        else
        {
            update_homework_status(element, is_complete);
        }
        return false;
    });
    function update_homework_status(element, is_complete)
    {
        var data = {
            lesson_homework_id: element.data('lesson_homework_id'),
            schedule_id:  element.data('schedule_id'),
            _token: csrf_token,
            is_complete: is_complete
        };

        $.ajax({
            url: route('lesson.update_homework_status'),
            type : 'POST',
            data: data,
            success: function(response){
                if(response.status != 1)
                {
                    var message = response.message || trans('messages.something-went-wrong')
                    Swal.fire({
                        text: message,
                        icon: 'warning',
                        showCancelButton: false,
                        confirmButtonColor: '#3085d6',
                        cancelButtonColor: '#d33',
                    });
                }
                else
                {
                    element.prop('checked', is_complete);
                    element.parents('.lesson_homework_row').find('.status_line_text').text(response.status_line);
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

    $('.btn_save_comments').click(function(){
        var container = $(this).closest('td');
        var element = container.find(".commments_text_area");
        var data = {
            'comments' : element.val(),
            'schedule_id': element.data('schedule_id'),
            'lesson_id': element.data('lesson_id'),
            _token: csrf_token,
        };

        container.find('.prelaoder').show();

        $.ajax({
            url: route('schedule.lesson.comments'),
            type : 'POST',
            data: data,
            success: function(response){
                container.find('.prelaoder').hide();
                if(response.status != 1)
                {
                    var message = response.message || trans('messages.something-went-wrong')
                    Swal.fire({
                        text: message,
                        icon: 'warning',
                        showCancelButton: false,
                        confirmButtonColor: '#3085d6',
                        cancelButtonColor: '#d33',
                    });
                }
                else
                {
                    container.find('.comments_status_line').text(response.comments_status_line);
                }
            },
            error: function(e){
                container.find('.prelaoder').hide();
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
});
