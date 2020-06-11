@extends('layouts.app')

@section('content')
    <div class="row justify-content-center">
        @include('partials.success')
        @include('partials.error')
        <form action="" id="filter_form">
            <input type="hidden" name="sort_field" id="sort_field" value="" >
            <input type="hidden" name="sort_dir" id="sort_dir" value="" >
        </form>
        <div class="col-12">
            <h1>{{ __('messages.courselist') }}</h1>
            <table class="table table-hover">
        	@if(!$courses->isEmpty())
                <thead>
                    <tr>
                        <th data-collumn_name="title" class="collumn_sort">{{ __('messages.title') }}</th>
                        <th>{{ __('messages.thumbnail') }}</th>
                        <th>{{ __('messages.description') }}</th>
                        <th>{{ __('messages.objectives') }}</th>
                        <th>{{ __('messages.actions') }}</th>
                    </tr>
                </thead>
                <tbody>
        		@foreach($courses as $course)
        			<tr>
                        <td><a href="{{ url('/course/details/'.$course->id) }}">{{$course->title}}</a></td>
                        <td>
                            @if($course->thumbnail)
                            {!! $course->the_image() !!}
                            @endif
                        </td>
                        <td>{{ $course->description }}</td>
                        <td>{{ $course->objectives }}</td>
                        <td>
                            <a href="{{ url('/course/edit/'.$course->id) }}" class="btn btn-success btn-sm">{{ __('messages.edit') }}</a>
                            <form class="delete mb-0" method="POST" action="{{ route('course.destroy', $course->id) }}">
                                @csrf
                                @method('DELETE')
                                <button class="btn btn-danger btn-sm" type="submit">{{ __('messages.delete') }}</button>
                            </form>
                        </td>
                    </tr>
        		@endforeach
                </tbody>
        	@endif
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
