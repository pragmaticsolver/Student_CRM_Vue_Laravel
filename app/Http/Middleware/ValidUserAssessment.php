<?php

namespace App\Http\Middleware;

use Closure;
use App\AssessmentUsers;

class ValidUserAssessment
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
        if(\Auth::user()->hasPermissionTo('edit-assessment-response'))
        {
            return $next($request);
        }

        $assessment_user = AssessmentUsers::find($request->assessment_user_id);
        // should be able to submit assessment only once.
        if($assessment_user->user->id == \Auth::id() && $assessment_user->complete == 0) {
            return $next($request);
        } else {
            return abort('403');
        }
    }
}
