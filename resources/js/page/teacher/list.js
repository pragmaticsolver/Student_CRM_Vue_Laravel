var is_archive_confirmed = false;
window.addEventListener('DOMContentLoaded', function(){
    $('.btn_archive_teacher').click(function(){

        var btn = $(this);
        var teacher_id = btn.data('teacher_id');
        var teacher_name = btn.data('teacher_name');

        $('#DropEventModal input[name="teacher_id"]').val(teacher_id);
        $('#DropEventModal #current_teacher_name').text(teacher_name);

        $('#DropEventModal select[name="take_over_teacher_id"] option').each(function(i, element){
            option_elment = $(element);
            if(option_elment.val() == teacher_id)
            {
                option_elment.attr('disabled', true);
            }
            else
            {
                option_elment.removeAttr('disabled');
            }
        });
        $('#DropEventModal select[name="take_over_teacher_id"]').val("");

        $('#DropEventModal input[name="take_over_date"]').val(today_date);
        $('#DropEventModal').modal('show');
        is_archive_confirmed = false;
    });

    $('#archive_teacher_form').submit(function(e){
        if(!is_archive_confirmed)
        {
            e.preventDefault();

            Swal.fire({
                title: trans('messages.are-you-sure'),
                html: __('messages.are-you-sure-you-want-to-archive-teacher?') + " <br/> " + __('messages.you-wont-be-able-to-revert-this'),
                confirmButtonText: trans('messages.yes-i-sure'),
                cancelButtonText: trans('messages.cancel'),
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
            }).then(function (result) {
                if (result.value) {
                    is_archive_confirmed = true;
                    $('#archive_teacher_form').attr('action',route('teacher.archive')).submit();
                }
            });
        }
    });
});