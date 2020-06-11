<?php

namespace App\Http\Controllers;

use App\Activity;
use App\ActivityLog;
use App\Helpers\CommonHelper;
use App\Settings;
use App\User;
use Illuminate\Http\Request;
use Carbon\Carbon;

class ActivityLogsController extends Controller
{
    public function index()
    {
        $school_timezone = CommonHelper::getSchoolTimezone();
        $now = Carbon::now($school_timezone);
        $from_date = $to_date = $max_date = $now->format('Y-m-d');
        return view('activity_logs.index', [
            'now' => $now,
            'from_date' => $from_date,
            'to_date' => $to_date,
            'max_date' => $max_date,
            'users' => User::orderBy('name','ASC')->get(),
            'activities' => Activity::orderBy('name','ASC')->get()
        ]);
    }

    public function data(Request $request)
    {
        $school_timezone = CommonHelper::getSchoolTimezone();

        $activityLogsQuery = ActivityLog::with('user.student','activity');
        if($request->from_date && $request->to_date)
        {
            $from = Carbon::createFromFormat('Y-m-d',$request->from_date, $school_timezone)->startOfDay()->setTimezone("UTC");
            $to = Carbon::createFromFormat('Y-m-d',$request->to_date, $school_timezone)->endOfDay()->setTimezone("UTC");
            $activityLogsQuery->whereBetween('created_at', [$from, $to]);
        }
        if($request->user_id != 'all')
        {
            $activityLogsQuery->where('user_id', $request->user_id);
        }
        if($request->activity_id != 'all')
        {
            $activityLogsQuery->where('activity_id', $request->activity_id);
        }
        if($request->sort_field && $request->sort_dir)
        {
            $activityLogsQuery->orderBy($request->sort_field,$request->sort_dir);
        }

        $activityLogs = $activityLogsQuery->get();
        return view('activity_logs.table', [
            'activityLogs' => $activityLogs,
            'school_timezone' => $school_timezone
        ]);
    }
}
