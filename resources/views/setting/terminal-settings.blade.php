@extends('layouts.app')

@section('content')
	<div class="row justify-content-center">
		<div class="col-12">
			<h1>{{ __('messages.terminal-settings') }}</h1>
			@if(session()->get('success'))
                <div class="alert alert-success">
                    {{ session()->get('success') }}
                </div><br/>
            @endif
	        @if($errors->any())
	            <div class="alert alert-danger">
	              <ul>
	                  @foreach($errors->all() as $error)
	                      <li>{{ $error }}</li>
	                  @endforeach
	              </ul>
	            </div><br/>
            @endif
            @include('partials.error')
			<form method="POST" action="{{ route('terminal-settings.update') }}" enctype="multipart/form-data">
                @csrf
                <div class="form-group row">
                    <label class="col-lg-3 col-form-label">{{ __('messages.terminal-checkin') }}</label>
                    <div class="col-lg-3">
                        <input name="terminal_checkin" data-toggle="toggle" type="checkbox" {{ old('terminal_checkin',$terminal_checkin) ? 'checked' : '' }}>
                    </div>
                </div>
                <div class="form-group row">
                    <label class="col-lg-3 col-form-label">{{ __('messages.terminal-checkout') }}</label>
                    <div class="col-lg-3">
                        <input name="terminal_checkout" data-toggle="toggle" type="checkbox" {{ old('terminal_checkout',$terminal_checkout) ? 'checked' : '' }}>
                    </div>
                </div>
                <div class="form-group row">
                    <label class="col-lg-3 col-form-label">{{ __('messages.terminal-reservation') }}</label>
                    <div class="col-lg-3">
                        <input name="terminal_reservation" data-toggle="toggle" type="checkbox" {{ old('terminal_reservation',$terminal_reservation) ? 'checked' : '' }}>
                    </div>
                </div>
                <div class="form-group row">
                    <label class="col-lg-3 col-form-label">{{ __('messages.terminal-checkout_book') }}</label>
                    <div class="col-lg-3">
                        <input name="terminal_checkout_book" data-toggle="toggle" type="checkbox" {{ old('terminal_checkout_book',$terminal_checkout_book) ? 'checked' : '' }}>
                    </div>
                </div>
              	
                <div class="form-group row">
                    <label class="col-lg-3 col-form-label"></label>
                    <div class="col-lg-3">
                        <input name="edit" type="submit" value="{{ __('messages.edit') }}" class="btn btn-success form-control">
                    </div>
                </div>
            </form>
		</div>
	</div>
@endsection
@push('scripts')
<script src="//ajax.googleapis.com/ajax/libs/jquery/2.1.3/jquery.min.js"></script>
<link href="//gitcdn.github.io/bootstrap-toggle/2.2.2/css/bootstrap-toggle.min.css" rel="stylesheet">
<script src="//gitcdn.github.io/bootstrap-toggle/2.2.2/js/bootstrap-toggle.min.js"></script>
@endpush