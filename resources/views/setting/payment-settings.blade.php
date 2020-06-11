@extends('layouts.app')

@section('content')
	<div class="row justify-content-center">
		<div class="col-12">
			<h1>{{ __('messages.payment-settings') }}</h1>
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
			<form method="POST" action="{{ route('payment-settings.update') }}" enctype="multipart/form-data">
                @csrf

                <div class="form-group row">
                    <label class="col-lg-2 col-form-label">{{ __('messages.payment-categories') }}:</label>
                    <div class="col-lg-10">
                        <input type="text" name="payment_categories" value="{{ $payment_categories }}" class="level-selectize">
                    </div>
                </div>

                <div class="form-group row">
                    <label class="col-lg-2 col-form-label">{{ __('messages.payment-methods') }}:</label>
                    <div class="col-lg-10">
                        <input type="text" name="payment_methods" value="{{ $payment_methods }}" class="level-selectize">
                    </div>
                </div>

                <div class="form-group row">
					<label class="col-lg-2 col-form-label">{{ __('messages.generate-payment-info-for') }}:</label>
					<div class="col-lg-10">
						<select name="generate_payment_info_for_roles[]" id="generate_payment_info_for_roles" class="form-control" multiple="multiple" aria-describedby="generate_payment_info_for_roles_desc">    
                            @foreach($roles as $role)
                                <option value="{{ $role->id }}" 
                                    {{ in_array($role->id, $generate_payment_info_for_roles) ? 'selected' : '' }}
                                    >{{ $role->name }}</option>
                            @endforeach
                        </select>
                        <small id="generate_payment_info_for_roles_desc" class="form-text text-muted">
                           {{ __('messages.please-select-student-roles-for-which-you-want-to-generate-batch-wise-payment-records')  }}.
                        </small>
					</div>
                </div>
                <hr>
                <div class="form-group row">
	            	<label class="col-lg-2 col-form-label"></label>
	            	<div class="col-lg-10">
                        <label><input type="checkbox" id="use_stripe" name="use_stripe" {{ $use_stripe == 1 ? 'checked' : '' }} >{{ __('messages.use-stripe') }}</label>
	            	</div>
                </div>

                <div id="stripe_fields" style="{{ $use_stripe == 0 ? 'display:none;' : '' }}">
                    <div class="form-group row">
                        <label class="col-lg-2 col-form-label">{{ __('messages.stripe-publishable-key') }}:</label>
                        <div class="col-lg-10">
                            <input type="text" name="stripe_publishable_key" id="stripe_publishable_key" class="form-control required {{ $errors->has('stripe_publishable_key') ? ' is-invalid' : '' }}" value="{{ old('stripe_publishable_key',$stripe_publishable_key) }}" autocomplete="off">
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-lg-2 col-form-label">{{ __('messages.stripe-secret-key') }}:</label>
                        <div class="col-lg-10">
                            <input type="text" name="stripe_secret_key" id="stripe_secret_key" class="form-control required {{ $errors->has('stripe_secret_key') ? ' is-invalid' : '' }}" value="{{ old('stripe_secret_key',$stripe_secret_key) }}" autocomplete="off">
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-lg-2 col-form-label">{{ __('messages.stripe-webhook-setup') }}:</label>
                        <div class="col-lg-10">
                                <p>{{ __('messages.set-following-url-as-stripe-webhook-url-and-subscribe-to-below-mentioned-events-on-stripe') }}<br>
                                {{ __('messages.url') }}: <b><em>{{ route('api.stripe.webhook') }}</em></b><br>
                                {{ __('messages.events-to-subscribe') }}: <b><em>invoice.payment_succeeded</em></b>
                            </p>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-lg-2 col-form-label">{{ __('messages.stripe-webhook-signing-secret-key') }}:</label>
                        <div class="col-lg-10">
                            <input type="text" name="stripe_webhook_signing_secret_key" id="stripe_webhook_signing_secret_key" class="form-control required {{ $errors->has('stripe_webhook_signing_secret_key') ? ' is-invalid' : '' }}" value="{{ old('stripe_webhook_signing_secret_key',$stripe_webhook_signing_secret_key) }}" autocomplete="off">
                        </div>
                    </div>
                </div>   

                <div class="form-group row">
                    <label class="col-lg-2 col-form-label"></label>
                    <div class="col-lg-10">
                        <input name="edit" type="submit" value="{{ __('messages.save') }}" class="btn btn-success form-control">
                    </div>
                </div>
            </form>
		</div>
	</div>
@endsection

@push('scripts')
<script>
	window.addEventListener('DOMContentLoaded', function() {
        (function($) {
            $('#generate_payment_info_for_roles').select2({ width: '100%'  });
            showHideStripeFields();
            $('#use_stripe').change(function(){
                showHideStripeFields();
            });

        })(jQuery);
    });
    
    function showHideStripeFields()
    {
        if($('#use_stripe').is(':checked'))
        {
            $('#stripe_fields').show();
            $('#stripe_fields .required').attr('required',true);
        }
        else
        {
            $('#stripe_fields').hide();
            $('#stripe_fields .required').removeAttr('required');
        }
    }
</script>
@endpush