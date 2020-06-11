@can('class-usage')
    <li class="{{ (request()->is('student/class_usage')) ? 'active' : '' }}">
        <a aria-expanded="false" role="button" href="{{ url('/student/class_usage') }}" class="nav-link">{{ __('messages.class-usage') }}</a>
    </li>
@endcan
