@extends('layouts.app')

@section('content')
    <div class="justify-content-center">
        @include('partials.success')
        @include('partials.error')
        <div class="clearfix mb-2 mt-2">
            <h1 class="float-left">{{ __('messages.coursedetails') }}</h1>
            <a href="{{ url('/unit/add?course_id='.$course->id) }}" class=""></a>
            <button type="button" id="add_unit_btn" class="btn btn-success float-right">{{ __('messages.unitadd') }}</button>
        </div>
        <table class="table table-striped table-bordered table-hover">
            <tbody>
                <tr>
                    <td>{{ __('messages.title') }}</td>
                    <td>{{ $course->title }}</td>
                </tr>
                <tr>
                    <td>{{ __('messages.description') }}</td>
                    <td>{{ $course->description }}</td>
                </tr>
                <tr>
                    <td>{{ __('messages.objectives') }}</td>
                    <td>{{ $course->objectives }}</td>
                </tr>
            </tbody>
        </table>
    </div>

    <div style="position:relative;width:100%;">
        <div id="class_unit_preloader" class="text-center;" style="width: 100%;height: 100%;position: absolute;top:0;
        left: 0;z-index:10;background:#58608852;">
            <div class="fa fa-spinner fa-spin" style="font-size:30px;position: relative;display: inline-block;position: absolute;left: 50%;top: 50%;
            text-align:center;"></div>
        </div>
        <div id="class-units-section">

        </div>
    </div>

@endsection

@push('modals')
    <div class="modal inmodal" id="AddUnitModal" tabindex="-1" role="dialog"  aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">{{ __('messages.unitadd') }}</h4>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-12">
                            <form id="add_unit_form" method="POST" action="{{ route('unit.store') }}">
                                @csrf
                                    <div class="form-group row">
                                        <label class="col-lg-2 col-form-label">{{ __('messages.name') }}</label>
                                        <div class="col-lg-10">
                                            <input name="name" type="text" class="form-control{{ $errors->has('name') ? ' is-invalid' : '' }}" value="{{ old('name') }}" required="">
                                        </div>
                                    </div>

                                    <div class="form-group row">
                                        <label class="col-lg-2 col-form-label">{{ __('messages.objectives') }}</label>
                                        <div class="col-lg-10">
                                            <textarea name="objectives" id="objectives" rows="2" class="form-control{{ $errors->has('objectives') ? ' is-invalid' : '' }}"  required>{{ old('objectives') }}</textarea>
                                        </div>
                                    </div>

                                    <input type="hidden" name="course_id" value="{{  $course->id }}">
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                                    <div class="col">
                                        <button type="submit" class="btn btn-success pull-right mx-1" name="add" id="submit_unit_btn">
                                            {{ __('messages.add') }}
                                            <span class="form_spinner">&nbsp<span class="fa fa-spinner fa-spin"></span></span>
                                        </button>
                                        <button type="button" class="btn btn-secondary pull-right mx-1" onclick="$('#AddUnitModal').modal('hide');">{{ __('messages.cancel') }}</button>
                                    </div>
                                </div>
                            </form>
                </div>
            </div>
        </div>
    </div>

    <div class="modal inmodal" id="EditUnitModal" tabindex="-1" role="dialog"  aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">{{ __('messages.editunit') }}</h4>
                </div>
                <div class="modal-body">
                    <div class="row mr-1">
                        <div class="col-12">
                            <form id="edit_unit_form" enctype="multipart/form-data">
                            <span class="form-fields"></span>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                            <div class="col">
                                <button type="submit" class="btn btn-success pull-right mx-1" name="add" id="edit_unit_sumbit_btn">
                                    {{ __('messages.edit') }}
                                    <span class="form_spinner">&nbsp<span class="fa fa-spinner fa-spin"></span></span>
                                </button>
                                <button type="button" class="btn btn-secondary pull-right mx-1" onclick="$('#EditUnitModal').modal('hide');">{{ __('messages.cancel') }}</button>
                            </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="modal inmodal" id="AddLessonModal" tabindex="-1" role="dialog"  aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title"></h4>
                </div>
                <div class="modal-body" style="max-height: 450px;overflow-y: scroll;">
                    <div class="row mr-1">
                        <div class="col-12">
                            <form id="add_lesson_form" enctype="multipart/form-data">
                                <input type="hidden" name="course_id" value="">
                                <input type="hidden" name="unit_id" value="">
                                <span class="form-fields"></span>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                            <div class="col">
                                <button type="submit" class="btn btn-success pull-right mx-1" name="add" id="submit_lesson_btn">
                                    {{ __('messages.add') }}
                                    <span class="form_spinner">&nbsp<span class="fa fa-spinner fa-spin"></span></span>
                                </button>
                                <button type="button" class="btn btn-secondary pull-right mx-1" onclick="$('#AddLessonModal').modal('hide');">{{ __('messages.cancel') }}</button>
                            </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="modal inmodal" id="EditLessonModal" tabindex="-1" role="dialog"  aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">{{ __('messages.editlesson') }}</h4>
                </div>
                <div class="modal-body" style="max-height: 450px;overflow-y: scroll;">
                    <div class="row mr-1">
                        <div class="col-12">
                            <form id="edit_lesson_form" enctype="multipart/form-data">
                            <span class="form-fields"></span>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                            <div class="col">
                                <button type="submit" class="btn btn-success pull-right mx-1" name="add" id="edit_lesson_sumbit_btn">
                                    {{ __('messages.edit') }}
                                    <span class="form_spinner">&nbsp<span class="fa fa-spinner fa-spin"></span></span>
                                </button>
                                <button type="button" class="btn btn-secondary pull-right mx-1" onclick="$('#EditLessonModal').modal('hide');">{{ __('messages.cancel') }}</button>
                            </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="modal inmodal" id="ReorderUnitsModal" tabindex="-1" role="dialog"  aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">{{ __('messages.reorderunits') }}</h4>
                </div>
                <div class="modal-body">
                    <div class="row mr-1">
                        <div class="col-12">
                            <form id="reorder_units_form" enctype="multipart/form-data">
                            <span class="form-fields"></span>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                            <div class="col">
                                <button type="submit" class="btn btn-success pull-right mx-1" name="add" id="reoder_units_sumbit_btn">
                                    {{ __('messages.save') }}
                                    <span class="form_spinner">&nbsp<span class="fa fa-spinner fa-spin"></span></span>
                                </button>
                                <button type="button" class="btn btn-secondary pull-right mx-1" onclick="$('#ReorderUnitsModal').modal('hide');">{{ __('messages.cancel') }}</button>
                            </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="modal inmodal" id="ReorderLessonsModal" tabindex="-1" role="dialog"  aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title"></h4>
                </div>
                <div class="modal-body">
                    <div class="row mr-1">
                        <div class="col-12">
                            <form id="reorder_lessons_form" enctype="multipart/form-data">
                            <span class="form-fields"></span>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                            <div class="col">
                                <button type="submit" class="btn btn-success pull-right mx-1" name="add" id="reoder_lessons_sumbit_btn">
                                    {{ __('messages.save') }}
                                    <span class="form_spinner">&nbsp<span class="fa fa-spinner fa-spin"></span></span>
                                </button>
                                <button type="button" class="btn btn-secondary pull-right mx-1" onclick="$('#ReorderLessonsModal').modal('hide');">{{ __('messages.cancel') }}</button>
                            </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    @include('course.unit.lesson.file-name')
