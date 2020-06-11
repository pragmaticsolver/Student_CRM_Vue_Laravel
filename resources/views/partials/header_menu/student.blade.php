@if(Auth::user()->hasPermissionTo('student-list') ||
    Auth::user()->hasPermissionTo('student-create') ||
    Auth::user()->hasPermissionTo('student-map')
    )

    @php
        $date = \Carbon\Carbon::now(\App\Helpers\CommonHelper::getSchoolTimezone())->format('Y-m-d');
        $allStudentsTodoCount = \App\Students::all_student_todo_alert_count($date);
    @endphp

    <li class="{{ (request()->is('student') ||
            request()->is('student/create') ||
            request()->is('student/map') ||
            Route::currentRouteName() == 'payment_batches.index'
            ) ? 'dropdown active' : 'dropdown' }}">
        <a aria-expanded="false" role="button" href="#" class="nav-link dropdown-toggle" data-toggle="dropdown">
            {{ __('messages.student') }} <span class="badge badge-danger nav_all_student_todo_count" style="font-size:12px;margin-left: 2px;{{ $allStudentsTodoCount == 0 ? 'display:none;' : ''}}">{{ $allStudentsTodoCount }}</span>
        </a>
        <ul role="menu" class="dropdown-menu">
            @can('student-list')
                <li class="{{ (request()->is('student')) ? 'active' : '' }}">
                    <a href="{{ url('/student') }}">
                        {{ __('messages.studentlist') }} <span class="badge badge-danger nav_all_student_todo_count" style="font-size:12px;margin-left: 2px;{{ $allStudentsTodoCount == 0 ? 'display:none;' : ''}}">{{ $allStudentsTodoCount }}</span>
                    </a>
                </li>
            @endif
            @can('student-create')
                <li class="{{ (request()->is('student/create')) ? 'active' : '' }}"><a href="{{ url('/student/create') }}">{{ __('messages.addstudent') }}</a></li>
            @endif
            @can('student-map')
                <li class="{{ (request()->is('student/map')) ? 'active' : '' }}"><a href="{{ route('student.map') }}">{{ __('messages.studentmap') }}</a></li>
            @endif
        </ul>
    </li>
@endif
