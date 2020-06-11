@canany(['course','test','take-student-test','assessment','take-assessment'])
<li class="dropdown level-menu 
    {{ (request()->is('course/list') 
        || request()->is('course/add') 
        || request()->is('unit/list')
        || request()->is('unit/add')
        || request()->is('lesson/list')
        || request()->is('lesson/add')

        || request()->is('test/list') 
        || request()->is('test/create')
        || request()->is('test/add')
        || request()->is('comment-template/list')
        || request()->is('comment-template/add')

        || request()->is('assessment/list') 
        || request()->is('assessment/add')
        || request()->is('user/assessment/list')
            
        ) ? ' active' : '' }}">
    <a href="#" class="nav-link dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="true"> <span class="nav-label">{{ __('messages.course') }}</span> <span class="caret"></span></a>
    <ul class="dropdown-menu">
        @include('partials.header_menu.course')
        @include('partials.header_menu.test')
        @include('partials.header_menu.assessment')
    </ul>
</li>
@endcanany