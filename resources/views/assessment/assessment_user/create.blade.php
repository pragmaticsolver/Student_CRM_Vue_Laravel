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
            <h1>{{ __('messages.addassessment') }}</h1>
            <form method="POST" action="{{ route('assessment_user.store', $schedule->id) }}">
                @csrf

                <div class="form-group row">
                    <label class="col-lg-2 col-form-label">{{ __('messages.assessment') }}</label>
                    <div class="col-lg-10">
                        <select name="assessment_id" class="form-control" required>
                            <option value="">{{ __('messages.selectassessment') }}</option>
                            @if($assessments->count() > 0)
                                @foreach($assessments as $assessment)
                                    <option value="{{$assessment->id}}" @if($assessment->id == old('assessment_id')) selected @endif>{{ $assessment->name }}</option>
                                @endforeach
                            @endif
                        </select>
                    </div>
                </div>

                <div class="form-group row">
                    <label class="col-lg-2 col-form-label">{{ __('messages.sendto') }}</label>
                    <div class="col-lg-10">
                        <select name="send_to" class="form-control{{ $errors->has('send_to') ? ' is-invalid' : '' }}" required>
                            <option value="">{{ __('messages.sendto') }}</option>
                            <option value="teacher" {{ old('send_to') == 'teacher' ? 'selected' : '' }}>{{ __('messages.teacher') }}</option>
                            <option value="student" {{ old('send_to') == 'student' ? 'selected' : '' }}>{{ __('messages.student') }}</option>
                        </select>
                    </div>
                </div>

                <div class="form-group row">
                    <label class="col-lg-2 col-form-label">{{ __('messages.student') }}</label>
                    <div class="col-lg-10">
                        @if($students->count() > 0)
                            <div class="row no-gutters">
                                @foreach($students as $student)
                                    <div class="col-lg-3 form-check">
                                        <label>
                                            <input type="checkbox" value="{{ $student->id }}" id="student{{ $student->id }}" name="students[]">
                                            {{ $student->get_kanji_name() }}
                                        </label>
                                    </div>
                                @endforeach
                            </div>
                        @endif
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
