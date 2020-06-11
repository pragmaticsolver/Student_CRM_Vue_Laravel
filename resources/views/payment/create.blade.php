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
	        <form method="POST" action="{{ route('payment.store', $customer_id) }}">
	        	@csrf
	          	<h1>{{ __('messages.addpayment') }}</h1>
	          	<div class="form-group row">
	            	<label class="col-lg-2 col-form-label">{{ __('messages.paymentprice') }}</label>
	            	<div class="col-lg-10">
	              		<input name="price" type="text" class="form-control{{ $errors->has('price') ? ' is-invalid' : '' }}" value="{{ old('price') }}" required="">
	            	</div>
	         	</div>
	         	<div class="form-group row">
	            	<label class="col-lg-2 col-form-label">{{ __('messages.paymentdate') }}</label>
	            	<div class="col-lg-10">
	              		<input name="date" type="date" class="form-control{{ $errors->has('date') ? ' is-invalid' : '' }}" value="{{ $date }}" required="">
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
	         	<div class="form-group row">
	            	<label class="col-lg-2 col-form-label">{{ __('messages.expirationdate') }}</label>
	            	<div class="col-lg-10">
	              		<input name="expiration_date" type="date" class="form-control{{ $errors->has('expiration_date') ? ' is-invalid' : '' }}" value="{{ $expiration_date }}">
	            	</div>
	         	</div>
	          	<div class="form-group row">
	            	<label class="col-lg-2 col-form-label"></label>
	            	<div class="col-lg-10">
	              		<input name="add" type="submit" value="{{ __('messages.addpayment') }}" class="form-control btn-success">
	            	</div>
	          	</div>
	        </form>
      	</div>
    </div>
@endsection
