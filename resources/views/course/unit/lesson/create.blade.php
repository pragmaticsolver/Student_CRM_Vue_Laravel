@extends('layouts.app')

@section('content')
    <div class="row justify-content-center">
        @include('partials.success')
        @include('partials.error')
        <div class="col-12">
            <h1>{{ __('messages.lessonadd') }}</h1>
            <form id="add_lesson_form" method="POST" action="{{ route('lesson.store') }}" enctype="multipart/form-data">

                @include('course.unit.lesson.edit-fields')

	          	<div class="form-group row">
	            	<label class="col-lg-2 col-form-label"></label>
	            	<div class="col-lg-10">
	              		<input name="add" type="submit" value="{{ __('messages.add') }}" class="form-control btn-success">
	            	</div>
	          	</div>
	        </form>
      	</div>
    </div>
@endsection

@push('scripts')
<script src="{{ asset('public'.mix('js/lesson-form.js')) }}"></script>
<script>
    window.addEventListener('DOMContentLoaded', function(){
        const lessonId = $('input[name="lesson_id"]').val();
        const userId = $('input[name="user_id"]').val();
        initLessonFrom($('#add_lesson_form'),lessonId, userId);
    });
</script>
@endpush
