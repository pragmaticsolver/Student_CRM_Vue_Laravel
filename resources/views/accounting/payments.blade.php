@extends('layouts.app')

@section('content')
    <div class="row justify-content-center">
        <div class="col-12 mt-2" id="vue-app">
            <app-payment-list 
                :filter="{{ json_encode($filter) }}"
            ></app-payment-list>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="{{ asset('public'.mix('js/page/accounting/payments.js')) }}"></script>
@endpush