@extends('layouts.app')

@section('content')
    <div class="row justify-content-center">
        <div class="col-12">
        	@if($errors->any())
		      	<div class="alert alert-danger">
		        	<ul>
		            	@foreach($errors->all() as $error)
		              		<li>{{ $error }}</li>
		            	@endforeach
		        	</ul>
		      	</div><br/>
		    @endif
	        <form method="POST" id="monthlyPaymentForm" action="{{ route('payment.monthly.store', $customer_id) }}">
	        	@csrf
				<h1>{{ __('messages.add-payment') }}</h1>
				<div class="form-group row">
					<label class="col-lg-2 col-form-label">{{ __('messages.payment-type') }}:</label>
					<div class="col-lg-10 pt-2">
						<label><input type="radio" name="payment_type" value="monthly" checked>{{ __('messages.monthly') }}</label>
						<label><input type="radio" name="payment_type" value="oneoff">{{ __('messages.other') }}</label>
					</div>
                </div>
                <div id="payment-category-section" class="form-group row form-section">
                    <label class="col-lg-2 col-form-label">{{ __('messages.payment-category') }}:</label>
                    <div class="col-lg-10">
                        <select name="payment_category" id="payment_category" class="form-control required {{ $errors->has('payment_category') ? ' is-invalid' : '' }}" >
                            <option value="">{{ __('messages.select-payment-category') }}</option>
                            @if($payment_categories)
                                @foreach($payment_categories as $cat)
                                    <option value="{{ $cat }}">{{ $cat }}</option>
                                @endforeach
                            @endif
                        </select>
                    </div>
                </div>
                <div id="rest-month-section" class="form-group row form-section">
					<label class="col-lg-2 col-form-label"></label>
					<div class="col-lg-10 pt-2">
						<label><input type="checkbox" name="rest_month" value="1" >{{ __('messages.is-rest-month') }}</label>
					</div>
				</div>
	          	<div id="payment-price-section" class="form-group row form-section">
	            	<label class="col-lg-2 col-form-label">{{ __('messages.paymentprice') }}:</label>
	            	<div class="col-lg-10">
	              		<input name="price" type="number" class="form-control required {{ $errors->has('price') ? ' is-invalid' : '' }}" value="{{ old('price') }}">
	            	</div>
	         	</div>
                <div id="payment-period-section" class="form-group row form-section">
                    <label class="col-lg-2 col-form-label">{{ __('messages.paymentperiod') }}:</label>
                    <div class="col-lg-10">
                            <input name="period" type="month" class="form-control required {{ $errors->has('period') ? ' is-invalid' : '' }}" value="{{ \Carbon\carbon::now(\App\Helpers\CommonHelper::getSchoolTimezone())->format('Y-m') }}" >
                    </div>
                </div>
                <div id="number-of-lessons-section" class="form-group row form-section">
                    <label class="col-lg-2 col-form-label">{{ __('messages.paymentnumberlesson') }}:</label>
                    <div class="col-lg-10">
                        <input name="number_of_lessons" type="number" class="form-control required {{ $errors->has('number_of_lessons') ? ' is-invalid' : '' }}" value="{{ old('number_of_lessons') }}">
                    </div>
                </div>
	         	<div id="payment-memo-section" class="form-group row form-section">
	            	<label class="col-lg-2 col-form-label">{{ __('messages.paymentmemo') }}:</label>
	            	<div class="col-lg-10">
	              		<textarea name="memo" class="form-control{{ $errors->has('memo') ? ' is-invalid' : '' }}">{{ old('memo') }}</textarea>
	            	</div>
                </div>
                <div id="payment-method-section" class="form-group row form-section">
	            	<label class="col-lg-2 col-form-label">{{ __('messages.paymentmethod') }}:</label>
	            	<div class="col-lg-10">
                        <select name="payment_method" id="payment_method" class="form-control" {{ $errors->has('payment_method') ? ' is-invalid' : '' }} required>
                            @if($payment_methods)
                                @foreach($payment_methods as $payment_method)
                                    <option value="{{ strtolower($payment_method) }}" {{ old('payment_method', $paymentSettings->payment_method) == strtolower($payment_method) ? 'selected' : '' }}>{{ $payment_method }}</option>
                                @endforeach
                            @endif
                        </select>
	            	</div>
                </div>
	          	<div class="form-group row">
	            	<label class="col-lg-2 col-form-label"></label>
	            	<div class="col-lg-10">
	              		<input name="add" type="submit" value="{{ __('messages.add-payment') }}" class="form-control btn-success">
	            	</div>
	          	</div>
	        </form>
      	</div>
    </div>
@endsection

@push('scripts')
<script>
    window.addEventListener('DOMContentLoaded', function() {
        showHidePaymentFormFields();

        $('input[name="payment_type"], input[name="rest_month"], #payment_method').change(function() {
            showHidePaymentFormFields();
        });

        function showHidePaymentFormFields()
        {
            $('.form-section').hide();
            var payment_type = $('#monthlyPaymentForm input[name="payment_type"]:checked').val();
            var is_rest_month = $('input[name="rest_month"]').is(':checked');
            if(payment_type == 'oneoff')
            {
                $('#payment-price-section,#payment-category-section,#payment-method-section,#payment-memo-section').show();
            }
            else
            {
                $('#rest-month-section,#payment-period-section').show();
                if(!is_rest_month)
                {
                    $('#payment-price-section,#number-of-lessons-section,#payment-method-section,#payment-memo-section').show();
                }
            }

            var payment_method = $('#monthlyPaymentForm #payment_method').val()
            if(payment_method != 'stripe' && (payment_type == 'oneoff' || (payment_type == 'monthly' && !is_rest_month)))
            {
                $('#payment-date-section').show();
            }
            else
            {
                $('#payment-date-section').hide();
            }

            if(!is_rest_month)
            {
                $('#payment-status-section').show();
            }
            else
            {
                $('#payment-status-section').hide();
            }

            $('.form-section').each(function(){
                if($(this).css('display') == 'none')
                {
                    $(this).find('.required').removeAttr('required');
                }
                else
                {
                    $(this).find('.required').attr('required', true);
                }
            });
        }
    });
</script>
@endpush
