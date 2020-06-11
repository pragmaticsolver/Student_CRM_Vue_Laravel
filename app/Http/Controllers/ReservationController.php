<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use DB;
use App\Schedules;
use Carbon\Carbon;
use App\Yoyaku;
use App\Students;

class ReservationController extends Controller
{
	public function index(Request $request)
    {
        
        $start = $end = null;
        if ($request->has('start') && !empty($request->get('start'))) {
            $start = $request->get('start');
        } else {
            $start = Carbon::now()->startOfMonth()->toDateTimeString();
        }
        if ($request->has('end') && !empty($request->get('end'))) {
            $end = $request->get('end');
        } else {
            $end = Carbon::today()->toDateTimeString();
        }
        $schedules   = Schedules::leftJoin('classes','classes.id','=','schedules.class_id')->select(['schedules.id as ID','schedules.class_id','schedules.teacher_id','schedules.start_time','schedules.end_time','schedules.day_of_week','schedules.date','classes.title'])->get();
        $dow    =   ['Sunday'=>0, 'Monday'=>1, 'Tuesday'=>2, 'Wednesday'=>3,'Thursday'=>4, 'Friday'=>5, 'Saturday'=>6];
        $events =   array();
        //dd($schedules);
        foreach($schedules as $key=>$schedule){
            $events[$key]['title'] = $schedule->title;
            $events[$key]['ID'] = $schedule->ID;
            $events[$key]['class_id'] = $schedule->class_id;
            $events[$key]['teacher_id'] = $schedule->teacher_id;
            if(empty($schedule->date) && !empty($schedule->day_of_week)){
                $events[$key]['dow']    =   [$dow[$schedule->day_of_week]];
                $events[$key]['start']  =   $schedule->start_time;
                $events[$key]['end']  =   $schedule->end_time;
            }else{
                $events[$key]['start']  =   $schedule->date;
            }
        }
        $events = json_encode($events);

        return view('schedule.monthly',compact('events'));
    }
    
}
