@extends('layouts.app')

@section('content')
    <div class="row justify-content-center">
        <div class="col-12 mt-2">
            <div class="row">
                <div class="col-6">
                    <h1>{{ __('messages.todo-progress') }}</h1>
                </div>
            </div>
        </div>
        <div class="col-12">
            @include('partials.success')
            @include('partials.error')
            <table class="table table-hover">
                <tbody>
                    @if(count($records) > 0)
                        <tr>
                            <th>{{ __('messages.student') }} / {{ __('messages.user') }}</th>
                            <th>{{ __('messages.progress') }}</th>
                        </tr>
                        @foreach($records as $record)
                            <tr>
                                <td>
                                    @if(isset($record->student))
                                        <a href="{{ url('/student/'.$record->student->id) }}" data-toggle="popover" data-placement="right" data-img="{{ $record->student->image ? $record->student->getImageUrl() : '' }}">{{ $record->student->getFullNameAttribute() }}</a>
                                    @else
                                        {{ @$record->user->name }}
                                    @endif
                                </td>
                                <td>{{ $record->progress_percentage }}%
                                    ({{ __('messages.completed-:done_taks-of-:total_tasks-tasks-from-:no_of_todos-todos', ['done_taks' => $record->done_tasks,
                                        'total_tasks' => $record->total_tasks,
                                        'no_of_todos' => $record->assigned_todos
                                    ] ) }})
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
