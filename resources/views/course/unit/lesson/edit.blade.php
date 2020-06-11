@extends('layouts.app')

@section('content')
    <div class="row justify-content-center">
        @include('partials.success')
        @include('partials.error')
        <div class="col-12">
            <h1>{{ __('messages.editlesson') }}</h1>
            <form id="edit_lesson_form" method="POST" action="{{ route('lesson.update', $lesson->id) }}" enctype="multipart/form-data">

                @include('course.unit.lesson.edit-fields')

                <div class="form-group row">
                    <label class="col-lg-2 col-form-label"></label>
                    <div class="col-lg-10">
                        <input name="edit" type="submit" value="{{ __('messages.edit') }}" class="btn btn-success form-control">
                    </div>
                </div>
            </div>
          </form>
      </div>
    </div>
@endsection

@push('scripts')
<script src="{{ asset('public'.mix('js/lesson-form.js')) }}"></script>
<script>
    window.uploadLessonDownloadableFileUrl = "{{ route('lessonfile.upload',['1','']) }}";
    window.uploadLessonPdfFileUrl = "{{ route('lessonfile.upload',['2','']) }}";
    window.uploadLessonAudioFileUrl = "{{ route('lessonfile.upload',['3','']) }}";
    window.uploadLessonExtraMaterialFileUrl = "{{ route('lessonfile.upload',['4','']) }}";
    window.deleteLessonFileUrl = "{{ route('lessonfile.delete',['']) }}";

    window.addEventListener('DOMContentLoaded', function(){
        const lessonId = $('input[name="lesson_id"]').val();
        const userId = $('input[name="user_id"]').val();
        initLessonFrom($('#edit_lesson_form'),lessonId, userId);
    });
</script>
@endpush
