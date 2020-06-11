@if($email_template['line_message_type'])
    <h5>{{ __('messages.line-notification-texts') }}</h5>
    <div class="form-group row">
        @php
        $lineTexts = $emailTemplate->lineTexts->keyBy('key')->all();
        @endphp
        <div class="col-6 pl-0">
            @include('setting.line-notification-text-fields', ['lang' => 'en'])
        </div>
        <div class="col-6 pl-0">
            @include('setting.line-notification-text-fields', ['lang' => 'ja'])
        </div>
    </div>
@endif