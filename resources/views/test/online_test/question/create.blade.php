@extends('layouts.app')

@section('content')
    <div class="row justify-content-center">
        @include('partials.success')
        @include('partials.error')
        <div class="col-12">
	        <form method="POST" action="{{ route('question.store') }}">
	        	@csrf
	          	<h1>{{ __('messages.addquestion') }}</h1>
	          	<div class="form-group row">
	            	<label class="col-lg-2 col-form-label">{{ __('messages.name') }}</label>
	            	<div class="col-lg-10">
	              		<input name="question" type="text" class="form-control{{ $errors->has('question') ? ' is-invalid' : '' }}" value="{{ old('question') }}" required>
	            	</div>
	         	</div>

                <div class="form-group row">
                    <label class="col-lg-2 col-form-label">{{ __('messages.test') }}</label>
                    <div class="col-lg-10">
                        <select name="test_id" class="form-control" required="">
                            <option value="">{{ __('messages.selecttest') }}</option>
                            @if(!$tests->isEmpty())
                                @foreach($tests as $test)
                                    <option value="{{$test->id}}" @if($test_id && $test_id == $test->id) selected @endif>{{$test->name}}</option>
                                @endforeach
                            @endif
                        </select>
                    </div>
                </div>

                <div class="form-group row">
                    <label class="col-lg-2 col-form-label">{{ __('messages.score') }}</label>
                    <div class="col-lg-10">
                        <input name="score" type="number" class="form-control{{ $errors->has('score') ? ' is-invalid' : '' }}" value="{{ old('score') }}" required min="0" step="0.01">
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
