@extends('layouts.app')

@section('content')
    <div class="row justify-content-center">
        @include('partials.success')
        @include('partials.error')
        <div class="col-12">
	        <form method="POST" action="{{ route('assessment.store') }}">
	        	@csrf
                <h1>{{ __('messages.addassessment') }}</h1>
                @if($errors->any())
                    <div class="alert alert-danger">
                        <ul>
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div><br/>
                @endif
	          	<div class="form-group row">
	            	<label class="col-lg-2 col-form-label">{{ __('messages.name') }}</label>
	            	<div class="col-lg-10">
	              		<input name="name" type="text" class="form-control{{ $errors->has('title') ? ' is-invalid' : '' }}" value="{{ old('name') }}" required="">
	            	</div>
	         	</div>

                <div class="form-group row">
	            	<label class="col-lg-2 col-form-label">{{ __('messages.description') }}</label>
	            	<div class="col-lg-10">
                        <textarea name="description" rows="3" class="form-control" class="{{ $errors->has('description') ? ' is-invalid' : '' }}">{{ old('description') }}</textarea>
	            	</div>
                </div>
                 
                <div class="form-group row">
                    <label class="col-lg-2 col-form-label">{{ __('messages.type') }}</label>
                    <div class="col-lg-10">
                        <select name="assessment_type" class="form-control{{ $errors->has('assessment_type') ? ' is-invalid' : '' }}" required="">
                            <option value="">{{ __('messages.selecttype') }}</option>
                            <option value="automatic" @if(old('course_id') == 'automatic') selected @endif>{{ __('messages.automatic') }}</option>
                            <option value="manual" @if(old('course_id') == 'manual') selected @endif>{{ __('messages.manual') }}</option>
                        </select>
                    </div>
                </div>

                <div class="assessment-type assessment-type-automatic">
                    <div class="form-group row">
                        <label class="col-lg-2 col-form-label">{{ __('messages.course') }}</label>
                        <div class="col-lg-10">
                            <select name="course_id" class="form-control{{ $errors->has('course_id') ? ' is-invalid' : '' }}">
                                <option value="">{{ __('messages.selectcourse') }}</option>
                                @if(!$courses->isEmpty())
                                    @foreach($courses as $course)
                                        <option value="{{$course->id}}" @if($course->id == old('course_id')) selected @endif>{{$course->title}}</option>
                                    @endforeach
                                @endif
                            </select>
                        </div>
                    </div>

                    <div class="form-group row">
                        <label class="col-lg-2 col-form-label">{{ __('messages.unit') }}</label>
                        <div class="col-lg-10">
                            <select name="unit_id" class="form-control{{ $errors->has('unit_id') ? ' is-invalid' : '' }}">
                                <option value="">{{ __('messages.selectunit') }}</option>
                                @if(!$units->isEmpty())
                                    @foreach($units as $unit)
                                        <option
                                            value="{{$unit->id}}" data-course="{{ $unit->course->id }}" class="option-unit"
                                            @if($unit->id == old('unit_id')) selected @endif>
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
                            <select name="lesson_id" class="form-control{{ $errors->has('lesson_id') ? ' is-invalid' : '' }}">
                                <option value="">{{ __('messages.selectlesson') }}</option>
                                @if(!$lessons->isEmpty())
                                    @foreach($lessons as $lesson)
                                        <option
                                            value="{{ $lesson->id }}" data-course="{{ $lesson->course->id }}" data-unit="{{ $lesson->unit->id }}" class="option-lesson"
                                            @if($lesson->id == old('lesson_id')) selected @endif>
                                            {{ $lesson->title }}
                                        </option>
                                    @endforeach
                                @endif
                            </select>
                        </div>
                    </div>

                    <div class="form-group row">
                        <label class="col-lg-2 col-form-label">{{ __('messages.sendto') }}</label>
                        <div class="col-lg-10">
                            <select name="send_to" class="form-control{{ $errors->has('send_to') ? ' is-invalid' : '' }}">
                                <option value="">{{ __('messages.sendto') }}</option>
                                <option value="teacher">{{ __('messages.teacher') }}</option>
                                <option value="student">{{ __('messages.student') }}</option>
                            </select>
                        </div>
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
