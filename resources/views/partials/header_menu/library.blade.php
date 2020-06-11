@can('library')
    <li class="{{ (request()->is('book') || request()->is('book/create') || request()->is('book/checkin') || request()->is('book/checkout')) ? 'dropdown active' : 'dropdown' }}">
        <a aria-expanded="false" role="button" href="#" class="nav-link dropdown-toggle" data-toggle="dropdown">{{ __('messages.library') }}</a>
        <ul role="menu" class="dropdown-menu">
            <li class="{{ (request()->is('book')) ? 'active' : '' }}"><a href="{{ url('/book') }}">{{ __('messages.booklist') }}</a></li>
            <li class="{{ (request()->is('book/create')) ? 'active' : '' }}"><a href="{{ url('/book/create') }}">{{ __('messages.addbook') }}</a></li>
            <li class="{{ (request()->is('book/checkin')) ? 'active' : '' }}"><a href="{{ url('/book/checkin') }}">{{ __('messages.checkin') }}</a></li>
            <li class="{{ (request()->is('book/checkout')) ? 'active' : '' }}"><a href="{{ url('/book/checkout') }}">{{ __('messages.checkout') }}</a></li>
        </ul>
    </li>
@endcan
