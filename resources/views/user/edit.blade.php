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
            @include('partials.error')
	        <form method="POST" action="{{ route('users.update', $user->id) }}">
	        	@method('PATCH')
	        	@csrf
	        	<h1>{{ __('messages.edituser')}} {{$user->name}}</h1>
              	<div class="form-group row">
                	<label class="col-lg-2 col-form-label">{{ __('messages.name')}}</label>
                	<div class="col-lg-10">
                  		<input name="name" type="text" value="{{empty(old('name')) ? $user->name : old('name')}}" class="form-control{{ $errors->has('name') ? ' is-invalid' : '' }}" required="">
                	</div>
              	</div>
                <div class="form-group row">
                    <label class="col-lg-2 col-form-label">{{ __('messages.username')}}</label>
                    <div class="col-lg-10">
                        <input name="username" type="text" class="form-control{{ $errors->has('username') ? ' is-invalid' : '' }}" value="{{empty(old('username')) ? $user->username : old('username')}}" required="">
                    </div>
                </div>
                <div class="form-group row">
                    <label class="col-lg-2 col-form-label">{{ __('messages.email')}}</label>
                    <div class="col-lg-10">
                        @if($user->willUseParentEmail())
                            {{ __('messages.parent-email') }} <em>{{ $user->getEmailAddress() }}</em> {{ __('messages.will-be-used-for-all-communications') }}
                        @else
                            <input name="email" type="text" class="form-control{{ $errors->has('email') ? ' is-invalid' : '' }}" value="{{empty(old('email')) ? $user->email : old('email')}}" required="">
                        @endif
                    </div>
                </div>
                <hr>
                @if($user->hasRole('parent'))
                    <div class="form-group row">
                        <label class="col-lg-2 col-form-label">{{ __('messages.children')}}</label>
                        <div class="col-lg-10">
                            <select name="children[]" id="children" class="form-control" multiple="multiple">
                                @php
                                    $show_selected = $children_ids;
                                    if(old('name')) // do not check old_children here for empty array fix.
                                    {
                                        $show_selected = (array)old('children');
                                    }
                                @endphp
                                @foreach($students as $student)
                                    @php
                                        $is_disabled = false;
                                        $display_text = $student->getFullNameAttribute();
                                        if($student->parent_user_id != NULL && $student->parent_user_id != $user->id)
                                        {
                                            $is_disabled = true;
                                            @$display_text .= " ( ".__('messages.assigned-to')." ".$student->parent_user->name." )";
                                        }
                                    @endphp
                                    <option value="{{ $student->id }}"
                                        {{  in_array($student->id, $show_selected) ? 'selected' : '' }}
                                    {{  $is_disabled ? 'disabled' : '' }} >{{ $display_text }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <hr>
                @endif
                <div class="form-group row">
    	            	<label class="col-lg-2 col-form-label">{{ __('messages.changepassword')}}</label>
    	            	<div class="col-lg-10">
    	              		<input id="change-password" name="change_password" type="checkbox">
    	            	</div>
    	        </div>
                <div class="password" style="display:none;">
                    <div class="form-group row">
    	            	<label class="col-lg-2 col-form-label">{{ __('messages.password')}}</label>
    	            	<div class="col-lg-10">
    	              		<input name="password" type="password" class="form-control{{ $errors->has('password') ? ' is-invalid' : '' }}" >
    	            	</div>
    	         	</div>
    	         	<div class="form-group row">
    	            	<label class="col-lg-2 col-form-label">{{ __('messages.confirmpassword')}}</label>
    	            	<div class="col-lg-10">
    	              		<input name="password_confirmation" type="password" class="form-control{{ $errors->has('password_confirm') ? ' is-invalid' : '' }}" >
    	            	</div>
    	         	</div>
	         	</div>
                <div class="form-group row">
                    <label class="col-lg-2 col-form-label"></label>
                    <div class="col-lg-10">
                        <input name="edit" type="submit" value="{{ __('messages.edit')}}" class="form-control btn-success">
                    </div>
                </div>
	        </form>
        </div>
    </div>
@endsection

@push('scripts')
<script>
	window.addEventListener('DOMContentLoaded', function() {
        $('#children').select2({ width: '100%'  });
    });
</script>
@endpush
