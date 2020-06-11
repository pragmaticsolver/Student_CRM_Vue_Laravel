@extends('layouts.app')

@section('content')
    <div class="row justify-content-center">
        <div class="col-lg-10">
            <h1>{{ __('messages.eventdetails') }}</h1>
        </div>
        <div class="col-lg-2">
            @can('event-edit')
            <a href="{{ url('/event/'.$event->id.'/edit') }}" class="btn btn-warning">{{ __('messages.edit') }}</a>
            @endcan
            @can('event-delete')
            <form class="delete" method="POST" action="{{ route('event.destroy', $event->id) }}">
                @csrf
                @method('DELETE')
                <button class="btn btn-danger" type="submit">{{ __('messages.delete') }}</button>
            </form>
                @endcan
        </div>
        <table class="table table-hover">
            <thead>
                <th>{{ __('messages.eventname') }}</th>
                <th>{{ __('messages.description') }}</th>
                <th>{{ __('messages.eventtime') }}</th>
                <th>{{ __('messages.date') }}</th>
            </thead>
            <tbody>
                <td>{{ $event->title }}</td>
                <td>{{ $event->description }}</td>
                <td>
                    @if($schedule->type == '3')
                        {{ $schedule->start_time }} - {{ $schedule->end_time }}
                    @else
                        {{ __('messages.allday') }}
                    @endif
                </td>
                <td>{{ $schedule->date }}</td>
            </tbody>
        </table>
    </div>
@endsection
