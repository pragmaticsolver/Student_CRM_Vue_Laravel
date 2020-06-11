@can('attendance-add')
    <li class="{{ (request()->is('attendance/add/yoyaku/repeat') || request()->is('attendance/add/yoyaku/once')) ? 'dropdown active' : 'dropdown' }}">
        <a aria-expanded="false" role="button" href="#" class="nav-link dropdown-toggle" data-toggle="dropdown">{{ __('messages.reservations') }}</a>
        <ul role="menu" class="dropdown-menu">
            <li class="{{ (request()->is('attendance/add/yoyaku/repeat')) ? 'active' : '' }}"><a href="{{ url('/attendance/add/yoyaku/repeat') }}">{{ __('messages.repeatclass') }}</a></li>
            <li class="{{ (request()->is('attendance/add/yoyaku/once')) ? 'active' : '' }}"><a href="{{ url('/attendance/add/yoyaku/once') }}">{{ __('messages.oneoffclass') }}</a></li>
        </ul>
    </li>
@endcan
