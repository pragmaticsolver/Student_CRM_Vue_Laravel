@extends('layouts.guest')
@section('content')
<div class="middle-box signup-screen animated fadeIn">
    <div>
        <div>
            <h1 class="text-center logo-name">{{ __('messages.ut') }}</h1>
        </div>

        <div class="container">
            <h2 class="text-center m-b-md">{{ __('messages.formregister') }}</h2>
            <div class="row justify-content-center">
                <div class="col-8">
                    @if($errors->any())
                        <div class="alert alert-danger">
                            <ul>
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                        <br/>
                    @endif
                    <form method="POST" action="{{ route('signup.store') }}">
                        @csrf
                        <input name="role" type="hidden" value="{{$signup_role}}">
                        <div class="form-group row">
                            <label class="col-lg-4 col-form-label">{{ __('messages.nickname')}}</label>
                            <div class="col-lg-8">
                                <input name="nickname" type="text" class="form-control{{ $errors->has('nickname') ? ' is-invalid' : '' }}" value="{{ old('nickname') }}" required="">
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-lg-4 col-form-label">{{ __('messages.email') }}</label>
                            <div class="col-lg-8">
                                <input name="email" type="email" class="form-control{{ $errors->has('email') ? ' is-invalid' : '' }}" value="{{ old('email') }}" required="">
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-lg-4 col-form-label">{{ __('messages.password') }}</label>
                            <div class="col-lg-8">
                                <input name="password" type="password" class="form-control{{ $errors->has('password') ? ' is-invalid' : '' }}" required="">
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-lg-4 col-form-label">{{ __('messages.confirmpassword') }}</label>
                            <div class="col-lg-8">
                                <input name="password_confirmation" type="password" class="form-control{{ $errors->has('password_confirm') ? ' is-invalid' : '' }}" required="">
                            </div>
                        </div>
                        <hr/>
                        @if($signup_role == 'teacher')
                            @include('auth.signup.teacher')
                        @elseif(!empty($is_student))
                            @include('auth.signup.student')
                        @else
                            @include('auth.signup.admin')
                        @endif
                        <br/><br/>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection


