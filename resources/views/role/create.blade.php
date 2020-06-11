@extends('layouts.app')

@section('content')
    <div class="row justify-content-center">
        <div class="col-12">
            @include('partials.error')
            @include('partials.success')
	        <form method="POST" action="{{ route('roles.store') }}">
	        	@csrf
	          	<h1>{{ __('messages.addrole') }}</h1>
	          	<div class="form-group row">
	            	<label class="col-lg-2 col-form-label">{{ __('messages.name') }}</label>
	            	<div class="col-lg-10">
	              		<input name="name" type="text" class="form-control{{ $errors->has('name') ? ' is-invalid' : '' }}" value="{{ old('name') }}" required="">
	            	</div>
				 </div>
				 <div class="form-group row">
					<label class="col-lg-2 col-form-label">{{ __('messages.defaultlanguage') }}</label>
					<div class="col-lg-10">
						<select name="default_lang" class="form-control {{ $errors->has('default_lang') ? ' is-invalid' : '' }}">
							<option value="">Select Language</option>  
							<option value="en" <?php if(old('default_lang',$default_lang) == 'en') echo 'selected'; ?>>English</option>
							<option value="ja" <?php if(old('default_lang',$default_lang) == 'ja') echo 'selected'; ?>>Japanese</option>
						</select>
					</div>
				</div>
	         	<div class="form-group row">
	            	<label class="col-lg-2 col-form-label">{{ __('messages.permissions') }}</label>
	            	<div class="col-lg-10 row">
	              		@foreach ($permissions as $permission)
				        <div class="col-md-3 padding-10">
				        	<div class="checkbox m-r-xs">
                                <input type="checkbox" name="permissions[]" value="{{$permission->id}}" id="checkbox{{$permission->id}}">
                                <label for="checkbox{{$permission->id}}">
                                    {{ucfirst($permission->name)}}
                                </label>
                            </div>
				        </div>
				        @endforeach
	            	</div>
	         	</div>
	         	<div class="form-group row">
                    <label class="col-lg-2 col-form-label">{{ __('messages.can-login') }}</label>
                    <div class="col-lg-10">
                        <input name="can_login" type="checkbox" class="form-control">
                    </div>
				</div>
				<div class="form-group row">
                    <label class="col-lg-2 col-form-label">{{ __('messages.login-redirect-path') }}</label>
                    <div class="col-lg-10">
                        <input name="login_redirect_path" type="text" class="form-control{{ $errors->has('login_redirect_path') ? ' is-invalid' : '' }}" value="{{ old('login_redirect_path') }}" required="">
                    </div>
                </div>
                <div class="form-group row">
                    <label class="col-lg-2 col-form-label">{{ __('messages.is-student') }}</label>
                    <div class="col-lg-10">
                        <input name="is_student" type="checkbox" class="form-control">
                    </div>
				</div>
				<div class="form-group row">
                    <label class="col-lg-2 col-form-label">{{ __('messages.sendlogindetails') }}</label>
                    <div class="col-lg-10">
                        <input name="send_login_details" type="checkbox" class="form-control" @if(old('send_login_details')) checked @endif>
                    </div>
                </div>
	          	<div class="form-group row">
		            <label class="col-lg-2 col-form-label"></label>
		            <div class="col-lg-10">
		              <input name="add" type="submit" value="{{ __('messages.addrole') }}" class="form-control btn-success">
		            </div>
		        </div>
	        </form>
      	</div>
    </div>
@endsection
