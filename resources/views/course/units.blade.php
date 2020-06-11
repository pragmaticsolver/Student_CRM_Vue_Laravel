<div class="card card-body course-progress">
    @if($course->units->count() > 0)
        <div class="row my-2">
            <div class="col-11">
            </div>

            @if($course->units->count() > 1)
                <div class="col-1">
                    <button
                        data-id="{{ $course->id }}"
                        class="btn btn-success float-right btn_reorder_units">
                        {{ __('messages.reorderunits') }}
                    </button>
                </div>
            @endif

        </div>
        @foreach($course->units as $unit_index => $unit)
            @php
                $keep_expanded = false;
                $unit_section_id = "unit".$unit->id;
                if(in_array($unit_section_id,$open_unit_sections))
                {
                    $keep_expanded = true;
                }
            @endphp

            <div class="unit-progress">
                <div class="row">
                    <div class="col-11">
                        <a class="btn btn-secondary btn-block text-left unit_title_btn" data-toggle="collapse" href="#{{ $unit_section_id }}" role="button" aria-expanded="false" aria-controls="{{ $unit_section_id }}">
                            {{ __('messages.unitnumber', ['number' => $unit_index+1]) }}: {{ $unit->name }}
                        </a>
                    </div>
                    <div class="col-1">
                        <button
                            data-id="{{ $unit->id }}"
                            class="btn btn-success float-right btn_edit_unit">
                            {{ __('messages.editunit') }}
                        </button>
                    </div>
                </div>

                <div class="collapse {{ $keep_expanded ? 'show' : ''}}" id="{{ $unit_section_id }}">
                    <table class="table table-striped table-bordered table-hover">
                        <tr>
                            <th>{{ __('messages.objectives') }}</th>
                            <td>{{ $unit->objectives }}</td>
                        </tr>
                    </table>

                    <button
                        class="btn btn-success btn_add_lesson"
                        data-course_id="{{  $course->id }}"
                        data-unit_id="{{ $unit->id }}"
                        >{{ __('messages.lessonaddforunit', ['unit' => $unit->name]) }}
                    </button>

                    @if($unit->lessons->count() > 1)
                        <button
                            class="btn btn-success btn_reorder_lessons"
                            data-unit_id="{{ $unit->id }}"
                            >{{ __('messages.reorderlessonsforunit', ['unit' => $unit->name]) }}
                        </button>
                    @endif

                    @if($unit->lessons->count() > 0)
                        @foreach($unit->lessons as $lesson_index => $lesson)
                            <div class="clearfix mb-2 mt-2">
                                <h3 class="float-left">{{ __('messages.lessonnumber', ['number' => $lesson_index+1]) }}: {{ $lesson->title }}</h3>
                                <button
                                    data-id="{{ $lesson->id }}"
                                    class="btn btn-danger float-right btn_delete_lesson ml-1">
                                    {{ __('messages.delete-lesson') }}
                                </button>

                                <button
                                    data-id="{{ $lesson->id }}"
                                    class="btn btn-success float-right btn_edit_lesson">
                                    {{ __('messages.editlesson') }}
                                </button>
                            </div>

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
                                                @foreach($lesson->lessonExercises as $lessonExercise)
                                                    <li>{{ $lessonExercise->title }}</li>
                                                @endforeach
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
                                                @foreach($lesson->lessonHomeworks as $lessonHomework)
                                                    <li>{{ $lessonHomework->title }}</li>
                                                @endforeach
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
                                </tbody>
                            </table>
                        @endforeach
                    @endif
                </div>
            </div>
        @endforeach
    @else
        <p class="text-center">{{ __('messages.no-units-added-yet') }}</p>
    @endif
</div>
