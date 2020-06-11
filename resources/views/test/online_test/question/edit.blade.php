@extends('layouts.app')

@section('content')
    <div class="row justify-content-center">
        @include('partials.success')
        @include('partials.error')
        <div class="col-12">
            <form method="POST" action="{{ route('question.update', $question->id) }}">
                @csrf
                @method('PATCH')

                <h1>{{ __('messages.editquestion') }}</h1>
                <div class="form-group row">
                    <label class="col-lg-2 col-form-label">{{ __('messages.question') }}</label>
                    <div class="col-lg-10">
                        <input name="question" type="text" class="form-control{{ $errors->has('question') ? ' is-invalid' : '' }}" value="{{ $question->question }}" required>
                    </div>
                </div>

                <div class="form-group row">
                    <label class="col-lg-2 col-form-label">{{ __('messages.score') }}</label>
                    <div class="col-lg-10">
                        <input name="score" type="number" class="form-control{{ $errors->has('order') ? ' is-invalid' : '' }}" value="{{ $question->score }}" min="0" step="0.01">
                    </div>
                </div>

                <div class="form-group row">
                    <label class="col-lg-2 col-form-label"></label>
                    <div class="col-lg-10">
                        <input name="add" type="submit" value="{{ __('messages.edit') }}" class="form-control btn-success">
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection
