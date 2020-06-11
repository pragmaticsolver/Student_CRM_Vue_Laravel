@extends('layouts.app')

@section('content')
    <div class="row justify-content-center">
        <form action="" id="filter_form">
            <input type="hidden" name="sort_field" id="sort_field" value="" >
            <input type="hidden" name="sort_dir" id="sort_dir" value="" >
        </form>
        <div class="col-12 mt-2">
            <div class="row">
                <div class="col-6">
                    <h1>{{ __('messages.children') }}</h1>
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
            <table class="table table-hover">
                <tbody>
                    @if(!$children->isEmpty())
                        <tr>
                            <th data-collumn_name="fullname" class="collumn_sort">{{ __('messages.name') }}</th>
                            <th>{{ __('messages.actions') }}</th>
                        </tr>
                        @foreach($children as $student)
                            <tr>
                                <td>{{ $student->fullname }}</td>
                                <td>
                                    @can('student-impersonate')
                                        <a class="btn btn-primary" href="{{ route('student.start_impersonate', $student->user_id) }}">{{ __('messages.impersonate') }}</a>
                                    @endcan
                                </td>
                            </tr>
                        @endforeach
                    @else
                        <p class="text-center">{{ __('messages.no-records-found') }}</p>
                    @endif
                </tbody>
            </table>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        var _sort_field = "{{ @$filter['sort_field'] }}";
        var _sort_dir = "{{ @$filter['sort_dir'] }}";

        window.addEventListener('DOMContentLoaded', function() {
            (function($) {
                $(".collumn_sort[data-collumn_name='"+_sort_field+"']").addClass(_sort_dir);
                $('.collumn_sort').click(function(){
                    sort_dir = $(this).hasClass('asc') ? 'desc' : 'asc';
                    $('#filter_form #sort_field').val($(this).data('collumn_name'));
                    $('#filter_form #sort_dir').val(sort_dir);
                    $('#filter_form').submit();
                });
            })(jQuery);
        });
    </script>
@endpush