@endpush

@push('scripts')
<script src="{{ asset('public'.mix('js/lesson-form.js')) }}"></script>
<script src="{{ asset('public'.mix('js/page/filename.js')) }}"></script>

{{-- Reordering doesnt work without this script on iphone --}}
<script defer src="{{ asset('public'.mix('js/vendor/jquery.ui.touch-punch.js')) }}"></script>
<script>
    add_lesson_form_html = `<?php echo $add_lesson_form_html ?>`;
    window.uploadLessonDownloadableFileUrl = "{{ route('lessonfile.upload',['1','']) }}";
    window.uploadLessonPdfFileUrl = "{{ route('lessonfile.upload',['2','']) }}";
    window.uploadLessonAudioFileUrl = "{{ route('lessonfile.upload',['3','']) }}";
    window.uploadLessonExtraMaterialFileUrl = "{{ route('lessonfile.upload',['4','']) }}";
    window.deleteLessonFileUrl = "{{ route('lessonfile.delete',['']) }}";

    getCourseUnitsUrl = "{{ route('course.units') }}";
    courseId = "{{  $course->id }}";
    var openedUnitsSections = [];
    var userId = "{{ \Auth::id() }}";
    var storeUnitUrl = "{{ route('unit.store') }}";
    var editCourseModalUrl = "{{ route('course.edit.modal', '') }}";
    var updateUnitUrl = "{{ route('unit.update', '') }}";
    var lessonStoreUrl = "{{ route('lesson.store') }}";
    var editLessonFieldsUrl = "{{ route('lesson.edit.fields', '') }}";
    var updateLessonUrl = "{{ route('lesson.update', '') }}";
    var reorderUnitsFormUrl = "{{ route('course.reorder_units.form', '') }}";
    var reorderUnitsSaveUrl = "{{ route('course.reorder_units.save', '') }}";
    var reorderLessonFormUrl = "{{ route('unit.reorder_lessons.form', '') }}";
    var reorderLessonSaveUrl = "{{ route('unit.reorder_lessons.save', '') }}";
    var lessonDestroyUrl = "{{ route('lesson.destroy', '') }}";
    var csrfToken = "{{ csrf_token() }}";
</script>
<script src="{{ asset('public'.mix('js/page/course/details.js')) }}"></script>
@endpush
