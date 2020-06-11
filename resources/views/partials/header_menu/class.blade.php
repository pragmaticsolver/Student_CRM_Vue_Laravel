@if(
    Auth::user()->hasPermissionTo('class-list') || Auth::user()->hasPermissionTo('class-create') ||
    Auth::user()->hasPermissionTo('class-category') || Auth::user()->hasPermissionTo('event')
)
    <li class="{{ (
        request()->is('class') || request()->is('class/create') ||
        request()->is('class-category') || request()->is('class-category/create') ||
        request()->is('event') || request()->is('event/create')
    ) ? 'dropdown active' : 'dropdown' }}">
        <a aria-expanded="false" role="button" href="#" class="nav-link dropdown-toggle" data-toggle="dropdown">{{ __('messages.class') }}</a>
        <ul role="menu" class="dropdown-menu">
            @can('class-list')
                <li class="{{ (request()->is('class')) ? 'active' : '' }}"><a href="{{ url('/class') }}">{{ __('messages.classlist') }}</a></li>
            @endcan
            @can('class-create')
                <li class="{{ (request()->is('class/create')) ? 'active' : '' }}"><a href="{{ url('/class/create') }}">{{ __('messages.addclass') }}</a></li>
            @endcan
            @can('event')
                <li class="{{ (request()->is('event')) ? 'active' : '' }}"><a href="{{ url('/event') }}">{{ __('messages.eventlist') }}</a></li>
                <li class="{{ (request()->is('event/create')) ? 'active' : '' }}"><a href="{{ url('/event/create') }}">{{ __('messages.addevent') }}</a></li>
            @endcan
            @can('class-category')
                <li class="{{ (request()->is('class-category')) ? 'active' : '' }}"><a href="{{ url('/class-category') }}">{{ __('messages.category-list') }}</a></li>
                <li class="{{ (request()->is('class-category/create')) ? 'active' : '' }}"><a href="{{ url('/class-category/create') }}">{{ __('messages.add-category') }}</a></li>
            @endcan
        </ul>
    </li>
@endif
