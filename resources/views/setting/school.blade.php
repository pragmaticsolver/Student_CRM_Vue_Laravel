@extends('layouts.app')

@section('content')
	<div class="row justify-content-center">
		<div class="col-12">
			<h1>School Settings</h1>
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
			<form method="POST" action="{{ route('school-settings.update') }}" enctype="multipart/form-data">
                @csrf
                <div class="form-group row">
                    <label class="col-lg-2 col-form-label">{{ __('messages.school_name') }}</label>
                    <div class="col-lg-10">
                        <input name="school_name" type="text" value="{{ old('school_name',$school_name) }}" class="form-control{{ $errors->has('school_name') ? ' is-invalid' : '' }}" required="">
                    </div>
                </div>
                <div class="form-group row">
                    <label class="col-lg-2 col-form-label">{{ __('messages.school_initial') }}</label>
                    <div class="col-lg-10">
                        <input name="school_initial" type="text" value="{{ old('school_initial',$school_initial) }}" class="form-control{{ $errors->has('school_initial') ? ' is-invalid' : '' }}" required="">
                    </div>
                </div>
                
                <div class="form-group row">
					<label class="col-lg-2 col-form-label">{{ __('messages.defaultlanguage') }}</label>
					<div class="col-lg-10">
						<select name="default_lang" class="form-control {{ $errors->has('default_lang') ? ' is-invalid' : '' }}">
							<option value="en" <?php if(old('default_lang',$default_lang) == 'en') echo 'selected'; ?>>English</option>
							<option value="ja" <?php if(old('default_lang',$default_lang) == 'ja') echo 'selected'; ?>>Japanese</option>
						</select>
					</div>
				</div>
				<div class="form-group row">
	              	<label class="col-lg-2 col-form-label">{{ __('messages.defaultclasssize') }}</label>
	          		<div class="col-lg-10">
	              		<input name="default_size" type="number" value="{{empty(old('default_size')) ? $default_size[0]->value : old('default_size')}}" class="form-control{{ $errors->has('default_size') ? ' is-invalid' : '' }}" required="">
	          		</div>
                </div>
                <div class="form-group row">
	            	<label class="col-lg-2 col-form-label"></label>
	            	<div class="col-lg-10">
                        <label><input type="checkbox" name="use_monthly_payments" <?php if($use_monthly_payments[0]->value == 'true') echo 'checked'; ?>>{{ __('messages.usemonthlypayments') }}</label>
	            	</div>
	            </div>

                <div class="form-group row">
                    <label class="col-lg-2 col-form-label">{{ __('messages.class-student-levels') }}</label>
                    <div class="col-lg-10">
                        <input type="text" name="class_student_levels" value="{{ $class_student_levels }}" class="level-selectize">
                    </div>
                </div>
                <div class="form-group row">
                    <label class="col-lg-2 col-form-label">{{ __('messages.default-calendar-view') }}</label>
                    <div class="col-lg-10">
                        @foreach($calendar_views as $value => $label)
                            <div class="form-check form-check-inline">
                                <input
                                    class="form-check-input" type="radio" id="{{ $value }}" value="{{ $value }}" name="default_calendar_view"
                                    @if($value === $default_calendar_view) checked @endif
                                >
                                <label class="form-check-label" for="{{ $value }}">{{ $label }}</label>
                            </div>
                        @endforeach
                    </div>
                </div>
                <div class="form-group row">
                    <label class="col-lg-2 col-form-label">{{ __('messages.default-calendar-color-coding') }}</label>
                    <div class="col-lg-10">
                        <input type="text" name="default_calendar_color_coding" value="{{ $default_calendar_color_coding }}">
                    </div>
                </div>
                <div class="form-group row">
                    <label class="col-lg-2 col-form-label">{{ __('messages.default-class-length') }}</label>
                    <div class="col-lg-10">
                        <input type="text" name="default_class_length" id="default_class_length" class="form-control {{ $errors->has('default_class_length') ? ' is-invalid' : '' }}" value="{{ old('default_class_length',$default_class_length) }}" required autocomplete="off">
                    </div>
                </div>
                <div class="form-group row">
                    <label class="col-lg-2 col-form-label">{{ __('messages.default-signup-role') }}</label>
                    <div class="col-lg-10">
                        <select name="default_signup_role" class="form-control{{ $errors->has('role') ? ' is-invalid' : '' }}" required>
                            <option value="">{{ __('messages.please-select-role') }}</option>
                            @if($roles->count())
                                @foreach($roles as $role)
                                    <option value="{{ $role->name }}" @if($role->name == $default_signup_role) selected="selected" @endif>{{ $role->name }}</option>
                                @endforeach
                            @endif
                        </select>
                    </div>
                </div>
                <div class="form-group row">
                    <label class="col-lg-2 col-form-label">{{ __('messages.google_map_api_key') }}</label>
                    <div class="col-lg-10">
                        <input type="text" name="google_map_api_key" id="google_map_api_key" class="form-control {{ $errors->has('google_map_api_key') ? ' is-invalid' : '' }}" value="{{ old('google_map_api_key',$google_map_api_key) }}" autocomplete="off">
                    </div>
                </div>
                <div class="form-group row">
                    <label class="col-lg-2 col-form-label">{{ __('messages.leftover_class_expiration_period') }}</label>
                    <div class="col-lg-10">
                        <input type="text" name="leftover_class_expiration_period" id="leftover_class_expiration_period" class="form-control {{ $errors->has('leftover_class_expiration_period') ? ' is-invalid' : '' }}" value="{{ old('leftover_class_expiration_period',$leftover_class_expiration_period) }}" autocomplete="off" required>
                    </div>
                </div>
                <div class="form-group row">
                    <label class="col-lg-2 col-form-label">{{ __('messages.student-reminder-email-time') }}</label>
                    <div class="col-lg-10">
                        <input type="text" name="student_reminder_email_time" id="student_reminder_email_time" class="form-control {{ $errors->has('student_reminder_email_time') ? ' is-invalid' : '' }}" value="{{ old('student_reminder_email_time',$student_reminder_email_time) }}" required autocomplete="off">
                    </div>
                </div>
                <div class="form-group row">
                    <label class="col-lg-2 col-form-label">{{ __('messages.student-reminder-email-lesson-types') }}</label>
                    <div class="col-lg-10 pt-2">
                        <label for="srel_class" class="mr-3"><input type="checkbox" name="student_reminder_email_lesson_types[]" value="class" id="srel_class" {{ in_array('class',$student_reminder_email_lesson_types) ? 'checked' : '' }} >{{ __('messages.classes') }}</label>
                        <label for="srel_event" class="mr-3"><input type="checkbox" name="student_reminder_email_lesson_types[]" value="event" id="srel_event" {{ in_array('event',$student_reminder_email_lesson_types) ? 'checked' : '' }} >{{ __('messages.events') }}</label>
                    </div>
                </div>
                <div class="form-group row">
					<label class="col-lg-2 col-form-label">{{ __('messages.school-timezone') }}</label>
					<div class="col-lg-10">
                        @php
                            $tzlist = DateTimeZone::listIdentifiers(DateTimeZone::ALL);
                        @endphp
						<select name="school_timezone" id="school_timezone" class="form-control {{ $errors->has('school_timezone') ? ' is-invalid' : '' }}">
                            @foreach($tzlist as $tz)
                                @php $utc_offset = "UTC/GMT ". \Carbon\carbon::now($tz)->format('P'); @endphp;
                                <option value="{{ $tz }}" <?php if(old('school_timezone',$school_timezone) == $tz) echo 'selected'; ?>>{{ $utc_offset.' - '.$tz }}</option>
                            @endforeach
                        </select>
					</div>
                </div>

                <div class="form-group row">
                    <div class="col-2">
                        <label>{{ __('messages.favicon') }}</label>
                    </div>
                    <div class="col-10">
                        <div class="input-group mb-3">
                            <div class="custom-file">
                                <input type="file" class="custom-file-input" id="inputGroupFile02" name="favicon" data-default_placeholder="{{ __('messages.choose-image') }}">
                                <label class="custom-file-label" for="inputGroupFile02" aria-describedby="inputGroupFileAddon02"></label>
                            </div>
                        </div>
                        <input type="hidden" id="remove_favicon" name="remove_favicon" value="0">
                        @if(\Storage::disk('public')->exists('favicon.ico'))
                            <div id="favicon_preview_section">
                                <div>
                                <img src="{{ \Storage::disk('public')->url('favicon.ico') }}" alt="{{ __('messages.favicon') }}" style="max-width:100px;">
                                </div>
                                <div>
                                    <button type="button" id="remove_favicon_btn" class="btn btn-sm btn-primary mt-1" >{{ __('messages.remove-image') }}</button>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>

                <div class="form-group row">
                    <label class="col-lg-2 col-form-label">{{ __('messages.show-other-teachers-classes') }}</label>
                    <div class="col-lg-10 pt-2">
                        <label class="mr-3"><input type="checkbox" name="show_other_teachers_classes" value="class" {{ $show_other_teachers_classes == 1 ? 'checked' : '' }} ></label>
                        <br>
                        <label style="font-style:italic">{{ __('messages.show-other-teachers-classes-desc') }}.</label>
                    </div>
                </div>

                <hr>
                <div class="form-group row">
	            	<label class="col-lg-2 col-form-label"></label>
	            	<div class="col-lg-10">
                        <label><input type="checkbox" id="use_zoom" name="use_zoom" {{ $use_zoom == 1 ? 'checked' : '' }} >{{ __('messages.use-zoom') }}</label>
	            	</div>
                </div>

                <div id="zoom_fields" style="{{ $use_zoom == 0 ? 'display:none;' : '' }}">
                    <div class="form-group row">
                        <label class="col-lg-2 col-form-label">{{ __('messages.zoom-api-key') }}:</label>
                        <div class="col-lg-10">
                            <input type="text" name="zoom_api_key" id="zoom_api_key" class="form-control required {{ $errors->has('zoom_api_key') ? ' is-invalid' : '' }}" value="{{ old('zoom_api_key',$zoom_api_key) }}" autocomplete="off">
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-lg-2 col-form-label">{{ __('messages.zoom-secret-key') }}:</label>
                        <div class="col-lg-10">
                            <input type="text" name="zoom_secret_key" id="zoom_secret_key" class="form-control required {{ $errors->has('zoom_secret_key') ? ' is-invalid' : '' }}" value="{{ old('zoom_secret_key',$zoom_secret_key) }}" autocomplete="off">
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-lg-12 col-form-label">{{ __('messages.zoom-meeting-reminder-settings') }}</label>
                    </div>
                    <div class="form-group row">
                        <label class="col-lg-2 col-form-label">{{ __('messages.send-email-notification-to') }}:</label>
                        <div class="col-lg-10 pt-2">
                        <label class="mr-3"><input type="checkbox" name="zoom_email_notification_to[]" value="student" {{ in_array('student',$zoom_email_notification_to) ? 'checked' : '' }} >{{ __('messages.student')}}</label>
                        <label class="mr-3"><input type="checkbox" name="zoom_email_notification_to[]" value="teacher" {{ in_array('teacher',$zoom_email_notification_to) ? 'checked' : '' }} >{{ __('messages.teacher') }}</label>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-lg-2 col-form-label">{{ __('messages.send-email-notification-before') }}:</label>
                        <div class="col-lg-10">
                        <input type="text" name="zoom_email_notification_before" id="zoom_email_notification_before" class="form-control required {{ $errors->has('zoom_email_notification_before') ? ' is-invalid' : '' }}" value="{{ old('zoom_email_notification_before',$zoom_email_notification_before) }}" autocomplete="off">{{ __('messages.minute(s)') }} [ {{ __('messages.set-value-to-greater-than-0-to-keep-sending-emails-enter-0-to-stop-sending') }} ]
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-lg-2 col-form-label">{{ __('messages.webhook-setup') }}:</label>
                        <div class="col-lg-10">
                                <p>
                                    {{ __('messages.setup-event-subscriptions-on-your-zoom-app-as-per-below-settings') }} <br><br>

                                    Enter <b>{{ route('api.zoom.webhook') }}</b> in <b>Event notification endpoint URL</b> field <br><br>
                                    
                                    In <b>Event types</b> section select following events <br>
                                    <em>Meeting:</em> 
                                    <li>Meeting has been updated</li>
                                    <li>Meeting has been deleted</li> 

                                    <br>
                                    {{ __('messages.once-you-submit-these-details-on-zoom-verification-token-will-be-generated-and-displayed-on-zoom-please-enter-that-verification-token-below') }}
                                </p>
                            </p>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-lg-2 col-form-label">{{ __('messages.webhook-verification-token') }}:</label>
                        <div class="col-lg-10">
                        <input type="text" name="zoom_webhook_verification_token" id="zoom_webhook_verification_token" class="form-control required {{ $errors->has('zoom_webhook_verification_token') ? ' is-invalid' : '' }}" value="{{ old('zoom_webhook_verification_token',$zoom_webhook_verification_token) }}" autocomplete="off">
                        </div>
                    </div>
                </div>

                <div class="form-group row">
                    <label class="col-lg-2 col-form-label"></label>
                    <div class="col-lg-10">
                        <input name="edit" type="submit" value="{{ __('messages.edit') }}" class="btn btn-success form-control">
                    </div>
                </div>
            </form>
		</div>
	</div>
@endsection

@push('scripts')
<script>
    window.addEventListener('DOMContentLoaded', function() {
        showHideZoomFields();
        $('#use_zoom').change(function(){
            showHideZoomFields();
        });

        $('#student_reminder_email_time').datetimepicker({
            format: 'HH:mm',
            useCurrent: false,
            defaultDate: false
        });
        $('#school_timezone').select2({ width: '100%'  });

        $('#remove_favicon_btn').click(function(){
            $('#remove_favicon').val(1);
            $('#favicon_preview_section').hide();
        });
    });
  
    function showHideZoomFields()
    {
        if($('#use_zoom').is(':checked'))
        {
            $('#zoom_fields').show();
            $('#zoom_fields .required').attr('required',true);
        }
        else
        {
            $('#zoom_fields').hide();
            $('#zoom_fields .required').removeAttr('required');
        }
    }
</script>
@endpush
