var exercises = [];
var homeworks = [];


// run this only once.
window.addEventListener('DOMContentLoaded', function(){
    $(document).on('click','.add_exercise', function(){
        add_exercise();
    });

    $(document).on('click','.add_video', function(){
        add_video();
    });

    $(document).on('click','.add_homework', function(){
        add_homework();
    });
});

// run this every time modal opens
window.initLessonFrom = function(parentElement, lessonId, userId)
{
    exercises = [];
    homeworks = [];
    videos = [];

    parentElement.find('.exercises_container').html('');
    parentElement.find('.homeworks_container').html('');
    parentElement.find('.video_container').html('');
    
    if(parentElement.find('#exist_video').length)
    {
        videos = JSON.parse(parentElement.find('#exist_video').val());
    }
    if(parentElement.find('#existing_exercies').length)
    {
        exercises = JSON.parse(parentElement.find('#existing_exercies').val());
    }
    if($('#existing_homeworks').length)
    {
        homeworks = JSON.parse(parentElement.find('#existing_homeworks').val());
    }
    $.each(videos, function(i, video){
        add_video(video)
    });

    $.each(exercises, function(i, exercise){
        add_exercise(exercise)
    });

    $.each(homeworks, function(i, homework){
        add_homework(homework)
    });

    if(lessonId)
    {
        refreshDropzonesForLessonForm(lessonId, userId);
    }
}

function add_video(video)
{
    video_id = video ? video.id : '';
    video_link = video ? video.file_path : '';
    video_name = video ? video.file_name : '';
    var html = `
            <div class="row mb-1">
                <input type="hidden" name="video_ids[]" value="${video_id}" >

                <div class="col-sm-8 pr-0">
                    <input name="video_links[]" type="text" value="${video_link}" class="form-control" placeholder="YouTube Link" required >
                </div>
                <div class="col-sm-2 pr-0">
                    <input name="video_names[]" type="text" value="${video_name}" class="form-control" placeholder="Name" required >
                </div>
                <div class="col-sm-2">
                <input type="button" tabindex="-1" value="${trans('messages.remove')}" class="btn btn-danger btn-sm mt-1" onclick="$(this).closest('.row').remove();">
            </div></div>`;
    $('.video_container').append(html);
}

function add_exercise(exercise)
{
    exercise_id = exercise ? exercise.id : '';
    exercise_title = exercise ? exercise.title : '';
    var html = `
            <div class="row mb-1">
                <input type="hidden" name="exercise_ids[]" value="${exercise_id}" >

                <div class="col-sm-10 pr-0">
                    <input name="exercise_titles[]" type="text" value="${exercise_title}" class="form-control" required >
                </div>
                <div class="col-sm-2">
                <input type="button" tabindex="-1" value="${trans('messages.remove')}" class="btn btn-danger btn-sm mt-1" onclick="$(this).closest('.row').remove();">
            </div></div>`;
    $('.exercises_container').append(html);
}


function add_homework(homework)
{
    homework_id = homework ? homework.id : '';
    homework_title = homework ? homework.title : '';
    var html = `
            <div class="row mb-1">
                <input type="hidden" name="homework_ids[]" value="${homework_id}" >

                <div class="col-sm-10 pr-0">
                    <input name="homework_titles[]" type="text" value="${homework_title}" class="form-control" required >
                </div>
                <div class="col-sm-2">
                <input type="button" tabindex="-1" value="${trans('messages.remove')}" class="btn btn-danger btn-sm mt-1" onclick="$(this).closest('.row').remove();">
            </div></div>`;
    $('.homeworks_container').append(html);
}
