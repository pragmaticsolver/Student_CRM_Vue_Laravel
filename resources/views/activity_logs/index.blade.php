@extends('layouts.app')

@section('content')
    <div class="row justify-content-center">
        <div class="col-12 mt-2">
            <div class="row">
                <div class="col-6">
                    <h1>{{ __('messages.activity-logs') }}</h1>
                </div>
            </div>
        </div>
    </div>
    <div class="col-12">
        @include('partials.success')
        @include('partials.error')
        <div class="row mb-2">
            <div class="col-lg-3">
                <label>{{ __('messages.activity-time') }}</label>
                <input class="form-control" type="text" name="dates_filer" value="">
            </div>
            <div class="col-lg-3">
                <label>{{ __('messages.activity-by') }}</label>
                <select id="user_id" class="form-control">
                    <option value="all">{{ __('messages.all') }}</option>
                    @foreach($users as $user)
                        <option value="{{ $user->id }}">{{ $user->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-lg-3">
                <label>{{ __('messages.activity') }}</label>
                <select id="activity_id" class="form-control">
                    <option value="all">{{ __('messages.all') }}</option>
                    @foreach($activities as $activity)
                        <option value="{{ $activity->id }}">{{ $activity->get_display_name() }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-lg-2">
                <button id="filter_btn" class="btn btn-primary mt-4" type="button">
                    {{ __('messages.filter') }}
                    <span class="preloader" style="display:none;">&nbsp<i class="fa fa-spinner fa-spin"></i></span>
                </button>
            </div>
        </div>
        <div id="activity_data">
            {{-- Dynamic Data via ajax --}}
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        var _sort_field = "id";
        var _sort_dir = "desc";
        var from_date = '{{ $from_date }}';
        var to_date = '{{ $to_date }}';
        window.addEventListener('DOMContentLoaded', function() {
            $(document).on('click','.collumn_sort',function(){
                _sort_field = $(this).data('collumn_name');
                _sort_dir = $(this).hasClass('asc') ? 'desc' : 'asc';
                get_data();
            });

            $('input[name="dates_filer"]').daterangepicker({
                startDate: from_date,
                endDate: to_date,
                ranges: {
                    'Today': [ "{{ (clone $now)->format('Y-m-d') }}", "{{ (clone $now)->format('Y-m-d') }}"],
                    'Yesterday': [ "{{ (clone $now)->subDay()->format('Y-m-d') }}", "{{ (clone $now)->subDay()->format('Y-m-d') }}"],
                    'Last 7 Days': ["{{ (clone $now)->subDays(6)->format('Y-m-d') }}", "{{ (clone $now)->format('Y-m-d') }}"],
                    'Last 30 Days': ["{{ (clone $now)->subDays(29)->format('Y-m-d') }}", "{{ (clone $now)->format('Y-m-d') }}"],
                    'This Month': ["{{ (clone $now)->firstOfMonth()->format('Y-m-d') }}", "{{ (clone $now)->endOfMonth()->format('Y-m-d') }}"],
                    'Last Month': ["{{ (clone $now)->subMonth()->firstOfMonth()->format('Y-m-d') }}", "{{ (clone $now)->subMonth()->endOfMonth()->format('Y-m-d') }}"]
                },
                locale: {
                    format: 'Y-MM-DD'
                },
                alwaysShowCalendars: true,
                autoApply: true,
                maxDate: '{{ $max_date }}'
            },
            function(start, end, label) {
                from_date = start.format('YYYY-MM-DD');
                to_date = end.format('YYYY-MM-DD');
            });

            $('#filter_btn').click(function(){
                get_data();
            });

            $('#user_id,#activity_id').select2({ width: '100%'  });
            get_data();
        });
        function get_data()
        {
            $('#activity_data').html('<div class="text-center" style="font-size:20px;"><i class="fa fa-spinner fa-spin"></i></div>');
            $('#filter_btn').attr('disabled',true).find('.preloader').show();
            data = {
                from_date: from_date,
                to_date: to_date,
                user_id: $('#user_id').val(),
                activity_id: $('#activity_id').val(),
                sort_field: _sort_field,
                sort_dir: _sort_dir
            };
            $.ajax({
                url: "{{ route('activity_logs.data') }}",
                type: "GET",
                data: data,
                success: function(response){
                    $('#activity_data').html(response);
                    $(".collumn_sort[data-collumn_name='"+_sort_field+"']").addClass(_sort_dir);
                    $('#filter_btn').removeAttr('disabled').find('.preloader').hide();

                },
                error: function()
                {
                    Swal.fire({
                        text: "{{ __('messages.something-went-wrong')}}",
                        icon: 'warning',
                        showCancelButton: false,
                        confirmButtonColor: '#3085d6',
                        cancelButtonColor: '#d33',
                    });
                    $('#filter_btn').removeAttr('disabled').find('.preloader').hide();
                }
            });
        }
    </script>

@endpush
