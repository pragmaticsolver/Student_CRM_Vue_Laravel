@can('contact-list')
    <li class="{{ (request()->is('contact/list')) ? 'active' : '' }}">
        <a aria-expanded="false" role="button" href="{{ url('/contact/list') }}" class="nav-link">{{ __('messages.contact') }}</a>
    </li>
@endcan
