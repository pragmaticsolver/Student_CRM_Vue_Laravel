@if ($is_student)
<div class="form-group row">
    <label class="col-lg-4 col-form-label">{{ __('messages.lastnameromaji') }}</label>
    <div class="col-lg-8">
        <input name="lastname" type="text" class="form-control{{ $errors->has('lastname') ? ' is-invalid' : '' }}" value="{{ old('lastname') }}" placeholder="{{ __('messages.lastnameromajiplaceholder') }}" required="">
    </div>
</div>
<div class="form-group row">
    <label class="col-lg-4 col-form-label">{{ __('messages.firstnameromaji') }}</label>
    <div class="col-lg-8">
        <input name="firstname" type="text" class="form-control{{ $errors->has('firstname') ? ' is-invalid' : '' }}" value="{{ old('firstname') }}" placeholder="{{ __('messages.firstnameromajiplaceholder') }}" required="">
    </div>
</div>
<div class="form-group row">
    <label class="col-lg-4 col-form-label">{{ __('messages.lastnamekanji') }}</label>
    <div class="col-lg-8">
        <input name="lastname_kanji" type="text" class="form-control{{ $errors->has('lastname_kanji') ? ' is-invalid' : '' }}" value="{{ old('lastname_kanji') }}" placeholder="{{ __('messages.lastnamekanjiplaceholder') }}" required="">
    </div>
</div>
<div class="form-group row">
    <label class="col-lg-4 col-form-label">{{ __('messages.firstnamekanji') }}</label>
    <div class="col-lg-8">
        <input name="firstname_kanji" type="text" class="form-control{{ $errors->has('firstname_kanji') ? ' is-invalid' : '' }}" value="{{ old('firstname_kanji') }}" placeholder="{{ __('messages.firstnamekanjiplaceholder') }}" required="">
    </div>
</div>
<div class="form-group row">
    <label class="col-lg-4 col-form-label">{{ __('messages.lastnamekatakana') }}</label>
    <div class="col-lg-8">
        <input name="lastname_furigana" type="text" class="form-control{{ $errors->has('lastname_furigana') ? ' is-invalid' : '' }}" value="{{ old('lastname_furigana') }}" placeholder="{{ __('messages.lastnamekatakanaplaceholder') }}" required="">
    </div>
</div>
<div class="form-group row">
    <label class="col-lg-4 col-form-label">{{ __('messages.firstnamekatakana') }}</label>
    <div class="col-lg-8">
        <input name="firstname_furigana" type="text" class="form-control{{ $errors->has('firstname_furigana') ? ' is-invalid' : '' }}" value="{{ old('firstname_furigana') }}" placeholder="{{ __('messages.firstnamekatakanaplaceholder') }}" required="">
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
        <input name="mobile_phone" type="tel" class="form-control{{ $errors->has('mobile_phone') ? ' is-invalid' : '' }}" value="{{ old('mobile_phone') }}" placeholder="{{ __('messages.cellphoneplaceholder') }}" required="">
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
    <label class="col-lg-4 col-form-label">{{ __('messages.levels') }}</label>
    <div class="col-lg-8">
        <select id="levels" name="levels[]" class="form-control{{ $errors->has('levels') ? ' is-invalid' : '' }}" required multiple>
            @if($class_student_levels)
                @foreach($class_student_levels as $level)
                    <option value="{{ $level }}" {{ in_array($level,(array)old('levels')) ? 'selected="selected"' : '' }}>{{ $level }}</option>
                @endforeach
            @endif
        </select>
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
    <label class="col-lg-4 col-form-label"></label>
    <div class="col-lg-8">
        <div class="row">
            <div class="col-sm-6">
                <a href="{{route('login')}}" class="form-control btn-danger text-center">{{ __('messages.back') }}</a>
            </div>
            <div class="col-sm-6">
                <input name="add" type="submit" value="{{ __('messages.signup') }}" class="form-control btn-success">
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    window.levelsPlaceholder = "{{ __('messages.please-select-level-s') }}";
	window.addEventListener('DOMContentLoaded', function() {
        $('#levels').select2({
            width: '100%',
            placeholder: levelsPlaceholder
        });
    });
</script>
@endpush


@else
    @include('auth.signup.admin')
@endif
