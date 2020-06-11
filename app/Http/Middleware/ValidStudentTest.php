<?php

namespace App\Http\Middleware;

use Closure;
use App\Students;
use App\StudentTests;

class ValidStudentTest
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
        $student = Students::where('user_id', \Auth::id())->first();

        if($student) {
            $student_test = StudentTests::find($request->student_test_id);

            if($student_test->student->id == $student->id) {
                return $next($request);
            } else {
                return abort('403');
            }
        } else {
            return abort('403');
        }
    }
}
