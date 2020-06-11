
@php
    if ($lang == 'en') {
        $langLabel = 'English';
        $lineTextDbFieldName = 'text_en';
    } else if ($lang == 'ja') {
        $langLabel = 'Japanese';
        $lineTextDbFieldName = 'text_ja';
    }
@endphp

@foreach($email_template['line_text_fields'] as $lineTextField)
    <div class="col-12">
        <label class="mt-1">{{ ucfirst(str_replace("_"," ",$lineTextField['name'] )) }} ({{$langLabel}}):</label>
            
        @if($lineTextField['max_length'] > 20)
            <textarea name="{{ $lineTextField['name'] }}_{{ $lang }}" rows="7" class="form-control char-sensitive-field" data-max-length="{{ $lineTextField['max_length'] }}">{{ @$lineTexts[$lineTextField['name']][$lineTextDbFieldName] }}</textarea>
        @else
            <input name="{{ $lineTextField['name'] }}_{{ $lang }}" type="text" value="{{ @$lineTexts[$lineTextField['name']][$lineTextDbFieldName] }}" class="form-control char-sensitive-field" data-max-length="{{ $lineTextField['max_length'] }}">
        @endif

        <div class="float-right char-length-indicator"></div>
    </div>
    <div class="clearfix"></div>
@endforeach