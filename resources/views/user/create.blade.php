@extends('layouts.app')

@section('content')
    <div class="row justify-content-center">
        @include('partials.success')
        @include('partials.error')
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
	        <form method="POST" action="{{ route('users.store') }}">
	        	@csrf
	          	<h1>{{ __('messages.adduser') }}</h1>
	          	<div class="form-group row">
	            	<label class="col-lg-2 col-form-label">{{ __('messages.name') }}</label>
	            	<div class="col-lg-10">
	              		<input name="name" type="text" class="form-control{{ $errors->has('name') ? ' is-invalid' : '' }}" value="{{ old('name') }}" required="">
	            	</div>
	         	</div>
	         	<div class="form-group row">
	            	<label class="col-lg-2 col-form-label">{{ __('messages.username') }}</label>
	            	<div class="col-lg-10">
	              		<input name="username" type="text" class="form-control{{ $errors->has('username') ? ' is-invalid' : '' }}" value="{{ old('username') }}" required="">
	            	</div>
	         	</div>
	         	<div class="form-group row">
	            	<label class="col-lg-2 col-form-label">{{ __('messages.email') }}</label>
	            	<div class="col-lg-10">
	              		<input name="email" type="text" class="form-control{{ $errors->has('email') ? ' is-invalid' : '' }}" value="{{ old('email') }}" required="">
	            	</div>
	         	</div>
	         	<div class="form-group row">
	            	<label class="col-lg-2 col-form-label">{{ __('messages.roles') }}</label>
	            	<div class="col-lg-10">
						@foreach ($roles as $role)
	                    <div class="i-checks">
	                        <label>
	                            <input name="role" type="radio" value="{{ $role->name }}" required {{ old('role') == $role->name ? 'checked' : '' }} >
	                            <i></i> {{ ucfirst($role->name) }}
	                            </label>
	                    </div>
                    	@endforeach
	            	</div>
                 </div>
                 <div class="form-group row" id="children_selection" style="display:none;">
                    <label class="col-lg-2 col-form-label">{{ __('messages.children')}}</label>
                    <div class="col-lg-10">
                        <select name="children[]" id="children" class="form-control" multiple="multiple">
                            @foreach($students as $student)
                                @php
                                    $is_disabled = false;
                                    $display_text = $student->getFullNameAttribute();
                                    if($student->parent_user_id != NULL)
                                    {
                                        $is_disabled = true;
                                        @$display_text .= " ( ".__('messages.assigned-to')." ".$student->parent_user->name." )";
                                    }
                                @endphp
                                <option value="{{ $student->id }}"
                                    {{  in_array($student->id, (array)old('children')) ? 'selected' : '' }}
                                    {{  $is_disabled ? 'disabled' : '' }}
                                    >{{ $display_text }}</option>
                            @endforeach
                        </select>
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
		              <input name="add" type="submit" value="{{ __('messages.adduser') }}" class="form-control btn-success">
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
        showHideFields();

        $("input[name='role']").change(function(){
            showHideFields();
        })
    });

    function showHideFields()
    {
        selected_role = $("input[name='role']:checked").val();
        if(selected_role == 'parent')
        {
            $('#children_selection').show();
        }
        else
        {
            $('#children_selection').hide();
        }
    }
</script>
@endpush
