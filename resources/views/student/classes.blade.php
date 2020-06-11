@extends('layouts.app')

@section('content')
    <div class="row justify-content-center">
        <div class="col-12 mt-2">
            <div class="row">
                <div class="col-6">
                    <h1>{{ __('messages.classes') }}</h1>
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
                    @if(!$schedules->isEmpty())
                        <tr>
                            <th>{{ __('messages.class') }}</th>
                            <th>{{ __('messages.actions') }}</th>
                        </tr>
                        @foreach($schedules as $schedule)
                            <tr>
                                <td>{{ $schedule->class->title }}</td>
                                <td>
                                    @can('st-class-details')
                                        <a class="btn btn-primary" href="{{ route('student.class_details', $schedule->id) }}">{{ __('messages.view-details') }}</a>
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

