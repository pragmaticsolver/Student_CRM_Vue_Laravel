<?php

namespace App\Http\Middleware;

use Closure;
use App\Schedules;
use App\Settings;

class ValidUserSchedule
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $current_user = \Auth::user();
        if($current_user->hasRole('Teacher'))
        {
            $teacher = $current_user->teacher;
            $schedule = Schedules::find($request->schedule_id);
            if($schedule->teacher_id != $teacher->id && Settings::get_value('show_other_teachers_classes') != 1)
            {
                return abort('403');
            }
        }
        return $next($request);
    }
}
