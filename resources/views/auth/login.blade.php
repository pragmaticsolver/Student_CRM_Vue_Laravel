@extends('layouts.guest')
@php
    $linking_line = request()->linking_line ? true : false;
@endphp
@section('content')
    <div class="middle-box text-center loginscreen animated fadeIn">
        <div>
            <div>
                <h1 class="logo-name">{{ __(\App\Settings::get_value('school_initial')) }}</h1>
            </div>
            <h3>{{ __('messages.welcometo', ['name'=>\App\Settings::get_value('school_name')]) }}</h3>
            @include('partials.success')
            <form class="mt-3" role="form" method="post" action="{{ route('login') }}">
                @csrf
                
                @if($linking_line)
                    <div class="alert alert-info">
                        <span>{{ __('messages.please-login-into-your-account-to-connect-it-with-your-line-account') }}</span>
                    </div>
                @endif

                @if ($message = Session::get('line_login_error'))
                    <div class="alert alert-danger">
                        <span>{{ $message }}</span>
                    </div>
                @endif
                
                <div class="form-group">
                    <input id="username" type="text" class="form-control{{ $errors->has('username') ? ' is-invalid' : '' }}" name="username" placeholder="benchanyamada" value="{{ old('username') }}" required autofocus>

                    @if ($errors->has('username'))
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $errors->first('username') }}</strong>
                        </span>
                    @endif
                </div>
                <div class="form-group">
                    <input id="password" type="password" class="form-control{{ $errors->has('password') ? ' is-invalid' : '' }}" name="password" placeholder="Password" required>

                    @if ($errors->has('password'))
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $errors->first('password') }}</strong>
                        </span>
                    @endif
                </div>
                <button type="submit" class="btn btn-primary block full-width m-b">{{ __('messages.login') }}</button>

                @if(!$linking_line)
                    @if(\App\Settings::get_value('use_login_with_line'))
                        <a class="btn btn-block" href="{{ route('login.line') }}" style="background: #5bba25;color: #fdfefd;">{{ __('messages.log-in-with-line-account') }}</a>
                    @endif
                @endif

                {{-- <a href="{{ route('signup.create') }}" class="btn btn-danger block full-width m-t">{{ __('messages.signup') }}</a> --}}
                
                <div class="pull-right text-right mt-1">
                    <a class="mb-4" href="{{ route('forgot-password') }}">{{ __('messages.forgot-password') }} ?</a>
                </div>
                <div class="pull-left ">
                    @if(!$linking_line)
                        @if(\App\Settings::get_value('use_line_messaging_api'))
                            <div class="mt-2">
                                {!! \App\Settings::get_value('line_add_friend_button_html') !!}
                            </div>
                        @endif
                    @endif
                </div>
                <div class="clearfix"></div>
            </form>
        </div>
    </div>
@endsection

