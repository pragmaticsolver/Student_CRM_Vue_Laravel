<?php

namespace App\Http\Controllers;

use App\AssessmentUsers;
use App\Attendances;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use DB;
use App\Schedules;
use App\Teachers;
use App\Yoyaku;
use App\Students;
use App\ClassesOffDays;
use App\Courses;
use App\ScheduleLessons;
use App\CourseSchedules;
use App\Helpers\ActivityEnum;
use App\Helpers\ActivityLogHelper;
use App\Helpers\CommonHelper;
use App\Helpers\NotificationHelper;
use App\Helpers\ScheduleHelper;
use App\Helpers\ZoomHelper;
use App\LessonExerciseStatus;
use App\LessonHomeworkStatus;
use App\Settings;
use App\SchoolOffDays;
use App\StudentPaperTests;
use App\StudentTests;
use App\User;
use App\ScheduleComment;
use App\ScheduleFile;
use App\ZoomMeeting;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class ScheduleController extends Controller
{
    /**
     * @param Request $request
     * @return array|\Illuminate\Contracts\View\Factory|\Illuminate\View\View|mixed
     */
    public function index()
    {
        $classes = DB::table('classes')->where('class_type', 0)->get();
        if(Auth::user()->hasRole('Teacher') && Settings::get_value('show_other_teachers_classes') == 0)
        {
            $teachers = Teachers::where('id', Auth::user()->teacher->id)->get();
        }
        else
        {
            $teachers = Teachers::get();
        }

        $manage_school_off_days = Auth::user()->hasPermissionTo('manage-school-off-days') ? 1 : 0;
        
        $courses = DB::table('courses')->get();
        $class_student_levels = explode(',', Settings::get_value('class_student_levels'));
        $default_show_calendar = explode(';', Settings::get_value('default_show_calendar'));
        $visible_days = Settings::get_value('working_days');
        $default_class_length = Settings::get_value('default_class_length');
        $week_start_day = Settings::get_value('week_start_day');
        return view('schedule.monthly',compact(
            'classes', 'teachers', 'courses',
            'class_student_levels', 'default_show_calendar', 'visible_days',
            'default_class_length', 'week_start_day',
            'manage_school_off_days'
        ));
    }

    public function cal_data(Request $request)
    {
        $filter_from = Carbon::createFromTimestamp($request->start);
        $filter_to = Carbon::createFromTimestamp($request->end)->subSecond();

        $working_days = Settings::get_value('working_days');
        $working_days = str_replace("sun","Sunday",$working_days);
        $working_days = str_replace("mon","Monday",$working_days);
        $working_days = str_replace("tue","Tuesday",$working_days);
        $working_days = str_replace("wed","Wednesday",$working_days);
        $working_days = str_replace("thu","Thursday",$working_days);
        $working_days = str_replace("fri","Friday",$working_days);
        $working_days = str_replace("sat","Saturday",$working_days);
        $visible_days = explode(",",$working_days);

        $school_off_days = DB::table('school_off_days')
                            ->whereBetween('date',[
                                (clone $filter_from)->format('Y-m-d'),
                                (clone $filter_to)->format('Y-m-d')
                            ])->pluck('date')->toArray();

        $cancel_class_records = DB::table('classes_off_days')->whereBetween('date',[
                            (clone $filter_from)->format('Y-m-d'),
                            (clone $filter_to)->format('Y-m-d')
                        ])->get();
        $cancel_classes = array();
        foreach($cancel_class_records as $cancel_class)
        {
            $cancel_classes[$cancel_class->schedule_id][] = $cancel_class->date;
        }

        $dow = ['Sunday'=>0, 'Monday'=>1, 'Tuesday'=>2, 'Wednesday'=>3,'Thursday'=>4, 'Friday'=>5, 'Saturday'=>6];
        $current_user = \Auth::user();
        $current_user_role = $current_user->get_role()->name;
        $hide_full_permission = $current_user->hasPermissionTo('calendar-hide-full-class');


        if(Auth::user()->hasRole('Teacher') && Settings::get_value('show_other_teachers_classes') == 0) // show only data of selected teachers
        {
            $teachers = Teachers::where('id', Auth::user()->teacher->id)->get();
        }
        else
        {
            $teachers = Teachers::get();
        }

        $schedules  = Schedules::with('class.category','teacher','yoyaku')
                        ->where(function($query) use($teachers){
                            $query->whereIn('teacher_id',$teachers->pluck('id'));
                            $query->orWhere('teacher_id', NULL);
                        })
                        ->where(function($query) use($filter_from,$filter_to){
                            $query->where('date',NULL);
                            $query->orWhereBetween('date',[$filter_from->format('Y-m-d'),$filter_to->format('Y-m-d')]);
                        })->get();
        $events = array();
        foreach($schedules as $key => $schedule) {
            if(empty($schedule->date) && !empty($schedule->day_of_week)){
                if(!in_array($schedule->day_of_week,$visible_days))
                {
                    continue;
                }

                if(!empty($schedule->start_date) && !empty($schedule->end_date)) {
                    $start_date = Carbon::parse($schedule->start_date);
                    $end_date = Carbon::parse($schedule->end_date);

                    if($schedule->day_of_week == (clone $start_date)->format('l'))
                    {
                        $next_day_of_week = $start_date;
                    }
                    else
                    {
                        $next_day_of_week = $start_date->modify('next '.$schedule->day_of_week);
                    }

                    while($next_day_of_week->lessThanOrEqualTo($end_date)) {
                        if(
                            $next_day_of_week->greaterThanOrEqualTo($filter_from) &&
                            $next_day_of_week->lessThanOrEqualTo($filter_to))
                        {
                            $event_date = $next_day_of_week->format('Y-m-d');
                            $off_day = false;
                            if(in_array($event_date,$school_off_days))
                            {
                                $off_day = true;
                            }
                            if($off_day == false && isset($cancel_classes[$schedule->id]) && in_array($event_date,$cancel_classes[$schedule->id]))
                            {
                                $off_day = true;
                            }
                            if(!$off_day)
                            {
                                $event = $this->get_event_array($schedule, $current_user, $current_user_role,$event_date);
                                $event['start'] = $event_date.' '.$schedule->start_time;
                                $event['end'] = $event_date.' '.$schedule->end_time;
                                $event['hideFull'] = $hide_full_permission;
                                $events[] = $event;
                            }
                        }

                        $next_day_of_week = $next_day_of_week->modify('next '.$schedule->day_of_week);
                    }
                } else {
                    $event = $this->get_event_array($schedule, $current_user, $current_user_role,'');
                    $event['dow']    =   [$dow[$schedule->day_of_week]];
                    $event['start']  =   $schedule->start_time;
                    $event['end']  =   $schedule->end_time;
                    $event['hideFull'] = $hide_full_permission;
                    $events[] = $event;
                }
            } else {
                $day_of_week = Carbon::createFromFormat('Y-m-d',$schedule->date)->format('l');
                if(!in_array($day_of_week,$visible_days))
                {
                    continue;
                }

                $event_date = $schedule->date;
                $off_day = false;
                if(in_array($event_date,$school_off_days))
                {
                    $off_day = true;
                }
                if($off_day == false && isset($cancel_classes[$schedule->id]) && in_array($event_date,$cancel_classes[$schedule->id]))
                {
                    $off_day = true;
                }
                if(!$off_day)
                {
                    $event = $this->get_event_array($schedule, $current_user, $current_user_role,$event_date);
                    $event['start']  =   date('Y-m-d H:i:s', strtotime($schedule->date . ' ' . $schedule->start_time));
                    $event['end']  =   date('Y-m-d H:i:s', strtotime($schedule->date . ' ' . $schedule->end_time));
                    $event['hideFull'] = $hide_full_permission;
                    $events[] = $event;
                }
            }
        }

        // Student Birthdays
        $dates = array();
        $check_bd_dates = [];
        $check_date = (clone $filter_from);
        while($check_date->lessThanOrEqualTo($filter_to))
        {
            $md = $check_date->format('m-d');
            $check_bd_dates[] = $md;
            $dates[$md] = $check_date->format('Y-m-d H:i:s');
            $check_date->addDay();
        }
        $chek_bd_dates_str = "'" . implode("','",$check_bd_dates) . "'";
        $students = Students::whereRaw("DATE_FORMAT(birthday,'%m-%d') IN ( $chek_bd_dates_str ) ")->get();
        foreach($students as $student)
        {
            $event_date = $dates[substr($student->birthday,'5')];
            $event = array();
            $event['start'] = $event_date;
            $event['end']  = $event_date;
            $event['hideFull'] = $hide_full_permission;
            $event['fullDates'] = [];
            $event['allDay'] = true;
            $event['title'] = $student->getFullNameAttribute();
            $event['isBirthdayEvent'] = true;
            $event['backgroundColor'] = '#1a97b3';
            $events[] = $event;
        }

        
        // School Off Days
        foreach($school_off_days as $schoo_off_date){
            $event = array();
            $event['start'] = $schoo_off_date;
            $event['end']  = $schoo_off_date;
            $event['hideFull'] = $hide_full_permission;
            $event['fullDates'] = [];
            $event['allDay'] = true;
            $event['title'] = __('messages.school-off-day');
            $event['isSchoolOffDayEvent'] = true;
            $event['backgroundColor'] = '#e81414';
            $events[] = $event;
        }

        // Show hide teacher filter
        $event_exists_teacher_ids = [];
        foreach($events as $event)
        {
            if(isset($event['teacher_id']))
            {
                $event_exists_teacher_ids[] = $event['teacher_id'];
            }
        }
        $event_exists_teacher_ids = array_unique($event_exists_teacher_ids);

        $display_teachers = [];
        foreach($teachers as $teacher)
        {
            // Active Teachers or Archived Teachers but class exists (for past months)
            if($teacher->status == 0 || in_array($teacher->id, $event_exists_teacher_ids))
            {
                $display_teachers[] = $teacher->id;
            }
        }

        $out['display_teachers'] = $display_teachers;
        $out['events'] = $events;

        return $out;
    }

    public function event_data(Request $request)
    {
        $current_user = \Auth::user();
        $current_user_role = $current_user->get_role()->name;
        $hide_full_permission = $current_user->hasPermissionTo('calendar-hide-full-class');

        $schedule  = Schedules::with('class.category','teacher','yoyaku')->where('id',$request->schedule_id)->first();
        $event_date = $request->date;

        $event = $this->get_event_array($schedule, $current_user, $current_user_role,$event_date);
        $event['start'] = $event_date.' '.$schedule->start_time;
        $event['end'] = $event_date.' '.$schedule->end_time;
        $event['hideFull'] = $hide_full_permission;
        $out['event'] = $event;
        return $out;
    }

    public function create($type)
    {
    	$classes = DB::table('classes')->where('status','=',0)->get();
    	$teachers = Teachers::where('status', 0)->get();
        return view('schedule.create', array('type' => $type, 'classes' => $classes, 'teachers' => $teachers, 'courses' => Courses::all()));
    }

    public function store(Request $request, $type)
    {
        $request->validate(Schedules::get_validate_params($type));

        try {
            $date = NULL;
            $day_of_week = NULL;

            switch($type) {
                case 'repeat':
                    $schedule_type = 0;
                    $day_of_week = $request->day_of_week;
                    break;
                case 'once':
                    $schedule_type = 1;
                    $date = $request->date;
                    break;
                default:
                    $schedule_type = 2;
                    $date = $request->date;
            }

            $schedule = Schedules::create([
                'class_id' => $request->class_id,
                'teacher_id' => $request->teacher_id,
                'start_time' => $request->start_time,
                'end_time' => $request->end_time,
                'start_date' => $request->start_date,
                'end_date' => $request->end_date,
                'day_of_week' => $day_of_week,
                'date' => $date,
                'type' => $schedule_type,
            ]);

            if($request->course_id) {
                CourseSchedules::create(['schedule_id' => $schedule->id, 'course_id' => $request->course_id]);
            }

            return redirect('/schedule/monthly');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }


    }

    public function save(Request $request)
    {
        $type = $request->get('type');

        $date = NULL;
        $day_of_week = NULL;
        if($type == '0') {
            $day_of_week = date('l', strtotime($request->get('date')));
        } else {
            $date = $request->get('date');
        }

        // For one off events - do not create event if the given date is off day.
        if($date)
        {
            if(SchoolOffDays::where('date', $date)->exists())
            {
                return redirect()->back();
            }
        }

        $schedule = Schedules::create([
            'class_id' => $request->class_id,
            'teacher_id' => $request->teacher_id,
            'start_time' => $request->start_time,
            'end_time' => $request->end_time,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'day_of_week' => $day_of_week,
            'date'=> $date,
            'type'=> $type,
        ]);

        if($request->course_id) {
            CourseSchedules::create(['schedule_id' => $schedule->id, 'course_id' => $request->course_id]);
        }

        ActivityLogHelper::create(
            ActivityEnum::CLASS_SCHEDULED,
            CommonHelper::getMainLoggedInUserId(),
            ActivityLogHelper::getClassScheduledParams($schedule)
        );

        // For recurring events - cancel classes on school off day
        if(!$date)
        {
            $offDates = SchoolOffDays::whereBetween('date', [$request->start_date, $request->end_date])->pluck('date')->toArray();
            foreach($offDates as $off_date)
            {
                CommonHelper::cancelClass($schedule, $off_date, CommonHelper::getMainLoggedInUserId());
            }
        }
        
        return redirect()->back();
    }

    public function calendar()
    {
        $teachers = Teachers::where('status', 0)->get();
        $class_student_levels = explode(',', Settings::get_value('class_student_levels'));
        $student_levels = \Auth::user()->student ? explode(",",\Auth::user()->student->levels) : [];
        $student_levels = json_encode($student_levels);
        $default_show_calendar = explode(';', Settings::get_value('default_show_calendar'));
        $visible_days = Settings::get_value('working_days');
        $week_start_day = Settings::get_value('week_start_day');
        return view('schedule.calendar',compact(
            'teachers', 'class_student_levels', 'student_levels',
            'default_show_calendar', 'visible_days', 'week_start_day'
        ));
    }

    public function cal_data_1(Request $request)
    {
        $filter_from = Carbon::createFromTimestamp($request->start);
        $filter_to = Carbon::createFromTimestamp($request->end)->subSecond();

        $working_days = Settings::get_value('working_days');
        $working_days = str_replace("sun","Sunday",$working_days);
        $working_days = str_replace("mon","Monday",$working_days);
        $working_days = str_replace("tue","Tuesday",$working_days);
        $working_days = str_replace("wed","Wednesday",$working_days);
        $working_days = str_replace("thu","Thursday",$working_days);
        $working_days = str_replace("fri","Friday",$working_days);
        $working_days = str_replace("sat","Saturday",$working_days);
        $visible_days = explode(",",$working_days);

        $school_off_days = DB::table('school_off_days')
                            ->whereBetween('date',[
                                (clone $filter_from)->format('Y-m-d'),
                                (clone $filter_to)->format('Y-m-d')
                            ])->pluck('date')->toArray();

        $cancel_class_records = DB::table('classes_off_days')->whereBetween('date',[
                            (clone $filter_from)->format('Y-m-d'),
                            (clone $filter_to)->format('Y-m-d')
                        ])->get();
        $cancel_classes = array();
        foreach($cancel_class_records as $cancel_class)
        {
            $cancel_classes[$cancel_class->schedule_id][] = $cancel_class->date;
        }

        $schedules  = Schedules::with('class.category','teacher','yoyaku')
                        ->where(function($query) use($filter_from,$filter_to){
                            $query->where('date',NULL);
                            $query->orWhereBetween('date',[$filter_from->format('Y-m-d'),$filter_to->format('Y-m-d')]);
                        })->get();

        $dow = ['Sunday'=>0, 'Monday'=>1, 'Tuesday'=>2, 'Wednesday'=>3,'Thursday'=>4, 'Friday'=>5, 'Saturday'=>6];
        $current_user = \Auth::user();
        $current_user_role = $current_user->get_role()->name;
        $hide_full_permission = $current_user->hasPermissionTo('calendar-hide-full-class');
        $events = array();

        foreach($schedules as $key=>$schedule) {
            if(empty($schedule->date) && !empty($schedule->day_of_week)){

                if(!in_array($schedule->day_of_week,$visible_days))
                {
                    continue;
                }

                if(!empty($schedule->start_date) && !empty($schedule->end_date)) {
                    $start_date = Carbon::parse($schedule->start_date);
                    $end_date = Carbon::parse($schedule->end_date);

                    if($schedule->day_of_week == (clone $start_date)->format('l'))
                    {
                        $next_day_of_week = $start_date;
                    }
                    else
                    {
                        $next_day_of_week = $start_date->modify('next '.$schedule->day_of_week);
                    }

                    while($next_day_of_week->lessThanOrEqualTo($end_date)) {
                        if(
                            $next_day_of_week->greaterThanOrEqualTo($filter_from) &&
                            $next_day_of_week->lessThanOrEqualTo($filter_to))
                        {
                            $event_date = $next_day_of_week->format('Y-m-d');
                            $off_day = false;
                            if(in_array($event_date,$school_off_days))
                            {
                                $off_day = true;
                            }
                            if($off_day == false && isset($cancel_classes[$schedule->id]) && in_array($event_date,$cancel_classes[$schedule->id]))
                            {
                                $off_day = true;
                            }
                            if(!$off_day)
                            {
                                $event = $this->get_event_array_student($schedule, $current_user, $current_user_role, $event_date);
                                $event['start'] = $event_date.' '.$schedule->start_time;
                                $event['end'] = $event_date.' '.$schedule->end_time;
                                $event['hideFull'] = $hide_full_permission;
                                $events[] = $event;
                            }
                        }

                        $next_day_of_week = $next_day_of_week->modify('next '.$schedule->day_of_week);
                    }
                } else {
                    $event = $this->get_event_array_student($schedule, $current_user, $current_user_role, '');
                    $event['dow']    =   [$dow[$schedule->day_of_week]];
                    $event['start']  =   $schedule->start_time;
                    $event['end']  =   $schedule->end_time;
                    $event['hideFull'] = $hide_full_permission;
                    $events[] = $event;
                }
            }else{
                $day_of_week = Carbon::createFromFormat('Y-m-d',$schedule->date)->format('l');
                if(!in_array($day_of_week,$visible_days))
                {
                    continue;
                }
                $event_date = $schedule->date;
                $off_day = false;
                if(in_array($event_date,$school_off_days))
                {
                    $off_day = true;
                }
                if($off_day == false && isset($cancel_classes[$schedule->id]) && in_array($event_date,$cancel_classes[$schedule->id]))
                {
                    $off_day = true;
                }
                if(!$off_day)
                {
                    $event = $this->get_event_array_student($schedule, $current_user, $current_user_role, $schedule->date);
                    $event['start']  =   date('Y-m-d H:i:s', strtotime($schedule->date . ' ' . $schedule->start_time));
                    $event['end']  =   date('Y-m-d H:i:s', strtotime($schedule->date . ' ' . $schedule->end_time));
                    $event['hideFull'] = $hide_full_permission;
                    $events[] = $event;
                }
            }
        }

        // School Off Days
        foreach($school_off_days as $schoo_off_date){
            $event = array();
            $event['start'] = $schoo_off_date;
            $event['end']  = $schoo_off_date;
            $event['hideFull'] = $hide_full_permission;
            $event['fullDates'] = [];
            $event['allDay'] = true;
            $event['title'] = __('messages.school-off-day');
            $event['isSchoolOffDayEvent'] = true;
            $event['backgroundColor'] = '#e81414';
            $events[] = $event;
        }
        
        $out['events'] = $events;
        return $out;
    }

    public function event_data_1(Request $request)
    {
        $current_user = \Auth::user();
        $current_user_role = $current_user->get_role()->name;
        $hide_full_permission = $current_user->hasPermissionTo('calendar-hide-full-class');

        $schedule  = Schedules::with('class.category','teacher','yoyaku')->where('id',$request->schedule_id)->first();
        $event_date = $request->date;

        $event = $this->get_event_array_student($schedule, $current_user, $current_user_role,$event_date);
        $event['start'] = $event_date.' '.$schedule->start_time;
        $event['end'] = $event_date.' '.$schedule->end_time;
        $event['hideFull'] = $hide_full_permission;
        $out['event'] = $event;
        return $out;
    }

    public function calendar_data(Request $request)
    {
        $schedules   = Schedules::Join('classes','classes.id','=','schedules.class_id')->get();
        $dow    =   ['Sunday'=>0, 'Monday'=>1, 'Tuesday'=>2, 'Wednesday'=>3,'Thursday'=>4, 'Friday'=>5, 'Saturday'=>6];
        $events =   array();

        foreach($schedules as $key=>$schedule){
            $events[$key]['title'] = $schedule->title;
            if(empty($schedule->date) && !empty($schedule->day_of_week)){
                $events[$key]['dow']    =   [$dow[$schedule->day_of_week]];
                $events[$key]['start']  =   $schedule->start_time;
                $events[$key]['end']  =   $schedule->end_time;
            }else{
                $events[$key]['start']  =   date('Y-m-d H:i:s', strtotime($schedule->date . ' ' . $schedule->start_time));
                $events[$key]['end']  =   date('Y-m-d H:i:s', strtotime($schedule->date . ' ' . $schedule->end_time));
            }
        }
        echo json_encode($events);
    }

    public function schedule_details(Request $request)
    {
        if($request->has('schedule_id')){
            $id =   $request->get('schedule_id');
            $date   =   $request->get('date');
            $schedule = Schedules::find($id);
            $current_number_of_students = DB::table('yoyakus')->select('yoyakus.schedule_id')->join('students','yoyakus.customer_id','students.id')->where('yoyakus.schedule_id','=',$id)->where('yoyakus.date','=',$date)->where('yoyakus.status','<>',2)->where('yoyakus.waitlist','=',0)->get()->count();
            $class_id = $schedule->class_id;
            $limit = DB::table('classes')->select('size')->where('id','=',$class_id)->get();
            if($limit[0]->size == NULL) {
                $default_size = DB::table('settings')->select('name', 'value')->where('name','=','limit_number_of_students_per_class')->get();
                $limit = $default_size[0]->value;
            } else {
                $limit = $limit[0]->size;
            }
            if($current_number_of_students >= $limit) {
                $full = true;
            } else {
                $full = false;
            }

            $users = DB::table('yoyakus')->select('yoyakus.id', 'yoyakus.customer_id', 'yoyakus.schedule_id', 'yoyakus.taiken', 'yoyakus.start_date', 'yoyakus.end_date', 'yoyakus.date', 'yoyakus.waitlist', 'schedules.class_id', 'schedules.teacher_id', 'classes.payment_plan_id', 'students.lastname_kanji', 'students.firstname_kanji', 'users.id as user_id')->join('students','yoyakus.customer_id','students.id')->join('users','students.user_id','users.id')->join('schedules','yoyakus.schedule_id','schedules.id')->join('classes','schedules.class_id','classes.id')->where('schedule_id','=',$id)->where('yoyakus.date','=',$date)->where('yoyakus.status','<>',2)->get();
            if($schedule->is_event()) {
                if($request->get('view') == 'body') {
                    $student_id = \Auth::user()->student->id;
                    return view('schedule.modal_event', compact('schedule', 'date', 'id', 'full', 'student_id'), ['users' => $users]);
                } else {
                    if($request->get('view') == 'facing-footer') {
                        return view('schedule.reserve_event', compact('schedule', 'date', 'id', 'full'), ['users' => $users]);
                    }
                    if($request->get('view') == 'monthly-footer') {
                        return view('schedule.reserve_event_by_teacher', compact('schedule', 'date', 'id', 'full'), ['users' => $users]);
                    }
                }
            } else {
                if($request->get('view') == 'body') {

                    $use_zoom = Settings::get_value('use_zoom');
                    $zoomMeeting = NULL;
                    if($use_zoom)
                    {
                        $scheduleZoomMeeting = $schedule->getScheduleZoomMeeting($date);
                        if($scheduleZoomMeeting)
                        {
                            $zoomMeeting = $scheduleZoomMeeting->zoomMeeting;
                        }
                    }

                    return view('schedule.modal', compact('schedule', 'date', 'id', 'full', 'zoomMeeting'), ['users' => $users]);
                } else {
                    if($request->get('view') == 'facing-footer') {
                        return view('schedule.reserve', compact('schedule', 'date', 'id', 'full'), ['users' => $users]);
                    }
                    if($request->get('view') == 'monthly-footer') {
                        return view('schedule.reserve_by_teacher', compact('schedule', 'date', 'id', 'full'), ['users' => $users]);
                    }
                }
            }
        }
    }

    public function schedule_cancel_details(Request $request){
        if($request->has('schedule_id')){
            $id =   $request->get('schedule_id');
            $date   =   $request->get('date');
            $schedule   =   Schedules::join('classes','classes.id','schedules.class_id')->join('teachers','schedules.teacher_id','teachers.id')->where('schedules.id',$id)->first();

            return view('schedule.cancel',compact('schedule','date','id'));
        }
    }

    public function schedule_details_student_list(Request $request){

        if($request->has('schedule_id')){
            $id =   $request->get('schedule_id');
            $date   =   $request->get('date');

            $schedule = Schedules::find($id);
            $current_number_of_students = DB::table('yoyakus')->select('schedule_id')->join('students','yoyakus.customer_id','students.id')->where('schedule_id','=',$id)->where('date','=',$date)->where('yoyakus.status','<>',2)->where('waitlist','=',0)->get()->count();
            $class_id = $schedule->class_id;
            $limit = DB::table('classes')->select('size')->where('id','=',$class_id)->get();
            if($limit[0]->size == NULL) {
                $default_size = DB::table('settings')->select('name', 'value')->where('name','=','limit_number_of_students_per_class')->get();
                $limit = $default_size[0]->value;
            } else {
                $limit = $limit[0]->size;
            }
            if($current_number_of_students >= $limit) {
                $full = true;
            } else {
                $full = false;
            }

            $students = Students::orderBy('firstname','ASC')->orderBy('lastname','ASC')->get();

            $yoyakus = Yoyaku::with('student', 'schedule')
                    ->where('schedule_id','=',$id)
                    ->where('date','=',$date)
                    ->where('status','!=',2)
                    ->where('waitlist','=','0')->get();

            if($schedule->is_class()) 
            {
                $use_zoom = Settings::get_value('use_zoom');
                $permission_manage_zoom_meetings = \Auth::user()->hasPermissionTo('manage-zoom-meetings-for-class');
                $zoomMeeting = NULL;
                if($use_zoom)
                {
                    $scheduleZoomMeeting = $schedule->getScheduleZoomMeeting($date);
                    if($scheduleZoomMeeting)
                    {
                        $zoomMeeting = $scheduleZoomMeeting->zoomMeeting;
                    }
                }

                $waitlisted_yoyakus = Yoyaku::with('student','schedule')
                                    ->where('schedule_id','=',$id)
                                    ->where('date','=',$date)
                                    ->where('status','!=',2)
                                    ->where('waitlist','=',1)->get();

                return view('schedule.modal_students',compact('schedule','date','id','zoomMeeting','use_zoom','permission_manage_zoom_meetings'), ['yoyakus' => $yoyakus, 'waitlisted_yoyakus' => $waitlisted_yoyakus, 'students' => $students]);
            } else {
                return view('schedule.modal_students_event', compact('schedule','date','id'), ['yoyakus' => $yoyakus, 'students' => $students]);
            }
        }
    }

    public function reservation(Request $request)
    {
        $schedule = Schedules::findOrFail($request->schedule_id);
        $activityByUser = User::findOrFail(CommonHelper::getMainLoggedInUserId());
        $currentUser = Auth::user();

        $student = Students::where('user_id',\Auth::user()->id)->first();
        if(!$student)
        {
            return [
                'success'=>false,
                'error'=>'You are not a student. Kindly register as a student or contact your administrator.'
            ];
        }
        $date = $request->date;
        return ScheduleHelper::makeReservation($schedule, $student, $date, $activityByUser, $currentUser);
    }

    public function reservation_by_teacher(Request $request)
    {
        $schedule = Schedules::find($request->get('schedule_id'));
        $student = Students::where('id',$request->get('customer_id'))->first();
        $date = $request->date;
        $currentUser = Auth::user();
        $activityByUser = User::findOrFail(CommonHelper::getMainLoggedInUserId());
        $taiken = $request->taiken ? $request->taiken : 0;

        $send_email = 1;
        if(isset($request->send_email) && $request->send_email == 0)
        {
            $send_email = 0;
        }

        $class = $schedule->class()->first();
        if($class->class_type == 1)
        {
            $res = ScheduleHelper::registerForEvent($currentUser, $schedule, $student, $date);
            if($res['success'])
            {
                ActivityLogHelper::create(
                    ActivityEnum::RESERVATION_MADE,
                    $activityByUser->id,
                    ActivityLogHelper::getReservationMadeParams($res['yoyaku'],$activityByUser->id)
                );
            }
            return $res;
        }
        else
        {
            if($request->has('schedule_id') && $request->has('date') && $request->has('customer_id'))
            {
                $school_off_days = DB::table('school_off_days')->where('date','=',$request->get('date'))->get()->first();
                if($school_off_days) {
                    return response()->json(['success'=>false,'error'=>'School day off']);
                }
                $current_number_of_students = DB::table('yoyakus')->select('yoyakus.schedule_id')->join('students','yoyakus.customer_id','students.id')->where('yoyakus.schedule_id','=',$request->get('schedule_id'))->where('yoyakus.date','=',$request->get('date'))->where('yoyakus.status','<>',2)->where('yoyakus.waitlist','=',0)->get()->count();
                $class_id = DB::table('schedules')->select('class_id')->where('id','=',$request->get('schedule_id'))->get();
                $limit = DB::table('classes')->select('size')->where('id','=',$class_id[0]->class_id)->get();
                if($limit[0]->size == NULL) {
                    $default_size = DB::table('settings')->select('name', 'value')->where('name','=','limit_number_of_students_per_class')->get();
                    $limit = $default_size[0]->value;
                } else {
                    $limit = $limit[0]->size;
                }
                if($current_number_of_students >= $limit) {
                    return response()->json(['success'=>false,'error'=>'This class limit '. $limit . ' students!']);
                } else {
                    $created_yoyaku = NULL;
                    $last_insert_id = -1;
                    if($schedule->type == '0' && !empty($request->get('start_date')) && !empty($request->get('end_date')))
                    {
                        $date = $request->get('date');
                        $start_date = $request->get('start_date');
                        $end_date = $request->get('end_date');

                        $dates = array();
                        $dates_registered = array();

                        // while ($date >= $start_date && $date <= $end_date) {
                        //     $dates[] = $date;
                        //     $date = date('Y-m-d', strtotime($date . ' +1 week'));
                        // }

                        $carbon_end = Carbon::createFromFormat('Y-m-d',$end_date);

                        $day_name = Carbon::createFromFormat('Y-m-d',$date)->format('l');
                        $start_date_day_name = Carbon::createFromFormat('Y-m-d',$start_date)->format('l');
                        if($day_name == $start_date_day_name)
                        {
                            $carbon_check_date = Carbon::createFromFormat('Y-m-d',$start_date);
                        }
                        else
                        {
                            $carbon_check_date = Carbon::createFromFormat('Y-m-d',$start_date)->modify("next ". $day_name);
                        }
                        while($carbon_check_date <= $carbon_end)
                        {
                            $dates[] = (clone $carbon_check_date)->format('Y-m-d');
                            $carbon_check_date->addWeek();
                        }

                        if(!empty($dates))
                        {
                            foreach ($dates as $date)
                            {
                                if(!$schedule->isPastClassCheckPasses(Auth::user(),$date))
                                {
                                    return response()->json(['success'=>false,'error'=> __('messages.reservation-can-not-be-made-for-past-classes-or-events') ]);
                                }
                            }

                            foreach ($dates as $date) {
                                $res    =  new Yoyaku();
                                $res->customer_id   =   $request->get('customer_id');
                                $res->schedule_id   =   $request->get('schedule_id');
                                $res->date          =   $date;
                                $res->start_date    =   $start_date;
                                $res->end_date      =   $end_date;
                                $res->taiken        =   $taiken;
                                $res->save();
                                $created_yoyaku = $res;
                                if($date == $request->get('date')) {
                                    $last_insert_id = $res->id;
                                }
                                $dates_registered[] = $date;
                            }
                        }
                        else
                        {
                            return response()->json(['success'=>false,'error'=> __('messages.no-classes-found-between-given-dates') ]);
                        }
                    } else {
                        if(!$schedule->isPastClassCheckPasses(Auth::user(),$request->get('date')))
                        {
                            return response()->json(['success'=>false,'error'=> __('messages.reservation-can-not-be-made-for-past-classes-or-events') ]);
                        }

                        $res    =  new Yoyaku();
                        $res->customer_id   =   $request->get('customer_id');
                        $res->schedule_id   =   $request->get('schedule_id');
                        $res->date          =   $request->get('date');
                        $res->start_date    =   $request->get('start_date');
                        $res->end_date      =   $request->get('end_date');
                        $res->taiken        =   $taiken;
                        $res->save();
                        $created_yoyaku = $res;

                        $last_insert_id = $res->id;
                        $dates_registered[] = $res->date;
                    }

                    ActivityLogHelper::create(
                        ActivityEnum::RESERVATION_MADE,
                        CommonHelper::getMainLoggedInUserId(),
                        ActivityLogHelper::getReservationMadeParams($created_yoyaku,CommonHelper::getMainLoggedInUserId())
                    );

                    if($request->has('yoyaku_id')) {
                        $yoyaku_id = $request->get('yoyaku_id');
                        $yoyaku = Yoyaku::find($yoyaku_id);
                        $yoyaku->delete();
                    }
                    
                    if($send_email)
                    {
                        NotificationHelper::sendRegisterClassNotification($student, $schedule, $dates_registered);
                        NotificationHelper::sendRegisterClassNotificationToTeacher($student, $schedule, $dates_registered);  
                    }

                    if($current_number_of_students + 1 == $limit) {
                        $full = true;
                    } else {
                        $full = false;
                    }

                    $inserted_yoyaku = NULL;
                    if($last_insert_id > 0) {
                        $inserted_yoyaku = DB::table('yoyakus')->select('yoyakus.id as yoyaku_id', 'yoyakus.customer_id', 'yoyakus.schedule_id', 'yoyakus.date', 'yoyakus.start_date', 'yoyakus.end_date', 'schedules.class_id', 'schedules.teacher_id', 'classes.payment_plan_id', 'students.lastname_kanji', 'students.firstname_kanji')->join('students','yoyakus.customer_id','students.id')->join('schedules','yoyakus.schedule_id','schedules.id')->join('classes','schedules.class_id','classes.id')->where('yoyakus.id','=',$last_insert_id)->get()->first();
                    }


                    $payments = DB::table('payments')->where('customer_id','=',$student->id)->orderby('date')->get();
                    return ScheduleHelper::checkPayment($student->id, $payments, $schedule, $full, $inserted_yoyaku);
                }
            }
            else
            {
                return response()->json(['success'=>false,'error'=>'something went wrong, please try later.']);
            }
        }
    }

    public function list(Request $request)
    {
        $date = Carbon::now(CommonHelper::getSchoolTimezone())->format("Y-m-d");
        $yoyakus = Yoyaku::with('schedule.class','schedule.teacher')->select('yoyakus.*')
                            ->join('students','students.id','yoyakus.customer_id')
                            ->join('schedules','schedules.id','yoyakus.schedule_id')
                            ->join('teachers','teachers.id','schedules.teacher_id')
                            ->join('classes','classes.id','schedules.class_id')
                            ->where([
                                ['students.user_id',\Auth::user()->id],
                                ['yoyakus.date','>=',$date]
                            ])->get();

        return view('schedule.list',['yoyakus' => $yoyakus]);
    }

    public function schedule_cancel_class(Request $request)
    {
        try
        {
            $send_email = 1;
            if(isset($request->send_email) && $request->send_email == 0)
            {
                $send_email = 0;
            }
            
            $schedule = Schedules::find($request->schedule_id);
            CommonHelper::cancelClass($schedule, $request->date, CommonHelper::getMainLoggedInUserId(), $send_email);
            return redirect()->back();
        }
        catch(\Exception $e)
        {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    public function schedule_cancel_classes(Request $request) {
        if($request->has('schedule_id')){
            $id =   $request->get('schedule_id');
            $date   =   $request->get('date');
            $cancel_classes = DB::table('classes_off_days')->where('schedule_id','=',$id)->where('date','=',$date)->get();

            return response()->json(compact('cancel_classes'));
        }
    }

    public function waitlist(Request $request)
    {
        $student    =   Students::where('user_id',\Auth::user()->id)->first();
        $res    =  new Yoyaku();
        $res->customer_id   =   $student->id;
        $res->schedule_id   =   $request->get('schedule_id');
        $res->date          =   $request->get('date');
        $res->taiken        =   '0';
        $res->waitlist      =   '1';
        $res->save();

        if($res->schedule && $res->schedule->class)
        {
            NotificationHelper::sendWaitlistClassNotification($res);
        }
        
        return response()->json(['success'=>true,'message'=>'Add to wait list successfully!']);
    }

    public function waitlist_by_teacher(Request $request)
    {
        $res    =  new Yoyaku();
        $res->customer_id   =   $request->get('customer_id');
        $res->schedule_id   =   $request->get('schedule_id');
        $res->date          =   $request->get('date');
        $res->taiken        =   $request->taiken ? $request->taiken : 0;
        $res->waitlist      =   '1';
        $res->save();

        $send_email = 1;
        if(isset($request->send_email) && $request->send_email == 0)
        {
            $send_email = 0;
        }

        if($send_email && $res->schedule && $res->schedule->class)
        {
            NotificationHelper::sendWaitlistClassNotification($res);
        }

        $inserted_yoyaku = DB::table('yoyakus')->select('yoyakus.id as yoyaku_id', 'yoyakus.customer_id', 'yoyakus.schedule_id', 'yoyakus.date', 'yoyakus.start_date', 'yoyakus.end_date','yoyakus.taiken', 'schedules.class_id', 'schedules.teacher_id', 'classes.payment_plan_id', 'students.lastname_kanji', 'students.firstname_kanji')->join('students','yoyakus.customer_id','students.id')->join('schedules','yoyakus.schedule_id','schedules.id')->join('classes','schedules.class_id','classes.id')->where('yoyakus.id','=',$res->id)->get()->first();

        return response()->json(['success'=>true,'message'=>'Add to wait list successfully!','yoyaku'=>$inserted_yoyaku]);
    }

    public function waitlist_delete(Request $request)
    {
        $id = $request->get('yoyaku_id');
        $yoyaku = Yoyaku::find($id);
        $yoyaku->delete();

        return response()->json(['success'=>true,'message'=>'Delete student successfully!']);
    }

    public function waitlisted_students(Request $request)
    {
        $waitlist_students = DB::table('yoyakus')->select('yoyakus.id', 'yoyakus.customer_id', 'yoyakus.schedule_id', 'yoyakus.date', 'classes.title', 'students.lastname_kanji', 'students.firstname_kanji')->join('students','yoyakus.customer_id','students.id')->join('schedules','yoyakus.schedule_id','schedules.id')->join('classes','schedules.class_id','classes.id')->where('yoyakus.status','<>',2)->where('yoyakus.waitlist','=','1')->get();

        return view('schedule.waitlisted_students', compact('waitlist_students'));
    }

    public function show($id)
    {
        $schedule = Schedules::find($id);
        $course = $schedule->course_schedule ? $schedule->course_schedule->course : NULL;
        $masterLessonExerciseStatus = LessonExerciseStatus::where('schedule_id', $schedule->id)->get()->keyBy('lesson_exercise_id');
        $masterLessonHomeworkStatus = LessonHomeworkStatus::where('schedule_id', $schedule->id)->get()->keyBy('lesson_homework_id');
        return view('schedule.details', [
            'schedule' => $schedule,
            'course' => $course,
            'masterLessonExerciseStatus' => $masterLessonExerciseStatus,
            'masterLessonHomeworkStatus' => $masterLessonHomeworkStatus,
            'comment_lang' => Auth::user()->lang
        ]);
    }

    private function get_event_array($schedule, $current_user, $current_user_role, $check_date)
    {
        $event = array();
        $class = $schedule->class;
        $visible_user_roles = json_decode($class->category->visible_user_roles);
        
        $title = $class->title;
        if($class->size == 1)
        {
            $yoyaku = $schedule->getFirstReservation($check_date);
            if ($yoyaku) {
                $title = $yoyaku->student->getFullNameAttribute();
            }
        }

        $event['fullDates'] = $class ? $class->getFullDates($schedule->id) : [];
        $event['allDay'] = $schedule->type == Schedules::EVENT_ALLDAY_TYPE ? true : false;
        $event['isEvent'] = $schedule->is_event();
        $event['title'] = $title;
        $event['ID'] = $schedule->id;
        $event['class_id'] = $schedule->class_id;
        $event['teacher_id'] = $schedule->teacher_id;
        $event['backgroundColor'] = $schedule->is_class() ? $schedule->teacher->get_color_coding() : null;
        $event['classLevel'] = $class->level;
        $event['isVisible'] = ($visible_user_roles && in_array($current_user_role, $visible_user_roles));
        $event['isReserved'] = $schedule->is_reserved_user($current_user);
        $event['isEmpty'] = $schedule->isEmpty($check_date);
        $event['isWaitlisted'] = $schedule->isWaitlisted($check_date);
        $event['greyedPastClass'] = !$schedule->isPastClassCheckPasses($current_user,$check_date);
        return $event;
    }

    private function get_event_array_student($schedule, $current_user, $current_user_role, $check_date)
    {
        $event = array();
        $class = $schedule->class;
        $visible_user_roles = json_decode($class->category->visible_user_roles);

        $event['fullDates'] = $class ? $class->getFullDates($schedule->id) : [];
        $event['allDay'] = $schedule->type == Schedules::EVENT_ALLDAY_TYPE ? true : false;
        $event['isEvent'] = $schedule->is_event();
        $event['title'] = $class->title;
        $event['ID'] = $schedule->id;
        $event['class_id'] = $schedule->class_id;
        $event['teacher_id'] = $schedule->teacher_id;
        $event['backgroundColor'] = $schedule->is_class() ? $schedule->teacher->get_color_coding() : null;
        $event['classLevel'] = $class->level;
        $event['isVisible'] = ($visible_user_roles && in_array($current_user_role, $visible_user_roles));
        $event['isReserved'] = $schedule->is_reserved_user($current_user);
        $event['isStudentRegistered'] = $schedule->isStudentRegistered($current_user, $check_date);
        $event['greyedPastClass'] = !$schedule->isPastClassCheckPasses($current_user,$check_date);
        return $event;
    }

    public function student_row(Request $request)
    {
        $yoyaku = Yoyaku::where('id',$request->yoyaku_id)->where('status','!=',2)->first();

        if(!$yoyaku)
        {
            $out['row_html'] = '';
        }
        else
        {
            if($yoyaku->schedule->is_class())
            {
                $out['row_html'] = view('schedule.student_row', compact('yoyaku'))->render();
            }
            else
            {
                $out['row_html'] = view('schedule.student_row_event', compact('yoyaku'))->render();
            }
        }

        return $out;
    }

    public function waitlist_student_row(Request $request)
    {
        $yoyaku = Yoyaku::with('student','schedule')
                    ->where('id', $request->yoyaku_id)
                    ->where('status','!=',2)
                    ->where('waitlist','=',1)->first();

        if(!$yoyaku)
        {
            $out['row_html'] = '';
        }
        else
        {
            $out['row_html'] = view('schedule.waitlisted_student_row', compact('yoyaku'))->render();
        }

        return $out;
    }

    public function schedule_details_student(Request $request)
    {
        if($request->has('schedule_id')){
            $id =   $request->get('schedule_id');
            $yoyaku = Yoyaku::find($request->get('yoyaku_id'));
            $date   =   $request->get('date');
            $schedule = Schedules::find($id);

            if($request->get('view') == 'body')
            {
                return view('schedule.modal_student_page', compact('schedule', 'date', 'id','yoyaku'));
            }
        }
    }

    public function cancel_multiple_modal(Request $request)
    {
        $schedule = Schedules::find($request->schedule_id);
        $school_off_days = DB::table('school_off_days')->pluck('date')->toArray();

        $cancel_classes = DB::table('classes_off_days')->where('schedule_id',$request->schedule_id)->pluck('date')->toArray();

        $dates = array();
        if(empty($schedule->date) && !empty($schedule->day_of_week) && !empty($schedule->start_date) && !empty($schedule->end_date))
        {
            $start_date = Carbon::parse($schedule->start_date);
            $end_date = Carbon::parse($schedule->end_date);

            if($schedule->day_of_week == (clone $start_date)->format('l'))
            {
                $next_day_of_week = $start_date;
            }
            else
            {
                $next_day_of_week = $start_date->modify('next '.$schedule->day_of_week);
            }

            while($next_day_of_week->lessThanOrEqualTo($end_date))
            {
                $event_date = $next_day_of_week->format('Y-m-d');

                $off_day = false;
                if(in_array($event_date,$school_off_days) || in_array($event_date,$cancel_classes))
                {
                    $off_day = true;
                }
                if(!$off_day)
                {
                    $dates[] = $event_date;
                }
                $next_day_of_week = $next_day_of_week->modify('next '.$schedule->day_of_week);
            }
        }

        return view('schedule.multiple_cancel_modal', compact('dates','schedule'));
    }

    public function cancel_multiple(Request $request)
    {
        $send_email = 1;
        if(isset($request->send_email) && $request->send_email == 0)
        {
            $send_email = 0;
        }
        
        $schedule = Schedules::find($request->schedule_id);
        $dates = $request->dates;
        foreach($dates as $date)
        {
            CommonHelper::cancelClass($schedule, $date, CommonHelper::getMainLoggedInUserId(), $send_email);
        }

        $out['success'] = true;
        $out['message'] = __('messages.class-cancelled-for-selected-dates');
        return $out;
    }

    public function getEditScheduleData(Request $request)
    {
        $schedule = Schedules::findOrFail($request->schedule_id);
        $default_update_mode = 'all';
        $update_modes = [];
        $update_modes[] = [
            'text' => __('messages.all-classes-of-current-schedule'),
            'value' => 'all' 
        ];

        $classes = DB::table('classes')->where('class_type', 0)->select('id','title')->get();
        $courses = DB::table('courses')->select('id','title')->orderBy('id','ASC')->get();

        if(Auth::user()->hasRole('Teacher') && Settings::get_value('show_other_teachers_classes') == 0)
        {
            $teachersQuery = Teachers::where('id', Auth::user()->teacher->id);
        }
        else
        {
            $teachersQuery = Teachers::where('status', 0);
        }

        $teachers = $teachersQuery->select('id','nickname')->get();

        if($schedule->type == 0) // Recurring class
        {
            $split_from_day = Carbon::createFromFormat('Y-m-d',$request->date, CommonHelper::getSchoolTimezone())->startOfDay();
            $previous_instance = (clone $split_from_day)->modify('previous '.$schedule->day_of_week);

            if($previous_instance->greaterThanOrEqualTo($schedule->start_date) && $previous_instance->lessThan($schedule->end_date))
            {
                $update_modes[] = [
                    'text' => __('messages.this-and-future-classes-of-current-schedule'),
                    'value' => 'future' 
                ];
                $default_update_mode = NULL;
            }
        }

        $out['classes'] = $classes;
        $out['courses'] = $courses;
        $out['teachers'] = $teachers;
        $out['class_id'] = $schedule->class_id;
        $out['course_id'] = @$schedule->course_schedule->course->id;
        $out['teacher_id'] = $schedule->teacher->id;
        $out['update_modes'] = $update_modes;
        $out['default_update_mode'] = $default_update_mode;
        
        return $out;
    }

    public function updateSchedule(Request $request)
    {
        $schedule = Schedules::findOrFail($request->schedule_id);
        $update_mode = $request->update_mode;
        $class_changed = $course_changed = $teacher_changed = false;

        if($schedule->class_id != $request->class_id)
        {
            $class_changed = true;
        }
        if(@$schedule->course_schedule->course_id != $request->course_id)
        {
            $course_changed = true;
        }
        if($schedule->teacher->id != $request->teacher_id)
        {
            $teacher_changed = true;
        }

        if($class_changed || $course_changed || $teacher_changed)
        {
            // If selecting this & future option on first class instance, is same as updating entire schedule.
            // displayed options dymaically on ui side but also keeping it also here.
            if($update_mode == 'future')
            {
                $split_from_day = Carbon::createFromFormat('Y-m-d',$request->date, CommonHelper::getSchoolTimezone())->startOfDay();
                $previous_instance = (clone $split_from_day)->modify('previous '.$schedule->day_of_week);

                if(!($previous_instance->greaterThanOrEqualTo($schedule->start_date) && $previous_instance->lessThan($schedule->end_date)))
                {
                    $update_mode = 'all';
                }
            }

            if($update_mode == 'all')
            {
                if($class_changed)
                {
                    $schedule->class_id = $request->class_id;
                    $schedule->save();

                    Attendances::where('schedule_id', $schedule->id)
                                    ->update([
                                        'class_id' => $schedule->class_id,
                                        'payment_plan_id' => $schedule->class->payment_plan_id
                                    ]);
                }

                if($teacher_changed)
                {
                    $schedule->teacher_id = $request->teacher_id;
                    $schedule->save();
                }

                if($course_changed)
                {
                    $course_schedule = $schedule->course_schedule;
                    if($request->course_id == NULL)
                    {
                        if($course_schedule)
                        {
                            $course_schedule->delete();
                        }
                    }
                    else
                    {
                        if(!$course_schedule) // course already not existing but attaching
                        {
                            $course_schedule = new CourseSchedules();
                            $course_schedule->schedule_id = $schedule->id;
                        }
                        $course_schedule->course_id = $request->course_id;
                        $course_schedule->save();
                    }
                    
                    LessonExerciseStatus::where('schedule_id', $schedule->id)->delete();
                    LessonHomeworkStatus::where('schedule_id', $schedule->id)->delete();
                    ScheduleLessons::where('schedule_id', $schedule->id)->delete();

                    $paper_test_to_be_deleted = StudentPaperTests::where('schedule_id', $schedule->id)->pluck('id')->toArray();
                    StudentPaperTests::deleteWithFiles($paper_test_to_be_deleted);
                    AssessmentUsers::where('schedule_id', $schedule->id)->delete();
                    StudentTests::where('schedule_id', $schedule->id)->delete();
                }
            }
            else if($update_mode == 'future')
            {
                // Create new schedule by copying existing schedule.
                $new_start_date = $split_from_day->format('Y-m-d');
                $new_end_date = $previous_instance->format('Y-m-d');

                $new_schedule = $schedule->replicate();
                $new_schedule->start_date = $new_start_date; // End date is copied from original schedule.
                $new_schedule->save();

                $new_schedule = Schedules::find($new_schedule->id); // Need to refetch new schedule from db other wise laravel calls relationships on old schedule object. (used in below code)

                if(@$schedule->course_schedule->course_id)
                {
                    $courseSchedule = new CourseSchedules();
                    $courseSchedule->schedule_id = $new_schedule->id;
                    $courseSchedule->course_id = @$schedule->course_schedule->course_id;
                    $courseSchedule->save();
                }
                
                // Shorten the end date of exsting schedule.
                $schedule->end_date = $new_end_date;
                $schedule->save();

                ClassesOffDays::where('schedule_id', $schedule->id)
                            ->where('date','>', $new_end_date)
                            ->update([
                                'schedule_id' => $new_schedule->id
                            ]);

                Yoyaku::where('schedule_id', $schedule->id)
                            ->where('date','>', $new_end_date)
                            ->update([
                                'schedule_id' => $new_schedule->id
                            ]);

                Attendances::where('schedule_id', $schedule->id)
                            ->where('date','>', $new_end_date)
                            ->update([
                                'schedule_id' => $new_schedule->id,
                            ]);

                if($class_changed)
                {
                    $new_schedule->class_id = $request->class_id;
                    $new_schedule->save();

                    Attendances::where('schedule_id', $new_schedule->id)
                                    ->update([
                                        'class_id' => $new_schedule->class_id,
                                        'payment_plan_id' => $new_schedule->class->payment_plan_id
                                    ]);
                }

                if($teacher_changed)
                {
                    $new_schedule->teacher_id = $request->teacher_id;
                    $new_schedule->save();
                }

                if($course_changed)
                {
                    $course_schedule = $new_schedule->course_schedule;
                    if($request->course_id == NULL)
                    {
                        if($course_schedule)
                        {
                            $course_schedule->delete();
                        }
                    }
                    else
                    {
                        if(!$course_schedule) // Course already not existing but attaching
                        {
                            $course_schedule = new CourseSchedules();
                            $course_schedule->schedule_id = $new_schedule->id;
                        }
                        $course_schedule->course_id = $request->course_id;
                        $course_schedule->save();
                    }
                }
            }
        }

        $out['status'] = 1;
        return $out;
    }

    public function addSchoolOffDay(Request $request)
    {
        $from_date = $request->get('from_date');
        $to_date = $request->get('to_date');
        $send_email = $request->send_email ? 1 : 0;

        $carbonFrom = Carbon::createFromFormat('Y-m-d H:i:s', $from_date .' 00:00:00');
        $carbonTo = Carbon::createFromFormat('Y-m-d H:i:s', $to_date. '23:59:59');

        $schedules  = Schedules::where(function($query) use($from_date,$to_date){
                            $query->where('date',NULL);
                            $query->orWhereBetween('date',[$from_date,$to_date]);
                        })->get();

        $events = array();
        foreach($schedules as $key => $schedule) {
            if(empty($schedule->date) && !empty($schedule->day_of_week))
            {
                if(!empty($schedule->start_date) && !empty($schedule->end_date)) 
                {
                    $start_date = Carbon::parse($schedule->start_date);
                    $end_date = Carbon::parse($schedule->end_date);

                    if($schedule->day_of_week == (clone $start_date)->format('l'))
                    {
                        $next_day_of_week = $start_date;
                    }
                    else
                    {
                        $next_day_of_week = $start_date->modify('next '.$schedule->day_of_week);
                    }

                    while($next_day_of_week->lessThanOrEqualTo($end_date)) {
                        if(
                            $next_day_of_week->greaterThanOrEqualTo($carbonFrom) &&
                            $next_day_of_week->lessThanOrEqualTo($carbonTo))
                        {
                            $event_date = $next_day_of_week->format('Y-m-d');
                            $events[] = [
                                'schedule' => $schedule,
                                'date' => $event_date
                            ];
                        }
                        $next_day_of_week = $next_day_of_week->modify('next '.$schedule->day_of_week);
                    }
                }
            } else {
                $events[] = [
                    'schedule' => $schedule,
                    'date' => $schedule->date
                ];
            }
        }

        foreach($events as $event)
        {
            CommonHelper::cancelClass($event['schedule'], $event['date'], CommonHelper::getMainLoggedInUserId(), $send_email);
        }

        $start_date = \Carbon\Carbon::createFromFormat('Y-m-d', $from_date);
        $end_date = \Carbon\Carbon::createFromFormat('Y-m-d', $to_date);
        while($start_date->lessThanOrEqualTo($end_date))
        {
            $date = (clone $start_date)->format('Y-m-d');
            if(!SchoolOffDays::where('date', $date)->exists())
            {
                $day = new SchoolOffDays([
                    'date' => $date,
                ]);
                $day->save();
            }
            $start_date->addDay();
        }

        $out['message'] = __('messages.school-off-day(s)-added-successfully');
        return $out;
    }

    public function deleteSchoolOffDay(Request $request)
    {
        SchoolOffDays::where('date', $request->date)->delete();
        
        $out['message'] = __('messages.school-off-day-deleted-successfully');
        return $out;
    }

    public function createZoomMeeting(Request $request)
    {
        $schedule = Schedules::findOrFail($request->schedule_id);

        $scheduleZoomMeeting = $schedule->getScheduleZoomMeeting($request->date);
        if($scheduleZoomMeeting) {
            return [
                'status' => 0,
                'message' => __('messages.zoom-meeting-is-already-created-for-this-schedule')
            ];
        }

        if(!$schedule->teacher->user->zoom_email) {
            return [
                'status' => 0,
                'message' => __('messages.please-enter-valid-zoom-email-for-teacher'). " " . $schedule->teacher->nickname
            ];
        }
        
        $res = ZoomHelper::createMeetingForSchedule($schedule, $request->date);
        if($res['status'] == 0) {
            return [
                'status' => 0,
                'message' => $res['message']
            ];
        }

        $sheduleZoomMeeting = $res['schedule_zoom_meeting'];
        return [
            'status' => 1,
            'message' =>  __('messages.zoom-meeting-created-successfully'),
            'zoom_meeting' => $sheduleZoomMeeting->zoomMeeting
        ];
    }

    public function deleteZoomMeeting($zoom_meeting_id)
    {
        $res = ZoomHelper::deleteZoomMeeting($zoom_meeting_id);
        if($res['status'] == 0) {
            return [
                'status' => 0,
                'message' => $res['message']
            ];
        }

        return [
            'status' => 1,
            'message' => __('messages.zoom-meeting-deleted-successfully')
        ];
    }

    public function sendZoomMeetingReminder(Request $request, $to)
    {
        $schedule_id = $request->schedule_id;
        $date = $request->date;

        $schedule = Schedules::findOrFail($schedule_id);
        $scheduleZoomMeeting = $schedule->getScheduleZoomMeeting($date);
        if(!$scheduleZoomMeeting)
        {
            abort(500, __('messages.zoom-meeting-is-not-scheduled-for-this-class'));
        }
        $zoomMeeting = $scheduleZoomMeeting->zoomMeeting;

        if($to == 'students')
        {
            $yoyakus = Yoyaku::where('schedule_id', $schedule->id)
                        ->where('date', $date)
                        ->where('waitlist',0)
                        ->where('status',0)->get();
            foreach($yoyakus as $yoyaku)
            {
                $user = $yoyaku->student->user;
                NotificationHelper::sendZoomMeetingReminderForClass($user, $schedule, $date, $zoomMeeting);
            }

            $out['message'] = __('messages.meeting-reminder-will-be-sent-to-registered-students-soon');
        }
        else if($to == 'teacher')
        {
            $user = $schedule->teacher->user;
            NotificationHelper::sendZoomMeetingReminderForClass($user, $schedule, $date, $zoomMeeting, true);
            $out['message'] = __('messages.meeting-reminder-will-be-sent-to-teacher-soon');
        }
        
        return $out;
    }

    public function syncZoomMeeting($zoom_meeting_id)
    {
        $zoomMeeting = ZoomMeeting::findOrFail($zoom_meeting_id);
        $res = ZoomHelper::syncZoomMeeting($zoomMeeting);
        if($res['status'] == 0) {
            return [
                'status' => 0,
                'message' => $res['message']
            ];
        }

        return [
            'status' => 1,
            'message' => __('messages.zoom-meeting-successfully-synced-with-zoom-server'),
            'zoom_meeting' => $res['zoom_meeting']
        ];
    }

    public function addComment(Request $request)
    {
        $comment = new ScheduleComment;
        $comment->date = $request->get('date');
        $comment->schedule_id = $request->get('schedule_id');;
        $comment->comment = $request->get('comment');;
        $comment->user_id = Auth::user()->id;
        $comment->created_at = Carbon::now()->format('Y-m-d H:i:s');
        $comment->updated_at = Carbon::now()->format('Y-m-d H:i:s');
        $comment->save();
        $date = $request->get('date');
        $schedule = Schedules::find($comment->schedule_id);
        $html = view('schedule.details.tabs.comments-list', compact('schedule','date'))->render();
        return response()->json(['status' => 1, 'message' => __('messages.addcommentsuccessfully'), 'html' => $html]);
    }
    
    public function uploadFile(Request $request)
    {
        $file = $request->file('file');
        $fileName = $file->getClientOriginalName();
        $file_path = \Storage::disk('public')->putFileAs('schedule_files', $file, (\Auth::user()->id.time().'__').$fileName);
            
        $file = new ScheduleFile;
        $file->date = $request->get('date');
        $file->schedule_id = $request->get('schedule_id');
        $file->file = $file_path;
        $file->file_name = $fileName;
        $file->user_id = Auth::user()->id;
        $file->created_at = Carbon::now()->format('Y-m-d H:i:s');
        $file->updated_at = Carbon::now()->format('Y-m-d H:i:s');
        $file->save();
        $date = $request->get('date');
        $schedule = Schedules::find($file->schedule_id);
        $html = view('schedule.details.tabs.commentfiles-list', compact('schedule','date'))->render();
        return response()->json(['status' => 1, 'message' => __('messages.addfilesuccessfully'), 'html' => $html]);
    }

    public function student_classs_details(Request $request, $schedule_id) {
        $date = $request->get('date');
        $schedule = Schedules::find($schedule_id);
        return view('schedule.student_class_details', compact('schedule','date'));
    }
    
}
