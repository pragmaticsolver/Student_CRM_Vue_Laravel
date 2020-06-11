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
                  </div>
                  <br/>
            @endif
            
            @if(session()->get('error'))
                <div class="alert alert-danger">
                    {{ session()->get('error') }}
                </div><br/>
            @endif
	        <form method="POST" id="monthlyPaymentForm" action="{{ route('payment.monthly.update', $payment->id) }}">
	        	@csrf
                <h1>{{ __('messages.edit-payment') }}</h1>

                @if($payment->payment_method == 'stripe')
                    <div class="alert alert-warning">
                        <span class="fa fa-exclamation-triangle"></span>
                        {{ __('messages.details-you-update-here-will-not-be-reflected-in-stripe-invoice-if-stripe-invoice-is-already-generated-and-sent-to-customer') }}
                    </div>
                @endif
                @if($payment->rest_month)
                    <div class="form-group row form-section">
                        <label class="col-lg-2 col-form-label">{{ __('messages.paymentperiod') }}:</label>
                        <div class="col-lg-10">
                            <input name="period" type="month" class="form-control required {{ $errors->has('period') ? ' is-invalid' : '' }}" value="{{ $payment->period }}" >
                        </div>
                    </div>
                @elseif($payment->isOneOffPayment())
                    <div class="form-group row form-section">
                        <label class="col-lg-2 col-form-label">{{ __('messages.payment-category') }}:</label>
                        <div class="col-lg-10">
                            <select name="payment_category" id="payment_category" class="form-control required {{ $errors->has('payment_category') ? ' is-invalid' : '' }}" >
                                <option value="">{{ __('messages.select-payment-category') }}</option>
                                @if($payment_categories)
                                    @foreach($payment_categories as $cat)
                                        <option value="{{ $cat }}" {{ $payment->payment_category == $cat ? 'selected' : ''}}>{{ $cat }}</option>
                                    @endforeach
                                @endif
                            </select>
                        </div>
                    </div>
                    <div class="form-group row form-section">
                        <label class="col-lg-2 col-form-label">{{ __('messages.paymentprice') }}:</label>
                        <div class="col-lg-10">
                            <input name="price" type="number" class="form-control required {{ $errors->has('price') ? ' is-invalid' : '' }}" value="{{ $payment->price }}">
                        </div>
                    </div>
                    <div class="form-group row form-section">
                        <label class="col-lg-2 col-form-label">{{ __('messages.paymentmemo') }}:</label>
                        <div class="col-lg-10">
                              <textarea name="memo" class="form-control{{ $errors->has('memo') ? ' is-invalid' : '' }}">{{ old('memo', $payment->memo) }}</textarea>
                        </div>
                    </div>
                    <div class="form-group row form-section">
                        <label class="col-lg-2 col-form-label">{{ __('messages.paymentmethod') }}:</label>
                        <div class="col-lg-10">
                            <select name="payment_method" id="payment_method" class="form-control" {{ $errors->has('payment_method') ? ' is-invalid' : '' }} required>
                                @if($payment_methods)
                                    @foreach($payment_methods as $payment_method)
                                        <option value="{{ strtolower($payment_method) }}" {{ old('payment_method', $payment->payment_method) == strtolower($payment_method) ? 'selected' : '' }}>{{ $payment_method }}</option>
                                    @endforeach
                                @endif
                            </select>
                        </div>
                    </div>
                @else
                    <div class="form-group row form-section">
                        <label class="col-lg-2 col-form-label">{{ __('messages.paymentprice') }}:</label>
                        <div class="col-lg-10">
                            <input name="price" type="number" class="form-control required {{ $errors->has('price') ? ' is-invalid' : '' }}" value="{{ $payment->price }}">
                        </div>
                    </div>
                    <div class="form-group row form-section">
                        <label class="col-lg-2 col-form-label">{{ __('messages.paymentperiod') }}:</label>
                        <div class="col-lg-10">
                            <input name="period" type="month" class="form-control required {{ $errors->has('period') ? ' is-invalid' : '' }}" value="{{ $payment->period }}" >
                        </div>
                    </div>
                    <div class="form-group row form-section">
                        <label class="col-lg-2 col-form-label">{{ __('messages.paymentnumberlesson') }}:</label>
                        <div class="col-lg-10">
                            <input name="number_of_lessons" type="number" class="form-control required {{ $errors->has('number_of_lessons') ? ' is-invalid' : '' }}" value="{{ $payment->number_of_lessons }}">
                        </div>
                    </div>
                    <div class="form-group row form-section">
                        <label class="col-lg-2 col-form-label">{{ __('messages.paymentmemo') }}:</label>
                        <div class="col-lg-10">
                              <textarea name="memo" class="form-control{{ $errors->has('memo') ? ' is-invalid' : '' }}">{{ old('memo', $payment->memo) }}</textarea>
                        </div>
                    </div>
                    <div class="form-group row form-section">
                        <label class="col-lg-2 col-form-label">{{ __('messages.paymentmethod') }}:</label>
                        <div class="col-lg-10">
                            <select name="payment_method" id="payment_method" class="form-control" {{ $errors->has('payment_method') ? ' is-invalid' : '' }} required>
                                @if($payment_methods)
                                    @foreach($payment_methods as $payment_method)
                                        <option value="{{ strtolower($payment_method) }}" {{ old('payment_method', $payment->payment_method) == strtolower($payment_method) ? 'selected' : '' }}>{{ $payment_method }}</option>
                                    @endforeach
                                @endif
                            </select>
                        </div>
                    </div>
                @endif
                <div class="form-group row">
	            	<label class="col-lg-2 col-form-label"></label>
	            	<div class="col-lg-10">
	              		<input name="add" type="submit" value="{{ __('messages.submit') }}" class="form-control btn-success">
	            	</div>
                </div>
	        </form>
      	</div>
    </div>
@endsection
