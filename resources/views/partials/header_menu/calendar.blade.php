@can('calendar')
    <li class="{{ (request()->is('schedule/calendar')) ? 'active' : '' }}">
        <a aria-expanded="false" role="button" href="{{ url('/schedule/calendar') }}" class="nav-link">{{ __('messages.calendar') }}</a>
    </li>
@endcan
