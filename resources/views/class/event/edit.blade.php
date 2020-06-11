@extends('layouts.app')

@section('content')
    <div class="row justify-content-center">
        <div class="col-12">
            @include('partials.success')
            @include('partials.error')
            <form method="POST" action="{{ route('event.update', $event->id) }}">
                @method('PATCH')
                @csrf
                <h1>{{ __('messages.editevent') }}</h1>
                <div class="form-group row">
                    <label class="col-lg-2 col-form-label">{{ __('messages.eventname') }}</label>
                    <div class="col-lg-10">
                        <input name="title" type="text" class="form-control{{ $errors->has('title') ? ' is-invalid' : '' }}" value="{{ empty(old('title')) ? $event->title : old('title') }}" required="">
                    </div>
                </div>
                <div class="form-group row">
                    <label class="col-lg-2 col-form-label">{{ __('messages.category') }}</label>
                    <div class="col-lg-10">
                        <select name="category_id" class="form-control">
                            <option value="">{{ __('messages.select-category') }}</option>
                            @if($categories->count() > 0)
                                @foreach($categories as $category)
                                    <option value="{{ $category->id }}" @if($category->id == $event->category_id) selected @endif>{{ $category->name }}</option>
                                @endforeach
                            @endif
                        </select>
                    </div>
                </div>
                <div class="form-group row">
                    <label class="col-lg-2 col-form-label">{{ __('messages.level') }}</label>
                    <div class="col-lg-10">
                        <select name="level" class="form-control{{ $errors->has('level') ? ' is-invalid' : '' }}" required>
                            <option value="">{{ __('messages.please-select-level') }}</option>
                            @if($class_student_levels)
                                @foreach($class_student_levels as $level)
                                    <option value="{{ $level }}"
                                            @if($level == $event->level) selected @endif>{{ $level }}</option>
                                @endforeach
                            @endif
                        </select>
                    </div>
                </div>
                <div class="form-group row">
                    <label class="col-lg-2 col-form-label">{{ __('messages.description') }}</label>
                    <div class="col-lg-10">
                        <textarea name="description" class="form-control{{ $errors->has('description') ? ' is-invalid' : '' }}" required="">{{ empty(old('description')) ? $event->description : old('description') }}</textarea>
                    </div>
                </div>
                <div class="form-group row">
                    <label class="col-lg-2 col-form-label">{{ __('messages.date') }}</label>
                    <div class="col-lg-10">
                        <input name="date" type="date" class="form-control{{ $errors->has('date') ? ' is-invalid' : '' }}" value="{{ empty(old('date')) ? $schedule->date : old('date') }}" required="">
                    </div>
                </div>
                <div class="form-group row">
                    <label class="col-lg-2 col-form-label">{{ __('messages.cost') }}</label>
                    <div class="col-lg-10">
                        <input name="cost" type="number" class="form-control{{ $errors->has('cost') ? ' is-invalid' : '' }}" value="{{ empty(old('cost')) ? $event->cost : old('cost') }}" required="">
                    </div>
                </div>
                <div class="form-group row">
                    <label class="col-lg-2 col-form-label">{{ __('messages.size') }}</label>
                    <div class="col-lg-10">
                        <input name="size" type="number" class="form-control{{ $errors->has('size') ? ' is-invalid' : '' }}" value="{{ empty(old('size')) ? $event->size : old('size') }}" required="">
                    </div>
                </div>
                <div class="form-group row">
                    <label class="col-lg-2 col-form-label">{{ __('messages.isallday') }}</label>
                    <div class="col-lg-10">
                        <input name="allday" type="checkbox" id="all_day" class="form-control{{ $errors->has('all_day') ? ' is-invalid' : '' }}" value="allday" {{ $schedule->type == "2" ? 'checked' : '' }}>
                    </div>
                </div>
                <div class="form-group row" style="{{ $schedule->type == 2 ? 'display: none' : '' }}">
                    <label class="col-lg-2 col-form-label">{{ __('messages.starttime') }}</label>
                    <div class="col-lg-10">
                        <input name="start_time" type="time" class="form-control{{ $errors->has('start_time') ? ' is-invalid' : '' }}" value="{{ empty(old('start_time')) ? $schedule->start_time : old('start_time') }}" required="">
                    </div>
                </div>
                <div class="form-group row" style="{{ $schedule->type == 2 ? 'display: none' : '' }}">
                    <label class="col-lg-2 col-form-label">{{ __('messages.endtime') }}</label>
                    <div class="col-lg-10">
                        <input name="end_time" type="time" class="form-control{{ $errors->has('end_time') ? ' is-invalid' : '' }}" value="{{ empty(old('end_time')) ? $schedule->end_time : old('end_time') }}" required="">
                    </div>
                </div>

                <div class="form-group row">
                    <label class="col-lg-2 col-form-label"></label>
                    <div class="col-lg-10">
                        <input name="edit" type="submit" value="{{ __('messages.edit') }}" class="btn btn-success form-control">
                    </div>
                </div>
        </div>
        </form>
    </div>
    </div>
@endsection
