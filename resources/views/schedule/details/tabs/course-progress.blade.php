@push('styles')
<style>
.collapsing {
    -webkit-transition: none;
    transition: none;
    display: none;
}
.cp-lesson-row {
    cursor: pointer;
}
</style>
@endpush

@php
    $_today_date = \Carbon\carbon::now(\App\Helpers\CommonHelper::getSchoolTimezone())->format('Y-m-d');
@endphp

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
                            <table class="table table-bordered table-hover">
                                <thead>
                                <tr>
                                    <th>{{ __('messages.lesson') }}</th>
                                    <th>{{ __('messages.test') }}</th>
                                    <th>{{ __('messages.papertest') }}</th>
                                    <th>{{ __('messages.assessment') }}</th>
                                    <th>{{ __('messages.date') }}</th>
                                    <th>&nbsp</th>
                                </tr>
                                </thead>
                                <tbody>
                                    @foreach($unit->lessons as $lesson)
                                        @php
                                            $scheduledLesson = $schedule->getScheduledLesson($lesson->id);
                                            $is_completed = $scheduledLesson && $scheduledLesson->complete ? true : false;
                                        @endphp
                                        <tr class="cp-lesson-row lesson {{  $is_completed ? 'complete' : '' }}" data-toggle="collapse" href="#cp-lesson{{ $lesson->id }}" role="button" aria-expanded="false" aria-controls="cp-lesson{{ $lesson->id }}">
                                            <td><a href="{{ route('lesson.show', $lesson->id) }}">{{ $lesson->title }}</a></td>
                                            <td>
                                                @if($lesson->tests->count() > 0)
                                                    @foreach($lesson->tests as $test)
                                                        <a href="{{ route('test.show', $test->id) }}">{{ $test->name }}</a><br>
                                                    @endforeach
                                                @else
                                                    {{ __('messages.notest') }}
                                                @endif
                                            </td>
                                            <td>
                                                @if($lesson->paper_tests->count() > 0)
                                                    @foreach($lesson->paper_tests as $paper_test)
                                                        <a href="{{ route('paper_test.show', $paper_test->id) }}">{{ $paper_test->name }}</a><br>
                                                    @endforeach
                                                @else
                                                    {{ __('messages.notest') }}
                                                @endif
                                            </td>
                                            <td>
                                                @if($lesson->assessments->count() > 0)
                                                    @foreach($lesson->assessments as $assessment)
                                                        <a href="{{ route('assessment.show', $assessment->id) }}">{{ $assessment->name }}</a><br>
                                                    @endforeach
                                                @else
                                                    {{ __('messages.noassessment') }}
                                                @endif
                                            </td>
                                            <form method="POST" action="{{ route('schedule.lesson.complete') }}">
                                                <input type="hidden" name="schedule_id" value="{{ $schedule->id }}">
                                                <input type="hidden" name="lesson_id" value="{{ $lesson->id }}">
                                                @csrf
                                                @method('PATCH')
                                                <td>
                                                    <input type="date" class="form-control" value="{{ $is_completed ? $scheduledLesson->date : $_today_date }}" name="date">
                                                </td>
                                                <td style="width:215px;">
                                                <button class="btn {{ $is_completed ? 'btn-warning' : 'btn-primary' }}" type="submit">
                                                        {{ $is_completed ? __('messages.update-completed-date') : __('messages.complete') }}
                                                    </button>
                                                </td>
                                            </form>
                                        </tr>
                                        <tr class="collapse" id="cp-lesson{{ $lesson->id }}">
                                            <td colspan="6">
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
                                                                    @include('course.unit.lesson.exercise-section')
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
                                                                    @include('course.unit.lesson.homework-section')
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

                                                        @if($lesson->teachers_notes)
                                                            <tr>
                                                                <th>{{ __('messages.teachers-notes') }}</th>
                                                                <td>{!! nl2br( $lesson->teachers_notes) !!}</td>
                                                            </tr>
                                                        @endif

                                                        @if($lesson->teachers_prep)
                                                            <tr>
                                                                <th>{{ __('messages.teachers-prep') }}</th>
                                                                <td>{!! nl2br($lesson->teachers_prep) !!}</td>
                                                            </tr>
                                                        @endif

                                                        <tr>
                                                            <th>{{ __('messages.comments') }}</th>
                                                            <td>
                                                                <textarea class="commments_text_area form-control" rows="4"
                                                                data-schedule_id="{{ $schedule->id }}"
                                                                data-lesson_id="{{ $lesson->id }}"
                                                            >{{ $scheduledLesson ? $scheduledLesson->comments : '' }}</textarea>
                                                                <div>
                                                                    <button class="btn_save_comments btn btn-primary my-1 pull-left">
                                                                        {{ __('messages.save') }} &nbsp
                                                                        <i class="prelaoder fa fa-spinner fa-spin" style="display:none;"></i>
                                                                    </button>
                                                                    <div class="pull-right m-1" style="font-style:italic;">
                                                                        <span class="comments_status_line">{{ $scheduledLesson && $scheduledLesson->comment_updated_at ? $scheduledLesson->getCommentStatusLine() : '' }}</span>
                                                                    </div>
                                                                </div>
                                                            </td>
                                                        </tr>
                                                    </tbody>
                                                </table>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
    @endif
@endif

@push('scripts')
<script src="{{ asset('public'.mix('js/page/schedule/details/tabs/course-progress.js')) }}"></script>
@endpush
