<?php

namespace App\Http\Controllers;

use App\Helpers\CommonHelper;
use App\Helpers\StatsHelper;
use App\Schedules;
use App\SchoolOffDays;
use App\Settings;
use Illuminate\Http\Request;
use App\Teachers;
use App\TodoAccess;
use App\Yoyaku;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class HomeController extends Controller
{
    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if(Auth::user()->hasPermissionTo('schedule-list'))
        {
            $timezone = CommonHelper::getSchoolTimezone();
            $now = Carbon::now($timezone);
            $date = $now->format('Y-m-d');
            return $this->byDate($date);
        }
        else
        {
            return view('home-welcome');
        }
    }

    public function getdate(Request $request)
    {
        $date = $request->get('date');
        $date = date('Y-m-d', strtotime($date));
        return redirect('/date/'.$date);
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function byDate($date)
    {
        $dayOfWeek = date('l', strtotime($date));

        if(Auth::user()->hasRole('Teacher') && Settings::get_value('show_other_teachers_classes') == 0) // show only data of selected teachers
        {
            $teachers = Teachers::where('id', Auth::user()->teacher->id)->get();
        }
        else
        {
            $teachers = Teachers::get();
        }

        $schedules = Schedules::whereDate('date', $date)
                        ->orWhere(function($query) use($dayOfWeek, $date){
                            $query->where('start_date','<=', $date)
                                ->where('end_date','>=', $date)
                                ->where('day_of_week', $dayOfWeek);
                        })
                        ->orderBy('start_time')
                        ->whereIn('teacher_id', $teachers->pluck('id'))
                        ->get();

        $schedules = $schedules->filter(function($schedule, $key) use ($date) {
            if ($schedule->valid_date($date)) {
                return $schedule;
            }
        });

        // Show Active Teachers ( but for past dates show archived teachers as well )
        $schdeule_exists_teacher_ids = [];
        foreach($schedules as $schedule)
        {
            if(isset($schedule['teacher_id']))
            {
                $schdeule_exists_teacher_ids[] = $schedule['teacher_id'];
            }
        }
        $schdeule_exists_teacher_ids = array_unique($schdeule_exists_teacher_ids);

        $display_teachers = [];
        foreach($teachers as $teacher) {
            if($teacher->status == 0 || in_array($teacher->id, $schdeule_exists_teacher_ids))
            {
                $display_teachers[] = $teacher;
            }
        }
        $teachers = collect($display_teachers);

        $temp = [];
        foreach($schedules as $schedule)
        {
            $key = substr($schedule['start_time'],0,5).' - '.substr($schedule['end_time'],0,5);
            $temp[$key][] = $schedule;
        }
        $schedules = $temp;

        $todoAccessList = TodoAccess::Query()
                ->select('todo_accesses.*')
                ->join('todos','todos.id','=','todo_accesses.todo_id')
                ->join('students','students.id','=','todo_accesses.student_id')
                ->whereExists(function ($query) use($date, $teachers){
                    $query->select("yoyakus.id")
                          ->from('yoyakus')
                          ->join('schedules', 'schedules.id','=','yoyakus.schedule_id')
                          ->whereRaw('yoyakus.customer_id = students.id')
                          ->where('yoyakus.date', $date)
                          ->whereIn('schedules.teacher_id', $teachers->pluck('id'));
                })
                ->where('todo_accesses.student_id','!=',NULL)
                ->where('todos.start_alert_before_days','!=',NULL)
                ->whereRaw("'".$date."'".' >= DATE_SUB(IFNULL(todo_accesses.custom_due_date,todo_accesses.due_date), INTERVAL todos.start_alert_before_days DAY)')
                ->whereRaw('(SELECT COUNT(*) from todo_tasks where todo_id = todos.id ) > (SELECT COUNT(*) from todo_task_statuses where todo_access_id = todo_accesses.id AND status = 1)')
                ->get();
        $loaded_from_page = 'home';

        return view('home', compact('schedules', 'date','teachers','todoAccessList','loaded_from_page'));
    }

    public function student_row(Request $request)
    {
        $yoyaku = Yoyaku::find($request->yoyaku_id);
        $out['row_html'] = view('student-row', compact('yoyaku'))->render();
        return $out;
    }

    public function daily_stats(Request $request)
    {
        $reqDate = Carbon::createFromFormat('Y-m-d',$request->date,CommonHelper::getSchoolTimezone())->startOfDay();
        $averageStart = (clone $reqDate)->subDays(179)->format('Y-m-d');
        $avergeEnd = (clone $reqDate)->format('Y-m-d');

        $schoolOffdates = SchoolOffDays::whereBetween('date',[$averageStart, $avergeEnd])->pluck('date')->toArray();

        $reservations = Yoyaku::join('schedules','schedules.id','=','yoyakus.schedule_id')
                            ->leftjoin('classes_off_days', function($join){
                                 $join->on('classes_off_days.schedule_id','yoyakus.schedule_id')
                                    ->on('classes_off_days.date','yoyakus.date');
                            })
                            ->where('yoyakus.date', $request->date)
                            ->whereIn('schedules.teacher_id', (array)$request->selected_teachers)
                            ->whereIn('yoyakus.status',[0, 1])
                            ->where('yoyakus.waitlist',0)
                            ->whereRaw('classes_off_days.id IS NULL')
                            ->whereNotIn('yoyakus.date', $schoolOffdates)
                            ->count();

        $reservations_average = Yoyaku::selectRaw("COUNT(*) / COUNT(DISTINCT yoyakus.date) as count")
                            ->leftjoin('classes_off_days', function($join){
                                    $join->on('classes_off_days.schedule_id','yoyakus.schedule_id')
                                    ->on('classes_off_days.date','yoyakus.date');
                            })
                            ->join('schedules','schedules.id','=','yoyakus.schedule_id')
                            ->whereBetween('yoyakus.date', [$averageStart, $avergeEnd])
                            ->whereIn('schedules.teacher_id', (array)$request->selected_teachers)
                            ->whereIn('yoyakus.status',[0, 1])
                            ->where('yoyakus.waitlist',0)
                            ->whereRaw('classes_off_days.id IS NULL')
                            ->whereNotIn('yoyakus.date', $schoolOffdates)
                            ->get()->pluck('count')[0];

        $students_per_class = Yoyaku::selectRaw("COUNT(*) / COUNT(DISTINCT yoyakus.schedule_id) as count")
                            ->leftjoin('classes_off_days', function($join){
                                    $join->on('classes_off_days.schedule_id','yoyakus.schedule_id')
                                    ->on('classes_off_days.date','yoyakus.date');
                            })
                            ->join('schedules','schedules.id','=','yoyakus.schedule_id')
                            ->where('yoyakus.date', $request->date)
                            ->whereIn('schedules.teacher_id', (array)$request->selected_teachers)
                            ->whereIn('yoyakus.status',[0, 1])
                            ->where('yoyakus.waitlist',0)
                            ->whereRaw('classes_off_days.id IS NULL')
                            ->whereNotIn('yoyakus.date', $schoolOffdates)
                            ->get()->pluck('count')[0];

        $students_per_class_average = Yoyaku::selectRaw("COUNT(*) / COUNT(DISTINCT yoyakus.schedule_id, yoyakus.date) as count")
                            ->leftjoin('classes_off_days', function($join){
                                    $join->on('classes_off_days.schedule_id','yoyakus.schedule_id')
                                    ->on('classes_off_days.date','yoyakus.date');
                            })
                            ->join('schedules','schedules.id','=','yoyakus.schedule_id')
                            ->whereBetween('yoyakus.date', [$averageStart, $avergeEnd])
                            ->whereIn('schedules.teacher_id', (array)$request->selected_teachers)
                            ->whereIn('yoyakus.status',[0, 1])
                            ->where('yoyakus.waitlist',0)
                            ->whereRaw('classes_off_days.id IS NULL')
                            ->whereNotIn('yoyakus.date', $schoolOffdates)
                            ->get()->pluck('count')[0];

        $cancels = Yoyaku::join('schedules','schedules.id','=','yoyakus.schedule_id')
                            ->leftjoin('classes_off_days', function($join){
                                    $join->on('classes_off_days.schedule_id','yoyakus.schedule_id')
                                    ->on('classes_off_days.date','yoyakus.date');
                            })
                            ->where('yoyakus.date', $request->date)
                            ->whereIn('schedules.teacher_id', (array)$request->selected_teachers)
                            ->where('yoyakus.status',2)
                            ->where('yoyakus.waitlist',0)
                            ->whereRaw('classes_off_days.id IS NULL')
                            ->whereNotIn('yoyakus.date', $schoolOffdates)
                            ->count();

        $cancels_average = Yoyaku::selectRaw("COUNT(*) / COUNT(DISTINCT yoyakus.date) as count")
                            ->leftjoin('classes_off_days', function($join){
                                $join->on('classes_off_days.schedule_id','yoyakus.schedule_id')
                                    ->on('classes_off_days.date','yoyakus.date');
                            })
                            ->join('schedules','schedules.id','=','yoyakus.schedule_id')
                            ->whereBetween('yoyakus.date', [$averageStart, $avergeEnd])
                            ->whereIn('schedules.teacher_id', (array)$request->selected_teachers)
                            ->where('yoyakus.status',2)
                            ->where('yoyakus.waitlist',0)
                            ->whereRaw('classes_off_days.id IS NULL')
                            ->whereNotIn('yoyakus.date', $schoolOffdates)
                            ->get()->pluck('count')[0];

        $out['reservations'] = $reservations;
        $out['reservations_percentage'] = $reservations_average > 0 ? round( ($reservations - $reservations_average) / $reservations_average * 100, 0) : 0;

        $out['students_per_class'] = round($students_per_class, 2);
        $out['students_per_class_percentage'] = $students_per_class_average > 0 ?round( ($students_per_class - $students_per_class_average) / $students_per_class_average * 100, 0) : 0;

        $out['cancels'] = $cancels;
        $out['cancels_percentage'] = $cancels_average > 0 ? round( ($cancels - $cancels_average) / $cancels_average * 100, 0) : 0;

        return $out;
    }

    public function stats()
    {
        $timezone = CommonHelper::getSchoolTimezone();
        return view('stats', ['timezone' => $timezone]);
    }

    public function statsDataNonZeroClass(Request $request)
    {
        if($request->timeline == 'month')
        {
            $today = Carbon::now(CommonHelper::getSchoolTimezone())->startOfMonth();
            $start = (clone $today)->subMonth(11);
            $end = (clone $today);
            return StatsHelper::getNonZeroClassByMonth($start, $end);
        }
        else if($request->timeline == 'year')
        {
            return StatsHelper::getNonZeroClassByYear();
        }
        else if($request->timeline == 'week')
        {
            $today = Carbon::now(CommonHelper::getSchoolTimezone())->startOfWeek();
            $start = (clone $today)->subWeek(11);
            $end = (clone $today);
            return StatsHelper::getNonZeroClassByWeek($start, $end);
        }
        else if($request->timeline == 'day')
        {
            $start = Carbon::createFromFormat('Y-m-d', $request->dayFilterFrom, CommonHelper::getSchoolTimezone())->startOfDay();
            $end =  Carbon::createFromFormat('Y-m-d', $request->dayFilterTo, CommonHelper::getSchoolTimezone())->startOfDay();
            return StatsHelper::getNonZeroClassByDay($start, $end);
        }
    }

    public function statsDataAttendances(Request $request)
    {
        if($request->timeline == 'month')
        {
            $today = Carbon::now(CommonHelper::getSchoolTimezone())->startOfMonth();
            $start = (clone $today)->subMonth(11);
            $end = (clone $today);
            return StatsHelper::getAttendanceByMonth($start, $end);
        }
        else if($request->timeline == 'year')
        {
            return StatsHelper::getAttendanceByYear();
        }
        else if($request->timeline == 'week')
        {
            $today = Carbon::now(CommonHelper::getSchoolTimezone())->startOfWeek();
            $start = (clone $today)->subWeek(11);
            $end = (clone $today);
            return StatsHelper::getAttendanceByWeek($start, $end);
        }
        else if($request->timeline == 'day')
        {
            $start = Carbon::createFromFormat('Y-m-d', $request->dayFilterFrom, CommonHelper::getSchoolTimezone())->startOfDay();
            $end =  Carbon::createFromFormat('Y-m-d', $request->dayFilterTo, CommonHelper::getSchoolTimezone())->startOfDay();
            return StatsHelper::getAttendanceByDay($start, $end);
        }
    }

    public function doLanguageChange(Request $request)
    {
        Session::put('lang',$request->language);
        return response()->json(['status' => true]);
    }
}
