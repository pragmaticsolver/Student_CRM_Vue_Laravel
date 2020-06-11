@extends('layouts.app')

@section('content')
    <div class="row justify-content-center">
        @include('partials.success')
        @include('partials.error')
        <div class="col-12">
	        <form method="POST" action="{{ route('test.store') }}">
	        	@csrf
	          	<h1>{{ __('messages.addtest') }}</h1>
	          	<div class="form-group row">
	            	<label class="col-lg-2 col-form-label">{{ __('messages.name') }}</label>
	            	<div class="col-lg-10">
	              		<input name="name" type="text" class="form-control{{ $errors->has('title') ? ' is-invalid' : '' }}" value="{{ old('name') }}" required="">
	            	</div>
	         	</div>

                <div class="form-group row">
                    <label class="col-lg-2 col-form-label">{{ __('messages.type') }}</label>
                    <div class="col-lg-10">
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" id="online" value="online" name="test_type" checked>
                            <label class="form-check-label" for="online">{{ __('messages.online') }}</label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" id="paper" value="paper" name="test_type">
                            <label class="form-check-label" for="paper">{{ __('messages.paper') }}</label>
                        </div>
                    </div>
                </div>


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


                <div class="test-type test-type-paper">

                    <div class="form-group row">
                        <label class="col-lg-2 col-form-label">{{ __('messages.totalscore') }}</label>
                        <div class="col-lg-10">
                            <input name="total_score" type="number" class="form-control{{ $errors->has('total_score') ? ' is-invalid' : '' }}" value="{{ old('total_score') }}">
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
