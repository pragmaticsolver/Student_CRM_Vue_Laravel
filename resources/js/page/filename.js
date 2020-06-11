window.addEventListener('DOMContentLoaded', function(){
    var parent = null;
        
    // Edit Lesson file name
    $('#EditFileModal').on('hidden.bs.modal', function (e) {
        $('#edit_filename_form')[0].reset();
        parent = null;
    });
    $(document).on('click','.btn_file_name_edit', function(){
        var id = $(this).data('id');
        var name = $(this).data('name');
        var type = $(this).data('type');
        parent = $(this).parents('.files-list');

        $('#EditFileModal').find('.form_spinner').hide();
        $('#EditFileModal').modal('show');
        $('#EditFileModal').find('input[name="file_id"]').val(id);
        $('#EditFileModal').find('input[name="type"]').val(type);
        $('#EditFileModal').find('input[name="file_name"]').val(name);
    });
    $('#edit_filename_form').submit(function(e){
        e.stopPropagation();
        e.preventDefault();

        if(!$('#edit_filename_form')[0].checkValidity())
        {
            return;
        }
        $('#edit_filename_sumbit_btn').attr('disabled',true);
        $('#EditFileModal').find('.form_spinner').show();

        var id = $('#edit_filename_form').find('input[name="file_id"]').val();
        var formData = new FormData($(this)[0]);
        var url = $('#edit_filename_form').attr('action');
        $.ajax({
            url: url + '/' + id,
            type: 'POST',
            data: formData,
            contentType: false,
            processData: false,
            success: function(response){
                if(response.status == 1)
                {
                    parent.parent().html(response.html);
                    $('#EditFileModal').modal('hide');
                    toastr.success(response.message);
                }
                else
                {
                    $('#EditFileModal').find('.form_spinner').hide();
                    Swal.fire({
                        text: trans('messages.something-went-wrong'),
                        icon: 'warning',
                        showCancelButton: false,
                        confirmButtonColor: '#3085d6',
                        cancelButtonColor: '#d33',
                    });
                }
                $('#edit_filename_sumbit_btn').removeAttr('disabled');
            },
            error: function(e){
                $('#edit_filename_sumbit_btn').removeAttr('disabled');
                $('#EditFileModal').find('.form_spinner').hide();
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
    $(document).mouseup(function(e) {
        if ($('#video-lightbox').length > 0 && $('#video-lightbox').is(":visible")) {
            var container = $("#video-lightbox");
            // if the target of the click isn't the container nor a descendant of the container
            if (!container.is(e.target) && container.has(e.target).length === 0) 
            {  
                hideVideo('video-lightbox','youtube');
            }
        }
    });
});
// Function to reveal lightbox and adding YouTube autoplay
window.revealVideo = function(div,video_id,video_src) {
    var videoId = getVideoID(video_src);
    if (videoId != 'error') {
        query = '';
        if(video_src.split("?").length>1) {
            query = video_src.split("?")[1];
        }
        video_src = 'https://www.youtube.com/embed/'+videoId+'?'+query;
    }
    document.getElementById(video_id).src = video_src; // adding autoplay to the URL
    document.getElementById(div).style.display = 'block';
}
  
// Hiding the lightbox and removing YouTube autoplay
window.hideVideo = function(div,video_id) {
    document.getElementById(video_id).src = ''; // adding autoplay to the URL
    document.getElementById(div).style.display = 'none';
}
window.getVideoID = function(url) {
    var regExp = /^.*(youtu.be\/|v\/|u\/\w\/|embed\/|watch\?v=|\&v=)([^#\&\?]*).*/;
    var match = url.match(regExp);

    if (match && match[2].length == 11) {
        return match[2];
    } else {
        return 'error';
    }
}


