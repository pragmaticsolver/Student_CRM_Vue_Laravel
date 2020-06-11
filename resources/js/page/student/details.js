window.addEventListener('DOMContentLoaded', function() {
    (function($) {
        // Update nav paramter in url to display same tab that was lat opened.
        $('.sticky_tabs_container .nav-link').click(function(){
            var nav = $(this).attr('href').replace("#",'');
            var url = new URL(window.location.href);
            var query_string = url.search;
            var search_params = new URLSearchParams(query_string);
            search_params.delete('nav');
            search_params.append('nav', nav);
            url.search = search_params.toString();
            var new_url = url.toString();
            history.replaceState(null, null, new_url);
        });
    })(jQuery);

    if($('#vue-app').length)
    {
        const vm = new Vue({
            el: '#vue-app'
        });
        vm.$eventBus.display_expanded_tags = true;
    }

    $('.btn_mark_as_paid').click(function(){
        $('#mark_as_paid_modal input[name="id"]').val($(this).data('id'));
        $('#mark_as_paid_modal').modal("show");
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
                $('#archive_student_form').attr('action',route('student.archive',student_id)).submit();
            }
        });
    });

    $(".student-notes").keypress(function (e) {
        if(e.which == 13 && !e.shiftKey) {        
            $(this).closest("form").submit();
        }
        $('#notes_changed').val('1');
    });
    $(".student-notes").blur(function (e) {
        $(this).closest("form").submit();
    });

    $('.ajax').submit(function(e) {

        /* stop form from submitting normally */
        e.stopPropagation();
        e.preventDefault();
        var url = $(this).attr('action');
        $.ajaxSetup({
             headers: {
                 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
             }
        });
        if(!$(this)[0].checkValidity())
        {
            return;
        }
        $(this).find('.submit').attr('disabled',true);
        
        var formData = new FormData($(this)[0]);

        $.ajax({
            url: url,
            type: 'POST',
            data: formData,
            contentType: false,
            processData: false,
            success: function(response){
                if(response.status == 1)
                {
                    toastr.success(response.message);
                    $('#notes_changed').val('0');
                }
                else
                {
                    Swal.fire({
                        text: trans('messages.something-went-wrong'),
                        icon: 'warning',
                        showCancelButton: false,
                        confirmButtonColor: '#3085d6',
                        cancelButtonColor: '#d33',
                    });
                }
                $(this).find('.submit').removeAttr('disabled');
            },
            error: function(e){

                var errorString = "";
                $.each( e.responseJSON.errors, function( key, value) {
                    errorString +=  value + '<br/>';
                });

                $(this).find('.submit').removeAttr('disabled');
                Swal.fire({
                    html: errorString || trans('messages.something-went-wrong'),
                    icon: 'warning',
                    showCancelButton: false,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                });
            }
        });
    });
    $(".student-notes").on('change keyup paste', function() {
        $('#notes_changed').val('1');
    }); 
});
window.onbeforeunload = function(e){
    if ($('#notes_changed').val() == '1') {
        return trans('messages.notes-still-received');
    }
};