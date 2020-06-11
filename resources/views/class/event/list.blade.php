@extends('layouts.app')

@section('content')
    <div class="row justify-content-center">
        <div class="col-12">
            <h1>{{ __('messages.eventlist') }}</h1>
            @if(session()->get('success'))
                <div class="alert alert-success">
                    {{ session()->get('success') }}
                </div><br/>
            @endif
            <table class="table table-hover">
                <tbody>
                @if(!$events->isEmpty())
                    <tr>
                        <th>{{ __('messages.eventname') }}</th>
                        <th>{{ __('messages.cost') }}</th>
                        <th>{{ __('messages.size') }}</th>
                        <th>{{ __('messages.category') }}</th>
                        <th>{{ __('messages.level') }}</th>
                        <th>{{ __('messages.edit') }}</th>
                        <th>{{ __('messages.delete') }}</th>
                    </tr>
                    @foreach($events as $event)
                        <tr>
                            <td><a href="{{ url('/event/'.$event->id) }}">{{$event->title}}</a></td>
                            <td>{{ $event->cost }}</td>
                            <td>{{ $event->size }}</td>
                            <td>{{ $event->category->name }}</td>
                            <td>{{ $event->level }}</td>
                            <td><a href="{{ url('/event/'.$event->id.'/edit') }}" class="btn btn-success">{{ __('messages.edit') }}</a></td>
                            <td>
                                <form class="delete" method="POST" action="{{ route('event.destroy', $event->id) }}">
                                    @csrf
                                    @method('DELETE')
                                    <button class="btn btn-danger" type="submit">{{ __('messages.delete') }}</button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                @else
                    {{ __('messages.emptyevent') }}
                @endif
                </tbody>
            </table>
        </div>
    </div>
@endsection
