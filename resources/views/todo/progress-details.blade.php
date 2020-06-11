@extends('layouts.app')

@php
    $total_todo_tasks = $todo->todoTasks->count();
    $todoTasks = $todo->todoTasks;
@endphp

@section('content')
    <div class="row justify-content-center">
        <div class="col-12 mt-2">
            <div class="row">
                <div class="col-6">
                    <h1>{{ __('messages.todo-progress') }}</h1>
                    <h2>{{ $todo->title }}</h2>
                </div>
            </div>
        </div>
        <div class="col-12">
        	@include('partials.success')
            @include('partials.error')
            <div style="overflow-x:auto;">
                <table class="table table-hover" style="width:auto;">
                    <tr>
                        <th><div style="width:200px;">{{ __('messages.student') }} / {{  __('messages.user') }}</div></th>
                        <th><div style="width:100px;">{{ __('messages.progress') }}</div></th>
                        @foreach($todoTasks as $key => $todoTask)
                            <th class="text-center">
                                <div style="width:100px;">
                                    {{ $key + 1}}
                                    <span class="empty-class-filter-warning">
                                        <i class="fa fa-info-circle"
                                            data-toggle="tooltip"
                                            title="{{ $todoTask->task }}"
                                            data-placement="right"
                                            ></i>
                                    </span>
                                </div>
                            </th>
                        @endforeach
                    </tr>
                    @foreach($todoAccessList as $todoAccess)
                        @php
                            $done_tasks = $todoAccess->todoTaskStatus()->where('status',1)->count();
                            $_todo_task_status = $todoAccess->todoTaskStatusByTaskId();
                        @endphp
                        <tr>
                            <td>
                                @if($todoAccess->student_id != '')
                                    <a href="{{ url('/student/'.$todoAccess->student->id) }}" data-toggle="popover" data-placement="right" data-img="{{ $todoAccess->student->image ? $todoAccess->student->getImageUrl() : '' }}">{{ $todoAccess->student->getFullNameAttribute() }}</a>
                                @else
                                    {{ @$todoAccess->user->name }}
                                @endif
                            </td>
                            <td>{{ $done_tasks." / ". $total_todo_tasks}}</td>
                            @foreach($todo->todoTasks as $key => $todoTask)
                                <td class="text-center">
                                    @php
                                        if(isset($_todo_task_status[$todoTask->id]))
                                        {
                                            $todoTaskStatus = $_todo_task_status[$todoTask->id];
                                            $is_done = $todoTaskStatus->status == 1 ? 1 : 0;
                                            $updated_at = $todoTaskStatus->getLocalUpdatedAt()->format('Y/m/d H:i');
                                            $updated_by = @$todoTaskStatus->updatedByUser->name;
                                            $status_line = ( $is_done ?  __('messages.marked-as-complete-by') : __('messages.marked-as-incomplete-by') ).' '.$updated_by.' at '.$updated_at;
                                        }
                                    @endphp
                                    @if(isset($_todo_task_status[$todoTask->id]))
                                        <i class="fa {{ $is_done == 1 ? 'fa-check' : 'fa-times' }}"
                                            data-toggle="tooltip" title="{{ $status_line }}"
                                            data-placement="right"
                                        ></i>
                                    @endif
                                </td>
                            @endforeach
                        </tr>
                    @endforeach
                </table>
            </div>

        </div>
    </div>
@endsection

@push('scripts')
<script>
    window.addEventListener('DOMContentLoaded', function() {
        reInitializeToolitip();
    });

    function reInitializeToolitip()
    {
        $('[data-toggle="popover"]').popover({
            html: true,
            trigger: 'hover',
            content: function () {
                return '<img src="'+$(this).data('img') + '" style="max-width:300px;"/>';
            }
        });
    }
</script>
@endpush
