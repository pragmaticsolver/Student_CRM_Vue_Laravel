@can('assessment')
<li class="dropdown-submenu dropdown
    {{ (request()->is('assessment/list') 
        || request()->is('assessment/add')
        ) ? 'active' : '' }}">
    <a aria-expanded="false" role="button" href="#" class="nav-link dropdown-toggle" data-toggle="dropdown">{{ __('messages.assessment') }}</a>
    <ul role="menu" class="dropdown-menu">
        <li class="{{ (request()->is('assessment/list')) ? 'active' : '' }}"><a href="{{ url('/assessment/list') }}">{{ __('messages.assessmentlist') }}</a></li>
        <li class="{{ (request()->is('assessment/add')) ? 'active' : '' }}"><a href="{{ url('/assessment/add') }}">{{ __('messages.addassessment') }}</a></li>
    </ul>
</li>
@endcan

@can('take-assessment')
<li class="dropdown {{ (request()->is('user/assessment/list')) ? 'active' : '' }}">
    <a aria-expanded="false" role="button" href="{{ url('/user/assessment/list') }}" class="nav-link">{{ __('messages.assessment') }}</a>
</li>
@endcan
