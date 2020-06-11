@extends('layouts.app')

@section('content')
    <div class="row justify-content-center">
        <div class="col-12 lesson-boxs">
            <h1>{{ __('messages.lesson-settings') }}</h1>
            @if(session()->get('success'))
                <div class="alert alert-success">
                    {{ session()->get('success') }}
                </div><br/>
            @endif
            @if($errors->any())
                <div class="alert alert-danger">
                  <ul>
                      @foreach($errors->all() as $error)
                          <li>{{ $error }}</li>
                      @endforeach
                  </ul>
                </div><br/>
            @endif
            @include('partials.error')
            <form method="POST" action="{{ route('lesson-settings.update') }}" enctype="multipart/form-data">
                @csrf
                <div class="form-group row">
                    <div class="col-lg-3">
                    </div>
                    <div class="col-lg-3">
                    </div>
                    <label class="col-lg-3 col-form-label font-weight-bold">{{ __('messages.is_required') }}</label>
                </div>
                <div class="form-group row">
                    <label class="col-lg-3 col-form-label">{{ __('messages.lesson_description') }}</label>
                    <div class="col-lg-3">
                        <input name="lesson_description" data-toggle="toggle" type="checkbox" {{ old('lesson_description',$lesson_description) ? 'checked' : '' }}>
                    </div>
                    <div class="col-lg-3">
                        <input name="lesson_description_required" data-onstyle="danger" data-offstyle="success" data-toggle="toggle" type="checkbox" {{ old('lesson_description_required',$lesson_description_required) ? 'checked' : '' }}>
                    </div>
                </div>
                <div class="form-group row">
                    <label class="col-lg-3 col-form-label">{{ __('messages.lesson_objectives') }}</label>
                    <div class="col-lg-3">
                        <input name="lesson_objectives" data-toggle="toggle" type="checkbox" {{ old('lesson_objectives',$lesson_objectives) ? 'checked' : '' }}>
                    </div>
                    <div class="col-lg-3">
                        <input name="lesson_objectives_required" data-onstyle="danger" data-offstyle="success" data-toggle="toggle" type="checkbox" {{ old('lesson_objectives_required',$lesson_objectives_required) ? 'checked' : '' }}>
                    </div>
                </div>
                <div class="form-group row">
                    <label class="col-lg-3 col-form-label">{{ __('messages.lesson_fulltext') }}</label>
                    <div class="col-lg-3">
                        <input name="lesson_fulltext" data-toggle="toggle" type="checkbox" {{ old('lesson_fulltext',$lesson_fulltext) ? 'checked' : '' }}>
                    </div>
                    <div class="col-lg-3">
                        <input name="lesson_fulltext_required" data-toggle="toggle" data-onstyle="danger" data-offstyle="success" type="checkbox" {{ old('lesson_fulltext_required',$lesson_fulltext_required) ? 'checked' : '' }}>
                    </div>
                </div>
                <div class="form-group row">
                    <label class="col-lg-3 col-form-label">{{ __('messages.lesson_thumbnail') }}</label>
                    <div class="col-lg-3">
                        <input name="lesson_thumbnail" data-toggle="toggle" type="checkbox" {{ old('lesson_thumbnail',$lesson_thumbnail) ? 'checked' : '' }}>
                    </div>
                    <div class="col-lg-3">
                        <input name="lesson_thumbnail_required" data-toggle="toggle" data-onstyle="danger" data-offstyle="success" type="checkbox" {{ old('lesson_thumbnail_required',$lesson_thumbnail_required) ? 'checked' : '' }}>
                    </div>
                </div>
                <div class="form-group row">
                    <label class="col-lg-3 col-form-label">{{ __('messages.lesson_video') }}</label>
                    <div class="col-lg-3">
                        <input name="lesson_video" data-toggle="toggle" type="checkbox" {{ old('lesson_video',$lesson_video) ? 'checked' : '' }}>
                    </div>
                   
                    <div class="col-lg-3">
                        <input name="lesson_video_required" data-onstyle="danger" data-offstyle="success" data-toggle="toggle" type="checkbox" {{ old('lesson_video_required',$lesson_video_required) ? 'checked' : '' }}>
                    </div>
               </div>
                <div class="form-group row">
                    <label class="col-lg-3 col-form-label">{{ __('messages.student_lesson_prep') }}</label>
                    <div class="col-lg-3">
                        <input name="student_lesson_prep" data-toggle="toggle" type="checkbox" {{ old('student_lesson_prep',$student_lesson_prep) ? 'checked' : '' }}>
                    </div>
                    <div class="col-lg-3">
                        <input name="student_lesson_prep_required" data-onstyle="danger" data-offstyle="success" data-toggle="toggle" type="checkbox" {{ old('student_lesson_prep_required',$student_lesson_prep_required) ? 'checked' : '' }}>
                    </div>
               </div>
                
                <div class="form-group row">
                    <label class="col-lg-3 col-form-label">{{ __('messages.vocab_list') }}</label>
                    <div class="col-lg-3">
                        <input name="vocab_list" data-toggle="toggle" type="checkbox" {{ old('vocab_list',$vocab_list) ? 'checked' : '' }}>
                    </div>
                    <div class="col-lg-3">
                        <input name="vocab_list_required" data-toggle="toggle" data-onstyle="danger" data-offstyle="success" type="checkbox" {{ old('vocab_list_required',$vocab_list_required) ? 'checked' : '' }}>
                    </div>
               </div>
                <div class="form-group row">
                    <label class="col-lg-3 col-form-label">{{ __('messages.extra_materials_text') }}</label>
                    <div class="col-lg-3">
                        <input name="extra_materials_text" data-toggle="toggle" type="checkbox" {{ old('extra_materials_text',$extra_materials_text) ? 'checked' : '' }}>
                    </div>
                    <div class="col-lg-3">
                        <input name="extra_materials_text_required" data-onstyle="danger" data-toggle="toggle" data-offstyle="success" type="checkbox" {{ old('extra_materials_text_required',$extra_materials_text_required) ? 'checked' : '' }}>
                    </div>
               </div>
                <div class="form-group row">
                    <label class="col-lg-3 col-form-label">{{ __('messages.lesson_teachers_notes') }}</label>
                    <div class="col-lg-3">
                        <input name="lesson_teachers_notes" data-toggle="toggle" type="checkbox" {{ old('lesson_teachers_notes',$lesson_teachers_notes) ? 'checked' : '' }}>
                    </div>
                    <div class="col-lg-3">
                        <input name="lesson_teachers_notes_required" data-toggle="toggle" data-onstyle="danger" data-offstyle="success" type="checkbox" {{ old('lesson_teachers_notes_required',$lesson_teachers_notes_required) ? 'checked' : '' }}>
                    </div>
               </div>
                <div class="form-group row">
                    <label class="col-lg-3 col-form-label">{{ __('messages.lesson_teachers_prep') }}</label>
                    <div class="col-lg-3">
                        <input name="lesson_teachers_prep" data-toggle="toggle" type="checkbox" {{ old('lesson_teachers_prep',$lesson_teachers_prep) ? 'checked' : '' }}>
                    </div>
                    <div class="col-lg-3">
                        <input name="lesson_teachers_prep_required" data-toggle="toggle" data-onstyle="danger" data-offstyle="success" type="checkbox" {{ old('lesson_teachers_prep_required',$lesson_teachers_prep_required) ? 'checked' : '' }}>
                    </div>
               </div>
                <div class="form-group row">
                    <label class="col-lg-3 col-form-label">{{ __('messages.exercises') }}</label>
                    <div class="col-lg-3">
                        <input name="exercises" data-toggle="toggle" type="checkbox" {{ old('exercises',$exercises) ? 'checked' : '' }}>
                    </div>
                    <div class="col-lg-3">
                        <input name="exercises_required" data-toggle="toggle"   data-onstyle="danger"  data-offstyle="success" type="checkbox" {{ old('exercises_required',$exercises_required) ? 'checked' : '' }}>
                    </div>
               </div>
                <div class="form-group row">
                    <label class="col-lg-3 col-form-label">{{ __('messages.homework') }}</label>
                    <div class="col-lg-3">
                        <input name="homework" data-toggle="toggle" type="checkbox" {{ old('homework',$homework) ? 'checked' : '' }}>
                    </div>
                    <div class="col-lg-3">
                        <input name="homework_required" data-toggle="toggle" data-onstyle="danger" data-offstyle="success" type="checkbox" {{ old('homework_required',$homework_required) ? 'checked' : '' }}>
                    </div>
               </div>
                <div class="form-group row">
                    <label class="col-lg-3 col-form-label">{{ __('messages.downloadable_files') }}</label>
                    <div class="col-lg-3">
                        <input name="downloadable_files" data-toggle="toggle"  type="checkbox" {{ old('downloadable_files',$downloadable_files) ? 'checked' : '' }}>
                    </div>
                    <div class="col-lg-3">
                        <input name="downloadable_files_required" data-toggle="toggle" data-onstyle="danger" data-offstyle="success" type="checkbox" {{ old('downloadable_files_required',$downloadable_files_required) ? 'checked' : '' }}>
                    </div>
               </div>
                <div class="form-group row">
                    <label class="col-lg-3 col-form-label">{{ __('messages.pdf_files') }}</label>
                    <div class="col-lg-3">
                        <input name="pdf_files" data-toggle="toggle" type="checkbox" {{ old('pdf_files',$pdf_files) ? 'checked' : '' }}>
                    </div>
                    <div class="col-lg-3">
                        <input name="pdf_files_required" data-toggle="toggle" data-onstyle="danger" data-offstyle="success" type="checkbox" {{ old('pdf_files_required',$pdf_files_required) ? 'checked' : '' }}>
                    </div>
                </div>
                <div class="form-group row">
                    <label class="col-lg-3 col-form-label">{{ __('messages.audio_files') }}</label>
                    <div class="col-lg-3">
                        <input name="audio_files" data-toggle="toggle" type="checkbox" {{ old('audio_files',$audio_files) ? 'checked' : '' }}>
                    </div>
                    <div class="col-lg-3">
                        <input name="audio_files_required" data-toggle="toggle" data-onstyle="danger" data-offstyle="success" type="checkbox" {{ old('audio_files_required',$audio_files_required) ? 'checked' : '' }}>
                    </div>
               </div>
                
                <div class="form-group row">
                    <div class="col-lg-12">
                        <input name="edit" type="submit" value="{{ __('messages.edit') }}" class="btn btn-success form-control">
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection
@push('scripts')
<script src="//ajax.googleapis.com/ajax/libs/jquery/2.1.3/jquery.min.js"></script>
<link href="//gitcdn.github.io/bootstrap-toggle/2.2.2/css/bootstrap-toggle.min.css" rel="stylesheet">
<script src="//gitcdn.github.io/bootstrap-toggle/2.2.2/js/bootstrap-toggle.min.js"></script>
@endpush
