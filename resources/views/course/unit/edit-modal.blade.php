    @method('PATCH')
    @csrf
    <div class="form-group row">
        <label class="col-lg-2 col-form-label">{{ __('messages.name') }}</label>
        <div class="col-lg-10">
            <input name="name" type="text" value="{{empty(old('name')) ? $unit->name : old('name')}}" class="form-control{{ $errors->has('name') ? ' is-invalid' : '' }}" required="">
        </div>
    </div>
    <div class="form-group row">
        <label class="col-lg-2 col-form-label">{{ __('messages.objectives') }}</label>
        <div class="col-lg-10">
            <textarea name="objectives" id="objectives" rows="2" class="form-control{{ $errors->has('objectives') ? ' is-invalid' : '' }}"  required>{{ old('objectives',$unit->objectives) }}</textarea>
        </div>
    </div>
    <input type="hidden" name="course_id" value="{{ $unit->course->id }}">
    <input type="hidden" name="unit_id" value="{{ $unit->id }}">
