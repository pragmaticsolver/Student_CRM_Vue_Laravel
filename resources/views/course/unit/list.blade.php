@extends('layouts.app')

@section('content')
    <div class="row justify-content-center">
        @include('partials.success')
        @include('partials.error')
        <div class="col-12">
            <h1>{{ __('messages.unitlist') }}</h1>
            <table class="table table-hover data-table order-column">
        	@if(!$units->isEmpty())
                <thead>
                    <tr>
                        <th>{{ __('messages.name') }}</th>
                        <th>{{ __('messages.course') }}</th>
                        <th>{{ __('messages.objectives') }}</th>
                        <th>{{ __('messages.edit') }}</th>
                        <th>{{ __('messages.delete') }}</th>
                    </tr>
                </thead>
                <tbody>
        		@foreach($units as $unit)
        			<tr>
                        <td><a href="{{ url('/unit/details/'.$unit->id) }}">{{$unit->name}}</a></td>
                        <td>{{$unit->course->title}}</td>
                        <td>{{$unit->objectives}}</td>
                        <td><a href="{{ url('/unit/edit/'.$unit->id) }}" class="btn btn-success">{{ __('messages.edit') }}</a></td>
                        <td>
                            <form class="delete" method="POST" action="{{ route('unit.destroy', $unit->id) }}">
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
