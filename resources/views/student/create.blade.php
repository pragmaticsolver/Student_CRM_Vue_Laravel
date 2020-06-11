@extends('layouts.app')

@section('content')
    <div class="row justify-content-center">
        <div class="col-12">
        	@include('partials.success')
            @include('partials.error')
	        <form method="POST" action="{{ route('student.store') }}">
	        	@csrf
	          	<h1>{{ __('messages.addstudent') }}</h1>
                <div class="form-group row required">
                    <label class="col-lg-4 col-form-label">{{ __('messages.role') }}<span>*</span></label>
                    <div class="col-lg-8">
                        <select name="role" class="form-control{{ $errors->has('role') ? ' is-invalid' : '' }}" required>
                            <option value="">{{ __('messages.please-select-role') }}</option>
                            @if($student_roles)
                                @foreach($student_roles as $role)
                                    <option value="{{ $role->name }}" {{ old('role') == $role->name ? 'selected="selected"' : '' }}>{{ $role->name }}</option>
                                @endforeach
                            @endif
                        </select>
                    </div>
                </div>
	         	<div class="form-group row required">
	            	<label class="col-lg-4 col-form-label">{{ __('messages.lastnameromaji') }}<span>*</span></label>
	            	<div class="col-lg-8">
	              		<input name="lastname" type="text" class="form-control{{ $errors->has('lastname') ? ' is-invalid' : '' }}" value="{{ old('lastname') }}" placeholder="{{ __('messages.lastnameromajiplaceholder') }}" required="">
	            	</div>
	         	</div>
	         	<div class="form-group row required">
	            	<label class="col-lg-4 col-form-label">{{ __('messages.firstnameromaji') }}<span>*</span></label>
	            	<div class="col-lg-8">
	              		<input name="firstname" type="text" class="form-control{{ $errors->has('firstname') ? ' is-invalid' : '' }}" value="{{ old('firstname') }}" placeholder="{{ __('messages.firstnameromajiplaceholder') }}" required="">
	            	</div>
	         	</div>
	         	<div class="form-group row required">
	            	<label class="col-lg-4 col-form-label">{{ __('messages.email') }}<span>*</span></label>
	            	<div class="col-lg-8">
	              		<input name="email" type="email" class="form-control{{ $errors->has('email') ? ' is-invalid' : '' }}" value="{{ old('email') }}" placeholder="{{ __('messages.emailplaceholder') }}" required="">
	            	</div>
				 </div>
				<div class="form-group row">
					<label class="col-lg-4 col-form-label">{{ __('messages.add-advance-info') }}</label>
					<div class="col-lg-8">
						<input type="checkbox" class="toggle" data-id="advance-info" data-toggle="toggle">
					</div>
				</div>
				<div id="advance-info" class="option-info">
					<div class="form-group row">
						<label class="col-lg-4 col-form-label">{{ __('messages.lastnamekanji') }}</label>
						<div class="col-lg-8">
							<input name="lastname_kanji" type="text" class="form-control{{ $errors->has('lastname_kanji') ? ' is-invalid' : '' }}" value="{{ old('lastname_kanji') }}" placeholder="{{ __('messages.lastnamekanjiplaceholder') }}" >
						</div>
					</div>
					<div class="form-group row">
						<label class="col-lg-4 col-form-label">{{ __('messages.firstnamekanji') }}</label>
						<div class="col-lg-8">
							<input name="firstname_kanji" type="text" class="form-control{{ $errors->has('firstname_kanji') ? ' is-invalid' : '' }}" value="{{ old('firstname_kanji') }}" placeholder="{{ __('messages.firstnamekanjiplaceholder') }}" >
						</div>
					</div>
					<div class="form-group row">
						<label class="col-lg-4 col-form-label">{{ __('messages.lastnamekatakana') }}</label>
						<div class="col-lg-8">
							<input name="lastname_furigana" type="text" class="form-control{{ $errors->has('lastname_furigana') ? ' is-invalid' : '' }}" value="{{ old('lastname_furigana') }}" placeholder="{{ __('messages.lastnamekatakanaplaceholder') }}" >
						</div>
					</div>
					<div class="form-group row">
						<label class="col-lg-4 col-form-label">{{ __('messages.firstnamekatakana') }}</label>
						<div class="col-lg-8">
							<input name="firstname_furigana" type="text" class="form-control{{ $errors->has('firstname_furigana') ? ' is-invalid' : '' }}" value="{{ old('firstname_furigana') }}" placeholder="{{ __('messages.firstnamekatakanaplaceholder') }}" >
						</div>
					</div>
					<div class="form-group row">
						<label class="col-lg-4 col-form-label">{{ __('messages.advisor')}}</label>
						<div class="col-lg-8">
							<select name="teacher_id" class="form-control">
								<option value="">{{ __('messages.classteacher')}}</option>
								@if(!$teachers->isEmpty())
									@foreach($teachers as $teacher)
										<option value="{{$teacher->id}}" <?php if($teacher->id == old('teacher_id')) echo 'selected'; ?>>{{$teacher->nickname}}</option>
									@endforeach
								@endif
							</select>
						</div>
					</div>
					<div class="form-group row">
						<div class="col-lg-4">{{ __('messages.joindate')}}：</div>
						<div class="col-lg-8">
							<input name="join_date" type="date" class="form-control{{ $errors->has('join_date') ? ' is-invalid' : '' }}" value="{{old('join_date')}}">
						</div>
					</div>
					<div class="form-group row">
						<label class="col-lg-4 col-form-label">{{ __('messages.birthday') }}</label>
						<div class="col-lg-8">
							<input name="birthday" type="date" class="form-control{{ $errors->has('birthday') ? ' is-invalid' : '' }}" value="{{ old('birthday') }}">
						</div>
					</div>
					<div class="form-group row">
						<label class="col-lg-4 col-form-label">{{ __('messages.address')}}</label>
						<div class="col-lg-8">
							<input name="address" type="text" value="{{old('address')}}" class="form-control{{ $errors->has('address') ? ' is-invalid' : '' }}" placeholder="{{__('messages.address-holder')}}">
						</div>
					</div>
					<div class="form-group row">
						<label class="col-lg-4 col-form-label">{{ __('messages.homephone') }}</label>
						<div class="col-lg-8">
							<input name="home_phone" type="tel" class="form-control{{ $errors->has('home_phone') ? ' is-invalid' : '' }}" value="{{ old('home_phone') }}" placeholder="{{ __('messages.homephoneplaceholder') }}" >
						</div>
					</div>
					<div class="form-group row">
						<label class="col-lg-4 col-form-label">{{ __('messages.cellphone') }}</label>
						<div class="col-lg-8">
							<input name="mobile_phone" type="tel" class="form-control{{ $errors->has('mobile_phone') ? ' is-invalid' : '' }}" value="{{ old('mobile_phone') }}" placeholder="{{ __('messages.cellphoneplaceholder') }}" >
						</div>
					</div>
					<div class="form-group row">
						<label class="col-lg-4 col-form-label">{{ __('messages.levels') }}</label>
						<div class="col-lg-8">
							<select id="levels" name="levels[]" class="form-control{{ $errors->has('levels') ? ' is-invalid' : '' }}"  multiple>
								@if($class_student_levels)
									@foreach($class_student_levels as $level)
										<option value="{{ $level }}" {{ in_array($level, (array)old('levels'))  ? 'selected="selected"' : '' }}>{{ $level }}</option>
									@endforeach
								@endif
							</select>
						</div>
					</div>
					<div class="form-group row">
						<label class="col-lg-4 col-form-label">{{ __('messages.referrer') }}</label>
						<div class="col-lg-8">
							<input name="toiawase_referral" type="text" class="form-control{{ $errors->has('toiawase_referral') ? ' is-invalid' : '' }}" value="{{ old('toiawase_referral') }}" placeholder="{{ __('messages.referrerplaceholder') }}">
						</div>
					</div>
					<div class="form-group row">
						<label class="col-lg-4 col-form-label">{{ __('messages.firstcontacttype') }}</label>
						<div class="col-lg-8">
							<label class="radio-inline">
								<input type="radio" name="toiawase_houhou" value="Eメール" <?php if(old('toiawase_houhou') == 'Eメール') echo 'checked'; ?>>{{ __('messages.email') }}
							</label>
							<label class="radio-inline">
								<input type="radio" name="toiawase_houhou" value="電話" <?php if(old('toiawase_houhou') == '電話') echo 'checked'; ?>>{{ __('messages.telephone') }}
							</label>
							<label class="radio-inline">
								<input type="radio" name="toiawase_houhou" value="直接" <?php if(old('toiawase_houhou') == '直接') echo 'checked'; ?>>{{ __('messages.direct') }}
							</label>
							<label class="radio-inline">
								<input type="radio" name="toiawase_houhou" value="LINE" <?php if(old('toiawase_houhou') == 'LINE') echo 'checked'; ?>>{{ __('messages.line') }}
							</label>
						</div>
					</div>
					<div class="form-group row">
						<label class="col-lg-4 col-form-label">{{ __('messages.firstcontactgetter') }}</label>
						<div class="col-lg-8">
							<input name="toiawase_getter" type="text" class="form-control{{ $errors->has('toiawase_getter') ? ' is-invalid' : '' }}" value="{{ old('toiawase_getter') }}" placeholder="{{ __('messages.getterplaceholder') }}">
						</div>
					</div>
					<div class="form-group row">
						<label class="col-lg-4 col-form-label">{{ __('messages.firstcontactdate') }}</label>
						<div class="col-lg-8">
							<input name="toiawase_date" type="date" class="form-control{{ $errors->has('toiawase_date') ? ' is-invalid' : '' }}" value="{{ old('toiawase_date') }}">
						</div>
					</div>
					<div class="form-group row">
						<label class="col-lg-4 col-form-label">{{ __('messages.memo') }}</label>
						<div class="col-lg-8">
							<textarea name="toiawase_memo" class="form-control{{ $errors->has('toiawase_memo') ? ' is-invalid' : '' }}" placeholder="{{ __('messages.memoplaceholder') }}">{{ old('toiawase_memo') }}</textarea>
						</div>
					</div>
					<div class="form-group row">
						<label class="col-lg-4 col-form-label">{{ __('messages.comment')}}</label>
						<div class="col-lg-8">
							<textarea name="comment" type="text" placeholder="{{ __('messages.comment-placeholder')}}" class="form-control{{ $errors->has('comment') ? ' is-invalid' : '' }}">{{old('comment')}}</textarea>
						</div>
					</div>
					<div class="form-group row">
						<label class="col-lg-4 col-form-label">{{ __('messages.rfidtoken') }}</label>
						<div class="col-lg-8">
							<input name="rfid_token" type="text" value="{{ old('rfid_token' ) }}" class="form-control{{ $errors->has('rfid_token') ? ' is-invalid' : '' }}">
						</div>
					</div>
				</div>
				<div class="form-group row">
					<label class="col-lg-4 col-form-label">{{ __('messages.add-office-info') }}</label>
					<div class="col-lg-8">
						<input type="checkbox" class="toggle" data-id="office-info" data-toggle="toggle">
					</div>
				</div>
				<div id="office-info" class="option-info">
					<div class="form-group row">
						<label class="col-lg-4 col-form-label">{{ __('messages.office-name') }}</label>
						<div class="col-lg-8">
							<input name="office_name" type="text" value="{{ old('office_name' ) }}" class="form-control{{ $errors->has('office_name') ? ' is-invalid' : '' }}">
						</div>
					</div>
					<div class="form-group row">
						<label class="col-lg-4 col-form-label">{{ __('messages.office-address') }}</label>
						<div class="col-lg-8">
							<input name="office_address" type="text" value="{{ old('office_address' ) }}" class="form-control{{ $errors->has('office_address') ? ' is-invalid' : '' }}">
						</div>
					</div>
					<div class="form-group row">
						<label class="col-lg-4 col-form-label">{{ __('messages.office-phone') }}</label>
						<div class="col-lg-8">
							<input name="office_phone" type="text" value="{{ old('office_phone' ) }}" class="form-control{{ $errors->has('office_phone') ? ' is-invalid' : '' }}">
						</div>
					</div>
				</div>
				<div class="form-group row">
					<label class="col-lg-4 col-form-label">{{ __('messages.add-school-info') }}</label>
					<div class="col-lg-8">
						<input type="checkbox" class="toggle" data-id="school-info" data-toggle="toggle">
					</div>
				</div>
				<div id="school-info" class="option-info">
					<div class="form-group row">
						<label class="col-lg-4 col-form-label">{{ __('messages.school-name') }}</label>
						<div class="col-lg-8">
							<input name="school_name" type="text" value="{{ old('school_name' ) }}" class="form-control{{ $errors->has('school_name') ? ' is-invalid' : '' }}">
						</div>
					</div>
					<div class="form-group row">
						<label class="col-lg-4 col-form-label">{{ __('messages.school-address') }}</label>
						<div class="col-lg-8">
							<input name="school_address" type="text" value="{{ old('school_address' ) }}" class="form-control{{ $errors->has('school_address') ? ' is-invalid' : '' }}">
						</div>
					</div>
					<div class="form-group row">
						<label class="col-lg-4 col-form-label">{{ __('messages.school-phone') }}</label>
						<div class="col-lg-8">
							<input name="school_phone" type="text" value="{{ old('school_phone' ) }}" class="form-control{{ $errors->has('school_phone') ? ' is-invalid' : '' }}">
						</div>
					</div>
				</div>
				<div class="form-group row">
					<label class="col-lg-4 col-form-label">{{ __('messages.add-1guardian-info') }}</label>
					<div class="col-lg-8">
						<input type="checkbox" class="toggle" data-id="guardian1-info" data-toggle="toggle">
					</div>
				</div>
				<div id="guardian1-info" class="option-info">
					<div class="form-group row">
						<label class="col-lg-4 col-form-label">{{ __('messages.guardian1-name') }}</label>
						<div class="col-lg-8">
							<input name="guardian1_name" type="text" value="{{ old('guardian1_name' ) }}" class="form-control{{ $errors->has('guardian1_name') ? ' is-invalid' : '' }}">
						</div>
					</div>
					<div class="form-group row">
						<label class="col-lg-4 col-form-label">{{ __('messages.guardian1-address') }}</label>
						<div class="col-lg-8">
							<input name="guardian1_address" type="text" value="{{ old('guardian1_address' ) }}" class="form-control{{ $errors->has('guardian1_address') ? ' is-invalid' : '' }}">
						</div>
					</div>
					<div class="form-group row">
						<label class="col-lg-4 col-form-label">{{ __('messages.guardian1-phone') }}</label>
						<div class="col-lg-8">
							<input name="guardian1_phone" type="text" value="{{ old('guardian1_phone' ) }}" class="form-control{{ $errors->has('guardian1_phone') ? ' is-invalid' : '' }}">
						</div>
					</div>
				</div>
				<div class="form-group row">
					<label class="col-lg-4 col-form-label">{{ __('messages.add-2guardian-info') }}</label>
					<div class="col-lg-8">
						<input type="checkbox" class="toggle" data-id="guardian2-info" data-toggle="toggle">
					</div>
				</div>
				<div id="guardian2-info" class="option-info">
					<div class="form-group row">
						<label class="col-lg-4 col-form-label">{{ __('messages.guardian2-name') }}</label>
						<div class="col-lg-8">
							<input name="guardian2_name" type="text" value="{{ old('guardian2_name' ) }}" class="form-control{{ $errors->has('guardian2_name') ? ' is-invalid' : '' }}">
						</div>
					</div>
					<div class="form-group row">
						<label class="col-lg-4 col-form-label">{{ __('messages.guardian2-address') }}</label>
						<div class="col-lg-8">
							<input name="guardian2_address" type="text" value="{{ old('guardian2_address' ) }}" class="form-control{{ $errors->has('guardian2_address') ? ' is-invalid' : '' }}">
						</div>
					</div>
					<div class="form-group row">
						<label class="col-lg-4 col-form-label">{{ __('messages.guardian2-phone') }}</label>
						<div class="col-lg-8">
							<input name="guardian2_phone" type="text" value="{{ old('guardian2_phone' ) }}" class="form-control{{ $errors->has('guardian2_phone') ? ' is-invalid' : '' }}">
						</div>
					</div>
				</div>
				
	          	<div class="form-group row">
	            	<label class="col-lg-4 col-form-label"></label>
	            	<div class="col-lg-8">
	              		<input name="add" type="submit" value="{{ __('messages.addstudent') }}" class="form-control btn-success">
	            	</div>
	          	</div>
	        </form>
      	</div>
    </div>
@endsection

@push('scripts')
<script src="//ajax.googleapis.com/ajax/libs/jquery/2.1.3/jquery.min.js"></script>
<link href="//gitcdn.github.io/bootstrap-toggle/2.2.2/css/bootstrap-toggle.min.css" rel="stylesheet">
<script src="//gitcdn.github.io/bootstrap-toggle/2.2.2/js/bootstrap-toggle.min.js"></script>
<script>
    window.levelsPlaceholder = "{{ __('messages.please-select-level-s') }}";
	window.addEventListener('DOMContentLoaded', function() {
        $('#levels').select2({
            width: '100%',
            placeholder: levelsPlaceholder
        });
    });
</script>
<script src="{{ asset('public'.mix('js/page/student/create.js')) }}"></script>
@endpush
