@extends('layouts.app')

@section('content')
    <div class="row justify-content-center">
        <div class="col-12">
        	@if($errors->any())
		      	<div class="alert alert-danger">
		        	<ul>
		            	@foreach($errors->all() as $error)
		              		<li>{{ $error }}</li>
		            	@endforeach
		        	</ul>
		      	</div><br/>
		    @endif
		    @if($type == 'once')
		        <form method="POST" action="{{ route('schedule.store', $type) }}">
		        	@csrf
		          	<h1>{{ __('messages.addoneoffschedule') }}</h1>
		          	<div class="form-group row">
		            	<label class="col-lg-2 col-form-label">クラス名</label>
		            	<div class="col-lg-10">
		            		<select name="class_id" class="form-control" required="">
				                <option value="">{{ __('messages.selectclass') }}</option>
				                @if(!$classes->isEmpty())
				                	@foreach($classes as $class)
				                		<option value="{{$class->id}}" <?php if($class->id == old('class_id')) echo 'selected'; ?>>{{$class->title}}</option>
				                	@endforeach
				                @endif
				            </select>
		            	</div>
		         	</div>
		          	<div class="form-group row">
		            	<label class="col-lg-2 col-form-label">{{ __('messages.date') }}</label>
		           	 	<div class="col-lg-10">
			              	<input name="date" type="date" class="form-control{{ $errors->has('date') ? ' is-invalid' : '' }}" value="{{ old('date') }}" required="">
		            	</div>
		          	</div>
		          	<div class="form-group row">
		            	<label class="col-lg-2 col-form-label">{{ __('messages.starttime') }}</label>
		           	 	<div class="col-lg-10">
			              	<input name="start_time" type="time" class="form-control{{ $errors->has('start_time') ? ' is-invalid' : '' }}" value="{{ old('start_time') }}" required="">
		            	</div>
		          	</div>
		          	<div class="form-group row">
		            	<label class="col-lg-2 col-form-label">{{ __('messages.endtime') }}</label>
		           	 	<div class="col-lg-10">
			              	<input name="end_time" type="time" class="form-control{{ $errors->has('end_time') ? ' is-invalid' : '' }}" value="{{ old('end_time') }}" required="">
		            	</div>
		          	</div>
		          	<div class="form-group row">
		            	<label class="col-lg-2 col-form-label">{{ __('messages.classteacher') }}</label>
		            	<div class="col-lg-10">
		            		<select name="teacher_id" class="form-control" required="">
				                <option value="">{{ __('messages.selectteacher') }}</option>
				                @if(!$teachers->isEmpty())
				                	@foreach($teachers as $teacher)
				                		<option value="{{$teacher->id}}" <?php if($teacher->id == old('teacher_id')) echo 'selected'; ?>>{{$teacher->nickname}}</option>
				                	@endforeach
				                @endif
				            </select>
		            	</div>
		         	</div>

                    <div class="form-group row">
                        <label class="col-lg-2 col-form-label">{{ __('messages.course') }}</label>
                        <div class="col-lg-10">
                            <select name="course_id" class="form-control">
                                <option value="">{{ __('messages.selectcourse') }}</option>
                                @if(!$courses->isEmpty())
                                    @foreach($courses as $course)
                                        <option value="{{$course->id}}" <?php if($course->id == old('course_id')) echo 'selected'; ?>>{{$course->title}}</option>
                                    @endforeach
                                @endif
                            </select>
                        </div>
                    </div>

		          	<div class="form-group row">
		            	<label class="col-lg-2 col-form-label"></label>
		            	<div class="col-lg-10">
		              		<input name="add" type="submit" value="スケジュールする" class="form-control btn-success">
		            	</div>
		          	</div>
		        </form>
	        @elseif($type == 'repeat')
	        	<form method="POST" action="{{ route('schedule.store', $type) }}">
		        	@csrf
		          	<h1>{{ __('messages.addrepeatschedule') }}</h1>
		          	<div class="form-group row">
		            	<label class="col-lg-2 col-form-label">{{ __('messages.classname') }}</label>
		            	<div class="col-lg-10">
		            		<select name="class_id" class="form-control" required="">
				                <option value="">{{ __('messages.selectclass') }}</option>
				                @if(!$classes->isEmpty())
				                	@foreach($classes as $class)
				                		<option value="{{$class->id}}" <?php if($class->id == old('class_id')) echo 'selected'; ?>>{{$class->title}}</option>
				                	@endforeach
				                @endif
				            </select>
		            	</div>
		         	</div>
		          	<div class="form-group row">
		            	<label class="col-lg-2 col-form-label">{{ __('messages.dayofweek') }}</label>
		           	 	<div class="col-lg-10">
			              	<select name="day_of_week" class="form-control" required="">
				                <option value="">{{ __('messages.selectdayofweek') }}</option>
				                <option value="Monday" <?php if(old('day_of_week') == 'Monday') echo 'selected'; ?>>{{ __('messages.monday') }}</option>
				                <option value="Tuesday" <?php if(old('day_of_week') == 'Tuesday') echo 'selected'; ?>>{{ __('messages.tuesday') }}</option>
				                <option value="Wednesday" <?php if(old('day_of_week') == 'Wednesday') echo 'selected'; ?>>{{ __('messages.wednesday') }}</option>
				                <option value="Thursday" <?php if(old('day_of_week') == 'Thursday') echo 'selected'; ?>>{{ __('messages.thursday') }}</option>
				                <option value="Friday" <?php if(old('day_of_week') == 'Friday') echo 'selected'; ?>>{{ __('messages.friday') }}</option>
				                <option value="Saturday" <?php if(old('day_of_week') == 'Saturday') echo 'selected'; ?>>{{ __('messages.saturday') }}</option>
				                <option value="Sunday" <?php if(old('day_of_week') == 'Sunday') echo 'selected'; ?>>{{ __('messages.sunday') }}</option>
			              	</select>
		            	</div>
		          	</div>
		          	<div class="form-group row">
		            	<label class="col-lg-2 col-form-label">{{ __('messages.starttime') }}</label>
		           	 	<div class="col-lg-10">
			              	<input name="start_time" type="time" class="form-control{{ $errors->has('start_time') ? ' is-invalid' : '' }}" value="{{ old('start_time') }}" required="">
		            	</div>
		          	</div>

		          	<div class="form-group row">
		            	<label class="col-lg-2 col-form-label">{{ __('messages.endtime') }}</label>
		           	 	<div class="col-lg-10">
			              	<input name="end_time" type="time" class="form-control{{ $errors->has('end_time') ? ' is-invalid' : '' }}" value="{{ old('end_time') }}" required="">
		            	</div>
		          	</div>

                    <div class="form-group row">
                        <label class="col-lg-2 col-form-label">{{ __('messages.startdate') }}</label>
                        <div class="col-lg-10">
                            <input name="start_date" type="date" class="form-control{{ $errors->has('start_date') ? ' is-invalid' : '' }}" value="{{ old('start_date') }}" required="">
                        </div>
                    </div>

                    <div class="form-group row">
                        <label class="col-lg-2 col-form-label">{{ __('messages.enddate') }}</label>
                        <div class="col-lg-10">
                            <input name="end_date" type="date" class="form-control{{ $errors->has('end_date') ? ' is-invalid' : '' }}" value="{{ old('end_date') }}" required="">
                        </div>
                    </div>

		          	<div class="form-group row">
		            	<label class="col-lg-2 col-form-label">{{ __('messages.classteacher') }}</label>
		            	<div class="col-lg-10">
		            		<select name="teacher_id" class="form-control" required="">
				                <option value="">{{ __('messages.selectteacher') }}</option>
				                @if(!$teachers->isEmpty())
				                	@foreach($teachers as $teacher)
				                		<option value="{{$teacher->id}}" <?php if($teacher->id == old('teacher_id')) echo 'selected'; ?>>{{$teacher->nickname}}</option>
				                	@endforeach
				                @endif
				            </select>
		            	</div>
		         	</div>

                    <div class="form-group row">
                        <label class="col-lg-2 col-form-label">{{ __('messages.course') }}</label>
                        <div class="col-lg-10">
                            <select name="course_id" class="form-control">
                                <option value="">{{ __('messages.selectcourse') }}</option>
                                @if(!$courses->isEmpty())
                                    @foreach($courses as $course)
                                        <option value="{{$course->id}}" <?php if($course->id == old('course_id')) echo 'selected'; ?>>{{$course->title}}</option>
                                    @endforeach
                                @endif
                            </select>
                        </div>
                    </div>

		          	<div class="form-group row">
		            	<label class="col-lg-2 col-form-label"></label>
		            	<div class="col-lg-10">
		              		<input name="add" type="submit" value="{{ __('messages.schedule') }}" class="form-control btn-success">
		            	</div>
		          	</div>
		        </form>

	        @endif
      	</div>
    </div>
@endsection
