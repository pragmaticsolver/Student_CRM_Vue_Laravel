@extends('layouts.app')

@section('content')
    <div class="row justify-content-center">
        @include('partials.success')
        @include('partials.error')
        <div class="col-12">
          <form method="POST" action="{{ route('unit.update', $unit->id) }}" enctype="multipart/form-data">
            @method('PATCH')
            @csrf
            <h1>{{ __('messages.editunit') }}</h1>
                <div class="form-group row">
                  <label class="col-lg-2 col-form-label">{{ __('messages.name') }}</label>
                  <div class="col-lg-10">
                      <input name="name" type="text" value="{{empty(old('name')) ? $unit->name : old('name')}}" class="form-control{{ $errors->has('name') ? ' is-invalid' : '' }}" required="">
                  </div>
                </div>
                <div class="form-group row">
                    <label class="col-lg-2 col-form-label">{{ __('messages.objectives') }}</label>
                    <div class="col-lg-10">
                        <textarea name="objectives" id="objectives" rows="2" class="form-control{{ $errors->has('objectives') ? ' is-invalid' : '' }}"  required>{{ old('objectives',$unit->objectives) }}</textarea>
                    </div>
                </div>
              <div class="form-group row">
                  <label class="col-lg-2 col-form-label">{{ __('messages.course') }}</label>
                  <div class="col-lg-10">
                      <select name="course_id" class="form-control" required="">
                          <option value="">{{ __('messages.selectcourse') }}</option>
                          @if(!$courses->isEmpty())
                              @foreach($courses as $course)
                                  <option value="{{$course->id}}" @if($course->id == $unit->course_id) selected @endif>{{$course->title}}</option>
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
