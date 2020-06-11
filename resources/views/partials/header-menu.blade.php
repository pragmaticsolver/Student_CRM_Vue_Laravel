<ul class="nav navbar-nav mr-auto" id="main-menus">
    @include('partials.header_menu.contact')
    {{-- @include('partials.header_menu.attendance') --}}
    @include('partials.header_menu.student')
    @include('partials.header_menu.todo')
    @include('partials.header_menu.schedule')
    @include('partials.header_menu.class')
    @include('partials.header_menu.payment-plan')
    @include('partials.header_menu.teacher')
    @include('partials.header_menu.library')
    @include('partials.header_menu.calendar')
    @include('partials.header_menu.class-usage')
    @include('partials.header_menu.classes')
    @include('partials.header_menu.assessments')
    {{-- @include('partials.header_menu.reservation-list') --}}
    @include('partials.header_menu.main-course-menu')
    @include('partials.header_menu.children')
    @include('partials.header_menu.payment')
    @include('partials.header_menu.setting')
    @can('student-search')
    <li>
        <div class="nav-link">
            <button class="btn" type="button" id="nav-search-btn" style="background:#ffffff;"><i class="fa fa-search"></i></button>
        </div>
    </li>
    @endif
</ul>
