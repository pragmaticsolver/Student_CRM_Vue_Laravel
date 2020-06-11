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
		    @if($type == 'repeat')
		        <form method="POST" action="{{ route('yoyaku.store', 'repeat') }}">
		        	@csrf
		          	<h1>{{ __('messages.repeatclassyoyaku') }}</h1>
		          	<div class="form-group row">
		            	<label class="col-lg-2 col-form-label">{{ __('messages.class') }}</label>
		            	<div class="col-lg-10">
		            		<select name="schedule_id" class="form-control" required="">
				                <option value="">{{ __('messages.selectclass') }}</option>
				                @if(!$weekly_schedules->isEmpty())
				                	@foreach($weekly_schedules as $schedule)
				                		<option value="{{$schedule->id}}" <?php if($schedule->id == old('schedule_id')) echo 'selected'; ?>>{{$schedule->title}} {{$schedule->day_of_week}} {{$schedule->start_time}}-{{$schedule->end_time}}</option>
				                	@endforeach
				                @endif
				            </select>
		            	</div>
		         	</div>
		         	<div class="form-group row">
		            	<label class="col-lg-2 col-form-label">{{ __('messages.name') }}</label>
		            	<div class="col-lg-10">
		            		<select name="customer_id" class="form-control" required="">
				                <option value="">{{ __('messages.selectstudent') }}</option>
				                @if(!$students->isEmpty())
				                	@foreach($students as $student)
				                		<option value="{{$student->id}}" <?php if($student->id == old('customer_id')) echo 'selected'; ?>>{{$student->lastname_kanji}} {{$student->firstname_kanji}}</option>
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
            			<label class="col-sm-2 col-form-label">{{ __('messages.triallesson') }}</label>
            			<div class="col-sm-10">
              				<input type="checkbox" name="taiken" class="form-control">
            			</div>
          			</div>
		          	<div class="form-group row">
		            	<label class="col-lg-2 col-form-label"></label>
		            	<div class="col-lg-10">
		              		<input name="add" type="submit" value="{{ __('messages.reserve') }}" class="form-control btn-success">
		            	</div>
		          	</div>
		        </form>
		    @elseif($type == 'once')
		    	<form method="POST" action="{{ route('yoyaku.store', 'once') }}">
		        	@csrf
		          	<h1>{{ __('messages.oneoffclassreservation') }}</h1>
		          	<div class="form-group row">
		            	<label class="col-lg-2 col-form-label">{{ __('messages.class') }}</label>
		            	<div class="col-lg-10">
		            		<select name="schedule_id" class="form-control" required="">
				                <option value="">{{ __('messages.selectclass') }}</option>
				                @if(!$once_schedules->isEmpty())
				                	@foreach($once_schedules as $schedule)
				                		<option value="{{$schedule->id}}" <?php if($schedule->id == old('schedule_id')) echo 'selected'; ?>>{{$schedule->title}} {{$schedule->date}} {{$schedule->start_time}}-{{$schedule->end_time}}</option>
				                	@endforeach
				                @endif
				            </select>
		            	</div>
		         	</div>
		          	<div class="form-group row">
		            	<label class="col-lg-2 col-form-label">{{ __('messages.name') }}</label>
		            	<div class="col-lg-10">
		            		<select name="customer_id" class="form-control" required="">
				                <option value="">{{ __('messages.selectstudent') }}</option>
				                @if(!$students->isEmpty())
				                	@foreach($students as $student)
				                		<option value="{{$student->id}}" <?php if($student->id == old('customer_id')) echo 'selected'; ?>>{{$student->lastname_kanji}} {{$student->firstname_kanji}}</option>
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
            			<label class="col-sm-2 col-form-label">{{ __('messages.triallesson') }}</label>
            			<div class="col-sm-10">
              				<input type="checkbox" name="taiken" class="form-control">
            			</div>
          			</div>
		          	<div class="form-group row">
		            	<label class="col-lg-2 col-form-label"></label>
		            	<div class="col-lg-10">
		              		<input name="add" type="submit" value="{{ __('messages.reserve') }}" class="form-control btn-success">
		            	</div>
		          	</div>
		        </form>
		    @endif
      	</div>
    </div>
@endsection
