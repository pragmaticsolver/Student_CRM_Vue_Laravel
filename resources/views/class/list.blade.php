@extends('layouts.app')

@section('content')
    <div class="row justify-content-center">
        <div class="col-12">
            <h1>{{ __('messages.classlist') }}</h1>
            @include('partials.success')
            @include('partials.error')
            <table class="table table-hover">
                <tbody>
                	@if(!$classes->isEmpty())
                        <tr>
                            <th>{{ __('messages.classname') }}</th>
                            <th>{{ __('messages.points') }}</th>
                            <th>{{ __('messages.size') }}</th>
                            <th>{{ __('messages.level') }}</th>
                            <th>{{ __('messages.category') }}</th>
                            <th>{{ __('messages.length') }}</th>
                            @can('class-edit')
                            <th>{{ __('messages.edit') }}</th>
                            @endcan
                            @can('class-delete')
                            <th>{{ __('messages.delete') }}</th>
                            @endcan
                        </tr>
                		@foreach($classes as $class)
                			<tr>
		                        <td><a href="{{ url('/class/'.$class->id) }}">{{$class->title}}</a></td>
		                        <td>@if($class->payment_plan) {{ $class->payment_plan->points }} @endif</td>
                                @if($class->size)
                                    <td>{{ $class->size }}</td>
                                @else
                                    <td>{{ $default_size }}</td>
                                @endif
                                <td>{{ $class->level }}</td>
                                <td>{{ $class->category->name }}</td>
                                <td>{{ $class->length }}</td>
		                        @can('class-edit')
		                        <td><a href="{{ url('/class/'.$class->id.'/edit') }}" class="btn btn-success">{{ __('messages.edit') }}</a><a></a></td>
                                @endcan
                                @can('class-delete')
                                <td>
                                    <form class="delete" method="POST" action="{{ route('class.destroy', $class->id) }}">
                                        @csrf
                                        @method('DELETE')
                                        <button class="btn btn-danger" type="submit">{{ __('messages.delete') }}</button>
                                    </form>
                                </td>
                                @endcan
		                    </tr>
                		@endforeach
                	@endif
                </tbody>
            </table>
        </div>
    </div>
@endsection
