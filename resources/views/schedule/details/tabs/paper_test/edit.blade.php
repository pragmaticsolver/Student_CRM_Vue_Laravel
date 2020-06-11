@extends('layouts.app')

@section('content')
    <div class="row justify-content-center">
        @include('partials.success')
        @include('partials.error')
        @if($errors->any())
            <div class="alert alert-danger">
                <ul>
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div><br/>
        @endif
        <div class="col-lg-12">
            <h1>{{ __('messages.editpapertest') }}</h1>
            <form id="student_paper_test_form" method="POST" action="{{ route('student.paper_test.store', [$schedule->id, $studentPaperTest->id]) }}" >
                @csrf

                @include('schedule.details.tabs.paper_test.fields')

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
