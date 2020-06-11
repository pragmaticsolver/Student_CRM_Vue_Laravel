@if($course)
    <h3>{{ __('messages.coursedetails') }}</h3>

    <div class="table-responsive">
        <table class="table table-striped table-bordered table-hover ">
            <tbody>
            <tr>
                <th style="width:15%">{{ __('messages.name')}}</th>
                <td>{{ $course->title }}</td>
            </tr>
            @if($course->description)
                <tr>
                    <th>{{ __('messages.description')}}</th>
                    <td>{{ $course->description }}</td>
                </tr>
            @endif
            @if($course->objectives)
                <tr>
                    <th>{{ __('messages.objectives')}}</th>
                    <td>{{ $course->objectives }}</td>
                </tr>
            @endif
            @if($course->thumbnail)
                <tr>
                    <th>{{ __('messages.thumbnail')}}</th>
                    <td>{!! $course->the_image() !!}</td>
                </tr>
            @endif
            </tbody>
        </table>
    </div>

    @if($course->units->count() > 0)
        <div class="card card-body course-progress">
            @foreach($course->units as $unit_index => $unit)
                <div class="unit-progress">
                    <div class="unit-button">
                        <a class="btn btn-secondary" data-toggle="collapse" href="#unit{{ $unit->id }}" role="button" aria-expanded="false" aria-controls="unit{{ $unit->id }}">
                            {{ __('messages.unitnumber', ['number' => $unit_index+1]) }}: {{ $unit->name }}
                        </a>
                    </div>

                    <div class="collapse show" id="unit{{ $unit->id }}">
                        <table class="table table-striped table-bordered table-hover ">
                            <tbody>
                            <tr>
                                <th style="width:15%">{{ __('messages.objectives')}}</th>
                                <td>{{ $unit->objectives }}</td>
                            </tr>
                            </tbody>
                        </table>

                        @if($unit->lessons->count() > 0)
                            @foreach($unit->lessons as $lesson_index => $lesson)
                                <h3>{{ __('messages.lessonnumber', ['number' => $lesson_index+1]) }}: {{ $lesson->title }}</h3>
                                <table class="table table-hover">
                                    <tbody>
                                        @if($lesson->description)
                                            <tr>
                                                <th style="width:15%">{{ __('messages.description') }}</th>
                                                <td>{!! nl2br($lesson->description) !!}</td>
                                            </tr>
                                        @endif

                                        @if($lesson->objectives)
                                            <tr>
                                                <th>{{ __('messages.objectives') }}</th>
                                                <td>{!! nl2br($lesson->objectives) !!}</td>
                                            </tr>
                                        @endif

                                        @if($lesson->full_text)
                                            <tr>
                                                <th>{{ __('messages.fulltext') }}</th>
                                                <td>{!! nl2br($lesson->full_text) !!}</td>
                                            </tr>
                                        @endif

                                        @if($lesson->thumbnail)
                                            <tr>
                                                <th>{{ __('messages.thumbnail') }}</th>
                                                <td>{!! $lesson->the_image() !!}</td>
                                            </tr>
                                        @endif

                                        @if($lesson->lessonExercises->count() > 0)
                                            <tr>
                                                <th>{{ __('messages.exercises') }}</th>
                                                <td>
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
                                                                    <input type="checkbox" {{ $is_complete  ? 'checked' : '' }} disabled
                                                                    class="form-control my-1" style="width:25px;padding-right:0px;">
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
                                                </td>
                                            </tr>
                                        @endif

                                        @if($lesson->student_lesson_prep)
                                            <tr>
                                                <th>{{ __('messages.student-lesson-prep') }}</th>
                                                <td>{!! nl2br($lesson->student_lesson_prep) !!}</td>
                                            </tr>
                                        @endif

                                        @if($lesson->vocab_list)
                                            <tr>
                                                <th>{{ __('messages.vocab-list') }}</th>
                                                <td>{!! nl2br($lesson->vocab_list) !!}</td>
                                            </tr>
                                        @endif

                                        @if($lesson->lessonHomeworks->count() > 0)
                                            <tr>
                                                <th>{{ __('messages.homework') }}</th>
                                                <td>
                                                    <div style="background: #fefefe;padding:15px;border:2px solid #e9ecef;" class="col-md-9">
                                                        @foreach($lesson->lessonHomeworks as $lessonHomework)
                                                            <div class="row mt-1 lesson_exercise_row">
                                                                @php
                                                                    $is_complete = 0;
                                                                    if(isset($masterLessonHomeworkStatus[$lessonHomework->id]))
                                                                    {
                                                                        $lessonHomeworkStatus = $masterLessonHomeworkStatus[$lessonHomework->id];
                                                                        if($lessonHomeworkStatus->is_complete == 1)
                                                                        {
                                                                            $is_complete = 1;
                                                                        }
                                                                    }
                                                                @endphp
                                                                <div class="col-sm-1 pr-0">
                                                                    <input type="checkbox" {{ $is_complete  ? 'checked' : '' }} disabled
                                                                    class="form-control my-1" style="width:25px;padding-right:0px;">
                                                                </div>
                                                                <div class="col-sm-11 pl-0">
                                                                    <input type="text" class="form-control ml-0 my-1" value="{{ $lessonHomework->title }}" disabled>
                                                                </div>
                                                            </div>
                                                        @endforeach
                                                    </div>
                                                </td>
                                            </tr>
                                        @endif

                                        @if($lesson->extra_materials_text || $lesson->extraMaterialFiles->count() > 0)
                                            <tr>
                                                <th>{{ __('messages.extra-materials') }}</th>
                                                <td>
                                                    @if($lesson->extra_materials_text)
                                                        {!! nl2br($lesson->extra_materials_text) !!} <br><br>
                                                    @endif
                                                    {!! $lesson->the_extramaterial_files_url() !!}
                                                </td>
                                            </tr>
                                        @endif

                                        @if($lesson->downloadableFiles->count() > 0)
                                            <tr>
                                                <th>{{ __('messages.downloadablefiles') }}</th>
                                                <td>{!! $lesson->the_downloadable_files_url() !!}</td>
                                            </tr>
                                        @endif

                                        @if($lesson->pdfFiles->count() > 0)
                                            <tr>
                                                <th>{{ __('messages.pdffiles') }}</th>
                                                <td>{!! $lesson->the_pdf_files_url() !!}</td>
                                            </tr>
                                        @endif

                                        @if($lesson->audioFiles->count() > 0)
                                            <tr>
                                                <th>{{ __('messages.audiofiles') }}</th>
                                                <td>{!! $lesson->the_audio_files_url() !!}</td>
                                            </tr>
                                        @endif

                                        @if($lesson->videoFiles->count() > 0)
                                            <tr>
                                                <th>{{ __('messages.video') }}</th>
                                                <td>{!! $lesson->the_video_url() !!}</td>
                                            </tr>
                                        @endif
                                    </tbody>
                                </table>
                            @endforeach
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
    @endif

@endif
