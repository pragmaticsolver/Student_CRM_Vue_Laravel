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
	        <form method="POST" action="{{ route('teacher.store') }}">
	        	@csrf
	          	<h1>{{ __('messages.addteacher')}}</h1>
	          	<div class="form-group row">
	            	<label class="col-lg-2 col-form-label">{{ __('messages.namekanji')}}</label>
	            	<div class="col-lg-10">
	              		<input name="fullname" type="text" class="form-control{{ $errors->has('fullname') ? ' is-invalid' : '' }}" value="{{ old('fullname') }}" required="">
	            	</div>
	         	</div>
	         	<div class="form-group row">
	            	<label class="col-lg-2 col-form-label">{{ __('messages.namekatakana')}}</label>
	            	<div class="col-lg-10">
	              		<input name="furigana" type="text" class="form-control{{ $errors->has('furigana') ? ' is-invalid' : '' }}" value="{{ old('furigana') }}" required="">
	            	</div>
	         	</div>
	         	<div class="form-group row">
	            	<label class="col-lg-2 col-form-label">{{ __('messages.nickname')}}</label>
	            	<div class="col-lg-10">
	              		<input name="nickname" type="text" class="form-control{{ $errors->has('nickname') ? ' is-invalid' : '' }}" value="{{ old('nickname') }}" required="">
	            	</div>
	         	</div>
				<div class="form-group row">
	            	<label class="col-lg-2 col-form-label">{{ __('messages.username')}}</label>
	            	<div class="col-lg-10">
	              		<input name="username" type="text" class="form-control{{ $errors->has('username') ? ' is-invalid' : '' }}" value="{{ old('username') }}" required="">
	            	</div>
	         	</div>
                <div class="form-group row">
                    <label class="col-lg-2 col-form-label">{{ __('messages.email') }}</label>
                    <div class="col-lg-10">
                        <input name="email" type="email" class="form-control{{ $errors->has('email') ? ' is-invalid' : '' }}" value="{{ old('email') }}" required="">
                    </div>
				</div>
				<div class="form-group row">
                    <label class="col-lg-2 col-form-label">{{ __('messages.zoom-email') }}</label>
                    <div class="col-lg-10">
                        <input name="zoom_email" type="email" class="form-control{{ $errors->has('zoom_email') ? ' is-invalid' : '' }}" value="{{ old('zoom_email') }}" >
                    </div>
                </div>
	         	<div class="form-group row">
	            	<label class="col-lg-2 col-form-label">{{ __('messages.birthday')}}</label>
	            	<div class="col-lg-10">
	              		<input name="birthday" type="date" class="form-control{{ $errors->has('birthday') ? ' is-invalid' : '' }}" value="{{ old('birthday') }}" required="">
	            	</div>
	         	</div>
	         	<div class="form-group row">
	            	<label class="col-lg-2 col-form-label">{{ __('messages.hometown')}}</label>
	            	<div class="col-lg-10">
	              		<input name="birthplace" type="text" class="form-control{{ $errors->has('birthplace') ? ' is-invalid' : '' }}" value="{{ old('birthplace') }}" required="">
	            	</div>
	         	</div>
	         	<div class="form-group row">
	            	<label class="col-lg-2 col-form-label">{{ __('messages.profile')}}</label>
	            	<div class="col-lg-10">
	            		<textarea name="profile" class="form-control{{ $errors->has('profile') ? ' is-invalid' : '' }}">{{ old('profile') }}</textarea>
	            	</div>
	         	</div>
                <div class="form-group row">
                    <label class="col-lg-2 col-form-label">{{ __('messages.calendar-color-coding') }}</label>
                    <div class="col-lg-10">
                        <div id="color_picker" data-default="{{ $default_color }}"></div>
                        <input type="hidden" value={{$default_color}} name="color_coding">
                    </div>
                </div>
                <div class="form-group row">
                    <label class="col-lg-2 col-form-label">{{ __('messages.password') }}</label>
                    <div class="col-lg-10">
                        <input name="password" type="password" class="form-control{{ $errors->has('password') ? ' is-invalid' : '' }}" required="">
                    </div>
                </div>
                <div class="form-group row">
                    <label class="col-lg-2 col-form-label">{{ __('messages.confirmpassword') }}</label>
                    <div class="col-lg-10">
                        <input name="password_confirmation" type="password" class="form-control{{ $errors->has('password_confirm') ? ' is-invalid' : '' }}" required="">
                    </div>
                </div>

	          	<div class="form-group row">
		            <label class="col-lg-2 col-form-label"></label>
		            <div class="col-lg-10">
		              <input name="add" type="submit" value="{{ __('messages.addteacher')}}" class="form-control btn-success">
		            </div>
		        </div>
	        </form>
      	</div>
    </div>
@endsection
