@extends('layouts.app')

@section('content')
    <div class="row justify-content-center">
        @include('partials.success')
        @include('partials.error')
        <div class="col-12">
            <form method="POST" action="{{ route('answer.store') }}">
                @csrf
                <h1>{{ __('messages.addanswer') }}</h1>
                <div class="form-group row">
                    <label class="col-lg-2 col-form-label">{{ __('messages.answer') }}</label>
                    <div class="col-lg-10">
                        <input name="answer" type="text" class="form-control{{ $errors->has('answer') ? ' is-invalid' : '' }}" value="{{ old('answer') }}" required>
                    </div>
                </div>

                <div class="form-group row">
                    <label class="col-lg-2 col-form-label">{{ __('messages.test') }}</label>
                    <div class="col-lg-10">
                        <select name="test_id" class="form-control" required="">
                            <option value="">{{ __('messages.selecttest') }}</option>
                            @if($tests->count() > 0)
                                @foreach($tests as $test)
                                    <option
                                        value="{{$test->id}}"
                                        @if($test_id && $test_id == $test->id) selected @endif
                                    >
                                        {{$test->name}}
                                    </option>
                                @endforeach
                            @endif
                        </select>
                    </div>
                </div>

                <div class="form-group row" style="display: none">
                    <label class="col-lg-2 col-form-label">{{ __('messages.question') }}</label>
                    <div class="col-lg-10">
                        <select name="question_id" class="form-control" required="">
                            <option value="">{{ __('messages.selectquestion') }}</option>
                            @if($questions->count() > 0)
                                @foreach($questions as $question)
                                    <option
                                        value="{{$question->id}}" data-test="{{ $question->test->id }}" class="option-question"
                                        @if($question_id && $question_id == $question->id) selected @endif
                                    >
                                        {{$question->question}}
                                    </option>
                                @endforeach
                            @endif
                        </select>
                    </div>
                </div>

                <div class="form-group row">
                    <label class="col-lg-2 col-form-label">{{ __('messages.order') }}</label>
                    <div class="col-lg-10">
                        <input name="order" type="number" class="form-control{{ $errors->has('order') ? ' is-invalid' : '' }}" value="{{ old('order') }}" min="0" step="1">
                        <small id="fileHelp" class="form-text text-muted">{{ __('messages.0gofirst') }}</small>
                    </div>
                </div>

                <div class="form-group row">
                    <label class="col-lg-2 col-form-label">{{ __('messages.correct') }}</label>
                    <div class="col-lg-10">
                        <input name="correct" type="checkbox" class="form-control{{ $errors->has('correct') ? ' is-invalid' : '' }}" value="checked">
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
