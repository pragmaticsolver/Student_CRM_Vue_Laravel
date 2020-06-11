@extends('layouts.app')

@section('content')
    <div class="row justify-content-center">
        <div class="col-12">
            @if(session()->get('success'))
                <div class="alert alert-success">
                    {{ session()->get('success') }}
                </div><br/>
            @endif
            @if(session()->get('error'))
                <div class="alert alert-success">
                    {{ session()->get('error') }}
                </div><br/>
            @endif

            <form method="POST" action="{{ route('book.checkin.store') }}">
                @csrf
                <h1>{{ __('messages.checkin') }}</h1>
                <div class="form-group row">
                    <label class="col-lg-2 col-form-label">{{ __('messages.barcode') }}</label>
                    <div class="col-lg-10">
                        <input name="barcode" type="text" class="form-control{{ $errors->has('barcode') ? ' is-invalid' : '' }}" value="{{ old('barcode') }}" required autofocus>
                    </div>
                </div>
                <div class="form-group row">
                    <label class="col-lg-2 col-form-label">{{ __('messages.student') }}</label>
                    <div class="col-lg-10">
                        <select name="student_id" class="form-control" required="">
                            <option value="">{{ __('messages.selectstudent') }}</option>
                            @if(!$students->isEmpty())
                                @foreach($students as $student)
                                    <option value="{{$student->id}}" <?php if($student_id && $student->id == $student_id) echo 'selected'; ?>>{{ $student->get_kanji_name() }}</option>
                                @endforeach
                            @endif
                        </select>
                    </div>
                </div>
                <div class="form-group row">
                    <label class="col-lg-2 col-form-label">{{ __('messages.checkindate') }}</label>
                    <div class="col-lg-10">
                        <input name="checkin_date" type="date" class="form-control{{ $errors->has('checkin_date') ? ' is-invalid' : '' }}" value="{{ $today }}" required>
                    </div>
                </div>
                <div class="form-group row">
                    <label class="col-lg-2 col-form-label"></label>
                    <div class="col-lg-10">
                        <input name="add" type="submit" value="{{ __('messages.checkin') }}" class="form-control btn-success">
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection
