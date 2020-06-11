@extends('layouts.guest')

@section('title', __('messages.reset-password'))

@section('content')
    <div class="middle-box text-center loginscreen animated fadeIn">
        <div>
            <div>
                <h1 class="logo-name">{{ __('messages.ut') }}</h1>
            </div>
            <h3>{{ __('messages.reset-password') }}</h3>
            @include('partials.success')
            @include('partials.error')
            @if($passwordRestToken)
                <form class="mt-3" role="form" method="post" action="{{ route('reset-password-submit') }}">
                    @csrf
                    <input type="hidden" name="token" value="{{ $passwordRestToken->token }}">

                    <div class="form-group">
                        <input id="username" type="text" class="form-control{{ $errors->has('username') ? ' is-invalid' : '' }}" name="username" placeholder="{{ __('messages.enter-username') }}" value="{{ old('username') }}" required autofocus>
                        @if ($errors->has('username'))
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $errors->first('username') }}</strong>
                            </span>
                        @endif
                    </div>

                    <div class="form-group">
                        <input id="password" type="password" class="form-control{{ $errors->has('password') ? ' is-invalid' : '' }}" name="password" placeholder="{{ __('messages.enter-new-password') }}" required>
                        @if ($errors->has('password'))
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $errors->first('password') }}</strong>
                            </span>
                        @endif
                    </div>

                    <div class="form-group">
                        <input id="confirm_password" type="password" class="form-control{{ $errors->has('confirm_password') ? ' is-invalid' : '' }}" name="confirm_password" placeholder="{{ __('messages.confirm-password') }}" required>
                        @if ($errors->has('confirm_password'))
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $errors->first('confirm_password') }}</strong>
                            </span>
                        @endif
                    </div>

                    <button type="submit" class="btn btn-primary block full-width m-b">{{ __('messages.submit') }}</button>
                    <div class="text-right">
                        <a href="{{ route('login') }}">{{ __('messages.back-to-login') }}</a>
                    </div>
                </form>
            @else
                <div class="alert alert-danger" role="alert">
                    {{ __('messages.password-rest-link-is-expired')  }}
                </div>
            @endif
        </div>
    </div>
@endsection

