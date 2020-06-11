@extends('layouts.app')

@section('content')
    <div class="justify-content-center">
        @include('partials.success')
        @include('partials.error')
        <h1>{{ __('messages.lessondetails') }}</h1>
        <table class="table table-hover">
            <tbody>
                <tr>
                    <th style="width:15%">{{ __('messages.title') }}</th>
                    <td>{{ $lesson->title }}</td>
                </tr>
                <tr>
                    <th>{{ __('messages.course') }}</th>
                    <td><a href="{{ route('course.show', $lesson->course->id) }}">{{ $lesson->course->title  }}</a></td>
                </tr>
                <tr>
                    <th>{{ __('messages.unit') }}</th>
                    <td><a href="{{ route('unit.show', $lesson->unit->id) }}">{{ $lesson->unit->name  }}</a></td>
                </tr>

                @if($lesson->description)
                    <tr>
                        <th>{{ __('messages.description') }}</th>
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

                <tr>
                    <th>{{ __('messages.thumbnail') }}</th>
                    <td>{!! $lesson->the_image() !!}</td>
                </tr>

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
    </div>
    @include('course.unit.lesson.file-name')
@endsection
@push('scripts')
<script src="{{ asset('public'.mix('js/page/filename.js')) }}"></script>
@endpush
