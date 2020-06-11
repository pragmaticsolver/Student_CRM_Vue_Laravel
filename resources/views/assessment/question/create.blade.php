@extends('layouts.app')

@section('content')
    <div class="row justify-content-center">
        @include('partials.success')
        @include('partials.error')
        <div class="col-12">
            <h1>{{ __('messages.addquestion') }}</h1>
            <form method="POST" action="{{ route('assessment-question.store') }}">
                @csrf

                @include('assessment.question.fields')

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

@push('scripts')
    <script>
        window.addEventListener('DOMContentLoaded', function(){
            reInitializeQuestionsForm();
        });
    </script>
@endpush
