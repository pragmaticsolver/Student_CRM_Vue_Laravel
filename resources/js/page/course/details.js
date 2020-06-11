window.addEventListener('DOMContentLoaded', function(){
    get_course_units();

    $('#class-units-section')
        .on('hidden.bs.collapse', updateOpenedUnitsSections)
        .on('shown.bs.collapse', updateOpenedUnitsSections);

    // Add Unit
    $('#add_unit_btn').click(function(){
        $('#AddUnitModal').find('form')[0].reset();
        $('#AddUnitModal').find('.form_spinner').hide();
        $('#AddUnitModal').modal('show');
    });
    $('#add_unit_form').submit(function(e){
        e.stopPropagation();
        e.preventDefault();

        if(!$('#add_unit_form')[0].checkValidity())
        {
            return;
        }
        $('#submit_unit_btn').attr('disabled',true);
        $('#AddUnitModal').find('.form_spinner').show();

        $.ajax({
            url: storeUnitUrl,
            type: 'POST',
            data: $('#add_unit_form').serialize(),
            success: function(response){
                if(response.status == 1)
                {
                    $('#AddUnitModal').modal('hide');
                    toastr.success(response.message);
                    get_course_units();
                }
                else
                {
                    $('#AddUnitModal').find('.form_spinner').hide();
                    Swal.fire({
                        text: trans('messages.something-went-wrong'),
                        icon: 'warning',
                        showCancelButton: false,
                        confirmButtonColor: '#3085d6',
                        cancelButtonColor: '#d33',
                    });
                }
                $('#submit_unit_btn').removeAttr('disabled');
            },
            error: function(e){
                $('#submit_unit_btn').removeAttr('disabled');
                $('#AddUnitModal').find('.form_spinner').hide();
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

    // Edit Unit
    $(document).on('click','.btn_edit_unit', function(){
        var id = $(this).data('id');

        $('#EditUnitModal').find('.form_spinner').hide();
        $('#EditUnitModal').find('.form-fields').html('<div class="text-center"><div class="fa fa-spinner fa-spin" style="font-size:100px"></div></div>');
        $('#EditUnitModal').modal('show');

        $.ajax({
            url: editCourseModalUrl + '/' + id,
            type: 'GET',
            success: function(response){
                $('#EditUnitModal').find('.form-fields').html(response);
            }
        });
    });
    $('#edit_unit_form').submit(function(e){
        e.stopPropagation();
        e.preventDefault();

        if(!$('#edit_unit_form')[0].checkValidity())
        {
            return;
        }
        $('#edit_unit_sumbit_btn').attr('disabled',true);
        $('#EditUnitModal').find('.form_spinner').show();

        var id = $('#edit_unit_form').find('input[name="unit_id"]').val();

        var formData = new FormData($(this)[0]);

        $.ajax({
            url: updateUnitUrl + '/' + id,
            type: 'POST',
            data: formData,
            contentType: false,
            processData: false,
            success: function(response){
                if(response.status == 1)
                {
                    $('#EditUnitModal').modal('hide');
                    toastr.success(response.message);
                    get_course_units();
                }
                else
                {
                    $('#EditUnitModal').find('.form_spinner').hide();
                    Swal.fire({
                        text: trans('messages.something-went-wrong'),
                        icon: 'warning',
                        showCancelButton: false,
                        confirmButtonColor: '#3085d6',
                        cancelButtonColor: '#d33',
                    });
                }
                $('#edit_unit_sumbit_btn').removeAttr('disabled');
            },
            error: function(e){
                $('#edit_unit_sumbit_btn').removeAttr('disabled');
                $('#EditUnitModal').find('.form_spinner').hide();
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

    // Add Lesson
    $('#AddLessonModal').on('hidden.bs.modal', function (e) {
        $('#AddLessonModal').find('.form-fields').html('');
    });
    $(document).on('click','.btn_add_lesson', function(){
        var course_id = $(this).data('course_id');
        var unit_id = $(this).data('unit_id');
        var title = $(this).text();

        $('#AddLessonModal').find('.modal-title').text(title);
        $('#AddLessonModal .preview-section').hide();
        $('#AddLessonModal').find('input[name="course_id"]').val(course_id);
        $('#AddLessonModal').find('input[name="unit_id"]').val(unit_id);
        $('#AddLessonModal').find('.form-fields').html(add_lesson_form_html);
        $('#AddLessonModal').find('.form_spinner').hide();

        initLessonFrom($('#add_lesson_form'),'', userId);
        $('#AddLessonModal').modal('show');
    });
    $('#add_lesson_form').submit(function(e){
        e.stopPropagation();
        e.preventDefault();

        if(!$('#add_lesson_form')[0].checkValidity())
        {
            return;
        }
        $('#submit_lesson_btn').attr('disabled',true);
        $('#AddLessonModal').find('.form_spinner').show();

        var formData = new FormData($(this)[0]);

        $.ajax({
            url: lessonStoreUrl,
            type: 'POST',
            data: formData,
            contentType: false,
            processData: false,
            success: function(response){
                if(response.status == 1)
                {
                    $('#AddLessonModal').modal('hide');
                    toastr.success(response.message);
                    get_course_units();
                }
                else
                {
                    $('#AddLessonModal').find('.form_spinner').hide();
                    Swal.fire({
                        text: trans('messages.something-went-wrong'),
                        icon: 'warning',
                        showCancelButton: false,
                        confirmButtonColor: '#3085d6',
                        cancelButtonColor: '#d33',
                    });
                }
                $('#submit_lesson_btn').removeAttr('disabled');
            },
            error: function(e){
                $('#submit_lesson_btn').removeAttr('disabled');
                $('#AddLessonModal').find('.form_spinner').hide();
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

    // Edit Lesson
    $('#EditLessonModal').on('hidden.bs.modal', function (e) {
        $('#EditLessonModal').find('.form-fields').html('');
    });
    $(document).on('click','.btn_edit_lesson', function(){
        var id = $(this).data('id');

        $('#EditLessonModal').find('.form_spinner').hide();
        $('#EditLessonModal').find('.form-fields').html('<div class="text-center"><div class="fa fa-spinner fa-spin" style="font-size:100px"></div></div>');
        $('#EditLessonModal').modal('show');

        $.ajax({
            url: editLessonFieldsUrl + '/' + id,
            type: 'GET',
            success: function(response){
                $('#EditLessonModal').find('.form-fields').html(response);
                initLessonFrom($('#edit_lesson_form'),id, userId);
            }
        });
    });
    $('#edit_lesson_form').submit(function(e){
        e.stopPropagation();
        e.preventDefault();

        if(!$('#edit_lesson_form')[0].checkValidity())
        {
            return;
        }
        $('#edit_lesson_sumbit_btn').attr('disabled',true);
        $('#EditLessonModal').find('.form_spinner').show();

        var id = $('#edit_lesson_form').find('input[name="lesson_id"]').val();

        var formData = new FormData($(this)[0]);

        $.ajax({
            url: updateLessonUrl + '/' + id,
            type: 'POST',
            data: formData,
            contentType: false,
            processData: false,
            success: function(response){
                if(response.status == 1)
                {
                    $('#EditLessonModal').modal('hide');
                    toastr.success(response.message);
                    get_course_units();
                }
                else
                {
                    $('#EditLessonModal').find('.form_spinner').hide();
                    Swal.fire({
                        text: trans('messages.something-went-wrong'),
                        icon: 'warning',
                        showCancelButton: false,
                        confirmButtonColor: '#3085d6',
                        cancelButtonColor: '#d33',
                    });
                }
                $('#edit_lesson_sumbit_btn').removeAttr('disabled');
            },
            error: function(e){
                $('#edit_lesson_sumbit_btn').removeAttr('disabled');
                $('#EditLessonModal').find('.form_spinner').hide();
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

    // Reorder Units
    $(document).on('click','.btn_reorder_units', function(){
        var id = $(this).data('id');

        $('#ReorderUnitsModal').find('.form_spinner').hide();
        $('#ReorderUnitsModal').find('.form-fields').html('<div class="text-center"><div class="fa fa-spinner fa-spin" style="font-size:100px"></div></div>');
        $('#ReorderUnitsModal').modal('show');

        $.ajax({
            url: reorderUnitsFormUrl + '/' + id,
            type: 'GET',
            success: function(response){
                $('#ReorderUnitsModal').find('.form-fields').html(response);
                $('.units-reorder-section').sortable();
            }
        });
    });
    $('#reorder_units_form').submit(function(e){
        e.stopPropagation();
        e.preventDefault();

        if(!$('#reorder_units_form')[0].checkValidity())
        {
            return;
        }
        $('#reoder_units_sumbit_btn').attr('disabled',true);
        $('#ReorderUnitsModal').find('.form_spinner').show();

        var id = $('#reorder_units_form').find('input[name="course_id"]').val();

        var formData = new FormData($(this)[0]);

        $.ajax({
            url: reorderUnitsSaveUrl + '/' + id,
            type: 'POST',
            data: formData,
            contentType: false,
            processData: false,
            success: function(response){
                if(response.status == 1)
                {
                    $('#ReorderUnitsModal').modal('hide');
                    toastr.success(response.message);
                    get_course_units();
                }
                else
                {
                    $('#ReorderUnitsModal').find('.form_spinner').hide();
                    Swal.fire({
                        text: trans('messages.something-went-wrong'),
                        icon: 'warning',
                        showCancelButton: false,
                        confirmButtonColor: '#3085d6',
                        cancelButtonColor: '#d33',
                    });
                }
                $('#reoder_units_sumbit_btn').removeAttr('disabled');
            },
            error: function(e){
                $('#reoder_units_sumbit_btn').removeAttr('disabled');
                $('#ReorderUnitsModal').find('.form_spinner').hide();
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

    // Reorder Lessons
    $(document).on('click','.btn_reorder_lessons', function(){
        var id = $(this).data('unit_id');
        var title = $(this).text();

        $('#ReorderLessonsModal').find('.modal-title').text(title);
        $('#ReorderLessonsModal').find('.form_spinner').hide();
        $('#ReorderLessonsModal').find('.form-fields').html('<div class="text-center"><div class="fa fa-spinner fa-spin" style="font-size:100px"></div></div>');
        $('#ReorderLessonsModal').modal('show');

        $.ajax({
            url: reorderLessonFormUrl + '/' + id,
            type: 'GET',
            success: function(response){
                $('#ReorderLessonsModal').find('.form-fields').html(response);
                $('.lessons-reorder-section').sortable();
            }
        });
    });
    $('#reorder_lessons_form').submit(function(e){
        e.stopPropagation();
        e.preventDefault();

        if(!$('#reorder_lessons_form')[0].checkValidity())
        {
            return;
        }
        $('#reoder_lessons_sumbit_btn').attr('disabled',true);
        $('#ReorderLessonsModal').find('.form_spinner').show();

        var id = $('#reorder_lessons_form').find('input[name="unit_id"]').val();

        var formData = new FormData($(this)[0]);

        $.ajax({
            url: reorderLessonSaveUrl + '/' + id,
            type: 'POST',
            data: formData,
            contentType: false,
            processData: false,
            success: function(response){
                if(response.status == 1)
                {
                    $('#ReorderLessonsModal').modal('hide');
                    toastr.success(response.message);
                    get_course_units();
                }
                else
                {
                    $('#ReorderLessonsModal').find('.form_spinner').hide();
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
                $('#ReorderLessonsModal').find('.form_spinner').hide();
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

    // Delete Lesson
    $(document).on('click','.btn_delete_lesson', function(){
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
            if (result.value){
                $.ajax({
                    url: lessonDestroyUrl + '/' + id,
                    type: 'DELETE',
                    data: {
                        '_token' : csrfToken
                    },
                    success: function(response){
                        if(response.status == 1)
                        {
                            toastr.success(response.message);
                            get_course_units();
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
                    error: function(){
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
});

function get_course_units()
{
    $('#class_unit_preloader').show();
    $.ajax({
        url: getCourseUnitsUrl,
        type: 'GET',
        data: {
            id: courseId,
            open_unit_sections: openedUnitsSections
        },
        success: function(response){
            $('#class-units-section').html(response);
            $('#class_unit_preloader').hide();
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

function updateOpenedUnitsSections()
{
    _temp = [];
    $('.collapse.show').each(function(){
        _temp.push($(this).attr('id'));
    });
    openedUnitsSections = _temp;
}
