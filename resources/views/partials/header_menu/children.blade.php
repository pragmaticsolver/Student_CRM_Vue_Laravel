@can('children')
    <li class="{{ app()->request->route()->getName() == 'children.index' ? 'active' : '' }}">
        <a aria-expanded="false" role="button" href="{{ route('children.index') }}" class="nav-link">{{ __('messages.children') }}</a>
    </li>
@endcan
