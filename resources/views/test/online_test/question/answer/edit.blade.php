@extends('layouts.app')

@section('content')
    <div class="row justify-content-center">
        @include('partials.success')
        @include('partials.error')
        <div class="col-12">
            <form method="POST" action="{{ route('answer.update', $answer->id) }}">
                @csrf
                @method('PATCH')

                <h1>{{ __('messages.editanswer') }}</h1>
                <div class="form-group row">
                    <label class="col-lg-2 col-form-label">{{ __('messages.answer') }}</label>
                    <div class="col-lg-10">
                        <input name="answer" type="text" class="form-control{{ $errors->has('answer') ? ' is-invalid' : '' }}" value="{{ $answer->answer }}" required>
                    </div>
                </div>

                <div class="form-group row">
                    <label class="col-lg-2 col-form-label">{{ __('messages.order') }}</label>
                    <div class="col-lg-10">
                        <input name="order" type="number" class="form-control{{ $errors->has('order') ? ' is-invalid' : '' }}" value="{{ $answer->order }}" min="0" step="1">
                        <small id="fileHelp" class="form-text text-muted">{{ __('messages.0gofirst') }}</small>
                    </div>
                </div>

                <div class="form-group row">
                    <label class="col-lg-2 col-form-label">{{ __('messages.correct') }}</label>
                    <div class="col-lg-10">
                        <input
                            name="correct" type="checkbox" value="checked" @if($answer->correct) checked @endif
                            class="form-control{{ $errors->has('correct') ? ' is-invalid' : '' }}">
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
