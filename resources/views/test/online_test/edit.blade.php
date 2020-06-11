@extends('layouts.app')

@section('content')
    <div class="row justify-content-center">
        @include('partials.success')
        @include('partials.error')
        <div class="col-12">
          <form method="POST" action="{{ route('test.update', $test->id) }}" enctype="multipart/form-data">
            @method('PATCH')
            @csrf
            <h1>{{ __('messages.edittest') }}</h1>
            <div class="form-group row">
              <label class="col-lg-2 col-form-label">{{ __('messages.name') }}</label>
              <div class="col-lg-10">
                  <input name="name" type="text" value="{{ empty(old('name')) ? $test->name : old('name') }}" class="form-control{{ $errors->has('name') ? ' is-invalid' : '' }}" required="">
              </div>
            </div>

            <div class="form-group row">
              <label class="col-lg-2 col-form-label">{{ __('messages.course') }}</label>
              <div class="col-lg-10">
                  <select name="course_id" class="form-control" required="">
                      <option value="">{{ __('messages.selectcourse') }}</option>
                      @if(!$courses->isEmpty())
                          @foreach($courses as $course)
                              <option value="{{$course->id}}" @if($test->course_id == $course->id) selected @endif>{{$course->title}}</option>
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
                                  @if($unit->id == $test->unit_id) selected @endif>
                                  {{$unit->name}}
                              </option>
                          @endforeach
                      @endif
                  </select>
              </div>
            </div>

            <div class="form-group row">
              <label class="col-lg-2 col-form-label">{{ __('messages.lesson') }}</label>
              <div class="col-lg-10">
                  <select name="lesson_id" class="form-control" required="">
                      <option value="">{{ __('messages.selectlesson') }}</option>
                      @if(!$lessons->isEmpty())
                          @foreach($lessons as $lesson)
                              <option
                                  value="{{ $lesson->id }}" data-course="{{ $lesson->course->id }}" data-unit="{{ $lesson->unit->id }}" class="option-lesson"
                                  @if($lesson->id == $test->lesson_id) selected @endif>
                                  {{ $lesson->title }}
                              </option>
                          @endforeach
                      @endif
                  </select>
              </div>
            </div>

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
