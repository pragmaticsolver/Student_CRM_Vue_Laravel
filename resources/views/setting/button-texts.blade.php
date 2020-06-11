@if(count($emailTemplate->buttonTexts) > 0)
    @php 
        $title = $lang == 'en' ? "Button Texts (English)" : "Button Texts (Japanese)";
        $db_field = 'text_'.$lang;
    @endphp
    <label class="mt-1">{{$title}}: </label>
    <div class="row">
        @foreach ($emailTemplate->buttonTexts as $buttonText)
            <div class="col-sm-4 {{($key == 'daily_reservation_reminder' && ($buttonText->key =='cancel-class-reservation' && !\App\Settings::get_value('class_reminder')) || ($buttonText->key =='cancel-event-reservation' && !\App\Settings::get_value('event_reminder'))) ? 'hide' : '' }} btn_{{$buttonText->key}}">
                <label>{{ $buttonText->display_key }}</label>
                @php
                    $field_name = $buttonText->getInputFieldName($db_field);
                    $laravel_field_name = $buttonText->getLarvelFieldName($db_field);
                @endphp
                <input name="{{ $field_name }}" type="text" value="{{ old($laravel_field_name, $buttonText[$db_field]) }}" class="form-control{{ $errors->has($laravel_field_name) ? ' is-invalid' : '' }}"  required>
            </div>
        @endforeach
    </div>
@endif