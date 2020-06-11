@extends('layouts.app')

@section('content')
    <div class="row justify-content-center">
        @include('partials.success')
        @include('partials.error')
        <div class="col-12">
            <h1>{{ __('messages.lessonlist') }}</h1>
            <table class="table table-hover data-table order-column">
        	@if(!$lessons->isEmpty())
                <thead>
                    <tr>
                        <th>{{ __('messages.title') }}</th>
                        <th>{{ __('messages.course') }}</th>
                        <th>{{ __('messages.unit') }}</th>
                        <th>{{ __('messages.thumbnail') }}</th>
                        <th width="20%">{{ __('messages.description') }}</th>
                        <th width="20%">{{ __('messages.objectives') }}</th>
                        <th width="30%">{{ __('messages.fulltext') }}</th>
                        <th>{{ __('messages.edit') }}</th>
                        <th>{{ __('messages.delete') }}</th>
                    </tr>
                </thead>
                <tbody>
        		@foreach($lessons as $lesson)
        			<tr>
                        <td><a href="{{ url('/lesson/details/'.$lesson->id) }}">{{$lesson->title}}</a></td>
                        <td>{{$lesson->course->title}}</td>
                        <td>{{$lesson->unit->name}}</td>
                        <td>
                            @if($lesson->thumbnail)
                            {!! $lesson->the_image() !!}
                            @endif
                        </td>
                        <td>{{$lesson->description}}</td>
                        <td>{{$lesson->objectives}}</td>
                        <td>{{$lesson->full_text}}</td>
                        <td><a href="{{ url('/lesson/edit/'.$lesson->id) }}" class="btn btn-success">{{ __('messages.edit') }}</a></td>
                        <td>
                            <form class="delete" method="POST" action="{{ route('lesson.destroy', $lesson->id) }}">
                                @csrf
                                @method('DELETE')
                                <button class="btn btn-danger" type="submit">{{ __('messages.delete') }}</button>
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
