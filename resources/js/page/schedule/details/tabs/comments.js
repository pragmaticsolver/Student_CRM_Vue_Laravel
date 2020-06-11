window.addEventListener('DOMContentLoaded', function(){
    $("#schedule_file_form").on("change", ".file-upload-field", function(){ 
        $(this).parent(".file-upload-wrapper").attr("data-text",$(this).val().replace(/.*(\/|\\)/, '') );
    });
    // Comments
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });
   
    $('#comment_form').submit(function(e){
        e.stopPropagation();
        e.preventDefault();

        if(!$(this)[0].checkValidity())
        {
            return;
        }
        $(this).find('.btn_save_class_comments').attr('disabled',true);
    
        var url = $(this).attr('action');
        var form = $(this);
        var formData = new FormData(form[0]);
        var comments_div = $(this).parent().find('.comments-details');
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
                    comments_div.html(response.html);
                    form[0].reset();
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
                $(this).find('.btn_save_class_comments').removeAttr('disabled');
            },
            error: function(e){
                $(this).find('.btn_save_class_comments').removeAttr('disabled');
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

    $('#schedule_file_form').submit(function(e){
        e.stopPropagation();
        e.preventDefault();

        if(!$(this)[0].checkValidity())
        {
            return;
        }
        $(this).find('.btn_save_class_files').attr('disabled',true);
   
        var url = $(this).attr('action');
        var form = $(this);
        var formData = new FormData(form[0]);
        var comments_div = $(this).parent().find('.commentfile-details');
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
                    comments_div.html(response.html);
                    form[0].reset();
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
                $(this).find('.btn_save_class_files').removeAttr('disabled');
            },
            error: function(e){
                $(this).find('.btn_save_class_files').removeAttr('disabled');
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
