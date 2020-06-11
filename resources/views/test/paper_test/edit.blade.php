@extends('layouts.app')

@section('content')
    <div class="row justify-content-center">
        @include('partials.success')
        @include('partials.error')
        <div class="col-12">
            <form method="POST" action="{{ route('paper_test.update', $paper_test->id) }}">
                @csrf
                @method('PATCH')

                <h1>{{ __('messages.editpapertest') }}</h1>
                <div class="form-group row">
                    <label class="col-lg-2 col-form-label">{{ __('messages.name') }}</label>
                    <div class="col-lg-10">
                        <input name="name" type="text" class="form-control{{ $errors->has('title') ? ' is-invalid' : '' }}" value="{{ $paper_test->name }}" required="">
                    </div>
                </div>

                <div class="form-group row">
                    <label class="col-lg-2 col-form-label">{{ __('messages.course') }}</label>
                    <div class="col-lg-10">
                        <select name="course_id" class="form-control" required="">
                            <option value="">{{ __('messages.selectcourse') }}</option>
                            @if(!$courses->isEmpty())
                                @foreach($courses as $course)
                                    <option value="{{$course->id}}" @if($course->id == $paper_test->course_id) selected @endif>{{$course->title}}</option>
                                @endforeach
                            @endif
                        </select>
                    </div>
                </div>

                <div class="form-group row">
                    <label class="col-lg-2 col-form-label">{{ __('messages.unit') }}</label>
                    <div class="col-lg-10">
                        <select name="unit_id" class="form-control" required="">
                            <option value="">{{ __('messages.selectunit') }}</option>
                            @if(!$units->isEmpty())
                                @foreach($units as $unit)
                                    <option
                                        value="{{$unit->id}}" data-course="{{ $unit->course->id }}" class="option-unit"
                                        @if($unit->id == $paper_test->unit_id) selected @endif>
                                        {{$unit->name}}
                                    </option>
                                @endforeach
                            @endif
                        </select>
                    </div>
                </div>

                <div class="form-group row">
                    <label class="col-lg-2 col-form-label">{{ __('messages.lesson') }}</label>
                    <div class="col-lg-10">
                        <select name="lesson_id" class="form-control" required="">
                            <option value="">{{ __('messages.selectlesson') }}</option>
                            @if(!$lessons->isEmpty())
                                @foreach($lessons as $lesson)
                                    <option
                                        value="{{ $lesson->id }}" data-course="{{ $lesson->course->id }}" data-unit="{{ $lesson->unit->id }}" class="option-lesson"
                                        @if($lesson->id == $paper_test->lesson_id) selected @endif>
                                        {{ $lesson->title }}
                                    </option>
                                @endforeach
                            @endif
                        </select>
                    </div>
                </div>

                <div class="form-group row">
                    <label class="col-lg-2 col-form-label">{{ __('messages.totalscore') }}</label>
                    <div class="col-lg-10">
                        <input name="total_score" type="number" class="form-control{{ $errors->has('total_score') ? ' is-invalid' : '' }}" value="{{ $paper_test->total_score }}" required="">
                    </div>
                </div>

                <div class="form-group row">
                    <label class="col-lg-2 col-form-label"></label>
                    <div class="col-lg-10">
                        <input name="add" type="submit" value="{{ __('messages.add') }}" class="form-control btn-success">
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection
