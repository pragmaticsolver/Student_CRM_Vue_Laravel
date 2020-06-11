@if(Auth::user()->hasPermissionTo('teacher-list') || Auth::user()->hasPermissionTo('teacher-create'))
    <li class="{{ (request()->is('teacher') || request()->is('teacher/create')) ? 'dropdown active' : 'dropdown' }}">
        <a aria-expanded="false" role="button" href="#" class="nav-link dropdown-toggle" data-toggle="dropdown">{{ __('messages.teacher') }}</a>
        <ul role="menu" class="dropdown-menu">
            @can('teacher-list')
                <li class="{{ (request()->is('teacher')) ? 'active' : '' }}"><a href="{{ url('/teacher') }}">{{ __('messages.teacherlist') }}</a></li>
            @endcan
            @can('teacher-create')
                <li class="{{ (request()->is('teacher/create')) ? 'active' : '' }}"><a href="{{ url('/teacher/create') }}">{{ __('messages.addteacher') }}</a></li>
            @endcan
        </ul>
    </li>
@endif
