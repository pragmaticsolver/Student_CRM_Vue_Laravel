@extends('layouts.app')

@section('content')
    <div class="row justify-content-center">
        @include('partials.success')
        @include('partials.error')
        <div class="col-12">
          <form method="POST" action="{{ route('comment_template.update', $comment_template->id) }}" enctype="multipart/form-data">
            @method('PATCH')
            @csrf

              <h1>{{ __('messages.addcommenttemplate') }}</h1>
              <div class="form-group row">
                  <label class="col-lg-2 col-form-label">{{ __('messages.name') }}</label>
                  <div class="col-lg-10">
                      <input name="name" type="text" class="form-control{{ $errors->has('name') ? ' is-invalid' : '' }}" value="{{ $comment_template->name }}" required="">
                  </div>
              </div>

              <p>You can use these parameters: <b>{student_name} {class_name} {test} {score} {date} {comment}</b></p>
              <div class="form-group row">
                  <label class="col-lg-2 col-form-label">{{ __('messages.contenten') }}</label>
                  <div class="col-lg-10">
                      <textarea name="content_en" class="form-control" rows="7">{!! $comment_template->content_en !!}</textarea>
                  </div>
              </div>

              <div class="form-group row">
                  <label class="col-lg-2 col-form-label">{{ __('messages.contentja') }}</label>
                  <div class="col-lg-10">
                      <textarea name="content_ja" class="form-control" rows="7">{!! $comment_template->content_ja !!}</textarea>
                  </div>
              </div>

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
