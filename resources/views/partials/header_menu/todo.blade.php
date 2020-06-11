@if(Auth::user()->hasPermissionTo('todo-mytodos') ||
    Auth::user()->hasPermissionTo('todo-list') ||
    Auth::user()->hasPermissionTo('todo-create') ||
    Auth::user()->hasPermissionTo('todo-progress')
    )
    @php
        $active = false;
        $currentRoute = request()->route()->getName();
        if(in_array($currentRoute,['todo.index','todo.create','mytodos','todo.progress']))
        {
            $active = true;
        }

        $date = \Carbon\Carbon::now(\App\Helpers\CommonHelper::getSchoolTimezone())->format('Y-m-d');
        $myTodoCount = \Auth::user()->my_todo_alert_count($date);
    @endphp

    <li class="dropdown {{ $active ? 'active' : '' }}">
        <a aria-expanded="false" role="button" href="#" class="nav-link dropdown-toggle" data-toggle="dropdown">
        <span>{{ __('messages.todo') }}</span><span class="badge badge-danger nav_my_todo_count" style="font-size:12px;margin-left:4px;{{ $myTodoCount == 0 ? 'display:none;' : ''}}">{{ $myTodoCount }}</span>
        </a>

        <ul role="menu" class="dropdown-menu">
            @can('todo-mytodos')
                <li class="{{ $currentRoute == 'mytodos' ? 'active' : '' }}">
                    <a href="{{ route('mytodos') }}">
                        <span>{{ __('messages.mytodos') }}</span><span class="badge badge-danger nav_my_todo_count" style="font-size:12px;margin-left: 4px;{{ $myTodoCount == 0 ? 'display:none;' : '' }}">{{ $myTodoCount }}</span>
                    </a>
                </li>
            @endif
            @can('todo-list')
                <li class="{{ $currentRoute == 'todo.index' ? 'active' : '' }}">
                    <a href="{{ route('todo.index') }}">{{ __('messages.todos') }}</a>
                </li>
            @endif
            @can('todo-create')
                <li class="{{ $currentRoute == 'todo.create' ? 'active' : '' }}">
                    <a href="{{ route('todo.create') }}">{{ __('messages.add-todo') }}</a>
                </li>
            @endif
            @can('todo-progress')
                <li class="{{ $currentRoute == 'todo.progress' ? 'active' : '' }}">
                    <a href="{{ route('todo.progress') }}">{{ __('messages.todo-progress') }}</a>
                </li>
            @endif
        </ul>
    </li>
@endif
