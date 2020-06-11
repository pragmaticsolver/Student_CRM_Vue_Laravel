    @csrf

    @if(isset($lesson))
        @method('PATCH')
    @endif

    <input type="hidden" name="lesson_id" value="{{ isset($lesson) ? $lesson->id : '' }}">
    <input type="hidden" name="user_id" value="{{ \Auth::id() }}">

    <input type="hidden" name="exist_downloadable_files" value="{{ isset($lesson) ? $lesson->downloadableFilesForDropzone() : '[]' }}">
    <input type="hidden" name="exist_pdf_files" value="{{  isset($lesson) ? $lesson->pdfFilesForDropzone() : '[]' }}">
    <input type="hidden" name="exist_audio_files" value="{{ isset($lesson) ? $lesson->audioFilesForDropzone() : '[]' }}">
    <input type="hidden" name="exist_extra_material_files" value="{{ isset($lesson) ? $lesson->extraMaterialFilesForDropzone() : '[]' }}">

    <input type="hidden" name="exist_video" id="exist_video" value="{{ isset($lesson) ? $lesson->lessonVideoJson() : '[]' }}">
    <input type="hidden" name="existing_exercies" id="existing_exercies" value="{{ isset($lesson) ? $lesson->lessonExercisesJson() : '[]' }}">
    <input type="hidden" name="existing_homeworks" id="existing_homeworks" value="{{ isset($lesson) ? $lesson->lessonHomeworksJson() : '[]'  }}">

    <div class="form-group row">
        <label class="col-lg-2 col-form-label">{{ __('messages.title') }}</label>
        <div class="col-lg-10">
            <input name="title" type="text" value="{{ isset($lesson) ? old('title', $lesson->title) : old('title') }}" class="form-control{{ $errors->has('title') ? ' is-invalid' : '' }}" required="">
        </div>
    </div>

    @if(isset($show_course_selection) && $show_course_selection == 1)
        <div class="form-group row">
            <label class="col-lg-2 col-form-label">{{ __('messages.course') }}</label>
                <div class="col-lg-10">
                    <select name="course_id" class="form-control" required="">
                    <option value="">{{ __('messages.selectcourse') }}</option>
                    @if(!$courses->isEmpty())
                        @foreach($courses as $course)
                            <option value="{{$course->id}}" @if($course->id == $course_id) selected @endif>{{$course->title}}</option>
                        @endforeach
                    @endif
                </select>
            </div>
        </div>

        <div class="form-group row">
            <label class="col-lg-2 col-form-label">{{ __('messages.unit') }}</label>
            <div class="col-lg-10">
                <select name="unit_id" class="form-control" required="">
                    <option value="">{{ __('messages.selectunit') }}</option>
                    @if(!$units->isEmpty())
                        @foreach($units as $unit)
                            <option
                                value="{{$unit->id}}" data-course="{{ $unit->course->id }}" class="option-unit"
                                @if($unit->id == $unit_id) selected @endif>
                                {{$unit->name}}
                            </option>
                        @endforeach
                    @endif
                </select>
            </div>
        </div>
    @endif
   
    @if (\App\Settings::get_value('lesson_description'))
    <div class="form-group row">
        <label class="col-lg-2 col-form-label">{{ __('messages.description') }}</label>
        <div class="col-lg-10">
            <textarea name="description" class="form-control{{ $errors->has('description') ? ' is-invalid' : '' }}" @if (\App\Settings::get_value('lesson_description_required')) required="" @endif>{{ isset($lesson) ? old('description', $lesson->description) :  old('description') }}</textarea>
        </div>
    </div>
    @endif
    @if (\App\Settings::get_value('lesson_objectives'))
    <div class="form-group row">
        <label class="col-lg-2 col-form-label">{{ __('messages.objectives') }}</label>
        <div class="col-lg-10">
            <textarea name="objectives" class="form-control{{ $errors->has('objectives') ? ' is-invalid' : '' }}" @if (\App\Settings::get_value('lesson_objectives_required')) required="" @endif>{{ isset($lesson) ? old('objectives', $lesson->objectives) :  old('objectives') }}</textarea>
        </div>
    </div>
    @endif
    @if (\App\Settings::get_value('lesson_fulltext'))
    <div class="form-group row">
        <label class="col-lg-2 col-form-label">{{ __('messages.fulltext') }}</label>
        <div class="col-lg-10">
            <textarea name="full_text" class="form-control{{ $errors->has('full_text') ? ' is-invalid' : '' }}" @if (\App\Settings::get_value('lesson_fulltext_required')) required="" @endif>{{ isset($lesson) ? old('full_text', $lesson->full_text) :  old('full_text') }}</textarea>
        </div>
    </div>
    @endif
    @if (\App\Settings::get_value('lesson_thumbnail'))
    <div class="form-group row">
        <label for="cost" class="col-lg-2 col-form-label">{{ __('messages.thumbnail') }}</label>
        <div class="col-lg-10 input-file-wrapper">

            @if(isset($lesson) && $lesson->thumbnail)
                <div class="preview-section">
                    {!! $lesson->the_image() !!}
                </div>
            @else
                <div style="display: none" class="preview-section">
                    <img src="#" alt="thumbnail-image"/>
                </div>
            @endif

            <div class="input-section">
                <input type="file" class="insert-image {{ $errors->has('image') ? 'is-invalid' : '' }}" name="image" accept=".png,.jpg,.jpeg" @if (\App\Settings::get_value('lesson_thumbnail_required') && isset($lesson)) required="" @endif>
                <small id="fileHelp" class="form-text text-muted">{{ __('messages.acceptfiletypes') }}</small>
                <input type="hidden" name="update_image" value="false" class="file-update">
            </div>
        </div>
    </div>
    @endif
    @if (\App\Settings::get_value('exercises'))
    <div class="form-group row">
        <label for="cost" class="col-lg-2 col-form-label">{{ __('messages.exercises') }}</label>
        <div class="col-lg-10">
            <input type="button" value="{{ __('messages.add') }}" class="btn btn-primary btn-sm m-1 add_exercise">
            <div class="exercises_container">

            </div>
        </div>
    </div>
    @endif
    @if (\App\Settings::get_value('student_lesson_prep'))
    <div class="form-group row">
        <label class="col-lg-2 col-form-label">{{ __('messages.student-lesson-prep') }}</label>
        <div class="col-lg-10">
            <textarea name="student_lesson_prep" class="form-control{{ $errors->has('student_lesson_prep') ? ' is-invalid' : '' }}" @if (\App\Settings::get_value('student_lesson_prep_required')) required="" @endif>{{ isset($lesson) ? old('student_lesson_prep',$lesson->student_lesson_prep) : old('student_lesson_prep') }}</textarea>
        </div>
    </div>
    @endif
    @if (\App\Settings::get_value('vocab_list'))
    <div class="form-group row">
        <label class="col-lg-2 col-form-label">{{ __('messages.vocab-list') }}</label>
        <div class="col-lg-10">
            <textarea name="vocab_list" class="form-control{{ $errors->has('vocab_list') ? ' is-invalid' : '' }}" @if (\App\Settings::get_value('vocab_list_required')) required="" @endif>{{isset($lesson) ? old('vocab_list', $lesson->vocab_list) : old('vocab_list')}}</textarea>
        </div>
    </div>
    @endif
    @if (\App\Settings::get_value('homework'))
    <div class="form-group row">
        <label for="cost" class="col-lg-2 col-form-label">{{ __('messages.homework') }}</label>
        <div class="col-lg-10">
            <input type="button" value="{{ __('messages.add') }}" class="btn btn-primary btn-sm m-1 add_homework">
            <div class="homeworks_container">

            </div>
        </div>
    </div>
    @endif
    @if (\App\Settings::get_value('extra_materials_text'))
    <div class="form-group row">
        <label class="col-lg-2 col-form-label">{{ __('messages.extra-materials') }}</label>
        <div class="col-lg-10">
            <textarea name="extra_materials_text" class="form-control{{ $errors->has('extra_materials_text') ? ' is-invalid' : '' }}" @if (\App\Settings::get_value('extra_materials_text_required')) required="" @endif>{{ isset($lesson) ? old('extra_materials_text',$lesson->extra_materials_text) : old('extra_materials_text') }}</textarea>
            <br>
            @if(isset($lesson))
                <div class=" dropzone" id="extra_material_files"></div>
            @endif
        </div>
    </div>
    @endif
    @if(isset($lesson))
    @if (\App\Settings::get_value('downloadable_files'))
        <div class="form-group row">
            <label for="cost" class="col-sm-12 col-lg-2 col-form-label">{{ __('messages.downloadablefiles') }}</label>
            <div class="col-lg-10">
                <div class="dropzone" id="downloadable_files"></div>
            </div>
        </div>
    @endif
    @if (\App\Settings::get_value('pdf_files'))
        <div class="form-group row">
            <label for="cost" class="col-lg-2 col-form-label">{{ __('messages.pdffiles') }}</label>
            <div class="col-lg-10">
                <div class="dropzone" id="pdf_files"></div>
            </div>
        </div>
    @endif
    @if (\App\Settings::get_value('audio_files'))
        <div class="form-group row">
            <label for="cost" class="col-lg-2 col-form-label">{{ __('messages.audiofiles') }}</label>
            <div class="col-lg-10">
                <div class="dropzone" id="audio_files"></div>
            </div>
        </div>
    @endif
    @if (\App\Settings::get_value('lesson_video'))
        <div class="form-group row">
            <label for="cost" class="col-lg-2 col-form-label">{{ __('messages.videolink') }}</label>
            <div class="col-lg-10">
                <input type="button" value="{{ __('messages.add') }}" class="btn btn-primary btn-sm m-1 add_video">
                <div class="video_container">

                </div>
            </div>
        </div>
    
    @endif
    @endif
    @if (\App\Settings::get_value('lesson_teachers_notes'))
   
    <div class="form-group row">
        <label class="col-lg-2 col-form-label">{{ __('messages.teachers-notes') }}</label>
        <div class="col-lg-10">
            <textarea name="teachers_notes" class="form-control{{ $errors->has('teachers_notes') ? ' is-invalid' : '' }}" @if (\App\Settings::get_value('lesson_teachers_notes_required')) required="" @endif>{{ isset($lesson) ? old('teachers_notes', $lesson->teachers_notes) : old('teachers_notes')}}</textarea>
        </div>
    </div>
    @endif
    @if (\App\Settings::get_value('lesson_teachers_prep'))
   
    <div class="form-group row">
        <label class="col-lg-2 col-form-label">{{ __('messages.teachers-prep') }}</label>
        <div class="col-lg-10">
            <textarea name="teachers_prep" class="form-control{{ $errors->has('teachers_prep') ? ' is-invalid' : '' }}" @if (\App\Settings::get_value('lesson_teachers_prep_required')) required="" @endif>{{ isset($lesson) ? old('teachers_prep', $lesson->teachers_prep ) : old('teachers_prep') }}</textarea>
        </div>
    </div>
    @endif