@extends('layouts.app')

@section('content')
    <div class="row justify-content-center">
        <div class="col-12 mt-2" id="vue-app">
            <app-manage-batch-payments initial_month_year="{{ $month_year }}"></app-manage-batch-payments>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="{{ asset('public'.mix('js/page/accounting/manage-monthly-payments.js')) }}"></script>
@endpush