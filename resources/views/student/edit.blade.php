@extends('layouts.app')

@push('styles')
    <style>.dz-image img{ max-width: 150px; }</style>
@endpush

@section('content')
    <div class="row justify-content-center">
        <div class="col-12">
        	@include('partials.success')
            @include('partials.error')
            @if($errors->any())
                <div class="alert alert-danger">
                    <ul>
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div><br/>
            @endif
	        <form method="POST" action="{{ route('student.update', $student->id) }}">
	        	@method('PATCH')
	        	@csrf
	        	<div class="row">
                    <div class="col-lg-12">
                        <h2>{{$student->lastname}} {{$student->firstname}}</h2>
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-4">
                        @if($student->image)
                            <img src="{{ $student->getImageUrl() }}" style="max-width:300px;" class="img-responsive">
                        @endif
                    </div>
                    <div class="col-lg-8">
                        <div class="form-group row required">
                            <label class="col-lg-3 col-form-label">{{ __('messages.role') }}<span>*</span>: </label>
                            <div class="col-lg-9">
                                <select name="role" class="form-control{{ $errors->has('role') ? ' is-invalid' : '' }}" required>
                                    <option value="">{{ __('messages.please-select-role') }}</option>
                                    @if($student_roles)
                                        @foreach($student_roles as $role)
                                            <option
                                                value="{{ $role->name }}"
                                                @if($student->user->get_role() && $student->user->get_role()->name === $role->name) selected @endif
                                            >{{ $role->name }}</option>
                                        @endforeach
                                    @endif
                                </select>
                            </div>
                        </div>
                        <div class="form-group row required">
                            <div class="col-lg-3">{{ __('messages.profile-picture') }}<span>*</span>：</div>
                            <div class="col-lg-9">
                                <div class="dropzone" id="studentImage"></div>
                                <input type="hidden" id="uploadedStudentImage"
                                    value="{{ $student->uploadedImageDetails()  }}"
                                >
                            </div>
                        </div>
                        <div class="form-group row required">
                            <div class="col-lg-3">{{ __('messages.lastnameromaji')}}<span>*</span>：</div>
                            <div class="col-lg-9">
                                <input name="lastname" type="text" value="{{empty(old('lastname')) ? $student->lastname : old('lastname')}}" class="form-control{{ $errors->has('lastname') ? ' is-invalid' : '' }}" required="">
                            </div>
                        </div>
                        <div class="form-group row required">
                            <div class="col-lg-3">{{ __('messages.firstnameromaji')}}<span>*</span>：</div>
                            <div class="col-lg-9">
                                <input name="firstname" type="text" value="{{empty(old('firstname')) ? $student->firstname : old('firstname')}}" class="form-control{{ $errors->has('firstname') ? ' is-invalid' : '' }}" required="">
                            </div>
                        </div>
                        <div class="form-group row required">
                            <div class="col-lg-3">{{ __('messages.email')}}<span>*</span>：</div>
                            <div class="col-lg-9">
                                @if($student->willUseParentEmail())
                                    {{ __('messages.parent-email') }} <em>{{ $student->getEmailAddress() }}</em> {{ __('messages.will-be-used-for-all-communications') }}
                                @else
                                    <input name="email" type="email" value="{{empty(old('email')) ? $student->email : old('email')}}" class="form-control{{ $errors->has('email') ? ' is-invalid' : '' }}" required="">
                                @endif
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-lg-3 col-form-label">{{ __('messages.add-advance-info') }}</label>
                            <div class="col-lg-9">
                                <input type="checkbox" class="toggle" data-id="advance-info" data-toggle="toggle" checked>
                            </div>
                        </div>
                        <div id="advance-info" class="option-info">
                            <div class="form-group row">
                                <div class="col-lg-3">{{ __('messages.lastnamekanji')}}：</div>
                                <div class="col-lg-9">
                                    <input name="lastname_kanji" type="text" value="{{empty(old('lastname_kanji')) ? $student->lastname_kanji : old('lastname_kanji')}}" class="form-control{{ $errors->has('lastname_kanji') ? ' is-invalid' : '' }}" >
                                </div>
                            </div>
                            <div class="form-group row">
                                <div class="col-lg-3">{{ __('messages.firstnamekanji')}}：</div>
                                <div class="col-lg-9">
                                    <input name="firstname_kanji" type="text" value="{{empty(old('firstname_kanji')) ? $student->firstname_kanji : old('firstname_kanji')}}" class="form-control{{ $errors->has('firstname_kanji') ? ' is-invalid' : '' }}" >
                                </div>
                            </div>
                            <div class="form-group row">
                                <div class="col-lg-3">{{ __('messages.lastnamekatakana')}}：</div>
                                <div class="col-lg-9">
                                    <input name="lastname_furigana" type="text" value="{{empty(old('lastname_furigana')) ? $student->lastname_furigana : old('lastname_furigana')}}" class="form-control{{ $errors->has('lastname_furigana') ? ' is-invalid' : '' }}" >
                                </div>
                            </div>
                            <div class="form-group row">
                                <div class="col-lg-3">{{ __('messages.firstnamekatakana')}}：</div>
                                <div class="col-lg-9">
                                    <input name="firstname_furigana" type="text" value="{{empty(old('firstname_furigana')) ? $student->firstname_furigana : old('firstname_furigana')}}" class="form-control{{ $errors->has('firstname_furigana') ? ' is-invalid' : '' }}" >
                                </div>
                            </div>
                            <div class="form-group row">
                                <div class="col-lg-3">{{ __('messages.advisor')}}：</div>
                                <div class="col-lg-9">
                                    <select name="teacher_id" class="form-control">
                                        <option value="">{{ __('messages.classteacher')}}</option>
                                        @if(!$teachers->isEmpty())
                                            @foreach($teachers as $teacher)
                                                <option value="{{$teacher->id}}" <?php if($teacher->id == $student->teacher_id) echo 'selected'; ?>>{{$teacher->nickname}}</option>
                                            @endforeach
                                        @endif
                                    </select>
                                </div>
                            </div>
                            <div class="form-group row">
                                <div class="col-lg-3">{{ __('messages.joindate')}}：</div>
                                <div class="col-lg-9">
                                    <input name="join_date" type="date" value="{{empty(old('join_date')) ? $student->join_date : old('join_date')}}" class="form-control{{ $errors->has('join_date') ? ' is-invalid' : '' }}">
                                </div>
                            </div>
                            <div class="form-group row">
                                <div class="col-lg-3">{{ __('messages.birthday')}}：</div>
                                <div class="col-lg-9">
                                    <input name="birthday" type="date" value="{{empty(old('birthday')) ? $student->birthday : old('birthday')}}" class="form-control{{ $errors->has('birthday') ? ' is-invalid' : '' }}">
                                </div>
                            </div>
                            <div class="form-group row">
                                <div class="col-lg-3">{{ __('messages.address')}}：</div>
                                <div class="col-lg-9">
                                    <input name="address" type="text" value="{{empty(old('address')) ? $student->address : old('address')}}" class="form-control{{ $errors->has('address') ? ' is-invalid' : '' }}">
                                </div>
                            </div>
                            <div class="form-group row">
                                <div class="col-lg-3">{{ __('messages.homephone')}}：</div>
                                <div class="col-lg-9">
                                    <input name="home_phone" type="tel" value="{{empty(old('home_phone')) ? $student->home_phone : old('home_phone')}}" class="form-control{{ $errors->has('home_phone') ? ' is-invalid' : '' }}">
                                </div>
                            </div>
                            <div class="form-group row">
                                <div class="col-lg-3">{{ __('messages.cellphone')}}：</div>
                                <div class="col-lg-9">
                                    <input name="mobile_phone" type="tel" value="{{empty(old('mobile_phone')) ? $student->mobile_phone : old('mobile_phone')}}" class="form-control{{ $errors->has('mobile_phone') ? ' is-invalid' : '' }}" >
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-lg-3 col-form-label">{{ __('messages.levels') }}: </label>
                                <div class="col-lg-9">
                                    <select id="levels" name="levels[]" class="form-control{{ $errors->has('levels') ? ' is-invalid' : '' }}"  multiple>
                                        @if($class_student_levels)
                                            @php $selected_levels = explode(",",$student->levels); @endphp
                                            @foreach($class_student_levels as $level)
                                                <option value="{{ $level }}" @if(in_array($level, $selected_levels)) selected @endif>{{ $level }}</option>
                                            @endforeach
                                        @endif
                                    </select>
                                </div>
                            </div>
                            <div class="form-group row">
                                <div class="col-lg-3">{{ __('messages.referrer')}}：</div>
                                <div class="col-lg-9">
                                    <input name="toiawase_referral" type="text" value="{{empty(old('toiawase_referral')) ? $student->toiawase_referral : old('toiawase_referral')}}" class="form-control{{ $errors->has('toiawase_referral') ? ' is-invalid' : '' }}">
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-lg-3 col-form-label">{{ __('messages.firstcontacttype') }}</label>
                                <div class="col-lg-9">
                                    <label class="radio-inline">
                                        <input type="radio" name="toiawase_houhou" value="Eメール" <?php if(in_array('Eメール', [old('toiawase_houhou'), $student->toiawase_houhou])) echo 'checked'; ?>>{{ __('messages.email') }}
                                    </label>
                                    <label class="radio-inline">
                                        <input type="radio" name="toiawase_houhou" value="電話" <?php if(in_array('電話', [old('toiawase_houhou'), $student->toiawase_houhou])) echo 'checked'; ?>>{{ __('messages.telephone') }}
                                    </label>
                                    <label class="radio-inline">
                                        <input type="radio" name="toiawase_houhou" value="直接" <?php if(in_array('直接', [old('toiawase_houhou'), $student->toiawase_houhou])) echo 'checked'; ?>>{{ __('messages.direct') }}
                                    </label>
                                    <label class="radio-inline">
                                        <input type="radio" name="toiawase_houhou" value="LINE" <?php if(in_array('LINE', [old('toiawase_houhou'), $student->toiawase_houhou])) echo 'checked'; ?>>{{ __('messages.line') }}
                                    </label>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-lg-3 col-form-label">{{ __('messages.firstcontactgetter') }}</label>
                                <div class="col-lg-9">
                                    <input name="toiawase_getter" type="text" value="{{empty(old('toiawase_getter')) ? $student->toiawase_getter : old('toiawase_getter')}}" class="form-control{{ $errors->has('toiawase_getter') ? ' is-invalid' : '' }}">
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-lg-3 col-form-label">{{ __('messages.firstcontactdate') }}</label>
                                <div class="col-lg-9">
                                    <input name="toiawase_date" type="date" value="{{empty(old('toiawase_date')) ? $student->toiawase_date : old('toiawase_date')}}" class="form-control{{ $errors->has('toiawase_date') ? ' is-invalid' : '' }}">
                                </div>
                            </div>
                            <div class="form-group row">
                                <div class="col-lg-3">{{ __('messages.memo') }}</div>
                                <div class="col-lg-9">
                                    <textarea name="toiawase_memo" class="form-control{{ $errors->has('toiawase_memo') ? ' is-invalid' : '' }}">{{empty(old('toiawase_memo')) ? $student->toiawase_memo : old('toiawase_memo')}}</textarea>
                                </div>
                            </div>
                            <div class="form-group row">
                                <div class="col-lg-3">{{ __('messages.comment')}}</div>
                                <div class="col-lg-9">
                                    <textarea name="comment" type="text" class="form-control{{ $errors->has('comment') ? ' is-invalid' : '' }}">{{empty(old('comment')) ? $student->comment : old('comment')}}</textarea>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-lg-3 col-form-label">{{ __('messages.rfidtoken') }}</label>
                                <div class="col-lg-9">
                                    <input name="rfid_token" type="text" value="{{ old('rfid_token',$student->rfid_token ) }}" class="form-control{{ $errors->has('rfid_token') ? ' is-invalid' : '' }}">
                                </div>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-lg-3 col-form-label">{{ __('messages.add-office-info') }}</label>
                            <div class="col-lg-9">
                                <input type="checkbox" class="toggle" data-id="office-info" data-toggle="toggle" {{ (!empty($student->office_name) || !empty($student->office_address) || !empty($student->office_phone)) ? 'checked' : '' }}>
                            </div>
                        </div>
                        <div id="office-info" class="option-info"  style="display: {{(!empty($student->office_name) || !empty($student->office_address) || !empty($student->office_phone)) ? 'block' : 'none' }}">
                            <div class="form-group row">
                                <label class="col-lg-3 col-form-label">{{ __('messages.office-name') }}</label>
                                <div class="col-lg-9">
                                    <input name="office_name" type="text" value="{{ old('office_name',$student->office_name ) }}" class="form-control{{ $errors->has('office_name') ? ' is-invalid' : '' }}">
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-lg-3 col-form-label">{{ __('messages.office-address') }}</label>
                                <div class="col-lg-9">
                                    <input name="office_address" type="text" value="{{ old('office_address',$student->office_address ) }}" class="form-control{{ $errors->has('office_address') ? ' is-invalid' : '' }}">
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-lg-3 col-form-label">{{ __('messages.office-phone') }}</label>
                                <div class="col-lg-9">
                                    <input name="office_phone" type="text" value="{{ old('office_phone',$student->office_phone ) }}" class="form-control{{ $errors->has('office_phone') ? ' is-invalid' : '' }}">
                                </div>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-lg-3 col-form-label">{{ __('messages.add-school-info') }}</label>
                            <div class="col-lg-9">
                                <input type="checkbox" class="toggle" data-id="school-info" data-toggle="toggle"  {{ (!empty($student->school_name) || !empty($student->school_address) || !empty($student->school_phone)) ? 'checked' : '' }}>
                            </div>
                        </div>
                        <div id="school-info" class="option-info" style="display: {{(!empty($student->school_name) || !empty($student->school_address) || !empty($student->school_phone)) ? 'block' : 'none' }}">
				
                            <div class="form-group row">
                                <label class="col-lg-3 col-form-label">{{ __('messages.school-name') }}</label>
                                <div class="col-lg-9">
                                    <input name="school_name" type="text" value="{{ old('school_name',$student->school_name ) }}" class="form-control{{ $errors->has('school_name') ? ' is-invalid' : '' }}">
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-lg-3 col-form-label">{{ __('messages.school-address') }}</label>
                                <div class="col-lg-9">
                                    <input name="school_address" type="text" value="{{ old('school_address',$student->school_address ) }}" class="form-control{{ $errors->has('school_address') ? ' is-invalid' : '' }}">
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-lg-3 col-form-label">{{ __('messages.school-phone') }}</label>
                                <div class="col-lg-9">
                                    <input name="school_phone" type="text" value="{{ old('school_phone',$student->school_phone ) }}" class="form-control{{ $errors->has('school_phone') ? ' is-invalid' : '' }}">
                                </div>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-lg-3 col-form-label">{{ __('messages.add-1guardian-info') }}</label>
                            <div class="col-lg-9">
                                <input type="checkbox" class="toggle" data-id="guardian1-info" data-toggle="toggle" {{ (!empty($student->guardian1_name) || !empty($student->guardian1_address) || !empty($student->guardian1_phone)) ? 'checked' : '' }}>
                            </div>
                        </div>
                        <div id="guardian1-info" class="option-info" style="display: {{(!empty($student->guardian1_name) || !empty($student->guardian1_address) || !empty($student->guardian1_phone)) ? 'block' : 'none' }}">
                            <div class="form-group row">
                                <label class="col-lg-3 col-form-label">{{ __('messages.guardian1-name') }}</label>
                                <div class="col-lg-9">
                                    <input name="guardian1_name" type="text" value="{{ old('guardian1_name',$student->guardian1_name ) }}" class="form-control{{ $errors->has('guardian1_name') ? ' is-invalid' : '' }}">
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-lg-3 col-form-label">{{ __('messages.guardian1-address') }}</label>
                                <div class="col-lg-9">
                                    <input name="guardian1_address" type="text" value="{{ old('guardian1_address',$student->guardian1_address ) }}" class="form-control{{ $errors->has('guardian1_address') ? ' is-invalid' : '' }}">
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-lg-3 col-form-label">{{ __('messages.guardian1-phone') }}</label>
                                <div class="col-lg-9">
                                    <input name="guardian1_phone" type="text" value="{{ old('guardian1_phone',$student->guardian1_phone ) }}" class="form-control{{ $errors->has('guardian1_phone') ? ' is-invalid' : '' }}">
                                </div>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-lg-3 col-form-label">{{ __('messages.add-2guardian-info') }}</label>
                            <div class="col-lg-9">
                                <input type="checkbox" class="toggle" data-id="guardian2-info" data-toggle="toggle" {{ (!empty($student->guardian2_name) || !empty($student->guardian2_address) || !empty($student->guardian2_phone)) ? 'checked' : '' }}>
                            </div>
                        </div>
                        <div id="guardian2-info" class="option-info" style="display: {{(!empty($student->guardian2_name) || !empty($student->guardian2_address) || !empty($student->guardian2_phone)) ? 'block' : 'none' }}">
				            <div class="form-group row">
                                <label class="col-lg-3 col-form-label">{{ __('messages.guardian2-name') }}</label>
                                <div class="col-lg-9">
                                    <input name="guardian2_name" type="text" value="{{ old('guardian2_name',$student->guardian2_name ) }}" class="form-control{{ $errors->has('guardian2_name') ? ' is-invalid' : '' }}">
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-lg-3 col-form-label">{{ __('messages.guardian2-address') }}</label>
                                <div class="col-lg-9">
                                    <input name="guardian2_address" type="text" value="{{ old('guardian2_address',$student->guardian2_address ) }}" class="form-control{{ $errors->has('guardian2_address') ? ' is-invalid' : '' }}">
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-lg-3 col-form-label">{{ __('messages.guardian2-phone') }}</label>
                                <div class="col-lg-9">
                                    <input name="guardian2_phone" type="text" value="{{ old('guardian2_phone',$student->guardian2_phone ) }}" class="form-control{{ $errors->has('guardian2_phone') ? ' is-invalid' : '' }}">
                                </div>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-lg-3 col-form-label"></label>
                            <div class="col-lg-9">
                                <input name="edit" type="submit" value="{{ __('messages.edit')}}" class="form-control btn-success">
                            </div>
                        </div>
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
    var uploadImageUrl = "{{ route('image.store', $student->id) }}";
    var removeImageUrl = "{{ route('image.delete', $student->id) }}";
</script>
<script src="{{ asset('public'.mix('js/page/student/edit.js')) }}"></script>
@endpush
