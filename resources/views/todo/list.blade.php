@extends('layouts.app')

@section('content')
    <div class="row justify-content-center">
        <div class="col-12 mt-2">
            <div class="row">
                <div class="col-6">
                    <h1>{{ __('messages.todos') }}</h1>
                </div>
                @can('todo-create')
                    <div class="col-6 text-right">
                        <a class="btn btn-success" href="{{ route('todo.create') }}">{{ __('messages.add-todo') }}</a>
                    </div>
                @endif
            </div>
        </div>
        <div class="col-12">
            @include('partials.success')
            @include('partials.error')
            <table class="table table-hover">
                <tbody>
                    @if(!$todos->isEmpty())
                        <tr>
                            <th>{{ __('messages.title') }}</th>
                            <th>{{ __('messages.no-of-tasks') }}</th>
                            <th>{{ __('messages.no-of-assigned-users') }}</th>
                            <th>{{ __('messages.no-of-assigned-students') }}</th>
                            <th>{{ __('messages.due-days') }}</th>
                            <th>{{ __('messages.show-alert-before-days') }}</th>
                            <th>{{ __('messages.no-of-attachements') }}</th>
                            <th>{{ __('messages.actions') }}</th>
                        </tr>
                        @foreach($todos as $todo)
                            <tr>
                                <td>{{ $todo->title }}</td>
                                <td>{{ $todo->todoTasks->count() }}</td>
                                <td>{{ $todo->todoAccess()->forUsers()->count() }}</td>
                                <td>{{ $todo->todoAccess()->forStudents()->count() }}</td>
                                <td>{{ $todo->due_days }}</td>
                                <td>{{ $todo->start_alert_before_days ? $todo->start_alert_before_days : 0}}</td>
                                <td>{{ $todo->todoFiles()->count() }}</td>
                                <td>
                                    @can('todo-progress-details')
                                        <a class="btn btn-primary mb-1" href="{{ route('todo.progress_details', $todo->id) }}">{{ __('messages.todo-progress') }}</a>
                                    @endcan
                                    @can('todo-edit')
                                        <a class="btn btn-warning mb-1" href="{{ route('todo.edit', $todo->id) }}">{{ __('messages.edit') }}</a>
                                    @endcan
                                    @can('todo-delete')
                                        <form class="delete" method="POST" action="{{ route('todo.destroy', $todo->id) }}">
                                            @csrf
                                            <button class="btn btn-danger mb-1" type="submit">{{ __('messages.delete') }}</button>
                                        </form>
                                    @endcan
                                </td>
                            </tr>
                        @endforeach
                    @endif
                </tbody>
            </table>
        </div>
    </div>
@endsection
