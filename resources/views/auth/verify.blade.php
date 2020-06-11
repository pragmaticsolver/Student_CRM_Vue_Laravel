@extends('layouts.app-unverify')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">{{ __('Verify Your Email Address') }}</div>

                <div class="card-body">
                    @if (session('resent'))
                        <div class="alert alert-success" role="alert">
                            {{ __('A fresh verification link has been sent to your email address.') }}
                        </div>
                    @endif

                    <form class="d-inline" method="POST" action="{{ route('verification.resend') }}">
                        @csrf
                        
                        {{ __('Before proceeding, please ') }}
                        
                        <button type="submit" class="btn btn-link px-0 mx-0 align-baseline">
                            {{ __('click here to request a verification link') }}
                        </button>.
                    </form>

                    <p>{{ __('messages.verification-link-will-be-sent-on') }} <em>{{ Auth::user()->getEmailAddress() }}</em></p>

                    @if(Auth::user()->willUseParentEmail())
                        <p>{{ __('messages.parent-email') }} <em>{{ Auth::user()->getEmailAddress() }}</em> {{ __('messages.will-be-used-for-all-communications') }}</p>
                    @endif


                </div>
            </div>
        </div>
    </div>
</div>
@endsection
