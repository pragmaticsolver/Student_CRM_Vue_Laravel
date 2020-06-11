<?php

namespace App\Http\Controllers;

use App\AssessmentUsers;
use App\ClassUsageSummary;
use App\Contacts;
use App\Helpers\AutomatedTagsHelper;
use App\Helpers\CommonHelper;
use App\Helpers\NotificationHelper;
use App\Http\Requests\StudentRequest;
use App\LessonExerciseStatus;
use App\LessonHomeworkStatus;
use App\PaymentSetting;
use DB;
use App\Students;
use App\User;
use App\StudentTests;
use App\Role;
use App\Schedules;
use App\Settings;
use App\Tag;
use App\TodoAccess;
use App\Yoyaku;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Request;
use Illuminate\Support\Str;

class StudentController extends Controller
{
    public function index(Request $request)
    {
        $date = Carbon::now(CommonHelper::getSchoolTimezone())->format('Y-m-d');

        $default['sort_field'] = 'fullname';
        $default['sort_dir'] = 'asc';
        $default['role_id'] = 'all';

        $students_filter = session('students_filter');
        if($request->sort_field && $request->sort_dir)
        {
            $students_filter['sort_field'] = $request->sort_field;
            $students_filter['sort_dir'] = $request->sort_dir;
            session(['students_filter' => $students_filter]);
        }
        if($request->role_id)
        {
            $students_filter['role_id'] = $request->role_id;
            session(['students_filter' => $students_filter]);
        }

        $session_filter = session('students_filter');
        if(isset($session_filter['sort_field']) && isset($session_filter['sort_dir']) && $session_filter['sort_field'] && $session_filter['sort_dir'])
        {
            $filter['sort_field'] = $session_filter['sort_field'];
            $filter['sort_dir'] = $session_filter['sort_dir'];
        }
        else
        {
            $filter['sort_field'] = $default['sort_field'];
            $filter['sort_dir'] = $default['sort_dir'];
        }

        if(isset($session_filter['role_id']) && $session_filter['role_id'])
        {
            $filter['role_id'] = $session_filter['role_id'];
        }
        else
        {
            $filter['role_id'] = $default['role_id'];
        }


        $sql = "( SELECT
                    students.id,
                    IF(parent_users.id,1,0) as uses_parent_email,
                    IF(IF(parent_users.id, parent_users.email_verified_at, users.email_verified_at),1,0) as is_email_verified
                    FROM students
                    LEFT JOIN users
                    ON users.id = students.user_id
                    LEFT JOIN users as parent_users
                    ON parent_users.id = students.parent_user_id ) as email_verifications";

        $assigend_todos_sql = "( SELECT student_id, COUNT(*) as count
                                    FROM todo_accesses
                                    WHERE student_id IS NOT NULL
                                    GROUP BY student_id
                                ) as assigend_todos";

        $done_todos_sql = "( SELECT student_id, count(*) as count
                            FROM todo_accesses
                            WHERE (SELECT COUNT(*) from todo_tasks where todo_tasks.todo_id = todo_accesses.todo_id ) = (SELECT COUNT(*) from todo_task_statuses where todo_task_statuses.todo_access_id = todo_accesses.id AND todo_task_statuses.status = 1)
                            GROUP BY student_id
                            ) as done_todos";


        $due_todos_sql = "( SELECT todo_accesses.student_id, COUNT(*) as count
                            FROM todo_accesses
                            JOIN todos
                            ON todos.id = todo_accesses.todo_id
                            WHERE todo_accesses.student_id IS NOT NULL
                            AND todos.start_alert_before_days IS NOT NULL
                            AND '{$date}' >= DATE_SUB(IFNULL(todo_accesses.custom_due_date,todo_accesses.due_date), INTERVAL todos.start_alert_before_days DAY)
                            AND (SELECT COUNT(*) from todo_tasks where todo_id = todos.id ) > (SELECT COUNT(*) from todo_task_statuses where todo_access_id = todo_accesses.id AND status = 1)
                            GROUP BY todo_accesses.student_id ) as due_todos";

        $studentsQyuery = Students::selectRaw("students.*,
                                        CONCAT(firstname,' ',lastname) as fullname,
                                        email_verifications.is_email_verified,
                                        email_verifications.uses_parent_email,
                                        IFNULL(assigend_todos.count,0) as assigend_todos_count,
                                        IFNULL(done_todos.count,0) as done_todos_count,
                                        IFNULL(due_todos.count,0) as due_todos_count,
                                        model_has_roles.role_id as role_id,
                                        roles.name as role_name
                                        ")
                            ->leftJoin(\DB::raw($sql),'email_verifications.id','=','students.id')
                            ->leftJoin(\DB::raw($assigend_todos_sql),'assigend_todos.student_id','=','students.id')
                            ->leftJoin(\DB::raw($done_todos_sql),'done_todos.student_id','=','students.id')
                            ->leftJoin(\DB::raw($due_todos_sql),'due_todos.student_id','=','students.id')
                            ->leftJoin('model_has_roles',function($join){
                                $join->on('model_has_roles.model_id','=','students.user_id')
                                    ->where('model_has_roles.model_type','=','App\User');
                            })
                            ->leftJoin('roles', 'roles.id','=','model_has_roles.role_id');

        if($filter['role_id'] != 'all')
        {
            $studentsQyuery->where('model_has_roles.role_id',$filter['role_id']);
        }
        $studentsQyuery->orderBy($filter['sort_field'],$filter['sort_dir']);
        $students = $studentsQyuery->get();

        return view('student.list', array(
            'students' => $students,
            'filter' => $filter,
            'date' => $date,
            'roles' => Role::get_student_roles()
        ));
    }

    public function create()
    {
        $teachers = DB::table('teachers')->where('status','=',0)->get();
        return view('student.create', [
            'class_student_levels' => explode(',', Settings::get_value('class_student_levels')),
            'student_roles' => Role::get_student_roles(), 'teachers' => $teachers
        ]);
    }

    public function store(StudentRequest $request)
    {
        //try {
            $password = Str::random(6);

            $user = User::create([
                'email' => $request->email,
                'name' => $request->firstname . ' ' . $request->lastname,
                'username' => $request->firstname . rand(0, 100),
                'password' => bcrypt($password),
                'lang' => User::getDefaultLanuage($request->role)
            ]);
                
            $user->assignRole($request->role); //Assigning role to user

            $student = Students::create([
                'user_id' => $user->id,
                'lastname' => $request->lastname,
                'firstname' => $request->firstname,
                'lastname_kanji' => $request->lastname_kanji ?? $request->lastname,
                'firstname_kanji' => $request->firstname_kanji ?? $request->firstname,
                'lastname_furigana' => $request->lastname_furigana ?? $request->lastname,
                'firstname_furigana' => $request->firstname_furigana ?? $request->firstname,
                'home_phone' => $request->home_phone,
                'mobile_phone' => $request->mobile_phone,
                'email' => $request->email,
                'status' => 1,
                'join_date' => !empty($request->join_date) ? date('Y-m-d', strtotime($request->join_date)) : null,
                'address' => $request->address,
                'toiawase_referral' => $request->toiawase_referral ? $request->toiawase_referral : '',
                'toiawase_memo' => $request->toiawase_memo ? $request->toiawase_memo : '',
                'toiawase_getter' => $request->toiawase_getter ? $request->toiawase_getter : '',
                'toiawase_houhou' => $request->toiawase_houhou ? $request->toiawase_houhou : '',
                'toiawase_date' => $request->toiawase_date ? $request->toiawase_date : Carbon::now(CommonHelper::getSchoolTimezone())->format('Y-m-d'),
                'teacher_id' => $request->teacher_id,
                'birthday' => !empty($request->birthday) ? date('Y-m-d', strtotime($request->birthday)) : null,
                'comment' => $request->comment,
                'levels' => !empty($request->levels) ? implode(",",$request->levels) : '',
                'rfid_token' => $request->rfid_token,
                'office_name' => $request->office_name,
                'office_address' => $request->office_address,
                'office_phone' => $request->office_phone,
                'school_name' => $request->school_name,
                'school_address' => $request->school_address,
                'school_phone' => $request->school_phone,
                'guardian1_name' => $request->guardian1_name,
                'guardian1_address' => $request->guardian1_address,
                'guardian1_phone' => $request->guardian1_phone,
                'guardian2_name' => $request->guardian2_name,
                'guardian2_address' => $request->guardian2_address,
                'guardian2_phone' => $request->guardian2_phone,
            ]);

            NotificationHelper::sendNewUserNotification(User::find($user->id), $password);
            $student->updateAddressLatLong();

            $automatedTagsHelper = new AutomatedTagsHelper($student);
            $automatedTagsHelper->refreshUpcommingBirthdayTag();
            $automatedTagsHelper->refreshNewStudentTag();
            $automatedTagsHelper->refreshLongTimeStudentTag();
            $automatedTagsHelper->refreshRFIDRegisteredTag();

            return redirect('/student')->with('success', 'Student has been added!');
        // } catch (\Exception $e) {
        //     return redirect()->back()->withInput()->with('error', $e->getMessage());
        // }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $student = Students::find($id);

        if($student->teacher_id) {
            $teachers = DB::table('teachers')->where('id','=',$student->teacher_id)->get();
            $teacher = $teachers[0];
        } else {
            $teacher = array();
        }
        
        $contacts = Contacts::with('student','createdBy')->where('customer_id','=',$id)->orderby('date','desc')->get();
        $payments = DB::table('payments')->where('customer_id','=',$id)->orderby('date')->get();
        $attendance_payments = array();
        $attendance_ids = array();
        $number_of_payments = count($payments);
        $count_payments = 0;
        $last_remaining_points = 0;
        $last_expiration_date = NULL;
        $last_payment_id = 0;
        $expiration_points = array();
        if(!$payments->isEmpty()) {
            foreach ($payments as $payment) {
                $count_payments ++;
                $payment_date = $payment->date;
                $payment_expiration_date = $payment->expiration_date;
                $remaining_points = $payment->points;
                $attendances = DB::table('attendances')->select('attendances.id','payment_plans.points', 'attendances.date', 'attendances.cancel_policy_id')->join('classes','attendances.class_id','=','classes.id')->join('payment_plans','classes.payment_plan_id','=','payment_plans.id')->where('customer_id','=',$id)->where('date','<=',$payment_expiration_date)->orderby('date')->get();
                if(!$attendances->isEmpty()) {
                    if($last_expiration_date != NULL && $attendances[0]->date <= $last_expiration_date) {
                        $remaining_points += $last_remaining_points;
                        $expiration_points[$last_payment_id] = 0;
                    }
                    foreach ($attendances as $attendance) {
                        if(!in_array($attendance->id, $attendance_ids)) {
                            if($attendance->cancel_policy_id != NULL) {
                                $policy = DB::table('cancellation_policies')->where('id','=',$attendance->cancel_policy_id)->get()->first();
                                $attendance->points = $policy->points;
                            }
                            if($remaining_points >= $attendance->points) {
                                $attendance_ids[] = $attendance->id;
                                $remaining_points -= $attendance->points;
                                $attendance->remaining_points = $remaining_points;
                                $attendance_payments[$payment->id][] = $attendance;
                            } else {
                                $last_remaining_points = $remaining_points;
                                $last_expiration_date = $payment_expiration_date;
                                $last_payment_id = $payment->id;
                                $expiration_points[$payment->id] = $remaining_points;
                            }
                        }
                    }
                } else {
                    $last_payment_id = $payment->id;
                    $expiration_points[$payment->id] = $remaining_points;
                }
                if($count_payments == $number_of_payments) {
                    $attendances = DB::table('attendances')->select('attendances.id','payment_plans.points', 'attendances.date', 'attendances.cancel_policy_id')->join('classes','attendances.class_id','=','classes.id')->join('payment_plans','classes.payment_plan_id','=','payment_plans.id')->where('customer_id','=',$id)->orderby('date')->get();
                    if(!$attendances->isEmpty()) {
                        if($attendances[0]->date > $payment_expiration_date) {
                            $remaining_points = 0;
                        }
                        foreach ($attendances as $attendance) {
                            if($attendance->cancel_policy_id != NULL) {
                                $policy = DB::table('cancellation_policies')->where('id','=',$attendance->cancel_policy_id)->get()->first();
                                $attendance->points = $policy->points;
                            }
                            if(!in_array($attendance->id, $attendance_ids)) {
                                $attendance_ids[] = $attendance->id;
                                $remaining_points -= $attendance->points;
                                if($remaining_points >= 0) {
                                    $attendance->remaining_points = $remaining_points;
                                    $attendance_payments[$payment->id][] = $attendance;
                                } else {
                                    $attendance->remaining_points = $remaining_points;
                                    $attendance_payments[9999][] = $attendance;
                                }
                            }
                        }
                    }
                }
            }
        } else {
            $remaining_points = 0;
            $attendances = DB::table('attendances')->select('attendances.id','payment_plans.points', 'attendances.date', 'attendances.cancel_policy_id')->join('classes','attendances.class_id','=','classes.id')->join('payment_plans','classes.payment_plan_id','=','payment_plans.id')->where('customer_id','=',$id)->orderby('date')->get();
            if(!$attendances->isEmpty()) {
                foreach ($attendances as $attendance) {
                    if($attendance->cancel_policy_id != NULL) {
                        $policy = DB::table('cancellation_policies')->where('id','=',$attendance->cancel_policy_id)->get()->first();
                        $attendance->points = $policy->points;
                    }
                    $remaining_points -= $attendance->points;
                    $attendance->remaining_points = $remaining_points;
                    $attendance_payments[9999][] = $attendance;
                }
            }
        }

        $teachers = DB::table('teachers')->where('status','=',0)->get();
        $yoteis = DB::table('one_shot_yoteis')->where('guest','=',$id)->where('name','=','Level Check')->get();
        $yoyakus = DB::table('yoyakus')->select('schedules.start_time', 'schedules.end_time', 'classes.title', 'teachers.nickname')->join('schedules','yoyakus.schedule_id','=','schedules.id')->join('classes','schedules.class_id','=','classes.id')->join('teachers','schedules.teacher_id','teachers.id')->where('customer_id','=',$id)->where('taiken','=',1)->get();
        $monthly_payments = $student->monthly_payments;
        $oneoff_payments = $student->oneoff_payments;
        foreach ($monthly_payments as $payment) {
            $period = $payment->period;
            $attendances = DB::table('attendances')->where('customer_id','=',$id)->where('date','LIKE','%'.$period.'%')->get();
            $attended = count($attendances);
            foreach ($attendances as $attendance) {
                if($attendance->cancel_policy_id != NULL) {
                    $policy = DB::table('cancellation_policies')->where('id','=',$attendance->cancel_policy_id)->get()->first();
                    if($policy->points == 0) {
                        $attended--;
                    }
                }
            }
            $payment->attended = $attended;
        }
        $use_points = DB::table('settings')->select('name', 'value')->where('name','=','use_points')->get();
        $use_monthly_payments = DB::table('settings')->select('name', 'value')->where('name','=','use_monthly_payments')->get();

        // Events
        $events = [];
        $index = 0;
        $eventYoyakus = $student->yoyakus()->orderBy('date','DESC')->get();
        foreach($eventYoyakus as $yoyaku) {
            $schedule = $yoyaku->schedule;
            $event = $schedule->class()->where('class_type', 1)->first();
            if($event) {
                $events[$index]['schedule'] = $schedule;
                $events[$index]['event'] = $event;
                $index++;
            }
        };

        return view('student.details', array(
            'student' => $student, 'teacher' => $teacher, 'payments' => $payments,
            'contacts' => $contacts, 'attendance_payments' => $attendance_payments, 'teachers' => $teachers,
            'yoteis' => $yoteis, 'yoyakus' => $yoyakus, 'expiration_points' => $expiration_points,
            'monthly_payments' => $monthly_payments, 'use_points' => $use_points,
            'use_monthly_payments' => $use_monthly_payments, 'events' => $events,
            'book_students' => $student->book_students, 'student_tests' => $student->student_tests,
            'paper_tests' => $student->student_paper_tests, 'assessment_users' => $student->user->assessment_users,
            'oneoff_payments' => $oneoff_payments,
            'todoAccessList' => TodoAccess::where('student_id',$student->id)->orderByRaw("IFNULL(custom_due_date,due_date) ASC")->get(),
            'default_show_calendar' => explode(';', Settings::get_value('default_show_calendar')),
            'visible_days' => Settings::get_value('working_days'),
            'week_start_day' => Settings::get_value('week_start_day'),
            'date' => Carbon::now(CommonHelper::getSchoolTimezone())->format('Y-m-d'),
            'payment_methods' => explode(',', Settings::get_value('payment_methods')),
            'paymentSettings' => $student->getPaymentSettings()
        ));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $teachers = DB::table('teachers')->where('status','=',0)->get();
        return view('student.edit', array(
            'student' => Students::find($id), 'teachers' => $teachers,
            'class_student_levels' => explode(',', Settings::get_value('class_student_levels')),
            'student_roles' => Role::get_student_roles()
        ));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(StudentRequest $request, $id)
    {
        try {
            $new_address = false;
            $new_birthday = false;
            $new_join_date = false;
            $student = Students::find($id);
            $student->lastname = $request->get('lastname');
            $student->firstname = $request->get('firstname');
            $student->lastname_kanji = $request->get('lastname_kanji') ?? $request->get('lastname');
            $student->firstname_kanji = $request->get('firstname_kanji') ?? $request->get('firstname');
            $student->lastname_furigana = $request->get('lastname_furigana') ?? $request->get('lastname');
            $student->firstname_furigana = $request->get('firstname_furigana') ?? $request->get('firstname');
            $student->home_phone = $request->get('home_phone');
            $student->mobile_phone = $request->get('mobile_phone');
            $student->levels = !empty($request->levels) ? implode(",",$request->levels) : '';
            
            $new_rfid_token = false;
            if($student->rfid_token != $request->rfid_token)
            {
                $new_rfid_token = true;
            }
            $student->rfid_token = $request->rfid_token;

            $student->office_name = $request->office_name;
            $student->office_address = $request->office_address;
            $student->office_phone = $request->office_phone;
            $student->school_name = $request->school_name;
            $student->school_address = $request->school_address;
            $student->school_phone = $request->school_phone;
            $student->guardian1_name = $request->guardian1_name;
            $student->guardian1_address = $request->guardian1_address;
            $student->guardian1_phone = $request->guardian1_phone;
            $student->guardian2_name = $request->guardian2_name;
            $student->guardian2_address = $request->guardian2_address;
            $student->guardian2_phone = $request->guardian2_phone;

            if(!empty($request->get('address'))) {
                if($student->address != $request->get('address'))
                {
                    $student->address = $request->get('address');
                    $new_address = true;
                }
            }
            if(!empty($request->get('teacher_id'))) {
                $student->teacher_id = $request->get('teacher_id');
            }
            if(!empty($request->get('join_date'))) 
            {
                if($student->join_date != $request->get('join_date'))
                {
                    $new_join_date = true;
                }
                $student->join_date = $request->get('join_date');
            }

            if(!empty($request->get('birthday'))) 
            {   
                if($student->birthday != $request->get('birthday'))
                {
                    $new_birthday = true;
                }
                $student->birthday = $request->get('birthday');
            }

            if(!empty($request->get('toiawase_referral'))) {
                $student->toiawase_referral = $request->get('toiawase_referral');
            }
            if(!empty($request->get('comment'))) {
                $student->comment = $request->get('comment');
            }
            if(!empty($request->get('toiawase_memo'))) {
                $student->toiawase_memo = $request->get('toiawase_memo');
            }

            if(!$student->willUseParentEmail())
            {
                $student->email = $request->get('email');
                $student->user->update(['email' => $request->email]);
            }

            $student->save();

            Role::get_student_roles()->map(function($role) use ($student) {
                $student->user->removeRole($role->name);
            });

            $student->user->assignRole($request->role);
            if($new_address)
            {
                $student->updateAddressLatLong();
            }

            if($new_birthday || $new_join_date || $new_rfid_token)
            {
                $automatedTagsHelper = new AutomatedTagsHelper($student);
            }

            if($new_birthday)
            {
                $automatedTagsHelper->refreshUpcommingBirthdayTag();
            }
            
            if($new_join_date)
            {
                $automatedTagsHelper->refreshNewStudentTag();
                $automatedTagsHelper->refreshLongTimeStudentTag();
            }

            if($new_rfid_token)
            {
                $automatedTagsHelper->refreshRFIDRegisteredTag();
            }

            return redirect('/student/'.$id)->with('success', 'Student has been updated!');
        } catch (\Exception $e) {
            return redirect()->back()->withInput()->with('error', $e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $student = Students::find($id);
        $student->delete();

        return redirect('/student')->with('success', 'Student has been deleted successfully!');
    }

    public function take_test($student_test_id)
    {
        try {
            $student_test = StudentTests::find($student_test_id);

            $student_test->valid_undone();

            return view('student.test.test', [
                'student_test' => $student_test,
                'test' => $student_test->test,
                'student' => $student_test->student,
                'schedule' => $student_test->schedule
            ]);
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    public function test_list()
    {
        $student = Students::where('user_id', \Auth::id())->first();

        return view('student.test.online-list', ['student_tests' => $student->student_tests]);
    }

    public function paper_test_list()
    {
        $student = Students::where('user_id', \Auth::id())->first();

        return view('student.test.paper-list', ['paper_tests' => $student->student_paper_tests]);
    }

    /**
     * @project: 2018112901_lut
     * Description:
     * @param $user_id
     * @return \Illuminate\Http\RedirectResponse
     * @auth: ChienKV - khuongchien@gmail.com
     * @version: 1.0
     */
    public function doForceReconfirm($user_id){
        $user = User::find($user_id);

        if($user->willUseParentEmail())
        {
            $au = $user->student->parent_user;
            $au->email_verified_at = null;
            $au->save();
        }
        else
        {
            $user->email_verified_at = null;
            $user->save();
        }

        $user->sendEmailVerificationNotification();

        return redirect()->back()->with('success', __('messages.resendsuccess'));
    }

    public function map()
    {
        $students = Students::where('addr_latitude','!=',NULL)
                            ->where('addr_longitude','!=',NULL)
                            ->get();
        $final = [];
        foreach($students as $student)
        {
            $temp = [];
            $temp['addr_latitude'] = $student->addr_latitude;
            $temp['addr_longitude'] = $student->addr_longitude;
            $temp['name'] = $student->getFullNameAttribute();
            $temp['address'] = $student->address;
            $final[] = $temp;
        }

        return view('student.map', [
            'students_json' => json_encode($final),
            'google_map_api_key' => Settings::get_value('google_map_api_key')
        ]);
    }

    public function search(Request $request)
    {
        $studentsQyuery = Students::Query();

        $studentsQyuery->where(function($query) use ($request){
            $query->where('firstname', 'LIKE', "%{$request->search}%");
            $query->orWhere('lastname', 'LIKE', "%{$request->search}%");
            $query->orWhere('home_phone', 'LIKE', "%{$request->search}%");
            $query->orWhere('mobile_phone', 'LIKE', "%{$request->search}%");
            $query->orWhere('email', 'LIKE', "%{$request->search}%");
        });

        $students = $studentsQyuery->get();

        return view('student.search', array(
            'students' => $students,
        ));
    }

    public function attendance_calendar_data(Request $request)
    {
        $filter_from = Carbon::createFromTimestamp($request->start);
        $filter_to = Carbon::createFromTimestamp($request->end)->subSecond();

        $school_off_days = DB::table('school_off_days')
                            ->whereBetween('date',[
                                (clone $filter_from)->format('Y-m-d'),
                                (clone $filter_to)->format('Y-m-d')
                            ])->pluck('date')->toArray();


        $yoyakus = Yoyaku::with('attendance.cancellationPolicy.cancelType')->where('customer_id',$request->student_id)
                        ->where(function($query){
                            $query->where('status','!=',0);
                            $query->orWhere(function($query){
                                $query->where('status',0);
                                $query->where('date','>=',Carbon::now(CommonHelper::getSchoolTimezone())->format('Y-m-d'));
                            });
                        })
                        ->whereHas('schedule', function($query){
                            $query->whereIn('type', Schedules::CLASS_TYPES);
                        })
                        ->whereBetween('date',[
                            (clone $filter_from)->format('Y-m-d'),
                            (clone $filter_to)->format('Y-m-d')
                        ])
                        ->whereNotIn('date', $school_off_days)
                        ->get();

        $events = [];
        foreach($yoyakus as $yoyaku)
        {
            $schedule = $yoyaku->schedule;
            $class = $schedule->class;

            $attendance_status = $yoyaku->attendance_status;
            switch ($attendance_status) {
                case 'Waitlisted':
                    $status_class = 'student-waitlisted';
                    break;

                case 'Reserved';
                    $status_class = 'student-registered';
                    break;

                case 'Signed In';
                    $status_class = 'student-signedin';
                    break;

                case 'Full Penalty Cancel';
                    $status_class = "student-full-penalty-cancel";
                    break;

                case 'Cancel';
                case 'Partial Penalty Cancel':
                    $status_class = 'student-cancel';
                    break;

                default:
                    $status_class = '';
                    break;
            }

            if($yoyaku->taiken)
            {
                $status_class .= " class_usage_taiken";
            }

            $event['ID'] = $schedule->id;
            $event['title'] = $class->title;
            $event['start']  =  $yoyaku->date . ' ' . $schedule->start_time;
            $event['end']  = $yoyaku->date . ' ' . $schedule->end_time;
            $event['backgroundColor'] = $schedule->is_class() ? $schedule->teacher->get_color_coding() : null;
            $event['yoyaku_id'] = $yoyaku->id;
            $event['attendance_status'] = $attendance_status;
            $event['status_class'] = $status_class;
            $events[] = $event;
        }

        $out['events'] = $events;
        return $out;
    }

    // Attendance Statastics
    function class_usage_details(Request $request)
    {
        $customer_id = $request->customer_id;
        $from_date = $request->from_date ? Carbon::createFromFormat('Y-m-d',$request->from_date, CommonHelper::getSchoolTimezone())->startOfDay() : Carbon::now(CommonHelper::getSchoolTimezone())->firstOfYear();
        $current_display_year_date = (clone $from_date)->format('Y-m-d');
        $previes_year_date = (clone $from_date)->subYear()->format('Y-m-d');
        $next_year_date = (clone $from_date)->addYear()->format('Y-m-d');
        $class_usage_details = [];
        $now = Carbon::now(CommonHelper::getSchoolTimezone());
        $classUsageSummaries = ClassUsageSummary::where('customer_id',$customer_id)
                            ->whereBetween('month_year', [
                                (clone $from_date)->format('Y-m-d'),
                                (clone $from_date)->endOfYear()->firstOfMonth()->format('Y-m-d'),
                            ])->get()->keyBy('month_year');

        for($i = 0; $i < 12; $i++)
        {
            $month_year = (clone $from_date)->format('Y-m-d');

            $classUsageSummary = NULL;
            if(isset($classUsageSummaries[$month_year]))
            {
                $classUsageSummary = $classUsageSummaries[$month_year];
            }

            $details['month_year'] = $month_year;
            $details['title'] = (clone $from_date)->format('F Y');

            $details['paid'] = $classUsageSummary && !is_null($classUsageSummary->paid) ? $classUsageSummary->paid : '-';
            $details['unpaid'] = $classUsageSummary && !is_null($classUsageSummary->unpaid) ? $classUsageSummary->unpaid : '-';
            $details['used'] = $classUsageSummary && !is_null($classUsageSummary->used) ? $classUsageSummary->used : '-';
            $details['used_leftovers'] = $classUsageSummary && !is_null($classUsageSummary->used_leftovers) ? $classUsageSummary->used_leftovers : '-';
            $details['new_leftovers'] = $classUsageSummary && !is_null($classUsageSummary->new_leftovers) ? $classUsageSummary->new_leftovers : '-';
            $details['leftovers'] = $classUsageSummary && !is_null($classUsageSummary->leftovers) ? $classUsageSummary->leftovers : '-';
            $details['expiring'] = $classUsageSummary && !is_null($classUsageSummary->expiring) ? $classUsageSummary->expiring : '-';;
            $details['cancelled'] = $classUsageSummary && !is_null($classUsageSummary->cancelled) ? $classUsageSummary->cancelled : '-';


            // Need to display only future reserved classes count.
            if((clone $from_date)->greaterThanOrEqualTo((clone $now)->firstOfMonth()))
            {
                $school_off_days = DB::table('school_off_days')
                            ->whereBetween('date',[
                                (clone $from_date)->format('Y-m-d'),
                                (clone $from_date)->lastOfMonth()->format('Y-m-d')
                            ])->pluck('date')->toArray();

                $reserved = Yoyaku::where('customer_id',$customer_id)
                            ->where('status',0)
                            ->where('waitlist',0)
                            ->where('date','>=', (clone $now)->format('Y-m-d'))
                            ->whereBetween('date', [
                                (clone $from_date)->format('Y-m-d'),
                                (clone $from_date)->lastOfMonth()->format('Y-m-d')
                            ])
                            ->whereNotIn('date', $school_off_days)
                            ->count();
            }
            else
            {
                $reserved = '-';
            }
            $details['reserved'] = $reserved;

            $class_usage_details[] = $details;

            $from_date->addMonth()->firstOfMonth();
        }

        $out['html'] = view('student.class_usage_details',
                            compact(
                                    'class_usage_details',
                                    'current_display_year_date',
                                    'previes_year_date',
                                    'next_year_date')
                            )->render();
        return $out;
    }

    public function class_usage()
    {
        $student = \Auth::user()->student;
        if(!$student)
        {
            dd("No Assosicated student record found");
        }
        return view('student.class_usage', [
            'student' => $student,
            'default_show_calendar' => explode(';', Settings::get_value('default_show_calendar')),
            'visible_days' => Settings::get_value('working_days'),
            'week_start_day' => Settings::get_value('week_start_day')
        ]);
    }

    public function classes()
    {
        $student = \Auth::user()->student;
        if(!$student)
        {
            dd("No Assosicated student record found");
        }

        $schedules = Schedules::whereHas('class')->whereHas('yoyaku' , function($query) use($student){
                        $query->where('customer_id' , $student->id)
                                ->where('status','!=',2)
                                ->where('waitlist',0);
                    })->get();

        return view('student.classes', [
            'schedules' => $schedules,
        ]);
    }

    public function class_details($schedule_id)
    {
        $student = \Auth::user()->student;
        if(!$student)
        {
            dd("No Assosicated student record found");
        }

        $schedule = Schedules::findOrFail($schedule_id);

        // only reserved classess can be seen by student.
        $exists = Schedules::where('id', $schedule->id)
                                ->whereHas('class')
                                ->whereHas('yoyaku' , function($query) use($student){
                                    $query->where('customer_id' , $student->id)
                                        ->where('status','!=',2)
                                        ->where('waitlist',0);
                                })->exists();
        if(!$exists)
        {
            return abort('403');
        }

        $masterLessonExerciseStatus = LessonExerciseStatus::where('schedule_id', $schedule->id)->get()->keyBy('lesson_exercise_id');
        $masterLessonHomeworkStatus = LessonHomeworkStatus::where('schedule_id', $schedule->id)->get()->keyBy('lesson_homework_id');

        $course = $schedule->course_schedule->course;
        return view('student.class_details', [
            'schedule' => $schedule,
            'course' => $course,
            'masterLessonExerciseStatus' => $masterLessonExerciseStatus,
            'masterLessonHomeworkStatus' => $masterLessonHomeworkStatus
        ]);
    }

    public function assessments()
    {
        $student = \Auth::user()->student;
        if(!$student)
        {
            dd("No Assosicated student record found");
        }

        $assessmentUsers = AssessmentUsers::where('for_student', $student->id)
                                        ->where('complete',1)->get();

        return view('student.assessments', [
            'assessmentUsers' => $assessmentUsers,
        ]);
    }

    public function view_assessment($assessment_user_id)
    {
        $student = \Auth::user()->student;
        if(!$student)
        {
            dd("No Assosicated student record found");
        }

        $assessment_user = AssessmentUsers::findOrFail($assessment_user_id);

        if(!($assessment_user->for_student == $student->id && $assessment_user->complete == 1))
        {
            return abort('403');
        }

        return view('student.assessment_details', ['assessment_user' => $assessment_user]);
    }

    public function savePaymentSettings(Request $request, $id)
    {
        $student = Students::findOrFail($id);
        $paymentSetting = $student->paymentSetting;
        if(!$paymentSetting)
        {
            $paymentSetting = new PaymentSetting();
            $paymentSetting->student_id = $student->id;
        }
        $paymentSetting->price = $request->price;
        $paymentSetting->payment_method = $request->payment_method;
        $paymentSetting->no_of_lessons = $request->no_of_lessons;
        $paymentSetting->save();

        return redirect()->back()->with('success', __('messages.payment-settings-saved-sucessfully'));
    }

    public function archiveStudent(Request $request, $id)
    {
        $student = Students::findOrFail($request->id);
        if($student->isArchived())
        {
            $message = __('messages.student-is-already-archived');
            if($request->expectsJson())
            {
                return [
                    'status' => 0,
                    'message' => $message
                ];
            }
            else
            {
                return redirect()->back()->with('error', $message);
            }

        }
        $student->archive();

        $message = __('messages.student-archived-successfully');
        if($request->expectsJson())
        {
            return [
                'status' => 1,
                'message' => $message,
                'applied_role' => Role::ARCHIVED_STUDENT,
            ];
        }
        else
        {
            return redirect()->back()->with('success', $message);
        }
    }

    public function payments()
    {
        $student = \Auth::user()->student;
        if(!$student)
        {
            dd("No Assosicated student record found");
        }

        $payments = $student->payments()->where('rest_month', 0)->whereIn('status',['paid','invoice-sent'])->get();

        return view('student.payments', [
            'payments' => $payments,
        ]);
    }

    public function doForceVerify($user_id){
        $user = User::find($user_id);

        if($user->willUseParentEmail())
        {
            $au = $user->student->parent_user;
            $au->email_verified_at = date('Y-m-d H:i:s');
            $au->save();
        }
        else
        {
            $user->email_verified_at = date('Y-m-d H:i:s');
            $user->save();
        }
        $student = $user->student;
        $student->is_force_verified = 1;
        $student->save();

        return redirect()->back()->with('success', __('messages.force-verify-success'));
    }

    public function updateComment(Request $request, $id)
    {
        $student = Students::find($id);
        $student->comment = $request->get('comment');
        $student->save();
        
        return response()->json(['status' => 1, 'message' =>  __('messages.student-comment-update')]);
    }
}
