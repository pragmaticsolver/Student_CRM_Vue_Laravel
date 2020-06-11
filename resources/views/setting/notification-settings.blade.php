@extends('layouts.app')

@section('content')
    <div class="row justify-content-center">
        <div class="col-12">
            <h1>{{ __('messages.notification-settings') }}</h1>
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

            <div class="accordion" id="accordionExample1">
                <div class="card">
                    <div class="card-header" id="heading_enable_disable_noti">
                        <h2 class="mb-0">
                            <button class="btn btn-link" type="button" data-toggle="collapse" data-target="#collapse_enable_disable_noti" aria-expanded="false" aria-controls="collapse_enable_disable_noti">
                            {{ __('messages.enable-/-disable-notification') }}
                            </button>
                        </h2>
                    </div>
                    <div id="collapse_enable_disable_noti" class="collapse" aria-labelledby="heading_enable_disable_noti" data-parent="#accordionExample1">
                        <div class="card-body">
                            <form id="notification-status-form">
                                <div class="form-group row">
                                    <div class="col-2">
                                        <label>
                                            {{ __('messages.untick-checkbox-to-disable-notificaion') }}
                                        </label>
                                    </div>
                                    <div class="col">
                                        @foreach($email_templates as $key => $email_template)
                                            @php $emailTemplate = $email_template['db'] @endphp
                                            <label class="mb-1"><input type="checkbox" name="enable[]" value="{{ $emailTemplate->name }}" {{ $emailTemplate->is_enable() ? 'checked' : '' }} >{{ $email_template['title'] }}
                                            </label>
                                            <br>
                                        @endforeach
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <div class="col">
                                        <input name="edit" type="submit" value="Save" class="btn btn-success form-control">
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            <hr>

            <h2 class="my-4">{{ __('messages.notification-texts') }}</h2>
            <div class="accordion" id="accordionExample">
                @foreach($email_templates as $key => $email_template)
                <div class="card">
                    <div class="card-header" id="heading_{{ $key }}">
                        <h2 class="mb-0">
                            <button class="btn btn-link" type="button" data-toggle="collapse" data-target="#collapse_{{ $key }}" aria-expanded="false" aria-controls="collapse_{{ $key }}">
                            {{ $email_template['title'] }}
                            </button>
                        </h2>
                    </div>

                    <div id="collapse_{{ $key }}" class="collapse" aria-labelledby="heading_{{ $key }}" data-parent="#accordionExample">
                        <div class="card-body">
                            <form class="notification-text-form" autocomplete="off">
                                <div class="mb-4 email_template_row">
                                    @php
                                        $subject_field_en = 'subject_en';
                                        $content_field_en = 'content_en';
                                        $subject_field_ja = 'subject_ja';
                                        $content_field_ja = 'content_ja';
                                        $emailTemplate = $email_template['db'];
                                    @endphp
                                    <input type="hidden" name="name" value="{{ $emailTemplate->name }}">
                                    <p>
                                        {{__('messages.parameters')}}:
                                        <b>
                                            <span class="text text-primary">
                                                @foreach($email_templates_global_parameters as $template_variable)
                                                    {{ '{'.$template_variable['name'].'}' }}
                                                    @if(isset($template_variable['info']) && $template_variable['info'] != '')
                                                        <i class="fa fa-info-circle mr-1" data-toggle="tooltip" title="{{ $template_variable['info'] }}" data-placement="right"></i>
                                                    @endif
                                                @endforeach
                                            </span>
                                            @foreach($email_template['template_variables'] as $template_variable)
                                                {{ '{'.$template_variable['name'].'}' }}
                                                @if(isset($template_variable['info']) && $template_variable['info'] != '')
                                                    <i class="fa fa-info-circle mr-1" data-toggle="tooltip" title="{{ $template_variable['info'] }}" data-placement="right"></i>
                                                @endif
                                            @endforeach
                                        </b>
                                    </p>
                                    <p>{{ __('messages.usage') }}: {{ $email_template['usage'] }}</p>
                                    <h5>{{ __('messages.email-notification-texts') }}</h5>
                                    <div class="form-group row">
                                        <div class="col-6 pl-0">
                                            <div class="col-12">
                                                <label class="mt-1">{{ __('messages.emailsubject') }} (English):</label>
                                                <input name="{{ $subject_field_en }}" type="text" value="{{ old( $subject_field_en, $emailTemplate->subject_en ) }}" 
                                                class="form-control{{ $errors->has($subject_field_en) ? ' is-invalid' : '' }}" >
                                            </div>
                                            <div class="col-12">
                                                <label class="mt-1">{{ __('messages.emailcontent') }} (English):</label>
                                                <textarea name="{{ $content_field_en }}" rows="7" class="form-control{{ $errors->has($content_field_en)}}" >{{ old($content_field_en, $emailTemplate->content_en) }}</textarea>
                                            </div>
                                            <div class="col-12">
                                                @include('setting.button-texts', ['emailTemplate' => $emailTemplate, 'lang' => 'en'])
                                            </div>
                                        </div>
                                        <div class="col-6 pl-0">
                                            <div class="col-12">
                                                <label class="mt-1">{{ __('messages.emailsubject') }} (Japanese):</label>
                                                <input name="{{ $subject_field_ja }}" type="text" value="{{ old( $subject_field_ja, $emailTemplate->subject_ja ) }}" 
                                                class="form-control{{ $errors->has($subject_field_ja) ? ' is-invalid' : '' }}" >
                                            </div>
                                            <div class="col-12">
                                                <label class="mt-1">{{ __('messages.emailcontent') }} (Japanese):</label>
                                                <textarea name="{{ $content_field_ja }}" rows="7" class="form-control{{ $errors->has($content_field_ja)}}" >{{ old($content_field_ja, $emailTemplate->content_ja ) }}</textarea>
                                            </div>
                                            <div class="col-12">
                                                @include('setting.button-texts', ['emailTemplate' => $emailTemplate, 'lang' => 'ja'])
                                            </div>
                                        </div>
                                        @if($key == 'daily_reservation_reminder')
                                            <div class="col-12 mt-1">
                                                <div class="form-group row preview_email_section mt-0 mb-4">
                                                    <div class="col-3">
                                                        <label>{{ __('messages.student') }}</label>
                                                        <select class="form-control drr_student_id" name="student_id">
                                                            @foreach($students as $student)
                                                                <option value="{{ $student->id }}">{{ $student->full_name }}</option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                    <div class="col-2">
                                                        <label>{{ __('messages.date') }}</label>
                                                        <input class="form-control drr_date" type="date" name="date" value="{{ $now->format('Y-m-d') }}">
                                                    </div>
                                                    @foreach ($email_lesson_types as $email_lesson_type)
                                                    <div class="col-2 reminder_toggle">
                                                        <label>{{ __('messages.'.$email_lesson_type.'_reminder') }}</label>
                                                        <input type="checkbox" class="toggle" data-toggle="toggle" name="{{ $email_lesson_type.'_reminder' }}"  {{ \App\Settings::get_value($email_lesson_type.'_reminder') ? 'checked' : '' }}>
                                                    </div>
                                                    @endforeach
                                                   
                                                    <div class="col">
                                                        <label for="" style="opacity:0;">dummy_text</label><br>
                                                        <button type="button" data-lang="en" class="btn btn-primary drr_view_email_preview_btn">{{ __('messages.preview') }} (English)</button>
                                                        <button type="button" data-lang="ja" class="btn btn-primary drr_view_email_preview_btn">{{ __('messages.preview') }} (Japanse)</button>
                                                    </div>
                                                </div>
                                            </div>
                                        @endif
                                    </div>
                                    
                                    @if($use_line_messaging_api)
                                        @include('setting.line-notification-texts')
                                    @endif
                                </div>
                                <div class="form-group row">
                                    <div class="col">
                                        <input name="edit" type="submit" value="{{ __('messages.save') }}" class="btn btn-success form-control">
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script src="//ajax.googleapis.com/ajax/libs/jquery/2.1.3/jquery.min.js"></script>
<link href="//gitcdn.github.io/bootstrap-toggle/2.2.2/css/bootstrap-toggle.min.css" rel="stylesheet">
<script src="//gitcdn.github.io/bootstrap-toggle/2.2.2/js/bootstrap-toggle.min.js"></script>

<script src="{{ asset('public'.mix('js/page/setting/notification-settings.js')) }}"></script>
@endpush