window.addEventListener('DOMContentLoaded', function() {
    (function($) {
        $(".collumn_sort[data-collumn_name='"+_sort_field+"']").addClass(_sort_dir);
        $('.collumn_sort').click(function(){
            sort_dir = $(this).hasClass('asc') ? 'desc' : 'asc';
            $('#filter_form #sort_field').val($(this).data('collumn_name'));
            $('#filter_form #sort_dir').val(sort_dir);
            $('#filter_form').submit();
        });

        $('#role_filter').change(function(){
            $('#filter_form #role_id').val($(this).val());
            $('#filter_form').submit();
        });

        $('[data-toggle="popover"]').popover({
            html: true,
            trigger: 'hover',
            content: function () {
                return '<img src="'+$(this).data('img') + '" style="max-width:300px;"/>';
            }
        });
    })(jQuery);

    $('.btn_add_contact').click(function(){
        $('#add_contact_modal').find('form')[0].reset();
        $('#add_contact_modal').find('.modal-title').text($(this).data('modal_title'));
        $('#add_contact_modal').find('#customer_id').val($(this).data('student_id'));
        $('#add_contact_modal').modal('show');
    });

    $('.btn_reconfirm').click(function(){
        $('#reconfirm_form').attr('action',reconfirm_url + '/' + $(this).data('user_id')).submit();
    });

    $('.btn_force_verify').click(function(){
        $('#reconfirm_form').attr('action',force_verify_url + '/' + $(this).data('user_id')).submit();
    });


    $('.btn_archive_student').click(function(){
        button = $(this);
        Swal.fire({
            title: trans('messages.are-you-sure'),
            text: __('messages.are-you-sure-you-want-to-change-student-role-to-archived-student-?'),
            confirmButtonText: trans('messages.yes-i-sure'),
            cancelButtonText: trans('messages.cancel'),
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
        }).then(function (result) {
            if (result.value) {
                var student_id = button.data('student_id');
                button.attr('disabled', true);

                axios.post(route('student.archive', student_id).url())
                    .then(res => {
                        let data = res.data;
                        if (data.status == 1) {
                            
                            toastr.success(data.message);
                            
                            // Update local row
                            if($('#role_filter').val() == 'all')
                            {
                                button.hide();
                                button.closest('tr').find('.role-col').text(data.applied_role);
                            }
                            else
                            {
                                button.closest('tr').remove();
                            }

                        } else {
                            toastr.error(data.message || trans('messages.something-went-wrong'));
                        }
                        button.removeAttr('disabled');
                    })
                    .catch(error => {
                        toastr.error(error.response.data.message || trans('messages.something-went-wrong'));
                        button.removeAttr('disabled');
                    });
            }
        });
    });
});
