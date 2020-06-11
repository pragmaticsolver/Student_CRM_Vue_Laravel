@can('st-classes')
    <li class="{{ app()->request->route()->getName() == 'student.classes' ? 'active' : '' }}">
        <a aria-expanded="false" role="button" href="{{ route('student.classes') }}" class="nav-link">{{ __('messages.classes') }}</a>
    </li>
@endcan
