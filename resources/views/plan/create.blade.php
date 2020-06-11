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
	        <form method="POST" action="{{ route('plan.store') }}">
	        	@csrf
	          	<h1>{{ __('messages.addplan') }}</h1>
	          	<div class="form-group row">
	            	<label class="col-lg-2 col-form-label">{{ __('messages.paymentprice') }}</label>
	            	<div class="col-lg-10">
	              		<input name="cost" type="number" class="form-control{{ $errors->has('cost') ? ' is-invalid' : '' }}" value="{{ old('cost') }}" required="">
	            	</div>
	         	</div>
	         	<div class="form-group row">
	            	<label class="col-lg-2 col-form-label">{{ __('messages.teachersalary') }}</label>
	            	<div class="col-lg-10">
	              		<input name="cost_to_teacher" type="number" class="form-control{{ $errors->has('cost_to_teacher') ? ' is-invalid' : '' }}" value="{{ old('cost_to_teacher') }}" required="">
	            	</div>
	         	</div>
	         	<div class="form-group row">
	            	<label class="col-lg-2 col-form-label">{{ __('messages.pointvalue') }}</label>
	            	<div class="col-lg-10">
	              		<select name="points" value="{{ old('points') }}" class="form-control{{ $errors->has('points') ? ' is-invalid' : '' }}" required>
                        	<option value="">{{ __('messages.selectpointvalue') }}</option>
                        	<option value="15" <?php if(old('points') == '15') echo 'selected'; ?>>15</option>
                        	<option value="30" <?php if(old('points') == '30') echo 'selected'; ?>>30</option>
                      	</select>
	            	</div>
	         	</div>
	         	@if(!$cancel_types->isEmpty())
	         	<div class="form-group row">
	         		<div class="col-lg-2">
	         			<select class="form-control" name="cancel_type_1">
	         				<option value="">Select a cancel type</option>
	         				@foreach($cancel_types as $cancel_type)
	         				<option value="{{$cancel_type->id}}">{{$cancel_type->name}}</option>
	         				@endforeach
	         			</select>
	         		</div>
	         		<div class="col-lg-5">
	         			<input class="form-control" type="text" name="points_1" placeholder="Points">
	         		</div>
	         		<div class="col-lg-5">
	         			<input class="form-control" type="text" name="salary_1" placeholder="Salary">
	         		</div>
	         	</div>
	         	<div class="form-group row">
	         		<div class="col-lg-2">
	         			<select class="form-control" name="cancel_type_2">
	         				<option value="">Select a cancel type</option>
	         				@foreach($cancel_types as $cancel_type)
	         				<option value="{{$cancel_type->id}}">{{$cancel_type->name}}</option>
	         				@endforeach
	         			</select>
	         		</div>
	         		<div class="col-lg-5">
	         			<input class="form-control" type="text" name="points_2" placeholder="Points">
	         		</div>
	         		<div class="col-lg-5">
	         			<input class="form-control" type="text" name="salary_2" placeholder="Salary">
	         		</div>
	         	</div>
	         	<div class="form-group row">
	         		<div class="col-lg-2">
	         			<select class="form-control" name="cancel_type_3">
	         				<option value="">Select a cancel type</option>
	         				@foreach($cancel_types as $cancel_type)
	         				<option value="{{$cancel_type->id}}">{{$cancel_type->name}}</option>
	         				@endforeach
	         			</select>
	         		</div>
	         		<div class="col-lg-5">
	         			<input class="form-control" type="text" name="points_3" placeholder="Points">
	         		</div>
	         		<div class="col-lg-5">
	         			<input class="form-control" type="text" name="salary_3" placeholder="Salary">
	         		</div>
	         	</div>
	         	@endif
	          	<div class="form-group row">
	            	<label class="col-lg-2 col-form-label"></label>
	            	<div class="col-lg-10">
	              		<input name="add" type="submit" value="{{ __('messages.addplan') }}" class="form-control btn-success">
	            	</div>
	          	</div>
	        </form>
      	</div>
    </div>
@endsection
