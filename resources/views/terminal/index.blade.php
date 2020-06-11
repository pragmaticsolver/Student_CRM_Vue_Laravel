@extends('layouts.app')

@push('styles')
<style>
    .btn-terminal {
        min-height: 250px;
        /* min-width: 225px; */
        display: block;
        width:95%;
        margin: 5px;
        font-size: 20px;
        vertical-align: middle;
    }
    .btn-terminal.selected {
        border: 3px solid #f3f3f4;
        outline: 3px solid #045f4c;
        background-color: #045f4c;
    }
</style>
@endpush

@section('content')
    <div class="container-fluid text-center">
        @include('partials.success')
        @include('partials.error')
        <div class="row no-gutters d-flex justify-content-center">
            @if(\App\Settings::get_value('terminal_checkin'))
            <div class="col-sm-6 col-md-6 col-lg-3">
                <button id="checkin-btn" class="btn btn-primary btn-terminal" >
                    <p style="font-size:13px;" class="btn-active-text">{{ __('messages.scan-rfid-to')}}</p>
                    {{ __('messages.terminal-check-in')}}
                    <p id="checkin_token_text" style="font-size:13px; "></p>
                    <div id="checkin_spinner" class="fa fa-spinner fa-spin" style="display:none;"></div>
                </button>
            </div>
            @endif
            @if(\App\Settings::get_value('terminal_checkout'))
            <div class="col-sm-6 col-md-6 col-lg-3">
                <button id="checkout-btn" class="btn btn-primary btn-terminal" >
                    <p style="font-size:13px;" class="btn-active-text">{{ __('messages.scan-rfid-to')}}</p>
                    {{ __('messages.terminal-check-out')}}
                    <p id="checkout_token_text" style="font-size:13px;"></p>
                    <div id="checkout_spinner" class="fa fa-spinner fa-spin" style="display:none;"></div>
                </button>
            </div>
            @endif
            @if(\App\Settings::get_value('terminal_reservation'))
            <div class="col-sm-6 col-md-6 col-lg-3">
                <button class="btn btn-primary btn-terminal" onclick="window.location.href='{{ route('terminal.make_reservation') }}'">{{ __('messages.make-reservation')}}</button>
            </div>
            @endif
            @if(\App\Settings::get_value('terminal_checkout_book'))
            <div class="col-sm-6 col-md-6 col-lg-3">
                <button class="btn btn-primary btn-terminal" onclick="window.location.href='{{ route('terminal.checkout_book') }}'">{{ __('messages.checkout-book')}}</button>
            </div>
            @endif
        </div>
    </div>

    <form id="checkin_form" method="POST" autocomplete="off" onsubmit="return false;">
        @csrf
        <input type="hidden" name="rfid_token" class="form-control">
    </form>
    <form id="checkout_form" method="POST" autocomplete="off" onsubmit="return false;">
        @csrf
        <input type="hidden" name="rfid_token" class="form-control">
    </form>
@endsection

@push('scripts')
    <script>
        active_btn = '';
        generic_error_message = "{{ __('messages.something-went-wrong') }}";
        window.addEventListener('DOMContentLoaded', function() {
            $(document).on('keypress',function(e){
                if(e.keyCode == 13)
                {
                    if(active_btn == 'checkin-btn')
                    {
                        checkin_submit();
                    }
                    else if(active_btn == 'checkout-btn')
                    {
                        checkout_submit();
                    }
                }
                else
                {
                    if(active_btn == 'checkin-btn')
                    {
                        field = $('#checkin_form input[name="rfid_token"]');
                        new_token_value = field.val() + String.fromCharCode(e.keyCode);
                        field.val(new_token_value);
                        $('#checkin_token_text').text(new_token_value);
                    }
                    else if(active_btn == 'checkout-btn')
                    {
                        field = $('#checkout_form input[name="rfid_token"]');
                        new_token_value = field.val() + String.fromCharCode(e.keyCode);
                        field.val(new_token_value);
                        $('#checkout_token_text').text(new_token_value);
                    }
                }
            });

            $(document).on('click','.btn-terminal', function(){
                $('.btn-terminal').removeClass('selected');
                $('.btn-active-text').hide();
                active_btn = $(this).attr('id');
                $(this).addClass('selected');
                $(this).find('.btn-active-text').show();
            });

            // Make checking feature selected bydefault
            $('#checkin-btn').click();
        });
        function checkin_submit()
        {
            $('#checkin_spinner').show();
            $.ajax({
                url: "{{ route('terminal.checkin_submit') }}",
                type: 'POST',
                data: $('#checkin_form').serialize(),
                success: function(response){
                    if(response.status == 1)
                    {
                        Swal.fire({
                            title: "{{ __('messages.successfully-checkedin') }}",
                            html: response.message,
                            icon: 'success',
                            showCancelButton: false,
                            confirmButtonColor: '#3085d6',
                            timer: 2000,
                        });
                    }
                    else
                    {
                        Swal.fire({
                            text: response.message || generic_error_message,
                            icon: 'error',
                            showCancelButton: false,
                            confirmButtonColor: '#3085d6',
                            timer: 2000,
                        });
                    }
                    $('#checkin_form input[name="rfid_token"]').val("");
                    $('#checkin_token_text').text("");
                    $('#checkin_spinner').fadeOut();
                },
                error:function(e){
                    Swal.fire({
                        text: generic_error_message,
                        icon: 'error',
                        showCancelButton: false,
                        confirmButtonColor: '#3085d6',
                        timer: 2000,
                    });
                    $('#checkin_form input[name="rfid_token"]').val("");
                    $('#checkin_token_text').text("");
                    $('#checkin_spinner').fadeOut();
                }
            });
        }

        function checkout_submit()
        {
            $('#checkout_spinner').show();
            $.ajax({
                url: "{{ route('terminal.checkout_submit') }}",
                type: 'POST',
                data: $('#checkout_form').serialize(),
                success: function(response){
                    if(response.status == 1)
                    {
                        Swal.fire({
                            title: "{{ __('messages.successfully-checkedout') }}",
                            html: response.message,
                            icon: 'success',
                            showCancelButton: false,
                            confirmButtonColor: '#3085d6',
                            timer: 2000,
                        });
                    }
                    else
                    {
                        Swal.fire({
                            text: response.message || generic_error_message,
                            icon: 'error',
                            showCancelButton: false,
                            confirmButtonColor: '#3085d6',
                            timer: 2000,
                        });
                    }
                    $('#checkout_form input[name="rfid_token"]').val("");
                    $('#checkout_token_text').text("");
                    $('#checkout_spinner').fadeOut();
                },
                error:function(e){
                    Swal.fire({
                        text: generic_error_message,
                        icon: 'error',
                        showCancelButton: false,
                        confirmButtonColor: '#3085d6',
                        timer: 2000,
                    });
                    $('#checkout_form input[name="rfid_token"]').val("");
                    $('#checkout_token_text').text("");
                    $('#checkout_spinner').fadeOut();
                }
            });
        }
    </script>
@endpush
