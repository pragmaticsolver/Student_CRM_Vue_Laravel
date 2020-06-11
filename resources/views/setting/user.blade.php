@extends('layouts.app')

@section('content')
	<div class="row justify-content-center">
		<div class="col-12">
			@if(session()->get('success'))
                <div class="alert alert-success">
                    {{ session()->get('success') }}
                </div><br/>
            @endif
	        @if($errors->any())
	            <div class="alert alert-danger">
	              <ul>
	                  @foreach($errors->all() as $error)
	                      <li>{{ $error }}</li>
	                  @endforeach
	              </ul>
	            </div><br/>
	        @endif
	        <form method="POST" action="{{ route('user-settings.update') }}">
                @csrf
                <div class="form-group row">
            		<label class="col-lg-2 col-form-label">{{ __('messages.username') }}</label>
            		<div class="col-lg-10">
                        <input type="text" name="username" class="form-control {{ $errors->has('username') ? ' is-invalid' : '' }}" required value="{{ old('username', Auth::user()->username) }}" >
                    </div>
            	</div>
            	<div class="form-group row">
            		<label class="col-lg-2 col-form-label">{{ __('messages.email') }}</label>
            		<div class="col-lg-10">
                        @if(Auth::user()->willUseParentEmail())
                            {{ __('messages.parent-email') }} <em>{{ Auth::user()->getEmailAddress() }}</em> {{ __('messages.will-be-used-for-all-communications') }}
                        @else
                            <input type="email" name="email" class="form-control" required="" value="{{empty(old('email')) ? Auth::user()->email : old('email')}}">
                        @endif
                    </div>
            	</div>
				<div class="form-group row">
	              	<label class="col-lg-2 col-form-label">{{ __('messages.language') }}</label>
	          		<div class="col-lg-10">
	              		<select name="lang" class="form-control">
	              			<option value="en" <?php if(Auth::user()->lang == 'en') echo 'selected'; ?>>English</option>
	              			<option value="ja" <?php if(Auth::user()->lang == 'ja') echo 'selected'; ?>>Japanese</option>
	              		</select>
	          		</div>
	            </div>
                <div class="form-group row">
                    <label class="col-lg-2 col-form-label">{{ __('messages.calendar-view') }}</label>
                    <div class="col-lg-10">
                        @foreach($calendar_views as $value => $label)
                            <div class="form-check form-check-inline">
                                <input
                                    class="form-check-input" type="radio" id="{{ $value }}" value="{{ $value }}" name="calendar_view"
                                    @if($value === Auth::user()->calendar_view) checked @endif
                                >
                                <label class="form-check-label" for="{{ $value }}">{{ $label }}</label>
                            </div>
                        @endforeach
                    </div>
                </div>
	            <div class="form-group row">
	              	<label class="col-lg-2 col-form-label">{{ __('messages.recieve-notifications-via-email') }}</label>
	          		<div class="col-lg-10">
	              		<input type="checkbox" name="receive_emails" <?php if(Auth::user()->receive_emails == 1) echo 'checked'; ?>>
	          		</div>
				</div>
				<div class="form-group row">
					<label class="col-lg-2 col-form-label">{{ __('messages.receive-notifications-via-line') }}</label>
					<div class="col-lg-10">
						<input type="checkbox" name="receive_line_messsges" <?php if(Auth::user()->receive_line_messsges == 1) echo 'checked'; ?>>
					</div>
				</div>
	            <div class="form-group row">
            		<label class="col-lg-2 col-form-label">{{ __('messages.changepassword') }}</label>
            		<div class="col-lg-10">
            			<input type="checkbox" name="change_password" id="change-password" <?php if($errors->has('password') || $errors->has('newpassword')) echo 'checked'; ?>>
            		</div>
            	</div>
            	<div class="form-group row" id="password" style="{{ $errors->has('password') || $errors->has('newpassword') ? '' : 'display: none;' }}">
            		<label class="col-lg-2 col-form-label"></label>
            		<div class="col-lg-10">
            			<input type="password" name="password" class="form-control{{ $errors->has('password') ? ' is-invalid' : '' }}" placeholder="Password">
            		</div>
            	</div>
            	<div class="form-group row" id="newpassword" style="{{ $errors->has('password') || $errors->has('newpassword') ? '' : 'display: none;' }}">
            		<label class="col-lg-2 col-form-label"></label>
            		<div class="col-lg-10">
            			<input type="password" name="newpassword" class="form-control{{ $errors->has('newpassword') ? ' is-invalid' : '' }}" placeholder="New Password">
            		</div>
            	</div>
	            <div class="form-group row">
                  	<label class="col-lg-2 col-form-label"></label>
                  	<div class="col-lg-10">
                  		<input type="hidden" name="user_id" value="{{Auth::user()->id}}">
                      	<input name="edit" type="submit" value="{{ __('messages.edit') }}" class="btn btn-success form-control">
                  	</div>
                </div>
			</form>
		</div>
	</div>
@endsection

