@can('reservation-list')
    <li class="{{ (request()->is('schedule/list')) ? 'active' : '' }}">
        <a aria-expanded="false" role="button" href="{{ url('/schedule/list') }}" class="nav-link">{{ __('messages.reservationlist') }}</a>
    </li>
@endcan
