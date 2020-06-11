<div class="form-group row">
    <label class="col-lg-4 col-form-label">{{ __('messages.namekanji')}}</label>
    <div class="col-lg-8">
        <input name="fullname" type="text" class="form-control{{ $errors->has('fullname') ? ' is-invalid' : '' }}" value="{{ old('fullname') }}" required="">
    </div>
</div>
<div class="form-group row">
    <label class="col-lg-4 col-form-label">{{ __('messages.namekatakana')}}</label>
    <div class="col-lg-8">
        <input name="furigana" type="text" class="form-control{{ $errors->has('furigana') ? ' is-invalid' : '' }}" value="{{ old('furigana') }}" required="">
    </div>
</div>
<div class="form-group row">
    <label class="col-lg-4 col-form-label">{{ __('messages.birthday')}}</label>
    <div class="col-lg-8">
        <input name="birthday" type="date" class="form-control{{ $errors->has('birthday') ? ' is-invalid' : '' }}" value="{{ old('birthday') }}" required="">
    </div>
</div>
<div class="form-group row">
    <label class="col-lg-4 col-form-label">{{ __('messages.hometown')}}</label>
    <div class="col-lg-8">
        <input name="birthplace" type="text" class="form-control{{ $errors->has('birthplace') ? ' is-invalid' : '' }}" value="{{ old('birthplace') }}" required="">
    </div>
</div>
<div class="form-group row">
    <label class="col-lg-4 col-form-label">{{ __('messages.profile')}}</label>
    <div class="col-lg-8">
        <textarea name="profile" class="form-control{{ $errors->has('profile') ? ' is-invalid' : '' }}">{{ old('profile') }}</textarea>
    </div>
</div>
<div class="form-group row">
    <label class="col-lg-4 col-form-label">{{ __('messages.calendar-color-coding') }}</label>
    <div class="col-lg-8">
        <div id="color_picker" data-default="{{ $default_color }}"></div>
        <input type="hidden" value={{$default_color}} name="color_coding">
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
