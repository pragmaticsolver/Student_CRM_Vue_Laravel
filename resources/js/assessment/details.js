questionId = ""; // Question Id being edited currently.
window.addEventListener('DOMContentLoaded', function(){
    get_assesment_questions();

    // Add Question
    $('#AddQuestionModal').on('hidden.bs.modal', function (e) {
        $('#AddQuestionModal').find('.form-fields').html('');
    });
    $(document).on('click','.btn_add_question', function(){
        $('#AddQuestionModal').find('.form-fields').html(add_question_fields_html);
        $('#AddQuestionModal').find('.form_spinner').hide();
        reInitializeQuestionsForm();
        $('#AddQuestionModal').modal('show');
    });
    $('#add_question_form').submit(function(e){
        e.stopPropagation();
        e.preventDefault();

        if(!$('#add_question_form')[0].checkValidity())
        {
            return;
        }
        $('#add_question_submit_btn').attr('disabled',true);
        $('#AddQuestionModal').find('.form_spinner').show();

        var formData = new FormData($(this)[0]);

        $.ajax({
            url: store_assessment_question_url,
            type: 'POST',
            data: formData,
            contentType: false,
            processData: false,
            success: function(response){
                if(response.status == 1)
                {
                    $('#AddQuestionModal').modal('hide');
                    toastr.success(response.message);
                    get_assesment_questions();
                }
                else
                {
                    $('#AddQuestionModal').find('.form_spinner').hide();
                    Swal.fire({
                        text: trans('messages.something-went-wrong'),
                        icon: 'warning',
                        showCancelButton: false,
                        confirmButtonColor: '#3085d6',
                        cancelButtonColor: '#d33',
                    });
                }
                $('#add_question_submit_btn').removeAttr('disabled');
            },
            error: function(e){

                var errorString = "";
                $.each( e.responseJSON.errors, function( key, value) {
                    errorString +=  value + '<br/>';
                });

                $('#add_question_submit_btn').removeAttr('disabled');
                $('#AddQuestionModal').find('.form_spinner').hide();
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

    // Edit Question
    $('#EditQuestionModal').on('hidden.bs.modal', function (e) {
        $('#EditQuestionModal').find('.form-fields').html('');
    });
    $(document).on('click','.btn_edit_question', function(){
        var id = $(this).data('id');
        questionId = id;
        $('#EditQuestionModal').find('.form_spinner').hide();
        $('#EditQuestionModal').find('.form-fields').html('<div class="text-center"><div class="fa fa-spinner fa-spin" style="font-size:100px"></div></div>');
        $('#EditQuestionModal').modal('show');

        $.ajax({
            url: assessment_question_edit_fields_url + '/' + id,
            type: 'GET',
            success: function(response){
                $('#EditQuestionModal').find('.form-fields').html(response);
                reInitializeQuestionsForm();
            }
        });
    });
    $('#edit_question_form').submit(function(e){
        e.stopPropagation();
        e.preventDefault();

        if(!$('#edit_question_form')[0].checkValidity())
        {
            return;
        }
        $('#edit_question_sumbit_btn').attr('disabled',true);
        $('#EditQuestionModal').find('.form_spinner').show();

        var formData = new FormData($(this)[0]);

        $.ajax({
            url: assessment_question_update_url + '/' + questionId,
            type: 'POST',
            data: formData,
            contentType: false,
            processData: false,
            success: function(response){
                if(response.status == 1)
                {
                    $('#EditQuestionModal').modal('hide');
                    toastr.success(response.message);
                    get_assesment_questions();
                }
                else
                {
                    $('#EditQuestionModal').find('.form_spinner').hide();
                    Swal.fire({
                        text: trans('messages.something-went-wrong'),
                        icon: 'warning',
                        showCancelButton: false,
                        confirmButtonColor: '#3085d6',
                        cancelButtonColor: '#d33',
                    });
                }
                $('#edit_question_sumbit_btn').removeAttr('disabled');
            },
            error: function(e){
                var errorString = "";
                $.each( e.responseJSON.errors, function( key, value) {
                    errorString +=  value + '<br/>';
                });

                $('#edit_question_sumbit_btn').removeAttr('disabled');
                $('#EditQuestionModal').find('.form_spinner').hide();
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

    // Delete Question
    $(document).on('click','.btn_delete_question', function(){
        var id = $(this).data('id');

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
                $.ajax({
                    url: assessment_question_destroy_url + '/' + id,
                    type: 'POST',
                    data: {
                        '_token' : csrf_token,
                        '_method': 'DELETE'
                    },
                    success: function(response){
                        if(response.status == 1)
                        {
                            toastr.success(response.message);
                            get_assesment_questions();
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
        });
    });

    // Reorder Questions
    $(document).on('click','.btn_reorder_questions', function(){
        var id = $(this).data('id');
        var title = $(this).text();

        $('#ReorderQuestionsModal').find('.modal-title').text(title);
        $('#ReorderQuestionsModal').find('.form_spinner').hide();
        $('#ReorderQuestionsModal').find('.form-fields').html('<div class="text-center"><div class="fa fa-spinner fa-spin" style="font-size:100px"></div></div>');
        $('#ReorderQuestionsModal').modal('show');

        $.ajax({
            url:  assessment_reorder_questions_form_url + '/' + id,
            type: 'GET',
            success: function(response){
                $('#ReorderQuestionsModal').find('.form-fields').html(response);
                $('.reorder-questions-section').sortable();
            }
        });
    });
    $('#reorder_questions_form').submit(function(e){
        e.stopPropagation();
        e.preventDefault();

        if(!$('#reorder_questions_form')[0].checkValidity())
        {
            return;
        }
        $('#reoder_lessons_sumbit_btn').attr('disabled',true);
        $('#ReorderQuestionsModal').find('.form_spinner').show();

        var id = $('#reorder_questions_form').find('input[name="assessment_id"]').val();

        var formData = new FormData($(this)[0]);

        $.ajax({
            url: assessment_reorder_questions_save_url + '/' + id,
            type: 'POST',
            data: formData,
            contentType: false,
            processData: false,
            success: function(response){
                if(response.status == 1)
                {
                    $('#ReorderQuestionsModal').modal('hide');
                    toastr.success(response.message);
                    get_assesment_questions();
                }
                else
                {
                    $('#ReorderQuestionsModal').find('.form_spinner').hide();
                    Swal.fire({
                        text: trans('messages.something-went-wrong'),
                        icon: 'warning',
                        showCancelButton: false,
                        confirmButtonColor: '#3085d6',
                        cancelButtonColor: '#d33',
                    });
                }
                $('#reoder_lessons_sumbit_btn').removeAttr('disabled');
            },
            error: function(e){
                $('#reoder_lessons_sumbit_btn').removeAttr('disabled');
                $('#ReorderQuestionsModal').find('.form_spinner').hide();
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

function get_assesment_questions()
{
    $('#questions_section_preloader').show();
    $.ajax({
        url: get_questions_url,
        type: 'GET',
        success: function(response){
            $('#assessment_questions_section').html(response);
            $('#questions_section_preloader').hide();
            if($('.assessment-question-row').length > 1)
            {
                $('.btn_reorder_questions').removeAttr('disabled');
            }
            else
            {
                $('.btn_reorder_questions').attr('disabled',true);
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
