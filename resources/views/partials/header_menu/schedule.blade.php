@if(Auth::user()->hasPermissionTo('schedule-list') || Auth::user()->hasPermissionTo('schedule-add') || Auth::user()->hasPermissionTo('waitlisted-students'))
    <li class="{{ ( request()->is('schedule/waitlisted_students') || request()->is('schedule/monthly')) ? 'dropdown active' : 'dropdown' }}">
        <a aria-expanded="false" role="button" href="#" class="nav-link dropdown-toggle" data-toggle="dropdown">{{ __('messages.scheduling') }}</a>
        <ul role="menu" class="dropdown-menu">
            @can('schedule-list')
                <li class="{{ (request()->is('schedule/monthly')) ? 'active' : '' }}"><a href="{{ url('/schedule/monthly') }}">{{ __('messages.monthlyschedule') }}</a></li>
            @endif
            {{-- @can('schedule-add')
                <li class="{{ (request()->is('schedule/add/repeat')) ? 'active' : '' }}"><a href="{{ url('/schedule/add/repeat') }}">{{ __('messages.addrepeatschedule') }}</a></li>
                <li class="{{ (request()->is('schedule/add/once')) ? 'active' : '' }}"><a href="{{ url('/schedule/add/once') }}">{{ __('messages.addoneoffschedule') }}</a></li>
            @endif --}}
            @can('waitlisted-students')
                <li class="{{ (request()->is('schedule/waitlisted_students')) ? 'active' : '' }}"><a href="{{ url('/schedule/waitlisted_students') }}">{{ __('messages.waitliststudents') }}</a></li>
            @endif
        </ul>
    </li>
@endif
