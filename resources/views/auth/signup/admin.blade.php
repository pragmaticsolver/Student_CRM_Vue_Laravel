<div class="form-group row">
    <label class="col-lg-4 col-form-label">{{ __('messages.namekanji')}}</label>
    <div class="col-lg-8">
        <input name="fullname" type="text" class="form-control{{ $errors->has('fullname') ? ' is-invalid' : '' }}" value="{{ old('fullname') }}" required="">
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
