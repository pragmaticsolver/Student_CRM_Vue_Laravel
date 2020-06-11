@can('test')
<li class="dropdown-submenu dropdown 
    {{ (request()->is('test/list') 
        || request()->is('test/create')
        || request()->is('test/add')
        || request()->is('comment-template/list')
        || request()->is('comment-template/add')
        ) ? 'active' : '' }}">
    <a aria-expanded="false" role="button" href="#" class="nav-link dropdown-toggle" data-toggle="dropdown">{{ __('messages.test') }}</a>
    <ul role="menu" class="dropdown-menu">
        <li class="{{ (request()->is('test/list')) ? 'active' : '' }}"><a href="{{ url('/test/list') }}">{{ __('messages.testlist') }}</a></li>
        <li class="{{ (request()->is('test/add')) ? 'active' : '' }}"><a href="{{ url('/test/add') }}">{{ __('messages.addtest') }}</a></li>
        <li class="{{ (request()->is('comment-template/list')) ? 'active' : '' }}"><a href="{{ url('/comment-template/list') }}">{{ __('messages.commenttemplatelist') }}</a></li>
        <li class="{{ (request()->is('comment-template/add')) ? 'active' : '' }}"><a href="{{ url('/comment-template/add') }}">{{ __('messages.addcommenttemplate') }}</a></li>
    </ul>
</li>
@endcan

@can('take-student-test')
<li class="dropdown-submenu dropdown 
    {{ (request()->is('student/online-test/list') 
        || request()->is('student/paper-test/list')
        ) ? 'dropdown active' : 'dropdown' }}">
    <a aria-expanded="false" role="button" href="#" class="nav-link dropdown-toggle" data-toggle="dropdown">{{ __('messages.test') }}</a>
    <ul role="menu" class="dropdown-menu">
        <li class="{{ (request()->is('student/online-test/list')) ? 'active' : '' }}"><a href="{{ url('/student/online-test/list') }}">{{ __('messages.onlinetest') }}</a></li>
        <li class="{{ (request()->is('student/paper-test/list')) ? 'active' : '' }}"><a href="{{ url('/student/paper-test/list') }}">{{ __('messages.papertest') }}</a></li>
    </ul>
</li>
@endcan
