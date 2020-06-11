<div style="background: #fefefe;padding:15px;border:2px solid #e9ecef;" class="col-md-9">
    @foreach($lesson->lessonExercises as $lessonExercise)
        <div class="row mt-1 lesson_exercise_row">
            @php
                $is_complete = 0;
                $status_line = '';
                if(isset($masterLessonExerciseStatus[$lessonExercise->id]))
                {
                    $lessonExerciseStatus = $masterLessonExerciseStatus[$lessonExercise->id];
                    if($lessonExerciseStatus->is_complete == 1)
                    {
                        $is_complete = 1;
                    }
                    $status_line = $lessonExerciseStatus->getStatusLine();
                }
            @endphp
            <div class="col-sm-1 pr-0">
                <input type="checkbox"
                data-lesson_exercise_id="{{ $lessonExercise->id }}"
                data-schedule_id="{{ $schedule->id }}"
                {{ $is_complete  ? 'checked' : '' }}
                class="lesson_exercise_cb form-control my-1" style="width:25px;padding-right:0px;">
            </div>
            <div class="col-sm-11 pl-0">
                <input type="text" class="form-control ml-0 my-1" value="{{ $lessonExercise->title }}" disabled>
            </div>
            <div class="col-sm-12 pl-0 text-right">
                <label style="font-style: italic;" class="status_line_text">{{ $status_line }}</label>
            </div>
        </div>
    @endforeach
</div>
