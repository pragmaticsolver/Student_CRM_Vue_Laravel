@extends('layouts.app')

@section('content')
    <div class="row justify-content-center">
        <div class="col-12">
            @include('partials.success')
            @include('partials.error')
            <form method="POST" action="{{ route('class.update', $class->id) }}">
                @method('PATCH')
                @csrf
                <h1>{{ __('messages.editclass') }}</h1>
                <div class="form-group row">
                    <label class="col-lg-2 col-form-label">{{ __('messages.title') }}</label>
                    <div class="col-lg-10">
                        <input name="title" type="text" value="{{empty(old('title')) ? $class->title : old('title')}}"
                               class="form-control{{ $errors->has('title') ? ' is-invalid' : '' }}" required="">
                    </div>
                </div>
                <div class="form-group row">
                    <label class="col-lg-2 col-form-label">{{ __('messages.category') }}</label>
                    <div class="col-lg-10">
                        <select name="category_id" class="form-control{{ $errors->has('category_id') ? ' is-invalid' : '' }}" required>
                            <option value="">{{ __('messages.select-category') }}</option>
                            @if($categories->count() > 0)
                                @foreach($categories as $category)
                                    <option value="{{ $category->id }}" @if($category->id == old('category_id',$class->category_id) ) selected @endif>{{ $category->name }}</option>
                                @endforeach
                            @endif
                        </select>
                    </div>
                </div>
                <div class="form-group row">
                    <label class="col-lg-2 col-form-label">{{ __('messages.size') }}</label>
                    <div class="col-lg-10">
                        <input name="size" type="number"
                               class="form-control{{ $errors->has('size') ? ' is-invalid' : '' }}"
                               value="{{ old('size',$class->size) }}">
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
                                            @if($level == old('level',$class->level)) selected @endif>{{ $level }}</option>
                                @endforeach
                            @endif
                        </select>
                    </div>
                </div>
                <div class="form-group row">
                    <label class="col-lg-2 col-form-label">{{ __('messages.length') }}</label>
                    <div class="col-lg-10">
                        <input type="text" name="length" id="length" class="form-control" value="{{empty(old('length')) ? $class->length : old('length')}}" autocomplete="off">
                    </div>
                </div>
                <div class="form-group row">
                    <label class="col-lg-2 col-form-label">{{ __('messages.default-course') }}</label>
                    <div class="col-lg-10">
                        <select name="default_course_id" class="form-control{{ $errors->has('default_course_id') ? ' is-invalid' : '' }}" >
                            <option value="">None</option>
                            @foreach($courses as $course)
                                <option value="{{ $course->id }}" {{ $course->id == old('default_course_id',$class->default_course_id) ? 'selected'  : '' }}>{{ $course->title }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="form-group row">
                    <label class="col-lg-2 col-form-label"></label>
                    <div class="col-lg-10">
                        <input name="edit" type="submit" value="{{ __('messages.edit') }}"
                               class="btn btn-success form-control">
                    </div>
                </div>
        </div>
        </form>
    </div>
    </div>
@endsection
