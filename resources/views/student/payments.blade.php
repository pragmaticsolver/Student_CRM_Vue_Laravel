@extends('layouts.app')

@section('content')
    <div class="row justify-content-center">
        <div class="col-12 mt-2">
            <div class="row">
                <div class="col-6">
                    <h1>{{  __('messages.payments') }}</h1>
                </div>
            </div>
        </div>
        <div class="col-12">
            @if(session()->get('success'))
                <div class="alert alert-success">
                    {{ session()->get('success') }}
                </div><br/>
            @endif
            @include('partials.error')

            @if(!$payments->isEmpty())
                <table class='table table-hover'>
                    <tr>
                        <th>{{ __('messages.payment-for') }}</th>
                        <th>{{ __('messages.paymentamount')}}</th>
                        <th>{{ __('messages.paymentmemo')}}</th>
                        <th>{{ __('messages.payment-method') }}</th>
                        <th>{{ __('messages.payment-status') }}</th>
                        <th>{{ __('messages.payment-received-at') }}</th>
                        <th>{{ __('messages.actions') }}</th>
                    </tr>
                    @foreach($payments as $payment)
                        <tr class="{{ $payment->rest_month ? 'rest-month-row' : '' }}">
                            <td>
                                @if($payment->number_of_lessons)
                                    {{ $payment->number_of_lessons.' Lesson(s) ( '. \Carbon\carbon::createFromFormat('Y-m', $payment->period)->format('F Y') .' )' }}    
                                @else
                                    {{ $payment->payment_category }}
                                @endif
                            </td>
                            <td>{{$payment->price}}</td>
                            <td>{{$payment->memo}}</td>
                            <td>{{ $payment->display_payment_method }}</td>
                            <td>{{ $payment->display_status }}</td>
                            <td>{{ $payment->localPaymentRecievedAt() }}</td>
                            <td>
                                @if($payment->stripe_invoice_url)
                                    <a href="{{ $payment->stripe_invoice_url }}" class="btn btn-sm btn-info my-1" target="_blank">{{ __('messages.view-stripe-invoice') }}</a>
                                    <button type="button" class="btn btn-sm btn-primary btn my-1" onclick="copyToClipboard('{{ $payment->stripe_invoice_url }}', this)">{{ __('messages.copy-stripe-invoice-url') }}</button>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </table>
            @else
                <p class="text-center">{{ __('messages.no-records-found') }}</p>
            @endif
        </div>
    </div>
@endsection