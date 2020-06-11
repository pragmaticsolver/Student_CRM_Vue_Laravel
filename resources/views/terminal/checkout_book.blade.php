@extends('layouts.app')

@section('content')
    <h1>{{ __('messages.checkout-book')}}</h1>    
    <div class="container text-center">
        @include('partials.error')
       <form action="{{ route('terminal.checkout_book_submit') }}" method="POST" autocomplete="off">
            @csrf
            <div class="form-group" id="rfid-section" style="display:none;">
                <label for="">{{ __('messages.scan-rfid')}}</label>
                <input type="text" name="rfid_token" class="form-control required"  value="{{ old('rfid_token') }}">
            </div>
            <div class="form-group" id="barcode-section" style="display:none;">
                <label for="">{{__('messages.scan-book')}}</label>
                <input type="text" name="barcode" class="form-control" value="{{ old('barcode') }}">
            </div>
            <input type="submit"  style="display:none;">
       </form>
    </div>
    <script type="text/javascript">
        window.rfid_valid = "{{ session()->get('rfid_valid') }}"
    </script>    
@endsection