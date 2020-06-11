@can('st-assessments')
    <li class="{{ app()->request->route()->getName() == 'student.assessments' ? 'active' : '' }}">
        <a aria-expanded="false" role="button" href="{{ route('student.assessments') }}" class="nav-link">{{ __('messages.assessments') }}</a>
    </li>
@endcan
