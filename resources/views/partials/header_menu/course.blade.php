@can('course')
<li class="dropdown-submenu dropdown 
    {{ (request()->is('course/list') 
        || request()->is('course/add') 
        || request()->is('unit/list')
        || request()->is('unit/add')
        || request()->is('lesson/list')
        || request()->is('lesson/add')) ? ' active' : '' }}">
    <a aria-expanded="false" role="button" href="#" class="nav-link dropdown-toggle" data-toggle="dropdown">{{ __('messages.course') }}</a>
    <ul role="menu" class="dropdown-menu">
        <li class="{{ (request()->is('course/list')) ? 'active' : '' }}"><a href="{{ url('/course/list') }}">{{ __('messages.courselist') }}</a></li>
        <li class="{{ (request()->is('course/add')) ? 'active' : '' }}"><a href="{{ url('/course/add') }}">{{ __('messages.courseadd') }}</a></li>
        <li class="{{ (request()->is('unit/list')) ? 'active' : '' }}"><a href="{{ url('/unit/list') }}">{{ __('messages.unitlist') }}</a></li>
        <li class="{{ (request()->is('unit/add')) ? 'active' : '' }}"><a href="{{ url('/unit/add') }}">{{ __('messages.unitadd') }}</a></li>
        <li class="{{ (request()->is('lesson/list')) ? 'active' : '' }}"><a href="{{ url('/lesson/list') }}">{{ __('messages.lessonlist') }}</a></li>
        <li class="{{ (request()->is('lesson/add')) ? 'active' : '' }}"><a href="{{ url('/lesson/add') }}">{{ __('messages.lessonadd') }}</a></li>
    </ul>
</li>
@endcan
